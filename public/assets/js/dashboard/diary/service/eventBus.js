import EventEmitter from '../../infrastructure/eventEmitter.js';

// Создаем ОДИН экземпляр на всё приложение
const eventBus = new EventEmitter();

// Экспортируем его, чтобы все файлы использовали один и тот же объект
export { eventBus };
