define([
    'Magento_Ui/js/grid/columns/select'
], function (Select) {
    'use strict';

    return Select.extend({
        defaults: {
            bodyTmpl: 'ui/grid/cells/html'
        },

        getLabel: function (record) {
            console.log("Original Value: ", this._super());
            var value = this._super();
            var displayValue = parseInt(value) === 1 ? 'Yes' : 'No';
            console.log("Display Value: ", displayValue);
            return displayValue;
        }

    });
});
