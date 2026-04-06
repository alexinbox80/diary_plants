import { eventBus } from './eventBus.js';
import MarkerFactory from '../factory/markerFactory.js';

export default class markerService {
    static handleSelection(input) {
        // Проверяем, что это именно радиокнопка и она выбрана
        if (input.type === 'radio' && input.checked) {

            // ВЫЗОВ ФАБРИКИ: передаем весь элемент
            const activeMarker = MarkerFactory.createFromInput(input);

            if (activeMarker) {
                // Уведомляем систему через EventEmitter
                eventBus.emit('marker:selected', activeMarker);
            }
        }
    }
}
