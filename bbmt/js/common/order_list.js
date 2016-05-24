
function order(data){
    if(data.return_code>0){
			
					$("body").css("background-color","#eee");	
					$("#main-cover").css("background-color","#eee");	
				$("#main-cover").append('<div class="d_kong" style="color:fff"><p style="font-size:25px;margin-bottom:15px">sorry!</p><p style="font-size:15px"></p></div>');
		
				$(".d_kong p:last-of-type").html(data.return_msg);
					
				}
			
        if(data.return_code==100){
            location.href="../../";
        }
        //    $.toast(data.return_msg, "cancel");
        
    else{
          var order = new OrderList(data);
    }
};


$(function() {
	$(function(){
    var oHead = document.getElementsByTagName('HEAD').item(0); 
    var oScript= document.createElement("script"); 
    oScript.type = "text/javascript"; 
    oScript.src=siteConfig.order_list+'?page=1&callback=order&t='+new Date().getTime(); 
    oHead.appendChild(oScript); 
});
})

var orderListTpl = [
	'{{each trade_list as value i}}',
		'<div class="d_container2">',
			'<div class="d_time">',
				'<p>下单时间:<span>{{value.created}}</span></p>',
				'<div class="d_status">',
					'{{value.trade_status | orderStatus}}',
				'</div>',
			'</div>',
			'{{each value.orders as nice i}}',
				'<div class="weui_panel_bd ">',
				'<a href="order_detail.html?tid={{value.tid}}" class="weui_media_box weui_media_appmsg">',
						'<div class="weui_media_hd" style="border:none">',
							'<img class="weui_media_appmsg_thumb" src="{{nice.pic_url}}@!pic_200_200" alt="">',
						'</div>',
						'<div class="weui_media_bd" style="margin-left:10px;">',
							'<h4 class="weui_media_title">{{nice.title}}</h4>',
							'<p class="weui_media_desc">规格：{{nice.sku_spec_name}}X{{nice.num}}</p>',
							'<div class="d_text">',
								'<p>商品总价：<span>&yen;{{nice.price}}</span></p>',
								'<p>付款金额：<span>&yen;{{nice.price}}</span></p>',
							'</div>',
						'</div>',
				'</a>',
				'</div>',
			'{{/each}}',
			'<div class="d_pay" data-tid="{{value.tid}}">',
				'<p>应付：<span class="d_rel_pay">&yen;{{value.pay_amount}}</span></p>',
					'<p>',
						'{{if value.trade_status==1}}',
							'<a class="marRight-10 cancel-order">取消订单</a>',
							'<a class="d_activeQ pay-order" data-orderid="{{value.tid}}" data-orderp="{{value.pay_amount}}">支付</a>',
						'{{/if}}',
						'{{if value.trade_status==10}}',
							'<a class="marRight-10 ok-order">确认收货</a>',
							 '{{each value.orders as nice i}}',
								'<a href="trace_detail.html?invoice_no={{nice.invoice_no}}&logistics_company={{nice.logistics_company}}" class="d_activeQ">查看物流</a>',
							'{{/each}}',
						'{{/if}}',
						'{{if value.trade_status==100}}',
							'<a class="marRight-10 return-order" href="tel:0571-81182537" style="color:#000000" id="show-alert">退货</a>',
						'{{/if}}',
				'</p>',
			'</div>',
		'</div>',
	'{{/each}}'

].join(''); //数组拼接

var OrderList = function(data) {
	this.data =data.data;
    this.page=this.data.total_page_num;
	this.init(); //新建JQ对象调用方法
	this.event();
};

OrderList.prototype.init = function() {
	template.helper('orderStatus', function(status) {
		var status=parseInt(status);
		switch (status) {
			case 1:
				return '待付款';
				break;
			case 4:
				return '待清关';
				break;
			case 8:
				return '待发货';
				break;
			case 10:
				return '待收货';
				break;
			case 100:
				return '订单完成';
				break;
			case 0:
				return '已关闭';
				break;
			default:
				return '';
		}
	}); 
	var render = template.compile(orderListTpl);
	var html = render(this.data);
	$('#main-cover').append(html);
	if(window.sessionStorage.orderHeight){
        $('body').animate({scrollTop:window.sessionStorage.orderHeight}, 0);
    }

};
OrderList.prototype.event=function(){
    var callpay_data={};
    function jsApiCall(){
				WeixinJSBridge.invoke("getBrandWCPayRequest",callpay_data, function(res) { 
					if (res.err_msg == "get_brand_wcpay_request:ok") {
                        var _tid=$('#order-tid').text();
					    location.href="order_detail.html?tid="+_tid;
					} else {
                        $.toast('支付失败', "forbidden");
                        var _tid=$('#order-tid').text();
					    setTimeout(function(){
							location.href="order_detail.html?tid="+_tid;
						},'1000')
					}
				});
			}
    function callpay(data){
        callpay_data=data;
        if (typeof WeixinJSBridge == "undefined") {
            if (document.addEventListener) {
                document.addEventListener("WeixinJSBridgeReady", jsApiCall, false);
            } else if (document.attachEvent) {
                document.attachEvent("WeixinJSBridgeReady", jsApiCall);
                document.attachEvent("onWeixinJSBridgeReady", jsApiCall);
            }
        }else{
            jsApiCall();
        }
    }
	var _this=this;
	$('#main-cover').on('click','.cancel-order',function(){
		var _tid=$(this).parent().parent().attr('data-tid');       
        $.confirm("确认要取消订单吗", function(){
            $.post(apiurl.order_cancel,{tid:_tid},function(data){
            location.reload();
            })
        });
        
	});
	$('#main-cover').on('click','.ok-order',function(){
		var _tid=$(this).parent().parent().attr('data-tid');
        $.confirm("确认收货", function(){
            $.post(apiurl.order_ok,{tid:_tid},function(data){
                location.reload();
            })
        });
	});
	$('#main-cover').on('click','.pay-order',function(){
        var data=$(this).data();
        $('#order-tid').text(data.orderid);
        $('#pay_price').text(data.orderp);
		$('.d_zhe').fadeIn(300);
		$('.d_tan').fadeIn(300);
	});
	$('#main-cover').on('click', '.d_guanbi', function() {
		$('.d_zhe').fadeOut(300);
		$('.d_tan').fadeOut(300);
		return ;
	});
    $('#main-cover').on('click', '.d_que', function() {
         var _tid=$('#order-tid').text();
            $.post(apiurl.pay,{tid:_tid},function(data){
                if(data.return_code==0){
                    var data=JSON.parse(data.data);
                    callpay(data);
                }else{
                    $.toast(data.return_msg, "forbidden");
                }

            })
		return ;
	});
    var loading = false;  //状态标记
    var lv=getMore();
    $(document.body).infinite().on("infinite", function() {
        if(loading) return;
        loading = true;
        $('.weui-infinite-scroll').show();
        //模拟延迟
        lv();
    });
    function getMore(){
        var page=1;
        return function(){
                    if(_this.page>page){
                        page++;
                        var lv=new ajax({
                        'url':siteConfig.order_list+'?page='+page,
                        type:'get',
                        success:function(data){
                        		window.sessionStorage.orderSize=page;
                                var html=Render.tpl(orderListTpl,data);
                                $('#main-cover').append(html);
                                loading = false;
                                $('.weui-infinite-scroll').hide();
                            }
                        })
                    }else{
                        $('.weui-infinite-scroll').remove(); 
                    }

             };
    };    
}