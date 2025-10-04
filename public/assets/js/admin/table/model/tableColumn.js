import EventEmitter from '../helper/eventEmitter.js';

export default class TableColumn {
    #defaultVisibleColumns;
    #visibleColumns;
    #eventEmitter;

    constructor() {
        this.#eventEmitter = new EventEmitter();
        this.#visibleColumns = {};

        this.#init();
    }

    #init() {
        this.#defaultVisibleColumns = {
            id: true,
            title: true,
            description: true,
            created_at: true,
            updated_at: true
        };

        this.#setStateToCheckboxes(this.#defaultVisibleColumns);
        this.#visibleColumns = this.#getInitialStateFromCheckboxes();
        this.#bindEvents();
        this.#updateTable()
    }

    #setStateToCheckboxes(visibleColumns) {
        document.querySelectorAll('.form-check-input').forEach(input => {
            const key = input.dataset.column;
            input.checked = visibleColumns[key];
        });
    }

    #getInitialStateFromCheckboxes() {
        const state = {};
        document.querySelectorAll('.form-check-input').forEach(input => {
            const key = input.dataset.column;
            state[key] = input.checked;
        });
        return state;
    }

    #bindEvents() {
        document.querySelectorAll('.form-check-input').forEach(input => {
            input.addEventListener('change', (event) => {
                const key = input.dataset.column;
                this.#toggleColumn(key, event.target.checked);
            });
        });
    }

    #toggleColumn(columnKey, isVisible) {
        this.#visibleColumns[columnKey] = isVisible;
        this.#updateTable();
        this.#eventEmitter.emit('columnsChanged', this.#visibleColumns);
    }

    #updateTable() {
        for (const [key, isVisible] of Object.entries(this.#visibleColumns)) {
            document.querySelectorAll(`[data-column="${key}"]`).forEach(element => {
                if (element.tagName === 'INPUT' && element.type === 'checkbox') {
                    return;
                }
                if (isVisible) {
                    element.classList.remove('hidden');
                } else {
                    element.classList.add('hidden');
                }
            });
        }
    }

    get visibleColumns() {
        return this.#visibleColumns;
    }

    addListener(event, callback) {
        this.#eventEmitter.addListener(event, callback);
    }
}
