<?php
$cV=$this->gO('cV');
if(is_array($cV['tabs']) && count($cV['tabs'])>0){
	$tabs=$cV['tabs'];
	$cur_tab=$this->gO("_cur_tab");
	if($cur_tab==false){
		$ta=array_keys($tabs);
		$cur_tab=$ta[0];
	}
?>
<div >
    <ul class = "nav nav-tabs" >
        <?php
            foreach ($tabs as $TK => $tA) {
                ?>
								<li <?php echo($TK == $cur_tab ? "class='active'" : ''); ?>>
							<?php 
							$url="/{$this->obj_slug}/$TK";
							if(isset($tA['_url']) && $tA['_url']!=false)
								$url=$tA['_url'];
							$tabTit="Def Tab Title";
							if(isset($tA['_tt']) && $tA['_tt']!=false)
								$tabTit=$tA['_tt'];
							if(isset($tA['_html']) && $tA['_html']!=false)
								$tabTit=$tA['_html'];

							?>
								<a href = '<?php echo aurl($url); ?>' ><?php echo $tabTit; ?></a ></li >
						<?php }?>
    </ul >
</div >

<?php  } ?>
