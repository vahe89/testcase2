<div class = "dbo_offpage_edit dbo_offpage dbo_<?php echo $this->oname; ?>_offpage_edit" >
    <input type = "button" value = "Back" onclick = "location.href='index.php?a=dbo_<?php echo $this->oname; ?>'" ><br />

    <table class = 'dbo_edit_wrap' >
        <tr class = "dbo_edit_tr dbo_edit_<?php echo $this->oname; ?>_tr" >
            <td class = "dbo_edit_td dbo_edit_<?php echo $this->oname; ?>_td" >
                <?php // include($this->p->TEMPL."/inc/dbo_{$this->copts['editInc']}_{$this->copts["adminOnlineEdit"]}.inc.php");?>
                <style >
                    table.ans_ed_titl_t > tbody > tr > td {
                        padding: 9px;
                    }
                </style >
                <table class = "ans_ed_titl_t" width = "900" cellspacing = "0" cellpadding = "9" bordercolor = "#009933" border = "2" >
                    <tbody >
                    <tr >
                        <td bgcolor = "#009933" style = "padding: 9px;" >
                            <strong ><font size = "3" face = "Arial, Helvetica, sans-serif" color = "#FFFFFF" style = "font-size:16px;" ><?php echo "{$this->cC['job']} {$this->cC['r_type']}"; ?> </font ></strong >
                        </td >
                    </tr >
                    <?php if ($this->cC['answer_to'] != 0) {
                        echo $this->p->t["job_offer"]->listDef("offers_list", array("queryWhere" => "ct.id={$this->cC['answer_to']}", "offer_show" => true));
                    } ?>
                    </tbody >
                </table >

                <br >
                <br >

                <form action = "index.php" method = "POST" enctype = "multipart/form-data" >
                    <input type = 'hidden' name = 'a' value = 'p_adb' >
                    <input type = "hidden" name = "redirect" value = "<?php echo($this->gO('redirect') ? $this->gO('redirect') : "a=dbo_{$this->oname}"); ?>" >
                    <?php
                    foreach ($this->langs as $l)
                        $this->drawHidden($l);
                    ?>


                    <table class = "ans_ed_tbl" width = "950" cellspacing = "0" cellpadding = "3" bordercolor = "#666666" border = "1" >
                        <tbody >
                        <?php if ($this->cC['type'] > 1) { ?>
                            <tr bgcolor = "#EAFDFF" >

                                <td bgcolor = "#EAFDFF" ><font size = "2" face = "Arial, Helvetica, sans-serif" >Lieu
                                        souhaité de prestation du stage :</font ></td >
                                <td bgcolor = "#EAFDFF" >
                                    <font size = "2" face = "Arial, Helvetica, sans-serif" ><?php echo $this->cC['r_prefer_location']; ?></font >
                                </td >
                                <td bgcolor = "#EAFDFF" >
                                    <strong ><font size = "2" face = "Arial, Helvetica, sans-serif" color = "#006600" ><?php $this->dC('prefer_location'); ?> </font ></strong >
                                </td >
                            </tr >
                            <tr bgcolor = "#EAFDFF" >

                                <td bgcolor = "#EAFDFF" ><font size = "2" face = "Arial, Helvetica, sans-serif" >Date
                                        souhaitée de début de stage:</font ></td >
                                <td bgcolor = "#EAFDFF" ><font size = "2" face = "Arial, Helvetica, sans-serif" > entre
                                        le :

                                        <?php echo $this->cC['start_btw1']; ?> et
                                        le <?php echo $this->cC['start_btw2']; ?></font ></td >
                                <td bgcolor = "#EAFDFF" >
                                    <strong ><font size = "2" face = "Arial, Helvetica, sans-serif" color = "#006600" >
                                            entre le <?php echo $this->dC('start_btw1'); ?> et
                                            le <?php echo $this->dC('start_btw2'); ?></font ></strong ></td >
                            </tr >
                            <tr bgcolor = "#EAFDFF" >

                                <td bgcolor = "#EAFDFF" ><font size = "2" face = "Arial, Helvetica, sans-serif" >Période
                                        souhaitée de stage :</font ></td >
                                <td bgcolor = "#EAFDFF" >
                                    <font size = "2" face = "Arial, Helvetica, sans-serif" ><?php echo $this->cC['test_period']; ?>
                                        mois</font ></td >
                                <td bgcolor = "#EAFDFF" >
                                    <strong ><font size = "2" face = "Arial, Helvetica, sans-serif" color = "#006600" ><?php echo $this->dC('test_period'); ?></font ></strong >
                                </td >
                            </tr >
                        <?php } ?>

                        <tr >

                            <td bgcolor = "#F9FFED" >
                                <font size = "2" face = "Arial, Helvetica, sans-serif" >Civilité</font ></td >
                            <td bgcolor = "#F9FFED" ><font size = "2" face = "Arial, Helvetica, sans-serif" >
                                    <?php echo $this->cC['title']; ?>
                                </font ></td >
                            <td bgcolor = "#F9FFED" ><?php echo $this->dC('title'); ?></td >
                        </tr >
                        <tr >

                            <td width = "217" bgcolor = "#F9FFED" >
                                <font size = "2" face = "Arial, Helvetica, sans-serif" >Nom</font ></td >
                            <td width = "248" bgcolor = "#F9FFED" >
                                <font size = "2" face = "Arial, Helvetica, sans-serif" ><?php echo $this->cC['name']; ?></font >
                            </td >
                            <td width = "334" bgcolor = "#F9FFED" ><?php echo $this->dC('name'); ?></td >
                        </tr >
                        <tr >

                            <td width = "217" bgcolor = "#F9FFED" >
                                <font size = "2" face = "Arial, Helvetica, sans-serif" >Post Nom</font ></td >
                            <td width = "248" bgcolor = "#F9FFED" >
                                <font size = "2" face = "Arial, Helvetica, sans-serif" ><?php echo $this->cC['post_name']; ?></font >
                            </td >
                            <td width = "334" bgcolor = "#F9FFED" ><?php echo $this->dC('post_name'); ?></td >
                        </tr >
                        <tr >

                            <td width = "217" bgcolor = "#F9FFED" >
                                <font size = "2" face = "Arial, Helvetica, sans-serif" >Prénom</font ></td >
                            <td width = "248" bgcolor = "#F9FFED" >
                                <font size = "2" face = "Arial, Helvetica, sans-serif" ><?php echo $this->cC['christ_name']; ?></font >
                            </td >
                            <td width = "334" bgcolor = "#F9FFED" ><?php echo $this->dC('christ_name'); ?></td >
                        </tr >
                        <tr >

                            <td bgcolor = "#F9FFED" >
                                <font size = "2" face = "Arial, Helvetica, sans-serif" >Téléphone</font ></td >
                            <td bgcolor = "#F9FFED" >
                                <font size = "2" face = "Arial, Helvetica, sans-serif" ><?php echo $this->cC['phone']; ?></font >
                            </td >
                            <td bgcolor = "#F9FFED" ><?php echo $this->dC('phone'); ?></td >
                        </tr >
                        <tr >

                            <td bgcolor = "#F9FFED" >
                                <font size = "2" face = "Arial, Helvetica, sans-serif" >E-mail</font ></td >
                            <td bgcolor = "#F9FFED" >
                                <font size = "2" face = "Arial, Helvetica, sans-serif" ><?php echo $this->cC['email']; ?></font >
                            </td >
                            <td bgcolor = "#F9FFED" ><?php echo $this->dC('email'); ?></td >
                        </tr >
                        <tr >

                            <td bgcolor = "#F9FFED" >
                                <font size = "2" face = "Arial, Helvetica, sans-serif" >Statut</font ></td >
                            <td bgcolor = "#F9FFED" >
                                <font size = "2" face = "Arial, Helvetica, sans-serif" ><?php echo $this->cC['family_stat']; ?></font >
                            </td >
                            <td bgcolor = "#F9FFED" ><?php echo $this->dC('family_stat'); ?></td >
                        </tr >
                        <tr >

                            <td bgcolor = "#F9FFED" ><font size = "2" face = "Arial, Helvetica, sans-serif" >Date de
                                    naissance</font ></td >
                            <td bgcolor = "#F9FFED" >
                                <font size = "2" face = "Arial, Helvetica, sans-serif" ><?php echo $this->cC['birth']; ?></font >
                            </td >
                            <td bgcolor = "#F9FFED" ><?php echo $this->dC('birth'); ?></td >
                        </tr >
                        <tr >

                            <td bgcolor = "#F9FFED" ><font size = "2" face = "Arial, Helvetica, sans-serif" >Province de
                                    résidence</font ></td >
                            <td bgcolor = "#F9FFED" >
                                <font size = "2" face = "Arial, Helvetica, sans-serif" ><?php echo $this->cC['r_town']; ?></font >
                            </td >
                            <td bgcolor = "#F9FFED" ><?php echo $this->dC('town'); ?></td >
                        </tr >
                        <tr bordercolor = "#999999" >

                            <td valign = "top" ><font size = "2" face = "Arial, Helvetica, sans-serif" >Décrivez ce qui
                                    vous motive à postuler chez SEP Congo</font ></td >
                            <td valign = "top" >
                                <font size = "2" face = "Arial, Helvetica, sans-serif" ><?php echo $this->cC['motivation']; ?></font >
                            </td >
                            <td valign = "top" ><?php echo $this->dC('motivation'); ?></td >
                        </tr >
                        <tr >

                            <td bgcolor = "#EAFDFF" ><font size = "2" face = "Arial, Helvetica, sans-serif" >Catégorie
                                    de formation</font ></td >
                            <td bgcolor = "#EAFDFF" >
                                <font size = "2" face = "Arial, Helvetica, sans-serif" ><?php echo $this->cC['r_formation'];
                                    echo $this->cC['formation_o'] != false ? " (AUTRE: {$this->cC['formation_o']} )" : ""; ?></font >
                            </td >
                            <td bgcolor = "#EAFDFF" >
                                <?php echo $this->dC('formation'); ?>
                                <br >AUTRE: <?php echo $this->dC('formation_o'); ?>
                            </td >
                        </tr >
                        <tr >

                            <td width = "217" bgcolor = "#EAFDFF" >
                                <font size = "2" face = "Arial, Helvetica, sans-serif" >Langues parlées</font ></td >
                            <td width = "248" bgcolor = "#EAFDFF" >
                                <font size = "2" face = "Arial, Helvetica, sans-serif" >
                                    <?php echo $this->cC['c_lang']; ?>
                                </font ></td >
                            <td width = "334" bgcolor = "#EAFDFF" >
                                <?php echo $this->dC('c_lang'); ?>
                            </td >
                        </tr >
                        <tr >

                            <td bgcolor = "#EAFDFF" ><font size = "2" face = "Arial, Helvetica, sans-serif" >Niveau
                                    d'études - Dernier diplôme</font ></td >
                            <td bgcolor = "#EAFDFF" >
                                <font size = "2" face = "Arial, Helvetica, sans-serif" ><?php echo $this->cC['r_diplome'];
                                    echo $this->cC['diplome_o'] != false ? " (AUTRE: {$this->cC['diplome_o']} )" : ""; ?></font >
                            </td >
                            <td bgcolor = "#EAFDFF" >
                                <?php echo $this->dC('diplome'); ?>
                                <br >AUTRE: <?php echo $this->dC('diplome_o'); ?>
                            </td >
                        </tr >
                        <tr >

                            <td bgcolor = "#EAFDFF" ><font size = "2" face = "Arial, Helvetica, sans-serif" >Université,
                                    Institut, Ecole</font ></td >
                            <td bgcolor = "#EAFDFF" >
                                <font size = "2" face = "Arial, Helvetica, sans-serif" ><?php echo $this->cC['r_univ'];
                                    echo $this->cC['univ_o'] != false ? " (AUTRE: {$this->cC['univ_o']} )" : ""; ?></font >
                            </td >
                            <td bgcolor = "#EAFDFF" >
                                <?php echo $this->dC('univ'); ?>
                                <br >AUTRE: <?php echo $this->dC('univ_o'); ?>
                            </td >
                        </tr >
                        <tr >

                            <td bgcolor = "#EAFDFF" ><font size = "2" face = "Arial, Helvetica, sans-serif" >Autres
                                    formations/habilitations obtenues</font ></td >
                            <td bgcolor = "#EAFDFF" >
                                <font size = "2" face = "Arial, Helvetica, sans-serif" ><?php echo $this->cC['oth_training']; ?></font >
                            </td >
                            <td bgcolor = "#EAFDFF" ><?php echo $this->dC('oth_training'); ?></td >
                        </tr >
                        <tr >

                            <td width = "217" bgcolor = "#EAFDFF" bordercolor = "#CCCCCC" >
                                <font size = "2" face = "Arial, Helvetica, sans-serif" >Connaissances
                                    informatiques</font ></td >
                            <td width = "248" bgcolor = "#EAFDFF" bordercolor = "#CCCCCC" >
                                <font size = "2" face = "Arial, Helvetica, sans-serif" >
                                    <?php echo $this->cC['it']; ?>
                                </font ></td >
                            <td width = "334" bgcolor = "#EAFDFF" bordercolor = "#CCCCCC" >
                                <?php echo $this->dC('it'); ?>
                            </td >
                        </tr >
                        </tbody >
                    </table >
                    <br >
                    <?php $d = unserialize($this->cC['docs']);
                    if (is_array($d) && count($d) > 0) {
                        ?>
                        <p >
                            <font style = "font-size:24px;" size = "5" face = "Arial, Helvetica, sans-serif" color = "#006600" >Attached
                                documents</font ></p >
                        <table class = "ans_ed_tbl" width = "800" cellspacing = "0" cellpadding = "3" border = "0" >
                            <tbody >
                            <?php foreach ($d as $n => $u) { ?>
                                <tr >
                                    <td width = "14%" >
                                        <div align = "right" >
                                            <img width = "23" height = "10" src = "./css/img/attachment_symbol89.jpg" >
                                        </div >
                                    </td >
                                    <td width = "30%" >
                                        <font size = "2" face = "Arial, Helvetica, sans-serif" ><?php echo $n ?></font >
                                    </td >
                                    <td width = "13%" ><font size = "3" face = "Arial, Helvetica, sans-serif" >
                                            <a href = "<?php echo path2url($u); ?>" target = "_blank" >télécharger</a >
                                        </font ></td >
                                    <td width = "43%" ><font size = "3" face = "Arial, Helvetica, sans-serif" >
                                            <?php /* ?>
                  <input type="submit" value=" voir dans le navigateur ">
									</font>
<?php */ ?>
                                    </td >
                                </tr >
                            <?php } ?>
                            </tbody >
                        </table >
                    <?php } ?>
                    <br >
                    <br >
                    <br >

                    <p ><font size = "2" face = "Arial, Helvetica, sans-serif" >
                            <?php echo $this->act_wrap("u", "SAUVER") ?>
                        </font >
                    </p >

                </form >


            </td >
        </tr >
    </table >
</div >


