<div id = "dbo_<?php echo $this->oname; ?>_add" class = "dbo_offpage_add dbo_<?php echo $this->oname; ?>_offpage_add" style = "display:none" >
    <input type = "button" value = "Back" onclick = "$('#dbo_<?php echo $this->oname; ?>_add').hide();$('#dbo_<?php echo $this->oname; ?>_table').show();" ><br />
    <table class = 'dbo_edit_wrap dbo_def_offpage dbo_def_offpage_add dbo_def_offpage_<?php echo $this->oname ?>' >
        <tr class = "dbo_add_tr dbo_add_<?php echo $this->oname; ?>_tr" >
            <td >
                <?php include($this->p->TEMPL . "/inc/dbo_{$this->copts['editInc']}_offpage.inc.php"); ?>
            </td >
        </tr >
    </table >
</div >

<div id = "dbo_<?php echo $this->oname; ?>_table" class = "dbo_table_wrap dbo_<?php echo $this->oname; ?>_table_wrap" >

    <input type = "button" value = "Add <?php echo $this->copts['adminOneItemTitle']; ?>" onclick = "$('#dbo_<?php echo $this->oname; ?>_table').hide();$('#dbo_<?php echo $this->oname; ?>_add').show();" >

    <?php if ($this->tbl == "companies") { ?>
        <form action = "index.php" method = "POST" >
            <input type = 'hidden' name = 'a' value = 'p_filter' >
            Filter on Company ID <input name = 'id_filt' value = '<?php echo $_REQUEST['id_filt'] ?>' >
            <input type = 'Submit' >
        </form >
    <?php } ?>

    <table class = 'dbo_list dbo_list_offpage dbo_def_offpage dbo_def_offpage_edit dbo_def_offpage_<?php echo $this->oname ?>' >
        <?php
        $hh = $this->gO("listHeader");
        if (is_array($hh)) {
            echo "<thead><tr class='dbo_header dbo_{$this->oname}'>";
            foreach ($hh as $v)
                echo "<td><span>$v</span></td>";
            echo "</tr></thead>";
        } else if (!$this->gO('noHeader')) {
            echo "<thead><tr class='dbo_header dbo_{$this->oname}'>";
            echo "<th><span>Controls</span></th>";
            foreach ($this->copts['adminListFlds'] as $fn)
                echo "<th><span>" . (isset($this->fctrls[$fn]['t']) ? $this->fctrls[$fn]['t'] : $fn) . "</span></th>";

            echo "</tr></thead>";
        }
        echo "<tbody>";

        echo $this->listDef("{$this->copts['listInc']}");

        echo "</tbody>";

        $hh = $this->gO("listHeader");
        if (is_array($hh)) {
            echo "<tfoot><tr class='dbo_header dbo_{$this->oname}'>";
            foreach ($hh as $v)
                echo "<td><span>$v</span></td>";
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
    <input type = "button" value = "Add <?php echo $this->copts['adminOneItemTitle']; ?>" onclick = "$('#dbo_<?php echo $this->oname; ?>_table').hide();$('#dbo_<?php echo $this->oname; ?>_add').show();" >
</div >
