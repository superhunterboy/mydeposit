<?php
require_once('../wmpay/conf.php');
require_once('../wmpay/common.php');

if (!IS_AJAX) {
    exit(json_encode(['status'=>1, 'msg'=>'必须是AJAX!']));
}

$order         = $_POST['order'] ?? '';
$number = preg_match('/^[0-9]+$/', $order);
if ($number != 1) {
    exit(json_encode(['status'=>1, 'msg'=>'非法id值!']));
}
echo $order;