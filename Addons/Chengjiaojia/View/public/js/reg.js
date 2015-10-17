/**
 * Created by admin on 2015/7/11.
 */
    //微信js api准备
$(function(){
    prepareWx()
    //wx.ready(function () {
    //    document.querySelector('#uploadImage').onclick = function () {
    //        wx.chooseImage({
    //            success: function (res) {
    //                images.localId = res.localIds;
    //                $("#uploadImage").attr("src",res.localIds[0]);
    //                upload(res.localIds[0]);
    //            }
    //        });
    //    };
    //    function upload(localIds){
    //        wx.uploadImage({
    //            localId: localIds,
    //            success: function (res) {
    //                $.post(
    //                    uploadUrl,
    //                    {
    //                        "serverId":res.serverId
    //                    },
    //                    function(data){
    //                        if(data.length > 0 ){
    //                            if(uploadStatus == 1){
    //                                alert(data)
    //                                $.post(
    //                                        saveUrl,
    //                                        {
    //                                            avatar:data
    //                                        },
    //                                        function(data1){
    //                                            $("input[name='avatar']").val(data)
    //                                            sessionStorage.setItem("avatar",data)
    //                                        }
    //                                )
    //                            }else{
    //                                $("input[name='avatar']").val(data)
    //                                sessionStorage.setItem("avatar",data)
    //                            }
    //                        }
    //                    }
    //
    //
    //                )
    //
    //
    //
    //            },
    //            fail: function (res) {
    //                alert(JSON.stringify(res));
    //            }
    //        });
    //    }
    //})

    wx.ready(function () {
        document.querySelector('.avatar').onclick = function () {
            alert(2)
            wx.chooseImage({
                success: function (res) {
                    images.localId = res.localIds;
                    $("#uploadImage").attr("src",res.localIds[0]);
                    alert(res.localIds[0])
                    upload(res.localIds[0]);
                }
            });
        };
        function upload(localIds){
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
                                alert(data)
                                $("input[name='avatar']").val(data)
                                sessionStorage.setItem("avatar",data)
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