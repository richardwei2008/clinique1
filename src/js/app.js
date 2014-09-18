/*global $, jQuery, window: true, browser: true, alert: true, BMap, AppConfig, Beyond*/
var App = {};
App = {
    Config: AppConfig,
    alert: function (obj) {
        "use strict";
        Beyond.Common.alert(this.Config.DEBUG, obj);
    },
    globalUser: {},

    initChoose: function () {
        "use strict";
        //        App.alert("Init leave");
        App.globalUser = Beyond.WeChat.requireOauth();
        var userIcon = "images/unknown.png";
        if (App.globalUser !== null
            && App.globalUser.headimgurl !== null
            && App.globalUser.headimgurl !== "") {
            userIcon = App.globalUser.headimgurl;
        }
//        App.alert(userIcon);
        $("#photo").attr("src", userIcon);
    }

};




