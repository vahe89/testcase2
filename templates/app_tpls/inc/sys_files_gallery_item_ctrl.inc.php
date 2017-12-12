<?php
$en_links = false;
if ($this->types[$tname]['links'] != false) {
    $en_links = true;
    $cp = "{$css_prefix}_{$id}";
}
?>
<li <?php echo $this->types[$tname]['c'] == 'img' ? "class=\"selected\"" : "" ?>>
    <a class = "thumb" name = "<?php echo $id ?>" href = "<?php echo "{$fpath}?" . time(); ?>" title = "<?php echo $id ?>" >
        <img src = "<?php echo "{$tpath}?" . time(); ?>" alt = "<?php echo $id ?>" />
    </a >

    <div class = "caption" >
        <div id = "<?php echo $css_prefix ?>_title_div_<?php echo $ctid . "_" . $id ?>" style = "display:none;" >


            <?php
            $cp = "{$css_prefix}_{$id}";
            if ($en_links) {
                $l = $this->types[$tname]['links'];
                $ops = array("<option value=\"none\">None</option>");
                $sls = array();
                $hurl = false;
                $hlink = (isset($row['l_id']) && $row['l_id'] != false);

                foreach ($l as $v) {
                    if ($v != "url" && is_object($this->p->p->t[$v])) {
                        $ops[] = "<option " . ($hlink && $row['l_type'] == 1 && $row['l_rtbl'] == $v ? "selected=\"true\"" : "") . " value=\"{$v}\">{$v}</options>";
                        $sls[$v] = $this->p->p->t[$v]->sel(($hlink && $row['l_type'] == 1 && $row['l_rtbl'] == $v ? $row['l_rid'] : false), array('echo' => false, 'echo_INS' => false));
                    } else
                        $hurl = true;
                }
                if ($hurl) {
                    $ops[] = "<option " . ($hlink && $row['l_type'] == 0 ? "selected=\"true\"" : "") . " value=\"url\">URL</options>";
                }

                ?>
                <form action = "index.php" method = "POST" id = "<?php echo $css_prefix ?>_links_form_<?php echo $ctid . "_" . $id ?>"
                      onsubmit = "return false;" >

                    <select id = "<?php echo $cp ?>_ltype" name = "data[r.rtbl]"
                            onchange = "var cc=$('.<?php echo $cp ?>_opt').hide();if(this.value!='url'){cc.attr('disabled',true);$('#<?php echo $cp ?>_type').val(1);}else{$('#<?php echo $cp ?>_type').val(0);}$('#<?php echo $cp; ?>_'+this.value).removeAttr('disabled').show();" ><?php echo implode("", $ops) ?></select >

                    <?php
                    //	$class="";
                    //	$dis="";
                    //	if($hlink){
                    $class = "hidden";
                    $dis = "disabled=\"true\"";
                    //	}
                    $dtype = "0";
                    if (count($l) > 0) {
                        $dtype = "1";
                        foreach ($l as $v) {
                            if ($v == "url") continue;
                            if ($hlink && $row['l_type'] == 1 && $row['l_rtbl'] == $v) {
                                $class = "";
                                $dis = "";
                            }
                            ?>
                            <select id = "<?php echo "{$cp}_{$v}" ?>" <?php echo $dis ?> class = "<?php echo $class ?> <?php echo $cp ?>_opt" name = "data[r.rid]" ><?php echo $sls[$v]; ?></select >
                            <?php
                            $class = "hidden";
                            $dis = "disabled=\"true\"";
                        }
                    }
                    if ($hurl) {
                        if ($hlink && $row['l_type'] == 0) {
                            $class = "";
                            $dis = "";
                        }
                        ?>
                        <input id = "<?php echo $cp ?>_url" <?php echo $dis ?> class = "<?php echo $class ?> <?php echo $cp ?>_opt" type = "text" name = "data[c.url]" value = "<?php echo $hlink && $row['l_type'] == 0 ? $row['l_url'] : ""; ?>" />
                        <input id = "<?php echo $cp ?>_type" type = "hidden" name = "data[r.type]" value = "<?php echo $hlink ? $row['l_type'] : $dtype; ?>" >

                    <?php }
                    $ndis = "";
                    $udis = "disabled=\"true\"";
                    $dudis = "disabled=\"true\"";
                    $ddis = "disabled=\"true\"";
                    if ($hlink) {
                        $ndis = $udis;
                        $udis = "";
                        $dudis = "";
                    }
                    ?>

                    <input <?php echo $ndis ?> class = "<?php echo $cp ?>_ins <?php echo $cp ?>_c_dis" type = "hidden" name = "data[r.tbl]" value = "sys_files_own_links" >
                    <input <?php echo $ndis ?> class = "<?php echo $cp ?>_ins <?php echo $cp ?>_c_dis" type = "hidden" name = "data[r.tid]" value = "<?php echo $id; ?>" >
                    <input <?php echo $ndis ?> class = "<?php echo $cp ?>_ins <?php echo $cp ?>_c_dis" type = "hidden" name = "act_i_sys_links" value = "1" >

                    <input <?php echo $dudis ?> class = "<?php echo $cp ?>_upd <?php echo $cp ?>_del <?php echo $cp ?>_c_dis" type = "hidden" name = "data[w.tbl]" value = "sys_files_own_links" >
                    <input <?php echo $dudis ?> class = "<?php echo $cp ?>_upd <?php echo $cp ?>_del <?php echo $cp ?>_c_dis" type = "hidden" name = "data[ret.w.tid]" value = "<?php echo $id; ?>" >
                    <input <?php echo $udis ?> class = "<?php echo $cp ?>_upd <?php echo $cp ?>_c_dis" type = "hidden" name = "act_u_sys_links" value = "1" >

                    <input <?php echo $ddis ?> class = "<?php echo $cp ?>_del <?php echo $cp ?>_c_dis" type = "hidden" name = "act_d_sys_links" value = "1" >
                    <input type = "hidden" name = "a" value = "p_adb" >
                    <input type = "hidden" name = "ajax" value = "1" >

                </form >
            <?php } ?>

            <form action = "index.php" method = "POST" id = "<?php echo $css_prefix ?>_title_form_<?php echo $ctid . "_" . $id ?>"
                  onsubmit = "<?php if ($en_links) {
                      echo "if($('#{$cp}_ltype').val()=='none'){\$('.{$cp}_c_dis').attr('disabled',true);$('.{$cp}_del').removeAttr('disabled');}
$.post('index.php',$('#{$css_prefix}_links_form_{$ctid}_{$id}').serialize(),
function(d,m,x){
	$('.{$cp}_c_dis').attr('disabled',true);if($('#{$cp}_ltype').val()=='none'){\$('.{$cp}_ins').removeAttr('disabled');}else{\$('.{$cp}_upd').removeAttr('disabled');}});";
                  } ?>
                      tinyMCE.triggerSave();
                      $.post('index.php',$('#<?php echo $css_prefix ?>_title_form_<?php echo $ctid . "_" . $id ?>').serialize(),
                      function(d,m,x){
                      $('#<?php echo $css_prefix ?>_title_div_<?php echo $ctid . "_" . $id ?>').dialog('close');});return false;" >
                <input type = "hidden" name = "a" value = "p_adb" >
                <input type = "hidden" name = "ajax" value = "1" >
                <input type = "hidden" name = "multi_row[<?php echo $this->p->def_lang; ?>][data][ret.w.id]" value = "<?php echo $id; ?>" >
                <input type = "hidden" name = "multi_row_share[act_u_sys_files]" value = "1" >

                <?php foreach ($this->p->langs as $ll) { ?>
                    <input class = "<?php echo "{$cp}"; ?>_l_btn lang_btn <?php echo($this->p->def_lang == $ll ? "cl" : "") ?>" type = "button" value = "<?php echo $ll ?>" onclick = "lang_btn_cur(this);$('.<?php echo $cp; ?>_dsc_hide').hide();$('#<?php echo "{$cp}_{$ll}"; ?>_dsc').show();" >
                <?php }
                $hidclass = "hidden";

                foreach ($this->p->langs as $ll) {
                    ?>
                    <?php if ($ll != $this->p->def_lang) { ?>
                        <input type = "hidden" name = "multi_row[<?php echo $ll; ?>][data][o.w.lid]" value = "<?php echo $id; ?>"><?php } else {
                        $hidclass = "";
                    } ?>
                    <div id = "<?php echo "{$cp}_{$ll}"; ?>_dsc" class = "<?php echo "$hidclass $cp"; ?>_dsc_hide" >
                        <textarea id = "<?php echo $css_prefix ?>_textarea_<?php echo $ctid . "_" . $id ?>" class = "mce_editor" cols = "30" rows = "10" name = "multi_row[<?php echo $ll ?>][data][c.title]" ><?php echo isset($this->cimgsl["gallery_{$tname}"][$ll][$id]['title']) ? $this->cimgsl["gallery_{$tname}"][$ll][$id]['title'] : ""; ?></textarea >
                    </div >
                    <?php
                    $hidclass = "hidden";
                } ?>

                <input type = "submit" value = "Save" >
            </form >

        </div >
        <input type = "button" value = "Image description <?php echo $en_links != false ? "& link" : ""; ?>" onclick = "$('#<?php echo $css_prefix ?>_title_div_<?php echo $ctid . "_" . $id ?>').dialog({modal:true,width:440,create:function(){tinyMCE.execCommand('mceAddControl',true,'<?php echo $css_prefix ?>_textarea_<?php echo $ctid . "_" . $id ?>');}/*,beforeClose:function(){tinyMCE.get('<?php echo $css_prefix ?>_textarea_<?php echo $ctid . "_" . $id ?>').remove();}*/ });" >
        |
        <input type = "button" value = "Delete image" onclick = "
            $.ajax({
            url: 'index.php?a=dbo_<?php echo $this->oname; ?>',
            data:'f=sys_files_d&rid=<?php echo $ctid ?>&ajax=1&sys_files[<?php echo "{$this->types[$tname]['c']}_{$tname}" ?>][id]=<?php echo $id; ?>',
            success: function(d,m,x){
            d=$.parseJSON(d);
            if(d.ret=='OK'){

        <?php echo $css_prefix ?>_img_counter--;
            if(<?php echo $css_prefix ?>_img_counter<=0){
            sys_files_<?php echo $css_prefix ?>_lastimg();
        <?php echo $css_prefix ?>_img_counter=0;
            }
        <?php echo $css_prefix ?>gallery.removeImageByHash('<?php echo $id; ?>')
        <?php echo $css_prefix ?>gallery.previous(false,true);


            }
            }
            });
            " > |
        <input type = "button" value = "Trim" onclick = "trim_plugin('<?php echo $trimpath; ?>',<?php echo $this->trimFuncParams($tname, "#{$css_prefix}slideshow img, .sys_files_img_{$tname}_{$ctid}", "#{$css_prefix}thumbs .selected img, .sys_files_img_{$tname}_{$ctid}_thumb") ?>)" >
    </div >
</li >

