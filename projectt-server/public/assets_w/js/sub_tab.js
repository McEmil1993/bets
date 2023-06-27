$(document).ready(function() {

	//Default Action
	$(".tab_content").hide(); //Hide all content
	$("ul.tabs li:first").addClass("active").show(); //Activate first tab
	$(".tab_content:first").show(); //Show first tab content
	$("#paginationArea:first").show(); //Show first tab content
	
	//On Click Event
	$("ul.tabs li").click(function() {
		$("ul.tabs li").removeClass("active"); //Remove any "active" class
		$(this).addClass("active"); //Add "active" class to selected tab
		$(".tab_content").hide(); //Hide all tab content
		$("#paginationArea").hide(); //Show first tab content
		var activeTab = $(this).find("a").attr("href"); //Find the rel attribute value to identify the active tab + content
		$(activeTab).fadeIn(); //Fade in the active content
		if (activeTab === '#tab_bet_list') { 
			$("#paginationArea").show();
		}
		return false;
	});

	//Default Action
	/*$(".tab_content_multi").hide(); //Hide all content
	$("ul.tabs_multi li:first").addClass("active").show(); //Activate first tab
	$(".tab_content_multi:first").show(); //Show first tab content
	$("#paginationArea_multi:first").show(); //Show first tab content
        
    $("ul.tabs_multi li").click(function() {
            $("ul.tabs_multi li").removeClass("active"); //Remove any "active" class
            $(this).addClass("active"); //Add "active" class to selected tab
            $(".tab_content_multi").hide(); //Hide all tab content
            $("#paginationArea_multi").hide(); //Show first tab content
            var activeTab = $(this).find("a").attr("href"); //Find the rel attribute value to identify the active tab + content
            $(activeTab).fadeIn(); //Fade in the active content
            if (activeTab === '#tab_bet_list_multi') { 
                    $("#paginationArea_multi").show();
            }
            return false;
	});*/
	
	
	$(".tab_content_multi").hide(); //Hide all content
	$("ul.tabs_multi li:first").addClass("active").show(); //Activate first tab
	$(".tab_content_multi:first").show(); //Show first tab content
	$("#paginationArea_multi:first").show(); //Show first tab content
        
    $("ul.tabs_multi li").click(function() {
            $("ul.tabs_multi li").removeClass("active"); //Remove any "active" class
            $(this).addClass("active"); //Add "active" class to selected tab
            $(".tab_content_multi").hide(); //Hide all tab content
            $("#paginationArea_multi").hide(); //Show first tab content
            var activeTab = $(this).find("a").attr("href"); //Find the rel attribute value to identify the active tab + content
            $(activeTab).fadeIn(); //Fade in the active content
            if (activeTab === '#tab_bet_list_multi') { 
                    $("#paginationArea_multi").show();
            }
            return false;
	});
});