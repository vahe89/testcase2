<?php

class sys_prios {

	static public $sdb;

	public $tbl;
	public $oname="";
	private $hierarhy;
	private $parent;
	private $oTf; //other table parent field name or false

	public $p;

	private $newprio;
	private $db_res;
	private $db;

	private $prioT;
	private $prioD;
	private $parT;
	private $parD;

	private $afterFld;

	function __construct(&$db,$icfg,$p){

		$dcfg=array("hierarhy"=>false,"parent"=>false,"oTf"=>false,"afterFld"=>false,'t'=>'Priotity (First or After)','pt'=>'Parent','d'=>'','pd'=>'');
		$icfg=array_merge($dcfg,$icfg);

		$this->p=$p;
		$this->db=$db;
		$this->newprio=false;

		$this->tbl=$p->tbl;
		$this->oname=$p->oname;
		$this->hierarhy=$icfg['hierarhy'];
		$this->parent=$icfg['parent'];
		$this->oTf=$icfg['oTf'];
		if($this->oTf)
			$this->parent=$this->oTf;
		$this->db_res="sys_prios_{$this->oname}";

		$this->afterFld=$icfg['afterFld'];

	$this->prioT=$icfg['t'];
	$this->prioD=$icfg['d'];
	$this->parT=$icfg['pt'];
	$this->parD=$icfg['pd'];
		
	
	}


	function autoCtrlChkArr($fn)
	{
		if($this->afterFld===$fn)
			return true;
		else
			return false;
	}
	
	function autoCtrlArr($fn)
	{
		if($this->afterFld===$fn)
		{
			if(($this->hierarhy || $this->parent) && $this->oTf==false )
				return array(
					"sys_prios_parent"=>array("c"=>"sys_prios_parent","t"=>$this->parT,'d'=>$this->parD),
					"sys_prios_prio"=>array("c"=>"sys_prios_prio","t"=>$this->prioT,'d'=>$this->prioD));
			else
				return array("sys_prios_prio"=>array("c"=>"sys_prios_prio","t"=>$this->prioT,'d'=>$this->prioD));

		}
	
	}

	
	function preAdb($act,$data){

		$iou=false;

					if(isset($data['ret.w.id'])){
						$this->db->query("select prio,parent from {$this->p->p->db_prefix}sys_prios where tbl='{$this->tbl}' and tid={$data['ret.w.id']}","{$this->db_res}_preAdbSel");
						if($this->db->numRows("{$this->db_res}_preAdbSel")>0){
						$r=$this->db->next("{$this->db_res}_preAdbSel");

						if($this->hierarhy && $this->oTf!==false){
							if(isset($data[$this->oTf]))
								$data['parent']=$data[$this->oTf];
							if(!isset($data['parent']) || $data['parent']==false)
								$data['parent']=0;
						}else if(!isset($data['parent']))
							$data['parent']=$r['parent'];



						if(!isset($data['prio']))
						$data['prio']=$r['prio'];
					

						if($r['parent']!=$data['parent']){
							$this->db->query($sqlr="update {$this->p->p->db_prefix}sys_prios set prio=prio-1 where tbl='{$this->tbl}' and (prio>={$r['prio']} and parent={$r['parent']})  order by prio asc","f_upd");
							$this->db->query($sqlr="update {$this->p->p->db_prefix}sys_prios set prio=prio+1 where tbl='{$this->tbl}' and (prio>={$data['prio']} and parent={$data['parent']})  order by prio desc","f_upd");
						}
						else if($r['prio']<$data['prio'])
							$this->db->query($sqlr="update {$this->p->p->db_prefix}sys_prios set prio=prio-1 where tbl='{$this->tbl}' and (prio>{$r['prio']} and prio<={$data['prio']} and parent={$data['parent']}) order by prio asc","f_upd");
						else if($r['prio']>$data['prio'])
							$this->db->query($sqlr="update {$this->p->p->db_prefix}sys_prios set prio=prio+1 where tbl='{$this->tbl}' and (prio<{$r['prio']} and prio>={$data['prio']} and parent={$data['parent']}) order by prio desc","f_upd");

						}else
							$iou=true;
					}
		if(!isset($data['ret.w.id']) || $iou){
					if(!isset($data['prio']))
						$data['prio']=0;

					if($this->hierarhy && $this->oTf!==false)
						$data['parent']=$data[$this->oTf];

					if(!isset($data['parent']))
						$data['parent']=0;
			
					$this->db->query($q="select id from {$this->p->p->db_prefix}sys_prios where tbl='{$this->tbl}' and prio={$data['prio']} and parent={$data['parent']}","{$this->db_res}_preAdbSelChk");
						if($this->db->numRows("{$this->db_res}_preAdbSelChk")>0)
							$this->db->query($sqlr="update {$this->p->p->db_prefix}sys_prios set prio=prio+1 where tbl='{$this->tbl}' and (prio>={$data['prio']} and parent={$data['parent']})  order by prio desc","f_upd");
					}


					if($act=="u" && !$iou)
						$this->db->query($sqlr="update {$this->p->p->db_prefix}sys_prios set prio={$data['prio']},parent={$data['parent']} where tbl='{$this->tbl}' and tid={$data['ret.w.id']}","f_upd");

					else if($act=="i" || $iou){
						$this->db->query($sqlr="insert into {$this->p->p->db_prefix}sys_prios set prio={$data['prio']},parent={$data['parent']},tbl='{$this->tbl}' ","f_upd");
						$this->newprio=$this->db->lastInsertId();
					}
	
	}


	function postAdb($act,$id,$rdt){

		if($act!="d")		{
			if($this->newprio)
				$this->db->query("update {$this->p->p->db_prefix}sys_prios set tid={$id} where tbl='{$this->tbl}' and id={$this->newprio}","f_upd");

		}
		else{
			foreach( $rdt as $dr){
			$this->db->query("select parent,prio from {$this->p->p->db_prefix}sys_prios where tbl='{$this->tbl}' and tid={$dr['id']}","{$this->db_res}_del_prios");
			$dpr=$this->db->next("{$this->db_res}_del_prios");

			if($dpr){
				$this->db->query($sqlr="update {$this->p->p->db_prefix}sys_prios set prio=prio-1 where tbl='{$this->tbl}' and parent={$dpr['parent']} and prio>={$dpr['prio']}  order by prio asc","f_upd");

				if($this->hierarhy)
					$this->delMenuTree($dr['id']);
				
				$this->db->query($sqlr="delete from {$this->p->p->db_prefix}sys_prios where tbl='{$this->tbl}' and tid={$dr['id']}");
			}
		}
		}
		$this->db->query("delete from {$this->p->p->db_prefix}sys_prios where tid=0");
	}

	function delMenuTree($parent)
	{
		$this->db->query("select tid from {$this->p->p->db_prefix}sys_prios where tbl='$this->tbl' and parent='$parent'","{$this->db_res}_del_prios_p");
		while($del_row=$this->db->next("{$this->db_res}_del_prios_p")){
			$this->delMenuTree($del_row['tid'],$this->tbl);
			$this->db->query($sqlr="delete from {$this->p->p->db_prefix}$this->tbl where id={$del_row['tid']}");
		}

		$this->db->query($sqlr="delete from {$this->p->p->db_prefix}sys_prios where tbl='$this->tbl' and parent=$parent");

	}



/*	function drawCtrl(){

		$ret="";

	if($this->hierarhy)
	{
		$ret.="Parent: <select class='sys_prios_{$this->tbl} {$this->rbl}_parent' id='sys_prios_{$this->tbl}_prios' name='".$this->p->in_rs('parent')."'>";
		$ret.=$this->selParent();
		$ret.="</select>";

		$ret.="Priority: <select class='sys_prios_{$this->tbl} {$this->rbl}_prios' id='sys_prios_{$this->tbl}_prios' name='".$this->p->in_rs('prio')."'>";
		$ret.=$this->selPrio();
		$ret.="</select>";
	
	}
	else
	{
		$ret.="Priority: <select class='sys_prios_{$this->tbl} {$this->rbl}_prios' id='sys_prios_{$this->tbl}_prios' name='".$this->p->in_rs('prio')."'>";
		$ret.=$this->selPrio();
		$ret.="</select>";
	}
			if($this->p->opts['echo'])
				echo $ret;
			else
				return $ret;
	
}
 */

	function drawParentCtrl(){
		$ret="";

		$this->p->setCopts(array("echo_INS"=>false,"echo"=>false));		

		$ret.="<select class='sys_prios_{$this->oname} {$this->rbl}_parent' id='".$this->p->hID("sys_prios_parent")."' name='".$this->p->in_rs('parent')."'>";
		$ret.="<option value='0'>- None -</option>";
		$ret.=$this->selParent();
		$ret.="</select>";

		$this->p->resetOpts();

		if($this->p->copts['echo'])
				echo $ret;
			else
				return $ret;

	}
	function drawPrioCtrl(){
		$ret="";
		$this->p->setCopts(array("echo_INS"=>false,"echo"=>false));		

		$cSelID=$this->p->hID("sys_prios_prios");

		$ret.="<select class='sys_prios_{$this->oname} {$this->rbl}_prios' id='{$cSelID}' name='".$this->p->in_rs('prio')."'>";
		$ret.="<option value='0'>- First -</option>";
		$ret.=$this->selPrio();
		$ret.="</select>";

		if($this->hierarhy){
			$parID=$this->p->hID("sys_prios_parent");
			if($this->oTf && isset($this->p->fctrls[$this->oTf]['c']) && $this->p->fctrls[$this->oTf]['c']=='select')
				$parID=$this->p->hID("sel_ctrl_{$this->oTf}");
		ob_start();?>

<script type="text/javascript">

$(document).ready(function(){
	$('#<?php echo $parID;?>').bind("change",function(){
		$('.<?php echo $this->p->hID("m-hide")?>').hide().each(function(){
			jQuery(this).removeProp("selected").attr('disabled','true');
		});
		parVal=$('#<?php echo $parID;?>').val();
		if(parVal=='' || parVal==false)
			parVal=0;
		$('.<?php echo $this->p->hID("curr-'+parVal+'");?>').show().each(function(){jQuery(this).removeAttr('disabled');});
		jQuery('#<?php echo $cSelID;?>').val(0);

	});

		$('.<?php echo $this->p->hID("m-hide")?>').hide().each(function(){
			jQuery(this).attr('disabled','true');
		});
		parVal=$('#<?php echo $parID;?>').val();
		if(parVal=='' || parVal==false)
			parVal=0;
		$('.<?php echo $this->p->hID("curr-'+parVal+'");?>').show().each(function(){jQuery(this).removeAttr('disabled');});
		if(jQuery('#<?php echo $cSelID;?> option[selected]').is('[disabled]'))
			jQuery('#<?php echo $cSelID;?>').val(0);
		
	});

</script>

<?php
		$ret.=ob_get_contents();
		ob_end_clean();
		}

		$this->p->resetOpts();

		if($this->p->copts['echo'])
				echo $ret;
			else
				return $ret;
		
	}

	function selParent($parent=0,$step="")
	{
		$ret="";
		$sel=false;
		$this->p->setCopts(array("echo"=>false,"echo_INS"=>false));
		if($parent==0)
			$incc="";
		else
			$incc="|--&gt;";

		$pnf=$this->p->copts['sys_prios_titleFld'];
		$pif=$this->p->copts['sys_prios_idFld'];
		$ppf=$pif;
		
		if($this->oTf){
		if(isset($this->p->cD[$this->oTf]))
			$sel=$this->p->cD[$this->oTf];
		}
		else
			$sel=$this->p->cD['sys_prios_parent'];


		$this->db->query("select ct.*, p.prio as prio,p.parent as parent from {$this->db_prefix}{$this->tbl} ct 
			left join {$this->db_prefix}sys_prios p on (p.tid=ct.id and p.tbl='{$this->tbl}') where ct.lang='{$this->p->def_lang}' ".(!$this->hierarhy?"":" and (p.parent=$parent or ( $parent=0 and isnull(p.parent)))")." order by p.prio asc,ct.{$pif} asc","{$this->tb_res}_s{$parent}");

		while($row=$this->db->next("{$this->tb_res}_s{$parent}"))
		{
			$ret.="<option ".($sel===$row[$pif]?"selected":"")." class='".$this->p->hID("par-unicid-{$prow['id']}")." ".$this->p->hID("recparent-{$row['parent']}")." ".$this->p->hID("p-sel")."' value='{$row[$pif]}'>{$step}$incc {$row[$pnf]}</option>";

			if($this->hierarhy)
			$ret.=$this->selParent($row[$ppf],$step."&nbsp;&nbsp;");

		}

		$this->p->resetOpts();

		return $ret;
	}


	function selPrio($parent=0,$step=0)
	{
		$ret="";
		$sel=false;
		$this->p->setCopts(array("echo"=>false,"echo_INS"=>false));

		$pnf=$this->p->copts['sys_prios_titleFld'];
		$pif=$this->p->copts['sys_prios_idFld'];
		$ppf=$pif;

		if(isset($this->p->cD[$pif]))
			$sel=$this->p->cD[$pif];


		$this->db->query("select ct.*,p.prio as prio,p.parent as parent from {$this->db_prefix}{$this->tbl} ct 
			left join {$this->db_prefix}sys_prios p on (p.tid=ct.id and p.tbl='{$this->tbl}') where ct.lang='{$this->p->def_lang}' ".(!$this->hierarhy||$this->oTf?"":" and (p.parent=$parent or ( $parent=0 and isnull(p.parent)))")." order by p.prio asc,ct.{$pif} asc","{$this->db_res}_s{$parent}");

		$row=$this->db->next("{$this->db_res}_s{$parent}");
		if($row==false){
			$this->p->resetOpts();
			return "";
		}
		
		$prow=false;
		$prio=array();
		do
		{
			if(!isset($row['parent']))
				$row['parent']=0;

//			if(!$this->hierarhy){
				if(!isset($prio[$row['parent']]))
					$prio[$row['parent']]=0;
//			}

			if($row[$pif]===$sel && $prow!==false){
//				$ret.="<option style='display:none;' disabled='true' class='curr-{$prow['parent']} m-hide recid-{$prow[$pif]}' selected='true' value='".($prio[$prow['parent']])."'>{$prow[$pnf]}</option>";
				$ret.="<option class='".$this->p->hID("curr-{$prow['parent']}")." ".$this->p->hID("m-hide")." ".$this->p->hID("recid-{$prow[$pif]}")." ".$this->p->hID("unicid-{$prow['id']}")."' selected='true' value='".($prio[$prow['parent']])."'>{$prow[$pnf]}</option>";
			}
			else if($prow['id']!==$sel && $prow!==false){
//				$ret.="<option style='display:none;' disabled='true' class='curr-{$prow['parent']} m-hide recid-{$prow[$pif]}' value='".($prio[$prow['parent']])."'>{$prow[$pnf]}</option>";
				$ret.="<option class='".$this->p->hID("curr-{$prow['parent']}")." ".$this->p->hID("m-hide")." ".$this->p->hID("recid-{$prow[$pif]}")." ".$this->p->hID("unicid-{$prow['id']}")."' value='".($prio[$prow['parent']])."'>{$prow[$pnf]}</option>";
			}

//			if(!$this->hierarhy && ($sel==false || $row[$pif]!==$sel))
			if($sel==false || $row[$pif]!==$sel)
				$prio[$row['parent']]++;
			if($this->hierarhy&& $this->oTf==false) 
				$ret.=$this->selPrio($row[$ppf],$step);

			$prow=$row;
		}
		while($row=$this->db->next("{$this->db_res}_s{$parent}"));

		if($prow!==false && $prow['id']!==$sel )
//			$ret.="<option ".($sel===false?"selected='true'":"")." disabled='true' style='display:none;' class='curr-{$prow['parent']} m-hide recid-{$prow[$pif]}' value='".($prio[$prow['parent']])."'>{$prow[$pnf]}</option>";
			$ret.="<option ".($sel===false?"selected='true'":"")." class='".$this->p->hID("curr-{$prow['parent']}")." ".$this->p->hID("m-hide")." ".$this->p->hID("recid-{$prow[$pif]}")." ".$this->p->hID("unicid-{$prow['id']}")."' value='".($prio[$prow['parent']])."'>{$prow[$pnf]}</option>";

		$this->p->resetOpts();

		return $ret;
	}


}

?>
