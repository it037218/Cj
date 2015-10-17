/**
 * Created by admin on 2015/7/31.
 */

$(function(){
    $.post(
        "./controller/adapter.php?action=getMemberList",
        function(data){
            var dataObj = eval("("+data+")")
            $("#memberList").datagrid("loadData",dataObj)
        }
    )
})

function operationFormatter(value, row){
    "use strict";
    return "" +
        "<a href='#' class='easyui-linkbutton' data-options=\"iconCls:'icon-search'\">编辑</a>" +
        "<a href='#' class='easyui-linkbutton'>审核</a>"
}


