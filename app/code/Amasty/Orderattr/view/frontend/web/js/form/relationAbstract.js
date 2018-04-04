define([
    'ko',
    'underscore',
    'uiRegistry',
    'Amasty_Orderattr/js/form/relationRegistry'
], function (ko, _, registry, relationRegistry) {
    'use strict';

    /**
     * @abstract
     */
    return {
        /**
         * @param {Object[]} relations
         * @param {string} relations[].attribute_name - element name of parent attribute
         * @param {string} relations[].dependent_name - element name of depend attribute
         * @param {string} relations[].option_value   - value which Parent should have to show Depend
         */
        relations: {},

        /**
         * check attribute dependencies on value change
         */
        onUpdate: function () {
            this._super();
            this.initCheck();
        },

        /**
         * run check dependency and clear relations
         */
        initCheck: function () {
            if (this.relations && this.relations.length) {
                relationRegistry.clear();
                this.checkDependencies();
            }
        },

        checkDependencies: function () {
            if (this.relations && this.relations.length) {
                var fieldset = registry.get(this.parentName);
                var localRegitry = registry;
                var localRelationRegistry = relationRegistry;
                this.relations.map(function (relation) {
                    var dependElement = fieldset.getChild(relation.dependent_name);
                    if (!dependElement) {
                        // get element by full name if in fieldset element is not ready
                        var elementFullNme = fieldset.name + '.' + relation.dependent_name;
                        dependElement = localRegitry.get(elementFullNme);
                    }
                    if (dependElement) {
                        dependElement.hidedByDepend = false;
                        if (!localRelationRegistry.isExist(relation.dependent_name)) {
                            if (this.isCanShow(relation)) {
                                this.showDepend(dependElement);
                            } else {
                                /** hide element only if no relation rules to show. On one check */
                                this.hideDepend(dependElement);
                            }
                        }
                    }
                }.bind(this));
            }
        },

        /**
         * Is element value eq relation value
         *
         * @param relation
         * @returns {boolean}
         */
        isCanShow: function (relation) {
            return (this.value() == relation.option_value && this.visible());
        },
        showDepend: function (dependElement) {
            if (dependElement.hidedByDepend && dependElement.hidedByDepend != this.index) {
                return;
            }
            dependElement.hidedByDepend = false;
            if (dependElement.hidedByRate) {
                return false;
            }
            dependElement.show();
            relationRegistry.add(dependElement.index);
            if (_.isFunction(dependElement.checkDependencies)) {
                dependElement.checkDependencies();
            }
        },
        hideDepend: function (dependElement) {
            dependElement.hidedByDepend = this.index;
            dependElement.hide();
            dependElement.value(void(0));
            if (_.isFunction(dependElement.checkDependencies)) {
                dependElement.checkDependencies();
            }
        }
    };
});
