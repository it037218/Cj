$(function(){
    //用户管理
    $("#user_manage").datagrid({
        onClickCell: function(rowIndex, field, value){
            $("#content_panel").panel({
                title: $(value).attr("dirTitle")
            });
            $("#content_frame").attr("src", $(value).attr("targetUrl"));
        }
    })


})