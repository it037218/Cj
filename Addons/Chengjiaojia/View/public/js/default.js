    var DEFAULT_BG_IMG_WIDTH =720;
    var DEFAULT_BG_IMG_HEIGHT = 1136;
    $(function(){
        adjustBgImg();
        function adjustBgImg(){

            $("#bg_img").each(function(){
                $(this).css("width", $(window).width() + "px");
                if ($(window).width() / $(window).height() > DEFAULT_BG_IMG_WIDTH / DEFAULT_BG_IMG_HEIGHT){
                    $(this).css("height", $(window).width() * DEFAULT_BG_IMG_HEIGHT / DEFAULT_BG_IMG_WIDTH);
                }
                else{
                    $(this).css("height", $(window).height() + "px");
                }
            });
        }
        var pointStart = {x: 0, y:0};
        $(document).bind("touchstart", function(evt) {
            pointStart.x = evt.originalEvent.touches[0].pageX;
            pointStart.y = evt.originalEvent.touches[0].pageY;
        });
        $(document).bind("touchmove", function(evt){
            //if ((evt.originalEvent.changedTouches[0].pageY - pointStart.y > 0 && $("#main_content")[0].scrollTop == 0) ||
            //    (evt.originalEvent.changedTouches[0].pageY - pointStart.y < 0 &&
            //    $("#main_content")[0].scrollHeight - $("#main_content")[0].scrollTop <= $(window).height() + 10)){
            //    //pointStart.x = evt.originalEvent.changedTouches[0].pageX;
            //    //pointStart.y = evt.originalEvent.changedTouches[0].pageY;
            //
            //    evt.preventDefault();
            //}

    });

    })


    var jsVer = 28;
	var phoneWidth = parseInt(window.screen.width);
	var phoneScale = phoneWidth / 720;

	var ua = navigator.userAgent;
	if (/Android (\d+\.\d+)/.test(ua)) {
	var version = parseFloat(RegExp.$1);
	// andriod 2.3
	if (version > 2.3) {
	document.write('<meta name="viewport" content="width=720, minimum-scale = ' + phoneScale + ', maximum-scale = ' + phoneScale + ', target-densitydpi=device-dpi">');
	// andriod 2.3以上
	} else {
	document.write('<meta name="viewport" content="width=720, target-densitydpi=device-dpi">');
	}
	// 其他系统
	} else {
	document.write('<meta name="viewport" content="width=720, user-scalable=no, target-densitydpi=device-dpi">');
	}