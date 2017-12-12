<?php
if(isset($_REQUEST['oh']) && $_REQUEST['oh']==1)
	$this->header_head();
if(isset($_REQUEST['obj']) && is_object($this->t[$_REQUEST['obj']])){
$obj=$this->t[$_REQUEST['obj']];

$emailPat="#^[a-zA-Z0-9_.-]+@[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,6}\$#im";
$phonePat="#^[0-9]{11}\$#im";

$filtType=false;
// f-filter , i-sfids, a=app ids, e-emails, q - where query
if(isset($_REQUEST['f']) && in_array($_REQUEST['f'],array("f","q",'i','e','a'))){
	$filtType=$_REQUEST['f'];

$filtVal=false;
$filtValO=false;
// filter or ids or emails
if($filtType=='q' && isset($_REQUEST['ajid']) && $_REQUEST['ajid']!=false){
	if($this->rFA($_REQUEST['ajid'])){
		$filtValO="(".implode(") and (",$obj->gOAA('queryFilter')).")";
		$_REQUEST['r']=str_replace(array("ct."),"",$filtValO);
	}
}

$cur_record=false;
if(isset($_REQUEST['r']) && $_REQUEST['r']!=false){
	$filtVal=$_REQUEST['r'];
	if($filtValO==false)
		$filtValO=$filtVal;
	if($filtType=='a'){
		$filtType='i';
		$this->db->query($q="select SF_Id from $obj->oname where id IN (".$this->db->escape($_REQUEST['r']).")",'aids');
		$taa=array();
		while($row=$this->db->next('aids'))
			$taa[]=$row['SF_Id'];
		$filtVal=implode(",",$taa);
		unset($taa);

	}
	if($filtValO==false)
		$filtValO=$filtVal;

	$qwh=false;
	$cl_cnt=0;
	if($filtType=='i'){
		$t=explode(",",$this->db->escape($filtVal));
		if(count($t)==1)
			$cur_record=$t[0];
		$cl_cnt=count($t);
		$qwh="Id in ('".implode("','",$t)."')";
	}else	if($filtType=='q'){
		$qwh=$_REQUEST['r'];
	}

if($cl_cnt==false && $qwh != false){
		$ret=SF_query($sfq="/bulk_rest_api?count=1&q=".urlencode("select count() from {$obj->oname} where $qwh "),false,false);
      $fdata=json_decode($ret,true);
      if(isset($fdata[0]['errorCode']) && strpos($fdata[0]['message'],"Too many query rows: 50001")!==false)
        $cl_cnt=">50 000";
      if(isset($fdata[0]['errorCode']))
        $cl_cnt=$fdata[0]['message'];
      if(isset($fdata[0]['Val__c']))
        $cl_cnt=$fdata[0]['Val__c'];
}

$cl_cntTT="{$cl_cnt} Client(s)";

$mT=false;

if(isset($_REQUEST['mT']) && $_REQUEST['mT']!=false){
	$mT=$_REQUEST['mT'];

$cc=false;
if(isset($_REQUEST['cc']) && $_REQUEST['cc']!=false)
	$cc=$_REQUEST['cc'];

$fA=false;
if(isset($_REQUEST['fA']) && $_REQUEST['fA']!=false)
	$fA=$_REQUEST['fA'];

$title=$_REQUEST['title'];


$sDNC=false;
if(isset($_REQUEST['sDNC']) && $_REQUEST['sDNC']!=false)
	$mT=$_REQUEST['sDNC'];

$hSubj=false;
if(isset($_REQUEST['hSubj']) && $_REQUEST['hSubj']!=false)
	$hSubj=$_REQUEST['hSubj'];


$fAType=false; // From Address Type absent/0 - not check, 1 - email, 2 - phone
$fatpl=false;
if(isset($_REQUEST['fAType']) && $_REQUEST['fAType']!=false)
	$fAType=$_REQUEST['fAType'];
if($fAType==1)
	$fatpl=$emailPat;
else if($fAType==2)
	$fatpl=$phonePat;

$defMsg=false;
if(isset($_REQUEST['defMsg']) && $_REQUEST['defMsg']!=false)
	$defMsg=$_REQUEST['defMsg'];

$countChars=false;
if(isset($_REQUEST['cntChr']) && $_REQUEST['cntChr']!=false)
	$cntChr=true;



?>
<style>
table#msgtbl{
	width:80%;
	margin:0 auto;
}

#msgtbl td {
	padding:5px;
	text-align:left;	
}

#msgtbl th {
	padding:5px;
	text-align:right;	
}

input[type=text] {
	width:400px;
}

#mbody {
	width:400px;
	height:	200px;
}

</style>
<?php 
if($title!=false)
	echo "<center><h2>$title</h2></center><br>";			
if($err!=false)
	echo "<center><h3 style='color:red'>ERROR: $err</h3></center>";			
?>
<form action="<?php echo aurl("/send_msg");?>" method='POST'>
<input type='hidden' name='obj' value='<?php echo $obj->oname;?>'>
<?php if(isset($_REQUEST['next']) && $_REQUEST['next']!=false){?>
<input type='hidden' name='next' value='<?php echo $_REQUEST['next'];?>'>
<?php } ?>
<input type='hidden' name='filtType' value='<?php echo $filtType;?>'>
<input type='hidden' name='filtVal' value="<?php echo addcslashes($filtVal,'"');?>">
<input type='hidden' name='filtValO' value="<?php echo addcslashes($filtValO,'"');?>">
<input type='hidden' name='mT' value='<?php echo $mT;?>'>
<input type='hidden' name='sDNC' value='<?php echo $sDNC;?>'>
<input type='hidden' name='fAType' value='<?php echo $fAType;?>'>
<input type='hidden' name='fA' value='<?php echo $fA;?>'>
<input type='hidden' name='hSubj' value='<?php echo $hSubj;?>'>
<?php if(isset($_REQUEST['ajid'])){?>
<input type='hidden' name='ajid' value='<?php echo $_REQUEST['ajid'];?>'>
<?php } ?>
<?php if(isset($_REQUEST['title'])){?>
<input type='hidden' name='title' value='<?php echo addcslashes($_REQUEST['title'],'"');?>'>
<?php } ?>
<?php if(isset($_REQUEST['d'])){?>
<input type='hidden' name='d' value='<?php echo addcslashes($_REQUEST['d'],'"');?>'>
<?php } ?>
<?php if(isset($_REQUEST['oh'])){?>
<input type='hidden' name='oh' value='<?php echo $_REQUEST['oh'];?>'>
<?php } ?>

<?php /* <center><input type='submit' name='act' value='Send!'></center> */ ?>

<table id='msgtbl'>

<?php if($fA!=false){ 
	$faddr=false;
	if($fAType==2){
		$fA=trim(preg_replace("#[^0-9]#mi","",$fA));
		if(strlen($fA)==10)
			$fA="1{$fA}";
//		$fA="+{$fA}";
	}
		if($fAType!=false && preg_match($fatpl,trim($fA))){
			$fA=4;
			$faddr=trim($fA);
		}
?>
<tr>
<th>From<th>
<td>
<?php if($fA!=4)
	echo "<input type='text' name='from' value='".(isset($_REQUEST['from'])?$_REQUEST['from']:"")."'>";
else{
	echo "$faddr <input type='hidden' name='from' value='$faddr'>";
}

?>

</td>
</tr>
<?php 
$fbmsg="You can select below to use address above as default if empty or force instead object values";
$fbhid="";
if($fA==1){
	$fbhid="<input type='hidden' name='from_beh' id='fbeh_def'> ";
	$fbmsg="Specified From address will be used if client from field is empty";
}else if($fA==2){
	$fbhid="<input type='hidden' name='from_beh' id='fbeh_force'> ";
	$fbmsg="Specified From address will be FORCED INSTED Clients From field";
}

if($fA!=false){ ?>
<tr>
<th>From Notice<th>
<td>
<?php echo $fbmsg;
if($fA!=3)
	echo $fbhid;
?>

</td>
</tr>

<?php if($fA==3){ ?>
<tr>
<th>From Behaviour<th>
<td>
<input type='radio' <?php echo (isset($_REQUEST['from_beh'])&&$_REQUEST['from_beh']=='fbeh_def'?"checked='true'":"")?> name='from_beh' value='fbeh_def' id='fbeh_def'> <label for='fbeh_def'>Default</label>
<input type='radio' <?php echo (isset($_REQUEST['from_beh'])&&$_REQUEST['from_beh']=='fbeh_force'?"checked='true'":"")?> name='from_beh' value='fbeh_force' id='fbeh_force'> <label for='fbeh_force'>Force</label>
</td>
</tr>
<?php 
}
}
} ?>

<tr>
<th>To<th>
<td><?php echo $cl_cntTT;?></td>
</tr>

<?php if($cc!=false){?>
<tr>
<th>CC<th>
<td><input type='text' name='cc' value="<?php echo (isset($_REQUEST['cc'])?$_REQUEST['cc']:"");?>"></td>
</tr>
<?php } 

if($cl_cnt>1){
?>

<tr>
<th>Mail Merge<th>
<td><select onchange="if(this.value!=''){jQuery('#api_code').html('{!<?php echo $obj->oname?>.'+this.value+'}');}else{jQuery('#api_code').html('');}">
<option value=''>--Select--</option>
<?php 
$fs=$obj->flds;
sort($fs);
foreach($fs as $f)
	echo "<option value='$f'>{$obj->fctrls[$f]['t']}</option>";
?>
</select></td>
</tr>

<tr>
<th>API Code<th>
<td><span id='api_code'></span></td>
</tr>
<?php } ?>

<?php if($hSubj!=true){?>
<tr>
<th>Subject<th>
<td><input type='text' name='subj' value="<?php echo (isset($_REQUEST['subj'])?$_REQUEST['subj']:"");?>"></td>
</tr>
<?php } ?>

<tr>
<th>Body<th>
<td>
<?php if($cntChr==true)
					echo "Characters in message: <span id='bodycntChr'></span><br/>"
?>
<textarea
<?php if($cntChr==true){
						echo "onkeyup=\"jQuery('#bodycntChr').html(this.value.length);\"";
}?>
 id='mbody' name='body' ><?php echo (isset($_REQUEST['body'])?$_REQUEST['body']:$defMsg);?></textarea>
<?php if($cntChr==true){
						echo "<script type='text/javascript'>jQuery('#bodycntChr').html(jQuery('#mbody').val().length);</script>";
}?>
</td>
</tr>

</table>
<center><input type='submit' name='act' value='Send!'></center> 
<?php
if(isset($_REQUEST['ajid']) && $_REQUEST['ajid']!=false	){
	$ssr=$this->storeForAjax($_REQUEST['ajid'],false,true);
	echo $ssr['r'];
}

if(isset($_REQUEST['shh']) && $_REQUEST['shh']!=false && $cur_record!=false){
	echo "<center>CHAT HISTORY</center>";
	$tm_id=$this->curUsr['SF_Id'];
	$noF=true;
	$this->db->query($q="select * from chat where
			(from_sf_id='{$cur_record}' or to_sf_id='{$cur_record}')
			 and 
			(from_sf_id='{$tm_id}' or to_sf_id='{$tm_id}')  order by id desc","histQ");
	while($hr=$this->db->next("histQ")){
		$noF=false;
		$f="You said:";
		if($hr['from_sf_id']==$cur_record)
			$f="Client said:";
		echo "<br>*** (".date("m/d/Y H:i",strtotime($hr['ts'])).") $f {$hr['msg']}";
	}
	if($noF)
		echo "<center>*** no messages in chat history ***</center>";
}
?>

</form>
<?php 
}else
	echo "ERR: Msg type not set or wrong";

}else
	echo "ERR: Recepients empty";
}else
	echo "ERR: Type emtpy";
}else
	echo "ERR: Obj not set or wrong";

if(isset($_REQUEST['oh']) && $_REQUEST['oh']==1)
	$this->footer_foot();

?>
