<tr >
    <td >
        <p >

        <h1 style = "font-size:16px;" ><?php echo "{$this->cD['name']} {$this->cD['post_name']} {$this->cD['christ_name']}"; ?></h1 >
        <strong >
            <font style = "font-size:16px;" size = "3" face = "Arial, Helvetica, sans-serif" >
                <font color = "#003399" style = "font-size:16px;" >Post n° :</font > <?php echo $this->cD['id']; ?> -
                <font style = "font-size:16px;" color = "#003399" style = "font-size:16px;" >Job démarche</font >
                : <?php echo $this->cD['job']; ?> - </font >
            <font size = "3" face = "Arial, Helvetica, sans-serif" > <font color = "#003399" >Date
                    :</font > <?php echo date("d/m/Y", strtotime($this->cD['create_date'])); ?></font ></strong >
        <font size = "2" face = "Arial, Helvetica, sans-serif" > <br >
            <?php if ($this->cD['title'] != false) { ?>
                <font color = "#003399" ><strong >Civilité</strong ></font > : <?php echo $this->cD['title']; ?> - <?php } ?>
            <?php if ($this->cD['name'] != false) { ?>
                <strong ><font color = "#003399" >Nom</font ></strong > : <?php echo $this->cD['name']; ?> - <?php } ?>
            <?php if ($this->cD['post_name'] != false) { ?>
                <font color = "#003399" ><strong >Post-nom</strong ></font > : <?php echo $this->cD['post_name']; ?> - <?php } ?>
            <?php if ($this->cD['christ_name'] != false) { ?>
                <font color = "#003399" ><strong >Prénom</strong ></font > : <?php echo $this->cD['christ_name']; ?> - <?php } ?>
            <?php if ($this->cD['phone'] != false) { ?>
                <strong ><font color = "#003399" >Phone</font ></strong > : <?php echo $this->cD['phone']; ?> - <?php } ?>
            <?php if ($this->cD['email'] != false) { ?>
                <font color = "#003399" ><strong >Email</strong ></font > : <?php echo $this->cD['email']; ?> -<?php } ?>
            <?php if ($this->cD['family_stat'] != false) { ?><strong ><font color = "#003399" >Etat Civil</font >
            </strong >: <?php echo $this->cD['family_stat']; ?> - <?php } ?>
            <?php if ($this->cD['birth'] != false) { ?><font color = "#003399" ><strong >Date de
                    naissance</strong ></font > : <?php echo $this->cD['birth']; ?> - <?php } ?>
            <?php if ($this->cD['r_town'] != false) { ?><font color = "#003399" ><strong >Province de
                    résidence</strong ></font > : <?php echo $this->cD['r_town']; ?> - <?php } ?>
            <?php if ($this->cD['motivation'] != false) { ?>
                <font color = "#003399" ><strong >Motivation</strong ></font > : <?php echo $this->cD['motivation']; ?> - <?php } ?>
            <?php if ($this->cD['r_formation'] != false) { ?><font color = "#003399" ><strong >Catégorie de
                    formation</strong ></font > : <?php echo $this->cD['r_formation']; ?> - <?php } else if ($this->cD['formation_o'] != false) { ?>
                <font color = "#003399" ><strong >Catégorie de
                        formation</strong ></font > : <?php echo $this->cD['formation_o']; ?> - <?php } ?>
            <?php if ($this->cD['c_lang'] != false) { ?><font color = "#003399" ><strong >Langues
                    parlées </strong ></font >: <?php echo $this->cD['c_lang']; ?> - <?php } ?>
            <?php if ($this->cD['r_diplome'] != false) { ?>
                <font color = "#003399" ><strong >Diplôme</strong ></font > : <?php echo $this->cD['r_diplome']; ?> - <?php } else if ($this->cD['diplome_o'] != false) { ?>
                <font color = "#003399" ><strong >Diplôme</strong ></font > : <?php echo $this->cD['diplome_o']; ?> - <?php } ?>
            <?php if ($this->cD['r_univ'] != false) { ?><font color = "#003399" ><strong >Ecole
                    Université</strong ></font > : <?php echo $this->cD['r_univ']; ?> - <?php } else if ($this->cD['univ_o'] != false) { ?>
                <font color = "#003399" ><strong >Ecole
                        Université</strong ></font > : <?php echo $this->cD['univ_o']; ?> - <?php } ?>
            <?php if ($this->cD['oth_training'] != false) { ?><font color = "#003399" ><strong >Autres
                    formations</strong ></font > : <?php echo $this->cD['oth_training']; ?> - <?php } ?>
            <?php if ($this->cD['it'] != false) { ?>
                <font color = "#003399" ><strong >Informatique</strong ></font > : <?php echo $this->cD['it']; ?><?php } ?>

            <?php
            $d = unserialize($this->cC['docs']);
            if (is_array($d)) {
                ?>
                - <font color = "#003399" ><strong >Documents joints</strong ></font > :
                <?php
                foreach ($d as $n => $u) {
                    echo "<a target='_blank' href='" . path2url($u) . "'>$n</a> ";
                }
            }
            ?>
        </font ></p>
        <p ><font size = "2" face = "Arial, Helvetica, sans-serif" >
                <input type = "submit" value = "    VOIR L'OFFRE ET CORRIGER                        " onclick = "location.href='./index.php?a=dbo_job_answer&rid=<?php echo $this->cC['id']; ?>&s=answer_edit';return false;" >
            </font >-<font size = "2" face = "Arial, Helvetica, sans-serif" >
                <input type = "text" size = "20" value = "e-mail" id = "mailto_<?php echo $this->cD['id']; ?>" name = "textfield8" onfocus = "if(this.value=='e-mail'){this.value='';}" onblur = "if(this.value==''){this.value='e-mail'}" >
                <label ></label >
            </font ><font size = "3" face = "Arial, Helvetica, sans-serif" >
                <input type = "button" id = "send_<?php echo $this->cD['id']; ?>" value = " envoyer à l'email " onclick = "ci='#mailto_<?php echo $this->cD['id']; ?>';cv=jQuery(ci).val();if(cv=='' || /^[^@]+@[a-zA-Z0-9.-]+\.[a-zA-Z0-9.-]+/.test(cv)==false){this.value='Bad format or emapty';return false;}jQuery.post('index.php',{a:'p_send_answer',email:cv,id:<?php echo $this->cD['id']; ?>},function(r,x,h){if(r=='OK'){jQuery('#send_<?php echo $this->cD['id']; ?>').val('Email sent');}else{jQuery('#send_<?php echo $this->cD['id']; ?>').val('Error: '+r);}});" >
            </font ></p ></td >
</tr >

