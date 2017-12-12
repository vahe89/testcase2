<?php
global $_listAjaxData;
if(!(isset($_REQUEST['tcode']) && $_REQUEST['tcode']!=false))
	die('TCERR');

$tblCode=$_REQUEST['tcode'];
$this->p->rFA($tblCode);
$cV = $this->gOA('cV');
$listOpts=array();
if(is_array($cV['list']['_opts'])){
	$listOpts=$cV['list']['_opts'];
	unset($cV['list']['_opts']);
}
$list=array_merge($cV['list'],$listOpts);
unset($cV['list']['_rowColors']);
unset($cV['list']['_sort']);
unset($cV['list']['_link_buttons']);
$extSort = false;
if (isset($list['_extSort'])){
		$listOpts['_extSort']=$list['_extSort'];
    $extSort = $list['_extSort'];
}
unset($cV['list']['_extSort']);
unset($cV['list']['_colFilters']);
unset($cV['list']['_noSFFList']);
unset($cV['list']['_customClass']);
unset($cV['list']['_noSFFilter']);

$sAll = false;
$dsAll=array();
if (isset($list['_searchAll']) && $list['_searchAll'] != false) {
		$listOpts['_searchAll']=$list['_searchAll'];
    $sAll = $list['_searchAll'];
    if (!is_array($sAll))
        $sAll = array();
}
unset($cV['list']['_searchAll']);
unset($cV['list']['_ajaxBaseUrl']);

$_listAjaxData = array(
    "draw" => intval($_REQUEST['draw']),
    "data" => array()
	);
$opts = array();
/*if(!is_array($_SESSION['_ajaxList']))
	$_SESSION['_ajaxList']=array();
 */
if (isset($_REQUEST['start']) && isset($_REQUEST['length'])) {
    $opts['queryLimit'] = "{$_REQUEST['start']},{$_REQUEST['length']}";
//	$_SESSION['_ajaxList'][$this->oname]=$opts['queryLimit'];
}
/*else if(isset($_SESSION['_ajaxList'][$this->oname]) && preg_match("#^[0-9]+,[0-9]+$#",$_SESSION['_ajaxList'][$this->oname]))
	$opts['queryLimit']=$_SESSION['_ajaxList'][$this->oname];
 */


if (isset($_REQUEST['columns']) && is_array($_REQUEST['columns'])) {
	$sfRels = array();
	$sOrder = array();
	$qWhere = array();
	$qWhereC = array();
	$listCols=array();
	$lList=array();
	$cvList=$cV['list'];
	$lList2cv=array();
/*	if($this->gO('sf_filter_list'))
		$cvList=$this->gO('sf_filter_list');
 */
//var_dump($cvList);

	foreach($cvList as $llk=>$llv){
		$tto=$llv;
		if(is_array($llv))
			$tto=$llk;
		$ttv=preg_replace("#{$this->oname}[.]#i","",$tto);	
		$lList2cv[$ttv]=$tto;
		$lList[strtolower(str_replace('.','__x__',$ttv))]=$ttv;
		//lList[strtolower(str_replace('__x__','.',$ttv))]=$ttv;
	}
	foreach ($_REQUEST['columns'] as $coN => $coD) {
		if(isset($coD['data']) && $coD['data']!=false)
			if(in_array($coD['data'],array('_idc_','_acts_')))
				continue;
		if(!isset($lList[$coD['data']]))
			continue;
		$fn=$lList2cv[$lList[$coD['data']]];
		if(isset($cvList[$fn])){
			$fc=$this->prepCtrl(false,$fn,false);
			$listCols[$fn]=array('r'=>$coD,'fc'=>$fc);
		}else if(in_array($fn,$cvList))
			$listCols[$fn]=array('r'=>$coD,'fc'=>$this->fctrls[$fn]);
	}
	foreach($listCols as $fn=>$fnD){
		$dsAll[$fn]=$fn;
		$fnq="ct.{$fn}";
		if(!in_array($fn,$this->flds)){
			$sfRels[$fn]=$fn;
			$fnq=preg_replace("#__c[.]#","__r.",$fn);
			$fnq=str_replace(".","__x__",$fnq);
		}
		$listCols[$fn]['fnq']=$fnq;
		$r=$fnD['r'];
		$fc=$fnD['fc'];
		if (isset($r['searchable']) && $r['searchable'] == "true"){
			$svals=array();
			if(isset($_REQUEST["search"]['value']) && $_REQUEST["search"]['value'] != false)
				$svals[0]=$_REQUEST["search"]['value'];
			if(isset($r["search"]['value']) && $r["search"]['value'] != false)
				$svals[1]=$r["search"]['value'];
			if(count($svals)>0){
				foreach($svals as $svk=>$sval){
					$sval=str_replace(' ','%',$sval);
					if (isset($this->rels[$fn])) 
						$tqq = "{$this->rels[$fn]['tbln']}.{$this->rels[$fn]['fld']} like '%" . $this->db->escape($sval) . "%'";
					else
						$tqq = "{$fnq} like '%" . $this->db->escape($sval) . "%'";
					if($svk===0)
						$qWhere[]=$tqq;
					else
						$qWhereC[]=$tqq;

				}
			}
		}
	}
	if( ! (isset($_REQUEST['order']) && is_array($_REQUEST['order']) && count($_REQUEST['order'])>0 ))
		$sOrder[]="ct.id desc";
	else{
		foreach($_REQUEST['order'] as $ov){
			if(!isset($lList[$_REQUEST['columns'][$ov['column']]['data']]))
				continue;
			$fn=$lList[$_REQUEST['columns'][$ov['column']]['data']];
			if(!isset($listCols[$fn]))
				continue;
			$fnD=$listCols[$fn];
			$r=$fnD['r'];
			$fc=$fnD['fc'];
			$fnq=$fnD['fnq'];
			$odir='asc';
			if(isset($ov['dir']) && strtolower($ov['dir'])=='desc')
				$odir='desc';
			if (isset($r["orderable"]) &&  $r["orderable"]== "true") {
				if (isset($this->rels[$fn]))
					$sOrder[] = "{$this->rels[$fn]['tbln']}.{$this->rels[$fn]['fld']} $odir ";
				else{
					$sOrder[] = "{$fnq} $odir ";
				}
			}
		}
	}

	//===========================

	if (is_array($extSort)) {
		if (isset($_SESSION['_ext_sort'][$this->oname]) && $_SESSION['_ext_sort'][$this->oname] != false) {
			$esa = explode("__x__", $_SESSION['_ext_sort'][$this->oname]);
			if (in_array($esa[1], $extSort))
				$sOrder = array_merge(array("{$esa[1]} {$esa[0]}"),$sOrder);
		}
	}
	if (count($sOrder) > 0)
		$opts['queryOrder'] = implode(",", $sOrder);

	$wh = "";
	if (is_array($sAll) && isset($_REQUEST['search']['value']) && $_REQUEST['search']['value'] != false) {
		foreach ($this->flds as $fn) {
			if (!in_array($fn, $sAll) && !in_array($fn, $dsAll)) {
				if (isset($this->rels[$fn]))
					$qWhere[] = "{$this->rels[$fn]['tbln']}.{$this->rels[$fn]['fld']} like '%" . $this->db->escape($_REQUEST['search']['value']) . "%'";
				else
					$qWhere[] = "ct.{$fn} like '%" . $this->db->escape($_REQUEST['search']['value']) . "%'";
			}
		}
	}
	if (count($qWhere) > 0)
		$wh = "(" . implode(" or ", $qWhere) . ")";
	if (count($qWhereC) > 0) {
		if ($wh == false)
			$wh = "(" . implode(" and ", $qWhereC) . ")";
		else
			$wh = "((" . implode(" and ", $qWhereC) . ") and ($wh))";
	}
	if ($wh != false)
		$opts['queryWhere'] = array($wh);


//	$opts['query_sfRels'] = $sfRels;
/*
	if ($this->gO("sf_filter") != false)
		$opts['queryFilter'][] = $this->gO("sf_filter");
	if (is_array($_SESSION['_colfilter'][$this->oname])) {
		foreach ($_SESSION['_colfilter'][$this->oname] as $k => $v) {
			if ($v != false){
				if(is_array($listCols[$k]['fc']['treatSame'])){
					$tta=array();
					foreach($listCols[$k]['fc']['treatSame'] as $tsv)
						$tta[$tsv]="ct.{$k}='$tsv'";
					$opts['queryFilter'][]="(".implode(" or ",$tta).")";
				}else
					$opts['queryFilter'][] = "ct.{$k}='$v'";

			}

		}

	}
 */
	//var_dump($opts);
	
	$this->listDef("custom_view_ajaxrequest", $opts);
	$_listAjaxData["recordsTotal"] = (int)$this->numQ;
	$_listAjaxData["recordsFiltered"] = (int)$this->numQ;

	die(json_encode($_listAjaxData));
}
die('{"error":"Bad request"}');
?>
