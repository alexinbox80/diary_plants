import configure from '../config/configure.js';
import http from '../../infrastructure/dataHandler.js';

const API_URL = `${configure.url}/api/v1/dashboard`;

export default {
    async getAll(year, month) {
        const url = `${API_URL}/usages/${year}/${month}`;

        return await http.get(
            (err) => console.error('Ошибка получения маркеров: ', err),
            url
        );
    },

    async saveAll(diaryDataArray) {
        const url = `${API_URL}/usages/create`;

        return await http.create(
            (err) => console.error('Ошибка пакетного сохранения: ', err),
            url,
            diaryDataArray
        );
    },

    async saveEntry(data) {
        return await http.create(
            (err) => console.error('Ошибка сохранения: ', err),
            API_URL,
            data
        );
    },

    async removeEntry(ids) {
        // Обычно для удаления передается ID или cellId в URL или body
        return await http.delete(
            (err) => console.error('Ошибка удаления: ', err),
            `${API_URL}/usages/remove`,
            ids
        );
    }
}
