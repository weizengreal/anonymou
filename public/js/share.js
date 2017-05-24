/**
 * Created by WeiZeng on 2017/04/26.
 * 需要提供基础支持的url：
 * postShareUrl、callBackUrl
 * 当前js文件为分享类库，需要外部提供title、des、link、imgurl
 *
 */

var share = "https://api.aufe.vip/jssdk/axcShare",_title="匿名评教1",_desc="匿名评教1",_link="http://www.baidu.com",_imgUrl="http://notice.woai662.net/anonymou/public/image/share.png";



$.post(share, {url: encodeURIComponent(location.href.split('#')[0])}, function (_data) {
    var _data = JSON.parse(_data);
    wx.config({
        debug: false,
        appId: _data.appId,
        timestamp: _data.timestamp,
        nonceStr: _data.nonceStr,
        signature: _data.signature,
        jsApiList: [
            'onMenuShareTimeline',
            'onMenuShareAppMessage',
            'onMenuShareQQ',
            'onMenuShareWeibo',
            'onMenuShareQZone',
            'hideAllNonBaseMenuItem'
        ]
    });
});

wx.ready(function () {

    wx.onMenuShareTimeline({
        title: "匿名评教1", // 分享标题
        link: "http://www.baidu.com", // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
        imgUrl: "http://notice.woai662.net/anonymou/public/image/share.png", // 分享图标
        success: function () {
            // 用户确认分享后执行的回调函数
            alert("分享到朋友圈成功");
        },
        cancel: function () {
            // 用户取消分享后执行的回调函数
            alert("取消分享到朋友圈");
        },
        fail: function (res) {
            alert(JSON.stringify(res));
        }
    });

    wx.onMenuShareAppMessage({
        title: "匿名评教1", // 分享标题
        desc: "匿名评教1", // 分享描述
        link: "http://www.baidu.com", // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
        imgUrl: "http://notice.woai662.net/anonymou/public/image/share.png", // 分享图标
        success: function () {
            // 用户确认分享后执行的回调函数
            alert("分享成功");
        },
        cancel: function () {
            // 用户取消分享后执行的回调函数
            alert("取消分享");
        },
        fail: function (res) {
            alert(JSON.stringify(res));
        }
    });


    wx.onMenuShareQQ({
        title: "匿名评教1", // 分享标题
        desc: "匿名评教1", // 分享描述
        link: "http://www.baidu.com", // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
        imgUrl: "http://notice.woai662.net/anonymou/public/image/share.png", // 分享图标
        success: function () {
            // 用户确认分享后执行的回调函数
        },
        cancel: function () {
            // 用户取消分享后执行的回调函数
        },
        fail: function (res) {
            alert(JSON.stringify(res));
        }
    });


    wx.onMenuShareWeibo({
        title: "匿名评教1", // 分享标题
        desc: "匿名评教1", // 分享描述
        link: "http://www.baidu.com", // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
        imgUrl: "http://notice.woai662.net/anonymou/public/image/share.png", // 分享图标
        success: function () {
            // 用户确认分享后执行的回调函数
        },
        cancel: function () {
            // 用户取消分享后执行的回调函数
        },
        fail: function (res) {
            alert(JSON.stringify(res));
        }
    });


    wx.onMenuShareQZone({
        title: "匿名评教1", // 分享标题
        desc: "匿名评教1", // 分享描述
        link: "http://www.baidu.com", // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
        imgUrl: "http://notice.woai662.net/anonymou/public/image/share.png", // 分享图标
        success: function () {
            // 用户确认分享后执行的回调函数
        },
        cancel: function () {
            // 用户取消分享后执行的回调函数
        },
        fail: function (res) {
            alert(JSON.stringify(res));
        }
    });


    // wx.hideAllNonBaseMenuItem();


});