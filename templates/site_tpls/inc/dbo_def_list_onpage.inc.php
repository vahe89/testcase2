<tr class = "dbo_tr dbo_list_tr dbo_<?php echo $this->oname; ?>_tr dbo_<?php echo $rr[$i % 2]; ?> dbo_<?php echo $this->oname; ?>_list" id = "dbo_<?php echo $this->oname ?>_list_<?php echo $row['id']; ?>" >
    <td class = "dbo_td dbo_ctrl dbo_edit_ctrl dbo_<?php echo $this->oname ?>_td" >
        <input type = "button" value = "Edit" onclick = "$('.dbo_list_tr').show();$('.dbo_e_tr,#dbo_<?php echo $this->oname ?>_list_<?php echo $row['id']; ?>').hide();$('#dbo_<?php echo $this->oname ?>_edit_<?php echo $row['id']; ?>').show();" >

        <form action = "index.php" method = "POST" >
            <input type = "hidden" name = "a" value = "p_adb" >
            <input type = "hidden" name = "redirect" value = "a=dbo_<?php echo $this->oname; ?>&s=<?php echo $this->copts['listTpl']; ?>" >
            <?php $this->act_wrap("d", "Delete"); ?>
        </form >
    </td >


    <?php
    //var_dump($row);die("OK");
    foreach ($this->copts['adminListFlds'] as $fn) {
        ?>
        <td class = "dbo_td dbo_<?php echo $this->oname ?>_td" >
            <?php
            $varr = $this->drawValue($fn, $row);
            foreach ($varr as $pv)
                echo "$pv<br>";

            /*	if(in_array($this->fctrls[$fn]['c'],array("","text","select","textarea","htmltextarea"))){
                foreach($this->langs as $rl)
                    echo (isset($this->rels[$fn])?$row[$rl]["r_".$fn]:$row[$rl][$fn])."</br>";
                }	else
                    echo (isset($this->rels[$fn])?$row[$this->def_lang]["r_".$fn]:$row[$this->def_lang][$fn])."</br>";
             */
            ?>
        </td >
    <?php } ?>

</tr >
<tr class = "dbo_<?php echo $rr[$i % 2]; ?> dbo_e_tr dbo_e_<?php echo $this->oname; ?>_tr" id = "dbo_<?php echo $this->oname; ?>_edit_<?php echo $row['id']; ?>" style = "display:none;" >
    <?php include($this->p->TEMPL . "/inc/dbo_{$this->copts['editInc']}_onpage.inc.php"); ?>
</tr >
