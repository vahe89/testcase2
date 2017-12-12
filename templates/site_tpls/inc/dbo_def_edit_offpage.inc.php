<?php
$logicDeps = array();

?>
<form id = "idform" action = 'index.php' method = 'POST' enctype = 'multipart/form-data' >
    <input type = 'hidden' name = 'a' value = 'p_adb' >

    <input type = "hidden" name = "redirect" value = "<?php echo($this->gO('redirect') ? $this->gO('redirect') : "a=dbo_{$this->oname}"); ?>" >

    <table class = "dbo_edit dbo_offpage_table dbo_<?php echo $this->oname; ?>_offpage_table" >
        <?php
        $lastlang = end($this->langs);
        if (count($this->langs) > 1) { ?>
            <tr class = "dbo_lang_selector dbo_offpage_tr dbo_<?php echo $this->oname; ?>_tr" >
                <td colspan = 3 >
                    <?php foreach ($this->langs as $l) { ?>
                        <input type = "button" class = 'lang_btn <?php echo($this->def_lang == $l ? "cl" : "") ?>' value = "<?php echo $l; ?>" onclick = "$('.dbo_row_lang_box').hide();$('.dbo_row_lang_<?php echo $l; ?>').show();" >&nbsp;
                    <?php } ?>
                </td >
            </tr >
        <?php } ?>

        <?php
        if (true || is_array($this->fctrls)) {

            foreach ($this->fctrls as $fn => $fc) {
                foreach ($this->langs as $l) {

                    if ((in_array($fc['c'], array("", "text", "textarea", "htmltextarea")) && !(isset($fc['_s']) && $fc['_s'] == true)) || $l == $this->def_lang) {
                        $lang_class = "";
                        if (in_array($fc['c'], array("", "text", "textarea", "htmltextarea")) && !(isset($fc['_s']) && $fc['_s'] == true))
                            $lang_class = "dbo_row_lang_box dbo_row_lang_{$l} " . ($l == $this->def_lang ? "" : " hidden");

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


                        ?>
                        <tr class = "<?php echo $lang_class;
                        echo " {$logicClasses} "; ?> dbo_offpage_tr dbo_<?php echo $this->oname; ?>_tr row_fld_<?php echo $fn; ?> offpage_<?php echo $fn; ?>" >
                            <td class = "dbo_offpage_td dbo_<?php echo $this->oname; ?>_td dbo_title_td" >
                                <?php echo $fc['t']; ?>
                            </td >
                            <td class = "dbo_offpage_td dbo_<?php echo $this->oname; ?>_td dbo_data_td" >
                                <?php

                                $this->drawCtrl($fn, $l);

                                ?>
                            </td >
                            <td class = "dbo_offpage_td dbo_<?php echo $this->oname; ?>_td dbo_descr_td" >
                                <?php echo $fc['d']; ?>
                            </td >
                        </tr >
                        <?php
                    }

                }
            } ?>
        <?php } ?>


        <tr class = "dbo_offpage_tr dbo_e_ctrl_tr dbo_<?php echo $this->oname; ?>_tr " >

            <td align = 'center' colspan = 3 class = "dbo_offpage_td dbo_e_ctrl_td dbo_<?php echo $this->oname; ?>_td " >
                <?php
                foreach ($this->langs as $l)
                    $this->drawHidden($l);

                $this->act_wrap("u", array("u" => "Save", "i" => "Add")); ?>
            </td >
        </tr >
    </table >

</form >

<?php if (count($logicDeps) > 0) { ?>
    <script type = 'text/javascript' >

        function _showLogic_checkState(fn) {
            if (typeof fn == 'undefined' || fn == false)
                return false;

            if (_showLogicFlags[fn + '_cur'] == _showLogicFlags[fn + '_ok']) {
                if (_showLogicFlags[fn + '_hidden'] == true) {
                    _showLogicFlags[fn + '_hidden'] = false;
                    jQuery('.dbo_show_logic_' + fn).show().find('input[name],select[name],textarea[name],button[name]').each(function () {
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


