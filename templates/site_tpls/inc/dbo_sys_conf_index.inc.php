<form id = "idform" action = 'index.php' method = 'POST' enctype = 'multipart/form-data' >
    <input type = 'hidden' name = 'a' value = 'p_adb' >

    <input type = "hidden" name = "redirect" value = "a=dbo_<?php echo $this->oname; ?>" >

    <table class = "dbo_edit dbo_offpage_table dbo_<?php echo $this->oname; ?>_offpage_table" >
        <?php
        if (count($this->langs) > 1) {
            ?>
            <tr class = "dbo_lang_selector dbo_offpage_tr dbo_<?php echo $this->oname; ?>_tr" >
                <td colspan = 3 >
                    <?php foreach ($this->langs as $l) { ?>
                        <input type = "button" class = 'lang_btn <?php echo($this->def_lang == $l ? "cl" : "") ?>' value = "<?php echo $l; ?>" onclick = "$('.dbo_row_lang_box').hide();$('.dbo_row_lang_<?php echo $l; ?>').show();" >&nbsp;
                    <?php } ?>
                </td >
            </tr >
        <?php }

        if (true || is_array($this->fctrls)) {

            if (is_object($this->sys_files) && $this->sys_files->autoCtrlChk(true)) {
                ?>
                <tr class = "dbo_offpage_tr dbo_<?php echo $this->oname; ?>_tr offpage__sys_files" >
                    <td colspan = 3 class = "dbo_offpage_td dbo_<?php echo $this->oname; ?>_td dbo_title_td" >
                        <?php $this->sys_files->autoCtrl(true); ?>
                    </td >
                </tr >
                <?php
            }
            foreach ($this->fctrls as $fn => $fc) {
                foreach ($this->langs as $l) {
                    ?>
                    <tr class = "dbo_row_lang_box dbo_row_lang_<?php echo $l;
                    echo($l == $this->def_lang ? "" : " hidden") ?> dbo_offpage_tr dbo_<?php echo $this->oname; ?>_tr offpage_<?php echo $fn; ?>" >
                        <td class = "dbo_offpage_td dbo_<?php echo $this->oname; ?>_td dbo_title_td" >
                            <?php echo $fc['t']; ?>
                        </td >
                        <td class = "dbo_offpage_td dbo_<?php echo $this->oname; ?>_td dbo_data_td" >
                            <?php

                            $this->drawCtrl($fn, $l);

                            ?>
                        </td >
                        <td class = "dbo_offpage_td dbo_<?php echo $this->oname; ?>_td dbo_descr_td" >
                            <?php echo $fc['d']; ?>
                        </td >
                    </tr >
                    <?php if (is_object($this->sys_files) && $this->sys_files->autoCtrlChk($fn)) { ?>
                        <tr class = "dbo_offpage_tr dbo_<?php echo $this->oname; ?>_tr offpage_sys_files" >
                            <td colspan = 3 class = "dbo_offpage_td dbo_<?php echo $this->oname; ?>_td dbo_title_td" >
                                <?php $this->sys_files->autoCtrl($fn); ?>
                            </td >
                        </tr >
                        <?php
                    }

                }
            } ?>
        <?php } ?>
        <?php if (is_object($this->sys_files) && $this->sys_files->autoCtrlChk(false)) { ?>
            <tr class = "dbo_offpage_tr dbo_<?php echo $this->oname; ?>_tr offpage_sys_files" >
                <td colspan = 3 class = "dbo_offpage_td dbo_<?php echo $this->oname; ?>_td dbo_title_td" >
                    <?php $this->sys_files->autoCtrl(false); ?>
                </td >
            </tr >
        <?php } ?>


        <tr class = "dbo_offpage_tr dbo_e_ctrl_tr dbo_<?php echo $this->oname; ?>_tr " >

            <td align = 'center' colspan = 3 class = "dbo_offpage_td dbo_e_ctrl_td dbo_<?php echo $this->oname; ?>_td " >
                <?php $this->act_wrap("u", array("u" => "Save", "i" => "Add")); ?>
            </td >
        </tr >
    </table >

</form >


