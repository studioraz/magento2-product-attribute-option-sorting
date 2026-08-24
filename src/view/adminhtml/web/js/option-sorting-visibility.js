define([
    'jquery'
], function ($) {
    'use strict';

    return function (config) {
        var field = $('#' + config.fieldId),
            frontendInput = $('#frontend_input'),
            supportedTypes = config.supportedTypes || [],
            manualValue = config.manualValue || 'manual',
            fieldRow = field.closest('.admin__field, .field');

        function toggleField() {
            var isSupported = supportedTypes.indexOf(frontendInput.val()) !== -1;

            fieldRow.toggle(isSupported);
            if (!isSupported) {
                field.val(manualValue);
            }
        }

        if (!field.length || !frontendInput.length) {
            return;
        }

        frontendInput.on('change', toggleField);
        toggleField();
    };
});
