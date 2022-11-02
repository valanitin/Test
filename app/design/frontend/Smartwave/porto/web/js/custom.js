require(['jquery'], function ($) {
  'use strict';

  var searchbtnText = 'Highlight';
  var inputId = 'faqs__search_text_area';
  var btnId = 'faqs__search_text_btn';

  $(document).ready(function(){
    $(".drop-menu > a").off("click").on("click", function(){
        if($(this).parent().children(".nav-sections").hasClass("visible")) {
            $(this).parent().children(".nav-sections").removeClass("visible");
            $(this).removeClass("active");
        }
        else {
            $(this).parent().children(".nav-sections").addClass("visible");
            $(this).addClass("active");
        }
    });
  });

  $(document).ready(function(){
    // var items = [];
    // var element;
    // var index;
    var searchInput = '<div class="faqs__search_box" ><input type="text" id="'+ inputId +'"/>' + '<button id="' + btnId +'">' + searchbtnText + '</button></div>';

    $('.cms-faqs .cls_shipping_panelmain').prepend(searchInput);

    $('#' + inputId).on('keydown',function(e) {        
      if(e.key === 'Enter') {
        $('#' + btnId).click();
      }
    });

    $('#' + btnId).on("click", function() {
      var searched = $('#' + inputId).val().trim();
      var searchSelector = $('.cls_shipping_panelmain .accordion_body .md-paragraph, .cls_shipping_panelmain h4');
      var firstMark = searchSelector.find('mark').first();
      var parentOfFirstMark = firstMark.closest('.cls_shipping_panelsub').children();
      var re = new RegExp(searched, "gi"); // search for all instances
      var newText = $(this).text().replace(re, ($1) => '<mark>' + $1 + '</mark>');

      if (searched !== "") {
        
        // searchSelector.each(function (element, index) {
        searchSelector.each(function () {
          // delete the old marks
          $(this).find('mark').contents().unwrap();
          // var text = $(this).html();            
          $(this).html(newText);
        });

        if (firstMark.length > 0) {
          if ($(parentOfFirstMark[1]).css("display") === "none") {
            parentOfFirstMark[0].click();
          }

          $('html, body').animate({
            scrollTop: firstMark.offset().top
          }, 500);
        }
      }
    });

    $(".accordion_head").click(function() {
      if ($('.accordion_body').is(':visible')) {
        $(".accordion_body").slideUp(300);
        $(".plusminus").text('+');
      }
      if ($(this).next(".accordion_body").is(':visible')) {
        $(this).next(".accordion_body").slideUp(300);
        $(this).find(".panel-title").children(".plusminus").text('+');
      } else {
        $(this).next(".accordion_body").slideDown(300);
        $(this).find(".panel-title").children(".plusminus").text('-');
      }
    });

    $("#get_started_refer").on("click", function(){
      $("#clsreferleft").hide();
      $("#result_referbox").hide();
      return false;
    });

    $("#btn_register").on("click", function(){
      $("#clsreferleft").hide();
      return false;
    });
  });

    /*START JS CODE FOR TOGGLE*/
  $('.faqspart .faqbox .question').click( function() {
    var trig = $(this);

    if ( trig.hasClass('active') ) {
      trig.next('.faqspart .faqbox .answer').slideToggle('slow');
      trig.removeClass('active');
    } 
    else {
      $('.active').next('.faqspart .faqbox .answer').slideToggle('slow');
      $('.active').removeClass('active');
      trig.next('.faqspart .faqbox .answer').slideToggle('slow');
      trig.addClass('active');
    }
    return false;
  });

  $(document).on('click', '.clslogin', function() {
    var input_pass = $("#pass");

    $(this).toggleClass("cls_hide");
    input_pass.attr('type') === 'password' ? input_pass.attr('type','text') : input_pass.attr('type','password');
  });

  $(document).on('click', '.clspassowrd', function() {
    var input_password = $("#password");

    $(this).toggleClass("cls_hide");
    input_password.attr('type') === 'password' ? input_password.attr('type','text') : input_password.attr('type','password');
  });

  $(document).on('click', '.clsconfirmpassword', function() {
    var input_password_confirmation = $("#password-confirmation");

    $(this).toggleClass("cls_hide");
    input_password_confirmation.attr('type') === 'password' ? input_password_confirmation.attr('type','text') : input_password_confirmation.attr('type','password');
  });

  $(document).on('click', '.clscurrentpassword', function() {
    var input_clscurrentpassword = $("#current-password");

    $(this).toggleClass("cls_hide");
    input_clscurrentpassword.attr('type') === 'password' ? input_clscurrentpassword.attr('type','text') : input_clscurrentpassword.attr('type','password');
  });

  $(document).on('click', '.clsnewpassword', function() {
    var input_clsnewpassword = $(".new-password");

    $(this).toggleClass("cls_hide");
    input_clsnewpassword.attr('type') === 'password' ? input_clsnewpassword.attr('type','text') : input_clsnewpassword.attr('type','password');
  });

  $(document).on('click', '.clsnewconformpassword', function() {
    var input_clsnewconformpassword = $(".password_confirmation");

    $(this).toggleClass("cls_hide");
    input_clsnewconformpassword.attr('type') === 'password' ? input_clsnewconformpassword.attr('type','text') : input_clsnewconformpassword.attr('type','password');
  });

  $(".user-logged_in .far.fa-user").click(function(){
    $(".user-login-option").show();
  });

  $(document).ready(function(){
    $('#mySidenav #switcher-website').remove();
  });

  $(document).on("click","#open-menu", function() {
    $('#mySidenav').addClass("openmenu");
    $(this).parent().addClass('active');
    $('body').addClass("cbopen-menu");
  });
  
  $(document).on("click","#close-menu", function() {
    $('#mySidenav').removeClass("openmenu");
    $(this).parent().removeClass('active');
    $('body').removeClass("cbopen-menu");
  });

  setTimeout(function(){
    $(".custom-close-button").click(function(){
      $(".modals-overlay").click();
    });
  }, 10000);

});

function closePopupspecialrequest() {
  'use strict';
  var modal = document.getElementById("myModalspec");

  modal.style.display = "none";

}

require(['jquery','mage/url'], function ($, url) {
    'use strict';

    $(document).ready(function(){
      var modalPrice = document.getElementById("myModal-price-match");
      var btnPrice = document.getElementById("myspecialricematch");
      var spanPrice = $(modalPrice).find('.close');
      var modalTittle;
      var modalDataName;
      var modal = document.getElementById("myModalspec");
      var myModalPriceSuccess = document.getElementById("myModalPriceSuccess");
      var btn = document.getElementById("myspecialreq");
      var TicketBtn = document.getElementById("create-ticket-btn");
      var span = document.getElementsByClassName("close")[0];
      var pricesucessClose = document.getElementById("pricesucessClose");

      url.setBaseUrl(BASE_URL);

      if(spanPrice) {
          spanPrice.on("click", () => {
              modalPrice.style.display = "none";
          });
      }
      if(modalPrice) {
          modalPrice.addEventListener("click", (e) => {
              if (e.target === modalPrice) {
                modalPrice.style.display = "none";
              }
          });
      }

      if(btnPrice) {
          btnPrice.onclick = function () {
            modalPrice.style.display = "block";
          };
      }

      if(pricesucessClose) {
        pricesucessClose.onclick = function() {
          myModalPriceSuccess.style.display = "none";
        };
      }
      if(btn) {
        btn.onclick = function() {
            modalTittle = $(this).attr('data-title');
            if (modalTittle) {
                $(modal).find('.clsspecialpopupheading').text(modalTittle);
            }
          modal.style.display = "block";
        };
      }
      if(TicketBtn) {
        TicketBtn.onclick = function () {
            modalTittle = $(this).attr('data-title');
            modalDataName = $(this).attr('data-page-name');
            if (modalTittle) {
                $(modal).find('.clsspecialpopupheading').text(modalTittle);
            }
            if (modalDataName) {
                $(modal).find('#brand').val(modalDataName);
            }
            modal.style.display = "block";
        };
      }
      if(span){
        span.onclick = function() {
          modal.style.display = "none";
        };
      }
      window.onclick = function(event) {
        if (event.target === modal) {
          modal.style.display = "none";
        }
      };

      $('#btn_submit').click(function(){
          var dataForm = jQuery('#'+$(this).closest('form').attr('id'));
          var link = url.build('redirectcontactus/index/success');

          if(dataForm.validation('isValid')){
              $("#result-message").text('');
              $('#myModalspec').css({"display":"none"});
              // $('#myModalPriceSuccess').css({"display":"block"});
              // console.log(dataForm.attr('action'));
              $.ajax({
                  url: dataForm.attr('action'),
                  type: dataForm.attr('method'),
                  data: dataForm.serialize(),
                  dataType: 'json',
                  async: true,
                  beforeSend: function() {
                    $('#loader-message').show();
                  },
                  complete: function(){
                    $('#loader-message').hide();
                  },
                  success: function (response) {
                      if(response.errors === false) {
                        $(location).prop('href', link);
                        // $('#result-message').html(response.message);
                        // dataForm[0].reset();
                      } else {
                        $('#result-message').html(response.message);
                        dataForm[0].reset();
                      }
                  },
                  error: function (response) {
                      console.log(JSON.parse(response));
                  },
              });
              event.stopImmediatePropagation();
              return false;
          }
      });
    });
});

require(['jquery', 'jquery/ui'], function($) {
  'use strict';
  $(document).ready(function() {
    var wd = $(window).width();

    $(".block-category-list .block-content").hide();
    $(".block-category-list .block-title").click(function(){
      $(".block-category-list .block-content").slideToggle(1000);
      $(this).toggleClass("open");
    });

    if(wd < 991){
      $('.footer .block-title').click(function(){        
        if($(this).hasClass('open')){
          $(this).removeClass('open');
        } else {
          $('.block-title.open').removeClass('open');
          $(this).addClass('open');
        }
      });
    }
  });
});