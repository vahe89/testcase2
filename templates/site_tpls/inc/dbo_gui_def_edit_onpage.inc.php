<?php foreach ($this->fctrls as $fn => $fc) {
    if ($fc['c'] == 'gui_slaves')
        continue;
    ?>

    <td class = "dbo_e_td dbo_e_<?php echo $this->oname; ?>_td" >
        <?php foreach ($this->langs as $l) {

            if (($lf = in_array($fc['c'], array("", "text", "textarea", "htmltextarea"))) || $l == $this->def_lang) {
                echo "<div>" . ($lf ? "<span class='onpage_lang'>{$l}</span>" : "");
                echo $this->dC($fn, $l);
                echo "</div>";
            }
        } ?>
    </td >

<?php } ?>

<td class = "dbo_e_td dbo_e_edit_ctrl dbo_e_<?php echo $this->oname ?>_td" >
    <?php $this->dH(); ?>
    <?php // $this->act_wrap("u",array("u"=>"Save","i"=>"Add"));
    if ($this->copts['ajax'] == 0) {
        ?>
        <input type = "button" value = "Cancel" onclick = "$('#<?php $this->hID("list") ?> .ctrl_td > span').html('');$('#<?php $this->hID("edit") ?>').hide();$('#<?php $this->hID("list") ?>').show();" >
    <?php } else {
        ?>
        <input type = 'button' value = 'Delete' onclick = "$('.<?php $this->hID("mr") ?>').hide();$('#<?php $this->hID("dr") ?> span').html('<?php echo addslashes($this->act_wrap("d", "Delete", false, false, array('echo' => false))); ?>');$('#<?php $this->hID("dr") ?>').show();" >
    <?php } ?>
</td >

