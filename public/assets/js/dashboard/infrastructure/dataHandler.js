const getCsrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

export default {
    async get(errorCallBack, url) {
        return await fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            }
        })
            .then((response) => {
                if (response.ok) {
                    return response.json();
                } else {
                    return errorCallBack(response.status);
                }
            })
            .then((data) => data)
            .catch((error) => {
                return errorCallBack(error);
            })
    },

    async create(errorCallBack, url, data) {
        return await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify(data)
        })
            .then((response) => {
                if (response.ok) {
                    return response.json()
                } else {
                    return errorCallBack(response.status);
                }
            })
            .catch((error) => {
                return errorCallBack(error);
            })
    },

    async delete(errorCallBack, url, ids) {
        return await fetch(url, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify({ids: ids})
        })
            .then((response) => {
                if (response.ok) {
                    return response.json()
                } else {
                    return errorCallBack(response.status);
                }
            })
            .catch((error) => {
                return errorCallBack(error);
            })
    }
}
