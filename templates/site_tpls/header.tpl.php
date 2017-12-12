<!DOCTYPE html>
<html >
<head >
    <meta http-equiv = "X-UA-Compatible" content = "IE=8" >
    <meta http-equiv = "Content-Type" content = "text/html; charset=UTF-8" >


    <title ><?php echo $this->webtitle[$this->def_lang]; ?></title >

    <link href = "<?php echo $this->skin; ?>css/admin.css" rel = "stylesheet" type = "text/css" >
    <link href = "<?php echo $this->skin; ?>css/ui-lightness/ui.css" rel = "stylesheet" type = "text/css" >
    <!-- <link rel="stylesheet" href="css/farbtastic/farbtastic.css" type="text/css" /> -->
    <link href = "<?php echo $this->skin; ?>css/fancybox/fancybox.css" media = "all" rel = "stylesheet" type = "text/css" >

    <script src = "js/jquery.min.js" type = "text/javascript" ></script >
    <script src = "js/jquery.mousewheel.js" type = "text/javascript" ></script >
    <script src = "js/jquery.fancybox-1.3.4.pack.js" type = "text/javascript" ></script >
    <script type = "text/javascript" src = "js/jup.1.0.1.js" ></script >

    <?php global $def_editor;
    if ($def_editor == 'ckeditor') {
        ?>
        <script src = "js/ckeditor/ckeditor.js" type = "text/javascript" ></script >
    <?php }else{/*?>
<script src="./js/tiny_mce/jquery.tinymce.js" type="text/javascript"></script>
	<?php */
    ?>
        <script src = "js/tiny_mce/tiny_mce.js" type = "text/javascript" ></script >

    <?php } ?>
    <script src = "js/jquery-ui.min.js" type = "text/javascript" ></script >
    <script src = "js/farbtastic.js" type = "text/javascript" ></script >

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
                dateFormat: 'dd/mm/yy',
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true
            });
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
        });

    </script >
</head >

<body >
<div class = 'admin_content' >

    <div class = 'admin_head' >

    </div >

    <?php if ($this->isAdmin){
    $amenu = array(
        "Home" => array('_url' => 'index.php'),
        "News" => array('_url' => 'index.php?a=dbo_news',
            "News" => 'index.php?a=dbo_news',
            "News Types" => 'index.php?a=dbo_news_type',
        ),
        "Jobs" => array('_url' => 'index.php?a=dbo_jobs'),
        "Videos" => array('_url' => 'index.php?a=dbo_videos'),
        "Pages" => array('_url' => 'index.php?a=dbo_pages'),
        "Sections" => array('_url' => 'index.php?a=dbo_sections'),
        "Menu" => array('_url' => 'index.php?a=dbo_menu',
            "Menu" => 'index.php?a=dbo_menu',
            "Top Menu" => 'index.php?a=dbo_topmenu',
            "Bottom Menu" => 'index.php?a=dbo_botmenu',
        ),
        "HR" => array('_url' => 'index.php?a=dbo_job_offer',
            "Offres" => 'index.php?a=dbo_job_offer',
            "Réponses" => 'index.php?a=dbo_job_answer',
            "Rechercher" => 'index.php?a=a_job_search',
            "Type" => 'index.php?a=dbo_job_type',
            "Domaine" => 'index.php?a=dbo_job_domain',
            "Nature" => 'index.php?a=dbo_job_nature',
            "Province" => 'index.php?a=dbo_job_town',
            "Formation" => 'index.php?a=dbo_job_formation',
            "Diplome" => 'index.php?a=dbo_job_diplome',
            "Langues parlées" => 'index.php?a=dbo_job_lang',
            "Connaissances informatiques" => 'index.php?a=dbo_job_it',
            "Institut d'études" => 'index.php?a=dbo_job_univ',

        ),
        "Index data" => array('_url' => 'index.php?a=dbo_index_page'),
        "Right slider" => array('_url' => 'index.php?a=dbo_right_slider'),
        "Users" => array('_url' => 'index.php?a=dbo_users'),
        "Logout" => array('_url' => 'index.php?a=p_logout'),
    );
    $amenu = array(
        "Home" => array('_url' => 'index.php'),
        "Cars" => array('_url' => 'index.php?a=dbo_cars'),
        "Articles" => array('_url' => 'index.php?a=dbo_articles'),
        "Brand" => array('_url' => 'index.php?a=dbo_brand'),
        "Statut" => array('_url' => 'index.php?a=dbo_statut'),
        "Menu" => array('_url' => 'index.php?a=dbo_menu',
        ),
        "MainPage" => array('_url' => 'index.php?a=dbo_index_page'),
        "Logout" => array('_url' => 'index.php?a=p_logout'),
    );

    /*	$accs=array(
            "News"=>"news",
            "News Types"=>"news",
            "Logout"=>"_free",

        );*/
    $accs = array();
    ?>
    <div class = 'admin_menu_wrap' >
        <h3 >Main menu</h3 >
        <ul class = 'admin_main_menu' >
            <?php foreach ($amenu as $k => $v) {
                if (!check_access($k, $accs, $this->access))
                    continue;
                ?>
                <li class = 'admin_main_menu_li' >
                    <a href = '<?php echo $v['_url'] ?>' ><?php echo $k;
                        if (count($v) > 1) {
                            echo " >";
                        } ?></a >
                    <?php unset($v['_url']);
                    if (count($v) > 0) {
                        ?>
                        <div class = 'admin_menu_sub_wrap' >
                            <ul class = 'admin_menu_sub' >
                                <?php foreach ($v as $sk => $sv) {
                                    if (!check_access($sk, $accs, $this->access, true))
                                        continue;
                                    echo "<li class='admin_menu_sub_li'><a href='{$sv}'>{$sk}</a></li>";
                                }
                                ?>
                            </ul >
                        </div >
                    <?php } ?>
                </li >
            <?php } ?>
        </ul >
    </div >

    <div class = 'admin_main' >
        <?php if (is_object($this->cdbo) && $this->cdbo->gO("hr_hdr")) { ?>
            <style >
                .admin_menu_wrap {
                    display: none !important;
                }

                .admin_main {
                    width: 980px !important;
                }

                .menu_tbl, .menu_tbl2 {
                    border-collapse: collapse;
                    width: 100% !important;

                }

                .menu_tbl td {
                    border-collapse: collapse;
                    padding: 5px;
                    border-left: 1px solid #999;
                    border-right: 1px solid #999;
                    border: 1px solid #aaa;
                }
            </style >
            <div class = "hr_hdr" >
            </div >
            <div >Hello, <?php echo $this->userCreds['ulogin']; ?></div >
            <table class = 'menu_tbl' >
                <tr >
                    <td ><a href = 'index.php' >Accueil Administration</a ></td >
                    <?php $i = 0;
                    foreach ($amenu['HR'] as $sk => $sv) {
                        $i++;
                        if ($i > 4)
                            break;
                        if ($sk == "_url")
                            continue;
                        if (!check_access($sk, $accs, $this->access, true))
                            continue;
                        echo "<td><a href='{$sv}'>{$sk}</a></td>";
                    }
                    ?>
                    <td ><a href = 'index.php?a=p_logout' >Logout</a ></td >
                </tr >
            </table >
            <table class = 'menu_tbl2' >
                <tr >
                    <?php $i = 0;
                    foreach ($amenu['HR'] as $sk => $sv) {
                        $i++;
                        if ($i < 5)
                            continue;
                        if ($sk == "_url")
                            continue;
                        if (!check_access($sk, $accs, $this->access, true))
                            continue;
                        echo "<td><a href='{$sv}'>{$sk}</a></td>";
                    }
                    ?>
                </tr >
            </table >

        <?php }
        }else{ ?>
        <div class = 'admin_login' >
            <?php } ?>
