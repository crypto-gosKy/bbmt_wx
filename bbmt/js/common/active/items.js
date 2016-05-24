
function items(data){
    if(data.return_code>0){
			if(data.return_code>0){
					$("body").css("background-color","#eee");	
					$(".d_container2").css("background-color","#eee");	
				$(".d_container2").append('<div class="d_kong" style="color:fff"><p style="font-size:25px;margin-bottom:15px">sorry!</p><p style="font-size:15px"></p></div>');
		
				$(".d_container2 p:last-of-type").html(data.return_msg);
					
				}
        if(data.return_code==100){
            location.href="../../";
        }else{
   
        }
    }else{
         var lv=new GoodList(data.data);
    }
};
$(function(){
    var _href=location.href;
    var param=_href.substr(_href.indexOf('?'));
    var oHead = document.getElementsByTagName('HEAD').item(0); 
    var oScript= document.createElement("script"); 
    oScript.type = "text/javascript"; 
    oScript.src=siteConfig.item_list+param+'&callback=items&t='+new Date().getTime(); 
    oHead.appendChild(oScript); 
});
var tpl=[
    '{{each item_list as data i}}',
       '<div class="items-content clearfix">',
            '<a href="item.html?item_id={{data.item_id}}" style="">',
                '<div class="img fl">',
                    '<img class="" data-original="{{data.pic_url}}@!pic_200_200" src="../../img/grey.jpg" alt="">',
                '</div>',
                '<div class="text-content fl">',
                    '<h4 class="title">{{data.title}}</h4>',
                    '<p class="price">成本单价:<span>&yen;{{data.price}}</span></p>',
                '</div>',
                '<span class="tip fr"><img src="../../img/jiantou.png" width="100%"/></span>',
            '</a>',
        '</div>',
        '{{/each}}'
        ].join('');
var GoodList=function(data){
        this.data=data;
        this.page=data.total_page_num;
        this.init();
        this.event();

    };
    GoodList.prototype.init=function(){
        var str=location.search;
        if(str=='?keyword='){
            $('.search').focus();
        }
        var html=Render.tpl(tpl,this.data);
        $('.d_container2').html(html);
        this.lazyload();
    };
    GoodList.prototype.lazyload=function(){
        $("img").lazyload({
                effect : "fadeIn"
        });
    };
    GoodList.prototype.event=function(){
        var _this=this;
        var _href=location.href;
        var param=_href.substr(_href.indexOf('?'));
        $(document).scroll(function(){
            var _height=$(this).scrollTop();
            if(_height>100){
                $('#moverTop').fadeIn(300);
            }else{
                $('#moverTop').fadeOut(300);
            }
        });
        $('#moverTop').on('click',function(){
            $('body').animate({scrollTop:0}, 200);
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
                            'url':siteConfig.item_list+param+'&page='+page,
                            type:'get',
                            success:function(data){
                                    var html=Render.tpl(tpl,data);
                                    $('.d_container2').append(html);
                                    loading = false;
                                    $('.weui-infinite-scroll').hide();
                                    _this.lazyload();
                                }
                            })
                        }else{
                            $('.weui-infinite-scroll').remove(); 
                        }
                        
                 };
        };
    }
  
          