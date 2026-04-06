import configure from '../config/configure.js';
import { eventBus } from '../service/eventBus.js';
import Diary from './Diary.js';
import DiaryRepository from '../repository/diaryRepository.js';
import idParser from '../utility/idParser.js';

export default class DiaryList {
    #diaryList;
    #savedCellIds = null;
    #deletedBaseIds = [];

    constructor() {
        this.#diaryList = [];
        this.#savedCellIds = new Set();
        this.#deletedBaseIds = [];
        this.#subscribe();
    }

    #subscribe() {
        // Наблюдатель за созданием
        eventBus.addListener('diary:add', (data) => {

            // Если ячейка уже в списке ИЛИ уже сохранена в БД — игнорируем
            if (this.diaryIsExist(data.cellId) || this.#savedCellIds.has(data.cellId)) {
                console.warn(`Ячейка ${data.cellId} уже занята или сохранена`);
                return;
            }

            const newEntry = new Diary(data);
            this.add(newEntry.get);

            eventBus.emit('diary:visual-render', newEntry.get);
        });

        eventBus.addListener('diary:remove', (cellId) => {
            // 1. Находим запись в текущем списке
            const index = this.#diaryList.findIndex(item => item.cellId === cellId);

            if (index !== -1) {
                const entry = this.#diaryList[index];

                // 2. Если у записи есть baseId (она из БД), запоминаем её для удаления на сервере
                if (entry.baseId) {
                    this.#deletedBaseIds.push(entry.baseId);
                }

                // 3. Удаляем из локального списка и из сета сохраненных
                this.#diaryList.splice(index, 1);
                this.#savedCellIds.delete(cellId);

                if (configure.debug) {
                    console.log(`Запись ${cellId} помечена на удаление. ID: ${entry.baseId || 'нет'}`);
                }
            }
        });

        eventBus.addListener('diary:clear-request', (type) => {
            this.#diaryList = this.#diaryList.filter(item => item.type !== type);
            console.log(this.#diaryList);
            if (configure.debug) console.log('Данные в памяти очищены');
        });
    }

    load(callback, diaryClass){
        callback().then(data => {
            this.#diaryList = data.map(item => new diaryClass(item));
            eventBus.emit('loaded');
        });
    }

    getUnsavedEntries() {
        return this.#diaryList.filter(diary => !this.#savedCellIds.has(diary.cellId));
    }

    markAsSaved(entries) {
        entries.forEach(diary => this.#savedCellIds.add(diary.cellId));
    }

    getDeletedIds() {
        return this.#deletedBaseIds;
    }

    clearDeletedIds() {
        this.#deletedBaseIds = [];
    }

    async createDiaryListApi(date) {
        await DiaryRepository.getAll(date.year, date.month)
            .then(result => {
                result.data.forEach(diary => {

                    const diaryCell = new Diary({
                        cellId: diary.cell_id,
                        plantId: idParser.parse(diary.cell_id).plantId,
                        date: diary.date,
                        usableId: diary.usable_id,
                        usableType: diary.usable_type,
                        baseId: diary.base_id,
                    });
                    this.add(diaryCell.get);

                    // КРИТИЧНО: Помечаем ячейку как сохраненную
                    this.#savedCellIds.add(diaryCell.cellId);
                })
            });

        if (configure.debug) {
            console.log('DiaryList(): ', this.#diaryList);
            console.log('DiaryList(): ', this.#savedCellIds);
        }

        return true;
    }

    add(diary) {
        this.#diaryList.push(diary);
    }

    diaryIsExist(cellId) {
        return this.#diaryList.some(item => item.cellId === cellId);
    }

    get(data) {
        return this.#diaryList.find(diary => diary.date === data.date);
    }

    getAll() {
        return this.#diaryList;
    }

    updateBaseIds(savedResults) {
        savedResults.forEach(item => {
            const diary = this.#diaryList.find(d => d.cellId === item.cellId);
            if (diary) {
                // Обновляем ID в объекте (через сеттер или прямой доступ)
                diary.baseId = item.baseId;
                // Посылаем сигнал View перерисовать конкретно эту ячейку, теперь с ID
                eventBus.emit('diary:visual-render', diary);
            }
        });
    }
}
