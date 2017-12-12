<?php
require_once("admin.class.php");

class User extends Admin
{

	function __construct()
	{
		parent::__construct();
	}

	function __destruct()
	{
		parent::__destruct();
	}

	function do_settings()
	{
		if (!$this->isLogged || !$this->isAdmin)
			return false;

		$this->show['conf_menu'] = array(
				"smtp" => array("_url" => "/settings/smtp", "_html" => "SMTP Settings"),
				"notices" => array("_url" => "/settings/notices", "_html" => "Notices Management"),
				"company" => array("_url" => "/settings/company", "_html" => "Company Information"),
		);

		if (!isset($_REQUEST['slug_arr'][1]) || $_REQUEST['slug_arr'][1] == 'smtp') {
			$this->show['conf_page_id'] = "smtp";
			$this->show['redir_url'] = "/settings/smtp";
			$this->show['form_a'] = "p_smtp_settings_test";
			$this->show['conf_opts'] = array(
					"smtp_server" => array("t" => "Server"),
					"smtp_username" => array("t" => "Username"),
					"smtp_password" => array("t" => "Password"),
					"smtp_port" => array("t" => "Port"),
					"smtp_security" => array("t" => "Security", "c" => "select", "opts" => array("tls" => "TLS", "ssl" => "SSL", "none" => "None")),
					"smtp_mail_from" => array("t" => "Alert Mail From"),
			);
			$this->show['conf_save_label'] = "Save and Test";
			$this->show['conf_save_label'] = "Save and Test";

			$this->LookAtVar('config_page', true, true);
		} else if (isset($_REQUEST['slug_arr'][1]) && $_REQUEST['slug_arr'][1] == 'notices') {
			$this->show['conf_page_id'] = "notices";
			$this->show['redir_url'] = "/settings/notices";
			$this->show['conf_opts'] = array(
					"notice_on_1st" => array("t" => "On First Login:", "c" => "checkbox"),
					"notice_on_1st_text" => array("t" => "", "c" => "htmltextarea"),
					"notice_on_next" => array("t" => "On Next Login:", "c" => "checkbox"),
					"notice_on_next_text" => array("t" => "", "c" => "htmltextarea"),
			);
			$this->show['conf_save_label'] = "Save";
			$this->LookAtVar('config_page', true, true);
		} else if (isset($_REQUEST['slug_arr'][1]) && $_REQUEST['slug_arr'][1] == 'company') {
			$this->show['conf_page_id'] = "company";
			$this->show['redir_url'] = "/settings/company";
			$this->show['conf_opts'] = array(
					"b1" => array("c" => "html", "html" => "<table style='float:left;' class='dbo_offpage_table'><tr><td class='dbo_fields_td'><div class='edit_title col legend'> Company Details </div></td><td class='dbo_fields_td'>
				<div class='edit_title col legend'> Address Details </div></td>
				<tr><td class='dbo_fields_td'>"),
					"corp_name" => array("t" => "Name"),
					"corp_website" => array("t" => "Website"),
					"corp_email" => array("t" => "Email"),
					"corp_phone" => array("t" => "Phone"),
					"corp_fax" => array("t" => "Fax"),
					"corp_skype" => array("t" => "Skype ID"),
					"b2" => array("c" => "html", "html" => "</td><td class='dbo_fields_td'>"),
					"corp_addr_street" => array("t" => "Street Address"),
					"corp_addr_city" => array("t" => "City"),
					"corp_addr_state" => array("t" => "Sate"),
					"corp_addr_zip" => array("t" => "Zip"),
					"b3" => array("c" => "html", "html" => "</td></tr></table>"),
			);
			$this->show['conf_save_label'] = "Save";
			$this->LookAtVar('config_page', true, true);
		}

		return true;
	}


	function do_new_pipe(){
		if(!$this->isLogged)
			return false;
		if(!isset($_SESSION['new_pipe_ord']))
			$_SESSION['new_pipe_ord']="desc";
		if(isset($_REQUEST['new_pipe_ord'])){
			if($_REQUEST['new_pipe_ord']=='asc')
				$_SESSION['new_pipe_ord']='asc';
			else
				$_SESSION['new_pipe_ord']='desc';
		}
		$this->db->query($q="select SQL_CALC_FOUND_ROWS id,Client_ID__c from SEOX3_Client__c where Sales_Rep__c={$this->curUsrId} and Status__c!='Inactive' and Prospective_Stage__c='Initial Contact' order by CreatedDate {$_SESSION['new_pipe_ord']} limit 1","nclnt");
		$cc=$this->db->getRow("SELECT FOUND_ROWS() as cnt");
//		$Rid=$this->db->getCol("Client_ID__c","nclnt");
		$_REQUEST['rid']=$this->db->getCol("Client_ID__c","nclnt");
		$oldest=$this->db->getRow($q="select CreatedDate from SEOX3_Client__c where Sales_Rep__c={$this->curUsrId} and Status__c!='Inactive' and Prospective_Stage__c='Initial Contact' order by CreatedDate asc limit 1");
		$o=$this->t['SEOX3_Client__c'];
		if(!isset($_SESSION['new_pipe_start']) || ($_SESSION['new_pipe_start']==false && $cc['cnt']>0))
			$_SESSION['new_pipe_start']=$cc['cnt'];
/*		if(!isset($_REQUEST['rid']) || $_REQUEST['rid']!=$Rid){
			header("Location: ".aurl("/new_pipe/$Rid"));die();
		}*/
		$o->showAdmin("new_pipe",array('left_cnt'=>$cc['cnt'],'sort_n'=>'new_pipe_ord','pipe_next'=>'new_pipe','oldest'=>$oldest['CreatedDate'],"rContext"=>"_newpipe"));
		return true;		
	}

	function do_retry_pipe(){
		if(!$this->isLogged)
			return false;
		if(!isset($_SESSION['retry_ord']))
			$_SESSION['retry_ord']="desc";
		if(isset($_REQUEST['retry_ord'])){
			if($_REQUEST['retry_ord']=='asc')
				$_SESSION['retry_ord']='asc';
			else
				$_SESSION['retry_ord']='desc';
		}
		$this->db->query($q="select SQL_CALC_FOUND_ROWS id,Client_ID__c from SEOX3_Client__c where Sales_Rep__c={$this->curUsrId} and Status__c!='Inactive' and Prospective_Stage__c='Initial - to Retry' and Next_Call_Back__c < NOW() order by CreatedDate {$_SESSION['retry_ord']} limit 1","nclnt");
		$cc=$this->db->getRow("SELECT FOUND_ROWS() as cnt");
		$_REQUEST['rid']=$this->db->getCol("Client_ID__c","nclnt");

		$o=$this->t['SEOX3_Client__c'];
		if(!isset($_SESSION['retry_pipe_start']) || ($_SESSION['retry_pipe_start']==false && $cc['cnt']>0))
			$_SESSION['retry_pipe_start']=$cc['cnt'];

		$o->showAdmin("new_pipe",array('left_cnt'=>$cc['cnt'],'sort_n'=>'retry_ord','pipe_next'=>'retry_pipe',"rContext"=>"_newpipe"));
			return true;		
	}

	function do_miss_cb_pipe(){
		return $this->do_cb_pipe(true);
	}
	function do_cb_pipe($mcb=false){
		if(!$this->isLogged)
			return false;

		$pname='cb_pipe';
		if($mcb==true)
			$pname='miss_cb_pipe';

		if(!isset($_SESSION['cb_pipe_ord']))
			$_SESSION[$pname.'_ord']="asc";
		if(isset($_REQUEST[$pname.'_ord'])){
			if($_REQUEST[$pname.'_ord']=='asc')
				$_SESSION[$pname.'_ord']='asc';
			else
				$_SESSION[$pname.'_ord']='desc';
		}

		if(!isset($_SESSION[$pname.'_filt']))
			$_SESSION[$pname.'_filt']="all";
		if(isset($_REQUEST[$pname.'_filt']) && in_array($_REQUEST[$pname.'_filt'],array('all','todaypast','past','today')))
			$_SESSION[$pname.'_filt']=$_REQUEST[$pname.'_filt'];

		$cbfilt=" date_format(Next_Call_Back__c,'%Y-%m-%d')<=date_format(NOW(),'%Y-%m-%d') ";
		switch($_SESSION[$pname.'_filt']){
			case "today":
				$cbfilt=" date_format(Next_Call_Back__c,'%Y-%m-%d')=date_format(NOW(),'%Y-%m-%d') ";
				break;
			case "todaypast":
				$cbfilt="( date_format(Next_Call_Back__c,'%Y-%m-%d')=date_format(NOW(),'%Y-%m-%d') and Next_Call_Back__c<NOW())";
				break;
			case "past":
				$cbfilt=" date_format(Next_Call_Back__c,'%Y-%m-%d')<date_format(NOW(),'%Y-%m-%d') ";
				break;
		
		}
	
		$prosp=" IN ('Overcome Objections','Promised to Buy','Ready to Close')";
		if($mcb==true)
			$prosp=" = 'Missed Callback'";

		$this->db->query($q="select SQL_CALC_FOUND_ROWS id, Client_ID__c from SEOX3_Client__c where Sales_Rep__c={$this->curUsrId} and Status__c!='Inactive' and Prospective_Stage__c $prosp and $cbfilt order by Next_Call_Back__c {$_SESSION[$pname.'_ord']} limit 1","nclnt");

		$cc=$this->db->getRow("SELECT FOUND_ROWS() as cnt");
		$_REQUEST['rid']=$this->db->getCol("Client_ID__c","nclnt");

		if(!isset($_SESSION[$pname.'_start']) || ($_SESSION[$pname.'_start']==false && $cc['cnt']>0))
			$_SESSION[$pname.'_start']=$cc['cnt'];

		$o=$this->t['SEOX3_Client__c'];
		$o->showAdmin("new_pipe",array('left_cnt'=>$cc['cnt'],'sort_n'=>$pname.'_ord','pipe_next'=>$pname,"rContext"=>"_cbpipe"));
			return true;		
	}

	function do_pipe_history(){
		if(!$this->isLogged)
			return false;
		if( ! (isset($_REQUEST['p']) && $_REQUEST['p']!=false && in_array($_REQUEST['p'],array('new_pipe','cb_pipe','retry_pipe','miss_cb_pipe')))){
		//	$this->setMsg('Wrong arg 1');
			echo "<h3>Wrong arg 1</h3>";
			return true;
		}
		if(is_array($_SESSION['pipes_history'][$_REQUEST['p']]) && count($_SESSION['pipes_history'][$_REQUEST['p']])>0){
			$this->t['SEOX3_Client__c']->showAdmin("custom_view_ajaxlist",array('ownHeader'=>true,'queryWhere'=>array("ct.id in ('".implode("','",$_SESSION['pipes_history'][$_REQUEST['p']])."')")));
			die();
		}else
			die('<h1 style="white-space:nowrap;"><br>No history for this session</h1>');

	}
	
	function do_next_pipe(){
		if(!$this->isLogged)
			return false;

		if( ! (isset($_REQUEST['result']) && $_REQUEST['result']!=false))
			$this->setMsg('Wrong arg 1');
		if( ! (isset($_REQUEST['cid']) && $_REQUEST['cid']!=false))
			$this->setMsg('Wrong arg 2');
		if( ! (isset($_REQUEST['sfid']) && $_REQUEST['sfid']!=false))
			$this->setMsg('Wrong arg 3');

		if(!isset($_SESSION['pipes_history']))
			$_SESSION['pipes_history']=array();

		if(!isset($_SESSION['pipes_history'][$_REQUEST['next']]))
			$_SESSION['pipes_history'][$_REQUEST['next']]=array();

		$_SESSION['pipes_history'][$_REQUEST['next']][$_REQUEST['cid']]=$_REQUEST['cid'];

		switch($_REQUEST['result']){
		case "no_answer":
			$ntit="No Answer (by {$this->curUsr['Name']})";
			$nbod="No Answer";
			$dbret=$this->cadb("i",$this->t['Note'],array("ParentId"=>$_REQUEST['sfid'],"Title"=>$ntit,"Body"=>$nbod));
			$rdata=array("ret.w.id"=>$_REQUEST['cid'],"Prospective_Stage__c"=>'Initial - to Retry',"Next_Call_Back__c"=>date("Y-m-d H:i:s",strtotime("+1 days")));
				if($_REQUEST['next']=='cb_pipe'){
					unset($rdata['Prospective_Stage__c']);
				}
			$dbret=$this->cadb("u",$this->t['SEOX3_Client__c'],$rdata);
			break;
		case "miss_cb":
			$ntit="Missed Callback at {$_REQUEST['cb_date']} (by {$this->curUsr['Name']})";
			$nbod="Missed Callback at {$_REQUEST['cb_date']}";
			$dbret=$this->cadb("i",$this->t['Note'],array("ParentId"=>$_REQUEST['sfid'],"Title"=>$ntit,"Body"=>$nbod));
			$rdata=array("ret.w.id"=>$_REQUEST['cid'],"Prospective_Stage__c"=>'Missed Callback');
			$dbret=$this->cadb("u",$this->t['SEOX3_Client__c'],$rdata);
			break;
		case "left_vm":
				$lvn="";
				if(isset($_REQUEST['note']))
					$lvn="[{$_REQUEST['note']}]";
				$ntit="Left VM (by {$this->curUsr['Name']}) $lvn";
				$nbod="Left VM\n$lvn";
				$rdata=array("ret.w.id"=>$_REQUEST['cid'],"Prospective_Stage__c"=>'Initial - to Retry',"Next_Call_Back__c"=>date("Y-m-d H:i:s",strtotime("+2 days")));
				if($_REQUEST['next']=='cb_pipe'){
					unset($rdata['Prospective_Stage__c']);
				}
			$this->cadb("i",$this->t['Note'],array("ParentId"=>$_REQUEST['sfid'],"Title"=>$ntit,"Body"=>$nbod));
			$dbret=$this->cadb("u",$this->t['SEOX3_Client__c'],$rdata);
				break;

			case "cb":
				$nbod="Callback Note (by {$this->curUsr['Name']})";
				$ntit=($_REQUEST['note']==false?$nbod:$_REQUEST['note']);
				$dbret=$this->cadb("i",$this->t['Note'],array("ParentId"=>$_REQUEST['sfid'],"Title"=>$ntit,"Body"=>$nbod));
				if( ! (isset($_REQUEST['cb_ps']) && $_REQUEST['cb_ps']!=false && in_array($_REQUEST['cb_ps'],array('Overcome Objections','Promised to Buy','Ready to Close')))){
					$this->setMsg('CB sel arg ERR');
					break;
				}
				if( ! (isset($_REQUEST['cb_date']) && $_REQUEST['cb_date']!=false && $stt=strtotime($_REQUEST['cb_date']))){
					$this->setMsg('CB date arg ERR');
					break;
				}
				$dbret=$this->cadb("u",$this->t['SEOX3_Client__c'],array("ret.w.id"=>$_REQUEST['cid'],"Prospective_Stage__c"=>$_REQUEST['cb_ps'],"Next_Call_Back__c"=>date("Y-m-d H:i:s",$stt)));

				$s_er=$e_er=true;
				if(!(isset($_REQUEST['e_skip']) && $_REQUEST['e_skip']=='1') && check_arrX($_REQUEST,'e_subj','e_body')){
					$e_er=$this->send_msg(array("obj"=>"SEOX3_Client__c","filtType"=>"i","filtVal"=>$_REQUEST['sfid'],'mT'=>'Email',
						'subj'=>stripcslashes($_REQUEST['e_subj']),'body'=>stripcslashes($_REQUEST['e_body'])
					));
				}
				if(!(isset($_REQUEST['s_skip']) && $_REQUEST['s_skip']=='1') && check_arrX($_REQUEST,'s_body')){
					$s_er=$this->send_msg(array("obj"=>"SEOX3_Client__c","filtType"=>"i","filtVal"=>$_REQUEST['sfid'],'mT'=>'TwillioSMS','hSubj'=>1,
						'body'=>stripcslashes($_REQUEST['s_body'])
					));
				}
				$mer=false;
				if($e_er!==true)
					$mer.="EMAIL ERROR: $e_er";
				if($s_er!==true){
					if($mer!=false)
						$mer.="<br><br>";
					$mer.="SMS ERROR: $s_er";
				}
				if($mer!=false)
					$this->setMsg($mer);
				break;

			case "lost":
				if(isset($_REQUEST['note']) && $_REQUEST['note']!=false){
				$ntit="Lost Note (by {$this->curUsr['Name']}) [{$_REQUEST['note']}]";
				$nbod=$_REQUEST['note']==false?$ntit:$_REQUEST['note'];
				$this->cadb("i",$this->t['Note'],array("ParentId"=>$_REQUEST['sfid'],"Title"=>$ntit,"Body"=>$nbod));
				}

				$data=array("ret.w.id"=>$_REQUEST['cid'],"Prospective_Stage__c"=>"Lost");
				if(isset($_REQUEST['lost_res']) && $_REQUEST['lost_res']!=false && in_array($_REQUEST['lost_res'],$this->t['SEOX3_Client__c']->fctrls['Lost_Reason__c']['opts'])){
					$data['Lost_Reason__c']=$_REQUEST['lost_res'];
				}
				$this->cadb("u",$this->t['SEOX3_Client__c'],$data);
				break;
			case "invalid":
				$nbod="Invalid Info Note (by {$this->curUsr['Name']})";
				$ntit=$nbod;
				$this->cadb("i",$this->t['Note'],array("ParentId"=>$_REQUEST['sfid'],"Title"=>$ntit,"Body"=>$nbod));

				$data=array("ret.w.id"=>$_REQUEST['cid'],"Prospective_Stage__c"=>"Invalid Lead");
				$this->cadb("u",$this->t['SEOX3_Client__c'],$data);
				break;
			case "closed":
				$nbod="CLOSED Note (by {$this->curUsr['Name']})";
				$ntit=($_REQUEST['note']==false?$nbod:$_REQUEST['note']);
				$dbret=$this->cadb("i",$this->t['Note'],array("ParentId"=>$_REQUEST['sfid'],"Title"=>$ntit,"Body"=>$nbod));

				$ntit="Sold Note [{$_REQUEST['prod']}:\${$_REQUEST['amount']}] (by {$this->curUsr['Name']})";
				$nbod="Sold by {$this->curUsr['Name']}:\nProduct: {$_REQUEST['prod']}\nPrice: \${$_REQUEST['amount']}\n";
				$dbret=$this->cadb("i",$this->t['Note'],array("ParentId"=>$_REQUEST['sfid'],"Title"=>$ntit,"Body"=>$nbod));

					$dbret=$this->cadb("u",$this->t['SEOX3_Client__c'],array("ret.w.id"=>$_REQUEST['cid'],"Prospective_Stage__c"=>'Closed',"Next_Call_Back__c"=>false));
				break;
				
			default:
				$this->setMsg('Switch param ERR');
		}
//			$this->do_new_pipe();
		//			return true;
		if(isset($_REQUEST['next']) && $_REQUEST['next']!=false){
			header('Location: '.aurl("/{$_REQUEST['next']}"));
		}else
			header('Location: '.aurl("/"));
		die();
	}

	function do_client_btns(){
		if(!$this->isLogged)
			return false;

		if( ! (isset($_REQUEST['cid']) && $_REQUEST['cid']!=false))
			$this->setMsg('Wrong arg 2');
		if( ! (isset($_REQUEST['sfid']) && $_REQUEST['sfid']!=false))
			$this->setMsg('Wrong arg 3');
		if( ! (isset($_REQUEST['s']) && $_REQUEST['s']!=false))
			$this->setMsg('Wrong arg 4');

		if(isset($_REQUEST['tpl']) && $_REQUEST['tpl']!=false){
			$ffid="_clnbtnf";
			echo "<form id='{$ffid}' action='".aurl("/client_btns")."' method='POST'>";
			echo "<input type='hidden' name='cid' value='{$_REQUEST['cid']}'>";
			echo "<input type='hidden' name='sfid' value='{$_REQUEST['sfid']}'>";
			echo "<input type='hidden' name='t' value='{$_REQUEST['t']}'>";
			echo "<input type='hidden' name='s' value='{$_REQUEST['s']}'>";
			switch($_REQUEST['t']){
			case "leftvm":
				echo "Note:<textarea name='note'></textarea><br>";
				break;
			case "cb":
				echo "Prospective stage:<br><select required='true' name='cb_ps'><option value=''>--Select--</option><option value='Overcome Objections'>Overcome Objections</option><option value='Promised to Buy'>Promised to Buy</option><option value='Ready to Close'>Ready to Close</option></select><br><br>";
				echo "Callback Time:<br><input id='{$ffid}_cb' required='true' type='text' name='cb_date' value='' class='dbo_datetime'><br><br>";
				echo "Note:<br><textarea name='note'></textarea><br>";
				echo "<script type='text/javascript'>controls_init();jQuery('#{$ffid}').bind('submit',function(){var cbd=jQuery('#{$ffid}_cb').datetimepicker('getDate');nd=new Date();if(cbd-nd<900*1000){alert('Callback date should be set and be minimum at 15 mins in future');return false;}});</script>";
				break;
					
			}
			echo "<br><input type='submit' value='Save'></form>";
			die();			
		}
		$emsg="Error";	
		switch($_REQUEST['t']){
		case "noans":
			$ntit="No Answer (by {$this->curUsr['Name']})";
			$nbod="No Answer";
			$dbret=$this->cadb("i",$this->t['Note'],array("ParentId"=>$_REQUEST['sfid'],"Title"=>$ntit,"Body"=>$nbod));
			$dbret=$this->cadb("u",$this->t['SEOX3_Client__c'],array("ret.w.id"=>$_REQUEST['cid'],"Prospective_Stage__c"=>'Initial - to Retry',"Next_Call_Back__c"=>date("Y-m-d H:i:s",strtotime("+1 days"))));
			$emsg="Changes saved";
			break;
		case "leftvm":
				$lvn="";
				if(isset($_REQUEST['note']))
					$lvn="[{$_REQUEST['note']}]";
				$ntit="Left VM (by {$this->curUsr['Name']})";
				$nbod="Left VM\n$lvn";
			$this->cadb("i",$this->t['Note'],array("ParentId"=>$_REQUEST['sfid'],"Title"=>$ntit,"Body"=>$nbod));
			$dbret=$this->cadb("u",$this->t['SEOX3_Client__c'],array("ret.w.id"=>$_REQUEST['cid'],"Prospective_Stage__c"=>'Initial - to Retry',"Next_Call_Back__c"=>date("Y-m-d H:i:s",strtotime("+2 days"))));
				$this->setMsg("Changes saved");
				header("Location: ".aurl("/".$this->t['SEOX3_Client__c']->obj_slug."/{$_REQUEST['s']}"));
				die();
				break;

			case "cb":
				$nbod="Callback Note (by {$this->curUsr['Name']})";
				$ntit=($_REQUEST['note']==false?$nbod:$_REQUEST['note']);
				$dbret=$this->cadb("i",$this->t['Note'],array("ParentId"=>$_REQUEST['sfid'],"Title"=>$ntit,"Body"=>$nbod));
				if( ! (isset($_REQUEST['cb_ps']) && $_REQUEST['cb_ps']!=false && in_array($_REQUEST['cb_ps'],array('Overcome Objections','Promised to Buy','Ready to Close')))){
					$this->setMsg('CB sel arg ERR');
					header("Location: ".aurl("/".$this->t['SEOX3_Client__c']->obj_slug."/{$_REQUEST['s']}"));
					die();
					break;
				}
				if( ! (isset($_REQUEST['cb_date']) && $_REQUEST['cb_date']!=false && $stt=strtotime($_REQUEST['cb_date']))){
					$this->setMsg('CB date arg ERR');
					header("Location: ".aurl("/".$this->t['SEOX3_Client__c']->obj_slug."/{$_REQUEST['s']}"));
					die();
					break;
				}
				$dbret=$this->cadb("u",$this->t['SEOX3_Client__c'],$q=array("ret.w.id"=>$_REQUEST['cid'],"Prospective_Stage__c"=>$_REQUEST['cb_ps'],"Next_Call_Back__c"=>date("Y-m-d H:i:s",$stt)));
				$this->setMsg("Changes saved");
				header("Location: ".aurl("/".$this->t['SEOX3_Client__c']->obj_slug."/{$_REQUEST['s']}"));
				die();
				break;

			case "asent":
				$nbod="Analysis Sent (by {$this->curUsr['Name']})";
				$ntit=$nbod;
				$dbret=$this->cadb("i",$this->t['Note'],array("ParentId"=>$_REQUEST['sfid'],"Title"=>$ntit,"Body"=>$nbod));
				$emsg="Changes saved";

				break;
				
			default:
				$this->setMsg('Switch param ERR');
		}
		die("<br><br><h1>$emsg </h1>");
	}




	function do_start_dialer(){
		if(!$this->isLogged)
			return false;
		if($this->curUsr['Phone_Burner_Token__c']==false){
			$this->setMsg("You can't use dialer, ask your manager to assign you access token in SF");
			return false;
		}
		if(isset($_REQUEST['_idc_referer']) && is_object($this->t[$_REQUEST['_idc_referer']]) && isset($_REQUEST['_idc_checked']) && $_REQUEST['_idc_checked']!=false){
			$o=$this->t[$_REQUEST['_idc_referer']];
			$uid=md5(microtime().mt_rand(0,99999));
			$this->db->query($q="select * from $o->oname where id in (".$this->db->escape($_REQUEST['_idc_checked']).")",'dlr');
			if($_REQUEST['_idc_referer']=='SEOX3_Client__c'){
				$map=array(
						"name"=>true,
						"email"=>"E_Mail__c",
						"phone"=>"Phone1__c",
						"phone_type"=>1,
						"address1"=>"Street__c",
						"city"=>"City__c",
						"state"=>"State__c",
						"zip"=>"Zip__c",
						"country"=>"US",
						"ad_code"=>"Marketing_Source__c",
						"notes"=>"Notes__c",
						"viewed"=>1,
						"category_id"=>0,
						"tags"=>array("Primary_Interest__c","autodial_{$uid}","$uid","Marketing_Source__c"),
						"custom_fields"=>array("Primary Interest"=>"Primary_Interest__c","Marketing Source"=>"Marketing_Source__c","Created Date"=>array("v"=>"CreatedDate","t"=>3),"Prospective Stage"=>"Prospective_Stage__c"),
						"lead_id"=>"SF_Id",
					);
			}else if($_REQUEST['_idc_referer']=='FU_User__c'){
				$map=array(
						"name"=>true,
						"email"=>"E_Mail__c",
						"phone"=>"Phone__c",
						"phone_type"=>1,
						"address1"=>"Street_Address__c",
						"city"=>"City__c",
						"state"=>"State__c",
						"zip"=>"Zip__c",
						"country"=>"US",
//						"ad_code"=>"Marketing_Source__c",
						"notes"=>"Notes__c",
						"viewed"=>1,
						"category_id"=>0,
						"tags"=>array("FU_Users","autodial_{$uid}","$uid"),
						"custom_fields"=>array("Created Date"=>array("v"=>"CreatedDate","t"=>3),"Total Amount Approved"=>"Total_Amount_Approved__c","Total Final Success Fee"=>"Total_Final_Success_Fee__c"),
						"lead_id"=>"SF_Id",
					);

			}else{
				$this->setMsg("ERROR: This object not yet supported.");
	      return false;
							
			}
			$contacts=array();
			while($row=$this->db->next('dlr')){
				$u=new stdClass();
				foreach($map as $mk=>$mv){
					if($mk=="name" && $mv=true){
						$t=explode(" ",$row['Name']);
						$u->first_name=$t[0];
						if(isset($t[1]))
							$u->last_name=$t[1];
						else
							$u->last_name=null;
					}
					else if($mk=="custom_fields" && is_array($mv)){
						$ta=array();
						foreach($mv as $tk=>$tv){
							$to=new stdClass();
							$to->name=$tk;
							$to->type=1;
							if(is_array($tv)){
								if(isset($tv['v']) && isset($row[$tv['v']]))
									$to->value=$row[$tv['v']];
								else
									$to->value=$tv['v'];
								if(isset($tv['t']))
									$to->type=$tv['t'];
							}else{
								if(isset($row[$tv]))
									$to->value=$row[$tv];
								else
									$to->value=$tv;

							}
							$ta[]=$to;
						}
						if(count($ta)>0)
							$u->custom_fields=$ta;
					}
					else if(is_array($mv)){
						$ta=array();
						foreach($mv as $tv){
								if(isset($row[$tv]))
									$ta[]=$row[$tv];
								else
									$ta[]=$tv;
						}
						if(count($ta)>0)
							$u->{$mk}=$ta;
					}
					else if(isset($row[$mv])){
						$u->{$mk}=$row[$mv];
					}else
						$u->{$mk}=$mv;

				}
				$u->phone=preg_replace('#[^0-9]#',"",$u->phone);
				$contacts[]=$u;	
			}
			$dsa=array("contacts"=>$contacts,"callbacks"=>array("callback_type"=>"api_calldone","callback"=>"http://sla.99th-floor.com/dialapi"));
			//echo json_encode($dsa);
		$ret=FB_query($this->curUsr['Phone_Burner_Token__c'],"POST","/dialsession",json_encode($dsa));
		$dsa=json_decode($ret,true);
		$this->show['dsa']=$dsa;
		$this->LookAtVar("fb_dialer",true,true);
		}
		return true;
	}

	function do_start_dialer_old(){
		if(!$this->isLogged)
			return false;

		if(isset($_REQUEST['_idc_referer']) && is_object($this->t[$_REQUEST['_idc_referer']]) && isset($_REQUEST['_idc_checked']) && $_REQUEST['_idc_checked']!=false){
			$o=$this->t[$_REQUEST['_idc_referer']];
			$this->db->query($q="select * from $o->oname where id in (".$this->db->escape($_REQUEST['_idc_checked']).")",'dlr');
			global $root_path;
			$dir=$root_path."/dlr";
			if(!is_dir($dir))
				mkdir($dir);
			$fn="dlr_".time()."_".mt_rand(9,9999).".csv";
			$f=fopen("{$dir}/$fn","w");
			fwrite($f,"Client #, Name , Phone, URL, Notes\n");
			$phone_arr=array("Phone1__c","Phone2__c");
			while($row=$this->db->next('dlr')){
				$row['Notes__c']=str_replace(array("\n","\r","\t"),array('\n','\r','\t'),$row['Notes__c']);
				$ffs=array("Client_ID__c","Name",$phone_arr,"null","Notes__c");
				$ta=array();
				foreach($ffs as $fs){
					if(is_array($fs)){
						$t="null";
						foreach($fs as $fs2){
							if(isset($row[$fs2])){
								$t=$row[$fs2];
								if(in_array($fs2,$phone_arr))
									$t=preg_replace("#[^0-9]#im","",$t);
								break;
							}
						}
						$ta[]=$t;
					}else{
						if(isset($row[$fs]))
							$t=$row[$fs];
						else
							$t=$fs;
						if(in_array($fs,$phone_arr))
							$t=preg_replace("#[^0-9]#im","",$t);
						$ta[]=$t;
					}
				}

				fwrite($f,implode(",",$ta)."\n");
			}
			fclose($f);
			$sd=scandir($dir);
			foreach($sd as $dfn){
				$ta=explode("_",$dfn);
				if($dfn=="." || $dfn=="..")
					continue;
				if(!is_numeric((int)$ta[1]) || time()>((int)$ta[1])+86400)
					unlink("{$dir}/$dfn");
			}
		// http://207.182.144.202/getCSV.php?url=http%3A%2F%2Fppmpgroup.force.com%2Fservlet%2Fservlet.FileDownload%3Ffile%3D01570000002ZnZDAA0
			header("Location: http://198.245.75.186/getCSV.php?url=".urlencode(aurl("/dlr/$fn")));
			die();
		}else
			return false;
	}

	function do_dialapi(){
		$d=file_get_contents("php://input");
		$a=json_decode($d,true);
		if(isset($a['contact']['lead_id'])){
			$u=$this->db->getRow("select * from SEOX3_Client__c where SF_Id='{$a['contact']['lead_id']}'");
			$ntit="Auto PB dialer: {$a['status']}";
			$nbod="Status: {$a['status']}\nIs answered?:".($a['connected']==1?"Yes":"No")."\nDuration: {$a['duration']}\nNotes: {$a['contact']['notes']}";
			
			$this->cadb("i",$this->t['Note'],array("ParentId"=>$u['SF_Id'],"Title"=>$ntit,"Body"=>$nbod));	
		}
		die('OK');
	}

	function do_formstackview(){
		if(!$this->isLogged)
			return false;
		$CD=dirname(__FILE__);	
		$this->showHeader();
		require_once("{$CD}/FSapi.php");
		$ff=new FormstackApi("0ee4111ebbf1ef9b6dd7de9ee0c879ba");
		$sa=$ff->getSubmissionDetails($_REQUEST['rid'],'auau2000');
		$fa=$ff->getFormDetails($sa['form']);
		$fva=array();
		foreach($fa['fields'] as $fv){
			$fva[$fv['id']]=$fv;
		}
		echo "<center><style>.fsrow td {padding:5px;}</style>";	
		echo "<h2>{$fa['name']} ({$_REQUEST['rid']})</h2>";
		echo "<table>";
		foreach($sa['data'] as $dv){
			echo "<tr class='fsrow'> <td style='text-align:right'><b>{$fva[$dv['field']]['label']}:</b><td><td> {$dv['value']}</td></tr>";
		}
		echo "</table>";
		echo "</center>";
		$this->showFooter();
		return true;
	}

	function do_mails_import_export(){
		if(!$this->isLogged)
			return false;
		$this->LookAtVar("emails_imp_exp",true,true,true);
		return true;
	}

	function set_missed_callbacks(){
		$q="select * from SEOX3_Client__c where Next_Call_Back__c<date_sub(NOW(),interval 2 hour)";
		while($this->db->qnext($q,"MCBD")){
		
		}	
	}

	function next_cb_mod($o,$fn,$v,$ret){
		$ts=$o->cD["{$fn}_ts"];
		$Cts=time();
		$tod=date("Y-m-d",$Cts);
		$cbD=date("Y-m-d",$ts);
		$tf=false;
		$color="";
		if($cbD==$tod)
			$tf=true;
		if($tf==true && $ts>$Cts){
			$color="green";
			$diff=$ts-$Cts;
			$hT=$mT;
			$h=floor($diff/60/60);
			if($h!=0)
				$hT=" $h Hr(s)";
			$m=floor($diff/60%60);
			$mT=" $m Min(s)";
			$v.=" [ In{$hT}{$mT} ! ]";
		}else if($tf==true && $ts<=$Cts)
			$color="orange";
		else if($ts<$Cts)
			$color="red";
		else if($ts>$Cts)
			$color="blue";
		return "<span style='color:$color;font-weight:bold;'>$v</span>";
	}

	function leadgrade_mod($o,$fn,$v,$ret){
		$color="black";	
		switch(strtolower($v)){
			case "av":
			case "au":
				$color="green";
				break;
			case "bv":
			case "bu":
				$color="blue";
				break;
			case "cv":
			case "cu":
				$color="orange";
				break;
			case "dv":
			case "du":
				$color="brown";
				break;
			case "ev":
			case "eu":
				$color="red";
				break;
		}
		return "<span style='color:$color;font-weight:bold'>$v</span>";
	}

	function do_tlist(){
		$v=array(
				"no_user_filter_allowed"=>true,
				"list"=>array("test1","test2"),
				);

		$data=array(
				array("test1"=>"row1 col1","test2"=>"row1 col2"),
				array("test1"=>"row2 ol1","test2"=>"row2 col2"),
				);

		$o=$this->new_vobj("test2",$data,array(),$v);

		$o->showAdmin("custom_view_list");
		return true;


	}

	function rend_lead_hide($o,$cx,$k,$pk,$v,$pv,$fo){
		if($pk=="rows"){
			if($cx==="view_newpipe"){
				if(isset($v['title']) && preg_match("#Next Call-Back Note|Broker Personal Detail|Stats#im",$v['title']))
					return false;
			}else{

				if(isset($v['title'])){
					$pi=explode(" - ",$o->cD['Primary_Interest__c']);
					$pi=$pi[0];
					if(strpos(strtolower($v['title']),"qualifications")!==false){
						$skip=true;
						if(preg_match("#{$pi}[ ]+Qualifications#i",$v['title'])){
							$skip=false;
						}
					//						if($skip)return false;
					}

					if(strtolower($v['title'])=="additional info" && strpos(strtolower($o->cD['Primary_Interest__c']),"analysis")===false){
											return false;
					}
				}
			}
			if(isset($v['flds'])){
				$skip=true;
				foreach($v['flds'] as $ff){
					if($o->cD[$ff]!=false){
						$skip=false;
						break;
					}
				}
								if($skip)	return false;
			}
		}
		else if($cx==="view_newpipe"){
			if(isset($v['fld']) && in_array($v['fld'],
				array("Next_Call_Back__c","Prospective_Stage__c","Prospective_Stage_Date__c",
				"Invalid_Reason__c","Lost_Reason__c"))){
				return false;
			}
		}
		else if($cx==="view_cbpipe"){
			if(isset($v['fld']) && in_array($v['fld'],
				array("Invalid_Reason__c","Lost_Reason__c"))){
				return false;
			}
		}

	}


    function do_stplist(){
//		var_dump($_REQUEST);
        $res=$this->db->query("select * from {$this->db->db_prefix}stop_list");
		$data=array();
		$v=array();
        if($res!==false){
            $v=array(
                "no_user_filter_allowed"=>true,

                '_sort'=>array('srch'=>'desc'),
                "list"=>array('_idc_',"srch"=>array("t"=>"Search"),"place"=>array("t"=>"Place"),"obj"=>array("t"=>"Object")
				),
            );
            while ($item=$this->db->next()){
            	$tempArrayForData=array();
            	foreach ($item as $key=>$item2){

                    $tempArrayForData[$key]=$item2;
				}
                array_push($data, $tempArrayForData);
			}
		}

        $oStop=$this->new_vobj("stop_list",$data,array(),$v);

        $oStop->showAdmin("custom_view_list");
        return true;
    }

    function do_messCampList(){
//		var_dump($_REQUEST);
        $res=$this->db->query("select * from {$this->db->db_prefix}message_campaign__c");
        $data=array();
        $v=array();
        $v=array(
            "no_user_filter_allowed"=>true,

            '_sort'=>array('Describe__c'=>'desc'),
            "list"=>array('_idc_',"Describe__c","Type__c","Recepients_Count__c",'Total_Processed__c','Total_Messages_Queue__c','Total_Success__c','Total_Failed__c','Total_Skipped__c','Total_NOT_Processed__c','Total_DNC_Blocked__c'
            ),
        );
        $tempArrayForData=array();

        $listOfAccountedFields=array('Recepients_Count__c','Total_Processed__c','Total_Messages_Queue__c','Total_Success__c','Total_Failed__c','Total_Skipped__c','Total_NOT_Processed__c','Total_DNC_Blocked__c');
        if($res!==false){
            while ($row=$this->db->next()){
                $mtype='';
                if(strpos(strtolower($row['Type__c']),'sms')!==false){
                	$mtype='sms';
				}else{
                    if(strpos(strtolower($row['Type__c']),'email')!==false)
                    	$mtype='email';
                }
				if($row['Describe__c']!==null)
                if(isset($tempArrayForData[$row['Describe__c']][$mtype])){
                	foreach ($listOfAccountedFields as $accFld)
                    	$tempArrayForData[$row['Describe__c']][$mtype][$accFld]+=$row[$accFld];
				}else{
                    foreach ($listOfAccountedFields as $accFld)
                        $tempArrayForData[$row['Describe__c']][$mtype][$accFld]=$row[$accFld];
				}

            }

            foreach ($tempArrayForData as $key=>$item){
            	foreach ($tempArrayForData[$key] as $key2=>$item2){
                    $tempArrayForDataInTable=array();
                    $tempArrayForDataInTable['Describe__c']=$key;
                    $tempArrayForDataInTable['Type__c']=$key2;
            		foreach ($tempArrayForData[$key][$key2] as $key3=>$item3){
                        $tempArrayForDataInTable[$key3]=$item3;
					}
                    array_push($data, $tempArrayForDataInTable);
				}
			}
        }

        $oStop=$this->new_vobj("messageCampaignStatistic",$data,array(),$v);

        $oStop->showAdmin("custom_view_list");
        return true;
    }


    function draw_btn_filterToStopList($k,$opts,$oname,$tblCode){

        $ret= "
				<input id='$id' type='button' value='$name' class='$class'>

				<script type='text/javascript'>
				jQuery('#{$id}').bind('click',function(){
						
						";
            $ret.="
						function testAjax(handleData) {
						var srch=$('#dbo_datatable_{$obj}_{$onlist}_filter input').val();
						
                      $.ajax({
                        url:\"blaster\",  
                        data: { srch: srch,obj:'{$_REQUEST['obj']}'},
                        success:function(data2) {
//                        alert(data2);
                        location.reload();
                          handleData(data2); 
                          
                        }
                      });
                    }
                    testAjax(function(output){
//                    location.reload();
                    });
						
						";
        $ret.="
				});
			</script>
				";
        return $ret;
    }




		function do_junk_filter(){

			$pb=500;
			$pass='JUYwf22344';

			$this->db->query("delete from msgs_batches where upd<date_sub(NOW(),interval 30 day)");
			if(!($this->isAdmin || (isset($_REQUEST['pass']) && $_REQUEST['pass']==$pass)) && isset($_REQUEST['bid'])){
					$rr=$this->db->getRow("select count(*) as cnt from msgs_batches where jf_id={$_REQUEST['bid']} and status=0");
					if($rr['cnt']>0)
						$_REQUEST['pass']=$pass;
				
			}

			if($this->isAdmin || (isset($_REQUEST['pass']) && $_REQUEST['pass']==$pass)){

				if(isset($_REQUEST['cancel']) && $_REQUEST['cancel']==1 && isset($_REQUEST['bid']) && $_REQUEST['bid']!=false){
					$this->db->query("delete from msgs_batches where jf_id={$_REQUEST['bid']}");
					die("OK");
				}

				if($_REQUEST['sf_save']=='1'){
					$name=time();
					$val = addslashes(file_get_contents("php://input"));
					$this->db->query("insert into sys_config (`name`,`val`) VALUES ({$name}, '{$val}')");
					echo mysql_insert_id();
					die();
				}

				if(!$this->isAdmin){

					$this->set('isAdmin',true);
					$this->set('isLogged', true);
					$this->set('rfull',true);
					$this->set('access',array("all"));
					$this->set('userId',0);
				}
				$this->set('v',array("objs"=>array("stop_list"=>$this->vs['SLA']['objs']['stop_list'])));


				if(isset($_REQUEST['sendmsg']) && $_REQUEST['sendmsg']=="Send!"){
					$sendRet=$this->send_msg($_SESSION['send_msg_req']);
					//var_dump($sendRet);die();
					$msg=$_SESSION['send_msg_req'];
					unset($_SESSION['send_msg_req']);
					if($sendRet===true){
						$brr=$this->db->getRow("select id from msgs_batches where jf_id={$_REQUEST['bid']} and status=0 order by id asc limit 1");
						$this->db->query("update msgs_batches set status=1 where id={$brr['id']}");
						$brr=$this->db->getRow("select count(*) as cnt from msgs_batches where jf_id={$_REQUEST['bid']} and status=0 order by id asc");
						if($brr['cnt']==0){
							$this->db->query("delete from msgs_batches where jf_id={$_REQUEST['bid']}");
							if(isset($msg['sf_redir']) && $msg['sf_redir']!=false){
								header("Location: https://{$msg['sf_redir']}");
								die();
							}
							die("<script type='text/javascript'>parent.jQuery.fancybox('Sending started',{href:null,content:'<h1 style=\"white-space:nowrap;\">Sending started....</h1>',type:'html'});</script>");
						}else{
							header("Location: ".aurl("/junk_filter?bid={$_REQUEST['bid']}"));
							die();
						}
					}else{
						echo "<center><h2 style='color:red'>ERROR: $sendRet</h2></center>";
					}
				}


				if(isset($_REQUEST['_idc_btn']) && $_REQUEST['_idc_btn']!=false){
					if($_REQUEST['_idc_btn']=='Add filter to Block List' && trim($_REQUEST['_idc_srch'])!=""){
						$this->db->query("replace into stop_list (`srch`) VALUES ('{$_REQUEST['_idc_srch']}')");
					}else if($_REQUEST['_idc_checked']!=false){
						$ids=$_REQUEST['_idc_checked'];
						if($_REQUEST['_idc_btn']=='Disable') {
							$this->db->query("update stop_list set `enabled`=0 where `id` in ({$ids})");
						}
						elseif($_REQUEST['_idc_btn']=='Enable'){
							$this->db->query("update stop_list set `enabled`=1 where `id` in ({$ids})");
						}
						elseif($_REQUEST['_idc_btn']=='Delete'){
							$this->db->query("delete from stop_list where `id` in ({$ids})");
						}
						elseif($_SESSION['send_msg_req']['obj']=="SEOX3_Client__c" && $_REQUEST['_idc_btn']=='Invalid-DNC'){
							$brr=$this->db->getRow("select id,obj_ids from msgs_batches where jf_id={$_REQUEST['bid']} and status=0 order by id asc limit 1");
							$sfd=array();
							$dncc=array();
							$ids="'".str_replace(",","','",$ids)."'";
							$qret=SF_q("select Id,Phone1__c,E_Mail__c from {$_SESSION['send_msg_req']['obj']} where Id in ({$ids})");
							//while($row=$this->db->next('invq')){
							foreach($qret as $row){
								if(trim($row['Id'])!=false)
									$brr['obj_ids']=preg_replace("#{$row['Id']}[,]|[,]{$row['_Id']}$#","",$brr['obj_ids']);
								$t=array('Id'=>$row['Id'],'Prospective_Stage__c'=>'Invalid Lead');
								$t2=array("Email__c"=>$row['E_Mail__c'],'Phone__c'=>$row['Phone1__c']);
								$sfd[]=$t;
								$dncc[]=$t2;
								if(count($sfd)>100){
									SF_act($_SESSION['send_msg_req']['obj'],$sfd);
									SF_act("DNC__c",$dncc);
									$sfd=array();
									$dncc=array();
								}
							}
							$this->db->query("update msgs_batches set obj_ids='{$brr['obj_ids']}' where id={$brr['id']}");
							if(count($sfd)>0){
								SF_act($_SESSION['send_msg_req']['obj'],$sfd);
								SF_act("DNC__c",$dncc);
								//									var_dump($sfd,$dncc);
								$sfd=array();
								$dncc=array();
							}
						}
						}
						if(isset($_REQUEST['ajax']) && $_REQUEST['ajax']!=false)
							die($_REQUEST['_idc_btn']);
					}

					if(isset($_REQUEST['sysid'])){ //request comes from SF
						$tempRow=$this->db->getRow("select * from sys_config where id='{$_REQUEST['sysid']}'");
						if($tempRow!=false && $tjs=json_decode($tempRow['val'],true)){
							$tjs['filtValO']=$tjs['filtVal'];
							$_SESSION['send_msg_req']=$tjs;
							$this->db->query("delete from sys_config where id='{$_REQUEST['sysid']}'");
							unset($_REQUEST['sysid']);
							if($tjs['filtType']=='f'){
								include ('auto_filters.php');
								$_SESSION['send_msg_req']['filtValO']=$sys_sf_filters[$tjs['obj']][$tjs['filtVal']]['q2'];

							}
						}
					}
					if(!isset($_REQUEST['bid']) || $_REQUEST['bid']==false){
						$bid=$this->getConfig("jf_cnt");
						$this->setConfig("jf_cnt",$bid+1);
						//					var_dump($_SESSION['send_msg_req']);
						$mc=$_SESSION['send_msg_req'];
						$bmc=$mc;
						$oids=array();
						if($mc['filtType']=="i"){
							$toa=explode(",",$mc['filtVal']);
							$allr=count($toa);
							$tca=array_chunk($toa,$pb);
							foreach($tca as $ba){
								$this->db->query("insert into msgs_batches set jf_id='$bid', msg_data='".$this->db->escape(json_encode($mc),true)."',
										obj_ids='".implode(",",$ba)."',max_msgs={$pb},msgs_tot=$allr");
							}
						}
						else if($mc['filtType']=="f"){
							$ret=SF_query($q="/services/data/v32.0/sobjects/{$mc['obj']}/listviews/{$mc['filtVal']}/describe",false,false,true);
							$ret=json_decode($ret,true);
							preg_match("#where (.*?) (order by|$)#i",$ret['query'],$mres);
							$qw=$mres[1];

							$wh="";
							$ba=array();
							$allr=0;

/*							$Cret=SF_query($sfq="/bulk_rest_api?count=1&q=".urlencode("select count() from {$mc['obj']} ".($qw!=false?"where $qw":"")),false,false);
							$Cdata=json_decode($Cret,true);
							if(isset($Cdata[0]['errorCode']) && strpos($Cdata[0]['message'],"Too many query rows: 50001")!==false)
								$allr=50001;
							if(isset($Cdata[0]['errorCode']))
								var_dump("Error: SF query count error",$Cdata[0]['message']);
							if(isset($Cdata[0]['Val__c']))
								$allr=$Cdata[0]['Val__c'];
*/
							header("Location: ".aurl("/junk_filter?bid={$bid}"));
						  ini_set("max_execution_time",0);
						  ini_set("ignore_user_abort",true);

							$allr=0;
							$ret=array();
							while(true){
								$tw=array();
								if(count($ret)>0){
									$ret=array_chunk($ret,$pb);
									foreach($ret as $retB){
										$ba=array();
										foreach($retB as $row){
											$ba[$row['Id']]=$row['Id'];
											$wh=" Id>'{$row['Id']}'";
										}

										if(count($ba)>0){
											$this->db->query("insert into msgs_batches set jf_id='$bid', msg_data='".$this->db->escape(json_encode($mc),true)."',
													obj_ids='".implode(",",$ba)."',max_msgs={$pb}, msgs_tot=$allr");
										}
									}
								}
								($qw==false?"":$tw[]="({$qw})");
								($wh==false?"":$tw[]="({$wh})");
								$tw=implode(" and ",$tw);
								$tw=($tw==""?"":"where $tw");
								$ret=SF_q($q="select Id from {$mc['obj']} $tw order by Id asc  limit 9000 ");
								if(count($ret)==0 || !isset($ret[0]['Id']))	
									break;
								$allr+=count($ret);

							}
							$this->db->query("update msgs_batches set msgs_tot=$allr where jf_id='$bid'");

						}
//						header("Location: ".aurl("/junk_filter?bid={$bid}"));
						die();
					}
					if(isset($_REQUEST['bid']) && $_REQUEST['bid']!=false){

						$brr=$this->db->getRow("select * from msgs_batches where jf_id={$_REQUEST['bid']} and status=0 order by id asc limit 1");
						$_SESSION['send_msg_req']=array();
						if(is_array($brr)){
							$cm=json_decode($brr['msg_data'],true);
							$cm['filtType']="i";
							$cm['filtVal']=$cm['filtValO']=$brr['obj_ids'];
							$_SESSION['send_msg_req']=$cm;
						}
					}

					//var_dump($_SESSION['send_msg_req']);
					if(isset($_SESSION['send_msg_req'])){
						$this->LookAtVar("msg_junk_filter",true,true,true);
					}else
						die('Message data not set');

				}else
					return false;

				return true;
			}	

		}

?>
