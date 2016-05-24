var Render={};
Render.tpl=function(tpl,data){
    var render = template.compile(tpl);
    var html = render(data);
    return html;
};




