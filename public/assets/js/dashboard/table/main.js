import TableController from './controller/tableController.js';

const scriptTag = document.querySelector('script[data-entity]');
if (!scriptTag) {
    console.error('Script tag with [data-entity] attribute not found!');
}

const entity = scriptTag.dataset.entity;

const tableController = new TableController(entity);
tableController.init();
