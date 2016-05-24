$(function(){
  $('body').on('blur','input',function(){
      var id=parseInt($(this).attr('data-auto'))+1;
      $.each($('input'),function(i,d){
        var x=parseInt($(d).attr('data-auto'));
            if(x==id){
                $(d).focus();
            }
      })
  })
});
