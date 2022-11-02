/**
 * Mage Tracer.
 *
 * @category  Magetracer
 * @package   Magetracer_StoreOptimization
 * @author    Magetracer
 * @copyright Copyright (c) Mage Tracer (https://www.magetracer.com)
 * @license   https://www.magetracer.com/license.html
 */
define([
    'jquery',
    'Magetracer_StoreOptimization/js/jquery.lazyload'
], function ($) {

    return function (options) {
        $(function () {
            $("img.wk_lazy.new-lazy").lazyload();

            $("img.wk_lazy").one("appear", function () {
                $(this).removeClass('new-lazy')
            });
        });
    };
});