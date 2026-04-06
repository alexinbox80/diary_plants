import configure from '../config/configure.js';

export default class diaryService {
    #markersByType = {};
    #activeMarkers = {
        watering: null,
        pest: null,
        fertilize: null,
        stimulant: null
    };

    constructor() {
        this.#initMarkersFromDom();
    }

    #initMarkersFromDom() {
        const markerInputs = document.querySelectorAll('input.marker-pointer');

        markerInputs.forEach(input => {
            const markerId = parseInt(input.dataset.id, 10);

            // Условие: не загружаем маркеры с id = 0
            if (markerId === 0) return;

            // Определяем тип (имя вкладки)
            const tabPane = input.closest('.tab-pane');
            const type = tabPane ? tabPane.id : input.name;

            // Если такой группы еще нет в объекте — создаем массив
            if (!this.#markersByType[type]) {
                this.#markersByType[type] = [];
            }

            this.#markersByType[type].push({
                markerId: markerId,
                letter: input.value,
                color: input.dataset.color,
                type: type,
                description: input.title
            });
        });

        if (configure.debug) {
            console.log('Загруженные маркеры:', this.#markersByType);
        }
    }

    /**
     * Поиск маркера, когда известны и ID, и тип (например, "watering")
     * @param {number|string} id
     * @param {string} type
     */
    getMarkerByIdAndType(id, type) {
        const markerId = parseInt(id, 10);
        const group = this.#markersByType[type];

        if (!group) return null;

        return group.find(m => m.markerId === markerId) || null;
    }

    setActiveMarker(marker) {
        this.#activeMarkers[marker.type] = marker;
    }

    getMarkerForType(type) {
        return this.#activeMarkers[type];
    }

    getTypeName(type) {
        const names = {
            watering: 'Полив',
            pest: 'Вредители',
            stimulant: 'Стимуляторы',
            fertilizer: 'Удобрения'
        };
        return names[type] || type;
    }
}
