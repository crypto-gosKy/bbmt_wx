$(function(){
  var lv=new Login();
});
jQuery.support.cors = true;
var Login=function(){
        this.lv=new Validator();
        this.init();
        this.event();
        this._findPassWord();
    };
    Login.prototype.init=function(){
        if(location.href.indexOf('register')!=-1){
            new PCAS("province","city","district");
        }
    };
    Login.prototype.event=function(){
        var _this=this;
        $('#index1').click(function() {
            var _username=$('#phone').val();
            var _password=$('#password').val();
            if(_this.lv.all()){
                var lv=new ajax({
                    url:apiurl.login,
                    data:{username:_username,password:_password},
                    success:function(data){
                        if(data.isbd) {
                            location.href="template/personal-store/mine.html";
                        } else {
                            location.href="template/cats/cats.html";
                        }
                    }
                });
            };
        });
    };
    Login.prototype._findPassWord=function(){
        var _this=this;
        $('#yanzheng').on('click',function(){
            var _mobile=$('#phone').val();
            if($(this).attr('data-status')==0){ 
                if(_this._Vcode(_mobile)){
                    second();
                    $.post(apiurl.getcode,{mobile:_mobile,tpl_id:'restpwd'},function(data){
                        if(data.return_code==0){
                            $('#yanzheng').attr('data-status',1);
                            $.toast(data.data);
                        }else{
                            $.toast(data.return_msg, "forbidden");
                        }
                    });
                }
            }
           
        });
        $('#yanzheng-btn').on('click',function(){   //找回密码
            var _mobile=$('#phone').val();
            var _password=$('#password').val();
            var _password2=$('#password1').val();    
            var _code=$('#code').val();
            if(_this.lv.all()){
                 $.post(apiurl.restpwd,{username:_mobile,password:_password,password2:_password2,code:_code},function(data){
                     if(data.return_code==0){
                        $.toast(data.return_msg);
                        setTimeout(function(){
                            location.href="../../index.html";
                        },1000);                        
                     }else{
                        $.toast(data.return_msg, "forbidden");
                     }
                 });
            }
        });   
        $('#next').on('click',function(){
            var _mobile=$('#phone').val();
            var _password=$('#password').val();
            var _password2=$('#password1').val();    
            var _code=$('#code').val();
            _this._Vcode(_mobile);
            if(_code==''){
                $.toast('验证码不能为空', "forbidden");
                return false;
            }
            if(_password!='' && _password2!=''){
                if(_password.length<6 || _password2.length<6){
                    $.toast('密码长度不能小于六位', "forbidden");
                    return false;
                }else{
                    if(_password!=_password2){
                        $.toast('两次密码不一致', "forbidden");
                        return false;
                    }
                }
            }else{
                $.toast('密码不能为空', "forbidden");
                return false;
            }
            
            $.post(apiurl.reg,{username:_mobile,password:_password,password2:_password2,code:_code},function(data){
                $('#userId').val(data.data.user_id);
                if(data.return_code==0){
                    $.get(apiurl.mine,function(data){
                        if(data.return_code==0){
                            $('.d_tou').html('<img src="'+data.data.avatar_url+'" />');
                        }else{
                            $('.d_tou').html('<img src="../../grey.jpg" />');
                        }
                    })
                    $('#regTpl_2').fadeIn(300);
                    $('#regTpl_1').hide();
                }else{
                    $.toast(data.return_msg || '提交失败',"forbidden");
                }
            })
        })
         $('#zhuce').on('click',function(){  //注册
            var userId=$('#userId').val();
            var _store_name=$('#dname').val();
            var _master_name=$('#master_name').val();
            var tr = $('#city-picker').val();
            var state= $('#province').find('option:selected').text();
            var city= $('#city').find('option:selected').text();
            var district= $('#district').find('option:selected').text();
            var address= $('#address').val();
            var address = $('#address').val();
            if(_this.lv.all()){
                 $.post(apiurl.regStore,{user_id:userId,store_name:_store_name,master_name:_master_name,state:state,city:city,district:district,address:address},function(data){
                     if(data.return_code==0){
                        $.toast(data.return_msg);
                        setTimeout(function(){
                             location.href="../../index.html";
                        },1000);  
                       
                     }else{
                        $.toast(data.return_msg || '提交失败', "forbidden");
                     }
                 });
            }
        });
        function second(){
            var s=60;
            var t=setInterval(function(){
                    s=s-1;
                    $('#yanzheng').text(s+'s');
                    if(s==0){
                        $('#yanzheng').text('重新获取');
                        $('#yanzheng').attr('data-status',0);
                        clearInterval(t);
                    }
            },1000);
        };
        
    };
    Login.prototype._Vcode=function(value){
        var reg=/^1[3|4|5|7|8]\d{9}$/;
        if(!(reg.test(value))){
           $.toast('手机号码格式有误', "forbidden");
           return false;
        }else{
           return true;
        }     
    };
                          
