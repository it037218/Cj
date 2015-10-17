/**
 * Created by yf on 15/10/14.
 */
function checkInfo(e){
    if(e == false || e == undefined){
        alert("系统出错")
        return
    }else{
        $.post(
            "./account.php?action=getMemberAccountDetail",
            {
                user_openid:e
            },
            function(data){
                console.log(data)
                if(data==-1){
                    $("#accountInfo .panel-content tbody").empty();
                    $("#accountInfo .panel-content tbody").append("" +
                        "<tr><td colspan='4'>暂无信息</td></tr>")
                    $("#accountInfo").show();
                }else{
                    $("#accountInfo .panel-content tbody").empty();

                    var dataObj = eval("("+data+")");

                    for(var i = 0;i<dataObj.length;i++){
                        $("#accountInfo .panel-content tbody").append("" +
                            "<tr>" +
                            "<td>"+(i+1)+"</td>" +
                            "<td>"+dataObj[i].desc+"</td>" +
                            "<td>"+dataObj[i].remark+"</td>" +
                            "<td>"+dataObj[i].create_time+"</td>" +
                            "</tr>")
                    }
                    $("#accountInfo").show();

                }

            }



        )



    }


}