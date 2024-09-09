define([
    'jquery',
    'uiComponent',
    'underscore',
    'Magento_Customer/js/customer-data',
    'mage/translate',
], function ($, Component, _, customerData) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Magenest_FullPageCache/gender'
        },
        customerId: '',
        customerLoggedIn: true,

        /**
         * @return {Object}
         */
        initialize: function (args) {
            this._super();
            this.genderId = args.genderId;
            this.isLoggedIn();
            return this;
        },

        getGender: function (genderId) {
            if (!genderId) {
                genderId = customerData.get('customer')().genderId;
            }
            switch (parseInt(genderId)) {
                case 1:
                    return 'male';
                case 2:
                    return 'female';
                default:
                    return 'unknown';
            }
        },

        isLoggedIn: function () {
            let firstname = customerData.get('customer')().firstname;
            this.customerLoggedIn = this.customerId !== '' || typeof (firstname) !== "undefined";
        }
    });
});
