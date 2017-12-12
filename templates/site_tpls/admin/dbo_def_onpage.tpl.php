<?php /*?>
<div id="dbo_<?php echo $this->oname;?>_add" class="dbo_onpage_add dbo_<?php echo $this->oname;?>_onpage_add" style="display:none">
<input type="button" value="Back" onclick="$('#dbo_<?php echo $this->oname;?>_add').hide();$('#dbo_<?php echo $this->oname;?>_table').show();"><br/>
<table border=0 align="center" cellpadding=0 cellspacing=3 class='dbo_def_onpage dbo_def_onpage_add dbo_def_onpage_<?php echo $this->oname?>'>
<tr class="dbo_add_tr dbo_add_<?php echo $this->oname;?>_tr"  >
<?php include($this->p->TEMPL."/inc/dbo_{$this->copts['editInc']}_onpage.inc.php");?>
</tr>
</table>
</div>
<?php */ ?>

<div id = "dbo_<?php echo $this->oname; ?>_table" class = "dbo_table_wrap dbo_<?php echo $this->oname; ?>_table_wrap" >
    <input type = "button" value = "Add <?php echo $this->copts['adminOneItemTitle']; ?>" onclick = "/*$('#dbo_<?php echo $this->oname; ?>_table').hide();*/$('#dbo_<?php echo $this->oname; ?>_add').show();" >
    <table border = 1 align = "center" cellpadding = 3 cellspacing = 0 class = 'dbo_list dbo_list_onpage dbo_def_onpage dbo_def_onpage_edit dbo_def_onpage_<?php echo $this->oname ?>' >
        <?php
        $hh = $this->gO("listHeader");
        if (is_array($hh)) {
            echo "<thead><tr class='dbo_header dbo_{$this->oname}'>";
            echo "<th><span>Controls</span></th>";
            foreach ($hh as $v)
                echo "<th><span>$v</span></th>";
            echo "</tr></thead>";
        } else if (!$this->gO('noHeader')) {
            echo "<thead><tr class='dbo_header dbo_{$this->oname}'>";
            echo "<th><span>Controls</span></th>";
            foreach ($this->copts['adminListFlds'] as $fn)
                echo "<th><span>" . (isset($this->fctrls[$fn]['t']) ? $this->fctrls[$fn]['t'] : $fn) . "</span></th>";
            echo "</tr></thead>";
        } ?>
        <tbody >

        <tr name = "add_row" class = "hidden dbo_add_tr dbo_e_tr dbo_add_<?php echo $this->oname; ?>_tr" id = "dbo_<?php echo $this->oname; ?>_add" >
            <?php include($this->p->TEMPL . "/inc/dbo_{$this->copts['editInc']}_onpage.inc.php"); ?>
        </tr >

        <?php echo $this->listDef("{$this->copts['listInc']}");

        echo "</tbody>";

        $hh = $this->gO("listHeader");
        if (is_array($hh)) {
            echo "<tfoot><tr class='dbo_header dbo_{$this->oname}'>";
            echo "<th><span>Controls</span></th>";
            foreach ($hh as $v)
                echo "<th><span>$v</span></th>";
            echo "</tr></tfoot>";
        } else if (!$this->gO('noHeader')) {
            echo "<tfoot><tr class='dbo_header dbo_{$this->oname}'>";
            echo "<th><span>Controls</span></th>";
            foreach ($this->copts['adminListFlds'] as $fn)
                echo "<th><span>" . (isset($this->fctrls[$fn]['t']) ? $this->fctrls[$fn]['t'] : $fn) . "</span></th>";
            echo "</tr></tfoot>";
        }
        ?>
    </table >
    <input type = "button" value = "Add <?php echo $this->copts['adminOneItemTitle']; ?>" onclick = "/*$('#dbo_<?php echo $this->oname; ?>_table').hide();*/$('#dbo_<?php echo $this->oname; ?>_add').show();location.href='#add_row'" >
</div >
