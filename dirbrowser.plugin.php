<?php
ini_set("display_errors",1);
session_start();
//$_REQUEST['file']=stripslashes($_REQUEST['file']);
if(!isset($_SESSION['class_admin']['isAdmin']) || $_SESSION['class_admin']['isAdmin']!==true)
	die("Access denied");

global $tw,$th,$css_prefix,$url_path,$subpath,$trim_path,$dbo,$titles;

require_once("classes/common_funcs.php");
require_once("classes/db.class.php");
require_once("classes/init.php");
$dbo=new db($init_db['host'],$init_db['user'],$init_db['pass'],$init_db['db'],$init_db['charset'],"") or die("DB connect error: ".$this->db->getLastError());

$titlesres=$dbo->query("select val from sys_config where name='gallery_titles'");
$titlesrow=$dbo->next();
$titles=unserialize($titlesrow['val']);
if(!$titles)
	$titles=array();


if(isset($_REQUEST['draw_empty']) && $_REQUEST['draw_empty']==1 )
{
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


$url_path="/gallery";
$trim_path="../gallery";
$path=rpath($url_path);

$home_tit="GALLERY";

if(!isset($_REQUEST['spath']))
	$subpath="/";
else
	$subpath=$_REQUEST['spath'];



$root_path=url("/");

$css_prefix="dirbrows_";

$tw=100;
$th=100;
if(isset($_REQUEST['ajax']) && $_REQUEST['ajax']==1 )
{
	if(isset($_REQUEST['a']) && $_REQUEST['a']=="add" && isset($_REQUEST['dir']) && $_REQUEST['dir']!=""){
		$tpp=$path.$subpath.$_REQUEST['dir'];
		mkdir($tpp);
		if(is_dir($tpp)){
		drawItem($_REQUEST['dir'],false,$subpath.$_REQUEST['dir']);
		}


		die();
	}else if(isset($_REQUEST['a']) && $_REQUEST['a']=="rename" && isset($_REQUEST['old']) && $_REQUEST['old']!="" && isset($_REQUEST['name']) && $_REQUEST['name']!=""){
		$tpp=$path.$subpath.$_REQUEST['old'];
		$rret="OK";
		if(is_dir($tpp) || is_file($tpp)){
			if(!rename($tpp,$path.$subpath.$_REQUEST['name']))
				$rret="Rename failed! May be same name already exists?";
		}else
			$rret="Item with such name not exists or not file or directory";
		die('{"name":"'.$_REQUEST['name'].'","ret":"'.$rret.'"}');
	}else	if(isset($_REQUEST['a']) && $_REQUEST['a']=="add" && $_FILES['img']['tmp_name']!=""){
		copy($_FILES['img']['tmp_name'],$path.$subpath.$_FILES['img']['name']);
			drawItem($_FILES['img']['name'],url($url_path.$subpath.$_FILES['img']['name']));
		die();
	}
	else if($_REQUEST['del']!="" && isset($_REQUEST['dir']) && $_REQUEST['dir']==1)
	{
		$rret="OK";
		if(is_dir($path.$subpath.$_REQUEST['del'])){
			if(!@rmdir($path.$subpath.$_REQUEST['del']))
				$rret="Directory not empty, blocked by another resource or access denied.";
		$cdir=scandir($path.$subpath);
		if(count($cdir)<=2)
			$rret="LAST";
		}else
			$rret="Directory '{$path}{$subpath}{$_REQUEST['del']}' not found";

		die('{"imgid":"'.$_REQUEST['del'].'","ret":"'.$rret.'"}');
	}
	else if($_REQUEST['del']!="")
	{
		$rret="OK";
		unlink($path.$subpath.$_REQUEST['del']);
		$cdir=scandir($path.$subpath);
		if(count($cdir)<=2)
			$rret="LAST";

		unset($titles[$subpath.$_REQUEST['del']]);
		$dbo->query("update sys_config set val='".($dbo->escape(serialize($titles)))."' where name='gallery_titles'");

		die('{"imgid":"'.$_REQUEST['del'].'","ret":"'.$rret.'"}');
	}
else if(isset($_REQUEST['a']) && $_REQUEST['a']=="title" && isset($_REQUEST['title']) && $_REQUEST['title']!="" && isset($_REQUEST['id']) && $_REQUEST['id']!=""){

	$titles[$subpath.$_REQUEST['id']]=$_REQUEST['title'];
	$dbo->query("update sys_config set val='".($dbo->escape(serialize($titles)))."' where name='gallery_titles'");
	die("OK");

}
die("OK");
}

function drawGallery($path,$subpath){
	global $tw,$th,$css_prefix,$url_path;
	$ret="";
	$id=0;
	$hd=false;
	$cdir=scandir($path.$subpath);
	foreach($cdir as $d){
		if($d=="." || $d=="..")
			continue;
		$hd=true;
//echo $d."<hr>";
		$src=$path.$subpath.$d;
		$srcurl=url("{$url_path}{$subpath}{$d}");
		if(is_file($src))
			drawItem($d,$srcurl);
		else if(is_dir($src))
			drawItem($d,$srcurl,$subpath.$d."/");

	}

	if(!$hd){
		drawItem(0,0,0,true);
	}

}


function drawItem($id,$src,$srcsub=false,$iempty=false)
{ 
	global $tw,$th,$css_prefix,$subpath,$trim_path,$titles;
	$tadd=time();

if($iempty==false){
	if($srcsub!=false){
		$src="css/dirbrowser/dir_img.png";
	}
?>
<li>
<a class="thumb" name="<?php echo $id?>" href="<?php echo "$src";?>" title="<?php echo $id?>" >
<img src="<?php echo "$src";?>" alt="<?php echo $id?>" <?php echo ($srcsub?"ondblclick=\"location.href='".url("/admin/dirbrowser.plugin.php?spath=$srcsub")."'\"":"");?> />
<div id='<?php echo $id?>_itemname'><?php echo $id;?></div>
</a>
<div class="caption">
<?if($srcsub!=false){?>
<input type="button" value="OPEN" onclick="location.href='<?php echo url("/admin/dirbrowser.plugin.php?spath=$srcsub")?>';"/>
<input type='text' id="<?php echo $id?>_newname" value="<?php echo $id?>">
<input type="button" value="Rename" onclick="
$.ajax({
url: 'dirbrowser.plugin.php',
data:'ajax=1&a=rename&name='+$('#<?php echo $id?>_newname').val()+'&<?php echo "&spath=".urlencode($subpath).($srcsub?"&old={$id}":"");?>',
success: function(d,m,x){
d=$.parseJSON(d);
if(d.ret=='OK'){
$('#<?php echo $id?>_itemname').html(d.name);
}else
	alert(d.ret);
}
});
">
<?php }else{ ?>
<input type='button' value="Title" onclick="$('#<?php echo str_replace(".","_",$id)?>_dlg').dialog({modal:true})">
<div style="display:none;" class="hidden" id="<?php echo str_replace(".","_",$id)?>_dlg">
<textarea id="<?php echo str_replace(".","_",$id)?>_tit"><?php echo $titles[$subpath.$id];?></textarea>
<br>
<input type="button" value="Save" onclick="
$.ajax({
type:'POST',
url: 'dirbrowser.plugin.php',
data:'a=title&ajax=1&id=<?php echo "{$id}&spath=".urlencode($subpath);?>&title='+$('#<?php echo str_replace(".","_",$id)?>_tit').val(),
});
$('#<?php echo str_replace(".","_",$id)?>_dlg').dialog('destroy');
">
</div>
<?php } ?>
<input type="button" value="Delete <?php echo ($srcsub?"folder":"file")?>" onclick="
$.ajax({
url: 'dirbrowser.plugin.php',
data:'ajax=1&del=<?php echo "{$id}&spath=".urlencode($subpath).($srcsub?"&dir=1":"");?>',
success: function(d,m,x){
d=$.parseJSON(d);
if(d.ret=='OK'){
<?php echo $css_prefix?>gallery.removeImageByHash('<?php echo $id;?>')
<?php echo $css_prefix?>gallery.previous(false,true);
}
else if(d.ret=='LAST'){
dirbu_plugin_lastimg();
<?php echo $css_prefix?>gallery.removeImageByHash('<?php echo $id;?>')
<?php echo $css_prefix?>gallery.previous(false,true);
}else
	alert(d.ret);
}
});
"> <?php if(!$srcsub) { ?>
| <input type="button" value="Trim" onclick="trim_plugin('<?php echo "{$trim_path}{$subpath}{$id}";?>',false,false,false,false,'#<?php echo $css_prefix?>slideshow img','#<?php echo $css_prefix?>thumbs .selected img')"> 
<?php } ?>
</div>
</li>

					<?php 						}else{  ?>

<li>
<a class="thumb <?php echo $css_prefix?>gallery_empty" name="this-plugin-empty.gif" title="" href="./dirbrowser.plugin.php?draw_empty=1">
<img src="./dirbrowser.plugin.php?draw_empty=1" alt="" />
</a>
<div class="caption">
</div>
</li>

<?php }
 }

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>File browser/uploader</title>
<style type="text/css">

#<?php echo $css_prefix?>thumbs {
	float:left;
	width:300px;
}

#<?php echo $css_prefix?>thumbs .thumbs li {
	list-style:none;
}

#<?php echo $css_prefix?>thumbs .thumbs li img {
	width:100px;
	height:100px;
}

#<?php echo $css_prefix?>gallery  {
	float:left;
	width:350px;
}



</style>

<link href="css/galleriffic-2.css" rel="stylesheet" type="text/css">
<link href="css/sunny/sunny.css" rel="stylesheet" type="text/css">

<script type="text/javascript" src="<?php echo $root_path?>js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $root_path?>js/jquery.galleriffic.js"></script>
<script type="text/javascript" src="<?php echo $root_path?>js/jquery.opacityrollover.js"></script>
<script type="text/javascript" src="<?php echo $root_path?>js/jup.1.0.1.js"></script>
<script type="text/javascript" src="js/jquery-ui.min.js"></script>
<script type="text/javascript" src="trim.plugin.js"></script>
<script type="text/javascript" src="<?php echo $root_path?>js/jquery.validate.min.js"></script>
<script type="text/javascript">
$(document).ready(function (){
	$('#<?php echo $css_prefix?>idform').validate();
})
	</script>

<script type="text/javascript">

function dirbu_plugin_lastimg()
{
	<?php echo $css_prefix?>gallery.insertImage('<li> \
		<a class="thumb <?php echo $css_prefix?>gallery_empty" name="this-plugin-empty.gif" title="" href="./dirbrowser.plugin.php?draw_empty=1"> \
		<img src="./dirbrowser.plugin.php?draw_empty=1" alt="" /> \
		</a>  \
		<div class="caption"> \
		</div> \
		</li>',0);
}



var <?php echo $css_prefix?>gallery=false;
$(document).ready(function($) {

	// We only want these styles applied when javascript is enabled
	$('div.navigation').css({'width' : '300px', 'float' : 'left'});
	$('div.gallery_content').css('display', 'block');
	// Initially set opacity on thumbs and add
	// additional styling for hover effect on thumbs
	var onMouseOutOpacity = 0.67;
	<?php if(true || $row['imgs']>0){?>
	$('#<?php echo $css_prefix?>thumbs ul.thumbs li').opacityrollover({
		mouseOutOpacity:   onMouseOutOpacity,
			mouseOverOpacity:  1.0,
			fadeSpeed:         'fast',
			exemptionSelector: '.selected'
	});
	// Initialize Advanced Galleriffic Gallery
	<?php echo $css_prefix?>gallery = $('#<?php echo $css_prefix?>thumbs').galleriffic({
		enableKeyboardNavigation: false,
			delay:                     2500,
			numThumbs:                 15,
			preloadAhead:              10,
			enableTopPager:            true,
			enableBottomPager:         true,
			maxPagesToShow:            7,
			imageContainerSel:         '#<?php echo $css_prefix?>slideshow',
			controlsContainerSel:      '#<?php echo $css_prefix?>controls',
			captionContainerSel:       '#<?php echo $css_prefix?>caption',
			loadingContainerSel:       '#<?php echo $css_prefix?>loading',
			renderSSControls:          true,
			renderNavControls:         true,
			playLinkText:              'Play Slideshow',
			pauseLinkText:             'Pause Slideshow',
			prevLinkText:              '&lsaquo; Previous Photo',
			nextLinkText:              'Next Photo &rsaquo;',
			nextPageLinkText:          'Next &rsaquo;',
			prevPageLinkText:          '&lsaquo; Prev',
			enableHistory:             false,
			autoStart:                 false,
			syncTransitions:           true,
defaultTransitionDuration: 900,
onSlideChange:             function(prevIndex, nextIndex) {
	// 'this' refers to the gallery, which is an extension of $('#thumbs')
	this.find('ul.thumbs').children()
		.eq(prevIndex).fadeTo('fast', onMouseOutOpacity).end()
		.eq(nextIndex).fadeTo('fast', 1.0);
},
	onPageTransitionOut:       function(callback) {
		this.fadeTo('fast', 0.0, callback);
	},
		onPageTransitionIn:        function() {
			this.fadeTo('fast', 1.0);
		}
	});
	<?php }?>

	$("#<?php echo $css_prefix?>ajax_gall").jup({
		json:false,
		onComplete : function(resp, formId){
			//assuming JSON
			if( resp == false ){
				alert("Empty or corrupted responce.");
				//jup didn't receive valid JSON response
			}else{
				<?php echo $css_prefix?>gallery.insertImage(resp,0);
				if($('.<?php echo $css_prefix?>gallery_empty').html())
				{
					<?php echo $css_prefix?>gallery.removeImageByHash("this-plugin-empty.gif");
				}

			}
		}
	});


});
</script>

</head>
<body>

<?php if(true || $row['imgs']>0){?>
<div class='dpath'>
<a href='dirbrowser.plugin.php'><?php echo $home_tit?></a>
<?php
$pa=explode("/",$subpath);
$tp="/";
foreach($pa as $v){	
	if($v==false)
		continue;
	$tp.="$v/";

?>
 / <a href='dirbrowser.plugin.php?spath=<?php echo $tp; ?>'><?php echo $v?></a>
<?php
}
?>
</div>
<hr>
<div>
Add <?php echo ($subpath=="/"?"folder":"image")?>:
<form action='dirbrowser.plugin.php?ajax=1' method="POST" id="<?php echo $css_prefix?>ajax_gall" style="display:inline">
<input type="hidden" name="a" value="add">
<input type="hidden" name="spath" value="<?php echo $subpath?>">
<?php if($subpath=="/"){?>
<input type="text" name="dir">
<input type="submit" value="Add folder">
<?php }else {?>
<input type="file" name="img">
<input type="submit" value="Add image">
<?php }?>
</form>

</div>

<div id="<?php echo $css_prefix?>thumbs" class="navigation">
<ul class="thumbs noscript">
<?php
echo drawGallery($path,$subpath);?>
</ul>
</div>

<div id="<?php echo $css_prefix?>gallery" class="gallery_content">
<div id="<?php echo $css_prefix?>controls" class="controls"></div>
<hr/>
<div id="<?php echo $css_prefix?>caption" class="caption-container"></div>

	<div class="<?php echo $css_prefix?>slideshow-container">
		<div id="<?php echo $css_prefix?>loading" class="loader"></div>
		<div id="<?php echo $css_prefix?>slideshow" class="slideshow"></div>
	</div>

</div>

<?php }else {?>
	<center>
<h2>Add first image to gallery: </h2>
<form action='dirbrowser.plugin.php' method="POST" style="display:inline" enctype="multipart/form-data">
<input type="hidden" name="a" value="p_adb">
<input type="hidden" name="redirect" value="a=p_a_edit&cid=<?php echo $row['id']?>">
<input type="hidden" name="data[r.article]" value="<?php echo $row['id']?>">
Title: <input name="data[c.title]"> <input type="file" name="img">
<input type="hidden" name="act_i_gallery" value="1">
<input type="submit" value="Add image">
</form>
</center>
<?php }?>
</body>
</html>
