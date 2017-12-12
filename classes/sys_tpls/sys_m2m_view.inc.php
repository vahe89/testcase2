<div class='m2m_view'>
<div class="m2m_cblock">
<?php
$do=$this->p->p->t[$this->objs[$obj]['obj']];
$mfn=preg_replace("#^.*\.#","",$do->gO("sel_titleFld"));
//echo $do->fctrls[$mfn]['t']."<br>";
echo "* ".$data[$mfn];
?>
</div>
<?php 
	if($use_meta && is_array($this->objs[$obj]['show_meta_flds'])){ 
		foreach($this->objs[$obj]['show_meta_flds'] as $fn){
			echo '<div class="m2m_cblock">'.$meta_o->fctrls[$fn]['t']."<br>";
			echo $meta[$fn];
			echo "</div>";
		}
	}
?>

</div>

