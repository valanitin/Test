/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

define([
    'jquery',
    'underscore',
    'jquery/ui',
    'jquery/jquery.parsequery',
    'Magento_Swatches/js/swatch-renderer'
], function ($, _) {
    'use strict';

    $.widget('tigren.SwatchRenderer', $.mage.SwatchRenderer, {
        /**
         * Render controls
         *
         * @private
         */
        _RenderControls: function () {
            var $widget = this,
                container = this.element,
                classes = this.options.classes,
                chooseText = this.options.jsonConfig.chooseText,
                showTooltip = this.options.showTooltip;

            $widget.optionsMap = {};

            $.each(this.options.jsonConfig.attributes, function () {
                var item = this,
                    controlLabelId = 'option-label-' + item.code + '-' + item.id,
                    options = $widget._RenderSwatchOptions(item, controlLabelId),
                    select = $widget._RenderSwatchSelect(item, chooseText),
                    input = $widget._RenderFormInput(item),
                    listLabel = '',
                    label = '';

                // Show only swatch controls
                if ($widget.options.onlySwatches && !$widget.options.jsonSwatchConfig.hasOwnProperty(item.id)) {
                    return;
                }

                if ($widget.options.enableControlLabel) {
                    label +=
                        '<span id="' + controlLabelId + '" class="' + classes.attributeLabelClass + '">' +
                        $('<i></i>').text(item.label).html() +
                        '</span>' +
                        '<span class="' + classes.attributeSelectedOptionLabelClass + '"></span>';
                }

                if ($widget.productForm) {
                    var attributeSelector = '.swatch-attribute ' + '.' + item.code,
                        attributeElm = $widget.productForm.find(attributeSelector);
                    if (attributeElm.length !== 0) {
                        attributeElm.after(input);
                        input = '';
                    }
                }

                // Create new control
                container.append(
                    '<div class="' + classes.attributeClass + ' ' + item.code + '" ' +
                         'data-attribute-code="' + item.code + '" ' +
                         'data-attribute-id="' + item.id + '">' +
                    label +
                        '<div aria-activedescendant="" ' +
                             'tabindex="0" ' +
                             'aria-invalid="false" ' +
                             'aria-required="true" ' +
                             'role="listbox" ' + listLabel +
                             'class="' + classes.attributeOptionsWrapper + ' clearfix">' +
                    options + select +
                    '</div>' + input +
                    '</div>'
                );

                $widget.optionsMap[item.id] = {};

                // Aggregate options array to hash (key => value)
                $.each(item.options, function () {
                    if (this.products.length > 0) {
                        $widget.optionsMap[item.id][this.id] = {
                            price: parseInt(
                                $widget.options.jsonConfig.optionPrices[this.products[0]].finalPrice.amount,
                                10
                            ),
                            products: this.products
                        };
                    }
                });
            });

            if (showTooltip === 1) {
            // Connect Tooltip
            container
                    .find('[data-option-type="1"], [data-option-type="2"],' +
                        ' [data-option-type="0"], [data-option-type="3"]')
                .SwatchRendererTooltip();
            }

            // Hide all elements below more button
            $('.' + classes.moreButton).nextAll().hide();

            // Handle events like click or change
            $widget._EventListener();

            // Rewind options
            $widget._Rewind(container);

            //Emulate click on all swatches from Request
            $widget._EmulateSelected($.parseQuery());
            $widget._EmulateSelected($widget._getSelectedAttributes());
        },


        _determineProductData: function () {
            var isInProductView;
            if (this.element.parents('#mb-ajaxsuite-popup-wrapper').length) {

                isInProductView = false;

            }

            return {
                isInProductView: isInProductView
            };
        },
        processUpdateBaseImage: function (images, context, isInProductView, gallery) {
            var justAnImage = images[0],
                initialImages = this.options.mediaGalleryInitial,
                imagesToUpdate,
                isInitial;

            if (isInProductView & !this.element.parents('#mb-ajaxsuite-popup-wrapper').length) {
                imagesToUpdate = images.length ? this._setImageType($.extend(true, [], images)) : [];
                isInitial = _.isEqual(imagesToUpdate, initialImages);

                if (this.options.gallerySwitchStrategy === 'prepend' && !isInitial) {
                    imagesToUpdate = imagesToUpdate.concat(initialImages);
                }

                imagesToUpdate = this._setImageIndex(imagesToUpdate);
                gallery.updateData(imagesToUpdate);

                if (isInitial) {
                    $(this.options.mediaGallerySelector).AddFotoramaVideoEvents();
                } else {
                    $(this.options.mediaGallerySelector).AddFotoramaVideoEvents({
                        selectedOption: this.getProduct(),
                        dataMergeStrategy: this.options.gallerySwitchStrategy
                    });
                }

                gallery.first();

            } else if (justAnImage && justAnImage.img) {
                context.find('.product-image-photo').attr('src', justAnImage.img);
            }
        },
    });

    return $.tigren.SwatchRenderer;
});
