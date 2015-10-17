/**
 * Created by yf on 15/9/21.
 */
function view(e){
    var key_id =  e;
    $("#keyInfo").show();
    $.post(
        "./key.php?action=getDetail",
        {
            key_id:key_id
        },
        function(data){
            if(data != null){
                //获取线索基本信息
                $(".panel-content").empty();

                var dataObj = eval("("+data+")");
                //var dataObj.buy_times = 1;
                if(dataObj.buy_times == 1){
                    $(".panel-content").append(
                    '<div class="key-nav">'+
                        '<ul class="nav nav-tabs">'+
                            '<li role="presentation" class="active" onclick="navChoose(this)"><a href="#num1">一次购买</a></li>'+
                        '</ul>'+
                    '</div>'+

                    '<div class="panel-choose" id="num1">'+
                    '    <table class="table">'+
                    '    <tr>'+
                    '       <td rowspan="6" style="text-align: center"><img src="'+dataObj.publisher.avatar+'" width="100"/> <br>卖家 </td> <td>姓名：'+dataObj.publisher.realname+'</td>'+
                    '       <td rowspan="6" style="text-align: center">'+
                            '    <img src="'+dataObj.buyer.avatar+'" width="100"/>'+
                            '    <br>买家'+
                        '    </td>'+
                        '    <td>姓名：'+dataObj.buyer.realname+'</td>'+
                    '   </tr>'+
                        '<tr><td>联系方式：'+dataObj.publisher.telephone+'</td><td>联系方式：'+dataObj.buyer.telephone+'</td></tr>'+
                        '<tr><td>品牌：'+dataObj.publisher.brand+'</td><td>品牌：'+dataObj.buyer.brand+'</td></tr>'+
                        '<tr><td>所在地：'+dataObj.publisher.city+dataObj.buyer.district+'</td><td>所在地：'+dataObj.buyer.city+dataObj.buyer.district+'</td></tr>'+
                        '<tr><td>4S店：'+dataObj.publisher.shop+'</td><td>4S店：'+dataObj.buyer.shop+'</td></tr>'+
                        '<tr><td>发布时间：'+dataObj.publisher.create_time+'</td><td>购买时间：'+dataObj.key.b1_buy_time+'</td></tr>'+
                        '</table>'+
                        '<table class="table">'+
                        '   <tr> <td >客户名称：'+dataObj.key.custom_name+' </td> <td>线索品牌：'+dataObj.key.brand+dataObj.key.series+' </td> <td>购车地址：'+dataObj.key.city+dataObj.key.district+' </td> </tr>'+
                        '   <tr> <td >联系方式：'+dataObj.key.custom_telephone+' </td> <td>购车时间：'+dataObj.key.time_limit+' </td> <td>线索状态：跟进中 </td> </tr> ' +
                        '   <tr> <td >价格：'+dataObj.key.money+'橙蕉豆 </td> <td>被购买次数：'+dataObj.buy_times+' </td> <td></td> </tr>'+
                        '</table>'+

                    '   <div class="key-desc">'+
                        '    <div class="title">线索详情</div>'+
                        '     <div  class="content">'+dataObj.key.description+'</div>'+
                        '    </div>'+
                    '   </div>'+
                    '    <div class="key-desc">'+
                        '   <div class="title">评价详情</div>'+
                        '   <div class="content">'+
                        '       <div>CC超人于2015.10.01,12:00评价</div>fbjkngjengjklwnfkljEGFJKnefjkNWEG</div>'+
                        '   </div>'+
                        '</div>'
                    );
                }
                else if(dataObj.buy_times == 2){
                    $(".panel-content").append(
                        '<div class="key-nav">'+
                        '<ul class="nav nav-tabs">'+
                        '<li role="presentation" class="active" onclick="navChoose(this)"><a href="#num1">一次购买</a></li>'+
                        '</ul>'+
                        '</div>'+

                        '<div class="panel-choose" id="num1">'+
                        '    <table class="table">'+
                        '    <tr>'+
                        '       <td rowspan="6" style="text-align: center"><img src="'+dataObj.publisher.avatar+'" width="100"/> <br>卖家 </td> <td>姓名：'+dataObj.publisher.realname+'</td>'+
                        '       <td rowspan="6" style="text-align: center">'+
                        '    <img src="'+dataObj.buyer.avatar+'" width="100"/>'+
                        '    <br>买家'+
                        '    </td>'+
                        '    <td>姓名：'+dataObj.buyer.realname+'</td>'+
                        '   </tr>'+
                        '<tr><td>联系方式：'+dataObj.publisher.telephone+'</td><td>联系方式：'+dataObj.buyer.telephone+'</td></tr>'+
                        '<tr><td>品牌：'+dataObj.publisher.brand+'</td><td>品牌：'+dataObj.buyer.brand+'</td></tr>'+
                        '<tr><td>所在地：'+dataObj.publisher.city+dataObj.buyer.district+'</td><td>所在地：'+dataObj.buyer.city+dataObj.buyer.district+'</td></tr>'+
                        '<tr><td>4S店：'+dataObj.publisher.shop+'</td><td>4S店：'+dataObj.buyer.shop+'</td></tr>'+
                        '<tr><td>发布时间：'+dataObj.publisher.create_time+'</td><td>购买时间：'+dataObj.key.b1_buy_time+'</td></tr>'+
                        '</table>'+
                        '<table class="table">'+
                        '   <tr> <td >客户名称：'+dataObj.key.custom_name+' </td> <td>线索品牌：'+dataObj.key.brand+dataObj.key.series+' </td> <td>购车地址：'+dataObj.key.city+dataObj.key.district+' </td> </tr>'+
                        '   <tr> <td >联系方式：'+dataObj.key.custom_telephone+' </td> <td>购车时间：'+dataObj.key.time_limit+' </td> <td>线索状态：跟进中 </td> </tr> ' +
                        '   <tr> <td >价格：'+dataObj.key.money+'橙蕉豆 </td> <td>被购买次数：'+dataObj.buy_times+' </td> <td></td> </tr>'+
                        '</table>'+

                        '   <div class="key-desc">'+
                        '    <div class="title">线索详情</div>'+
                        '     <div  class="content">'+dataObj.key.description+'</div>'+
                        '    </div>'+
                        '   </div>'+
                        '    <div class="key-desc">'+
                        '   <div class="title">评价详情</div>'+
                        '   <div class="content">'+
                        '       <div>CC超人于2015.10.01,12:00评价</div>fbjkngjengjklwnfkljEGFJKnefjkNWEG</div>'+
                        '   </div>'+
                        '</div>'+
                    '<div class="panel-choose" id="num2" style="display: none">'+
                    '    <table class="table">'+
                    '    <tr>'+
                    '       <td rowspan="6" style="text-align: center"><img src="'+dataObj.publisher.avatar+'" width="100"/> <br>卖家 </td> <td>姓名：'+dataObj.publisher.realname+'</td>'+
                    '       <td rowspan="6" style="text-align: center">'+
                    '    <img src="'+dataObj.buyer.avatar+'" width="100"/>'+
                    '    <br>买家'+
                    '    </td>'+
                    '    <td>姓名：'+dataObj.buyer.realname+'</td>'+
                    '   </tr>'+
                    '<tr><td>联系方式：'+dataObj.publisher.telephone+'</td><td>联系方式：'+dataObj.buyer.telephone+'</td></tr>'+
                    '<tr><td>品牌：'+dataObj.publisher.brand+'</td><td>品牌：'+dataObj.buyer.brand+'</td></tr>'+
                    '<tr><td>所在地：'+dataObj.publisher.city+dataObj.buyer.district+'</td><td>所在地：'+dataObj.buyer.city+dataObj.buyer.district+'</td></tr>'+
                    '<tr><td>4S店：'+dataObj.publisher.shop+'</td><td>4S店：'+dataObj.buyer.shop+'</td></tr>'+
                    '<tr><td>发布时间：'+dataObj.publisher.create_time+'</td><td>购买时间：'+dataObj.key.b1_buy_time+'</td></tr>'+
                    '</table>'+
                    '<table class="table">'+
                    '   <tr> <td >客户名称：'+dataObj.key.custom_name+' </td> <td>线索品牌：'+dataObj.key.brand+dataObj.key.series+' </td> <td>购车地址：'+dataObj.key.city+dataObj.key.district+' </td> </tr>'+
                    '   <tr> <td >联系方式：'+dataObj.key.custom_telephone+' </td> <td>购车时间：'+dataObj.key.time_limit+' </td> <td>线索状态：跟进中 </td> </tr> ' +
                    '   <tr> <td >价格：'+dataObj.key.money+'橙蕉豆 </td> <td>被购买次数：'+dataObj.buy_times+' </td> <td></td> </tr>'+
                    '</table>'+

                    '   <div class="key-desc">'+
                    '    <div class="title">线索详情</div>'+
                    '     <div  class="content">'+dataObj.key.description+'</div>'+
                    '    </div>'+
                    '   </div>'+
                    '    <div class="key-desc">'+
                    '   <div class="title">评价详情</div>'+
                    '   <div class="content">'+
                    '       <div>CC超人于2015.10.01,12:00评价</div>fbjkngjengjklwnfkljEGFJKnefjkNWEG</div>'+
                    '   </div>'+
                    '</div>'
                    );
                }
                else if(dataObj.buy_times == 0){
                    $(".panel-content").append(
                        '<div class="panel-choose" id="num1">'+
                        '    <table class="table" style="margin-top: 20px">'+
                        '    <tr>'+
                        '       <td rowspan="6" style="text-align: center"><img src="./images/pic.jpg" width="100"/> <br>卖家 </td> ' +
                        '       <td>姓名：朱云峰111</td>'+
                        '       <td rowspan="6" colspan="2" style="text-align: center"></td>'+
                        '   </tr>'+
                        '<tr><td>联系方式：18652362600</td></tr>'+
                        '<tr><td>品牌：18652362600</td></tr>'+
                        '<tr><td>所在地：18652362600</td></tr>'+
                        '<tr><td>4S店：18652362600</td></tr>'+
                        '<tr><td>发布时间：18652362600</td></tr>'+
                        '</table>'+
                        '<table class="table">'+
                        '   <tr> <td >客户名称：林若炜 </td> <td>线索品牌：奔驰C级 </td> <td>购车地址：上海,浦东 </td> </tr>'+
                        '   <tr> <td >联系方式：13661673982 </td> <td>购车时间：一个月内 </td> <td>线索状态：跟进中 </td> </tr> ' +
                        '   <tr> <td >价格：2000橙蕉豆 </td> <td>被购买次数：1 </td> <td></td> </tr>'+
                        '</table>'+

                        '   <div class="key-desc" style="margin-top: 20px">'+
                        '    <div class="title">线索详情</div>'+
                        '     <div  class="content">fbjkngjengjklwnfkljEGFJKnefjkNWEG</div>'+
                        '    </div>'+
                        '   </div>'+
                        '    <div class="key-desc">'+
                        '   <div class="title">评价详情</div>'+
                        '   <div class="content">'+
                        '       <div>CC超人于2015.10.01,12:00评价</div>fbjkngjengjklwnfkljEGFJKnefjkNWEG</div>'+
                        '   </div>'+
                        '</div>'
                    );
                }




            }

        }
    )
}

function navChoose(e){
    $(e).addClass("active").siblings("li").removeClass("active");
    var panel = $(e).find("a").attr("href");
    $(panel).show().siblings(".panel-choose").hide()
}
$(function(){
    //关闭面板
    $(".panel-close").bind("click",function(){
        $("#keyInfo").hide();
        //$(".cover").hide();
    })
})