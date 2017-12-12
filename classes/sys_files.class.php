<?php
if(!function_exists('mime_content_type')) {

	function mime_content_type($filename) {

		$mime_types = array(

			'txt' => 'text/plain',
			'htm' => 'text/html',
			'html' => 'text/html',
			'php' => 'text/html',
			'css' => 'text/css',
			'js' => 'application/javascript',
			'json' => 'application/json',
			'xml' => 'application/xml',
			'swf' => 'application/x-shockwave-flash',
			'flv' => 'video/x-flv',

			// images
			'png' => 'image/png',
			'jpe' => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'jpg' => 'image/jpeg',
			'gif' => 'image/gif',
			'bmp' => 'image/bmp',
			'ico' => 'image/vnd.microsoft.icon',
			'tiff' => 'image/tiff',
			'tif' => 'image/tiff',
			'svg' => 'image/svg+xml',
			'svgz' => 'image/svg+xml',

			// archives
			'zip' => 'application/zip',
			'rar' => 'application/x-rar-compressed',
			'exe' => 'application/x-msdownload',
			'msi' => 'application/x-msdownload',
			'cab' => 'application/vnd.ms-cab-compressed',

			// audio/video
			'mp3' => 'audio/mpeg',
			'qt' => 'video/quicktime',
			'mov' => 'video/quicktime',

			// adobe
			'pdf' => 'application/pdf',
			'psd' => 'image/vnd.adobe.photoshop',
			'ai' => 'application/postscript',
			'eps' => 'application/postscript',
			'ps' => 'application/postscript',

			// ms office
			'doc' => 'application/msword',
			'rtf' => 'application/rtf',
			'xls' => 'application/vnd.ms-excel',
			'ppt' => 'application/vnd.ms-powerpoint',

			// open office
			'odt' => 'application/vnd.oasis.opendocument.text',
			'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
		);

		$ext = strtolower(array_pop(explode('.',$filename)));
		if (array_key_exists($ext, $mime_types)) {
			return $mime_types[$ext];
		}
		elseif (function_exists('finfo_open')) {
			$finfo = finfo_open(FILEINFO_MIME);
			$mimetype = finfo_file($finfo, $filename);
			finfo_close($finfo);
			return $mimetype;
		}
		else {
			return 'application/octet-stream';
		}
	}
}


class sys_files
{
	private $db;
	static public $sdb;

	static $ctrls_content=array();

	private $adb_data=false;

	public $tbl;
	public $oname="";

	private $types;
	private $ctrls;

	private $db_res;

	private $cimgs;
	private $cimgsl;

	public $path;
	public $url;


	public $p;
	public $llcnt;

	public static $addtrim;
	public static $addgallery;




	function __construct(&$idb,$icfg,$p)
	{
/*		$this->types = array("main_img"=>array(
			"c"=>"img|gallery|file","t"=>"Admin title","d"=>"Admin tip","thumb"=>false|array("w"=>30,"h"=>30),
			"title"=>false,"leavename"=>false,"idtoname"=>false,"onNew"=>false|true,
			"afterFld"=>true=First|false=Last|fldName,
			"trim"=>false|array("w"=>100,"h"=>100),"autoResize"=>false|array("w"=>100,"h"=>100),
			"admShow"=>false|true|array("w"=>100,"h"=>100),"links"=>false|array("url","table names")
		)
);*/
		$tdef = array(
			"c"=>"img","t"=>"","d"=>"","thumb"=>false,"title"=>false,"leavename"=>false,"idtoname"=>false,"onNew"=>true,
			"afterFld"=>false,"trim"=>false,"autoResize"=>false,"admShow"=>true,"links"=>false,"copies"=>false);

		self::$addtrim=false;
		self::$addgallery=false;

		$this->cimgs=false;
		$this->types=array();
		foreach($icfg['types'] as $tn=>$tv){
			if(!isset($tv['afterFld']))
			{
				if($tv['c']=="gallery")
					$tv['afterFld']=false;
				else
					$tv['afterFld']=true;
			}
			$this->types[$tn]=array_merge($tdef,$tv);
			if($this->types[$tn]['links']!=false && !is_array($this->types[$tn]['links']))
				$this->types[$tn]['links']=array($this->types[$tn]['links']);
		}

		$this->ctrls=array();
		foreach($this->types as $tn=>$tv)
			$this->ctrls[$tv[c]][$tn]=$tv;

		$this->p=$p;
		$this->db=$idb;
		$this->tbl=$this->p->tbl;
		$this->oname=$this->p->oname;
		$this->db_res="sys_files_".$this->oname;
		$this->path=$icfg['path'];

		if(strrpos($this->path,"/")!=strlen($this->path)-1)
			$this->path.="/";
		if(!isset($icfg['url']))
			$this->url=$this->path;
		else
			$this->url=$icfg['url'];
		if(strrpos($this->url,"/")!=strlen($this->url)-1)
			$this->url.="/";

	if(!is_dir($this->path))
		mkdir($this->path);

/*	if($this->path[0]=='/')
		$this->path=rpath($this->path,false,false);
	else
		$this->path=rpath("/".$this->path,false,false);
*/


	}

	public static function contents()
	{
		echo implode("\n",self::$ctrls_content);	
	}
	
	public static function addTrimDlgJS()
	{
?>
		<script type='text/javascript' src='js/trim.plugin.js' ></script>

<?php
	}

	public static function addGalleryDlgJS()
	{
		if(self::$addgallery==false){
			$root_path = "./";
?>
<link href="css/galleriffic-2.css" rel="stylesheet" type="text/css">

<script type="text/javascript" src="<?php echo $root_path?>js/jquery.galleriffic.js"></script>
<script type="text/javascript" src="<?php echo $root_path?>js/jquery.opacityrollover.js"></script>
<script type="text/javascript" src="<?php echo $root_path?>js/jquery.validate.min.js"></script>

<?php
		}

		self::$addgallery=true;
	}
	function autoCtrlChkArr($fn)
	{
		foreach($this->types as $ln=>$lv){
			if($lv['afterFld']===$fn)
				return true;
		}
		return false;
	}

	function autoCtrlArr($fn)
	{
		$ret=array();
		foreach($this->types as $tn=>$tv){
			if($tv['afterFld']===$fn)
				$ret["sys_files_{$tn}"]=array("c"=>"sys_files","t"=>$tv['t'],"d"=>$tv['d'],"type"=>$tn);
		}
		return $ret;
	}

	function autoCtrlChk($cfld){
		foreach($this->types as $tn=>$t){
			if($t['afterFld']===$cfld){
				if(method_exists($this,$t['c']."Ctrl")){
					if(!$t['onNew'] && $this->p->current==false)
						return false;
					else
						return true;
				}
			}
		}
		return false;
	}

	function drawAdminCtrl($tname,$delim=""){
		return $this->drawCtrl($tname,$delim);
	}
	function drawCtrl($tname,$delim=""){
			if(isset($this->types[$tname])){
				if(method_exists($this,$this->types[$tname]['c']."Ctrl")){
					if(!$this->types[$tname]['onNew'] && $this->p->current==false)
						return false;
					else
						$this->{$this->types[$tname]['c']."Ctrl"}($tname,$delim);
				}
			}
		
	}

	function autoCtrl($cfld,$delim=""){
		foreach($this->types as $tn=>$t){
			if($t['afterFld']===$cfld){
				if(method_exists($this,$t['c']."Ctrl")){
					if(!$t['onNew'] && $this->p->current==false)
						return false;
					else
						$this->{$t['c']."Ctrl"}($tn,$delim);
				}
			}
		}
		return false;
	}

	function queryCurrent($tname,$tid=false,$cid=false)
	{

		if(($tid=='new' && $cid==false) || ($tid==false && $this->p->current==false))
		{
			$this->cimgs=false;
			return false;
		}

		$ff="ct.*";
		$jj="";
		if($this->types[$tname]['links']!=false){
			$ff="ct.*,l.name as l_name,l.type as l_type,l.url as l_url,l.rtbl as l_rtbl,l.rid as l_rid,l.id as l_id";
			$jj=" left join {$this->p->p->db_prefix}sys_links l on (l.tbl='sys_files_own_links' and l.tid=ct.id) ";
		}

		$qq="select $ff from {$this->p->p->db_prefix}sys_files ct $jj 
			where ".($cid!==false?"ct.id={$cid} or ct.lid={$cid}":" ct.tbl='{$this->tbl}' and ct.tid=".($tid===false?$this->p->current[$this->p->def_lang]['id']:$tid)." and ct.ftype='{$this->types[$tname]['c']}_{$tname}'")." order by ct.id desc";



		$this->db->query($qq,"{$this->db_res}_queryCurr");

		$this->cimgs=array();
		while($row=$this->db->next("{$this->db_res}_queryCurr")){
			if($row['lid']==0){
				$this->cimgs["{$this->types[$tname]['c']}_{$tname}"][]=$row;
				$this->cimgsl["{$this->types[$tname]['c']}_{$tname}"][$row['lang']][$row['id']]=$row;
			}else
				$this->cimgsl["{$this->types[$tname]['c']}_{$tname}"][$row['lang']][$row['lid']]=$row;
		}
	}

	function getCur($tname,$i,$n)
	{
		if(isset($this->cimgs["{$this->types[$tname]['c']}_{$tname}"][$i]))	
		{
			if($n=="path")
				return $this->path.$this->cimgs["{$this->types[$tname]['c']}_{$tname}"][$i]['full'];
			else if($n=="tpath")
				return $this->path."t".$this->cimgs["{$this->types[$tname]['c']}_{$tname}"][$i]['full'];
			else if($n=="url")
				return $this->url.$this->cimgs["{$this->types[$tname]['c']}_{$tname}"][$i]['full'];
			else if($n=="turl")
				return $this->url."t".$this->cimgs["{$this->types[$tname]['c']}_{$tname}"][0]['full'];
			else if(isset($this->cimgs["{$this->types[$tname]['c']}_{$tname}"][$i][$n]))
				return $this->cimgs["{$this->types[$tname]['c']}_{$tname}"][$i][$n];
		}
		else
			return false;
	}

	function getCurImg($tname,$n)
	{
		if(isset($this->cimgs["img_{$tname}"][0]))	
		{
			if($n=="path")
				return $this->path.$this->cimgs["img_{$tname}"][0]['full'];
			else if($n=="tpath")
				return $this->path."t".$this->cimgs["img_{$tname}"][$i]['full'];
			else if($n=="url")
				return $this->url.$this->cimgs["img_{$tname}"][0]['full'];
			else if($n=="turl")
				return $this->url."t".$this->cimgs["img_{$tname}"][0]['full'];
			else if(isset($this->cimgs["img_{$tname}"][0][$n]))
				return $this->cimgs["img_{$tname}"][0][$n];
		}
		else
			return false;
	}

	function getCurFile($tname,$n) 
	{
		if(isset($this->cimgs["file_{$tname}"][0]))	
		{
			if($n=="path")
				return $this->path.$this->cimgs["file_{$tname}"][0]['full'];
			else if($n=="tpath")
				return $this->path."t".$this->cimgs["file_{$tname}"][$i]['full'];
			else if($n=="url")
				return $this->url.$this->cimgs["file_{$tname}"][0]['full'];
			else if($n=="turl")
				return $this->url."t".$this->cimgs["file_{$tname}"][0]['full'];
			else if(isset($this->cimgs["file_{$tname}"][0][$n]))
				return $this->cimgs["file_{$tname}"][0][$n];
		}
		else
			return false;
	}

	function trimFuncParams($tname,$img=false,$thumb=false)
	{
		$aw=4;
		$ah=3;
		$nw="false";
		$nh="false";
		if(is_array($this->types[$tname]['trim']))
		{
			$nw=$aw=$this->types[$tname]['trim']['w'];
			$nh=$ah=$this->types[$tname]['trim']['h'];
		}
		else if(is_array($this->types[$tname]['autoResize']))
		{
			$nw=$aw=$this->types[$tname]['autoResize']['w'];
			$nh=$ah=$this->types[$tname]['autoResize']['h'];
		}
		if(is_array($this->types[$tname]['aspectRatio'])){
			$aw=$this->types[$tname]['aspectRatio']['w'];
			$ah=$this->types[$tname]['aspectRatio']['h'];
		}

		$cps=array();
		if(is_array($this->types[$tname]['copies'])){
			foreach($this->types[$tname]['copies'] as $k=>$v)
				$cps[]="{name:'{$k}',w:{$v['w']},h:{$v['h']}}";
		}

		return "$aw,$ah,$nw,$nh,".(is_array($this->types[$tname]['thumb'])?"{$this->types[$tname]['thumb']['w']},{$this->types[$tname]['thumb']['h']}" : "false,false").",".($img? "'$img'" : "false").",".($thumb? "'$thumb'" : "false").",[".implode(",",$cps)."]";
	}


	function imgCtrl($tname,$delim="")
	{

		if(!array_key_exists($tname,$this->ctrls['img']))
			return false;
/*
		$this->queryCurrent($tname);

		if($this->types[$tname]['admShow']!=false && $this->getCurImg($tname,"id"))
			echo "<img id='sys_files_img_{$tname}_{$this->p->current[$this->p->def_lang]['id']}' src='".$this->getCurImg($tname,"url")."?".time()."' type='text' name='sys_files[img_{$tname}]' ".(is_array($this->types[$tname]['admShow'])?"'width'='{$this->types[$tname]['w']}px' 'height'='{$this->types[$tname]['h']}px'":"")."><br/>";

		if($this->types[$tname]['title']==true)
			echo "<input type='text' name='sys_files[img_{$tname}][title]' value='".$this->getCurImg($tname,"title")."'>{$delim}";

		echo "<input type='file' name='sys_files_img_{$tname}'>";

		if($this->types[$tname]['trim']==true && $this->getCurImg($tname,"id")){
			echo "<input type='button' value='Trim' 
				onclick=\"trim_plugin('".$this->getCurImg($tname,"path")."',".$this->trimFuncParams($tname,"#sys_files_img_{$tname}_{$this->p->current[$this->p->def_lang]['id']}").")\">";

		}
 */

		$this->queryCurrent($tname);

		if($this->types[$tname]['admShow']!=false && $this->getCurImg($tname,"id"))
			echo "<img class='sys_files_img_{$tname}_{$this->p->cD['id']}' src='".$this->getCurImg($tname,"url")."?".time()."' type='text' name='sys_files[img_{$tname}]' ".(is_array($this->types[$tname]['admShow'])?"'width'='{$this->types[$tname]['w']}px' 'height'='{$this->types[$tname]['h']}px'":"")."><br/>";
?>
		<input type="button" value="Load and Trim" onclick="$('#sys_files_img_<?php echo "{$tname}_".(isset($this->p->cD['id'])?$this->p->cD['id']:"new")?>_dialog').dialog({modal:true,width:($(window).width()-50),height:($(window).height()-50)});" > 
<?php

		$isnew_v=(int)time();
		if(!isset($this->cD['id'])){?>
			<input type="hidden" name='<?php echo $this->p->in_as("[sys_files][new_id][{$tname}]");?>' value="<?php echo $isnew_v?>"> 
<?php }

		ob_start();
			include($this->p->p->TEMPL."/inc/sys_files_gallery_ctrl.inc.php");
		self::$ctrls_content[]=ob_get_contents();
		ob_end_clean();

	}

	function fileCtrl($tname,$delim="")
	{
		if(!array_key_exists($tname,$this->ctrls['file']))
			return false;

		$this->queryCurrent($tname);

		if($this->types[$tname]['admShow']!=false && $this->getCurFile($tname,"id") && $this->types[$tname]['helpFor']==false)
			echo "<a href='".$this->getCurFile($tname,"url")."'>".$this->getCurFile($tname,"full")."</a>&nbsp;";

		if($this->types[$tname]['title']==true)
			echo "<input type='text' name='sys_files[file_{$tname}][title]' value='".$this->getCurFile($tname,"title")."'>{$delim}";

//		echo "<input type='file' name='sys_files_file_{$tname}".(isset($this->p->cD['id'])&&$this->p->cD['id']!=false?"_cid".$this->p->cD['id']:"")."'>";
		echo "<input type='file' name='".$this->p->input_file_name("sys_files_file_{$tname}",array("echo"=>false))."'>";

	}

	function galleryCtrl($tname,$delim="")
	{
		if(!array_key_exists($tname,$this->ctrls['gallery']))
			return false;


		$this->queryCurrent($tname);
?>
		<input type="button" value="Gallery" onclick="$('#sys_files_gallery_<?php echo "{$tname}_".(isset($this->p->cD['id'])?$this->p->cD['id']:"new")?>_dialog').dialog({modal:true,width:($(window).width()-50),height:($(window).height()-50)});" > 
<?php

		$isnew_v=(int)time();
		if(!isset($this->cD['id'])){?>
			<input type="hidden" name='<?php echo $this->p->in_as("[sys_files][new_id][{$tname}]");?>' value="<?php echo $isnew_v?>"> 
<?php }

		ob_start();
			include($this->p->p->TEMPL."/inc/sys_files_gallery_ctrl.inc.php");
		self::$ctrls_content[]=ob_get_contents();
		ob_end_clean();

	}


	function mngPrep(&$fl,$act,$tid,$tname,$ifile,$cid=false)
	{

		if($act!="d" && !(is_array($ifile) && is_file($ifile['tmp_name'])))
			return false;
		$this->queryCurrent($tname,$tid,$cid);
		$fl['cid']=$this->getCur($tname,0,"id");
		$fl['cpath']=$this->getCur($tname,0,"path");
		$fl['isnew']="0";

		if($tid=="new")
			$fl['isnew']=$_REQUEST['isnew'];

		if($act!="d"){
			$fl['pinf']=pathinfo($ifile['name']);
			if(!isset($fl['pinf']['filename']))
				$fl['pinf']['filename']=substr($fl['pinf']['basename'],0,(strlen($fl['pinf']['basename'])-(strlen($fl['pinf']['extension'])+1)));

			$title="";
			if($this->types[$tname]['title'] && isset($_REQUEST['sys_files']["{$this->types[$tname]['c']}_{$tname}"]))
				$fl['title']=$_REQUEST['sys_files']["{$this->types[$tname]['c']}_{$tname}"];
		}
		return true;
	}

	function mngIns(&$fl,$tid,$tname)
	{
		$this->db->query("insert into {$this->p->p->db_prefix}sys_files set tbl='{$this->tbl}', tid=".($tid=='new'?"0":$tid).",
			ftype='{$this->types[$tname]['c']}_{$tname}',isnew={$fl['isnew']},dir='{$this->url}',lang='{$this->p->def_lang}'");
		$fl['nid']=$this->db->lastInsertId();
		if(count($this->p->langs)>1){
			foreach($this->p->langs as $l)
			{
				if($l==$this->p->def_lang) continue;
				$this->db->query("insert into {$this->p->p->db_prefix}sys_files set tbl='{$this->tbl}', tid=".($tid=='new'?"0":$tid).",
					ftype='{$this->types[$tname]['c']}_{$tname}',isnew={$fl['isnew']},dir='{$this->url}',lang='{$l}',lid={$fl['nid']}");
			}
		}
		//				die($this->db->getLastError());
	}
	function mngUpd(&$fl,$tid,$tname,$ifile)
	{
		//		die($this->db->getLastError());
		if($fl['cid'] && is_file($fl['cpath']))
			unlink($fl['cpath']);
		if($fl['nid'])
			$fl['cid']=$fl['nid'];
		if($this->types[$tname]['leavename']){
			if($this->types[$tname]['idtoname']){
				$fl['nn']=$fl['cid']."_".$fl['pinf']['basename'];
				$fl['pinf']['filename']=$fl['cid']."_".$fl['pinf']['filename'];
			}else
				$fl['nn']=$fl['pinf']['basename'];
		}else{
			$fl['nn']=$fl['cid'].".".$fl['pinf']['extension'];
			$fl['pinf']['filename']=$fl['cid'];
		}

		if($this->types[$tname]['no_hashname']!=true){
			$fl['pinf']['filename']=md5($fl['cid'].time().mt_rand(1,999999));
			$fl['nn']="{$fl['pinf']['filename']}.".$fl['pinf']['extension'];
		}

		$fl['pinf']['basename']=$fl['nn'];

		copy($ifile['tmp_name'],$this->path.$fl['nn']);
		$fl['mm']=mime_content_type($this->path.$fl['nn']);
		$this->db->query("update {$this->p->p->db_prefix}sys_files set 
			full='{$fl['pinf']['basename']}',name='{$fl['pinf']['filename']}',ext='{$fl['pinf']['extension']}',
			mime='{$fl['mm']}' where id={$fl['cid']}");
		if(count($this->p->langs)>1){
			foreach($this->p->langs as $l)
			{
				if($l==$this->p->def_lang) continue;
				$this->db->query("update {$this->p->p->db_prefix}sys_files set 
					full='{$fl['pinf']['basename']}',name='{$fl['pinf']['filename']}',ext='{$fl['pinf']['extension']}',
					mime='{$fl['mm']}' where lid={$fl['cid']}");
			}
		}
		if(isset($this->types[$tname]['helpFor']) && $this->types[$tname]['helpFor']!=false){

			$this->db->query($q="update {$this->p->p->db_prefix}{$this->tbl} set {$this->types[$tname]['helpFor']}='".aurl("/{$this->url}{$fl['pinf']['basename']}")."'
				where id={$tid} or lid={$tid}");
		}

	}
	function mngDel(&$fl,$tid)
	{
		if(is_file($fl['cpath']))
			unlink($fl['cpath']);
		$this->db->query("delete from {$this->p->p->db_prefix}sys_files where id={$fl['cid']} or lid={$fl['cid']}");
		$this->db->query("delete from {$this->p->p->db_prefix}sys_links where tbl='sys_files_own_links' and tid={$fl['cid']}");
	}

	function imgManage($act,$tid,$tname,$ifile)
	{
		$f=array();

		if(!$this->mngPrep($f,$act,$tid,$tname,$ifile))
			return false;
		$f['tpath']=$this->getCur($tname,0,"tpath");


		switch($act){
		case "i":
			if(!$f['cid']){
				$this->mngIns($f,$tid,$tname);
			}
		case "u":
			if(!($f['cid'] || $f['nid'])){
				$this->mngIns($f,$tid,$tname);
			}
			$this->mngUpd($f,$tid,$tname,$ifile);
			if($this->types[$tname]['autoResize']!=false || $this->types[$tname]['trim']!=false)
				$this->convertToJPEG_ifNot_PNG_or_GIF($f);
			if($this->types[$tname]['autoResize']!=false)
				$this->autoResize($this->path.$f['nn'],$this->types[$tname]['autoResize']);
			$turl=$this->url.$f['nn'];
			if($this->types[$tname]['thumb']!=false && is_array($this->types[$tname]['thumb'])){
					$this->makeThumb($this->path.$f['nn'],$this->types[$tname]['thumb'],100,"t");
					$turl=$this->url."t".$f['nn'];
			}
			if($this->types[$tname]['copies']!=false && is_array($this->types[$tname]['copies'])){
					foreach($this->types[$tname]['copies'] as $k=>$v)
					$this->makeThumb($this->path.$f['nn'],$v,100,$k);
			}
					

			if(isset($_REQUEST['ajax']) && $_REQUEST['ajax']==1){
				if($act=='i'){
				$css_prefix="{$this->types[$tname]['c']}_{$tname}_{$tid}";
				$id=$f['cid'];
				$ctid=$tid;
				$rtitle=$f['title'];
				$fpath=$this->url.$f['nn'];
				$trimpath=$this->path.$f['nn'];
				$tpath=$turl;
				ob_start();
				require("{$this->p->p->TEMPL}/inc/sys_files_gallery_item_ctrl.inc.php");
				$rdat=ob_get_contents();
				ob_end_clean();
				die("{error:'',\nimgid:{$f['cid']},ctrl_data:'".addcslashes($rdat,"'\n\r\t\\")."',\nimg:'{$this->url}{$f['nn']}?".time()."',thumb:'$turl?".time()."',title:'{$f['title']}'}");
				}else{
					die("{error:'',\nimg:'{$this->url}{$f['nn']}',thumb:'$turl',imgid:{$f['cid']},title:'{$f['title']}'}");
				}
			}

/*			if(isset($_REQUEST['ajax']) && $_REQUEST['ajax']==1)
				die("{error:'',\nimg:'{$this->url}{$f['nn']}',thumb:'$turl',imgid:{$f['cid']},title:'{$f['title']}'}");
 */
			break;
		case "d":
			$this->mngDel($f,$tid);
			if($this->types[$tname]['thumb']!=false)
				$this->delThumb($f);
			if(isset($_REQUEST['ajax']) && $_REQUEST['ajax']==1)
				die("{\"ret\":\"OK\",\"imgid\":\"{$f['cid']}\"}");
			break;
		}
	}

	function fileManage($act,$tid,$tname,$ifile)
	{
		$f=array();
		if(!$this->mngPrep($f,$act,$tid,$tname,$ifile))
			return false;

		switch($act){
		case "i":
			if(!$cid){
				$this->mngIns($f,$tid,$tname);
			}
		case "u":
			if(!($f['cid'] || $f['nid'])){
				$this->mngIns($f,$tid,$tname);
			}
			$this->mngUpd($f,$tid,$tname,$ifile);
			if(isset($_REQUEST['ajax']) && $_REQUEST['ajax']==1)
				die("{error:'',\nimg:'{$this->url}{$f['nn']}',thumb:'$turl',imgid:{$f['cid']},title:'{$f['title']}'}");
			break;
		case "d":
			$this->mngDel($f,$tid);
			if(isset($_REQUEST['ajax']) && $_REQUEST['ajax']==1)
				die("{\"ret\":\"OK\",\"imgid\":\"{$f['cid']}\"}");
			break;
		}
	}
	function galleryManage($act,$tid,$tname,$ifile)
	{
		$f=array();
		if(!$this->mngPrep($f,$act,$tid,$tname,$ifile,$_REQUEST['sys_files']["{$this->types[$tname]['c']}_{$tname}"]['id']))
			return false;
		$f['tpath']=$this->getCur($tname,0,"tpath");

		switch($act){
		case "i":
			$this->mngIns($f,$tid,$tname);
		case "u":
			$turl=false;
			$this->mngUpd($f,$tid,$tname,$ifile);
			if($this->types[$tname]['autoResize']!=false || $this->types[$tname]['trim']!=false)
				$this->convertToJPEG_ifNot_PNG_or_GIF($f);
			if($this->types[$tname]['autoResize']!=false)
				$this->autoResize($this->path.$f['nn'],$this->types[$tname]['autoResize']);
			if($this->types[$tname]['thumb']!=false && is_array($this->types[$tname]['thumb'])){
					$this->makeThumb($this->path.$f['nn'],$this->types[$tname]['thumb'],100,"t");
					$turl=$this->url."t".$f['nn'];
			}
			if($this->types[$tname]['copies']!=false && is_array($this->types[$tname]['copies'])){
					foreach($this->types[$tname]['copies'] as $k=>$v)
					$this->makeThumb($this->path.$f['nn'],$v,100,$k);
			}
			if(isset($_REQUEST['ajax']) && $_REQUEST['ajax']==1){
				if($act=='i'){
				$css_prefix="{$this->types[$tname]['c']}_{$tname}_{$tid}";
				$id=$f['cid'];
				$ctid=$tid;
				$rtitle=$f['title'];
				$fpath=$this->url.$f['nn'];
				$trimpath=$this->path.$f['nn'];
				$tpath=$turl;
				if($tpath==false)
					$tpath=$fpath;
				ob_start();
				require("{$this->p->p->TEMPL}/inc/sys_files_gallery_item_ctrl.inc.php");
				$rdat=ob_get_contents();
				ob_end_clean();
				die("{error:'',\nctrl_data:'".addcslashes($rdat,"'\n\r\t\\")."'}");
				}else{
					die("{error:'',\nimg:'{$this->url}{$f['nn']}',thumb:'$turl',imgid:{$f['cid']},title:'{$f['title']}'}");
				}
			}
			break;
		case "d":
			$this->mngDel($f,$tid);
			if($this->types[$tname]['thumb']!=false)
				$this->delThumb($f);
			if(isset($_REQUEST['ajax']) && $_REQUEST['ajax']==1)
				die("{\"ret\":\"OK\",\"imgid\":\"{$f['cid']}\"}");
			break;
		}
	}

	function delThumb($fl)
	{
		if(is_file($fl['tpath']))
			unlink($fl['tpath']);
	}

	function manage($act,$tid)
	{
		if($act!="d"){
			$postfix=$this->p->gO("sys_files_postfix");
			foreach($_FILES as $fn=>$fv)
			{
//echo "<br>".$fn." - $postfix";
				if(!(strpos($fn,"sys_files_")===0 && is_array($fv) && is_file($fv['tmp_name']) && ($postfix==false || (strrpos($fn,"_".$postfix)==strlen($fn)-strlen($postfix)-1))))
					continue;
				$ta=explode("_",str_replace("sys_files_","",$fn),2);

				if($postfix!=false)
					$ta[1]=substr($ta[1],0,strrpos($ta[1],$postfix)-1);
//echo ":OK: {$ta[0]} - {$ta[1]}<hr>";

				if(isset($this->ctrls[$ta[0]][$ta[1]])){
					if(method_exists($this,$ta[0]."Manage"))
						$this->{$ta[0]."Manage"}($act,$tid,$ta[1],$fv);
				}
			}
		}else if(isset($_REQUEST['sys_files']))
		{
			foreach($_REQUEST['sys_files'] as $n=>$v)
			{
				if(isset($v['id']))
				{
					$tn=explode("_",$n,2);
					$tn=$tn[1];
					if(isset($this->types[$tn])){
						if(method_exists($this,$this->types[$tn]['c']."Manage"))
							$this->{$this->types[$tn]['c']."Manage"}($act,$tid,$tn,false);
					}
				}

			}
		}
		
	}


	function makeThumb($fpath,$size,$q=100,$name)
	{
		list($w, $h) = getimagesize($fpath);
		$image = imagecreatefromstring(file_get_contents($fpath));

		$thumb = imagecreatetruecolor($size['w'], $size['h']);

		imagecopyresampled($thumb, $image, 0, 0, 0, 0, $size['w'], $size['h'], $w, $h);

		$pinf=pathinfo($fpath);
		if(!isset($pinf['filename']))
			$pinf['filename']=substr($pinf['basename'],0,(strlen($pinf['basename'])-(strlen($pinf['extension'])+1)));


		sys_files::saveImg($thumb,$pinf['dirname']."/".($name=="t"?$name:$name."_").$pinf['filename'].".jpg",$q);

	}

	public static function saveImg($img,$path,$q){
		$mime=mime_content_type($path);
		if($mime=="image/png")
			imagepng($img,$path);
		else if($mime=="image/gif")
			imagegif($img,$path);
		else
			imagejpeg($img,$path,$q);
	
	}
	function autoResize($fpath,$size)
	{
		list($w, $h) = getimagesize($fpath);
		$image = imagecreatefromstring(file_get_contents($fpath));
		$image_resized = imagecreatetruecolor($size['w'], $size['h']);
		imagecopyresampled($image_resized, $image, 0,0,0,0, $size['w'], $size['h'], $w, $h);
		sys_files::saveImg($image_resized,$fpath,100);

	}

	function convertToJPEG_ifNot_PNG_or_GIF(&$fl){
		if(in_array(strtolower($fl['pinf']['extension']),array("jpg","jpeg","png","gif")))
			return false;
		$this->convertToJPEG($fl);
	}

	

	function convertToJPEG(&$fl)
	{
		if($fl['pinf']['extension']=="jpg" || $fl['pinf']['extension']=="jpeg")
			return false;
		$image = imagecreatefromstring(file_get_contents($this->path.$fl['nn']));
		$fl['pinf']['extension']='jpg';
		$fl['pinf']['basename']=$fl['pinf']['filename'].".".$fl['pinf']['extension'];

		unlink($this->path.$fl['nn']);

		$fl['nn']=$fl['pinf']['basename'];


		imagejpeg($image,$this->path.$fl['pinf']['basename']);

		$fl['mm']=mime_content_type($this->path.$fl['nn']);
		$this->db->query("update {$this->p->p->db_prefix}sys_files set 
			full='{$fl['pinf']['basename']}',name='{$fl['pinf']['filename']}',ext='{$fl['pinf']['extension']}',
			mime='{$fl['mm']}' where id={$fl['cid']} or lid={$fl['cid']}");

	}

	public static function imgTrim($data=false){

		list($w, $h) = getimagesize($data['fname']);
		$w=$w-$data['x'];
		$h=$h-$data['y'];
		$image_new = imagecreatetruecolor($data['w'], $data['h']);
/*if($data['l']>0)
	$w-=$data[';'];
if($data['t']>0)
$h-=$data['t'];*/
		$image = imagecreatefromstring(file_get_contents($data['fname']));

		if(in_array(mime_content_type($data['fname']),array("image/png","image/gif"))){
			imagecolortransparent($image_new,imagecolorallocate($image_new, 0,0,0));
		}
		else{
			$white = imagecolorallocate($image_new, 255, 255, 255);
			imagefill($image_new, 0, 0, $white);
		}
		
		imagecopyresampled($image_new, $image, $data['l'], $data['t'], $data['x'], $data['y'], $w, $h, $w, $h);


		if($data['h']!=$data['th'] || $data['w']!=$data['tw']){
			$image_thumb = imagecreatetruecolor($data['tw'], $data['th']);
			imagecopyresampled($image_thumb, $image_new, 0,0,0,0, $data['tw'], $data['th'], $data['w'], $data['h']);
			$pinf=pathinfo($data['fname']);
			if(!isset($pinf['filename']))
				$pinf['filename']=substr($pinf['basename'],0,(strlen($pinf['basename'])-(strlen($pinf['extension'])+1)));

			$tfile=$pinf['dirname']."/t".$pinf['filename']."."."jpg";
			sys_files::saveImg($image_thumb,$tfile, $data['q']);
		}

		if(isset($data['cps']) && is_array($data['cps'])){
			$pinf=pathinfo($data['fname']);
			if(!isset($pinf['filename']))
				$pinf['filename']=substr($pinf['basename'],0,(strlen($pinf['basename'])-(strlen($pinf['extension'])+1)));

			foreach($data['cps'] as $k=>$v){
			$image_copy = imagecreatetruecolor($v['w'], $v['h']);
			imagecopyresampled($image_copy, $image_new, 0,0,0,0, $v['w'], $v['h'], $data['w'], $data['h']);

			$cpfile=$pinf['dirname']."/{$k}_".$pinf['filename']."."."jpg";
			sys_files::saveImg($image_copy,$cpfile, $data['q']);
			}
		}

		if($data['h']!=$data['nh'] || $data['w']!=$data['nw']){
			$image_resized = imagecreatetruecolor($data['nw'], $data['nh']);
			imagecopyresampled($image_resized, $image_new, 0,0,0,0, $data['nw'], $data['nh'], $data['w'], $data['h']);
			$image_new=$image_resized;
		}

		sys_files::saveImg($image_new,$data['fname'], $data['q']);
	}


	function onParentDel($tid)
	{
		$this->db->query("select id,full from {$this->p->p->db_prefix}sys_files where tbl='$this->tbl' and tid={$tid}","{$this->db_res}_delParent");
		while($cf=$this->db->next("{$this->db_res}_delParent")){
			$this->db->query("delete from {$this->p->p->db_prefix}sys_links where tbl='sys_files_own_links' and tid={$cf['id']}");
			if(is_file($this->path.$cf['full']))
				unlink($this->path.$cf['full']);
			if(is_file($this->path."t".$cf['full']))
				unlink($this->path."t".$cf['full']);

		}

		$this->db->query("delete from {$this->p->p->db_prefix}sys_files where tbl='$this->tbl' and tid={$tid}");
	}

	function orphanNewDel()
	{
		$this->db->query("select full from {$this->p->p->db_prefix}sys_files where isnew>0","{$this->db_res}_orphDelParent");
		while($cf=$this->db->next("{$this->db_res}_orphDelParent")){
			if(is_file($this->path.$cf['full']))
				unlink($this->path.$cf['full']);
			if(is_file($this->path."t".$cf['full']))
				unlink($this->path."t".$cf['full']);

		}

		$this->db->query("delete from {$this->p->p->db_prefix}sys_files where isnew>0");
	}


	function trimDlg()
	{

	}

	function show($tname,$tid,$tmpl)
	{

	}

	function showGallery($tname,$tid,$tmpl="gallery")
	{
		if(!isset($this->types[$tname]))
			return false;

		$this->queryCurrent($tname,$tid);
/*		$ff="ct.*";
		$jj="";
		if($this->types[$tname]['links']!=false){
			$ff="ct.*,l.name as l_name,l.type as l_type,l.url as l_url,l.rtbl as l_rtbl,l.rid as l_rid,l.id as l_id";
			$jj=" left join {$this->p->p->db_prefix}sys_links l on (l.tbl='sys_files_own_links' and l.tid=ct.id) ";
		}

		$cq="select $ff from {$this->p->p->db_prefix}sys_files ct $jj 
			where ct.tbl='{$this->tbl}' and ct.tid=$tid and ct.ftype='{$this->types[$tname]['c']}_$tname' order by ct.id desc";

		$this->db->query($cq,"{$this->db_res}_galleryShow");

		while($row=$this->db->next("{$this->db_res}_galleryShow"))
		{
			$row['link']=false;
			if(isset($row['l_id']) && $row['l_id']!=false)
			{
				$row['has_link']=true;
				$row['link']=($row['l_type']==0?$row['url']:"index.php?a=dbo_{$row['l_rtbl']}&rid={$row['l_rid']}");
			}
			$row['url']=$this->url.$row['full'];
			$row['title']=$
			include($this->p->p->tpl_path("inc/sys_files_{$tmpl}_show.inc.php"));
		}
 */
		$this->llcnt=0;
		if(is_array($this->cimgsl["gallery_{$tname}"][$this->p->cur_lang])){
		foreach($this->cimgsl["gallery_{$tname}"][$this->p->cur_lang] as $row)
		{
			$row['link']=false;
			if(isset($row['l_id']) && $row['l_id']!=false)
			{
				$row['has_link']=true;
				$row['link']=($row['l_type']==0?$row['url']:"index.php?a=dbo_{$row['l_rtbl']}&rid={$row['l_rid']}");
			}
			$row['url']=$this->url.$row['full'];
			include($this->p->p->tpl_path("inc/sys_files_{$tmpl}_show.inc.php"));
			$this->llcnt++;
		}
		}
	}

	function showImg($tname,$tid,$type=false){
		if(!isset($this->types[$tname]))
			return false;

		$cq="select * from {$this->p->p->db_prefix}sys_files where tbl='{$this->tbl}' and tid=$tid and ftype='{$this->types[$tname]['c']}_$tname'";

		$this->db->query($cq,"{$this->db_res}_imageShow");

		$row=$this->db->next("{$this->db_res}_imageShow");
		if($type==false)
			return $this->url.$row['full'];
		else if($type=="t")
			return $this->url."t".$row['full'];
		else
			return $this->url."{$type}_".$row['full'];
	}

	static function s_showImg($tname,$tbl,$tid){
		$cq="select * from sys_files where tbl='{$tbl}' and tid=$tid and ftype='img_{$tname}'";
		self::$sdb->query($cq,"sys_files_s_imageShow");

		$row=self::$sdb->next("sys_files_s_imageShow");
		return $row['dir'].$row['full'];
	
	}

	function preAdb($act,$data){
		$this->adb_data=false;

		if(isset($data['sys_files']))
			$this->adb_data=$data['sys_files'];

	}
	
	function postAdb($act,$id,$rdt)
	{
		if($id==false)
			return false;
			if($act=="d"){
				foreach($rdt as $d)
					$this->onParentDel($d['id']);
			}
			else
				$this->manage($act,$id);

			if($act=="i" && is_array($this->adb_data))
			{
				foreach($this->adb_data['new_id'] as $k=>$v)
					$this->db->query("update {$this->p->p->db_prefix}sys_files set tid={$id},isnew=0 where isnew=$v ");
			}
			
			$this->orphanNewDel();

	}

	static function langUpdate($db,$prefix,$langs)
	{
		$d=array();
		$db->query("select * from {$prefix}sys_files","sys_fs_lang_upd");	
		while($row=$db->next("sys_fs_lang_upd")){
			if($row['lid']==0){
				$d[$row['lang']][$row['id']]=$row;
				$dl=$row['lang'];
			}else
				$d[$row['lang']][$row['lid']]=$row;
		}
		if(count($d)>0){
			foreach($d[$dl] as $k=>$v)
			{
				foreach($langs as $l)
					if(!isset($d[$l][$k]))			
						$db->query("insert into {$prefix}sys_files (tbl,tid,full,name,ext,mime,ftype,dir,lang,lid)
						values('{$v['tbl']}',{$v['tid']},'{$v['full']}','{$v['name']}','{$v['ext']}','{$v['mime']}','{$v['ftype']}','{$v['dir']}','$l',{$k})");
			}
		}
	}

	function si($tname,$type=false)
	{
		return $this->showImgAdminCtrl($tname,$type);
	}
	function showImgAdminCtrl($tname,$type=false)
	{
		if($type==false){
			$ipath=$this->url.$this->p->cD["sys_files_img_{$tname}"];
		}
		else if($type=="thumb"){
			$ipath=$this->url."t".$this->p->cD["sys_files_img_{$tname}"];
			$type="_".$type;
		}else{
			$ipath=$this->url."{$type}_".$this->p->cD["sys_files_img_{$tname}"];
			$type="_".$type;
		}


		$r="<img class='sys_files_img_{$tname}_{$this->p->cD['id']}{$type}' src='".$ipath."?".time()."' type='text' ".(is_array($this->types[$tname]['admShow'])?"'width'='{$this->types[$tname]['w']}px' 'height'='{$this->types[$tname]['h']}px'":"").">";
		if($this->p->gO('echo')==true)
			echo $r;
		else
			return $r;		
	}

	function getImgPath($tname,$type=false)
	{
		if($type==false){
			$ipath=$this->url.$this->p->cD["sys_files_img_{$tname}"];
		}
		else if($type=="thumb"){
			$ipath=$this->url."t".$this->p->cD["sys_files_img_{$tname}"];
			$type="_".$type;
		}else{
			$ipath=$this->url."{$type}_".$this->p->cD["sys_files_img_{$tname}"];
			$type="_".$type;
		}


		$r=$ipath."?".time();
		if($this->p->gO('echo')==true)
			echo $r;
		else
			return $r;		
	}

}



?>
