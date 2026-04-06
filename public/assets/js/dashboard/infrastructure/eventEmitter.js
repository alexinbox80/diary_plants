import configure from '../diary/config/configure.js';

export default class EventEmitter {
    // Объект который, хранит обработчики событий
    #listeners;

    constructor() {
        this.#listeners = {};
    }

    /**
     * Добавляет обработчик события
     * @param {string} type - тип события
     * @param {function} callback - функция-обработчик
     */
    addListener(type, callback) {
        if (typeof type !== 'string') {
            throw new Error('Event type must be a string');
        }

        if (typeof callback !== 'function') {
            throw new Error('Callback must be a function');
        }

        if (this.#listeners[type]) {
            this.#listeners[type].push(callback);
        } else {
            this.#listeners[type] = [callback];
        }

        if (configure.debug) {
            console.log('addListener(): ', this.#listeners);
        }

    }

    /**
     * Вызывает все обработчики события
     * @param {string} type - тип события
     * @param {*} [params] - параметры для передачи в обработчики
     */
    emit(type, ...params) {
        if (!this.#listeners[type]) {
            if (configure.debug) {
                console.log(`emit(): no one subscribed to the event "${type}".`);
            }
            return;
        }

        this.#listeners[type].forEach(callback => {
            if (typeof callback !== 'function') {
                throw new Error('callback is not a function');
            }
            callback(...params);
        });

        if (configure.debug) {
            console.log('emit(): ', this.#listeners);
        }
    };

    /**
     * Удаляет конкретный обработчик события
     * @param {string} type - тип события
     * @param {function} callback - функция-обработчик
     */
    removeListener(type, callback) {
        if (!this.#listeners[type]) return;

        this.#listeners[type] = this
            .#listeners[type]
            .filter(
                cb => cb !== callback
            );
    };

    /**
     * Подписывается на событие только один раз
     * @param {string} type - тип события
     * @param {function} callback - функция-обработчик
     */
    once(type, callback) {
        const onceCallback = (...args) => {
            callback(...args);
            this.removeListener(type, onceCallback);
        };

        this.addListener(type, onceCallback);
    };

    /**
     * Проверяет, есть ли обработчики для указанного события
     * @param {string} type - тип события
     * @returns {boolean}
     */
    has(type) {
        return !!this.#listeners[type] && this.#listeners[type].length > 0;
    };

    /**
     * Очищает все события и их обработчики
     */
    clear() {
        this.#listeners = {};
    };

    /**
     * Возвращает список обработчиков для указанного события
     * @param {string} type - тип события
     * @returns {Array<function>}
     */
    getListeners(type) {
        return this.#listeners[type] || [];
    }
}
