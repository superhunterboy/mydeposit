/**
 * Created by Administrator on 2016/12/3. modified by silen on 2017/05/19
 */
Date.prototype.format = function(fmt) {
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
$(function() {
    setInterval(function() {
        var time = new Date();
        $(".timeInput").val(time.format("yyyy-MM-dd hh:mm:ss"));
    }, 1000);
    $("#pay_type").val($(".check-icon.checked").attr("data-value"));
    $(".content").on("click", ".one-type", function() {
        var _self = $(this).find(".check-icon");
        var val = _self.attr("data-value");
        var type = _self.attr("data-type");
        var vendor = _self.attr("data-vendor");
        if (_self.hasClass("checked")) {
            return;
        }
        $(".checked").removeClass("checked");
        _self.addClass("checked");
        $("#pay_type").val(val);
        $("#vendor_type").val(vendor);
        if (type == "3") {
            $("select").prop("disabled", false);
        } else {
            $("select").prop("disabled", true);
        }
    });
    $(".content").on("click", ".bank", function() {
        $(this)
            .addClass("bank-check")
            .siblings()
            .removeClass("bank-check");
    });

    $(".tab-item").on("click", function() {
        var m_div = $(this).attr("m-div");

        // if (m_div !== "transfer") {
        //   popInfos("正在维护中,请稍后再试");
        //   return;
        // }

        var _this = $(this);
        var $div = $("." + _this.attr("m-div"));
        _this.addClass("tab-active") && _this.siblings().removeClass("tab-active");
        $div.show() && $div.siblings().hide();
    });

    $(".other-btn").on("click", "a", function(e) {
        $(this).addClass("active") &&
        $(this)
            .siblings()
            .removeClass("active") &&
        e.preventDefault();

        var val = $(this).attr("data-value");
        var type = $(this).attr("data-type");
        var vendor = $(this).attr("data-vendor");

        $("#pay_type").val(val);
        $("#vendor_type").val(vendor);
        $("#s_type").val(type);
        $("#paytype2").val("");
    });

    $("#paytype2").on("change", function() {
        $(".other-btn .active").removeClass("active");
        var $bankCard = $(".b-card");

        var val = $bankCard.attr("data-value");
        var type = $bankCard.attr("data-type");
        var vendor = $bankCard.attr("data-vendor");

        $("#pay_type").val(val);
        $("#vendor_type").val(vendor);
        $("#s_type").val(type);
    });

    $("#vendor_type").val($(".active").attr("data-vendor"));
    $("#pay_type").val($(".active").attr("data-value"));
    $("#s_type").val($(".active").attr("data-type"));

    document.body.addEventListener("touchstart", function() {});
});

function doSubmit(formId) {
    var form = $("#" + formId);
    var account = form
            .find("input[name=account]")
            .val()
            .replace(/\s+/g, ""),
        money = form
            .find("input[name=money]")
            .val()
            .replace(/\s+/g, "");

    form.find("input[name=account]").val(account);
    form.find("input[name=money]").val(money);

    if (!account) {
        popInfos("请输入需要存款的会员账号");
        return false;
    }
    if (!money) {
        popInfos("请输入存款金额");
        return false;
    }

    if ($("#s_type").val() == "3") {
        if ($("#paytype2").val() == "") {
            popInfos("请选择银行卡");
            return false;
        }
    }

    memberCheck(account, function(rs) {
        if (rs == 0) {
            var url = "form.php";
            url += "?account=" + account;
            url += "&money=" + money;
            url += "&companyNo=" + $("#companyNo").val();
            url += "&vendor_type=" + $("#vendor_type").val();
            url += "&device=" + $("#device").val();
            url += "&pay_type=" + $("#pay_type").val();
            url += "&paytype2=" + $("#paytype2").val();
            window.open(url);

            setTimeout(function() {
                layer.open({
                    content: "是否完成支付?",
                    btn: ["已完成支付", "联系在线客服"],
                    shadeClose: false,
                    yes: function() {
                        window.location.reload();
                    },
                    no: function() {
                        window.open(
                            "https://chat7.livechatvalue.com/chat/chatClient/chatbox.jsp?companyID=660171&configID=61440&jid=3820856310&s=1"
                        );
                        window.location.reload();
                    }
                });
            }, 100);
        }
    });
}

function memberCheck(account, callback) {
    $.ajax({
        url: "../../api/member.php",
        async: false,
        type: "get",
        dataType: "json",
        data: { member: account },
        success: function(res) {
            if (res.status == 1) {
                popInfos(res.msg, 3000);
            } else {
                callback(res.status);
            }
        }
    });
}

function popInfos(msg, tm) {
    if (!tm) tm = 2500;
    var mask = $("#mask");
    mask
        .show()
        .find(".alert_words")
        .text(msg);
    var width = mask.find(".alert_words").width() + 20;
    mask
        .find(".alert_words")
        .css({ left: "50%", "margin-left": -width / 2 + "px" });
    setTimeout(function() {
        mask.hide();
    }, tm);
}

function transferNext() {
    var $p1 = $(".p1"),
        $p2 = $(".p2"),
        account = $("#tf-account")
            .val()
            .replace(/\s+/g, "");

    if (!account) {
        popInfos("请输入需要存款的会员账号");
        return;
    }

    memberCheck(account, function(rs) {
        if (rs == 0) {
            $.ajax({
                url: "../../api/getOffBank.php",
                type: "get",
                dataType: "json",
                data: {
                    account: account
                },
                success: function(res) {
                    if (res.status == 0) {
                        $("#bankId1").val(res.data.id);
                        $("#bank-name").text(res.data.bank_name);
                        $("#cardno").text(res.data.bank_number);
                        $("#account-name").text(res.data.user_name);
                        $p1.removeClass("show") && $p2.addClass("show");
                    } else {
                        popInfos(res.msg);
                    }
                }
            });
        }
    });
}

function transferSubmit() {
    var $p1 = $(".p1"),
        $p2 = $(".p2"),
        account = $("#tf-account")
            .val()
            .replace(/\s+/g, ""),
        money = $("#tf-money")
            .val()
            .replace(/\s+/g, ""),
        name = $("#tf-name")
            .val()
            .replace(/\s+/g, "");

    if (!account) {
        popInfos("请输入需要存款的会员账号");
        return;
    }

    if (!money) {
        popInfos("请确认您的存款金额");
        return;
    }

    if (!name) {
        popInfos("请确认您的存款人姓名");
        return;
    }

    memberCheck(account, function(rs) {
        if (rs == 0) {
            $.ajax({
                url: "../../api/bankInfo.php",
                type: "post",
                dataType: "json",
                data: {
                    account: account,
                    money: money,
                    name: name,
                    card_id : $("#bankId1").val(),
                    token: $("#token").val()
                },
                success: function(res) {
                    if (res.status == 0) {
                        $("#tf-account").val(""),
                            $("#tf-money").val(""),
                            $("#tf-name").val("");
                        $p2.removeClass("show") && $p1.addClass("show");
                        alert("您的存款信息已经提交成功，我们会尽快为您添加入款");
                    } else {
                        popInfos(res.msg);
                    }
                }
            });
        }
    });
}

function back() {
    $("#merchant-code").empty();
    $("#mcode").empty();
    $(".fshow").removeClass("fshow");
    $(".modal-page").hide() && $(".c-wrapper").show();
}

function getWechatQrcode() {
    layer.open({type: 2});
    $.ajax({
        url: "../../api/getQrcode.php",
        type: "get",
        dataType: "json",
        data: {
            type: 1
        },
        success: function(res) {
            layer.closeAll();
            if (res.status == 0) {
                var wechatQrType = res.data.type;
                $(".qr-pay .qp1").removeClass("qshow") &&
                $(".wechat-modal-page").addClass("qshow") &&
                $(".c-wrapper").hide();
                if (wechatQrType == "1") {
                    $(".wechat-personal")
                        .addClass("qfshow")
                        .siblings()
                        .removeClass("qfshow");
                    $(".pay2qr-code").attr("src", res.data.url+"?x-oss-process=image/resize,w_180");
                    $(".pay2qr-code").attr("init-id", res.data.id);
                    $(".group-img").hide();
                    $("#merchant-code").hide();
                    $(".pay2qr-code").show();
                } else if (wechatQrType == "2") {
                    $(".wechat-merchant")
                        .addClass("qfshow")
                        .siblings()
                        .removeClass("qfshow");
                    $("#merchant-code").attr("init-id", res.data.merId);
                    $(".pay2qr-code").hide();
                    $(".group-img").hide();
                    creatQRcode(res.data.url, "merchant-code");
                } else if (wechatQrType == "3") {
                    $(".wechat-group")
                        .addClass("qfshow")
                        .siblings()
                        .removeClass("qfshow");
                    $(".group-img").attr("src", res.data.url+"?x-oss-process=image/resize,w_180");
                    $(".group-img").attr("init-id", res.data.id);
                    $(".pay2qr-code").hide();
                    $("#merchant-code").hide();
                    $(".group-img").show();
                }
            } else if (res.status == 1) {
                $(".qr-pay .qp1").removeClass("qshow") &&
                $(".wechat-modal-page").addClass("qshow") &&
                $(".c-wrapper").hide();
                $(".wechat-addfriend")
                    .addClass("qfshow")
                    .siblings()
                    .removeClass("qfshow");

                $.ajax({
                    url: "../api/getWecthPic.php",
                    type: "get",
                    dataType: "json",
                    data: {
                        type: 1
                    },
                    success: function(res) {
                        if (res.status == 0) {
                            if (res.data.length > 0) {
                                $.each(res.data, function(idx, item) {
                                    if (item.type == 1) {
                                        $(".personl-img").attr("src", item.picture);
                                    }
                                });
                            }
                        } else {
                            popInfos(res.msg);
                        }
                    }
                });
            } else {
                popInfos(res.msg);
            }

            $(window).scrollTop(0);
        }
    });
}


function getQQrcode() {
    layer.open({type: 2});
    $.ajax({
        url: "../../api/getQrcode.php",
        type: "get",
        dataType: "json",
        data: {
            type: 3
        },
        success: function(res) {
            layer.closeAll();
            if (res.status == 0) {
                $(".qq-pay .qp1").removeClass("qshow") &&
                $(".qq-modal-page").addClass("qshow") &&
                $(".c-wrapper").hide();
               
                $(".qq-personal")
                    .addClass("qfshow")
                    .siblings()
                    .removeClass("qfshow");
                $(".pay2qq-code").attr("src", res.data.url+"?x-oss-process=image/resize,w_180");
                $(".pay2qq-code").attr("init-id", res.data.id);
                $(".pay2qq-code").show();
            } else if (res.status == 1) {
                    popInfos(res.msg);
            } else {
                popInfos(res.msg);
            }

            $(window).scrollTop(0);
        }
    });
}


function wechatNext() {
    var account = $("#qrpay-account")
        .val()
        .replace(/\s+/g, "");

    if (!account) {
        popInfos("请输入需要存款的会员账号", 1500);
        return;
    }

    memberCheck(account, function(rs) {
        if (rs == 0) {
            getWechatQrcode();
        }
    });
}
function qqNext() {
    var account = $("#qqpay-account")
        .val()
        .replace(/\s+/g, "");

    if (!account) {
        popInfos("请输入需要存款的会员账号", 1500);
        return;
    }

    memberCheck(account, function(rs) {
        if (rs == 0) {
            getQQrcode();
        }
    });
}
function wback() {
    $("#merchant-code").empty();
    $("#mcode").empty();
    $(".wechat-modal-page").removeClass("qshow") &&
    $(".qr-pay .qp1").addClass("qshow") &&
    $(".c-wrapper").show();
}
function qback() {
    $(".qq-modal-page").removeClass("qshow") &&
    $(".qq-pay .qp1").addClass("qshow") &&
    $(".c-wrapper").show();
}
function cleanWf() {
    $("#qrpay-money").val("");
    $("#wechat-account").val("");
    $("#qrpay-account").val("");
    $("#merchant-order").val("");
    wback();
}
function cleanQf() {
    $("#qq-money").val("");
    $("#qq-drawee").val("");
    $("#qqpay-account").val("");
    qback();
}

function wechatPaySubmit(wechatQrType) {
    if (wechatQrType) {
        var obj = { type: 1 };
        obj.member = $("#qrpay-account")
            .val()
            .replace(/\s+/g, "");
        if (wechatQrType == "1") {
            obj.id = $(".pay2qr-code").attr("init-id");
            obj.money = $("#qrpay-money")
                .val()
                .replace(/\s+/g, "");
            obj.drawee = $("#wechat-account")
                .val()
                .replace(/\s+/g, "");
            if (!obj.money) {
                popInfos("请确认您的存款金额");
                return;
            }

            if (!obj.drawee) {
                popInfos("请输入您的微信账户昵称");
                return;
            }

            $.ajax({
                url: "../../api/addQrcodeOrder.php",
                type: "post",
                dataType: "json",
                data: obj,
                success: function(res) {
                    if (res.status == 0) {
                        alert("您的存款信息已经提交成功，我们会在5分钟之内为您添加入款");
                        cleanWf();
                    } else {
                        popInfos(res.msg, 2000);
                    }
                }
            });
        } else if (wechatQrType == "2") {
            obj.order = $("#merchant-order").val();
            obj.merchant_id = $("#merchant-code").attr("init-id");

            if (!obj.order) {
                popInfos("请输入商户单号后七位");
                return;
            }

            $.ajax({
                url: "../api/addMerOder.php",
                type: "post",
                dataType: "json",
                data: obj,
                success: function(res) {
                    if (res.status == 0) {
                        alert("您的存款信息已经提交成功，我们会在5分钟之内为您添加入款");
                        cleanWf();
                    } else {
                        popInfos(res.msg, 2000);
                    }
                }
            });
        }
    }
}

function qqPaySubmit(wechatQrType) {
    var obj = { type: 3 };
    obj.member = $("#qqpay-account")
        .val()
        .replace(/\s+/g, "");
        obj.id = $(".pay2qq-code").attr("init-id");
        obj.money = $("#qq-money")
            .val()
            .replace(/\s+/g, "");
        obj.drawee = $("#qq-drawee")
            .val()
            .replace(/\s+/g, "");
        if (!obj.money) {
            popInfos("请确认您的存款金额");
            return;
        }

        if (!obj.drawee) {
            popInfos("请输入您的QQ账户昵称");
            return;
        }

        $.ajax({
            url: "../../api/addQrcodeOrder.php",
            type: "post",
            dataType: "json",
            data: obj,
            success: function(res) {
                if (res.status == 0) {
                    alert("您的存款信息已经提交成功，我们会在5分钟之内为您添加入款");
                    cleanQf();
                } else {
                    popInfos(res.msg, 2000);
                }
            }
        });
}

function alipayNext() {
    var account = $("#alis-account")
        .val()
        .replace(/\s+/g, "");
    var type = $(".p-active").attr("data-type");

    if (!account) {
        popInfos("请输入需要存款的会员账号", 1500);
        return;
    }

    memberCheck(account, function(rs) {
        if (rs == 0) {
            switch (type) {
                case "1":
                    $(".modal-page").show() && $(".c-wrapper").hide();
                    $(".ali-transfer")
                        .addClass("fshow")
                        .siblings()
                        .removeClass("fshow");
                    break;
                case "2":
                    getSingleAlipay(2, function(res) {
                        $(".modal-page").show() && $(".c-wrapper").hide();
                        if (res.data.url) {
                            $(".ali-act .act-qr-code img").attr("src", res.data.url+"?x-oss-process=image/resize,w_180");
                            $(".ali-act .act-qr-code img").attr("data-id", res.data.id);
                        }

                        $(".ali-act")
                            .addClass("fshow")
                            .siblings()
                            .removeClass("fshow");
                    });
                    break;
                case "3":
                    getSingleAlipay(3, function(res) {
                        $(".modal-page").show() && $(".c-wrapper").hide();
                        if (res.data) {
                            $("#mcode").attr("data-id", res.data.merId);
                        }
                        creatQRcode(res.data.url, "mcode");

                        $(".ali-qr")
                            .addClass("fshow")
                            .siblings()
                            .removeClass("fshow");
                    });
                    break;
            }

            $(window).scrollTop(0);
        }
    });
}

function alipaySubmit(type) {
    var obj = {};
    switch (type) {
        case 1:
            obj.account = $("#alis-account")
                .val()
                .replace(/\s+/g, "");
            obj.amount = $("#ct-money")
                .val()
                .replace(/\s+/g, "");
            obj.card_id = $("#bankId").val();
            obj.token = $("#token").val();
            obj.type = 2;
            obj.depositor = $("#ct-name")
                .val()
                .replace(/\s+/g, "");

            if (!obj.amount) {
                popInfos("请确认您的存款金额", 1500);
                return;
            }

            if (!obj.depositor) {
                popInfos("请输入您的支付宝昵称", 1500);
                return;
            }

            $.ajax({
                url: "../../api/bankInfo.php",
                type: "post",
                dataType: "json",
                data: obj,
                success: function(res) {
                    if (res.status == 0) {
                        alert("您的存款信息已经提交成功，我们会在5分钟之内为您添加入款");
                        back();
                        clearForm();
                    } else {
                        popInfos(res.msg, 1500);
                    }
                }
            });
            break;
        case 2:
            obj.member = $("#alis-account")
                .val()
                .replace(/\s+/g, "");
            obj.money = $("#act-money")
                .val()
                .replace(/\s+/g, "");
            obj.id = $(".ali-act .act-qr-code img").attr("data-id");
            obj.type = "2";
            obj.drawee = $("#act-name")
                .val()
                .replace(/\s+/g, "");

            if (!obj.money) {
                popInfos("请确认您的存款金额", 1500);
                return;
            }

            if (!obj.drawee) {
                popInfos("请输入您的支付宝昵称", 1500);
                return;
            }

            $.ajax({
                url: "../../api/addQrcodeOrder.php",
                type: "post",
                dataType: "json",
                data: obj,
                success: function(res) {
                    if (res.status == 0) {
                        alert("您的存款信息已经提交成功，我们会在5分钟之内为您添加入款");
                        back();
                        clearForm();
                    }
                }
            });
            break;
        case 3:
            obj.member = $("#alis-account")
                .val()
                .replace(/\s+/g, "");
            obj.money = $("#qct-money")
                .val()
                .replace(/\s+/g, "");
            obj.merchant_id = $("#mcode").attr("data-id");
            obj.type = "2";
            obj.order = $("#qct-no")
                .val()
                .replace(/\s+/g, "");

            if (!obj.money) {
                popInfos("请确认您的存款金额", 1500);
                return;
            }

            if (!obj.order) {
                popInfos("请输入交易单号后七位", 1500);
                return;
            }

            $.ajax({
                url: "../../api/addMerOder.php",
                type: "post",
                dataType: "json",
                data: obj,
                success: function(res) {
                    if (res.status == 0) {
                        alert("您的存款信息已经提交成功，我们会在5分钟之内为您添加入款");
                        back();
                        clearForm();
                    }
                }
            });
            break;
    }
}

function clearForm() {
    $("#merchant-code").empty();
    $("#mcode").empty();
    $("#ct-money").val("");
    $("#ct-name").val("");
    $("#qct-money").val("");
    $("#qct-no").val("");

    $("#alis-account").val("");
    $(".modal-page input").val("");
    $("div[title='点击复制']").remove();
}

function getAliPaySetting() {
    $.ajax({
        url: "../../api/getAliSet.php",
        type: "get",
        dataType: "json",
        data: {},
        success: function(res) {
            if (res.status == 0 && res.data[0].val) {
                var html = "";
                var aliSet = res.data[0].val;
                if (aliSet) {
                    aliSet.split(",").forEach(function(item) {
                        if (item == "1") {
                            html +=
                                '<div class="pay-type-item p-active" data-type="1"><span class="tab-selected"></span>转到银行卡</div>';
                            getbankCard();
                        }
                        if (item == "2")
                            html +=
                                '<div class="pay-type-item act" data-type="2"><span class="tab-selected"></span>个人支付宝扫码</div>';
                        if (item == "3")
                            html +=
                                '<div class="pay-type-item qr" data-type="3"><span class="tab-selected"></span>商家扫码</div>';
                    });
                    $(".pay-type-box").append(html);
                    $(".pay-type-box div")
                        .eq(0)
                        .addClass("p-active");
                    $(".pay-type-item").bind("click", function() {
                        $(this)
                            .addClass("p-active")
                            .siblings()
                            .removeClass("p-active");
                    });
                }
            } else {
                $(".tab-item").css("width", "33.33%");
                $(".apli-tab-item").hide();
            }
        }
    });
}

function onbankChanged(e) {
    var bankName = $(e).val();

    $(".show-bank").removeClass("activited");
    $(".show-bank").each(function(idx, item) {
        if (bankName.indexOf($(item).attr("type")) != -1) {
            $(item).addClass("activited");
        }
    });

    $.each(bankList, function(idx, bank) {
        if (bankName == bank.bank_name) {
            $("#bankId").val(bank.id);
            $("#ali-bank-name").text(bank.bank_name);
            $("#ali-cardno").text(bank.bank_number);
            $("#ali-account-name").text(bank.user_name);
            // $("#ali-bank-address").text(bank.address);
            return false;
        }
    });
}

var bankList;
function getbankCard() {
    $.ajax({
        url: "../../api/getBanks.php",
        type: "get",
        dataType: "json",
        data: {},
        success: function(res) {
            if (res.status == 0) {
                var html = "";
                bankList = res.data;
                if (bankList && bankList.length > 0) {
                    $.each(res.data, function(idx, item) {
                        html +=
                            '<option value="' +
                            item.bank_name +
                            '">' +
                            item.bank_name +
                            "</option>";
                    });
                    $("#bank-selector").append(html);
                    setTimeout(function() {
                        $("#bank-selector").val(bankList[0].bank_name);
                        onbankChanged($("#bank-selector").get(0));
                    }, 50);
                }
            } else {
                popInfos(res.msg, 2000);
            }
        }
    });
}

function getSingleAlipay(paytype, callback) {
    layer.open({type: 2});
    $.ajax({
        url: "../../api/getQrcode.php",
        type: "get",
        dataType: "json",
        data: {
            type: 2,
            payType: paytype
        },
        success: function(res) {
            layer.closeAll();
            if (res.status == 0) {
                if (callback) callback(res);
            } else {
                popInfos(res.msg, 2000);
            }
        }
    });
}

function creatQRcode(url, id) {
    $("#" + id).hide();
    $("#" + id).empty();

    var qrcode = new QRCode(id, {
        text: url,
        width: 300,
        height: 300,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });

    setTimeout(function() {
        $("#" + id).show();
    }, 20);
}

function init() {
    getAliPaySetting();
}

init();