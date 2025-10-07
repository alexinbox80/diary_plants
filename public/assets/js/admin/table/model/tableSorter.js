import configure from '../config/configure.js';
export default class TableSorter {
    #sortManager;
    #defaultDirection;
    #table;
    #tbody;
    #headers;
    #currentDirectionMap = {};

    constructor(sortManager, tableSelector = '.table-striped', defaultDirection = 'asc') {

        this.#sortManager = sortManager;
        this.#defaultDirection = defaultDirection;
        this.#table = document.querySelector(tableSelector);
        this.#tbody = this.#table.querySelector('tbody');
        this.#headers = this.#table.querySelectorAll('th.sortable');

        this.#init();
    }

    #init() {
        // Подписываемся на событие сортировки
        this.#sortManager.addListener('sort', this.#handleSort.bind(this));

        // Назначаем обработчики кликов на заголовки
        this.#headers.forEach(header => {
            header.addEventListener('click', () => {
                const key = header.dataset.column;
                this.#sortManager.sort(key);
            });
        });

        // Убираем стрелки при инициализации
        this.#clearArrows();
    }

    #clearArrows() {
        this.#headers.forEach(header => {
            const arrow = header.querySelector('.sort-arrow');
            if (arrow) {
                arrow.classList.remove('asc', 'desc');
            }
        });
    }

    /**
     * Парсит строку в дату. Возвращает объект Date или null.
     * @param {string} dateStr - строка с датой
     * @returns {Date | null}
     */
    #parseDate(dateStr) {
        if (!dateStr) return null;

        // Удаляем лишние пробелы и символы
        const cleanDate = dateStr.trim().replace(/[^0-9\.\s]/g, '');

        // Попробуем стандартный парсинг
        let parsed = new Date(cleanDate);

        if (isNaN(parsed)) {
            const ruRegex = /^(\d{2})\.(\d{2})\.(\d{4})(?:\s+(\d{2}):(\d{2}))?$/;
            const isoRegex = /^(\d{4})-(\d{2})-(\d{2})(?:\s+(\d{2}):(\d{2}))?$/;
            const match = cleanDate.match(ruRegex) || cleanDate.match(isoRegex);

            if (match) {
                if (ruRegex.test(cleanDate)) {
                    const [, day, month, year, hour = '00', minute = '00'] = match;
                    parsed = new Date(`${year}-${month}-${day}T${hour}:${minute}`);
                } else {
                    const [, year, month, day, hour = '00', minute = '00'] = match;
                    parsed = new Date(`${year}-${month}-${day}T${hour}:${minute}`);
                }
            }
        }

        return isNaN(parsed) ? null : parsed;
    }

    #handleSort({ key, direction }) {
        if (!direction) {
            direction = this.#currentDirectionMap[key] === 'asc' ? 'desc' : 'asc';
        }

        this.#currentDirectionMap[key] = direction;

        const rows = Array.from(this.#tbody.querySelectorAll('tr'));

        rows.sort((a, b) => {
            const aData = a.querySelector(`td[data-column="${key}"]`)?.textContent.trim() || '';
            const bData = b.querySelector(`td[data-column="${key}"]`)?.textContent.trim() || '';

            const aNum = parseFloat(aData);
            const bNum = parseFloat(bData);

            const aDate = this.#parseDate(aData);
            const bDate = this.#parseDate(bData);

            let result;

            // Сначала проверяем, являются ли значения числами
            if (!isNaN(aNum) && !isNaN(bNum)) {
                result = aNum - bNum;
            }
            // Потом проверяем, являются ли значения датами
            else if (aDate && bDate) {
                result = aDate - bDate;
            }
            // Иначе — сравниваем как строки
            else {
                result = aData.localeCompare(bData);
            }

            // Проверка направления
            if (direction === 'desc') {
                result = -result;
            }

            return result;
        });

        this.#tbody.innerHTML = '';
        rows.forEach(row => this.#tbody.appendChild(row));

        this.#updateArrows(key, direction);
    }

    #updateArrows(activeKey, direction) {
        this.#headers.forEach(header => {
            const arrow = header.querySelector('.sort-arrow');
            if (header.dataset.column === activeKey) {
                arrow.classList.remove('asc', 'desc');
                arrow.classList.add(direction);
            } else {
                arrow.classList.remove('asc', 'desc');
            }
        });
    }
}
