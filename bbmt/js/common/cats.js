function cats(data){
    if(data.return_code>0){
        if(data.return_code==100){
            location.href="../../";
        }else{
            $.toast(data.return_msg, "cancel");
        }
    }else{
          var data=data.data;
          var lv=new Sort(data);
    }
};
var left1Tpl=[
    '{{each cat_list_1 as data i}}',
                '<li {{if i==0}}class="d_active"{{/if}} data-id="{{i}}" >{{data.cat_name}}</li>',
    '{{/each}}'
].join('');

var left2Tpl=[
    '{{each cat_list_2 as data i}}',
                '<li {{if i==0}}class="d_active"{{/if}} data-id="{{i}}" >{{data.cat_name}}</li>',
    '{{/each}}'
].join('');

var rightTpl=[
            '<li>',
                '{{if cid==0}}',
                    '<h3>热门推荐</h3>',
                '{{else}}',
                    '<h3>分类</h3>',
                '{{/if}}',
                '<div class="category">',
                    '{{each sub_cat_list as data i}}',
                        '<a href="../Item/items.html?cat_id={{data.cid}}&type={{cid | zyz}}" class="category_list">',
                            '{{data.cat_name}}',
                        '</a>',
                    '{{/each}}',
                '</div>',
            '</li>',     
            '<li>',  
                '{{if cid==0}}',
                    '<h3>热卖品牌</h3>',
                '{{else}}',
                    '<h3>品牌</h3>',
                '{{/if}}',
                '<div class="category">',
                    '{{each brand_list as data i}}',
                        '<a href="../Item/items.html?cat_id={{cid}}&brand_id={{data.brand_id}}&type={{cid | zyz}}" class="category_img">',
                           '<img src="{{data.pic_url}}" width="80%"/>',
                            '<label>{{data.brand_name}}</label>',
                        '</a>',
                    '{{/each}}',
                '</div>',
            '</li>'
].join('');
var Sort=function(data){
    this.data=data;
    this.parent=2;
    this.initLeft(0);
    this.initRight(0);
    this.event();
};
Sort.prototype.initLeft=function(index){
    var tpl=index==0?left2Tpl:left1Tpl;
    var html=Render.tpl(tpl,this.data);
    $('.d_left').html(html);
};
Sort.prototype.initRight=function(index){
    var _this=this;
    template.helper('zyz', function() {
        return _this.parent;
    });
    var data=this.parent==1?this.data.cat_list_1:this.data.cat_list_2;
    var html=Render.tpl(rightTpl,data[index]);
    $('.d_right').html(html)
};
Sort.prototype.event=function(){
    var _this=this;
    var a = $(window).height()-$('.d_wrap').offset().top-50;
    $('.d_left').height(a);
    $('.d_right').height(a);
    $('.d_tab ').on('click','li',function() {
        var line=$(this).data();
        $('.xtab-underline').css('transform','translateX('+line.x+') translateZ(0px)')
        $(this).addClass('d_bor_active').siblings().removeClass('d_bor_active');
        var Index = $(this).index();
        _this.parent=2-Index;
        _this.initLeft(Index);
        _this.initRight(0);
    });
    $('.d_left').on('click','li',function() {
        var id=$(this).attr('data-id');
        $(this).addClass('d_active').siblings().removeClass('d_active');
        _this.initRight(id);
    });
    $('.search_bar').on('click','.search',function(){
        location.href='../Item/items.html?keyword=';
    });
};

$(function(){
    var oHead = document.getElementsByTagName('HEAD').item(0); 
    var oScript= document.createElement("script"); 
    oScript.type = "text/javascript"; 
    oScript.src=siteConfig.cat_list+'?callback=cats&t='+new Date().getTime(); 
    oHead.appendChild(oScript); 
});