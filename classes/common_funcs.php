<?php
global $url_base, $url_pref,$adm_url_pref, $root_path,$adm_url_base,$current_url,$current_uri,$url_host;

$url_base="/";
$url_pref="";
$adm_url_pref="";

if(isset($_SERVER['SERVER_NAME']) && isset($_SERVER['SERVER_PORT']) && (isset($_SERVER['DOCUMENT_ROOT']) || isset($_SERVER['SCRIPT_FILENAME']))){

	$current_url=($_SERVER['SERVER_PORT']==443?"https":"http")."://".(isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:$_SERVER['SERVER_NAME']).$_SERVER['REQUEST_URI'];
	if(isset($_SERVER['SCRIPT_URI']) && $_SERVER['SCRIPT_URI']!=false)
		$current_uri=$_SERVER['SCRIPT_URI'];
/*	if($current_uri==false)
		$current_uri=$_SERVER['REQUEST_URI'];*/


$tu=explode($_SERVER['DOCUMENT_ROOT'],$_SERVER['SCRIPT_FILENAME'],2);
if(isset($_SERVER['SCRIPT_URL']) && isset($_SERVER['SCRIPT_URI'])){
	$tu2=explode($_SERVER['SCRIPT_URL'],$_SERVER['SCRIPT_URI'],2);
	$url_host=$tu2[0];
	$url_base=$tu2[0].dirname($tu[1]);
}
else{
	$url_host=($_SERVER['SERVER_PORT'] == 443 ? "https" : "http") . "://" . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME']);
	$url_base = $url_host . ($tu[1][0]==='/'? '' : '/') . dirname($tu[1]);
}

$adm_url_base=$url_base;
if(preg_match("#.*?(?=/admin$)#i",$url_base,$r))
	$url_base=$r[0];

$url_base=preg_replace("#/$#","",$url_base);
$adm_url_base=preg_replace("#/$#","",$adm_url_base);

}


$root_path=realpath(dirname(__FILE__)."/../");
$url_pref=str_replace($url_host,"",$url_base);
$adm_url_pref=str_replace($url_host,"",$adm_url_base);

function url($part,$end="")
{
	global $url_base;
	return $url_base.$part.$end;
}
function aurl($part,$end="")
{
	global $adm_url_base;
	return $adm_url_base.$part.$end;
}

function purl($part,$end="")
{
	global $url_base,$url_pref;
	return $url_base.$url_pref.$part.$end;
}
function rpath($part,$end="",$rp=false)
{
	global $root_path;
	if($rp)
		return realpath($root_path.$part.$end);
	else
		return $root_path.$part.$end;
}

function path2url($path,$end="")
{
	global $root_path, $url_base;
	return $url_base.str_replace($root_path,"",$path).$end;
}

if(!function_exists("array_merge_recursive_new")){
	function array_merge_recursive_new() {

        $arrays = func_get_args();
        $base = array_shift($arrays);
				if(!is_array($base))
					$base=array();

        foreach ($arrays as $array) {
            reset($base); //important
            while (list($key, $value) = @each($array)) {
                if (is_array($value) && @is_array($base[$key])) {
                    $base[$key] = array_merge_recursive_new($base[$key], $value);
                } else {
                    $base[$key] = $value;
                }
            }
        }

        return $base;
}
}
if(!function_exists("array_merge_new")){
	function array_merge_new() {

        $arrays = func_get_args();
        $base = array_shift($arrays);
				if(!is_array($base))
					$base=array();

        foreach ($arrays as $array) {
            reset($base); //important
            while (list($key, $value) = @each($array)) {
                    $base[$key] = $value;
            }
        }

        return $base;
}
}


function modcurl($vars=false,$url=false){
	if($url==false)
		$c=$_SERVER['REQUEST_URI'];
	else
		$c=$url;
	$cc=explode("?",$c,2);
	$resv=$cc[1];
	if(is_array($vars)){
		if(isset($cc[1]) || $cc[1]!=false){
			$tv=explode("&",$cc[1]);
			if(is_array($tv)){
				foreach($tv as $v){
					$k=explode("=",$v,2);
					$rv[$k[0]]="{$k[0]}={$k[1]}";
				}
			}
		}
		foreach($vars as $k=>$v){
			$rv[$k]="{$k}={$v}";
		}
		$resv=implode("&",$rv);
	}

	return $cc[0].($resv!=false?"?{$resv}":"");
}


function check_access($obj,$arules,$uaccess,$strict=false){
	if(in_array("all",$uaccess))
		return true;


	if(isset($arules[$obj])){
		if(!is_array($arules[$obj]))
			$arules[$obj]=array($arules[$obj]);
		foreach($arules[$obj] as $rv){
			if(( in_array($rv,$uaccess) || $rv=="_free" ) && ($strict==false || $rv==$arules[$obj][0]))
				return true;
		}
	}
	return false;

}

function strtr_norm($s){
 $nChars = array( 
            'Á'=>'A', 'À'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Å'=>'A', 'Ä'=>'A', 'Æ'=>'AE', 'Ç'=>'C', 
            'É'=>'E', 'È'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Í'=>'I', 'Ì'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ð'=>'Eth', 
            'Ñ'=>'N', 'Ó'=>'O', 'Ò'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 
            'Ú'=>'U', 'Ù'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 
    
            'á'=>'a', 'à'=>'a', 'â'=>'a', 'ã'=>'a', 'å'=>'a', 'ä'=>'a', 'æ'=>'ae', 'ç'=>'c', 
            'é'=>'e', 'è'=>'e', 'ê'=>'e', 'ë'=>'e', 'í'=>'i', 'ì'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'eth', 
            'ñ'=>'n', 'ó'=>'o', 'ò'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 
            'ú'=>'u', 'ù'=>'u', 'û'=>'u', 'ü'=>'u', 'ý'=>'y', 
            
            'ß'=>'sz', 'þ'=>'thorn', 'ÿ'=>'y' 
        ); 

 return strtr($s,$nChars);
}


function check_arr($iarr){
	$ina=func_num_args();
	if($ina>1 && is_array($iarr)){
		$cv=func_get_args();
		for($i=1;$i<$ina;$i++){
			if(isset($iarr[$cv[$i]]) && $iarr[$cv[$i]]!=false)
				continue;
			return false;
		}
		return true;	
	}
	return false;
}


function check_arrX(&$iarr){
	$ina=func_num_args();
	if($ina>1 && is_array($iarr)){
		$cv=func_get_args();
		$isEsc=ini_get("magic_quotes_gpc");
		for($i=1;$i<$ina;$i++){
			if(isset($iarr[$cv[$i]]) && $iarr[$cv[$i]]!=false){
				if($isEsc==false)
					$iarr[$cv[$i]]=mysql_real_escape_string($iarr[$cv[$i]]);
				continue;
			}
			return false;
		}
		return true;	
	}
	return false;
}

function check_arrK(&$iarr,$keys,$do_esc){
	if(is_array($keys) && count($keys)>0){
		$isEsc=ini_get("magic_quotes_gpc");
		foreach($keys as $ak){
			if(isset($iarr[$ak]) && $iarr[$ak]!=false){
				if($do_esc && $isEsc==false)
					$iarr[$ak]=mysql_real_escape_string($iarr[$ak]);
				continue;
			}
			return false;
		}
		return true;	
	}
	return false;
}

require_once("new_sf_api.php");

function run_async($func,$req,$db,$dbg=false){

	$cmd=false;
	$url=aurl("/index.php");
	if($aurl=="/index.php")
		$cmd=true;
	$cmd=true;

	$res=Dmysql_query("select * from {$db->db_prefix}sys_config where name='artp_data'");
	$r=mysql_fetch_assoc($res);
	$artp=unserialize($r['val']);
	if(!is_array($artp))
		$artp=array("next"=>1,"pass"=>array(),'ts'=>array());

	$rp=md5(time()).(rand());
	$num=1;		

	if(isset($artp['next']))
		$num=$artp['next'];

	$ts=time();

	$artp['pass'][$num]=$rp;
	$artp['ts'][$num]=$ts;
	$artp['next']=$num+1;

	global $sys_app_name;
	$adir="async_apps_req_{$sys_app_name}";
	if(!is_dir("/tmp/$adir"))
		mkdir("/tmp/$adir");
	$f=fopen("/tmp/$adir/req_{$num}_{$rp}.{$ts}","w");
	fwrite($f,serialize($req));
	fclose($f);

	Dmysql_query("update sys_config set val='".Dmysql_real_escape_string(serialize($artp))."' where name='artp_data'");
	if(Dmysql_num_rows()==0)
		Dmysql_query("insert into sys_config set val='".Dmysql_real_escape_string(serialize($artp))."', name='artp_data'");

	if($cmd==false){
		$tout=2;
		if($dbg)
			$tout=200000;
		//var_dump('curl',aurl("/index.php"));

		$Treq=curl_init(aurl("/index.php"));
		$trigSet=array(
				CURLOPT_SSLVERSION=>3,
				CURLOPT_RETURNTRANSFER=>true,
				CURLOPT_POST=>true,
				CURLOPT_TIMEOUT=>$tout,
				CURLOPT_POSTFIELDS=>array(
					"a"=>"p_async",
					"artp"=>$rp,
					"artpn"=>$num,
					"func"=>$func,
					"v2"=>true,
//					"req"=>serialize($req),
					),
				);
		curl_setopt_array($Treq,$trigSet);
		$Tret=curl_exec($Treq);
//		var_dump($Tret,curl_error($Treq));
		if($dbg){
			var_dump(curl_error($Treq),$Tret);
			die("\nEND");
		}
		curl_close($Treq);
		//	echo $Tret;
//			die($Tret);
		/*	if($req['tbl']=='sys_files'){
				var_dump($Tret);
				die();
				}
		 */
	}else{
		$dn=dirname(__FILE__);
		exec("( ( php {$dn}/async.php '$func' '$rp' $num $ts) &> /tmp/async_log_{$app} ) &");
	}

}

function wordwrap_add($in,$len,$add){
	$inl=strlen($in);
	$n="";
	$c=0;
	for($i=$len;$i<$inl;$i+=$len){
		$n.=substr($in,$c,$i).$add;
		$c+=$i;
	}
	if($c==0)
		$n=$in;
	else if($c<$inl){
		$n.=substr($in,-($inl-$c),($inl-$c));
	}
	return $n;
	
}

function arr2lower($arr){
	$ret=array();
	foreach($arr as $rrk=>$rrv){
		if(is_array($rrv)){
			$ret[strtolower($rrk)]=arr2lower($rrv);
		}
		else
			$ret[strtolower($rrk)]=$rrv;
	}
	return $ret;
}
function Dmysql_error(){
//  db_recon_if_needed();
  global $sf_api_db_link;
  return mysql_error($sf_api_db_link);
}

function Dmysql_real_escape_string($s){
  db_recon_if_needed();
  global $sf_api_db_link;
  return mysql_real_escape_string($s,$sf_api_db_link);
}

function Dmysql_query($q,$link=false){
      db_recon_if_needed();
      global $sf_api_db_link;
      if($link)
        return mysql_query($q,$link);
      else
        return mysql_query($q,$sf_api_db_link);

}

function db_recon_if_needed(){
  global $sf_api_db_link;
  if(!is_resource($sf_api_db_link) || mysql_ping($sf_api_db_link)!=true){
    if(is_resource($sf_api_db_link))
      mysql_close($sf_api_db_link);
    Dinit_db();
  }
}

function Dclose_db(){
  global $sf_api_db_link;
   if(is_resource($sf_api_db_link))
		return mysql_close($sf_api_db_link);
	return false;
}
function Dmysql_close(){
	return Dclose_db();
}

function Dmysql_num_rows($res=false){
  global $sf_api_db_link;
	if(is_resource($res))
	  return mysql_num_rows($res);
	else
	  return mysql_affected_rows($sf_api_db_link);

}
function Dmysql_connect(){
	return Dinit_db();
}
function Dinit_db(){
  require(dirname(__FILE__)."/init.php");
  global $sf_api_db_link;
  $sf_api_db_link=mysql_connect($init_db['host'],$init_db['user'],$init_db['pass'],true);
  mysql_select_db($init_db['db'],$sf_api_db_link);
  $charset=$init_db['charset'];
  mysql_query("SET NAMES '$charset'",$sf_api_db_link);
  mysql_query("SET CHARACTER SET '$charset'",$sf_api_db_link);
  mysql_query("SET character_set_client = '$charset'",$sf_api_db_link);
  mysql_query("SET character_set_connection = '$charset'",$sf_api_db_link);
  mysql_query("SET character_set_database = '$charset'",$sf_api_db_link);
  mysql_query("SET character_set_results = '$charset'",$sf_api_db_link);
  mysql_query("SET character_set_server = '$charset'",$sf_api_db_link);
  mysql_query("SET character_set_system = '$charset'",$sf_api_db_link);
	return $sf_api_db_link;
}


?>
