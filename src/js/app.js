/*global $, jQuery, window: true, browser: true, alert: true, BMap, AppConfig, Beyond*/
var App = {};
App = {
    Config: AppConfig,
    alert: function (obj) {
        "use strict";
        Beyond.Common.alert(this.Config.DEBUG, obj);
    },
    globalUser: {
        openid : "",
        headimgurl : "",
        nickname : ""
    },
    targetUser : null,
    wechatShare: {},
    mySwiper: null,
    defaultIcon : "images/unknown.png",
    defaultNickname : "我",
    defaultTag : "倩碧真男人特供\n是不是真男人？\n由你说了算！",
    followUrl : "http://mp.weixin.qq.com/s?__biz=MzA4MDE5MjkxNA==&mid=204175232&idx=1&sn=38761952f6372dba05be77df9fe15916#rd",
    formatRedirectUri : function(relativeTargetUri) {
        return (function() {
            return 'http://' + window.location.hostname + '/' + App.Config.ROOT + '/' + relativeTargetUri;
        }());
    },
    formatRedirectUriWithGlobalUserParam : function(globalUser, relativeTargetUri) {
        return  (function() {
            return App.formatRedirectUri(relativeTargetUri) + '?'
                + '&openid=' + encodeURI(globalUser.openid)
                + '&nickname=' + encodeURI(globalUser.nickname)
                + '&headimgurl=' + encodeURI(globalUser.headimgurl)
                + '&tag=' + encodeURI(globalUser.tag)
                + '&support=' + encodeURI(globalUser.support);
        }());
    },
    redirectToPageWithGlobalUserParam : function(globalUser, relativeTargetUri) {
        return (function() {
            var redirectToUrl = App.formatRedirectUriWithGlobalUserParam(globalUser, relativeTargetUri);
//            alert("Redirect: " + redirectToUrl);
            window.location.href = redirectToUrl
            window.event.returnValue = false;
//        $(window.location.href).attr('href', redirectToUrl);
            return false;
        }());

    },

    initNavigation : function() {
//        alert("width: " + window.innerWidth);
//        alert("height: " + window.innerHeight);
        $('.ui-button-border-custom').mousedown(function () {
            return (function (obj) {
                var that = $(obj);
                that.addClass("ui-button-border-active-custom");
                setTimeout(function(){
                    $(that).removeClass("ui-button-border-active-custom");
                }, 100);
            })(this);
        });
        $('#start').click(function() {
//            window.location.href = App.formatRedirectUri("index.html");
            return (function() {
                App.redirectToPageWithGlobalUserParam(App.globalUser, "index.html");
            })();
        });
        $('#rank').click(function() {
//            window.location.href = App.formatRedirectUri("top.html");
            return (function() {
                App.redirectToPageWithGlobalUserParam(App.globalUser, "top.html");
            })();
        });
        $('#rule').click(function() {
//            window.location.href = App.formatRedirectUri("rule.html");
            return (function() {
                App.redirectToPageWithGlobalUserParam(App.globalUser, "rule.html");
            })();
        });
        $('#invite').click(function() {
            return (function() {
                document.getElementById('sharemcover').style.display='block';
                App.globalUser.openid = "";
                App.globalUser.invite = true;
            })();
        });
        $('#status').click(function() {
            return (function() {
                if ($.isEmptyObject(App.globalUser.openid) || App.globalUser.openid === "undefined") {
                    Beyond.WeChat.redirectToPageWithOauth("success.html");
                } else {
                    App.redirectToPageWithGlobalUserParam(App.globalUser, "success.html");
                }
            })();
        });
    },
    initGame : function() {
        App.handleUrlUserInfo();
//        alert("Enter Game " + JSON.stringify(App.globalUser));
        $('#join').click(function() {
            return (function() {
                if ($.isEmptyObject(App.globalUser.openid) || App.globalUser.openid === "undefined") {
                    Beyond.WeChat.redirectToPageWithOauth("choose.html");
                } else {
                    App.redirectToPageWithGlobalUserParam(App.globalUser, "choose.html");
                }
            })();
        });
    },
    initSwiper: function() {
        mySwiper = new Swiper('.swiper-container',{
            pagination: '.pagination',
            loop:true,
            grabCursor: true,
            paginationClickable: false
        });
        $("#arrow-left").click(function(e){
            e.preventDefault();
            mySwiper.swipeNext();
        });
        $("#arrow-right").click( function(e){
            e.preventDefault();
            mySwiper.swipePrev();
        });
    },
    decorateUserInfo: function (user) {
//        App.alert(user.headimgurl);
        if ($.isEmptyObject(user.headimgurl) || user.headimgurl === 'undefined') {
            user.headimgurl = App.defaultIcon;
        }
        $("#photo").attr("src", user.headimgurl);
        if ($.isEmptyObject(user.nickname) || user.nickname === 'undefined') {
            user.nickname = App.defaultNickname;
        }
        $('#nickname').text(user.nickname);
    },
    requireSessionUser : function() {
        var sessionUser = null;
        $.ajax({
            async: false,
            url: 'server/service/SessionService.php',
            type: "GET",
            dataType : "json",
            timeout: 5000,
            success: function (result) {
//                Beyond.Common.alert(Beyond.WeChat.DEBUG, "session oauth success");
//                Beyond.Common.alert(Beyond.WeChat.DEBUG, JSON.stringify(result));
                sessionUser = result;
            },
            error : function (xhr, textStatus, errorThrown) {
                $("#errorMsg").html("服务器繁忙，<br>请稍后再来！");
                document.getElementById('messageMask').style.display='block';
            }
        });
        return sessionUser;
    },
    setupGlobalUserFromSessionOtherwiseOAuth : function () {
//        var sessionUser = App.requireSessionUser();
        App.handleUrlUserInfo();
        if ($.isEmptyObject(App.globalUser.openid)) {
            var oauthUser = Beyond.WeChat.requireOauth(null);
            App.globalUser = {
                headimgurl : App.defaultIcon,
                nickname : App.defaultNickname
            }
            if (oauthUser !== null) {
                App.globalUser = oauthUser;
            }
        }
//        else {
//            App.globalUser = sessionUser;
//        }
        App.decorateGlobalUser();
    },
    initPrompt : function() {
        App.handleUrlUserInfo();
        App.decorateUserInfo(App.globalUser);
        $('#follow').click(function() {
            window.location.href = App.followUrl;
        });
        $('#continue').click(function() {
            var redirectToUrl = App.formatRedirectUriWithGlobalUserParam(App.globalUser, "choose.html");
            redirectToUrl = redirectToUrl + '&ignore=true';
//            App.alert("Redirect: " + redirectToUrl);
            window.location.href = redirectToUrl;
        });
    },
    initChoose: function () {
        "use strict";
//        App.alert("Init choose");
        var ignore = $.getUrlParam('ignore');
        App.setupGlobalUserFromSessionOtherwiseOAuth();
//        alert("Enter Game " + JSON.stringify(App.globalUser));

        // 1. check if user ever chose a tag, and redirect to status page if it did.

        $.ajax({
            async: false,
            url: 'server/service/TagService.php',
            type: "POST",
            data : JSON.stringify({method : 'POST', type : 'READ', openid : App.globalUser.openid}),
            dataType : "json",
            timeout: 5000,
            success: function (response) {
//                alert("Request: " + JSON.stringify(response.request));
//                alert("Return: " + JSON.stringify(response));
//                alert("Redirect: " + !response.success);
                if (!response.success) {
                    return (function () {
                        setTimeout(function(){
                            App.redirectToPageWithGlobalUserParam(App.globalUser, "success.html");
                        }, 100);
                    })();
                } else {
                    $.ajax({
                        async: false,
                        url: 'server/service/UserService.php',
                        type: "POST",
                        data : JSON.stringify({method : 'POST', type : 'ADD', openid : App.globalUser.openid, data : App.globalUser}),
                        dataType : "json",
                        timeout: 5000,
                        success: function (response) {
//                            alert("Request: " + JSON.stringify(response.request));
//                            alert("Return: " + JSON.stringify(response.data));
                            if (!response.success) {
                                switch (response.code) {
                                    case 'E001':
                                    case 'E002':
                                        $("#errorMsg").html(response.message);
                                        break;
                                    default :
                                        $("#errorMsg").html("服务器繁忙，<br>请稍后再来！");
                                        break;
                                }
                                document.getElementById('messageMask').style.display='block';
                            } else {
                                // 2. check if user follow wechat account, and prompt dialog for user to follow
                                if (ignore !== 'true' && (App.defaultNickname === App.globalUser.nickname
                                    || App.defaultIcon === App.globalUser.headimgurl)) {
                                    App.redirectToPageWithGlobalUserParam(App.globalUser, "prompt.html");
                                    return;
                                }
                            }
                        },
                        error : function (xhr, textStatus, errorThrown) {
                            $("#errorMsg").html("服务器繁忙，<br>请稍后再来！");
                            document.getElementById('messageMask').style.display='block';
                        }
                    });
                }
            },
            error : function (xhr, textStatus, errorThrown) {
                $("#errorMsg").html("服务器繁忙，<br>请稍后再来！");
                document.getElementById('messageMask').style.display='block';
            }
        });
        App.decorateUserInfo(App.globalUser);
        $("#confirm").click(function () {
            return App.confirmTag();
        });
    },
    confirmTag: function() {
        var choice = $(".swiper-slide-active .option").html();
        App.globalUser.tag = choice;
        var encodedChoice = encodeURI(choice);
        $.ajax({
            async: false,
            url: 'server/service/TagService.php',
            type: "POST",
            data : JSON.stringify({method : 'POST', type : 'CHOOSE', openid : App.globalUser.openid, tag : encodedChoice}),
            dataType : "json",
            timeout: 5000,
            success: function (response) {
                // alert("Request: " + JSON.stringify(response.request));
                // alert("Return: " + JSON.stringify(response));
                if (response.success) {
                    var redirectToUrl = App.formatRedirectUriWithGlobalUserParam(App.globalUser, "confirm.html");
                    redirectToUrl = redirectToUrl + '&choice=' + encodedChoice;
                    window.location.href = redirectToUrl;
                } else {
                    switch (response.code) {
                        case 'E001':
                            $("#errorMsg").html(response.message);
                            break;
                        default :
                            $("#errorMsg").html("服务器繁忙，<br>请稍后再来！");
                            break;
                    }
                    document.getElementById('messageMask').style.display='block';
                }
            },
            error : function (xhr, textStatus, errorThrown) {
                $("#errorMsg").html("服务器繁忙，<br>请稍后再来！");
                document.getElementById('messageMask').style.display='block';
            }
        });

    },
    initStatus: function () {
        "use strict";
        App.setupGlobalUserFromSessionOtherwiseOAuth();
//        // 1. check if user follow wechat account, and prompt dialog for user to follow
//        if (App.defaultNickname === App.globalUser.nickname
//            || App.defaultIcon === App.globalUser.headimgurl) {
//            // TODO
//            App.alert("是否关注倩碧官方微信帐号？");
//            // return;
//        }
        // 2. check if user ever chose a tag, and redirect to status page if it did.
        var isRedirect = false;
        $.ajax({
            async: false,
            url: 'server/service/TagService.php',
            type: "POST",
            data : JSON.stringify({method : 'POST', type : 'READ', openid : App.globalUser.openid}),
            dataType : "json",
            timeout: 5000,
            success: function (response) {
//                alert("Request: " + JSON.stringify(response.request));
//                alert("Return: " + JSON.stringify(response));
                if (response.success && $.isEmptyObject(response.data)) {
                    // not tag found means never join the game, redirect to start game
                    isRedirect = true;
                    return (function () {
                        setTimeout(function(){
                            App.redirectToPageWithGlobalUserParam(App.globalUser, "index.html");
                        }, 100);
                    })();
                }
            },
            error : function (xhr, textStatus, errorThrown) {
                $("#errorMsg").html("服务器繁忙，<br>请稍后再来！");
                document.getElementById('messageMask').style.display='block';
            }
        });
        if (isRedirect) {
            return;
        }
//        App.alert("openid " + App.globalUser.openid);
//        App.// alert("headimgurl " + App.globalUser.headimgurl);
//        App.alert("nickname " + App.globalUser.nickname);
        App.decorateUserInfo(App.globalUser);
        $.ajax({
            async: false,
            url: 'server/service/SupportService.php',
            type: "POST",
            data : JSON.stringify({method : 'GET', type : 'ME', openid : App.globalUser.openid}),
            dataType : "json",
            timeout: 5000,
            success: function (response) {
                // alert("Request: " + JSON.stringify(response.request));
                // alert("Return: " + JSON.stringify(response));
                if (response.success) {
                    if (response.tag) {
//                            alert("Return: " + JSON.stringify(response.tag))
                        $("#tag").html(response.tag.content);
                        App.globalUser.tag = response.tag.content;
                    }
                    if (response.data.numberOfSupport) {
                        App.globalUser.support = response.data.numberOfSupport;
                        if (response.data.numberOfSupport == 0) {
                            $("#lackOfSupport").html("&nbsp;3&nbsp;");
                            $("#circle").css("background","url('images/status-circle3_640.png') no-repeat");
                            $("#go").css("display", "none");
                            $("#status-empty").css("display", "block");
                            $("#status-full").css("display", "none");
                        } else if (response.data.numberOfSupport == 1) {
                            $("#lackOfSupport").html("&nbsp;2&nbsp;");
                            $("#circle").css("background","url('images/status-circle2_640.png') no-repeat");
                            $("#go").css("display", "none");
                            $("#status-empty").css("display", "block");
                            $("#status-full").css("display", "none");
                        } else if (response.data.numberOfSupport == 2) {
                            $("#lackOfSupport").html("&nbsp;1&nbsp;");
                            $("#circle").css("background","url('images/status-circle1_640.png') no-repeat");
                            $("#go").css("display", "none");
                            $("#status-empty").css("display", "block");
                            $("#status-full").css("display", "none");
                        } else if (response.data.numberOfSupport >= 3) {
                            $("#numOfSupport").html("&nbsp;"+response.data.numberOfSupport+"&nbsp;");
                            $("#circle").css("background","url('images/status-success_640.png') no-repeat");
                            $("#go").css("display", "block");
                            $("#status-empty").css("display", "none");
                            $("#status-full").css("display", "block");
                        } else {
                            $("#lackOfSupport").html("&nbsp;3&nbsp;");
                            $("#circle").css("background","url('images/status-circle3_640.png') no-repeat");
                            $("#go").css("display", "none");
                            $("#status-empty").css("display", "block");
                            $("#status-full").css("display", "none");
                        }
                        $("#circle").css("background-position","center");
                        $("#circle").css("background-size","100%");
                        document.getElementById('checkGift').style.display='block';
                    }
                } else {
                    switch (response.code) {
                        case 'E001':
                            $("#errorMsg").html(response.message);
                            break;
                        default :
                            $("#errorMsg").html("服务器繁忙，<br>请稍后再来！");
                            break;
                    }
                    document.getElementById('messageMask').style.display='block';
                }
            },
            error : function (xhr, textStatus, errorThrown) {
                $("#errorMsg").html("服务器繁忙，<br>请稍后再来！");
                document.getElementById('messageMask').style.display='block';
            }
        });
        $("#checkGift").click(function () {
            $.ajax({
                async: false,
                url: 'server/service/SuccessLogService.php',
                type: "POST",
                data : JSON.stringify({method : 'GET', type : 'CHECK', data : {openid : App.globalUser.openid}}),
                dataType : "json",
                timeout: 5000,
                success: function (response) {
//                    App.alert("Request: " + JSON.stringify(response.request));
//                    App.alert("Return: " + JSON.stringify(response.data));
                    if (response.success) {
                        var redirectToUrl = App.formatRedirectUriWithGlobalUserParam(App.globalUser, "submit.html");
                        window.location.href = redirectToUrl;
                    } else {
                        switch (response.code) {
                            case 'E001':
                            case 'E002':
                            case 'E003':
                            case 'E004':
                            case 'E005':
                                $("#errorMsg").html(response.message);
                                break;
                            default :
                                $("#errorMsg").html("服务器繁忙，<br>请稍后再来！");
                                break;
                        }
                        document.getElementById('messageMask').style.display='block';
                    }
                },
                error : function (xhr, textStatus, errorThrown) {
                    $("#errorMsg").html("服务器繁忙，<br>请稍后再来！");
                    document.getElementById('messageMask').style.display='block';
                }
            });

        });
    },
    handleUrlUserInfo : function() {
        var openid = $.getUrlParam('openid');
        var nickname = $.getUrlParam('nickname');
        var headimgurl = $.getUrlParam('headimgurl');
        var tag = $.getUrlParam('tag');
        var support = $.getUrlParam('support');
        if (App.globalUser === null) {
            App.globalUser = {
                openid : "",
                headimgurl : "",
                nickname : "",
                support : ""
            };
        }
        App.globalUser.openid = openid;
        App.globalUser.headimgurl = headimgurl;
        App.globalUser.nickname = nickname;
        App.globalUser.tag = tag;
        App.globalUser.support = support;
//        App.alert("openid " + App.globalUser.openid);
//        App.alert("headimgurl " + App.globalUser.headimgurl);
//        App.alert("nickname " + App.globalUser.nickname);
        App.decorateGlobalUser();
    },
    decorateGlobalUser : function() {
        return (function() {
            if ($.isEmptyObject(App.globalUser.openid)
                || App.globalUser.openid === 'undefined') {
                App.globalUser.openid = "";
            }
            if ($.isEmptyObject(App.globalUser.headimgurl)
                || App.globalUser.headimgurl === 'undefined') {
                App.globalUser.headimgurl = App.defaultIcon;
            }
            if ($.isEmptyObject(App.globalUser.nickname)
                || App.globalUser.nickname === 'undefined') {
                App.globalUser.nickname = App.defaultNickname;
            }
            if ($.isEmptyObject(App.globalUser.tag)
                || App.globalUser.tag === 'undefined') {
                App.globalUser.tag = App.defaultTag;
            }
        })();
    },
    initConfirm: function () {
        "use strict";
//        App.alert("Init confirm");
        var choice = $.getUrlParam('choice');
        App.globalUser.tag = choice;
        if (choice !== null && choice !== 'null' && choice !== "") {
            $("#selected").html(choice);
        }
        App.handleUrlUserInfo();
        App.decorateUserInfo(App.globalUser);
    },
    initTop : function () {
        App.handleUrlUserInfo();
        $.ajax({
            async: false,
            url: 'server/service/SupportService.php',
            type: "POST",
            data : JSON.stringify({method : 'POST', type : 'TOP'}),
            dataType : "json",
            timeout: 5000,
            success: function (result) {
                $("#tableTop").empty();
//                App.alert(JSON.stringify(result));
                $("#dailyTopSupport").tmpl(result.data).appendTo("#tableTop");
            },
            error : function (xhr, textStatus, errorThrown) {
                $("#errorMsg").html("服务器繁忙，<br>请稍后再来！");
                document.getElementById('messageMask').style.display='block';
            }
        });
    },
    initRule : function () {
        App.handleUrlUserInfo();
    },
    initShare : function () {
        App.handleUrlUserInfo();
    },
    initSubmit: function() {
        App.handleUrlUserInfo();
//        App.decorateUserInfo(App.globalUser);

        $('#submit').click(function(){
            var provinceCode = $('#province').val();
            if (provinceCode < 0) {
                alert("请选择 省/直辖市");
                return;
            }
            var provinceName = $('#province option').filter(function() {return this.getAttribute('value') == provinceCode}).text();
            var cityCode = $('#city').val();
            if (cityCode < 0) {
                alert("请选择 城市");
                return;
            }
            var cityName = $('#city option').filter(function() {return this.getAttribute('value') == cityCode}).text();
            var siteCode = $('#site').val();
            if (siteCode < 0) {
                alert("请选择 门店");
                return;
            }
            var siteName = $('#site option').filter(function() {return this.getAttribute('value') == siteCode}).text();
            var cellphone = $('#cellphone').val();
            if ($.isEmptyObject(cellphone) || cellphone.length !== 11) {
                alert("请完整输入11位手机号码：（XXX-XXXX-XXXX）");
                return;
            }
            var data = {
                openid: App.globalUser.openid,
                provinceCode : provinceCode,
                provinceName : provinceName,
                cityCode : cityCode,
                cityName : cityName,
                siteCode : siteCode,
                siteName : siteName,
                cellphone : cellphone
            };
            $.ajax({
                async: false,
                url: 'server/service/SuccessLogService.php',
                type: "POST",
                data : JSON.stringify({method : 'POST', type : 'ADD', data : data}),
                dataType : "json",
                timeout: 5000,
                success: function (response) {
//                        App.alert("Request: " + JSON.stringify(response.request));
//                        App.alert("Return: " + JSON.stringify(response.data));
                    if (response.success) {
                        var redirectToUrl = App.formatRedirectUriWithGlobalUserParam(App.globalUser, "share.html");
//                        App.alert("Redirect: " + redirectToUrl);
                        window.location.href = redirectToUrl;
                    } else {
                        switch (response.code) {
                            case 'E001':
                            case 'E002':
                            case 'E003':
                            case 'E004':
                            case 'E005':
                                $("#errorMsg").html(response.message);
                                break;
                            default :
                                $("#errorMsg").html("服务器繁忙，<br>请稍后再来！");
                                break;
                        }
                        document.getElementById('messageMask').style.display='block';
                    }
                },
                error : function (xhr, textStatus, errorThrown) {
                    $("#errorMsg").html("服务器繁忙，<br>请稍后再来！");
                    document.getElementById('messageMask').style.display='block';
                }
            });
        });
        var cacheProvince = null;
        var loadProvince = function (data) {
            cacheProvince = data;
            $("#province").empty();
            $("#city").empty();
            $("#city").append($("<option/>").text("--请选择--").attr("value", "-1"));
            $("#site").empty();
            $("#site").append($("<option/>").text("--请选择--").attr("value", -1));
            $(data.root).each(function () {
                $("#province").append($("<option/>").text(this.name).attr("value", this.id));
            });
        };
        if (cacheProvince === null) {
            $.getJSON("data/province.min.json", loadProvince);
        } else {
            loadProvince(cacheProvince);
        }

        var cacheCity = null;
        $("#province").unbind('change').bind('change',
            function(){
                var provinceId = $("#province").val();
                var loadCity = function(data) {
                    cacheCity = data;
                    $("#city").empty();
                    $("#site").empty();
                    $("#site").append($("<option/>").text("--请选择--").attr("value", -1));
                    $(data.root).each(function () {
                        if (this.pid == provinceId) {
                            $(this.list).each(function () {
                                $("#city").append($("<option/>").text(this.name).attr("value", this.value));
                            });
                            if ($("#city").val() !== "" && $("#city").val() !== "-1") {
                                $("#city").trigger('change');
                            }
                        }
                    });
                }
                if (cacheCity === null) {
                    $.getJSON("data/city.min.json", loadCity);
                } else {
                    loadCity(cacheCity);
                }
            }
        );
        var cacheSite = null;
        $("#city").unbind('change').bind('change',
            function(){
                var cityId = $("#city").val();
                var loadSite = function (data) {
                    cacheSite = data
                    $(data.root).each(function () {
                        if (this.cid == cityId) {
//                            alert(JSON.stringify(this.list));
                            $(this.list).each(function () {
                                $("#site").append($("<option/>").text(this.name).attr("value", this.value));
                            });
                        }
                    });
                }
                $("#site").empty();
                if (cacheSite === null) {
                    $.getJSON("data/site.min.json", loadSite);
                } else {
                    loadSite(cacheSite);
                }

            }
        );
    },
    initAgreeOrDisagree : function () {
        var openid =  $.getUrlParam('openid');
        var oauthUser = Beyond.WeChat.requireOauth(null);
        if (!$.isEmptyObject(oauthUser)) {
            App.globalUser = oauthUser;
        }
        App.targetUser = null;
        if (!$.isEmptyObject(openid)
            && openid === App.globalUser.openid) {
//                return (function () {
//                    setTimeout(function(){
//                        App.redirectToPageWithGlobalUserParam(App.globalUser, "success.html");
//                    }, 100);
//                })();
            App.redirectToPageWithGlobalUserParam(App.globalUser, "success.html");
        }
        if (!$.isEmptyObject(openid)) {
            $.ajax({
                async: false,
                url: 'server/service/UserService.php',
                type: "POST",
                data : JSON.stringify({method : 'POST', type:'READ', openid : openid, support_openid: App.globalUser.openid}),
                dataType : "json",
                timeout: 5000,
                success: function (response) {
//                    App.alert("Request: " + JSON.stringify(response.request));
//                    App.alert("Return: " + JSON.stringify(response.data));
                    if (response.success) {
                        App.decorateUserInfo(response.data);
                        App.targetUser = response.data;
                        $("#theNickname").text(response.data.nickname + "认为：");
                        if (response.tag) {
//                            alert("Return: " + JSON.stringify(response.tag))
                            $("#tag").html(response.tag.content);
                            App.globalUser.tag = response.tag.content;
                        }
                    } else {
                        switch (response.code) {
                            case 'E001':
                                $("#errorMsg").html(response.message);
                                break;
                            case 'E002':
                                $("#errorMsg").html(response.message);
                                break;
                            default :
                                $("#errorMsg").html("服务器繁忙，<br>请稍后再来！");
                                break;
                        }
                        document.getElementById('messageMask').style.display='block';
                    }
                },
                error : function (xhr, textStatus, errorThrown) {
                    $("#errorMsg").html("服务器繁忙，<br>请稍后再来！");
                    document.getElementById('messageMask').style.display='block';
                }
            });
        }
        $('#agree').click(function(){
            $.ajax({
                async: false,
                url: 'server/service/SupportService.php',
                type: "POST",
                data : JSON.stringify({method : 'POST', type:'SUPPORT', openid : openid, support_openid : App.globalUser.openid}),
                dataType : "json",
                timeout: 5000,
                success: function (response) {
//                    App.alert("Request: " + JSON.stringify(response.request));
//                    App.alert("Return: " + JSON.stringify(response.data));
                    if (response.success) {
                        var redirectToUrl = App.formatRedirectUriWithGlobalUserParam(App.globalUser, "agreed.html");
                        redirectToUrl = redirectToUrl + '&target_openid=' + App.targetUser.openid;
                        redirectToUrl = redirectToUrl + '&target_nickname=' + App.targetUser.nickname;
                        redirectToUrl = redirectToUrl + '&target_headimgurl=' + App.targetUser.headimgurl;
//                        App.alert("Redirect: " + redirectToUrl);
                        window.location.href = redirectToUrl;
                    } else {
                        switch (response.code) {
                            case 'E001':
                                $("#errorMsg").html(response.message);
                                break;
                            case 'E002':
                                $("#errorMsg").html(response.message);
                                break;
                            default :
                                $("#errorMsg").html("服务器繁忙，<br>请稍后再来！");
                                break;
                        }
                        document.getElementById('messageMask').style.display='block';
                    }
                },
                error : function (xhr, textStatus, errorThrown) {
                    $("#errorMsg").html("服务器繁忙，<br>请稍后再来！");
                    document.getElementById('messageMask').style.display='block';
                }
            });
        });
        $('#disagree').click(function(){
            App.redirectToPageWithGlobalUserParam(App.globalUser, "index.html");
        });
    },
    initAgreed : function () {
        App.handleUrlUserInfo();
        App.targetUser = {
            openid : $.getUrlParam('target_openid'),
            nickname : $.getUrlParam('target_nickname'),
            headimgurl : $.getUrlParam('target_headimgurl')
        };

        App.decorateUserInfo(App.targetUser);
        $('#game').click(function(){
            return (function() {
                App.redirectToPageWithGlobalUserParam(App.globalUser, "index.html");
            })();
        });
    }

};




