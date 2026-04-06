export default {
    createItem(context, marker, baseId = null) {
        let span = context.querySelector('span');
        if (!span) {
            span = document.createElement('span');
            context.appendChild(span);
        }

        span.style.backgroundColor = marker.color;
        span.textContent = marker.letter;
        span.classList.add('marker-cell');
        span.setAttribute('title', marker.description);

        if (baseId) {
            span.setAttribute('data-base-id', baseId);
        } else {
            span.removeAttribute('data-base-id');
        }
    },

    removeItem(context, marker) {
        // Логика удаления: если ID маркера 0 (X), очищаем ячейку
        if (parseInt(marker.markerId, 10) === 0) {
            this.removeAll(context);
        }
    },

    removeAll(context) {
        context.textContent = '';
        context.style.backgroundColor = '';
    }
}
