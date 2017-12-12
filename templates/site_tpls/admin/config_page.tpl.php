<?php
if (!isset($redir_url) || $redir_url == false)
    $redir_url = "/";

if (!isset($form_a) || $form_a == false)
    $form_a = "p_adb";

?>

<div class = "top_tbl_pos_div" ></div >
<div >
    <?php
    if (isset($conf_menu) && is_array($conf_menu)) {
        ?>
        <ul class = "nav nav-tabs" >
            <?php
            foreach ($conf_menu as $ck => $cv) {
                ?>
                <li <?php echo($ck == $conf_page_id ? "class='active'" : ''); ?>>
                    <a href = '<?php echo url("/admin" . $cv['_url']); ?>' ><?php echo $cv['_html']; ?></a ></li >
            <?php } ?>
        </ul >
    <?php } ?>
</div >

<form action = 'index.php' method = 'POST' >
    <input class = "dbo_submit dbo_btn_save dbo_btn_manage" type = 'submit' name = "<?php echo $this->_secureFormNames('multi_row_share[act_u_sys_config]'); ?>" value = '<?php echo(isset($conf_save_label) && $conf_save_label != false ? $conf_save_label : "Save"); ?>' >
    <hr >

    <input type = 'hidden' name = '<?php echo $this->_secureFormNames('a'); ?>' value = '<?php echo $form_a; ?>' >
    <input type = 'hidden' name = '<?php echo $this->_secureFormNames('redirect_url'); ?>' value = '<?php echo url("/admin" . $redir_url); ?>' >
    <input type = 'hidden' name = "<?php echo $this->_secureFormNames('multi_row_share[isLang]'); ?>" value = '0' >

    <?php

    $cnt = 0;
    if (isset($conf_opts) && is_array($conf_opts)){
    foreach ($conf_opts as $oN => $oO){
    $cnt++;

    if ($oO['c'] != 'html') {
        $curN = $this->_secureFormNames("multi_row[o{$cnt}][data][r.val]");
        ?>
        <input type = 'hidden' name = '<?php echo $this->_secureFormNames("multi_row[o{$cnt}][data][w.name]"); ?>' value = '<?php echo $oN; ?>' >
        <?php
        if ($oO['c'] != 'checkbox')
            echo $oO['t'];
        ?>

        <?php
    }
    if (!isset($oO['c']) || $oO['c'] == 'text'){
        ?>
        <br ><input type = 'text' name = '<?php echo $curN; ?>' value = "<?php echo $this->getConfig($oN); ?>" ><br >
        <?php

    } else if ($oO['c'] == 'textarea'){ ?>
        <br ><textarea name = '<?php echo $curN; ?>' >
<?php echo htmlentities($this->getConfig($oN)); ?>
</textarea ><br >

    <?php } else if ($oO['c'] == 'htmltextarea'){ ?>
        <br ><textarea class = 'ckeditor mce_editor' name = '<?php echo $curN; ?>' >
<?php echo htmlentities($this->getConfig($oN)); ?>
</textarea ><br >

    <?php } else if ($oO['c'] == 'select'){
    $sV = $this->getConfig($oN);
    ?>
    <br ><select name = '<?php echo $curN; ?>' >
        <?php foreach ($oO['opts'] as $ssV => $ssT) {
            echo "<option " . ($ssV == $sV ? "selected='true'" : "") . " value='{$ssV}'>{$ssT}</option>";

        }
        echo "</select><br>";
        } else if ($oO['c'] == 'html') {
            echo $oO['html'];

        } else if ($oO['c'] == 'checkbox') { ?>
            <input type = 'hidden' name = '<?php echo $curN; ?>' value = "0" >
            <input id = "conf_page_ctl<?php echo $cnt ?>" <?php echo($this->getConfig($oN) != false ? 'checked="true"' : ''); ?> type = 'checkbox' name = '<?php echo $curN; ?>' value = "1" >
            <label for = "conf_page_ctl<?php echo $cnt ?>" ><?php echo $oO['t'] ?></label >
            <br >

            <?php
        }

        }

        ?>

        <hr class = "footerline" >
        <input class = "dbo_submit dbo_btn_save dbo_btn_manage" type = 'submit' name = "<?php echo $this->_secureFormNames('multi_row_share[act_u_sys_config]'); ?>" value = '<?php echo(isset($conf_save_label) && $conf_save_label != false ? $conf_save_label : "Save"); ?>' >
        <?php } ?>

</form >

