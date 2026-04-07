import configure from '../config/configure.js';
import EventEmitter from '../../infrastructure/eventEmitter.js';
import TableColumn from '../model/tableColumn.js';

import SortManager from '../manager/sortManager.js';
import TableSorter from '../model/tableSorter.js';

export default class TableController {
    #eventEmitter;
    #tableColumn;
    #entity;

    constructor(entity) {
        // Показывать прятать столбик таблицы
        this.#entity = entity;
        this.#eventEmitter = new EventEmitter();
        this.#tableColumn = new TableColumn();

        this.#setupListeners();
        this.#restoreFromLocalStorage(this.#entity);

        // Сортировка таблицы по клику на заголовок
        const sortManager = new SortManager();
        const tableSorter = new TableSorter(sortManager);
    }

    #setupListeners() {
        this.#tableColumn.addListener('columnsChanged', this.#handleColumnsChange.bind(this));
    }

    #handleColumnsChange(columns) {
        if (configure.debug) {
            console.log('Visible columns updated:', columns);
        }

        this.#saveToLocalStorage(this.#entity, columns);
        // Тут можно добавить логику перерисовки или обновления UI
    }

    #createStorageKey(entity) {
        //entity = entity.charAt(0).toUpperCase() + entity.slice(1);
        entity = `${entity.charAt(0).toUpperCase()}${entity.slice(1)}`;
        return `tableColumnsState${entity}`;
    }

    #saveToLocalStorage(entity, columns) {
        localStorage.setItem(this.#createStorageKey(entity), JSON.stringify(columns));
    }

    #restoreFromLocalStorage(entity) {
        const savedState = localStorage.getItem(this.#createStorageKey(entity));

        if (savedState) {
            this.#tableColumn.visibleColumns = JSON.parse(savedState);
        }
    }

    init() {
        if (configure.debug) {
            console.log('TableController: init()');
        }
    }
}
