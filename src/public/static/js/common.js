var GLOBAL = {};
GLOBAL.namespace = function(str){
    var arr = str.split("."),o=GLOBAL;
    for(var i=((arr[0] == 'CACHE')?1:0); i<arr.length; i++){
        o[arr[i]] = o[arr[i]] || {};
        o=o[arr[i]];
    }
};
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
GLOBAL.namespace('func');
GLOBAL.func.random = function(min, max){
    var Range = max - min;
    var Rand = Math.random();
    return(min + Math.round(Rand * Range));
};
GLOBAL.func.moneyFormat = function(num){
    if(!num){
        return '';
    }
    if(typeof(num) == 'number'){
        num = num.toFixed(2);
    }
    var numStr = num.toString();
    numStr = numStr.replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
    return numStr;
}
GLOBAL.func.byteFormat = function(num){
    if(!num){
        return '';
    }
    if(typeof(num) == 'number'){
        num = num.toFixed(2);
    }
    var numStr = num.toString();
    numStr = numStr.replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
    numStr = numStr.replace('.00', '');
    return numStr;
}
GLOBAL.func.dateFilter = function(date){
    if(date == '0000-00-00' || date == '1970-01-01'){
        return '';
    }
    return date;
}
GLOBAL.func.dateTimeFilter = function(dateTime){
    if(dateTime == '0000-00-00 00:00:00' || dateTime == '1970-01-01 00:00:00'){
        return '';
    }
    return dateTime;
}
GLOBAL.func.addUrlParam = function(url, name, value){
    url += url.indexOf('?') === -1?'?':'&';
    url += encodeURIComponent(name) + '=' + encodeURIComponent(value);
    return url;
}
GLOBAL.func.escapeALinkStringParam = function(str) {
    if(!str){
        return str;
    }
	//转换半角单引号
	str = str.replace(/'/g, "\\'");
    str = str.replace(/"/g, "");
	return str;
}
GLOBAL.func.formatBoolean = function(val){
    if(val){
        return '<span class="badge badge-success">是</span>';
    }else{
        return '<span class="badge badge-warning">否</span>';
    }
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
GLOBAL.namespace('css');
GLOBAL.css.table = {
		trGray: 'color:#999;background-color:#F3F3F3;',
		trWarn: 'color:#FF0000;background:#FFB90F;',
		trError: 'color:#FF0000;background:#FFF8DC;',
		trDel: 'text-decoration:line-through;',
		trSuc: 'color:#5ebfef;background-color:#f5f7d6;'
};
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
GLOBAL.namespace('listTree');
GLOBAL.listTree.lastSelectedNodeId = '0';
var DEFAULT_DB_DATE_VALUE = '0000-00-00';
var DEFAULT_DB_DATETIME_VALUE = '0000-00-00 00:00:00';
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
var commonModule = {
    IsEmptyObject: function(obj){
        for(var n in obj){return false}
        return true;
    }
}

var HtmlUtil = {
    /*1.用浏览器内部转换器实现html转码*/
    htmlEncode:function (html){
        /*
        //1.首先动态创建一个容器标签元素，如DIV
        var temp = document.createElement ("div");
        //2.然后将要转换的字符串设置为这个元素的innerText(ie支持)或者textContent(火狐，google支持)
        (temp.textContent != undefined ) ? (temp.textContent = html) : (temp.innerText = html);
        //3.最后返回这个元素的innerHTML，即得到经过HTML编码转换的字符串了
        var output = temp.innerHTML;
        temp = null;
        return output;*/
        return html
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    },
    /*2.用浏览器内部转换器实现html解码*/
    htmlDecode:function (text){
        //1.首先动态创建一个容器标签元素，如DIV
        var temp = document.createElement("div");
        //2.然后将要转换的字符串设置为这个元素的innerHTML(ie，火狐，google都支持)
        temp.innerHTML = text;
        //3.最后返回这个元素的innerText(ie支持)或者textContent(火狐，google支持)，即得到经过HTML解码的字符串了。
        var output = temp.innerText || temp.textContent;
        temp = null;
        return output;
    }
};