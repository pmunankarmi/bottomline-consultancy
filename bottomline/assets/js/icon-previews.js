(function ($) {
    'use strict';

    const selector = '.acf-field[data-name="icon"] select, .acf-field[data-name="icon_2"] select';

    function renderPreview(select) {
        const field = select.closest('.acf-field');
        if (!field.dataset.key.startsWith('field_bl_')) {
            return;
        }
        let preview = field.querySelector('.bl-icon-preview');
        if (!preview) {
            preview = document.createElement('span');
            preview.className = 'bl-icon-preview';
            preview.setAttribute('aria-hidden', 'true');
            select.closest('.acf-input').appendChild(preview);
        }
        // Markup comes only from the theme's fixed SVG collection.
        preview.innerHTML = blIconPreviews[select.value] || '';
        preview.hidden = !preview.innerHTML;
    }

    function refreshPreviews() {
        document.querySelectorAll(selector).forEach(renderPreview);
    }

    $(document).on('change', selector, function () {
        renderPreview(this);
    });
    $(refreshPreviews);
    if (window.acf) {
        acf.addAction('append', refreshPreviews);
    }
})(jQuery);
