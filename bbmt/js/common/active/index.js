


function getHeight(){
    var height=$(window).height();
    return height;
}



function cheaking(data){
    if(data.return_code>0){
			if(data.return_code>0){
					$("body").css("background-color","#eee");	
				$(".d_container6").append('<div class="d_kong" style="color:fff"><p><img src="../../img/error_active.png" width="18%"/></p><p style="font-size:15px"></p></div>');
		      
				$(".d_container6 p:last-of-type").html(data.return_msg );
					
				}
        if(data.return_code==100){
            location.href="../../";
        }else{
            
        }
    }else{
         return true;
    }
};
function getDate(url,fun){
    var _href=location.href;
    var all;
    if(_href.indexOf('?')==-1){
        all=url+'?callback='+fun+'&t='+new Date().getTime();
    }else{
        var param=_href.substr(_href.indexOf('?'));
        all=url+param+'&callback='+fun+'&t='+new Date().getTime();
    }
    var oHead = document.getElementsByTagName('HEAD').item(0); 
    var oScript= document.createElement("script"); 
    oScript.type = "text/javascript"; 
    oScript.src=all; 
    oHead.appendChild(oScript); 
};


var tpl_index=[
        '{{each trade_list as value i}}',
            '<a href="items.html?item_activity_id={{value.item_activity_id}}" class="index-banner">',
                '<img data-original="{{value.url}}" src="../../img/banner_gif.png" alt=""/>',
                '<p><span class="active_des {{value.name | change}}">{{value.name}}</span><span class="fr"></span></p>',
            '</a>',
        '{{/each}}'
].join('');

var tpl_items=[
    '{{each item_list as data i}}',
       '<div class="items-content clearfix">',
            '<a href="item.html?item_id={{data.item_id}}" style="border:0px">',
                '<div class="img fl {{if data.buy_status==2}}active_end{{/if}}">',
                    '<img class="" data-original="{{data.pic_url}}@!pic_200_200" src="../../img/grey.jpg" alt="">',
                '</div>',
                '<div class="text-content fl">',
                    '<h4 class="title">{{data.title}}</h4>',
                    '<p class="price"><span>&yen;{{data.price}}</span><span class="marketpirce">&yen;{{data.market_price}}</span></p>',    
                '</div>',
                '<div class="fr active-tip">',
                    '{{if data.buy_status==0}}',
                        '<p class="time btn-no" style="background:#333333; ">即将开始</p>',
                     
                       
                    '{{/if}}',
                     '{{if data.buy_status==1}}',
                        '<p class="time btn-yes">抢购中</p>', 
                      
                    '{{/if}}',
                     '{{if data.buy_status==2}}',
                        '<p class="time btn-no">已结束</p>', 
                      
                    '{{/if}}',
                '</div>',   
            '</a>',
        '</div>',
        '{{/each}}'
    ].join('');



   var SwiperTpl=[
    '{{each pics as data i}}',
        '<div class="swiper-slide"><img src="{{data}}@!pic_640_640"></div>',
    '{{/each}}'
].join('');
  
var tpl_item=[
    '<div class="item-content">',
       ' <div class="item-content-top">',
            '<p><span class="sign-price-title">单价 </span> <span class="sign-price-num">&yen;{{price}} </span><span class="market-price-title">市场价 </span><span class="market-price-num">&yen; {{market_price}}</span></span></p>',
            '<p class="first-discount">【首单立减{{first_order_discount}}元】</p>',
                '{{if activity_type==2}}',
                    '<div class="active-time">',
                        '{{if buy_status==0}}',
                            '<label>距活动开始剩余：</label>', 
                            '<p class="time btn-no" style="background:#333333;">00:00:00{{now_time,activity_start_time,".time" | showtime2}}</p>',
                        '{{/if}}',
                        '{{if buy_status==1}}',
                            '<label>距活动结束剩余：</label>', 
                            '<p class="time btn-yes" style="background:#ff5000;" >00:00:00{{ now_time,activity_end_time,".time" | showtime2}}</p>', 
                        '{{/if}}',
                    '</div>', 
                '{{/if}}',
        '</div>',
        '<div class="title-tip">',
            '{{if type==1}}<img src="../../img/guonei.jpg" />{{else}}<img src="../../img/baoshui.jpg" />{{/if}}',
            '<p>{{title}}</p>',
        '</div>',
        '<div class="item-title"></div>',
        '{{if intro}}',
        '<p class="des-word">{{intro}}</p>',
         '{{/if}}',
         '{{if buying_tips}}',
        '<p class="prompt"><img src="../../img/item_tishi.jpg"/>提示：{{buying_tips}}</p>',
         '{{/if}}',
    '</div>',
    '<div style="height:0.8em;background-color:#efeff4"></div>',
    '<div class="item-des-content">',
        '<p class="details-des-tip"><img src="../../img/item_tishi2.jpg"/>宝贝详情</p>',
       '{{if detail}}',
        '<div class="details-des">',
            '{{each detail as value i}}',
                '<p><span class="tip">{{value.name}}</span><span class="name">{{value.value}}</span></p>',
            '{{/each}}',
        '</div>',
        '{{/if}}',
    '</div>'
       
    ].join('');


var wechat_tpl=[
        '<p class="active-title">{{user_num}}人参团</p>',
        '{{if user_num>0}}',
        '<div class="active-head">',
            '{{each wechat as value i}}',
                '{{if i<5}}',
                        '{{if value.headimgurl}}',
                            '<span style="background:url({{value.headimgurl}}) center center no-repeat;border-radius:50%;background-size:100%;"></span>',
                        '{{else}}',
                            '<span style="background:url(../../img/logo_head.png) center center no-repeat;border-radius:50%;background-size:100%;"></span>',
                        '{{/if}}',
                '{{/if}}',
            '{{/each}}',
        '</div>',
        '{{/if}}',
        '{{if user_num>0}}',
            '<p class="active-details-tip" ><span class="active-details-tip-btn">点击查看参团详情</span></p>',
            '<ul class="active-details-line">',
                '{{each wechat as lv i}}',
                    '<li>',
                        '{{if lv.headimgurl}}',
                            '<span class="head fl" style="background:url({{lv.headimgurl}}) no-repeat;background-size:100%;"></span>',
                        '{{else}}',
                            '<span class="head fl" style="background:url(../../img/logo_head.png) no-repeat;background-size:100%"></span>',
                        '{{/if}}',
                        '<span class="name">{{lv.nickname}}</span><span class="time fr">{{lv.pay_time}}</span>',
                    '</li>',
                '{{/each}}',
            '</ul>',
        '{{/if}}'
].join('');
var foottpl=[
    '{{if status==0}}',
        '<a href="javascript:" class="btn">已下架</a>',
    '{{else}}',
        '{{if quantity==0}}',
            '<a href="javascript:" class="btn">已售空</a>',     
        '{{else}}',
            '{{if buy_status==0}}',
                '<a href="javascript:" class="bottom-sub-btn">即将开始</a>', 
            '{{/if}}',
            '{{if buy_status==1}}',
                '<a id="submit-btn">立即抢购</a>', 
            '{{/if}}',
            '{{if buy_status==2}}',
                '<a href="javascript:" class="bottom-sub-over">已结束</a>', 
            '{{/if}}',
        '{{/if}}',
        
    '{{/if}}'
].join('');

var tplhideTpl=[
                '<ul class="d_ul2" data-itemid="{{item_id}}" id="itemid">',
                    '<img src="../../img/guanbi.png" class="d_guanbi">',
                    '<li>{{title}}</li>',
                    '<li><span class="color" style="font-size: 17px;">&yen;<span id="totlePrice">{{if specs.length!=0}} {{specs[0].price}} {{else}}{{price}}{{/if}}</span></span><span class="color1 marLeft-10">(单价：&yen;<span id="price">{{if specs.length!=0}}{{specs[0].price}} {{else}} {{market_price}}  {{/if}}</span>)</span></li>',
                '</ul>',
                '<div class="d_fenlei">',
                    '{{if specs.length!=0}}',
                        '<ul>',
                            '<p>规格</p>',
                            '{{each specs as data i}}',
                                '<li class="guige" data-itemid="{{item_id}}" data-sku="{{data.sku_id}}" data-status="0" data-num="0" data-price="{{data.price}}" data-price1="{{data.price1}}">{{data.spec_name}}</li>',
                            '{{/each}}',
                        '</ul>',
                    '{{/if}}',
                    '{{if type==1}}',
                    '<ul class="  marBot-15">',
                        '<p>数量</p>',
                        '<p class="d_jisuan"><img src="../../img/jian.png" id="d_jian"><span id="number">1</span><img src="../../img/jia.png" id="d_jia" /></p>',
                    '</ul>',
                    '{{/if}}',
                '</div>',
                '<a class="d_que" href="#">确定购买</a>'
                ].join(''); 

  var ActiveList=function(data){
            if(cheaking(data)){
                this.data=data.data;
                cheaking(data)
                this.init();
            }
        };
        ActiveList.prototype.init=function(){
            var height=getHeight();
            $('body').css('min-height',height);
            template.helper('change',function(data){
                if(data.indexOf('【')==-1){
                    return 'change'
                }
            })
            var html=Render.tpl(tpl_index,this.data);
            $('.d_container6').html(html);
			 this.lazyload();
                
        };
        ActiveList.prototype.lazyload=function(){
            $("img").lazyload({
                    effect : "fadeIn"
            });
        };




var GoodList=function(data){
        if(cheaking(data)){
            this.data=data.data;
            this.page=data.data.total_page_num;
            this.init();
            this.event();
        } 
    };
    GoodList.prototype.init=function(){
        var str=location.search;
        if(str=='?keyword='){
            $('.search').focus();
        }

        template.helper('showtime', function(time1, time2 ,time3,i){
            var selector='.time'+i;
		 
            if(time1>time3){
                var time=(time1-time3); 
                GoodList.prototype.time(i,time);
            }
            if(time2>=time3 && time1<=time3){
                $(selector).css('background','#ff5000').html('去拼团 >');
                return '去拼团 >';
            }
            if(time2<time3){
                $(selector).css('background','#333333').html('已结束');
                return '已结束';
            }
            
        });
     
        if(this.data.item_list.length===1){//只有一个商品则直接跳转到这个商品的详细页
          location.href='item.html?item_id='+this.data.item_list[0].item_id;
        }else{
        var html=Render.tpl(tpl_items,this.data);
       // var html1=Render.tpl(wechat_tpl,this.data);
        $('.d_container6').html(html);
       // $('.item-active').html(html1);
        this.lazyload();
       }
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
                                    var html=Render.tpl(tpl_items,data);
                                    $('.d_container6').append(html);
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
    };

    GoodList.prototype.time=function(id,time){
                var selector='.time'+id;
                var t;
                t=setInterval(function(){
					time--;
                    var str= GoodList.prototype.arrive_timer_format(time);
                    if(time<0){
                        $(selector).html('去拼团 >').css('background','#ff5000');
                    }else{
            
                        str=GoodList.prototype.time_division(str);
                   
                       
                        $(selector).html(str);
                    }
                },1000);
    };
     

   
    GoodList.prototype.arrive_timer_format=function(s) {
		var t;
		if(s > -1){
			hour = Math.floor(s/3600);
			min = Math.floor(s/60) % 60;
			sec = s % 60;
			day = parseInt(hour / 24);
			if (day > 0) {
				hour = hour - 24 * day;
				t = day + "天" + (hour<10?'0'+hour+ ":":hour + ":");
				}
			else t =hour<10?'0'+hour+":":hour + ":";                       
			if(min < 10){t += "0";}
				t += min + ":";
			if(sec < 10){t += "0";}
				t += sec;
		}
		return t;
	};

    GoodList.prototype.time_division=function(time){
        if(time.indexOf('天')!=-1){
            var index=(time.indexOf('天'))+1;
            var day=time.slice(0,index);
            var second=time.substr(index);
            var time='<b>'+day+'</b><span>'+second+'</span>';
        }else{
            var time='<span>'+time+'</span>';
        }
        
        return time;
    }

  template.helper('showtime2', function(starttime,endtime, selector ){
             
            
             
            var time=endtime-starttime;  
             
            if(time>0){
                
                 t=setInterval(function(){
		 time--;
                 if(time==0){ location.reload(); }
                    var str= GoodList.prototype.arrive_timer_format(time);
                  
                     $(selector).html(str);
                    
                },1000);
               
            }
         
           
              
        });


var Item=function(data){
        if(cheaking(data)){
            this.data=data;
            this.num='';
            this.init();
            this.swipe();
            this.event();
        }
    };
    Item.prototype.init=function(){
         
        
        var html=Render.tpl(SwiperTpl,this.data.data);
        $('.swiper-wrapper').html(html);
        var html1=Render.tpl(tpl_item,this.data.data);
        $('.d_contant2').html(html1);
        var html2=Render.tpl(tplhideTpl,this.data.data);
        $('.d_tan').html(html2);
        var html3=Render.tpl(foottpl,this.data.data);
        $('.d_footer1').append(html3);
        var str=this.htmlspecialcharsDecode(this.data.data.desc);
        $('.d_show').html(str);
//        var html4=Render.tpl(wechat_tpl,this.data.data);
//        $('.item-active').html(html4);

     
    };
    Item.prototype.swipe=function(){
        var swiper = new Swiper('.swiper-container', {
                        pagination: '.swiper-pagination',
                        paginationClickable: true,
                        spaceBetween: 30,
                        centeredSlides: true,
                        autoplay: 2000,
                        autoplayDisableOnInteraction: false
                    });
    };
    Item.prototype.htmlspecialcharsDecode=function(str){   
        str = str.replace(/&amp;/g, '&'); 
        str = str.replace(/&lt;/g, '<');
        str = str.replace(/&gt;/g, '>');
        str = str.replace(/&quot;/g, "\"");  
        str = str.replace(/&#039;/g, "'");  
        str=str.replace(/\/Uploads/gm,'http://admin.com/Uploads');
        return str;  
    };
    Item.prototype.event=function(){
        $('.active-details-tip-btn').click(function(){
            $('.active-details-line').slideToggle('1000');
            return '';
        })


        $('#submit-btn').click(function() {
            $('.d_zhe').fadeIn(200);
            $('.d_tan').slideDown(200);
        });
        $('.d_guanbi ').click(function() {
            $('.d_zhe').fadeOut(200);
            $('.d_tan').slideUp(200);
        });
        $('.d_fenlei>ul li').click(function() {
            $(this).addClass('active_').siblings().removeClass('active_');
            $('#totlePrice').text($(this).attr('data-price'));
            $('#price').text($(this).attr('data-price1'));
            if($(this).attr('data-status')==0){
                var data=$(this).data();
                getItemQuantity($(this),data);
            }
        });
        var a = parseInt($('#number').text());
        $('#d_jian').click(function() {
            if (a > 1) {
                a = a - 1;
                $('#number').text(a);
            }
        });
        $('#d_jia').click(function() {
            if($('.active_').size()>0){
                var totle=parseInt($('.active_').attr('data-num'));
                var num=parseInt($('#number').text());
                if(num<totle){
                    a = a + 1;
                    $('#number').text(a); 
                }else{
                    $.toast('最多只能购买这么多', "forbidden");
                }
            }else{
                $.toast('请选择规格', "forbidden");
            }
        });
        $('.d_que').on('click',function(){
            var item_id=$('#itemid').attr('data-itemid');
            var sku_id=$('.active_').attr('data-sku');
            var num=$('#number').text();
            var _s=$('.d_fenlei').find('.active_').size();
            var _obj='';
            if(sku_id==undefined || sku_id==null){
                sku_id='';
            }
            if(num==undefined || num==null){
                num='';
            };
            _obj={item_id:item_id,sku_id:sku_id,num:num};
            window.localStorage.order=JSON.stringify(_obj);
            if($('.guige').size()>0){
                if(_s>0){
                    location.href="../order/order.html?item_id="+item_id+"&sku_id="+_obj.sku_id+"&num="+_obj.num;
                }else{
                    $.toast('请选择规格', "forbidden");
                }
            }else{
                location.href="../order/order.html?item_id="+item_id+"&sku_id="+_obj.sku_id+"&num="+_obj.num;
            }
            return false;
        })
        function getItemQuantity($selector,data){
            var lv=new ajax({
                                url:apiurl.item_quantity,
                                data:{item_id:data.itemid,sku_id:data.sku},
                                type:'get',
                                success:function(data){
                                            $selector.attr('data-status','1');
                                            $selector.attr('data-num',data.quantity);
                                        }
                            });
        }
    };