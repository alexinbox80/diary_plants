import configure from '../config/configure.js';
import EventEmitter from '../helper/eventEmitter.js';
import TableColumn from '../model/tableColumn.js';

export default class TableController {
    #eventEmitter;
    #tableColumn;

    constructor() {
        this.#eventEmitter = new EventEmitter();

        this.#tableColumn = new TableColumn();
        this.#setupListeners();
    }

    #setupListeners() {
        this.#tableColumn.addListener('columnsChanged', this.#handleColumnsChange.bind(this));
    }

    #handleColumnsChange(columns) {
        if (configure.debug) {
            console.log('Visible columns updated:', columns);
        }

        // Тут можно добавить логику перерисовки или обновления UI
    }

    init() {
        if (configure.debug) {
            console.log('TableController: init()');
        }
    }
}
