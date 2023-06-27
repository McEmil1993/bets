/*
* Author:      Marco Kuiper (http://www.marcofolio.net/)
*/

$(document).ready(function()
{
	// Hide all the tooltips
	$("#jquery li").each(function() {
		$("a strong", this).css("opacity", "0");
	});
	
	$("#jquery ul").each(function() {
		$("a strong", this).css("opacity", "0");
	});
	
	$("#jquery ul").hover(function() { // Mouse over
		$(this)
			.stop().fadeTo(500, 1)
			.siblings().stop().fadeTo(500, 0.5);
			

		
	}, function() { // Mouse out
		$(this)
			.stop().fadeTo(500, 1)
			.siblings().stop().fadeTo(500, 1);
			
	});

	$("#jquery li").hover(function() { // Mouse over
		$(this)
			.stop().fadeTo(500, 1)
			.siblings().stop().fadeTo(500, 0.5);
			

		
	}, function() { // Mouse out
		$(this)
			.stop().fadeTo(500, 1)
			.siblings().stop().fadeTo(500, 1);
			
	});
	
});