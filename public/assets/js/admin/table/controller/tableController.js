import configure from '../config/configure.js';
import EventEmitter from '../helper/eventEmitter.js';
import TableColumn from '../model/tableColumn.js';

export default class TableController {
    #eventEmitter;
    #tableColumn;
    #entity;

    constructor(entity) {
        this.#entity = entity;
        this.#eventEmitter = new EventEmitter();
        this.#tableColumn = new TableColumn();

        this.#setupListeners();
        this.#restoreFromLocalStorage(this.#entity);
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
        entity = entity.charAt(0).toUpperCase() + entity.slice(1);
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
