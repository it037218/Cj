/**
 * Created by yf on 15/9/2.
 */
//调用微信JS api 支付
var clickStatus = true

$(function(){
    $(".recharge-confirm").bind("touchstart",function(){

        if(clickStatus == false){
            return false;
        }
        clickStatus = false;
        $(".money-input input").blur()
        var money = $(".money-input input").val();
        if(money.length == 0){
            $().message("请输入充值金额");
            return
        }
        $.post(
            url,
            {money:money},
            function(data){
                if (typeof WeixinJSBridge == "undefined"){
                    if( document.addEventListener ){
                        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
                    }else if (document.attachEvent){
                        document.attachEvent('WeixinJSBridgeReady', jsApiCall);
                        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
                    }
                }else{
                    jsApiCall();
                }

                var obj2 = eval("("+data+")")
                function jsApiCall()
                {
                    var obj2 = eval("("+data+")")
                    WeixinJSBridge.invoke(
                        'getBrandWCPayRequest',
                        obj2,
                        function(rst){
                            if(rst.err_msg == "get_brand_wcpay_request:ok" ) {
                                clickStatus = true

                                $().message("充值成功")
                                setInterval(function(){
                                   window.location.href = preHref
                                },500)

                            }else if(rst.err_msg == "get_brand_wcpay_request:cancel" ||rst.err_msg == "get_brand_wcpay_request:fail"){
                               //$().message("充值失败")
                                clickStatus = true
                            }
                        }
                    );
                }
            }


        )



    })
})