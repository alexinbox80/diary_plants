import TableController from './controller/tableController.js';

const scriptTag = document.querySelector('script[data-entity]');
const entity = scriptTag.dataset.entity;

const tableController = new TableController(entity);
tableController.init();
