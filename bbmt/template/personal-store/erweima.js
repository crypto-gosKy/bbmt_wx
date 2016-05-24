window.onload = function() {
	$.ajax({
		type: "get",
		url:apiurl.erweima,
		success: function(data) {
			var food = {
				data: data,
				init: function(data) {

					var html = template('d_art', food.data);
					$(".d_art").html(html);
				}
			}
			food.init();
			console.log(data);
		}
	});

}