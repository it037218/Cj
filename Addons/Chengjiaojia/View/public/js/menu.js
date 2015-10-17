//$.noConflict()
jQuery(function(){
    'use strict';
	$(".footer li").click(function(){
		$(this).find("dl").slideToggle("fast")
		$(this).siblings().find("dl").hide();
	})
})