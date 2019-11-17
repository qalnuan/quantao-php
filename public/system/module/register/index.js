(function(global,$){
    var $err = $('#err');
    $err.hide();
    var showError = function(err){
        $err.html(err).show();
        return false;
    };

    $('form').on('submit',function(){
        var account = $('#account'),
          pwd = $('#pwd'),
          repwd = $('#repwd'),
          real_name = $('#real_name');
          var reg = /^1[3456789]\d{9}$/;
        if (!account) return showError('请输入手机号!');
        if (!reg.test(account[0].value)) return showError('请输入正确的手机号!')
        if (!real_name) return showError('请输入商户名!');
        if(!pwd) return showError('请输入密码');
        if(pwd[0].value !== repwd[0].value) return showError("密码与确认密码不一致");
    });
})(window,jQuery);
$(document).ready(function() {
    $('.login-bg').iosParallax({
        movementFactor: 50
    });
});

(function captcha(){
    var $captcha = $('#verify_img'),src = $captcha[0].src;
    $captcha.on('click',function(){
        this.src = src+'?'+Date.parse(new Date());
    });
})();