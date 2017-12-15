<?php
$tblCode = time() . rand(100, 999999);

$this->draw_tabs();
$cV = $this->gOA('cV');
if($cV==false || ! (isset($cV['list']) && is_array($cV['list']) && count($cV['list'])>0 )){
echo "<script type='text/javascript'>location.href='" . aurl("/") . "'</script>";
    die('');
}

if ($this->gO('preListTpl'))
    $this->doInc($this->gO('preListTpl'));


$opts=array();
if(is_array($cV['list']['_opts'])){
	$opts=$cV['list']['_opts'];
	unset($cV['list']['_opts']);
}

$list=array_merge($cV['list'],$opts);

unset($list['_searchAll']);

$sf_filtr_list=true;
if (isset($list['_noSFFList'])) {
		$listOpts['_noSFFList']=$list['_noSFFList'];
		if($list['_noSFFList']==true)
			$sf_filtr_list=false;
    unset($list['_noSFFList']);
}

$noSFFilt=true;
if (isset($list['_noSFFilter'])) {
		$listOpts['_noSFFilter']=$list['_noSFFilter'];
		if($list['_noSFFilter']==true)
			$noSFFilt=false;
    unset($list['_noSFFilter']);
}

$_PPR = 'l';
if (isset($list['_hidePPR'])) {
		$listOpts['_hidePPR']=$list['_hidePPR'];
		if($list['_hidePPR']==true)
			$_PPR='';
    unset($list['_hidePPR']);
}
$_srchBox = 'f';
if (isset($list['_hideSrch'])) {
		$listOpts['_hideSrch']=$list['_hideSrch'];
		if($list['_hideSrch']==true)
			$_srchBox='';
    unset($list['_hideSrch']);
}
$_tblInfo='i';
if (isset($list['_hideInfo'])) {
		$listOpts['_hideInfo']=$list['_hideInfo'];
		if($list['_hideInfo']==true)
			$_tblInfo='';
    unset($list['_hideInfo']);
}

$_pageSel='p';
if (isset($list['_hidePages'])) {
		$listOpts['_hidePages']=$list['_hidePages'];
		if($list['_hidePages']==true)
			$_pageSel='';
    unset($list['_hidePages']);
}
if (isset($list['_hideTblAll'])) {
		$listOpts['_hideTblAll']=$list['_hideTblAll'];
		if($list['_hideTblAll']==true)
			$_PPR=$_srchBox=$_tblInfo=$_pageSel='';
    unset($list['_hideTblAll']);
}
if (isset($list['_hideTblTop'])) {
		$listOpts['_hideTblTop']=$list['_hideTblTop'];
		if($list['_hideTblTop']==true)
			$_PPR=$_srchBox='';
    unset($list['_hideTblTop']);
}
if (isset($list['_hideTblBot'])) {
		$listOpts['_hideTblBot']=$list['_hideTblBot'];
		if($list['_hideTblBot']==true)
			$_tblInfo=$_pageSel='';
    unset($list['_hideTblBot']);
}


$rColors = false;
if (isset($list['_rowColors'])) {
		$listOpts['_rowColors']=$list['_rowColors'];
    $rColors = $list['_rowColors'];
    unset($list['_rowColors']);
}
$_ajaxList=false;
if (isset($list['_ajaxList'])) {
		$listOpts['_ajaxList']=$list['_ajaxList'];
    $_ajaxList=$list['_ajaxList'];
    unset($list['_ajaxList']);
}
$_ajaxBase=false;
if (isset($list['_ajaxBaseUrl'])) {
		$listOpts['_ajaxBaseUrl']=$list['_ajaxBaseUrl'];
    $_ajaxBase=$list['_ajaxBaseUrl'];
    unset($list['_ajaxBaseUrl']);
}

$linkBtns = array();
if (isset($list['_link_buttons']) && is_array($list['_link_buttons']))
		$listOpts['_link_buttons']=$list['_link_buttons'];
    $linkBtns = $list['_link_buttons'];

unset($list['_link_buttons']);

$extSort = false;

if (isset($list['_extSort']) && is_array($list['_extSort'])) {
		$listOpts['_extSort']=$list['_extSort'];
    foreach ($list['_extSort'] as $k) {
        $extSort["asc__x__{$k}"] = $this->fctrls[$k]['t'] . " ASC";
        $extSort["desc__x__{$k}"] = $this->fctrls[$k]['t'] . " DESC";
    }
}
unset($list['_extSort']);

if(isset($list['_customClass'])){
		$listOpts['_customClass']=$list['_customClass'];
	unset($list['_customClass']);
}

$colFilters = array();
if (isset($list['_colFilters']) && is_array($list['_colFilters'])) {
		$listOpts['_colFilters']=$list['_colFilters'];
    $colFilters = $list['_colFilters'];
}
unset($list['_colFilters']);
global $sys_def_tbl_rows;
$_defRows=10;
if(isset($list['_def_rows']) && $list['_def_rows']!=false){
		$listOpts['_def_rows']=$list['_def_rows'];
	$_defRows=$list['_def_rows'];
	unset($list['_def_rows']);
}
else if($this->gO('_list_def_rows')!=false)
	$_defRows=$this->gO('_list_def_rows');
else if(isset($sys_def_tbl_rows) && $sys_def_tbl_rows!=false)
	$_defRows=$sys_def_tbl_rows;


$srtSet = '_def_';

if (isset($list['_sort'])) {
		$listOpts['_sort']=$list['_sort'];

    $tS = $list['_sort'];
    unset($list['_sort']);

    $aSort = array();
		foreach ($tS as $fn => $v) {
			$sortok=false;
			$_srt = 0;
			foreach ($list as $lk => $lv) {
				if ($lk === $fn || $lv===$fn){
					$sortok=true;
					break;
				}
				$_srt++;
			}
			if($sortok)
				$aSort[] = "[" . (!is_numeric($_srt) ? 0 : $_srt) . ",'$v']";
		}
    if (count($aSort) > 0)
        $srtSet = '"aaSorting":[' . implode(',', $aSort) . '],';
}

$listFlds=$list;

$_tid="dbo_datatable_{$this->oname}_{$tblCode}";

if($_ajaxList==true){
	$ajax_base_url="/{$this->obj_slug}";
	if($_ajaxBase==='slug' && isset($_REQUEST['slug_req']) && $_REQUEST['slug_req']!=false)
		$ajax_base_url="/{$_REQUEST['slug_req']}";
	if($_ajaxBase!==true && $_ajaxBase!=false){
		$ajax_base_url=$_ajaxBase;
	}

}

?>
<div id = "dbo_tbl_wrap_<?php echo $_tid; ?>" class = "dbo_table_wrap dbo_datatable_wrap dbo_<?php echo $this->oname; ?>_table_wrap" >
    <div class = 'dbo_table_acts_wrap top' >
        <?php
				$tblBtns=$colsF=array('h'=>'','v'=>'');
        if (count($colFilters) > 0)
					$colsF=$this->p->draw_colsFilters($this->oname, $colFilters);

				if($colsF['v']!="")
					echo $colsF['v']."&nbsp;";

				$tblBtns=$this->p->draw_tbl_buttons($linkBtns, $tblCode, $this->oname);

				if($tblBtns['h']!="")
					echo $tblBtns['h']."&nbsp;";
				$bCont = false;
        $bWrap = "<div class='drop_btn tbl_menu_btn top_acts'><div class='btn'>Actions <span class='caret'></span></div><div class='acts_menu_wrap drop_wrap'>";
            if ($tblBtns['v']!="") {
                $bCont = true;
                echo $bWrap;
            }
        if (isset($cV['acts']) && is_array($cV['acts']) && array_search("i", $cV['acts']) !== false) {
						echo $this->drawActBtn("list_ctl_i",false,array('_drawBtnClass'=>"dbo_top"));
						if($bCont)
							echo "<br>";
				}
        if (isset($cV['acts']) && is_array($cV['acts']) && array_search("t", $cV['acts']) !== false) {
            echo $this->drawActBtn("list_ctl_t",false,array('_drawBtnClass'=>"dbo_top"));
            if($bCont)
                echo "<br>";
        }
				if($tblBtns['v']!=""){
					echo $tblBtns['v'];
				}

        if ($bCont == true) {
            echo "</div></div>";
        }
				$fUsed='';
        ob_start();
        if (is_array($extSort) && count($extSort) > 0) {
            if (isset($_REQUEST['_ext_sort'][$this->oname])) {
                $_SESSION['_ext_sort'][$this->oname] = $_REQUEST['_ext_sort'][$this->oname];
						}
						if(isset($_SESSION['_ext_sort'][$this->oname]) && $_SESSION['_ext_sort'][$this->oname]!=false)
							$fUsed='hlb';
            echo "<form method='POST' class='ext_sort dbo_tbl_top_form'>";
            echo "<select name='_ext_sort[{$this->oname}]'>";
            echo "<option value=''> -------- </option>";
            foreach ($extSort as $k => $v)
                echo "<option " . ($_SESSION['_ext_sort'][$this->oname] == $k ? "selected='true'" : "") . " value='$k'>$v</option>";
            echo "</select> <input type='submit' value='Sort'>";
            echo "</form>";
        }

				if($noSFFilt==true){
				$sfRet=$this->drawSFFilter();
				echo $sfRet['r'];
				if($sfRet['u']==true)
					$fUsed='hlb';
				if($colsF['hu']==true)
					$fUsed='hlb';
				}

				if($colsF['h']!="")
					echo $colsF['h'];
        $fsAndSorts = ob_get_contents();
        ob_end_clean();
        if ($fsAndSorts != false) {
            echo "<div class='$fUsed top_filter_wrap drop_btn tbl_menu_btn tbl_menu_btn_2nd top_acts'><div class='btn'>Filters and Sorts <span class='caret'></span></div><div class='acts_menu_wrap drop_wrap'>";
            echo $fsAndSorts;
            echo "</div></div>";
        }
        ?>
    </div >
    <table id = '<?php echo $_tid;?>' class = 'dbo_datatable dbo_datatable_hidden dbo_list dbo_list_offpage dbo_def_offpage dbo_def_offpage_edit dbo_def_offpage_<?php echo $this->oname ?> dbo_datatable_<?php echo $this->oname ?>' >
<?php
	$queryFilter=array();
	if ($this->gO("sf_filter") != false)
		$queryFilter[] = $this->gO("sf_filter");
	if (is_array($_SESSION['_colfilter'][$this->oname])) {
		foreach ($_SESSION['_colfilter'][$this->oname] as $k => $v) {
			if ($v != false){
				$ffc=$this->prepCtrl(false,$k,false);
				if(is_array($ffc['treatSame'])){
					$tta=array();
					foreach($ffc['treatSame'] as $tsv)
						$tta[$tsv]="ct.{$k}='$tsv'";
					$queryFilter[]="(".implode(" or ",$tta).")";
				}else
					$queryFilter[] = "ct.{$k}='$v'";

			}

		}

	}
	if(count($queryFilter)>0)
		$this->copts['queryFilter']=$queryFilter;
				
        $hh = $this->gO("listHeader");
        $colOpts = array();
        if (is_array($hh)) {
            echo "<thead><tr class='dbo_header dbo_{$this->oname}'>";
            foreach ($hh as $v)
                echo "<td><span>$v</span></td>";
            echo "</tr></thead>";
        } else if (!$this->gO('noHeader')) {
            echo "<thead><tr class='dbo_header dbo_{$this->oname}'>";
            //	echo "<th><span>Controls</span></th>";
						$colN = 0;
						if($sf_filtr_list==true && $this->gO('sf_filter_list'))
							$listFlds=$this->gO('sf_filter_list');
            
						foreach ($listFlds as $fnkk => $fn) {
                if ($fnkk==='_idc_' || $fn === '_idc_') {
/*                    if ($colN == 0 && $srtSet === '_def_')
												$srtSet = '"aaSorting":[[1,"asc"]],';
 */
                    echo "<th class='dbo_list_idc'><span><input class='idc_topt_for_{$tblCode}' type='checkbox' onchange=\"if(jQuery(this).is(':checked')){window.cb_tot_{$tblCode}=jQuery('.idc_tbl_{$tblCode}_rows').attr('checked','true').length;}else{window.cb_tot_{$tblCode}=0;jQuery('.idc_tbl_{$tblCode}_rows').removeAttr('checked')};\"></span></th>";
                    $colOpts[] = array("name"=>"_idc_","data" => "_idc_", "class" => "dbo_td dbo_{$this->oname}_td dbo_idc ", "orderable" => false);
                } else if ($fnkk === '_acts_' || $fn === '_acts_') {
                    echo "<th class='dbo_list_acts'><span>" . (isset($cV['labels'][$fn]) ? $cV['labels'][$fn] : "Actions") . "</span></th>";
                    $colOpts[] = array("name"=>"_acts_","data" => "_acts_", "class" => "dbo_td dbo_{$this->oname}_td dbo_list_acts","orderable" => false);
                } else { //if(in_array($fn,$this->flds))
                    /*				if($fnkk!=false && is_array($fn)){
                                        $fc=$this->fctrls[$fnkk];
                                        $fc=array_merge($fc,$fn);
                                        $fn=$fnkk;
                                    }else
																			$fc=$this->fctrls[$fn];*/
										if($srtSet === '_def_')
												$srtSet = '"aaSorting":[['.$colN.',"asc"]],';
                    $fc = $this->prepCtrl($fnkk, $fn, false);
                    
										echo "<th class='" . (isset($fc['sfdata']['st']) && $fc['sfdata']['t'] == 'currency' ? " dbo_numeric_col" : "") . "'><span>" . (isset($fc['t']) ? $fc['t'] : $fn) . "</span></th>";
										$ttO = array("name"=>str_replace(".", "__x__", strtolower($fn)),"data" => str_replace(".", "__x__", strtolower($fn)), "class" => "dbo_td dbo_{$this->oname}_td dbo_{$this->oname}_td_{$fn}" . (isset($fc['sfdata']['st']) && $fc['sfdata']['t'] == 'currency' ? " dbo_numeric_col" : ""));

										$colOpts[]=$ttO;
                }
                $colN++;
            }

            echo "</tr></thead>";
        }
        echo "<tbody>";

				//echo "<tr><td colspan='".count($cV['list'])."'>Loading data....</td></tr>";
				if($_ajaxList==false){
					$_REQUEST['tcode']=$tblCode;
					echo $this->listDef("custom_view_list");
				}

        echo "</tbody>";

        /*
        $hh=$this->gO("listHeader");
        if(is_array($hh)){
            echo "<tfoot><tr class='dbo_header dbo_{$this->oname}'>";
            foreach($hh as $v)
                echo "<td><span>$v</span></td>";
            echo "</tr></tfoot>";
        }else if(!$this->gO('noHeader')){
            echo "<tfoot><tr class='dbo_header dbo_{$this->oname}'>";
        //	echo "<th><span>Controls</span></th>";
            foreach($cV['list'] as $fnkk=>$fn){
                if($fnkk!=false && is_array($fn)){
                    $fc=$this->fctrls[$fnkk];
                    $fc=array_merge($fc,$fn);
                    $fn=$fnkk;
                }else
                    $fc=$this->fctrls[$fn];
                if($fn=='_acts_')
                    echo "<th><span>".(isset($cV['labels'][$fn])?$cV['labels'][$fn]:"Actions")."</span></th>";
                else if(in_array($fn,$this->flds))
                    echo "<th><span>".(isset($fc['t'])?$fc['t']:$fn)."</span></th>";
            }

            echo "</tr></tfoot>";
        }
         */

        if ($srtSet === '_def_')
            $srtSet = '"aaSorting":[[0,"asc"]],';

        ?>

    </table >


<div class = 'dbo_table_acts_wrap bottom' >

<?php
				if($tblBtns['h']!="")
					echo $tblBtns['h'];
				
    if ($bCont == true)
        echo "<div class='drop_btn tbl_menu_btn bot_acts'><div class='btn'>Actions <span class='up_caret'></div><div class='acts_menu_wrap drop_wrap'>";

    if (isset($cV['acts']) && is_array($cV['acts']) && array_search("i", $cV['acts']) !== false) {
						echo $this->drawActBtn("list_ctl_i",false,array('_drawBtnClass'=>"dbo_bottom"));
						if($bCont)
							echo "<br>";
		}

				if($tblBtns['v']!="")
					echo $tblBtns['v'];
//    echo $this->p->draw_tbl_buttons($linkBtns, $tblCode, $this->oname);

    if ($bCont == true)
        echo "</div></div>";

		?>
</div>
<?php
		$sDom='<"tbl_top"'.$_PPR.$_srchBox.'>Wr<"tbl_scroller"t><"tbl_foot"'.$_tblInfo.'>'.$_pageSel;
		$rppOpts=array(5, 10, 25, 50, 100, 500);
		$putDef=true;
		$rppO=array();
		foreach($rppOpts as $rv){
			if($putDef==true && (int)$_defRows<=(int)$rv){
				$rppO[$_defRows]=$_defRows;
				$putDef=false;
			}
			$rppO[$rv]=$rv;
		}
		if($putDef)
				$rppO[$_defRows]=$_defRows;
?>
    <script type = 'text/javascript' >
        jQuery('#<?php echo $_tid;?>').show().dataTable(
				{
								"sDom":'<?php echo $sDom;?>',
                "bJQueryUI": false,
                "sPaginationType": "full_numbers",
                "oSearch": {"bSmart": false},
								"iDisplayLength": <?php echo $_defRows;?>,
								"aLengthMenu": [[<?php echo implode(",",$rppO);?>], [<?php echo implode(",",$rppO);?>]],
                /*										"oColumnFilterWidgets": {
                 "aiExclude": [ 0,1,4 ]
                 ,"iMaxSelections":1
                 },*/

                "oLanguage": {
                    "sLengthMenu": " _MENU_ ",
                    "sSearch": "_INPUT_"
								},
/*								"columnDefs":[{
									"targets":'_all',
									"data":null,
									"render":{
										'sort':'sort'
									}
				}],*/
                "columns":<?php echo json_encode($colOpts);?>,
                "stateSave": true,
                "deferRender": true,
								<?php echo $srtSet;
								if($_ajaxList==true){
								?>
                "bProcessing": true,
                "bServerSide": true,
								"ajax": "<?php echo aurl("{$ajax_base_url}?ajax=true&dataTable=true&tcode={$tblCode}".(isset($_REQUEST['slug_req']) && $_REQUEST['slug_req']!=false?"&slr=".urlencode($_REQUEST['slug_req']):"")); ?>",

								<?php	} ?> 
                "fnRowCallback": function (row, data, iDisp) {
if($(row).hasClass('yellow_circle')){
    $(row).find('td.dbo_cls_td_status').prepend('<span class="ui-icon ui-icon-bullet" style="display:inline-block;background-color:yellow"></span>');
}else if($(row).hasClass('green_mark')){
    $(row).find('td.dbo_cls_td_status').prepend('<span class="ui-icon ui-icon-circle-check" style="display:inline-block;background-color:green"></span>');
}else if($(row).hasClass('red_cross')){
    $(row).find('td.dbo_cls_td_status').prepend('<span class="ui-icon ui-icon-circle-close" style="display:inline-block;background-color:red"></span>');
}

                    <?php

                    if(is_array($rColors)){
											$fAr=array();
                        foreach($rColors as $fn=>$v){
                    /*		$ak=array_search($fn,$cV['list']);
                            if($ak===false && isset($cV['list'][$fn])){
                                $ak=0;
                                foreach($cV['list'] as $_ak=>$_av){
                                    if($_ak==$fn)
                                        break;
                                    $ak++;
                                }
                            }
										 */
													if(is_array($v)){
														$ak=strtolower($fn);
														foreach($v as $CCC=>$CCK){
															$fAr[]="
															cv=data.{$ak};
															cd=jQuery('<div>'+cv+'</div>').find('div.coData').html();
															if({$CCK}){
																jV=jQuery(row);
																jV.attr('style','{$CCC}'+jV.attr('style'));

														}
														";
														}
													}else{
														$fAr[]="
															if({$v}){
																jV=jQuery(row);
																jV.attr('style','{$fn}'+jV.attr('style'));

														}
														";
														}
                        }
                        if(count($fAr)>0){
                            echo implode("\n",$fAr);
                        }

                    }
                    ?>
                }
            }
		);
			_tv=jQuery('#dbo_tbl_wrap_<?php echo $_tid; ?>').find('.dbo_table_acts_wrap.top > * , .tbl_top > *')
			if(_tv.length==0)
				jQuery('#<?php echo $_tid; ?>_wrapper .tbl_scroller').css('margin-top','-45px');
			_tv=jQuery('#dbo_tbl_wrap_<?php echo $_tid; ?>').find('.dataTables_paginate, .dbo_table_acts_wrap.bottom > *');
			if(_tv.length==0)
				jQuery('#<?php echo $_tid; ?>_wrapper .tbl_foot').css('margin-bottom','0px');

			
    </script>

		<?php if($_ajaxList==true){
			$tCV=$this->gO('cV');
			$tCV['list']=$listFlds;
			$tCV['list']['_opts']=$listOpts;
			$this->setCopts(array('cV'=>$tCV));
			echo $this->p->sFA2($tblCode);
			$this->resetOpts();
			}?>

<?php
//var_dump(array_keys($_SESSION['ajaxStore']));
    if ($this->gO('postListTpl'))
        $this->doInc($this->gO('postListTpl'));
    ?>

</div >
