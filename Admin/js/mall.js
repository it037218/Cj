$(function(){
    $("#statusChoose").change(function(){
        var status = $(this).val();
        var url = "mall.php?action=list&status="+status;
        window.location.href = url;
    })
})

function verify(e){
    $.post(
        "./mall.php?action=send",
        {
            result: 1,
            id: e
        },
        function(data){
            if(data == 1){
                alert("兑换成功");
                window.refresh
            }else{
                alert("兑换失败")
            }


        }


    )


}
