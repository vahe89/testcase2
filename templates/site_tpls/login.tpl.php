<html >
<head >
    <meta http-equiv = "X-UA-Compatible" content = "IE=8" >
    <meta http-equiv = "Content-Type" content = "text/html; charset=UTF-8" >

    <title ><?php echo $this->webtitle[$this->def_lang]; ?> - Login</title >

    <base href = "<?php echo aurl("/"); ?>" >
    <link href = "<?php echo $this->skin; ?>css/wHumanMsg.min.css" rel = "stylesheet" type = "text/css" >

    <link href = "<?php echo $this->skin; ?>css/ui-lightness/ui.css" rel = "stylesheet" type = "text/css" >
    <link href = "<?php echo $this->skin; ?>css/admin.app.css" rel = "stylesheet" type = "text/css" >
    <link href = "<?php echo $this->skin; ?>css/login.css" rel = "stylesheet" type = "text/css" >

    <script src = "js/jquery.min.js" type = "text/javascript" ></script >


    <script type = "text/javascript" >
        $(document).ready(function () {
            var dateVar = new Date();
            var ttz = dateVar.getTimezoneOffset() / 60 * (-1);
            jQuery('#fld_TZ').val((ttz >= 0 ? "GMT+" + ttz : "GMT" + ttz));
        });

    </script >

</head >

<body id = 'login_page' >
<!-- <img id='login_logo' src='./css/img/FastUnsecured-logo.png'> -->
<form id = 'login_form' method = "POST" action = "<?php echo aurl('/'); ?>" >
    <input type = 'hidden' name = 'a' value = 'p_login' >
    <input type = 'hidden' name = 'TZ' id = 'fld_TZ' >
    Username:<br >
    <input type = "text" name = "login" ><br >
    Password:<br >
    <input type = "password" name = "pass" ><br >
    <?php /* ?>
<p> <a href='./password-reset'><i class="icon-wrench"></i> Forgot Your Password</a></p>
<?php */ ?>

    <input type = "submit" value = 'Login' >

</form >

<?php // include_once("tos.tpl.php"); ?>

<script src = "./js/wHumanMsg.min.js" type = "text/javascript" ></script >
<script type = 'text/javascript' >
    jQuery(document).ready(function () {
//$("body").wHumanMsg('testt??');
        <?php echo $this->showMsg();?>
    });

</script >


</body >
</html >
