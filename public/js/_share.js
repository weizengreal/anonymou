/**
 * Created by WeiZeng on 2017/04/26.
 * 需要提供基础支持的url：
 * postShareUrl、callBackUrl
 * 当前js文件为分享类库，需要外部提供title、des、link、imgurl
 *
 */

$.post(share, { url: encodeURIComponent(location.href.split('#')[0]) }, function (data) {
    var date = JSON.parse(data);
    wx.config({
        debug: true,
        appId: date.appId,
        timestamp: date.timestamp,
        nonceStr: date.nonceStr,
        signature: date.signature,
        jsApiList: [
            'onMenuShareTimeline',
            'onMenuShareAppMessage',
            'onMenuShareQQ',
            'onMenuShareWeibo',
            'onMenuShareQZone'
        ]
    });
});

wx.ready(function () {

    wx.onMenuShareTimeline({
        title: title, // 分享标题
        link: link, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
        imgUrl: imgUrl, // 分享图标
        success: function () {
            // 用户确认分享后执行的回调函数
            alert("分享到朋友圈成功");
        },
        cancel: function () {
            // 用户取消分享后执行的回调函数
            alert("取消分享到朋友圈");
        }
    });

    wx.onMenuShareAppMessage({
        title: title, // 分享标题
        desc: desc, // 分享描述
        link: link, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
        imgUrl: imgUrl, // 分享图标
        success: function () {
            // 用户确认分享后执行的回调函数
            alert("分享成功");
        },
        cancel: function () {
            // 用户取消分享后执行的回调函数
            alert("取消分享");
        }
    });


    wx.onMenuShareQQ({
        title: title, // 分享标题
        desc: desc, // 分享描述
        link: link, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
        imgUrl: imgUrl, // 分享图标
        success: function () {
            // 用户确认分享后执行的回调函数
        },
        cancel: function () {
            // 用户取消分享后执行的回调函数
        }
    });


    wx.onMenuShareWeibo({
        title: title, // 分享标题
        desc: desc, // 分享描述
        link: link, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
        imgUrl: imgUrl, // 分享图标
        success: function () {
            // 用户确认分享后执行的回调函数
        },
        cancel: function () {
            // 用户取消分享后执行的回调函数
        }
    });


    wx.onMenuShareQZone({
        title: title, // 分享标题
        desc: desc, // 分享描述
        link: link, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
        imgUrl: imgUrl, // 分享图标
        success: function () {
            // 用户确认分享后执行的回调函数
        },
        cancel: function () {
            // 用户取消分享后执行的回调函数
        }
    });

    // wx.hideAllNonBaseMenuItem();

});