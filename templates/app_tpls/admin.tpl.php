<?php if ($this->v != false) { ?>
    <h2 >Home page</h2 >

    <br >

    <div style = "width:400px;margin:0px auto;" >
        <?php if (isset($this->superAdmin) && $this->superAdmin == true) { ?>
            <form action = 'index.php' method = 'POST' >
                Admin email:
                <input name = 'data[r.val]' value = "<?php echo htmlentities($this->getConfig("admin_email")); ?>" >
                <input type = 'hidden' name = 'data[w.name]' value = 'admin_email' >
                <input type = 'hidden' name = 'a' value = 'p_adb' >
                <input type = 'hidden' name = 'redirect_url' value = 'index.php' >
                <input type = 'submit' name = "act_u_sys_config" value = 'Save' >
            </form >
            <br >
            <form action = 'index.php' method = 'POST' >
                Admin email from:
                <input name = 'data[r.val]' value = "<?php echo htmlentities($this->getConfig("admin_email_from")); ?>" >
                <input type = 'hidden' name = 'data[w.name]' value = 'admin_email_from' >
                <input type = 'hidden' name = 'a' value = 'p_adb' >
                <input type = 'hidden' name = 'redirect_url' value = 'index.php' >
                <input type = 'submit' name = "act_u_sys_config" value = 'Save' >
            </form >
            <br >

            <form action = "index.php" method = "POST" style = "float:left;margin-left:20px;" onsubmit = "if($('#p1').val()==$('#p2').val()){return true}else{alert('Password and confirmation not equal!');return false;};" >
                Change admin password:<br />
                <input type = "hidden" name = "a" value = "p_apass" >
                Current password:<br />
                <input type = "password" name = "old" ><br />
                New password:<br />
                <input type = "password" id = "p1" name = "new" ><br />
                Confirm new password:<br />
                <input type = "password" id = "p2" name = "new2" ><br />
                <input type = "submit" value = "Change admin password" >
            </form >
        <?php } else {
            echo "<h1>Use menu to navigate.</h1>";
        } ?>
    </div >
<?php } else {
    echo "<center><h1>Select from menu above</h1></center>";
} ?>
