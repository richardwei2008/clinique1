
(function () {
    if (typeof WeixinJSBridge == "undefined") {
        if (document.addEventListener) {
            document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
        } else if (document.attachEvent) {
            document.attachEvent('WeixinJSBridgeReady', onBridgeReady);
            document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
        }
    } else {
        onBridgeReady();
    }
    function onBridgeReady() {
        // 发送给好友;
		WeixinJSBridge.on('menu:share:appmessage', function (argv) {
			WeixinJSBridge.invoke('sendAppMessage', makeContext(), function (res) {});
		});
		// 分享到朋友圈;
		WeixinJSBridge.on('menu:share:timeline', function (argv) {
			WeixinJSBridge.invoke('shareTimeline', makeContext(), function (res) {});
		});
        WeixinJSBridge.call('showOptionMenu');
    };
	setWxContent = function (title) {
		if (typeof(App.wechatShare) != "undefined") {
			App.wechatShare["tTitle"] = title;
			App.wechatShare["fTitle"] = title;
			App.wechatShare["wContent"] = title + " —— " + window.shareData["fContent"];
		}
	};
    makeContext = function() {
        return (function() {
            initShareContext();
            var context = {
                "img_url" : App.wechatShare.imgUrl,
                "img_width" : "640",
                "img_height" : "640",
                "link" : App.wechatShare.sendFriendLink,
                "desc" : App.wechatShare.fContent,
                "title" : App.wechatShare.fTitle
            };
//            alert("Context " + JSON.stringify(context));
            return context;
        })();
    };
    initShareContext = function() {
        return (function() {
            var commonTitle = "是不是真男人？由你说了算！";
            var commonContent = App.defaultTag;
            var urlTemplate = 'http://' + window.location.hostname + '/' + App.Config.ROOT;
            var shareLink = urlTemplate + "/agreeornot.html";
            var imgUrl = urlTemplate + "/images/shareicon.png";
            if (!$.isEmptyObject(App.globalUser.openid) && App.globalUser.openid !== 'undefined') {
                shareLink = shareLink + "?" + "openid=" + App.globalUser.openid;
                shareLink = Beyond.WeChat.formatBaseOauthUrl(shareLink);
                if ($.isEmptyObject(App.globalUser.nickname)
                    || App.globalUser.nickname === 'undefined') {
                    App.globalUser.nickname = "我";
                }
//                if ($.isNumeric(App.globalUser.support) && App.globalUser.support >= 3) {
//                    commonTitle = '哇，' + App.globalUser.nickname + ' 的特供"真男人"！';
//                } else {
//                    commonTitle = App.globalUser.nickname + " " + commonTitle;
//                }
                commonTitle = App.globalUser.nickname + " " + commonTitle;
            } else {
                shareLink = urlTemplate + "/index.html";
                if (App.globalUser.invite) {
                    commonTitle = "我在召唤你，真男人~";
                }
            }
            if (!$.isEmptyObject(App.globalUser.tag)
                && App.globalUser.tag !== 'undefined') {
//                alert(App.globalUser.tag);
                commonContent = App.globalUser.tag.split("<br>").filter(function(item){return item!="";}).join("\n");
                if (!App.globalUser.invite) {
                    commonContent = commonContent + "\n你同意吗？";
                } else {
                    commonContent = App.defaultTag;
                }
//                alert(commonContent);
            }

            if (!$.isEmptyObject(imgUrl)
                && imgUrl === 'images/unknown.png') {
                imgUrl = urlTemplate + "/images/shareicon.png";
            }
            App.wechatShare = {
                "imgUrl": imgUrl,
                //可以是页面的头像，也可以是自己定义的一张图片不变，每个页面可以有这个JS
                "timeLineLink": shareLink,
                "sendFriendLink": shareLink,
                //发送朋友圈
                "tTitle": commonTitle,
                "tContent": commonContent,
                //发送给朋友
                "fTitle": commonTitle,
                "fContent": commonContent
            };
//            alert("App.wechatShare " + JSON.stringify(App.wechatShare));
        })();
    };
})();

function isWeiXin() {
		var ua = window.navigator.userAgent.toLowerCase();
		if (ua.match(/MicroMessenger/i) == 'micromessenger') {
			return true;
		} else {
			return false;
		}
	};

function addWxContact(wxid) {
    if (typeof WeixinJSBridge == 'undefined') return false;
    WeixinJSBridge.invoke('addContact', {
        webtype: '1',
        username: 'gh_e5430c6431e7'
    }, function (d) {
        // 返回d.err_msg取值，d还有一个属性是err_desc
        // add_contact:cancel 用户取消
        // add_contact:fail　关注失败
        // add_contact:ok 关注成功
        // add_contact:added 已经关注
        WeixinJSBridge.log(d.err_msg);
        cb && cb(d.err_msg);
    });
};
