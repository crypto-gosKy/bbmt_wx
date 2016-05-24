var _order=JSON.parse(window.localStorage.order);
    function order(data){
        if(data.return_code>0){
             if(data.return_code==100){
                 location.href="../../";
              }else{
                $.toast(data.return_msg, "cancel");
             }
        }else{
            good.init(data.data);
        }
    };
$(function(){
	var _href=location.href;
		var param=_href.substr(_href.indexOf('?'));
    var oHead = document.getElementsByTagName('HEAD').item(0); 
    var oScript= document.createElement("script"); 
    oScript.type = "text/javascript"; 
    oScript.src=siteConfig.order_submit+param+'&callback=order&t='+new Date().getTime(); 
    oHead.appendChild(oScript); 
});
var good = {
	init: function(data) {
	var html = template('d_order',data);
    // if(data.trade_type==1){
    //    $('.top_prompt').slideDown('300');
    // }
    var html2 = template('d_order_head',data);
	$("#main-wrap").append(html);
    $(".weui_panel_bd").html(html2);
    

         if(data.trade_type==0){
            $.each($('.idCard-tpl'),function(i,d){
                $(d).remove();
            });       
        }
	}
}
