/*global $, jQuery, window: true, Beyond*/
// Beyond.Common.namespace("Beyond").WeChat = {};
Beyond.namespace("WeChat");
Beyond.WeChat = {
    DEBUG: true,
    // REDIRECT_URI_TEMPLATE: "http%3A%2F%2F" + "%s" + "%2F" + "%s", // (%s, %s) <= (window.location.hostname, attendance.html)
    HTTP_OAUTH_CODE_URI: "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxc63c757bdae5dd41&redirect_uri=" + "%s" + "&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect", // (%s) <= (redirect_uri)
    OAUTH_URL: "http://" + window.location.hostname + "/oauth2.php",
    isWeiXin: function () {
        "use strict";
        var ua = window.navigator.userAgent.toLowerCase(),
            matches = ua.match(/MicroMessenger/i);
        return (matches !== null && matches.length >= 0 && matches[0] === 'micromessenger');
    },
    formatRedirectUrl: function (pagename) {
        "use strict";
        var redirectTemplate = window.location.href.substring(0, window.location.href.lastIndexOf('/')) + "/" + "%s",
            redirectUri= Beyond.Common.sprintf(redirectTemplate, pagename);
        return window.encodeURIComponent(redirectUri);
    },
    formatOauthUrl: function (redirectUri) {
        "use strict";
        return Beyond.Common.sprintf(this.HTTP_OAUTH_CODE_URI, redirectUri);
    },
    redirectToPageWithOauth: function (gotoUri) {
        "use strict";
        return (function () {
            // Beyond.Common.alert(Beyond.WeChat.DEBUG, "isWeiXin: " +  Beyond.WeChat.isWeiXin());
            if (Beyond.WeChat.isWeiXin()) {
                var redirect_uri = Beyond.WeChat.formatRedirectUrl(gotoUri);
                // var redirect_uri = "http%3A%2F%2F" + window.location.hostname + "%2Fattendance.html";
                Beyond.Common.alert(Beyond.WeChat.DEBUG, redirect_uri);
				var oauth_uri = Beyond.WeChat.formatOauthUrl(redirect_uri);
				// var oauth_uri = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxc63c757bdae5dd41&redirect_uri="+ redirect_uri + "&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect";				
				Beyond.Common.alert(Beyond.WeChat.DEBUG, oauth_uri);
                // window.location.href = oauth_uri;
                window.location.href = oauth_uri;
            } else {
                window.location.href = window.location.href.substring(0, window.location.href.lastIndexOf('/')) + "/" + gotoUri;
            }
        }());
    },
    getQueryString: function (name) {
        "use strict";
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i"),
            r = window.location.search.substr(1).match(reg);
        if (r !== null) {
            return window.decodeURI(r[2]); // unescape(r[2]);
        }
        return null;
    },
    requireOauth: function (callback) {
        "use strict";
        var userInfo = null,
        code = this.getQueryString("code");
        if (this.isWeiXin()) {
            Beyond.Common.alert(this.DEBUG, "code " + code);
            $.ajax({
                async: false,
                url: this.OAUTH_URL, //这是我的服务端处理文件php的
                type: "GET",
                data: {code: code}, // 传递本页面获取的code到后台，以便后台获取openid
                timeout: 1000,
                success: function (result) {
                    Beyond.Common.alert(Beyond.WeChat.DEBUG, "oauth success");
                    Beyond.Common.alert(Beyond.WeChat.DEBUG, result);
                    var resultObj = eval(result);
                    // Beyond.Common.alert(Beyond.WeChat.DEBUG, resultObj.result);
                    userInfo = JSON.parse(resultObj.result);
                    Beyond.Common.alert(Beyond.WeChat.DEBUG,  callback);
                    if (null !== callback) {
                        callback(userInfo);
                    }
                },
                error: function (xhr) {
                    // Beyond.Common.alert(Beyond.WeChat.DEBUG, "oauth failed");
                    $("#fatalMsg").text(xhr);
                    $.mobile.changePage('#fatal', {transition: 'pop', role: 'dialog'});
                }
            });
        }
        // Beyond.Common.alert(this.DEBUG, "return " + JSON.stringify(userInfo));
        return userInfo;
    }
};