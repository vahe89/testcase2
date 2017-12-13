<?php $this->header_head();?>
<body >
<div id = 'main_wrapper' >
    <div id = 'header'>
				<div class = 'hwrap wrap_width'>
            <img id = 'main_logo' src = '<?php echo $this->skin?>img/GT-PRO-logo-new.png' >

					<div class='flex'>

						<div class = 'mwrap' >
							<div class = 'menu' >
							<div class='ms_left'></div>

                <ul >
                    <?php
                    global $current_url;
		$mfst = true;
		$defurl=false;

		if(isset($this->v['defpage']) && is_array($this->v['defpage'])){
			if(isset($this->v['defpage']['url']))
				$defurl=$this->v['defpage']['url'];
			if(isset($this->v['defpage']['obj']))
				$defurl=aurl("/".$this->t[$this->v['defpage']['obj']]->obj_slug);
		}
					if(isset($this->v['menu']) && is_array($this->v['menu'])){
                    foreach ($this->v['menu'] as $k => $v) {
                        $subm = false;
                        $sam = false;
                        if (isset($v['_sub']) && is_array($v['_sub'])) {
                            ob_start();
                            echo "<div class='submenuwrap'><ul>";
                            $cls = 'sub_menu';
                            foreach ($v['_sub'] as $sk => $sv) {
																$url = aurl("/");
                                if (isset($sv['_obj']) && $sv['_obj'] != false) {
                                    $url = aurl("/{$this->t[$sv['_obj']]->obj_slug}");
                                } else if (isset($sv['_url']) && $sv['_url'] != false) {
                                    $url = $sv['_url'];
																}

                                $htm = $k;
                                if (isset($sv['_html']) && $sv['_html'] != false)
                                    $htm = $sv['_html'];
																$acls = "";
																if ($defurl==false && strpos($current_url, $url) === 0 && aurl('/') != $current_url && aurl('/') != $url) {
                                    $sam = true;
                                    $acls = 'sub_act';
                                }
                                echo "<li class='{$cls} {$acls}'><a href='{$url}'>{$htm}</a></li>";
                                $acls = '';

                            }
                            echo "</ul></div>";
                            $subm = ob_get_contents();
                            ob_end_clean();
                        }
                        $url = aurl("/");
                        if (isset($v['_obj']) && $v['_obj'] != false) {
                            $url = aurl("/{$this->t[$v['_obj']]->obj_slug}");
                        } else if (isset($v['_url']) && $v['_url'] != false) {
                            $url = $v['_url'];
                        }
                        $htm = $k;
                        if (isset($v['_html']) && $v['_html'] != false)
                            $htm = $v['_html'];
                        $cls = "mmenu";
//var_dump($current_url,$url);			

//var_dump("OKK",$defurl,(strpos($current_url, $url) === 0 && aurl('/')!=$url && aurl('/')!=$current_url) || ($mfst == true && aurl('/') == $current_url), $sam);

												if (
													(strpos($current_url, $url) === 0 && aurl('/')!=$url && aurl('/')!=$current_url) 
													|| ($defurl==false && $mfst == true && aurl('/') == $current_url) 
													
													|| $sam)
													$cls = "mmenu active";

												if(aurl('/')==$current_url && $defurl!=false && strpos($url, $defurl) === 0)
													$cls = "mmenu active";
											
												if ($sam)
    	                        $cls .= " asub";
										
												if ($mfst)
        	                    $cls .= " mfst";
									
												if ($subm != false)
														$cls .= " have_sub";
												
                        $mfst = false;
                        echo "<li class='{$cls}'><a href='{$url}'>{$htm}</a>";
                        echo $subm;
                        echo "</li>";
										}
}	?>
								</ul >
							<div class='ms_right'></div>
						</div >  <!-- .mscroll -->

                <div class = "hmenu_dd btn-group dropdown" >
                    <div class = "btn profilebtn" title = "Admin" >
                        <i class = "icon-user" ></i > <?php echo $this->curUsr['Name'];?></div >
                    <div class = "btn dropdown-toggle drop_btn" >
												<span class = "caret" ></span >
												<div class='drop_bridge'></div>
                        <ul class = "dropdown-menu drop_wrap" >
                            <li >
                                <a href = "<?php echo aurl("/logout"); ?>" title = "Logout" ><i class = "icon-off" ></i > Logout</a>
                            </li >
                        </ul >
                    </div >
                </div >

						</div >  <!-- .menu -->

	        </div > <!-- .flex -->
        </div > <!-- .hwrap -->
    </div > <!-- #header -->
    <div class = 'wrap_width' >
        <div id = 'content' class = 'wrap_width' >
