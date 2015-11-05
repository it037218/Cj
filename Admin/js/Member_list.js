/**
 * Created by yf on 15/9/14.
 */
$(function(){
    $("#card_container").bind("click",function(){
        $(this).hide();
        //$(".cover").hide();
    })

    /*
    * 审核通过
    * */

    $(".verify-yes").on("click",function(){
        alert(11)
        var user_id = $("input[name='user_id']").val();
        if(user_id.length == 0){
            alert("请重新选择");
            return
        }else{
            $.post(

                "member.php?action=verifyResult",
                {
                    user_id:user_id,
                    verify_result:1
                },
                function(data){
                    if(data == 1){
                        $(".bs-example-modal-sm").modal("hide")
                        alert("审核成功");
                        location.reload
                        //$("tr[data='"+user_id+"']").remove();
                    }else{
                        alert("审核出错")

                        $(".bs-example-modal-sm").modal("hide")
                        return
                    }
                }
            )
        }
    })
    /*
    * 审核不通过
    * */
    $(".verify-no").bind("click",function(){
        var user_id = $("input[name='user_id']").val();
        if(user_id.length == 0){
            alert("请重新选择");
            return
        }else{
            $.post(

                "member.php?action=verifyResult",
                {
                    user_id:user_id,
                    verify_result:0
                },
                function(data){
                    if(data == 1){
                        $(".bs-example-modal-sm").modal("hide")
                        alert("审核成功")
                        $("tr[data='"+user_id+"']").remove();
                    }else{
                        alert("审核出错")

                        $(".bs-example-modal-sm").modal("hide")
                        return
                    }
                }
            )
        }
    })


    //关闭面板
    $(".panel-close").bind("click",function(){
        $("#memberInfo").hide();
        $(".cover").hide();
    })
})

function checkDetail(e){
    $("#myModal").modal("show");
    $("#myModal").css({"top":100,"left":300})

}
function check_card(e) {

    var content = $(e).children();
    $("#card_pic").empty()
    for (var i = 0; i < content.length; i++) {
        //console.log($(content[i]).attr("src"))
        $("#card_pic").append("<img src='" + $(content[i]).attr("src") + "' style='width:500px'>")
    }
    var height = $("#card_container").height();
    var width = $("#card_container").width();

    $("#card_container").css({
        "top": function () {
            return ( (parseInt($(window).height())-height) / 2 + "px");
        },
        "left": function () {
            return ((parseInt($("#page-wrapper").width()) - width) / 2 + "px");
        }
    });
    $(".cover").css({"height":$(window).height(),"width":$(window).width()}).show();
    $("#card_container").show();
}

function verify_yes(){
    var user_id = $("input[name='user_id']").val();
    if(user_id.length == 0){
        alert("请重新选择");
        return
    }else{
        $.post(

            "member.php?action=verifyResult",
            {
                user_id:user_id,
                verify_result:1
            },
            function(data){
                if(data == 1){
                    $(".bs-example-modal-sm").modal("hide")
                    alert("审核成功")
                    $("tr[data='"+user_id+"']").remove();
                }else{
                    alert("审核出错")

                    $(".bs-example-modal-sm").modal("hide")
                    return
                }
            }
        )
    }
}

function verify_no(){
    var user_id = $("input[name='user_id']").val();
    if(user_id.length == 0){
        alert("请重新选择");
        return
    }else{
        $.post(

            "member.php?action=verifyResult",
            {
                user_id:user_id,
                verify_result:0
            },
            function(data){
                if(data == 1){
                    $(".bs-example-modal-sm").modal("hide")
                    alert("审核成功")
                    $("tr[data='"+user_id+"']").remove();
                }else{
                    alert("审核出错")

                    $(".bs-example-modal-sm").modal("hide")
                    return
                }
            }
        )
    }
}


function verify(e){
    var user_id = e;
    $("#user_id").val(e);

    //根据用户id获取用户信息
    $.post(
        "./member.php?action=getDetail",
        {
            id:e
        },
        function(data){
            if(data != null){
                var dataObj = eval("("+data+")");
                if(dataObj.status==4 || dataObj.status == 5){
                    var content = '<tr><td>姓名：'+dataObj.realname+'</td><td>手机号：'+dataObj.telephone+'</td><td>身份证号：'+dataObj.card_number+'</td></tr>'+
                        '<tr><td>所在地：'+dataObj.city+dataObj.district+'</td><td>4S店：'+dataObj.shop+'</td><td>品牌：'+dataObj.brand+'</td></tr>'+
                    '<tr><td >昵称：'+dataObj.nickname +'</td><td colspan="2">审核时间：'+dataObj.verify_time+'</td></tr>'+
                    '<tr>'+
                    '<td class="col-lg-3" ><a href="#" onclick="check_card(this)"><img class="col-lg-12" src="'+dataObj.business_card+'" style="max-height:300px"></a></td>'+
                    '<td class="col-lg-3" ><a href="#" onclick="check_card(this)"><img class="col-lg-12" src="'+dataObj.card_id1+'" style="max-height:300px"></a></td>'+
                    '<td class="col-lg-3" ><a href="#" onclick="check_card(this)"><img class="col-lg-12" src="'+dataObj.card_id2+'" style="max-height:300px"></a></td>'+
                    '</tr>'+
                    '</tr>'
                }else if(dataObj.status == 0 || dataObj.status == 1){
                    alert("用户还未填写信息");
                    return false
                }else if(dataObj.status == 2){
                    var content = '<tr><td>昵称：'+dataObj.nickname +'</td><td>手机号：'+dataObj.telephone+'</td><td>品牌：'+dataObj.brand+'</td></tr>'+
                        '<tr><td>所在地：'+dataObj.city+dataObj.district+'</td><td colspan="2">4S店：'+dataObj.shop+'</td></tr>';
                }else if(dataObj.status == 3){
                    var content = '<tr><td>姓名：'+dataObj.realname+'</td><td>手机号：'+dataObj.telephone+'</td><td>身份证号：'+dataObj.card_number+'</td></tr>'+
                        '<tr><td>所在地：'+dataObj.city+dataObj.district+'</td><td>4S店：'+dataObj.shop+'</td><td>品牌：'+dataObj.brand+'</td></tr>'+
                        '<tr><td colspan="3">昵称：'+dataObj.nickname +'</td></tr>';
                }
                $("#memberInfo table").empty();
                $("#memberInfo table").append(content)
                $("#memberInfo").show();
                $(".cover").show();
            }
        }
    )


}

function edit_verify(e){
    var user_openid = e;
    $("#user_openid").val(e);

    //根据用户id获取用户信息
    $.post(
        "./member.php?action=getDetail",
        {
            id:e
        },
        function(data){
            if(data != null){
                var dataObj = eval("("+data+")");
                var content = '<tr><td>姓名：'+dataObj.realname+'</td><td>手机号：'+dataObj.telephone+'</td><td>身份证号：'+dataObj.card_number+'</td></tr>'+
                    '<tr><td>所在地：'+dataObj.city+dataObj.district+'</td><td>4S店：'+dataObj.shop+'</td><td>品牌：'+dataObj.brand+'</td></tr>'+
                    '<tr><td colspan="3">昵称：'+dataObj.nickname +'</td></tr>'+
                    '<tr>'+
                    '<td class="col-lg-3" ><a href="#" onclick="check_card(this)"><img class="col-lg-12" src="'+dataObj.business_card+'" style="max-height:300px"></a></td>'+
                    '<td class="col-lg-3" ><a href="#" onclick="check_card(this)"><img class="col-lg-12" src="'+dataObj.card_id1+'" style="max-height:300px"></a></td>'+
                    '<td class="col-lg-3" ><a href="#" onclick="check_card(this)"><img class="col-lg-12" src="'+dataObj.card_id2+'" style="max-height:300px"></a></td>'+
                    '</tr>'+
                    '<td colspan="2"></td>'+
                    '    <td>'+
                    '<input type="hidden" name="user_openid" value="'+dataObj.id+'">'+
                    '    <button type="button" class="btn btn-default verify-no" style="width: 48%" onclick="verify_no()">不通过</button>'+
                    '    <button type="button" class="btn btn-primary verify-yes" style="width: 48%" onclick="verify_yes()">通过</button>'+
                    '</td>'+
                    '</tr>'

                $("#memberInfo table").empty();
                $("#memberInfo table").append(content)

            }
        }


    )
    $("#memberInfo").show();
    $(".cover").show();
}