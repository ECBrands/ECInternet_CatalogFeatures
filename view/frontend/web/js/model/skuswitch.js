/*jshint browser:true jquery:true*/
/*global alert*/
define([
    'jquery',
    'mage/utils/wrapper'
], function ($, wrapper) {
    'use strict';

    return function (targetModule) {
        var reloadPrice = targetModule.prototype._reloadPrice;
        var reloadPriceWrapper = wrapper.wrap(reloadPrice, function (original) {
            // Call original method
            var result = original();

            // Extract sku
            var simpleSku = this.options.spConfig.skus[this.simpleProduct];
            if (simpleSku !== '') {
                $('div.product-info-main .sku .value').html(simpleSku);
            }

            // Extract name
            var simpleName = this.options.spConfig.names[this.simpleProduct];
            if (simpleName !== '') {
                $('h1.page-title .base').html(simpleName);
            }

            // Return original value
            return result;
        });

        targetModule.prototype._reloadPrice = reloadPriceWrapper;

        return targetModule;
    };
});