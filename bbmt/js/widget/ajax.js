    jQuery.support.cors = true;  //允许跨域
    var ajax = function (settings) {
        var _this = this;
        this.settings = $.extend({
            type: "post",
            url: "",
            data: {},
            success: $.noop,
            error: $.noop,
            async: true,
            prr: false,
            complete: $.noop,
            timeout: 30000,
            showLoading: true,
            LoadUrl:"",
            crossDomain:true
        }, settings);

         
        // 防重复提交
        // if (this.settings.prr !== false) {
        //     var key = this.settings.url;
        //     if (this.settings.data) {
        //         key += typeof this.settings.data === "object" ? $.param(this.settings.data) : this.settings.data;
        //     }
        //     if (preventRepeatRequest.prevent(key)) {
        //         return;
        //     }
        // }
        // this.settings.url = siteConfig.apiUrl + this.settings.url;
        this.settings.success = function (response) {
            // response = JSON.parse(response);
            if (response.return_code==0) {
                var response = response.data;
                settings.success && settings.success(response);
            }
            else {
                $.toast(response.return_msg || "服务器忙，请稍后再试","forbidden");
            }
        };
        this.settings.error = function (response) {
            
        };
        this.settings.beforeSend = function (data) {
            // if (_this.settings.showLoading) {
            //     loading.show();
            // }
            // settings.beforeSend && settings.beforeSend();
        }
        this.settings.complete = function (jqXHR, textStatus) {

            if (textStatus === "timeout") {
                $.toast('请求超时，请稍后再试', "forbidden");
            }
            else if (textStatus === "error" || textStatus === "parsererror" || textStatus === "abort") {
                $.toast('请求超时，请稍后再试', "forbidden");
            }
            //if (_this.settings.showLoading) {
            // loading.hide();
            //}
            settings.complete && settings.complete();
        };
        // if (typeof this.settings.data === "string") {
        //     this.settings.data = base.deserialize(this.settings.data);
        // }
        // this.settings.data.platform = "h5";
        // if (user) {
        //     if (user.userInfo) {
        //         this.settings.data.userId = user.userInfo.id;
        //     }
        //     if (user.artist && !this.settings.data.artistId) {
        //         this.settings.data.artistId = user.artist.id;
        //     }
        //     this.settings.data.loginVerification = user.userName;
        //     this.settings.data.passwordVerification = md5(user.password);
        // }
        return $.ajax(this.settings);
    };