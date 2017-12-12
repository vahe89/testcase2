<?php
if(!isset($rpath))
	$rpath="";
require_once("db.class.php");
require_once("db_obj.class.php");

if(!function_exists("array_merge_recursive_new")){
function array_merge_recursive_new() {

        $arrays = func_get_args();
        $base = array_shift($arrays);

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

class WebUser
{

	public $db;
	protected $show;
	public $webtitle;

	public $isAdmin=false;
	protected $curskin=false;
	public $isLogged=false;
	protected $userCreds=false;
	protected $userId=false;
	protected $order=false;
	protected $rfull=false;
	protected $notConfirmed=false;
	public $db_prefix="";
	public $STEMPL="templates";
	public $skin;

	public $def_lang="fr";

	public $t;
	public $cur_lang;

	protected $stpl="hlist";

	public $root="./";

	public $type="search";

	public $call='dyn';

	public $rpath;

	public $demandRet=true;
	public $confirmedRet=false;
	public $adb_ids=array();
	public $ajax=false;


	function __construct($rpath="")
	{
		require_once("init.php");
		require_once("tables.php");
		$this->def_lang=$def_lang;
		$this->rpath=$rpath;
		if(isset($_REQUEST['clang']))
			$_SESSION['lang']=$_REQUEST['clang'];
		if(!isset($_SESSION['lang']))
			$_SESSION['lang']=$this->def_lang;


		$this->show['curskin']="def";
		$this->set('curskin',$this->show['curskin']);
		$this->skin="{$this->rpath}skins/{$this->curskin}/";

		$this->TEMPL=$this->rpath.$this->STEMPL.(isset($_SESSION['lang'])?"/".$_SESSION['lang']:"/".$this->def_lang);

		$this->webtitle=$webtitle;
		$this->cur_lang=$_SESSION['lang'];

		$this->db=new db($init_db['host'],$init_db['user'],$init_db['pass'],$init_db['db'],$init_db['charset'],"") or die("DB connect error: ".$this->db->getLastError());
		
		$this->t=array();

		foreach($dbobjs as $objname=>$objcfg)
			$this->t[$objname]=new DB_Obj($this->db,$objname,$objcfg,$_SESSION['lang'],$this);
	

		if(isset($_SESSION['class_user'])){
			foreach($_SESSION['class_user'] as $k=>$v)
				$this->set($k,$v);
		}
		else
			$_SESSION['class_user']=array();

		$this->show=array();
		$this->isAdmin=false;



	}


	function __destruct()
	{

	}


	function run()
	{
		if(isset($_REQUEST['slug_req']) && $_REQUEST['slug_req']!=false)
		{
			$rq=$_REQUEST['slug_req'];
			if(strpos($rq,"/")!==false){
				$rqa=explode("/",$rq);
				if(count($rqa)>=2)
				{
					if(isset($this->t[$rqa[0]])){
						$_REQUEST['a']="dbo_{$rqa[0]}";
						$_REQUEST['rid']="{$rqa[1]}";
					}else if(method_exists($this,"do_{$rqa[0]}")){
						$_REQUEST['a']="p_{$rqa[0]}";
						$_REQUEST['rid']="{$rqa[1]}";
					}
				}
			}else{
				if(preg_match("#(.+)\.html$#i",$rq,$rret)){
					$_REQUEST['a']="s_{$rret[1]}";
				}else if(preg_match("#(.+)\.htm$#i",$rq,$rret)){
					$_REQUEST['a']="sl_{$rret[1]}";
				}else if(preg_match("#(.+)\.php$#i",$rq,$rret)){
					$_REQUEST['a']="p_{$rret[1]}";
				}else
					$_REQUEST['a']="dbo_{$rq}";

			}
		}
		header('Content-Type: text/html;charset=utf-8');
		//		var_dump($_SESSION);
		$cookname=str_replace(".","_",$_SERVER['SERVER_NAME'])."_visited";
		$vvv=$this->getConfig("visits");
		if(isset($_REQUEST['ajax']) && $_REQUEST['ajax']==true)
			$this->ajax=true;

		if(!isset($_COOKIE[$cookname]) || $_COOKIE[$cookname]!="visited"){
			$vvv++;
			$this->setConfig('visits',$vvv);
		}

		setcookie($cookname,"visited",(time()+864000*365));


		if(isset($_REQUEST['a']))
		{
			$way=explode("_",$_REQUEST["a"],2);
//			var_dump($way);die();
			if($way[0]=="p" && method_exists($this,"do_".$way[1]))
			{	
				if($tmpl=$this->{"do_".$way[1]}()){
					if(!$this->returnHook($way[1]) && $tmpl!==true)
						$this->lookAt($tmpl);
					return true;
				}
			}
			else if($way[0]=="dbo")
			{	
				if($tmpl=$this->dbo($way[1])){
					if($tmpl!==true)
						$this->lookAt($tmpl);
					return true;
				}
			}

			else if($way[0]=="s" && $this->LookAtVar($way[1],false))
				return true;
			else if($way[0]=="sl" && $this->LookAtVar($way[1],true))
				return true;
			else if($way[0]=="a" && $this->LookAtVar($way[1],true,true))
				return true;
			else if($way[0]=="o" && $this->LookAtVar($way[1],false,false,true))
				return true;

		}
		$this->do_default();

		return true;

	}

	function tpl_require_once_wrap($tpl)
	{
		return $this->tpl_require_wrap($tpl,true);
	}

	function tpl_path($tpl)
	{
		$p="{$this->TEMPL}/".$tpl;
		if($_SESSION['lang']!=$this->def_lang){
			if(is_file($p)){
				return $p;
			}else{
				$p=$this->rpath.$this->STEMPL."/".$this->def_lang."/".$tpl;
				return $p;
			}
		}else
			return $p;
	}

	function inc_file($inc)
	{
		$inc=$inc.".inc.php";
		$p="{$this->TEMPL}/inc/".$inc;
		if($_SESSION['lang']!=$this->def_lang){
			if(is_file($p)){
				return $p;
			}else{
				$p=$this->rpath.$this->STEMPL."/".$this->def_lang."/inc/".$inc;
				return $p;
			}
		}else
			return $p;
	}

	function tpl_require_wrap($tpl,$once=false)
	{
		$p=rpath("/{$this->TEMPL}/".$tpl);
		if($_SESSION['lang']!=$this->def_lang){
			if(is_file($p)){
				if($once)
					require_once($p);
				else
					require($p);
			}else{
				$p=$this->rpath.$this->STEMPL."/".$this->def_lang."/".$tpl;
				if(is_file($p)){
					if($once)
						require_once($p);
					else
						require($p);
				}else
					return false;
			}
		}else{
			if(is_file($p)){
				if($once)
					require_once($p);
				else
					require($p);
			}else
				return false;
		}
		return true;
	}

	function showHeader($tpl="header.tpl.php")
	{
		if($this->ajax==false)
			$this->tpl_require_once_wrap($tpl);
	}

	function showFooter($tpl="footer.tpl.php")
	{
		if($this->ajax==false)
			$this->tpl_require_once_wrap($tpl);
	}


	function lookAt($templ,$ownHeader=false)
	{
		foreach($this->show as $k=>$v)
			$$k=$v;		
		if(!$ownHeader)
			$this->showHeader();

		$this->tpl_require_once_wrap("$templ.tpl.php");

		if(!$ownHeader)
			$this->showFooter();

	}

	function lookAtVar($templ,$mustlogin=false,$admin=false,$ohdr=false)
	{
		foreach($this->show as $k=>$v)
			$$k=$v;
		if(!$ohdr)
			$this->showHeader();
		if(!$mustlogin && $this->tpl_require_once_wrap("dyn/$templ.tpl.php",true)){
			if(!$ohdr)
				$this->showFooter();
			return true;
		}
		else if($mustlogin && !$admin){
			if(!($this->isLogged && $this->tpl_require_once_wrap("dyn_logged/$templ.tpl.php")))
			{
				$rr=array();
				foreach($_REQUEST as $k=>$v)
					$rr[]="$k=$v";

				$_SESSION['class_user']['prev_request']=implode("&",$rr);;
				$this->tpl_require_once_wrap("dyn/login.tpl.php");
			}

			if(!$ohdr)
				$this->showFooter();
			return true;
		}
		else if($mustlogin && $admin){
			if(!($this->isAdmin && $this->tpl_require_once_wrap("admin/$templ.tpl.php")))
				$this->tpl_require_once_wrap("dyn/login.tpl.php");

			if(!$ohdr)
				$this->showFooter();
			return true;
		}
		return false;
	}


	function getConfig($name)
	{
		$this->db->query("select val from {$this->db_prefix}sys_config where name='$name'");

		if($row=$this->db->next())
			return $row['val'];

		return false;
	}

	function setConfig($name,$val)
	{
		$this->db->query("update {$this->db_prefix}sys_config set val='".$this->db->escape($val)."'  where name='$name'");

		return $val;
	}

	function set($var,$val)
	{
		$_SESSION['class_user'][$var]=$val;
		if(property_exists($this,$var))
			$this->{$var}=$val;
	}






	/*******************************************************************************************************/
	/*COMMON ACTIONS*/
	/*******************************************************************************************************/



	function sendEmail($address,$templ,$vars=array())
	{
		if(is_file($this->TEMPL."/emails/$templ.txt"))
		{
			$f=fopen($this->TEMPL."/emails/$templ.txt","rb");
			$c=fread($f,filesize($this->TEMPL."/emails/$templ.txt"));
			$arr=explode('[BODY]',$c,2);
			foreach($vars as $k=>$v){
				if(is_string($v) || is_numeric($v))
					$arr[0]=preg_replace("#\\\${$k}(?=[^a-zA-Z0-9_\\-]+)#mi",$v,$arr[0]);
			}
			foreach($vars as $k=>$v){
				if(is_string($v) || is_numeric($v))
					$arr[1]=preg_replace("#\\\${$k}(?=[^a-zA-Z0-9_\\-]+)#mi",$v,$arr[1]);
			}
			mail($address,$arr[0],$arr[1],"From: ".$this->getConfig("admin_email_from"));
		}
	}

	function sendEmailWithAttach($address,$templ,$vars=array(),$attach=array())
	{
		if(is_file($this->TEMPL."/emails/$templ.txt"))
		{
			//			$ff=finfo_open(FILEINFO_MIME_TYPE);			
			$f=fopen($this->TEMPL."/emails/$templ.txt","rb");
			$c=fread($f,filesize($this->TEMPL."/emails/$templ.txt"));
			$arr=explode('[BODY]',$c,2);
			$arr2=explode('[HEAD]',$arr[0],2);
			$arr2[1]=preg_replace("/\n|\r/im","",$arr2[1]);
			if(!isset($arr2[1]))$arr2[1]="";
			$atch="";
			$bndr="----bndr".uniqid();
			$hdrs="MIME-Version: 1.0\r\nContent-Type: multipart/mixed; boundary=\"$bndr\"\r\n\r\n";
			$bndr="--$bndr";
			$lnks=array();
			if(is_array($attach)){
			foreach($attach as $vfl=>$afl){
				$attach[$vfl]=$afl=array('uid'=>$this->emailUIDs(),'file'=>$afl);
				$lnks[$afl['file']]=$afl['uid'];
				$fname=pathinfo($afl['file']);

				$fl=fopen($afl['file'],"r");
				$atch.="\r\n\r\n$bndr\r\n"."Content-ID: ".$afl['uid']."\r\nContent-Type: ".mime_content_type($afl['file'])."\r\nContent-Disposition:attachment;filename={$fname['basename']};\r\nContent-Transfer-Encoding: base64\r\n\r\n".base64_encode(file_get_contents($afl['file']));
//				$atch.="\r\n\r\n$bndr\r\n"."Content-ID: ".$afl['uid']."\r\nContent-Type: ".mime_content_type($afl['file'])."\r\nContent-Disposition:attachment;filename={$fname['basename']};\r\nContent-Transfer-Encoding: base64\r\n\r\n".base64_encode(fread($fl,filesize($afl['file'])));
				fclose($fl);
			}
		}
			foreach($vars as $k=>$v)
				$arr[1]=str_replace("\$$k","$v",$arr[1]);
			foreach($lnks as $k=>$v)
				$arr[1]=str_replace("$k","cid:$v",$arr[1]);
			mail($address,$arr2[0],$bndr."\r\nContent-Transfer-Encoding: base64\r\n{$arr2[1]}\r\n\r\n".base64_encode($arr[1]).$atch."\r\n\r\n".$bndr,"From: ".$this->getConfig("admin_email_from")."\r\n".$hdrs);
			//			finfo_close($ff);

		}
	}

	function emailUIDs()
	{
		return uniqid().str_replace(".","",$_SERVER['REMOTE_ADDR']).$_SERVER['REMOTE_PORT'].time()."@".$_SERVER['SERVER_NAME'];
	}
	




	function drawMenu($parent=0,$draw=true,$tbl="menu",$tmpl="def_menu")
	{
		return $this->t[$tbl]->listDef($tmpl,array("queryWhere"=>" sys_prios.parent={$parent} ","echo"=>$draw,"echo_INS"=>$draw));
	}

	function drawBanner($pos,$num)
	{
		$cn=unserialize($this->getConfig("banners"));

		if(!is_array($cn) || !isset($cn[$pos][$num]) || !is_file($this->rpath.$cn[$pos][$num]['src']))
			return false;
		if($cn[$pos][$num]['link']!="")
			echo "<a class='{$pos}_{$num}_blink {$pos}_blink blink' href='{$cn[$pos][$num]['link']}'>";
		if($cn[$pos][$num]['c']=="swf"){?>
			<object class='<?php echo "flash_banner {$pos}_{$num}_banner {$pos}_banner banner"?>'>
				<param class='<?php echo "flash_banner {$pos}_{$num}_banner {$pos}_banner banner"?>' name="movie" value="<?php echo $this->rpath.$cn[$pos][$num]['src'];?>"/>
				<param name="wmode" value="opaque" />
				<embed wmode="opaque" class='<?php echo "flash_banner {$pos}_{$num}_banner {$pos}_banner banner"?>' src="<?php echo $this->rpath.$cn[$pos][$num]['src'];?>" >
				</embed>
				</object>

				<?php }else{?>
						<img class='<?php echo "img_banner {$pos}_{$num}_banner {$pos}_banner banner"?>' src="<?php echo $this->rpath.$cn[$pos][$num]['src'];?>"/>
				<?php	}
				if($cn[$pos][$num]['link']!="")
					echo "</a>";
	}


	/*******************************************************************************************************/
	/*USER ACTIONS*/
	/*******************************************************************************************************/


	function do_demand()
	{
		$cstr=md5($_REQUEST['email'].rand().rand());
		$cnf=unserialize($this->getConfig("confirm"));
		$cnf[$cstr]=$_REQUEST;
		if(isset($_FILES) && is_array($_FILES)){
			$cnf[$cstr]['_FILES']=$_FILES;
		}
		$cnf[$cstr]['__do_demand_timestamp']=time();
		foreach($cnf as $k=>$v){
			if(time()-$v['__do_demand_timestamp']>86400*3)
				unset($cnf[$k]);
		}
		$dtpl=$this->onDemand(&$cnf[$cstr]);
		$this->setConfig("confirm",serialize($cnf));
		$this->sendEmail($_REQUEST['email'],$dtpl,array("confirm_url"=>url("/index.php?a=p_confirm&h=$cstr"),"hash"=>$cstr));
		
//		$this->LookAt("confirm",true);
		return $this->demandRet;
	}

	function do_confirm()
	{
		$cnf=unserialize($this->getConfig("confirm"));
		if(isset($_REQUEST['h']) && isset($cnf[$_REQUEST['h']]))
		{
//			$this->sendEmail($this->getConfig("email"),"demand",$cnf[$_REQUEST['h']]);
			$this->setConfig("confirm",serialize($cnf));
			$_SESSION['conf_msg']="OK";
			$this->emailConfirmed(true,$cnf[$_REQUEST['h']]);
			unset($cnf[$_REQUEST['h']]);
			$this->setConfig("confirm",serialize($cnf));
		}else{
			$_SESSION['conf_msg']="ERR";
			$this->emailConfirmed(false,false);

		}

		return $this->confirmedRet;
	}
	function onDemand($req){
		return "user_email_confirmation";
	}

	function setMsg($msg,$id="def"){
		$_SESSION['webuser_msg'][$id]=$msg;
	}

	function getMsg($id="def",$del=true){
		if(!isset($_SESSION['webuser_msg'][$id]) && $_SESSION['webuser_msg'][$id]==false)
			return false;
		$msg=$_SESSION['webuser_msg'][$id];
		if($del)
			unset($_SESSION['webuser_msg'][$id]);
		return $msg;
	}

	function showMsg($id="def",$del=true,$fb=true){
		if(!isset($_SESSION['webuser_msg'][$id]) && $_SESSION['webuser_msg'][$id]==false)
			return false;
		$msg=$_SESSION['webuser_msg'][$id];
		if($del)
			unset($_SESSION['webuser_msg'][$id]);
?>
				<div id='webuser_msg_<?php echo $id?>' class='hidden'>
					<?php echo $msg?>
				</div>
				<script type="text/javascript">
				<?php if($fb){?>
					jQuery(document).ready(function(){jQuery.fancybox(jQuery('#webuser_msg_<?php echo $id?>').html(),{})});
				<?php }else{?>
					jQuery(document).ready(function(){alert(jQuery('#webuser_msg_<?php echo $id?>').html())});
				<?php }?>
				</script>
<?php
	}

	function emailConfirmed($isok,$req){
		return $isok;
	}

	function do_emptyimg(){

		$image=imagecreatetruecolor(1, 1);
		imagesavealpha($image, true);	
		imagealphablending($image,false);
		$color = imagecolorallocatealpha($image, 0, 0, 0, 127);
		imagefilledrectangle($image,0,0,0,0,$color);
		imagealphablending($image,true);

		header('Content-Type: image/png');
		imagepng($image);
		imagedestroy($image);
		die();
	}


	function do_default()
	{
		$this->lookAt("default");
	}

	function dbo($obj){

		$o=$this->t[$obj];
		if((isset($_REQUEST['rid']) && $_REQUEST['rid']!=false)){
			$o->showUser();	
			return true;
		}
		return false;
	}

	function returnHook($name){
		return false;
	}


	//===============================================================
	

/*	function do_adb(){
		if(!$this->isAdmin)
			return false;

		$this->adb($_REQUEST);
	
//		die("OK:".$this->db->getLastError());			

		if($_REQUEST['ajax']==1)
			die("OK");

		//	var_dump($rdt);die();
		$popupr="";
			if(isset($_REQUEST['popup']) && $_REQUEST['popup']==1)
				$popupr="popup=1&";
		if(isset($_REQUEST['on_redirect']) && $_REQUEST['on_redirect']!="" && $a[1]=="i")
			header("Location: index.php?$popupr{$_REQUEST['on_redirect']}$pk");
		else if(isset($_REQUEST['on_redirect']) && $_REQUEST['on_redirect']!="" && $a[1]=="u")
			header("Location: index.php?$popupr{$_REQUEST['on_redirect']}$pk");
		else if(isset($_REQUEST['redirect']) && $_REQUEST['redirect']!="")
			header("Location: index.php?$popupr{$_REQUEST['redirect']}");
		else if(isset($_REQUEST['redirect_url']) && $_REQUEST['redirect_url']!="")
			header("Location: {$_REQUEST['redirect_url']}");
		else
			$this->LookAtVar($a[2],true,true);
		return true;
	}
 */



	function adb($ireq){

		if(isset($ireq['multi_row']) && is_array($ireq['multi_row']))
			$r_loop=$ireq['multi_row'];
		else
			$r_loop=array(0=>$ireq);

		$this->adb_ids=array();
			$pk=0;
		foreach($r_loop as $lk=>$creq){

			if(!isset($creq['data']))
				$creq['data']=array();


				if(isset($ireq['multi_row_share'])){
					$creq=array_merge_recursive_new($ireq['multi_row_share'],$creq);
				}

			if(!isset($creq['isLang']) || $creq['isLang']==true){
			if($lk!=$this->def_lang && $lk!==0)
				$creq['data']['r.lid']=$pk;
			else
				$creq['data']['r.lid']=0;
			if($lk!==0)
				$creq['data']['r.lang']=$lk;
			}else{
				$creq['data']['r.lang']=$this->def_lang;
				$creq['data']['r.lid']=0;
			}


			$i=preg_grep("#act_.*#",array_keys($creq));
			$a=explode("_",reset($i),3);
			$msg="";

/*			$acc=$this->t[$a[2]]->gO("access_id");
			if(!(isset($this->access) && is_array($this->access) && (in_array("all",$this->access) || in_array($a[2],$this->access) || in_array($acc,$this->access))))
			continue;
 */
			if(count($a)==3){

				if(method_exists($this,"adb_before_all"))
					$this->adb_before_all();

				$o=false;
				if(is_object($this->t[$a[2]])){
					$o=$this->t[$a[2]];
					$a[2]=$o->tbl;
				}

				if($pk==0)	{
					if($o)
						$o->preAdb($a[1],$creq);
				}else{
					if($o)
						$o->preAdbClear($creq);
				}

				if(method_exists($this,"adb_before_adb"))
					$this->adb_before_adb();

				//----------ADB
				$rdt=array();

				$ar=$this->db->act($a[1],$a[2],$creq['data'],$msg,$rdt);
				if($a[1]=="i" || $a[1]=="u")
					$this->adb_ids[]=$ar;
/*echo "<hr>ADB";
var_dump($ar,$a[1],$a[2],$creq['data'],"RDT:",$rdt,$creq['data']['r.lang'],$creq['data']['r.lid']);
echo "<hr>ERR?:".$this->db->getLastError()."<hr>";
echo "</hr>";
die();*/
				//----------ADB
				if(method_exists($this,"adb_after_adb"))
					$this->adb_after_adb();

				if($pk==0)	{
					if($o)
						$o->postAdb($a[1],$ar,$rdt,$creq);
				}				
				if($pk==0)
					$pk=$ar;

				if(method_exists($this,"adb_after_all"))
					$this->adb_after_all();

			}
		}

	}


}


?>
