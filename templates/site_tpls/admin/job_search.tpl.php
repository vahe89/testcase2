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


<table width = "900" cellspacing = "0" cellpadding = "0" border = "0" align = "center" >
    <tbody >
    <tr >
        <td valign = "bottom" >
            <font style = "font-size:24px;" size = "5" face = "Arial, Helvetica, sans-serif" color = "#FF0000" >Module
                de recherche des candidatures (Sep Admin)</font ></td >
        <td width = "140" >
            <div align = "right" >
                <a href = "./index.php?a=p_job_search" ><img width = "140" vspace = "3" border = "0" height = "71" src = "./css/img/search_all.jpg" ></a >
            </div >
        </td >
    </tr >
    </tbody >
</table >

<form action = "index.php" method = "POST" enctype = "multipart/form-data" >
    <input type = "hidden" name = "a" value = "p_job_search" >
    <table class = "ans_ed_tbl" width = "900" cellspacing = "0" cellpadding = "0" border = "0" align = "center" >
        <tbody >
        <tr >
            <td width = "668" valign = "top" >
                <table width = "708" cellspacing = "0" cellpadding = "3" bordercolor = "#CCCCCC" border = "1" align = "center" >
                    <tbody >
                    <tr bgcolor = "#F9FFED" >

                        <td ><font size = "2" face = "Arial, Helvetica, sans-serif" >Full text search</font ></td >
                        <td ><font size = "2" face = "Arial, Helvetica, sans-serif" >
                                <input type = "text" id = "Groupe de boutons radio1_4" value = "" name = "search[search]" >
                            </font ></td >
                    </tr >

                    <tr bgcolor = "#F9FFED" >

                        <td ><font size = "2" face = "Arial, Helvetica, sans-serif" >Candidatures</font ></td >
                        <td ><font size = "2" face = "Arial, Helvetica, sans-serif" >
                                <label ><input type = "radio" id = "Groupe de boutons radio1_4" value = "1" name = "search[kind]" >Emploi</label >
                                <label ><input type = "radio" id = "Groupe de boutons radio1_4" value = "2" name = "search[kind]" >Stage
                                    professionnel</label >
                                <label ><input type = "radio" id = "Groupe de boutons radio1_4" value = "3" name = "search[kind]" >Stage
                                    académique</label >
                            </font ></td >
                    </tr >
                    <tr bgcolor = "#F9FFED" >

                        <td ><font size = "2" face = "Arial, Helvetica, sans-serif" >Réponses à l'offre</font ></td >
                        <td ><font size = "2" face = "Arial, Helvetica, sans-serif" >
                                <select id = "select14" name = "search[answer_to]" >
                                    <option value = "" >-- Select --</option >
                                    <?php echo $this->t['job_offer']->sel(false, array("sel_titleFld" =>
                                        "concat(ct.id,' - ',date_format(ct.create_date,'%d/%m/%Y'),' - ',t.name,' / ',ct.title)", "sel_join" =>
                                        " left join job_type t on t.id=ct.type", "sel_titleLen" => false)); ?>
                                </select >
                            </font ></td >
                    </tr >
                    <tr bgcolor = "#F9FFED" >

                        <td width = "100" ><font size = "2" face = "Arial, Helvetica, sans-serif" >Nom Postnom
                                Prénom</font ></td >
                        <td width = "482" ><font size = "2" face = "Arial, Helvetica, sans-serif" >
                                <input type = "text" size = "50" id = "textfield3" name = "search[fullname]" >
                            </font ></td >
                    </tr >
                    <?php /*?>
            <tr bgcolor="#F9FFED">
              
              <td width="100"><font size="2" face="Arial, Helvetica, sans-serif">Postnom</font></td>
              <td width="482"><font size="2" face="Arial, Helvetica, sans-serif">
                <input type="text" size="50" id="textfield4" name="search[post_name]">
              </font></td>
            </tr>
            <tr bgcolor="#F9FFED">
              
              <td width="100"><font size="2" face="Arial, Helvetica, sans-serif">Prénom</font></td>
              <td width="482"><font size="2" face="Arial, Helvetica, sans-serif">
                <input type="text" size="50" id="textfield5" name="search[christ_name]">
              </font></td>
							</tr>
						<?php */ ?>

                    <tr bgcolor = "#F9FFED" >

                        <td width = "100" ><font size = "2" face = "Arial, Helvetica, sans-serif" >Civilité</font >
                        </td >
                        <td ><font size = "2" face = "Arial, Helvetica, sans-serif" >
                                <label ><input type = "radio" id = "Groupe de boutons radio1_1" value = "monsieur" name = "search[title]" >
                                    Monsieur</label >
                                <label ><input type = "radio" id = "Groupe de boutons radio1_2" value = "madame" name = "search[title]" >
                                    Madame</label >
                                <label ><input type = "radio" id = "Groupe de boutons radio1_3" value = "mademoiselle" name = "search[title]" >
                                    Mademoiselle</label >
                            </font ></td >
                    </tr >
                    <tr bgcolor = "#F9FFED" >

                        <td ><font size = "2" face = "Arial, Helvetica, sans-serif" >Statut</font ></td >
                        <td ><select id = "select" name = "search[family_stat]" >
                                <option value = "" >-- Select --</option >
                                <option >Célibataire</option >
                                <option >Marié</option >
                                <option >Divorcé(e)</option >
                                <option >Veuf(ve)</option >
                            </select ></td >
                    </tr >
                    <tr bgcolor = "#F9FFED" >

                        <td width = "100" ><font size = "2" face = "Arial, Helvetica, sans-serif" >Naissance</font >
                        </td >
                        <td width = "482" ><font size = "2" face = "Arial, Helvetica, sans-serif" >à partir de
                                <select id = "select2" name = "search[birth]" >
                                    <option value = "" >-- Select --</option >
                                    <?php
                                    $yc = date("Y");
                                    $ye = $yc - 18;
                                    $ys = $ye - 50;
                                    for ($ycc = $ys; $ycc <= $ye; $ycc++) {
                                        echo "<option value='$ycc'>$ycc</option>";
                                    }
                                    ?>
                                </select >
                            </font ></td >
                    </tr >
                    <tr bgcolor = "#F9FFED" >

                        <td ><font size = "2" face = "Arial, Helvetica, sans-serif" >Province d'origine</font ></td >
                        <td ><select id = "select4" name = "search[town]" >
                                <option value = "" >-- Select --</option >
                                <?php echo $this->t['job_town']->sel(); ?>
                            </select ></td >
                    </tr >
                    <tr >

                        <td >&nbsp;</td >
                        <td >&nbsp;</td >
                    </tr >
                    <tr >

                        <td bgcolor = "#EAFDFF" >
                            <font size = "2" face = "Arial, Helvetica, sans-serif" >Formation</font ></td >
                        <td bgcolor = "#EAFDFF" ><select id = "select5" name = "search[formation]" >
                                <option value = "" >-- Select --</option >
                                <?php echo $this->t['job_formation']->sel(); ?>
                            </select ></td >
                    </tr >
                    <tr >

                        <td bgcolor = "#EAFDFF" >
                            <font size = "2" face = "Arial, Helvetica, sans-serif" >Informatique</font ></td >
                        <td bgcolor = "#EAFDFF" >
                            <select id = "select6" name = "search[it][]" multiple = 'true' size = '5' >
                                <?php echo $this->t['job_it']->sel(false, array("sel_idFld" => "ct.name")); ?>
                            </select ></td >
                    </tr >
                    <tr >

                        <td bgcolor = "#EAFDFF" ><font size = "2" face = "Arial, Helvetica, sans-serif" >Diplôme</font >
                        </td >
                        <td bgcolor = "#EAFDFF" ><select id = "select7" name = "search[diplome]" >
                                <option value = "" >-- Select --</option >
                                <?php echo $this->t['job_diplome']->sel(); ?>
                            </select ></td >
                    </tr >
                    <tr >

                        <td bgcolor = "#EAFDFF" ><font size = "2" face = "Arial, Helvetica, sans-serif" >Université /
                                Institut</font ></td >
                        <td bgcolor = "#EAFDFF" ><select id = "select8" name = "search[univ]" >
                                <option value = "" >-- Select --</option >
                                <?php echo $this->t['job_univ']->sel(); ?>
                            </select ></td >
                    </tr >
                    <tr >

                        <td bgcolor = "#EAFDFF" ><font size = "2" face = "Arial, Helvetica, sans-serif" >Domaine
                                postulé</font ></td >
                        <td bgcolor = "#EAFDFF" ><select id = "select9" name = "search[domain]" >
                                <option value = "" >-- Select --</option >
                                <?php echo $this->t['job_domain']->sel(); ?>
                            </select ></td >
                    </tr >
                    <tr >

                        <td bgcolor = "#EAFDFF" ><font size = "2" face = "Arial, Helvetica, sans-serif" >Province
                                postulée</font ></td >
                        <td bgcolor = "#EAFDFF" ><select id = "select10" name = "search[prefer_location]" >
                                <option value = "" >-- Select --</option >
                                <?php echo $this->t['job_town']->sel(); ?>
                            </select ></td >
                    </tr >
                    <tr >

                        <td bgcolor = "#EAFDFF" ><font size = "2" face = "Arial, Helvetica, sans-serif" >Durée
                                souhaitée</font ></td >
                        <td bgcolor = "#EAFDFF" ><select id = "select11" name = "search[test_peariod]" >
                                <option value = '' >-- Select --</option >
                                <option value = '1' >1 mois</option >
                                <option value = '2' >2 mois</option >
                                <option value = '3' >3 mois</option >
                                <option value = '6' >6 mois</option >
                                <option value = '' >Indéterminé</option >
                            </select ></td >
                    </tr >
                    </tbody >
                </table >
            </td >
            <td width = "179" valign = "top" >
                <div align = "right" >
                    <p >
                        <input type = "image" width = "227" border = "0" hspace = "9" height = "465" src = "./css/img/search_button2.jpg" >
                    </p >
                </div >
            </td >
        </tr >
        </tbody >
    </table >
</form >

