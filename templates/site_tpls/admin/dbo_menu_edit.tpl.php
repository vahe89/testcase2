<div class = 'menu_edit' >
    <script type = "text/javascript" src = "../js/jquery.validate.min.js" ></script >
    <script type = "text/javascript" >
        $(document).ready(function () {
            $('#idform').validate();
        })
    </script >


    <input type = "button" value = "Back" onclick = "location.href='index.php?a=dbo_<?php echo $this->oname ?>'" ><br />

    <form id = "idform" action = 'index.php' method = 'POST' enctype = 'multipart/form-data' >
        <input type = 'hidden' name = 'a' value = 'p_adb' >
        <input type = 'hidden' name = 'redirect_url' value = 'index.php?a=dbo_<?php echo $this->oname; ?>' >

        <table >
            <?php
            foreach ($this->fctrls as $fn => $fc) {

                ?>
                <tr >
                    <td > <?php echo(isset($fc['t']) ? $fc['t'] : $fn) ?>:</td >
                    <td >
                        <?php

                        foreach ($this->langs as $l) {
                            echo "{$l}: ";
                            echo $this->dC($fn, $l) . "<br/>";
                        }

                        ?>
                    </td >
                </tr >
            <?php } ?>

            <tr >
                <td >Type</td >
                <td >
                    <?php echo $this->dC("sys_links_link"); ?>

                </td >
            </tr >
            <tr >
                <td >Parent:</td >
                <td >

                    <?php echo $this->dC("sys_prios_parent"); ?>

                </td >
            </tr >
            <tr >
                <td >First or After:</td >
                <td >

                    <?php echo $this->dC("sys_prios_prio"); ?>

                </td >
            </tr >

            <tr >
                <td colspan = 2 >

                    <?php $this->act_wrap("u", array("u" => "Save", "i" => "Add")); ?>

                </td >
            </tr >
        </table >
    </form >

    <?php /* if(isset($this->cD['id'])) {?>
<script type="text/javascript">
$(document).ready(function(){
	$('#<?php echo $this->hID("sys_prios_parent")?>').bind("change",function(){
		$('.<?php echo $this->hID("m-hide")?>').hide().removeProp("selected");
//		$('#<?php echo $this->hID("sys_prios_prios")?>').val($('.<?php echo $this->hID("curr-'+$('#".$this->hID("sys_prios_parent",true,array('echo_INS'=>false))."').val()+'");?>').show().first().val());
		$('.<?php echo $this->hID("curr-'+$('#".$this->hID("sys_prios_parent",true,array('echo_INS'=>false))."').val()+'");?>').show().prop('selected',true);

	});
/*
 		$('.<?php echo $this->hID("m-hide")?>').hide().removeProp("selected");
		$('.<?php echo $this->hID("curr-'+$('#".$this->hID("sys_prios_parent",true,array('echo_INS'=>false))."').val()+'");?>').show().prop('selected',true);
 */    /*
 		$('.<?php echo $this->hID("m-hide")?>').hide();
		$('.<?php echo $this->hID("curr-'+$('#".$this->hID("sys_prios_parent",true,array('echo_INS'=>false))."').val()+'");?>').show();
	});
</script>
<?php } */ ?>
</div >
