<?php

require_once './conf.php';

if ($_POST && isset($_SESSION['_token'])) {
    $companyNo  = $_POST['companyNo'];
    $vendorType = $_POST['vendorType'];
    $username   = $_POST['account'];
    $banknumber = $_POST['banknumber'];
    //$username_confirm = $_POST['confirm_account']; 取消会员账号确认
    $paytype  = $_POST['pay_type'];
    $paytype2 = isset($_POST['paytype2']) ? $_POST['paytype2'] : '';
    $coin     = $_POST['money'];
    if ($vendorType == 61 && $paytype == 'unionpayqrh5') {
        $coin = $_POST['money'] + 0.01;
    }
    $time   = date('Y-m-d H:i:s');
    $device = $_POST['device'];
    // 注：以下支付 code 包含已经作废的 闪亿付 Shanyi
    $wechatPaycode = [
        '0201', '0202', 'wxewm', 'WECHAT_PC', '00', '1004', '1007', '8011', 'wx', 'weixin', '12', '100010', '100012', '1003', 'WEIXIN', 'MSWEIXIN', 'WECHATQR', '6001', 'WEIXIN_NATIVE',
        'wxpay', 'weixin_scan', 'WXPAY', '21', '10000168', '902', '102', '001', 'WXZF', '20', 'WxSm', '02', '2', '20001', '23', 'WECHAT', '0101', 'WX', '1000', '10', 'wxpay', 'wechat', '10000103', 'wxcode', '62', 'WXP', 'zxweixin', 'pay.weixin.scan.trade.precreate',
    ];
    $alipayPaycode = [
        'VPay_ZFB', 'ALIPAY_PC', 'ali', '0301', '0302', '992', '1006', '8012', 'zfb', 'alipay', '30', '400010', '400012', '1009', 'ZHIFUBAO', 'MSAli', '3', 'ALIPAYQR', '6003',
        'ALIPAY', '01003', 'alipay_scan', '10000169', '7', '101', '903', '002', '22', 'DFYzfb', '1', '10001', '0103', 'ZFB', '02010', '020', 'ALIPAY_NATIVE', 'alipay', '2', '20000203', 'alipay', 'ALIPAY_QRCODE_PAY', 'sm', '21', 'alpls', 'zxalp', '903', '8006', 'shunda001',
    ];
    $qqPaycode = [
        '6011', 'qqrcode', 'QQWALLET', 'QQ', '993', 'qqpay', 'QQPAY', '0501', '01009', 'tenpay_scan', '31', 'MSTENPAY', '10000170', '1010', '908', '003',
        'QQZF', '46', '1593', '010500', 'tenpay', 'QQSCAN', 'qq', 'qqQR', '0102', '01005', 'QQ_NATIVE', 'qqpay', 'QQ_QRCODE_PAY',
    ];
    $jdPaycode = [
        'JDPAY', 'JD', 'MSJD', '0801', '912', '004', 'JdPay', '1008', 'JDSCAN', 'jdpay', '0001007', '0601', '01002', '41', 'JD_NATIVE', 'jdpay', 'JD_QRCODE_PAY',
    ];
    $baiduPaycode = [
        'BAIDUPAY', 'BDPAY', 'BAIDU', '001003',
    ];
    $unionPaycode = [
        'UNIONPAY', '0040', '0701', '913', '005', '001007', 'UNIONQRPAY', 'unionPayQR', 'ylsm', 'UNION_WALLET', '001009', '1001', '0002', 'UNIONPAY_NATIVE', 'unionpayqr', '60000103', 'unionpay', 'UNIONPAY_QRCODE_PAY',
    ];
    $wapWechatPaycode = [
        'WEIXINWAP', 'wxewm', 'WECHAT_MOBILE', '901', '01007', 'WXZFWAP', '0020', '48', '1005', 'WECHATWAP', 'wxpayh5', 'wxwap', 'wxh5', 'W1WEIXIN_PAY', 'WX_WAP', '1002', '01030', '1100', '11', 'WEIXIN_H5', '10000203', '60000103', 'WXWAP', '0121', '62', 'WXP', 'zxweixin', 'pay.weixin.scan.trade.precreate',
    ];
    $wapAlipayPaycode = [
        'ALIPAYWAP', 'ALIPAY_MOBILE', '904', '0', '4', '01006', '38', 'ZfbWap', 'aliwap', 'alipaywap', 'Z2ALIPAY', 'ZFB_WAP', '01004', '0203', '1101', '022', 'ALIPAY_H5', 'alipayh5', '2', '20000203', 'alipaywap', '0131', 'ALIPAY_WAP_PAY', 'sm', '21', 'alpls', 'zxalp', '904', '8007', 'shunda001',
    ];
    $wapQqPaycode = [
        'QQPAYWAP', 'QQWAP', '905', '01008', '1594', 'qqpayh5', 'qqwap', 'Q2TEN_PAY', 'QQ_WAP', '001006', '0503', '1102', 'QQ_H5',
    ];
    $wapJdPaycode = [
        'JDPAYWAP', 'JDWAP', '910', 'JD_WAP', '001008', '0603', '01012', 'JD_H5', 'JD_WAP_PAY',
    ];
    $wapbBaiduPaycode = [
    ];
    $wapUnionPaycode = [
        'UNION_WALLET_H5', '0050', '1012', 'UNIONPAY_H5', 'unionpayqrh5', '60000103', 'unionpay', 'UNIONPAY_WAP_PAY',
    ];
    $netbankPaycode = [
        '01',
    ];
    $yunPaycode = [
        'daniuyun',
    ];
    $wapyunPaycode = [
        'daniuyun',
    ];
    $availablePaycode = array_merge($wechatPaycode, $alipayPaycode, $qqPaycode, $jdPaycode, $baiduPaycode, $unionPaycode, $netbankPaycode,
        $wapWechatPaycode, $wapAlipayPaycode, $wapQqPaycode, $wapJdPaycode, $wapbBaiduPaycode, $wapUnionPaycode, $yunPaycode, $wapyunPaycode);
    $wechatAlipayQQJDBaiduUnionPaycode = array_merge($wechatPaycode, $alipayPaycode, $qqPaycode, $jdPaycode, $baiduPaycode, $unionPaycode, $wapWechatPaycode,
        $wapAlipayPaycode, $wapQqPaycode, $wapJdPaycode, $wapbBaiduPaycode, $wapUnionPaycode, $yunPaycode, $wapyunPaycode);

    /*if ($username != $username_confirm) {

    echo "<script type='text/javascript'>alert('会员账号不一致！');window.history.back();</script>";exit();

    }*/

    if (!preg_match('/^[A-Za-z0-9@_]+$/', $username)) {
        // || !preg_match('/^[A-Za-z0-9@_]+$/', $username_confirm)

        echo "<script type='text/javascript'>alert('会员账号包含特殊字符！');window.history.back();</script>";exit();

    }
    // print_r($paytype);
    // echo "<pre>";
    // print_r($availablePaycode);die;
    if (!in_array($paytype, $availablePaycode)) {

        echo "<script type='text/javascript'>alert('支付类型不正确！');window.history.back();</script>";exit();

    }

    if (!in_array($paytype, $wechatAlipayQQJDBaiduUnionPaycode) && !preg_match('/^[A-Za-z0-9]+$/', $paytype2)) {

        echo "<script type='text/javascript'>alert('请选择银行！');window.history.back();</script>";exit();

    }

    if (!preg_match('/^[0-9]+(.[0-9]{1,2})?$/', $coin)) {

        echo "<script type='text/javascript'>alert('充值金额格式不正确！');window.history.back();</script>";exit();

    }

    if (!preg_match('/^[1-9]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])\s+(20|21|22|23|[0-1]\d):[0-5]\d:[0-5]\d$/', $time)) {

        echo "<script type='text/javascript'>alert('充值时间格式不正确！');window.history.back();</script>";exit();

    }

    if (!preg_match('/^[0-9]+$/', $companyNo)) {

        echo "<script type='text/javascript'>alert('业务平台编号不正确！');window.history.back();</script>";exit();

    }

    if (!preg_match('/^(1|2|3|4|5|6|7|8|9|10|11|12|13|14|15|16|17|18|19|20|21|22|23|24|25|26|27|28|29|30|31|32|33|34|35|36|37|38|39|40|41|42|43|44|45|46|47|48|49|50|51|52|53|55|56|57|58|59|60|61|62|63|64|66|67|68|69|70|71|72|73|74|75|76|77|78|79|80|81|82|83|84)$/', $vendorType)) {

        echo "<script type='text/javascript'>alert('支付平台类型不正确！');window.history.back();</script>";exit();

    }

    if (!preg_match('/^(1|2)$/', $device)) {

        echo "<script type='text/javascript'>alert('支付设备类型不正确！');window.history.back();</script>";exit();

    }

    $ip         = getIp();
    $postParams = array(
        'companyNo'        => $companyNo,
        'vendorType'       => $vendorType,
        'username'         => $username,
        'username_confirm' => $username_confirm,
        'banknumber'       => $banknumber,
        'paytype'          => $paytype,
        'paytype2'         => $paytype2,
        'money'            => $coin,
        'payTime'          => $time,
        'device'           => $device,
        'ip'               => $ip,
    );
    // 调用后台接口，提交充值请求

    $output = sendHttpRequest(PAYMENT_API_DOMAIN . '/payment', $postParams);
    echo $output;
    if ($output == '') {
        echo '当前支付方式不可用,请使用其他支付方式支付!1';

    } else {

        // 01
        unset($_SESSION['_token']);

        if (preg_match("/^(http|https|HTTP|HTTPS):\/\/.*$/", $output)) {
            header('Location: ' . $output);

        } elseif (preg_match("/<.*<\/html>/", formatHtml($output)) || strpos(formatHtml($output), '<form') !== false) {
            echo $output;

        } else {

            // 仅支持 5 自由付、6 沃雷特、7 金海哲
            // echo $output;
            // header('Location: http://tyn101.com');
            // 02
            $status    = 1;
            $qrcodeUrl = '';

            if (in_array($paytype, $wechatPaycode, true) || in_array($paytype, $wapWechatPaycode, true)) {
                $status    = 2;
                $qrcodeUrl = WECHATALIPAYQRCODEURL . '/ay/wechat.html';
            }

            if (in_array($paytype, $alipayPaycode, true) || in_array($paytype, $wapAlipayPaycode, true)) {
                $status    = 3;
                $qrcodeUrl = WECHATALIPAYQRCODEURL . '/ay/alipay.html';
            }

            if (in_array($paytype, $qqPaycode, true) || in_array($paytype, $wapQqPaycode, true)) {
                //$status    = 3;
                //echo (string) $output;
            }

            if (isJSON($output)) {
                // 03
                $tmpArr = json_decode($output, true);
                if ($vendorType == 61) {
                    header('Location:' . $tmpArr['url']);
                }
                if ($tmpArr['data']['mch_order']) {
                    // 04
                    $tmpArr['order'] = $tmpArr['data']['mch_order'];
                }
                if (is_array($tmpArr) && isset($tmpArr['order'])) {
                    // 05
                    $order      = $tmpArr['order'];
                    $postParams = array('order' => $order, 'status' => $status);
                    $returnData = sendHttpRequest(PAYMENT_API_DOMAIN . '/updateQrcodeStatus', $postParams);
                    return $returnData;
                } elseif (is_array($tmpArr) && isset($tmpArr['code'])) {
                    $msg = $tmpArr['error'] ?? '网络错误或支付方式不可用,请用其他支付方式重试!';
                    exit($msg);
                } else {
                    exit('当前支付方式不可用,请使用其他支付方式支付!');
                }
                // echo $returnData;exit();
            }

        }
    }
} else {
    echo '非法操作#1';
}
