<?php

require_once './conf.php';

if ($_POST)
{
    if (empty($_POST['id']))
    {
        echo json_encode(['status'=>1,'msg'=>'当前支付方式不可用!']);die;
    }
    if (!preg_match('/^[A-Za-z0-9@_]+$/', $_POST['member']))
    {
        echo json_encode(['status'=>1,'msg'=>'会员账号包含特殊字符！']);die;
    }
    if (!preg_match('/^[0-9]+(.[0-9]{1,2})?$/', $_POST['money']))
    {
        echo json_encode(['status'=>1,'msg'=>'充值金额格式不正确！']);die;
    }
    $postParams = array(
        'id'        => $_POST['id'],
        'username'  => $_POST['member'],
        'money'     => $_POST['money'],
        'payTime'   => date('Y-m-d H:i:s'),
        'device'    => $_POST['device'],
        'ip'        => getIp(),
        'netbankPaycode'=> (isset($_POST['netbank']) && $_POST['netbank'] == 1) ? $_POST['netbankPaycode'] : null,
    );
    // 调用后台接口，提交充值请求

    $output = sendHttpRequest(PAYMENT_API_DOMAIN . '/doPayment', $postParams);

    if ($output == '')
    {
        echo json_encode(['status'=>1,'msg'=>'当前支付方式不可用,请使用其他支付方式支付!#1']);
    }
    else
    {
        if (preg_match("/^(http|https|HTTP|HTTPS):\/\/.*$/", $output))
        {
            echo json_encode(['status'=>0,'type'=>'http','data'=>$output]);
        }
        elseif (preg_match("/<.*<\/html>/", formatHtml($output)) || strpos(formatHtml($output), '<form') !== false)
        {
            echo json_encode(['status'=>0,'type'=>'html','data'=>$output]);
        }
        else
        {
            $status = 1;
            if (isJSON($output))
            {
                // 03
                $tmpArr = json_decode($output, true);
                if ($tmpArr['data']['mch_order']) {
                    // 04
                    $tmpArr['order'] = $tmpArr['data']['mch_order'];
                }
                if (is_array($tmpArr) && isset($tmpArr['order'])) {
                    // 05
                    $order      = $tmpArr['order'];
                    $postParams = array('order' => $order, 'status' => $status);
                    $returnData = sendHttpRequest(PAYMENT_API_DOMAIN . '/updateQrcodeStatus', $postParams);
                    echo $returnData;
                } elseif (is_array($tmpArr) && isset($tmpArr['code'])) {
                    $msg = $tmpArr['error'] ?? '网络错误或支付方式不可用,请用其他支付方式重试!';
                    echo json_encode(['status'=>1,'msg'=>'网络错误或支付方式不可用,请用其他支付方式重试!#2']);
                }
                else
                {
                    echo json_encode(['status'=>1,'msg'=>'当前支付方式不可用,请使用其他支付方式支付!']);
                }

            }

        }
    }
}
else
{
    echo json_encode(['status'=>1,'msg'=>'非法操作#1']);
}
