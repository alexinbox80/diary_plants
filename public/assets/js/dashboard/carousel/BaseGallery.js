export default class BaseGallery {
    /**
     * Базовый класс для галерей
     * @param {string} galleryId - Уникальный идентификатор галереи
     * @param {Array} images - Массив изображений
     */
    constructor(galleryId, images) {
        this.galleryId = galleryId;
        this.images = images;
        this.currentIndex = 0;

        // Элементы будут инициализированы в подклассах
        this.imageElement = null;
        this.counterElement = null;
        this.prevButton = null;
        this.nextButton = null;
        this.editLink = null;
        this.statusIndicator = null;
    }

    /**
     * Инициализация галереи — должен быть вызван в подклассе
     */
    init() {
        if (!this.areElementsReady()) {
            //console.warn(`Gallery elements not found for galleryId: ${this.galleryId}`);
            return;
        }

        this.updateImage(0);

        this.prevButton.addEventListener('click', () => {
            this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
            this.updateImage(this.currentIndex);
        });

        this.nextButton.addEventListener('click', () => {
            this.currentIndex = (this.currentIndex + 1) % this.images.length;
            this.updateImage(this.currentIndex);
        });
    }

    /**
     * Проверка, что все необходимые DOM-элементы найдены
     * @returns {boolean}
     */
    areElementsReady() {
        return this.imageElement &&
            this.counterElement &&
            this.prevButton &&
            this.nextButton;
    }

    /**
     * Обновление отображаемого изображения
     * @param {number} index
     */
    updateImage(index) {
        const img = this.images[index];
        if (!img || !img.path || !img.filename) {
            console.error('Invalid image data:', img);
            return;
        }

        this.imageElement.src = `/uploads/${img.path}${img.filename}`;
        this.imageElement.alt = img.alt || 'Изображение';
        this.imageElement.dataset.currentIndex = index;

        this.counterElement.textContent = `${index + 1} / ${this.images.length}`;

        if (this.editLink) {
            this.editLink.href = `/dashboard/images/${img.id}/edit`;
        }

        if (this.statusIndicator) {
            this.statusIndicator.style.backgroundColor = img.is_shown ? '#28a745' : '#dc3545';
            this.statusIndicator.title = img.is_shown ? 'Опубликовано' : 'Черновик';
        }
    }
}
