<?php
require_once("common_funcs.php");
require_once("sys_files.class.php");
require_once("sys_prios.class.php");
require_once("sys_links.class.php");
require_once("sys_m2m.class.php");
require_once("gui_slaves.class.php");



class DB_Obj
{
	public $tbl;
	public $flds;
	public $lcflds;

	public $fctrls;
	public $ctrlsf;
	public $hctrls;
	public $cflds;
	
	public $sys_flds;
	private $curNumRows;
	private $numQ;

	private $codata;



	public $def_lang;
	public $cur_lang;
	public $langs;

	private $opts;
	public 	$copts;
	private $popts;

	public $current;
	public $cC;
	public $cD;
	public $cID;
	private $queryArr;
	private $nextAjaxFlag;
	

	public $curr_id;
	public $rels;
	private $uprels;


	public $sys_files;
	public $sys_prios;
	public $sys_links;

	public $p;
	private $skin;

	public $db;

	public $gui;

	private $ajaxData;
	private $ajaxOpts;
	public 	$defs;
	public $llcnt=0;
	public $oname="";

	public $obj_slug=false;
	public $slug_field=false;
	public $slug_prefix=false;

	function __construct(&$db,$ioname,$cfg,$icur_lang,$p)
	{
		$this->virt=false;
		$this->queryArr=false;
		$this->cID=false;
		$this->nextAjaxFlag=false;

		$this->popts=array();
		$this->copts=array();
		$this->ajaxData=array();
		$this->ajaxOpts=array();
		$this->hctrls=array();
		$this->gui=array();
		$this->cflds=array();
		$this->codata=array();
		$this->db=$db;
		$this->oname=$ioname;
		$this->current=false;
		$this->currentArr=array();
		$this->curr_id=0;

		$this->uprels=false;

		$this->defs=array('textarea'=>array("cols"=>20,"rows"=>5));

		
		$this->opts=array(
			"popupEdit"=>false,
			"ownHeader"=>false,
			"user_ownHeader"=>false,

			"userTpl"=>"def_".$ioname,

			"listTpl"=>"def",
			"editTpl"=>"def_edit",
			"popupTpl"=>"def",
			"listInc"=>"def_list",
			"editInc"=>"def_edit",

			"retDefLang"=>false,
			"echo"=>true,
			"echo_INS"=>true,
			"false"=>false,

			"adminOnlineEdit"=>"offpage",
			"adminListFlds"=>false,
			"adminExFlds"=>array(),
			"adminOneItemTitle"=>$ioname,
			"adminMultyItemTitle"=>$ioname,

			"queryPrefix"=>"main",
			"queryWhere"=>false,
			"queryOrder"=>false,
			"queryLimit"=>false,
			"queryGroup"=>false,
			"rowsPerPage"=>false,

			"sys_prios"=>false,
			"sys_files"=>false,
			"sys_links"=>false,
			"sys_m2m"=>false,

			"sel_titleLen"=>false,
			'sel_titleMinLen'=>1,
			"sel_titleTags"=>false,
			"sel_idFld"=>"ct.id",
			"sel_titleFld"=>"ct.name",
			"sel_sortOrd"=>"ASC",
			"sel_sortQ"=>false,
//			"sel_sortFld"=>false,

			"previewLen"=>25,
			"previewMinLen"=>5,
			"previewStriptags"=>true,

			'sys_prios_titleFld'=>'name',
			'sys_prios_idFld'=>'id',

			'user_form_names'=>false,

			'linkedFields'=>false,
			
			'noDefColumns'=>false,

				'sf_table' => false,
		);

		global $dbo_def_opts;
		if(is_array($dbo_def_opts) && count($dbo_def_opts)>0)
			$this->opts=array_merge($this->opts,$dbo_def_opts);
		
		if(isset($cfg['virt']) && is_array($cfg['virt'])){
			$this->virt=$cfg['virt'];
		}


		$this->p=$p;

		
		$this->tbl=$this->oname;
		if(isset($cfg['table']))
			$this->tbl=$cfg['table'];
		
		$this->fctrls=array();
		if(isset($cfg['fctrls']))
			$this->fctrls=$cfg['fctrls'];

		if(count($this->fctrls)==0 && is_array($this->virt) && count($this->virt)>0){
			$t=reset($this->virt);
			$tk=array_keys($t);
			foreach($tk as $kv){
				$this->fctrls[$kv]=array("c"=>"string","t"=>$kv);
			}
		}

		if(isset($cfg['obj_slug']) && $cfg['obj_slug']!=false)
			$this->obj_slug=$cfg['obj_slug'];
		else
			$this->obj_slug=$this->oname;

		if(isset($cfg['slug_field']) && $cfg['slug_field']!=false)
			$this->slug_field=$cfg['slug_field'];
		else
			$this->slug_field = "id";

		if(isset($cfg['slug_prefix']) && $cfg['slug_prefix']!=false)
			$this->slug_prefix=$cfg['slug_prefix'];
/*		else
			$this->slug_prefix="APP";
 */
		if((!isset($cfg['fields']) || !is_array($cfg['fields']) || count($this->fctrls)==0)){
			if(count($this->fctrls)>0)
				$this->flds=array_keys($this->fctrls);
			else
				$this->flds=array();

		}else
			$this->flds=$cfg['fields'];

		$this->sys_flds=array("id","lang","lid");
		$this->def_lang=isset($cfg['def_lang'])?$cfg['def_lang']:$icur_lang;
		$this->cur_lang=$icur_lang;
		$this->langs=$cfg['langs'];
		
		$this->curNumRows=false;
		$this->numQ=false;

		if(is_array($this->fctrls)){
			$tfu=array();
			foreach($this->fctrls as $n=>$v){
				$this->ctrlsf[$v['c']][$n]=$v;
				if($v['c']=='fileurl'){
					$tfu["{$n}_fileurl"]=array("c"=>"file","t"=>$v['t'],"helpFor"=>$n,"autoResize"=>false,"onNew"=>true,"admShow"=>true);
				}
			}
			if(count($tfu)>0){
			if(!is_array($this->opts['sys_files'])){
				$this->opts['sys_files']=array("path"=>"files/","url"=>"files/");
			}
			if(isset($this->opts['sys_files']['types']))
				$this->opts['sys_files']['types']=array_merge($this->opts['sys_files']['types'],$tfu);
			else
				$this->opts['sys_files']['types']=$tfu;

			}

		}


		if(is_array($cfg['opts']))
			$this->opts=array_merge($this->opts,$cfg['opts']);

		if(!isset($cfg['opts']["sel_sortFld"]))
		$this->opts["sel_sortFld"]=$this->opts["sel_titleFld"];		

		$this->rels=$cfg['rels'];
		$this->uprels=$cfg['uprels'];

		$this->db_res=$this->oname;

/*		if($this->gO("user_form_names")){
			$this->_restoreSecFormNames();
}*/

		if(is_array($this->opts['sys_files']) && isset($this->opts['sys_files']['path']))
			$this->sys_files=new sys_files($this->db,$this->opts['sys_files'],$this);

		if(is_array($this->opts['sys_prios']))
			$this->sys_prios=new sys_prios($this->db,$this->opts['sys_prios'],$this);

		if(is_array($this->opts['sys_links']))
			$this->sys_links=new sys_links($this->db,$this->opts['sys_links'],$this);

		if(is_array($this->opts['sys_m2m']))
			$this->sys_m2m=new sys_m2m($this->db,$this->opts['sys_m2m'],$this);

		if(is_array($cfg['gui'])){
			foreach($cfg['gui'] as $gn=>$go){
				$guicn="gui_$gn";
				$this->gui[$gn]=new $guicn($this->db,$go,$this);
			}
		}

		$this->copts=$this->opts;


//autoCtrls START----------------------------------------
			{
				if(!is_array($this->fctrls))
					$this->fctrls=array();
				$tfctrls=array();
//				$tfctrls=$this->fctrls;


				if(is_object($this->sys_m2m) && $this->sys_m2m->autoCtrlChkArr(true))
					$tfctrls=array_merge($tfctrls,$this->sys_m2m->autoCtrlArr(true));

				if(is_object($this->sys_links) && $this->sys_links->autoCtrlChkArr(true))
					$tfctrls=array_merge($tfctrls,$this->sys_links->autoCtrlArr(true));

				if(is_object($this->sys_prios) && $this->sys_prios->autoCtrlChkArr(true))
					$tfctrls=array_merge($tfctrls,$this->sys_prios->autoCtrlArr(true));

				if(is_object($this->sys_files) && $this->sys_files->autoCtrlChkArr(true))
					$tfctrls=array_merge($tfctrls,$this->sys_files->autoCtrlArr(true));
				

				foreach($this->gui as $go)
				{
					if(is_object($go) && $go->autoCtrlChkArr(true))
						$tfctrls=array_merge($tfctrls,$go->autoCtrlArr(true));
				}
				foreach($this->flds as $fn)
				{
					$this->lcflds[strtolower($fn)]=$fn;
					if(!isset($this->fctrls[$fn])){
						$fcd="text";
						if(is_array($this->rels) && array_key_exists($fn,$this->rels))
							$fcd="select";
						if(is_array($this->uprels) && array_key_exists($fn,$this->uprels)){
//							$this->hctrls[$fn]=$tfctrls[$fn];
							$this->hctrls[$fn]=$this->fctrls[$fn];
						}else
							$tfctrls[$fn]=array("c"=>$fcd,"t"=>"${fn}:");
					}
					else
						if($this->fctrls[$fn]['c']=="hidden")
							$this->hctrls[$fn]=$this->fctrls[$fn];
						else
							$tfctrls[$fn]=$this->fctrls[$fn];

					if(is_object($this->sys_m2m) && $this->sys_m2m->autoCtrlChkArr($fn))
						$tfctrls=array_merge($tfctrls,$this->sys_m2m->autoCtrlArr($fn));
					if(is_object($this->sys_links) && $this->sys_links->autoCtrlChkArr($fn))
						$tfctrls=array_merge($tfctrls,$this->sys_links->autoCtrlArr($fn));
					if(is_object($this->sys_prios) && $this->sys_prios->autoCtrlChkArr($fn))
						$tfctrls=array_merge($tfctrls,$this->sys_prios->autoCtrlArr($fn));
					if(is_object($this->sys_files) && $this->sys_files->autoCtrlChkArr($fn))
						$tfctrls=array_merge($tfctrls,$this->sys_files->autoCtrlArr($fn));
					foreach($this->gui as $go)
					{
						if(is_object($go) && $go->autoCtrlChkArr($fn))
							$tfctrls=array_merge($tfctrls,$go->autoCtrlArr($fn));
					}
				}

				if(is_object($this->sys_m2m) && $this->sys_m2m->autoCtrlChkArr(false))
					$tfctrls=array_merge($tfctrls,$this->sys_m2m->autoCtrlArr(false));
				if(is_object($this->sys_links) && $this->sys_links->autoCtrlChkArr(false))
					$tfctrls=array_merge($tfctrls,$this->sys_links->autoCtrlArr(false));
				if(is_object($this->sys_prios) && $this->sys_prios->autoCtrlChkArr(false))
					$tfctrls=array_merge($tfctrls,$this->sys_prios->autoCtrlArr(false));
				if(is_object($this->sys_files) && $this->sys_files->autoCtrlChkArr(false))
					$tfctrls=array_merge($tfctrls,$this->sys_files->autoCtrlArr(false));

				foreach($this->gui as $go)
				{
					if(is_object($go) && $go->autoCtrlChkArr(false))
						$tfctrls=array_merge($tfctrls,$go->autoCtrlArr(false));
				}

				//		if($this->tbl=="provinces"){		var_dump($tfctrls);die("OKKKKKK");			}

				if(is_array($this->downrels)){
					foreach($this->downrels as $dtn=>$dtv)
						$tfctrls["downrels_{$dtn}"]=array("c"=>'downrels',"tbl"=>$dtn);
				}


				$this->fctrls=$tfctrls;
				if(!is_array($this->opts["adminListFlds"])){
					$this->opts["adminListFlds"]=array_keys($tfctrls);
					foreach($this->opts["adminListFlds"] as $k=>$v){
						if($this->fctrls[$v]['c']=="gui_slaves")
							unset($this->opts["adminListFlds"][$k]);
					}

					if($this->gOA("adminExFlds"))
						$this->opts["adminListFlds"]=array_diff($this->opts["adminListFlds"],$this->opts["adminExFlds"]);
					$this->copts=$this->opts;
				}

				$this->resetOpts();
			}
//autoCtrls ---------------------------------------- END

/*		if (isset($_REQUEST['_sffilter'][$this->oname])) {
			if (!is_array($_SESSION['_sffilter']))
				$_SESSION['_sffilter'] = array();
			$_SESSION['_sffilter'][$this->oname] = $_REQUEST['_sffilter'][$this->oname];
		}
		if (isset($_SESSION['_sffilter'][$this->oname])) {
			$this->setOpts(array("_sffilterID" => $_SESSION['_sffilter'][$this->oname]));
		}
*/
		if (is_array($_SESSION['_storedOpts'][$this->oname]))
			$this->setOpts($_SESSION['_storedOpts'][$this->oname]);



		if(isset($_REQUEST['ajaxID']) && isset($_SESSION['ajaxData'])){
			$a=$this->gAO();
			if(is_array($a))
				$this->changeOpts($a);
		}


		if($_REQUEST['a']!='p_emptyimg' && !isset($_REQUEST['ajaxID']) && !isset($_REQUEST['ajax']) && isset($_SESSION['ajaxData']) && !isset($_REQUEST['popup'])){
			unset($_SESSION['ajaxData']);
		}

		$this->skin=$this->p->skin;
	}

	function get($n){
		if(isset($this->{$n}))
			return $this->{$n};	
	}

	function eF()
	{
		$this->copts['prev_echo']=$this->copts['echo'];
		$this->copts['echo']=false;
	}

	function eFA()
	{
		$this->copts['prev_echo']=$this->copts['echo'];
		$this->copts['prev_echo_INS']=$this->copts['echo_INS'];
		$this->copts['echo_INS']=$this->copts['echo']=false;

	}

	function eT()
	{
		$this->copts['echo']=true;
	}
	function eTA()
	{
		$this->copts['echo_INS']=$this->copts['echo']=true;
	}

	function eB()
	{
		$this->copts['echo']=$this->copts['echo'];
		$this->copts['echo_INS']=$this->copts['echo_INS'];
		if(isset($this->copts['prev_echo_INS'])){
			$this->copts['echo_INS']=$this->copts['prev_echo_INS'];
			unset($this->copts['prev_echo_INS']);
		}
		if(isset($this->copts['prev_echo'])){
			$this->copts['echo']=$this->copts['prev_echo'];
			unset($this->copts['prev_echo']);
		}
	}

	function eD()
	{
		$this->copts['echo']=$this->opts['echo'];
		$this->copts['echo_INS']=$this->opts['echo_INS'];
	}

	function delOpts($iopts)
	{
		if(!is_array($iopts))
			$iopts=array($iopts);
		foreach($iopts as $dk){
		unset($this->opts[$dk]);
		unset($this->copts[$dk]);
		foreach($this->popts as $pk=>$pv)
			unset($this->popts[$pk][$dk]);
		}


	}

	function addOpts($iopts)
	{
		if(!is_array($iopts))
			return false;

		$narr=array();
		foreach($iopts as $k=>$v)
		{
			$cv=$this->gO($k);
			if(!$cv)
				$narr[$k]=$v;
			else if(!is_array($cv))
				$narr[$k]=array($cv,$v);
			else if(is_array($v))
				$narr[$k]=array_merge($cv,$v);
			else{
				$narr[$k]=$cv;
				$narr[$k][]=$v;
			}
		}

		if(count($narr)>0)
			$this->changeOpts($narr);
	
	}
	
	function gO($on) // getCopt
	{
		if(isset($this->copts[$on]))
			return $this->copts[$on];
		else
			return false;
	}

	
	function gOA($on) // getCopt as Array
	{
		if(isset($this->copts[$on]))
			if(!is_array($this->copts[$on]))
				return array($this->copts[$on]);
			else
				return $this->copts[$on];
		else
			return false;
	}
	function gOAA($on) // getCopt as Array even if false
	{
		$r=$this->gOA($on);	
		return (is_array($r)?$r:array());
	}

	function changeOpts($iopts)
	{
		if(!is_array($iopts))
			debug_print_backtrace();
		if(isset($iopts['opts_replace']) && $iopts['opts_replace']==true){
			unset($iopts['opts_replace']);
			$this->opts=array_merge($this->opts,$iopts);
			$this->copts=array_merge($this->copts,$iopts);
			foreach($this->popts as $pk=>$pv)
				$this->popts[$pk]=array_merge($pv,$iopts);
		}else{
			$this->opts=array_merge_recursive_new($this->opts,$iopts);
			$this->copts=array_merge_recursive_new($this->copts,$iopts);
			foreach($this->popts as $pk=>$pv)
				$this->popts[$pk]=array_merge_recursive_new($pv,$iopts);
		}
	}
	function setOptsR($iopts){
		if(!is_array($iopts))
			debug_print_backtrace();
		$iopts['opts_replace']=true;
		$this->changeOpts($iopts);
	}
	function setOpts($iopts){
		$this->changeOpts($iopts);
	}

	function resetOpts($todef=false,$iopts=array())
	{
//		echo "<hr>RESET:".count($this->popts)."<hr>";
		if($todef){
			$this->copts=$this->opts;
			$this->popts=array();
		}
		else if(count($this->popts)>0){
			$this->copts=array_pop($this->popts);
			$this->getQuery();
		}

		$this->copts=array_merge($this->copts,$iopts);
	}

	function setCoptsR($iopts=array()){
		if(!is_array($iopts))
			$iopts=array();
		$iopts['opts_replace']=true;
		$this->setCopts($iopts);

	}
	function setCopts($iopts=array())
	{
		if(!is_array($iopts))
			$iopts=array();
		array_push($this->popts,$this->copts);
		if(isset($iopts['opts_replace']) && $iopts['opts_replace']==true){
			unset($iopts['opts_replace']);
			$this->copts=array_merge($this->copts,$iopts);
		}else
			//$this->copts=array_merge_recursive_new($this->copts,$iopts);
			$this->copts = array_merge($this->copts, $iopts);

//		echo "<hr>SET:".count($this->popts)."<hr>";
		
	}

	function queryRels($irels=false,$iopts=array())
	{
		$this->setCopts($iopts);

		$rels=array();
		$sys_rels=array();
		if($irels==false)
			$trels=$this->rels;
		else{
			foreach($irels as $r){
				if(array_key_exists($r,$this->rels))
					$trels[$r]=$this->rels[$r];
			}
		}
		if($qorels=$this->gO("queryRels"))
			$trels=array_merge($trels,$qorels);
		


		if($this->copts["sys_files"]){
			foreach($this->copts["sys_files"]['types'] as $tn=>$tv){
				if($tv['c']!="gallery")
					$trels["{$tv['c']}_$tn"]=array("tbl"=>"sys_files","tbln"=>"sys_files_".$tn,"on"=>array("vals"=>array("ftype"=>"{$tv['c']}_$tn","tbl"=>$this->tbl),"flds"=>array("id"=>"tid")));
			}
		}


		if ($this->gOA("sys_m2m_add_rels")) {

			$mmta = $this->gOAA("sys_m2m_add_rels");

			foreach($mmta as $mmt)
			{

//var_dump($mmt,$this->tbl,is_object($this->p->t[$mmt]->sys_m2m),$this->p->t[$mmt]->sys_m2m->check($this->tbl));
				if(is_object($this->sys_m2m) && $this->sys_m2m->check($mmt)){
					$this->sys_m2m->addRels($trels);
				}else	if(is_object($this->p->t[$mmt]->sys_m2m) && $this->p->t[$mmt]->sys_m2m->check($this->tbl)){
					$this->p->t[$mmt]->sys_m2m->addRels($trels,$this->tbl);
				}
			}
		}

		
		if(is_object($this->gui["slaves"]) && $this->gO("query_slaves")){
			$this->gui["slaves"]->addRels($trels);
		}

		if($this->copts["sys_prios"]){
					$trels["sys_prios"]=array("tbl"=>"sys_prios","tbln"=>"sys_prios","on"=>array("vals"=>array("tbl"=>$this->tbl),"flds"=>array("id"=>"tid","lid"=>"tid"),"block"=>array("flds"=>true,"vals"=>false),"op"=>array("lid"=>"or")));
		}


		$ret="";

		$rtbls=array("ct"=>$this->tbl);

		if($trels){
			foreach($trels as $rf=>$ra){
				if (isset($ra['obj']) && is_object($this->p->t[$ra['obj']]) && $this->p->t[$ra['obj']]->gO('sf_table') == true) {
					$ra['tbln'] = preg_replace("#__c$#", "__r", $rf);
				}

				if(!isset($ra['tbln']))
					$ra['tbln']="{$ra['tbl']}_{$rf}";
				if(!isset($ra['join']) || $ra['join']==false)
					$ra['join']="left join";
				if(!isset($ra['ctbl']) || $ra['ctbl']==false)
					$ra['ctbl']="ct";

				$rtbls[$ra['tbln']]=$ra['tbl'];

				if(isset($ra['to_flds']) &&  $ra['to_flds']!=false)
					$this->addOpts(array("__queryFields"=>"{$ra['tbln']}.{$ra['fld']} as r_{$ra['to_flds']}"));
 
				if(is_array($ra['on'])){
					$tton="";
					$f=true;
				if(is_array($ra['on']['flds'])){
					foreach($ra['on']['flds'] as $ctf=>$otf){
						if($f){	$ttop=(isset($ra['on']['block']['flds'])&&$ra['on']['block']['flds']==true?"(":"");$f=false; } 
						else
							 $ttop= (isset($ra['on']['op'][$ctf]) ? $ra['on']['op'][$ctf] : "and");
						$tton.=" $ttop {$ra['ctbl']}.$ctf={$ra['tbln']}.{$otf}";
					}
					$tton.=(isset($ra['on']['block']['flds'])&&$ra['on']['block']['flds']==true&&$tton!=""?")":"");
				}
				if(is_array($ra['on']['vals'])){
					foreach($ra['on']['vals'] as $ctf=>$otf){
						if($ttop=(isset($ra['on']['block']['vals'])&&$ra['on']['block']['vals']==true?"(":"")){	$ttop="";$f=false; } 
						else
							 $ttop= (isset($ra['on']['op'][$ctf]) ? $ra['on']['op'][$ctf] : "and");
						$tton.=" $ttop {$ra['tbln']}.$ctf='$otf'";
					}
					$tton.=(isset($ra['on']['block']['vals'])&&$ra['on']['block']['vals']==true&&$tton!=""?")":"");
				}
					$ret.=" {$ra['join']} {$ra['tbl']} as {$ra['tbln']} on ($tton)";

				}
				else
					$ret.=" {$ra['join']} {$ra['tbl']} as {$ra['tbln']} on {$ra['ctbl']}.{$rf}={$ra['tbln']}.{$ra['on']}";
			}
		}else{
			$this->resetOpts();
			return "";
		}

		$aso=array();

		if($so=$this->gO("querySearchAll"))
		{
			foreach($rtbls as $tn=>$t)
			{
				if(is_object($this->p->t[$t]))
					$ct=$this->p->t[$t];
				else
					continue;

				foreach($ct->flds as $f)
					$aso[]=" {$tn}.{$f} $so ";
			}
			$this->changeOpts(array("querySearchAllArr"=>$aso));
		}

		$this->resetOpts();
		return $ret;


	}

	function queryFields($iflds=false,$irels=false,$iopts=array())
	{
		$this->setCopts($iopts);

		$trels=array();
		$tflds=array();
		$sys_rels=array();
		if($irels==false)
			$trels=$this->rels;
		else{
			foreach($irels as $r){
				if(array_key_exists($r,$this->rels)){
					$trels[$r]=$this->rels[$r];
				}
			}
		}
		if($iflds==false)
			$tflds=$this->flds;
		else
			$tflds=$iflds;

		if(!$this->gO("noDefColumns"))
			$tflds=array_merge($tflds,$this->sys_flds);

		$reta=array();
		foreach($tflds as $f)
			$reta[]="ct.$f";
		if($trels){
			foreach($trels as $rf=>$ra){
				if (isset($ra['obj']) && is_object($this->p->t[$ra['obj']]) && $this->p->t[$ra['obj']]->gO('sf_table') == true) {
					$ra['tbln'] = preg_replace("#__c$#", "__r", $rf);
				}

				if(!isset($ra['tbln']))
					$ra['tbln']="{$ra['tbl']}_{$rf}";

				if(in_array($rf,$tflds)){
					$reta[]="{$ra['tbln']}.{$ra['fld']} as r_{$rf}";
					if(isset($this->fctrls[$rf]['sfdata']['st']) && $this->fctrls[$rf]['sfdata']['st']=='tns:ID')
						$reta[]="ct.{$rf}_slug";
				}
			}
		}

		if($oqff=$this->gOA("__queryFields"))
			$reta=array_merge($reta,$oqff);

		if($oqff=$this->gOA("queryFields"))
			$reta=array_merge($reta,$oqff);

		if($this->copts["sys_files"]){
			foreach($this->copts["sys_files"]['types'] as $tn=>$tv){

				if($tv['c']!="gallery"){
					$reta[]="CONCAT('{$this->sys_files->url}',sys_files_{$tn}.full) as {$tv['c']}_$tn";

					$reta[]="sys_files_{$tn}.full as sys_files_{$tv['c']}_$tn";

/*					if($tv['thumb']!=false)
	$reta[]="CONCAT('{$this->sys_files->url}t',sys_files_{$tn}.full) as {$tv['c']}_{$tn}_t"; */
				}
			}

		}
	if($this->copts["sys_prios"]){
				$reta[]="sys_prios.prio as sys_prios_prio";
				$reta[]="sys_prios.parent as sys_prios_parent";
	}
		if ($this->gOA('query_sfRels') != false) {
			$_ra = $this->gOA('query_sfRels');
			foreach ($_ra as $_sff){
				$_sffT=$_sff;
				$_sffA=explode(".",$_sff);
				if(count($_sffA)>0 && strtolower($_sffA[0])==strtolower($this->oname)){
					array_shift($_sffA);
					$_sffT=implode('.',$_sffA);
				}

				if(count($_sffA)>1){
					if(!in_array($_sffA[0],array("sys_m2m"))){
						$_sffA[0]=$this->rels[str_replace("__r","__c",$_sffA[0])]['tbln'];
						$_sffN=$_sffA[count($_sffA)-1];
						unset($_sffA[count($_sffA)-1]);
/*						foreach($_sffA as $_sffAK=>$_sffAV){
							if(isset($this->rels[str_replace("__r","__c",$_sffAV)]['tbln']))
								$_sffA[$_sffAK]=$this->rels[str_replace("__r","__c",$_sffAV)]['tbln'];
						}*/						
						$reta[] = implode("__X__",$_sffA).".{$_sffN} as " . str_replace(".", "__x__", $_sff);
					}
				}else{
					$reta[] = str_replace("__c.", "__r.", $_sffT) . " as " . str_replace(".", "__x__", $_sff);
				}
			}
		}
	

		$this->resetOpts();
		return implode(",",$reta);

	}

	function popupEdit($iopts=array())
	{
		$this->setCopts($iopts);
		$ret="";
		if($this->copts['popupEdit'])
		{
			$ucd="";
			$aucd=$this->gO("_use_cD");
			if($aucd){
				$ucd="&_ucdo={$aucd['o']}&_ucdc={$aucd['c']}";
			}
/*			$ret="<span><input type='button' value='Add' onclick=\"p=jQuery(this).parent();p.find('a.add_button_a').trigger('click');\">
	<a style='display:none' class='iframe add_button_a' href='index.php?popup=1&a=dbo_{$this->oname}{$ucd}'>&nbsp;</a></span>";*/
			$ret="<input type='button' value='Add' onclick=\"jQuery.fancybox({type:'iframe',href:'index.php?popup=1&a=dbo_{$this->oname}{$ucd}'});\">";
			if($this->copts['echo'])
				echo $ret;
			else{
				$this->resetOpts();
				return $ret;
			}
		}
		$this->resetOpts();
	}
	function aL($url,$urldata=false,$data=array(),$iopts=array()){
		return $this->ajaxLink($url,$urldata,$data,$iopts);
	}
	function ajaxLink($url,$urldata=false,$iopts=array()){
		$this->setCopts($iopts);
		$ret="";

		$udata=$urldata;
		if(is_array($urldata))
			$udata=implode(",",$urldata);

		$ddid=(string)(rand()%100).time().rand()%100;

		$_SESSION['ajaxData'][$ddid][$this->oname]=$this->ajaxData;
		$_SESSION['ajaxData']['opts'][$ddid][$this->oname]=$this->ajaxOpts;


		if($udata==false )
			$udata="ajaxID=$ddid";
		else
			$udata="ajaxID={$ddid}&".$udata;
	
		$ret="'{$url}?{$udata}'";
		if(isset($this->copts['ajax_Method']) && strtolower($this->copts['ajax_Method'])=="post")
				$ret="'{$url}','{$udata}'";

			if($this->copts['echo_INS'])
				echo $ret;
			else{
			$this->resetOpts();				
				return $ret;
			}
			$this->resetOpts();		


	}


	function isAjax(){
		return (isset($_REQUEST['ajaxID']) && $_REQUEST['ajaxID']!=false ? true : false);
	}


	function gA($rd=false){
		return $this->getAjaxData($rd);
	}

	function gAO($rd=false){
		return $this->getAjaxOpts($rd);
	}

	function getAjaxData($rd=false)
	{
/*		if(isset($_REQUEST['ajaxID']) && isset($_SESSION['ajaxData'][$this->tbl]) && isset($_SESSION['ajaxData'][$this->tbl][$_REQUEST['ajaxID']])){

			if(!$rd)
				$t=$_SESSION['ajaxData'][$this->tbl][$_REQUEST['ajaxID']];
			else if(isset($_SESSION['ajaxData'][$this->tbl][$_REQUEST['ajaxID']][$rd])){
				$t=$_SESSION['ajaxData'][$this->tbl][$_REQUEST['ajaxID']][$rd];
			}
			else
				$t=false;
 */
		if(isset($_REQUEST['ajaxID']) && isset($_SESSION['ajaxData']) && isset($_SESSION['ajaxData'][$_REQUEST['ajaxID']])){

			if(!$rd)
				$t=$_SESSION['ajaxData'][$_REQUEST['ajaxID']][$this->oname];
			else if(isset($_SESSION['ajaxData'][$_REQUEST['ajaxID']][$this->oname][$rd])){
				$t=$_SESSION['ajaxData'][$_REQUEST['ajaxID']][$this->oname][$rd];
			}
			else
				$t=false;
 
	
		//		unset($_SESSION['ajaxData'][$_REQUEST['ajaxID']]);



			return $t;
		}else{
			return false;
		}
		
	}

	function getAjaxOpts($rd=false)
	{
		if(isset($_REQUEST['ajaxID']) && isset($_SESSION['ajaxData']['opts']) && isset($_SESSION['ajaxData']['opts'][$_REQUEST['ajaxID']])){
//var_dump("OK",$_SESSION['ajaxData']['opts'],"END");
			if(!$rd)
				$t=$_SESSION['ajaxData']['opts'][$_REQUEST['ajaxID']][$this->oname];
			else if(isset($_SESSION['ajaxData']['opts'][$_REQUEST['ajaxID']][$this->oname][$rd])){
				$t=$_SESSION['ajaxData']['opts'][$_REQUEST['ajaxID']][$this->oname][$rd];
			}
			else
				$t=array();

			return $t;
		}else{
			return array();
		}
		
	}




	function sA($rn,$rv){
		$this->setAjaxData($rn,$rv);
	}

	function sAO($rn,$rv){
		$this->setAjaxOpts($rn,$rv);
	}

	function sAOA($rv){
		$this->setAjaxOptsArray($rv);
	}

	function setAjaxOpts($rn,$rv)
	{
		$this->ajaxOpts[$rn]=$rv;	
	}
	function setAjaxOptsArray($rv)
	{
		$this->ajaxOpts=array_merge($this->ajaxOpts,$rv);
	}



	function setAjaxData($rn,$rv)
	{
		$this->ajaxData[$rn]=$rv;	
	}
	function sAC($rn,$rv){
		if(isset($_SESSION['ajaxData'][$_REQUEST['ajaxID']]))
			$_SESSION['ajaxData'][$_REQUEST['ajaxID']][$rn]=$rv;
	}

	function ff($fn) // fix field case (from lower to original)
	{
		if(is_string($fn)){
			$tfn=strtolower($fn);
			if(isset($this->lcflds[$tfn]))
				return $this->lcflds[$tfn];
		}
		return $fn;
	}

	function sel($sel=false,$iopts=array())
	{
		$ret="";
		$tret="";
		$this->setCopts($iopts);
		$j="";
		$o="";
		$cV=$this->gO("cV");
		if(is_array($this->copts['sys_prios']))
		{
			$j=" left join {$this->p->db_prefix}sys_prios as sys_prios on (sys_prios.tbl='{$this->tbl}' and sys_prios.tid={$this->copts['sel_idFld']}) ";		
			$o=" sys_prios.prio asc, ";
		}else if($this->gO("sel_join"))
			$j=$this->gO("sel_join");
		if(isset($cV['user_filter']) && $this->gO("ignore_user_filter")==false && isset($this->p->curUsr)){

			$fltarr=array();
			foreach($cV['user_filter'] as $k=>$v){
				if(isset($this->p->curUsr[$v]))
					$fltarr[]="{$k}='{$this->p->curUsr[$v]}'";
				else if (is_numeric($k)) {
					preg_match_all("#_f_([a-z0-9_]+)#mi", $v, $gar);
					if (is_array($gar[1]) && count($gar[1]) > 0) {
						foreach ($gar[1] as $gv)
							if (isset($this->p->curUsr[$gv]))
								$v = str_replace("_f_{$gv}", $this->p->curUsr[$gv], $v);
					}
					$fltarr[] = $v;
				} else
					$fltarr[] = "{$k}='{$v}'";
			}
			if(count($fltarr)>0){
				if($this->copts['sel_where']!=""){
					if(!(isset($this->p->u) && isset($this->p->u['tbl']) && $this->tbl==$this->p->u['tbl'] 
						&& isset($_REQUEST['rid']) && $_REQUEST['rid']==$this->p->curUsr['id']))
						$this->copts['sel_where']=" (".implode(" and ",$fltarr).") and ({$this->copts['sel_where']})";
				}else
					$this->copts['sel_where']="(".implode(" and ",$fltarr).") ";
			}
		}
		else if(!$this->gO("noViews") && !(isset($cV['user_filter']) && is_array($cV['user_filter'])) && ($cV==false || !isset($cV['no_user_filter_allowed']) || $cV['no_user_filter_allowed']==false ))
			return false;

		$idFn=explode(".",$this->copts['sel_idFld']);
		$idFn=$this->ff($idFn[count($idFn)-1]);
		$titFn=explode(".",$this->copts['sel_titleFld']);
		$titFn=$this->ff($titFn[count($titFn)-1]);

		$cq="select {$this->copts['sel_idFld']} as sel_id,{$this->copts['sel_titleFld']} as sel_name, ct.lang as lang 
			from {$this->p->db_prefix}{$this->tbl} ct $j  ".(isset($this->copts['sel_where']) && $this->copts['sel_where']!="" ? " where {$this->copts['sel_where']}" : "");


		if($this->copts['sel_sortQ']===false)
			$cq.=" order by {$o} {$this->copts['sel_sortFld']} {$this->copts['sel_sortOrd']}";
		else
			$cq.=" order by {$o} {$this->copts['sel_sortQ']}";
		$this->db->query($cq,"{$this->db_res}_sel");
//echo "$cq:".$this->db->getLastError();
		$jsret=array();
		$arrret=array();

		while($row=$this->db->next("{$this->db_res}_sel"))
		{
      if($row['lang']!=$this->cur_lang)
				continue;
			if( ! ($this->gO('sel_no_dV') || (isset($this->fctrls[$titFn]['_no_dV']) && $this->fctrls[$titFn]['_no_dV']!=false)))

				$row['sel_name']=$this->drawValue($titFn,array($titFn=>$row['sel_name']),$this->def_lang);

			if($this->copts['sel_titleTags']==true)
				$row["sel_name"]=strip_tags($row['sel_name']);
//				$row["sel_name"]=preg_replace("#&.{1,6};#mis","",strip_tags($row['sel_name']));

			if($this->copts['sel_titleLen']!==false && strlen($row["sel_name"])>$this->copts['sel_titleLen']){
				preg_match("#^.{{$this->copts['sel_titleMinLen']},{$this->copts['sel_titleLen']}}.*?(\.|,|:| |)#imsu",$row["sel_name"],$tit);
				$row["sel_name"]=$tit[0];
			}

			if($this->copts['json']==true){
				$jsret['val'][]=$row[$this->copts['sel_idFld']];
				$jsret['name'][]=$row[$this->copts['sel_titleFld']];
			}else	if($this->copts['selArray']==true){
				$arrret[$row['sel_id']]=$row['sel_name'];
			}else
			$tret="<option value=\"{$row['sel_id']}\" ".($sel===$row['sel_id']?"selected=\"true\"":"").">{$row['sel_name']}</option>";



			if($this->copts['echo']==true)
				echo $tret;
			else
				$ret.=$tret;
			
		}
			if($this->copts['selArray']==true && count($arrret)>0)
				return $arrret;

		if($this->copts['json']==true)
			die("{\"val\":\"[".implode("\",\"",$jsret['val'])."\"],name:[\"".implode("\",\"",$jsret['name'])."\"]}");
		if($this->copts['echo']==false){
			$this->resetOpts();
			return $ret;
		}
			$this->resetOpts();
	}

	function setCurrent($irid,$iopts=array())
	{
		global $cC,$cD,$cID,$cR;
//		global $sys_def_date_format,$sys_def_time_format,$sys_def_datetime_format;
		$cC=$cD=$cID=$cR=false;
		$this->setCopts($iopts);

		if($irid=='new'){
			if(is_array($_REQUEST['_new_defs'])){
				$dntmp=array();
				foreach($_REQUEST['_new_defs'] as $dfn =>$dfv){
					if(strtolower($dfn)!='id' && in_array($dfn,$this->flds))
						$dntmp[$dfn]=$dfv;
				}
						$cR=$this->current=$dntmp;
						$cC=$this->cC=$dntmp;
						$cD=$this->cD=$dntmp;
						$this->setOpts(array('_new_defs'=>$dntmp));
			}
			$this->resetOpts();
			return true;
		}

		if(true || !is_numeric($irid)){

			$this->db->query($q="select id from {$this->tbl} ct where ((ct.{$this->slug_field}='{$irid}' and ct.id!='{$irid}') or (ct.id='{$irid}' and ct.{$this->slug_field}!='{$irid}')) and ct.lid=0 ","{$this->db_res}_curr_slug");
			$srr=$this->db->next("{$this->db_res}_curr_slug");

			if(is_array($srr) && count($srr)>0){
				$this->curr_id=$irid=$srr['id'];
			}
		}

		if($this->curr_id>0 || $irid>0)
		{
			$this->curr_id=$irid;
			$ctmp=array();

			$cq = $this->queryRels() . " where ct.id={$this->curr_id} or ct.lid={$this->curr_id} group by ct.id order by ct.lid desc";
			$cq = "select " . $this->queryFields() . " from {$this->tbl} ct $cq";
			$this->db->query($cq,"{$this->db_res}_curr");


			while($rr=$this->db->next("{$this->db_res}_curr")){
/*				if(is_array($this->ctrlsf['date']))
				{

					foreach($this->ctrlsf['date'] as $n=>$v)
						$rr[$n]=date((isset($v['f'])?$v['f']:$sys_def_date_format),strtotime($rr[$n]));
					foreach($this->ctrlsf['datetime'] as $n=>$v)
						$rr[$n]=date((isset($v['f'])?$v['f']:"{$sys_def_datetime_format}"),strtotime($rr[$n]));
					
				}*/

				$ctmp[$rr['lang']]=$rr;
			}


			if(count($ctmp)>0){
				$cR=$this->current=$ctmp;
				$cC=$this->cC=$ctmp[$this->cur_lang];
				$cD=$this->cD=$ctmp[$this->def_lang];
				$cID=$this->cD['id'];
			}
			else
				$this->current=false;


		}else
			$this->current=false;

			$this->resetOpts();

	}

	function setCurrentFull($irid,$iopts=array())
	{

		$this->setCopts($iopts);

		if($irid=='new'){
			if(is_array($_REQUEST['_new_defs'])){
				$dntmp=array();
				foreach($_REQUEST['_new_defs'] as $dfn =>$dfv){
					if(strtolower($dfn)!='id' && in_array($dfn,$this->flds))
						$dntmp[$dfn]=$dfv;
				}
						$this->current=array($this->def_lang=>$dntmp,$this->cur_lang=>$dntmp);
						$this->cC=$dntmp;
						$this->cD=$dntmp;
						$this->setOpts(array('_new_defs'=>$dntmp));
			}
			$this->resetOpts();
			return true;
		}

		//if(!is_numeric($irid))
		if($this->slug_field!=false && strtolower($this->slug_field)!='id'){
			//foreach(array("(ct.{$this->slug_field}='{$irid}' and ct.id!='{$irid}')","(ct.id='{$irid}' and ct.{$this->slug_field}!='{$irid}')") as $qa){
			foreach(array("(ct.{$this->slug_field}='{$irid}' and ct.id!='{$irid}')") as $qa){
				$cnt=$this->db->getRow("select count(*) as cnt from {$this->tbl} ct where $qa");
				if($cnt['cnt']>0){
					$this->queryAll(array("queryWhere"=>$qa));
					break;
				}
			
//			$this->queryAll(array("queryWhere"=>" ((ct.{$this->slug_field}='{$irid}' and ct.id!='{$irid}') or (ct.id='{$irid}' and ct.{$this->slug_field}!='{$irid}'))"));
		}
		}else
			$this->queryAll(array("queryWhere"=>"ct.id='{$irid}' or ct.lid='{$irid}' "));

		if(is_array($this->queryArr[$this->gO("queryPrefix")]) && count($this->queryArr[$this->gO("queryPrefix")])>0)
			$ra=each($this->queryArr[$this->gO("queryPrefix")]);
		else
			$ra=false;
		if($ra==false){
			$this->cID=$this->cD=$this->cC=$this->current=false;
			$this->resetOpts();
			return false;
		}
			$rkey=$ra[0];
			$row=$ra[1];

			$this->current=$row;
			$this->cC=$row[$this->cur_lang];
			$this->cD=$row[$this->def_lang];
			$this->cID=$rkey;
			$this->resetOpts();
	}

	function _checkField($fnkk,$fn,$ret=false){
		if($ret===false)
			$ret=array('rels'=>array(),'fn'=>$fn);
		if(is_array($fn) && $fnkk!==false && !is_numeric($fnkk)){
			$fn=$fnkk;
			$ret['fn']=$fn;
		}
		$tfn=$fn;

		$fa=explode(".",$fn);
		if(count($fa)>1 && strtolower($fa[0])==strtolower($this->oname)){
			array_shift($fa);
			$tfn=implode('.',$fa);
		}
		if(count($fa)>1){
			if(!in_array($fa[0],array('sys_m2m'))){
				$ttr = preg_replace("#__r$#i", "__c", $fa[0]);
				if (is_array($this->rels[$ttr]) && is_object($this->rels[$ttr]['_dbo'])){
					$ret['rels'][]=array("o"=>$this->rels[$ttr]['_dbo']->oname,'tbln'=>$this->rels[$ttr]['tbln'],"lfld"=>$ttr,"rfld"=>$this->rels[$ttr]['on']);
					unset($fa[0]);
					return $this->rels[$ttr]['_dbo']->_checkField(false,implode(".",$fa),$ret);

				}else if(is_object($this->p->t[$ttr])){
					unset($fa[0]);
					foreach($this->rels as $rfn=>$rfa){
						if (is_object($rfa['_dbo']) && $rfa['_dbo']->oname==$ttr){
							$ret['rels'][]=array("o"=>$rfa['_dbo']->oname,'tbln'=>$rfa['tbln'],"lfld"=>$rfn,"rfld"=>$rfa['on']);
							$ret=$rfa['_dbo']->_checkField(false,implode(".",$fa),$ret);
							$ret['alias']=str_replace("__c","__r",$rfn).".".implode(".",$fa);
							return $ret;
						}

					}
				}
			}

		}else if(in_array($tfn,$this->flds)){
			return $ret;
		}
		return false;
	}

	function queryViewCache_doFld($r,&$sfRels,&$qRels){
		if(is_array($r)){
			if(count($r['rels'])>0){
				if(isset($r['alias']) && $r['alias']!=false)
					$sfRels[$r['fn']]=$r['alias'];
				else
					$sfRels[$r['fn']]=$r['fn'];
				if(count($r['rels'])>1){
					$rpath=$r['rels'][0]['tbln'];
					unset($r['rels'][0]);
					foreach($r['rels'] as $ra){
						$ro=$ra['o'];
						$rf=$ra['tbln'];
						$tro=$this->p->t[$ro];
						//$npath=$rpath."__X__{$rf}"; ?????
						$npath=$rpath."__X__".$ra['lfld'];
						$qRels[$npath]=array("obj"=>$ro,"tbl"=>$tro->tbl,"tbln"=>$npath,"ctbl"=>$rpath,"on"=>array("flds"=>array($ra['lfld']=>$ra['rfld'])));
						$rpath=$npath;
					}

				}
			}
		}
	}

	function queryViewCache(){

		$sfRels=array();
		$qRels=array();
		$cV=$this->gO('cV');
		if($cV==false)
			return false;

		$flag=$this->gO('__sys_viewCacheFlag');
		if($flag==true){
			return true;
		}


		if(isset($cV['list'])){
			foreach($cV['list'] as $fnkk=>$fn){
				$r=$this->_checkField($fnkk,$fn);
				$this->queryViewCache_doFld($r,$sfRels,$qRels);
			}
		}
 
		$ev=array('view','edit');
		foreach($ev as $m){
			if(is_array($cV[$m]['_l'])){
				foreach($cV[$m]['_l'] as $r){
					if (!is_array($r))
						continue;
					foreach($r as $c){
						if (!is_array($c))
							continue;
							foreach ($c as $fnkk => $fn) {
								$r = $this->_checkField($fnkk, $fn);
								$this->queryViewCache_doFld($r, $sfRels, $qRels);
							}
						}
					}
				}
		}
		$this->setOpts(array('__sys_viewCacheFlag'=>true,'query_sfRels' => $sfRels,"queryRels"=>$qRels));
	}

	function queryAll($iopts=array())
	{
		if(is_array($this->virt)){
			$this->setCopts($iopts);
			$ret=array();
			foreach($this->virt as $rK=>$row){
				if(!isset($row['id']))
					$row['id']=$rK;
				if(!isset($row['lid']))
					$row['lid']=0;
				if(!isset($row['lang']))
					$row['lang']=$this->def_lang;
				$ret[$row['id']][$row['lang']]=$row;

			}
			$this->queryArr[$this->gO("queryPrefix")]=$ret;
			$this->resetOpts();
			return;
		}

		$this->putQuery();
		$this->queryViewCache();
		$this->setCopts($iopts);

		$this->numQ=false;
		$cq="";
		$sprios="";

		$cq .= " \n from {$this->tbl} ct " . $this->queryRels();
		$whs="";
		if(count($this->gOAA('_sys_queryWhere'))>0){
			$wht=implode(") and (",$this->gOA('_sys_queryWhere'));
			if(trim($wht)!=false){
				if($whs!="")
					$whs.=" and ($wht) ";
				else
					$whs="($wht) ";
			}
		}

		if(count($this->gOAA('queryWhere'))>0){
			$wht=implode(") and (",$this->gOA('queryWhere'));
			if(trim($wht)!=false){
				if($whs!="")
					$whs.=" and ($wht) ";
				else
					$whs="($wht) ";
			}
		}

/*		if($this->gO('queryWhere'))
			$whs.=($this->gO("sys_queryWhere") ? "{$this->copts['sys_queryWhere']} and ({$this->copts['queryWhere']})":"{$this->copts['queryWhere']}");
		else
$whs.=($this->gO("sys_queryWhere") ? "{$this->copts['sys_queryWhere']}":"");*/

		if($this->copts['querySearchAll'] && count($this->gOAA("querySearchAllArr"))){
			$wht=implode(" or ",$this->gOAA("querySearchAllArr"));
			if(trim($wht)!=false){
				if($whs!="")
					$whs.=" and ($wht) ";
				else
					$whs="($wht) ";
			}
		}

		if(count($this->gOAA("queryFilter"))>0){
			$wht=implode(") and (",$this->gOA('queryFilter'));
			if(trim($wht)!=false){
				if ($whs != "")
					$whs.=" and ($wht )";
				else
					$whs="($wht) ";
			}
		}

/*		$cq.=($this->copts['querySearchAll'] && !$this->copts['queryWhere'] ? "where ".implode(" or ",$this->gO("querySearchAllArr")):"");
$cq.=($this->copts['querySearchAll'] && $this->copts['queryWhere'] ? " and ( ".implode(" or ",$this->gO("querySearchAllArr"))." )":"");*/

		$cV=$this->gOAA("cV");
		if(isset($cV['user_filter']) && $cV['user_filter']!=false && $this->gO("ignore_user_filter")==false && isset($this->p->curUsr)){

			$fltarr=array();
			if(!is_array($cV['user_filter']))
				$cV['user_filter']=array($cV['user_filter']);
			foreach($cV['user_filter'] as $k=>$v){
				if(isset($this->p->curUsr[$v]))
					$fltarr[]="{$k}='{$this->p->curUsr[$v]}'";
				else if(is_numeric($k) && $v!=false){
					preg_match_all("#(_f_([a-z0-9_]+))#im", $v, $ppret);
					foreach ($ppret[2] as $pf) {
						if (isset($this->p->curUsr[$pf]))
							$v = str_replace("_f_{$pf}", $this->p->curUsr[$pf], $v);
					}
					$fltarr[] = $v;
				} else
					$fltarr[] = "{$k}='{$v}'";

			}
			if(count($fltarr)>0){
				if($whs!=""){
					if(!(isset($this->p->u) && isset($this->p->u['tbl']) && $this->tbl==$this->p->u['tbl'] 
						&& isset($_REQUEST['rid']) && $_REQUEST['rid']==$this->p->curUsr['id']))
						$whs="(".implode(" and ",$fltarr).") and ({$whs})";
				}else
					$whs="(".implode(" and ",$fltarr).") ";
			}
		}
		else if(!$this->gO("noViews") && !(isset($cV['user_filter']) && is_array($cV['user_filter'])) && ($cV==false || !isset($cV['no_user_filter_allowed']) || $cV['no_user_filter_allowed']==false ))
			return false;

		if(count($this->gOAA('sys_queryWhere'))>0){
			$wht=implode(") and (",$this->gOA('sys_queryWhere'));
			if(trim($wht)!=false){
				if($whs!="")
					$whs="($wht) and ({$whs})";
				else
					$whs="($wht) ";
			}
		}

		if($whs!="")
			$cq.=" \n where {$whs} ";


		$cq.=($this->copts['queryGroup']? " group by {$this->copts['queryGroup']}":"");

		if($this->opts['sys_prios'])
			$sprios="sys_prios_prio asc,";


		if($this->copts['queryOrder'])
			$cq.=" order by $sprios {$this->copts['queryOrder']}";
		else if(!$this->gO('noDefColumns'))
			$cq.=" order by $sprios ct.id desc";

		//		$cq.=($this->copts['queryOrder']?" order by $sprios {$this->copts['queryOrder']}":" order by $sprios ct.id desc");

		if($this->copts['queryLimit']==false && $this->copts['rowsPerPage'])
		{

			//			$this->numQ="select count(*) as cnt ".$cq;

			$cp=(isset($_REQUEST['page']) ? $_REQUEST['page']-1 : 0);
			if($cp<0)
				$cp=0;
			$ps=$this->copts['rowsPerPage']*$cp;
			$pe=$ps+$this->copts['rowsPerPage'];
			$cq.=" limit $ps , $pe";
		}
		else
			$cq.=($this->copts['queryLimit']?" limit {$this->copts['queryLimit']}":"");

		$fq="select SQL_CALC_FOUND_ROWS ".$this->queryFields();

		$this->db->query($fq.$cq,"{$this->db_res}_queryAll_".$this->gO("queryPrefix"));
		//		die($this->db->getLastError());
		//			echo $fq.$cq;
		//	if($this->oname=="FU_Analysis__c")
//var_dump($fq.$cq,"<hr>",$this->db->getLastError(),"<hr>");

		global $sys_adb_debug_msg;
		if ($sys_adb_debug_msg == true && $this->db->getLastError() != false) {
			$this->p->setMsg("QALL ERROR:<br>" . $this->db->getLastError() . "<br><br><hr><br><br>QUERY:<br>" . $this->db->getLastQuery());
		} else if ($this->gO('_dbgQ') == true){
			//$this->p->setMsg("QALL ERROR:<br>" . $this->db->getLastError() . "<br><br><hr><br><br>QUERY:<br>" . $this->db->getLastQuery());
			var_dump("QALL ERROR:<br>" . $this->db->getLastError() . "<br><br><hr><br><br>QUERY:<br>" . $this->db->getLastQuery());
		}
		$this->db->query("SELECT FOUND_ROWS() as cnt", "{$this->db_res}_queryAllNumGet_" . $this->gO("queryPrefix"));

		$tCnt=$this->db->next("{$this->db_res}_queryAllNumGet_".$this->gO("queryPrefix"));
		$this->numQ=$tCnt['cnt'];

		$this->curNumRows=$this->db->numRows("{$this->db_res}_queryAll_".$this->gO("queryPrefix"));


		//		if($this->gO("__debugDieQALL"))
		//		{	
		//			var_dump($this->curNumRows,$fq.$cq);
		//			var_dump($this->curNumRows,$this->gO("__queryFields"));
		//					die($fq.$cq);
		//		}

		$ret=array();

		$tID=0;

		global $sys_def_date_format,$sys_def_time_format,$sys_def_datetime_format;

		$_skiptimeST=$this->gO('skipTimeStamps');
		$tfc=$this->fctrls;
		while($row=$this->db->next("{$this->db_res}_queryAll_".$this->gO("queryPrefix"))){
/*				if(is_array($this->ctrlsf['date']))
				{
					foreach($this->ctrlsf['date'] as $n=>$v)
						$row[$n]=date($v['f'],strtotime($row[$n]));
}*/
			$_sfRels = $this->gOAA('query_sfRels');
			foreach($row as $n=>$v){
				$relFn=str_replace("__x__", ".", $n);
				if (in_array($relFn, $_sfRels)) {
					$refAlias=array_search($relFn,$_sfRels);
					if($refAlias!=false && is_string($refAlias))
						$relFn=$refAlias;

					$row[$relFn] = $row[$n];
					unset($row[$n]);
					$n=$relFn;
				}

				$this->fctrls=$tfc;
				$this->prepCtrl(false,$n,true);

				if(isset($this->fctrls[$n]))
				{
					if(($this->fctrls[$n]['c']=="date" || (isset($this->fctrls[$n]['sfdata']['t']) && $this->fctrls[$n]['sfdata']['t']=="date")) && $v!=null /*&& isset($this->fctrls[$n]['f']) && is_array($this->ctrlsf['date'])*/){
						if($_skiptimeST==false)
							$row[$n."_ts"]=strtotime($v);
						if($v!=false && ($v!="0000-00-00" && $v!="0000-00-00 00:00:00")){
							$row[$n]=date((isset($this->fctrls[$n]['f'])?$this->fctrls[$n]['f']:$sys_def_date_format),strtotime($v));
						}else
							$row[$n]=false;
					}
					else if(($this->fctrls[$n]['c']=="datetime" || (isset($this->fctrls[$n]['sfdata']['t']) && $this->fctrls[$n]['sfdata']['t']=="datetime")) && $v!=null){
						if($_skiptimeST==false)
							$row[$n."_ts"]=strtotime($v);
						if($v!=false && ($v!="0000-00-00 00:00:00" && $v!="0000-00-00"))
							$row[$n]=date((isset($this->fctrls[$n]['f'])?$this->fctrls[$n]['f']:$sys_def_datetime_format),strtotime($v));
						else
							$row[$n]=false;
					}
					else if(($this->fctrls[$n]['c']=="time" || (isset($this->fctrls[$n]['sfdata']['t']) && $this->fctrls[$n]['sfdata']['t']=="time")) && $v!=null){
						if($_skiptimeST==false)
							$row[$n."_ts"]=strtotime($v);
						if($v!=false && $v!="00:00:00")
							$row[$n]=date((isset($this->fctrls[$n]['f'])?$this->fctrls[$n]['f']:$sys_def_time_format),strtotime($v));
						else
							$row[$n]=false;
					}
					else if(($this->fctrls[$n]['c']=="radio" || $this->fctrls[$n]['c']=="select") && isset($this->fctrls[$n]['opts']) && is_array($this->fctrls[$n]['opts']) && (!isset($this->fctrls[$n]['idAsVal']) || $this->fctrls[$n]['idAsVal']==false) && $this->gO('opts_idAsVal')==false){
						$row[$n]=$this->fctrls[$n]['opts'][$v];
						$row["{$n}_opt_val"]=$v;
					}
				}
			}
			if($this->gO("noDefColumns")){
				$tID++;
				if(!isset($row['id']))
					$row['id']=$tID;
				if(!isset($row['lid']))
					$row['lid']=0;
				if(!isset($row['lang']))
					$row['lang']=$this->def_lang;
			}

			if($row['lid']==0){
				if(!in_array($row['lang'],$this->langs))
					$row['lang']=$this->def_lang;
				$ret[$row['id']][$row['lang']]=$row;
			}else{
				$ret[$row['lid']][$row['lang']]=$row;
				if(!isset($ret[$row['lid']][$this->def_lang]['id'])){
					$ret[$row['lid']][$this->def_lang]['id'] = $row['lid'];
				}
			}
		}

		if(is_object($this->sys_m2m) && $this->gO('query_sys_m2m')!=false)
			$ret=$this->sys_m2m->queryAll($ret,$this->gO('query_sys_m2m'));

		$this->queryArr[$this->gO("queryPrefix")]=$ret;
		reset($this->queryArr[$this->gO("queryPrefix")]);

		$this->resetOpts();

	}

	function next()
	{
		global $cC,$cD,$cID,$cR;
		$cC=$cD=$cID=$cR=false;

		if($this->gO('ajax'))
			$this->sA('gui_slaves_rid',$this->copts['ajax']);
		else{
			$this->changeOpts(array('ajax'=>$this->gA('gui_slaves_rid')));
		}
		if(is_array($this->queryArr[$this->gO("queryPrefix")]))
			$ra=each($this->queryArr[$this->gO("queryPrefix")]);
		else
			$ra=false;

		if($ra==false && ($this->nextAjaxFlag || !$this->gO('ajax'))){
			$this->nextAjaxFlag=false;
			$this->cID=$this->cD=$this->cC=$this->current=false;

			return false;
		}
		if($ra){
			$this->nextAjaxFlag=false;
			$rkey=$ra[0];
			$row=$ra[1];
			$cR=$this->current=$row;
			$cC=$this->cC=$row[$this->cur_lang];
			$cD=$this->cD=$row[$this->def_lang];
			$cID=$this->cID=$rkey;
	
			if($this->gO('gui_slaves_parent')){
				$dp=$this->copts['gui_slaves_in_o']."[{$this->cID}]";
				$dhp=$this->copts['gui_slaves_html_prefix_o']."_{$this->cID}";

				$this->sA('gui_slaves_rid',$this->cID);
//				$this->sA('gui_slaves_html_prefix_o',$this->opts[$dhp]);
				$this->changeOpts(array('gui_slaves_in'=>$dp,'gui_slaves_html_prefix'=>$dhp));
			}


			return $row;
		}else{

			$this->nextAjaxFlag=true;
			$cID=$this->cID=$rkey=$this->gO('ajax');
			$this->cD=$this->cC=$this->current=false;

			if($this->gO('gui_slaves_parent')){
				if($this->gO('gui_slaves_new')){
				$dp=$this->copts['gui_slaves_in_o']."[new][{$this->cID}]";
				$dhp=$this->copts['gui_slaves_html_prefix_o']."_new_{$this->cID}";
				}else{
				$dp=$this->copts['gui_slaves_in_o']."[{$this->cID}]";
				$dhp=$this->copts['gui_slaves_html_prefix_o']."_{$this->cID}";
				}

				$this->sA('gui_slaves_rid',$this->cID);
//				$this->sA('gui_slaves_html_prefix_o',$this->opts[$dhp]);
				$this->changeOpts(array('gui_slaves_in'=>$dp,'gui_slaves_html_prefix'=>$dhp));
			}



			return $rkey;
		}



/*
		}else{


			if($this->gO('gui_slaves_parent')){
				if($agso=$this->gA("gui_slaves_in_o")){
					$this->changeOpts(array("gui_slaves_in_o"=>$agso));
				}

				if($this->gO('ajax'))
						$this->sA('gui_slaves_rid',$this->copts['ajax']);
					else
						$this->copts['ajax']=$this->gA('gui_slaves_rid');

					$this->opts['gui_slaves_in']=$dp=$this->copts['gui_slaves_in_o']."[new][{$this->copts['ajax']}]";
					$this->opts['gui_slaves_html_prefix']=$this->copts['gui_slaves_html_prefix_o']."_new_{$this->copts['ajax']}";
					$this->sA('gui_slaves_html_prefix_',$this->opts['gui_slaves_html_prefix']);

			}
		} */

			
	}

	function in_rs($ifld)
	{
		return $this->input_name($ifld,"r",false);
	}
	function in_r($ifld,$ilang)
	{
		return $this->input_name($ifld,"r",$ilang);
	}
	function in_cs($ifld)
	{
		return $this->input_name($ifld,"c",false);
	}
	function in_c($ifld,$ilang)
	{
		return $this->input_name($ifld,"c",$ilang);
	}
	function in($ifld,$pref,$ilang)
	{
		return $this->input_name($ifld,$pref,$ilang);
	}
	function in_s($ifld,$pref)
	{
		return $this->input_name($ifld,$pref,false);
	}

	function in_a($ifld,$ilang)
	{
		return $this->input_name($ifld,false,$ilang,array("dataArray"=>true));
	}
	function in_as($ifld)
	{
		return $this->input_name($ifld,false,false,array("dataArray"=>true));
	}

	function in_sub($ifld)
	{
		return $this->input_name($ifld,false,false,array("skipData"=>true));
	}
	function in_nsub($ifld)
	{
		return $this->input_name($ifld, false, false, array("skipData" => true, "dataArray" => true));
	}

	function input_name($ifld,$ipref="c",$ilang=false,$iopts=array())
	{
		$this->setCopts($iopts);


		$ret="";
		$da=false;
		if($this->gO('dataArray'))
			$da=true;


		if($this->gO('gui_slaves_in_o')){

			if($this->current==false){
				if($this->gO('ajax'))
					$this->sA('gui_slaves_rid',$this->copts['ajax']);
				else if($this->gO('gui_slaves_rid')){
					$this->changeOpts(array('ajax'=>$this->gA('gui_slaves_rid')));
				}else
					$this->changeOpts(array('ajax'=>$this->gO('gui_slaves_counter')));
					
				if($this->gO('gui_slaves_new')){
					$dp=$this->copts['gui_slaves_in_o']."[new][{$this->copts['ajax']}]";
					$dhp=$this->copts['gui_slaves_html_prefix_o']."_new_{$this->copts['ajax']}";
				}else{
					$dp=$this->copts['gui_slaves_in_o']."[{$this->copts['ajax']}]";
					$dhp=$this->copts['gui_slaves_html_prefix_o']."_{$this->copts['ajax']}";
				}


//				$this->sA('gui_slaves_html_prefix_o',$dhp);
				$this->changeOpts(array('gui_slaves_in'=>$dp,'gui_slaves_html_prefix'=>$dhp));
			}
			else{
				$dp=$this->copts['gui_slaves_in_o']."[{$this->cD['id']}]";

				$dhp=$this->copts['gui_slaves_html_prefix_o']."_{$this->cD['id']}";

				$this->sA('gui_slaves_rid',$this->cD['id']);
//				$this->sA('gui_slaves_html_prefix_o',$dhp);
				$this->changeOpts(array('gui_slaves_in'=>$dp,'gui_slaves_html_prefix'=>$dhp));
			}
			if($ilang===false)
				$dp.="[multi_row_share]";
			else
				$dp.="[multi_row][$ilang]";

		}
		else
			$dp="";

		$Ndata="[data]";
		if($this->gO("skipData"))
			$Ndata="";

		if($ipref!=false)
			$ipref=$ipref.".";

		if(!$da){
			if($ilang===false || $dp!="")
				$ret="multi_row_share{$dp}{$Ndata}[{$ipref}{$ifld}]";
			else
				$ret="multi_row[$ilang]{$dp}{$Ndata}[{$ipref}{$ifld}]";
		}else{
			if($ilang===false || $dp!="")
				$ret="multi_row_share{$dp}{$Ndata}{$ifld}";
			else
				$ret="multi_row[$ilang]{$dp}{$Ndata}{$ifld}";

		}

		if($this->gO("user_form_names")){
			$ret=$this->_secureFormNames($ret);
		}

		if($this->copts['echo'])
			echo $ret;
		else{
			$this->resetOpts();
			return $ret;
		}
		$this->resetOpts();


	}

	function _secureFormNames($fn){
		return $this->p->_secureFormNames($fn);
/*		$ret=$fn;
		if($this->gO("user_form_names")){
			$ret=$this->_secureFormNamesForce($fn);
		}
		return $ret;*/
	}
	function _secureFormNamesForce($fn){
		return $this->p->_secureFormNamesForce($fn);
/*		$ret=preg_replace_callback('#^[^\[\]]+?(?=\[)|(?<=\[)[^\[\]]+?(?=\])#i',array($this,"_secureFormNames_callback"),$fn);
return $ret;*/
	}
/*	function _secureFormNames_callback($ina){
		$in=$ina[0];
		if(isset($_SESSION['dbo_objs']['sec_field_rmap'][$in])){
			$tsec=$_SESSION['dbo_objs']['sec_field_rmap'][$in];
			if($_SESSION['dbo_objs']['sec_field_map'][$tsec]==$in){
				return $_SESSION['dbo_objs']['sec_field_rmap'][$in];
			}
		}
		$sec=str_replace(".","_",crypt($in));
		$_SESSION['dbo_objs']['sec_field_map'][$sec]=$in;
		$_SESSION['dbo_objs']['sec_field_rmap'][$in]=$sec;
		return $sec;
	}
	function _restoreSecFormNames($delReqData=true,$ireq=false){
		global $secFieldsRestored;
		if($secFieldsRestored=="done")
			return true;
		if($ireq==false){
			if(isset($_SESSION['dbo_objs']['sec_field_map']) && is_array($_SESSION['dbo_objs']['sec_field_map']) && count($_SESSION['dbo_objs']['sec_field_map'])>0){
				if($delReqData){
					unset($_REQUEST['data']);
					unset($_REQUEST['multi_row']);
					foreach($_REQUEST as $k=>$v){
						if(strpos($k,"act_")===0)
							unset($_REQUEST[$k]);
					}
				}
				
				$_REQUEST=$this->_restoreSecFormNames($delReqData,$_REQUEST);
				$secFieldsRestored="done";
					return true;

			}else
				return false;

		}else if(is_array($ireq)){
			$sessm=$_SESSION['dbo_objs']['sec_field_map'];
			$req=array();
			foreach($ireq as $k=>$v){
				if(is_array($v))
					$v=$this->_restoreSecFormNames($delReqData,$v);
				if(isset($sessm[$k])){
					$req[$sessm[$k]]=$v;
				}else
					$req[$k]=$v;
			}
			return $req;
		}
		return $ireq;

	}*/

	function input_file_name($ifld,$iopts=array())
	{
		$this->setCopts($iopts);


		$ret="";


		if($this->gO('gui_slaves_in_o')){
			if($this->current==false){
				if($this->gO('ajax'))
					$this->sA('gui_slaves_rid',$this->copts['ajax']);
				else{
					$this->changeOpts(array('ajax'=>$this->gA('gui_slaves_rid')));
				}
				if($this->gO('gui_slaves_new')){
					$dp=$this->copts['gui_slaves_in_o']."[new][{$this->copts['ajax']}]";
					$dhp=$this->copts['gui_slaves_html_prefix_o']."_new_{$this->copts['ajax']}";
				}else{
					$dp=$this->copts['gui_slaves_in_o']."[{$this->copts['ajax']}]";
					$dhp=$this->copts['gui_slaves_html_prefix_o']."_{$this->copts['ajax']}";
				}


//				$this->sA('gui_slaves_html_prefix_o',$dhp);
				$this->changeOpts(array('gui_slaves_in'=>$dp,'gui_slaves_html_prefix'=>$dhp));
			}
			else{
				$dp=$this->copts['gui_slaves_in_o']."[{$this->cD['id']}]";

				$dhp=$this->copts['gui_slaves_html_prefix_o']."_{$this->cD['id']}";

				$this->sA('gui_slaves_rid',$this->cD['id']);
//				$this->sA('gui_slaves_html_prefix_o',$dhp);
				$this->changeOpts(array('gui_slaves_in'=>$dp,'gui_slaves_html_prefix'=>$dhp));
			}
		}
		else
			$dp="";

		if($dhp!=false)
			$ret="{$ifld}_{$dhp}";
		else
			$ret=$ifld;

		if($this->gO("user_form_names")){
			$ret=$this->_secureFormNames($ret);
		}

		if($this->copts['echo'])
			echo $ret;
		else{
			$this->resetOpts();
			return $ret;
		}
		$this->resetOpts();


	}

	function hC($pref,$useid=false,$iopts=array())
	{
		return $this->htmlClass($pref,$useid,$iopts);
	}

	function hID($pref,$useid=true,$iopts=array())
	{
		return $this->htmlId($pref,$useid,$iopts);
	}

	function htmlClass($prefix,$useid=false,$iopts=array()){
/*		if($agso=$this->gA("gui_slaves_html_prefix_o")){
			$this->changeOpts(array("gui_slaves_html_prefix_o"=>$agso));
		}
 */

				if($this->gO('ajax'))
					$this->sA('gui_slaves_rid',$this->copts['ajax']);
				else
					$this->changeOpts(array('ajax'=>$this->gA('gui_slaves_rid')));


		$this->setCopts($iopts);


		$ret="$prefix dbo_{$this->oname} dbo_{$this->oname}_$prefix";

		$ret.=(isset($this->copts['adminOnlineEdit']) && $this->copts['adminOnlineEdit']!=false ? " dbo_{$this->oname}_{$prefix}_".$this->copts['adminOnlineEdit'] : "" );

		$ret.=(isset($this->copts['gui_slaves_html_prefix']) && $this->copts['gui_slaves_html_prefix']!=false ? " dbo_".$this->copts['gui_slaves_html_prefix']."_dbo_{$this->oname}_{$prefix}" : "" );

		if($useid)
			$ret.=" ".$this->htmlId($pref,true,$iopts);


		if($this->copts['echo_INS'])
			echo $ret;
		else{
			$this->resetOpts();
			return $ret;
		}
		$this->resetOpts();
	}

	function htmlId($pref,$useid=true,$iopts=array())
	{
/*		if($agso=$this->gA("gui_slaves_html_prefix_o")){
			$this->changeOpts(array("gui_slaves_html_prefix_o"=>$agso));
}*/

				if($this->gO('ajax'))
					$this->sA('gui_slaves_rid',$this->copts['ajax']);
				else
					$this->changeOpts(array('ajax'=>$this->gA('gui_slaves_rid')));

				$nnew="";
				if($this->gO('gui_slaves_new'))
					$nnew="new_";
		
				if($this->current==false && $this->gO('gui_slaves_in_o')){
				$dhp=$this->copts['gui_slaves_html_prefix_o']."_{$nnew}{$this->copts['ajax']}";
				$this->changeOpts(array('gui_slaves_html_prefix'=>$dhp));				
				}
		
				$ret="";

				$this->setCopts($iopts);

		$ret.=($useid && $this->gO('gui_slaves_html_prefix')!=false ? "dbo_".$this->copts['gui_slaves_html_prefix']."_" : "" );
		$ret.=(!$useid && $this->gO('gui_slaves_html_prefix_o')!=false ? "dbo_".$this->copts['gui_slaves_html_prefix_o']."_" : "" );
		$ret.="dbo_{$pref}_{$this->oname}";
//		$ret.=(isset($this->copts['adminOnlineEdit']) && $this->copts['adminOnlineEdit']!=false ? "_".$this->copts['adminOnlineEdit'] : "" );
		$ret.=($useid && isset($this->cD['id']) && $this->cD['id']!=false ? "_".$this->cD['id'] : "" );
		$ret.=($useid && $this->gO('ajax')!=false ? "_$nnew".$this->copts['ajax'] : "" );

		$ret = $this->gO('_idPrefix') . $ret;

		if($this->copts['echo_INS'])
			echo $ret;
		else{
			$this->resetOpts();
			return $ret;
		}
		$this->resetOpts();
	}


	function isl_s($ifld,$icv,$idef=false)
	{
		return $this->input_isChecked($ifld,$icv,$idef,false,array("isSelect"=>true));
	}
	function isl($ifld,$icv,$ilang,$idef=false)
	{
		return $this->input_isChecked($ifld,$icv,$idef,$ilang,array("isSelect"=>true));
	}
	function ic_s($ifld,$icv,$idef=false)
	{
		return $this->input_isChecked($ifld,$icv,$idef,false);
	}
	function ic($ifld,$icv,$ilang,$idef=false)
	{
		return $this->input_isChecked($ifld,$icv,$idef,$ilang);
	}
	function ic_a($ifld,$icv,$idef=false,$delim=false)
	{
		return $this->input_isChecked($ifld,$icv,$idef,false,array("_checkArrayVal"=>true,'_cc_delim'=>$delim));
	}
	function input_isChecked($ifld,$icv,$idef=false,$ilang=false,$iopts=array())
	{
		$this->setCopts($iopts);
		$ret="";
		if($ilang===false)
			$ilang=$this->def_lang;

		if($this->gO("_checkArrayVal")){
			$Odelim=";";
			if(isset($this->fctrls[$fn]['delim']))
				$Odelim=$this->fctrls[$fn]['delim'];
			if($this->gO('_cc_delim')!=false)
				$Odelim=$this->gO('_cc_delim');

			$tv=$this->current[$ilang][$ifld];
			if($Odelim!=false)
				$tv=explode($Odelim,$this->current[$ilang][$ifld]);

			if(is_array($tv) && in_array($icv,$tv))
				$ret="checked='true'";
		}else{
			if(($this->current!==false && isset($this->current[$ilang][$ifld]) && $this->current[$ilang][$ifld]==$icv) || $idef==true){
				if($this->gO("isSelect"))
					$ret="selected='selected'";
				else
					$ret="checked='true'";
			}
		}

		if($this->copts['echo'])
			echo $ret;
		else{
			$this->resetOpts();
			return $ret;
		}
		$this->resetOpts();
	}

	function iv_s($ifld,$idef=false)
	{
		return $this->input_val($ifld,false,$idef);
	}
	function iv($ifld,$ilang,$idef=false,$iopts=array())
	{
		return $this->input_val($ifld,$ilang,$idef,$iopts);
	}
	function iv_cd($ifld,$idef=false)
	{
		return $this->input_val($ifld,$this->cur_lang,$idef);
	}
	function iv_c($ifld,$idef=false)
	{
		return $this->input_val($ifld,$this->cur_lang,$idef);
	}

	function input_val($ifld,$ilang=false,$idef=false,$iopts=array())
	{
		$this->setCopts($iopts);
		global $sys_def_date_format,$sys_def_time_format,$sys_def_datetime_format;
		$ret="";
		if($ilang===false)
			$ilang=$this->def_lang;

	if($this->gO('_iv_sel_opt'))
		$ifld="{$ifld}_opt_val";

		if($this->current!==false && isset($this->current[$ilang][$ifld])){
		 if($this->copts['retDefLang']==true)
			 $ret=$this->current[$this->def_lang][$ifld];
		 else
			 $ret=$this->current[$ilang][$ifld];
		}
		else if($idef!==false)
			$ret=$idef;
		else if($this->fctrls[$ifld]['c']=="date" && isset($this->fctrls[$ifld]['def']) && $this->fctrls[$ifld]['def']=='now')
			$ret=date((isset($this->fctrls[$ifld]['f'])?$this->fctrls[$ifld]['f']:$sys_def_date_format),time());
		else if($this->fctrls[$ifld]['c']=="datetime" && isset($this->fctrls[$ifld]['def']) && $this->fctrls[$ifld]['def']=='now')
			$ret=date((isset($this->fctrls[$ifld]['f'])?$this->fctrls[$ifld]['f']:$sys_def_datetime_format),time());
		else if($this->fctrls[$ifld]['c']=="time" && isset($this->fctrls[$ifld]['def']) && $this->fctrls[$ifld]['def']=='now')
			$ret=date((isset($this->fctrls[$ifld]['f'])?$this->fctrls[$ifld]['f']:$sys_def_time_format),time());

		$ret=$this->fieldMod($ifld,$ret,$this->cC[$ifld]);

		if($this->copts['echo'])
			echo $ret;
		else{
			$this->resetOpts();
			return $ret;
		}
		$this->resetOpts();

	}



	function act_wrap($iact,$ititle=false,$ihid=false,$ihclass=false,$iopts=array())
	{
		$this->setCopts($iopts);
		$ret="";
		$title="";
		$hid="";
		$hclass="";
		$title=array("i"=>"Insert","u"=>"Update","d"=>"Delete","dm"=>"Are you sure?");
		$csubmit="submit";

		if(isset($this->copts['gui_slaves_in']) && $this->copts['gui_slaves_in']!=false)
		{
			if($this->current==false)			{
				if($iact=="i")
					$dp=$this->copts['gui_slaves_in_o']."[new][{$this->copts['ajax']}]";
				else if($iact=="u")
					$dp=$this->copts['gui_slaves_in_o']."[edit][{$this->copts['ajax']}]";
				else if($iact=="d")
					$dp=$this->copts['gui_slaves_in_o']."[ndel][{$this->copts['ajax']}]";

		}else		{
					if($iact=="i")
						$dp=$this->copts['gui_slaves_in_o']."[new][{$this->copts['ajax']}]";
					else if($iact=="u")
						$dp=$this->copts['gui_slaves_in_o']."[edit][{$this->cD['id']}]";
					else if($iact=="d")
						$dp=$this->copts['gui_slaves_in_o']."[del][{$this->cD['id']}]";
				}

		$ccname="multi_row_share{$dp}";
		if($this->gO("user_form_names")){
			$ccname=$this->_secureFormNames($ccname);
		}
			if($this->current==false)
				$ret="<input type='hidden' name='$ccname' value='{$this->copts['ajax']}'/>";
			else		
				$ret="<input type='hidden' name='$ccname' value='{$this->cD['id']}'/>";


		if($this->copts['echo'])
			echo $ret;
		else{
			$this->resetOpts();
			return $ret;
		}
		$this->resetOpts();

		}
		else
			$dp="";


		if($ititle!==false){
			if(is_array($ititle))
				$title=array_merge($title,$ititle);
			else
				$title[$iact]=$ititle;
		}
		$ccnf=false;
		if($this->gO("user_form_names")){
			$ccnf=true;
		}
		if($ihid!==false)
			$hid="id='$ihid'";
		if($ihclass!==false)
			$hclass="class='$ihclass'";
		if($iact=="i" || $this->current==false || !isset($this->cD['id']) || $this->cD['id']==false){
			$tnn="multi_row_share{$dp}[act_i_{$this->oname}]";
			$ret.="<input class='dbo_submit dbo_submit_i' type='{$csubmit}' name='".($ccnf?$this->_secureFormNames($tnn):$tnn)."' value='{$title['i']}' $hid $hclass >";
		}
		else if($iact=="u"){
			$f=true;
			foreach($this->langs as $l)
			{
				if(isset($this->current[$l]['id'])){
					if($f){
						$tnn="multi_row[$l]{$dp}[act_{$iact}_{$this->oname}]";
						$ret.="<input class='dbo_submit dbo_submit_u' type='{$csubmit}' name='".($ccnf?$this->_secureFormNames($tnn):$tnn)."' value='{$title['u']}' $hid $hclass />";
						$f=false;
					}
					else{
						$tnn="multi_row[$l]{$dp}[act_{$iact}_{$this->oname}]";
						$ret.="<input type='hidden' name='".($ccnf?$this->_secureFormNames($tnn):$tnn)."'/>";
					}

					$tnn="multi_row[$l]{$dp}[data][ret.w.id]";
					$ret.="<input type='hidden' name='".($ccnf?$this->_secureFormNames($tnn):$tnn)."' value='{$this->current[$l]['id']}'/>";
				}else{
					$tnn="multi_row[$l]{$dp}[act_i_{$this->oname}]";
					$ret.="<input type='hidden' name='".($ccnf?$this->_secureFormNames($tnn):$tnn)."'/>";
				}

			}
		}else if($iact=="d"){
			$tnn="multi_row_share{$dp}[act_{$iact}_{$this->oname}]";
			$ret.="<input class='dbo_submit dbo_submit_u' type='{$csubmit}' name='".($ccnf?$this->_secureFormNames($tnn):$tnn)."' value='{$title['d']}' $hid $hclass onclick=\"return confirm('{$title['dm']}');\">";
			$tnn="multi_row_share{$dp}[data][ret.w.id]";
			$ret.="<input type='hidden' name='".($ccnf?$this->_secureFormNames($tnn):$tnn)."' value='{$this->current[$this->def_lang]['id']}'/>";
			$tnn="multi_row_share{$dp}[data][o.w.lid]";
			$ret.="<input type='hidden' name='".($ccnf?$this->_secureFormNames($tnn):$tnn)."' value='{$this->current[$this->def_lang]['id']}'/>";
		}

		if($this->copts['echo'])
			echo $ret;
		else{
			$this->resetOpts();
			return $ret;
		}
		$this->resetOpts();


	}

	function doInc($tpl,$iopts=array())
	{
		$row=$this->current;

/*		if(!is_array($this->cD))
			var_dump($row);
 */
		if(is_array($row))
		$row=array_merge($row,$this->cD);

		$ttret="";
		if(!$this->copts['echo'])
			ob_start();

		require($this->p->tpl_path("inc/$tpl.inc.php"));

		if(!$this->copts['echo']){
			$ttret=ob_get_contents();
			ob_end_clean();

}
		if($this->copts['echo'])
			echo $ttret;
		else{
			return $ttret;
		}

	}

	function putQuery(){

		array_push($this->currentArr,array(0=>$this->cID,1=>$this->current,2=>count($this->popts)));

		$cobj_pn=count($this->currentArr);
		$this->copts['queryPrefix']=$this->opts["queryPrefix"].$cobj_pn;

		return $cobj_pn;
	}

	function getQuery(){
		global $cC,$cD,$cID,$cR;

		$cc=count($this->currentArr);
		if($cc>1 && count($this->popts)+1==$this->currentArr[$cc-1][2]){
			$cC=$cD=$cID=$cR=false;
			$ar=array_pop($this->currentArr);
			$cID=$this->cID=$ar[0];
			$row=$ar[1];
			if($row!=false && is_array($row)){
			$cR=$this->current=$row;
			$cC=$this->cC=$row[$this->cur_lang];
			$cD=$this->cD=$row[$this->def_lang];
			return $row;
			}else{
				$this->cID=$this->cD=$this->cC=$this->current=false;
				return false;
			}
		}
		return false;
	}

	function listDef($dtpl=false,$iopts=array())
	{

		$this->setCopts($iopts);
		$ret="";
		$this->llcnt=0;
		$rr=array(0=>"even",1=>"odd");

		if($dtpl==false)
			$dtpl=$this->copts['listTpl'];

		$ttp="dbo_{$dtpl}_{$this->copts['adminOnlineEdit']}";
		if(!is_file($this->p->TEMPL."/inc/$ttp.inc.php"))
			$ttp="dbo_{$dtpl}";

		$this->queryAll();

		while($this->next())
		{
			$ret.=$this->doInc($ttp,$this->copts);
			$this->llcnt++;
		}

		if(!$this->copts['echo']){
			$this->resetOpts();
			return $ret;
		}
			$this->resetOpts();

	}

	function showAdmin($templ=false,$iopts=array())
	{
		if(!$this->p->isAdmin)
			return false;

		$this->skin=$this->p->skin;
		$this->setCopts($iopts);

		if(isset($_REQUEST['rid']) && $_REQUEST['rid']!=false){
			if($templ==false)
				$templ=$this->copts['editTpl'];
			$this->setCurrentFull($_REQUEST['rid']);
		}
		if($templ==false)
			$templ=$this->copts['listTpl'];

		$ttp = "{$this->p->TEMPL}/admin/dbo_{$templ}_{$this->copts['adminOnlineEdit']}.tpl.php";
		if(!is_file($ttp))
			$ttp = "{$this->p->TEMPL}/admin/dbo_{$templ}.tpl.php";

		if(isset($_REQUEST['popup']) && $_REQUEST['popup']==1){
			if(isset($_REQUEST['_ucdo']) && $_REQUEST['_ucdo']!=false && isset($_REQUEST['_ucdc']) && $_REQUEST['_ucdc']!=false && is_object($this->p->t[$_REQUEST['_ucdo']])){
				$this->changeOpts(array("_ucd"=>array("o"=>$_REQUEST['_ucdo'],"c"=>$_REQUEST['_ucdc']),"_ucdu"=>"&_ucdo={$_REQUEST['_ucdo']}&_ucdc={$_REQUEST['_ucdc']}"));
			}
			require("{$this->p->TEMPL}/admin/dbo_{$this->copts['popupTpl']}_popup.tpl.php");

		}else{
			if(!$this->gO('ajax') && !$this->gO('ownHeader'))
				$this->p->showHeader();
			require($ttp);
			if(!$this->gO('ajax') && !$this->gO('ownHeader'))
				$this->p->showFooter();

		}
		$this->resetOpts();
	}

	function showUser($templ=false,$iopts=array())
	{
		$this->skin=$this->p->skin;
		$this->setCopts($iopts);

		if(isset($_REQUEST['rid']) && $_REQUEST['rid']!=false)
			$this->setCurrent($_REQUEST['rid']);

		if(!$templ)
			$templ=$this->gO("userTpl");

		if(!$this->copts['user_ownHeader'])
		$this->p->showHeader();

		if(is_file($this->p->tpl_path("dyn/dbo_$templ.tpl.php")))
			require_once($this->p->tpl_path("dyn/dbo_$templ.tpl.php"));
		else
			require_once($this->p->tpl_path("page404.tpl.php"));

		if(!$this->copts['user_ownHeader'])
		$this->p->showFooter();

		$this->resetOpts();

	}

function run_triggers($plc,$act,$old,$new){
	global $sys_history_tracking, $sys_app_name ;
	if($plc=='pre' && $act=="i"){
		if($this->gO('sf_table')==true){
			foreach($new as $id=>$ndata){
				if(!isset($ndata['SF_Id']))
					$ndata['SF_Id']="SFID_{$this->oname}".time().mt_rand(0,999);
			}
		}
	}

	if($plc=='post' && $sys_history_tracking==true){
		if($act=='i'){
			foreach($new as $id=>$ndata){
				$o_sf_id="";
				if($this->gO('sf_table')==true && isset($new['SF_Id']))
					$o_sf_id=$ndata['SF_Id'];
				$this->db->query("insert into {$this->p->db_prefix}sys_change_history set 
						user_id='{$this->p->curUsrId}',username='{$this->p->curUsrSlug}', 
						obj='{$this->oname}', obj_id='{$ndata['id']}',
						obj_slug='{$ndata[$this->slug_field]}', obj_sf_id='$o_sf_id', act='$act',app='$sys_app_name'");
			}
		}
		if($act=='u'){
			foreach($old as $oldK=>$oldD){
				$o_sf_id="";
				if($this->gO('sf_table')==true && isset($oldD['SF_Id']))
					$o_sf_id=$oldD['SF_Id'];
				$uf=array();
				$oldData=array();
				$newData=array();
				foreach($oldD as $k=>$v){
					$v=$this->fn_def($k,$v,false);
					$new[$oldK][$k]=$this->fn_def($k,$new[$oldK][$k],false);
					if((isset($new[$oldK][$k]) || is_null($new[$oldK][$k])!=is_null($v)) && $new[$oldK][$k]!=$v){
						$uf[$k]=$k;
						$oldData[$k]=$oldD[$k];
						$newData[$k]=$new[$oldK][$k];
					}
				}

				$oldData=$this->db->escape(json_encode($oldData),true);
				$newData=$this->db->escape(json_encode($newData),true);
				$ufc=count($uf);
				$uf=$this->db->escape(json_encode($uf),true);

				//				var_dump($oldData,$newData,$uf);
				if($ufc>0){
					$this->db->query($q="insert into {$this->p->db_prefix}sys_change_history set 
							user_id='{$this->p->curUsrId}',username='{$this->p->curUsrSlug}', 
							obj='{$this->oname}', obj_id='{$oldD['id']}',
							obj_slug='{$oldD[$this->slug_field]}', obj_sf_id='$o_sf_id', act='$act',app='$sys_app_name',
							upd_fields='$uf', old='$oldData',new='$newData'");
					//				var_dump($q,mysql_error());
				}
			}
		}
		if($act=='d'){
			foreach($old as $oldK=>$oldD){
				$o_sf_id="";
				if($this->gO('sf_table')==true && isset($oldD['SF_Id']))
					$o_sf_id=$oldD['SF_Id'];

				$oldData=$this->db->escape(json_encode($oldD),true);

				$this->db->query("insert into {$this->p->db_prefix}sys_change_history set 
						user_id='{$this->p->curUsrId}',username='{$this->p->curUsrSlug}', 
						obj='{$this->oname}', obj_id='{$oldD['id']}',
						obj_slug='{$oldD[$this->slug_field]}', obj_sf_id='$o_sf_id', act='$act',app='$sys_app_name',
						old='$oldData'");
			}
		}
	}		
	if($this->gO('triggers')){
		$t=$this->gO('triggers');
		if(is_array($t) && (is_array($t[$plc][$act]) && count($t[$plc][$act])>0) || is_array($t[$plc]['a']) && count($t[$plc]['a'])>0){
			$tgf=dirname(__FILE__).'/triggers.php';
			require_once($tgf);

			$nna=array();
			if(is_array($new)){
				foreach($new as $nk=>$nv)
					$nna[$nk]=stripslashes($nv);
				$new=$nna;
			}
			$tq=array();
			if(isset($t[$plc][$act]))
				$tq[]=$t[$plc][$act];
			if(isset($t[$plc]['a']))
				$tq[]=$t[$plc]['a'];

			foreach($tq as $cq){
				foreach($cq as $fnk=>$fnv){
					$fn=$fnk;
					$fnarg=$fnv;
					if(is_numeric($fnk)){
						$fn=$fnv;
						$fnarg=false;
					}
					if(function_exists("trg_".$fn)){
						$newr=call_user_func("trg_".$fn,$this,$old,$new,$plc,$act,$fnarg);
						if(is_array($newr))
							$new=$newr;
					}
				}
			}
			$nna=array();
			if(is_array($new)){
				foreach($new as $nk=>$nv)
					$nna[$nk]=$this->db->escape($nv,true);
				$new=$nna;
			}

		}
	}
	return $new;
}

	function preAdbClear(&$ireq){
		$cdata=array();
		$odata=array();
		foreach($ireq['data'] as $k=>$v){

			if(strpos($k,"c.")!==false || strpos($k,"r.")!==false){
				$tk=preg_replace("#c\.|r\.#mi","",$k);
				$cdata[$tk]=$v;			
				$odata[$tk]=$k;
			}
			else{
				$cdata[$k]=$v;
				$odata[$k]=$k;
			}
		
		}

		$this->preAdbDataFix($act, $ireq, $cdata, $odata);
		

			unset($ireq['data']['sys_files']);
			unset($ireq['data'][$odata['prio']]);
			unset($ireq['data'][$odata['parent']]);
			unset($ireq['data']['links']);
			unset($ireq['data']['sys_m2m']);

	}

	function reqArr($ireq,$lang=false){
		$ret=array();
		$l=($lang?$lang:$this->cur_lang);
		$go=array("l"=>$ireq['multi_row'][$l]['data'],"m"=>$ireq['multi_row_share']['data']);
		foreach($go as $ar){
			foreach($ar as $k=>$v){

				if(strpos($k,"c.")!==false || strpos($k,"r.")!==false){
					$tk=preg_replace("#c\.|r\.#mi","",$k);
					$ret[$tk]=$v;			
				}
				else{
					$ret[$k]=$v;
				}
			}
		}
		return $ret;
	}


	function preAdbDataFix($act, &$ireq, &$cdata, &$odata, $iopts = array())
	{
global $sys_def_date_format,$sys_def_time_format,$sys_def_datetime_format;
/*	if(isset($this->fctrls['SF_Id']) && (!isset($cdata['SF_Id']) || $cdata['SF_Id']==false)){
		$sfid="SFID".time().mt_rand(0,999);
		if(!isset($cdata['SF_Id']))
			$odata['SF_Id'] = "c.SF_Id";

		$cdata['SF_Id'] = $ireq['data'][$odata['SF_Id']]=$sfid;
	}*/
		

	if ($act != "d") {
			foreach ($cdata as $k => $v) {
				if (is_array($v) && $this->fctrls[$k]['c'] == "checkbox") {
					$cdata[$k] = $ireq['data'][$odata[$k]] = implode(";", $v);
				}
				if (in_array($this->fctrls[$k]['c'],array('date','datetime')) && $v != false) {

					if ((isset($this->fctrls[$k]['f']) && $this->fctrls[$k]['f'] == "d/m/Y") || $sys_def_date_format=='d/m/Y' || strpos($sys_def_time_format,"d/m/Y")===0)
						$v = str_replace("/", "-", $v);
					if($this->fctrls[$k]['c']=='date')
						$cdata[$k] = $ireq['data'][$odata[$k]] = date("Y-m-d", strtotime($v));
					if($this->fctrls[$k]['c']=='datetime')
						$cdata[$k] = $ireq['data'][$odata[$k]] = date("Y-m-d H:i:s", strtotime($v));

				}
			}
			if (is_array($ireq['checkboxes'])) {
				foreach ($ireq['checkboxes'] as $cbk => $cbv) {
					if (!isset($odata[$cbk]) || !isset($ireq['data'][$odata[$cbk]])) {
						$odata[$cbk] = "c.{$cbk}";
						$cdata[$cbk] = $ireq['data'][$odata[$cbk]] = $cbv;
					}
				}
			}
		}
	}

	function preAdb($act,&$ireq,$iopts=array())
	{
		$this->setCopts($iopts);


		if(is_object($this->gui['slaves'])){
			$this->gui['slaves']->preAdb($act,$ireq['gui_slaves']);
			unset($ireq['gui_slaves']);
		}

		$cdata=array();
		$odata=array();
		foreach($ireq['data'] as $k=>$v){
			if(strpos($k,"c.")!==false || strpos($k,"r.")!==false){
				$tk=preg_replace("#c\.|r\.#mi","",$k);
				$odata[$tk]=$k;
			}
			else{
				$odata[$k]=$k;
				$tk=$k;
			}
			$cdata[$tk]=$v;			
		
		}

		$this->preAdbDataFix($act, $ireq, $cdata, $odata);

		if($this->opts['sys_files']!=false){
			$this->sys_files->preAdb($act,$ireq['data'],$lang,$lid);
			unset($ireq['data']['sys_files']);
		}

		if($this->opts['sys_prios']!=false){
			$this->sys_prios->preAdb($act,$cdata);
			unset($ireq['data'][$odata['prio']]);
			unset($ireq['data'][$odata['parent']]);
		}
		if($this->opts['sys_links']!=false){
			$this->sys_links->preAdb($act,$ireq['data']);
			unset($ireq['data']['links']);
		}

		if($this->opts['sys_m2m']!=false){
			$this->sys_m2m->preAdb($act,$ireq['data']);
			unset($ireq['data']['sys_m2m']);
		}

		$this->codata['cdata']=$cdata;
		$this->codata['odata']=$odata;

		$this->resetOpts();
	}



	function postAdb($act,$id,$rdt,&$ireq,$iopts=array())
	{
		$this->setCopts($iopts);

		$cdata=$this->codata['cdata'];
		$odata=$this->codata['odata'];
		
		if($this->opts['sys_files']!=false){
			$this->sys_files->postAdb($act,$id,$rdt);
		}

		if($this->opts['sys_prios']!=false){
			$this->sys_prios->postAdb($act,$id,$rdt);
			}
		if($this->opts['sys_links']!=false){
			$this->sys_links->postAdb($act,$id,$rdt);
		}
		if($this->opts['sys_m2m']!=false){
			$this->sys_m2m->postAdb($act,$id,$rdt);
		}

		if(is_object($this->gui['slaves'])){
			$this->gui['slaves']->postAdb($act,$id,$rdt,$this->codata,$ireq);
		}

		$this->resetOpts();
	}

	function showPagesSel($tpl="default",$iopts=array())
	{
		$this->setCopts($iopts);
		if(!$this->copts['rowsPerPage'])
			return false;

		$ret="";
		if($this->numQ!=""){
			$this->db->query($this->numQ,"{$this->db_res}_queryCNT");
	
			$nrow=$this->db->next("{$this->db_res}_queryCNT");
			$rcnt=$nrow['cnt'];
			$pn=$rcnt/$this->copts['rowsPerPage'];
			if($rcnt%$this->copts['rowsPerPage']>0)
				$pn++;
		}else
			$pn=0;
		$tqa=array();
		foreach($_GET as $k=>$v){
			if($k!="page")
				$tqa[]="$k=$v";
		}
		$curQuery=implode("&",$tqa);

		
		
		for($i=1;$i<=$pn;$i++){
			if(!$this->copts['echo'])
				ob_start();

			require($this->p->TEMPL."/inc/dbo_{$tpl}_pagination.inc.php");

			if(!$this->copts['echo']){
				$ret.=ob_get_contents();
				ob_end_clean();
			}

		}
		if(!$this->copts['echo']){
			$this->resetOpts();
			return $ret;
		}
	}

	function dT($fn,$td='t',$iopts=array()){
		return $this->drawTD($fn,$td,$iopts);
	}
	
	function drawTD($fn,$td='t',$iopts=array())
	{
		$this->setCopts($iopts);
		if($this->copts['echo'])
			echo $this->fctrls[$fn][$td];
		else{
			$this->resetOpts();
			return $this->fctrls[$fn][$td];
		}
		$this->resetOpts();
	}

	function dC($fn,$l=false,$iopts=array())
	{
		return $this->drawCtrl($fn,$l,$iopts);
	}
	function dCC($fn,$l=false,$iopts=array())
	{
		$iopts['echo']=false;
		return $this->drawCtrl($fn,$l,$iopts);
	}
	function dH($l=false,$iopts=array())
	{
		return $this->drawHidden($l,$iopts);
	}

	function drawHidden($l=false,$iopts=array())
	{
		$this->setCopts($iopts);
		
		$this->eF();
		
		foreach($this->hctrls as $fn=>$ct)
		{
			$fin=$this->in($fn,"c",$l);
			$fiv=$this->iv($fn,$l);
			if(is_array($this->uprels) && array_key_exists($fn,$this->uprels)){
				if($this->uprels[$fn]['lang'])
					$fiv=$this->iv($fn,$l,$this->p->t[$this->uprels[$fn]['tbl']]->iv($this->uprels[$fn]['fld'],$l,false,array('echo'=>false)));
				else
					$fiv=$this->iv($fn,false,$this->p->t[$this->uprels[$fn]['tbl']]->iv($this->uprels[$fn]['fld'],false,false,array('echo'=>false)));
			}
			if($fiv=="" && isset($ct['def']))
				$fiv=$ct['def'];

			$ret.="<input type='hidden'  name=\"$fin\" value=\"$fiv\">";
		}
		$this->eB();

		if($this->copts['echo'])
			echo $ret;
		else{
			$this->resetOpts();
			return $ret;
		}
		$this->resetOpts();

	}

	function prepCtrl($fnkk, &$fn, $upd_fctrls)
	{
		$tn = false;
		$ina = false;
		$ca = false;
		if ($fnkk != false && is_array($fn)) {
			$tn = $fnkk;
			$ina = $fn;
		} else {
			$tn = $fn;
		}
		$otn=$tn;
		$t = explode(".", $tn);
		if(count($t)>1 && strtolower($t[0])==strtolower($this->oname)){
			array_shift($t);
			$otn=$tn=implode('.',$t);
		}
		if (count($t) > 1) {
			if($t[0]=="sys_m2m" && is_object($this->sys_m2m) && isset($this->sys_m2m->objs[$t[1]]))
				$ca=array("t"=>$this->sys_m2m->objs[$t[1]]['t'],'c'=>'sys_m2m','obj'=>$t[1],'path_a'=>$t);
			else{	
				$ttr = preg_replace("#__r$#i", "__c", $t[0]);
				unset($t[0]);
				$ttf=implode(".",$t);
				if (is_array($this->rels[$ttr]) && isset($this->rels[$ttr]['_dbo']))
					$ca=$this->rels[$ttr]['_dbo']->prepCtrl(false,$ttf,false);
				else if(is_object($this->p->t[$ttr]))
					$ca=$this->p->t[$ttr]->prepCtrl(false,$ttf,false);
			}
		} else if(isset($this->fctrls[$tn])){
			$ca = $this->fctrls[$tn];
		}
		else
			$ca=array('t'=>'_ERR_FCTRL_ABSENT_','c'=>'string');

		$cV=$this->gO('cV');
		if(is_array($cV['fctrls'][$tn]))
			$ca = array_merge($ca, $cV['fctrls'][$tn]);

		if (is_array($ina)) {
			$ca = array_merge($ca, $ina);
		}
		$fn = $otn;

		if ($upd_fctrls == true)
			$this->fctrls[$fn] = $ca;

		return $ca;
	}
	function preview($in,$len=false,$strip=false,$minlen=false)
	{
		if(!$len)
			$len=$this->gO("previewLen");
		if(!$minlen)
			$minlen=$this->gO("previewMinLen");

		if(!$strip)
			$strip=$this->gO("previewStriptags");

		if($strip==true)
			$in=strip_tags($in);
//			$in=preg_replace("#&.{1,6};#misU","",strip_tags($in));

		if($len!=false && strlen($in)>$len){
			preg_match("#^.{{$minlen},{$len}}.*?(\.|,|:| |)#ims",$in,$tit);
			return $tit[0];
		}else
			return $in;


	}

	function dCU($fn,$l=false,$iopts=array())
	{

		return $this->drawCtrl($fn,$l,array_merge($iopts,array("user_form_names")));
	}

	/**
	 * @param $fn - object field name
	 * @param mixed|false $l - language
	 * @param array $iopts - object options
	 * @return string - control html
	 */
	function drawCtrl($fn,$l=false,$iopts=array())
	{
		if($l==false)
			$l=$this->def_lang;
		$this->setCopts($iopts);
		$this->eFA();

		$fin=$this->in($fn,"c",$l);
		$fiv=$this->iv($fn,$l);
		if(isset($this->fctrls[$fn]['editFormatVal']) && $this->fctrls[$fn]['editFormatVal']==true)
			$fiv=$this->format_val($fn,$fiv);

		if(is_array($this->uprels) && array_key_exists($fn,$this->uprels)){
			$this->p->t[$this->uprels[$fn]['tbl']]->eF();
			if($this->uprels[$fn]['lang'])
				$fiv=$this->iv($fn,$l,$this->p->t[$this->uprels[$fn]['tbl']]->iv($this->uprels[$fn]['fld'],$l));
			else
				$fiv=$this->iv($fn,false,$this->p->t[$this->uprels[$fn]['tbl']]->iv($this->uprels[$fn]['fld']));
			$this->p->t[$this->uprels[$fn]['tbl']]->eB();
		}
		if($fiv=="" && isset($this->fctrls[$fn]['def']))
			$fiv=$this->fctrls[$fn]['def'];

		if($this->gO('_dC_cval')!==false)
			$fiv=$this->gO('_dC_cval');

		if(isset($this->fctrls[$fn]['editFormatVal']) && $this->fctrls[$fn]['editFormatVal']==true)
			$fiv=$this->format_val($fn,$fiv);

//var_dump("START",$this->ajaxOpts,"END");

		$ret="";
		if(isset($this->fctrls[$fn]['_s']) && $this->fctrls[$fn]['_s']==true)
			$fin=$this->in_s($fn,"c");
		
		if($this->gO('_dC_cname')!=false)
			$fin=$this->gO('_dC_cname');

		global $_sys_def_ref_ctrl;
		if($this->fctrls[$fn]['c']=='ref')
			$this->fctrls[$fn]['c']=isset($_sys_def_ref_ctrl)?$_sys_def_ref_ctrl:'select';

		switch($this->fctrls[$fn]['c']){
		case "url": 
		case "text": 
			$ret="<input type='text' id='".$this->hID("{$l}_{$fn}_form_ctrl")."' name=\"$fin\" value=\"$fiv\" ".(isset($this->fctrls[$fn]['size'])?"size=\"{$this->fctrls[$fn]['size']}\"":"")." ".(isset($this->fctrls[$fn]['custHtml'])?$this->fctrls[$fn]['custHtml']:"").">";
			break; 
		case "string": 

			if(isset($this->fctrls[$fn]['opts']) && is_array($this->fctrls[$fn]['opts']) && isset($this->fctrls[$fn]['opts'][$fiv]))
				$fiv=$this->fctrls[$fn]['opts'][$fiv];
			if(isset($this->fctrls[$fn]['sfdata']))
				$ret="<input class='disabled' type='text' disabled='true' value='{$fiv}'>";
			else
				$ret="<div id='".$this->hID("{$l}_{$fn}_form_ctrl")."'>$fiv</div>";
			break; 
		case "disabled": 
			if(isset($this->fctrls[$fn]['opts']) && is_array($this->fctrls[$fn]['opts']) && isset($this->fctrls[$fn]['opts'][$fiv]))
				$fiv=$this->fctrls[$fn]['opts'][$fiv];
			if(isset($this->rels[$fn]))
				$fiv=$this->cD["r_".$fn];

				$ret="<input class='disabled' type='text' disabled='true' value='{$fiv}'>";
			break;

/*		case "hidden": 
			$ret="<input id='".$this->hID("{$l}_{$fn}_form_ctrl")."' type='hidden'  name=\"$fin\" value=\"$fiv\">";
			break; */
		case "colorpicker": 
			$ret="<input id='".$this->hID("{$fn}_form_ctrl")."' name=\"".$this->in_s($fn,"c")."\" value=\"$fiv\" ".(isset($this->fctrls[$fn]['size'])?"size=\"{$this->fctrls[$fn]['size']}":"")."\">";
			$ret.="<div id='".$this->hID("{$fn}_form_ctrl_colorpicker")."'></div>";
			$ret.="<script type='text/javascript'>$(document).ready(function(){ $('#".$this->hID("{$fn}_form_ctrl_colorpicker")."').farbtastic('#".$this->hID("{$fn}_form_ctrl")."');});</script>";
			break; 
		case "checkbox": 
			$cfin=$this->in_s($fn,"c");
			$cf_cb_def = '';
			$Odelim=";";
			if(isset($this->fctrls[$fn]['delim']))
				$Odelim=$this->fctrls[$fn]['delim'];
			if($Odelim!=false)
				$fiv=explode($Odelim,$fiv);
			if(isset($this->fctrls[$fn]['opts']) && is_array($this->fctrls[$fn]['opts'])){
				if(!is_array($fiv))
					$fiv=array($fiv);
				foreach($fiv as $fv){
					if ($fv != false && (!isset($this->fctrls[$fn]['opts'][$fv]) || !in_array($fv, $this->fctrls[$fn]['opts'])) && !$this->gO('strictOpts'))
					$this->fctrls[$fn]['opts'][$fv]=$fv;
				}
				foreach($this->fctrls[$fn]['opts'] as $k=>$v)
					$ret.="<span class='chb_opt'><input id='".$this->hID("{$l}_{$fn}_{$k}_form_ctrl")."' type='checkbox' name=\"{$cfin}[{$k}]\" value=\"".($this->fctrls[$fn]['valAsName']?$v:$k)."\" 	".$this->ic_a($fn,($this->fctrls[$fn]['valAsName']?$v:$k))." />&nbsp;<label for='".$this->hID("{$l}_{$fn}_{$k}_form_ctrl")."'>$v</label>&nbsp;</span>";
			}
			else if(is_array($this->rels) && array_key_exists($fn,$this->rels)){
				$chbox_opts = $this->p->t[$this->rels[$fn]['obj']]->sel($fiv, array("echo" => false, "selArray" => true));
				foreach($chbox_opts as $k=>$v)
					$ret.="<span class='chb_opt'><input id='".$this->hID("{$l}_{$fn}_{$k}_form_ctrl")."' type='checkbox'  name=\"{$cfin}[$k]\" value=\"$v\" 	".$this->ic_a($fn,$v,false,',')." /> <label for='".$this->hID("{$l}_{$fn}_{$k}_form_ctrl")."'>$v</label>&nbsp;</span>";
				if($this->p->isAdmin)
					$ret .= $this->p->t[$this->rels[$fn]['obj']]->popupEdit(array("echo" => false, "_use_cD" => array("o" => $this->oname, "c" => $fn)));
			} else {
				$cf_cb_def = '0';

				$ret .= "<span class='chb_opt'><input id='" . $this->hID("{$l}_{$fn}_form_ctrl") . "' type='checkbox'  name=\"$cfin\" value=\"1\" 	" . $this->ic_s($fn, 1) . " />&nbsp;</span>";
			}

			$ret .= "<input type='hidden' name='" . $this->in_nsub("[checkboxes][$fn]", "c") . "' value='$cf_cb_def'>";
	
			break; 
		case "textarea": 
			if(!isset($this->fctrls[$fn]['cols']))
				$this->fctrls[$fn]['cols']=$this->defs['textarea']['cols'];
			if(!isset($this->fctrls[$fn]['rows']))
				$this->fctrls[$fn]['rows']=$this->defs['textarea']['rows'];
			$ret="<textarea id='".$this->hID("{$l}_{$fn}_form_ctrl")."' cols={$this->fctrls[$fn]['cols']} rows={$this->fctrls[$fn]['rows']} name=\"$fin\">$fiv</textarea>";
			break; 
		case "htmltextarea": 
			$ret="<textarea id='".$this->hID("{$l}_{$fn}_form_ctrl")."' class='ckeditor mce_editor' cols=30 rows=5 name=\"$fin\">$fiv</textarea>";
			break;
		case "time": 
		case "datetime": 
		case "date": 
			$ret="<input readonly='true' id='".$this->hID("{$l}_{$fn}_form_ctrl")."' class='dbo_{$this->fctrls[$fn]['c']}' name=\"".$this->in_s($fn,"c")."\" value=\"$fiv\">";
			break; 
		case "password": 
			$ret="<input id='".$this->hID("{$l}_{$fn}_form_ctrl")."' name=\"$fin\" value=\"$fiv\" type=\"password\">";
			break; 
		case "select": 
			global $dbo_sel_default;
			$sel_default = $dbo_sel_default;
			$optsret = "";
			$preSel = "";
			$aftSel = "";
			$selCls = array();
			if (isset($this->fctrls[$fn]['selDefTit']))
				$sel_default = $this->fctrls[$fn]['selDefTit'];

			$relcase=false;
			if(isset($this->fctrls[$fn]['opts']) && is_array($this->fctrls[$fn]['opts'])){
				$fiv=$this->iv("{$fn}_opt_val",$l,false);				

				if($fiv!=false && !isset($this->fctrls[$fn]['opts'][$fiv]) && !$this->gO('strictOpts'))
					$this->fctrls[$fn]['opts'][$fiv]=$fiv;

				if(isset($this->fctrls[$fn]['unsetOpts']) && is_array($this->fctrls[$fn]['unsetOpts'])){
					foreach($this->fctrls[$fn]['unsetOpts'] as $Uo)
						unset($this->fctrls[$fn]['opts'][$Uo]);
				}
//				$ret="<select class=\"dbo_opts_e_sel dbo_sel\" name=\"".$this->in_s($fn,"c")."\">";
//				if(!(isset($this->fctrls[$fn]['nosel'])&&$this->fctrls[$fn]['nosel']==true))
//					$ret.="<option value=''>{$sel_default}</option>";
				foreach($this->fctrls[$fn]['opts'] as $k=>$v)
					$optsret .= "<option " . ($this->isl("{$fn}_opt_val", $k, $l)) . " value='{$k}'>{$v}</option>";
//				$ret.="</select> ";
			}
			else if(is_array($this->rels) && array_key_exists($fn,$this->rels)){
				$selCls[] = "dbo_{$this->rels[$fn]['obj']}_e_sel";
//				$ret="<select class=\"dbo_{$this->rels[$fn]['obj']}_e_sel dbo_sel\" name=\"".$this->in_s($fn,"c")."\">";
//				if(!(isset($this->fctrls[$fn]['nosel'])&&$this->fctrls[$fn]['nosel']==true))
//					$ret.="<option value=''>{$sel_default}</option>";
				$relcase=true;
				$optsret .= $this->p->t[$this->rels[$fn]['obj']]->sel($fiv, array("echo" => false));
//				$ret.="</select> ";
				if($this->p->isAdmin)
					$aftSel .= $this->p->t[$this->rels[$fn]['obj']]->popupEdit(array("echo" => false));
			}else if(isset($this->fctrls[$fn]['int']) && is_array($this->fctrls[$fn]['int']) && isset($this->fctrls[$fn]['int']['f'])){
				$Os = (int)$this->fctrls[$fn]['int']['f'];
				$Oe=false;
				$step = 1;
				$desc = false;
				if(isset($this->fctrls[$fn]['int']['t']))
					$Oe = (int)$this->fctrls[$fn]['int']['t'];
				else if(isset($this->fctrls[$fn]['int']['l']))
					$Oe = (int)$Os + $this->fctrls[$fn]['int']['l'];
				if (is_numeric($this->fctrls[$fn]['int']['step']) && $this->fctrls[$fn]['int']['step'] != 0)
					$step = (int)$this->fctrls[$fn]['int']['step'];
				if (isset($this->fctrls[$fn]['int']['desc']) && $this->fctrls[$fn]['int']['desc'] == true)
					$desc = true;

				if (
						($desc == false && (($step > 0 && $Os > $Oe) || ($step < 0 && $Os < $Oe)))
						||
						($desc == true && (($step < 0 && $Os > $Oe) || ($step > 0 && $Os < $Oe)))
				) {
					$tOs = $Os;
					$Os = $Oe;
					$Oe = $tOs;
				}

//				if(($Os!=false || $Oe!=false) && $Os!=$Oe){
//					$ret="<select class=\"dbo_{$this->rels[$fn]['obj']}_e_sel dbo_sel\" name=\"".$this->in_s($fn,"c")."\">";
				for ($_i = $Os; ($Os <= $Oe && $_i <= $Oe) || ($Os > $Oe && $_i >= $Oe); ($desc == false ? $_i += $step : $_i -= $step)) {
					$optsret .= "<option " . ($this->isl($fn, $_i, $l)) . " value='$_i'>$_i</option>";

			}
//				$ret.="</select> ";
//				}
			}

			$ret = "<select class=\"dbo_opts_e_sel dbo_sel\" name=\"" . $this->in_s($fn, "c") . "\">";
			$ret = "<select class=\"dbo_{$this->rels[$fn]['obj']}_e_sel dbo_sel\" name=\"" . $this->in_s($fn, "c") . "\">";
			$ret = "<select class=\"dbo_{$this->rels[$fn]['obj']}_e_sel dbo_sel\" name=\"" . $this->in_s($fn, "c") . "\">";
			$defRet = "";
			if (!(isset($this->fctrls[$fn]['nosel']) && $this->fctrls[$fn]['nosel'] == true))
				$defRet = "<option value=''>{$sel_default}</option>";

			$ret = $preSel . "<select id='" . $this->hID("sel_ctrl_{$fn}") . "' class=\"dbo_opts_e_sel dbo_sel " . implode(" ", $selCls) . "\" name=\"" . $this->in_s($fn, "c") . "\">
				" . $defRet . $optsret;
			$ret .= "</select> ";
			$ret .= $aftSel;

			break;
		case "radio": 
			if(isset($this->fctrls[$fn]['opts']) && is_array($this->fctrls[$fn]['opts'])){
			foreach($this->fctrls[$fn]['opts'] as $k=>$v)
				$ret.="<input ".($this->ic($fn,$k,$l))." id='".$this->hID("{$l}_{$fn}_{$k}_form_ctrl")."' type='radio' class=\"dbo_radio dbo_radio_{$fn}\" name=\"".$this->in_s($fn,"c")."\"  value='{$k}'> <label for='".$this->hID("{$l}_{$fn}_{$k}_form_ctrl")."'>{$v}</label>&nbsp;";
			}
			break;
		case "sys_prios_parent": 
			$ret=$this->sys_prios->drawParentCtrl();
			break; 
		case "sys_prios_prio": 
			$ret=$this->sys_prios->drawPrioCtrl();
			break; 
		case "sys_links": 
	//		if($l==$this->def_lang)
				$ret=$this->sys_links->drawAdminCtrl($this->fctrls[$fn]['ln']);
			break; 
		case "fileurl":
			if($fiv!=false)
				$ret="<a target='_blank' href='{$fiv}'>Current file</a>";
		case "sys_files":
				$tt="";
				if($this->fctrls[$fn]['c']=='fileurl')
					$tt="{$fn}_fileurl";
				else
					$tt=$this->fctrls[$fn]['type'];
//			if($l==$this->def_lang)
				ob_start();
				$this->sys_files->drawAdminCtrl($tt);
				$ret.=ob_get_contents();
				ob_end_clean();
			break; 
		case "sys_m2m": 
			$ret=$this->sys_m2m->drawAdminCtrl($this->fctrls[$fn]['obj']);
			break; 
		case "gui_slaves": 
			$ret=$this->gui['slaves']->drawAdminCtrl($this->fctrls[$fn]['slave']);
			break;
		default:
			if(method_exists($this->p,"control_{$this->fctrls[$fn]['c']}")){
				$ret=$this->p->{"control_{$this->fctrls[$fn]['c']}"}($this->oname,$fn,$fin,$fiv,$this->hID("{$l}_{$fn}"));
			}	
		} 
		$ret="<span class='dbo_{$this->oname}_ctr_group_{$fn}'>$ret</span>";

		$this->eB();

		if($this->copts['echo'])
			echo $ret;
		else{
			$this->resetOpts();
			return $ret;
		}
		$this->resetOpts();
	}


	function dV($fn,$row,$il,$iopts=array()){
		return $this->drawValue($fn,$row,$il=false,$iopts);
	}
	function dVnl($fn,$row,$iopts=array()){
		return $this->drawValue($fn,$row,$il=false,array_merge($iopts,array('dV_nolang'=>true)));
	}

	function drawValue($fn,$row,$il=false,$iopts=array()){
		$rarr=false;
		if($il==false){
			$la=$this->langs;
			$rarr=array();
		}
		else{
			$la=array($il);
		}
		$this->setCopts($iopts);
		
		$perlang=array("","text","textarea","htmltextarea");
		if(!isset($this->fctrls[$fn]['c']))
			$this->fctrls[$fn]['c']="";

		foreach($la as $l){
			$ret="";
			if($rarr!=false && $l!=$this->def_lang && (!in_array($this->fctrls[$fn]['c'],$perlang) || (isset($this->fctrls[$fn]['_s']) && $this->fctrls[$fn]['_s']==true)))
				continue;

			$ufn=isset($this->rels[$fn])?"r_{$fn}":$fn;
			if(isset($row[$l][$ufn]))
				$fiv=$row[$l][$ufn];
			else if(isset($row[$ufn]))
				$fiv=$row[$ufn];

			$fastEditCtrl=false;
			if(isset($this->fctrls[$fn]['fast_edit']) && $this->fctrls[$fn]['fast_edit']!=false){
				$fastEditCtrl=$this->draw_fastEdit($fn,$l);
			}

			if(method_exists($this->p,"listCtrl_{$this->fctrls[$fn]['c']}"))
				$ret=$this->p->{"listCtrl_{$this->fctrls[$fn]['c']}"}($this->oname,$fn,$row[$l],isset($this->rels[$fn]));
			else{

				$slugT=$this->fctrls[$fn]['c'];
				$asSlug=false;

				if($fn==$this->slug_field ){
					if($this->gO('noSlugLink')!=true && !(isset($this->fctrls[$fn]['noSlugLink']) && $this->fctrls[$fn]['noSlugLink']==true))
						$this->fctrls[$fn]['c']="ref";
				}else if(isset($this->fctrls[$fn]['noSlugLink']) && $this->fctrls[$fn]['noSlugLink']==true)
						$this->fctrls[$fn]['c']="string";

				if(($this->gO('fldAsSlugLink')!=false && $this->gO('fldAsSlugLink')==$fn) || (isset($this->fctrls[$fn]['asSlugLink']) && $this->fctrls[$fn]['asSlugLink']==true)){
					$asSlug=true;
					$this->fctrls[$fn]['c']="ref";
				}
			switch($this->fctrls[$fn]['c']){
				case "string":
					if(isset($this->fctrls[$fn]['opts']) && is_array($this->fctrls[$fn]['opts']) && isset($this->fctrls[$fn]['opts'][$fiv]))
						$ret=$this->fctrls[$fn]['opts'][$fiv];
					else if(isset($this->fctrls[$fn]['yn'][$fiv]))
						$ret=$this->fctrls[$fn]['yn'][$fiv];
					else
						$ret=$fiv;
					$ret=$this->format_val($fn,$ret);
					$ret=$this->fieldMod($fn,$ret,$this->cC[$fn]);
					break;
				case "checkbox":
					if(isset($this->fctrls[$fn]['opts']) && is_array($this->fctrls[$fn]['opts'])){
						$Topts=$this->fctrls[$fn]['opts'];
						$Tvals=explode(";",$fiv);
						$retA=array();
						foreach($Tvals as $Tv){
							if(isset($Topts[$Tv]))
								$retA[]=$Topts[$Tv];
							else
								$retA[]=$Tv;
						}
						if(count($retA)==0)
							$ret=$fiv;
						else
							$ret=implode(",",$retA);
					}
					else if(isset($this->fctrls[$fn]['yn'][$fiv]))
						$ret=$this->fctrls[$fn]['yn'][$fiv];
					else
						$ret=$fiv;
					$ret=$this->format_val($fn,$ret);
					$ret=$this->fieldMod($fn,$ret,$this->cC[$fn]);

					break;
				case "sf_ref":
				case "ref":
					$ret="";
					$clsA=false;
					$fiv=$this->format_val($fn,$fiv);
					$fiv=$this->fieldMod($fn,$fiv,$this->cC[$fn]);
					if($fn==$this->slug_field || $asSlug==true){
						$ret.="<a href='".aurl("/{$this->obj_slug}/{$row[$l][$this->slug_field]}")."'>";
						$clsA=true;
					}else if(in_array($fn,array_keys($this->rels)) && isset($this->rels[$fn]['obj']) && is_object($this->p->t[$this->rels[$fn]['obj']])){
						$rO=$this->p->t[$this->rels[$fn]['obj']];
						if(isset($this->p->v['objs'][$rO->oname])){
							$slv=$this->cD[$fn];
							if(isset($this->cD[$fn.'_slug']))
								$slv=$this->cD[$fn.'_slug'];
							$ret.="<a href='".aurl("/{$rO->obj_slug}/{$slv}")."'>";
							$clsA=true;
						}
					}
					$ret.=$fiv;
					if($clsA)
						$ret.='</a>';
					break;
				case "fileurl":
				case "url":
					if($fiv!=false){
						$fiv=$this->format_val($fn,$fiv);
						$fiv=$this->fieldMod($fn,$fiv,$this->cC[$fn]);
						$uLab="View link";
						if(isset($this->fctrls[$fn]['urlName']) && $this->fctrls[$fn]['urlName']!=false)
							$uLab=$this->fctrls[$fn]['urlName'];
						$ret="<a target='_blank' href='{$fiv}'>{$uLab}</a>";
						$ret.=" <a href='javascript:' class='clipboard_copy_btn' data-clipboard-text='$fiv'>[Copy]</a>";
					}
					break;
				case "htmltextarea":
					$fiv=$this->format_val($fn,$fiv,array('_dV_html'=>true));
					$fiv=$this->fieldMod($fn,$fiv,$this->cC[$fn]);
					$ret="<div class='readonly_textarea'><pre>{$fiv}</pre></div>";
					break;
				case "textarea":
					$fiv=$this->format_val($fn,$fiv);
					$fiv=$this->fieldMod($fn,$fiv,$this->cC[$fn]);
					$ret="<div class='readonly_textarea'><pre>{$fiv}</pre></div>";
					break;
				case "select":
					if($fiv==false && $fn==$ufn && $row[$l]["{$fn}_opt_val"]!=false)
						$fiv=$row[$l]["{$fn}_opt_val"];
						$ret=$fiv;
					break;
				case "sys_m2m":
					if(is_object($this->sys_m2m))
						$ret=$fiv=$this->sys_m2m->drawValue($row,$this->fctrls[$fn]['obj'],$l);
					break;				
				default:
					$fiv=$this->format_val($fn,$fiv);
					$fiv=$this->fieldMod($fn,$fiv,$this->cC[$fn]);
					
					$ret=$fiv;
				}

			}
			if($fn==$this->slug_field)				
				$this->fctrls[$fn]['c']=$slugT;
			if($asSlug==true)
				$this->fctrls[$fn]['c']=$slugT;
	

			global $sys_wbr_len,$sys_wbr_tag;
//			$ret=wordwrap_add($ret,$sys_wbr_len,$sys_wbr_tag);

			$ret=$this->fieldModFunc($fn,$fiv,$ret,$iopts);
			$ret=($fastEditCtrl!=false?$fastEditCtrl.$ret."</a>":$ret);

			if(is_array($rarr) && $this->gO('dV_nolang')==false)
				$rarr[$l]=$ret;
			else
				$rarr=$ret;
		}

		$this->resetOpts();

		return $rarr;
//		return ($fastEditCtrl!=false?$fastEditCtrl.$rarr."</a>":$rarr);


	}

	function draw_fastEdit($fn,$l=false,$iopts=array()){
		$this->setCopts($iopts);
		$fc=$this->fctrls[$fn];
		$fa=$this->fctrls[$fn]['fast_edit'];
		if(!is_array($fa))
			$fa=array();

		$v=$this->cD[$fn];

		if(isset($fa['emptyOnly']) && $fa['emptyOnly']==true && ($v!==false || $v!==''))
			return false;

		if(isset($fa['zeroOnly']) && $fa['zeroOnly']==true && $v!==0)
			return false;

		if(isset($fa['falseOnly']) && $fa['falseOnly']==true && $v!=false)
			return false;

		$on_emp_text="";
		if(isset($fa['emptyText']) && $fa['emptyText']!=false && $v==false)
			$on_emp_text=$fa['emptyText'];


		if($l==false)
			$l=$this->def_lang;

		$this->eFA();

		$fid=$this->hID("fast_edit_{$fn}_{$l}_form");
/*		$ret="<div class='hidden'><form class='fa_form' id='{$fid}' method='POST' action='".aurl("/")."'>
	<img onclick=\"jQuery('#{$fid}').hide();jQuery('#{$fid}_val').show();return false;\" class='fa_close' src='".aurl("/css/img/deny.png")."'>";*/
		$ret="<div class='hidden'><form class='fa_form' id='{$fid}' method='POST' action='".aurl("/")."' enctype='multipart/form-data'>";
		$ret .= "<input type='hidden' name='" . $this->_secureFormNames('redirect_url') . "' value='" . (isset($fa['fast_edit_redirect']) && $fa['fast_edit_redirect'] != false ? $fa['fast_edit_redirect'] : aurl("/{$this->obj_slug}/{$this->cD[$this->slug_field]}")) . "'>";
		$ret.="<input type='hidden' name='".$this->_secureFormNames('a')."' value='p_adb'>";
//		$ret.="<input type='hidden' name='".$this->_secureFormNames('ajax')."' value='1'>";
		$ret.="<input type='hidden' name='".$this->_secureFormNames('fast_edit')."' value='{$fn}'>";
		$ret.="<input type='hidden' name='".$this->input_name("ret.w.id",false,false)."' value='{$this->cD['id']}'>";
		$ret.="<input type='hidden' name='".$this->input_name("o.w.lid",false,false)."' value='{$this->cD['id']}'>";
		$ret.="<input type='hidden' name='".$this->in_sub("act_u_{$this->oname}")."' value='1'>";
		$ret.=$this->drawCtrl($fn,$l,array('echo'=>false));
		$ret.="<br><input type='submit' value='Save'>";
		$ret.="</form></div>";
		$ret.="<script type='text/javascript'>";
		$ret.="jQuery(document).ready(function(){
			jQuery('#${fid}_val').fancybox();";
/*		$ret.="	jQuery('#${fid}').jup({onComplete:function(r,formId){
				alert('OK:'+formId+':'+r);
}});";*/
		$ret.="});";
		$ret.="</script>";
		$ret.="<a id='${fid}_val' href='#{$fid}' >{$on_emp_text}";

		$this->eB();

		$this->resetOpts();	
		return $ret;
	}

	function format_val($fn,$v,$iopts=array()){
		$this->setCopts($iopts);
		if($this->gO('_dV_html')==false)
			$ret = htmlentities($v, 0, ini_get("default_charset"));
		else
			$ret=$v;
		global $sys_def_date_format, $sys_def_time_format,$sys_def_datetime_format;

		$ff=false;
		$sfd=false;
		$pDec=0;
		if(isset($this->fctrls[$fn]['sfdata']) && is_array($this->fctrls[$fn]['sfdata'])){
			$sfd=$this->fctrls[$fn]['sfdata'];
			if(isset($sfd['t']))
				$ff=$sfd['t'];
			if(isset($sfd['s']))
				$pDec=$sfd['s'];

		}else if(isset($this->fctrls[$fn]['sf_format']) && $this->fctrls[$fn]['sf_format']!=false){
			$ff=$this->fctrls[$fn]['sf_format'];
			if(isset($this->fctrls[$fn]['sf_format_dec']))
				$pDec=$this->fctrls[$fn]['sf_format_dec'];

		}

		if(is_numeric($v) && isset($this->fctrls[$fn]['autonum']) && $this->fctrls[$fn]['autonum']==true){
				$v=number_format($v,$pDec);
				$ret=$v;
		}
 
		if($ff!=false && $v!=="" && $v!==false){
			switch(strtolower($ff)){
			case "percent":
				$ret="";
				if($v!=false)
					$ret="{$v}%";
				break;
			case "currency":
				$ret="$".number_format($v,$pDec);
				break;
			case "datetime":
				if(isset($this->fctrls[$fn]['c']) && $this->fctrls[$fn]['c']!='datetime' && $v!=false)
					$ret=date("{$sys_def_datetime_format}",strtotime($v));
				else if(isset($this->cD[$fn."_ts"]))
					$ret=date("{$sys_def_datetime_format}",$this->cD[$fn."_ts"]);
				break;
			case "date":
				if(isset($this->fctrls[$fn]['c']) && $this->fctrls[$fn]['c']!='date' && $v!=false)
					$ret=date("{$sys_def_date_format}",strtotime($v));
				else if(isset($this->cD[$fn."_ts"]))
					$ret=date("{$sys_def_date_format}",$this->cD[$fn."_ts"]);
				break;
			case "email":
				$ret="<a class='nowrap' href='mailto:$v'>$v</a>";
				$ret.=" <a href='javascript:' class='clipboard_copy_btn' data-clipboard-text='$v'>[Copy]</a>";
				break;
			case "phone":
					$t=preg_replace("#[^0-9]+#","",$v);
					if($t!=false && strlen($t)>=10){
					$ret="(".substr($t,-10,3).") ".substr($t,-7,3)."-".substr($t,-4,4);
					if(strlen($t)>10)
						$ret="+".substr($t,0,strlen($t)-10).$ret;
					}
					$rphone=$ret;
					$ret="<a class='nowrap' href='tel:{$t}'>$rphone</a> <a href='javascript:' class='clipboard_copy_btn' data-clipboard-text='$t'>[Copy]</a>";
					break;
			};
		}
			
		$this->resetOpts();
		return $ret;
	
	}

	function fieldMod($fn,$v,$ov,$iopts=array()){
		$this->setCopts($iopts);
		$ret=$v;

		if(isset($this->fctrls[$fn]['modif']) && $this->fctrls[$fn]['modif']!=false){
			$ret=eval($this->fctrls[$fn]['modif']);
		}
		$this->resetOpts();
		return $ret;
	}
	function fieldModFunc($fn,$v,$ret,$iopts=array()){
		$this->setCopts($iopts);

		if(isset($this->fctrls[$fn]['modif_func']) && $this->fctrls[$fn]['modif_func']!=false){
			if(method_exists($this->p,$this->fctrls[$fn]['modif_func']))
			$ret=$this->p->{$this->fctrls[$fn]['modif_func']}($this,$fn,$v,$ret);
		}
		$this->resetOpts();
		return $ret;
	}

	function actBtnWrap($fnkk = false, $fn = false, $row = false)
	{
		if ($row == false)
			$row = $this->cD;
		$cV = $this->gO('cV');
		$ctls = $cV['acts'];

		if ($fnkk == '_acts_' && is_array($fn))
			$ctls = $fn;

		$btns = array();
		foreach ($ctls as $ck=>$cv) {
			if(is_array($cv))
				$btns[] = $this->drawActBtn("list_ctl_custom", $row, array("echo" => false,'__acts_custom'=>$cv));
			else if($cv!='i')
				$btns[] = $this->drawActBtn("list_ctl_{$cv}", $row, array("echo" => false));
		}
		return implode("&nbsp;", $btns);
	}
	function drawActBtn($type,$row,$iopts=array()){
		$this->setCopts($iopts);
		$cV=$this->gO('cV');

		$ret="";
		$bclass="";
		if($this->gO('_drawBtnClass'))
			$bclass=$this->gO('_drawBtnClass');

		$ru="";
		if($this->gO("_act_redir_url")!=false)
			$ru=urlencode($this->gO("_act_redir_url"));

		switch($type){
		case "list_ctl_i":
			$href=aurl("/{$this->obj_slug}/new");
			$ret="<form method='POST' action='{$href}' class='{$bclass} dbo_add_new_frm'>";
			$ret.=" <button onclick = \"jQuery(this).parents('form').get(0).submit();\" class = 'dbo_add_new {$bclass}' >
				<i class = 'icon-plus' ></i >
				Add ".(isset($cV['sLabel']) && $cV['sLabel'] != false ? $cV['sLabel'] : $this->copts['adminOneItemTitle'])."</button >";
			if($this->gOA('_list_ctl_i_defs')){
				foreach($this->gOAA('_list_ctl_i_defs') as $dfn=>$dfv){
					$ret.="<input type='hidden' name='".$this->_secureFormNames("_new_defs[$dfn]")."' value='".addcslashes($dfv,"'")."'>";
				}
			}
			if($ru!=false)
					$ret.="<input type='hidden' name='".$this->_secureFormNames("rr_url")."' value='{$ru}'>";
			$ret.="</form>";

			break;

		case "list_ctl_u":
			$href=aurl("/{$this->obj_slug}/{$row[$this->slug_field]}/e".($ru!=false?"?rr_url={$ru}":""));
			$ret="<a class='btn btn-mini {$bclass}' href='$href'><i class='icon-edit'></i> Edit</a>";
			break;
		case "list_ctl_d":
			$ret="<form action='".aurl('/')."' method='POST'><input type='hidden' name='a' value='p_adb'>";
			$ret.="<input type='hidden' name='redirect_url' value='".($ru!=false?$ru:aurl("/{$this->obj_slug}"))."'>";
			$ret.="<input type='hidden' name='".$this->_secureFormNames('multi_row_share[data][ret.w.id]')."' value='{$row['id']}'>";
			$ret.="<input type='hidden' name='".$this->_secureFormNames('multi_row_share[data][o.w.lid]')."' value='{$row['id']}'>";
			$ret.="<input type='hidden' name='".$this->_secureFormNames("multi_row_share[act_d_{$this->oname}]")."' value='_'>";

			$ret.="<a onclick=\"if(confirm('You sure you want delete this record ?')){jQuery(this).parents('form').get(0).submit();return false;}else{return false;}\" class='btn btn-mini {$bclass}' href='$href'><i class='icon-trash'></i> Delete</a>";
			$ret.="</form>";
			break;
		case "list_ctl_l":
			$href=aurl("/fastauth/{$row[$this->slug_field]}");
			$ret="<a class='btn btn-mini {$bclass}' href='$href'><i class='icon-user'></i> Login</a>";
			break;
		case "list_ctl_v":
			$href = aurl("/{$this->obj_slug}/{$row[$this->slug_field]}");
			$ret = "<a class='btn btn-mini {$bclass}' href='$href'><i class='icon-user'></i> View</a>";
			break;
		case 'list_ctl_custom':

			if($this->gO('__acts_custom')!=false){
				$ao=$this->gO('__acts_custom');
				if(isset($ao['_url']) && $ao['_url']!=false && isset($ao['_t']) && $ao['_t']!=false){
					$href = addcslashes(str_replace(array('{id}','{oslug}','{rslug}','{oname}'),array($row['id'],$this->obj_slug,$row[$this->slug_field],$this->oname),$ao['_url']),'"');
					$cls="btn-mini";
					if(isset($ao['_class']))
						$cls=$ao['_class'];
					$ret = "<a class='btn {$cls} {$bclass}' href=\"$href\">{$ao['_t']}</a>";
				}
			}
			break;
		}

		if($this->copts['echo'])
			echo $ret;
		else{
			$this->resetOpts();
			return $ret;
		}
		$this->resetOpts();
	}

	function drawSFFilter()
	{
		$d = dirname(__FILE__);
		$reta=array('r'=>'','u'=>false);
		if (is_file("$d/auto_filters.php")) {
			ob_start();
			global $dbo_sel_default;
			include("$d/auto_filters.php");
			$topts=array("_sffilterID"=>"","sf_filter"=>false,"sf_filter_list"=>false,"sf_filter_order"=>false);
			if (is_array($sys_sf_filters[$this->oname])) {
				$ta = $sys_sf_filters[$this->oname];
				$cf = $this->gO('_sffilterID');
			 	if (isset($_REQUEST['_sffilter'][$this->obj_slug])){
		 			$cf=$_REQUEST['_sffilter'][$this->obj_slug];
					$topts["_sffilterID"]=$cf;
				}
				$topts["sf_filter"]=$ta[$cf]['q2'];
				$topts["sf_filter_list"]=$ta[$cf]['list'];
				$topts["sf_filter_order"]=$ta[$cf]['order'];

				if (isset($ta[$cf]['q2'])) {
/*					$this->storeOpts("sf_filter", $ta[$cf]['q2']);
					$this->storeOpts("sf_filter_list", $ta[$cf]['list']);
					$this->storeOpts("sf_filter_order", $ta[$cf]['order']);*/
				} else{
/*					$this->unstoreOpts("sf_filter");
					$this->unstoreOpts("sf_filter_list");
					$this->unstoreOpts("sf_filter_order");
					*/
				}
				$this->setOpts($topts);
				echo "<form class='sf_filters dbo_tbl_top_form' method='GET' onsubmit=\"jQuery('.dbo_datatable_{$this->oname}').dataTable().api().state.clear();\">";
				if(count($_REQUEST['_sffilter'])){
					foreach($_REQUEST['_sffilter'] as $s=>$v){
						if($s!=$this->obj_slug)
							echo "<input type='hidden' name='_sffilter[$s]' value='$v'>";
					}
				}
				echo "<select name='_sffilter[{$this->obj_slug}]'>";
				echo "<option value=''>$dbo_sel_default</option>";
				if($cf!=false)
					$ret['u']=true;
				asort($ta);
				foreach ($ta as $id => $v) {
					echo "<option " . ($cf == $id ? "selected='true'" : "") . " value='$id'>{$v['t']}</option>";
				}
				echo "</select> ";
				echo "<input type='submit' value='Filter with SF View'>";
				echo "</form>";

			}
			$reta['r']=ob_get_contents();
			ob_end_clean();
		}
		return $reta;
	}

	function storeOpts($name, $val)
	{
		$sn = '_storedOpts';
		if (!is_array($_SESSION[$sn]))
			$_SESSION[$sn] = array();
		if (!is_array($_SESSION[$sn][$this->oname]))
			$_SESSION[$sn][$this->oname] = array();

		if ($name)
			$_SESSION[$sn][$this->oname][$name] = $val;
		else
			$_SESSION[$sn][$this->oname][$name] = $val;
		$this->setOpts(array($name => $val));
	}

	function unstoreOpts($name, $all = false)
	{
		$sn = "_storedOpts";
		if ($all == true)
			unset($_SESSION[$sn][$this->oname]);
		else if ($name != false)
			unset($_SESSION[$sn][$this->oname][$name]);
	}

	function draw_tabs(){
		$this->doInc("dbo_draw_tabs");
	}


	function fn_def($fn,$val,$fromFalse,$iopts=array()){
		if(is_null($val))
			return $val;
		$this->setCopts($iopts);
		$defval=false;
		switch($this->fctrls[$fn]['c']){
			case 'time':
				$defval="00:00:00";
				break;
			case 'datetime':
				$defval="0000-00-00 00:00:00";
				break;
			case 'date':
				$defval="0000-00-00";
				break;
		}
		if($fromFalse==true && $val==false)
			$val=$defval;
		else if($fromFalse==false && (($defval===false && $val==$defval) || $val===$defval))
			$val=false;
//		var_dump($fn,$this->fctrls[$fn]['c'],$val,$defval,"<hr>");


		$this->resetOpts();
		return $val;
	
	}

	function drawDates($fn,$row){
		global $sys_def_date_format,$sys_def_datetime_format,$sys_def_time_format;
		$ret="";
		switch($this->fctrls[$fn]['c']){
			case 'date':
					$ret=date($sys_def_date_format,strtotime($row[$fn]));
					break;
			case 'datetime':
					$ret=date($sys_def_datetime_format,strtotime($row[$fn]));
					break;
			case 'time':
					$ret=date($sys_def_time_format,strtotime($row[$fn]));
					break;
			default:
					$ret=$row[$fn];
		}
		return $ret;
	}

	function rendInit($rn="def"){
		if(!isset($this->rend))
			$this->rend=array();
		$this->rend[$rn]=array();
	}

	function rendAddStag($en,$d,$rn="def"){
		return $this->rendAdd($en,$d,$rn,false,array('key'=>'Stag','set'=>true));
	}
	function rendAddEtag($en,$d,$rn="def"){
		return $this->rendAdd($en,$d,$rn,false,array('key'=>'Etag','set'=>true));
	}
	function rendAddTags($en,$sd,$ed,$rn="def"){
		return $this->rendAdd($en,$sd,$rn,false,array('tag'=>$ed));
	}
	function rendAddBody($en,$d,$rn="def"){
		return $this->rendAdd($en,$d,$rn,false,array('key'=>'body'));
	}
	function rendAddPBody($en,$d,$rn="def"){
		return $this->rendAdd($en,$d,$rn,false,array('key'=>'pbody'));
	}
	function rendAddPre($en,$d,$rn="def"){
		return $this->rendAdd($en,$d,$rn,false,array('key'=>'pre'));
	}
	function rendAddPost($en,$d,$rn="def"){
		return $this->rendAdd($en,$d,$rn,false,array('key'=>'post'));
	}
	function rendAddKey($en,$key,$d,$rn="def"){
		return $this->rendAdd($en,$d,$rn,false,array('key'=>$key));
	}
	function rendMove($en,$d=false,$rn="def"){
		return $this->rendAdd($en,$d,$rn,false,array('move'=>true));
	}
	function rendAdd($en,$d,$rn="def",$ra=false,$ropt=array()){
//		echo "\n\n ============================ \n\n";
		if(!is_array($en))
			$enA=explode(".",$en);
		else
			$enA=$en;
		$sKey=isset($ropt['key'])?$ropt['key']:"";

		if($ra===false)
			$tra=$this->rend[$rn];
		else
			$tra=$ra;
		if(!is_array($tra))
			$tra=array();
		$cN=reset($enA);
		$cK=key($enA);
		$cNX=next($enA);


		if($cN=="*"){
			if(!isset($tra['_cur']))
				$tra['_cur']=0;
			if(isset($ropt['move']) && $ropt['move']==true){
				$tmp=$enA;
				unset($tmp[$cK]);
//				var_dump("MOVE",implode(".",$tmp),$enA);
				if(strpos(implode(".",$tmp),"*")===false){
					if($d===false)
						$tra['_cur']=count($tra)-1;
					else
						$tra['_cur']=$d;
//					var_dump($tra);
					return $tra;
				}
			}
			$cN=$tra['_cur'];
			//			if(!isset($tra[$tra['_cur']]))
			//				$tra[$tra['_cur']]=array();
			//			$tra[$tra['_cur']]=$this->rendAdd($enA,$d,$rn,$tra[$tra['_cur']]);

		}

		if(count($enA)>1){
			unset($enA[$cK]);
			if(!isset($tra[$cN]))
				$tra[$cN]=array();
			if(!isset($tra[$cN]['childs']) && $cNX!='*' )
				$tra[$cN]['childs']=array();
//			var_dump("\n===K",$cN,$cNX,$enA,$tra,$tra[$cN]);

			//			if(isset($tra['body'][$cN])){
			//			$tra=$tra['body'][$cN];
			if($cNX=='*')
				$tra[$cN]=$this->rendAdd($enA,$d,$rn,$tra[$cN],$ropt);
			else
				$tra[$cN]['childs']=$this->rendAdd($enA,$d,$rn,$tra[$cN]['childs'],$ropt);
			//			}
/*			else{
				var_dump("IN",);
				$tra[$cN]=$this->rendAdd($enA,$d,$rn,$tra[$cN]);
}*/

		}else if(count($enA)==1){
//			var_dump("\n--ONE",$cN,$ra,$ra==false,$tra,$d,"\n--\n");
			if(isset($ropt['tag']) && $ropt['tag']!=false){
				$tra[$cN]['Stag']=$d;
				$tra[$cN]['Etag']=$ropt['tag'];
			}else{
				if($sKey!=false){
					if(is_array($d) || (isset($ropt['set']) && $ropt['set']==true))
						$tra[$cN][$sKey]=$d;
					else
						$tra[$cN][$sKey].=$d;
				}else{
					if(is_array($d) || (isset($ropt['set']) && $ropt['set']==true))
						$tra[$cN]=$d;
					else
						$tra[$cN].=$d;
				}
			}
		}
//		var_dump("\n||||||RET TRA",$tra);
		if($ra===false){
			$this->rend[$rn]=$tra;
			return $this->rend[$rn];
		}
//		var_dump("\n+++RET",$tra);
		return $tra;

	}
 
	function rendLayout($cx,$rn="def",$iopts=array()){
		$this->setCopts($iopts);
//		var_dump($this->rend[$rn]);
		//		echo " \n\n =================================== \n\n";
		if(isset($this->rend[$rn]) && is_array($this->rend[$rn]) && count($this->rend[$rn]))
			$this->_rendLayout($cx,$this->rend[$rn]);
		$this->resetOpts();
	}

	function _rendLogic($cx,$k,$pk,$v,$pv){
		$cV=$this->gOAA("cV");
		$retV=false;
		if(isset($cV['rendLogic'])){
			if(!is_array($cV['rendLogic']) && $cV['rendLogic']!=false)	
				$cV['rendLogic']=array($cV['rendLogic']);
			foreach($cV['rendLogic'] as $fk=>$fv){
				$fo=array();
				$fn=$fv;
				if(is_array($fv)){
					$fn=$fk;
					$fo=$fv;
				}
				if(method_exists($this->p,"rend_{$fn}")){
					// $cx - context,$k - key ,$pk - parent key,$v - element,$fo - rendLogic options
					$ret=$this->p->{"rend_{$fn}"}($this,$cx,$k,$pk,$v,$pv,$fo);
					if($ret===false)
						return false;
					if(is_array($ret)){
						$retV=true;
						$v=$ret;
					}
				}
			}
		}
		return ($retV==true ? $v : true);
	}

	function _rendLayout($cx,$el,$pk=false,$pv=false){
		foreach($el as $k=>$v){
/*			echo "\n\n=============================\n\n";
			var_dump($k,$pk,$v);
			echo "\n\n********\n\n";
 */
			if($k==="_cur")
				continue;

			if(is_array($v)){

				if(isset($v['_cur'])){

					$this->_rendLayout($cx,$v,$k,$el);
					continue;
				}

				//				var_dump($k,$pk,$v);die();
				$RL=$this->_rendLogic($cx,$k,$pk,$v,$el);
				if($RL===false)
					continue;
				if(is_array($RL))
					$v=$RL;

				if(isset($v['pre'])){
					echo $v['pre'];
				}
				if(isset($v['Stag'])){
					echo $v['Stag'];
				}
				if(isset($v['body'])){
					if(is_array($v['body']))
						echo implode("",$v['body']);
					else
						echo $v['body'];
				}

				if(isset($v['childs'])){
					$this->_rendLayout($cx,$v['childs'],$k,$el);
				}

				if(isset($v['pbody'])){
					if(is_array($v['pbody']))
						echo implode("",$v['pbody']);
					else
						echo $v['pbody'];
				}

				if(isset($v['Etag'])){
					echo $v['Etag'];
				}
				if(isset($v['post'])){
					echo $v['post'];
				}

			}
/*			else
	var_dump("ERR",$k,$pk,$v);*/
		}
	}

}

?>
