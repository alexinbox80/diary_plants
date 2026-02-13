import BaseGallery from './BaseGallery.js';

export default class ImageGallery extends BaseGallery {
    constructor(galleryId, images) {
        super(galleryId, images);

        this.imageElement = document.getElementById(`galleryImage_${galleryId}`);
        this.counterElement = document.getElementById(`galleryCounter_${galleryId}`);
        this.prevButton = document.getElementById(`galleryPrev_${galleryId}`);
        this.nextButton = document.getElementById(`galleryNext_${galleryId}`);
        this.editLink = document.getElementById(`editLink_${galleryId}`);
        this.statusIndicator = document.getElementById(`statusIndicator_${galleryId}`);

        this.init();
    }
}
