<?php
require_once("common_funcs.php");
require("tables_sf.php");

$dbobjs = array(
    'FU_Payment__c' => array(
        'fctrls' => array(
            'User_Type__c' => array('c' => 'select', 'opts' => array('Client' => 'Client', 'Broker' => 'Broker'))
        )
    ),
    'TT_Supplier_Payment__c' => array(
        'fctrls' => array(
            'User_Type__c' => array('c' => 'select', 'opts' => array('Broker - Affiliate' => 'Broker - Affiliate', 'Broker - Credits' => 'Broker - Credits', 'Client' => 'Client', 'Supplier' => 'Supplier')),
//            'Supplier__c' => array('c' => 'sf_ref'),
            // 'Supplier__c' => array('c' => 'sf_ref_list'),
//            'Amount__c' => array('c' => 'sf_spinner'),
//            'Method__c' => array('c' => 'sf_select'),
        )
			),
			"SEOX3_Client__c"=>array(
				"obj_slug"=>"Clients",
				'fctrls' => array(
					"Next_Call_Back__c"=>array('c'=>'datetime','modif_func'=>'next_cb_mod'),
					"Appointment_Link__c"=>array('c'=>'url'),
					"Lead_Grade__c"=>array('modif_func'=>'leadgrade_mod'),
  //      'Sales_Rep__c' => array('c' => 'sf_ref'),
//        'Merged_Old_Client__c' => array('c' => 'sf_ref'),
			),
		),

		"Note"=>array(
			'fctrls' => array(
				"ParentId"=>array('sfdata'=>array('sfid'=>true),'sf_api_force_fld'=>true),
			),
		),
		"FU_User__c"=>array(
				"fctrls"=>array(
/*						"Personal_Funding_Package__c"=>array("c"=>"url"),
						"Corporate_Funding_Package__c"=>array("c"=>"url"),
						"Photo_ID__c"=>array("c"=>"url"),
						"Social_Security_Card__c"=>array("c"=>"url"),
						"Utility_Bill__c"=>array("c"=>"url"),
						"Proof_of_Income__c"=>array("c"=>"url"),
						"Personal_Tax_Returns__c"=>array("c"=>"url"),
						"Personal_Bank_Statements__c"=>array("c"=>"url"),
						"Corporate_Articles__c"=>array("c"=>"url"),
						"SOS_Print_out__c"=>array("c"=>"url"),
						"COGS__c"=>array("c"=>"url"),
						"EIN_Letter__c"=>array("c"=>"url"),
						"Business_License__c"=>array("c"=>"url"),
						"Corporate_Balance_Sheet__c"=>array("c"=>"url"),
						"FE_Articles__c"=>array("c"=>"url"),
						"FE_COGS__c"=>array("c"=>"url"),
						"FE_SOS_Print_out__c"=>array("c"=>"url"),
						"Corporate_Bank_Statements__c"=>array("c"=>"url"),
						"Corporate_Tax_Returns__c"=>array("c"=>"url"),
						"Corporate_Income_Statement__c"=>array("c"=>"url"),*/
						"Portal_One_Click_Login_URL__c"=>array("c"=>"url"),
					),
		),
		"FU_App__c"=>array(
				"fctrls"=>array(
					"Creditor_Website__c"=>array("c"=>"url"),
					"Photo_ID__c"=>array("c"=>"url"),
				),
		),
		"FU_Analysis__c"=>array(
				"fctrls"=>array(
					"Success_Fee__c"=>array('emptyVals'=>array('%')),
					"FU_App_Summary__c"=>array("c"=>"htmltextarea"),
					"Personal_Funding_Package__c"=>array("c"=>"url"),
					"Corporate_Funding_Package__c"=>array("c"=>"url"),
					"Client_Photo_ID__c"=>array("c"=>"url"),
					"Social_Security_Card__c"=>array("c"=>"url"),
					"Utility_Bill__c"=>array("c"=>"url"),
					"Proof_of_Income__c"=>array("c"=>"url"),
					"Personal_Bank_Statements__c"=>array("c"=>"url"),
					"Corporate_Articles__c"=>array("c"=>"url"),
					"SOS_Print_out__c"=>array("c"=>"url"),
					"COGS__c"=>array("c"=>"url"),
					"EIN_Letter__c"=>array("c"=>"url"),
					"Business_License__c"=>array("c"=>"url"),
					"FE_Articles__c"=>array("c"=>"url"),
					"FE_SOS_Print_out__c"=>array("c"=>"url"),
					"FE_COGS__c"=>array("c"=>"url"),
					"Corporate_Bank_Statements__c"=>array("c"=>"url"),
					"Corporate_Tax_Returns__c"=>array("c"=>"url"),
				),
		),
		"sys_change_history"=>array(
			"table"=>"sys_change_history",
      "def_lang" => $def_lang,
      "langs" => $glangs,
			"obj_slug"=>"change_history",
			"fctrls"=>array(
				"obj"=>array('c'=>'string','t'=>'Object'),
				"obj_id"=>array('c'=>'string','t'=>'Object ID'),
				"obj_slug"=>array('c'=>'string','t'=>'Object Slug'),
				"obj_sf_id"=>array('c'=>'string','t'=>'Object SF Id'),
				"act"=>array('c'=>'string','t'=>'Action'),
				"user_id"=>array('c'=>'string','t'=>'User ID'),
				"username"=>array('c'=>'string','t'=>'Username'),
				"time"=>array('c'=>'datetime','t'=>'Timestamp'),
				"upd_fields"=>array('c'=>'string','t'=>'Changed','modif_func'=>"__sys_history_fld_list"),
				"old"=>array('c'=>'string','t'=>'Old'),
				"new"=>array('c'=>'string','t'=>'New'),
			),
			"opts"=>array(
				"cV"=>array("user_filter"=>"rel_list"),
			)
		),
    "apilog" => array(
        "table" => "sys_sf_rest_api_dirty_log",
        "fctrls" => array(
            "obj" => array("t" => "Obj Name"),
            "obj_id" => array("t" => "Obj ID"),
            "act" => array("t" => "Action"),
            "dirty_from" => array("t" => "Dirty From"),
            "sf_error" => array("t" => "SF Error"),
            "retries" => array("t" => "Retries #")
        ),
        "def_lang" => $def_lang,
        "langs" => $glangs,
    ),

		"Message_Queue__c"=>array(
				"fctrls"=>array(
					"major_type"=>array("auto_db_type"=>"VARCHAR(25)","auto_db_def"=>"NOT NULL DEFAULT ''"),
					"is_sf_synced"=>array("auto_db_type"=>"TINYINT(1)","auto_db_def"=>"NOT NULL DEFAULT '0'"),
					"last_upd"=>array("auto_db_type"=>"TIMESTAMP","auto_db_def"=>"NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"),
					),
				),

		"chat" => array(
        "table" => "chat",
				"auto_db"=>true,
        "fctrls" => array(
            "f" => array("t" => "From","c"=>"text","auto_db_type"=>"varchar(100)","auto_db_def"=>"NOT NULL DEFAULT ''","auto_db_key"=>true),
            "t" => array("t" => "To","c"=>"text","auto_db_type"=>"varchar(100)","auto_db_def"=>"NOT NULL DEFAULT ''","auto_db_key"=>true),
            "msg" => array("t" => "Message","c"=>"textarea","auto_db_type"=>"text","auto_db_def"=>"NOT NULL DEFAULT ''"),
            "mtype" => array("t" => "Message Type","c"=>"text","auto_db_type"=>"varchar(50)","auto_db_def"=>"NOT NULL DEFAULT ''","auto_db_key"=>true),
            "mqid" => array("t" => "MsgQ ID","c"=>"string","auto_db_type"=>"bigint(11)","auto_db_def"=>"NOT NULL DEFAULT '0'","auto_db_key"=>true),
            "from_sf_id" => array("t" => "From SF ID","c"=>"string","auto_db_type"=>"varchar(30)","auto_db_def"=>"NOT NULL DEFAULT ''","auto_db_key"=>true),
            "to_sf_id" => array("t" => "To SF ID","c"=>"string","auto_db_type"=>"varchar(30)","auto_db_def"=>"NOT NULL DEFAULT ''","auto_db_key"=>true),
            "ts" => array("t" => "Timestamp","c"=>"datetime","auto_db_type"=>"TIMESTAMP","auto_db_def"=>"NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"),
        ),
        "def_lang" => $def_lang,
        "langs" => $glangs,
    ),
    "mngr" => array(
        "table" => "mngr",
        "auto_db"=>true,
        "fctrls" => array(

            "name" => array('rodata'=>array('mngr'),"t" => "Name","c"=>"text","auto_db_type"=>"varchar(100)","auto_db_def"=>"NOT NULL DEFAULT ''"),
            "status" => array('unsetOpts'=>array('Terminated'=>'Terminated'),'opts'=>array('New'=>'New','Active'=>'Active','Terminated'=>'Terminated'),"t" => "Status","c"=>"select","auto_db_type"=>"enum('New','Active','Terminated')","auto_db_def"=>"NOT NULL DEFAULT ''"),
            "email" => array('rodata'=>array('mngr'), "t" => "Email","c"=>"text","auto_db_type"=>"varchar(100)","auto_db_def"=>"NOT NULL DEFAULT ''","auto_db_key"=>true),
            "username" => array("t" => "Username","c"=>"text","auto_db_type"=>"varchar(50)","auto_db_def"=>"NOT NULL DEFAULT ''","auto_db_key"=>true),
            "password" => array("t" => "Password","c"=>"password","auto_db_type"=>"varchar(250)","auto_db_def"=>"NOT NULL DEFAULT ''"),
		),
        "def_lang" => $def_lang,
        "langs" => $glangs,
    ),
    "cls" => array(
        "table" => "cls",
        "auto_db"=>true,
        "fctrls" => array(
            "mngrid" => array("t" => "Manager","c"=>"select","auto_db_type"=>"bigint(11)","auto_db_def"=>"NOT NULL","auto_db_key"=>true),
            "name" => array("t" => "Name","c"=>"text","auto_db_type"=>"varchar(100)","auto_db_def"=>"NOT NULL DEFAULT ''"),
            "address" => array("t" => "address","c"=>"text","auto_db_type"=>"varchar(100)","auto_db_def"=>"NOT NULL DEFAULT ''"),
            "status" => array('opts'=>array('New'=>'New','Interested'=>'Interested','Lost'=>'Lost'),"t" => "Status","c"=>"select","auto_db_type"=>"enum('New','Interested','Lost')","auto_db_def"=>"NOT NULL DEFAULT 'New'"),
            "email" => array("t" => "Email","c"=>"text","auto_db_type"=>"varchar(100)","auto_db_def"=>"NOT NULL DEFAULT ''","auto_db_key"=>true),
            "phone" => array("t" => "Phone","c"=>"text","auto_db_type"=>"varchar(50)","auto_db_def"=>"NOT NULL DEFAULT ''"),
            "notes" => array("cols"=>100,"rows"=>5,"t" => "Notes","c"=>"textarea","auto_db_type"=>"text","auto_db_def"=>"NOT NULL  DEFAULT '' "),
        ),
        "def_lang" => $def_lang,
        "langs" => $glangs,
        "rels"=>array(
            "mngrid"=>array("obj"=>"mngr","fld"=>"Name","on"=>"id"),

        ),
    ),
		"fastaccess" => array(
        "table" => "fastaccess",
				"auto_db"=>true,
        "fctrls" => array(
            "hash" => array("t" => "Hash","c"=>"text","auto_db_type"=>"varchar(100)","auto_db_def"=>"NOT NULL DEFAULT ''","auto_db_key"=>true),
            "url" => array("t" => "Url","c"=>"text","auto_db_type"=>"varchar(255)","auto_db_def"=>"NOT NULL DEFAULT ''","auto_db_key"=>true),
            "params" => array("t" => "Params","c"=>"textarea","auto_db_type"=>"text","auto_db_def"=>"NOT NULL DEFAULT ''"),

						"userid" => array("t" => "User ID","c"=>"text","auto_db_type"=>"varchar(50)","auto_db_def"=>"NOT NULL DEFAULT ''","auto_db_key"=>true),
						"use_cnt" => array("t" => "Usage Count","c"=>"text","auto_db_type"=>"INT(11)","auto_db_def"=>"NOT NULL DEFAULT '0'"),
            "autologin" => array("t" => "Autologin","c"=>"checkbox","auto_db_type"=>"tinyint(1)","auto_db_def"=>"NOT NULL DEFAULT '0'","auto_db_key"=>true),
            "ts" => array("t" => "Timestamp","c"=>"datetime","auto_db_type"=>"TIMESTAMP","auto_db_def"=>"NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"),
        ),
        "def_lang" => $def_lang,
        "langs" => $glangs,
    ),

		"rc_calls" => array(
        "table" => "rc_calls",
				"auto_db"=>true,
        "fctrls" => array(
            "call_time" => array("t" => "Call Time","c"=>"datetime","auto_db_type"=>"DATETIME","auto_db_def"=>"NULL DEFAULT NULL"),
            "lead_sf_id" => array("t" => "Lead SF Id","c"=>"text","auto_db_type"=>"varchar(100)","auto_db_def"=>"NOT NULL DEFAULT ''","auto_db_key"=>true),
            "sr_sf_id" => array("t" => "SR SF Id","c"=>"text","auto_db_type"=>"varchar(100)","auto_db_def"=>"NOT NULL DEFAULT ''","auto_db_key"=>true),
            "sr_phone" => array("t" => "SR Phone","c"=>"text","auto_db_type"=>"varchar(100)","auto_db_def"=>"NOT NULL DEFAULT ''","auto_db_key"=>true),
            "lead_phone" => array("t" => "Lead Phone","c"=>"text","auto_db_type"=>"varchar(100)","auto_db_def"=>"NOT NULL DEFAULT ''","auto_db_key"=>true),
            "is_inbound" => array("t" => "Is Inbound","c"=>"string","auto_db_type"=>"tinyint(1)","auto_db_def"=>"NOT NULL DEFAULT '0'","opts"=>array(0=>'No',1=>'Yes')),
            "duration" => array("t" => "Duration","c"=>"text","auto_db_type"=>"bigint(20)","auto_db_def"=>"NOT NULL DEFAULT '0'"),
            "details" => array("t" => "Details","c"=>"text","auto_db_type"=>"text","auto_db_def"=>"NOT NULL DEFAULT ''"),
            "ts" => array("t" => "Timestamp","c"=>"datetime","auto_db_type"=>"TIMESTAMP","auto_db_def"=>"NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"),
        ),
        "def_lang" => $def_lang,
        "langs" => $glangs,
       "rels"=>array(
          "sr_sf_id"=>array("obj"=>"SEOX3_Team_Member__c","fld"=>"Name","on"=>"SF_Id"),
          "lead_sf_id"=>array("obj"=>"SEOX3_Client__c","fld"=>"Name","on"=>"SF_Id"),
        ),
    ),
		"stop_list" => array(
        "table" => "stop_list",
				"auto_db"=>true,
        "fctrls" => array(
            "srch" => array("t" => "Pattern","c"=>"text","auto_db_type"=>"varchar(256)","auto_db_def"=>"NOT NULL DEFAULT ''",'auto_db_key_uniq'=>true),
            "enabled" => array("t" => "Is Enabled","c"=>"string","auto_db_type"=>"tinyint(1)","auto_db_def"=>"NOT NULL DEFAULT '1'","opts"=>array(0=>'No',1=>'Yes')),
        ),
        "def_lang" => $def_lang,
        "langs" => $glangs,
    ),
		"msgs_batches" => array(
        "table" => "msgs_batches",
				"auto_db"=>true,
        "fctrls" => array(
            "jf_id" => array("t" => "Task Id","c"=>"string","auto_db_type"=>"bigint(20)","auto_db_def"=>"NOT NULL DEFAULT '0'"),
            "msg_data" => array("t" => "Send Msg Data","c"=>"string","auto_db_type"=>"longtext","auto_db_def"=>"DEFAULT ''"),
            "obj_ids" => array("t" => "Objects Ids","c"=>"string","auto_db_type"=>"text","auto_db_def"=>"DEFAULT ''"),
            "status" => array("t" => "Status","c"=>"string","auto_db_type"=>"tinyint(1)","auto_db_def"=>"NOT NULL DEFAULT '0'","opts"=>array(0=>"Waiting",1=>"Sent")),
            "max_msgs" => array("t" => "Max Messages","c"=>"string","auto_db_type"=>"int(10)","auto_db_def"=>"NOT NULL DEFAULT '0'"),
						"upd"=>array("auto_db_type"=>"TIMESTAMP","auto_db_def"=>"NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"),
            "msgs_tot" => array("t" => "Total messages","c"=>"string","auto_db_type"=>"bigint(20)","auto_db_def"=>"NOT NULL DEFAULT '0'"),
        ),
        "def_lang" => $def_lang,
        "langs" => $glangs,
    ),



);

if (isset($dbobjs_sf) && is_array($dbobjs_sf))
    $dbobjs = array_merge_recursive_new($dbobjs_sf, $dbobjs);


$dbobjs["log_Message_Queue__c"]=$dbobjs["Message_Queue__c"];
$dbobjs["log_Message_Queue__c"]['table']="log_Message_Queue__c";
$dbobjs["log_Message_Queue__c"]["sf_table"]=false;
$dbobjs["log_Message_Queue__c"]["auto_db"]=true;

$dbobjs["log_Message_Campaign__c"]=$dbobjs["Message_Campaign__c"];
$dbobjs["log_Message_Campaign__c"]['table']="log_Message_Campaign__c";
$dbobjs["log_Message_Campaign__c"]["sf_table"]=false;
$dbobjs["log_Message_Campaign__c"]["auto_db"]=true;

$dbobjs["log_Message_Queue__c"]["sf_table"]=false;
$dbobjs["Message_Campaign__c"]['fctrls']['Status__c']['auto_db_key']=true;

?>
