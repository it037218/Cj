/**
 * Created by admin on 2015/7/11.
 */
    //微信js api准备
$(function(){
    prepareWx()

    wx.ready(function () {
        document.querySelector('.card1').onclick = function () {
            wx.chooseImage({
                success: function (res) {
                    images.localId = res.localIds;
                    upload(res.localIds[0],1);
                }
            });
        };
        document.querySelector('.card2').onclick = function (e) {
            wx.chooseImage({
                success: function (res) {
                    images.localId = res.localIds;
                    upload(res.localIds[0],2);
                }
            });
        };
        document.querySelector('.card3').onclick = function (e) {
            wx.chooseImage({
                success: function (res) {
                    images.localId = res.localIds;
                    upload(res.localIds[0],3);
                }
            });
        };
        function upload(localIds,e){

            //$("#uploadImage").attr("src",res.localIds);
            wx.uploadImage({
                localId: localIds,
                success: function (res) {
                    $.post(
                        uploadUrl,
                        {
                            "serverId":res.serverId
                        },
                        function(data){
                            if(data.length > 0 ){
                                $(".card"+e).find("img").remove();
                                $(".card"+e).append("<div><img src='"+data+"' width='305' height='190'></div>")
                                $(".card"+e).siblings("input").val(data)
                            }
                        }
                    )
                },
                fail: function (res) {
                    alert(JSON.stringify(res));
                }
            });
        }
    })

})