require([
  'jquery',
  'jquery/ui',
  'mage/translate'
  ], function($){
  $(document).ready(function() {
       alert($.mage.__('Already subscribed Sololuxury!'));
      var storeName = '<?php echo $storeCode ?>';
      $('.footer-subscribe span').click(function(){
        $('.footer-messages').show();
        $('.footer-messages').delay(15000).fadeOut(800);
        var custEmail = $("#newsletter").val();
        if(custEmail == ''){
          $("#result-newsletter").text('Email is required');
          $('#result-newsletter').delay(8000).fadeOut(800);

        }
        else{
          // console.log(email);
           $("#result-newsletter").text('').css({"display":"block"});
            $.ajax({
                url: "https://sololuxury.com/newsletter.php",
                type: "POST",
                data: {
                  email: custEmail,
                  website:"WWW.SOLOLUXURY.COM",
                  store_name: storeName

                },
                dataType: "JSON",
                beforeSend: function() {
                    $('#loader-newsletter').show();
                 },
                 complete: function(){
                    $('#loader-newsletter').hide();
                    // $('#result').fadeIn(800);
                 },
                success: function (jsonStr) {
                     var status = jsonStr['code'];
                     alert($.mage.__(jsonStr['message']));
                     var message = $.mage.__(jsonStr['message']);
                     // $("#result-newsletter").text(JSON.stringify(jsonStr));
                     console.log(message);
                     // console.log(jsonStr['message']);
                     if(status == 200){
                       $("#result-newsletter").text(message);
                       $('#result-newsletter').delay(8000).fadeOut(800);
                     }
                     else{
                       $("#result-newsletter").text(message);
                     }
                    // $("#result").text(JSON.stringify(jsonStr));
               }


            });
        }


      });

  });
});
