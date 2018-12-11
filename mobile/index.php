<?php

require_once('../wmpay/conf.php');

// 调取支付接口，获取支付方式信息
$output = sendHttpRequest(PAYMENT_API_DOMAIN . '/payment/getPaymentInfo/' . COMPANY_NO, array(), 'get');

$payTypes = json_decode($output, true);

$wechatPayType = '';
$wapWechatPayType = '';
$alipayType = '';
$wapAlipayType = '';
$bankPayType = '';
$qqPayType = '';
$wapQqPayType = '';
$jdPayType = '';
$wapJdPayType = '';
$baiduPayType ='';
$wapBaiduPayType ='';
$unionPayType ='';
$wapUnionPayType ='';

if ($payTypes) {
    // type： 0 系统未配置、1 雅付、2 闪付、3 讯宝、4 乐盈
    $wechatPayType      = key($payTypes['data'][1]);
    $alipayType         = key($payTypes['data'][2]);
    $bankPayType        = $payTypes['data'][3];
    $qqPayType          = key($payTypes['data'][4]);
    $jdPayType          = key($payTypes['data'][5]);
    $baiduPayType       = key($payTypes['data'][6]);
    $unionPayType       = key($payTypes['data'][7]);
    $wapWechatPayType   = key($payTypes['data'][8]);
    $wapAlipayType      = key($payTypes['data'][9]);
    $wapQqPayType       = key($payTypes['data'][10]);
    $wapJdPayType       = key($payTypes['data'][11]);
    $wapBaiduPayType    = key($payTypes['data'][12]);
    $wapUnionPayType    = key($payTypes['data'][13]);
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
        <base target="_blank" />
        <meta content="telephone=no" name="format-detection" />
        <title>澳亚国际快速充值中心</title>
        <link rel="stylesheet" href="./css/pay.css?v=5" />
    </head>

    <body>
        <input id="bankId" type="hidden" value="" />
        <div class="modal-page">
            <div class="ali-transfer">
                <div class="c-head">
                    <a onclick="back()">
                        < 返回</a>转到银行卡</div>

                <div class="c-content">
                    <div class="c-bank-row">
                            <select id="bank-selector" onchange="onbankChanged(this)">
                                <!-- <option>农业银行</option>
                                <option>招商银行</option> -->
                            </select>
                            <div class="show-bank activited" type="招商银行">
                                <img src="./img/pay/cmb.png" />
                                <span>切换银行 ></span>
                            </div>
                            <div class="show-bank" type="农业银行">
                                <img src="./img/pay/abc.png" />
                                <span>切换银行 ></span>
                            </div>
                        </div>

                        <div class="c-bank-box">
                            <div>
                                <span>开户银行：</span>
                                <span id="ali-bank-name"></span>
                            </div>
                            <div>
                                <span>银行卡号：</span>
                                <span id="ali-cardno"></span>
                            </div>
                            <div>
                                <span>银行户名：</span>
                                <span id="ali-account-name"></span>
                            </div>
                        </div>

                        <div class="c-tform">
                            <div>
                                <span>存款金额：</span>
                                <input id="ct-money" type="text" placeholder="请确认您的存款金额" onkeyup="this.value=this.value.replace(/[^\d{1,}\.\d{1,}|\d{1,}]/g,'')"
                                    onafterpaste="this.value=this.value.replace(/[^\d{1,}\.\d{1,}|\d{1,}]/g,'')" />
                            </div>
                            <div>
                                <span>支付宝昵称：</span>
                                <input id="ct-name" type="text" placeholder="请输入您的支付宝昵称" />
                            </div>
                        </div>

                        <div class="c-tips" style="color:red;">
                            <h3>温馨提示：</h3>
                            <p>1. 为了不影响您的体验游戏，建议选择“实时到账”;</p>
                            <p>2. 转账成功后，输入存款信息，点击“确认以上信息并提交”按钮。</p>
                        </div>

                        <div class="c-btn-submit" onclick="alipaySubmit(1)">确认以上信息并提交</div>
                </div>
            </div>

            <div class="ali-act">
                <div class="c-head">
                    <a onclick="back()">
                        < 返回</a>个人支付宝扫码</div>

                <div class="c-content">
                    <div class="act-qr-code">
                        <img src="" />
                        <span></span>
                    </div>

                    <div class="c-tform">
                        <div>
                            <span>存款金额：</span>
                            <input id="act-money" type="text" placeholder="请确认您的存款金额" onkeyup="this.value=this.value.replace(/[^\d{1,}\.\d{1,}|\d{1,}]/g,'')"
                                onafterpaste="this.value=this.value.replace(/[^\d{1,}\.\d{1,}|\d{1,}]/g,'')" />
                        </div>
                        <div>
                            <span>支付宝昵称：</span>
                            <input id="act-name" type="text" placeholder="请输入您的支付宝昵称" />
                        </div>
                    </div>

                    <div class="c-tips" style="font-size:.3rem;">
                        <h3 style="color:red;">注：扫描二维码后在“添加备注”填写澳亚国际会员帐号再进行充值。</h3>
                        <p style="margin-bottom:.2rem;">操作说明：</p>
                        <p>1、长按二维码，选择保存图片至相册；</p>
                        <p>2、打开支付宝，选择“扫一扫”,点击右上角“相册”按钮；</p>
                        <p>3、选择保存的二维码图片； </p>
                        <p style="color:red;">4、输入充值金额，点击“添加备注”填写澳亚国际会员帐号再进行充值；</p>
                        <p>5、转账成功后，输入存款信息，点击<span style="color:red;">“确认以上信息并提交”</span>按钮。</p>
                    </div>

                    <div class="c-btn-submit" onclick="alipaySubmit(2)">确认以上信息并提交</div>
                </div>
            </div>

            <div class="ali-qr">
                <div class="c-head">
                    <a onclick="back()">
                        < 返回</a>商家扫码</div>

                <div class="c-content">
                    <div class="act-qr-code">
                        <div id="mcode"></div>
                        <span></span>
                    </div>

                    <div class="c-tform">
                        <div>
                            <span>存款金额：</span>
                            <input id="qct-money" placeholder="请确认您的存款金额" type="text" onkeyup="this.value=this.value.replace(/[^\d{1,}\.\d{1,}|\d{1,}]/g,'')"
                                onafterpaste="this.value=this.value.replace(/[^\d{1,}\.\d{1,}|\d{1,}]/g,'')" />
                        </div>
                        <div>
                            <span>交易单号：</span>
                            <input id="qct-no" type="text" placeholder="请输入交易单号后七位" maxlength="7" onkeyup="this.value=this.value.replace(/[^\d{1,}\.\d{1,}|\d{1,}]/g,'')"
                                onafterpaste="this.value=this.value.replace(/[^\d{1,}\.\d{1,}|\d{1,}]/g,'')" />
                        </div>
                    </div>

                    <div class="c-tips" style="font-size:.3rem;">
                        <h3 style="color:red;">注：扫描二维码后在“添加备注”填写澳亚国际会员帐号再进行充值。</h3>
                        <p style="margin-bottom:.2rem;">操作说明：</p>
                        <p>1、长按二维码，选择保存图片至相册；</p>
                        <p>2、打开支付宝，选择“扫一扫”,点击右上角“相册”按钮； </p>
                        <p>3、选择保存的二维码图片； </p>
                        <p style="color:red;">4、输入充值金额，点击“添加备注”填写澳亚国际会员帐号再进行充值；</p>
                        <p>5、转账成功后，输入存款信息，点击<span style="color:red;">“确认以上信息并提交”</span>按钮。</p>
                    </div>

                    <div class="c-btn-submit" onclick="alipaySubmit(3)">确认以上信息并提交</div>
                </div>
            </div>
        </div>

        <div class="wechat-modal-page">
            <div class="wechat-personal">
                <div class="c-head">
                        <a onclick="wback()">
                            < 返回</a>个人微信扫码</div>

                <div class="c-content">
                    <div class="act-qr-code">
                        <img class="pay2qr-code" src=""/>
                    </div>

                    <div class="c-tform">
                        <div>
                            <span>存款金额：</span>
                            <input id="qrpay-money"  type="text" placeholder="请确认您的存款金额" onkeyup="this.value=this.value.replace(/[^\d{1,}\.\d{1,}|\d{1,}]/g,'')"
                                onafterpaste="this.value=this.value.replace(/[^\d{1,}\.\d{1,}|\d{1,}]/g,'')" />
                        </div>
                        <div>
                            <span>微信昵称：</span>
                            <input id="wechat-account"  type="text" placeholder="请输入您的微信账户昵称" />
                        </div>
                    </div>

                    <div class="c-tips" style="font-size:.3rem;">
                        <h3 style="color:red;">注：扫描二维码后在“添加备注”填写澳亚国际会员帐号再进行充值。</h3>
                        <p style="margin-bottom:.2rem;">操作说明：</p>
                        <p>1、长按二维码，选择保存图片至相册；</p>
                        <p>2、打开手机微信，请点击右上角“+”，选择“扫一扫”；
                            <br/>&nbsp;a、安卓手机点击扫码界面右上角"设置"按钮，选择"从相册选取二维码"；
                            <br/>&nbsp;b、苹果手机点击扫码界面右上角"相册"按钮；</p>
                        <p>3、选择保存的二维码图片； </p>
                        <p style="color:red;">4、输入充值金额，点击“添加备注”填写澳亚国际会员帐号再进行充值；</p>
                        <p>5、转账成功后，输入存款信息，点击<span style="color:red;">“确认以上信息并提交”</span>按钮。</p>
                    </div>

                    <div class="c-btn-submit" onclick="wechatPaySubmit('1')">确认以上信息并提交</div>
                </div>
            </div>
            <div class="wechat-merchant">
                    <div class="c-head">
                            <a onclick="wback()">
                                < 返回</a>商家微信扫码</div>

                    <div class="c-content">
                        <div class="act-qr-code">
                            <div id="merchant-code"></div>
                        </div>

                        <div class="c-tform">
                            <div>
                                <span>商户单号：</span>
                                <input id="merchant-order" maxlength="7"  type="text" placeholder="请输入商户单号后七位" />
                            </div>
                        </div>

                        <div class="c-tips" style="font-size:.3rem;">
                            <h3 style="color:red;">注：扫描二维码后在“添加备注”填写澳亚国际会员帐号再进行充值。</h3>
                            <p style="margin-bottom:.2rem;">操作说明：</p>
                            <p>1、长按二维码，选择保存图片至相册；</p>
                            <p>2、打开手机微信，请点击右上角“+”，选择“扫一扫”；
                                <br/>&nbsp;a、安卓手机点击扫码界面右上角"设置"按钮，选择"从相册选取二维码"；
                                <br/>&nbsp;b、苹果手机点击扫码界面右上角"相册"按钮；</p>
                            <p>3、选择保存的二维码图片； </p>
                            <p style="color:red;">4、输入充值金额，点击“添加备注”填写澳亚国际会员帐号再进行充值；</p>
                            <p>5、转账成功后，输入存款信息，点击<span style="color:red;">“确认以上信息并提交”</span>按钮。</p>
                        </div>

                        <div class="c-btn-submit" onclick="wechatPaySubmit('2')">确认以上信息并提交</div>
                    </div>
            </div>
            <div class="wechat-addfriend">
                <div class="c-head">
                        <a onclick="wback()">
                            < 返回</a>加微信好友支付</div>

                <div class="c-content">
                    <div class="act-qr-code">
                        <img class="personl-img" src="" />
                    </div>

                    <div class="c-tips" style="font-size:.4rem;">
                        <p style="margin-bottom:.2rem;">操作说明：</p>
                        <p>1、长按二维码，选择保存图片至相册；</p>
                        <p>2、打开微信，选择“扫一扫”；
                            <br/>&nbsp;a、安卓手机点击扫码界面右上角“设置”按钮，选择从相册选取二维码；
                            <br/>&nbsp;b、苹果手机点击扫码界面右上角“相册”按钮； </p>
                        <p>3、添加专员微信好友，通过微信转账进行入款。</p>
                    </div>
                </div>
            </div>
            <div class="wechat-group">
                <div class="c-head">
                        <a onclick="wback()">
                            < 返回</a>智能微信扫码</div>

                <div class="c-content">
                    <div class="act-qr-code">
                        <img class="group-img" src="" />
                    </div>

                    <div class="c-tips" style="font-size:.3rem;">
                        <h3 style="color:red;">注：扫描二维码后在“添加备注”填写澳亚国际会员帐号再进行充值。</h3>
                        <p style="margin-bottom:.2rem;">操作说明：</p>
                        <p>1、长按二维码，选择保存图片至相册；</p>
                        <p>2、打开手机微信，请点击右上角“+”，选择“扫一扫”；
                            <br/>&nbsp;a、安卓手机点击扫码界面右上角"设置"按钮，选择"从相册选取二维码"；
                            <br/>&nbsp;b、苹果手机点击扫码界面右上角"相册"按钮；</p>
                        <p>3、选择保存的二维码图片； </p>
                        <p style="color:red;">4、输入充值金额，点击“添加备注”填写澳亚国际会员帐号再进行充值；</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="qq-modal-page">
            <div class="qq-personal">
                <div class="c-head">
                        <a onclick="qback()">
                            < 返回</a>QQ扫码</div>

                <div class="c-content">
                    <div class="act-qr-code">
                        <img class="pay2qq-code" src=""/>
                    </div>

                    <div class="c-tform">
                        <div>
                            <span>存款金额：</span>
                            <input id="qq-money"  type="text" placeholder="请确认您的存款金额" onkeyup="this.value=this.value.replace(/[^\d{1,}\.\d{1,}|\d{1,}]/g,'')"
                                onafterpaste="this.value=this.value.replace(/[^\d{1,}\.\d{1,}|\d{1,}]/g,'')" />
                        </div>
                        <div>
                            <span>QQ账户昵称：</span>
                            <input id="qq-drawee"  type="text" placeholder="请输入您的QQ账户昵称" />
                        </div>
                    </div> 

                    <div class="c-tips" style="font-size:.3rem;">
                        <h3 style="color:red;">注：扫描二维码后在“转账留言”填写澳亚国际会员帐号再进行充值。</h3>
                        <p style="margin-bottom:.2rem;">操作说明：</p>
                        <p>1、长按二维码，选择保存图片至相册；</p>
                        <p>2、打开手机QQ，请点击右上角“+”，选择“扫一扫”；
                            <br/>&nbsp;a、安卓手机点击扫码界面右上角"设置"按钮，选择"从相册选取二维码"；
                            <br/>&nbsp;b、苹果手机点击扫码界面右上角"相册"按钮；</p>
                        <p>3、选择保存的二维码图片； </p>
                        <p style="color:red;">4、输入充值金额，点击“转账留言”填写澳亚国际会员帐号再进行充值；</p>
                        <p>5、转账成功后，输入存款信息，点击<span style="color:red;">“确认以上信息并提交”</span>按钮。</p>
                    </div>

                    <div class="c-btn-submit" onclick="qqPaySubmit('1')">确认以上信息并提交</div>
                </div>
            </div>
        </div>

        <div class="c-wrapper">
            <div class="head clearfix">
                <div class="head-left">
                    <div class="desc desc-2" style="margin-left:2px;" onclick=window.location='https://9661o.com/'>返回首页</div>
                    <div class="desc desc-3" onclick=window.location='http://xingmim.com/'>活动申请大厅</div>
                </div>
                <div class="desc-1" onclick=window.location="https://static.meiqia.com/dist/standalone.html?_=t&eid=93887"></div>
            </div>
            <div class="content">
                <div class="tab">
                    <div class="tab-item tab-active" m-div="online-pay">在线支付</div>
                    <div class="tab-item" m-div="transfer">转账</div>
                    <div class="tab-item" m-div="qr-pay">微信</div>
                    <div class="tab-item apli-tab-item" m-div="ali-pay">支付宝</div>
                    <div class="tab-item" m-div="qq-pay">QQ</div>

                </div>
                <div class="content-inner">
                    <div class="online-pay">
                        <form method="post" action="/wmpay/payment.php" id="pay_form">
                            <input type="hidden" id="companyNo" name="companyNo" value="<?php echo COMPANY_NO; ?>">
                            <input type="hidden" name="vendorType" id="vendor_type" value="<?php echo $payTypes['type']['wechat']; ?>">
                            <input type="hidden" id="device" name="device" value="2">
                            <input type="hidden" id="token" name="token" value="<?php echo $_token; ?>">
                            <input type="hidden" name="pay_type" id="pay_type" value="<?php echo $wechatPayType; ?>" />
                            <input name="time" id="timeInput" class="timeInput" value="" type="hidden" />
                            <input type="hidden" id="s_type" />
                            <div class="form-row">
                                <span>会员账号：</span>
                                <input type="text" name="account" placeholder="请输入需要存款的会员账号" />
                            </div>

                            <div class="form-row">
                                <span>存款金额：</span>
                                <input type="text" name="money" placeholder="请输入存款金额" />
                            </div>

                            <?php if ($payTypes['payStatus']['netpay'] == 1) { ?>
                            <?php if ($payTypes['type']['netpay'] != 5) { ?>
                            <div class="form-row">
                                <span>选择银行：</span>
                                <span style="display:none;" data-type="3" data-value="01" data-vendor="<?php echo $payTypes['type']['netpay']; ?>" class="b-card"></span>
                                <select name="paytype2" id="paytype2">
                                    <option>请选择银行</option>
                                    <?php if ($bankPayType) {
                                                foreach ($bankPayType as $key => $val) { ?>
                                    <option value="<?php echo $key; ?>">
                                        <?php echo $val; ?>
                                    </option>
                                    <?php
                                                }
                                            }
                                            ?>
                                </select>
                            </div>
                            <?php } ?>
                            <?php } ?>
                            <div class="other-btn">
                                <span>其它支付方式：</span>
                                <?php if ($payTypes['payStatus']['wapWechat'] == 1) { ?><!--微信-->
                                <a data-type="1" data-value="<?php echo $wapWechatPayType; ?>" data-vendor="<?php echo $payTypes['type']['wapWechat']; ?>" class="wechat-pay active">
                                    <img class="gou" src="./img/pay/gou.png" />
                                    <img class="img img-selected" src="./img/pay/wechat-pay-active.png" />
                                </a>
                                <?php } elseif ($payTypes['payStatus']['wechat'] == 1) { ?>
                                <a data-type="1" data-value="<?php echo $wechatPayType; ?>" data-vendor="<?php echo $payTypes['type']['wechat']; ?>" class="wechat-pay active">
                                    <img class="gou" src="./img/pay/gou.png" />
                                    <img class="img img-selected" src="./img/pay/wechat-pay-active.png" />
                                </a>
                                <?php } ?>

                                <?php if ($payTypes['payStatus']['wapQq'] == 1) { ?><!--QQ-->
                                <a data-type="4" data-value="<?php echo $wapQqPayType; ?>" data-vendor="<?php echo $payTypes['type']['wapQq']; ?>" class="qqpay">
                                    <img class="gou" src="./img/pay/gou.png" />
                                    <img class="img img-selected" src="./img/pay/qq-pay-active.png" />
                                </a>
                                <?php } elseif ($payTypes['payStatus']['qq'] == 1) { ?>
                                <a data-type="4" data-value="<?php echo $qqPayType; ?>" data-vendor="<?php echo $payTypes['type']['qq']; ?>" class="qqpay">
                                    <img class="gou" src="./img/pay/gou.png" />
                                    <img class="img img-selected" src="./img/pay/qq-pay-active.png" />
                                </a>
                                <?php } ?>

                                <?php if ($payTypes['payStatus']['wapAlipay'] == 1) { ?><!--支付宝-->
                                <a data-type="2" data-value="<?php echo $wapAlipayType; ?>" data-vendor="<?php echo $payTypes['type']['wapAlipay']; ?>" class="alipay">
                                    <img class="gou" src="./img/pay/gou.png" />
                                    <img class="img img-selected" src="./img/pay/ali-pay-active.png" />
                                </a>
                                <?php } elseif ($payTypes['payStatus']['alipay'] == 1) { ?>
                                <a data-type="2" data-value="<?php echo $alipayType; ?>" data-vendor="<?php echo $payTypes['type']['alipay']; ?>" class="alipay">
                                    <img class="gou" src="./img/pay/gou.png" />
                                    <img class="img img-selected" src="./img/pay/ali-pay-active.png" />
                                </a>
                                <?php } ?>

                                <?php if ($payTypes['payStatus']['wapJd'] == 1) { ?><!--京东-->
                                <a data-type="5" data-value="<?php echo $wapJdPayType; ?>" data-vendor="<?php echo $payTypes['type']['wapJd']; ?>" class="jd">
                                    <img class="gou" src="./img/pay/gou.png" />
                                    <img class="img img-selected" src="./img/pay/jd-pay-active.png" />
                                </a>
                                <?php } elseif ($payTypes['payStatus']['jd'] == 1) { ?>
                                <a data-type="5" data-value="<?php echo $jdPayType; ?>" data-vendor="<?php echo $payTypes['type']['jd']; ?>" class="jd">
                                    <img class="gou" src="./img/pay/gou.png" />
                                    <img class="img img-selected" src="./img/pay/jd-pay-active.png" />
                                </a>
                                <?php } ?>

                                <?php if ($payTypes['payStatus']['wapBaidu'] == 1) { ?><!--百度-->
                                <a data-type="6" data-value="<?php echo $wapBaiduPayType; ?>" data-vendor="<?php echo $payTypes['type']['wapBaidu']; ?>" class="baidu">
                                    <img class="gou" src="./img/pay/gou.png" />
                                    <img class="img img-selected" src="./img/pay/baidu-pay-active.png" />
                                </a>
                                 <?php } elseif ($payTypes['payStatus']['baidu'] == 1) { ?>
                                <a data-type="6" data-value="<?php echo $baiduPayType; ?>" data-vendor="<?php echo $payTypes['type']['baidu']; ?>" class="baidu">
                                    <img class="gou" src="./img/pay/gou.png" />
                                    <img class="img img-selected" src="./img/pay/baidu-pay-active.png" />
                                </a>
                                <?php } ?>

                                <?php if ($payTypes['payStatus']['wapUnion'] == 1) { ?><!--银联-->
                                <a data-type="7" data-value="<?php echo $wapUnionPayType; ?>" data-vendor="<?php echo $payTypes['type']['wapUnion']; ?>" class="union">
                                    <img class="gou" src="./img/pay/gou.png" />
                                    <img class="img img-selected" src="./img/pay/union-pay-active.png" />
                                </a>
                                <?php } elseif ($payTypes['payStatus']['union'] == 1) { ?>
                                <a data-type="7" data-value="<?php echo $unionPayType; ?>" data-vendor="<?php echo $payTypes['type']['union']; ?>" class="union">
                                    <img class="gou" src="./img/pay/gou.png" />
                                    <img class="img img-selected" src="./img/pay/union-pay-active.png" />
                                </a>
                                <?php } ?>
                            </div>
                            <input class="btn-submit" type="button" onclick="doSubmit('pay_form')" value="确认提交" />
                        </form>
                    </div>

                    <div class="qr-pay">
                        <div class="qp1 qshow">
                            <div class="ali-row" style="padding-top:.5rem;">
                                <span>会员账号：</span>
                                <input style="text-indent:10px;" id="qrpay-account" type="text" placeholder="请输入需要存款的会员账号" />
                            </div>
                            <input style="margin-top:6rem;" class="btn-submit" type="button" onclick="wechatNext()" value="下一步" />
                        </div>
                    </div>

                    <div class="qq-pay">
                        <div class="qp1 qshow">
                            <div class="ali-row" style="padding-top:.5rem;">
                                <span>会员账号：</span>
                                <input style="text-indent:10px;" id="qqpay-account" type="text" placeholder="请输入需要存款的会员账号" />
                            </div>
                            <input style="margin-top:6rem;" class="btn-submit" type="button" onclick="qqNext()" value="下一步" />
                        </div>
                    </div>

                    <div class="ali-pay">
                        <div class="step1">
                            <div class="ali-row" style="margin-bottom:1rem;">
                                <span>会员账号：</span>
                                <input style="text-indent:10px;" id="alis-account" type="text" placeholder="请输入需要存款的会员账号" />
                            </div>

                            <div class="ali-row">
                                <span style=" vertical-align:top;display: inline-block;">付款方式：</span>
                                <div class="pay-type-box">
                                    <!-- <div class="pay-type-item p-active" data-type="1">
                                        <span class="tab-selected"></span>转到银行卡</div>
                                    <div class="pay-type-item act" data-type="2">
                                        <span class="tab-selected"></span>转到支付宝</div>
                                    <div class="pay-type-item qr" data-type="3">
                                        <span class="tab-selected"></span>扫码支付</div> -->
                                </div>
                            </div>

                            <input style="margin-top:3rem;" class="btn-submit" type="button" onclick="alipayNext()" value="下一步" />
                        </div>
                    </div>

                    <div class="transfer">
                        <div class="p1 show">
                            <div class="row" style="padding-top:1rem;">
                                <span>会员账号：</span>
                                <input style="text-indent:10px;" id="tf-account" type="text" placeholder="请输入需要存款的会员账号" />
                            </div>
                            <input type="button" class="btn-submit" onclick="transferNext()" value="下一步" />
                        </div>

                        <div class="p2">
                            <div class="wrapper">
                                <input id="bankId1" type="hidden" value="" />
                                <div class="form-row">
                                    <span>开户银行：</span>
                                    <div id="bank-name"></div>
                                </div>

                                <div class="form-row">
                                    <span>银行卡号：</span>
                                    <div id="cardno"></div>
                                </div>

                                <div class="form-row" style="margin-bottom:.2rem;">
                                    <span>银行户名：</span>
                                    <div id="account-name"></div>
                                </div>
                            </div>
                            <div class="row">
                                    <span>存款金额：</span>
                                    <input id="tf-money" type="text" placeholder="请确认您的存款金额" onkeyup="this.value=this.value.replace(/[^\d{1,}\.\d{1,}|\d{1,}]/g,'')"
                                        onafterpaste="this.value=this.value.replace(/[^\d{1,}\.\d{1,}|\d{1,}]/g,'')" />
                            </div>
                            <div class="row">
                                <span style="text-align:center;">存款姓名：</span>
                                <input id="tf-name" type="text" placeholder="请确认您的存款人姓名" />
                            </div>
                            <P class="tip"><span>温馨提示：</span>
                                <br/>*复制账号户名至银行界面转账；
                                <br/>*为了不影响您的体验游戏，建议选择实时到账；
                                <br/>*转账成功后，输入存款信息，点击“提交”按钮。</P>
                            <button class="btn btn-complete" onclick="transferSubmit()">提交</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer">
                <div class="copy-right">Copyright © 澳亚国际 Reserved</div>
            </div>
        </div>
        <div id="mask">
            <span class="alert_words green">错误信息</span>
        </div>
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
        <script type="text/javascript" src="../js/zeroClipboard.min.js"></script>
        <script type="text/javascript" src="../js/qrcode.min.js"></script>
        <script type="text/javascript" src="./js/layer.js"></script>
        <script type="text/javascript" src="./js/pay.js"></script>
        <script>
        var _hmt = _hmt || [];
        (function() {
          var hm = document.createElement("script");
          hm.src = "https://hm.baidu.com/hm.js?66b60f37dcdd95821feeb6962a9ab34a";
          var s = document.getElementsByTagName("script")[0];
          s.parentNode.insertBefore(hm, s);
        })();
        </script>
    </body>

    </html>