import EventEmitter from '../../infrastructure/eventEmitter.js';

/**
 * Класс для управления событиями сортировки
 */
export default class SortManager extends EventEmitter {
    #currentSortKey;
    #currentDirection;

    constructor() {
        super();
        this.#currentSortKey = null;
        this.#currentDirection = 'asc';
    }

    /**
     * Сортирует по указанному ключу и направлению
     * @param {string} key - ключ колонки
     */
    sort(key) {
        if (key === this.#currentSortKey) {
            this.#currentDirection = this.#currentDirection === 'asc' ? 'desc' : 'asc';
        } else {
            this.#currentSortKey = key;
            this.#currentDirection = 'asc';
        }

        this.emit('sort', { key: this.#currentSortKey, direction: this.#currentDirection });
    }

    get currentSort() {
        return { key: this.#currentSortKey, direction: this.#currentDirection };
    }
}
