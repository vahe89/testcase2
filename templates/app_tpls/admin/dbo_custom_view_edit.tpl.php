<script type = 'text/javascript' src = '<?php echo aurl("/js/jquery.validate.min.js") ?>' ></script>
<script type = 'text/javascript' src = '<?php echo aurl("/js/additional-methods.min.js") ?>' ></script>
<?php
$this->draw_tabs();

$backslug=($this->cD[$this->slug_field]=="new"?"":$this->cD[$this->slug_field]);

$cV = $this->gOA("cV");

if($cV==false){
echo "<script type='text/javascript'>location.href='" . aurl("/") . "'</script>";
    die();
}
$ifc = $this->fctrls;

$logicDeps = array();
$forVld = array();
?>

<div class = "dbo_offpage_edit dbo_tpl_edit dbo_offpage dbo_<?php echo $this->oname; ?>_offpage_edit">

    <?php /*?> <input type="button" value="Back" onclick="location.href='index.php?a=dbo_<?php echo $this->oname;?>&s=def_offpage'"><br/>
<?php */ 
?>

    <form id = "idform" action = '<?php echo aurl("/"); ?>' method = 'POST' enctype = 'multipart/form-data' >
        <input type = 'hidden' name = 'a' value = 'p_adb' >
<?php 
		$ttAct = "i";
//		if (isset($_REQUEST['rid']) && $_REQUEST['rid'] != false)
		if(is_array($this->cD))
	    $ttAct = "u";


		$redir=aurl("/{$this->obj_slug}");
		if (isset($cV['edit']['onActRedir']) && isset($cV['edit']['onActRedir'][$ttAct]) && $cV['edit']['onActRedir'][$ttAct]!=false){
			$redir=$cV['edit']['onActRedir'][$ttAct];
			if($ttAct!='i'){
				$redir=str_replace("{id}",$this->cD['id'],$redir);
				$redir=str_replace("{slug}",$this->cD[$this->slug_field],$redir);
			}
		}
		else if($this->gO('redirect')!=false)
			$redir=$this->gO('redirect');

		if(isset($_REQUEST['rr_url']) && $_REQUEST['rr_url']!=false)
			$redir=$_REQUEST['rr_url'];
?>
        <input type = "hidden" name = "redirect_url" value = "<?php echo $redir?>" >

        <?php
        if (isset($_SESSION['cur_nonce']) && $_SESSION['cur_nonce'] != false)
            echo "<input type='hidden' name='form_nonce' value='{$_SESSION['cur_nonce']}'>";
        ?>

<table class = 'dbo_edit_wrap' >
        <tr class = "dbo_offpage_tr dbo_e_ctrl_tr dbo_<?php echo $this->oname; ?>_tr " >

            <td align = 'center' colspan = 3 class = "dbo_offpage_td dbo_e_ctrl_td dbo_<?php echo $this->oname; ?>_td " >
                <br>
								<input type = "button" value = "Back" onclick = "location.href='<?php echo aurl("/".$this->obj_slug."/$backslug");?>';"/>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <?php

                $this->act_wrap("u", array("u" => "Save", "i" => "Add")); ?>
                <?php /*  <a class = 'btn' href = "<?php echo aurl("/{$this->obj_slug}"); ?>" >Back</a > */ ?>
                <br>
                <br>
            </td >
        </tr >
	<?php //====================================================================== ?>	
            <?php $lastlang = end($this->langs);
            if (count($this->langs) > 1) { ?>
                <tr class = "dbo_lang_selector dbo_offpage_tr dbo_<?php echo $this->oname; ?>_tr" >
                    <td >
                        <?php foreach ($this->langs as $l) { ?>
                            <input type = "button" class = 'lang_btn <?php echo($this->def_lang == $l ? "cl" : "") ?>' value = "<?php echo $l; ?>" onclick = "$('.dbo_row_lang_box').hide();$('.dbo_row_lang_<?php echo $l; ?>').show();" >&nbsp;
                        <?php } ?>
                    </td >
                </tr >
            <?php } ?>


            <tr class = "dbo_edit_tr dbo_edit_<?php echo $this->oname; ?>_tr" >
                <td class = "dbo_edit_td dbo_edit_<?php echo $this->oname; ?>_td" >

                    <?php
                    if (isset($cV['edit']['_l']['_w']) && $cV['edit']['_l']['_w'] != false) {
                        $tbl_width = $cV['edit']['_l']['_w'];
                        unset($cV['edit']['_l']['_w']);
                    }

                    $tbl_wdth = "";
                    if ($tbl_width != false)
                        $tbl_wdth = "style='width:{$tbl_width}'";

                    echo "<table {$tbl_wdth} class='dbo_edit dbo_offpage_table dbo_{$this->oname}_offpage_table'>";

                    // ==================================================================

                    $this->doInc("dbo_custom_view_edit");

                    // ==================================================================

                    ?>
        </table > <!-- .dbo_edit -->

        </td>
        </tr>


        <?php //echo $this->dC("gui_slaves_FU_Analysis__c");?>

        <tr class = "dbo_offpage_tr dbo_e_ctrl_tr dbo_<?php echo $this->oname; ?>_tr " >

            <td align = 'center' colspan = 3 class = "dbo_offpage_td dbo_e_ctrl_td dbo_<?php echo $this->oname; ?>_td " >
                <hr class = "footerline" >
								<input type = "button" value = "Back" onclick = "location.href='<?php echo aurl("/".$this->obj_slug."/$backslug");?>';"/>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <?php

                $this->act_wrap("u", array("u" => "Save", "i" => "Add")); ?>
                <?php /*  <a class = 'btn' href = "<?php echo aurl("/{$this->obj_slug}"); ?>" >Back</a > */ ?>
            </td >
        </tr >

        </table>


    </form >

</div >



