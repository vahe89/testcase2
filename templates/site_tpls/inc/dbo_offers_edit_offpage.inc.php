<form id = "idform" action = 'index.php' method = 'POST' enctype = 'multipart/form-data' >
    <input type = 'hidden' name = 'a' value = 'p_adb' >

    <input type = "hidden" name = "redirect" value = "<?php echo($this->gO('redirect') ? $this->gO('redirect') : "a=dbo_{$this->oname}"); ?>" >
    <?php
    if ($this->cD['r_created_by'] == false) {
        ?>
        <input type = 'hidden' name = '<?php echo $this->in_s("created_by", "r"); ?>' value = '<?php echo $this->p->userId; ?>' >

    <?php } ?>
    <table width = "250" cellspacing = "0" cellpadding = "20" border = "0" bgcolor = "#EAFDFF" >
        <tbody >
        <tr >
            <td style = "padding:20px;" >
                <font size = "3" face = "Arial, Helvetica, sans-serif" ><strong style = "font-size:16px;" >JOB
                        REFERENCES</strong ></font ></td >
        </tr >
        </tbody >
    </table >

    <table class = "ans_ed_tbl" width = "900" cellspacing = "0" cellpadding = "3" bordercolor = "#CCCCCC" border = "1" bgcolor = "#EAFDFF" >
        <tbody >
        <tr >
            <td ><font size = "2" face = "Arial, Helvetica, sans-serif" >Created by: </font ></td >
            <td ><font size = "2" face = "Arial, Helvetica, sans-serif" ><?php if ($this->cD['id'] != false) {
                        echo $this->cD['r_created_by'] ? $this->cD['r_created_by'] : "Admin";
                    } else {
                        echo "(will be you)";
                    } ?></font ></td >
        </tr >
        <tr >
            <td width = "120" ><font size = "2" face = "Arial, Helvetica, sans-serif" >Offre reference </font ></td >
            <td width = "482" >
                <font size = "2" face = "Arial, Helvetica, sans-serif" ><?php echo $this->cD['id'] ? $this->cD['id'] : "(automatic reference)"; ?></font >
            </td >
        </tr >
        <tr >
            <td ><font size = "2" face = "Arial, Helvetica, sans-serif" >Offre date</font ></td >
            <td ><font size = "2" face = "Arial, Helvetica, sans-serif" >
                    <?php echo $this->dC("create_date"); ?>
                </font ></td >
        </tr >
        <tr >
            <td ><font size = "2" face = "Arial, Helvetica, sans-serif" >Offre titre</font ></td >
            <td ><font size = "2" face = "Arial, Helvetica, sans-serif" >
                    <?php echo $this->dC("title"); ?>
                </font ></td >
        </tr >
        <tr >
            <td ><font size = "2" face = "Arial, Helvetica, sans-serif" >Answers</font ></td >
            <td ><font size = "2" face = "Arial, Helvetica, sans-serif" >
                    <?php echo $this->cC["answers"]; ?>
                </font ></td >
        </tr >
        </tbody >
    </table >
    <br >
    <br >

    <table width = "250" cellspacing = "0" cellpadding = "20" border = "0" bgcolor = "#E3FFDE" >
        <tbody >
        <tr >
            <td style = "padding:20px;" >
                <strong ><font style = "font-size:16px;" size = "3" face = "Arial, Helvetica, sans-serif" >JOB
                        DESCRIPTION</font ></strong ></td >
        </tr >
        </tbody >
    </table >


    <table class = "ans_ed_tbl" width = "900" cellspacing = "0" cellpadding = "3" bordercolor = "#CCCCCC" border = "1" bgcolor = "#E3FFDE" >
        <tbody >

        <tr >
            <td width = "174" valign = "top" ><font size = "2" face = "Arial, Helvetica, sans-serif" >Job type</font >
            </td >
            <td width = "338" valign = "top" ><font size = "2" face = "Arial, Helvetica, sans-serif" >
                    <?php echo $this->dC("type"); ?>
                </font ></td >

        </tr >
        <tr >
            <td valign = "top" ><font size = "2" face = "Arial, Helvetica, sans-serif" >Job domain</font ></td >
            <td valign = "top" ><font size = "2" face = "Arial, Helvetica, sans-serif" >
                    <?php echo $this->dC("domain"); ?>
                </font ></td >

        </tr >
        <tr >
            <td valign = "top" ><font size = "2" face = "Arial, Helvetica, sans-serif" >Job nature</font ></td >
            <td valign = "top" ><font size = "2" face = "Arial, Helvetica, sans-serif" >
                    <?php echo $this->dC("nature"); ?>
                </font ></td >

        </tr >
        <tr >
            <td valign = "top" ><font size = "2" face = "Arial, Helvetica, sans-serif" >Job location</font ></td >
            <td valign = "top" ><font size = "2" face = "Arial, Helvetica, sans-serif" >
                    <?php echo $this->dC("town"); ?>
                </font ></td >

        </tr >
        <tr >
            <td valign = "top" ><font size = "2" face = "Arial, Helvetica, sans-serif" >Job description</font ></td >
            <td valign = "top" ><font size = "2" face = "Arial, Helvetica, sans-serif" >
                    <?php echo $this->dC("descr"); ?>
                </font ></td >

        </tr >
        <tr >
            <td valign = "top" ><font size = "2" face = "Arial, Helvetica, sans-serif" >Job validity date</font ></td >
            <td valign = "top" ><font size = "2" face = "Arial, Helvetica, sans-serif" >
                    <?php echo $this->dC("valid_to"); ?>
                </font ></td >

        </tr >
        </tbody >
    </table >
    <br >
    <br >

    <table width = "250" cellspacing = "0" cellpadding = "20" border = "0" bgcolor = "#F9FFED" >
        <tbody >
        <tr >
            <td style = "padding:20px;" >
                <strong ><font style = "font-size:16px;" size = "3" face = "Arial, Helvetica, sans-serif" >CANDIDAT
                        PROFILE</font ></strong ></td >
        </tr >
        </tbody >
    </table >


    <table class = "ans_ed_tbl" width = "900" cellspacing = "0" cellpadding = "3" bordercolor = "#CCCCCC" border = "1" bgcolor = "#F9FFED" >
        <tbody >
        <tr >
            <td width = "168" ><font size = "2" face = "Arial, Helvetica, sans-serif" >Job postul age</font ></td >
            <td width = "402" ><font size = "2" face = "Arial, Helvetica, sans-serif" >
                    <?php echo $this->dC("c_age"); ?>
                </font ></td >

        </tr >
        <tr >
            <td ><font size = "2" face = "Arial, Helvetica, sans-serif" >Job formation</font ></td >
            <td ><font size = "2" face = "Arial, Helvetica, sans-serif" >
                    <?php echo $this->dC("c_formation"); ?>
                </font ></td >

        </tr >
        <tr >
            <td width = "168" ><font size = "2" face = "Arial, Helvetica, sans-serif" >Job diplome</font ></td >
            <td width = "402" ><font size = "2" face = "Arial, Helvetica, sans-serif" >
                    <?php echo $this->dC("c_diplome"); ?>
                </font ></td >

        </tr >
        <tr >
            <td width = "168" ><font size = "2" face = "Arial, Helvetica, sans-serif" >Job languages</font ></td >
            <td ><font size = "2" face = "Arial, Helvetica, sans-serif" >
                    <?php echo $this->dC("c_lang"); ?>
                </font ></td >

        </tr >
        <tr >
            <td width = "168" ><font size = "2" face = "Arial, Helvetica, sans-serif" >Job comput</font ></td >
            <td width = "402" ><font size = "2" face = "Arial, Helvetica, sans-serif" >
                    <?php echo $this->dC("c_it"); ?>
                </font ></td >

        </tr >
        <tr >
            <td ><font size = "2" face = "Arial, Helvetica, sans-serif" >Job oth training</font ></td >
            <td ><font size = "2" face = "Arial, Helvetica, sans-serif" >
                    <?php echo $this->dC("c_oth_training"); ?>
                </font ></td >

        </tr >
        <tr >
            <td ><font size = "2" face = "Arial, Helvetica, sans-serif" >Job other habilitations</font ></td >
            <td ><font size = "2" face = "Arial, Helvetica, sans-serif" >
                    <?php echo $this->dC("c_oth_habs"); ?>
                </font ></td >

        </tr >
        <tr >
            <td ><font size = "2" face = "Arial, Helvetica, sans-serif" >Job experience</font ></td >
            <td ><font size = "2" face = "Arial, Helvetica, sans-serif" >
                    <?php echo $this->dC("c_exp"); ?>
                </font ></td >

        </tr >
        <tr >
            <td ><font size = "2" face = "Arial, Helvetica, sans-serif" >Job competences</font ></td >
            <td ><font size = "2" face = "Arial, Helvetica, sans-serif" >
                    <?php echo $this->dC("c_compet"); ?>
                </font ></td >

        </tr >
        <tr >
            <td ><font size = "2" face = "Arial, Helvetica, sans-serif" >Job other exigences</font ></td >
            <td ><font size = "2" face = "Arial, Helvetica, sans-serif" >
                    <?php echo $this->dC("c_oth_exig"); ?>
                </font ></td >

        </tr >
        </tbody >
    </table >


    <table >
        <tr class = "dbo_offpage_tr dbo_e_ctrl_tr dbo_<?php echo $this->oname; ?>_tr " >

            <td align = 'center' colspan = 3 class = "dbo_offpage_td dbo_e_ctrl_td dbo_<?php echo $this->oname; ?>_td " >
                <?php
                foreach ($this->langs as $l)
                    $this->drawHidden($l);

                $this->act_wrap("u", array("u" => "Sauver", "i" => "Sauver")); ?>
            </td >
        </tr >
    </table >

</form >


