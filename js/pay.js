/**
 * Created by Administrator on 2016/12/3. modified by silen on 2017/05/19---1111
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
  $.ajax({
    url: "/api/getAffiche.php",
    type: "get",
    dataType: "json",
    success: function(res) {
      if (res.status == 0) {
        for(var i =0 ;i<res.data.length;i++){
          $(".news-text").append(res.data[i].content)
        }
      } else {
        $(".news-text").html("澳亚国际欢迎您！")
      }
    }
  });

  setInterval(function() {
    var time = new Date();
    $(".timeInput").val(time.format("yyyy-MM-dd hh:mm:ss"));
  }, 1000);

  $(".tab-item").bind("click", function() {
    var m_div = $(this).attr("m-div");

    // if(m_div!="transfer-pay"){
    //   popInfos("正在维护中，请稍后再试。");
    //   return;
    // }

   
    if(m_div !== "transfer-pay")$("div[title='点击复制']").hide();
    else $("div[title='点击复制']").show();
    

    if (m_div === "qrcode-pay") {
      $(".imp-tips").hide();
      $(".tab").attr("class", "tab tab-qr");
    } else if (m_div === "transfer-pay") {
      $(".imp-tips").show();
      $(".tab").attr("class", "tab tab-transfer");
    } else if (m_div === "ali-pay") {
      $(".imp-tips").hide();
      $(".tab").attr("class", "tab tab-alipay");
    } else if (m_div === "qq-pay") {
      $(".imp-tips").hide();
      $(".tab").attr("class", "tab tab-qq");
    }  else {
      $(".imp-tips").hide();
      $(".tab").attr("class", "tab");
    }

    var _this = $(this);
    var $div = $("." + _this.attr("m-div"));
    _this.addClass("tab-active") && _this.siblings().removeClass("tab-active");
    $div.show() && $div.siblings().hide();
  });

  $(".bank-list").on("click", ".bank-item", function() {
    var gftid=$("#gftinputhid").val();

    if(gftid == 69){
      $("#gftbanknum").css("display","block")
    }
    $(this).addClass("active") &&
      $(this)
        .siblings()
        .removeClass("active") &&
      $(".btn-pay-box .active").removeClass("active");
    setData();
  });

  $(".btn-pay-box").on("click", ".btn-pay", function() {
    $("#gftbanknum").css("display","none");
    $(this).addClass("active") &&
      $(this)
        .siblings()
        .removeClass("active") &&
      $(".bank-list .active").removeClass("active");
    setData();
  });

  function setData() {
    var $selected = $("#pay_form .active");

    var val = $selected.attr("data-value");
    var type = $selected.attr("data-type");
    var vendor = $selected.attr("data-vendor");

    $("#pay_type").val(val);
    $("#vendor_type").val(vendor);

    if (type == "3") {
      var id = $selected.attr("data-id");
      $("#paytype2").val(id);
    }
  }

  if($(".bank-list").children().length>0){
    $(".bank-list").children().eq(0).addClass("active");
    $("#paytype2").val($(".bank-list").children().eq(0).attr("data-id"));
  }else{
    $(".bwechat").addClass("active");
  }
  $("#pay_type").val($("#pay_form .active").attr("data-value"));
  $("#vendor_type").val($("#pay_form .active").attr("data-vendor"));
});

function doSubmit(formId) {
  var form = $("#" + formId);
  var account = form.find("input[name=account]").val().replace(/\s+/g, "");
  if(formId=='wechat_pay_form')
  {
      var money = form.find("input:radio[name=money]:checked").val();
  }
  else  var money = form.find("input[name=money]").val().replace(/\s+/g, "");
  var type = $("#pay_type").val();

  var gftinputhid = $("#gftinputhid").val();
  form.find("input[name=account]").val(account);
  form.find("input[name=money]").val(money);

  if (!account) {
    popInfos("请输入会员账号");
    return false;
  }
  if (!money) {
    popInfos("请输入存款金额");
    return false;
  }
  if (money<1) {
    popInfos("最低金额不能低于1元");
    return false;
  }
  // 迅捷微信充值
  if($("#vendor_type").val() == "48" && $("#pay_type").val() == "0101"){
    if (money<300) {
      popInfos("最低金额不能低于300元");
      return false;
    }
    if (money>10000) {
      popInfos("最低金额不能高于10000元");
      return false;
    }
  }
  if(!type){
    popInfos("请选择支付方式");
    return false;
  }

  $(".btn-submit").addClass("no-pointer");
  $(".btn-submit").val("处理中...");

  if(gftinputhid == 69 && formId == "pay_form"){
    var type2 = $("#paytype2").val();
    var banknumber = $("#banknumber").val();
    //var fruits = ["1001","1002","1003","1004","1005","1006","1007","1008","1009","1010","1011","1012","1013","1014","1015","1016","1017","1018","1019","1020","1021","1022","1023","1024","1025","1026","1027"];
    //var a = fruits.indexOf(type2);
    if(type2 != "" &&　type == '01'){
      if(!banknumber){
        popInfos("请输入存款的银行卡号");
        return false;
      }
    }
  }

  memberCheck(account, function(rs) {
    if (rs == 0) form.submit();
  });
}

function memberCheck(account, callback) {
  $.ajax({
    url: "../api/member.php",
    type: "get",
    dataType: "json",
    data: {
      member: account.replace(/\s+/g, "")
    },
    success: function(res) {
      if (res.status == 1) {
        popInfos(res.msg);
        location.reload();
      } else {
        callback(res.status);
      }
    }
  });
}

function popInfos(msg, tm) {
  if (!tm) tm = 2000;
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

function showTransferPanel() {
  var $p1 = $(".p1"),
    $p2 = $(".p2"),
    account = $("#tf-account")
      .val()
      .replace(/\s+/g, ""),
    money = $("#tf-money").val().replace(/\s+/g, ""),
    name = $("#tf-name").val().replace(/\s+/g, "");

  if ($p1.hasClass("ps")) {
    if (!account) {
      popInfos("请输入需要存款的会员账号", 1500);
      return;
    }

    memberCheck(account, function(rs) {
      if (rs == 0) {
        $.ajax({
          url: "../api/getOffBank.php",
          type: "get",
          dataType: "json",
          data: {
            account: account
          },
          success: function(res) {
            if (res.status == 0) {
                $("#bank-name").text(res.data.bank_name);
                $("#cardno").text(res.data.bank_number);
                $("#account-name").text(res.data.user_name);
                $("#bankId1").val(res.data.id);
              $p1.removeClass("ps") && $p2.addClass("ps");
              $(".transfer-button").attr("value", "提交");
              $(".imp-tips").show();
              copy("d_clip_button", "cardno");
            } else {
              popInfos(res.msg, 1500);
            }
          }
        });
      }
    });
  } else {
    if (!money) {
      popInfos("请确认您的存款金额");
      return;
    }

    if (!name) {
      popInfos("请确认您的存款人姓名");
      return;
    }

    $.ajax({
      url: "../api/bankInfo.php",
      type: "post",
      dataType: "json",
      data: {
        account: account,
        money: money,
        type: 1,
        name: name,
        card_id : $("#bankId1").val(),
        token: $("#token").val()
      },
      success: function(res) {
        if (res.status == 0) {
          alert("您的存款信息已经提交成功，我们会在5分钟之内为您添加入款");
          $("#tf-account").val(""),
            $("#tf-money").val(""),
            $("#tf-name").val("");
          $(".transfer-button").attr("value", "下一步");
          $p2.removeClass("ps") && $p1.addClass("ps");
          $(".imp-tips").hide();
        }
      }
    });
  }
}

var wechatQrType;
function getWechatQrcode() {
  $.ajax({
    url: "../api/getQrcode.php",
    type: "get",
    dataType: "json",
    data: {
      type: 1
    },
    success: function(res) {
      if (res.status == 0) {
        wechatQrType = res.data.type;
        $(".qrcode-pay .wp-1").removeClass("wshow") && $(".qrcode-pay .wp-2").addClass("wshow");
        if (wechatQrType == "1") {
          $(".qrcode-pay .imp-tips").show();
          $(".personal").addClass("wfshow");
          $(".qr-img").attr("src", res.data.url+"?x-oss-process=image/resize,w_160");
          $(".qr-img").attr("init-id", res.data.id);
          $(".qr-img").show();
        } else if (wechatQrType == "2") {
          $(".qrcode-pay .imp-tips").show();
          $(".merchant").addClass("wfshow");
          $("#wtcode").attr("init-id", res.data.merId);
          $(".qr-img").hide();
          creatQRcode(res.data.url, "wtcode");
        } else if (wechatQrType == "3") {
          $(".wp-2").addClass("group");
          $(".wp-2 .btn-submit").hide();
          $("#wtcode").hide();
          $(".qr-img").attr("src", res.data.url+"?x-oss-process=image/resize,w_160");
          $(".qr-img").attr("init-id", res.data.id);
        }
      } else if(res.status==1){
        $(".qrcode-pay .part2").addClass("pps") && $(".qrcode-pay .part1").removeClass("pps");
        $.ajax({
          url: "../api/getWecthPic.php",
          type: "get",
          dataType: "json",
          data: {
            type: 1
          },
          success: function(res) {
            if (res.status == 0) {
              if(res.data.length>0){
                $.each(res.data,function(idx,item){
                  if(item.type==1){
                    $(".personl-img").attr("src", item.picture);
                  }
                })
              }
            } else {
              popInfos(res.msg);
            }
          }
        });
      }else{
        popInfos(res.msg);
      }
    }
  });
}

function wechatPayNext() {
  var account = $("#wep-account")
    .val()
    .replace(/\s+/g, "");

  if (!account) {
    popInfos("请输入需要存款的会员账号", 1500);
    return;
  }

  memberCheck(account, function(rs) {
    if (rs == 0) {
      $("#wtcode").empty();
      getWechatQrcode();
    }
  });
}



function backWf(){
  $("#wep-account").val("");
  $(".qrcode-pay .part1").addClass("pps") && $(".qrcode-pay .part2").removeClass("pps");
}


function cleanWf() {
  $("#mcode").empty();
  $("#wtcode").empty();
  $(".wp-1").addClass("wshow") && $(".wp-2").removeClass("wshow");
  $(".qrcode-pay .wfshow").removeClass("wfshow");
  $("#pesonal-money").val("");
  $("#pesonal-name").val("");
  $("#wep-account").val("");
  $("#merchant-order").val("");
}

function wechatPaySubmit() {
  if (wechatQrType) {
    var obj = { type: 1 };
    obj.member = $("#wep-account")
      .val()
      .replace(/\s+/g, "");
    if (wechatQrType == "1") {
      obj.id = $(".qr-img").attr("init-id");
      obj.money = $("#pesonal-money").val().replace(/\s+/g, "");
      obj.drawee = $("#pesonal-name").val().replace(/\s+/g, "");
      if (!obj.money) {
        popInfos("请确认您的存款金额");
        return;
      }

      if (!obj.drawee) {
        popInfos("请输入您的微信账户昵称");
        return;
      }

      $.ajax({
        url: "../api/addQrcodeOrder.php",
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
      obj.merchant_id = $("#wtcode").attr("init-id");

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
    } else if (wechatQrType == "3") {
    }
  }
}

/* QQ */
function cleanQPanel(){
  $("#mcode").empty();
  $("#qqcode").empty();
  $(".qq-pay .qp-1").addClass("wshow") && $(".qq-pay .qp-2").removeClass("wshow");
  $(".qq-pay .wfshow").removeClass("wfshow");
  $("#qq-pesonal-money").val("");
  $("#qq-pesonal-name").val("");
  $("#qq-account").val("");
  $("#qq-merchant-order").val("");
}
function back_QQ(){
  $("#qq-account").val("");
  $(".qq-pay .part1").addClass("pps") && $(".qq-pay .part2").removeClass("pps");
}
function qqPayNext() {
  var account = $("#qq-account")
    .val()
    .replace(/\s+/g, "");

  if (!account) {
    popInfos("请输入需要存款的会员账号", 1500);
    return;
  }

  memberCheck(account, function(rs) {
    if (rs == 0) {
      $("#qqcode").empty();
      getQqQrcode();
    }
  });
}

var qqQrType;
function getQqQrcode() {
  $.ajax({
    url: "../api/getQrcode.php",
    type: "get",
    dataType: "json",
    data: {
      type: 4
    },
    success: function(res) {
      if (res.status == 0) {
        qqQrType = res.data.type;
        $(".qp-1").removeClass("wshow") && $(".qp-2").addClass("wshow");
        // if (qqQrType == "1") {
          $(".qp2 .imp-tips").show();
          $(".qq-personal").addClass("wfshow");
          $(".qq-qr-img").attr("src", res.data.url+"?x-oss-process=image/resize,w_160");
          $(".qq-qr-img").attr("init-id", res.data.id);
          $(".qq-qr-img").show();
        // } else if (qqQrType == "2") {
        //   $(".qp2 .imp-tips").show();
        //   $(".merchant").addClass("wfshow");
        //   $("#wtcode").attr("init-id", res.data.merId);
        //   $(".qq-qr-img").hide();
        //   creatQRcode(res.data.url, "qqcode");
        // } else if (qqQrType == "3") {
        //   $(".qp-2").addClass("group");
        //   // $(".qp-2 .btn-submit").hide();
        //   $("#qqcode").hide();
        //   $(".qq-qr-img").attr("src", res.data.url);
        //   $(".qq-qr-img").attr("init-id", res.data.id);
        // }
      } else if(res.status==1){
        popInfos(res.msg);
        // $(".qq-pay .part2").addClass("pps") && $(".qq-pay .part1").removeClass("pps");
        // $.ajax({
        //   url: "../api/getWecthPic.php",
        //   type: "get",
        //   dataType: "json",
        //   data: {
        //     type: 3
        //   },
        //   success: function(res) {
        //     if (res.status == 0) {
        //       if(res.data.length>0){
        //         $.each(res.data,function(idx,item){
        //           if(item.type==1){
        //             $(".qq-personl-img").attr("src", item.picture);
        //           }
        //         })
        //       }
        //     } else {
        //       popInfos(res.msg);
        //     }
        //   }
        // });
      }else{
        popInfos(res.msg);
      }
    }
  });
}


function qqPaySubmit() {
  if (qqQrType) {
    var obj = { type: 4};
    obj.member = $("#qq-account")
      .val()
      .replace(/\s+/g, "");
    // if (qqQrType == "1") {
      obj.id = $(".qq-qr-img").attr("init-id");
      obj.money = $("#qq-pesonal-money").val().replace(/\s+/g, "");
      obj.drawee = $("#qq-pesonal-name").val().replace(/\s+/g, "");
      if (!obj.money) {
        popInfos("请确认您的存款金额");
        return;
      }

      if (!obj.drawee) {
        popInfos("请输入您的QQ账户昵称");
        return;
      }

      $.ajax({
        url: "../api/addQrcodeOrder.php",
        type: "post",
        dataType: "json",
        data: obj,
        success: function(res) {
          if (res.status == 0) {
            alert("您的存款信息已经提交成功，我们会在5分钟之内为您添加入款");
            cleanQPanel();
          } else {
            popInfos(res.msg, 2000);
          }
        }
      });
    // } else if (qqQrType == "2") {
    //   obj.order = $("#qq-merchant-order").val();
    //   obj.merchant_id = $("#qqcode").attr("init-id");

    //   if (!obj.order) {
    //     popInfos("请输入商户单号后七位");
    //     return;
    //   }

    //   $.ajax({
    //     url: "../api/addMerOder.php",
    //     type: "post",
    //     dataType: "json",
    //     data: obj,
    //     success: function(res) {
    //       if (res.status == 0) {
    //         alert("您的存款信息已经提交成功，我们会在5分钟之内为您添加入款");
    //         cleanQPanel();
    //       } else {
    //         popInfos(res.msg, 2000);
    //       }
    //     }
    //   });
    // } else if (qqQrType == "3") {
    // }
  }
}
/*QQ*/ 

function alipayTypeChange(e) {
  var type = $(e).val();
  type != 1 ? $(".bank-select-box").hide() : $(".bank-select-box").show();
}

function alipayNext() {
  var account = $("#alipay-account")
    .val()
    .replace(/\s+/g, "");
  var type = $("#alipayType").val();
  var bankType = $("#bank-selector").val();

  if (!account) {
    popInfos("请输入需要存款的会员账号", 1500);
    return;
  }

  if (type == "0") {
    popInfos("请选择付款方式!", 1500);
    return;
  }

  memberCheck(account, function(rs) {
    if (rs == 0) {
      switch (type) {
        case "1":
          if (bankType == "0") {
            popInfos("请选择存款银行!", 1500);
            return;
          }

          $.each(bankList, function(idx, bank) {
            if (bankType == bank.bank_name) {
              $("#bankId").val(bank.id);
              $("#ali-bank-name").text(bank.bank_name);
              $("#ali-cardno").text(bank.bank_number);
              $("#ali-account-name").text(bank.user_name);
              return false;
            }
          });

          $(".alipay-bank-transfer")
            .addClass("show")
            .siblings()
            .removeClass("show");

          copy("ali_clip_button", "ali-cardno");
          break;
        case "2":
          getSingleAlipay(2, function(res) {
            if (res.data.url) {
              $(".alipay-account .ali-qrcode").attr("src", res.data.url+"?x-oss-process=image/resize,w_160");
              $(".alipay-account .ali-qrcode").attr("data-id", res.data.id);
            }

            $(".alipay-account")
              .addClass("show")
              .siblings()
              .removeClass("show");
          });

          break;
        case "3":
          getSingleAlipay(3, function(res) {
            if (res.data) {
              $(".alipay-qrcode .ali-qrcode").attr("data-id", res.data.merId);
            }
            creatQRcode(res.data.url, "mcode");
            $(".alipay-qrcode")
              .addClass("show")
              .siblings()
              .removeClass("show");
          });
          break;
      }
    }
  });
}

function alipaySubmit(type) {
  var obj = {};
  switch (type) {
    case 1:
      obj.account = $("#alipay-account")
          .val()
          .replace(/\s+/g, "");
      obj.type = 2;
      obj.card_id = $("#bankId").val();
      obj.amount = $("#ab-money").val().replace(/\s+/g, "");
      obj.token = $("#token").val();
      obj.depositor = $("#ab-name")
          .val()
          .replace(/\s+/g, "");

      if (!obj.amount) {
        popInfos("请确认您的存款金额");
        return;
      }

      if (!obj.depositor) {
        popInfos("请确认您的存款人姓名");
        return;
      }

      $.ajax({
        url: "../api/bankInfo.php",
        type: "post",
        dataType: "json",
        data: obj,
        success: function(res) {
          if (res.status == 0) {
            alert("您的存款信息已经提交成功，我们会在5分钟之内为您添加入款");
            cleanForm();
          } else {
            popInfos(res.msg, 2000);
          }
        }
      });
      break;
    case 2:
      obj.member = $("#alipay-account")
          .val()
          .replace(/\s+/g, "");
      obj.id = $(".alipay-account .ali-qrcode").attr("data-id");
      obj.type = "2";
      obj.money = $("#ac-money").val().replace(/\s+/g, "");
      obj.drawee = $("#ac-name")
          .val()
          .replace(/\s+/g, "");

      if (!obj.money) {
        popInfos("请确认您的存款金额");
        return;
      }

      if (!obj.drawee) {
        popInfos("请输入您的支付宝昵称");
        return;
      }

      $.ajax({
        url: "../api/addQrcodeOrder.php",
        type: "post",
        dataType: "json",
        data: obj,
        success: function(res) {
          if (res.status == 0) {
            alert("您的存款信息已经提交成功，我们会在5分钟之内为您添加入款");
            cleanForm();
          } else {
            popInfos(res.msg, 2000);
          }
        }
      });
      break;
    case 3:
      obj.member = $("#alipay-account")
          .val()
          .replace(/\s+/g, "");
      obj.merchant_id = $(".alipay-qrcode .ali-qrcode").attr("data-id");
      obj.type = "2";
      obj.money = $("#aliqr-money").val().replace(/\s+/g, "");
      obj.order = $("#order-no")
          .val()
          .replace(/\s+/g, "");

      if (!obj.money) {
        popInfos("请确认您的存款金额");
        return;
      }

      if (!obj.order) {
        popInfos("请输入交易单号后七位");
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
            cleanForm();
          } else {
            popInfos(res.msg, 2000);
          }
        }
      });
      break;
  }
}

function myalipaySubmit() {
  var obj = {};
  obj.member = $("#alipay-account").val().replace(/\s+/g, "");
  obj.money = $("#aliqr-money").val().replace(/\s+/g, "");

  if (!obj.member) {
    popInfos("请输入会员账号");
    return;
  }
  if (!obj.money) {
    popInfos("请确认您的存款金额");
    return;
  }


  memberCheck(obj.member, function(rs) {
    if (rs == 0)
    {$("#alipayform").submit();
      // $.ajax({
      //   url: "../api/addAliQrcodeOrder.php",
      //   type: "post",
      //   dataType: "json",
      //   data: obj,
      //   success: function(res) {
      //     if (res.status == 0) {
      //       location.href=res.qrCodeUrl;
      //     } else {
      //       popInfos(res.msg, 2000);
      //     }
      //   },
      //   error:function () {
      //     popInfos('网络错误，请刷新重试!', 2000);
      //   }
      // });
    }
  });

}


function back() {
  $("#ab-money").val("");
  $("#ab-name").val("");

  $("#ac-money").val("");
  $("#ac-name").val("");

  $("#aliqr-money").val("");
  $("#order-no").val("");
  $("#mcode").empty();
  $("#wtcode").empty();

  $(".ali-step1")
    .addClass("show")
    .siblings()
    .removeClass("show");
}

function cleanForm() {
  $("#alipay-account").val("");
  back();
}
function copy(buttonId, targetId) {
  $("div[title='点击复制']").remove();
  ZeroClipboard.setMoviePath("./js/ZeroClipboard.swf");
  var clip = new ZeroClipboard.Client();
  clip.setText("");
  clip.setHandCursor(true);
  clip.setCSSEffects(true);
  clip.addEventListener("load", function(client) {});
  clip.addEventListener("complete", function(client, text) {
    clip.hide();
  });
  clip.addEventListener("mouseOver", function(client) {});
  clip.addEventListener("mouseOut", function(client) {});
  clip.addEventListener("mouseDown", function(client) {
    clip.setText(document.getElementById(targetId).innerHTML);
  });
  clip.addEventListener("mouseUp", function(client) {
    alert("复制成功^-^");
  });
  clip.glue(buttonId);
}

function getAliPaySetting() {
  $.ajax({
    url: "../api/getAliSet.php",
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
              html += '<option value="1">转到银行卡</option>';
              getbankCard();
            }
            if (item == "2") html += '<option value="2">个人支付宝扫码</option>';
            if (item == "3") html += '<option value="3">商家扫码</option>';
          });
          $("#alipayType").append(html);
        }
      } else {
        $(".ali-tab-item").hide();
      }
    }
  });
}

var bankList;
function getbankCard() {
  $.ajax({
    url: "../api/getBanks.php",
    type: "get",
    dataType: "json",
    data: {},
    success: function(res) {
      if (res.status == 0) {
        var html = "";
        bankList = res.data;
        if (bankList) {
          $.each(res.data, function(idx, item) {
            html +=
              '<option value="' +
              item.bank_name +
              '">' +
              item.bank_name +
              "</option>";
          });
          $("#bank-selector").append(html);
        }
      } else {
        popInfos(res.msg, 2000);
      }
    }
  });
}

function getSingleAlipay(paytype, callback) {
  $.ajax({
    url: "../api/getQrcode.php",
    type: "get",
    dataType: "json",
    data: {
      type: 2,
      payType: paytype
    },
    success: function(res) {
      if (res.status == 0) {
        if (callback) callback(res);
      } else {
        popInfos(res.msg, 2000);
      }
    }
  });
}

function creatQRcode(url, id) {
  $("#"+id).hide();
  $("#"+id).empty();

  var qrcode = new QRCode(id, {
    text: url,
    width: 300,
    height: 300,
    colorDark: "#000000",
    colorLight: "#ffffff",
    correctLevel: QRCode.CorrectLevel.H
  });
  
  setTimeout(function(){
    $("#"+id).show();
  },20);
}

function init() {
  getAliPaySetting();
}

init();
