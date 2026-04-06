import { eventBus } from '../service/eventBus.js';
import markerView from './markerView.js';

export default class DiaryView {
    #markerService = null;

    constructor(markerService) {
        this.#markerService = markerService;
        this.#subscribe();
    }

    #subscribe() {
        // --- НОВЫЙ ОБСЕРВЕР: Рендеринг маркера по событию ---
        eventBus.addListener('diary:visual-render', (data) => {
            const cell = document.getElementById(data.cellId);
            const marker = this.#markerService.getMarkerByIdAndType(data.usableId, data.usableType);

            if (cell && marker) {
                markerView.createItem(cell, marker, data.baseId || null);
            } else {
                console.warn('Отрисовка невозможна: ячейка или маркер не найдены', {cell, marker, data});
            }
        });

        // Наблюдатель за удалением (очистка ячейки)
        eventBus.addListener('diary:remove', (cellId) => {
            const cell = document.getElementById(cellId);
            if (cell) {
                markerView.removeAll(cell);
            }
        });

        // Наблюдатель за полной очисткой текущей таблицы
        eventBus.addListener('diary:clear-request', (type) => {
            this.#clearTableByType(type);
        });

        // Наблюдатель за обновлением (если нужно синхронизировать данные)
        eventBus.addListener('diary:updated', (allData) => {
            console.log('UI: Данные в модели изменились', allData);
        });
    }

    #clearTableByType(type) {
        const container = document.getElementById(type);

        if (container) {
            // Ищем ячейки только внутри этого контейнера
            // Используем универсальный поиск по ID, начинающимся с типа
            const cells = container.querySelectorAll(`td[id^="${type}-"]`);

            cells.forEach(cell => markerView.removeAll(cell));

            console.log(`UI: Очищены все ячейки в таблице: ${type}`);
        }
    }

    showFlashAlert(message, type = 'warning') {
        const container = document.getElementById('dynamic-alert-container');
        const messageSpan = document.getElementById('dynamic-alert-message');
        const alertBox = container?.querySelector('.alert');

        if (container && messageSpan) {
            messageSpan.textContent = message;
            if (alertBox) {
                alertBox.className = `alert alert-${type} alert-dismissible show d-flex flex-column`;
            }
            container.classList.remove('d-none');
            setTimeout(() => container.classList.add('d-none'), 3000);
        }
    }

    getActiveTabType() {
        const activePane = document.querySelector('#myTabContent .tab-pane.active');
        return activePane ? activePane.id : null;
    }

    getTabContainersSelector() {
        return Array.from(document.querySelectorAll('#myTabContent > div'))
            .map(child => `#${child.id}`).join(', ');
    }

    bindMarkerChange(selector, handler) {
        document.querySelectorAll(selector).forEach(container => {
            container.addEventListener('change', (e) => {
                if (e.target.type === 'radio') handler(e.target);
            });
        });
    }

    bindSaveButtons(handler) {
        document.querySelectorAll('#save__schedule-top, #save__schedule-bottom')
            .forEach(btn => btn.addEventListener('click', handler));
    }

    bindClearButtons(handler) {
        document.querySelectorAll('#clear__schedule-top, #clear__schedule-bottom')
            .forEach(btn => btn.addEventListener('click', () => {
                const type = this.getActiveTabType();
                if (type) handler(type);
            }));
    }

    bindTableClicks(handler) {
        document.querySelectorAll('table tbody').forEach(tbody => {
            tbody.addEventListener('click', (e) => {
                const cell = e.target.closest('td');
                // Проверка регуляркой тоже может уйти во View или Renderer
                if (cell?.id && /^(watering|pest|stimulant|fertilizer)-/.test(cell.id)) {
                    handler(cell);
                }
            });
        });
    }

    toggleSaveLoading(isLoading) {
        // Находим все кнопки сохранения (верхнюю и нижнюю)
        const saveButtons = document.querySelectorAll('#save__schedule-top, #save__schedule-bottom');

        saveButtons.forEach(btn => {
            if (isLoading) {
                // 1. Блокируем кнопку (делаем некликбельной)
                btn.classList.add('disabled');
                btn.style.pointerEvents = 'none';

                // 2. Сохраняем старый текст и добавляем спиннер (иконку загрузки)
                btn.dataset.originalText = btn.innerHTML;
                btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Сохранение...`;
            } else {
                // 3. Возвращаем кнопку в исходное состояние
                btn.classList.remove('disabled');
                btn.style.pointerEvents = 'auto';

                // 4. Восстанавливаем текст
                if (btn.dataset.originalText) {
                    btn.innerHTML = btn.dataset.originalText;
                }
            }
        });
    }

    isCellEmpty(cell) {
        // Если внутри нет тега span, значит ячейка считается пустой
        return cell.querySelector('span') === null;
    }

    showCellNotEmptyAlert() {
        this.showFlashAlert(
            'Эта ячейка уже занята. Сначала удалите старый статус (X)',
            'warning'
        );
    }

    parseCellId(cellId) {
        const [type, id] = cellId.split('-');
        return { type, id };
    }

    toggleTableLoading(isLoading) {
        const tableContainer = document.querySelector('.table-responsive'); // ваш селектор таблицы
        if (!tableContainer) return;

        if (isLoading) {
            tableContainer.style.opacity = '0.5';
            tableContainer.insertAdjacentHTML('afterbegin', `
            <div id="table-spinner" class="position-absolute top-50 start-50 translate-middle" style="z-index: 10;">
                <div class="spinner-border text-primary" role="status"></div>
            </div>
        `);
        } else {
            tableContainer.style.opacity = '1';
            document.getElementById('table-spinner')?.remove();
        }
    }

    saveActiveTab(tabId) {
        localStorage.setItem('diary_active_tab', tabId);
    }

// Получить сохраненную вкладку
    getSavedTab() {
        return localStorage.getItem('diary_active_tab');
    }

// Ручное переключение вкладок без JS-библиотеки Bootstrap
    activateTab(tabId) {
        // 1. Убираем active у всех кнопок и панелей
        document.querySelectorAll('.nav-link, .tab-pane').forEach(el => {
            el.classList.remove('active', 'show');
        });

        // 2. Активируем нужную кнопку
        const btn = document.getElementById(`${tabId}-tab`);
        if (btn) btn.classList.add('active');

        // 3. Активируем нужную панель контента
        const pane = document.getElementById(tabId);
        if (pane) pane.classList.add('active', 'show');
    }
}
