<?php
session_start();
//$_REQUEST['file']=stripslashes($_REQUEST['file']);
if(!isset($_SESSION['class_admin']['isAdmin']) || $_SESSION['class_admin']['isAdmin']!==true)
	die("Access denied");

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

global $trim_args;

ini_set("display_errors",1);
$path="./files/imgs";
$root_path="../";

$css_prefix="mcebu_";

$trim_args=array("nw"=>"false","nh"=>"false","tw"=>"false","th"=>"false");
$trim_args['nw']=400;


$tw=100;
$th=100;
if(isset($_REQUEST['ajax']) && $_REQUEST['ajax']==1 )
{
	if(isset($_REQUEST['a']) && $_REQUEST['a']=="add" && $_FILES['img']['tmp_name']!=""){
		copy($_FILES['img']['tmp_name'],"$path/{$_FILES['img']['name']}");
		$fpath="$path/{$_FILES['img']['name']}";

		list($w, $h) = getimagesize($fpath);
		$ar=$w/$h;

		$size=array("w"=>400,"h"=>400/$ar);

/*		$image = imagecreatefromstring(file_get_contents($fpath));
		$image_resized = imagecreatetruecolor($size['w'], $size['h']);
		imagecopyresampled($image_resized, $image, 0,0,0,0, $size['w'], $size['h'], $w, $h);
		imagejpeg($image_resized,$fpath,100);
 */
		die('{"img":"'.$_FILES['img']['name'].'","imgid":"'.$_FILES['img']['name'].'"}');
	}else if($_REQUEST['del']!="")
	{
		$rret="OK";
		unlink("$path/{$_REQUEST['del']}");
		$cdir=scandir($path);
		if(count($cdir)<=2)
			$rret="LAST";

		die('{"imgid":"'.$_REQUEST['del'].'","ret":"'.$rret.'"}');
	}
}

function drawGallery($path){
	global $tw,$th,$css_prefix, $trim_args;
	$ret="";
	$id=0;

	$cdir=scandir($path);
	foreach($cdir as $d){
		if($d=="." || $d==".." || !is_file("$path/$d"))
			continue;
		$id=$d;
		ob_start();
?>


<?php $tadd=time();?>
<li>
<a class="thumb" name="<?php echo $id?>" href="<?php echo "$path/$d";?>" title="<?php echo $id?>" >
<img src="<?php echo "$path/$d";?>" alt="<?php echo $id?>" />
</a>
<div class="caption">
<input type="button" value="SELECT" onclick="<?php echo $css_prefix?>_fckbuplugin_sel('<?php echo $d?>');"/>
<input type="button" value="Delete image" onclick="
$.ajax({
url: 'mcebrowser.plugin.php',
data:'ajax=1&del=<?php echo $d;?>',
success: function(d,m,x){
d=$.parseJSON(d);
if(d.ret=='OK'){
<?php echo $css_prefix?>gallery.removeImageByHash('<?php echo $id;?>')
}
else if(d.ret=='LAST'){
fckbu_plugin_lastimg();
<?php echo $css_prefix?>gallery.removeImageByHash('<?php echo $id;?>')
}
<?php echo $css_prefix?>gallery.previous(false,true);
}
});
"> | <input type="button" value="Trim" onclick="trim_plugin('<?php echo "$path/$d";?>',<?php echo $trim_args['nw'];?>,<?php echo $trim_args['nh'];?>,<?php echo $trim_args['tw'];?>,<?php echo $trim_args['th'];?>,'#<?php echo $css_prefix?>slideshow img','#<?php echo $css_prefix?>thumbs .selected img')">
</div>
</li>


<?php
		$ret.=ob_get_contents();
		ob_end_clean();
	}
	if($ret==""){
		ob_start();
?>
<li>
<a class="thumb <?php echo $css_prefix?>gallery_empty" name="this-plugin-empty.gif" title="" href="./mcebrowser.plugin.php?draw_empty=1">
<img src="./mcebrowser.plugin.php?draw_empty=1" alt="" />
</a>
<div class="caption">
</div>
</li>

<?php
		$ret.=ob_get_contents();
		ob_end_clean();
	}
	return $ret;
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
<script type="text/javascript" src="js/tiny_mce/tiny_mce_popup.js"></script>
<script type="text/javascript" src="browsers_trim.plugin.js"></script>
<script type="text/javascript" src="<?php echo $root_path?>js/jquery.validate.min.js"></script>
<script type="text/javascript">


$(document).ready(function (){
	$('#<?php echo $css_prefix?>idform').validate();
})
	</script>

<script type="text/javascript">

function fckbu_plugin_lastimg()
{
	<?php echo $css_prefix?>gallery.insertImage('<li> \
		<a class="thumb <?php echo $css_prefix?>gallery_empty" name="this-plugin-empty.gif" title="" href="./mcebrowser.plugin.php?draw_empty=1"> \
		<img src="./mcebrowser.plugin.php?draw_empty=1" alt="" /> \
		</a>  \
		<div class="caption"> \
		</div> \
		</li>',0);
}

function <?php echo $css_prefix?>_fckbuplugin_sel(fl)
{
	var URL='<?php echo $path?>/'+fl;
	var win = tinyMCEPopup.getWindowArg("window");

	win.document.getElementById(tinyMCEPopup.getWindowArg("input")).value = URL;

        // are we an image browser
        if (typeof(win.ImageDialog) != "undefined") {
            // we are, so update image dimensions...
            if (win.ImageDialog.getImageData)
                win.ImageDialog.getImageData();

            // ... and preview if necessary
            if (win.ImageDialog.showPreviewImage)
                win.ImageDialog.showPreviewImage(URL);
        }

        // close popup window
        tinyMCEPopup.close();

//	window.close();
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
			numThumbs:                 8,
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
		onComplete : function(response, formId){
			//assuming JSON
			if( response == false ){
				//jup didn't receive valid JSON response
			}else{
				<?php echo $css_prefix?>gallery.insertImage('<li>																							\
					<a class="thumb" name="'+response.img+'" href="<?php echo "$path/";?>'+response.img+'" title="'+response.imgid+'">\
					<img src="<?php echo "$path";?>/'+response.img+'" alt="'+response.imgid+'" />\
					</a>																										\
					<div class="caption"> \
					<input type="button" value="SELECT" onclick="<?php echo $css_prefix?>_fckbuplugin_sel(\''+response.imgid+'\');"/> \
					<input type="button" value="Delete image" onclick=" \
					$.ajax({ \
					url: \'mcebrowser.plugin.php\', \
					data:\'ajax=1&del='+response.imgid+'\', \
					success: function(d,m,x){ \
					d=$.parseJSON(d); \
					if(d.ret==\'OK\') \
					{ \
					<?php echo $css_prefix?>gallery.removeImageByHash(d.imgid); \
					} \
					else if(d.ret==\'LAST\') \
					{ \
					fckbu_plugin_lastimg(); \
					<?php echo $css_prefix?>gallery.removeImageByHash(d.imgid); \
					} \
					<?php echo $css_prefix?>gallery.previous(false,true); \
					} \
					}); \
					"> |  <input type="button" value="Trim" onclick="trim_plugin(\'<?php echo $path?>/'+response.img+'\',<?php echo $trim_args['nw'];?>,<?php echo $trim_args['nh'];?>,<?php echo $trim_args['tw'];?>,<?php echo $trim_args['th'];?>,\'#<?php echo $css_prefix?>slideshow img\',\'#<?php echo $css_prefix?>thumbs .selected img\')"> \
					</div> \
					</li>',0);
				if($('.<?php echo $css_prefix?>gallery_empty').html())
				{
					<?php echo $css_prefix?>gallery.removeImageByHash("this-plugin-empty.gif"); 
					<?php echo $css_prefix?>gallery.previous(false,true); 
				}

			}
		}
	});


});
</script>

</head>
<body>

<?php if(true || $row['imgs']>0){?>

<div>
Add image:<form action='mcebrowser.plugin.php?ajax=1' method="POST" id="<?php echo $css_prefix?>ajax_gall" style="display:inline">
<input type="hidden" name="a" value="add">
<input type="file" name="img">
<input type="submit" value="Add image">
</form>

</div>

<div id="<?php echo $css_prefix?>thumbs" class="navigation">
<ul class="thumbs noscript">
<?php
echo drawGallery($path);?>
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
<form action='mcebrowser.plugin.php' method="POST" style="display:inline" enctype="multipart/form-data">
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
