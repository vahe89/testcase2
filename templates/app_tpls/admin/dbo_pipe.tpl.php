<?php
$this->p->t['SEOX3_Client__c']->copts['fldAsSlugLink']='Client_ID__c';
?>
<style>

    #mfrm {
        background:#efefef;
    }
    .cb_psd, .disc_psd, .notes_div, .hidden {
        display:none;
    }
    .ldr {
        vertical-align:center;
        position:fixed;
        left:0px;
        top:0px;
        width:100%;
        height:100%;
        z-index:10000;
    }
    .ldr div {
        width:100%;
        height:100%;
        background:#333;
        opacity:0.5;
    }
    .ldr h1 {
        position:absolute;
        top:35%;
        text-align:center;
        color:#fff;
        font-weight:bold;
        width:100%;
    }

</style>
<?php
/*unset($this->copts['cV']['edit']['_l']['r0']);
unset($this->copts['cV']['edit']['_l']['r2']);
 */
/*unset($this->copts['cV']['edit']['_l']['r1']['c4']);
$this->fctrls['Quality__c']['c']='radio';
//$this->fctrls['Status__c']['c']='radio';
unset($this->fctrls['Quality__c']['opts']['New']);
$this->copts['cV']['edit']['_l']['r1']['c2']=array_merge(array('CreatedDate'),$this->copts['cV']['edit']['_l']['r1']['c2']);
 */
?>
<style>
    .dbo_SDM_Cadastro_Garotas__c_ctr_group_Notes__c textarea {
        width:100%;
    }
</style>
<div class='ldr'>
    <div></div>
    <h1>LOADING.... <br> <img src="<?php echo $this->skin."loader2.gif"?>"></h1>
</div>
<center>


    <h1 style='font-size:20px;'>

        <?php if($this->gO('pipe_next')=='cb_pipe'){?>



            <?php
        }
        if($this->cD==false){
            if($this->gO('pipe_next')=='new_pipe')
                echo "<br><br><br>NO NEW CLIENTS<br><br><br></h1></center>";
            else if($this->gO('pipe_next')=='retry_pipe')
                echo "<br><br><br>NO CLIENTS TO RETRY<br><br><br></h1></center>";
            else if($this->gO('pipe_next')=='cb_pipe')
                echo "<br><br><br>NO CLIENTS TO CALLBACK<br><br><br></h1></center>";
            echo "<script type='text/javascript'>jQuery('.ldr').hide();</script>";
            $_SESSION[$this->gO('pipe_next').'_start']=0;
        }else{
        $os=$_SESSION[$this->gO('pipe_next').'_start'];
        $lc=$this->gO('left_cnt')-1;
        //echo number_format($lc)." left in pipe (".ceil(($lc)/($os/100))."% since session start)";

        ?></h1>


    <br>
    <a  class='btn btn-primary' href="<?php echo aurl("/{$this->obj_slug}/{$this->cD[$this->slug_field]}/e");?>">Edit</a> &nbsp;&nbsp;


    <a  class='btn btn-primary' href="<?php echo aurl("/pipe/{$this->cD[$this->slug_field]}?s=1");?>">Interested</a> &nbsp;&nbsp;
    <a  class='btn btn-primary' href="<?php echo aurl("/pipe/{$this->cD[$this->slug_field]}?s=2");?>">Lost</a> &nbsp;
    <a  class='btn btn-primary' href="<?php echo aurl("/{$this->obj_slug}/{$this->cD[$this->slug_field]}");?>">View</a> &nbsp;

    <br>
    <br>
</center>

    <form id='mfrm' class='hidden' action='<?php echo aurl("/next_pipe")?>' method='POST' onsubmit="v=jQuery(this).find('#result').val();if(typeof v=='undefined' || v==''){alert('Select RESULT');return false;};if(flds_chk(v)){jQuery('.abtn').attr('disabled','true');}else{return false;};return true;">
        <!-- <center> -->

        <h1 id='tit'></h1>

        <input type='hidden' name='cid' value='<?php echo $this->cD['id']?>'>
        <input type='hidden' name='sfid' value='<?php echo $this->cD['SF_Id']?>'>
        <input type='hidden' name='cb_date' value='<?php echo $this->cD['Next_Call_Back__c']?>'>
        <input type='hidden' name='next' value='<?php echo $this->gO('pipe_next');?>'>
        <input type='hidden' name='result' value='' id='result'>
        <input type='hidden' name='e_skip' value='1' id='e_body_skip'>
        <input type='hidden' name='s_skip' value='1' id='s_body_skip'>

        <?php /* ?>
<select name='result' id='cb_ps' disabled='true'>
<option value=''>--Select Result--</option>
<option value='no_answer'>No answer</option>
<option value='left_vm'>Left VM</option>
<option value='cb'>Callback</option>
<option value='discard'>Discard</option>
</select>
<br>
<br>
<?php */ ?>

        <div class='cb_psd res_hide'>


            <div>

                <br>
                <input type='checkbox' id='es' onchange="es_switch();"><label for='es'> Send Email</label><br>
                EMAIL Subject:<br>
                <input disabled='true' class='skipchk' size='80' type='text' name='e_subj' id='e_subj'><br>
                EMAIL Body:<br>
                <textarea class='skipchk msg'  disabled='true' cols=100 rows=20 name='e_body' id='e_body'><?php echo "\n\n\n\n\n\n".$this->p->curUsr[$this->cD['Sales_Rep_Signature_Field__c']];?></textarea>
                <br>
                <br>
                <input type='checkbox' id='ss' onchange="es_switch();"><label for='ss'> Send SMS</label><br>
                SMS Body:<br>
                Chars count in SMS below: <span id='smscnt'></span>
                <textarea class='skipchk msg' disabled='true' cols=100 rows=5 name='s_body' id='s_body' onkeyup="jQuery('#smscnt').html(this.value.length);"><?php echo "\n\n\n{$this->p->curUsr['Name']} {$this->cD['Interest_Domain__c']}";?></textarea>

            </div>
        </div>

        <div class='disc_psd res_hide'>

            <div class='lost_res'>
                <br>
                <select name='lost_res'>
                    <option value=''>--Select Lost Reason--</option>
                    <?php
                    foreach($this->p->t['SEOX3_Client__c']->fctrls['Lost_Reason__c']['opts'] as $o)
                        echo "<option value='$o'>$o</option>";
                    ?>
                </select>
            </div>

        </div>



        <div class='notes_div res_hide'>
            <br>
            <label for='noteid'>Note</label>:<br>
            <textarea id='noteid' name='note' cols='100' rows='5'>
</textarea>
        </div>
        <br>

        <input class='res_hide subdef btn-primary' id='defsubmit' type='submit' value='Save and Next' id='nxt'>
        <input class='res_hide subcb btn-primary' type='submit' value='Save/Send and Next' id='nxt'>

        <!-- </center> -->
    </form>

<?php $this->doInc("dbo_custom_view_readonly"); ?>

    <script type='text/javascript'>
        msgok={e_body:false,s_body:false};
        jQuery(document).ready(function(){
            jQuery( document ).ajaxComplete( function(){$.fancybox.update()});
            jQuery('.msg').bind('change',function(){
                var d=jQuery(this).attr('id');
                msgok[d]=true;
                jQuery('#'+d+'_skip').val('0');
                return true;
            });

            jQuery('.abtn').bind('click',function(){
                f=jQuery('#mfrm');
                v=jQuery(this).attr('id');
                jQuery('#tit',f).html(jQuery(this).val());
                jQuery('#result',f).val(v);
                jQuery('.res_hide',f).hide().find('input,textarea,select').removeClass('active').attr('disabled','true');
                if(v!='cb')
                    jQuery('.subdef').show();
                if (v=='cb'){
                    jQuery('.cb_psd,.notes_div,.subcb',f).show().find('input,textarea,select').addClass('active').removeAttr('disabled');
                    es_switch();
                }
                else if(v=='lost'){
                    jQuery('.disc_psd,.notes_div',f).show().find('input,textarea,select').addClass('active').removeAttr('disabled');
                    jQuery('.notes_div',f).find('textarea').addClass('skipchk');
                }
                else if(v=='left_vm'){
                    jQuery('#defsubmit').trigger('click');
                    return true;
                    jQuery('.notes_div',f).show().find('input,textarea,select').addClass('active').removeAttr('disabled');
                }
                else if(v=='closed'){
                    jQuery('.onclose_div,.notes_div').show().find('input,textarea,select').addClass('active').removeAttr('disabled');
                }
                else if(v=='invalid'){
                    jQuery('#defsubmit').trigger('click');
                    return true;
                }
                else if(v=='miss_cb'){
                    jQuery('#defsubmit').trigger('click');
                    return true;
                }
                else if(v=='no_answer'){
                    jQuery('#defsubmit').trigger('click');
                    return true;
                    /*		 if(confirm('Select NO Answer?')){
                     f.get(0).submit();
                     return true;
                     }else
                     return false;
                     */
                }
                f.dialog({
//      autoOpen: false,
                    height: 500,
                    width: 850,
                    modal: true,
                    open:function(e,u){
                        var jf=jQuery(e.target);
                        jf.css('height','auto');
//				jf.css('width','auto');
//				jf.dialog('option','width',jf.width());
//				alert(jQuery(e.target).height());
                    }
                });

//		jQuery.fancybox(f,{});
            }).removeAttr('disabled');
            jQuery('.abtn').removeAttr('disabled');
            jQuery('#smscnt').html(jQuery('#s_body').get(0).value.length);

            $(".spinner2").spinner({
                numberFormat:'C'
            });

            jQuery('#ph').fancybox({type:'ajax'});
            /*		jQuery('#ph').bind('click',function(){setTimeout("$.fancybox.update()",500);});
             jQuery('#ph').bind('click',function(){setTimeout("$.fancybox.update()",1000);});
             jQuery('#ph').bind('click',function(){setTimeout("$.fancybox.update()",2000);});
             jQuery('#ph').bind('click',function(){setTimeout("$.fancybox.update()",10000);});*/
            jQuery('.ldr').hide();

        });


        function es_switch(){
            if(jQuery('#es').is(':checked')){
                jQuery('#e_subj,#e_body').removeAttr('disabled');
            }else{
                jQuery('#e_body_skip').val('1');
                jQuery('#e_subj,#e_body').attr('disabled','true');
            }

            if(jQuery('#ss').is(':checked')){
                jQuery('#s_body').removeAttr('disabled');
            }else{
                jQuery('#s_body_skip').val('1');
                jQuery('#s_body').attr('disabled','true');
            }
        }

        function finv_res(v){
            if(v=='Invalid Contact Info'){
                jQuery('.notes_div').hide().find('textarea').attr('disabled','true').addClass('skipchk').removeClass('active');
            }else{
                jQuery('.notes_div').show().find('textarea').removeAttr('disabled').removeClass('skipchk').addClass('active');
            }
        }

        function inv_lost_h(v){
            jQuery('.inv_res,.lost_res').hide().find('select').removeClass('active').attr('disabled','true');
            if(v=='Invalid Lead'){
                jQuery('.inv_res').show().find('select').addClass('active').removeAttr('disabled');
            }
            else if(v=='Lost'){
                jQuery('.lost_res').show().find('select').addClass('active').removeAttr('disabled');
            }
        }
        function flds_chk(v){
            if(typeof v=='undefined' || v=='')
                return false;
            a=jQuery('select.active');
            for(si=0;si<a.length;si++){
                o=jQuery(a[si]);
                if(!o.hasClass('skipchk') && (o.val()==false || o.val()=='')){
                    alert('Select value in dropdown box');
                    return false;
                }
            }
            a=jQuery('input.active');

            for(si=0;si<a.length;si++){
                o=jQuery(a[si]);
                oid=o.attr('id');
                if(!o.hasClass('skipchk') && (o.val()==false || o.val()=='')){
                    mm=jQuery('label[for='+oid+']').text();
                    if(mm!=false)
                        alert('Fill value in "'+mm+'"');
                    else
                        alert('Fill value in date field');
                    return false;
                }
                if(oid=='cbdate'){
                    var cbd=o.datetimepicker('getDate');
                    nd=new Date();
                    if(cbd-nd<900*1000){
                        alert('Callback date should be set and be minimum at 15 mins in future');
                        return false;
                    }
                }
                if(oid=='amount' && !isNumeric(o.val())){
                    alert('Amount - should be numeric');
                    return false;
                }
            }
            a=jQuery('textarea.active');
            for(si=0;si<a.length;si++){
                o=jQuery(a[si]);
                oid=o.attr('id');
                if(!o.hasClass('skipchk') && (o.val()==false || o.val()=='')){
                    mm=jQuery('label[for='+oid+']').text();
                    if(mm!=false)
                        alert('Fill value in "'+mm+'"');
                    else
                        alert('Fill text area');
                    return false;
                }
            }

            if(v=='cb'){
                if(jQuery('#es').is(':checked') && msgok.e_body==false && !confirm('Email selected but not changed, so will be skipped. Ok?')){
                    return false;
                }
                if(jQuery('#ss').is(':checked') && msgok.s_body==false && !confirm('SMS selected but not changed, so will be skipped. Ok?')){
                    return false;
                }
                if(jQuery('#es').is(':checked') && msgok.e_body!=false){
                    es=jQuery('#e_subj').val();
                    eb=jQuery('#e_body').val();
                    if((es.length==0 || eb.length==0) && es.length!=eb.length){
                        alert('EMAIL require Subject to be filled, fill it or skip emails sending.');
                        return false;
                    }
                }

            }
            return true;
        }



    </script>



<?php } ?>
