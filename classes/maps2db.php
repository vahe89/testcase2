<?php
require_once("common_funcs.php");
require_once("init.php");
require_once("tables.php");
//require_once("views.php");
mysql_connect($init_db['host'],$init_db['user'],$init_db['pass']);
mysql_select_db($init_db['db']);
$charset=$init_db['charset'];
mysql_query("SET NAMES '$charset'");
mysql_query("SET CHARACTER SET '$charset'");
mysql_query("SET character_set_client = '$charset'");
mysql_query("SET character_set_connection = '$charset'");
mysql_query("SET character_set_database = '$charset'");
mysql_query("SET character_set_results = '$charset'");
mysql_query("SET character_set_server = '$charset'");
mysql_query("SET character_set_system = '$charset'");

$run=true;
if(isset($_SERVER['argv'][1]) && strtolower($_SERVER['argv'][1])=='run')
	$run=true;


foreach($dbobjs as $on=>$ov){
	if (!(isset($ov['sf_table']) && $ov['sf_table'] != false) && !(isset($ov['auto_db']) && $ov['auto_db'] != false))
		continue;

	echo "\n\n### {$ov['table']} ###################\n";
	$ins=true;
	$res=mysql_query("describe `{$ov['table']}`");
	$dbflds=array();
	if($res!=false){
		$ins=false;
		while($r=mysql_fetch_assoc($res)){
			if(is_null($r['Default']))
					$r['Default']="NULL";
			if($r['Default']=="NULL" || in_array(strtolower($r['Default']),array('current_timestamp'))){
				$r['Def']=(strtolower($r['Null'])=='yes'?"NULL":"NOT NULL")." DEFAULT ".$r['Default']."";
				if ($r['Extra']!=false)
					$r['Def'].=" ".strtoupper($r['Extra']);
			}
			else
				$r['Def']=(strtolower($r['Null'])=='yes'?"NULL":"NOT NULL")." DEFAULT '".$r['Default']."'";
			$dbflds[strtolower($r['Field'])]=$r;
		}
		unset($dbflds['id'],$dbflds['lid'],$dbflds['lang']);
	}
	$cmp=array();
	$ffs=array();
	$tl=0;
	$prev_name="FIRST";
	$prev_name="AFTER id";
	$tbKeys=array();
	$key_flag=false;
	foreach($ov['fctrls'] as $fn=>$fv){
		$nf=false;
		$nft=false;
		if(isset($fv['sfdata']['uniq']) && $fv['sfdata']['uniq']==true)
			$tbKeys[$fn]=array("t"=>"UNIQUE INDEX");

//		if (!(isset($ov['sf_table']) && $ov['sf_table'] != false)) {
		if(isset($ov['slug_field']) && $fn==$ov['slug_field'])
			$tbKeys[$fn]=array("t"=>"UNIQUE INDEX");
//		}

		if(strtolower($fn)=="name" && (!isset($ov['slug_field']) || $fn!=$ov['slug_field']))
			$tbKeys[$fn]=array("t"=>"INDEX");
		
		if(strtolower($fn)=="sf_id")
			$tbKeys[$fn]=array("t"=>"UNIQUE INDEX");
		
		switch($fv['sfdata']['st']){
		case 'tns:ID':
			if((isset($fv['sfdata']['sfid']) && $fv['sfdata']['sfid']==true) || !is_array($ov['rels'][$fn])){
				$nf="VARCHAR(18)";
				$nft="NOT NULL DEFAULT ''";
				$tbKeys[$fn]=array("t"=>"INDEX");
			}else{
				$nf="BIGINT(11)";
				$nft="NOT NULL DEFAULT '0'";
				$tbKeys[$fn]=array("t"=>"INDEX");
			}

			break;
		case 'xsd:boolean':
			$nf="INT(1)";
			$nft="NOT NULL DEFAULT '0'";
			break;
		case 'xsd:string':
			if($fv['sfdata']['t']=='textarea'){
				$nf="TEXT";
				$nft="NULL DEFAULT NULL";
			}else{
//				if($fv['sfdata']['l']>300)
//					echo "*** LONG VARCHAR: $fn = {$fv['sfdata']['l']} \n";
				$cll=$fv['sfdata']['l'];
				if($cll==1300)
					$cll=250;
				if($tl<15000){
					$tl+=$cll;
					$nf="VARCHAR({$cll})";
				 	$nft="NULL DEFAULT NULL";
				}else{
					$key_flag=$cll;
					echo "WARNING: CHAR len is $tl , USING TINYTEXT!\n";
					$nf="TINYTEXT";
				 	$nft="NULL DEFAULT NULL";
				}
			}
			break;
		case 'xsd:dateTime':
			$nf="DATETIME";
		 	$nft="NULL DEFAULT NULL";
			break;
		case 'xsd:date':
			$nf="DATE";
			$nft="NULL DEFAULT NULL";
			break;
		case 'xsd:double':
			$nf="DECIMAL({$fv['sfdata']['p']},{$fv['sfdata']['s']})";
		 	$nft="NULL DEFAULT NULL";
			break;
		default:
			$nf="TINYTEXT";
		 	$nft="NULL DEFAULT NULL";
		}
		if(isset($fv['sfdata']['index']) && $fv['sfdata']['index']==true && !is_array($tbKeys[$fn])){
				$tbKeys[$fn]=array("t"=>"INDEX");
				if($key_flag!=false){
					$nf="VARCHAR({$key_flag})";
				 	$nft="NULL DEFAULT NULL";
				}
		}

		if (isset($fv['auto_db_type']) && $fv['auto_db_type'] != false)
			$nf = $fv['auto_db_type'];
		if (isset($fv['auto_db_def']) && $fv['auto_db_def'] != false)
			$nft = $fv['auto_db_def'];
		if(
			((isset($fv['auto_db_key']) && $fv['auto_db_key'] ==true)
			|| (isset($fv['auto_db_key_uniq']) && $fv['auto_db_key_uniq'] ==true))	&& !is_array($tbKeys[$fn])){
			$tbKeys[$fn]=array("t"=>"INDEX");
			if(isset($fv['auto_db_key_uniq']))
				$tbKeys[$fn]=array("t"=>"UNIQUE INDEX");

			if($key_flag!=false){
				$nf="VARCHAR({$key_flag})";
				$nft="NULL DEFAULT NULL";
			}
		}

		if($ins){
			$ffs[]="`$fn` $nf $nft";
			if(is_array($ov['rels'][$fn])){
				$ro=false;
				if(isset($ov['rels'][$fn]['obj']) && isset($dbobjs[$ov['rels'][$fn]['obj']]))
					$ro=$dbobjs[$ov['rels'][$fn]['obj']];
				if(is_array($ro) && isset($ro['sf_table']) && $ro['sf_table']==true){
					$ffs[]="`{$fn}_id` VARCHAR(50) NOT NULL DEFAULT ''";
					$ffs[]="`{$fn}_slug` VARCHAR(50) NOT NULL DEFAULT ''";
					$tbKeys["{$fn}_id"]=array("t"=>"INDEX");
					$tbKeys["{$fn}_slug"]=array("t"=>"INDEX");
				}else if(false && is_array($ro) && isset($ro['slug_field']) && $ro['slug_field']!="" && strtolower($ro['slug_field'])!='id'){
					$ffs[]="`{$fn}_slug` VARCHAR(50) NOT NULL DEFAULT ''";
					$tbKeys["{$fn}_slug"]=array("t"=>"INDEX");
				}
			}
				
		}
		else{
			$ttfn=strtolower($fn);
			$nft=preg_replace("#[ ]+#i"," ",trim($nft));
			if(isset($dbflds[$ttfn]) && (strtolower($nf)!=strtolower($dbflds[$ttfn]['Type']) || strtolower($nft)!=strtolower($dbflds[$ttfn]['Def']))){
				$ffs[]="CHANGE COLUMN `{$fn}` `{$fn}` {$nf} {$nft} {$prev_name}";
				if(strtolower($nft)!=strtolower($dbflds[$ttfn]['Def']))
					$cmp[$fn]=strtoupper($nft)." != ".strtoupper($dbflds[$ttfn]['Def']);
				else
					$cmp[$fn]=strtoupper($nf)." != ".strtoupper($dbflds[$ttfn]['Type']);
			}else if(!isset($dbflds[$ttfn]))
				$ffs[]="ADD COLUMN `{$fn}` {$nf} {$nft} {$prev_name}";
			
			if(isset($dbflds[$ttfn]))
				unset($dbflds[$ttfn]);
//			echo "PRE $fn : ".$fv['c']." ======= \n";
			if(is_array($ov['rels'][$fn]) && isset($fv['sfdata']['st']) && $fv['sfdata']['st']=="tns:ID"){
				$Ottfn=$ttfn;
				$Ofn=$fn;

				$ttfn="{$Ottfn}_id";
				$rrfn="{$fn}_id";
				$tbKeys["{$ttfn}"]=array("t"=>"INDEX");
				if(isset($dbflds[$ttfn]) && "varchar(50)"!=strtolower($dbflds[$ttfn]['Type'])){
					$ffs[]="CHANGE COLUMN `{$rrfn}` `{$rrfn}` VARCHAR(50) NOT NULL DEFAULT '' AFTER `$fn`";
					$cmp[$rrfn]="{$nf} != {$dbflds[$fn]['Type']}";
				}else if(!isset($dbflds[$ttfn]))
					$ffs[]="ADD COLUMN `{$rrfn}` VARCHAR(50) NOT NULL DEFAULT '' AFTER `$fn`";

				if(isset($dbflds[$ttfn]))
					unset($dbflds[$ttfn]);

				$fn=$rrfn;
				
				$rrfn="{$Ofn}_slug";
				$ttfn="{$Ottfn}_slug";

				$tbKeys["{$ttfn}"]=array("t"=>"INDEX");

				if(isset($dbflds[$ttfn]) && "varchar(50)"!=strtolower($dbflds[$ttfn]['Type'])){
					$ffs[]="CHANGE COLUMN `{$rrfn}` `{$rrfn}` VARCHAR(50) NOT NULL DEFAULT '' AFTER `$fn`";
					$cmp[$rrfn]="{$nf} != {$dbflds[$fn]['Type']}";
				}else if(!isset($dbflds[$ttfn]))
					$ffs[]="ADD COLUMN `{$rrfn}` VARCHAR(50) NOT NULL DEFAULT '' AFTER `$fn`";

				if(isset($dbflds[$ttfn]))
					unset($dbflds[$ttfn]);

				$fn=$rrfn;
			}else if(false && is_array($ov['rels'][$fn])){
				$ro=false;
				if(isset($ov['rels'][$fn]['obj']) && isset($dbobjs[$ov['rels'][$fn]['obj']]))
					$ro=$dbobjs[$ov['rels'][$fn]['obj']];
				if(is_array($ro) && isset($ro['slug_field']) && $ro['slug_field']!="" && strtolower($ro['slug_field'])!='id' && ! (isset($ro['sf_table']) && $ro['sf_table']==true)){
					$ttfn="{$fn}_slug";
					if(!isset($dbflds[$ttfn]))
						$ffs[]="ADD COLUMN `{$ttfn}` VARCHAR(50) NOT NULL DEFAULT '' AFTER `$fn`";
					else
						unset($dbflds[$ttfn]);
				}

			}
			
		}
		$prev_name="AFTER `{$fn}`";		
	}
	if(count($dbflds)>0){
		foreach($dbflds as $fn=>$v)
			$ffs[]="DROP COLUMN `{$v['Field']}`";
	}

	$q=false;
	if($ins){
		$tKeys=array("PRIMARY KEY (`id`)");
		foreach($tbKeys as $kf=>$ka)
			$tKeys[]="{$ka['t']} `$kf` (`$kf`)";

	$q="CREATE TABLE `{$ov['table']}` (
`id` BIGINT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
".implode(",\n",$ffs).",
`lang` VARCHAR(4) NOT NULL DEFAULT '{$ov['def_lang']}',
`lid` BIGINT(11) UNSIGNED NOT NULL DEFAULT '0',
".implode(",",$tKeys)."
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM
ROW_FORMAT=DYNAMIC";
			if($q!=false){
				foreach($cmp as $kf=>$vv)
					echo "## $kf COMPARE: $vv \n";
				echo "$q \n ------------------- \n";
				echo "TL: $tl \n ------------------- \n";
				if($run)
					mysql_query($q);
				echo "\n -----\nERR: ".mysql_error()." \n ************************ \n";
				//	die(mysql_error());
			}else
				echo "\n=========== TABLE: {$ov['table']} SAME, NO changes ======= \n";
	}else{
		if(count($ffs)>0){
			$q="ALTER TABLE `{$ov['table']}` \n".implode(",\n",$ffs);
			if($q!=false){
				foreach($cmp as $kf=>$vv)
					echo "## $kf COMPARE: $vv \n";
				echo "$q \n ------------------- \n";
				echo "TL: $tl \n ------------------- \n";
				if($run)
					mysql_query($q);
				echo "\n -----\nERR: ".mysql_error()." \n ************************ \n";
				//	die(mysql_error());
			}else
				echo "\n=========== TABLE: {$ov['table']} SAME, NO changes ======= \n";
		}

		$kres=mysql_query("show index from `{$ov['table']}`");
		$curKeys=array();
		$altKeys=array();
		if($kres!=false){
			while($row=mysql_fetch_assoc($kres)){

				if($row['Key_name']=="PRIMARY")
					continue;
				$t="INDEX";
				if($row['Non_unique']=="0")
					$t="UNIQUE INDEX";
				if($row['Index_type']=="FULLTEXT")
					$t="INDEX";
				$curKeys[strtolower($row['Column_name'])]=array("t"=>$t,"kn"=>$row['Key_name']);
			}
		}
		foreach($tbKeys as $kn=>$ka){
			$lkn=strtolower($kn);

			if(!isset($curKeys[$lkn])){
				$altKeys[]="ADD {$ka['t']} `$kn` (`$kn`)\n";
			}else{
				if(strtolower($ka['t']) != strtolower($curKeys[$lkn]['t'])){
						echo "CHANGE KEY TYPE: from '{$curKeys[$lkn]['t']}' to '{$ka['t']}'\n";
						$altKeys[]="DROP INDEX `{$kn}`";
					 	$altKeys[]="ADD {$ka['t']} `$kn` (`$kn`)";
				}

				unset($curKeys[$lkn]);
				
			}
		}
		foreach($curKeys as $kn=>$ka){
			echo "DROP KEY: DROP INDEX `{$ka['kn']}` \n";
			$altKeys[]="DROP INDEX `{$ka['kn']}`";
		}
		
		if(count($altKeys)>0){
			$q="ALTER TABLE `{$ov['table']}` \n".implode(",\n",$altKeys);		
//			echo "\n\n +++++++ KEYS QQ: $q \n\n";
			echo "+KEYS QUERY: \n".implode(",\n",$altKeys);
			if($run)
				mysql_query($q);
			echo "\n KEYS Q ERR:".mysql_error()."\n===\n";
		}

	}
	
}

if(!$run)
	echo "\n\n\nWARNING: It was TEST run, no changes were made.\nAdd 'run' argument to apply changes.\n";

?>
