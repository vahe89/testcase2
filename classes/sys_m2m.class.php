<?php

class sys_m2m
{

	private $db;
	static public $sdb;

	private $db_res;
	public $objs;

	private $dataArr;

	public $p;
	public $tbl;
	public $oname="";


	function __construct(&$db,$icfg,$p)
	{
		$this->db=$db;
		$this->p=$p;
		$this->tbl=$p->tbl;
		$this->oname=$p->oname;
		$this->dataArr=false;

		/*		$dtb=array('afterFld'=>false,'t'=>'Table','role'=>'master'|'slave');*/

		$dtb=array('obj'=>false,'afterFld'=>false,'t'=>'Table','d'=>'','role'=>'master','distinct'=>true,'showOnNew'=>false,'edit_tpl'=>'sys_m2m_edit_selects','view_tpl'=>'sys_m2m_view',"meta_obj"=>false,"show_meta_flds"=>false);

		foreach($icfg as $n=>$v){
			$this->objs[$n]=array_merge($dtb,$v);
			if($this->objs[$n]['obj']==false){
				$this->objs[$n]['obj']=$n;
			}
			if(is_object($this->p->p->t[$this->objs[$n]['obj']]))
				$this->objs[$n]['dbo']=$this->p->p->t[$this->objs[$n]['obj']];
			else{
				$this->objs[$n]['dbo']=new StdClass();
				$this->objs[$n]['dbo']->tbl=$n;
			}
		}

		$this->db_res="sys_m2m_{$this->oname}";
	}

	function autoCtrlChkArr($fn)
	{
		foreach($this->objs as $on=>$ov){
			if($ov['afterFld']===$fn)
				return true;
		}
		return false;
	}

	function autoCtrlArr($fn)
	{
		$ret=array();
		foreach($this->objs as $on=>$ov){
			if($ov['afterFld']===$fn)
			{
				$ret["sys_m2m_{$on}"]=array("c"=>"sys_m2m","t"=>$ov['t'],"d"=>$ov['d'],"obj"=>$on);
			}
		}
		return $ret;
	}

	function drawAdminCtrl($obj,$ajax=0)
	{
		$this->p->setCopts(array("echo_INS"=>false,"echo"=>false));
		$ret="";
		$rid=1;
		$dnew=false;
		$rows=array();
		$rowsk=array();

		$mt=$this->objs[$obj]['dbo']->tbl;
		$st=$this->tbl;
		$qcid="sid";
		$qoid="mid";
		if($this->objs[$obj]['role']=="slave")
		{
		$st=$this->objs[$obj]['dbo']->tbl;
		$mt=$this->tbl;
		$qcid="mid";
		$qoid="sid";
		}


		if(!$ajax)
			$ret.="<div id='sys_m2m_div_{$this->oname}_{$obj}'>";
		ob_start();

			$use_meta=false;
			$meta_o=false;
			if($this->objs[$obj]['meta_obj']!="" && is_object($this->p->p->t[$this->objs[$obj]['meta_obj']])){
				$use_meta=true;	
				$meta_o=$this->p->p->t[$this->objs[$obj]['meta_obj']];
			}
		
		if($ajax==0 && isset($this->p->cD['id']) && $this->p->cD['id']!=false){

			$this->db->query("select * from {$this->p->p->db_prefix}sys_m2m where rel_name='$obj' and mtbl='$mt' and stbl='$st' and $qcid={$this->p->cD['id']} order by id asc","{$this->db_res}_drawACtrlS");
			if($this->db->numRows("{$this->db_res}_drawACtrlS")==0)
				$dnew=true;

			$meta_data=array();

			while($row=$this->db->next("{$this->db_res}_drawACtrlS")){
				$rows[$row['id']]=$row;
				$rowsk[$row['id']]=$row[$qoid];
			}

			if($use_meta){
				$this->db->query("select m.id as sys_m2m_id,ct.* from {$this->p->p->db_prefix}{$meta_o->tbl} ct
					right join {$this->p->p->db_prefix}sys_m2m m on ct.id=m.meta_id
					where m.id in (".implode(",",array_keys($rows)).")  order by id asc","{$this->db_res}_drawACtrlS_meta");

				while($row=$this->db->next("{$this->db_res}_drawACtrlS_meta"))
					$meta_data[$row['sys_m2m_id']]=$row;

			}


			if(count($rows)>0){
				$sw="";

			foreach($rows as $rid=>$row)
			{
				if($this->objs[$obj]['distinct'] && count($rowsk)>1){
					$ddtt=$rowsk[$rid];
					unset($rowsk[$rid]);
					$sw="ct.id not in (".implode(",",$rowsk).")";
					$rowsk[$rid]=$ddtt;
				}
				$div_wrapID="sys_m2m_o_{$this->oname}_{$obj}_{$rid}";
				$sel_name=$this->p->_secureFormNames("[sys_m2m][$obj][{$rid}]");
				$meta_name=$this->p->_secureFormNames("[sys_m2m][meta][$obj][{$rid}]");
				$classes="sys_m2m sys_m2m_{$this->oname} sys_m2m_{$this->oname}_{$obj}";
				$sel_ID="sys_m2m_sel_{$this->oname}_{$obj}_{$rid}";
				$cur_sel=$rowsk[$rid];
				$sel_opts=array("echo"=>false,"sel_where"=>$sw);
				$del_name=$this->p->_secureFormNames("[sys_m2m][del][$obj][]");
				$div_RESwrapID="sys_m2m_ores_{$this->oname}_{$obj}_{$rid}";

				require(dirname(__FILE__)."/sys_tpls/".$this->objs[$obj]['edit_tpl'].".inc.php");

			}
			}


		}
		else
			$dnew=true;

		if(($dnew && $this->objs[$obj]['showOnNew']) || $ajax>0){
			$rid=1;
			if($ajax>0)
				$rid=$ajax;

			$div_wrapID="sys_m2m_newo_{$this->oname}_{$obj}_{$rid}";
				$sel_name=$this->p->_secureFormNames("[sys_m2m][new][$obj][{$rid}]");
				$meta_name=$this->p->_secureFormNames("[sys_m2m][newmeta][$obj][{$rid}]");
				$classes="sys_m2m sys_m2m_{$this->oname} sys_m2m_{$this->oname}_{$obj} sys_m2m_new";
				$sel_ID="sys_m2m_newsel_{$this->oname}_{$obj}_{$rid}";
				$cur_sel=false;
				$sel_opts=array("echo"=>false);
				$del_name=$this->p->_secureFormNames("[sys_m2m][ndel][$obj][]");
				$div_RESwrapID="sys_m2m_newores_{$this->oname}_{$obj}_{$rid}";

				require(dirname(__FILE__)."/sys_tpls/".$this->objs[$obj]['edit_tpl'].".inc.php");
		}

		$ret.=ob_get_contents();
		ob_end_clean();


		if($ajax)
			die($ret);

		if(!$ajax)
			$ret.="</div>";

		$ret.="<input type='button' value='Add Another' onclick=\"$.get(".$this->p->aL("index.php","a=dbo_{$this->oname}&f=sys_m2m_{$obj}&rid=")."+counter_new_sys_m2m_{$this->oname}_{$obj},function(d){\$('#sys_m2m_div_{$this->oname}_{$obj}').append(d);counter_new_sys_m2m_{$this->oname}_{$obj}++;});\">";
		$ret.="<script type='text/javascript'> \n var counter_new_sys_m2m_{$this->oname}_{$obj}=2; \n</script>";

		$this->p->resetOpts();

		return $ret;

	}

	function queryAll($ret,$iobjs){
		$objs=false;
		if($iobjs===true)	{
			$objs=$this->objs;
		}else if(!is_array($iobjs) && $iobjs!=false && isset($this->objs[$iobjs])){
			$objs=array($iobjs);
		}else if(is_array($iobjs)){
			$objs=array();
			foreach($iobjs as $o){
				if(isset($this->objs[$o]))
					$objs[]=$o;
			}
		}
		if(is_array($objs) && count($objs)>0){
			$ids=array_keys($ret);
			foreach($objs as $obj){
				$mt=$this->objs[$obj]['dbo']->tbl;
				$st=$this->tbl;
				$qcid="sid";
				$qoid="mid";
				if($this->objs[$obj]['role']=="slave")
				{
					$st=$this->objs[$obj]['dbo']->tbl;
					$mt=$this->tbl;
					$qcid="mid";
					$qoid="sid";
				}

				$use_meta=false;
				$meta_o=false;
				$meta_rows=array();
				if($this->objs[$obj]['meta_obj']!="" && is_object($this->p->p->t[$this->objs[$obj]['meta_obj']])){
					$use_meta=true;	
					$meta_o=$this->p->p->t[$this->objs[$obj]['meta_obj']];
				}
				$this->db->query($q="select m.id as sys_m2m_id,m.$qcid as sys_m2m_rem_id, m.meta_id, ct.* from {$this->p->p->db_prefix}sys_m2m m 
					left join {$this->p->p->db_prefix}{$this->objs[$obj]['dbo']->tbl} ct on ct.id=m.{$qoid}
					where m.rel_name='$obj' and m.mtbl='$mt' and m.stbl='$st' and m.{$qcid} IN (".implode(",",$ids).")
					order by id asc",
					"{$this->db_res}_drawM2MQALL");

				while($r=$this->db->next("{$this->db_res}_drawM2MQALL")){
					$ret[$r['sys_m2m_rem_id']][$r['lang']]['sys_m2m'][$obj]['ids'][$r['id']]=$r['id'];
					$ret[$r['sys_m2m_rem_id']][$r['lang']]['sys_m2m'][$obj]['rids'][$r['sys_m2m_id']][]=$r['id'];
					$ret[$r['sys_m2m_rem_id']][$r['lang']]['sys_m2m'][$obj]['rows'][$r['sys_m2m_id']]['data']=$r;
					if($use_meta && $r['meta_id']>0)
						$meta_rows[$r['meta_id']]=$r;
				}

				if($use_meta){
					if(count($meta_rows)){
						$mids=array_keys($meta_rows);
						$this->db->query($q="select * from {$this->p->p->db_prefix}{$meta_o->tbl}
						where id IN (".implode(",",$mids).")
						order by id asc",
						"{$this->db_res}_drawM2MQALL");
						while($r=$this->db->next("{$this->db_res}_drawM2MQALL")){
							$mmr=$meta_rows[$r['id']];
							$ret[$mmr['sys_m2m_rem_id']][$mmr['lang']]['sys_m2m'][$obj]['rows'][$mmr['sys_m2m_id']]['meta']=$r;
						}

					}
				}

			}
		}
		//		die();
		return $ret;
	}

	function drawValue($irow,$obj,$l=false){
		ob_start();
		if($l==false)
			$lang_rows=$irow;
		else
			$lang_rows=array($l=>$irow[$l]);

		$use_meta=false;
		$meta_o=false;
		$meta_rows=array();
		if($this->objs[$obj]['meta_obj']!="" && is_object($this->p->p->t[$this->objs[$obj]['meta_obj']])){
			$use_meta=true;	
			$meta_o=$this->p->p->t[$this->objs[$obj]['meta_obj']];
		}
		foreach($lang_rows as $l=>$r){
			if(!isset($r['sys_m2m'][$obj]['rows']))
				continue;
			$m2m=$r['sys_m2m'][$obj]['rows'];
			foreach($m2m as $m2m_id=>$r){
				$data=false;
				$meta=false;				
				if(isset($r['data']))		
					$data=$r['data'];
				if(isset($r['meta']))		
					$meta=$r['meta'];
				require(dirname(__FILE__)."/sys_tpls/".$this->objs[$obj]['view_tpl'].".inc.php");
			}
		}

		$ret=ob_get_contents();
		ob_end_clean();
		return $ret;

	}

	function preAdb($act,$data)
	{
		if(isset($data['sys_m2m']['del']) && is_array($data['sys_m2m']['del'])){
			foreach($data['sys_m2m']['del'] as $obj=>$ov){
				foreach($ov as $oid)
					unset($data['sys_m2m'][$obj][$oid]);
			}
		}

		if(isset($data['sys_m2m']['ndel']) && is_array($data['sys_m2m']['ndel'])){
			foreach($data['sys_m2m']['ndel'] as $obj=>$ov){
				foreach($ov as $oid)
					unset($data['sys_m2m']['new'][$obj][$oid]);
			}
		}

		$this->dataArr=$data['sys_m2m'];

	}

	function postAdb($act,$id,$rdt)
	{
		if($id==false)
			return false;

		if($act=="d"){
			foreach($rdt as $v){
				$this->db->query("select meta_id,meta_tbl from {$this->p->p->db_prefix}sys_m2m 
					where (mtbl='{$this->tbl}' and mid={$v['id']}) OR (stbl='{$this->tbl}' and sid={$v['id']})","post_m2m_preDel");
				$meta_del=array();
				while($r=$this->db->next("post_m2m_preDel")){
					if($r['meta_tbl']!=""){
						if(!isset($meta_del[$r['meta_tbl']]))
							$meta_del[$r['meta_tbl']]=array();
						if(is_numeric($r['meta_id']))
							$meta_del[$r['meta_tbl']][$r['meta_id']]=$r['meta_id'];
					}
				}
				$this->db->query("delete from {$this->p->p->db_prefix}sys_m2m 
					where (mtbl='{$this->tbl}' and mid={$v['id']}) OR (stbl='{$this->tbl}' and sid={$v['id']})");
				foreach($meta_del as $tbl=>$ids){
					if(count($ids)>0)
						$this->db->query("delete from {$this->p->p->db_prefix}$tbl where id IN (".implode(",",$ids).")");
				}
			}

			return true;

		}

		if(!is_array($this->dataArr))
			return false;


		$cdel=false;

		if(isset($this->dataArr['del'])){
			$cdel=$this->dataArr['del'];
			unset($this->dataArr['del']);
		}
		if(isset($this->dataArr['ndel']))
			unset($this->dataArr['ndel']);

//		var_dump($this->dataArr);
		if($act!="d")
		{
			if(isset($this->dataArr['new'])){
			foreach($this->dataArr['new'] as $obj=>$v)
			{
				$use_meta=false;
				$meta_o=false;
				if($this->objs[$obj]['meta_obj']!="" && is_object($this->p->p->t[$this->objs[$obj]['meta_obj']])){
					$use_meta=true;	
					$meta_o=$this->p->p->t[$this->objs[$obj]['meta_obj']];
				}

				$dak=array();
				foreach($v as $ni=>$oid){
					if($this->objs[$obj]['distinct']){
						if((isset($this->dataArr[$obj]) && in_array($oid,$this->dataArr[$obj])) || isset($dak[$oid]))
							continue;
						$dak[$oid]=1;
					}
					$st=$this->tbl;
					$si=$id;
					$mt=$this->objs[$obj]['dbo']->tbl;
					$mi=$oid;
					if($this->objs[$obj]['role']=='slave')
					{
						$mt=$this->tbl;
						$mi=$id;
						$st=$this->objs[$obj]['dbo']->tbl;
						$si=$oid;
					}
					$Nmeta_tbl='';
					$Nmeta_id=0;

					if($use_meta){
						$meta_flds=array();
						if(is_array($this->dataArr['newmeta'][$obj][$ni])){
							foreach($this->dataArr['newmeta'][$obj][$ni] as $mfn=>$mfv)
								$meta_flds[$mfn]="{$mfn}='".$this->db->escape($mfv)."'";
						}
						if(count($meta_flds)>0){
							$this->db->query("insert into {$this->p->p->db_prefix}{$meta_o->tbl} set ".implode(",",$meta_flds));
							$Nmeta_id=$this->db->lastInsertId();
							$Nmeta_tbl=$meta_o->tbl;
						}
					}

					$this->db->query("insert into {$this->p->p->db_prefix}sys_m2m (rel_name,mtbl,mid,stbl,sid,meta_tbl,meta_id) value('$obj','$mt',$mi,'$st',$si,'$Nmeta_tbl',$Nmeta_id)");
				}
			}
			unset($this->dataArr['new']);
			unset($this->dataArr['newmeta']);
			}
			foreach($this->dataArr as $obj=>$v)
			{
				$use_meta=false;
				$meta_o=false;
				if($this->objs[$obj]['meta_obj']!="" && is_object($this->p->p->t[$this->objs[$obj]['meta_obj']])){
					$use_meta=true;	
					$meta_o=$this->p->p->t[$this->objs[$obj]['meta_obj']];
				}
				foreach($v as $rid=>$oid){
					$st=$this->tbl;
					$si=$id;
					$mt=$this->objs[$obj]['dbo']->tbl;
					$mi=$oid;
					if($this->objs[$obj]['role']=='slave')
					{
						$mt=$this->tbl;
						$mi=$id;
						$st=$this->objs[$obj]['dbo']->tbl;
						$si=$oid;
					}
					$meta_q="";

					if($use_meta){
						$meta_flds=array();
						if(is_array($this->dataArr['meta'][$obj][$rid])){
							foreach($this->dataArr['meta'][$obj][$rid] as $mfn=>$mfv)
								$meta_flds[$mfn]="{$mfn}='".$this->db->escape($mfv)."'";
						}
						if(count($meta_flds)>0){
							$mr=$this->db->getRow($q="select meta_id,count(meta_id) as cnt 
								from {$this->p->p->db_prefix}sys_m2m  
								where id=$rid");
							if(is_array($mr) && ($mr['cnt']==0 || $mr['meta_id']==false)){
								$this->db->query($q="insert into {$this->p->p->db_prefix}{$meta_o->tbl} set ".implode(",",$meta_flds));
								$mnid=$this->db->lastInsertId();
								$meta_q=", meta_tbl='{$meta_o->tbl}', meta_id=$mnid";
							}else{
								$this->db->query("update {$this->p->p->db_prefix}{$meta_o->tbl} set ".implode(",",$meta_flds)." where id={$mr['meta_id']}");
							}
						}
					}
					
					$this->db->query("update {$this->p->p->db_prefix}sys_m2m set rel_name='$obj',mtbl='$mt', mid=$mi, stbl='$st', sid=$si $meta_q where id=$rid");
				}

			}
					
		}

		if(is_array($cdel)){
			foreach($cdel as $obj=>$rids){
				$this->db->query("select meta_id,meta_tbl from {$this->p->p->db_prefix}sys_m2m 
					where	id in (".implode(",",$rids).")","post_m2m_preDelRel");
				$meta_del=array();
				while($r=$this->db->next("post_m2m_preDelRel")){
					if($r['meta_tbl']!=""){
						if(!isset($meta_del[$r['meta_tbl']]))
							$meta_del[$r['meta_tbl']]=array();
						if(is_numeric($r['meta_id']))
							$meta_del[$r['meta_tbl']][$r['meta_id']]=$r['meta_id'];
					}
				}
				$this->db->query($dq="delete from {$this->p->p->db_prefix}sys_m2m where id in (".implode(",",$rids).")");

				foreach($meta_del as $tbl=>$ids){
					if(count($ids)>0)
						$this->db->query("delete from {$this->p->p->db_prefix}$tbl where id IN (".implode(",",$ids).")");
				}
			}
		}
		
	}

	function check($ct)
	{
		return array_key_exists($ct,$this->objs);
	}


	function addRels(&$trels,$rtbl=false)
	{
			if($rtbl==false && $ta=$this->p->gO("query_m2m")){

			if(!$rev){
			foreach($this->objs as $tn=>$tv){
				if(is_array($ta) && !in_array($tn,$ta))
					continue;
				if($tv['role']=="slave"){
					$trels["r_sys_m2m_$tn"]=array("tbl"=>"sys_m2m","tbln"=>"r_sys_m2m_".$tn,"on"=>array(
						"vals"=>array("mtbl"=>$this->tbl,"stbl"=>$this->objs[$tn]['dbo']->tbl,"rel_name"=>$tn),
						"flds"=>array("id"=>"mid","lid"=>"mid"),
            "op"=>array("lid"=>"or"),
            "block"=>array("flds"=>true),
						));
					$trels["sys_m2m_$tn"]=array("ctbl"=>"r_sys_m2m_".$tn,"tbl"=>$this->objs[$tn]['dbo']->tbl,"tbln"=>"sys_m2m_".$tn,"on"=>array(
						"flds"=>array("sid"=>"id")
					));
				
				}
				else if($tv['role']=="master"){
					$trels["r_sys_m2m_$tn"]=array("tbl"=>"sys_m2m","tbln"=>"r_sys_m2m_".$tn,"on"=>array(
						"vals"=>array("stbl"=>$this->tbl,"mtbl"=>$this->objs[$tn]['dbo']->tbl,"rel_name"=>$tn),
						"flds"=>array("id"=>"sid","lid"=>"sid"),
            "op"=>array("lid"=>"or"),
            "block"=>array("flds"=>true),
					));
					$trels["sys_m2m_$tn"]=array("ctbl"=>"sys_m2m_".$tn,"tbl"=>$this->objs[$tn]['dbo']->tbl,"tbln"=>"sys_m2m_".$tn,"on"=>array(
						"flds"=>array("mid"=>"id")
					));
					
				}
			}
			}
			}else if($rtbl){
				$tv=$this->objs[$rtbl];
					if($tv['role']=="slave"){
					$trels["r_sys_m2m_{$this->tbl}"]=array("tbl"=>"sys_m2m","tbln"=>"r_sys_m2m_{$this->tbl}","on"=>array(
						"vals"=>array("mtbl"=>$this->tbl,"stbl"=>$this->objs[$rtbl]['dbo']->tbl,"rel_name"=>$rtbl),
						"flds"=>array("id"=>"sid","lid"=>"sid"),
            "op"=>array("lid"=>"or"),
            "block"=>array("flds"=>true),
					));
					$trels["sys_m2m_{$this->tbl}"]=array("ctbl"=>"sys_m2m_{$this->tbl}","tbl"=>$this->tbl,"tbln"=>"sys_m2m_{$this->tbl}","on"=>array(
						"flds"=>array("mid"=>"id")
					));
				
				}
				else if($tv['role']=="master"){
					$trels["r_sys_m2m_{$this->tbl}"]=array("tbl"=>"sys_m2m","tbln"=>"r_sys_m2m_{$this->tbl}","on"=>array(
//						"vals"=>array("stbl"=>$this->tbl,"mtbl"=>$this->tbl),
						"vals"=>array("stbl"=>$this->objs[$rtbl]['dbo']->tbl,"mtbl"=>$this->tbl,"rel_name"=>$rtbl),
						"flds"=>array("id"=>"mid","lid"=>"mid"),
            "op"=>array("lid"=>"or"),
            "block"=>array("flds"=>true),
					));
					$trels["sys_m2m_{$this->tbl}"]=array("ctbl"=>"sys_m2m_{$this->tbl}","tbl"=>$this->tbl,"tbln"=>"sys_m2m_{$this->tbl}","on"=>array(
						"flds"=>array("sid"=>"id")
					));
					
				}
		
			
		}

	}

	function listCur($obj,$order='ct.id asc',$on='id')
	{
		$mtbl=$this->objs[$obj]['dbo']->tbl;
		$stbl=$this->tbl;

		$jtbl=$mtbl;
		$jid="mid";
		$qcid="sid";

		if($this->objs[$obj]['role']=="slave"){
			$mtbl=$this->tbl;
			$stbl=$this->objs[$obj]['dbo']->tbl;
			$jtbl=$stbl;
			$jid="sid";
			$qcid="mid";
		}


		$ret=array();
		$this->db->query($q="select ct.* from {$this->p->p->db_prefix}sys_m2m m left join $jtbl ct on ct.id=m.{$jid} 
			where m.rel_name='$obj' and m.mtbl='$mtbl' and m.stbl='$stbl' and m.$qcid={$this->p->cD[$on]} order by $order","{$this->db_res}_{$mtbl}_{$stbl}_cur_query");


		if($this->db->numRows("{$this->db_res}_{$mtbl}_{$stbl}_cur_query")==0)
				return false;

		while($row=$this->db->next("{$this->db_res}_{$mtbl}_{$stbl}_cur_query"))
			$ret[$row['id']]=$row;

			return $ret;
	}

	static function listM2M($cobj,$mtbl,$stbl,$getmaster=true,$onmaster=true,$order='ct.id asc',$on='id')
	{
		$jtbl=$mtbl;
		$jid="mid";
		$qcid="sid";

		if($onmaster==false)
			$qcid="sid";

		if($getmaster==false)
		{
			$jtbl=$stbl;
			$jid="sid";
		}


		$ret=array();
		$this->db->query("select m.rel_name,ct.* from {$cobj->p->db_prefix}sys_m2m m left join $jtbl ct on ct.id=m.{$jid} 
			where m.mtbl='$mtbl' and m.stbl='$stbl' and m.$qcid={$cobj->p->cD[$on]} order by $order","sys_m2m_{$mtbl}_{$stbl}_list_query");

		if($cobj->db->numRows("sys_m2m_{$mtbl}_{$stbl}_list_query")==0)
				return false;

		while($row=$cobj->db->next("sys_m2m_{$mtbl}_{$stbl}_list_query"))
			$ret[$row['id']]=$row;

			return $ret;
	}


}


?>
