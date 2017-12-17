<?php

require_once("db.class.php");
require_once("db_obj.class.php");


class Admin
{
	/**
	 * @var db
	 */
	public $db;
	/**
	 * @var array
	 */
	protected $show;


	public $isAdmin=false;
	public $isLogged=false;
	protected $userCreds=false;
	public $userId=false;
	protected $order=false;
	protected $rfull=false;
	protected $notConfirmed=false;
	public $db_prefix="";
	public $TEMPL="./templates";
	public $def_lang="fr";
	public $langs=array("fr");
	public $access=array();
	public $superAdmin=false;
	public $t;
	public $slug2t=array();
	public $v;
	public $vs;
	public $u;
	/**  @var db_obj */
	public $cdbo=false;

	public $curUsr=false;
	public $curUsrId=false;
	public $curUsrSlug=false;
	public $usrSlugFld=false;

	protected $w_words=array();
	protected $y_words=array();

	public $root="../";

	public $type="";

	public $curskin;

	public $call='stat';

	public $vname=false;

//	public $urlmap=false;

	public $user_form_names=false;

	public $req_nonce_ok=false;

	public $trace_login=true;



	function __construct()
	{
		require_once("init.php");
		require_once("tables.php");
		require_once("views.php");
		//		require_once("urlmap.php");

		//		$this->urlmap=$urlmap;

		$this->TEMPL .= "/" . $sys_admin_tpls;
		$this->curskin = $sys_admin_skin;


		if(check_arr($sys_user_auth,'tbl','login','pass') || (isset($sys_user_auth['deflogin']) && $sys_user_auth['deflogin']==true))
			$this->u=$sys_user_auth;
		else
			$this->u=false;
		if(is_array($objviews) && count($objviews)>0){
			$this->vs=$objviews;
			$this->v=false;

		}else{
			$this->vs=false;
			$this->v=false;
		}

		$this->db=new db($init_db['host'],$init_db['user'],$init_db['pass'],$init_db['db'],$init_db['charset'],"") or die("DB connect error: ".$this->db->getLastError());
		$this->def_lang=$def_lang;
		global $def_editor;
		if(isset($set_editor))
			$def_editor=$set_editor;

		sys_files::$sdb=$this->db;
		sys_prios::$sdb=$this->db;
		sys_links::$sdb=$this->db;
		sys_m2m::$sdb=$this->db;

		$admlang=$this->def_lang;
		$this->t=array();

		$this->webtitle=$webtitle;


		$this->langs=$glangs;

		if(isset($_SESSION['class_admin'])){
			foreach($_SESSION['class_admin'] as $k=>$v)
				$this->set($k,$v);
		}
		else
			$_SESSION['class_admin']=array();

		if(isset($this->vname) && $this->vname!=false && $this->vname)
			$this->v=$this->vs[$this->vname];


		if(isset($sys_user_form_names) && $sys_user_form_names){
			$this->user_form_names=true;
			$this->_restoreSecFormNames();
		}


		foreach($dbobjs as $objname=>$objcfg){
			if(!isset($objcfg['opts']) || !is_array($objcfg['opts']))
				$objcfg['opts']=array();

			if(isset($objcfg['opts']['sys_files']['url']))
				$objcfg['opts']['sys_files']['url']="../".$objcfg['opts']['sys_files']['url'];
			if(isset($objcfg['opts']['sys_files']['path']))
				$objcfg['opts']['sys_files']['path']="../".$objcfg['opts']['sys_files']['path'];
			if(!isset($objcfg['opts']['rowsPerPage']))
				$objcfg['opts']['rowsPerPage']=false;

			if(isset($this->v['objs'][$objname])){
				if(isset($objcfg['opts']['cV']))
					$objcfg['opts']['cV']=array_merge($objcfg['opts']['cV'],$this->v['objs'][$objname]);
				else
					$objcfg['opts']['cV']=$this->v['objs'][$objname];
			}

			$objcfg['opts']['user_form_names']=$this->user_form_names;

			if (isset($objcfg['sf_table']) && $objcfg['sf_table'] == true)
				$objcfg['opts']['sf_table'] = true;

			if(isset($objcfg['obj_slug']) && $objcfg['obj_slug']!=false)
				$this->slug2t[$objcfg['obj_slug']]=$objname;
			 if(is_array($this->v['objs'][$objname]['obj_opts']))
	       $objcfg['opts']=array_merge_recursive_new($objcfg['opts'],$this->v['objs'][$objname]['obj_opts']);

			$this->t[$objname]=new DB_Obj($this->db,$objname,$objcfg,$admlang,$this);
		}
		foreach ($this->t as $n => $o) {
			/**
			 * @var $o db_obj
			 */

			if (is_array($o->rels)) {
				foreach ($o->rels as $rf => $rv) {

					if (isset($rv['obj']) && is_object($this->t[$rv['obj']])) {
						$o->rels[$rf]['_dbo'] = $this->t[$rv['obj']];
						if (!isset($rv['tbl']))
							$o->rels[$rf]['tbl'] = $this->t[$rv['obj']]->tbl;
					}
					if (!isset($rv['tbln'])) {
						if (is_object($o->rels[$rf]['_dbo']) && $this->t[$rv['obj']]->gO('sf_table') == true)
							$o->rels[$rf]['tbln'] = str_replace("__c", "__r", $rf);
						else if (is_object($o->rels[$rf]['_dbo']))
							$o->rels[$rf]['tbln'] = "{$rv['obj']}_{$rf}";
						else
							$o->rels[$rf]['tbln'] = "{$rv['tbl']}_{$rf}";
					}
				}
			}
		}

		$this->show=array();

		$this->show['curskin'] = $this->curskin;
		$this->skin = "./skins/{$this->curskin}/";

		$this->ajaxStoreClear();

		if(is_array($this->vs) && count($this->vs)>0 && !is_array($this->v) && ($thisd->isLogged || $this->isAdmin)){
			$this->do_logout();
		}
	}


	function __destruct()
	{

	}

	function new_vobj($name,$data,$cfg,$cV){
		if(!is_array($cfg))
			$cfg=array();
		if(!is_array($data))
			$data=array();
		if($name!=false){
		if(is_array($cV) && count($cV)>0){
			if(!is_array($cfg['opts']))
				$cfg['opts']=array();
			if(!is_array($cfg['opts']['cV']))
				$cfg['opts']['cV']=$cV;
			else
				$cfg['opts']['cV']=array_merge_recursive_new($cfg['opts']['cV'],$cV);
		}
		$cfg['virt']=$data;
		$cfg['langs']=$this->langs;
		$this->t[$name]=new DB_Obj($this->db,$name,$cfg,$this->def_lang,$this);

		return $this->t[$name];			
		}
		return false;
	}

	function run()
	{
		if(isset($_REQUEST['form_nonce']) && $_REQUEST['form_nonce']!=false && isset($_SESSION['cur_nonce']) && $_SESSION['cur_nonce']==$_REQUEST['form_nonce']){
			$this->req_nonce_ok=true;
			$_SESSION['cur_nonce']=false;
		}

		if(isset($_REQUEST['slug_req']) && $_REQUEST['slug_req']!=false)
		{
			$rq=$_REQUEST['slug_req'];
			if(strpos($rq,"/")!==false){
				$rqa=explode("/",$rq);
				$_REQUEST['slug_arr']=$rqa;
				if(count($rqa)>=2)
				{
					if(isset($this->slug2t[$rqa[0]]) && $this->slug2t[$rqa[0]]!=false){
						$tts=$this->slug2t[$rqa[0]];
						if(is_object($this->t[$tts]) && isset($this->t[$tts]->obj_slug) && $this->t[$tts]->obj_slug==$rqa[0])
							$rqa[0]=$tts;
					}
					if(isset($this->t[$rqa[0]])){
						$_REQUEST['a']="dbo_{$rqa[0]}";
						$_REQUEST['rid']="{$rqa[1]}";
					}else if(method_exists($this,"do_{$rqa[0]}")){
						$_REQUEST['a']="p_{$rqa[0]}";
						$_REQUEST['rid']="{$rqa[1]}";
					}
				}
			}else{
				$_REQUEST['slug_arr']=array($rq);

				if(preg_match("#(.+)\.html$#i",$rq,$rret)){
					$_REQUEST['a']="s_{$rret[1]}";
				}else if(preg_match("#(.+)\.htm$#i",$rq,$rret)){
					$_REQUEST['a']="sl_{$rret[1]}";
				}else if(preg_match("#(.+)\.php$#i",$rq,$rret)){
					$_REQUEST['a']="p_{$rret[1]}";
				}else if(method_exists($this,"do_{$rq}")){
					$_REQUEST['a']="p_{$rq}";
				}else{
					if(isset($this->slug2t[$rq]) && $this->slug2t[$rq]!=false){
						$tts=$this->slug2t[$rq];
						if(is_object($this->t[$tts]) && isset($this->t[$tts]->obj_slug) && $this->t[$tts]->obj_slug==$rq)
							$rq=$tts;
					}
					$_REQUEST['a']="dbo_{$rq}";
				}

			}
		}
		
		header('Content-Type: text/html;charset=utf-8');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Cache-Control: post-check=0, pre-check=0', FALSE);
		header('Pragma: no-cache');
//		var_dump($this->isAdmin);

		global $sys_public_methods;

		if(!($this->isAdmin || $this->isLogged) && (
		 	(!isset($_REQUEST['a']) || $_REQUEST['a']!='p_login') &&
			!(is_array($sys_public_methods) && isset($_REQUEST['a']) && in_array($_REQUEST['a'], $sys_public_methods))
		) ){
			if(isset($_REQUEST['slug_req']) && $_REQUEST['slug_req']!=false){
				header("Location: ".aurl("/"));
				die();
			}
			$this->do____auth();
			return false;
		}
//var_dump($_REQUEST);
		global $sys_access_tracking,$sys_app_name,$sys_skip_slug_tracking,$sys_skip_url_tracking;
;
		if($sys_access_tracking==true && !in_array($_SERVER['REQUEST_URI'],$sys_skip_url_tracking) && !in_array($_REQUEST['slug_req'],$sys_skip_slug_tracking)){
			$this->db->query("insert into {$this->p->db_prefix}sys_access_track set 
				user_id='{$this->curUsrId}',username='{$this->curUsrSlug}', 
				domain='{$_SERVER['HTTP_HOST']}', url='{$_SERVER['REQUEST_URI']}', slug_req='{$_REQUEST['slug_req']}',app='$sys_app_name'
				");
		}

		if (isset($_REQUEST['a']) && ($this->isAdmin || $_REQUEST['a'] == "p_login" ||
			(is_array($sys_public_methods) && isset($_REQUEST['a']) && in_array($_REQUEST['a'],$sys_public_methods) )
		))
		{
			$way=explode("_",$_REQUEST["a"],2);
			if($way[0]=="p" && method_exists($this,"do_".$way[1]))
			{	
				if($tmpl=$this->{"do_".$way[1]}()){
					if($tmpl!==true)
						$this->lookAt($tmpl);
					return true;
				}
			}
			else if($way[0]=="dbo" && isset($this->t[$way[1]]) && is_object($this->t[$way[1]]))
			{	
				$acc=$this->t[$way[1]]->gO("access_id");
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

		}

		if(isset($_REQUEST['slug_req']) && $_REQUEST['slug_req']!=false){
			header("Location: ".aurl("/"));
			die();
		}

		if($this->view_def())
			return true;
		
		if($this->isAdmin)
			$this->lookAt("admin");
		else if($this->isLogged)
			$this->lookAt("default");
		else
			$this->do_default();
		

		return true;

	}

	function view_def(){
		if(is_array($this->v) && isset($this->v['defpage'])){
			$dp=$this->v['defpage'];
			if(isset($dp['type'])){
				if($dp['type']=='obj' && isset($dp[$dp['type']])){
					if(!is_object($this->t[$dp['obj']]) || !isset($this->v['objs'][$dp['obj']]))
						return false;
					return $this->dbo($dp['obj']);
				}else if($dp['type']=='tpl' && isset($dp[$dp['type']])){
					$this->lookAt($dp['tpl']);
					return true;
				}else if($dp['type']=='dtpl' && isset($dp[$dp['type']])){
					$this->lookAtVar($dp['dtpl']);
					return true;
				}else if($dp['type']=='url' && isset($dp[$dp['type']])){
					header("Location: {$dp['url']}");
					die();
				}
				
			}
		}

		return false;

	}

	function tpl_path($tpl)
	{
		$p="{$this->TEMPL}/".$tpl;
		if($_SESSION['lang']!=$this->def_lang){
			if(is_file($p)){
				return $p;
			}else{
				$lp=$this->rpath.$this->STEMPL."/".$this->def_lang."/".$tpl;
				if(is_file($lp))
					return $lp;
			}
		}
		return $p;
	}

	function header_head(){
		require_once("{$this->TEMPL}/header_head.tpl.php");
	}
	function footer_foot(){
		require_once("{$this->TEMPL}/footer_foot.tpl.php");
	}

	function lookAt($templ,$ownHeader=false)
	{
		foreach($this->show as $k=>$v)
			$$k=$v;		
		if(!$ownHeader)
			require_once("{$this->TEMPL}/header.tpl.php");

		require_once("{$this->TEMPL}/$templ.tpl.php");

		if(!$ownHeader)
			require_once("{$this->TEMPL}/footer.tpl.php");
	}

	function showHeader()
	{
		require_once("{$this->TEMPL}/header.tpl.php");
	}
	function showFooter()
	{
		require_once("{$this->TEMPL}/footer.tpl.php");
	}


	function lookAtVar($templ,$mustlogin=false,$admin=false,$ownHeader=false)
	{
		foreach($this->show as $k=>$v)
			$$k=$v;
		if($ownHeader==false)
			require_once("{$this->TEMPL}/header.tpl.php");
		if (!$mustlogin && is_file("{$this->TEMPL}/dyn/$templ.tpl.php")) {
			require_once("{$this->TEMPL}/dyn/$templ.tpl.php");
			if($ownHeader==false)
				require_once("{$this->TEMPL}/footer.tpl.php");
			return true;
		} else if ($mustlogin && !$admin && is_file("{$this->TEMPL}/dyn_logged/$templ.tpl.php")) {
			if($this->isLogged)
				require_once("{$this->TEMPL}/dyn_logged/$templ.tpl.php");
			else
			{
				$rr=array();
				foreach($_REQUEST as $k=>$v)
					$rr[]="$k=$v";

				$_SESSION['class_admin']['prev_request']=implode("&",$rr);;
				require_once("{$this->TEMPL}/dyn/login.tpl.php");
			}

			if($ownHeader==false)
				require_once("{$this->TEMPL}/footer.tpl.php");
			return true;
		} else if ($mustlogin && $admin && is_file("{$this->TEMPL}/admin/$templ.tpl.php")) {
			if($this->isAdmin)
				require_once("{$this->TEMPL}/admin/$templ.tpl.php");
			else
				require_once("{$this->TEMPL}/dyn/login.tpl.php");

			if($ownHeader==false)
				require_once("{$this->TEMPL}/footer.tpl.php");
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
	function setConfig($name,$val,$autoIns=false)
	{
		$this->db->query("update {$this->db_prefix}sys_config set val='".$this->db->escape($val)."'  where name='$name'");
		if($this->db->numRows()==0 && $autoIns){
			$this->db->query("insert into {$this->db_prefix}sys_config set val='".$this->db->escape($val)."', name='$name'");
		}

		return $val;
	}

	function set($var,$val)
	{
		$_SESSION['class_admin'][$var]=$val;
		if(property_exists($this,$var))
			$this->{$var}=$val;
	}





	function listConfig()
	{
		$this->db->query("select * from {$this->db_prefix}sys_config");
		$ret="";
		while($r=$this->db->next())
			$ret.="<span id='s_{$r['name']}'>{$r['name']}</span><br><input type='text' id='{$r['name']}' name='var[{$r['id']}]' value='{$r['val']}'><br>";
		return $ret;
	}


	function _secureFormNames($fn){
		$ret=$fn;
		if($this->user_form_names){
			$ret=$this->_secureFormNamesForce($fn);
		}
		return $ret;
	}
	function _secureFormNamesForce($fn){
		$ret=preg_replace_callback('#^[^\[\]]+?(?=\[)|(?<=\[)[^\[\]]+?(?=\])#i',array($this,"_secureFormNames_callback"),$fn);
		return $ret;
	}
	function _secureFormNames_callback($ina){
		$in=$ina[0];
		if(isset($_SESSION['dbo_objs']['sec_field_rmap'][$in])){
			$tsec=$_SESSION['dbo_objs']['sec_field_rmap'][$in];
			if($_SESSION['dbo_objs']['sec_field_map'][$tsec]==$in){
				return $_SESSION['dbo_objs']['sec_field_rmap'][$in];
			}
		}
		$sec=str_replace(".","_",crypt($in));
		$_SESSION['dbo_objs']['sec_field_map'][$sec]=$in;
		$_SESSION['dbo_objs']['sec_field_rmap'][$in]=$sec;
		return $sec;
	}
	function _restoreSecFormNames($delReqData=true,$ireq=false){
		global $secFieldsRestored;
		if($secFieldsRestored=="done")
			return true;
		if($ireq==false){
			if(isset($_SESSION['dbo_objs']['sec_field_map']) && is_array($_SESSION['dbo_objs']['sec_field_map']) && count($_SESSION['dbo_objs']['sec_field_map'])>0){
				if($delReqData){
					unset($_REQUEST['data']);
					unset($_REQUEST['multi_row']);
					foreach($_REQUEST as $k=>$v){
						if(strpos($k,"act_")===0)
							unset($_REQUEST[$k]);
					}
				}
				
				$_REQUEST=$this->_restoreSecFormNames($delReqData,$_REQUEST);
				$secFieldsRestored="done";
					return true;

			}else
				return false;

		}else if(is_array($ireq)){
			$sessm=$_SESSION['dbo_objs']['sec_field_map'];
			$req=array();
			foreach($ireq as $k=>$v){
				if(is_array($v))
					$v=$this->_restoreSecFormNames($delReqData,$v);
				if(isset($sessm[$k])){
					$req[$sessm[$k]]=$v;
				}else
					$req[$k]=$v;
			}
			return $req;
		}
		return $ireq;

	}

	function sFA($ajaxC=false,$storeOpts=true,$drawRefresh=true,$repeatT=5000){
		return $this->storeForAjax($ajaxC,$storeOpts,$drawRefresh,$repeatT);
	}
	function sFA2($ajaxC,$storeOpts=true,$drawRefresh=true,$repeatT=5000){
		$r=$this->storeForAjax($ajaxC,$storeOpts,$drawRefresh,$repeatT);
		return $r['r'];
	}
	function storeForAjax($ajaxC=false,$storeOpts=true,$drawRefresh=true,$repeatT=5000){
			if($ajaxC==false){
				$ajaxC=mt_rand(0,99999).time().mt_rand(0,99999);
			}
			$ajaxC=preg_replace("#[^a-zA-Z_0-9]+#","",$ajaxC);
			if($storeOpts==true){
				$_SESSION['ajaxStore'][$ajaxC]=array();
				foreach($this->t as $on=>$o){
					$_SESSION['ajaxStore'][$ajaxC]['t']=time()+120;
					$_SESSION['ajaxStore'][$ajaxC]['d'][$on]=$o->copts;
/*					if($o->virt==true)
	$_SESSION['ajaxStore'][$ajaxC]['o'][$on]=$o;*/
				}
			}
			$ret="";
			if($drawRefresh==true){
				$ret.="<div id='__ajax_store_flag_{$ajaxC}' class='__ajax_store_flag_div' style='display:none'>{$ajaxC}</div>";
				$ret.="<script type='text/javascript'>";
				$ret.="
					if(typeof __ajax_store_refresh == 'undefined'){
						__ajax_store_refresh=function(){
							var ids='';
							var divs=jQuery('div.__ajax_store_flag_div');
							for(var ii=0;ii<divs.length;ii++){
								if(ids!='')
									ids+='|';
								ids+=jQuery(divs[ii]).html();
						}
						if(ids!='')	{
							jQuery.post('".aurl("/ajax_refresh")."',{c:ids});
						}
							setTimeout(function(){
								if(typeof __ajax_store_refresh != 'undefined'){
									//&& jQuery('div.__ajax_store_flag_div').length!=0)
									__ajax_store_refresh();
								}
								//else {delete __ajax_store_refresh;}
							},$repeatT);
						}
					__ajax_store_refresh();
					}
						";
				$ret.="</script>";
				}

			return array('r'=>$ret,'c'=>$ajaxC);
	}

	function rFA($ajaxC){
		return $this->restoreForAjax($ajaxC);
	}
	function restoreForAjax($ajaxC){
		$ajaxC=preg_replace("#[^a-zA-Z_0-9]+#","",$ajaxC);
		if(is_array($_SESSION['ajaxStore'][$ajaxC])){
			$s=$_SESSION['ajaxStore'][$ajaxC];
			foreach($this->t as $on=>$o){
				$o->setCopts(array());
				$o->copts=$s['d'][$on];
			}
/*			if(isset($s['o'])){
				foreach($s['o'] as $on=>$vo)
					$this->t[$on]=$vo;
		}*/
			return true;
		}
		return false;
	}

	function ajaxStoreClear($timeout=12){
		if(is_array($_SESSION['ajaxStore'])){
			$t=time();
			foreach($_SESSION['ajaxStore'] as $aid=>$d){
				if($t-$d['t']>$timeout)
					unset($_SESSION['ajaxStore'][$aid]);
			}
		}
	}

	function do_ajax_refresh(){
		if(!(isset($_REQUEST['c']) && $_REQUEST['c']!=false))
			die('ERR');
		$ca=explode("|",$_REQUEST['c']);
		foreach($ca as $c){
//		if(!is_array($_SESSION['ajaxStore'][$c]))
			//			die('ERR2');
			if(isset($_SESSION['ajaxStore'][$c]['t']))
				$_SESSION['ajaxStore'][$c]['t']=time();
		}
		die('OK');
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





/*	function selMenuParent($parent=0,$sel=false,$step="")
	{
		$ret="";
		if($parent==0)
			$incc="";
		else
			$incc="|--&gt;";

		$this->db->query("select m.*,p.prio as prio from {$this->db_prefix}menu m left join {$this->db_prefix}sys_prios p on (p.tid=m.id and p.tbl='menu') where lang='fr' and p.parent=$parent order by p.prio asc,m.id asc","menu_s{$parent}");
		while($row=$this->db->next("menu_s{$parent}"))
		{
			$ret.="<option ".($sel===$row['id']?"selected":"")." value='{$row['id']}'>{$step}$incc {$row['name']}</option>";
			$ret.=$this->selMenuParent($row['id'],$sel,$step."&nbsp;&nbsp;");
		}

		return $ret;
	}


	function selMenuPrio($parent=0,$sel=false,$step=0)
	{
		$ret="";

		$this->db->query("select m.*,p.prio as prio from {$this->db_prefix}menu m left join {$this->db_prefix}sys_prios p on (p.tid=m.id and p.tbl='menu') where lang='fr' and p.parent=$parent order by p.prio asc,m.id asc","menu_s{$parent}");
		$row=$this->db->next("menu_s{$parent}");
		if($row==false)
			return "";
		$prow=false;
		$prio=0;
		do
		{
			if($row['id']===$sel && $prow!==false){
				$ret.="<option style='display:none;' class='curr-{$parent} m-hide' selected value='".($prio)."'>{$prow['name']}</option>";
//				$prio++;
			}
			else if($prow!==false && $prow['id']!==$sel){
				$ret.="<option style='display:none;' class='curr-{$parent} m-hide' value='".($prio)."'>{$prow['name']}</option>";
//				$prio++;
			}
			$prio++;

			$ret.=$this->selMenuPrio($row['id'],$sel,$step);
			$prow=$row;
		}
		while($row=$this->db->next("menu_s{$parent}"));
		if($prow!==false && $prow['id']!==$sel)
			$ret.="<option ".($sel===false?"selected":"")." style='display:none;' class='curr-{$parent} m-hide' value='".($prio)."'>{$prow['name']}</option>";

		return $ret;
	}

	function lastMenuPrioId($parent)
	{
		$this->db->query("select max(prio)+1 as lid from {$this->db_prefix}menu where parent=$parent","lmprio");
		$row=$this->db->next("lmprio");
		return $row['lid'];
	}*/


	function setMsg($msg,$name="def"){
		if(!isset($_SESSION['custom_message']) || !is_array($_SESSION['custom_message']))
			$_SESSION['custom_message']=array();
		
		$_SESSION['custom_message'][$name]=$msg;
	}

	function showMsg($name="def"){
		if(!(isset($_SESSION['custom_message']) && is_array($_SESSION['custom_message']) && isset($_SESSION['custom_message'][$name]) && $_SESSION['custom_message'][$name]!=false))
			return false;

		$msg = str_replace(array("'", "\n", "\r"), array("\\'", '\n', '\r'), $_SESSION['custom_message'][$name]);
		unset($_SESSION['custom_message'][$name]);

		return "jQuery('body').wHumanMsg({opacity:1,msgDelay:10000000,offset:10}).wHumanMsg('msg','$msg');";
//		return "jQuery('<div class=\"msgDlg\">$msg</div>').dialog({modal:true,width:'90%'});";
	}


	/*******************************************************************************************************/
	/*CUSTOMER ACTIONS*/
	/*******************************************************************************************************/




	function do_fastauth(){
		if(!$this->isLogged && !$this->isAdmin)
			return false;
		if(!(isset($this->v['fastauth']) && $this->v['fastauth']==true))
			return false;
		if(!(isset($_REQUEST['slug_arr']) && isset($_REQUEST['slug_arr'][1]) && $_REQUEST['slug_arr'][1]!=false))
			return false;
		if(!(isset($this->t[$this->u['obj']]) && is_object($this->t[$this->u['obj']])))
			return false;

		$uo=$this->t[$this->u['obj']];
		$this->db->query("select * from {$this->db_prefix}{$this->u['tbl']} where {$uo->slug_field}='{$_REQUEST['slug_arr'][1]}'","fast_auth");
		$nu=$this->db->next("fast_auth");
		if(is_array($nu) && isset($nu[$uo->slug_field]) && $nu[$uo->slug_field]==$_REQUEST['slug_arr'][1]){
				$ts=$_SESSION;
				$_SESSION=array();
				$_SESSION['logins_stack']=array();
				array_push($_SESSION['logins_stack'],$ts);

			if($this->selUserView($nu)){
				$this->set('isAdmin',true);
				$this->set('isLogged',true);
				$this->set('access',array("all"));
				$this->set('superAdmin',true);
				$this->set('curUsr',$nu);
				$this->set('curUsrId',$nu['id']);
				$this->set('curUsrSlug',$nu[$this->t[$this->u['obj']]->slug_field]);
				$this->set('usrSlugFld',$this->t[$this->u['obj']]->slug_field);
				header("Location: ".aurl("/"));
				die();
			}else
				$_SESSION=$ts;
		}

		return false;		
	}

	function login_trace()
	{
		if(!$this->isLogged && !$this->isAdmin)
			return false;
		global $sys_login_trace_map;
		$tm=$sys_login_trace_map;
		if(is_array($tm) && (isset($tm['obj']) || isset($tm['tbl'])) && isset($tm['map']) && is_array($tm['map'])){
			if($this->trace_login){
				$qf=array();
				$sfobj=false;
				$isobj=false;
				$to=false;
				if(isset($tm['obj']) && is_object($this->t[$tm['obj']])){
					$isobj=true;
					$to=$this->t[$tm['obj']];

					if ($this->t[$tm['obj']]->gO('sf_table') == true)
						$sfobj=true;
				}
					
				foreach($tm['map'] as $k=>$v){
					if($v=='_timestamp')
						$qf[]="$k=NOW()";
					else if(strpos($v,"_g")===0){
						if(preg_match('#([^[]+)\[([^]]+)\]#im',str_replace("_g","",$v),$m) && is_array($m)){
							$qf[]="$k='".($GLOBALS[$m[1]][$m[2]])."'";
						}						
					}else	if(isset($this->curUsr[$v])){
						$qf[]="$k='{$this->curUsr[$v]}'";
						if(strtolower($v)=='id' && $sfobj==true){
							if(isset($to->fctrls[$k]['c']) && $to->fctrls[$k]['c']=='ref'){
								$qf[]="{$k}_id='{$this->curUsr['SF_Id']}'";
								$qf[]="{$k}_slug='{$this->curUsrSlug}'";
							}
						}
					}
				}
				
				$tbl=false;
				if(!$isobj && isset($tm['tbl']))
					$tbl=$tm['tbl'];
				if($isobj){
					$tbl=$to->tbl;
				}
				if($tbl!=false){
					$this->db->query($q="insert into {$tbl} set ".implode(",",$qf));
					$nid=$this->db->lastInsertId();
					if($nid!=false && $sfobj==true){
						if($to->slug_field!=false){
							$slgV = $this->dbo_makeslug($to->oname, $nid, "UTRC");
							$this->db->query($q="update {$tbl} set {$to->slug_field} = '{$slgV}' where id={$nid}");
						}

						run_async("async_app2sf_api",array('apireq'=>array('obj_name'=>$to->oname,'act'=>'manage','ids'=>array($nid))),$this->db);
						}
						
				}
			}
		}
	}

	function do_fastlink(){
		if(isset($_REQUEST['h']) && $_REQUEST['h']!=false){
			$fl=$this->db->getRow("select * from {$this->db_prefix}fastaccess where hash='{$_REQUEST['h']}'");
			if($fl['id']!=false && $fl['ts']!=false && $fl['hash']==$_REQUEST['h']){
				$ts=strtotime($fl['ts']);
				$cts=time();
				if($cts-86000<$ts && $fl['use_cnt']<=5){
					$ua=explode(":",$fl['userid']);
					$uid=$ua[0];
					$ufld=false;
					if(isset($ua[1]))
						$ufld=$ua[1];
					$uo=$this->t[$this->u['obj']];
					$uoSF=$uo->slug_field;
					if($ufld!=false)
						$uoSF=$ufld;
					$this->db->query($q="select * from {$this->db_prefix}{$this->u['tbl']} where {$uoSF}='{$uid}'","fast_auth");
					$nu=$this->db->next("fast_auth");
//					var_dump($q,$nu,$ua,$ufld,$uoSF);
					if(is_array($nu) && isset($nu[$uoSF]) && $nu[$uoSF]==$uid){
						if($this->selUserView($nu)){
							$this->set('isAdmin',true);
							$this->set('isLogged',true);
							$this->set('access',array("all"));
							$this->set('superAdmin',true);
							$this->set('curUsr',$nu);
							$this->set('curUsrId',$nu['id']);
							$this->set('curUsrSlug',$nu[$this->t[$this->u['obj']]->slug_field]);
							$this->set('usrSlugFld',$this->t[$this->u['obj']]->slug_field);
							header("Location: ".aurl("{$fl['url']}"));
							$this->db->query("update {$this->db_prefix}fastaccess set use_cnt=use_cnt+1 where id={$fl['id']}");
							die();
						}


					}
				}
			}
		}

		die('ERR');
		return false;
	}


		function do_login()
		{
			if($this->isLogged || $this->isAdmin)
				return false;
			if($_REQUEST['login']==false || $_REQUEST['pass']==false){
				$this->setMsg("Both Login and Password should be specified");
				$this->lookAt("login",true);
				return true;

			}

			if ($this->u == false || (isset($this->u['deflogin']) && $this->u['deflogin'] == true)) {

				$lr=$this->deflogin();

				if($this->isLogged || $this->isAdmin){

					if($this->selUserView(array('localAdmin'=>true,'localLogin'=>$this->userCreds['ulogin']))){
                        $this->set('curUsr', array('Name' => $this->userCreds['uname']));
                        $this->set('curUsrId','0');
                        $this->set('curUsrSlug','Admin');
                        $this->set('usrSlugFld','LocalAdmin');

                        header("Location: ".aurl("/"));
                        die();
					}else{

						return $lr;
					}
				}
				if ($this->u == false) {
					$this->setMsg("Wrong Username or Password.");
					$this->lookAt("login",true);
					return true;
				}

			}

			$this->db->query("select * from {$this->db_prefix}{$this->u['tbl']} where {$this->u['login']} LIKE '{$_REQUEST['login']}'");
			$u=$this->db->next();

			if(is_array($u) && strtolower($u[$this->u['login']])==strtolower($_REQUEST['login']) && $u[$this->u['pass']]==$_REQUEST['pass']){
				if($this->selUserView($u)){

					$this->set('isAdmin',true);
					$this->set('isLogged',true);
					$this->set('access',array("all"));
					$this->set('superAdmin',true);
					$this->set('curUsr',$u);
					$this->set('curUsrId',$u['id']);
					$this->set('curUsrSlug',$u[$this->t[$this->u['obj']]->slug_field]);
					$this->set('usrSlugFld',$this->t[$this->u['obj']]->slug_field);
					$this->login_trace();

					header("Location: ".aurl("/"));
					die();
				}
				/*				if($this->view_def())
									return true;
									return 'admin';*/
			}

			$this->setMsg("Wrong Username or Password.");
			$this->lookAt("login",true);
			return true;
		}

		function selUserView($u){

			$defv=false;
			if(!is_array($this->vs))
				return false;



			foreach($this->vs as $vn=>$v) {

                    $fnd = true;
                    if (isset($v['sel_on']['_def_view']) && $v['sel_on']['_def_view'] == true) {
                        $defv = $vn;
                    }
                    if (!isset($v['sel_on']) || !is_array($v['sel_on']))
                        continue;

                    foreach ($v['sel_on'] as $fn => $fv) {

                        if ((strpos($fn, '_regexp') === 0) && is_array($fv)) {

                            foreach ($fv as $rfn => $rfv) {
                                //var_dump($rfn,$rfv,$u[$rfn],"<hr>");
                                if (!isset($u[$rfn]) || !preg_match($rfv, $u[$rfn])) {
                                    $fnd = false;
                                    break;
                                }
                            }
                        } else if ((strpos($fn, '_cond') === 0)) {

                            foreach ($fv as $rfn => $rfv) {
                                if ($rfn !== $rfv) {
                                    $fnd = false;
                                    break;
                                }
                            }
                        } else if (!isset($u[$fn]) || $u[$fn] != $fv) {
                            $fnd = false;
                            break;
                        }

                    }


                    if ($fnd == true) {
                        $this->set("vname", $vn);
                        return true;
                    }


                if ($defv != false) {
                    $this->set("vname", $defv);
                    return true;
                }
//						die('ERR:'.$vn);

            }die;
            return false;
		}

		function deflogin()
        {
            if ($this->isLogged || $this->isAdmin)
                return false;

            if ($this->getConfig("admin_login") != "" && $this->getConfig("admin_pass") != "" && $this->getConfig("admin_login") == $_REQUEST['login'] && $this->getConfig("admin_pass") == $_REQUEST['pass']) {

                $this->set('isAdmin', true);
                $this->set('isLogged', true);
                $this->set('rfull', true);
                $this->set('access', array("all"));
                $this->set('superAdmin', true);
                $this->set('userId', 0);
                $this->set('userCreds', array("ulogin" => "Admin", 'LocalAdmin' => 'Admin', 'uname' => 'LocalAdmin'));

                return "admin";

            }

            if ($_REQUEST['login'] != false && $this->getConfig("locallogin_{$_REQUEST['login']}") != "" && $_REQUEST['pass'] != false && $this->getConfig("locallogin_{$_REQUEST['login']}") == $_REQUEST['pass']) {
                $this->set('isAdmin', true);
                $this->set('isLogged', true);
                $this->set('rfull', true);
                $this->set('access', array("all"));
                $this->set('superAdmin', true);
                $this->set('userId', 0);
                $this->set('userCreds', array("ulogin" => $_REQUEST['login'], 'LocalAdmin' => $_REQUEST['login'], 'uname' => $_REQUEST['login']));

                return "admin";

            }
            if ($this->getConfig("owner_login") != "" && $this->getConfig("owner_pass") != "" && $this->getConfig("owner_login") == $_REQUEST['login'] && $this->getConfig("owner_pass") == $_REQUEST['pass']) {
                $this->set('isLogged', true);
                header("Location: ../index.php");
                return true;

            }
            $this->db->query("select * from {$this->db_prefix}users where ulogin='{$_REQUEST['login']}'");
            $u = $this->db->next();
            if (is_array($u) && $u['ulogin'] == $_REQUEST['login'] && $u['upass'] == $_REQUEST['pass'] && $u['enabled'] = true) {
                $acc = array();
                $this->db->query("select a.aid as aid from{$this->db_prefix} users_access a left join {$this->db_prefix}sys_m2m m on 
						(m.mtbl='users' and m.stbl='users_access' and m.mid={$u['id']} and m.sid=a.id) where m.mid={$u['id']}");
                while ($ar = $this->db->next())
                    $acc[] = $ar['aid'];

                $this->set('access', $acc);
                $this->set('isAdmin', true);
                $this->set('rfull', true);
                $this->set('userId', $u['id']);
                $this->set('userCreds', $u);

                return "admin";

            }

            //		die($this->db->getLastError());
            return false;

		}


		function do_logout($fullOut=false)
		{
			if(!$this->isLogged  && !$this->isAdmin){
				return false;
			}

			if($this->isAdmin)
				$this->set('rfull',false);

			$ls=false;
			if(isset($_SESSION['logins_stack']) && is_array($_SESSION['logins_stack']) && count($_SESSION['logins_stack'])>0)
				$ls=$_SESSION['logins_stack'];

			$_SESSION=array();
			$this->isLogged=false;
			$this->isAdmin=false;
			if($fullOut)
				return false;
			if($ls!=false && is_array($ls))
				$_SESSION=array_pop($ls);

			header("Location: " . aurl("/"));
			die();

			//		return "login";
		}

		function do_default()
		{
			//		var_dump($_REQUEST);die();
			$this->lookAt("default");
		}
		function do____auth()
		{
			//		var_dump($_REQUEST);die();
			$this->lookAt("login",true);
		}


		/*******************************************************************************************************/
		/*ADMIN ACTIONS*/
		/*******************************************************************************************************/



		function do_sys_files_langs_update(){
			if(!$this->isAdmin)
				return false;

			sys_files::langUpdate($this->db,$this->db_prefix,$this->langs);
			header("Location: index.php");
			die();
		}

		function do_apass()
		{
			if(!$this->isAdmin)
				return false;

			if($_REQUEST['old']!="" && $_REQUEST['old']===$this->getConfig('admin_pass') && $_REQUEST['new']!="" && $_REQUEST['new2']==$_REQUEST['new'])
				$this->setConfig("admin_pass",$_REQUEST['new']);
			return false;
		}



		/*	function do_menuedit()
				{
				if(!$this->isAdmin)
				return false;

				if(isset($_REQUEST['cid']) && $_REQUEST['cid']!="")
				{
				$this->db->query("select m.*,p.prio as prio, p.parent as parent from {$this->db_prefix}menu m left join {$this->db_prefix}sys_prios p on (p.tid=m.id and p.tbl='menu') where m.id={$_REQUEST['cid']} or lid={$_REQUEST['cid']} order by lid desc");
				while($rr=$this->db->next())
				$this->show['row'][$rr['lang']]=$rr;

				$this->show['row']=array_merge($this->show['row'],$this->show['row']['fr']);
				$this->LookAtVar("menuedit",true,true);
				return true;

				}
				return false;

				} */


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


		function do_trim($param_arr=false){
			if(!$this->isAdmin)
				return false;

			sys_files::imgTrim($_REQUEST);

			if(isset($_REQUEST['on_redirect']) && $_REQUEST['on_redirect']!="" && $a[1]=="i")
				header("Location: index.php?{$_REQUEST['on_redirect']}$ar");
			else if(isset($_REQUEST['on_redirect']) && $_REQUEST['on_redirect']!="" && $a[1]=="u")
				header("Location: index.php?{$_REQUEST['on_redirect']}$ar");
			else if(isset($_REQUEST['redirect']) && $_REQUEST['redirect']!="")
				header("Location: index.php?{$_REQUEST['redirect']}");


			$ph=pathinfo($_REQUEST['fname']);
			$tfile=$ph['dirname']."/t".$ph['basename'];
			$_REQUEST['fname']=$_REQUEST['fname'];
			if(!is_file($tfile))
				$tfile=$_REQUEST['fname'];

			die("{iSel:'".($_REQUEST['ri']=="undefined"||$_REQUEST['ri']==false?"":$_REQUEST['ri'])."',tSel:'".($_REQUEST['rt']=="undefined"||$_REQUEST['rt']==false?"":$_REQUEST['rt'])."',file:'{$_REQUEST['fname']}?".time()."',tfile:'{$tfile}?".time()."'}");
		}

		function do_adb(){
			if(!$this->isAdmin && !$this->isLogged)
				return false;
			$pk=$this->adb($_REQUEST,true);

			//		die("OK:".$this->db->getLastError());			

			if($_REQUEST['ajax']==1)
				die("OK");

			/*echo "<hr>ERR?:".$this->db->getLastError()."<hr>";
				echo "<hr>LAST Q:".$this->db->getLastQuery()."<hr>";
				echo "</hr>";
				die();
			 */

			//	var_dump($rdt);die();
			//die('END');
			$popupr="";
			if(isset($_REQUEST['popup']) && $_REQUEST['popup']==1){
				$popupr="popup=1&";
				if(isset($_REQUEST['_ucdo']) && $_REQUEST['_ucdo']!=false && isset($_REQUEST['_ucdc']) && $_REQUEST['_ucdc']!=false && is_object($this->t[$_REQUEST['_ucdo']]))
					$popupr.="_ucdo={$_REQUEST['_ucdo']}&_ucdc={$_REQUEST['_ucdc']}&";
			}
			if(isset($_REQUEST['on_redirect']) && $_REQUEST['on_redirect']!="" && $a[1]=="i")
				header("Location: index.php?$popupr{$_REQUEST['on_redirect']}$pk");
			else if(isset($_REQUEST['on_redirect']) && $_REQUEST['on_redirect']!="" && $a[1]=="u")
				header("Location: index.php?$popupr{$_REQUEST['on_redirect']}$pk");
			else if(isset($_REQUEST['redirect']) && $_REQUEST['redirect']!="")
				header("Location: index.php?$popupr{$_REQUEST['redirect']}");
			else if(isset($_REQUEST['redirect_url']) && $_REQUEST['redirect_url']!="")
				header("Location: ".str_replace("{id}",$pk,$_REQUEST['redirect_url']));
			else
				$this->LookAtVar($a[2],true,true);
			return true;
		}



		function cadb($act,$o,$data,$sync=true){
			$on=false;
			if(is_object($o))
				$on=$o->oname;
			else
				$on=$o;
			$req=array("act_{$act}_{$on}"=>'act','data'=>$data);
			return $this->adb($req,$sync);
		}

		function adb($ireq,$_sync=false){
          //  echo"<pre>";print_r($_REQUEST);die;
			if(!$this->isAdmin && !$this->isLogged)
				return false;

			if(isset($ireq['multi_row']) && is_array($ireq['multi_row']))
				$r_loop=$ireq['multi_row'];
			else
				$r_loop=array(0=>$ireq);

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

				$uObj=false;
				$o=false;
				if(isset($a[2]) && is_object($this->t[$a[2]])){
					$o=$this->t[$a[2]];
					$a[2]=$o->tbl;
					if(isset($this->u['obj']) && $o->oname==$this->u['obj'])
						$uObj=true;

					$acc=$o->gO("access_id");

					$cV=$o->gOAA("cV");

					if ($o->gO('noViews') == false && !(isset($cV['acts']) && in_array($a[1], $cV['acts']))) {
						if(isset($ireq['fast_edit']) && $ireq['fast_edit']!=false){
							if(!(isset($cV['fast_edit']) && is_array($cV['fast_edit']) && (isset($cV['fast_edit'][$ireq['fast_edit']]) || in_array($ireq['fast_edit'],$cV['fast_edit'])))){
								$this->setMsg("You haven't permissions for this action");
								continue;
							}

						}else{
							if($this->req_nonce_ok==false){
								if(isset($_REQUEST['form_nonce']))
									$this->setMsg('Form timeout, please fill again and resubmit');
								continue;
							}
						}
					}

				}else
					$acc="all";


				if(!(isset($this->access) && is_array($this->access) && (in_array("all",$this->access) || (is_object($o) && in_array($o->oname,$this->access)) || in_array($a[2],$this->access) || in_array($acc,$this->access))))
					continue;

				if(count($a)==3){
					if(method_exists($this,"adb_before_all"))
						$this->adb_before_all();

					if($pk==0)	{
						if($o)
							$o->preAdb($a[1],$creq);
					}else{
						if($o)
							$o->preAdbClear($creq);
					}

					if(method_exists($this,"adb_before_adb"))
						$this->adb_before_adb($a,$creq,$o);

					//----------ADB
					$rdt=array();

					$creq['data']=$this->adb_defs($creq['data'],$a,$o);

					$ar=$this->db->act($a[1],$a[2],$creq['data'],$msg,$rdt,$o);
					global $sys_adb_debug_msg;
					if ($sys_adb_debug_msg == true && $this->db->getLastError() != false) {
						$this->setMsg("ADB ERROR: " . $this->db->getLastError() . "<hr> QUERY:" . $this->db->getLastQuery());
						//echo "MSG SET<hr>";
					}

					global $sys_adb_debug;
					if($sys_adb_debug){
						echo "<hr>ADB:";
						//var_dump($this->def_lang,$ar,$a[1],$a[2],$creq['data'],"<hr>",$r_loop,"<hr>RDT:",$rdt,$creq['data']['r.lang'],$creq['data']['r.lid']);
						echo "<hr>ERR?:".$this->db->getLastError()."<hr>";
						echo "<hr>LAST Q:".$this->db->getLastQuery()."<hr>";
						echo "</hr>";
						die();
					}
					//	die();
					//----------ADB
					//				var_dump($msg);
					if($msg!=false){
						$this->setMsg("Operation error");
						return false;
					}
					if($pk==0)	{
						if($o->slug_field!=false){
							foreach($rdt as $rdtk=>$r){
								if(!(isset($r[$o->slug_field]) && $r[$o->slug_field]!=false)){
									$nslg = $this->dbo_makeslug($o->oname, $r['id']);
									$this->db->query("update {$this->db_prefix}{$o->tbl} set {$o->slug_field}='".$nslg."' where id={$r['id']}","__dbo_{$o->oname}_def_slug");
									$rdt[$rdtk][$o->slug_field]=$nslg;
								}

							}
						}

						//$o->setOpts(array("rdt"=>$rdt[0]));

						if($o){
							$o->postAdb($a[1],$ar,$rdt,$creq);
						}

						if(method_exists($this,"adb_after_adb"))
							$this->adb_after_adb($a,$creq,$o,$ar,$rdt,true);
						if($uObj && isset($this->db->adb_ndt[$this->curUsr['id']])){
							$this->set("curUsr",array_merge_recursive_new($this->curUsr,$this->db->adb_ndt[$this->curUsr['id']]));
						}
					}				
					if(method_exists($this,"adb_after_adb"))
						$this->adb_after_adb($a,$creq,$o,$ar,$rdt,false);

					if($pk==0)
						$pk=$ar;


					/*				if($o){
										if($this->adb_defs($ar,$a,$o,$rdt)===-1)
										return false;
										}*/
				}
			}

			if ($_sync == true && count($this->db->adb_tbl_hist) > 0 /*&& is_object($o) && $o->gO('sf_table')*/) {
				$aqu=array();
				foreach($this->db->adb_tbl_hist as $tk=>$taa){
					foreach($taa as $ta=>$td){
						if (is_object($this->t[$tk]) && $this->t[$tk]->gO('sf_table')) {
							$apiReqA=array();
							$apiFld='id';
							$Aact='manage';
							$tda=&$this->db->adb_ndt;
							if($ta=='d'){
								$tda=&$this->db->adb_rdt;
								$apiFld=$o->slug_field;
								$Aact='del';
							}
							foreach($td as $cR){
								$apiReqA[]=$tda[$cR['id']][$apiFld];
							}
							$aqu[]=array('obj_name'=>$tk,'act'=>$Aact,'ids'=>$apiReqA);
							//run_async("async_app2sf_api",array('apireq'=>array('obj_name'=>$tk,'act'=>$Aact,'ids'=>$apiReqA)),$this->db);
						}
					}
				}
				if(count($aqu)>0){
					run_async("async_app2sf_api",array('api_queue'=>$aqu),$this->db);
				}
			}

			if(method_exists($this,"adb_after_all"))
				$this->adb_after_all();
			return $pk;
		}

		function adb_defs($data, $a, $o,$aft=false){
			if(!is_object($o))
				return $data;
			if($a[1]=="d")
				return $data;
			if(!is_array($data))
				return $data;

			$uObj=false;
			if(isset($this->u['obj']) && $o->oname==$this->u['obj'])
				$uObj=true;

			$cV=$o->gOAA('cV');
			$fn2c=array();
			$r=array();
			if($a[1]=='u'){
				$fn2c['id']="ret.w.id";
				$r['id']=$data['ret.w.id'];
			}
			foreach($data as $k=>$v){
				if(strpos($k,"c.")!==false || strpos($k,"r.")!==false){
					$tk=preg_replace("#c\.|r\.#mi","",$k);
					$fn2c[$tk]=$k;
					$r[$tk]=$v;
				}
			}
			$defN="defvals";
			$fDefN="defvals_force";
			if($uObj==true && $r['id']==$this->curUsr['id']){
				$defN="self_".$defN;
				$fDefN="self_".$fDefN;
			}

			if(isset($cV[$defN]) && is_array($cV[$defN])){
				foreach($cV[$defN] as $fn=>$v){
					if(!isset($r[$fn]) || $r[$fn]==false)
						$data=$this->adb_defs_proc($fn,$v,$data,$o,$r);
				}
			}

			if(isset($cV[$fDefN]) && is_array($cV[$fDefN])){
				foreach($cV[$fDefN] as $fn=>$v){
					$data=$this->adb_defs_proc($fn,$v,$data,$o,$r);
				}
			}
			if (is_array($o->rels) && $o->gO('sf_table')) {
				foreach($o->rels as $rfn=>$rv){
					if(isset($rv['obj']) && is_object($this->t[$rv['obj']])){
						$ro=$this->t[$rv['obj']];
						if ($ro->gO('sf_table') && isset($r[$rfn]) && $r[$rfn] != false) {
							$rrow=$this->db->getRow("select id,SF_Id, {$ro->slug_field} from {$this->db_prefix}{$ro->tbl} where id={$r[$rfn]}");
							if(is_array($rrow)){
								$data["c.{$rfn}_slug"]=$rrow[$ro->slug_field];
								$data["c.{$rfn}_id"]=$rrow['SF_Id'];
							}
						}
					}

				}
			}
			/*			if($o->slug_field!=false && !(isset($r[$o->slug_field]) && $r[$o->slug_field]!=false))
							$data["c.".$o->slug_field]=$this->adbdef_makeslug($o->oname,$r['id']);
			 */

			/*			if(count($updArr)>0)
							if(!$this->db->query($q="update {$a[2]} set ".implode(",",$updArr)." where id={$id} ","adb_defs_upd")){
							$this->showMsg("Operation error...");
							var_dump("LAST ERR",$this->db->getLastError());
							$this->db->query("delete from {$a[2]} where id={$id}");
							var_dump("DEL ERR",$this->db->getLastError());
							return -1;
							}
							var_dump("UPD Q:",$q);
			 */	

			return $data;
		}

		function adb_defs_proc($fn,$v,$data,$o,&$r){
			if(strpos($v,"_f_")===0 && method_exists($this,"adbdef_".str_replace("_f_","",$v))){
				$data["c.".$fn]=$this->{"adbdef_".str_replace("_f_","",$v)}($fn,$o->oname,$r);

			}else if(strpos($v,"_owner_")===0 && $v=='_owner_slug' ){
				if($this->curUsrSlug!=false)
					$data["c.".$fn]=$this->curUsrSlug;
			}
			else if(strpos($v,"_owner_")===0){
				if(isset($this->curUsr[str_replace("_owner_","",$v)]))
					$data["c.".$fn]=$this->curUsr[str_replace("_owner_","",$v)];
			}
			else if(strpos($v,"_p_")===0){
				$ttt=str_replace("_p_","",$v);
				$pto=$o->gO("gui_slaves_parent_obj");
				if($pto && is_object($this->t[$pto])){
					$Trdt=$this->t[$pto]->gO("rdt");
					if(is_array($Trdt)){
						if($ttt=="_slug")
							$data["c.".$fn]=$this->db->escape($Trdt[$this->t[$pto]->slug_field]);
						else if(isset($Trdt[$ttt]))
							$data["c.".$fn]=$this->db->escape($Trdt[$ttt]);
					}
				}
			}
			else if($v=='__now')
				$data["c.".$fn]=date("Y-m-d H:i:s");
			else if($v!=false && in_array($v,$o->flds)){
				$data["c.".$fn]=$r[$v];
			}else 
				$data["c.".$fn]=$v;
			if($r[$fn]!=$data["c.".$fn])
				$r[$fn]=$data["c.".$fn];
			return $data;	
		}

		function adbdef_passgen($fn,$oname,$r){
			$pass="";
			$l=mt_rand(5,8);
			for($i=0;$i<$l;$i++){
				$pass.=mt_rand(0,9);
			}
			return $pass;
		}

		function dbo_makeslug($oname, $numBase, $defPref = "APP")
		{
			$sp=$defPref;
			if(is_object($this->t[$oname]) && isset($this->t[$oname]->slug_prefix) && $this->t[$oname]->slug_prefix!=false)
				$sp=$this->t[$oname]->slug_prefix;
			return "{$sp}{$numBase}";
		}

		function dbo($obj){
			if(!$this->isAdmin)
				return false;
			if(!is_object($this->t[$obj]))
				return false;
			$o=$this->t[$obj];
			$this->cdbo=$o;
			$cV=$o->gOAA("cV");
			$acc=$o->gO("access_id");
			if(!(isset($this->access) && is_array($this->access) && (in_array("all",$this->access) || in_array($obj,$this->access) || in_array($acc,$this->access))))
				return false;

			if(isset($_REQUEST['s'])){
				$aj=false;
				if(isset($_REQUEST['ajax']))
					$aj=true;
				$o->showAdmin($_REQUEST['s'],array("ajax"=>$aj));	
				return true;
			}


			if(isset($_REQUEST['sa'])){

				if(isset($_REQUEST['rid']) && $_REQUEST['rid']>0){
					if($templ==false)
						$templ=$copts['editTpl'];
					$o->setCurrent($_REQUEST['rid']);
				}
				$ttp="dbo_{$_REQUEST['sa']}_{$o->copts['adminOnlineEdit']}";
				if(!is_file($this->TEMPL."/inc/$ttp.inc.php"))
					$ttp="dbo_{$_REQUEST['sa']}";

				echo $o->doInc($ttp);	
				return true;
			}

			if(isset($_REQUEST['f'])){
				if(strpos($_REQUEST['f'],"sys_files")===0){
					$o->sys_files->manage(str_replace("sys_files_","",$_REQUEST['f']),$_REQUEST['rid']);
					return true;
				}else if(strpos($_REQUEST['f'],"sys_links_")===0){
					$o->sys_links->drawAdminCtrl(str_replace("sys_links_","",$_REQUEST['f']),$_REQUEST['rid']);
					return true;
				}else if(strpos($_REQUEST['f'],"sys_m2m_")===0){
					$o->sys_m2m->drawAdminCtrl(str_replace("sys_m2m_","",$_REQUEST['f']),$_REQUEST['rid']);
					return true;
				}else if(strpos($_REQUEST['f'],"gui_slaves_")===0){
					$o->gui['slaves']->drawAdminCtrl(str_replace("gui_slaves_","",$_REQUEST['f']),$_REQUEST['rid']);
					return true;
				}else if($_REQUEST['f']=="ajaxSel"){
					if(isset($_REQUEST['_ucdo']) && $_REQUEST['_ucdo']!=false && isset($_REQUEST['_ucdc']) && $_REQUEST['_ucdc']!=false && is_object($this->t[$_REQUEST['_ucdo']]))
						die($this->t[$_REQUEST['_ucdo']]->dC($_REQUEST['_ucdc']));
					else
						die($o->sel());
				}
			}

			$qk=preg_grep("#q_.*#mi",array_keys($_REQUEST));
			$qw="";
			foreach($qk as $v)
			{
				$t=explode("_",$v,3);
				$op="and";
				if($qw=="")
					$op="";
				else if (count($t)==3){
					$op=$t[1];
					$t[1]=$t[2];
				}
				$qw.="$op {$t[1]}={$_REQUEST[$v]}";
			}

			if ($this->v == false) {
				$o->showAdmin(false, array("queryWhere" => array($qw)));
				return true;
			}

			//------------------------------------
			/*		if(!isset($this->v['objs'][$obj]['list']) || !is_array($this->v['objs'][$obj]['list'])){
						return false;
						}*/
//			var_dump($obj,$o->gO("cV"));die();
			if($cV==false)
				return false;
			if(is_array($cV['tabs']) && count($cV['tabs'])>0){
				$_tabs=$cV['tabs'];
				if(isset($_REQUEST['slug_arr'][1]) && is_array($_tabs[$_REQUEST['slug_arr'][1]])){
					$ctab=$_tabs[$_REQUEST['slug_arr'][1]];
					$o->setOpts(array('_cur_tab'=>$_REQUEST['slug_arr'][1]));
					if(is_array($ctab['tab_opts'])){
						if(isset($ctab['_replace']) && $ctab['_replace']==true){
							$cV=array_merge($cV,$ctab['tab_opts']);
							$o->setOptsR(array('cV'=>$cV));
						}else
							$o->setOpts(array('cV'=>$ctab['tab_opts']));
					}
					$cV=$this->gO('cV');
					if(is_array($cV['list'])){
						$cV['list']['_ajaxBaseUrl']='slug';
						$o->setOpts(array('cV'=>$cV));
					}

					$ri=count($_REQUEST['slug_arr']);
					unset($_REQUEST['slug_arr'][1]);
					for($riS=1;$riS<$ri;$riS++){
						if(isset($_REQUEST['slug_arr'][$riS+1])){
							$_REQUEST['slug_arr'][$riS]=$_REQUEST['slug_arr'][$riS+1];
							unset($_REQUEST['slug_arr'][$riS+1]);
						}
					}
					unset($_REQUEST['rid']);
					if(isset($_REQUEST['slug_arr'][1]))
						$_REQUEST['rid']=$_REQUEST['slug_arr'][1];
				}
				$cV=$o->gO('cV');
			}
			/*		if(is_array($cV['obj_opts']))
						$o->setOpts($cV['obj_opts']);
						$cV=$o->gO('cV');
			 */
			if (isset($_REQUEST['rid']) && strtolower($_REQUEST['rid']) == "new") {
				//			unset($_REQUEST['rid']);
				if (is_array($cV['acts']) && in_array("i", $cV['acts'])) {
					$etpl=($o->gO('editTplC')?$o->gO('editTplC'):"custom_view_edit");
					$o->showAdmin($etpl);
					return true;
				}
				return false;
			}

			$o->setOpts(array('listTpl' => 'custom_view_ajaxlist', 'editTpl' => 'custom_view_edit'));
			if(isset($_REQUEST['rid']) && !isset($_REQUEST['slug_arr'][2]) && $_REQUEST['slug_arr'][2]!='e'){
				$etpl=($o->gO('roTplC')?$o->gO('roTplC'):"custom_view_readonly");
				$o->setOpts(array("editTpl"=>$etpl));
				//			$o->setOpts(array("editTpl"=>"custom_view_readonly"));
			}

			if(!(isset($_REQUEST['rid']) && isset($_REQUEST['slug_arr'][2]) && $_REQUEST['slug_arr'][2]=='e' && is_array($cV['acts']) && in_array('u',$cV['acts']))){
				$etpl=($o->gO('roTplC')?$o->gO('roTplC'):"custom_view_readonly");
				$o->setOpts(array("editTpl"=>$etpl));
				//			$o->setOpts(array("editTpl"=>"custom_view_readonly"));
			}

			$listTpl=($o->gO('listTplC')?$o->gO('listTplC'):"custom_view_ajaxlist");
			//		$listTpl="custom_view_ajaxlist";
			$ownHdr=false;
			if(isset($_REQUEST['ajax']) && isset($_REQUEST['dataTable']) && $_REQUEST['ajax']==true && $_REQUEST['dataTable']==true){
				$listTpl=($o->gO('listTplC')?$o->gO('listTplC_ajax'):"custom_view_ajaxrequest");
				//			$listTpl="custom_view_ajaxrequest";
				$ownHdr=true;
				if(isset($_REQUEST['rid']))
					die('{"error":"RID set in list"}');
			}
			$o->showAdmin(false,array("queryWhere"=>array($qw),"listTpl"=>$listTpl,"ownHeader"=>$ownHdr));


			return true;
		}

		function setNonce(){
			$n=mt_rand(0,1000).time().mt_rand(1000,9000);
			$_SESSION['cur_nonce']=$n;
			return $n;
		}

		function do_profile(){
			if(!$this->isLogged)
				return false;

			if(isset($this->u) && is_array($this->u) && isset($this->u['obj'])){
				if(isset($this->t[$this->u['obj']])){
					if(!isset($this->v['objs'][$this->u['obj']]['self']))
						return false;
					$this->setNonce();
					$_REQUEST['rid']=$this->curUsr['id'];
					$this->v['objs'][$this->u['obj']]['edit']=$this->v['objs'][$this->u['obj']]['self'];

					$this->t[$this->u['obj']]->showAdmin("custom_view_edit",array("cV"=>$this->v['objs'][$this->u['obj']]));
					return true;
				}
			}

			return false;

		}



		function emptyDir($rd)
		{
			$cdrc=scandir($rd);
			foreach($cdrc as $cdf)
			{
				if(is_file($rd.$cdf))			
					unlink($rd.$cdf);
			}

		}


		function do_banners()
		{
			if(!$this->isAdmin)
				return false;

			$bns=unserialize($this->getConfig("banners"));
			foreach($_REQUEST['banners'] as $k=>$va){
				foreach($va as $kn=>$v)
					$bns[$k][$kn]['link']=$v['link'];
			}

			foreach($_FILES['banner']['name'] as $bp=>$ba){
				foreach($ba as $bn=>$v)
				{

					if(isset($_FILES['banner']['tmp_name'][$bp][$bn]) && is_file($_FILES['banner']['tmp_name'][$bp][$bn]))
					{
						$pf=pathinfo($v);
						$cn="banners/{$bp}_{$bn}.{$pf['extension']}";
						copy($_FILES['banner']['tmp_name'][$bp][$bn],"../".$cn);
						if(is_file("../".$cn)){
							$bns[$bp][$bn]['src']=$cn;
							$bns[$bp][$bn]['c']=$pf['extension'];
						}
						unlink($_FILES['banner']['tmp_name'][$bp][$bn]);
					}

				}
			}
			$this->setConfig("banners",serialize($bns));
			if(isset($_REQUEST['redirect_url']) && $_REQUEST['redirect_url']!=""){
				header("Location: {$_REQUEST['redirect_url']}");
				return true;
			}
			else
				return false;


		}

		function drawABanner($pos,$num)
		{
			$cn=unserialize($this->getConfig("banners"));


			if(!is_array($cn) || !isset($cn[$pos][$num]) || !is_file($this->root.$cn[$pos][$num]['src']))
				return false;

			if($cn[$pos][$num]['c']=="swf"){?>
				<object >
					<param class='<?php echo "flash_banner {$pos}_{$num}_banner {$pos}_banner banner"?>' name="movie" value="<?php echo $this->root.$cn[$pos][$num]['src'];?>">
					<embed class='<?php echo "flash_banner {$pos}_{$num}_banner {$pos}_banner banner"?>' src="<?php echo $this->root.$cn[$pos][$num]['src'];?>" >
					</embed>
					</object>

					<?php }else{?>
						<img class='<?php echo "img_banner {$pos}_{$num}_banner {$pos}_banner banner"?>' src="<?php echo $this->root.$cn[$pos][$num]['src'];?>"/>
							<?php 

					}

		}

		function drawBanner($pos,$num)
		{
			$cn=unserialize($this->getConfig("banners"));


			if(!is_array($cn) || !isset($cn[$pos][$num]) || !is_file($this->root.$cn[$pos][$num]['src']))
				return false;
			if($cn[$pos][$num]['link']!="")
				echo "<a class='{$pos}_{$num}_blink {$pos}_blink blink' href='{$cn[$pos][$num]['link']}'>";
			if($cn[$pos][$num]['c']=="swf"){?>
				<object >
					<param class='<?php echo "flash_banner {$pos}_{$num}_banner {$pos}_banner banner"?>' name="movie" value="<?php echo "<?php echo \$rpath;?>".$cn[$pos][$num]['src'];?>">
					<embed class='<?php echo "flash_banner {$pos}_{$num}_banner {$pos}_banner banner"?>' src="<?php echo "<?php echo \$rpath;?>".$cn[$pos][$num]['src'];?>" >
					</embed>
					</object>

					<?php }else{?>
						<img class='<?php echo "img_banner {$pos}_{$num}_banner {$pos}_banner banner"?>' src="<?php echo "<?php echo \$rpath;?>".$cn[$pos][$num]['src'];?>"/>
							<?php	}
			if($cn[$pos][$num]['link']!="")
				echo "</a>";
		}

		function do_api(){
			$apireq=file_get_contents('php://input');
			$json=json_decode($apireq,true);

			if(isset($json['act']) && $json['act']=='dirty_retry'){
				$rts=$this->getConfig("retry_ts");
/*				if($rts==false && $rts<time()-150){
					$this->setConfig("retry_ts",time(),true);*/
					run_async("async_sf2app_api",array("retry"=>true),$this->db);
/*				}else
					die("API OK, RETRY TO SOON (".($rts-time()).")");*/
			}else
				run_async("async_sf2app_api",array("apireq"=>$apireq),$this->db);

			die('API OK');

		}

		function async_sf2app_api(){
			ob_start();
			ini_set("ignore_user_abort",true);

			global $sys_app_name;
			$app="def_app";
			if(isset($sys_app_name))
				$app=$sys_app_name;

			require_once("sf_api.php");
			$curLogF="/tmp/sf2app_api_log_{$app}";
			if(isset($_REQUEST['retry']) && $_REQUEST['retry']==true){

				$ret = sf2app_dirty_sync($app);
				$curLogF="/tmp/sf2app_retry_log_{$app}";
				if (method_exists($this, "post_sf2app_proc")) {
					echo "\nPOST PROC CALLED:\n------\n";
					$this->post_sf2app_proc($ret, true);
				}
			} else {
				$ret = sf2app($_REQUEST['apireq']);
				if (method_exists($this, "post_sf2app_proc")) {
					echo "\nPOST PROC CALLED:\n------\n";
					$this->post_sf2app_proc($ret, false);
				}
			}

			$r=ob_get_contents();
			ob_end_clean();
			$f=fopen($curLogF,"w");
			fwrite($f,$r);
			fclose($f);

			die('OK');

		}

		
		function do_msg_api(){
			$apireq=file_get_contents('php://input');
			$json=json_decode($apireq,true);

			if(isset($json['act']) && $json['act']=='dirty_retry'){
				$rts=$this->getConfig("retry_ts");
/*				if($rts==false && $rts<time()-150){
					$this->setConfig("retry_ts",time(),true);*/
					run_async("async_sf2app_msg_api",array("retry"=>true),$this->db);
/*				}else
					die("API OK, RETRY TO SOON (".($rts-time()).")");*/
			}else
				run_async("async_sf2app_msg_api",array("apireq"=>$apireq),$this->db);

			die('API OK');

		}
		function async_sf2app_msg_api(){
			ob_start();
			ini_set("ignore_user_abort",true);

			global $sys_app_name;
			$app="def_app";
			if(isset($sys_app_name))
				$app=$sys_app_name;

			require_once("msg_sf_api.php");
			$curLogF="/tmp/msg_sf2app_api_log_{$app}";
			if(isset($_REQUEST['retry']) && $_REQUEST['retry']==true){

				$ret = sf2app_dirty_sync($app);
				$curLogF="/tmp/msg_sf2app_retry_log_{$app}";
				if (method_exists($this, "post_sf2app_proc")) {
					echo "\nPOST PROC CALLED:\n------\n";
					$this->post_sf2app_proc($ret, true);
				}
			} else {
				$ret = sf2app($_REQUEST['apireq']);
				if (method_exists($this, "post_sf2app_proc")) {
					echo "\nPOST PROC CALLED:\n------\n";
					$this->post_sf2app_proc($ret, false);
				}
			}

			$r=ob_get_contents();
			ob_end_clean();
			$f=fopen($curLogF,"w");
			fwrite($f,"\n==========================================================\n");
			fwrite($f,"NEW API REQ: ".date("Y-m-d H:i:s"));
			fwrite($f,"\n==========================================================\n");
			fwrite($f,$r);
			fclose($f);

			die('OK');

		}

		
		
		function async_sf2app_set_rels(){
			ob_start();
			$app="def_app";

			global $sys_app_name;

			if(isset($sys_app_name))
				$app=$sys_app_name;

			$curLogF="/tmp/sf2app_set_rels_{$app}";
			var_dump('START ASYNC RELS');
			ini_set("ignore_user_abort",true);	
			if(isset($_REQUEST['dbo']) && isset($_REQUEST['tbl']) && isset($_REQUEST['tids'])){
				require_once("sf_api.php");
				var_dump($_REQUEST);
				sf2app_set_rels($_REQUEST['dbo'],false,$_REQUEST['tbl'],$_REQUEST['tids']);
			}else{
				echo "ASYNC - Missing args";
			}
			$r=ob_get_contents();
			ob_end_clean();
			$f=fopen($curLogF,"w");
			fwrite($f,$r);
			fclose($f);
			die('OK');
		}

		function async_app2sf_set_rels(){
			ob_start();
			$app="def_app";

			global $sys_app_name;

			if(isset($sys_app_name))
				$app=$sys_app_name;

			$curLogF="/tmp/app2sf_set_rels_{$app}";

			ini_set("ignore_user_abort",true);
			if(isset($_REQUEST['succ']) && isset($_REQUEST['slug']) && isset($_REQUEST['tbl']) && isset($_REQUEST['deps'])){
				require_once("sf_api.php");
				app2sf_set_rels($_REQUEST['tbl'],$_REQUEST['slug'],$_REQUEST['deps'],$_REQUEST['succ']);
			}else{
				echo "ASYNC - Missing args";
			}
			$r=ob_get_contents();
			ob_end_clean();
			$f=fopen($curLogF,"w");
			fwrite($f,$r);
			fclose($f);
			die('OK');
		}

		function async_app2sf_api(){
			ob_start();
			ini_set("ignore_user_abort",true);

			require_once("sf_api.php");

			global $sys_app_name;
			$app="def_app";
			if(isset($sys_app_name))
				$app=$sys_app_name;

			$curLogF="/tmp/app2sf_api_log_{$app}";
			if(isset($_REQUEST['retry']) && $_REQUEST['retry']==true){
				$ret = app2sf_retry_dirty_log();
				$curLogF="/tmp/app2sf_retry_log_{$app}";
				if (method_exists($this, "post_app2sf_proc")) {
					echo "\nPOST PROC CALLED:\n------\n";
					$this->post_app2sf_proc($ret, true);
				}
			}else{
				if(isset($_REQUEST['api_queue']) && is_array($_REQUEST['api_queue'])){
					foreach ($_REQUEST['api_queue'] as $apireq) {
						$tra = app2sf($apireq);
						$ret = array_merge_recursive_new($ret, $tra);
					}
					if (method_exists($this, "post_app2sf_proc")) {
						echo "\nPOST PROC CALLED:\n------\n";
						$this->post_app2sf_proc($ret, false);
					}
				} else if (isset($_REQUEST['apireq']) && is_array($_REQUEST['apireq'])) {
					$ret = app2sf($_REQUEST['apireq']);
					if (method_exists($this, "post_app2sf_proc")) {
						echo "\nPOST PROC CALLED:\n------\n";
						$this->post_app2sf_proc($ret, false);
					}
				}

				run_async("async_app2sf_api",array("retry"=>true),$this->db);
			}

			$r=ob_get_contents();
			ob_end_clean();
			$f=fopen($curLogF,"w");
			fwrite($f,$r);
			fclose($f);

			die('OK');
		}


		function do_async(){
			ini_set('ignore_user_abort',1);
			if(isset($_REQUEST['v2']) && $_REQUEST['v2']==true){
				$artp=unserialize($this->getConfig("artp_data"));
				if(!is_array($artp))
					$artp=array();
				$artpPass=$artp['pass'][$_REQUEST['artpn']];
				$ts=$artp['ts'][$_REQUEST['artpn']];
				unset($artp['pass'][$_REQUEST['artpn']],$artp['ts'][$_REQUEST['artpn']]);
				$this->setConfig("artp_data",serialize($artp));

				if(isset($_REQUEST['artpn']) && isset($_REQUEST['artp']) && $_REQUEST['artp']!=false && $artpPass==$_REQUEST['artp']){

					if(!isset($_REQUEST['func']) || $_REQUEST['func']==false || !method_exists($this,$_REQUEST['func']))
						die('Function absent or not specified');
					$func=$_REQUEST['func'];
					global $sys_app_name;
					$adir="async_apps_req_{$sys_app_name}";
					$rf="/tmp/{$adir}/req_{$_REQUEST['artpn']}_{$_REQUEST['artp']}.{$ts}";
					if(is_file($rf)){
						$_REQUEST=unserialize(file_get_contents($rf));
						unlink($rf);
					}else
						$_REQUEST=array();
					$cd=scandir("/tmp/$adir/");
					$cts=time();
					foreach($cd as $fn){
						if($fn=='..' || $fn=='.')	
							continue;
						$fnt=explode(".",$fn);
						if(!isset($fnt[1]) || $fnt[1]<$cts-600)
							unlink("/tmp/$adir/$fn");
					}
					$this->{$func}();
					die('OK');
				}else{
					die('do_async - Access denied');
				}

			}
			return false;
		}

		function do_oneclicklogin()
		{
			$this->do_logout(true);
			$this->trace_login = false;
			$this->do_login();
		}

		function draw_tbl_buttons($linkBtns, $tblCode, $oname)
		{
			$reta = array('v'=>"",'h'=>'');
			if (is_array($linkBtns) && count($linkBtns) > 0) {
				ob_start();

				foreach ($linkBtns as $k => $urla) {
					$trg=false;
					$name = $k;
					if (is_array($urla)) {
						if (isset($urla['_url']))
							$url = $urla['_url'];
						if (isset($urla['_t']))
							$name = $urla['_t'];
						if(isset($urla['_trg']))
							$trg="target='{$urla['_trg']}'";
					} else {
						$url = $urla;
					}
					if(strpos($k,"_msgbtn")===0 && isset($urla['_t']) && isset($urla['_mT'])){
						$t2=$urla;
						unset($urla['_t']);
						unset($urla['_mT']);
						$ooo=array();
						foreach($urla as $uk=>$uv)
							$ooo[$uk]=$uv;
						echo $this->send_msg_btn($t2['_t'],"",$oname,false,false,$t2['_mT'],$ooo,$tblCode);
					}

					else if(isset($urla['_drawFunc']) && $urla['_drawFunc']!=false && method_exists($this,"draw_btn_{$urla['_drawFunc']}")){
						echo $this->{"draw_btn_{$urla['_drawFunc']}"}($k,$urla,$oname,$tblCode);
					}
					
else if((isset($urla['_idc_post']) && $urla['_idc_post']!=false) || (isset($urla['_srch_post']) && $urla['_srch_post']!=false)){
	?>

	<form <?php echo $trg; ?> action = "<?php echo $url ?>" method = 'POST' onsubmit = "<?php
	if(isset($urla['_confirm']) && $urla['_confirm']!=false){
		echo "if(!confirm('Are you sure?')){return false;}";
	}
	?>var _srch=jQuery('#dbo_datatable_<?php echo $oname; ?>_<?php echo $tblCode; ?>_filter input').val();var ida=jQuery('.idc_tbl_<?php echo $tblCode; ?>_rows:checked');<?php 
					if ($urla['_idc_post'] === 2) {
							echo "if(ida.length==0){alert('Select at least one row.');return false;}";
					} 
					if (isset($urla['_srch_post']) && $urla['_srch_post']==2) {
							echo "if(_srch=='' || _srch==null || _srch==false){alert('Enter value in search field.');return false;}";
					} 
					?>jQuery(this).find('._idc_srch').val(_srch);
					window._adv_<?php echo $tblCode; ?>='';ida.each(function(n){if(window._adv_<?php echo $tblCode; ?>!=''){window._adv_<?php echo $tblCode; ?>+=','}window._adv_<?php echo $tblCode; ?>+=this.value;});jQuery(this).find('._idc_checked').val(window._adv_<?php echo $tblCode; ?>)" >
						<input type = 'hidden' class='_idc_srch' name = '_idc_srch' >
						<input type = 'hidden' class='_idc_checked' name = '_idc_checked' >
						<input type = 'hidden' class='_idc_referer' name = '_idc_referer' value = '<?php echo $oname; ?>' >
						<input class='btn <?php echo (isset($urla['_class'])?$urla['_class']:"")?>' name='_idc_btn' type = 'submit' value = '<?php echo $name; ?>' >
						</form >


<?php } else {

							?>
								<a 
<?php	if(isset($urla['_confirm']) && $urla['_confirm']!=false){
		echo "onclick=\"if(!confirm('Are you sure?')){return false;}\"";
	}
 echo $trg; ?> href="<?php echo $url; ?>"
								class='btn clink_btn dbo_bottom <?php echo (isset($urla['_class'])?$urla['_class']:"")?>'><?php echo $name ?></a>
								<?php
						}
					$ret = ob_get_contents();
					ob_clean();
					if(isset($urla['_onTop']) && $urla['_onTop']==true)
						$reta['h'].="$ret";
					else
						$reta['v'].="$ret<br>";
				}
				ob_end_clean();
			}
			return $reta;
		}
		function draw_detail_buttons($linkBtns,$o)
		{
			if (is_array($linkBtns) && count($linkBtns) > 0 && is_object($o)) {
				ob_start();

				foreach ($linkBtns as $k => $urla) {
					$trg=false;
					$name = $k;
					if (is_array($urla)) {
						if (isset($urla['_url']))
							$url = $urla['_url'];
						if (isset($urla['_t']))
							$name = $urla['_t'];
						if(isset($urla['_trg']))
							$trg="target='{$urla['_trg']}'";
					} else {
						$url = $urla;
					}
					if(preg_match_all("#{([^}]+)}#im",$url,$pats)){
						if(count($pats)>1){
							$pats=$pats[1];
							foreach($pats as $p){
								if(isset($o->cD[$p]))
									$url=str_replace("{".$p."}",$o->cD[$p],$url);
							}
						}
					}
					if(strpos($k,"_msgbtn")===0 && isset($urla['_t']) && isset($urla['_mT'])){
						$t2=$urla;
						unset($urla['_t']);
						unset($urla['_mT']);
						$ooo=array();
						foreach($urla as $uk=>$uv)
							$ooo[$uk]=$uv;
						echo $this->send_msg_btn($t2['_t'],"",$o->oname,'i',$o->cD['SF_Id'],$t2['_mT'],$ooo);
					} else {
						$id="lbid".time().mt_rand(99,9999);
						?>
							<a id="<?php echo $id?>" <?php echo $trg; ?> href="<?php echo $url; ?>"
							class='btn dbo_bottom <?php echo (isset($urla['_class'])?$urla['_class']:"")?>'><?php echo $name ?></a>
							<?php
							if(isset($urla['_fancybox']) && $urla['_fancybox']!=false){?>
								<script type='text/javascript'>
									jQuery('#<?php echo $id?>').fancybox({href:'<?php echo $url;?>',type:'<?php echo $urla['_fancybox'];?>'});
								</script>

									<?php 		}	
					}
					$ret .= ob_get_contents();
					ob_clean();
				}
				ob_end_clean();
			}
			return $ret;
		}

		function draw_colsFilters($oname, $colFilters)
		{
			global $dbo_sel_default;
			if (!(isset($this->t[$oname]) && is_object($this->t[$oname])))
				return "";

			$o = $this->t[$oname];
			$reta = array('v'=>"",'h'=>'','hu'=>false);

			foreach ($colFilters as $k => $v) {
				$ret="";
				$col = $v;
				$conf = array();

				if (is_array($v)) {
					$col = $k;
					$conf = $v;
				}
				$fc=$o->prepCtrl(false,$col,false);
				if ((is_array($fc['opts']) && count($fc['opts']) > 0) || (is_array($conf['opts']) && count($conf['opts']) > 0)) {
					if (is_array($conf['opts']))
						$opts = $conf['opts'];
					else
						$opts = $fc['opts'];
					if (isset($_REQUEST['_colfilter'][$oname][$col]) && (isset($opts[$_REQUEST['_colfilter'][$oname][$col]]) || $_REQUEST['_colfilter'][$oname][$col] == false))
						$_SESSION['_colfilter'][$oname][$col] = $_REQUEST['_colfilter'][$oname][$col];

					if(!isset($_SESSION['_colfilter'][$oname][$col]) && isset($conf['_def']))
						$_SESSION['_colfilter'][$oname][$col]=$conf['_def'];

					$cf = false;
					if (isset($_SESSION['_colfilter'][$oname][$col]))
						$cf = $_SESSION['_colfilter'][$oname][$col];
					$ret .= "<form class='col_filters dbo_tbl_top_form' method='POST'>";
					$ret .= "<select name='_colfilter[{$oname}][$col]'>";
					$ret .= "<option value=''>$dbo_sel_default</option>";
					if(isset($fc['unsetOpts']) && is_array($fc['unsetOpts'])){
						foreach($fc['unsetOpts'] as $v)
							unset($opts[$v]);
					}
					if(isset($fc['treatSame']) && is_array($fc['treatSame'])){
						foreach($fc['treatSame'] as $v)
							unset($opts[$v]);
					}
					asort($opts);

					foreach ($opts as $id => $v) {
						$ret .= "<option " . ($cf == $id ? "selected='true'" : "") . " value='$id'>{$v}</option>";
					}
					$ret .= "</select> ";
					$ret .= "<input type='submit' value='Filter on {$fc['t']}'>";
					$ret .= "</form>";
				}
				if(isset($conf['_onTop']) && $conf['_onTop']==true)
					$reta['v'].=$ret;
				else{
					if($cf!=false)
						$reta['hu']=true;
					$reta['h'].=$ret;
				}
			}
			return $reta;
		}

		function __sys_history_fld_list($o,$fn,$v,$ret){
			$ret="";
			$uf=json_decode($o->cD['upd_fields'],true);	
			$of=json_decode($o->cD['old'],true);	
			$nf=json_decode($o->cD['new'],true);
			global $sys_def_datetime_format;
			//				$fc['t']=date($sys_def_datetime_format,strtotime($fc['t']));
			if(is_object($this->t[$o->cD['obj']]) && count($uf)>0 && count($uf)==count($of) && count($uf)==count($nf)){
				if($o->cD['act']=='u'){
					$ro=$this->t[$o->cD['obj']];
					$ret.="<table style='width:100%;'><tr><th>Field</th><th>Old</th><th>New</th></tr>";
					foreach($uf as $ufn){
						$fc=$ro->prepCtrl(false,$ufn,false);
						$ret.="<tr><td>{$fc['t']}</td><td>".$ro->drawDates($ufn,$of)."</td><td>".$ro->drawDates($ufn,$nf)."</td></tr>";			}
					$ret.="</table>";
				}else{
					$ret.="Record created";
				}
			}else{
				$ret="Err";
			}
			return $ret;
		}

		function send_msg($msg){
			$err="Unexpected error";
			$chkok=true;
			$emailPat="#^[a-zA-Z0-9_.-]+@[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,6}\$#im";
			$phonePat="#^[0-9]{11}\$#im";
			if(check_arrX($msg,"obj","filtType","filtVal")){
				if(isset($msg['fAType']) && isset($msg['from']) && $msg['from']!=false){
					if($msg['fAType']==2){
						$fA=trim(preg_replace("#[^0-9]#mi","",$msg['from']));
						if(strlen($fA)==10)
							$fA="1{$fA}";
						//							$fA="+{$fA}";
						if(!preg_match($phonePat,trim($fA))){
							$chkok=false;
							$err='From Address should be valid PHONE number';
						}
					}else if($msg['fAType']==1){
						if(!preg_match($emailPat,trim($msg['from']))){
							$chkok=false;
							$err='From Address should be valid EMAIL number';
						}
					}
				}

				$curl=curl_init("http://tools.fastunsecured.com/checklist.php?mt=".$msg['mT']);
				curl_setopt_array($curl,array(        
							CURLOPT_RETURNTRANSFER=>true,
							CURLOPT_HEADER=>false,
							));
				$cret=curl_exec($curl);
				$cret=json_decode($cret,true);
				curl_close($curl);
				if($cret['curVOK']!='OK'){
					$chkok=false;
					$err="Invalid Message Type ({$msg['mT']}), valid is {$cret['curVOK']}";
				}
				if($chkok==true && ((!isset($msg['hSubj']) || $msg['hSubj']!=1) && (!isset($msg['subj']) || $msg['subj']==false))){
					$chkok=false;
					$err='SUBJECT should be specified';
				}

				if($chkok==true && (!isset($msg['body']) || $msg['body']==false)){
					$chkok=false;
					$err='BODY should be specified';
				}

				if($chkok && in_array($msg['filtType'],array('i','q','f'))){
					$nCmp=array(
							'Filter_Type__c'=>'Default',
							"Record_Ids__c"=>$msg['filtVal'],
							"Type__c"=>$msg['mT'],
							"External__c"=>true,
							"Auto_Fields__c"=>true,
							'Ignore_DNC__c'=>(isset($msg['sDNC'])&&$msg['sDNC']==1?true:false),
							"Describe__c"=>"From App ".(isset($msg['d'])&&$msg['d']!=false?":{$msg['d']}":""),
							"Related_Object_Name__c"=>$msg['obj'],
							"Subject__c"=>$msg['subj'],
							"Message_Body__c"=>$msg['body'],
							"From__c"=>(isset($msg['from'])?$msg['from']:false),
							"CC_Addresses__c"=>(isset($msg['cc'])?$msg['cc']:false),
							//							"Priority__c"=>"1",
							"Override_From__c"=>(isset($msg['from_beh'])&&$msg['from_beh']=='fbeh_force'?true:false),

							"Status__c"=>"Preparing",

							);
					if($msg['filtType']=='f'){
						$nCmp["Filter_ID__c"]=trim($msg['filtVal']);
						unset($nCmp["Record_Ids__c"]);
					}else	if($msg['filtType']=='q'){
						$nCmp['Filter_Type__c']='SF Query';
						$nCmp["Record_Ids__c"]=stripslashes($nCmp["Record_Ids__c"]);
					}
					if(isset($msg['from_sf']) && $msg['from_sf']){
						$nCmp["Describe__c"]=(isset($msg['d'])&&$msg['d']!=false?$msg['d']:"");
					}
					$jsonS=json_encode(array("records"=>array($nCmp),"refs"=>(object)array(),"cfg"=>(object)array()));
					$qret=SF_query("/bulk_rest_api?obj_name=Message_Campaign__c&key_fld=Name&act=manage&absentOnly=0&not_skip_api=1",$jsonS,true);
					//						$this->setMsg('Sending started');
					//						header("Location: ".aurl("/{$this->t[$msg['obj']]->obj_slug}"));
					//						die("<script type='text/javascript'>parent.jQuery.fancybox.close();</script>");
					return true;
				}else if($chkok==true)
					$err="Wrong filter type";
			}else{
				$err='Wrong args';
			}
			return $err;	
		}

		function do_send_msg(){
			if(!$this->isLogged)
				return false;

			$shpage=true;
			if(isset($_REQUEST['act']) && $_REQUEST['act']=='Send!'){
				if(isset($_REQUEST['next']) && $_REQUEST['next']!=false && method_exists($this,"do_{$_REQUEST['next']}")){
			    $_SESSION['send_msg_req']=$_REQUEST;
          header('Location: '.aurl("/{$_REQUEST['next']}"));
          die();
				}
				$sendRet=$this->send_msg($_REQUEST);
				if($sendRet===true){
					die("<script type='text/javascript'>parent.jQuery.fancybox('Sending started',{href:null,content:'<h1 style=\"white-space:nowrap;\">Sending started....</h1>',type:'html'});</script>");
				}else{
					echo "<center><h2 style='color:red'>ERROR: $sendRet</h2></center>";
				}
				$_REQUEST['f']=$_REQUEST['filtType'];
				$_REQUEST['r']=$_REQUEST['filtVal'];
			}
			if($shpage==true){
				$oh=false;
				if(isset($_REQUEST['oh']) && $_REQUEST['oh']!=false)
					$oh=true;
				$this->show['err']=$err;
				$this->LookAtVar("send_msg",true,true,$oh);
			}
			return true;
		}

		function send_msg_btn($name,$class,$obj,$ftype,$fval,$msgType,$opts=array(),$onlist=false){
			$id="msgid".time().mt_rand(0,9999);
			$arr=array(
					'oh'=>1,
					'obj'=>$obj,
					'mT'=>$msgType
					);
			foreach($opts as $k=>$v)
				$arr[$k]=$v;

			if(!$onlist){
				$arr["f"]=$ftype;
				$arr['r']=$fval;
			}else
				$arr['ajid']=$onlist;


			foreach($arr as $k=>$v)
				$arr[$k]="{$k}=".urlencode($v);
			// ?f={$type}&r=$fval&obj={$obj}&mT=email&fA=3&ajid=1466743767752710&next=junk_filter
			$url=aurl("/send_msg?".implode("&",$arr));
			$ret= "
				<input id='$id' type='button' value='$name' class='$class'>

				<script type='text/javascript'>
				jQuery('#{$id}').bind('click',function(){
						var url='$url';
						";
						if($onlist){
						$ret.="
						ida=jQuery('.idc_tbl_{$onlist}_rows:checked');
						if(ida.length>0){
						url+='&f=a';
						var ids='';
						for(var ii=0;ii<ida.length;ii++){
						var t=ida.get(ii);
						if(ids!='')
						ids+=',';
						ids+=t.value;
						}
						url+='&r='+ids;
						}
						else
						url+='&f=q';
						";
						}
						$ret.="
							jQuery.fancybox({href:url,type:'iframe',width:'90%',height:500,autoScale:false,autoDimensions:false});
				});
			</script>
				";
			return $ret;
		}


		// SALESFORCE CONTROLS ================================================

		function do_sf_ref_ajax(){
			if(!$this->isLogged)
				die('ACCESS DENIED');
			if(!check_arrK($_REQUEST,array('hid','fn','o','ro'),true))
				return false;			
			$hid=$_REQUEST['hid'];
			$fn=$_REQUEST['fn'];
			$on=$_REQUEST['o'];
			$ron=$_REQUEST['ro'];
			$o=$this->t[$on];
			$ro=$this->t[$ron];

			$refFld = $o->rels[$fn]['fld'];
			$cur_cV = $this->v['objs'][$ron];
			$cur_cV['list'] = array(
					'_acts_'=>array(array('_t'=>'Select','_url'=>"javascript:sf_ref_sel_{$hid}('{id}')")),
					$refFld => array('noSlugLink' => true),
					'_def_rows' => '10',
					'_ajaxBaseUrl'=>true,
					'_noSFFList'=>true,
					//				'_hideTblAll' => 'true',

					);
			echo "<div style='padding:5px;display:inline-block;'>";
			$ro->showAdmin('custom_view_ajaxlist', array('ownHeader' => true, 'cV' => $cur_cV));
			echo "</div>";

			die();
		}

		function control_sf_ref($oname, $fn, $fin, $fiv, $hid)
		{
			$relV=$fiv;
			$o=$this->t[$oname];
			$ron=$this->t[$oname]->rels[$fn]['obj'];
			if(isset($this->cdbo->cD["r_".$fn]) && $this->cdbo->cD["r_".$fn]!=false)
				$relV=$this->cdbo->cD["r_".$fn];
			if(is_numeric($relV))
				$relV="";
			//		$ret .= "<span class='aa dbo_offpage_td  dbo_data_td ' id='div_{$hid}'>{$relV}</span>";
			$ret .= "<input id='tit_{$hid}' type='text' disabled='true' value='$relV' >";
			$ret .= "<input value='{$fiv}' type='hidden' id='val_{$hid}' name='{$fin}' >";
			$ret .= "<a id='sf_ref_{$hid}' href='javascript:' class='lookupIcon'></a>";

			$refFld = $o->rels[$fn]['fld'];
			$ret.="
				<script type='text/javascript'>

				function sf_ref_sel_{$hid}(sid){
					jQuery('#val_{$hid}').val(sid);
					jQuery('#tit_{$hid}').val(jQuery('#row_{$ron}_'+sid+' td.dbo_{$ron}_td_{$refFld}').html());
					jQuery.fancybox.close();
				}

			jQuery('a#sf_ref_{$hid}').fancybox({modal:false,type:'ajax',href:'".aurl("/sf_ref_ajax?hid={$hid}&fn={$fn}&o={$oname}&ro={$ron}")."',onClose:function(){jQuery('#fancybox-content').empty();}});

			</script>

				";
			return $ret;
		}

		function control_sf_spinner($oname, $fn, $fin, $fiv, $hid)
		{
			$ctrl_hid = $this->cdbo->hID("{$this->def_lang}_{$fn}_form_ctrl");
			$ret = "<script type='text/javascript'> $(document).ready(function() {
				$( '#{$ctrl_hid}').spinner();
		});</script>";
		$this->cdbo->fctrls[$fn]['c'] = 'text';
		$ret .= $this->cdbo->drawCtrl($fn);
		return $ret;
		}

		function control_sf_select($oname, $fn, $fin, $fiv, $hid)
		{
			$ctrl_hid = $this->cdbo->hID("sel_ctrl_{$fn}");
			$ret = "<script type='text/javascript'> $(document).ready(function() {
				$('#{$ctrl_hid}').select2();
		});
		</script>";
		$this->cdbo->fctrls[$fn]['c'] = 'select';
		$ret .= $this->cdbo->drawCtrl($fn);
		return $ret;
		}

		function control_sf_select_multi($oname, $fn, $fin, $fiv, $hid)
		{
			$ctrl_hid = $this->cdbo->hID("sel_ctrl_{$fn}");
			$ret = "<script type='text/javascript'> $(document).ready(function() {
				$('#{$ctrl_hid}').attr('multiple','multiple');
			$('#{$ctrl_hid}').select2();
		});
		</script>";
		$this->cdbo->fctrls[$fn]['c'] = 'select';
		$ret .= $this->cdbo->drawCtrl($fn);
		return $ret;
		}

		function control_sf_ref_list($oname, $fn, $fin, $fiv, $hid)
		{
			$refObj = $this->cdbo->fctrls[$fn]['sfdata']['refObj'];
			$refFld = $this->cdbo->rels[$fn]['fld'];
			$cur_cV = $this->v['objs'][$refObj];
			$cur_cV['list'] = array(
					'_idc_',
					'_def_rows' => '-1',
					'_hideTblAll' => 'true',
					$refFld => array('noSlugLink' => true),

					);
			$ret = "<script type='text/javascript'>
				$(document).ready(function() {
						$('a#{$hid}').fancybox({
								'modal': true,
								'autoDimensions':false,
								'height':500
								});
						var  sel_ctrl = $('div#dbo_hid_sel_ctrl_{$hid} input:checkbox');
						sel_ctrl.each(function(){
								if ($(this).val()== $('input#{$hid}').val()){
								$(this).prop('checked', true);
								$(this).parent().parent().addClass( 'active' );
								}
								});
						sel_ctrl.click(function() {
								if ($(this).prop('checked') == true){
								$('div#dbo_hid_sel_ctrl_{$hid} input:checkbox').prop('checked', false);
								$('div#dbo_hid_sel_ctrl_{$hid} input:checkbox').parent().parent().removeClass( 'active' );
								$(this).prop('checked', true);
								$(this).parent().parent().addClass( 'active' );
								} else {
								$(this).parent().parent().removeClass( 'active' );
								$(this).prop('checked', false);
								}});
						var selected = $('div#dbo_hid_sel_ctrl_{$hid} input:checked');
						$('#div_{$hid}').text(selected.parent().next().text());
						$('#modal_close_{$hid}').click(function(){
								$.fancybox.close();
								$('input#{$hid}').val($('div#dbo_hid_sel_ctrl_{$hid} input:checked').val());
								$('#div_{$hid}').text( $('div#dbo_hid_sel_ctrl_{$hid} input:checked').parent().next().text());});
				});
			</script>
				<span class='dbo_offpage_td  dbo_data_td ' id='div_{$hid}'></span>
				<input value='{$fiv}' type='hidden' id='{$hid}' name='{$fin}' >
				<a id='{$hid}' href='#dbo_hid_sel_ctrl_{$hid}' class='lookupIcon'></a>
				<div style='display: none'><div  id='dbo_hid_sel_ctrl_{$hid}'>
				<table class='responstable'>";
			$ret .= $this->t[$refObj]->listDef('custom_view_list', array('ownHeader' => true, 'cV' => $cur_cV, 'echo' => false));
			$ret .= "</table><button id='modal_close_{$hid}' >Ok</button></div></div>";
			return $ret;
		}



	}

?>
