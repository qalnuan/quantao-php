<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:57:"C:\Code\Mine\crmeb/application/admin\view\login\index.php";i:1527060420;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="renderer" content="webkit">
    <title>登录 - CRMEB管理系统</title>
    <link href="{__FRAME_PATH}css/bootstrap.min.css?v=3.4.0" rel="stylesheet">
    <link href="{__FRAME_PATH}css/font-awesome.min.css?v=4.3.0" rel="stylesheet">
    <link href="{__FRAME_PATH}css/animate.min.css" rel="stylesheet">
    <link href="{__FRAME_PATH}css/style.min.css?v=3.0.0" rel="stylesheet">
    <script>
        top != window && (top.location.href = location.href);
    </script>
</head>
<body class="gray-bg login-bg">
<div class="middle-box text-center loginscreen  animated fadeInDown">
    <div>
        <h3 class="login-logo">
            <img src="{__ADMIN_PATH}images/logo.png">
            <p>CRMEB管理系统</p>
        </h3>
        <form class="m-t" role="form" action="<?php echo url('verify'); ?>" method="post">
            <div class="form-group">
                <input type="text" class="form-control" id="account" name="account" placeholder="用户名" required="">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" id="pwd" name="pwd" placeholder="密码" required="">
            </div>
            <div class="form-group">
                <div class="input-group">
                    <input type="text" class="form-control" id="verify" name="verify" placeholder="验证码" required="">
                    <span class="input-group-btn" style="padding: 0;margin: 0;">
                        <img id="verify_img" src="<?php echo Url('captcha'); ?>" alt="验证码" style="padding: 0;height: 34px;margin: 0;">
                    </span>
                </div>
            </div>
            <div class="form-group">
                <strong>
                    <p class="text-danger" id="err" style="display: none;"></p>
                </strong>
            </div>
            <button type="submit" class="btn btn-primary block full-width m-b">登 录</button>
            <?php /*  <p class="text-muted text-center"> <a href="<?php echo url('./forgetpwd'); ?>"><small>忘记密码了？</small></a> | <a href="<?php echo url('./register'); ?>">注册一个新账号</a>
              </p>  */ ?>
        </form>
    </div>
</div>
<div class="footer" style="    position: fixed;bottom: 0;width: 100%;left: 0;margin: 0;">
    <div class="pull-right">© 2000-2019 <a href="http://www.crmeb.com/" target="_blank">西安众邦科技</a>
    </div>
</div>
<!-- 全局js -->
<script src="{__PLUG_PATH}jquery-1.10.2.min.js"></script>
<script src="{__FRAME_PATH}js/bootstrap.min.js?v=3.4.0"></script>
<script src="{__MODULE_PATH}login/index.js"></script>
<!--统计代码，可删除-->
<!--点击刷新验证码-->
<script>
    (function captcha(){
        var $captcha = $('#verify_img'),src = $captcha[0].src;
        $captcha.on('click',function(){
            this.src = src+'?'+Date.parse(new Date());
        });
    })();
</script>
</body>
</html>