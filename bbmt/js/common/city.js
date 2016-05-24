$(function(){
  var cityTpl=[
        '{{each list as v}}',
            '<option value="{{v.code}}">{{v.name}}</option>',
        '{{/each}}'
  ].join('');
  var data=province;            
  var SelectCity=function(data){
    this.init(data);
  }  
  SelectCity.prototype.init=function(data){
    var render = template.compile(cityTpl);
    var html = render(data);
    $('#province').append(html);
    $('#province').on('change',function(){
       var a=$(this).find('option:checked').val().slice(0,2);
       var data=city.list;
       var str='';
       $.each(data,function(i,d){
            if(d.code.slice(0,2)==a){
                str+="<option value="+d.code+">"+d.name+"</option>";
            }
       })
       $('#city').html('<option value="">请选择市</option>'+str);
       $('#district').html('<option value="">请选择区/县</option>');

    })
    $('#city').on('change',function(){
       var a=$(this).find('option:checked').val().slice(0,4);
       var data=district.list;
       var str='';
       $.each(data,function(i,d){
            if(d.code.slice(0,4)==a){
                str+="<option value="+d.code+">"+d.name+"</option>";
            }
       })
       $('#district').html(str);
    })
  };
  new SelectCity(data);
})
