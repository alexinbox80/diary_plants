export default class Diary {
    #cellId;
    #plantId;
    #date;
    #usableId;
    #usableType;
    #baseId = null;

    constructor({cellId, plantId, date, usableId, usableType, baseId = null}) {
        this.#cellId = cellId;
        this.#plantId = plantId;
        this.#date = date;
        this.#usableId = usableId;
        this.#usableType = usableType;
        this.#baseId = baseId;
    }

    get cellId() { return this.#cellId; }

    get plantId() { return this.#plantId; }

    get date() { return this.#date; }

    get usableId() { return this.#usableId; }

    get usableType() { return this.#usableType; }

    get baseId() { return this.#baseId; }

    get get() {
        return {
            cellId: this.#cellId,
            plantId: this.#plantId,
            date: this.#date,
            usableId: this.#usableId,
            usableType: this.#usableType,
            baseId: this.#baseId,
        };
    }
}
