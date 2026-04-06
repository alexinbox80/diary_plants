export default class date {
    /**
     * Получает дату
     * @returns string YYYY-MM-DD
     */
    static getDate(year = 2025, month = 1, day = 1) {
        return [this.#year(year), this.#month(month), this.#day(day)].join('-');
    }

    static #day(day) {
        day = '' + day;
        if (day.length < 2)
            return '0' + day;

        return day;
    }

    static #month(month) {
        month = '' + month;
        if (month.length < 2)
            return '0' + month;

        return month;
    }

    static #year(year) {
        return year;
    }
}
