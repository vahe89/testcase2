<?php

$root_path = "../";

$ctid = (isset($this->p->cD['id']) ? $this->p->cD['id'] : "new");

$css_prefix = "{$this->types[$tname]['c']}_{$tname}_{$ctid}";

$tw = 100;
$th = 100;
if (is_array($this->types[$tname]['thumb'])) {
    $tw = $this->types[$tname]['thumb']['w'];
    $th = $this->types[$tname]['thumb']['h'];
}


?>
<style type = "text/css" >

    #<?php echo $css_prefix?>thumbs {
        float: left;
        width: 300px;
    }

    #<?php echo $css_prefix?>thumbs .thumbs li {
        list-style: none;
    }

    #<?php echo $css_prefix?>thumbs .thumbs li img {
        width: <?php echo $tw?>px;
        height: <?php echo $tw?>px;
    }

    #<?php echo $css_prefix?>gallery {
        float: left;
        width: 400px;
    }


</style >

<?php sys_files::addGalleryDlgJS(); ?>

<script type = "text/javascript" >
    $(document).ready(function () {
        $('#<?php echo $css_prefix?>idform').validate();
    })
</script >

<script type = "text/javascript" >
    var <?php echo $css_prefix?>_img_counter =<?php echo count($this->cimgs["gallery_{$tname}"]);?>;

    function sys_files_<?php echo $css_prefix?>_lastimg() {
        <?php echo $css_prefix?>gallery.insertImage('<li> \
		<a class="thumb <?php echo $css_prefix?>gallery_empty" name="<?php echo $css_prefix?>-empty.gif" title="" href="index.php?a=p_emptyimg"> \
		<img src="index.php?a=p_emptyimg" alt="" /> \
		</a>  \
		<div class="caption"> \
		</div> \
		</li>', 0);
    }


    var <?php echo $css_prefix?>gallery = false;
    $(document).ready(function ($) {

        // We only want these styles applied when javascript is enabled
        $('div.navigation').css({'width': '300px', 'float': 'left'});
        $('div.gallery_content').css('display', 'block');
        // Initially set opacity on thumbs and add
        // additional styling for hover effect on thumbs
        var onMouseOutOpacity = 0.67;

        $('#<?php echo $css_prefix?>thumbs ul.thumbs li').opacityrollover({
            mouseOutOpacity: onMouseOutOpacity,
            mouseOverOpacity: 1.0,
            fadeSpeed: 'fast',
            exemptionSelector: '.selected'
        });
        // Initialize Advanced Galleriffic Gallery
        <?php echo $css_prefix?>gallery = $('#<?php echo $css_prefix?>thumbs').galleriffic({
            enableKeyboardNavigation: false,
            delay: 2500,
            numThumbs: 15,
            preloadAhead: 10,
            enableTopPager: true,
            enableBottomPager: true,
            maxPagesToShow: 7,
            imageContainerSel: '#<?php echo $css_prefix?>slideshow',
            controlsContainerSel: '#<?php echo $css_prefix?>controls',
            captionContainerSel: '#<?php echo $css_prefix?>caption',
            loadingContainerSel: '#<?php echo $css_prefix?>loading',
            renderSSControls: true,
            renderNavControls: true,
            playLinkText: 'Play Slideshow',
            pauseLinkText: 'Pause Slideshow',
            prevLinkText: '&lsaquo; Previous Photo',
            nextLinkText: 'Next Photo &rsaquo;',
            nextPageLinkText: 'Next &rsaquo;',
            prevPageLinkText: '&lsaquo; Prev',
            enableHistory: false,
            autoStart: false,
            syncTransitions: true,
            defaultTransitionDuration: 900,
            onSlideChange: function (prevIndex, nextIndex) {
                // 'this' refers to the gallery, which is an extension of $('#thumbs')
                this.find('ul.thumbs').children()
                    .eq(prevIndex).fadeTo('fast', onMouseOutOpacity).end()
                    .eq(nextIndex).fadeTo('fast', 1.0);
            },
            onPageTransitionOut: function (callback) {
                this.fadeTo('fast', 0.0, callback);
            },
            onPageTransitionIn: function () {
                this.fadeTo('fast', 1.0);
            }
        });

        $("#<?php echo $css_prefix?>ajax_gall").jup({
            onComplete: function (response, formId) {
                //assuming JSON
                if (response == false) {
                    //jup didn't receive valid JSON response
                } else {
                    <?php echo $css_prefix?>gallery.insertImage(response.ctrl_data, 0);
                    <?php echo $css_prefix?>_img_counter++;
                    if ($('.<?php echo $css_prefix?>gallery_empty').html()) {
                        <?php echo $css_prefix?>gallery.previous(false, true);
                        <?php echo $css_prefix?>gallery.removeImageByHash("<?php echo $css_prefix?>-empty.gif");
                    } else {

                        <?php if($this->types[$tname]['c']=='img'){?>
                        <?php echo $css_prefix?>_img_counter--;
                        <?php echo $css_prefix?>gallery.previous(false, true);
                        <?php echo $css_prefix?>gallery.removeImageByIndex(1);

                        $('.<?php echo "sys_files_img_{$tname}_{$this->p->cD['id']}"?>').attr('src', response.img);
                        $('.<?php echo "sys_files_img_{$tname}_{$this->p->cD['id']}"?>_thumb').attr('src', response.thumb);

//					setTimeout("$('#<?php echo $css_prefix?>thumbs .thumbs li').addClass('selected')",100);


                        <?php }?>
                    }

                }
            }
        });

    });
</script >

<div style = "display:none" id = 'sys_files_<?php echo $this->types[$tname]['c']; ?>_<?php echo "{$tname}_{$ctid}" ?>_dialog' >

    <div >
        Add image:
        <form action = 'index.php?a=dbo_<?php echo $this->oname; ?>' method = "POST" id = "<?php echo $css_prefix ?>ajax_gall" style = "display:inline" >
            <input type = "hidden" name = "f" value = "sys_files_i" >
            <input type = "hidden" name = "ajax" value = "1" >
            <input type = "hidden" name = "rid" value = "<?php echo $ctid; ?>" >
            <?php if ($ctid == "new") { ?>
                <input type = "hidden" name = "isnew" value = "<?php echo $isnew_v; ?>" >
            <?php } ?>
            <input type = "file" name = 'sys_files_<?php echo $this->types[$tname]['c']; ?>_<?php echo $tname ?>' >
            <input type = "submit" value = "<?php echo($this->types[$tname]['c'] == 'gallery' ? "Add image" : "Upload new image") ?>" >
        </form >

    </div >

    <div id = "<?php echo $css_prefix ?>thumbs" class = "navigation" >
        <ul class = "thumbs noscript" >


            <?php
            if (count($this->cimgs["{$this->types[$tname]['c']}_{$tname}"]) > 0) {
                foreach ($this->cimgs["{$this->types[$tname]['c']}_{$tname}"] as $row) {
                    $trimpath = $this->path . $row['full'];
                    $fpath = $this->url . $row['full'];
                    if ($this->types[$tname]['thumb'] != false)
                        $tpath = $this->url . "t" . $row['full'];
                    else
                        $tpath = $fpath;
                    $id = $row['id'];
                    $rtitle = $row['title'];
                    $tadd = time();
                    require("{$this->p->p->TEMPL}/inc/sys_files_gallery_item_ctrl.inc.php");
                }
            } else {
                ?>
                <li >
                    <a class = "thumb <?php echo $css_prefix ?>gallery_empty" name = "<?php echo $css_prefix ?>-empty.gif" title = "" href = "index.php?a=p_emptyimg" >
                        <img src = "index.php?a=p_emptyimg" alt = "" />
                    </a >

                    <div class = "caption" >
                    </div >
                </li >

                <?php
            }
            ?>


        </ul >
    </div >

    <div id = "<?php echo $css_prefix ?>gallery" class = "gallery_content" >
        <div id = "<?php echo $css_prefix ?>controls" class = "controls" ></div >
        <hr />
        <div id = "<?php echo $css_prefix ?>caption" class = "caption-container" ></div >

        <div class = "<?php echo $css_prefix ?>slideshow-container" >
            <div id = "<?php echo $css_prefix ?>loading" class = "loader" ></div >
            <div id = "<?php echo $css_prefix ?>slideshow" class = "slideshow" ></div >
        </div >

    </div >


</div >
