import Marker from '../model/Marker.js';

export default class markerFactory {
    static createFromInput(input) {
        if (!input || !input.value) return null;

        return new Marker({
            markerId: parseInt(input.dataset.id, 10),
            letter: input.value,
            description: input.title,
            color: input.dataset.color,
            type: input.name
        });
    }
}
