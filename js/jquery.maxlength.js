(function(a){a.fn.maxlength=function(b){
    var c=a(this);
    return c.each(function(){b=a.extend({},
        {counterContainer:!1,
            text:"%left"},b);
    var c=a(this),d={options:b,field:c,counter:a('<span class="maxlength"></span>'),
    maxLength:parseInt(c.attr("maxlength"),10),
    lastLength:null,
    updateCounter:function(){
        var b=this.field.val().length,
        c=this.options.text.replace(/\B%(length|maxlength|left)\b/g,a.proxy(function(a,c){
            return"length"==c?b:"maxlength"==c?this.maxLength:this.maxLength-b},this));this.counter.html(c),b!=this.lastLength&&this.updateLength(b)},
        updateLength:function(a){
            this.field.trigger("update.maxlength",[this.field,this.lastLength,a,this.maxLength,this.maxLength-a]),this.lastLength=a}};
            d.maxLength&&(d.field.data("maxlength",d).bind({"keyup change":function(){
                a(this).data("maxlength").updateCounter()},
                "cut paste drop":function(){setTimeout(a.proxy(function(){a(this).data("maxlength").updateCounter()},this),1)}}),
            b.counterContainer?b.counterContainer.append(d.counter):d.field.after(d.counter),d.updateCounter())}),c}})(jQuery);