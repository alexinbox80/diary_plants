import configure from '../config/configure.js';

export default class MenuController {
    #shortMenu = false;
    #shortMenuFlagName = 'shortMenu';

    constructor() {
        this.#setupListeners();
        this.#shortMenu = this.#restoreFromLocalStorage(this.#shortMenuFlagName) === 'true';

        if (this.#shortMenu === true) {
            document.body.classList.add('sidebar-icon-only');
        }
    }

    init() {
        if (configure.debug) {
            console.log('MenuController: init()');
        }
    }

    #emitEvent() {
        if (configure.debug) {
            console.log('Call emit Event!');
        }

        if (!this.#shortMenu) {
            this.#saveToLocalStorage(this.#shortMenuFlagName, true);
            this.#shortMenu = true;
        } else {
            document.body.classList.remove('sidebar-icon-only');
            this.#removeFromLocalStorage(this.#shortMenuFlagName);
            this.#shortMenu = false;
        }
    }

    #setupListeners() {
        const element = document.querySelector('#main-sidebar-menu');

        element.addEventListener('click', (event) => {
            if (configure.debug) {
                console.log('Элемент с id main-sidebar-menu был нажат!', event.target);
            }

            this.#emitEvent();
        });
    }

    #saveToLocalStorage(key, value) {
        try {
            localStorage.setItem(key, value);
            if (configure.debug) {
                console.log(`Saved to localStorage: ${key} = ${value}`);
            }
        } catch (error) {
            console.error('Failed to save to localStorage:', error);
        }
    }

    #restoreFromLocalStorage(data) {
        return localStorage.getItem(data);
    }

    #removeFromLocalStorage(data) {
        localStorage.removeItem(data);
    }
}
