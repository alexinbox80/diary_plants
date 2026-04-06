export default class Marker {
    #markerId;
    #letter;
    #description;
    #color;
    #type;

    constructor({markerId, letter, description, color, type}) {
        this.#markerId = markerId;
        this.#letter = letter;
        this.#description = description;
        this.#color = color;
        this.#type = type;
    }

    get markerId() { return this.#markerId; }

    get letter() { return this.#letter; }

    get description() { return this.#description; }

    get color() { return this.#color; }

    get type() { return this.#type; }

    get get() {
        return {
            markerId: this.markerId,
            letter: this.letter,
            description: this.description,
            color: this.color,
            type: this.type
        }
    }
}
