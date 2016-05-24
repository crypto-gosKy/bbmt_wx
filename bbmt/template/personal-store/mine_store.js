window.onload = function() {

        totalpage=0;//总页数
	$.ajax({

		type: "get",
		url:apiurl.mine_store,
		success: function(data) {
				if(data.return_code>0){
				$(".d_bao").css("background-color","#eee");		
				$(".d_bao").append('<div class="d_kong"><p style="font-size:25px;margin-bottom:15px">sorry!</p><p style="font-size:15px"></p></div>');
			
				$(".d_bao p:last-of-type").html(data.return_msg);
					
				}
				else{
                               $(".d_bao1").css("display","block"); 
                               totalpage=data.data.total_page_num;
                               var html = template('d_mine_store', data.data); 
				$("#listdata").html(html);
							
			}
		}
		
	});
	  var loading = false;
        var lv=getMore();
        $(document.body).infinite().on("infinite", function() {
          if(loading) return;
          loading = true;
          $('.weui-infinite-scroll').show();
          lv();
        });
    function getMore(){
            var page=1;
            return function(){
         
                        if(totalpage>page){
                            page++;
                            var lv=new ajax({
                            'url':apiurl.mine_store+'&page='+page,
                            type:'get',
                            success:function(data){
                                   $(".d_bao1").css("display","block");
                                   // var html=Render.tpl('d_mine_store',data.data);
                                    var html = template('d_mine_store', data);
                                    $('#listdata').append(html); 
                                    loading = false;
                                    $('.weui-infinite-scroll').hide();
                                   // _this.lazyload();
                                }
                            })
                        }else{
                            $('.weui-infinite-scroll').remove(); 
                        }
                        
                 };
        };
}