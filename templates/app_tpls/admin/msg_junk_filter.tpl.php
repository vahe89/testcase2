<?php

function getF($f,$a,$paths=array()){
	$ttf=$f;
	if(count($paths)==0)
		$paths[]="";
	//var_dump("PP",$paths,"---");
	foreach($paths as $pp){
		$pp=str_replace("__c","__r",$pp);
		if($pp!=false)
			$pp="{$pp}.";
		$f="{$pp}{$ttf}";
		$fa=explode(".",$f);
		$t=$a;
		//var_dump($f,$fa);
		$falFlg=false;
		foreach($fa as $k){
			$k=strtolower($k);
			if(is_array($t)){
				$lT=$t;
				$t=array();
				foreach($lT as $_tk=>$_tv)
					$t[strtolower($_tk)]=$_tv;
			}

			if(is_array($t) && isset($t[$k]))
				$t=$t[$k];
			else{
				$falFlg=true;
				break;
			}
			//        return false;
		}
		if($falFlg)
			continue;
		if($t!=false){
			//var_dump("T",$t);
			return trim($t);
		}
	}
	return false;
}
function phoneNumFormat($n){
    $n=preg_replace('#[^0-9]+#im','',trim($n));
      if(strlen($n)==10)
            $n='1'.$n;
        $n='+'.$n;
          return $n;
}

function removeDNC($in,$phone=false){
  $in=preg_replace('#^[+](9999)+#im','',$in);
  $in=preg_replace('#^(9999)+#im','',$in);
  $in=preg_replace('#(block)+$#im','',$in);
  if($phone)
    return phoneNumFormat($in);
  else
    return strtolower($in);
}


$fromSF=false;
if(!isset($_SESSION['send_msg_req'])){
	die('Message data not set');
}
$msg=$_SESSION['send_msg_req'];
if(isset($msg['sf_redir']) && $msg['sf_redir']!=false)
	$fromSF=true;

$body='';
$filtValO='';
$filtType='';
$from='';
$currObj='';
$subj='';
$from_beh='';
$addFlds=array();//fields that needed from DB
$data=array();//data for dataTable, need to array merge it with items in the list
$msgConf=json_decode(file_get_contents("http://tools.grandtetonprofessionals.com/msg_sys_configs.php?p=asOJGwfwg12"),true);
$v=array(

        'list'=> array(
						"_idc_"=>"SF_Id",
//						"SF_Id",
            "clientName" => array("t" => "Name"),
            "subj"=>array("t" => "Subject"),
            "mBody" => array("t" => "Message"),
            "ToAddrFld" => array("t" => "To"),
            "FromAddrFld" => array("t" => "From address"),
//                "FromNameFld" => array("t" => "Sent by"),
						"_def_rows"=>5000,
            "_link_buttons"=>array(
//                'btn2'=>array('_t'=>'Invalid','_idc_post'=>2,'_onTop' => true),
	              'btn3'=>array('_t'=>'Invalid-DNC','_idc_post'=>2,'_onTop' => true),
                'btn'=>array('_t'=>'Add filter to Block List','_srch_post'=>2,'_onTop' => true),

            )
        )
);
//$rowsToSelect=array();
$v2=array(

        'list'=> array(
						"SF_Id"=>array("t"=>"SF Id"),
            "clientName" => array("t" => "Name"),
            "ToAddrFld" => array("t" => "To"),
            "FromAddrFld" => array("t" => "From address"),
						"_def_rows"=>5000,
        )
);

$v3=$v2;
$v3['list']['stopW']=array("t" => "Stop word");


$msgType="email";
if(isset($msg['fAType']) && $msg['fAType']==2) {
			unset($v['list']['subj']);
			$msgType="sms";
}
if(isset($msg['hSubj']) && $msg['hSubj']==true && isset($v['list']['subj'])) {
			unset($v['list']['subj']);
}
 
	  $body=$msg['body'];
    $from=$msg['from'];
    $subj=$msg['subj'];
    $currObj=$msg['obj'];
    $from_beh=$msg['from_beh'];
    $filtType=$msg['filtType'];
		$filtValO = $msg['filtValO'];


if(isset($msgConf[strtolower($currObj)][strtolower($msgType)])){
	$msgConf=$msgConf[strtolower($currObj)][strtolower($msgType)];
}else{
        echo "Configuration for current object is missing!";
        die();
    }
foreach ($msgConf as $ftype){
	if(!is_array($ftype)){
		$ftype=array($ftype);
	}
		foreach ($ftype as $fld){
			if(strpos(".",$fld)!==false)
				$addFlds[]=$fld;
		}
}

//$rowsToSelect[]=$currObj.'.name as clientName';

$stop_list=array();
$stopQ=$this->db->query("select * from {$this->db->db_prefix}stop_list");

if($stopQ!==false){
	$ssT="";
	while ($item=$this->db->next()){
		if($item['enabled']!="1")
			continue;
		if(strlen($ssT)>1000){
			$stop_list[]=$ssT;
			$ssT="";
		}
		if($ssT!="")
			$ssT.="([^a-z]|$)|(^|[^a-z])";
		$ssT.=addcslashes($item['srch'],"|");
	}
	if($ssT!="")
		$stop_list[]=$ssT;
}


$qOpts=array("queryWhere"=>"({$filtValO})",'queryFields'=>$addFlds);
$qqw=$filtVal;
if($fromSF==true){
	$qOpts['cV']=array('no_user_filter_allowed'=>true);
}
if($filtType=='i'){
	$qqw="Id in ('".str_replace(",","','",$filtValO)."')";
	if($fromSF){
    $filtValO="'".str_replace(",","','",$filtValO)."'";
		$qOpts["queryWhere"]="ct.SF_Id IN ({$filtValO}) ";
	}else{
		$qOpts["queryWhere"]="ct.Id IN ({$filtValO}) ";
	}
}
if(strtolower($currObj)=="seox3_client__c"){
	$qOpts['queryFields'][]="Sales_Rep__r.Name as SR_Name";
	$addFlds[]="Sales_Rep__r.Name";
	foreach($this->t['SEOX3_Team_Member__c']->flds as $f){
		if(strpos($f,"_EMail__c")!==false || strpos($f,"_Signature__c")!==false){
			$qOpts['queryFields'][]="Sales_Rep__r.{$f} as SR_{$f}";
			$addFlds[]="Sales_Rep__r.{$f}";
		}
	}
}
//$qOpts['_dbgQ']=true;
//$this->t[$currObj]->queryAll($qOpts);


$hidA=array("stop"=>array(),"dnc"=>array(),"dup"=>array(),"inv"=>array());
$dups=array();
$qf=implode(",",$addFlds);
$qret=SF_q($sfq="select * ".($qf!=false?", $qf":"")." from {$currObj} where {$qqw}");
//while($row=$this->t[$currObj]->next()){






foreach($qret as $row){
//	$row=$row['en'];
//	$cRow=$row;
	$cRow=array();
	$fWith=array("Name","First_Name__c","Complete_Name__c");
	foreach($fWith as $fvv){
		if(isset($row[$fvv]))
			$cRow[$fvv]=$row[$fvv];
	}
/*	if(isset($this->t[$currObj]->fctrls)){
		foreach($this->t[$currObj]->fctrls as $fk=>$fd){
			if($fd['c']=="rel")
				unset($cRow[$fk]);
		}
	}
*/


	$TDR=array();
  $TDR["SF_Id"]=$row['Id'];
  $TDR["clientName"]=ucwords(strtolower($row['Name']));
	
	$DF=array("ToAddrFld","FromAddrFld");
	foreach($DF as $f){
		if(isset($msgConf[$f])){
			if(!is_array($msgConf[$f]))
				$msgConf[$f]=array($msgConf[$f]);
			foreach($msgConf[$f] as $rF){
				$fv=getF($rF,$row);				
				if($fv!=false){
					$TDR[$f]=$fv;
					$TDR["OrigAddr"]=$fv;
					if($msgType=="sms"){
						$TDR[$f]=phoneNumFormat(removeDNC($TDR[$f],true));
					}else{
						$TDR[$f]=removeDNC($TDR[$f]);
					}
					break;
				}
			}
		}
	}

	if($TDR['ToAddrFld']==false){
		$TDR['ToAddrFld']=$TDR["OrigAddr"];
		$hidA['inv'][$row['Id']]=$TDR;
		continue;
	}

	$cRow=implode(",",$cRow);
		foreach($stop_list as $stv){
//			var_dump($stv);
			if(preg_match($tt="#(^|[^a-z]){$stv}([^a-z]|$)#mi",$cRow,$mch)){
//			if(preg_match("#{$stv}#mi",$tSubj.$tempBody.implode(",",$row)))
				$TDR['stopW']=$mch[0];
				$hidA['stop'][$row['Id']]=$TDR;
				continue 2;
			}
		}

	if(isset($dups[$TDR['ToAddrFld']])){
		$hidA['dup'][$row['Id']]=$TDR;
		continue;
	}else
		$dups[$TDR['ToAddrFld']]=1;

		$dnq="select count(*) as cnt from DNC__c where ";
		if($msgType=="sms"){
			$dnq.="Formatted_Phone__c='{$TDR['ToAddrFld']}'";
		}else
			$dnq.="Formatted_Email__c='{$TDR['ToAddrFld']}'";

		$dnc=$this->db->getRow($dnq);

		if($dnc['cnt']>0){
			$hidA['dnc'][$row['Id']]=$TDR;			
			continue;
		}

		$tpls=array("subj"=>$subj,"body"=>$body);
		foreach($tpls as $tK=>$tV){
			preg_match_all("/{!?(.*?)}/m", $tV, $matches); 
			foreach ($matches[1] as $key => $item) {
				$str=$item;
				if (strpos($item, "{$currObj}.") !== FALSE) {
					$str = explode("{$currObj}.", $item,2); //get whole string after
					$str=$str[1];
				}

				//				var_dump($str,$matches[0][$key],$row[$str]);
				if(isset($row[$str])){
					$tsv=getF($str,$row);
					if($str=="Name" || $str=="First_Name__c"){
						$tsv=ucwords(strtolower($tsv));
					}
					$tpls[$tK] = str_replace($matches[0][$key], $tsv, $tpls[$tK]);

				}else if(strtolower($currObj)=="seox3_client__c" && in_array($str,array("Sales_Rep_sign_DONT_DEL_LenOfThisText45chr","SalesRepSign_DO_NOT_DEL"))){
					if($str=="Sales_Rep_sign_DONT_DEL_LenOfThisText45chr"){
						$tpls[$tK] = str_replace($matches[0][$key], "{$row['Sales_Rep__r']['Name']} {$row['Interest_Domain__c']}", $tpls[$tK]);
					}
					else if($str=="SalesRepSign_DO_NOT_DEL"){
						$tpls[$tK] = str_replace($matches[0][$key], $row["Sales_Rep__r"][$row['Sales_Rep_Signature_Field__c']], $tpls[$tK]);
					}

				}

			}
		}
    
//    $tempArrayForData["id"]=$row['id'];
    $TDR["mBody"]=nl2br($tpls['body']);
    $TDR["subj"]=nl2br($tpls['subj']);
		if($msgType=="sms"){
			$t=preg_replace("#[^0-9]+#","",$TDR["ToAddrFld"]);
			$t=substr($t,-10);
			$TDR["ToAddrFld"]="(".substr($t,0,3).") ".substr($t,3,3)."-".substr($t,6,4);
			$t=preg_replace("#[^0-9]+#","",$TDR["FromAddrFld"]);
			$t=substr($t,-10);
			$TDR["FromAddrFld"]="(".substr($t,0,3).") ".substr($t,3,3)."-".substr($t,6,4);
		}
    $data[]=$TDR;
}
$dups=array();
$o=$this->new_vobj("messageBlastFilter",$data,array(),$v);

$cntA=$this->db->getRow("select count(*) as cnt, max(msgs_tot) as tot from msgs_batches where jf_id={$_REQUEST['bid']}");
$cntL=$this->db->getRow("select count(*) as cnt from msgs_batches where jf_id={$_REQUEST['bid']} and status=0");


$this->header_head();
echo "<div style='width: 95%; padding:20px'>";
echo "<div id='bodyDiv''>";
echo "<h3>Message Template</h3><hr><br>";
if(isset($v['list']['subj'])){
	echo "<b>Subject</b>:<br>".$subj;
	echo "<br><br>";
}
echo "<b>Body:</b><br>";
echo $body;
echo "</div>";
?>

<style>
.topFixed {
	position:fixed;
	margin-top:0px !important;
	top:0px;
	left:auto;
	right:40px;
}
.dataTables_filter.topFixed {
	right:297px;
}

    input[id^="spoiler"]{
        display: none;
				margin-bottom:20px;
    }

    input[id^="spoiler"] ~ .spoiler, .hcb {
				display:none;
        width: 90%;
        overflow: hidden;
        opacity: 0;
        margin: 10px auto 0;
        padding: 10px;

        background: #eee;
        border: 1px solid #ccc;
        border-radius: 8px;
        transition: all .6s;
    }

    input[id^="spoiler"]:checked ~ .spoiler, .hcb.act{
				display:block !important;
        opacity: 1;
        padding: 10px 10px 35px;
    }
    #bodyDiv{
        width: 95%;
        background: #eee;
        border: 1px solid #ccc;
        border-radius: 8px;
	  	  padding: 10px 20px;
				text-align: center;
		}

</style>

<br><br>
<span>
<?php echo "Batches ".($cntA['cnt']-$cntL['cnt']+1)." from {$cntA['cnt']} (total records {$cntA['tot']})";?> 
&nbsp;&nbsp;&nbsp;&nbsp;
</span>

<?php 
	$nar=array(
			"spoiler"=>"Show Block List",
			"stop"=>"By Stop List ({cnt})",
			"dnc"=>"By DNC ({cnt})",
			"dup"=>"Duplicates ({cnt})",
			"inv"=>"Wrong Contacts ({cnt})",
		);
	foreach($nar as $nK=>$nV){
		$cc=count($hidA[$nK]);
		$nV=str_replace("{cnt}",$cc,$nV);
		echo "<label class='btn' onclick=\"var a=jQuery('#cb_{$nK}').hasClass('act');jQuery('#spoiler').removeAttr('checked');jQuery('.hcb').removeClass('act');if(a==false){jQuery('#cb_{$nK}').addClass('act');}\">$nV</label> &nbsp;&nbsp;";	
	}
?>

<div id="cb_spoiler" class="spoiler hcb">
    <?php
		$cvv=$this->v['objs']['stop_list'];
		$cvv['list']['_def_rows']=500;
    $this->t['stop_list']->showAdmin("custom_view_ajaxlist",array('ownHeader' => true,'cV'=>$cvv));
    ?>

</div>
<?php 
	foreach($nar as $nK=>$nV){
		$tO=$this->new_vobj("cb_{$nK}",$hidA[$nK],array(),($nK=="stop"?$v3:$v2));
		echo "<div id='cb_{$nK}' class='hcb'>";
		$tO->showAdmin("custom_view_list",array('ownHeader' => true,"_dV_html"=>true));
		echo "</div>";
	}
?>
<br>
<br>

<?php
echo "<div class='msgs'>";
$o->showAdmin("custom_view_list",array('ownHeader' => true,"_dV_html"=>true));

echo "</div><br><br><div>";
if($fromSF) {
    echo '<button style="float:left" type="button" onclick=\'oncanc("window.location=\"https://' . $msg['sf_redir'] . '\"; ");\'>Cancel and Get me back</button>';
}else{
//    echo '<br><br><br><button type="button" onclick="parent.$.fancybox.close();">Back </button>';
    echo '<button  style="float:left" type="button" onclick=\'oncanc("window.history.back();");\'>Cancel and Back</button>';
}
if(count($data)>0){
	echo "<form style='float:right' method='POST'><input type='submit' name='sendmsg' value='Send!'></form>";
}
echo "</div><br><br><br></div>";?>

<script type='text/javascript'>
jQuery(document).ready(function(){
var scO=jQuery('.msgs .dbo_table_acts_wrap.top,.msgs .dataTables_filter');
    scO_offset={top:scO.css('top'),obj:scO,off:0};
    sc_o=scO.get(0);
    while(sc_o.offsetParent!=null){
      scO_offset.off+=sc_o.offsetTop;
      sc_o=sc_o.offsetParent;
    }
    jQuery(document).bind('scroll',onScroll);
    onScroll(false);

		jQuery(".msgs .dbo_table_acts_wrap.top > form").bind('submit',function(e){
			var frm=jQuery(this);
			var sbn=frm.find('input[type=submit]').val();
//			alert(sbn);
			var pp=jQuery(this).parent().parent();
			var srch=pp.find('.dataTables_filter input').val();
			var okF=false;
			if(sbn=='Add filter to Block List' && srch!=null && srch!="" && srch!=false){
				remTbl(true);
				okF=true;
			}
			if(sbn=='Invalid-DNC'){
				var idc=frm.find('input[name=_idc_checked]').val();
				if(idc!=null || idc!=false)
					okF=true;
//				alert(idc);
			}
			if(okF==true){
			jQuery('input[name=_idc_btn]').attr('disabled',true);
			jQuery.post("<?php echo aurl('/junk_filter?ajax=1'.(isset($_REQUEST['bid'])?"&bid={$_REQUEST['bid']}":"").'&_idc_btn=')?>"+sbn.replace(/[ ]+/,'+'),frm.serialize(),function(rr){
				if(rr=='Invalid-DNC'){
					remTbl(false);
				}else{
					var stt=jQuery('.spoiler table').dataTable();
					stt.fnDraw();
				}
				jQuery('input[name=_idc_btn]').removeAttr('disabled');
				pp.find('.dataTables_filter input').val('').trigger('input');
			});
			}
			return false;
		});
		
});
	
	function remTbl(all){
				var errI=0;
				var dt=jQuery('.msgs table').dataTable();
				while(true){
				errI++;
				if(errI>10000)
					break;
				if(all==true)
					var rows=jQuery('.msgs table tr.dbo_tr');
				else if(all==false)
					var rows=jQuery('.msgs table tr.dbo_tr input._idc_items:checked');
				if(rows.length==0)
					break;
				for(var ri=0;ri<rows.length;ri++){
					if(all==true)
						dt.fnDeleteRow(rows.get(ri));
					else if(all==false){
						var rt=jQuery(rows[ri]).parent().parent().get(0);
						dt.fnDeleteRow(rt);
					}
				}
				dt.fnDraw();
				}
	
	}

  function onScroll(e){
    sv = window.pageYOffset || document.documentElement.scrollTop;
    if(sv>=scO_offset.off){
      //scO_offset.obj.css({position:'fixed',top:'40px',right:'40px',left:'auto'});
      scO_offset.obj.addClass('topFixed');
    }else
      scO_offset.obj.removeClass('topFixed');
//      scO_offset.obj.css({position:'relative',top:0,right:'0'});
      
}

function oncanc(o){
			jQuery.get("<?php echo aurl('/junk_filter?ajax=1&cancel=1'.(isset($_REQUEST['bid'])?"&bid={$_REQUEST['bid']}":""))?>",function(rr){
				eval(o);					
			});
}

</script>

<?php $this->footer_foot();
?>
