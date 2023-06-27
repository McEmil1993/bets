$(document).ready(function() {
    App.init();
    FormPlugins.init();
            
    $('.con_tabs a.tab').click(function() {
        // hide all other tabs
        $(".con_tabs li").removeClass("current");
        // show current tab
        $(this).parent().addClass("current");
        });
            
    $('.con_tabs a.remove').click(function() {
        // Get the tab name
        var tabid = $(this).parent().find(".tab").attr("id");

        // remove tab
        $(this).parent().remove();

        // if there is no current tab and if there are still tabs left, show the first one
        if ($(".con_tabs li.current").length == 0 && $(".con_tabs li").length > 0) {

            // find the first tab    
            var firsttab = $(".con_tabs li:first-child");
            firsttab.addClass("current");
        }
    });            
});
        
// 브라우저창 끝 이벤트
$(window).scroll(function() { 
    if ($(window).scrollTop() == $(document).height() - $(window).height()) 
    ; 
}); 