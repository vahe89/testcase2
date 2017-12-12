<?php
class gui_slaves{

	public $tbl;
	private $db;
	private $db_res;
	public $p;
	public $oname;

	private $dataArr;

	public $slaves;

	private $innCall=false;

	function __construct(&$db,$icfg,$p)
	{
		$this->dataArr=false;
		$this->slaves=array();
		$this->db=$db;
		$this->p=$p;
		$this->tbl=$p->tbl;
		$this->oname=$this->p->oname;
		$this->db_res="gui_slaves_{$this->oname}";

		$ds=array("lang"=>false,"onNew"=>false,"on"=>"id","afterFld"=>false,"t"=>"","d"=>"");

		foreach($icfg as $sn=>$sv){
			if(!isset($sv['opts']['listTpl']))
				$sv['opts']['listTpl']="gui_def";
			if(!isset($sv['opts']['listInc']))
				$sv['opts']['listInc']="gui_def_list";
			if(!isset($sv['opts']['editInc']))
				$sv['opts']['editInc']="gui_def_edit";

			$this->slaves[$sn]=array_merge($ds,$sv);

		}

	}

	function autoCtrlChkArr($fn)
	{
		foreach($this->slaves as $on=>$ov){
			if($ov['afterFld']==$fn)
				return true;
			else
				return false;
		}
	}

	function autoCtrlArr($fn)
	{
		$ret=array();
		foreach($this->slaves as $on=>$ov){
			if($lv['afterFld']==$fn)
			{
				$ret["gui_slaves_{$on}"]=array("c"=>"gui_slaves","t"=>$ov['t'],"d"=>$ov['d'],"slave"=>$on);
			}
		}
		return $ret;
	}


	function addRels(&$trels,$tpref="slave_",$ctbl="ct")
	{
		if($this->p->gO("query_slaves")){
			$ta=$this->p->gO("query_slaves");

			foreach($this->slaves as $stbl=>$sv)
			{
//				var_dump("<hr>",$stbl,"<hr>",$this->p->gO("query_slaves"),"<hr>",$this->p->gO("query_slaves_fields"),"<hr>");
				$stpref=$tpref.$stbl."_";
				if(is_array($ta) && in_array($stbl,$ta))
				{
					$trels["gui_slaves_$stbl"]=array("ctbl"=>$ctbl,"tbl"=>"$stbl","tbln"=>$tpref.$stbl,"on"=>array(
						"flds"=>array($sv['on']=>$sv['fld'])
					));
					if(is_array($this->p->p->t[$stbl]->rels)){
					foreach($this->p->p->t[$stbl]->rels as $srk=>$srfa)
					{
						$tcr=$srfa;
						if(!is_array($tcr['on']))
							$tcr['on']=array("flds"=>array($srk=>$tcr['on']));

						$tcr['tbln']=$stpref.$tcr['tbl'];
						$tcr['ctbl']=$tpref.$stbl;
						$tcr['to_flds']="{$stpref}{$srk}";

						$trels["gui_slaves_{$stbl}_{$srk}"]=$tcr;
					}
					}
				}

				$qsfld=$this->p->gO("query_slaves_fields");

//				var_dump("OKKKKKKKKKK",$qsfld," -- ------",$this->p->gO("query_slaves_fields"));
//				var_dump("<hr>",$stbl," ARR",$qsfld,"<hr>");

				$mtbl=$this->tbl;
				if($ctbl!="ct")
					$mtbl=$this->p->gO("__gui_slave_main_tbl");

				if(is_array($qsfld) && array_key_exists($stbl,$qsfld))
				{
//					var_dump("INNNN");
					$cf=$qsfld[$stbl];
					if(!is_array($cf) && $cf=="*")
						$cf=array_merge($this->p->p->t[$stbl]->sys_flds,$this->p->p->t[$stbl]->flds);
					else if(!is_array($cf))
						$cf=array($cf);

					$cfa=array();
					foreach($cf as $f)
						$cfa[]="{$tpref}{$stbl}.{$f} as {$tpref}{$stbl}_{$f}";

					$cfa[]="{$tpref}{$stbl}.id as id";
					$cfa[]="ct.id as ct_id";
						
					$this->p->p->t[$mtbl]->addOpts(array("__queryFields"=>$cfa));

//				var_dump("<hr> OK",$stbl,"<hr> FLDS",$this->p->gO("__queryFields"),"<hr>");

				}

				if(is_object($this->p->p->t[$stbl]->gui['slaves'])){

					$trels["gui_slaves_$stbl"]=array("ctbl"=>$ctbl,"tbl"=>"$stbl","tbln"=>$tpref.$stbl,"on"=>array(
						"flds"=>array($sv['on']=>$sv['fld'])
					));

					$this->p->p->t[$stbl]->setCopts(array("__gui_slave_main_tbl"=>$mtbl,"query_slaves"=>$ta,"query_slaves_fields"=>$this->p->gO("query_slaves_fields")));
					$this->p->p->t[$stbl]->gui['slaves']->addRels($trels,$tpref.$stbl."_",$tpref.$stbl);
					$this->p->p->t[$stbl]->resetOpts();
				}

			}
		}


	}

/*
 	function listSlaves($islaves,$tpl)
	{
		if(!is_array($islaves))	
			$slaves=array($islaves);
$this->innCall=true;
			foreach($this->slaves as $stbl=>$sv)
			{
				if(!in_array($stbl,$islaves)){
					if(is_object($this->p->p->t[$stbl]->gui['slaves'])){
						$osl=$this->p->p->t[$stbl]->gO("queryRels");
						if(!$osl)
							$osl=array($this->tbl=>array());
					$this->p->p->t[$stbl]->gui['slaves']->listSlaves($islaves,$tpl);
				}
					continue;
				}

				$this->drawAdminCtrl($stbl,0,$tpl);
				
			}
		$this->innCall=false;

	
	}
 */


 	function listSlave($islave,$tpl,$iwhere=false,$flds="*")
	{
		//		$this->p->changeOpts(array("__debugDieQALL"=>1));
		$s=$this->p->gO("__queryFields");
		$cd=$this->p->cD;
		$cc=$this->p->cC;
		$cr=$this->p->current;
		$this->p->changeOpts(array("__queryFields"=>false));

		$cid=$this->p->cD['id'];
		if(isset($this->p->cD['ct_id']))
			$cid=$this->p->cD['ct_id'];


		$this->p->listDef($tpl,array("queryPrefix"=>"slave_{$islave}_query","query_slaves_fields"=>array($islave=>$flds),"query_slaves"=>array($islave),"queryWhere"=>" ct.id={$cid} ".($iwhere?" and $iwhere":"")));
		$this->p->changeOpts(array("__queryFields"=>$s));
		$this->p->cD=$cd;
		$this->p->cC=$cc;
		$this->p->current=$cr;

	}

	function prepare($sn,$ajax=0){
		$cdr=$this->slaves[$sn];

/*		if($ajax>0 && $agso=$this->p->gA("gui_slaves_in_o")){
			$ddp=$agso;

}else{*/
		$ddp="[gui_slaves][{$sn}]";

		if(isset($this->p->copts['gui_slaves_in']))
			$ddp=$this->p->copts['gui_slaves_in'].$ddp;
		//		}


	/*	if($ajax>0 && $agso=$this->p->gA("gui_slaves_html_prefix_o")){
			$hhp=$agso;
	}else{*/
		$hhp="slave_{$sn}";

		if(isset($this->p->copts['gui_slaves_html_prefix']))
			$hhp=$this->p->copts['gui_slaves_html_prefix']."_".$hhp;
		//		}


		if(!is_array($cdr['opts']))
			$cdr['opts']=array();


		if($ajax==0){
		$cdr['opts']['gui_slaves_counter']=2;
		$cdr['opts']['gui_slaves_in']=$ddp;
		$cdr['opts']['gui_slaves_in_o']=$ddp;
		$cdr['opts']['gui_slaves_html_prefix']=$hhp;
		$cdr['opts']['gui_slaves_html_prefix_o']=$hhp;
		$cdr['opts']['gui_slaves_parent']=$this->tbl;
		$cdr['opts']['gui_slaves_parent_obj']=$this->p->oname;
		}

		$cdr['opts']['ajax']=$ajax;
		$cdr['opts']['ownHeader']=true;
		$cdr['opts']['gui_slaves_new']=false;

		if($this->p->isAjax() || $this->p->current==false){
			$cdr['opts']['gui_slaves_new']=true;
//			$this->p->p->t[$sn]->sAO('gui_slaves_new',true);
		}


		if($ajax>0){
			//			$this->p->sAC('gui_slaves_rid',1);
		$this->p->p->t[$sn]->sAC('gui_slaves_rid',1);
		}

/*		$this->p->p->t[$sn]->sAO('gui_slaves_in_o',$ddp);
		$this->p->p->t[$sn]->sAO('gui_slaves_html_prefix_o',$hhp);
		$this->p->p->t[$sn]->sAO('gui_slaves_parent',$this->tbl);*/

		$this->p->p->t[$sn]->changeOpts($cdr['opts']);

		unset($cdr['opts']['ajax']);
		if($ajax>0){
			$this->p->p->t[$sn]->sAOA($this->p->p->t[$sn]->gAO());
			$this->p->p->t[$sn]->sAO('gui_slaves_new',$cdr['opts']['gui_slaves_new']);
		}
		else
			$this->p->p->t[$sn]->sAOA($cdr['opts']);



/*			if(!$cdr['lang'])
				$qw="ct.{$cdr['fld']}=".$this->p->iv("id",false,"NULL");
			else				
				$qw="ct.{$cdr['fld']}=".$this->p->iv("id",$l,"NULL");
 */
		return "ct.{$cdr['fld']}=".$this->p->iv("id",false,"0",array('echo'=>false));
		
	}

	function dC($sn,$fn,$l=false,$ajax=0,$iopts=array()){
		$qw=$this->prepare($sn, $ajax);

		return $this->p->p->t[$sn]->dC($fn,$l,$iopts);
	}

	function drawAdminCtrl($sn,$ajax=0,$inTpl=false)
	{

		$ret="";
/*		
//-----------------
		$cdr=$this->slaves[$sn];

/*		if($ajax>0 && $agso=$this->p->gA("gui_slaves_in_o")){
			$ddp=$agso;

}else{*//*
		$ddp="[gui_slaves][{$sn}]";

		if(isset($this->p->copts['gui_slaves_in']))
			$ddp=$this->p->copts['gui_slaves_in'].$ddp;
		//		}


	/*	if($ajax>0 && $agso=$this->p->gA("gui_slaves_html_prefix_o")){
			$hhp=$agso;
	}else{*/ /*
		$hhp="slave_{$sn}";

		if(isset($this->p->copts['gui_slaves_html_prefix']))
			$hhp=$this->p->copts['gui_slaves_html_prefix']."_".$hhp;
		//		}


		if(!is_array($cdr['opts']))
			$cdr['opts']=array();


		if($ajax==0){
		$cdr['opts']['gui_slaves_counter']=2;
		$cdr['opts']['gui_slaves_in']=$ddp;
		$cdr['opts']['gui_slaves_in_o']=$ddp;
		$cdr['opts']['gui_slaves_html_prefix']=$hhp;
		$cdr['opts']['gui_slaves_html_prefix_o']=$hhp;
		$cdr['opts']['gui_slaves_parent']=$this->tbl;
		$cdr['opts']['gui_slaves_parent_obj']=$this->p->oname;
		}

		$cdr['opts']['ajax']=$ajax;
		$cdr['opts']['ownHeader']=true;
		$cdr['opts']['gui_slaves_new']=false;

		if($this->p->isAjax() || $this->p->current==false){
			$cdr['opts']['gui_slaves_new']=true;
//			$this->p->p->t[$sn]->sAO('gui_slaves_new',true);
		}


		if($ajax>0){
			//			$this->p->sAC('gui_slaves_rid',1);
		$this->p->p->t[$sn]->sAC('gui_slaves_rid',1);
		}

/*		$this->p->p->t[$sn]->sAO('gui_slaves_in_o',$ddp);
		$this->p->p->t[$sn]->sAO('gui_slaves_html_prefix_o',$hhp);
		$this->p->p->t[$sn]->sAO('gui_slaves_parent',$this->tbl);*/ /*

		$this->p->p->t[$sn]->changeOpts($cdr['opts']);

		unset($cdr['opts']['ajax']);
		if($ajax>0){
			$this->p->p->t[$sn]->sAOA($this->p->p->t[$sn]->gAO());
			$this->p->p->t[$sn]->sAO('gui_slaves_new',$cdr['opts']['gui_slaves_new']);
		}
		else
			$this->p->p->t[$sn]->sAOA($cdr['opts']);



/*			if(!$cdr['lang'])
				$qw="ct.{$cdr['fld']}=".$this->p->iv("id",false,"NULL");
			else				
				$qw="ct.{$cdr['fld']}=".$this->p->iv("id",$l,"NULL");
 */ /*
		$qw="ct.{$cdr['fld']}=".$this->p->iv("id",false,"0",array('echo'=>false));

		//----------------
 */
		$qw=$this->prepare($sn, $ajax);

		if($ajax==0){
			$ret=$this->p->p->t[$sn]->showAdmin($cdr['listTpl'],array('echo'=>false,"queryWhere"=>$qw));

		}
		else{
			$this->p->p->t[$sn]->next();

			//			$ret=$this->p->p->t[$sn]->listDef($this->p->p->t[$sn]->copts['listInc']);
			//$this->doInc($ttp,$this->copts);

			$ttp="dbo_{$this->p->p->t[$sn]->copts['listInc']}_{$this->p->p->t[$sn]->copts['adminOnlineEdit']}";

			if(!is_file($this->p->p->TEMPL."/inc/$ttp.inc.php"))
				$ttp="dbo_{$this->p->p->t[$sn]->copts['listInc']}";
//			var_dump("OA_START",$_SESSION['ajaxData']['opts'],"OA_END<hr>");
//			var_dump("A_START",$_SESSION['ajaxData'],"A_END");
			$ret.=$this->p->p->t[$sn]->doInc($ttp,array('echo'=>false));


			die($ret);
		}
		//var_dump($_SESSION['ajaxData']);
		return $ret;	
	}

	function preAdb($act,$data)
	{


		foreach($this->slaves as $sn=>$sv){

			if(isset($data[$sn]['del']) && is_array($data[$sn]['del'])){
				foreach($data[$sn]['del'] as $dn=>$dv){
					unset($data[$sn][$dn]);
				}
			}


			if(isset($data[$sn]['ndel']) && is_array($data[$sn]['ndel'])){
				foreach($data[$sn]['ndel'] as $ddn=>$ddv){
					unset($data[$sn]['new'][$ddn]);
				}

			}
			unset($data[$sn]['ndel']);
		}

//		var_dump("<hr/>",$data,"<hr/>");		
		$this->dataArr=$data;

	}

	function postAdb($act,$id,$rdt,$codata,$ireq)
	{
		if($id==false)
			return false;

		$cdata=$codata['cdata'];
		$odata=$codata['odata'];

		$oreq=$_REQUEST;


		if($act=="d")
		{
			foreach($rdt as $drow){
				foreach($this->slaves as $sn=>$sv){
					unset($sv['new']);
					unset($sv['del']);
					unset($sv['ndel']);
					unset($sv['edit']);

					$mo=$this->p->p;

					$rdt=array();
					$msg="";
					$arid=0;
					$req=array("act_d_{$mo->t[$sn]->tbl}"=>true,"data"=>array("ret.w.{$sv['fld']}"=>$drow[$this->slaves[$sn]['on']],"o.w.id"=>$drow[$this->slaves[$sn]['on']]));

					$this->p->setOpts(array("rdt"=>$mo->db->getRow("select * from {$mo->db_prefix}{$this->p->tbl} where id={$id}")));
					$cV=$this->p->gO('cV');
					$pCV=array();
					if(is_array($cV)){
						if(isset($cV['edit']['_l']["_gui_slave_{$sn}"]) && is_array($cV['edit']['_l']["_gui_slave_{$sn}"])){
							$pCV=$cV['edit']['_l']["_gui_slave_{$sn}"];
						}
					}
					$scV=$mo->t[$sn]->gO("cV");
					$mo->t[$sn]->setCopts(array(
						"cV"=>array_merge_recursive_new($scV,array("acts"=>$cV['acts']),$pCV),
						"gui_slaves_parent_obj"=>$this->p->oname
					));

					$mo->req_nonce_ok=true;

					$mo->adb($req);

					$mo->t[$sn]->resetOpts();
/*
					$_REQUEST['data']=$req;
					$this->p->p->t[$sn]->preAdb($cact);

					$arid=$this->db->act($cact,$sn,$_REQUEST['data'],$msg,$rdt);
//					echo "<br><br><hr>$sn ALLDEL req= $req --- ".$this->db->getLastError()."<hr>";


					$this->p->p->t[$sn]->postAdb($cact,$arid,$rdt);
 */

				}		
			}
			$_REQUEST=$oreq;
			return true;
		}


		if(!is_array($this->dataArr))
			return false;

		$mo=$this->p->p;


		foreach($this->dataArr  as $sn=>$sa){
			if(is_array($this->dataArr[$sn]['new'])){

				foreach($this->dataArr[$sn]['new'] as $newnum=>$req)
				{

					$rmrow=false;
					if(isset($req['multi_row']) && is_array($req['multi_row'])){
						$r_loop=$req['multi_row'];
						$rmrow=true;
					}
					else
						$r_loop=array(0=>$req);

					foreach($r_loop as $rlk=>$rlv){
						$ff=false;
						foreach($rlv['data'] as $fn=>$fv)
						{
							if(strpos($fn,$this->slaves[$sn]['fld'])!==false){
								$ff=true;
								if($rmrow)
									$req['multi_row'][$rlk][$fn]=($this->slaves[$sn]['on']=="id" ? $id : $cdata[$this->slaves[$sn]['on']]);
								else
									$req['data'][$fn]=($this->slaves[$sn]['on']=="id" ? $id : $cdata[$this->slaves[$sn]['on']]);
							}
						}
						if($rmrow){
							if(!$ff)
								$req['multi_row'][$rlk]['data']["r.{$this->slaves[$sn]['fld']}"]=($this->slaves[$sn]['on']=="id" ? $id : $cdata[$this->slaves[$sn]['on']]);
							$req['multi_row'][$rlk]["act_i_{$this->p->p->t[$sn]->tbl}"]=true;
						}else{
							if(!$ff)
								$req['data']["r.{$this->slaves[$sn]['fld']}"]=($this->slaves[$sn]['on']=="id" ? $id : $cdata[$this->slaves[$sn]['on']]);
							$req["act_i_{$mo->t[$sn]->tbl}"]=true;
						}
					}

					$topt=$mo->t[$sn]->gO("sys_files_postfix");
	
		
					$slOpts=array("sys_files_postfix"=>$topt."slave_{$sn}_new_{$newnum}");
					$mo->t[$sn]->changeOpts($slOpts);

					$this->p->setOpts(array("rdt"=>$mo->db->getRow("select * from {$mo->db_prefix}{$this->p->tbl} where id={$id}")));
					$cV=$this->p->gO('cV');
					$cV['rdt']=$mo->db->getRow("select * from {$mo->db_prefix}{$this->p->tbl} where id={$id}");
					$pCV=array();
					if(is_array($cV)){
						if(isset($cV['edit']['_l']["_gui_slave_{$sn}"]) && is_array($cV['edit']['_l']["_gui_slave_{$sn}"])){
							$pCV=$cV['edit']['_l']["_gui_slave_{$sn}"];
						}
					}
					$scV=$mo->t[$sn]->gO("cV");
					$mo->t[$sn]->setCopts(array(
						"cV"=>array_merge_recursive_new($scV,array("acts"=>$cV['acts']),$pCV),
						"gui_slaves_parent_obj"=>$this->p->oname
					));
					
					$mo->req_nonce_ok=true;

					$mo->adb($req);

					$mo->t[$sn]->resetOpts();


/*					$cact="i";
					//					$req['data']['r.lid']=$_REQUEST['data']['r.lid'];
					//					$req['data']['r.lang']=$_REQUEST['data']['r.lang'];
					$req['data']['r.lid']=$ilid;
					$req['data']['r.lang']=$ilang;*/


/*					$_REQUEST=$req;

					$this->p->p->t[$sn]->preAdb($cact,$req);


					$arid=$this->db->act($cact,$sn,$_REQUEST['data'],$msg,$rdt);

					//					echo "<br><br><hr>S=$sn t={$this->tbl} id=$id  arid=$arid msg=$msg NEW ".var_dump($_REQUEST['data'])." msg=$msg ---- Q= ".$this->db->getLastQuery()." <br> ".$this->db->getLastError()."<hr>";

					$this->p->p->t[$sn]->postAdb($cact,$arid,$rdt);
*/
	
					$mo->t[$sn]->changeOpts(array("sys_files_postfix"=>$topt));
/*					echo "<hr> OPT: ";
					var_dump($this->p->p->t[$sn]->gO("sys_files_postfix"));
					echo "<hr>";*/
				}
				unset($this->dataArr[$sn]['new']);
			}

			if(is_array($this->dataArr[$sn]['del'])){
				foreach($this->dataArr[$sn]['del'] as $ddid)
				{

					$req=array("act_d_{$mo->t[$sn]->tbl}"=>true,"data"=>array("ret.w.id"=>$ddid,"o.w.lid"=>$ddid));

					$this->p->setOpts(array("rdt"=>$mo->db->getRow("select * from {$mo->db_prefix}{$this->p->tbl} where id={$id}")));
					$cV=$this->p->gO('cV');
					$pCV=array();
					if(is_array($cV)){
						if(isset($cV['edit']['_l']["_gui_slave_{$sn}"]) && is_array($cV['edit']['_l']["_gui_slave_{$sn}"])){
							$pCV=$cV['edit']['_l']["_gui_slave_{$sn}"];
						}
					}
					$scV=$mo->t[$sn]->gO("cV");
					$mo->t[$sn]->setCopts(array(
						"cV"=>array_merge_recursive_new($scV,array("acts"=>$cV['acts']),$pCV),
						"gui_slaves_parent_obj"=>$this->p->oname
					));

					$mo->req_nonce_ok=true;

					$mo->adb($req);

					$mo->t[$sn]->resetOpts();

/*					$this->p->p->t[$sn]->preAdb($cact,$req);

					$arid=$this->db->act($cact,$sn,$req,$msg,$rdt);
					//					echo "<br><br><hr>$sn SLAVE - DEL --- ".$this->db->getLastError()."<hr>";


					$this->p->p->t[$sn]->postAdb($cact,$ddid,$rdt);
 */
				}
				unset($this->dataArr[$sn]['del']);

			}

		}

		foreach($this->dataArr as $sn=>$sa){
			$onedit=is_array($sa['edit']);
			
			foreach($sa as $ccid=>$req){
				if($ccid=="edit")
					continue;

				$rmrow=false;
				if(isset($req['multi_row']) && is_array($req['multi_row'])){
						$r_loop=$req['multi_row'];
						$rmrow=true;
				}
				else
					$r_loop=array(0=>$req);

				foreach($r_loop as $rlk=>$rlv){
					if($onedit && !in_array($ccid,$sa['edit'])){
						if($rmrow)
							$req['multi_row'][$rlk]=array();
						else
							$req=array();
				}
				

					if($rmrow){
						if($rlk==$this->p->def_lang || !in_array($rlk,$this->p->langs))
							$req['multi_row'][$rlk]["data"]["ret.w.id"]=$ccid;
						else
							$req['multi_row'][$rlk]["data"]["ret.w.lid"]=$ccid;

						$req['multi_row'][$rlk]["act_u_{$mo->t[$sn]->tbl}"]=true;
						}
						else{
							$req["data"]["ret.w.id"]=$ccid;
							$req["act_u_{$mo->t[$sn]->tbl}"]=true;
						}

				}


				$topt=$mo->t[$sn]->gO("sys_files_postfix")."slave_{$sn}_{$ccid}";
				$mo->t[$sn]->setOpts(array("sys_files_postfix"=>$topt));

					$this->p->setOpts(array("rdt"=>$mo->db->getRow("select * from {$mo->db_prefix}{$this->p->tbl} where id={$id}")));
					$cV=$this->p->gO('cV');
					$pCV=array();
					if(is_array($cV)){
						if(isset($cV['edit']['_l']["_gui_slave_{$sn}"]) && is_array($cV['edit']['_l']["_gui_slave_{$sn}"])){
							$pCV=$cV['edit']['_l']["_gui_slave_{$sn}"];
						}
					}
					$scV=$mo->t[$sn]->gO("cV");
					$mo->t[$sn]->setCopts(array(
						"cV"=>array_merge_recursive_new($scV,array("acts"=>$cV['acts']),$pCV),
						"gui_slaves_parent_obj"=>$this->p->oname
					));

					$mo->req_nonce_ok=true;
	
					$mo->adb($req);

					$mo->t[$sn]->resetOpts();


				/*
				$this->p->p->t[$sn]->preAdb($cact,$req);

				if($onedit && in_array($ccid,$sa['edit'])){
				$arid=$this->db->act($cact,$sn,$_REQUEST['data'],$msg,$rdt);
//				echo "<br><br><hr> $sn t={$this->tbl} ccid=$ccid arid=$arid EDIT $msg --- <hr>".$this->db->getLastError()."<hr>";
				}

				$this->p->p->t[$sn]->postAdb($cact,$ccid,$rdt);
				 */

//				$mo->t[$sn]->resetOpts();
				$mo->t[$sn]->changeOpts(array("sys_files_postfix"=>$topt));
				
			}
		}

		$_REQUEST=$oreq;

	
		//if($this->p->tbl=="companies")		var_dump($this->db->getLastQuery());
	//	die($this->db->getLastError());

	}


}
?>
