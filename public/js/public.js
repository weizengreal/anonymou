/**
 * Created by zhengwei on 2017/4/22.
 */
/**
 * 替换所有匹配exp的字符串为指定字符串
 * @param exp 被替换部分的正则
 * @param newStr 替换成的字符串
 */
var share = "https://api.aufe.vip/jssdk/axcShare",title="匿名评教",desc="匿名评教",link="http://www.baidu.com",imgUrl="http://notice.woai662.net/anonymou/public/image/share.png";

String.prototype.replaceAll = function (exp, newStr) {
    return this.replace(new RegExp(exp, "gm"), newStr);
};

/**
 * 原型：字符串格式化
 * @param args 格式化参数值
 */
String.prototype._format = function(args) {
    var result = this;
    if (arguments.length < 1) {
        return result;
    }

    var data = arguments; // 如果模板参数是数组
    if (arguments.length == 1 && typeof (args) == "object") {
        // 如果模板参数是对象
        data = args;
    }
    for ( var key in data) {
        var value = data[key];
        if (undefined != value) {
            result = result.replaceAll("\\{" + key + "\\}", value);
        }
    }
    return result;
}

function isWeixin() {
    var e = window.navigator.userAgent.toLowerCase();
    return "micromessenger" == e.match(/MicroMessenger/i);
}