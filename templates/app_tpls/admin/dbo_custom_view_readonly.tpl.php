<?php
if (!is_array($this->cD) || count($this->cD) == 0) {
    die("<script type='text/javascript'>location.replace('" . aurl("/{$this->obj_slug}") . "');</script>");
}
$this->draw_tabs();

$cV = $this->gOA("cV");
$ifc = $this->fctrls;

?>

<div class = "dbo_offpage_edit dbo_tpl_readonly dbo_offpage dbo_<?php echo $this->oname; ?>_offpage_edit">

    <?php /*?> <input type="button" value="Back" onclick="location.href='index.php?a=dbo_<?php echo $this->oname;?>&s=def_offpage'"><br/>

<form id="idform" action='<?php echo aurl("/");?>' method='POST' enctype='multipart/form-data'>
<?php */ ?>

		<table class = 'dbo_edit_wrap' >
        <tr class = "dbo_offpage_tr dbo_e_ctrl_tr dbo_<?php echo $this->oname; ?>_tr " >

            <td align = 'center' colspan = 3 class = "dbo_offpage_td dbo_e_ctrl_td dbo_<?php echo $this->oname; ?>_td " >

                <input type = "button" value = "Back" onclick = "location.href='<?php echo aurl("/".$this->obj_slug);?>';"/>

<?php  if (isset($cV['acts']) && is_array($cV['acts']) && in_array("u", $cV['acts'])) {
	$eok=true;
	if (isset($cV['edit']['onVal']) && is_array($cV['edit']['onVal']) && count($cV['edit']['onVal'])>0){
		foreach($cV['edit']['onVal'] as $fn=>$fv){
			if($this->cD[$fn]!=$fv){
				$eok=false;
				break;
			}
		}
	}
	if($eok==true){
?>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										<a class = 'btn btn-primary' href = "<?php echo aurl("/{$this->obj_slug}/{$this->cD[$this->slug_field]}/e"); ?>" >Edit</a >
								<?php } }
								if(is_array($cV['view']['_linkBtns'])){
									echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
									echo $this->p->draw_detail_buttons($cV['view']['_linkBtns'],$this);
								}
?>

								<?php /*  <a class = 'btn' href = "<?php echo aurl("/{$this->obj_slug}"); ?>" >Back</a > */ ?>
						<br><br>
            </td >
        </tr >

<?php
//var_dump($this->cD);die;
?>


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


                <?php //====================================================================================================?>


                <input type = 'hidden' name = 'a' value = 'p_adb' >

                <input type = "hidden" name = "redirect" value = "<?php echo($this->gO('redirect') ? $this->gO('redirect') : aurl("/{$this->obj_slug}")); ?>" >

                <?php
                $this->doInc("dbo_custom_view_readonly");

                //====================================================================================================?>


            </td >
        </tr >
        <tr class = "dbo_offpage_tr dbo_e_ctrl_tr dbo_<?php echo $this->oname; ?>_tr " >

            <td align = 'center' colspan = 3 class = "dbo_offpage_td dbo_e_ctrl_td dbo_<?php echo $this->oname; ?>_td " >
                <hr class = "footerline" >

                <input type = "button" value = "Back" onclick = "location.href='<?php echo aurl("/".$this->obj_slug);?>';"/>

<?php if (isset($cV['acts']) && is_array($cV['acts']) && in_array("u", $cV['acts'])) { 
	$eok=true;
	if (isset($cV['edit']['onVal']) && is_array($cV['edit']['onVal']) && count($cV['edit']['onVal'])>0){
		foreach($cV['edit']['onVal'] as $fn=>$fv){
			if($this->cD[$fn]!=$fv){
				$eok=false;
				break;
			}
		}
	}
	if($eok==true){
?>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										<a class = 'btn btn-primary' href = "<?php echo aurl("/{$this->obj_slug}/{$this->cD[$this->slug_field]}/e"); ?>" >Edit</a >
								<?php } } ?>

                <?php /*  <a class = 'btn' href = "<?php echo aurl("/{$this->obj_slug}"); ?>" >Back</a > */ ?>
            </td >
        </tr >

    </table >

    <?php /* ?>
</form>
<?php */ ?>

</div >


