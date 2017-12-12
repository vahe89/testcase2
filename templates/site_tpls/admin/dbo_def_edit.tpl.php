<div class = "dbo_offpage_edit dbo_offpage dbo_<?php echo $this->oname; ?>_offpage_edit" >
    <input type = "button" value = "Back" onclick = "location.href='index.php?a=dbo_<?php echo $this->oname; ?>'" ><br />

    <table class = 'dbo_edit_wrap' >
        <tr class = "dbo_edit_tr dbo_edit_<?php echo $this->oname; ?>_tr" >
            <td class = "dbo_edit_td dbo_edit_<?php echo $this->oname; ?>_td" >
                <?php include($this->p->TEMPL . "/inc/dbo_{$this->copts['editInc']}_{$this->copts["adminOnlineEdit"]}.inc.php"); ?>
            </td >
        </tr >
    </table >
</div >


