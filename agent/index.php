<?php

if (empty($_POST) || empty($_POST['inputCharset']) || empty($_POST['url'])) {
    header('Location: https://www.hnbtjy.cn');
}

$url = 'https://pg.openepay.com/gateway/index.do';
if (isset($_POST['url'])) {
    $url = $_POST['url'];
}
unset($_POST['url']);

$tmp = '<html>';
$tmp .= '<head>';
$tmp .= '<meta content="text/html; charset=UTF-8" http-equiv="Content-Type">';
$tmp .= '<title>Pay Page</title>';
$tmp .= '</head>';
$tmp .= '<body style="display:none;">';
$tmp .= '<form action="' . $url . '" method="post" name="orderForm">';
foreach ($_POST as $key => $value) {
    $tmp .= '<input type="hidden" name="' . $key . '" value="' . $value . '">';
}
$tmp .= '</form>';
$tmp .= '<script type="text/javascript">';
$tmp .= 'document.orderForm.submit();';
$tmp .= '</script>';
$tmp .= '</body>';
$tmp .= '</html>';

echo $tmp;