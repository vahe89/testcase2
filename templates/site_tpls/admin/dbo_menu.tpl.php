<div id = "menu_add" style = "display:none;margin: 0px auto;width:500px;" >
    <input type = "button" value = "Back" onclick = "$('#menu_add').hide();$('#menu_table').show();" ><br />
    <?php include($this->p->TEMPL . "/inc/dbo_{$this->copts['editInc']}_offpage.inc.php"); ?>

</div >

<div id = "menu_table" style = "margin: 0px auto;width:300px;" >
    <input type = "button" value = "Add menu item" onclick = "$('#menu_table').hide();$('#menu_add').show();" ><br />

    <form action = 'index.php' method = 'POST' enctype = 'multipart/form-data' style = "display:inline" >
        <input type = 'hidden' name = 'a' value = 'dbo_<?php echo $this->oname ?>' >
        Select menu item:<br />
        <select id = "cur_item" name = "rid" onchange = "$('#m_to_del,#m_to_del_en').val(this.value);" >
            <?php echo $this->sys_prios->selParent(); ?>
        </select ><br />
        <input type = "submit" value = "Edit" >
    </form >
    <form action = 'index.php' method = 'POST' enctype = 'multipart/form-data' style = "display:inline" onsubmit = "$('#m_to_del').val($('#cur_item').val());" >
        <input type = 'hidden' name = 'a' value = 'p_adb' >
        <input type = 'hidden' name = 'data[ret.w.id]' id = 'm_to_del' >
        <input type = 'hidden' name = 'data[o.w.lid]' id = 'm_to_del_en' >
        <input type = "submit" value = "Delete" name = "act_d_<?php echo $this->tbl ?>" onclick = "return confirm('Are you sure?');" >
        <input type = 'hidden' name = 'redirect_url' value = 'index.php?a=dbo_<?php echo $this->oname; ?>' >

    </form >
</div >
<?php /*?>
<script type="text/javascript">

$(document).ready(function(){
	$('#<?php echo $this->hID("sys_prios_parent")?>').bind("change",function(){
		$('.<?php echo $this->hID("m-hide")?>').hide().removeProp("selected");
//		$('#<?php echo $this->hID("sys_prios_prios")?>').val($('.<?php echo $this->hID("curr-'+$('#".$this->hID("sys_prios_parent",true,array('echo_INS'=>false))."').val()+'");?>').show().first().val());
		$('.<?php echo $this->hID("curr-'+$('#".$this->hID("sys_prios_parent",true,array('echo_INS'=>false))."').val()+'");?>').show().prop('selected',true);

	});
		$('.<?php echo $this->hID("m-hide")?>').hide().removeProp("selected");
		$('.<?php echo $this->hID("curr-'+$('#".$this->hID("sys_prios_parent",true,array('echo_INS'=>false))."').val()+'");?>').show().prop('selected',true);
		
	});

</script>
	<?php */ ?>

