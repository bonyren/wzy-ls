(function($){
    function setHeight(target){
        var opts = $(target).textbox('options');
        $(target).next().css({
            height: '',
            minHeight: '',
            maxHeight: ''
        });
        var tb = $(target).textbox('textbox');
        tb.css({
            height: 'auto',
            minHeight: opts.minHeight,
            maxHeight: opts.maxHeight
        });
        tb.css('height', 'auto');
        var height = tb[0].scrollHeight;
        tb.css('height', height+'px');
    }

    function autoHeight(target){
        var opts = $(target).textbox('options');
        var onResize = opts.onResize;
        opts.onResize = function(width,height){
            onResize.call(this, width, height);
            setHeight(target);
        }
        var tb = $(target).textbox('textbox');
        tb.unbind('.tb').bind('keydown.tb keyup.tb', function(e){
            setHeight(target);
        });
        setHeight(target);
    }
    $.extend($.fn.textbox.methods, {
        autoHeight: function(jq){
            return jq.each(function(){
                autoHeight(this);
            })
        }
    });
})(jQuery);
/**********************************************************************************************/
(function($) {
    function build(target, options, data) {
        if(data && data.length > 0) {
            $.each(data, function(index,row) {
                var item = $("<li/>").addClass('easyui-timeline-item');
                item.append('<i class="easyui-timeline-axis"/>');
                var content = $('<div class="easyui-timeline-content easyui-text"/>');
                content.append('<h3 class="easyui-timeline-title">' + this.time + '</h3>');
                var body = $('<div class="easyui-timeline-body"/>');

                body.html(this.content);
                content.append(body);

                item.append(content);

                if(options.onClick) {
                    item.on('click', function() {
                        options.onClick.call(target, row);
                    });
                }

                target.append(item);
            });

            if(options.onComplete) {
                options.onComplete.call(target, data);
            }
        }
    }

    $.fn.timeline = function(options) {
        if(typeof options == "string") {
            var params = [];
            for(var i = 1; i < arguments.length; i++) {
                params.push(arguments[i]);
            }
            this.each(function() {
                $.fn.timeline.methods[options].apply(this, params);
            });
            return this;
        }
        options = options || {};

        return this.each(function() {
            var data = $.data(this, "timeline");
            var newOptions;
            if(data) {
                newOptions = $.extend(data.options, options);
                data.opts = newOptions;
            } else {
                newOptions = $.extend({}, $.fn.timeline.defaults, $.fn.timeline.parseOptions(this), options);
                $.data(this, "circle", {
                    options: newOptions
                });
            }

            var target = $(this);
            target.addClass('easyui-timeline');
            if(newOptions.url) {
                $.ajax({
                    type: "get",
                    url: newOptions.url,
                    dataType: 'json',
                    success: function(data) {
                        if(newOptions.onLoadSuccess) {
                            newOptions.onLoadSuccess.call(target, data);
                        }
                        build(target, newOptions, data);
                    }
                });
            } else if(newOptions.data && newOptions.data.length > 0) {
                build(target, newOptions, newOptions.data);
            }
        });
    }

    $.fn.timeline.methods = {

    }

    $.fn.timeline.parseOptions = function(target) {
        return $.extend({}, $.parser.parseOptions(target, ["data", "url", {
            data: "array",
            url: "string"
        }]));
    };

    $.fn.timeline.defaults = {
        data: [],
        url: '',
        onLoadSuccess: function(data) {

        },
        onComplete:function(data){

        },
        onClick:function(item){

        }
    }

    $.parser.plugins.push('timeline');
})(jQuery);
/***********************************************************************************************************************/
$(function(){
    function buildAttaches($target, options){
        if(options.readOnly){
            var url = sprintf('/index.php/index/upload/viewAttaches.html?attachmentType=%d&externalId=%d&uiStyle=%d&callback=%s&prompt=%s&fit=%d',
                options.attachmentType,
                options.externalId,
                options.uiStyle,
                options.callback,
                encodeURIComponent(options.prompt),
                options.fit?1:0);
        }else{
            var url = sprintf('/index.php/index/upload/attaches.html?attachmentType=%d&externalId=%d&uiStyle=%d&callback=%s&prompt=%s&fit=%d',
                options.attachmentType,
                options.externalId,
                options.uiStyle,
                options.callback,
                encodeURIComponent(options.prompt),
                options.fit?1:0);
        }

        var $panel =$('<div data-options="border:false,' +
                'minimizable:false,' +
                'maximizable:false,' +
                'fit:' + (options.fit?'true,':'false,') +
                'href:\'' + url + '\'">' +
                '</div>').appendTo($target);

        $panel.panel();
        //$.parser.parse($panel);
    }
    function buildAttachesComplex($target, options){
        options.readOnly = options.readOnly ? 1 : 0;
        options.fit = options.fit ? 1 : 0;
        options.title = options.title ? options.title : '';
        var url = '/index.php/index/upload/attachesComplex.html?' + $.param(options);
        var $panel =$('<div data-options="border:false,\
                minimizable:false,\
                maximizable:false,\
                fit:' + (options.fit?'true':'false') + ',\
                href:\'' + url + '\'"> \
                </div>').appendTo($target);
        $panel.panel();
        //$.parser.parse($panel);
    }
    $.fn.attaches = function(options){
        if(typeof options == 'string'){
            var params = [];
            for(var i=1; i<arguments.length; i++){
                params.push(arguments[i]);
            }
            $.fn.attaches.methods[options].apply(this, params);
            return this;
        }
        options = options || {};
        this.each(function(){
            var newOptions = $.extend({}, $.fn.attaches.defaults, $.fn.attaches.parseOptions(this), options);
            var $target = $(this);
            buildAttaches($target, newOptions);
        });
        return this;
    }
    $.fn.attachesComplex = function(options){
        if(typeof options == 'string'){
            var params = [];
            for(var i=1; i<arguments.length; i++){
                params.push(arguments[i]);
            }
            $.fn.attaches.methods[options].apply(this, params);
            return this;
        }
        options = options || {};
        this.each(function(){
            var newOptions = $.extend({}, $.fn.attaches.defaults, $.fn.attaches.parseOptions(this), options);
            var $target = $(this);
            buildAttachesComplex($target, newOptions);
        });
        return this;
    }
    $.fn.attaches.methods = {
    }
    $.fn.attaches.parseOptions = function(target) {
        return $.extend({}, $.parser.parseOptions(target, ["attachmentType", "externalId", "callback", "uiStyle", "readOnly", "prompt", "fit", {
            attachmentType: "number",
            externalId: "number",
            uiStyle: "number",
            callback: "string",
            readOnly: "boolean",
            prompt: "string",
            fit: "boolean"
        }]));
    };
    $.fn.attaches.defaults = {
        attachmentType:1,
        externalId:0,
        uiStyle:1,
        callback:'',
        readOnly:false,
        prompt:'',
        attachesFit:false
    }
    $.parser.plugins.push('attaches');
    $.parser.plugins.push('attachesComplex');
});
/***********************************************************************************************************************/