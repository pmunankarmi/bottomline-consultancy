(function ($) {
    'use strict';

    $(function () {
        const form = $('#contactForm');
        if (!form.length || !$.fn.validate) {
            return;
        }

        const rules = {};
        const messages = {};
        const requiredFields = ['firstName', 'lastName', 'email', 'message'];

        ['firstName', 'lastName', 'email', 'company', 'phone', 'interest', 'message'].forEach(function (name) {
            rules[name] = {
                required: requiredFields.includes(name),
                maxlength: name === 'message' ? 10000 : 254,
                normalizer: function (value) {
                    return value.trim();
                }
            };
            messages[name] = {
                required: blContactValidation.required,
                maxlength: $.validator.format(blContactValidation.maxlength),
                email: blContactValidation.email
            };
        });
        rules.email.email = true;

        form.validate({
            rules: rules,
            messages: messages,
            errorElement: 'span',
            errorClass: 'bl-field-error',
            focusInvalid: true,
            errorPlacement: function (error, element) {
                error.insertAfter(element);
            },
            highlight: function (element) {
                $(element).addClass('bl-input-invalid').attr('aria-invalid', 'true');
            },
            unhighlight: function (element) {
                $(element).removeClass('bl-input-invalid').attr('aria-invalid', 'false');
            }
        });
    });
})(jQuery);
