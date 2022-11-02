/**
  * Mage Tracer.
  *
  * @category Magetracer
  * @package Magetracer_StoreOptomization
  * @author Magetracer
  * @copyright Copyright (c) Mage Tracer (https://www.magetracer.com)
  * @license https://www.magetracer.com/license.html
  */
 define(
    [
        'jquery'
    ],
    function ($) {
        $.widget('magetracer.scroller',{
        options: {
            gridContainer: ".grid.products-grid .products.list.items.product-items",
            listContainer: ".list.products-list .products.list.items.product-items",
            observer: null,
            toolbarNumber: "#toolbar-amount .toolbar-number:eq(1)",
            isLazyLoadEnable: false
        },

        _create: function () {
            let that = this;
            let pagenumber = 0;
            let toolbar = that.getToolbar();
            toolbar.hide();
            let pagination = toolbar.find(".item").find("a.page");
            //console.log(pagination);
            let intersectionCallback = (entries, observer) => {
                //check if footer has intersected to trigger fetchProducts
                    if (entries[0].isIntersecting) {
                        fetchProducts();
                    }                    
            };

            //initialize intersection observer for infinite scroll event
            let myIntersectionObserver = () => {
                let options = {
                root: null,
                rootMargin: '0px',
                threshold: .05
            }
                let i = 0;
                that.observer = new IntersectionObserver(intersectionCallback);

                let target = document.querySelector(".page-footer");
                //if target hets into root element view .05% then trigger the 
                // intersectionCallback function
                that.observer.observe(target);
            
            };

            let fetchProducts = function () {
                let url = null;
                //find the next page item in the pager 
                for (let i = 0; i<pagination.length; i++) {

                    if (i == pagenumber) {
                        if ($(pagination[i]).attr("href")) {
                            url = $(pagination[i]).attr("href");
                            pagenumber++;
                            break;
                        }
                        
                    }
                }

                //if next page url exists then fetch next page products
                if (url) {
                    $.ajax({
                        url: url,
                        method: "get",
                        dataType: "html",
                        showLoader:true,
                        success: function (page) {
                            let container = that.getListContainer();
                            //append next page products on the page 
                            $(container).append(
                                $(page).find(container).html()
                            );
                            $(container).trigger("contentUpdated");
                            //update total count of products on the page in toolbar 
                            $(that.options.toolbarNumber).html(
                                $(container).find(".product-item").length
                            );
                            if (that.options.isLazyLoadEnable) {
                                $("img.wk_lazy.new-lazy").lazyload();

                                $("img.wk_lazy").one("appear", function () {
                                    $(this).removeClass('new-lazy')
                                });
                            }
                        }
                    });
                }
            };     
            // start observer
            myIntersectionObserver();
          
        },

        // get page mode grid or list
        getPageMode() {
            let queryString = window.location.search
            let urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has("product_list_mode")) {
                return "list";
            }

            return "grid";
        },

        // get page container of products for list or grid
        getListContainer: function () {
           
            if (this.getPageMode() == "list") {
                return this.options.listContainer;
            }

            return this.options.gridContainer;
        },

        //get tool bar object on the page
        getToolbar() {
            if (this.getPageMode() == 'list') {
                return $(".products.wrapper.list.products-list").next(".toolbar");
            }
            return $(".products.wrapper.grid.products-grid").next(".toolbar");
        }
    });

    return $.magetracer.scroller;
    }
);
