<?php

$cV = $this->gOA("cV");

if (isset($cV['edit']['onVal']) && is_array($cV['edit']['onVal']) && count($cV['edit']['onVal'])>0){
	$eok=true;
	foreach($cV['edit']['onVal'] as $fn=>$fv){
		if($this->cD[$fn]!=$fv){
			$eok=false;
			break;
		}
	}
	if($eok==false){
		echo "<script type='text/javascript'>location.href='".aurl('/'.$this->obj_slug."/".$this->cD[$this->slug_field])."'</script>";
		die();
	}
}

$ifc = $this->fctrls;

$logicDeps = array();
$forVld = array();


?>


<?php //====================================================================================================?>



<?php
$flat = false;
if (isset($cV['edit']['_flat']))
    $flat = $cV['edit']['_flat'];

$ttAct = "i";
//if (isset($_REQUEST['rid']) && $_REQUEST['rid'] != false)
		if(is_array($this->cD))
	    $ttAct = "u";

		$onAct = array();
if (isset($cV['edit']['onAct']) && isset($cV['edit']['onAct'][$ttAct]) && is_array($cV['edit']['onAct'][$ttAct]))
    $onAct = $cV['edit']['onAct'][$ttAct];

$_l=false;
if(is_array($cV['edit']['_l']))
	$_l=$cV['edit']['_l'];

	if(isset($cV['editSel']) && is_array($cV['editSel'])){
		foreach($cV['editSel'] as $vn=>$va){
			$sok = true;
			foreach($va as $vfn=>$vfv){
				if (!(isset($this->cD[$vfn]) && ($this->cD[$vfn] == $vfv))) {
					$sok = false;
					break;
				}
			}
			if (($sok == true) && (is_array($cV['edit'][$vn]))) {
				$_l=$cV['edit'][$vn];
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
$cV['edit']['_l'] = $_l;
if (isset($cV['edit']['_style']) && $cV['edit']['_style'] != false) {
    echo "<style>{$cV['edit']['_style']}</style>";
}

$_new_def=array();
if($this->gO('_new_defs')!=false)
	$_new_defs=$this->gOAA('_new_defs');
$baseflat = $flat;
foreach ($cV['edit']['_l'] as $rowKey => $rows) {
		$this->rendInit();
    if (isset($rows['list']))
        unset($rows['list']);

    $flat = $baseflat;
    if (isset($rows['_flat']))
        $flat = $rows['_flat'];
    unset($rows['_flat']);

    $rtit = false;
    if (isset($rows['_t'])) {
        $ttt = $rows['_t'];
        unset($rows['_t']);
        $cs = count($rows);
        $cs = 20;
				$rtit = "<tr class='{$logicClasses}'><td colspan='{$cs}'><div class='edit_title row legend'>{$ttt}</div></td></tr>";
				$this->rendAddKey("rows.*","title",$ttt);
				$this->rendAddPre("rows.*",$rtit);
				
    }

    if (strpos($rowKey, "_gui_slave_") === 0) {
        $slvO = str_replace("_gui_slave_", "", $rowKey);
        if (is_object($this->gui["slaves"]) && isset($this->gui["slaves"]->slaves[$slvO])) {
            if (isset($rows['edit']))
                $this->setOpts(array("gui_slave_cV_edit" => $rows['edit']));

//            if ($rtit)                echo $rtit;
//            $this->dC("gui_slaves_{$slvO}");
				$this->rendAddBody("rows.*",$this->dCC("gui_slaves_{$slvO}"));
        }
				$this->rendLayout("edit".$this->gO("rContext"));
        continue;
    }


    $row_width = false;
    if (isset($rows['_w']) && $rows['_w'] != false) {
        $row_width = $rows['_w'];
        unset($rows['_w']);
    }


    $logicClasses = "";
    if (isset($rows['_showLogic']) && is_array($rows['_showLogic'])) {
        if (isset($rows['_showLogic']['__op'])) {
            unset($cols['_showLogic']['__op']);
        }
        $logicClasses = "dbo_show_logic dbo_show_logic_row_{$rowKey} dbo_show_logic_hidden";
        if (!isset($logicDeps["row_{$rowKey}"]))
            $logicDeps["row_{$rowKey}"] = array();
        foreach ($rows['_showLogic'] as $dFn => $dFv) {
            $logicDeps["row_{$rowKey}"][$dFn] = $dFv;
        }
        unset($rows['_showLogic']);
    }

    if (count($rows) == 0)
        continue;
		//    if ($rtit != false)        echo $rtit;

//    echo "<tr class='{$logicClasses}'>";
		$this->rendAddTags("rows.*","<tr class='{$logicClasses}'>","</tr>");

    $rowflat = $flat;
    foreach ($rows as $colKey => $cols) {
        $flat = $rowflat;
        if (isset($cols['_flat']))
            $flat = $cols['_flat'];
        unset($cols['_flat']);

        $col_width = $row_width;
        if (isset($cols['_w']) && $cols['_w'] != false) {
            $col_width = $cols['_w'];
            unset($cols['_w']);
        }

        $col_wdth = "";
        if ($col_width != false)
            $col_wdth = "style='width:{$col_width}'";

        $colspan = "";
        if (isset($cols['_colspan']) && $cols['_colspan'] != false) {
            $colspan = "colspan='{$cols['_colspan']}'";
            unset($cols['_colspan']);
        }

        $logicClasses = "";
        if (isset($cols['_showLogic']) && is_array($cols['_showLogic'])) {
            if (isset($cols['_showLogic']['__op'])) {
                unset($cols['_showLogic']['__op']);
            }
            $logicClasses = "dbo_show_logic dbo_show_logic_col_{$colKey} dbo_show_logic_hidden";
            if (!isset($logicDeps["col_{$colKey}"]))
                $logicDeps["col_{$colKey}"] = array();
            foreach ($cols['_showLogic'] as $dFn => $dFv) {
                $logicDeps["col_{$colKey}"][$dFn] = $dFv;
            }
            unset($cols['_showLogic']);
        }

//        echo "<td {$col_wdth} class='dbo_fields_td {$logicClasses}' $colspan >";
				$this->rendAddTags("rows.*.cols.*","<td {$col_wdth} class='dbo_fields_td {$logicClasses}' $colspan >","</td>");
        if (isset($cols['_t'])) {
            $ttt = $cols['_t'];
            unset($cols['_t']);
            //echo "<div class='edit_title col legend'> {$ttt} </div>";
						$this->rendAddBody("rows.*.cols.*","<div class='edit_title col legend'> {$ttt} </div>","</td>");
        }

        if (true || is_array($this->fctrls)) {


  //          echo "<table class='dbo_fields_tbl'>";
						$this->rendAddTags("rows.*.cols.*.tbl","<table class='dbo_fields_tbl'>","</table>");

            foreach ($cols as $fnkk => $fn) {
                $this->fctrls = $ifc;

                if ($fnkk != false && strpos($fnkk, "_t") === 0) {
//                    echo "<tr>";
									$this->rendAddPre("rows.*.cols.*.tbl.lang.*","<tr>");
                    if ($flat){
//                        echo "<td colspan='2' class='dbo_offpage_td dbo_title_td'>";
									$this->rendAddPre("rows.*.cols.*.tbl.lang.*","<td colspan='2' class='dbo_offpage_td dbo_title_td'>");
										}else{
										//	echo "<td class='dbo_offpage_td dbo_title_td dbo_non_flat'>";
											$this->rendAddPre("rows.*.cols.*.tbl.lang.*","<td class='dbo_offpage_td dbo_title_td dbo_non_flat'>");
										}
//                    echo "<div class='edit_title col legend'> {$fn} </div>";
//	                  echo "</td></tr>";
										$this->rendAddPre("rows.*.cols.*.tbl.lang.*","<div class='edit_title col legend'> {$fn} </div></td></tr>");
                    continue;
                }
                $drawSlaveObj = false;
                $slave_fctr_def = false;
                $to = $this;
								if ($fnkk != false && is_array($fn)) {
									$fn=$this->prepCtrl($fnkk, $fn, true);

                    if (isset($fn['slave_obj']) && is_object($this->gui['slaves']) && isset($this->gui['slaves']->slaves[$fn['slave_obj']]) && is_object($this->p->t[$fn['slave_obj']])) {
                        $drawSlaveObj = true;
                        $to = $this->p->t[$fn['slave_obj']];
                    }

                    if (is_array($to->fctrls[$fnkk])) {
                        if ($drawSlaveObj) {
                            $slave_fctr_def = $to->fctrls[$fnkk];
                        }

                        $to->fctrls[$fnkk] = array_merge($to->fctrls[$fnkk], $fn);
                        if (isset($onAct[$fnkk]) && is_array($onAct[$fnkk]))
                            $to->fctrls[$fnkk] = array_merge($to->fctrls[$fnkk], $onAct[$fnkk]);

                        $fn = $fnkk;
                        $fc = $to->fctrls[$fn];
                    } else {
                        $fc = $fn;
                        $fn = $fnkk;
                    }

                } else {
                    /*			if(isset($cols[$fn]) && is_array($cols[$fn])){
                                        $this->fctrls[$fn]=array_merge($this->fctrls[$fn],$cols[$fn]);
                    }*/
                    if (isset($onAct[$fn]) && is_array($onAct[$fn])) {
                        $this->fctrls[$fn] = array_merge($this->fctrls[$fn], $onAct[$fn]);
                    }


										$fc=$this->prepCtrl($fnkk, $fn, true);
//										$fc = $this->fctrls[$fn];
								}

								if(isset($_new_defs[$fn]))
									unset($_new_defs[$fn]);

                if (isset($fc['sfdata']['l']) && $fc['sfdata']['l'] != false && is_numeric($fc['sfdata']['l']) && !isset($fc['size'])) {
                    if ($fc['sfdata']['l'] < 10)
                        $to->fctrls[$fn]['size'] = $fc['sfdata']['l'];

                    $to->fctrls[$fn]['custHtml'] .= " maxlength='{$fc['sfdata']['l']}' ";
                    $fc = $to->fctrls[$fn];
                }
                if (isset($fc['placeholder']) && $fc['placeholder'] != false) {
                    $to->fctrls[$fn]['custHtml'] .= " placeholder='{$fc['placeholder']}' ";
                    $fc = $to->fctrls[$fn];
                }

                $forVld[] = array('fn' => $fn, 'fc' => $fc);
                if ($fc['c'] == 'hidden') {
                    $to->hctrls[$fn] = $fc;
                    continue;
                }

                $logicClasses = "";
                if (isset($fc['showLogic']) && is_array($fc['showLogic'])) {
                    if (isset($fc['showLogic']['__op'])) {
                        unset($fc['showLogic']['__op']);
                    }
                    $logicClasses = "dbo_show_logic dbo_show_logic_{$fn} dbo_show_logic_hidden";
                    if (!isset($logicDeps[$fn]))
                        $logicDeps[$fn] = array();
                    foreach ($fc['showLogic'] as $dFn => $dFv) {
                        $logicDeps[$fn][$dFn] = $dFv;
                    }
                }
                if (isset($fc['disOn']) && (($fc['disOn'] == 'filled' && $this->cD[$fn] != false) || ($fc['disOn'] == 'empty' && $this->cD[$fn] == false)))
                    $to->fctrls[$fn]['c'] = 'disabled';
	

                foreach ($to->langs as $l) {

                    if ((in_array($fc['c'], array("", "text", "textarea", "htmltextarea")) && !(isset($fc['_s']) && $fc['_s'] == true)) || $l == $to->def_lang) {
                        $lang_class = "";
                        if (in_array($fc['c'], array("", "text", "textarea", "htmltextarea")) && !(isset($fc['_s']) && $fc['_s'] == true))
                            $lang_class = "dbo_row_lang_box dbo_row_lang_{$l} " . ($l == $to->def_lang ? "" : " hidden");

                        $titD = "";
                        if (isset($fc['d']) && $fc['d'] != false)
                            $titD = "title='{$fc['t']} tip: {$fc['d']}'";

                       
                       echo "<tr {$titD} class = '{$lang_class} {$logicClasses} dbo_offpage_tr dbo_{$to->oname}_tr offpage_{$fn} row_fld_{$fn} $fn' >";
											$this->rendAddTags("rows.*.cols.*.tbl.lang.*","<tr {$titD} class = '{$lang_class} {$logicClasses} dbo_offpage_tr dbo_{$to->oname}_tr offpage_{$fn} row_fld_{$fn} $fn' >","</tr>");
                            
                            $ttflat = $flat;
                            if (isset($fc['_flat']))
                                $flat = $fc['_flat'];
                            if ($flat) {
                                if (!(isset($fc['noTitle']) && $fc['noTitle'] == true)) {
                                    
//																	echo "<td class = '".(isset($fc['wrap']) && $fc['wrap'] == true ? "dbo_wrap_words " : "")." dbo_offpage_td dbo_{$to->oname}_td dbo_title_td $fn' style = 'width:".($colspan != false ? "10%" : "50%")."'>";
																	$this->rendAddPre("rows.*.cols.*.tbl.lang.*.fn","<td class = '".(isset($fc['wrap']) && $fc['wrap'] == true ? "dbo_wrap_words " : "")." dbo_offpage_td dbo_{$to->oname}_td dbo_title_td $fn' style = 'width:".($colspan != false ? "10%" : "50%")."'>");

//                                    echo "{$fc['t']}" . ($fc['t'] != false ? " :" : "");
																	$this->rendAddPre("rows.*.cols.*.tbl.lang.*.fn","{$fc['t']}" . ($fc['t'] != false ? " :" : ""));
																	$this->rendAddPre("rows.*.cols.*.tbl.lang.*.fn","</td>");

/*																		if ($flat === true) {
                                        echo "</td>";
																	}*/
                                } else {
                                    $ttcols = "colspan='2'";
                                }
                                    
//																	 echo "<td {$ttcols} class = '".(isset($fc['wrap']) && $fc['wrap'] == true ? "dbo_wrap_words " : "")." dbo_offpage_td dbo_{$to->oname}_td dbo_data_td'>";
																	$this->rendAddTags("rows.*.cols.*.tbl.lang.*.fn","<td {$ttcols} class = '".(isset($fc['wrap']) && $fc['wrap'] == true ? "dbo_wrap_words " : "")." dbo_offpage_td dbo_{$to->oname}_td dbo_data_td'>","</td>");

                                $ttC="";
                                if ($drawSlaveObj == true) {
                                    $ttC=$this->gui['slaves']->dCC($fc['slave_obj'], $fn, $l);
                                    $this->p->t[$fc['slave_obj']]->fctrls[$fnkk] = $slave_fctr_def;
                                } else {
                                    $ttC=$this->dCC($fn, $l);
																}
																$this->rendAddBody("rows.*.cols.*.tbl.lang.*.fn",$ttC);
																

//                               echo "</td >";
                            } else {
                                
//															echo "<td class = '".(isset($fc['wrap']) && $fc['wrap'] == true ? "dbo_wrap_words " : "")." dbo_offpage_td dbo_{$to->oname}_td dbo_data_td dbo_non_flat' style = 'width:50%' >";
															$this->rendAddTags("rows.*.cols.*.tbl.lang.*.fn","<td class = '".(isset($fc['wrap']) && $fc['wrap'] == true ? "dbo_wrap_words " : "")." dbo_offpage_td dbo_{$to->oname}_td dbo_data_td dbo_non_flat' style = 'width:50%' >","</td>");

																	if (!(isset($fc['noTitle']) && $fc['noTitle'] == true)) {
                                       // echo "{$fc['t']}" . ($fc['t'] != false ? "<br>" : "");
																				$this->rendAddBody("rows.*.cols.*.tbl.lang.*.fn","{$fc['t']}" . ($fc['t'] != false ? "<br>" : ""));
                                    }
																		$ttC="";
                                    if ($drawSlaveObj == true) {
                                        $ttC=$this->gui['slaves']->dCC($fc['slave_obj'], $fn, $l);
                                        $to->fctrls[$fnkk] = $slave_fctr_def;
                                    } else
																			$ttC=$this->dCC($fn, $l);
		
																		$this->rendAddBody("rows.*.cols.*.tbl.lang.*.fn",$ttC);

//                             echo "</td >"; //.fn
                             }
														$flat = $ttflat;
														$this->rendMove("rows.*.cols.*.tbl.lang.*");
//                       echo " </tr >"; //.lang
                    }

                }

            }
            //echo "</table>"; //.tbl
            ?>
        <?php }
//				echo "</td>"; //.cols
				$this->rendMove("rows.*.cols.*");
						
    }
		//    echo "</tr>"; // .rows
		$this->rendLayout("edit".$this->gO("rContext"));

}

/*	global $slave;
		if($slave){
			var_dump($cols);
				die('OK');
			}
 */
?>


<!-- </table>  .dbo_edit -->


<?php //====================================================================================================?>

<?php
$ndth=$this->hctrls;
if(count($_new_defs)>0)
	foreach($_new_defs as $ndfn=>$ndfv)
		$this->hctrls[$ndfn]=array('def'=>$ndfv);
foreach ($this->langs as $l)
	$this->drawHidden($l);

$this->hctrls=$ndth;


?>


<script type = 'text/javascript' >
    <?php
         $Vr=array();
         foreach($forVld as $V){
             $vR=array();
             $fn=$V['fn'];
             $fc=$V['fc'];
             $sfd=false;
             if(!isset($fc['skipAutoVal']) || $fc['skipAutoVal']==false){
                 if(isset($fc['sfdata']) && is_array($fc['sfdata']))
                     $sfd=$fc['sfdata'];
                 if($sfd){
                     if(isset($sfd['nul']) && $sfd['nul']!='1')
                         $vR[]="required: true";
                     if(isset($sfd['t']) && $sfd['t']!=false){
                         switch($sfd['t']){
                         case "email":
                             $vR[]="email:true";
                             break;
                         case "percent":
                             $vR[]="number:true";
                             $vR[]="range:[0,100]";
                             break;
                         case "double":
                             $vR[]="number:true";
                             break;
                         case "currency":
                             $vR[]="currency: ['$', false]";
                             break;
                         }
                     }
                     if(isset($sfd['l']) && $sfd['l']!=false && is_numeric($sfd['l']))
                         $vR[]="maxlength:{$sfd['l']}";
                 }
             }
             if(isset($fc['valRules']) && is_array($fc['valRules']))
                 $vR=array_merge($vR,$fc['valRules']);
             if(count($vR)>0){
                 foreach($this->langs as $l){
                     if((in_array($fc['c'],array("","text","textarea","htmltextarea")) && !(isset($fc['_s']) && $fc['_s']==true)) || $l==$this->def_lang){
                         $Vr[$this->input_name($fn,"c",$l,array("echo"=>false))]=implode(',',$vR);
                     }
                 }
             }

         }
            if(count($Vr)>0){
                    $ra=array();
                    foreach($Vr as $fn=>$v)
                        $ra[]="\"$fn\":{ $v }";
    ?>
    jQuery("#idform").validate({
        rules: {<?php echo implode(",\n",$ra);	?>}
    });
    <?php } ?>
</script >

<?php if (count($logicDeps) > 0) { ?>
    <script type = 'text/javascript' >

        function _showLogic_checkState(fn) {
            if (typeof fn == 'undefined' || fn == false)
                return false;

            if (_showLogicFlags[fn + '_cur'] == _showLogicFlags[fn + '_ok']) {
                if (_showLogicFlags[fn + '_hidden'] == true) {
                    _showLogicFlags[fn + '_hidden'] = false;
                    jQuery('.dbo_show_logic_' + fn).show().find('input[name],select[name],textarea[name],button[name]').each(function () {
                        if (!jQuery(this).hasClass('disabled'))
                            jQuery(this).removeAttr('disabled');
                    });
                }
            } else {
                if (_showLogicFlags[fn + '_hidden'] == false || _showLogicFlags[fn + '_init'] == true) {
                    _showLogicFlags[fn + '_init'] = false;
                    _showLogicFlags[fn + '_hidden'] = true;
                    jQuery('.dbo_show_logic_' + fn).hide().find('input[name],select[name],textarea[name],button[name]').each(function () {
                        jQuery(this).attr('disabled', 'true');
                    });
                }
            }
            return true;
        }


        _showLogicFlags = {};
        <?php
             $jfuncs=array();
             foreach($logicDeps as $fn=>$deps){
                 $shS="";
                 $i=0;
                 foreach($deps as $dFn=>$dV){
                     $shS.="0";
                     if(!isset($jfuncs[$dFn])){
                         $jfuncs[$dFn]=array();
                    }
                     $jfuncs[$dFn][]="
        if(jQuery(this).val()=='$dV' ".($dv!=false?"&& !":"|| ")."jQuery(this).is(':hidden')){
        _showLogicFlags.{$fn}_cur=_showLogicFlags.{$fn}_cur[{$i}]='1';
        }else{
        _showLogicFlags.{$fn}_cur=_showLogicFlags.{$fn}_cur[{$i}]='0';
        }
        _showLogic_checkState('$fn');";
                     $i++;
                 }
                 echo "_showLogicFlags.{$fn}_init=true;\n";
                 echo "_showLogicFlags.{$fn}_hidden=true;\n";
                 echo "_showLogicFlags.{$fn}_cur='$shS';\n";
                 echo "_showLogicFlags.{$fn}_ok='".str_replace("0","1",$shS)."';\n";
             }
        //	 var_dump($funcs);
             echo "jQuery(document).ready(function(){";
             foreach($jfuncs as $fn=>$ff){
                 echo "
                     jQuery('.row_fld_{$fn}').find('input,select').each(function(){
                     if(jQuery(this).is('[name]')){
                             jQuery(this).bind('change.showLogic',function(){";
                             foreach($ff as $jfunc)
                                 echo $jfunc;
                             echo "
                             });
                          jQuery(this).trigger('change.showLogic');
                     }else{
                         jQuery(this).bind('click',function(){
                             setTimeout(function(){
                                 jQuery('.row_fld_{$fn}').find('input[name],select[name]').each(function(){
                             jQuery(this).bind('change.showLogic',function(){";
                             foreach($ff as $jfunc)
                                 echo $jfunc;
                             echo "
                             });
                          jQuery(this).trigger('change.showLogic');
             });
                             },500);
                         });
                     }
             });";
             }

        /*	 foreach($logicDeps as $fn=>$v){
                 echo "_showLogic_checkState('$fn');";
        }*/

             echo "});";

        ?>
    </script >

<?php } ?>



