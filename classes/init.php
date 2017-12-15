<?php
error_reporting(E_ALL & ~E_NOTICE ^ E_DEPRECATED);
ini_set("display_errors",1);
ini_set("output_buffering",0);
ini_set("default_charset","utf-8");
	if(ini_get("magic_quotes_gpc")==false)
	ini_set("magic_quotes_gpc",1);

require("db_creds.php"); // DONT add this file to SVN

$wtit=explode("?",$_SERVER['REQUEST_URI'],2);
$wtit=$wtit[0];
$webtitle=array(
	"en"=>"GTP{$wtit}"
);

global $sys_history_tracking;
$sys_history_tracking=true;

global $sys_history_def_show;
$sys_history_def_show=true;

global $sys_access_tracking;
$sys_access_tracking=true;

global $sys_app_name;
$sys_app_name = "99th";

global $sys_brand_name;
$sys_brand_name = "sla";
// will depend on domain name , can be topt/ib/sla/wsc/fu , based on it user access can should be granted or not

global $_sys_def_ref_ctrl;
$_sys_def_ref_ctrl="sf_ref";

if (preg_match('#^ib\.#i', $_SERVER['HTTP_HOST'])) 
	$sys_brand_name = "ib"; 
if (preg_match('#^sla\.#i', $_SERVER['HTTP_HOST'])) 
	$sys_brand_name = "sla"; 
if (preg_match('#^sales\.#i', $_SERVER['HTTP_HOST'])) 
	$sys_brand_name = "sla"; 
if (preg_match('#^topt\.#i', $_SERVER['HTTP_HOST'])) 
	$sys_brand_name = "topt"; 
if (preg_match('#^wsc\.#i', $_SERVER['HTTP_HOST'])) 
	$sys_brand_name = "wsc"; 
if (preg_match('#^fu\.#i', $_SERVER['HTTP_HOST'])) 
	$sys_brand_name = "fu"; 
if (preg_match('#^fs\.#i', $_SERVER['HTTP_HOST'])) 
	$sys_brand_name = "fs";
if (preg_match('#^mngr\.#i', $_SERVER['HTTP_HOST']))
    $sys_brand_name = "mngr";
//$sys_brand_name = "sla";


global $dbo_def_opts;
$dbo_def_opts = array('listTpl' => 'custom_view_list', 'editTpl' => 'custom_view_edit');
//$dbo_def_opts=array('noViews'=>true,"popupEdit"=>true);

global $sys_adb_debug_msg;
$sys_adb_debug_msg = false;

$def_lang="en";
$glangs=array("en");

global $sys_admin_tpls;
$sys_admin_tpls = "app_tpls";

global $sys_admin_skin;
$sys_admin_skin = "SF_skin";

global $sys_sf_objs;
//$sys_sf_objs=array();
$sys_sf_objs = array(
	//SLA
	'SEOX3_Client__c',
	'SEOX3_Team_Member__c',
	'Sales_Commission__c',
	'Online_Payment__c',
	'Online_Order__c',
//	'Person__c',
	//FU
	'FU_User__c',
	'FU_App__c',
	'FU_Analysis__c',
	'FU_Credit_Report__c',
	'FU_Inquiries__c',
	'FU_Tradeline__c',
	'FU_Creditor__c',
	'FU_Payment__c',
	'FU_User_Sessions__c',
	//TOPT
	'TT_Chargebacks__c', 
	'TT_Client_Order__c', 
	'TT_Credit_Report__c', 
	'TT_Creditor__c', 
	'TT_Express_Posting__c', 
	'TT_Package__c', 
	'TT_Supplier_Payment__c', 
	'TT_Penalty__c',
	'TT_Supplier_Order__c',
	'TT_Supplier_Referral_Fees__c',
	'TT_Tradeline__c',
	'TT_Tradeline_Group__c',
	'TT_Tradeline_Statements__c',
	'TT_User__c',
	'TT_User_Sessions__c',
	"TT_Tradeline_Pack__c",
	//IB
	'IB_User__c',
	'IB_Order__c',
	'IB_Dispute__c',	
	'IB_Creditor__c',
	'IB_Payment__c',
	//WSC
	'SC_User__c',
	'SC_Corp__c',
	'SC_Client_Order__c',
	'SC_Corp_Calls__c',
	//Notes
	'Note',
	//Msg system
	'Message_Queue__c',
	'Message_Campaign__c',
	'DNC__c',
	'RecordType',
	'emailTemplate',

	'FCEO_Corp__c'

);

global $sys_sf_obj_settings;
$sys_sf_obj_settings=array('Note'=>array('addSysFlds'=>array('ParentId','Title','Body')));


global $def_editor;
$def_editor="tinymce";
global $sys_links_url;
$sys_links_url="index.php";

global $sys_use_slugs;
$sys_use_slugs=true;

$sys_user_auth=array('obj'=>"mngr",'tbl'=>'mngr','login'=>'username','pass'=>'password','perms'=>false,'deflogin'=>true);

global $sys_login_trace_map;
$sys_login_trace_map=false;
/*$sys_login_trace_map=array('obj'=>"TT_User_Sessions__c",'tbl'=>'TT_User_Sessions__c',
	"map"=>array(
		"Date_Time__c"=>"_timestamp",
		"IP__c"=>"_g_SERVER[REMOTE_ADDR]",
		"User_Agent__c"=>"_g_SERVER[HTTP_USER_AGENT]",
		"Timezone__c"=>"_g_REQUEST[TZ]",
		"FU_User__c"=>"id",
		"Username__c"=>"Username__c",
	),
);
 */
global $sys_adb_debug;
$sys_adb_debug = false;

global $sf_api_sf2app_enable, $sf_api_app2sf_enable;
$sf_api_sf2app_enable=false;
$sf_api_app2sf_enable=false;


global $__sys_sf_api_calls_log;
$__sys_sf_api_calls_log=false;


global $sys_skip_url_tracking,$adm_url_pref,$url_pref;
$sys_skip_url_tracking = array("$adm_url_pref/","$url_pref/",$adm_url_pref,$url_pref,'/',"{$url_pref}favicon.ico","{$adm_url_pref}favicon.ico","/favicon.ico");
global $sys_skip_slug_tracking;
$sys_skip_slug_tracking = array('ajax_refresh');

global $dbo_sel_default;
$dbo_sel_default="-- Select --";

global $sys_public_methods;
$sys_public_methods=array("p_async","p_api","p_msg_api","p_junk_filter","p_dialapi","p_oneclicklogin","p_fastlink");


global $sys_def_date_format,$sys_def_time_format,$sys_def_datetime_format;
$sys_def_date_format="m/d/Y";
$sys_def_time_format="h:i A";
/*
$sys_def_date_format = "d/m/Y";
$sys_def_time_format = "H:i";
 */

$sys_def_datetime_format="$sys_def_date_format $sys_def_time_format";

global $sys_def_date_format_js,$sys_def_time_format_js;
$sys_def_date_format_js = "mm/dd/yy";
$sys_def_time_format_js = "hh:mm TT";

/*
$sys_def_date_format_js = "dd/mm/yy";
$sys_def_time_format_js = "HH:mm";
*/

global $sys_wbr_len,$sys_wbr_tag;
$sys_wbr_len=15;
$sys_wbr_tag="<wbr/>";

global $sys_user_form_names;
$sys_user_form_names=false;


?>
