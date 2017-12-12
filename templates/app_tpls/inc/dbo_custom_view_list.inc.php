<?php
if(!isset($_ajaxList))
	$_ajaxList=false;

$cV = $this->gOA("cV");
$tblCode = $_REQUEST['tcode'];
unset($cV['list']['_rowColors']);
unset($cV['list']['_sort']);
unset($cV['list']['_link_buttons']);
unset($cV['list']['_extSort']);
unset($cV['list']['_colFilters']);
unset($cV['list']['_searchAll']);
unset($cV['list']['_ajaxList']);
unset($cV['list']['_hidePPR']);
unset($cV['list']['_hideSrch']);
unset($cV['list']['_hideInfo']);
unset($cV['list']['_hidePages']);
unset($cV['list']['_def_rows']);
unset($cV['list']['_hideTblAll']);
unset($cV['list']['_hideTblBot']);
unset($cV['list']['_hideTblTop']);
unset($cV['list']['_def_rows']);
unset($cV['list']['_ajaxBaseUrl']);
unset($cV['list']['_noSFFList']);
unset($cV['list']['_noSFFilter']);
$listOpts=array();
if(is_array($cV['list']['_opts'])){
	$listOpts=$cV['list']['_opts'];
	unset($cV['list']['_opts']);
}

$custClass=false;
if(isset($cV['list']['_customClass']) || isset($listOpts['_customClass'])){
	if(isset($cV['list']['_customClass']) && ! isset($listOpts['_customClass']))
		$listOpts['_customClass']=$cV['list']['_customClass'];
	$custClass=$listOpts['_customClass'];
	unset($cV['list']['_customClass']);
}


//var_dump($row);die("OK");
$ifc = $this->fctrls;

$cvList=$cV['list'];

/*if($this->gO('sf_filter_list'))
	$cvList=$this->gO('sf_filter_list');
 */
//var_dump($cvList);
foreach ($cvList as $fnkk => $fn) {
    $this->fctrls = $ifc;
    if ($fn === '_idc_' || $fnkk === '_idc_') {
        $idf = "id";
        if ($fnkk === '_idc_') {
            $idf = $fn;
        }
        $cols['_idc_'] = "<td><input type='checkbox' value='{$row[$idf]}' class='_idc_items idc_tbl_{$_REQUEST['tcode']}_rows' onchange=\"if(typeof window.cb_tot_{$tblCode} == 'undefined'){window.cb_tot_{$tblCode}=0;}if(jQuery(this).is(':checked')){window.cb_tot_{$tblCode}++;}else if(window.cb_tot_{$tblCode}>0){window.cb_tot_{$tblCode}--;}if(window.cb_tot_{$tblCode}==jQuery('.idc_tbl_{$tblCode}_rows').length){jQuery('.idc_topt_for_{$tblCode}').attr('checked','true')}else{jQuery('.idc_topt_for_{$tblCode}').removeAttr('checked');}\"></td>";
        continue;

    } else if ($fn === '_acts_' || $fnkk === '_acts_') {
        /*		if(isset($cV['acts']))
                    $ctls=$cV['acts'];
                else if($fnkk=='_acts_' && is_array($fn)){
                    $ctls=$fn;
                }
                unset($ctls['i']);
                $btns=array();
                foreach($ctls as $cv){
                    $btns[]=$this->drawActBtn("list_ctl_{$cv}",$row,array("echo"=>false));
                }
                $cols['_acts_']=implode("&nbsp;",$btns);*/
        $cols['_acts_'] = $this->actBtnWrap($fnkk, $fn, $row);
        continue;
    } else {
        /*		if($fnkk!=false && is_array($fn)){
                    $this->fctrls[$fnkk]=array_merge($this->fctrls[$fnkk],$fn);
                    $fn=$fnkk;
                }

                $fc=$this->fctrls[$fn];

                if(!in_array($fn,$this->flds)){
                    continue;
                }*/
        $fc = $this->prepCtrl($fnkk, $fn, true);
				$col = "";
        if (isset($fc['addData']) && $fc['addData'] == true)
            $col .= "<div style='display:none' class='coData'>{$this->cC[$fn]}</div>";
        if (!(isset($fc['emptyVals']) && is_array($fc['emptyVals']) && in_array($this->cD[$fn], $fc['emptyVals']))) {
					$varr = $this->drawValue($fn, $row);
  //          $col .= "<pre>" . implode("<br>", $varr) . "</pre>";
            $col .= implode("<br>", $varr) ;
        } else
					$col .= "";
				if($_ajaxList==false){
					$_tdc="<td ";
					if(isset($fc['extSort']) && $fc['extSort']!=false)
						$_tdc.='data-sort="'.addcslashes($this->cC[($fc['extSort']===true?$fn:$fc['extSort'])],'"').'"';
					$_tdc.=" >";
					$col=$_tdc.$col."</td>";
				}
				/*else{
//					$col=array('def'=>$col);
				}*/
				$tkk=str_replace(".", "__x__", strtolower($fn));
				$cols[$tkk] = $col;
				if(in_array($fc['c'],array('date','datetime')) && isset($this->cD["{$fn}_ts"]))
					$cols[$tkk."_ts"] = $this->cD["{$fn}_ts"];

		}
}
$rid="row_{$this->oname}_{$this->cD['id']}";
$rclass="dbo_tr dbo_{$this->oname}_tr dbo_{$this->oname}_list";

if($custClass!=false ){
	if(method_exists($custClass,$this->t)){
		$rclass.=" ".call_user_method($custClass,$this->t,$this);
	}else{
		//	try{
		$rclass.=" ".eval($custClass);
		//	}catch(Exception $ee){	}
	}
}
if($_ajaxList==true){
	global $_listAjaxData;
	$cols['DT_RowId'] = $rid;
	$cols['DT_RowClass'] = $rclass;
	
	$_listAjaxData["data"][] = $cols;
}else{
	echo "<tr id = '{$rid}' class = '{$rclass}'>";
	echo implode("",$cols);
	echo "</tr>";
}

?>
