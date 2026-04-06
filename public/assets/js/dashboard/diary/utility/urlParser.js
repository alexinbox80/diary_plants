export default class urlParser {
    /**
     * Получает параметры даты из текущего URL /year/month
     * @returns {{year: number, month: number}}
     */
    static getDiaryParams() {
        const pathParts = window.location.pathname.split('/');

        const year = pathParts[pathParts.length - 2];
        const month = pathParts[pathParts.length - 1];

        if (this.#isValidDate(year, month)) {
            return { year: parseInt(year), month: parseInt(month) };
        }

        // Если в URL нет даты (например, /dashboard/diaries), возвращаем "сегодня"
        const now = new Date();
        return {
            year: now.getFullYear(),
            month: now.getMonth() + 1
        };
    }

    static #isValidDate(y, m) {
        // Простая проверка: год > 2000, месяц от 1 до 12
        return !isNaN(y) && !isNaN(m) && y > 2000 && m >= 1 && m <= 12;
    }
}
