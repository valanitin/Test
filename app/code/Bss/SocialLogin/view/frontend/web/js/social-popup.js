define([
    'jquery',
    "mage/translate",
    'Bss_SocialLogin/js/jquery.fancybox.min',
    'mage/mage',
    "domReady!"
], function ($,customerData) {
    'use strict';
    $.widget('mage.SocialLogin', {
        options: {
            class_login:[],
            class_register:[],
            isPopup:'',
            formLoginUrl:'',
            forgotFormUrl:'',
            createFormUrl:'',
            recaptchaLogin:'',
            recaptchaForgot:'',
            recaptchaCreate:'',
            recaptchaSitekey:'',
            recaptchaTheme:'',
            recaptchaType:'',
            recaptchaSize:'',
            popuphtml:''
        },

        _create: function () {
            var $widget = this;
            $widget._EventListener();
        },

        _init: function () {
            this._RenderButton();
            this._ReplaceButton();
            this.options.popuphtml = $("#fancybox-popup-social-login").html();
        },
        _ReplaceButton: function () {
            if ($("#fancybox-popup-social-login").length && this.options.isPopup != '') {
                $('a[href*="customer/account/forgotpassword"]').addClass('social-login fogot-pw').attr('href', '#');
                $('a[href*="customer/account/login"]').addClass('social-login login').attr('href', '#');
                $('a[href*="customer/account/create"]').addClass('social-login create').attr('href', '#');
            }
        },
        
        _RenderButton: function () {
            var class_login = this.options.class_login;
            var class_register = this.options.class_register;
            if ($('#tab-login .social-login-popup').length) {
                $('#add_button_login_byclass').clone(true).appendTo('#tab-login .social-login-popup').css('display','block');
            }
            
            if ($('#tab-create .social-login-popup').length) {
                $('#add_button_register_byclass').clone(true).appendTo('#tab-create .social-login-popup').css('display','block');
            }

            if(typeof class_login != "undefined" && class_login != null && class_login.length > 0 ){
                    $.each(class_login, function(index, c_login) {                      
                        if ( $(c_login).length ) {
                            $('#add_button_login_byclass').clone(true).appendTo(c_login).css('display','block');
                        } else {
                            var addbutton_lg = setInterval(function(){
                                        if ( $(c_login).length ) {
                                            $('#add_button_login_byclass').clone(true).appendTo(c_login).css('display','block');
                                            clearInterval(addbutton_lg);
                                        }
                               },100)

                            setTimeout(function(){
                                    clearInterval(addbutton_lg);
                            },5000)
                        }
                    });
            }
            if(typeof class_register != "undefined" && class_register != null && class_register.length > 0){
                    $.each(class_register, function(index, c_register) {
                        if ( $(c_register).length ) {
                            $('#add_button_register_byclass').clone(true).appendTo(c_register).css('display','block');
                        }
                        else{
                            var addbutton_rg = setInterval(function(){
                                        if ( $(c_register).length ) {
                                            $('#add_button_register_byclass').clone(true).appendTo(c_register).css('display','block');
                                            clearInterval(addbutton_rg);
                                        }
                               },100)

                            setTimeout(function(){
                                    clearInterval(addbutton_rg);
                            },5000)
                        }
                    });
            }
        },

        _EventListener: function () {

            var $widget = this;

            $(document).on('click', 'a.social-login, .social-login-popup ul li', function (e) {
                e.preventDefault();
                return $widget._OnClick($(this));
            });

            $(document).on('click', '#bt-social-login', function () {
                return $widget._AjaxLogin($(this));
            });

            $(document).on('click', '#bt-social-create', function () {
                return $widget._AjaxCreate($(this));
            });
            
            $(document).on('click', '#bt-social-fogot', function () {
                return $widget._AjaxForgot($(this));
            });

            $(document).on('click','.showmore-button', function() {
                $(this).parents('div.sociallogin-buttons').find('ul li').show();
                $(this).parents('div.sociallogin-showmore').hide();
            });

            $(document).on('click','.sociallogin-button-click', function() {
                $widget._PopupSocial($(this).data('href'), $(this).data('width'), $(this).data('height'));
                return false;
            });

            $(document).bind('keypress', function(e){ 
                var code = e.keyCode || e.which;
                if (code === 13 && $('#social-login-popup').css('display') != 'none') {
                    var from_action = $('#social-login-popup .block-container.active').attr('id');
                    if (from_action == 'tab-login') {
                        return $widget._AjaxLogin($('#bt-social-login'));
                    }
                    else if (from_action == 'tab-create') {
                        return $widget._AjaxCreate($('#bt-social-create'));
                    }
                    else if (from_action == 'tab-forgot-pw') {
                        return $widget._AjaxForgot($('#bt-social-fogot'));
                    }
                }
            });
        },

        _PopupSocial: function (href,width,height) {
            var popup_window = null;
            if (!width) {
                width = 650;
            }

            if(!height) {
                height = 350;
            }

            var left = parseInt(($(window).width() - width) / 2);
            var top = parseInt(($(window).height() - height) / 2);

            var params = [
                'resizable=yes',
                'scrollbars=no',
                'toolbar=no',
                'menubar=no',
                'location=no',
                'directories=no',
                'status=yes',
                'width='+ width,
                'height='+ height,
                'left='+ left,
                'top='+ top
            ];

            if(popup_window) {
                popup_window.close();
            }
            if(href) {
                popup_window = window.open(href,'',params.join(','));
                popup_window.focus();

            }else{
                alert('This Login Application was not configured correctly. Please contact our customer support.');
            }
            return false;
        },

        _OnClick: function ($this) {
            $("#fancybox-popup-social-login").html('');
            $.fancybox({
                openEffect: 'elastic',
                closeEffect: 'elastic',
                prevEffect: 'fade',
                nextEffect: 'fade',
                width: "auto",
                height:'auto',
                fitToView: false,
                autoSize : false,
                maxWidth: '98%',
                content : this.options.popuphtml,
            });
            // create recaptcha and validate
            $('#login-recaptcha,#fogot-recaptcha,#create-recaptcha').val('');
            var element_render = '';
            var validate = '';
            if (this.options.recaptchaLogin == 1 && $this.hasClass('login') && $('#tab-login-recaptcha').html() == ''){
                element_render = 'tab-login-recaptcha';
                validate = 'login-recaptcha';
            }
            if (this.options.recaptchaForgot == 1 && $this.hasClass('fogot-pw') && $('#tab-fogot-recaptcha').html() == ''){
                element_render = 'tab-fogot-recaptcha';
                validate = 'fogot-recaptcha';
            }
            if (this.options.recaptchaCreate == 1 && $this.hasClass('create') && $('#tab-create-recaptcha').html() == ''){
                element_render = 'tab-create-recaptcha';
                validate = 'create-recaptcha';
            }

            if (element_render !='') {
                grecaptcha.render(element_render, {
                    'sitekey': this.options.recaptchaSitekey,
                    'theme': this.options.recaptchaTheme,
                    'type': this.options.recaptchaType,
                    'size': this.options.recaptchaSize,
                    'callback': function(response) {
                        if (response.length > 0) {
                            $('#'+ validate).val('success');
                        }
                    }
                });
            }
            // end
            
            $('.popup-mess-social div').hide().find('span').html('');

            if ($this.hasClass('create')) {
                var formCreate = $('#social-form-create');
                formCreate.mage('validation', {});
                $('#social-login-popup li.login,#social-login-popup .block-container').removeClass('active');
                $('#social-login-popup li.create,#tab-create').addClass('active');
            } else if ($this.hasClass('fogot-pw')){
                var formForgot = $('#social-form-password-forgot');
                formForgot.mage('validation', {});
                $('#social-login-popup li.create,#social-login-popup .block-container').removeClass('active');
                $('#social-login-popup li.login,#tab-forgot-pw').addClass('active');
            } else {
                var formLogin = $('#social-form-login');
                formLogin.mage('validation', {});
                $('#social-login-popup li.create,#social-login-popup .block-container').removeClass('active');
                $('#social-login-popup li.login,#tab-login').addClass('active');    
            }

            var y = $('#social-login-popup').height();
            var nn = $('#social-login-popup').parents('.fancybox-wrap');

            var wy = $(window).height() - 70;
            if($(window).width() < 768){
                if (y > wy) { 
                    y = wy; 
                }
                else {
                    y = y;
                }
                setTimeout(function(){
                    jQuery('.fancybox-inner').css({
                        'max-height' : wy + 'px',
                        'overflow-x': 'hidden',
                        'overflow-y': 'auto'
                    });
                },500)
            }
        },

        _AjaxLogin: function ($this) {
            if ($this.parents('form').valid()) {
                var recaptchaLogin = this.options.recaptchaLogin;
                $('.ajax-loading-sc').fadeIn();
                $('.popup-mess-social div').hide().find('span').html('');
                var parameters = $('#social-form-login').serialize(true);
                $.ajax({
                    url: this.options.formLoginUrl,
                    prefilterUrl: function(url) {
                        return url && url.replace(new RegExp('^https://'), 'http://');
                      },
                    type: 'POST',
                    data: parameters,
                    success: function (response) {
                        $('.ajax-loading-sc').hide();
                        var result =  $.parseJSON(response);
                        if (result.success) {
                            $('.popup-mess-social .mesg-success').show().find('span').html(result.message);
                            $('.tab-social').remove();
                            $('.block-container').remove();
                            if (result.redirect !='' && result.redirect != null) {
                                window.location.href = result.redirect;
                            } else {
                                location.reload(true);
                            }
                        } else {
                            if (recaptchaLogin == 1) {
                                 grecaptcha.reset();
                            }
                            $('.popup-mess-social .mesg-error').show().find('span').html(result.message);
                        }
                    }

                });
            }
        },

        _AjaxForgot: function ($this) {
            if ($this.parents('form').valid()) {
                var recaptchaForgot = this.options.recaptchaForgot;
                $('.popup-mess-social div').hide().find('span').html('');
                $('.popup-mess-social span').html();
                var parameters = $('#social-form-password-forgot').serialize(true);
                $.ajax({
                    url: this.options.forgotFormUrl,
                    prefilterUrl: function(url) {
                        return url && url.replace(new RegExp('^https://'), 'http://');
                      },
                    type: 'POST',
                    data: parameters,
                    success: function (response) {
                        $('.ajax-loading-sc').hide();
                        var result =  $.parseJSON(response);
                        if (result.success) {
                            $('.popup-mess-social .mesg-success').show().find('span').html(result.message);
                            $('#tab-forgot-pw').hide();
                        } else {
                            if (recaptchaForgot == 1) {
                                 grecaptcha.reset();
                            }
                            $('.popup-mess-social .mesg-error').show().find('span').html(result.message);
                        }
                    }

                });
            }
        },

        _AjaxCreate: function ($this) {
            if ($this.parents('form').valid()) {
                var recaptchaCreate = this.options.recaptchaCreate;
                $('.ajax-loading-sc').fadeIn();
                $('.popup-mess-social div').hide().find('span').html('');
                var parameters = $('#social-form-create').serialize(true);
                $.ajax({
                    url: this.options.createFormUrl,
                    prefilterUrl: function(url) {
                        return url && url.replace(new RegExp('^https://'), 'http://');
                      },
                    type: 'POST',
                    data: parameters,
                    success: function (response) {
                        $('.ajax-loading-sc').hide();
                        var result =  $.parseJSON(response);
                        if (result.success) {
                            $('.popup-mess-social .mesg-success').show().find('span').html(result.message);
                            $('.tab-social').remove();
                            $('.block-container').remove();
                            if (result.redirect !='' && result.redirect != null) {
                                window.location.href = result.redirect;
                            } else {
                                location.reload(true);
                            }
                        } else {
                            if (recaptchaCreate == 1) {
                                 grecaptcha.reset();
                            }
                            $('.popup-mess-social .mesg-error').show().find('span').html(result.message);
                        }
                    }

                });
            }
        }
    });
    return $.mage.SocialLogin;
});
