import configure from '../config/configure.js';
import { eventBus } from '../service/eventBus.js';
import MarkerService from '../service/markerService.js';
import DiaryService from '../service/diaryService.js';
import DiaryView from '../view/diaryView.js';
import DiaryList from '../model/DiaryList.js';
import DiaryRepository from '../repository/diaryRepository.js';
import Diary from '../model/Diary.js';
import UrlParser from '../utility/urlParser.js';
import IdParser from '../utility/idParser.js';
import Date from '../utility/date.js';

export default class DiaryController {
    #service = null;
    #view = null;
    #diaryList = null;
    #yearMonth = null;

    constructor() {
        if (configure.debug) console.log('DiaryController():');

        this.#service = new DiaryService();
        this.#view = new DiaryView(this.#service);
        this.#diaryList = new DiaryList();
        this.#yearMonth = UrlParser.getDiaryParams();

        this.#process().catch(err => console.error('Init error: ', err));
    }

    async #process() {
        if (configure.debug) {
            console.log('DiaryController(): ', this.#yearMonth);
        }

        const savedTab = this.#view.getSavedTab();
        if (savedTab) {
            this.#view.activateTab(savedTab);
        }

        try {
            this.#view.toggleTableLoading(true);
            const success = await this.#diaryList.createDiaryListApi(this.#yearMonth);
            if (success) {
                this.#renderInitialData();
            }
            this.#view.toggleTableLoading(false);
        } catch (e) {
            this.#view.showFlashAlert('Ошибка загрузки данных', 'danger');
        }

        this.#setupInteractions();
        this.#setupObservers();
    }

    #renderInitialData() {
        this.#diaryList.getAll().forEach(entry => {
            eventBus.emit('diary:visual-render', entry);
        });
    }

    #setupInteractions() {
        this.#view.bindMarkerChange(
            this.#view.getTabContainersSelector(),
            (input) => MarkerService.handleSelection(input)
        );

        this.#view.bindTableClicks((cell) => this.#handleCellClick(cell));
        this.#view.bindSaveButtons(() => this.#handleSaveAll());
        this.#view.bindClearButtons((type) => this.#handleClearAll(type));

        document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                // Отрезаем "-tab" от "watering-tab", чтобы получить "watering"
                const tabId = e.target.id.replace('-tab', '');
                this.#view.saveActiveTab(tabId);
            });
        });
    }

    async #handleSaveAll() {
        const newData = this.#diaryList.getUnsavedEntries();
        const idsToDelete = this.#diaryList.getDeletedIds();

        if (newData.length === 0 && idsToDelete.length === 0) {
            this.#view.showFlashAlert('Нет изменений для сохранения', 'info');
            return;
        }

        this.#view.toggleSaveLoading(true);

        try {
            let saveSuccess = true;
            let deleteSuccess = true;

            // 1. Сначала удаляем записи из БД
            if (idsToDelete.length > 0) {
                const resultDelete = await DiaryRepository.removeEntry(idsToDelete);
                if (resultDelete && resultDelete.status === 'success') {
                    this.#diaryList.clearDeletedIds(); // Очищаем список удаленных в модели
                } else {
                    deleteSuccess = false;
                }
            }

            // 2. Затем создаем новые записи
            if (newData.length > 0) {
                const resultSave = await DiaryRepository.saveAll(newData);
                if (resultSave && resultSave.status === 'success') {
                    this.#diaryList.markAsSaved(newData);
                    if (resultSave.data) {
                        this.#diaryList.updateBaseIds(resultSave.data);
                    }
                } else {
                    saveSuccess = false;
                }
            }

            // Вывод уведомления в зависимости от результата
            if (saveSuccess && deleteSuccess) {
                this.#view.showFlashAlert('Все изменения успешно применены!', 'success');
            } else if (!saveSuccess || !deleteSuccess) {
                this.#view.showFlashAlert('Некоторые изменения не удалось сохранить', 'warning');
            }

        } catch (err) {
            console.error(err);
            this.#view.showFlashAlert('Критическая ошибка при синхронизации', 'danger');
        } finally {
            this.#view.toggleSaveLoading(false);
        }
    }

    #handleClearAll(type) {
        eventBus.emit('diary:clear-request', type);

        this.#view.showFlashAlert('Таблица очищена (изменения не сохранены на сервере)', 'info');
    }

    #setupObservers() {
        eventBus.addListener('marker:selected', (marker) => {
            this.#service.setActiveMarker(marker);
            if (configure.debug) {
                console.log(`Инструмент "${marker.description}" готов`);
            }
        });
    }

    #handleCellClick(cell) {
        const { type } = this.#view.parseCellId(cell.id);
        const marker = this.#service.getMarkerForType(type);

        if (!marker) {
            this.#view.showFlashAlert(`Пожалуйста, выберите статус во вкладке "${this.#service.getTypeName(type)}"`);
            return;
        }

        const isDeleteMode = parseInt(marker.markerId, 10) === 0;

        if (isDeleteMode) {
            // Если выбран "X", мы всегда можем очистить ячейку
            eventBus.emit('diary:remove', cell.id);
            return;
        }

        if (!this.#view.isCellEmpty(cell)) {
            this.#view.showCellNotEmptyAlert();
            return;
        }

        const data = IdParser.parse(cell.id);
        const diaryData = new Diary({
            cellId: cell.id,
            plantId: data.plantId,
            date: Date.getDate(this.#yearMonth.year, this.#yearMonth.month, data.day),
            usableId: marker.markerId,
            usableType: data.type
        });

        eventBus.emit('diary:add', diaryData.get);
    }
}
