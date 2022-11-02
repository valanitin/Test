require([
	    'jquery',
], function($){

		$.extend($.expr[":"], {
		"containsIN": function(elem, i, match, array) {
		return (elem.textContent || elem.innerText || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
		}
		});
		$('#searchText').keyup(function(){
			// Search text
			var text = $(this).val().toLowerCase();
			if(!text){
				$('#no-result').hide();
				$('.topics-detail li.morebrands').hide();
				$('.topics-detail li.lessbrands').css("display","block");
				$('.clsbrandslimorebtn').css("display","block");
				$('.branddetails .topics-detail ul.more-list').css('background','#FBECE5');
				$('.branddetails .topics-detail .more-list-title').css('background','#FBECE5');
				$('.branddetails .topics-detail ul').removeClass('more-list');
				$('.branddetails .topics-detail ul h3').removeClass('more-list-title');
				$('.morebrandsbtn').html("& more...");
				$('.morebrandsbtn').click(function(){
					var charbrand = $(this).attr("datachar");
					$('.branddetails .topics-detail ul.'+charbrand).addClass('more-list');
					$('.branddetails .topics-detail ul.'+charbrand+' h3').addClass('more-list-title');
					$('body.brands').css('background','rgba(0, 0, 0, 0.1)');
					$('.clsbrand .topics-detail ul.more-list .close-brand').css("display","block");
				});
			}
			else{
				$('#no-result').hide();

				// Hide all content class element
				$('.topics-detail').hide();
				$('.topics-detail li').hide();
				// Search and show
				$('.topics-detail:containsIN("'+text+'")').show();
				$('.topics-detail li:containsIN("'+text+'")').show();
				$('.branddetails .topics-detail ul.more-list').css('background','transparent');
				$('.branddetails .topics-detail .more-list-title').css('background','transparent');
				$('body.brands').css('background','#FBECE5');
				$('.clsbrand .topics-detail ul.more-list .close-brand').hide();
				$('.branddetails .topics-detail ul').removeClass('more-list');
				$('.branddetails .topics-detail ul h3').removeClass('more-list-title');
				// $('.topics-detail li').parent();
				// console.log($('.topics-detail li:contains("'+text+'")').parent().prop('className'));
				if($('.topics-detail:containsIN("'+text+'")').length == 0 && $('.topics-detail li:containsIN("'+text+'")').length ==0){
					$('#no-result').show();
	
				 } else{
					$('#no-result').hide();
				 }

			}


		});

		$('li a.searchChar').click(function(){
			var char = $(this).attr("char");
			$('.branddetails .topics-detail').hide();
			$('.branddetails .topics-detail.'+char).show();
			console.log(char);
		});

		$('li a.viewallbrands').click(function(){
			$('.branddetails .topics-detail').show();
		});

		var count = 1;
		$(".morebrandsbtn").click(function(){
			var charbrand = $(this).attr("datachar");
			count ++;
			if(count % 2 == 0){
				$(".branddetails .topics-detail ul."+charbrand).addClass('more-list');
				$(".branddetails .topics-detail .title-"+charbrand).addClass('more-list-title');
				$("body.brands").css('background','rgba(0,0,0,0.1)');
				$(".branddetails .topics-detail .title-"+charbrand+" .close-brand").css('display','inline-block');
				$('.close-brand').click(function(){
					if($(".morebrands"+charbrand).css('display') == 'block')
					{
						$(".branddetails .topics-detail .title-"+charbrand+" .close-brand").css('display','none');
						$(".morebrands"+charbrand).css('display','none');
						$('.morebrandsbtn').html("& more...");
						$(".branddetails .topics-detail ul."+charbrand).removeClass('more-list');
						$(".branddetails .topics-detail .title-"+charbrand).removeClass('more-list-title');
						$("body.brands").css('background','#FBECE5');
					}
					count = 1;
				});
			}
			else{
				$(".branddetails .topics-detail ul."+charbrand).removeClass('more-list');
				$(".branddetails .topics-detail .title-"+charbrand).removeClass('more-list-title');
				$("body.brands").css('background','#FBECE5');
				$(".branddetails .topics-detail .title-"+charbrand+" .close-brand").css('display','none');
			}


			if($(".morebrands"+charbrand).css('display') == 'block')
			{
				$(".morebrands"+charbrand).css('display','none');
				$(this).html("& more...");
				$(".branddetails .topics-detail").addClass("more-list");
			}
			else
			{
				$(".morebrands"+charbrand).css('display','block');
				$(this).html("less");
				$(".morebrandsbtn").removeClass("more-list");
			}
			console.log(count);
		});



});

