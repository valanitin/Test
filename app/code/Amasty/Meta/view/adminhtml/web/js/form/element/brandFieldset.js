define([
    'jquery',
    'uiRegistry',
    'Magento_Ui/js/form/components/fieldset',
    'Magento_Catalog/js/components/visible-on-option/strategy'
], function ($, uiRegistry, Fieldset, strategy) {
    'use strict';

    return Fieldset.extend(strategy).extend(
        {
            initialize: function (){
                this._super();
                var self = this;
                uiRegistry.promise('config_edit_form.config_edit_form.main_category.category_id').done(function(component) {
                    self.categoryChanged(component.initialValue);
                });

                return this;
            },

            categoryChanged: function (value) {
                var self = this;
                uiRegistry.promise('index = brand').done(function(component) {
                    component.visible(self.defaultCategories.includes(value) || value === '1');
                });
            }
        }
    );
});
