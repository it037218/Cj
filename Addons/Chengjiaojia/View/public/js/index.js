$(function(){

    $(".key_list_detail").last().find("div").css("border",0)
    //$(".area_choose li").eq(0).addClass("chosen")
    //$(".area_choose li").eq(0).find("dl").show()
    //$(".car_choose li").eq(0).addClass("chosen")
    //$(".car_choose li").eq(0).find("dl").show()

    $(".choose .select_area").bind("touchstart", function (event) {
        event.preventDefault();
        $(this).siblings(".options").toggle()
        $(this).parent().siblings(".choose").find(".options").hide()
        if($(this).find("span").eq(2).hasClass("up")){
            $(this).find("span").eq(2).removeClass("up fl").addClass("down fl")
            $(".cover").hide()
            $("body").css("position","")
        }else{
            $(this).find("span").eq(2).removeClass("down fl").addClass("up fl")
            $(".cover").show()
            $("body").css("position","fixed")
        }
        $(this).parent().siblings(".choose").find(".select_area span:last").attr("class","down fl")

    });
    $(".cover").bind("click", function (event) {
        event.preventDefault();
        $(this).hide()
        $("body").css("position","")
        $(".select_area").find("span").eq(2).removeClass().addClass("down fl")
        $('.choose').find(".options").hide();
    });
    $(".options li").bind("touchstart", function (event) {
        //event.preventDefault();
        $(this).addClass("chosen").find("dl").show()
        $(this).siblings().removeClass("chosen").find("dl").hide()
    });
    $(".options  dd").bind("touchstart", function (event) {
        event.preventDefault();
        $($(this).parents(".choose").find(".select_area .text")).text($(this).text())
        //$(this).parent().parent().hide()
        coverHide();
        $("body").css("position","")
        $(".options").hide()
        $(".choose").find(".select_area span:last").attr("class","down fl")
        $(this).css({"color":"#ffb600","borderBottomColor":"#ffb600"}).siblings().css({"color":"#000","borderBottomColor":"#ccc"});

    });
    $(".area_choose dd").bind("touchstart",function(){
        var city = $(this).text()
        var url1 = url+"/city/"+city
        window.location.href=url1;
    })
    $(".car_choose dd").bind("touchstart",function(){
        var series = $(this).text()
        var url1 = url+"/series/"+series
        window.location.href=url1;
    })
    $(".order_options dd").bind("touchstart",function(){
        var order=$(this).attr("id");
        var url3 = url+"/order/"+order;
        window.location.href=url3;
    })

    function coverShow(){
        $(".cover").show()
    }
    function coverHide(){
        $(".cover").hide()
    }
    $(".key_list_content").scroll(function(e){
        e.preventDefault();
    })
    //滑动ul时，线索列表不滚动
    // $("li").scroll(function(){
        $(".key_list_detail").scroll(function(e){
            e.preventDefault();
        })
    // })


})