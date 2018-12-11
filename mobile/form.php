<?php

require_once('../wmpay/conf.php');

// 调取支付接口，获取支付方式信息
$output = sendHttpRequest(PAYMENT_API_DOMAIN . '/payment/getPaymentInfo/' . COMPANY_NO, array(), 'get');

$payTypes = json_decode($output, true);

$wechatPayType = '';
$alipayType = '';
$bankPayType = '';
$qqPayType = '';
$jdPayType = '';
$baiduPayType ='';
$unionPayType ='';

if ($payTypes) {
    // type： 0 系统未配置、1 雅付、2 闪付、3 讯宝、4 乐盈
    $wechatPayType = key($payTypes['data'][1]);
    $alipayType = key($payTypes['data'][2]);
    $bankPayType = $payTypes['data'][3];
    $qqPayType = key($payTypes['data'][4]);
    $jdPayType = key($payTypes['data'][5]);
    $baiduPayType = key($payTypes['data'][6]);
    $unionPayType = key($payTypes['data'][7]);
}

$_token = md5(uniqid(rand(), true));
$_SESSION['_token'] = $_token;

?>
    <!DOCTYPE html>
    <html>

    <head lang="en">
        <meta charset="utf-8">
        <meta http-equiv="Expires" CONTENT="0">
        <meta http-equiv="Cache-Control" CONTENT="no-cache">
        <meta http-equiv="Pragma" CONTENT="no-cache">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta content="yes" name=" apple-mobile-web-app-capable" />
        <meta content="no" name="apple-touch-fullscreen" />
        <meta content="black" name=" apple-mobile-web-app-status-bar-style" />
        <meta content="telephone=no" name="format-detection" />
        <title>澳亚国际快速充值中心</title>
    </head>

    <body>
        <form method="post" action="/wmpay/payment.php" id="pay_form">
            <input type="hidden" name="companyNo" value="<?php echo COMPANY_NO; ?>">
            <input type="hidden" name="vendorType" id="vendor_type" value="<?php echo $payTypes['type']['wechat']; ?>">
            <input type="hidden" name="device" value="2">
            <input type="hidden" id="token" name="token" value="<?php echo $_token; ?>">
            <input type="hidden" name="pay_type" id="pay_type" value="<?php echo $wechatPayType; ?>" />
            <input type="hidden" name="time" class="timeInput" value="" />
            <input type="hidden" id="account" name="account" />
            <input type="hidden" id="money" name="money" />
            <input type="hidden" id="paytype2" name="paytype2" />
        </form>
        <div style="padding:2rem;text-align:center;font-size:.4rem;">支付信息提交中，请稍后...</div>
        <script>
            !(function (win, doc) {
                function setFontSize() {
                    // 获取window 宽度
                    // zepto实现 $(window).width()就是这么干的
                    var winWidth = window.innerWidth;
                    // doc.documentElement.style.fontSize = (winWidth / 640) * 100 + 'px' ;

                    // 2016-01-13 订正
                    // 640宽度以上进行限制 需要css进行配合
                    var size = (winWidth / 640) * 100;
                    doc.documentElement.style.fontSize = (size < 100 ? size : 100) + 'px';
                }
                var evt = 'onorientationchange' in win ? 'orientationchange' : 'resize';
                var timer = null;
                win.addEventListener(evt, function () {
                    clearTimeout(timer);

                    timer = setTimeout(setFontSize, 300);
                }, false);

                win.addEventListener("pageshow", function (e) {
                    if (e.persisted) {
                        clearTimeout(timer);

                        timer = setTimeout(setFontSize, 300);
                    }
                }, false);

                // 初始化
                setFontSize();

            }(window, document));
        </script>
        <script type="text/javascript">
            var iWidth = document.documentElement.clientWidth,
                iHeight = document.documentElement.clientHeight;
            if (isFirefox = navigator.userAgent.indexOf("Firefox") > 0) {
                iWidth = window.outerWidth;
                iHeight = window.outerHeight;
            }
            document.getElementsByTagName("html")[0].style.fontSize = iWidth / 10 + "px";
        </script>
        <script type="text/javascript" src="../js/jquery-1.11.0.min.js"></script>
        <script type="text/javascript">
            Date.prototype.format = function (fmt) {
                var o = {
                    "M+": this.getMonth() + 1, //月份
                    "d+": this.getDate(), //日
                    "h+": this.getHours(), //小时
                    "m+": this.getMinutes(), //分
                    "s+": this.getSeconds(), //秒
                    "q+": Math.floor((this.getMonth() + 3) / 3), //季度
                    S: this.getMilliseconds() //毫秒
                };
                if (/(y+)/.test(fmt))
                    fmt = fmt.replace(
                        RegExp.$1,
                        (this.getFullYear() + "").substr(4 - RegExp.$1.length)
                    );
                for (var k in o)
                    if (new RegExp("(" + k + ")").test(fmt))
                        fmt = fmt.replace(
                            RegExp.$1,
                            RegExp.$1.length == 1 ? o[k] : ("00" + o[k]).substr(("" + o[k]).length)
                        );
                return fmt;
            };
            $(function () {
                setInterval(function () {
                    var time = new Date();
                    $(".timeInput").val(time.format("yyyy-MM-dd hh:mm:ss"));
                }, 1000);
                // var name,value; 
                // var str=location.href;
                // var num=str.indexOf("?");
                // str=str.substr(num+1);

                // var arr=str.split("&");
                // for(var i=0;i < arr.length;i++){ 
                //     num=arr[i].indexOf("="); 
                //     if(num>0){ 
                //         name=arr[i].substring(0,num);
                //         value=arr[i].substr(num+1);
                //         this[name]=value;
                //     } 
                // } 

                function GetQueryString(name) {
                    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
                    var r = window.location.search.substr(1).match(reg);
                    if (r != null) return unescape(r[2]);
                    return null;
                }

                $("#account").val(GetQueryString("account"));
                $("#money").val(GetQueryString("money"));
                $("#companyNo").val(GetQueryString("companyNo"));
                $("#vendor_type").val(GetQueryString("vendor_type"));
                $("#device").val(GetQueryString("device"));
                $("#pay_type").val(GetQueryString("pay_type"));
                $("#paytype2").val(GetQueryString("paytype2"));

                $("#pay_form").submit();
            });
        </script>
    </body>

    </html>