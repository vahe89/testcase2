<div id='<?php echo $div_wrapID;?>' class='m2m_ctrls'>

<div class="m2m_cblock">
<a href='javascript:' style='color:red' onclick="$('#<?php echo $div_wrapID;?>').hide();$('#<?php echo $div_RESwrapID;?> span').html('<input type=\'hidden\' name=\'<?php echo $this->p->in_as($del_name);?>\' value=\'<?php echo $rid;?>\'/>');$('#<?php echo $div_RESwrapID;?>').show();"> X </a>
<?php	if($use_meta && is_array($this->objs[$obj]['show_meta_flds']) && count($this->objs[$obj]['show_meta_flds'])>0) 
	echo "<br>";
?>
<select name='<?php echo $this->p->in_as($sel_name);?>' class='<?php echo "$classes";?>' id='<?php $sel_ID?>'>
<?php echo $this->p->p->t[$this->objs[$obj]['obj']]->sel($cur_sel,$sel_opts); ?>
</select>
</div>
<?php 
	if($use_meta && is_array($this->objs[$obj]['show_meta_flds'])){ 
		foreach($this->objs[$obj]['show_meta_flds'] as $fn){
			echo '<div class="m2m_cblock">'.$meta_o->fctrls[$fn]['t']."<br>";
			$moa=array('_dC_cname'=>$this->p->in_as($meta_name."[$fn]"));
			if(isset($meta_data[$rid][$fn]))
				$moa['_dC_cval']=$meta_data[$rid][$fn];

			$meta_o->dC($fn,false,$moa);
			echo "</div>";
		}
	}
?>

</div>

<div id='<?php echo $div_RESwrapID;?>' style='display:none'><a href='javascript:' style='color:green' onclick="$('#<?php echo $div_RESwrapID;?> span').html('');$('#<?php echo $div_RESwrapID;?>').hide();$('#<?php echo $div_wrapID;?>').show();\">Restore</a><span></span></div>

