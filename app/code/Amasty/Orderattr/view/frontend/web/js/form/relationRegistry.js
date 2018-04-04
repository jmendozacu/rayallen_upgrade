define([
    'ko'
], function (ko) {
    'use strict';

    /**
     * @abstract
     */
    return {
        dependsToShow: [],
        clear: function () {
            this.dependsToShow = [];
        },
        add: function (relationIndex) {
            this.dependsToShow.push(relationIndex);
        },
        get: function () {
            return this.dependsToShow;
        },
        isExist: function (relationIndex) {
            return this.dependsToShow.indexOf(relationIndex) >= 0;
        }
    };
});