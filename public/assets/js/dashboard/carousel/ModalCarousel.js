import BaseGallery from './BaseGallery.js';

export default class ModalCarousel extends BaseGallery {
    constructor(modalId, images) {
        super(modalId, images);

        this.imageElement = document.getElementById(`carouselImage_${modalId}`);
        this.counterElement = document.getElementById(`carouselCounter_${modalId}`);
        this.prevButton = document.getElementById(`carouselPrev_${modalId}`);
        this.nextButton = document.getElementById(`carouselNext_${modalId}`);
        this.editLink = document.getElementById(`carouselEditLink_${modalId}`);
        this.statusIndicator = document.getElementById(`carouselStatusIndicator_${modalId}`);

        this.title = document.getElementById(`carouselTitle_${modalId}`);
        this.description = document.getElementById(`carouselDescription_${modalId}`);

        this.init();
    }
}
