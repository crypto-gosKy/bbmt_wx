
function order_detail(data){
	
    if(data.return_code>0){
        if(data.return_code==100){
            location.href="../../";
        }else{
            $.toast(data.return_msg, "cancel");
        }
    }else{
         var data=data.data;
	     good.init(data);
    }
}
$(function(){

 var _href=location.href;
 var param=_href.substr(_href.indexOf('?'));
 var oHead = document.getElementsByTagName('HEAD').item(0); 
 var oScript= document.createElement("script"); 
 oScript.type = "text/javascript"; 
 oScript.src=siteConfig.order_detail+param+'&callback=order_detail&t='+new Date().getTime(); 
 oHead.appendChild(oScript); 

});

	var good = {
		init: function(data) {
            
			$('.main-cover').html(template('d_content', data));		
            var $selector=$('.main-cover');
			var html = $(".d_xx0").html();
            if(html!=undefined){
                var r_html = html.substr(6, 8);
                html = html.replace(r_html, "********");
                $(".d_xx0").html(html)
            }
			
            $selector.on('click','#pay-btn',function(){
                var tid=$('#tid').text();
                var moery=$('#moery').text();
                $('.d_zhe').fadeIn(300);
                $('.d_tan').fadeIn(300);
                $('.d_bianhao').text(tid);
                $('.d_zhenshi').text(moery);
                return false;
            });
            $('.d_guanbi').on('click',function(){
                $('.d_zhe').fadeOut(300);
                $('.d_tan').fadeOut(300);
                return false;
            });
            $('.d_que').on('click',function(){
                var tid=$('#tid').text();
                $.post(apiurl.pay,{tid:tid},function(data){
                        if(data.return_code==0){
                            var data=JSON.parse(data.data);
                            callpay(data);
                        }else{
                            $.toast(data.return_msg, "forbidden");
                        }
                })
                return false;
            });
            
            $selector.on('click','#cancel-btn',function(){
                var tid=$('#tid').text();      
                $.confirm("确认要取消订单吗", function(){
                    $.post(apiurl.order_cancel,{tid:tid},function(data){
                    location.reload();
                    })
                });
                return false;
            });
            $selector.on('click','#ok-btn',function(){
                var tid=$('#tid').text();
                $.confirm("确认收货", function(){
                    $.post(apiurl.order_ok,{tid:tid},function(data){
                        location.reload();
                    })
                });
                return false;
            }); 
            var callpay_data={};
			function jsApiCall(){
				WeixinJSBridge.invoke("getBrandWCPayRequest",callpay_data, function(res) { 
					if (res.err_msg == "get_brand_wcpay_request:ok") {
                        var _tid=$('.d_bianhao').text();
					    location.href="order_detail.html?tid="+_tid;
					} else {
                        $.toast('支付失败', "forbidden");
                        var _tid=$('.d_bianhao').text();
					    location.href="order_detail.html?tid="+_tid;
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
            
            
            
            
		}
	};
	
// var	b=parseInt( Number(trade_status));

