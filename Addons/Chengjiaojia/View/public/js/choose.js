/**
 * Created by admin on 2015/7/10.
 */
$(function(){

    $(".options li").live("click", function (event) {
        $(this).addClass("chosen").find("dl").show()
        $(this).siblings().removeClass("chosen").find("dl").hide()
    });
    $(".options  dd").live("click", function (event) {
        //event.preventDefault();
        $(this).css({"color":"#ffb600","borderBottomColor":"#ffb600"}).siblings().css({"color":"#000","borderBottomColor":"#ccc"});

    });

})