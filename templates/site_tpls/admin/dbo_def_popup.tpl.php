<!DOCTYPE html ">
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=8" >
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<title><?php echo $this->webtitle;?></title>

<link href="<?php echo $this->skin;?>css/main.css" rel="stylesheet" type="text/css">
<link href="<?php echo $this->skin;?>css/ui-lightness/ui.css" rel="stylesheet" type="text/css">


<script src="./js/jquery.min.js" type="text/javascript"></script>
<script src="./js/jquery-ui.min.js" type="text/javascript"></script>


<link href="<?php echo $this->skin;?>css/admin.css" rel="stylesheet" type="text/css">
<link href="<?php echo $this->skin;?>css/ui-lightness/ui.css" rel="stylesheet" type="text/css">
<!-- <link href="<?php echo $this->skin;?>css/farbtastic/farbtastic.css" type="text/css" rel="stylesheet"/> -->
<link href="<?php echo $this->skin;?>css/fancybox/fancybox.css" media="all" rel="stylesheet" type="text/css">

<script src="js/jquery.min.js" type="text/javascript"></script>
<script src="js/jquery.mousewheel.js" type="text/javascript"></script>
<script src="js/jquery.fancybox-1.3.4.pack.js" type="text/javascript"></script>

<?php global $def_editor;
if($def_editor == 'ckeditor'){?>
	<script src="js/ckeditor/ckeditor.js" type="text/javascript"></script>
<?php }else{/*?>
<script src="./js/tiny_mce/jquery.tinymce.js" type="text/javascript"></script>
	<?php */?>
<script src="js/tiny_mce/tiny_mce.js" type="text/javascript"></script>
	
<?php }?>
<script src="js/jquery-ui.min.js" type="text/javascript"></script>
<script src="js/farbtastic.js" type="text/javascript" ></script>

<?php sys_files::addTrimDlgJS();?>

<script type="text/javascript">


tinymce.create('tinymce.plugins.lineheight', {
    'createControl' : function (n, cm) {
        switch (n) {
            case 'lineheight' :
            var ed = tinymce.activeEditor;
            var c = cm.createListBox('lineheight', {
                title : 'Line Height',
                onselect : function(v) {
                    ed.formatter.apply('LHT' + String(v)); // apply the selected format (line height)
                    return false;
                }
            });
            if (!ed.settings.formats) { // if no formats defined, create the object
                ed.settings.formats = {};
            }
            for (var h = 3; h <= 30; h += 1) { // edit the 50 -> 200 range if you want
                ed.settings.formats['LHT' + String(h)] = { // dynamically generate new formats
                    'block': 'p',
                    'styles': {
                        'lineHeight' : String(h) + 'px'
                    }
                }
                c.add(String(h) + 'px', h); // ...and add them to the menu
            };
            return c;
        }
    }
});

// register our custom plugin
tinymce.PluginManager.add('lineheight', tinymce.plugins.lineheight);


<?php if($def_editor == 'tinymce'){?>
	
	function devlFileBrowser (field_name, url, type, win) {
    tinyMCE.activeEditor.windowManager.open({
        file : '<?php echo aurl("/mcebrowser.plugin.php?type=")?>'+type,
        title : 'File Browser/Uploader',
        width : 800,  // Your dimensions may differ - toy around with them!
        height : 600,
        resizable : "yes",
        inline : "yes",  // This parameter only has an effect if you use the inlinepopups plugin!
        close_previous : "no"
    }, {
        window : win,
        input : field_name
    });
    return false;
  }
<?php }?>

function lang_btn_cur(ithis)
{
p=$(ithis).parent();
p.find('.lang_btn.cl').removeClass('cl');
$(ithis).addClass('cl');
}

function lang_btn_reinit(){
	$('.lang_btn').each(function(i){
		if($(this).attr('lang_btn_set')!=1){
			$(this).bind('click',function(){
				lang_btn_cur(this);
			});
		$(this).attr('lang_btn_set',1);
		}
	});

}

function controls_init(){
	$( ".dbo_date" ).datepicker({dateFormat: 'dd/mm/yy',changeMonth: true,changeYear: true	});
	$( "a.add_button_a" ).fancybox();
	lang_btn_reinit();	
<?php if($def_editor == 'tinymce'){?>
	 tinyMCE.init({
				mode : "specific_textareas",
        editor_selector : "mce_editor",
				theme : "advanced",
				plugins : '-lineheight,iframe',
				theme_advanced_buttons4:"fontselect,fontsizeselect,lineheight,forecolor,backcolor,iframe",
				file_browser_callback : 'devlFileBrowser',
				theme_advanced_font_sizes:'10px,11px,12px,13px,14px,15px,16px,18px,20px,22px,24px,26px,28px,30px,36px,40px,56px,80px',
				content_css:'css/editor_css.css',
				extended_valid_elements : "div[align|class|style|id|title]",
				extended_valid_elements : "iframe[name|src|framespacing|border|frameborder|scrolling|title|height|width],object[declare|classid|codebase|data|type|codetype|archive|standby|height|width|usemap|name|tabindex|align|border|hspace|vspace]"
		
			});

/*
	 	$('textarea.mce_editor').tinymce({
      script_url : 'js/tiny_mce/tiny_mce.js',
				theme : "advanced",
				theme_advanced_buttons4:"fontselect,fontsizeselect,forecolor,backcolor",
				file_browser_callback : 'devlFileBrowser',
				content_css:'css/editor_css.css'
});
 */

<?php }?>

}

$(document).ready(function() {
	controls_init();
	});

</script>


<script>
$(document).ready(function() {

	$('form').each(function(i){
		$(this).append("<input type='hidden' name='popup' value='1' >");
<?php if ($this->gO('_ucd')) {
    $_ucd = $this->gO('_ucd');
    ?>
    $(this).append("<input type = 'hidden' name = '_ucdo' value = '<?php echo $_ucd['o']; ?>' >");
    $(this).append("<input type = 'hidden' name = '_ucdc' value = '<?php echo $_ucd['c']; ?>' >");
<?php } ?>
});
$('a').each(function(i,el){
$(this).attr('href',$(this).attr('href')+'&popup=1<?php echo $this->gO('_ucdu'); ?>');
});

$.get('index.php?a=dbo_<?php echo $this->oname ?>&f=ajaxSel<?php echo $this->gO('_ucdu'); ?>','',function(d,s,x){

<?php if ($this->gO('_ucd')){
    $_ucd = $this->gO('_ucd');
    ?>
    n=jQuery(d);

    $('span.dbo_<?php echo $_ucd['o'] ?>_ctr_group_<?php echo $_ucd['c'] ?>',window.top.document).each(function(i){
    jQuery(this).find("input").each(function(i){
    if(jQuery(this).is(':checked')){
    n.find("input[name='"+jQuery(this).attr("name")+"']").attr("checked","true");
    }
    });
    jQuery(this).find("select").each(function(i){
    nn=n.find("select[name='"+jQuery(this).attr("name")+"']");
    if(nn.length>0)
    nn.val(jQuery(this).val());
    });
    jQuery(this).html(n.html());

    });

<?php }else{ ?>

$('.dbo_<?php echo $this->oname ?>_e_sel',window.top.document).each(function(i){
v=jQuery(this).val();
jQuery(this).html("
<option value = '' >-- Choisir --</options>"+d);
    jQuery(this).val(v);

    });
    <?php } ?>


    });


    $( ".dbo_date" ).datepicker({dateFormat: 'dd/mm/yy'});
    });
    </script>

    </
    head >

    < body >

    <?php		require_once("{$this->p->TEMPL}/admin/dbo_{$templ}_{$this->copts['adminOnlineEdit']}.tpl.php"); ?>

    < / body >
    < / html >
