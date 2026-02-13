export default class DeleteModel {
    /**
     * Класс для управления модели через модальное окном подтверждения удаления
     * @param {string} routeDelete - Базовый маршрут для удаления (например, 'dashboard.offspring.delete')
     */
    constructor(routeDelete) {
        this.routeDelete = routeDelete;
        this.modal = document.getElementById('confirmDeleteModal');
        this.titleSpan = document.getElementById('deleteItemTitle');
        this.form = document.getElementById('deleteForm');
        this.tokenInput = this.form.querySelector('input[name="_token"]');
        this.baseUrl = this.generateBaseUrl();

        if (!this.modal || !this.form || !this.titleSpan) {
            console.error('Не найдены необходимые DOM-элементы для DeleteModal');
            return;
        }

        this.init();
    }

    /**
     * Генерация базового URL на основе маршрута
     * @returns {string}
     */
    generateBaseUrl() {
        // В Twig мы не можем использовать path() напрямую, поэтому ожидаем, что URL будет передан или сгенерирован
        // Здесь можно использовать заглушку, но лучше передавать baseRoute извне
        // Пример: /dashboard/offspring/__ID__/delete
        return this.routeDelete.replace(/^([a-z]+\.[a-z]+)\.delete$/i, '/$1/__ID__/delete').replace(/\./g, '/');
    }

    /**
     * Инициализация обработчиков событий
     */
    init() {
        this.modal.addEventListener('show.bs.modal', (event) => {
            this.handleShow(event);
        });
    }

    /**
     * Обработка открытия модального окна
     * @param {Event} event
     */
    handleShow(event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const title = button.getAttribute('data-title');
        const token = button.getAttribute('data-token');

        this.titleSpan.textContent = title;
        this.form.action = this.baseUrl.replace('__ID__', id);
        this.tokenInput.value = token;
    }
}
