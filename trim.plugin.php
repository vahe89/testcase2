<?php
session_start();
//$_REQUEST['file']=stripslashes($_REQUEST['file']);
if(!isset($_SESSION['class_admin']['isAdmin']) || $_SESSION['class_admin']['isAdmin']!==true)
die("Access denied");


//$_REQUEST['file']="../img/".$_REQUEST['file'];

if(isset($_REQUEST['file']) && is_file($_REQUEST['file'])){
list($width, $height) = getimagesize($_REQUEST['file']);
$aspR=$width/$height;
$ow=$width;
$oh=$height;
$left=0;
$top=0;

if(!isset($_REQUEST['nw']) || !is_numeric($_REQUEST['nw']) || !$_REQUEST['nw']>0){
if(!isset($_REQUEST['nh']) || !is_numeric($_REQUEST['nh']) || !$_REQUEST['nh']>0)
	$_REQUEST['nw']=$ow;
else
	$_REQUEST['nw']=$_REQUEST['nh']*$aspR;
}

if(!isset($_REQUEST['nh']) || !is_numeric($_REQUEST['nh']) || !$_REQUEST['nh']>0){
if(!isset($_REQUEST['nw']) || !is_numeric($_REQUEST['nw']) || !$_REQUEST['nw']>0)
	$_REQUEST['nh']=$oh;
else
	$_REQUEST['nh']=$_REQUEST['nw']/$aspR;
}


if(!isset($_REQUEST['tw']) || !is_numeric($_REQUEST['tw']) || !$_REQUEST['tw']>0)
$_REQUEST['tw']=$ow;
if(!isset($_REQUEST['th']) || !is_numeric($_REQUEST['th']) || !$_REQUEST['th']>0)
$_REQUEST['th']=$oh;

/*$aw=$_REQUEST['nw']/10;
$ah=$_REQUEST['nh']/10;

if($width>=$height)
$unit=ceil($width/$aw);
else
$unit=ceil($height/$ah);

$height=$unit*$ah;
$width=$unit*$aw;

if($oh>$height)
{
$unit=ceil($oh/$ah);
$height=$unit*$ah;
$width=$unit*$aw;
}
else if($ow>$width)
{
$unit=ceil($ow/$aw);
$height=$unit*$ah;
$width=$unit*$aw;
}

if($oh<$height)
	$top=($height-$oh)/2;

if($ow<$width)
$left=($width-$ow)/2;
 */


$nasr=$_REQUEST['nw']/$_REQUEST['nh'];
if($width>=$height){
	$height=ceil($width/$nasr);
}
if($width<$height){
	$width=ceil($height/$nasr);
}


if($oh<$height)
	$top=($height-$oh)/2;

if($ow<$width)
	$left=($width-$ow)/2;

$cwidth=$width;
$cheight=$height;
if($cwidth<$ow)
	$cwidth=$ow;

if($cheight<$oh)
	$cheight=$oh;
?>
<div id="#trim_plugin_content">
<script type="text/javascript">
	
	
function mask()
{
	var cw=$('#container').width();
	var ch=$('#container').height();
	var sel=$('#sel');
	var x1=sel.css("left");
	x=x1.split("p");
	x=Number(x[0]);
	var y1=sel.css("top");
	y=y1.split("p");
	y=Number(y[0]);
	var x2=Number(sel.width())+x;
	var y2=Number(sel.height())+y;

	$('.vmask.pre').css("width",x1);
	$('.vmask.aft').css("width",(cw-x2-4));
	$('.hmask.pre').css("height",y1);
	$('.hmask.aft').css("height",(ch-y2-4));
	$('.mask').show();
	$('#sel').css("background","transparent");
}
function umask()
{
	$('#sel').css("background","#5555ff");
	$('.mask').hide();

}

function toggleMask()
{
if($('#msk').val()=='Mask')
{
	$('#sel').bind('drag',function(event, ui) {setVals();mask();});
	$('#sel').bind('dragstop',function(event, ui) {setVals();mask();});
	$('#sel').bind('resize',function(event, ui) {setVals();mask();});
	$('#sel').bind('resizestop',function(event, ui) {setVals();mask();});
	$('#msk').val('Unmask');mask()
}else{
	$('#msk').val('Mask');

	$('#sel').unbind('drag dragstop resizestop resize');
	$('#sel').bind('drag',function(event, ui) {setVals();});
	$('#sel').bind('dragstop',function(event, ui) {setVals();});
	$('#sel').bind('resize',function(event, ui) {setVals();});
	$('#sel').bind('resizestop',function(event, ui) {setVals();});

	/*	$('#sel').unbind('drag');
	$('#sel').unbind('stop');*/
	umask();
}

}

function setVals()
{  
var sel=$('#sel');
var x1=sel.css("left");
x=x1.split("p");
x=Number(x[0]);
if(isNaN(x))
	x=0
var y1=sel.css("top");
y=y1.split("p");
y=Number(y[0]);
if(isNaN(y))
	y=0

$('#dx').html(x);
$('#dy').html(y);
$('#dw').html(sel.width());
$('#dh').html(sel.height());
}

function vPos()
{
var sel=$('#sel');
sel.css("left",(($('#container').width()-sel.width())/2)+"px");
if($('#msk').val()=="Unmask")
	mask();
}

function hPos()
{
var sel=$('#sel');
sel.css("top",(($('#container').height()-sel.height())/2)+"px");
if($('#msk').val()=="Unmask")
	mask();
}

function trim_sub()
{
var ow=<?php echo $ow;?>;
var oh=<?php echo $oh;?>;
var sel=$('#sel');
var x1=sel.css("left");
x=x1.split("p");
x=Number(x[0]);
var y1=sel.css("top");
y=y1.split("p");
y=Number(y[0]);
var sx=x;
var sy=y;
var sw=sel.width();
var sh=sel.height();
$('#ix').val(x);
$('#iy').val(y);
$('#iw').val(sw);
$('#ih').val(sh);
$('#iq').val($( "#quality" ).slider( "option", "value" ));
var sel=$('#timg');
var x1=sel.css("left");
x=x1.split("p");
x=Number(x[0]);
var y1=sel.css("top");
y=y1.split("p");
y=Number(y[0]);
if(sx<x){
$('#il').val(x-sx);
$('#ix').val(0);
}
else if(sx>=x && sx<=(x+ow))
$('#ix').val(sx-x);
else
$('#il').val(0);

if(sy<y){
$('#it').val(y-sy);
$('#iy').val(0);
}
else if(sy>=y && sy<=(y+oh))
$('#iy').val(sy-y);
else
$('#it').val(0);

if($('#nw').val()==ow)
$('#nw').val(sw);
if($('#nh').val()==oh)
$('#nh').val(sh);

if($('#tw').val()==ow)
$('#tw').val(sw);
if($('#th').val()==oh)
$('#th').val(sh);

return true;
}

</script>
<style>
.mask {position:absolute;background:grey;display:none;}
.vmask {width:0px;height:100%;}
.hmask {width:100%;height:0;}
.pre {top:0px;left:0px;}
.aft {bottom:0px;right:0px;}
</style>

<div style="margin-bottom:5px;padding-bottom:3px;border:2px outset black;">

<form id="trim_plugin_form" action="index.php" method="POST" onsubmit="return trim_sub()">
<input type="hidden" name="a" value="p_trim">
<input id="ix" type="hidden" name="x" value="0">
<input id="iy" type="hidden" name="y" value="0">
<input id="iw" type="hidden" name="w" value="<?php echo $ow?>">
<input id="ih" type="hidden" name="h" value="<?php echo $oh?>">
<input id="iq" type="hidden" name="q" value="100">
<input id="it" type="hidden" name="t" value="0">
<input id="il" type="hidden" name="l" value="0">
<input id="nw" type="hidden" name="nw" value="<?php echo $_REQUEST['nw'];?>">
<input id="nh" type="hidden" name="nh" value="<?php echo $_REQUEST['nh'];?>">
<input id="tw" type="hidden" name="tw" value="<?php echo $_REQUEST['tw'];?>">
<input id="th" type="hidden" name="th" value="<?php echo $_REQUEST['th'];?>">
<input id="ri" type="hidden" name="ri" value="<?php echo $_REQUEST['ri'];?>">
<input id="rt" type="hidden" name="rt" value="<?php echo $_REQUEST['rt'];?>">
<?php if(isset($_REQUEST['cps']) && is_array($_REQUEST['cps'])){
	foreach($_REQUEST['cps'] as $k=>$v){?>
<input type="hidden" name="cps[<?php echo $k;?>][w]" value="<?php echo $v['w'];?>">
<input type="hidden" name="cps[<?php echo $k;?>][h]" value="<?php echo $v['h'];?>">
<?php	}
}
?>
<input type="hidden" name="fname" value="<?php echo $_REQUEST['file']?>">

<table border=0 cellpadding=0 cellspacing=0 width=100% align="middle">
<tr><td align='center' width="20%">
<input id="msk" value="Mask" type="button" onclick="toggleMask();"><br/><input type="button" value="V-Center" onclick="vPos()"><input type="button" value="H-Center" onclick="hPos()">
</td><td align='center'>
Image quality:<span id="q">100</span><div id="quality" style="width:400px;"></div>
</td><td align='center' width="20%">
<input type="submit" value="TRIM AND SAVE">
</td></tr>
</table>
</form>
</div>
<center><p>
X:<span id="dx"></span> Y:<span id="dy"></span> Width:<span id="dw"></span>Height:<span id="dh"></span>
</p></center>


<div id="container" style="margin:auto;position:relative;width:<?php echo $cwidth?>px;height:<?php echo $cheight?>px;background:white;">
<img id='timg' src="<?php echo $_REQUEST['file']."?".time()?>" style="position:absolute;left:<?php echo $left;?>px;top:<?php echo $top;?>px;">
<div class="mask vmask pre"></div>
<div class="mask hmask pre"></div>
<div id="sel" style="float:left;width:<?php echo $width;?>px;height:<?php echo $height;?>px;background:#5555ff;opacity:0.5;filter: alpha(opacity=50);"></div>
<div class="mask vmask aft"></div>
<div class="mask hmask aft"></div>
</div>

<?php } else {?>
	File not found <?php echo $_REQUEST['file']; }?>

