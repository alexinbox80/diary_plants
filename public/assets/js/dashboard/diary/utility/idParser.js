export default class idParser {
    /**
     * Разбирает строку вида "watering-4510" на составляющие
     * @param {string} cellId
     * @returns {{type: string, plantId: number, day: number}}
     */
    static parse(cellId) {
        // 1. Разделяем по дефису: ["watering", "4510"]
        const [type, numericPart] = cellId.split('-');

        if (!numericPart) return null;

        // 2. Последние 2 символа — это всегда день (например, "10")
        const dayStr = numericPart.slice(-2);

        // 3. Всё, что до последних 2 символов — это plantId (например, "45")
        const plantIdStr = numericPart.slice(0, -2);

        return {
            type: type,                    // "watering"
            plantId: parseInt(plantIdStr), // 45
            day: parseInt(dayStr)          // 10
        };
    }
}
