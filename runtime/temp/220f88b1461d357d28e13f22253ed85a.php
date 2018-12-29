<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:54:"F:\Mine\crmeb/application/admin\view\public\notice.php";i:1527060420;}*/ ?>
<script>
    var $eb = parent._mpApi,back = <?=$backUrl?>;
    $eb.notice('<?php echo $type; ?>',{
        title:'<?php echo $msg; ?>',
        desc:'<?php echo $info; ?>' || null,
        duration:<?=$duration?>
    });
    !!back ? (location.replace(back)) : history.go(-1);
</script>