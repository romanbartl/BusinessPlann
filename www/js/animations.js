        	$("#search-div-icon").click(
        		function() { 
        			$("#search-div-input").toggle('slide', {direction: 'right'}, 300);
        			$(".search-input").select();

        			if($(".search-input").val() != '') $(".search-input").val('');
        		}
        	);

        	$(document).keyup(function(e) {
  				if ((e.keyCode == 27) && ($('#search-div-input').css('display') === 'block'))
  				{ 
  					$("#search-div-input").toggle('slide', {direction: 'right'}, 300);
  					$(".search-input").val('');
				}
			});

        	$("#menu-button-index").click(
        		function() { 
        			$("nav").toggle('slide', {direction: 'left'}, 400);
        		}
        	);

			$("#user-photo-small").click(
				function() {
					$("#user-preferences").fadeToggle(120);
				}
			);


			$("#labels_heading").click(
				function() {
					$("#labels_list").toggle('slide', {direction: 'up'}, 200);
				}
			);

			$("#groups_heading").click(
				function() {
					$("#groups_list").toggle('slide', {direction: 'up'}, 200);
				}
			);



			/*
        	$(document).click(
        		function(element) {
    				if((!$(element.target).is('#search-div-icon, #')) && ($("#search-div-input").css('display') == 'block'))
    					$("#search-div-input").toggle('slide', {direction: 'right'}, 300);
  				}
  			);*/


      $('#change_name_but').click(
        function() {
          $('#name_input').prop("disabled", false);
          $('#name_input').select();
          $('#change_name_but').hide();
          $('#change_name_but_submit').show();
        }
      );