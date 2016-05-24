
function trace(data){
   
    if(data.return_code>0){
        if(data.return_code==100){
            location.href="../../";
        }else{
            $(".main-content").css("background-color","#eee"); 
                $(".main-content").append('<div class="d_kong"><p style="font-size:25px;margin-bottom:15px">sorry!</p><p style="font-size:15px"></p></div>');
            
                $(".d_kong p:last-of-type").html(data.return_msg);
        }
    }else{
         var data=data.data;
         var lv=new Trace(data);
    }
};
$(function(){
    var _href=location.href;
    var param=_href.substr(_href.indexOf('?'));
    var oHead = document.getElementsByTagName('HEAD').item(0); 
    var oScript= document.createElement("script"); 
    oScript.type = "text/javascript"; 
    oScript.src=siteConfig.trace_detail+param+'&callback=trace&t='+new Date().getTime(); 
    oHead.appendChild(oScript); 
});

var Trace=function(data){
    this.data=data;
    this.init();
}
Trace.prototype.init=function() {
	var html = template('d_trades', this.data);
	$(".main-content").html(html);
};
