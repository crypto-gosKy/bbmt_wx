window.onload = function () {
    $.ajax({
        type: "get",
        url: apiurl.mine,

        success: function (data) {
            if (data.return_code > 0) {
                $.toast(data.return_msg, "forbidden");


            }
            else {
                $('.d_wrap0').show();
                var food = {
                    data: data,
                    init: function (data) {
                        $('.d_banner1').css("background",'url(' + data.data.avatar_url + ') no-repeat center center / 100%');
                        var html = template('d_mine', food.data);
                        $(".d_banner1").html(html);
                    }
                }
                food.init(data);
            }
        }
    });

}