<!DOCTYPE html>
<html >
<head >
    <meta http-equiv = "X-UA-Compatible" content = "IE=8" >
    <meta http-equiv = "Content-Type" content = "text/html; charset=UTF-8" >
    <base href = "<?php echo aurl("/"); ?>" >

    <title ><?php echo $this->webtitle[$this->def_lang]; ?></title >

    <link href = "js/data_tables/css/jquery.dataTables.css" rel = "stylesheet" type = "text/css" >
    <link href = "<?php echo $this->skin; ?>css/wHumanMsg.min.css" rel = "stylesheet" type = "text/css" >

    <link href = "<?php echo $this->skin; ?>css/admin.css" rel = "stylesheet" type = "text/css" >
    <link href = "<?php echo $this->skin; ?>css/ui-lightness/ui.css" rel = "stylesheet" type = "text/css" >
    <!-- <link href="<?php echo $this->skin; ?>css/farbtastic/farbtastic.css" type="text/css" rel="stylesheet" /> -->
    <link href = "<?php echo $this->skin; ?>css/fancybox/fancybox.css" media = "all" rel = "stylesheet" type = "text/css" >

    <script src = "js/jquery.min.js" type = "text/javascript" ></script>
    <script src = "js/jquery.mousewheel.js" type = "text/javascript" ></script>
    <script src = "js/jquery.fancybox-1.3.4.pack.js" type = "text/javascript" ></script>
    <link href = "js/select2/select2.min.css" rel = "stylesheet"/>
    <script src = "js/select2/select2.min.js"></script>
    <script src = "js/data_tables/jquery.dataTables.min.js" type = "text/javascript" ></script>

    <script src = "js/jup.1.0.1.js" type = "text/javascript" ></script>


    <?php global $def_editor;
    if ($def_editor == 'ckeditor') {
        ?>
        <script src = "js/ckeditor/ckeditor.js" type = "text/javascript" ></script>
    <?php }else{/*?>
<script src="js/tiny_mce/jquery.tinymce.js" type="text/javascript"></script>
	<?php */
    ?>
        <script src = "js/tiny_mce/tiny_mce.js" type = "text/javascript" ></script>

    <?php } ?>
    <script src = "js/jquery-ui.min.js" type = "text/javascript" ></script>
    <script type = "text/javascript" src = "js/farbtastic.js" ></script>

		<script src = "js/jquery-ui-timepicker-addon.js" type = "text/javascript" ></script>
		<script src = "js/jquery-ui-sliderAccess.js" type = "text/javascript" ></script>

		<link href="js/timepicker/jquery-ui-timepicker-addon.css" rel="stylesheet" type="text/css" />

    <?php sys_files::addTrimDlgJS(); ?>

    <script type = "text/javascript" >


        tinymce.create('tinymce.plugins.lineheight', {
            'createControl': function (n, cm) {
                switch (n) {
                    case 'lineheight' :
                        var ed = tinymce.activeEditor;
                        var c = cm.createListBox('lineheight', {
                            title: 'Line Height',
                            onselect: function (v) {
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
                                    'lineHeight': String(h) + 'px'
                                }
                            }
                            c.add(String(h) + 'px', h); // ...and add them to the menu
                        }
                        ;
                        return c;
                }
            }
        });

        // register our custom plugin
        tinymce.PluginManager.add('lineheight', tinymce.plugins.lineheight);


        <?php if($def_editor=='tinymce'){?>

        function devlFileBrowser(field_name, url, type, win) {
            tinyMCE.activeEditor.windowManager.open({
                file: '<?php echo url("/admin/mcebrowser.plugin.php?type=")?>' + type,
                title: 'File Browser/Uploader',
                width: 800,  // Your dimensions may differ - toy around with them!
                height: 600,
                resizable: "yes",
                inline: "yes",  // This parameter only has an effect if you use the inlinepopups plugin!
                close_previous: "no"
            }, {
                window: win,
                input: field_name
            });
            return false;
        }
        <?php }?>

        function lang_btn_cur(ithis) {
            p = $(ithis).parent();
            p.find('.lang_btn.cl').removeClass('cl');
            $(ithis).addClass('cl');
        }

        function lang_btn_reinit() {
            $('.lang_btn').each(function (i) {
                if ($(this).attr('lang_btn_set') != 1) {
                    $(this).bind('click', function () {
                        lang_btn_cur(this);
                    });
                    $(this).attr('lang_btn_set', 1);
                }
            });

        }

        function controls_init() {
            $(".dbo_date").datepicker({
                dateFormat: '<?php echo $GLOBALS['sys_def_date_format_js']?>',
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true
				});
$('.dbo_datetime').datetimepicker({
                dateFormat: '<?php echo $GLOBALS['sys_def_date_format_js']?>',
	timeFormat:'hh:mm:ss.l',
                changeMonth: true,
                changeYear: true,
	showSecond: true,
	showMillisec: true,
	hourGrid:4,
	minuteGrid:10,
	secondGrid:10,
	millisecGrid:100
	
		
});
/*            $(".dbo_datetime").datetimepicker({
                dateFormat: '<?php echo $GLOBALS['sys_def_date_format_js']?>',
                timeFormat: '<?php echo $GLOBALS['sys_def_time_format_js']?>',
								showHour:true,
	hourGrid:4,
	minuteGrid:10,
	secondGrid:10,
	millisecGrid:100,
								showMinute:true
				});*/
            $("a.add_button_a").fancybox();
            lang_btn_reinit();
            <?php if($def_editor=='tinymce'){?>
            tinyMCE.init({
                mode: "specific_textareas",
                editor_selector: "mce_editor",
                theme: "advanced",
                plugins: '-lineheight,iframe',
                theme_advanced_buttons4: "fontselect,fontsizeselect,lineheight,forecolor,backcolor,iframe",
                file_browser_callback: 'devlFileBrowser',
                theme_advanced_font_sizes: '10px,11px,12px,13px,14px,15px,16px,18px,20px,22px,24px,26px,28px,30px,36px,40px,56px,80px',
                content_css: 'css/editor_css.css',
                extended_valid_elements: "div[align|class|style|id|title]",
                extended_valid_elements: "iframe[name|src|framespacing|border|frameborder|scrolling|title|height|width],object[declare|classid|codebase|data|type|codetype|archive|standby|height|width|usemap|name|tabindex|align|border|hspace|vspace]"

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

        $(document).ready(function () {
					controls_init();
					_mscroller();
				});

				function _mscroller(){
					mw=jQuery('.menu');
					w=mw.width();
					ml=jQuery('ul',mw);
					if(ml.width()>w){
						a=mw.find('.active');
						if(a.length>0){
							a=a.get(0);
							if(a.offsetLeft+a.offsetWidth>w){
								ml.css('left',-((a.offsetLeft+a.offsetWidth)-w)+"px");	
							}
						}
						ol=ml.get(0).offsetLeft;
						showdelay=20;
						if(ol*-1>showdelay)
							jQuery('.ms_left').show();
						else
							jQuery('.ms_left').hide();
						if(ml.width()-w+ol>showdelay)
							jQuery('.ms_right').show();
						else
							jQuery('.ms_right').hide();
					}


					jQuery('.menu').bind('mousemove',function(e){
						mw=jQuery(this);
						ml=jQuery('ul',this);
						w=mw.width();
						if(ml.width()>w){
							mwd=wd=ml.width()-w;
							of=0;
							co=e.currentTarget;
							while(co.offsetParent!=null){
								of+=co.offsetLeft;
								co=co.offsetParent;
							}
							p=e.pageX-of;
							pp=(p/(w/100))*0.01;
							c=(mwd*pp);
							ml.css('left',-(c)+"px");
							showdelay=20;
							if(c>showdelay)
								jQuery('.ms_left').show();
							else
								jQuery('.ms_left').hide();
							if((mwd-c)>showdelay)
								jQuery('.ms_right').show();
							else
								jQuery('.ms_right').hide();
						}else{
							ml.css('left',"0px");
							jQuery('.ms_right').hide();
							jQuery('.ms_left').hide();
						}

					});


				}

    </script>
</head>

