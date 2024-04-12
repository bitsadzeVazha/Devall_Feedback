define([
    'ko',
    'jquery',
    'uiComponent',
    'Magento_Ui/js/modal/alert'
], function (ko, $, Component, alert) {
    'use strict';

    return Component.extend({
        defaults: {
            title: '',
            feedback: '',
            submitAnonymously: false,
            productId: window.currentProductId || 0,
            formKey: window.formKey || '',
            template: 'Devall_CustomerFeedback/feedback-form',
            isFormVisible: false
        },

        initialize: function () {
            this._super();
            this.title = ko.observable(this.title);
            this.feedback = ko.observable(this.feedback);
            this.submitAnonymously = ko.observable(this.submitAnonymously);
            this.isFormVisible = ko.observable(this.isFormVisible);
            return this;
        },

        toggleForm: function() {
            this.isFormVisible(!this.isFormVisible());
        },

        submitFeedback: function () {
            if (!this.title() || !this.feedback()) {
                alert({
                    content: 'Please fill in all required fields!'
                });
                return false;
            }

            var formData = {
                title: this.title(),
                feedback: this.feedback(),
                product_id: this.productId,
                submitAnonymously: this.submitAnonymously(),
                form_key: this.formKey
            };

            $.ajax({
                url: '/feedback/submit/save',
                type: 'POST',
                dataType: 'json',
                data: formData,
                showLoader: true,
                success: function (response) {
                    if (response.success) {
                        alert({
                            content: response.message
                        });
                        this.title('');
                        this.feedback('');
                        this.submitAnonymously(false);
                    } else {
                        alert({
                            content: response.message
                        });
                    }
                }.bind(this),
                error: function () {
                    alert({
                        content: 'There was an error submitting your feedback. Please try again.'
                    });
                }
            });

            return false;
        }
    });
});
