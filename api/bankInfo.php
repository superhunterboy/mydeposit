<?php

header("Accept: application/json;");
header("Content-Type: application/json; charset=utf-8");

require_once('../wmpay/conf.php');
require_once('../wmpay/common.php');

if (!IS_AJAX) {
    exit(json_encode(['status'=>1, 'msg'=>'必须是AJAX!']));
}

$account    = $_POST['account'] ?? '';
$amount     = $_POST['money'] ?? '';
$depositor  = $_POST['name'] ?? '';
$token      = $_POST['token'] ?? '';
$type       = $_POST['type'] ?? '1';
$card_id    = $_POST['card_id'] ?? '';

$name = $_POST['name'] ?? $_POST['depositor'];
$dep = preg_match("/^([\x{4e00}-\x{9fa5}]|[a-zA-Z0-9_-])+$/u", $name);
if($dep != 1) {
    exit(json_encode(['status'=>1, 'msg'=>'非法姓名格式!']));
}

//$pos = preg_match("/^([\x{4e00}-\x{9fa5}]|[a-zA-Z0-9_-])+$/u", $_POST['depositor']);
//if($pos != 1) {
//    exit(json_encode(['status'=>1, 'msg'=>'非法姓名格式!']));
//}

$acc = preg_match('/^[a-zA-Z0-9_]{1,}$/', $account);
if($acc != 1) {
    exit(json_encode(['status'=>1, 'msg'=>'非法会员账号格式!']));
}

$result = sendHttpRequest(PAYMENT_API_DOMAIN . '/memberIsExists/' . $account, [], 'get');
if($result){
    $resArr = json_decode($result, true);
    if($resArr['status'] != 0){
        exit($result); //json_encode(['status'=>1, 'msg'=>'无效用户名!'])
    }
}
if ($_POST['amount'] && $_POST['depositor']) {
    $postData['account']    = $account;
    $postData['amount']     = $_POST['amount'];
    $postData['depositor']  = $_POST['depositor'];
    $postData['type']       = $type;
    $postData['card_id']    = $card_id;

    $result = sendHttpRequest(PAYMENT_API_DOMAIN . '/addOfflinePay', $postData);
    if($result){
        //$resArr = json_decode($result, true);
        exit($result);
    }else{
        exit(json_encode(['status'=>1, 'msg'=>'网路错误!']));
    }
}

if (empty($amount) || empty($depositor)) {
    $isQuery = true;
}

//防止重复提交
if(empty($_SESSION['_token']) || empty($token)) exit(json_encode(['status'=>1, 'msg'=>'操作异常请重试!']));

if($token != $_SESSION['_token']) exit(json_encode(['status'=>1, 'msg'=>'请勿重复操作!']));

if (isset($isQuery)) {
    $result = sendHttpRequest(PAYMENT_API_DOMAIN . '/getOfflinePayBank', [], 'get');
    if($result){
        //$resArr = json_decode($result, true);
        //if($resArr['status'] != 0){
            exit($result);
        //}
        //echo $result;
    }else{
        echo json_decode(['status'=>1, 'msg'=>'网络异常!']);
    }

}else{
    $amo = preg_match('/^[0-9]+\.{0,1}[0-9]{0,2}$/', $amount);
    if($amo != 1) exit(json_encode(['status'=>1, 'msg'=>'非法金额格式!']));

    $postData['account']    = $account;
    $postData['amount']     = $amount;
    $postData['depositor']  = $depositor;
    $postData['type']       = $type;
    $postData['card_id']    = $card_id;

    $result = sendHttpRequest(PAYMENT_API_DOMAIN . '/addOfflinePay', $postData);
    if($result){
        //$resArr = json_decode($result, true);
        //if($resArr['status'] != 0){
            exit($result);
        //}

        //$result = sendHttpRequest(PAYMENT_API_DOMAIN . '/getOfflinePayBank', [], 'get');
        //if($result){
        //    $resArr = json_decode($result, true);
        //    if($resArr['status'] != 0){
        //        exit($result);
        //    }
        //}
        //echo $result;
    }else{
        echo json_decode(['status'=>1, 'msg'=>'网络异常!']);
    }
}

