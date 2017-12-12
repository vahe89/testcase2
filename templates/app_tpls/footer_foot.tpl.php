<?php sys_files::contents(); ?>

<script src = "./js/wHumanMsg.min.js" type = "text/javascript" ></script >
<script type = 'text/javascript' >
    jQuery(document).ready(function () {
			jQuery('.drop_btn').bind('click', function (e) {
				jt = jQuery(e.target);
				if (!jt.is('.drop_btn_switch') && jt.parents(this).is('.drop_btn_dropped')) {
					jQuery(e.target).toggleClass('drop_btn_not_hide');
				} else {
					if (!jQuery(this).is('.drop_btn_dropped')) 
						jQuery(this).addClass('drop_btn_new');
					jQuery(this).removeClass('drop_btn_dropped_auto_close').toggleClass('drop_btn_dropped').find('.drop_wrap').toggleClass('dropped');
					jt.toggleClass('drop_btn_switch');
					//                e.stopPropagation();
				}
			});
			jQuery('body').bind('click', function (e) {
				if (jQuery(e.target).is('.drop_btn_not_hide')) {
					jQuery(e.target).toggleClass('drop_btn_not_hide');
				} else {

					var ds = jQuery('.drop_btn_dropped.drop_btn_dropped_auto_close').removeClass('drop_btn_dropped').removeClass('drop_btn_dropped_auto_close');
					ds.find('.drop_btn_switch').removeClass('drop_btn_switch');
					ds.find('.drop_wrap').toggleClass('dropped');
					jQuery('.drop_btn_dropped.drop_btn_new').removeClass('drop_btn_new').addClass('drop_btn_dropped_auto_close');
				}				
			});


        $(".jtitle").tooltip();
//        $("body").wHumanMsg();
				<?php echo $this->showMsg();?>
    });


</script>

