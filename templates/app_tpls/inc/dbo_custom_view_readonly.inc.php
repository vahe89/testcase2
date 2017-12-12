<?php
$cV = $this->gOA("cV");

if (isset($cV['view']['onVal']) && is_array($cV['view']['onVal']) && count($cV['view']['onVal'])>0){
	$eok=true;
	foreach($cV['view']['onVal'] as $fn=>$fv){
		if($this->cD[$fn]!=$fv){
			$eok=false;
			break;
		}
	}
	if($eok==false){
		echo "<script type='text/javascript'>location.href='".aurl('/'.$this->obj_slug)."'</script>";
		die();
	}
}



$ifc = $this->fctrls;


$flat = true;

if (!isset($cV['view']))
    $cV['view'] = $cV['edit'];

if (isset($cV['view']['_flat']))
    $flat = $cV['view']['_flat'];

if (isset($cV['view']['_l']['_w']) && $cV['view']['_l']['_w'] != false) {
    $tbl_width = $cV['view']['_l']['_w'];
    unset($cV['view']['_l']['_w']);
}
$tbl_wdth = "";
if ($tbl_width != false)
    $tbl_wdth = "style='width:{$tbl_width}'";

$_l=false;
if(is_array($cV['view']['_l']))
	$_l=$cV['view']['_l'];

	if(isset($cV['viewSel']) && is_array($cV['viewSel'])){
		foreach($cV['viewSel'] as $vn=>$va){
			$sok = true;
			foreach($va as $vfn=>$vfv){
				if (!(isset($this->cD[$vfn]) && ($this->cD[$vfn] == $vfv))) {
					$sok = false;
					break;
				}
			}
			if (($sok == true) && is_array($cV['view'][$vn])) {
				$_l=$cV['view'][$vn];
				break;			
			}
		}
	}

if (!is_array($_l)) {
    if (isset($this->obj_slug))
        echo "<script type='text/javascript'>location.href='" . aurl("/" . $this->obj_slug) . "'</script>";
    else
        echo "<script type='text/javascript'>location.href='" . aurl("/") . "'</script>";
    die();
}
$cV['view']['_l'] = $_l;

if (isset($cV['view']['_style']) && $cV['view']['_style'] != false) {
    echo "<style>{$cV['view']['_style']}</style>";
}
echo "<table {$tbl_wdth} class='dbo_roview dbo_offpage_table dbo_{$this->oname}_offpage_table'>";

$baseflat = $flat;


global $sys_history_def_show,$sys_history_tracking;
if($sys_history_tracking==true && ($sys_history_def_show==true || $this->gO('history_list')==true)){
	$this->p->v['objs']['sys_change_history']=array('no_user_filter_allowed' => true);
	$cV['view']['_l']['__sys_history_row']['_t']="Change History";
	$cV['view']['_l']['__sys_history_row']['list']=array("sys_change_history"=>array("username","time","upd_fields","_queryWhere"=>"ct.obj='{$this->oname}' and ct.obj_id={id}","_sort"=>array('time'=>'desc')));
}

foreach ($cV['view']['_l'] as $rows) {
	$this->rendInit();

//	$rend=array('rows'=>array());
//	$RR=array("Etag"=>"</tr>");
    $flat = $baseflat;
    if (isset($rows['_flat']))
        $flat = $rows['_flat'];
    unset($rows['_flat']);

    if (isset($rows['_showLogic']) && is_array($rows['_showLogic'])) {
        $op = "and";
        if (isset($rows['_showLogic']['__op'])) {
            $op = strtolower($rows['_showLogic']['__op']);
            unset($rows['_showLogic']['__op']);
        }
        $skipFlag = false;
        if ($op == 'or')
            $skipFlag = true;

        foreach ($rows['_showLogic'] as $dFn => $dFv) {
            if (isset($this->cD[$dFn]) && ((is_array($dFv) && in_array($this->cD[$dFn], $dFv)) || $this->cD[$dFn] == $dFv)) {
                if ($op == 'or') {
                    $skipFlag = false;
                    break;
                }
            } else if ($op != 'or') {
                $skipFlag = true;
                break;
            }
        }
        if ($skipFlag)
            continue;

        unset($rows['_showLogic']);
    }

    $row_width = false;
    if (isset($rows['_w']) && $rows['_w'] != false) {
        $row_width = $rows['_w'];
        unset($rows['_w']);
    }

		$preListTit="";
    if (isset($rows['_t'])) {
        $ttt = $rows['_t'];
        unset($rows['_t']);
        $cs = count($rows);
				$preListTit="<tr><td colspan='10'><div class='edit_title row legend'>{$ttt}</div></td></tr>";
//				$RR['title']=$preListTit;
				$this->rendAddKey("rows.*","title",$ttt);
				$this->rendAddPre("rows.*",$preListTit);
		}
		
// ====== START LIST =================================		
		if (isset($rows['list']) && is_array($rows['list'])) {
//			$RR['lists']=array();
			foreach ($rows['list'] as $o => $ll) {
				//				if(!is_object($this->p->t[$o]))
				//					continue;
				$oA=explode(".",$o);
				$oRelFn=false;
				if(count($oA)>1){
					$o=$oA[0];
					$oRelFn=$oA[1];
				}
				
				if(!isset($this->p->v['objs'][$o])){
					continue;
				}

				//echo $preListTit;
				$preListTit="";


				$lopts = array("ownHeader" => true,'queryWhere'=>array());
				$ltCV = array(					
					//'no_user_filter_allowed'=>true,
					"list" => $ll);

				$ltCV['list']['_noSFFList']=true;
				$ltCV['list']['_ajaxBaseUrl']=true;
				$newDefs=array();
				if(isset($ll['_queryWhere']) && $ll['_queryWhere']!=false){
					$qw=$ll['_queryWhere'];
					if(preg_match("#{([^}]+)}#mi",$ll['_queryWhere'],$tll)){
					for($ii=1;$ii<count($tll);$ii++)
						$qw=str_replace("{{$tll[$ii]}}",$this->cD[$tll[$ii]],$qw);
					}

					$lopts['queryWhere'][] = $qw;
					unset($ltCV['list']['_queryWhere']);
				}else{
					if (isset($this->p->t[$o]->rels)) {
						foreach ($this->p->t[$o]->rels as $rf => $ra) {
							if (isset($ra['tbl']) && $ra['tbl'] == $this->tbl && isset($ra['on']) && $ra['on'] == 'id') {
								if($oRelFn==false || $oRelFn==$rf){
									$lopts['queryWhere'][] = "ct.{$rf}={$this->cD['id']}";
									$newDefs[$rf]=$this->cD['id'];
									if(isset($this->cD[$ra['fld']]) && $this->cD[$ra['fld']]!=false)
										$newDefs["r_{$rf}"]=$this->cD[$ra['fld']];
									else if(isset($this->cD['Name']) && $this->cD['Name']!=false)
										$newDefs["r_{$rf}"]=$this->cD['Name'];
									break;
								}
							}
						}
					}
				}
				if(isset($ll['_newFlds']) && $ll['_newFlds']!=false){
					foreach($ll['_newFlds'] as $lnfk=>$lnfv)
						$newDefs[$lnfk]=$this->cD[$lnfv];
					unset($ltCV['list']['_newFlds']);
				}

				$RRlist=array();
				//echo "<tr><td colspan='10'>";
//				$RRlist['Stag']="<tr><td colspan='10'>";
				$ltCV = array_merge($this->p->t[$o]->GOAA('cV'), $ltCV);
				if(isset($ltCV['user_filter']) && $ltCV['user_filter']=='rel_list' && $lopts['queryWhere']!=false){
					$ltCV['no_user_filter_allowed']=true;
					$ltCV['user_filter']=false;
				}
				$lopts['cV'] = $ltCV;
				if(count($newDefs)>0){
					$lopts['_list_ctl_i_defs']=$newDefs;
				}
				$lopts['_act_redir_url']=aurl("/{$this->obj_slug}/{$this->cD[$this->slug_field]}");
				$this->rendAddStag("rows.*","<tr><td colspan='10'>");
				ob_start();
				$this->p->t[$o]->showAdmin("custom_view_ajaxlist", $lopts);
				//				$RRlist['body']=ob_get_contents();
				$this->rendAddBody("rows.*",ob_get_contents());
				ob_end_clean();
//				echo "<br><br></td></tr>";
//				$RRlist['Etag']="<br><br></td></tr>";
				$this->rendAddEtag("rows.*","<br><br></td></tr>");
				//				$RR['lists'][]=$RRlist;
				$this->rendMove("rows.*");
			}
			$this->rendLayout("view".$this->gO("rContext"));
			continue;
		}
		//else
//			echo $preListTit;

// ====== END LIST =================================		

//    echo "<tr>";
//    $RR['Stag']="<tr>";
		$this->rendAddTags("rows.*","<tr>","</td>");
		$rowflat = $flat;
//		$rend['row']=$RR;
		$RFNS=array();
		foreach ($rows as $cols) {
//				$RC=array('Etag'=>"</td>");
        $flat = $rowflat;
        if (isset($cols['_flat']))
            $flat = $cols['_flat'];
        unset($cols['_flat']);

        if (isset($cols['_showLogic']) && is_array($cols['_showLogic'])) {
            $op = "and";
            if (isset($cols['_showLogic']['__op'])) {
                $op = strtolower($cols['_showLogic']['__op']);
                unset($cols['_showLogic']['__op']);
            }
            $skipFlag = false;
            if ($op == 'or')
                $skipFlag = true;

            foreach ($cols['_showLogic'] as $dFn => $dFv) {
                if (isset($this->cD[$dFn]) && ((is_array($dFv) && in_array($this->cD[$dFn], $dFv)) || $this->cD[$dFn] == $dFv)) {
                    if ($op == 'or') {
                        $skipFlag = false;
                        break;
                    }
                } else if ($op != 'or') {
                    $skipFlag = true;
                    break;
                }
            }
            if ($skipFlag)
                continue;
            unset($rows['_showLogic']);
        }


        $col_width = $row_width;
        if (isset($cols['_w']) && $cols['_w'] != false) {
            $col_width = $cols['_w'];
            unset($cols['_w']);
        }

        $colspan = "";
        if (isset($cols['_colspan']) && $cols['_colspan'] != false) {
            $colspan = "colspan='{$cols['_colspan']}'";
            unset($cols['_colspan']);
        }

        $col_wdth = "";
        if ($col_width != false)
            $col_wdth = "style='width:{$col_width}'";

//        echo "<td {$col_wdth} class='dbo_fields_td' $colspan >";
//        $RC="<td {$col_wdth} class='dbo_fields_td' $colspan >";
				$this->rendAddTags("rows.*.cols.*","<td {$col_wdth} class='dbo_fields_td' $colspan >","</td>");
        if (isset($cols['_t'])) {
            $ttt = $cols['_t'];
            unset($cols['_t']);
//            echo "<div class='edit_title col legend'> {$ttt} </div>";
//						$RC['title']="<div class='edit_title col legend'> {$ttt} </div>";
						$this->rendAddBody("rows.*.cols.*","<div class='edit_title col legend'> {$ttt} </div>");
        }

				if (true || is_array($this->fctrls)) {

//						$RT=array('Etag'=>'</table>');
//            echo "<table class='dbo_fields_tbl dbo_fields_tbl_ro'>";
//						$RT['Stag']="<table class='dbo_fields_tbl dbo_fields_tbl_ro'>";
					$this->rendAddTags("rows.*.cols.*.tbl","<table class='dbo_fields_tbl dbo_fields_tbl_ro'>","</table>");
						foreach ($cols as $fnkk => $fn) {
                $this->fctrls = $ifc;
                /*	 if($fnkk!=false && is_array($fn)){
                         if(!is_array($this->fctrls[$fnkk]))
                                 $this->fctrls[$fnkk]=array();
                            $this->fctrls[$fnkk]=array_merge($this->fctrls[$fnkk],$fn);
                            $fn=$fnkk;
                            $fc=$this->fctrls[$fn];
                        }else
                            $fc=$this->fctrls[$fn];*/
                $fc = $this->prepCtrl($fnkk, $fn, true);

                if (isset($cV['fast_edit']) && (isset($cV['fast_edit'][$fn]) || in_array($fn, $cV['fast_edit']))) {
                    if (!isset($fc['no_fast_edit']) || $fc['no_fast_edit'] != true) {
                        $this->fctrls[$fn]['fast_edit'] = true;
                        if (isset($cV['fast_edit'][$fn]))
                            $this->fctrls[$fn]['fast_edit'] = $cV['fast_edit'][$fn];
                        $fc = $this->fctrls[$fn];
                    }
                }

                if ($this->cD[$fn] == false && isset($fc['hideEmpty']) && $fc['hideEmpty'] == true)
                    continue;
                if (isset($fc['showLogic']) && is_array($fc['showLogic'])) {
                    $op = "and";
                    if (isset($fc['showLogic']['__op'])) {
                        $op = strtolower($fc['showLogic']['__op']);
                        unset($fc['showLogic']['__op']);
                    }
                    $skipFlag = false;
                    if ($op == 'or')
                        $skipFlag = true;
                    foreach ($fc['showLogic'] as $dFn => $dFv) {
                        if (isset($this->cD[$dFn]) && ((is_array($dFv) && in_array($this->cD[$dFn], $dFv)) || $this->cD[$dFn] == $dFv || (isset($this->cD[$dFn."_opt_val"]) && $this->cD[$dFn."_opt_val"]==$dFv))) {
                            if ($op == 'or') {
                                $skipFlag = false;
                                break;
                            }
                        } else if ($op != 'or') {
                            $skipFlag = true;
                            break;
                        }
                    }
                    if ($skipFlag)
                        continue;
                }
                if (isset($fc['pre_html']))
                    $fc['pre_val_html'] = $fc['pre_tit_html'] = $fc['pre_html'];
                if (isset($fc['post_html']))
									$fc['post_val_html'] = $fc['post_tit_html'] = $fc['post_html'];
								
//								$RT['body']=array();
								foreach ($this->langs as $l) {
									if ((in_array($fc['c'], array("", "text", "textarea", "htmltextarea")) && !(isset($fc['_s']) && $fc['_s'] == true)) || $l == $this->def_lang) {
										$lang_class = "";
										if (in_array($fc['c'], array("", "text", "textarea", "htmltextarea")) && !(isset($fc['_s']) && $fc['_s'] == true))
											$lang_class = "dbo_row_lang_box dbo_row_lang_{$l} " . ($l == $this->def_lang ? "" : " hidden");

										//			                      echo "<tr class = '{$lang_class} dbo_offpage_tr dbo_{$this->oname}_tr offpage_{$fn} >";
//										$RLANG=array("Etag"=>"</td>");
//										$RLANG['Stag']="<tr class = '{$lang_class} dbo_offpage_tr dbo_{$this->oname}_tr offpage_{$fn} >";
										$this->rendAddTags("rows.*.cols.*.tbl.lang.*","<tr class = '{$lang_class} dbo_offpage_tr dbo_{$this->oname}_tr offpage_{$fn}' >","</td>");
										$this->rendAddKey("rows.*.cols.*.tbl.lang.*","fld",$fn);
										
//										$RFN=array('Etag'=>"</td>");

//										$RFNTIT=array();
										$ttflat = $flat;
										if (isset($fc['_flat']))
											$flat = $fc['_flat'];
//										$RFN['flat']=$flat;

										if ($flat) {
											$ttcols = "";
											if (!(isset($fc['noTitle']) && $fc['noTitle'] == true)) {
//												$RFNTIT['Etag']='</td>';

												//                                    echo "<td class = '".(isset($fc['wrap']) && $fc['wrap'] == true ? "dbo_wrap_words " : "")." dbo_roview dbo_offpage_td dbo_{$this->oname}_td dbo_title_td' style = 'width:".($colspan != false ? "10%" : "50%").">";
												//												$RFNTIT['Stag']="<td class = '".(isset($fc['wrap']) && $fc['wrap'] == true ? "dbo_wrap_words " : "")." dbo_roview dbo_offpage_td dbo_{$this->oname}_td dbo_title_td' style = 'width:".($colspan != false ? "10%" : "50%").">";
												$this->rendAddBody("rows.*.cols.*.tbl.lang.*.fn",
												"<td class = '".(isset($fc['wrap']) && $fc['wrap'] == true ? "dbo_wrap_words " : "")." dbo_roview dbo_offpage_td dbo_{$this->oname}_td dbo_title_td' style = 'width:".($colspan != false ? "10%" : "50%")."'>");

												//																		echo (isset($fc['pre_tit_html']) ? $fc['pre_tit_html'] : "") . "<span class='dbo_field_title'>{$fc['t']} " . ($fc['t'] != false ? ":" : "") . "<span>" . (isset($fc['post_tit_html']) ? $fc['post_tit_html'] : "");
//											$RFNTIT['body']=(isset($fc['pre_tit_html']) ? $fc['pre_tit_html'] : "") . "<span class='dbo_field_title'>{$fc['t']} " . ($fc['t'] != false ? ":" : "") . "<span>" . (isset($fc['post_tit_html']) ? $fc['post_tit_html'] : "");
											$this->rendAddBody("rows.*.cols.*.tbl.lang.*.fn",(isset($fc['pre_tit_html']) ? $fc['pre_tit_html'] : "") . "<span class='dbo_field_title'>{$fc['t']} " . ($fc['t'] != false ? ":" : "") . "<span>" . (isset($fc['post_tit_html']) ? $fc['post_tit_html'] : ""));

											//											echo "</td>";
											$this->rendAddBody("rows.*.cols.*.tbl.lang.*.fn","</td>");
//												$RFNTIT['flat']=true;
											} else {
												$ttcols = "colspan='2'";
											}
//											$RFN['title']=$RFNTIT;

											//                                 echo  "<td {$ttcols} class = '".(isset($fc['wrap']) && $fc['wrap'] == true ? "dbo_wrap_words " : "")." dbo_roview dbo_offpage_td dbo_{$this->oname}_td dbo_data_td'>";
//											$RFN['Stag']="<td {$ttcols} class = '".(isset($fc['wrap']) && $fc['wrap'] == true ? "dbo_wrap_words " : "")." dbo_roview dbo_offpage_td dbo_{$this->oname}_td dbo_data_td $fn'>";
											$this->rendAddBody("rows.*.cols.*.tbl.lang.*.fn","<td {$ttcols} class = '".(isset($fc['wrap']) && $fc['wrap'] == true ? "dbo_wrap_words " : "")." dbo_roview dbo_offpage_td dbo_{$this->oname}_td dbo_data_td $fn'>");

											//echo(isset($fc['pre_val_html']) ? $fc['pre_val_html'] : "");
//											$RFN['body'].=(isset($fc['pre_val_html']) ? $fc['pre_val_html'] : "");
											$this->rendAddBody("rows.*.cols.*.tbl.lang.*.fn",(isset($fc['pre_val_html']) ? $fc['pre_val_html'] : ""));
											//echo "<span class = 'dbo_field_val' >";
//											$RFN['body'].="<span class = 'dbo_field_val' >";
											$this->rendAddBody("rows.*.cols.*.tbl.lang.*.fn","<span class = 'dbo_field_val' >");


											if (!(isset($fc['emptyVals']) && is_array($fc['emptyVals']) && in_array($this->cD[$fn], $fc['emptyVals']))){
												//	echo $this->drawValue($fn, $this->current, $l, array("noSlugLink" => true));
												//												$RFN['body'].=$this->drawValue($fn, $this->current, $l, array("noSlugLink" => true));
												$TTfn=$this->drawValue($fn, $this->current, $l, array("noSlugLink" => true));
												$this->rendAddBody("rows.*.cols.*.tbl.lang.*.fn",$TTfn);
												
											}
//											$RFN['body'].="</span >";
											$this->rendAddBody("rows.*.cols.*.tbl.lang.*.fn","</span>");

											//echo(isset($fc['post_val_html']) ? $fc['post_val_html'] : "");
//											$RFN['body'].=(isset($fc['post_val_html']) ? $fc['post_val_html'] : "");
											$this->rendAddBody("rows.*.cols.*.tbl.lang.*.fn",(isset($fc['post_val_html']) ? $fc['post_val_html'] : "")."</td>");
											//echo "</td>";
										} else {

//                                echo "<td class = '".(isset($fc['wrap']) && $fc['wrap'] == true ? "dbo_wrap_words " : "")." dbo_roview dbo_offpage_td dbo_{$this->oname}_td dbo_data_td dbo_non_flat' style = 'width:50%' >";
//											$RFN['Stag']="<td class = '".(isset($fc['wrap']) && $fc['wrap'] == true ? "dbo_wrap_words " : "")." dbo_roview dbo_offpage_td dbo_{$this->oname}_td dbo_data_td dbo_non_flat $fn' style = 'width:50%' >";
											$this->rendAddBody("rows.*.cols.*.tbl.lang.*.fn","<td class = '".(isset($fc['wrap']) && $fc['wrap'] == true ? "dbo_wrap_words " : "")." dbo_roview dbo_offpage_td dbo_{$this->oname}_td dbo_data_td dbo_non_flat $fn' style = 'width:50%' >");
												//											
											if (!(isset($fc['noTitle']) && $fc['noTitle'] == true)) {
												//                                        echo (isset($fc['pre_tit_html']) ? $fc['pre_tit_html'] : "") . "<span class='dbo_field_title'>{$fc['t']}</span>" . ($fc['t'] != false ? "<br>" : "") . (isset($fc['post_tit_html']) ? $fc['post_tit_html'] : "");
//												$RFN['title']=(isset($fc['pre_tit_html']) ? $fc['pre_tit_html'] : "") . "<span class='dbo_field_title'>{$fc['t']}</span>" . ($fc['t'] != false ? "<br>" : "") . (isset($fc['post_tit_html']) ? $fc['post_tit_html'] : "");
												$this->rendAddBody("rows.*.cols.*.tbl.lang.*.fn",(isset($fc['pre_tit_html']) ? $fc['pre_tit_html'] : "") . "<span class='dbo_field_title'>{$fc['t']}</span>" . ($fc['t'] != false ? "<br>" : "") . (isset($fc['post_tit_html']) ? $fc['post_tit_html'] : ""));
											}
											//                                    echo (isset($fc['pre_val_html']) ? $fc['pre_val_html'] : "") . "<span class='dbo_field_val'>";
//											$RFN['body'].=(isset($fc['pre_val_html']) ? $fc['pre_val_html'] : "") . "<span class='dbo_field_val'>";
											$this->rendAddBody("rows.*.cols.*.tbl.lang.*.fn",(isset($fc['pre_val_html']) ? $fc['pre_val_html'] : "") . "<span class='dbo_field_val'>");

											$preS = $this->drawValue($fn, $this->current, $l);
											$TTfn="";
											if (isset($fc['emptyVals']) && is_array($fc['emptyVals']) && in_array($preS, $fc['emptyVals'])){
												//echo "<br>";
												$TTfn.="<br>";
											}else if ($preS != false){
												//echo $preS;
												$TTfn.=$preS;
											}
											else{
												//echo "<br>";
												$TTfn.="<br>";
											}
											$this->rendAddBody("rows.*.cols.*.tbl.lang.*.fn",$TTfn);

											//                                    echo "</span>" . (isset($fc['post_val_html']) ? $fc['post_val_html'] : "");
//											$RFN['body'].="</span>" . (isset($fc['post_val_html']) ? $fc['post_val_html'] : "");
											$this->rendAddBody("rows.*.cols.*.tbl.lang.*.fn","</span>" . (isset($fc['post_val_html']) ? $fc['post_val_html'] : "")."<br><br>");
											
											//                                    echo "<br><br>";
//											$RFN['body'].="<br><br>";


											//                             echo "</td >";
										}
										$flat = $ttflat;

//										$RLANG['body']=$RFN;

												//echo "</tr >";
									}
//									$RT['body'][]=$RLANG;
									$this->rendMove("rows.*.cols.*.tbl.lang.*");
								}
								//RT end
							$RFNS[$fn]=$fn;
						}

						//$RC['body']=$RT;
//            echo "</table>"; close .tbl.
				}
				$this->rendMove("rows.*.cols.*");

//				$RR['body']=$RC;
//        echo "</td>";
		}
		$this->rendAddKey("rows.*","flds",$RFNS);
		$this->rendMove("rows.*");
		
//    echo "</tr>";
								//===================== RENDER
								$this->rendLayout("view".$this->gO("rContext"));
								//===================== RENDER

}

?>


</table>

