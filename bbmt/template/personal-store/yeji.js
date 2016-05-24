window.onload = function() {

	$.ajax({
		type: "get",
		url: apiurl.yeji,
		success: function(data) {
				if(data.return_code>0){
					$(".d_rcontent").css("background-color","#eee");	
				$(".d_rcontent").append('<div class="d_kong"><p style="font-size:25px;margin-bottom:15px">sorry!</p><p style="font-size:15px"></p></div>');
			
				$(".d_rcontent p:last-of-type").html(data.return_msg);
					
				}
				else{

			var food = {
				data: data,
				init: function(data) {
					var html = template('d_yeji', food.data);
					$(".d_rcontent").html(html);
				}
			}
			food.init();

		}}
	})

}