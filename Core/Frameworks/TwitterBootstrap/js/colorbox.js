$(document).ready(function(){

	//first check if there is a colorbox on the page
	if ($("#calendarcolor").length > 0){
		// hide the selector and make an span out of every option with the right color:
		$("#calendarcolor").hide();
		$("#calendarcolor").after('<div id="colorboxOptions"></div>');
		$("#calendarcolor option").each(function(i, v){
			color = $(this).val();
			selected = "";
			console.log($(this).attr("selected"));
			if($(this).attr("selected") != undefined){
				selected = "currentcolor";
			}
			$('#colorboxOptions').append('<span class="colorboxOption ' + selected + '" data-color="' + color + '" id="color' + color + '" style="background: '+ color +'"></span>');
			
			
		});
		$( ".colorboxOption" ).bind( "click", function() {
  			color = $(this).attr("data-color");
  			$( ".colorboxOption" ).removeClass("currentcolor");
  			$(this).addClass("currentcolor")
  			console.log(color);
  			$("#calendarcolor").val(color);
		});
	}
	
});
