
var Validator=function(){
       this.$selector=$('.require');
    };
    Validator.prototype.all=function(){
       var error=0;
       var message='';
       $.each(this.$selector,function(i,d){
           var _val=$(d).val();
           var type=$(d).attr('data-type');
           if(type==undefined || type==''){
               if(!(_val)){
                   error++;
                   message='输入框不能为空';
                   return false;
               }
           }
           if(type=='mobile'){
               if(!(/^1[3|4|5|7|8]\d{9}$/.test(_val))){
                   error++;
                   message='号码格式有误';
                   return false;
               }
           }
           if(type=='idCard'){
                var reg = /^[1-9]{1}[0-9]{14}$|^[1-9]{1}[0-9]{16}([0-9]|[xX])$/;
                if (reg.test(_val)) {
                    return true;
                } else if ($.trim(_val).length == 0) {
                    error++;
                    message='请输入您的身份证号码';
                    return false;
                } else {
                    error++;
                    message='请输入正确的身份证号码';
                    return false;
                }
           }
           if(type=='china'){
                var reg=/^[\u0391-\uFFE5A-Za-z]+$/;
                if(reg.test(_val)){
                    return true;
                }else{
                    error++;
                    message='只能输入中英文';
                    return false;
                }
           }
           if(type=='password'){
               //var reg=/^(([a-z]+[0-9]+)|([0-9]+[a-z]+))[a-z0-9]*$/i;
               if(_val.length<6){
                   error++;
                   message='密码不能少于6位';
                   return false;
               }

           }
           if(type=="passwordTip"){
               var id1=$('#password').val();
               if(id1!=_val){
                   error++;
                   message='两次密码不一致';
                   return false;
               }
           }//验证两个密码一致时第一个密码框ID为password 第二个框 加上type==passwordTip
       });
       if(error>0){
           $.toast(message, "forbidden");
           return false	
       }else{
           return true;
       };
   };


$(function(){




   $('#phone').blur(function() {
        function isPhoneNo(phone) {
           var pattern = /^1[34578]\d{9}$/;
           return pattern.test(phone);
        };
        if ($.trim($('#phone').val()).length == 0) {
           $.toast("请输入手机号码", "forbidden");
        } else {
           if (isPhoneNo($('#phone').val()) == false) {
                $.toast("请输入正确的手机号码", "cancel");
            }
//			else{
//					$('#yanzheng').click(function() {
//					
//		//		$('#btn').css('background-color', '#DEDEDE')
//		//		$('#btn').css('color', '#787878')
//		var a = 60;
//		c = setInterval(function time() {
//			a = a - 1;
//			$('#yanzheng').html("" + a + "s");
//			if (a <= 0) {
//				clearInterval(c);
//                
//				$('#yanzheng').html("重新发送");
//			};
//		}, 1000)
//	})}
        };
    });
    $('#password').blur(function() {
        var a = $('#password').val();
        if (a == '') {
            $.toast("请输入密码", "forbidden");
            //      } else if (a !== true) {
            //          $.toast("密码错误", "cancal");
        }
    });
    $('#password1').blur(function() {
        var a = $('#password1').val();
        var e = $('#password').val();
        if (a == '') {
            $.toast("请再次输入密码", "forbidden");
        } else {
            if (a == e) {} else {
                $.toast("两次密码不一致", "cancel");
            }
        }
    });
    $('#idcard').blur(function() {
        var reg = /^[1-9]{1}[0-9]{14}$|^[1-9]{1}[0-9]{16}([0-9]|[xX])$/;
        if (reg.test($('#idcard').val())) {
            return true;
        } else if ($.trim($(this).val()).length == 0) {
            $.toast("请输入您的身份证号码", "forbidden");
            //                      $(this).val('请输入您的身份证号码');
        } else {
            $.toast("请输入正确的身份证号码", "forbidden");
        }
    });
    $('#dname').blur(function() {
        var a = $('#dname').val();
        if (a == '') {
            $.toast("请输入店铺名称", "forbidden");
        } else {}
    });
	$('#address').fucous
    $('#address').blur(function() {
        var a = $('#address').val();
        if (a == '') {
            $.toast("请输入详细地址", "forbidden");
        } else {}
    });

});
