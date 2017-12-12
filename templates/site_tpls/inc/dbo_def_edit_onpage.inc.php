<form action = "index.php" method = "POST" enctype = "multipart/form-data" >
    <input type = "hidden" name = "a" value = "p_adb" >
    <!-- <input type="hidden" name="data[ret.w.id]" value="<?php echo $row['id']; ?>"> -->
    <input type = "hidden" name = "redirect" value = "a=dbo_<?php echo $this->oname; ?>&s=<?php echo $_REQUEST['s']; ?>" >
    <?php
    foreach ($this->langs as $l)
        $this->drawHidden($l);
    ?>
    <td class = "dbo_e_td dbo_e_edit_ctrl dbo_e_<?php echo $this->oname ?>_td" >
        <?php $this->act_wrap("u", array("u" => "Save", "i" => "Add")); ?>
        <input type = "button" value = "Cancel" onclick = "$('.dbo_e_tr').hide();$('#dbo_<?php echo $this->oname; ?>_list_<?php echo $row['id']; ?>').show();" >
    </td >

    <?php foreach ($this->fctrls as $fn => $fc) { ?>
        <td class = "dbo_e_td dbo_e_<?php echo $this->oname; ?>_td" >
            <?php foreach ($this->langs as $l) { ?>
                <?php // echo $this->dT($fn);</br>?>

                <?php if (($lf = in_array($fc['c'], array("", "text", "textarea", "htmltextarea"))) || $l == $this->def_lang) {
                    echo "<div>" . ($lf ? "<span class='onpage_lang'>{$l}</span>" : "");
                    echo $this->dC($fn, $l);
                    echo "</div>";
                }
            } ?>
        </td >
        <?php
    }
    ?>
</form >

