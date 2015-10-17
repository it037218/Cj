/**
 * Created by admin on 2015/6/30.
 */

/*prepareWx();*/
function prepareWx(){
    //微信js api准备
    $.post(
        URL,
        {url:location.href},
        function(data){
            var dataObj = eval("("+data+")");
            wx.config({
                debug: false,
                appId:dataObj.appId ,
                timestamp: dataObj.timestamp,
                nonceStr: dataObj.nonceStr,
                signature:dataObj.signature ,
                jsApiList:[
                    "onMenuShareAppMessage",
                    "onMenuShareTimeline",
                    'chooseImage',
                    'previewImage',
                    'uploadImage'
                ]
            });
        }
    )
}

