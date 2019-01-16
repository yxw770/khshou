
$(function () {
  // 回车键监听
  document.onkeydown = function(e) {
    // 兼容FF和IE和Opera
    var theEvent = e || window.event;
    var code = theEvent.keyCode || theEvent.which || theEvent.charCode;
    if (code == 13) {
        submitModal();//具体处理函数
        return false;
    }
    return true;
  };
  $('.logo_mode a').click(function () { $(this).addClass('actived').siblings('a').removeClass('actived'); });
  $('.logo_mode a:first-child').click(function () {
    $('.email_mode').hide();
    $('.phone_mode').addClass('animated fadeInRight').show();
    mode="modeTel";
  });
  $('.logo_mode a:last-child').click(function () {
    $('.phone_mode').hide();
    $('.email_mode').addClass('animated fadeInRight').show();
    mode="modeEmail";
  });
});

var clickAbleSms = true;
var timeoutSms;
/**
 * 再次发送倒计时
 */
function countdownSms(count) {
  clickAbleSms = false;
  window.clearTimeout(timeoutSms);
  $('.yzm_btn').html(count + 's');
  timeoutSms = window.setTimeout(function () {
    if ( count <= 0) {
      $('.yzm_btn').html('获取验证码');
      clickAbleSms = true;
    } else {
      count -= 1;
      clickAbleSms = false;
      countdownSms(count);
    }
  }, 1000);
}
/**
 * 点击获取短信验证码
 */
function getCapthca() {
  if (!clickAbleSms) { return; }
  var $username = $('[name=username]');
  if ($username.val() == '') {
    YsToast('error', '请输入手机号');
    return ;
  } else if (!isPhone($username.val())) {
     YsToast('error', '请输入正确的手机号');
     return ;
   }
  var content = '<div class="yzm_box" style="position:relative;width:90%;margin:0 auto;"><input type="text" name="imgcode1" placeholder="请输入验证码" style="display:block;height:40px;width: calc(100% - 20px);padding: 0 10px;margin:20px auto 0 auto;background:#fff;border: 1px solid #eee;border-radius: 3px;font-size: 14px;"><img id="imgcode1"  alt="点击更换"  style="position: absolute;top: 2px; right: 0"></div>';
  var myModal = new jBox('Confirm', {
    title: '图片验证码',
    content: content,
    isolateScroll: false,
    animation: 'zoomIn',
    confirmButton: '确认',
    cancelButton: '关闭',
    closeOnConfirm: false,
    blockScroll: true,
    delayOpen: 0,
    onOpen: function(){
      $('#imgcode1').trigger('click');
      $('[name=imgcode1]').val(null);
    },
    confirm: function(){
      var $imgcode1 = $('[name=imgcode1]');
      if (isBlank($imgcode1.val())) {
        YsToast('error', '请输入验证码');
      } else {
        doSendSms($username, $imgcode1);
        myModal.destroy();
      }
    },
    onCloseComplete: function() {
      myModal.destroy();
    }
  });
  myModal.open();
}
/**
 * 获取短信验证码
 */
function doSendSms($username, $imgcode) {
  YsToast('loading', '正在发送');
  $.post('/back/sms', {username:$username.val(), imgcode:$imgcode.val()})
  .success(function(ret) {
    if (ret.state == 'success') {
      YsToast(ret.state, ret.msg);
      countdownSms(90);
    } else {
      YsToast('error', ret.msg);
    }
  })
  .error(function(ret) { YsToast('error', '网络错误'); });
}

function submitModal(type) {
  var $username = $('[name=username]');
  var $password = $('[name=password]');
  var $email    = $('[name=email]');
  var $smsCaptcha = $('[name=smsCaptcha]');
  var $imgCaptcha = $('[name=imgCaptcha]');

  if (!isPhone($username.val())) {
    YsToast('error', '请填写手机号');
    return ;
  }
  if (!isPhone($username.val())) {
    YsToast('error', '请正确填写手机号');
    return ;
  }
  if ("tel" === type) {
    if (!doPasswordCheck($password)) {
      return ;
    }
    if (isBlank($smsCaptcha.val())) {
      YsToast('error', '请输入验证码');
      return ;
    }
  } else if ("email" === type) {
      if (isBlank($email.val())) {
          YsToast('error', '请输入邮箱地址');
          return;
      }
      if (!isEmail($email.val())) {
          YsToast('error', '请正确输入邮箱地址');
          return;
      }
      if (isBlank($imgCaptcha.val())) {
          YsToast('error', '请输入验证码');
          return;
      }
  } else {
      // 错误的类型
      YsToast('error', '表单数值错误');
      return;
  }
    YsToast('loading', '正在保存');
    let params = {
        type: type,
        username: $username.val(),
        password: encryptStr($password.val()),
        smsCaptcha: $smsCaptcha.val(),
        email: $email.val(),
        imgCaptcha: $imgCaptcha.val()
    };
    $.post('/back/do', params)
        .success(function (ret) {
            if (ret.state === 'success') {
                YsToast(ret.state, ret.msg);
                window.setTimeout(function () {
                    let href = "/user/login";
                    if (ret.href) {
                        href = ret.href;
                    }
                    window.location.href = href;
                }, 800);
            } else {
                YsToast('error', ret.msg);
                if (ret.captcha) {
                    $('#imgCaptcha').trigger('click');
                }
            }
        })
        .error(function(ret) { YsToast('error', '提交失败'); });
}

/**
 * 密码合法性检查
 * @return {[type]} [description]
 */
function doPasswordCheck($password) {
  if (isBlank($password.val())) {
    YsToast('error', '请输入密码');
    $password.val($.trim($password.val()));
    return false;
  } else if($password.val().length < 6 || $password.val().length > 14){
    YsToast('error', '密码6-14位，由字母+数字组成');
		return false;
	// }else if(!RegExp(/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,14}$/).test($password.val())){
	// 	YsToast('error','密码填写不符合要求<br/>密码6-14位，由字母+数字组成');
	// 	return false;
	}
	return true;
}