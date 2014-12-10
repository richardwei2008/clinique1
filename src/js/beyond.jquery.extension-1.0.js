/**
 * Created by Administrator on 14-8-13.
 */
(function($){
    $.getUrlParam = function(name) {
        var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if (r!=null) return decodeURI(r[2]); return null;
    }
    $.getQueryStr = function (str) {
        var LocString = String(window.document.location.href);
        var rs = new RegExp("(^|)" + str + "=([^/&]*)(/&|$)", "gi").exec(LocString), tmp;
        if (tmp = rs) {
            return tmp[2];
        }
        // parameter cannot be found
        return "";
    }
})(jQuery)