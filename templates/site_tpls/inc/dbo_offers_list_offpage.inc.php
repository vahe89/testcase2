<tr >
    <?php if (!$this->gO("offer_show")) { ?>
        <td class = "dbo_td dbo_ctrl dbo_edit_ctrl dbo_<?php echo $this->oname ?>_td" >
            <input type = "button" value = "Modifier" onclick = "location.href='./index.php?a=dbo_<?php echo $this->oname; ?>&s=<?php echo $this->copts['editTpl']; ?>&rid=<?php echo $row['id']; ?>'" >

            <form action = "index.php" method = "POST" >
                <input type = "hidden" name = "a" value = "p_adb" >
                <input type = "hidden" name = "redirect" value = "a=dbo_<?php echo $this->oname; ?>&s=<?php echo $this->copts['listTpl']; ?>" >
                <?php $this->act_wrap("d", "Supprimer"); ?>
            </form >
        </td >
    <?php } ?>
    <td >
        <table width = "100%" cellspacing = "0" cellpadding = "12" bordercolor = "#FF0000" border = "0" >
            <tbody >
            <tr >
                <td ><p ><strong ><font style = "font-size:16px;" size = "3" face = "Arial, Helvetica, sans-serif" >
                                <font style = "font-size:16px;" color = "#0000CC" >Titre</font >
                                : <?php echo $this->cC['title']; ?> </font ></strong >
                        <font size = "2" face = "Arial, Helvetica, sans-serif" ><br ></font >
                        <font size = "2" face = "Arial, Helvetica, sans-serif" color = "#0000CC" >Références de
                            l'offre</font >
                        <font size = "2" face = "Arial, Helvetica, sans-serif" > : N° <?php echo $this->cC['id']; ?>
                            du <?php echo $this->cC['create_date']; ?>.
                            <?php if ($this->cD['valid_to'] != false){ ?><font color = "#0000CC" >Valide
                                jusqu'au</font > <?php echo $this->cC['valid_to']; ?></font ><?php } ?>
                    </p >

                    <p ><font size = "2" face = "Arial, Helvetica, sans-serif" >
                            <?php if ($this->cD['r_type'] != false) { ?><font color = "#0000CC" >Type
                                :</font > <?php echo $this->cC['r_type']; ?> - <?php } ?>
                            <?php if ($this->cD['r_domain'] != false) { ?>
                                <font color = "#0000CC" >Domaine</font > : <?php echo $this->cC['r_domain']; ?> -  <?php } ?>
                            <?php if ($this->cD['r_nature'] != false) { ?>
                                <font color = "#0000CC" >Nature</font > : <?php echo $this->cC['r_nature']; ?> -  <?php } ?>
                            <?php if ($this->cD['r_town'] != false) { ?>
                                <font color = "#0000CC" >Localisation</font > : <?php echo $this->cC['r_town']; ?> -  <?php } ?>
                            <?php if ($this->cD['descr'] != false) { ?>
                                <font color = "#0000CC" >Description</font > :  <?php echo $this->cC['descr']; ?> -  <?php } ?>
                            <?php if ($this->cD['c_age'] != false) { ?>
                                <font color = "#0000CC" >Age</font > : <?php echo $this->cC['c_age']; ?> - <?php } ?>
                            <?php if ($this->cD['r_c_formation'] != false) { ?>
                                <font color = "#0000CC" >Formation</font > : <?php echo $this->cC['r_c_formation']; ?> - <?php } ?>
                            <?php if ($this->cD['r_c_diplome'] != false) { ?>
                                <font color = "#0000CC" >Diplôme</font > : <?php echo $this->cC['r_c_diplome']; ?> - <?php } ?>
                            <?php if ($this->cD['c_lang'] != false) { ?>
                                <font color = "#0000CC" >Langues</font > : <?php echo $this->cC['c_lang']; ?> - <?php } ?>
                            <?php if ($this->cD['c_it'] != false) { ?>
                                <font color = "#0000CC" >Informatique</font > : <?php echo $this->cC['c_it']; ?> - <?php } ?>
                            <?php if ($this->cD['c_oth_training'] != false) { ?>
                                <font color = "#0000CC" >Training</font > : <?php echo $this->cC['c_oth_training']; ?> - <?php } ?>
                            <?php if ($this->cD['c_oth_habs'] != false) { ?><font color = "#0000CC" >Autres
                                habilitations</font > : <?php echo $this->cC['c_oth_habs']; ?> - <?php } ?>
                            <?php if ($this->cD['c_exp'] != false) { ?>
                                <font color = "#0000CC" >Expérience</font > : <?php echo $this->cC['c_exp']; ?> - <?php } ?>
                            <?php if ($this->cD['c_compet'] != false) { ?>
                                <font color = "#0000CC" >Compétences</font > : <?php echo $this->cC['c_compet']; ?> - <?php } ?>
                            <?php if ($this->cD['c_oth_exig'] != false) { ?><font color = "#0000CC" >Autres
                                exigences</font > : <?php echo $this->cC['c_oth_exig']; ?><?php } ?>
                        </font ></p >
                    <br >

                    <p ><font size = "2" face = "Arial, Helvetica, sans-serif" color = "#FF0000" >Nombre de
                            réponses</font ><font size = "2" face = "Arial, Helvetica, sans-serif" >
                            : <?php echo $this->cC['answers']; ?> -
                            <a href = "./index.php?a=dbo_job_answer&show=<?php echo $this->cD['id']; ?>" >Voir la liste
                                des réponses</a ></font ></p >
                </td >
            </tr >
            </tbody >
        </table >
    </td >
</tr >

