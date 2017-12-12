<?php

function dnc_emails($ems,$o){
	$o->db->query("select * from DNC__c where Permanently_Blocked__c=1 and lower(Formatted_Email__c) IN ('".implode("','",$ems)."')","emc");
	while($r=$o->db->next('emc')){
	//	echo "UNSET ".$ems[$r['Formatted_Email__c']]."  : ({$r['Formatted_Email__c']})<hr>";
		unset($ems[$r['Formatted_Email__c']]);
	}
	$ids=array();
	$inc=$o->db->getRow("SELECT `AUTO_INCREMENT`
			FROM  INFORMATION_SCHEMA.TABLES
			WHERE TABLE_SCHEMA = 'brands99th'
			AND   TABLE_NAME   = 'DNC__c';");

	$cid=$inc['AUTO_INCREMENT'];
	foreach($ems as $v){
		$cid++;
		$slug="ADNC-{$cid}";
		$q="insert into DNC__c set Name='$slug', Formatted_Email__c='$v',Email__c='$v',Block_Contact__c=1,Permanently_Blocked__c=1,Permanently_Blocked_Reason__c='Imported Bounce'";	
//		echo "$q <br><hr>";
		$o->db->query($q);	
//		$ii=$cid;
		$ii=$o->db->lastInsertId();
		$ids["id".$ii]=$ii;
	}
	return $ids;
}

if(isset($_REQUEST['export'])){
	$this->db->query("select ct.Name,ct.E_Mail__c,ct.Primary_Interest__c,ct.CreatedDate
			from SEOX3_Client__c ct
			left join DNC__c dn on (ct.E_Mail__c=dn.Formatted_Email__c and (dn.Permanently_Blocked__c=1 or dn.Permanently_Blocked__c=0))
			where 
			(isnull(dn.Permanently_Blocked__c) ".(isset($_REQUEST['hdnc'])?"":"or dn.Permanently_Blocked__c=0")." ) and (
				ct.Name not like '%test%' and ct.E_Mail__c not like 'wserf%' and 
				ct.E_Mail__c not like '%test%' and ct.E_Mail__c not like '%devl%' and
				ct.Primary_Interest__c != ''
				) group by ct.E_Mail__c","mmq");
	$ff=true;
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename=all_leads_emails_list.csv'); 
header('Content-Transfer-Encoding: binary');
header('Connection: Keep-Alive');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');

	while($r=$this->db->next("mmq")){
		if($ff==true){
			$t=array();
			$ff=false;
			foreach($r as $kk=>$v){
				$t[]='"'.addcslashes($kk,'"').'"';
			}
			echo implode(",",$t);
			echo "\n";

		}
		$t=array();
		foreach($r as $k=>$v){
			if($k=="E_Mail__c")
				$v=preg_replace('#(block)+$#im','',$v);
			$t[]='"'.addcslashes($v,'"').'"';
		}
		echo implode(",",$t);
		echo "\n";
	}
	die();
}else if(isset($_REQUEST['bimport'])){
		header("Location: ".aurl("/mails_import_export"));
	$f=fopen($_FILES['bf']['tmp_name'],"r");
	$emails=array();
	$ac=0;
	$tc=0;
	$ids=array();
	while($r=fgetcsv($f,0,$_REQUEST['delim'],$_REQUEST['enc'])){
		$oc++;
		foreach($r as $v){
			if(preg_match('#^[a-zA-Z0-9._-]+@[a-zA-Z0-9-.]+[.][a-zA-Z0-9]+$#i',trim($v))){
				$t=strtolower(trim($v));
				$t=preg_replace('#(block)+$#im','',$t);
				$emails[$t]=$t;
				$tc++;
			}
		}
		if(count($emails)>=100){
			$ids=array_merge($ids,dnc_emails($emails,$this));
			$emails=array();
		}
	}
		if(count($emails)>0){
			$ids=array_merge($ids,dnc_emails($emails,$this));
			$emails=array();
		}
		$ic=count($ids);
		var_dump($ic,$tc,$oc,$ids);
		$this->setMsg("$ic bounces were imported to DNC list (from $oc rows, and $tc emails found )");
		global $root_path;
		require_once("{$root_path}/classes/sf_api.php");
		app2sf(array('obj_name'=>'DNC__c','act'=>'manage','ids'=>$ids));
		die();
}

$this->showHeader();
?>
<center>
<form method='POST'>
Export emails list<br> 
Honor DNC 
<input type='checkbox' checked='true' name='hdnc' value='1'><br>
<input type='submit' name='export' value='Get Emails List'>
</form>
<br>
<hr>
<br>

<form method='POST' enctype='multipart/form-data'>
Import BOUNCES <br> <input required='true' type='file' name='bf'>   <br><br>
Delimiter<br><input required='true' type='text' size=2 value=',' name='delim'><br><br>
Strings enclosure<br><input required='true' type='text' size=2 value='"' name='enc'><br><br>
<input type='submit' name='bimport' value='Import Bounces'>
</form>
</center>
<?php 
$this->showFooter();
?>
