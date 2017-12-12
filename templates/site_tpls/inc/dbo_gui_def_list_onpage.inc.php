<br />
<table width = "100%" cellspacing = "0" cellpadding = "9" border = "0" bgcolor = "#FFE7FF" >
    <tbody >
    <tr class = '<?php $this->hID("mr") ?>' >
        <td valign = "top" >

            <table width = "900" cellspacing = "0" cellpadding = "0" border = "0" >
                <tbody >
                <tr >
                    <td ><p >
                            <font size = "4" face = "Arial, Helvetica, sans-serif" ><strong ><?php echo isset($this->copts['adminOneItemTitle']) ? $this->copts['adminOneItemTitle'] : $this->oname; ?></strong ></font >
                        </p ></td >
                </tr >
                </tbody >
            </table >

            <table width = "100%" cellspacing = "0" cellpadding = "1" bordercolor = "#666666" border = "1" bgcolor = "#FFFFFF" >
                <tbody >
                <tr >
                    <?php foreach ($this->copts['adminListFlds'] as $fn) { ?>
                        <th width = "25%" >
                            <font size = "2" face = "Arial, Helvetica, sans-serif" color = "#FF0000" ><?php echo isset($this->fctrls[$fn]['t']) ? $this->fctrls[$fn]['t'] : $fn ?></font >
                        </th >
                    <?php } ?>
                    <td width = "17%" >&nbsp;</td >
                </tr >

                <tr class = "dbo_tr dbo_<?php echo $this->oname; ?>_tr dbo_<?php echo $rr[$i % 2]; ?> dbo_<?php echo $this->oname; ?>_list" id = "<?php $this->hID("list") ?>" >
                    <?php
                    $cn = count($this->copts['adminListFlds']) + 1;
                    //var_dump($row);die("OK");
                    if ($this->copts['ajax'] == 0){
                    foreach ($this->copts['adminListFlds'] as $fn) {
                        ?>
                        <td class = "dbo_td dbo_<?php echo $this->oname ?>_td" >
                            <?php
                            if (in_array($this->fctrls[$fn]['c'], array("", "text", "select", "textarea", "htmltextarea"))) {
                                foreach ($this->langs as $rl)
                                    echo (isset($this->rels[$fn]) ? $row[$rl]["r_" . $fn] : $row[$rl][$fn]) . "</br>";
                            } else
                                echo (isset($this->rels[$fn]) ? $row[$this->def_lang]["r_" . $fn] : $row[$this->def_lang][$fn]) . "</br>";
                            ?>

                        </td >
                    <?php } ?>

                    <td class = 'ctrl_td' >
                        <span ></span >
                        <font size = "2" face = "Arial, Helvetica, sans-serif" >

                            <input type = "button" value = "Edit" onclick = "$('#<?php $this->hID("list") ?>').hide();$('#<?php $this->hID("list") ?> .ctrl_td > span').html('<?php echo addslashes($this->act_wrap("u", false, false, false, array('echo' => false))); ?>');$('#<?php $this->hID("edit") ?>').show();" >
                            <input type = 'button' value = 'Delete' onclick = "$('.<?php $this->hID("mr") ?>').hide();$('#<?php $this->hID("dr") ?> span').html('<?php echo addslashes($this->act_wrap("d", "Delete", false, false, array('echo' => false))); ?>');$('#<?php $this->hID("dr") ?>').show();" >
                        </font >
                    </td >
                </tr >

                <tr class = "dbo_<?php echo $rr[$i % 2]; ?> dbo_e_tr dbo_e_<?php echo $this->oname; ?>_tr" id = "<?php $this->hID("edit") ?>" style = "display:none;" >

                    <?php }

                    include($this->p->TEMPL . "/inc/dbo_" . (isset($this->copts['editInc']) ? $this->copts['editInc'] : "gui_def_edit") . "_onpage.inc.php"); ?>
                </tr >
                </tbody >
            </table >

        </td >
    </tr >
    <?php /*?>
<tr class='<?php $this->hID("mr")?>'><td>
There slaves of this object
</tr>
<?php */ ?>

    <tr class = 'dbo_dr_<?php echo $this->oname; ?>_tr' id = '<?php $this->hID("dr") ?>' style = 'display:none;' >
        <td colspan = "<?php echo $cn + 1 ?>" align = 'center' >
            <a href = 'javascript:' style = 'color:green'
               onclick = "$('#<?php $this->hID("dr") ?> span').html('');$('#<?php $this->hID("dr") ?>').hide();$('.<?php $this->hID("mr") ?>').show();" >Restore</a >
            <span ></span >
        </td >
    </tr >
</table >
