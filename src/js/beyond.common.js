/*global $, jQuery, window: true, BMap, AppConfig*/
var Beyond = {
    namespace : function (ns) {
        var parts = ns.split("."),
            object = this,
            i, len;

        for (i=0, len=parts.length; i < len; i++) {
            if (!object[parts[i]]) {
                object[parts[i]] = {};
            }
            object = object[parts[i]];
        }
        return object;
    }
};
Beyond.namespace("Common");
Beyond.Common = {
    alert: function (isEnabled, obj) {
        "use strict";
        if (isEnabled) {
            window.alert(obj);
        }
    },
    sprintf: function (text) {
        "use strict";
        var i = 1,
            args = arguments;
        return text.replace(/%s/g, function () {
            return (i < args.length) ? args[i++] : "";
        });
    }
};