<?php

//$_sfl__d_SEOX3_Client__c_Master['r11111']['_t']="Note";
//$_sfl__d_SEOX3_Client__c_Master['r11111']['list']=array("sys_change_history"=>array("username","time","upd_fields","_queryWhere"=>"ct.obj='SEOX3_Client__c' and ct.obj_id={id}"));

$_sfl__e_FU_User__c_Client['row0']['col0']['Inactive_Reason__c']["showLogic"]=array("Status__c"=>"Inactive");
$_sfl__e_FU_User__c_Client['row0']['col1']['Prospecting_Stage__c']["showLogic"]=array("Status__c"=>"New");
$_sfl__e_FU_User__c_Client['row13']['col0']['Collections_Status__c']["showLogic"]=array("Inactive_Reason__c"=>"Collections");

$_sfl__d_FU_User__c_Client['row0']['col0']['Inactive_Reason__c']["showLogic"]=array("Status__c"=>"Inactive");
$_sfl__d_FU_User__c_Client['row0']['col1']['Prospecting_Stage__c']["showLogic"]=array("Status__c"=>"New");
$_sfl__d_FU_User__c_Client['row13']['col0']['Collections_Status__c']["showLogic"]=array("Inactive_Reason__c"=>"Collections");

$_sfl__d_FU_Creditor__c_Master['row0']['col1']['Inactive_Reason__c']["showLogic"]=array("Status__c"=>"Inactive");
$_sfl__e_FU_Creditor__c_Master['row0']['col1']['Inactive_Reason__c']["showLogic"]=array("Status__c"=>"Inactive");

$objviews = array(
    "rclog" => array(
			"sel_on" => array(
					"localLogin"=>'rclog'
          ),
			"defpage" => array('type' => "obj", "obj" => "rc_calls"),
			"menu" => array(
				"RC Call Log" => array("_obj" => "rc_calls", "_html" => "<i class='icon-briefcase'></i> RC Call Log"),
				),
			"objs"=>array(
				"rc_calls" => array(
					'no_user_filter_allowed' => true,
					"sLabel"=>"RC Call Log",
//					"acts" => array("i","u"),
					"list" => array('call_time','duration','sr_sf_id','sr_phone','lead_sf_id','lead_phone','is_inbound'),
					),
				),
			),


    "adm" => array(
			"sel_on" => array(
					"localAdmin"=>true,"localLogin"=>'admin'
          ),
			"defpage" => array('type' => "obj", "obj" => "mngr"),
			"menu" => array(
				//"Email export" => array("_url" => aurl("/mails_import_export"), "_html" => "<i class='icon-briefcase'></i> Emails Export"),
                "Managers" => array("_obj" => "mngr","_url" => aurl("/mngr"), "_html" => "<i class='icon-briefcase'></i> Managers"),
          //      "Client" => array("_obj" => "cls","_url" => aurl("/cls"), "_html" => "<i class='icon-briefcase'></i> Clients"),
				),
        "objs"=>array(
            "mngr" => array(
                'no_user_filter_allowed' => true,
                "sLabel"=>"Manager",
				"acts" => array("i","u","d","v"),
                "list" => array('_idc_','_acts_','name','status','email','username'),
//                "edit"=>array(
//                    "_l"=>array("r1"=>array("c1"=>array("Name","Email"))),
//                ),
            ),
//            "cls" => array(
//                'no_user_filter_allowed' => true,
//                "sLabel"=>"RC Call Log",
////					"acts" => array("i","u"),
//                "list" => array('call_time','duration','sr_sf_id','sr_phone','lead_sf_id','lead_phone','is_inbound'),
//            ),
        ),
			),

    "SLA" => array(
			"sel_on" => array(
            "_regexp0" => array("Brand_App_Access__c" => "#(^|;)sla(;|$)#i"),
            'Status__c' => 'Active',
            "_cond1" => array($sys_brand_name => "sla"),
          ),
			"defpage" => array('type' => "obj", "obj" => "SEOX3_Client__c"),
			"menu" => array(
				"New pipe" => array("_url" => aurl("/new_pipe"), "_html" => "<i class='icon-briefcase'></i> New Leads"),
				"Retry pipe" => array("_url" => aurl("/retry_pipe"), "_html" => "<i class='icon-briefcase'></i> Retry Leads"),
				"Callbacks pipe" => array("_url" => aurl("/cb_pipe"), "_html" => "<i class='icon-briefcase'></i> Callbacks Scheduled"),
				"Missed Callbacks pipe" => array("_url" => aurl("/miss_cb_pipe"), "_html" => "<i class='icon-briefcase'></i>Missed Callbacks"),
				"Clients" => array("_obj" => "SEOX3_Client__c", "_html" => "<i class='icon-briefcase'></i> Clients"),
				"FU Deals" => array("_obj" => "FU_Analysis__c", "_html" => "<i class='icon-briefcase'></i> FU Deals"),
				//"Sales Comission" => array("_obj" => "Sales_Commission__c", "_html" => "<i class='icon-briefcase'></i> Sales Comission"),
				"Sales Comission" => array("_obj" => "Online_Payment__c", "_html" => "<i class='icon-briefcase'></i> Sales Comission"),
				"Team Members" => array("_obj" => "SEOX3_Team_Member__c", "_html" => "<i class='icon-briefcase'></i> Team Members"),
				),
			"objs" => array(
				"Online_Payment__c" => array(
					//'no_user_filter_allowed' => true,
					'user_filter'=>array("ct.Sales_Rep__c"=>"id"),
					"sLabel"=>"Sales Commission",
//					"acts" => array("i","u"),
					"list" => array('Brand__c','Product_Description__c','Status__c','Status_Date__c','SR_Commision__c','Amount__c','Name','Phone__c','Email__c','Payment_Method__c'),
					"obj_opts"=>array("preListTpl"=>"sr_pre_comis"),
					"view" => array(
                        "_flat" => true,
						"_l" => $_sfl__d_Online_Payment__c_Master,
						),
					"edit" => array(
                        "_flat" => true,
						"_l" => $_sfl__e_Online_Payment__c_Master,
						),
					),
				"Sales_Commission__c" => array(
					//'no_user_filter_allowed' => true,
					'user_filter'=>array("ct.Team_Member__c"=>"id"),
					"sLabel"=>"Sales Commission",
//					"acts" => array("i","u"),
					"list" => array('Commission_ID__c','Commission_Amount__c' => array(),'Date__c' => array(),'Commission_Amount__c','Name'),
					"view" => array(
                        "_flat" => true,
						"_l" => $_sfl__d_Sales_Commission__c_Master,
						),
					"edit" => array(
                        "_flat" => true,
						"_l" => $_sfl__e_Sales_Commission__c_Master,
						),
					),
/*					"sys_change_history"=>array(
						'no_user_filter_allowed' => true,
					),*/
					"Note" => array(
//						"user_filter"=>"rel_list",
					'no_user_filter_allowed' => true,
					"acts"=>array("i","u"),
					"edit"=>array(
							"_l"=>array("r1"=>array("c1"=>array("Title","Body"))),
						),
				),
				"SEOX3_Client__c" => array(
					"sLabel"=>"Client",
					"user_filter" => array("ct.Sales_Rep__c" => "id"),
//					'no_user_filter_allowed' => true,
					"acts" => array("i","u"),
					"list" => array('_idc_','_acts_','Client_ID__c','CreatedDate','Name','Phone1__c','E_Mail__c','Prospective_Stage__c','Primary_Interest__c','Marketing_Source__c','Last_Note__c','Next_Call_Back__c',
					"_customClass"=>'if(isset($this->cD["Next_Call_Back__c_ts"])){$t=$this->cD["Next_Call_Back__c_ts"];if($t<time()){if(date("Y-m-d")==date("Y-m-d",$t)){return "orange";}else{return "red";}}}else{return "";}',
					"_colFilters"=>array("Prospective_Stage__c"=>array("_onTop"=>true),'Status__c','Primary_Interest__c'),
//					"_noSFFilter"=>true,
					'_sort'=>array('CreatedDate'=>'desc'),
					"_rowColors"=>array("background:#ffaaaa;"=>"jQuery(row).hasClass('red')","background:#ffdd00;"=>"jQuery(row).hasClass('orange')"),
					"_link_buttons"=>array(
//						array('_url'=>aurl("/start_dialer"),"_idc_post"=>2,'_t'=>'Start Dialer','_trg'=>'_blank'),
						'_msgbtn'=>array('_t'=>'Email Blast','_mT'=>'Email','title'=>'Send EMAIL to Client(s)','d'=>'from list','defMsg'=>"\n\n\n\n\n\n\n{SalesRepSign_DO_NOT_DEL}",'fA'=>3,'fAType'=>1,'next'=>'junk_filter')
					),
				),
				"rendLogic"=>array(
						"lead_hide"
					),
				"view" => array(
					"_linkBtns"=>array(
							'_msgbtn1'=>array('_t'=>'Send Email','_mT'=>'Email','title'=>'Send EMAIL to Client','d'=>'from view','defMsg'=>"\n\n\n\n\n\n\n{SalesRepSign_DO_NOT_DEL}",'fA'=>0,'fAType'=>1),
							'_msgbtn2'=>array('_t'=>'Send SMS','_mT'=>'TwillioSMS','title'=>'Send SMS to Client','d'=>'from view','defMsg'=>"\n\n\n\n\n\n\n{SalesRepSign_DO_NOT_DEL}",'fA'=>0,'fAType'=>2,'hSubj'=>1,'defMsg'=>"\n\n\n{Sales_Rep_sign_DONT_DEL_LenOfThisText45chr}",'hSubj'=>1,'cntChr'=>1),
							"Left VM"=>array('_url'=>aurl("/client_btns?cid={id}&sfid={SF_Id}&t=leftvm&tpl=1&s={Client_ID__c}"),'_fancybox'=>'ajax'),
							"No Answer"=>array('_url'=>aurl("/client_btns?cid={id}&sfid={SF_Id}&t=noans&s={Client_ID__c}"),'_fancybox'=>'ajax'),
							"Analysis Sent"=>array('_url'=>aurl("/client_btns?cid={id}&sfid={SF_Id}&t=asent&s={Client_ID__c}"),'_fancybox'=>'ajax'),
							"Callback"=>array('_url'=>aurl("/client_btns?cid={id}&sfid={SF_Id}&t=cb&tpl=1&s={Client_ID__c}"),'_fancybox'=>'ajax'),
					),
                        "_flat" => true,
						"_l" => $_sfl__d_SEOX3_Client__c_SR_App,
						),
					"edit" => array(
                        "_flat" => true,
						"_l" => $_sfl__e_SEOX3_Client__c_SR_App,
						),
					),
					"stop_list"=>array(
						"no_user_filter_allowed"=>true,
						'_sort'=>array('srch'=>'desc'),
						"list"=>array('_idc_',"srch",'enabled',
							"_link_buttons"=>array(
								array('_t'=>'Enable','_url'=>aurl("/junk_filter?bid={$_REQUEST['bid']}"),'_idc_post'=>2,'_onTop' => true),
								array('_t'=>'Disable','_url'=>aurl("/junk_filter?bid={$_REQUEST['bid']}"),'_idc_post'=>2,'_onTop' => true),
								array('_t'=>'Delete','_url'=>aurl("/junk_filter?bid={$_REQUEST['bid']}"),'_idc_post'=>2,'_confirm'=>true,'_onTop' => true),
						)
					),
				),
					
					"FU_Analysis__c"=>array(
							"user_filter"=>array('Client__r.Sales_Rep__c'=>"id"),
							"acts" => array("v"),
							"list" => array('_idc_','Name','Client__r.Name','Client__r.Phone__c','Client__r.Total_Amount_Approved__c', 'Client__r.Total_Final_Success_Fee__c','Status__c', 'Status_Date__c',
								"_colFilters" => array("Status__c" => array('_onTop' => true)),
								"_noSFFilter"=>true,
								"_sort"=>array("Client__r.Total_Amount_Approved__c"=>"desc"),
/*								"_link_buttons"=>array(
									array('_url'=>aurl("/start_dialer"),"_idc_post"=>2,'_t'=>'Start Dialer','_trg'=>'_blank'),
									'_msgbtn'=>array('_onTop'=>false,'_t'=>'Email Blast','_mT'=>'Email','title'=>'Send EMAIL to FU User(s)','d'=>'from list','defMsg'=>"\n\n\n\n\n\n\n{SalesRepSign_DO_NOT_DEL}",'fA'=>3,'fAType'=>1),
									),*/

								),
//							"obj_opts"=>array("_sys_queryWhere"=>array("ct.Total_Amount_Approved__c>0")),
							"edit" => array(
								"_flat" => true,
								"_l" => $_sfl__d_FU_Analysis__c_Master,
								),

							),
				"SEOX3_Team_Member__c" => array(
//						'no_user_filter_allowed' => true,
						"user_filter"=>array('ct.Manager__c'=>"id"),
						"sLabel"=>"Team Member",
						"acts" => array("u"),
						"list" => array('Team_Member_ID__c', 'Name', 'Status__c', "_colFilters" => array("Status__c" => array('_onTop' => true))),
						"view" => array(
                            "_flat" => true,
							"_l" => $_sfl__d_SEOX3_Team_Member__c_Master,
							),
						"edit" => array(
                            "_flat" => true,
							"_l" => $_sfl__e_SEOX3_Team_Member__c_Master,
							),
						),
				),

				),

		"FU" => array(
        "sel_on" => array(
            "_regexp0" => array("Brand_App_Access__c" => "#(^|;)fu(;|$)#i"),
            'Status__c' => 'Active',
            "_cond1" => array($sys_brand_name => "fu"),
        ),
        "defpage" => array('type' => "obj", "obj" => "FU_User__c"),
        "menu" => array(
            "FU Users" => array("_obj" => "FU_User__c", "_html" => "FU Users"),
            "FU Analysis" => array("_obj" => "FU_Analysis__c", "_html" => "FU Analysis"),
            "FU Apps" => array("_obj" => "FU_App__c", "_html" => "FU Apps"),
            "FU Payments" => array("_obj" => "FU_Payment__c", "_html" => "FU Payments"),
            "FU Credit Reports" => array("_obj" => "FU_Credit_Report__c", "_html" => "FU Credit Reports"),
            "FU Inquiries" => array("_obj" => "FU_Inquiries__c", "_html" => "FU Inquiries"),
            "FU Tradelines" => array("_obj" => "FU_Tradeline__c", "_html" => "FU Tradelines"),
            "FU Creditors" => array("_obj" => "FU_Creditor__c", "_html" => "FU Creditors"),
            // FU CHargebacks in SF no FU sessions
            "FU Sessions" => array("_obj" => "FU_User_Sessions__c", "_html" => "FU Sessions"),
        ),
				"objs" => array(
					"Note" => array(
						//            "user_filter"=>"rel_list",
						'no_user_filter_allowed' => true,
						"acts"=>array("i","u"),
						"edit"=>array(
							"_l"=>array("r1"=>array("c1"=>array("Title","Body"))),
							),
						),
					"FU_User__c" => array(
						'no_user_filter_allowed' => true,
					"sLabel"=>"FU User",
						"acts" => array("i","u"),
						"list" => array('Name' => array(), 'User_Number__c' => array(), 'User_Type__c' => array(), 'Status__c' => array(), "_colFilters" => array("User_Type__c" => array('_onTop' => true))),
						"viewSel" => array(
							'admin' => array('User_Type__c' => 'Admin'),
							'client' => array('User_Type__c' => 'Client'),
							'broker' => array('User_Type__c' => 'Broker'),
							),
						"editSel" => array(
							'admin' => array('User_Type__c' => 'Admin'),
							'client' => array('User_Type__c' => 'Client'),
							'broker' => array('User_Type__c' => 'Broker'),
							),
						"view" => array(
							"_flat" => true,
							"_l" => $_sfl__d_FU_User__c_Master,
							'admin' => $_sfl__d_FU_User__c_Admin,
							'client' => $_sfl__d_FU_User__c_Client,
							'broker' => $_sfl__d_FU_User__c_Broker,
							),
						"edit" => array(
								"_flat" => true,
								"_l" => $_sfl__e_FU_User__c_Master,
								'admin' => $_sfl__e_FU_User__c_Admin,
								'client' => $_sfl__e_FU_User__c_Client,
								'broker' => $_sfl__e_FU_User__c_Broker,
								),
						),
						"FU_App__c" => array(
								'no_user_filter_allowed' => true,
					"sLabel"=>"FU App",
								"acts" => array("i","u"),
								"list" => array("_acts_",'Name' => array(), 'Client__c' => array(), 'Analysis__c' => array(), 'Creditor__c' => array(), 'Creditor_Type__c' => array(), 'Status__c' => array(),'Status_Date__c' => array(),'Amount_Approved__c'=>array()),
								"view" => array(
									"_flat" => true,
									"_l" => $_sfl__d_FU_App__c_Master,
									),
								"edit" => array(
									"_flat" => true,
									"_l" => $_sfl__e_FU_App__c_Master,
									),
								),
						"FU_Analysis__c" => array(
								'no_user_filter_allowed' => true,
					"sLabel"=>"FU Analysis",
								"acts" => array("i","u"),
								"list" => array( 'Name' => array(),'Client__c' => array(), 'Broker__c' => array(), 'Funding_Program__c' => array(),'Status__c' => array(),'Status_Date__c' => array(), 'Success_Fee__c' => array(),'Total_Approved_Final__c'=>array(),'Total_Funded__c'=>array(),'Total_Apps__c'=>array(),),
								"view" => array(
									"_flat" => true,
									"_l" => $_sfl__d_FU_Analysis__c_Master,
									),
								"edit" => array(
									"_flat" => true,
									"_l" => $_sfl__e_FU_Analysis__c_Master,
									),
								),
						"FU_Credit_Report__c" => array(
								'no_user_filter_allowed' => true,
					"sLabel"=>"FU Credit Report",
								"acts" => array("i","u"),
								"list" => array('Name' => array(), 'Client__c' => array(), 'Source_Type__c' => array(), 'Internal_Source__c' => array(), 'Last_Late_Payment__c' => array()),
								"view" => array(
									"_flat" => true,
									"_l" => $_sfl__d_FU_Credit_Report__c_Master,
									),
								"edit" => array(
									"_flat" => true,
									"_l" => $_sfl__e_FU_Credit_Report__c_Master,
									),
								),
						"FU_Inquiries__c" => array(
								'no_user_filter_allowed' => true,
					"sLabel"=>"FU Inquiries",
								"acts" => array("i","u"),
								"list" => array('Name' => array(), 'Credit_Report__c' => array(), 'Inquiry_Number__c' => array()),
								"view" => array(
									"_flat" => true,
									"_l" => $_sfl__d_FU_Inquiries__c_Master,
									),
								"edit" => array(
									"_flat" => true,
									"_l" => $_sfl__e_FU_Inquiries__c_Master,
									),
								),
						"FU_Tradeline__c" => array(
								'no_user_filter_allowed' => true,
					"sLabel"=>"FU Tradeline",
								"acts" => array("i","u"),
								"list" => array('Credit_Report__c' => array(), 'Name' => array(), 'Tradeline_Number__c' => array(), 'Type__c' => array(), 'Bureaus__c' => array(), 'Authorized_User__c' => array()),
								"view" => array(
									"_flat" => true,
									"_l" => $_sfl__d_FU_Tradeline__c_Master,
									),
								"edit" => array(
									"_flat" => true,
									"_l" => $_sfl__e_FU_Tradeline__c_Master,
									),
								),
						"FU_Creditor__c" => array(
								'no_user_filter_allowed' => true,
					"sLabel"=>"FU Creditor",
								"acts" => array("i","u"),
								"list" => array('Creditor_Number__c' => array(), 'Name' => array(), 'Type__c' => array(), 'Footprint__c' => array(), 'Status__c'=>array(),'Status_Date__c'=>array(),'Pre_App_Requirement__c'=>array(),'Application_Method__c'=>array(),'Credit_Score_Requirement__c'=>array()),
								"view" => array(
									"_flat" => true,
									"_l" => $_sfl__d_FU_Creditor__c_Master,
									),
								"edit" => array(
									"_flat" => true,
									"_l" => $_sfl__e_FU_Creditor__c_Master,
									),
								),
						"FU_Payment__c" => array(
								'no_user_filter_allowed' => true,
					"sLabel"=>"FU Payment",
								"acts" => array("i","u"),
								"list" => array('_acts_', 'Name' => array(), 'Date__c' => array(), 'Amount__c' => array(), 'User__c', 'User_Type__c' => array(), "_colFilters" => array("User_Type__c" => array('_onTop' => true))),
								"viewSel" => array(
									'client' => array('User_Type__c' => 'Client'),
									'broker' => array('User_Type__c' => 'Broker'),
									),
								"editSel" => array(
									'client' => array('User_Type__c' => 'Client'),
									'broker' => array('User_Type__c' => 'Broker'),
									),
								"view" => array(
									"_flat" => true,
									"_l" => $_sfl__d_FU_Payment__c_Master,
									'client' => $_sfl__d_FU_Payment__c_Client_Payment,
									'broker' => $_sfl__d_FU_Payment__c_Broker_Payment,
									),
								"edit" => array(
									"_flat" => true,
									"_l" => $_sfl__e_FU_Payment__c_Master,
									'client' => $_sfl__e_FU_Payment__c_Client_Payment,
									'broker' => $_sfl__e_FU_Payment__c_Broker_Payment,
									),
								),
								"FU_User_Sessions__c" => array(
										'no_user_filter_allowed' => true,
					"sLabel"=>"FU User Session",
										"acts" => array(),
										"list" => array('Name' => array(), 'Date_Time__c' => array(), 'IP__c' => array(), 'Location__c' => array(), 'Timezone__c' => array(), 'FU_User__c' => array()),
										"view" => array(
											"_flat" => true,
											"_l" => $_sfl__d_FU_User_Sessions__c_Master,
											),
										"edit" => array(
											"_flat" => true,
											"_l" => $_sfl__e_FU_User_Sessions__c_Master,
											),
										),

								),

    ),

		"TOPT" => array(
        "sel_on" => array(
            "_regexp0" => array("Brand_App_Access__c" => "#(^|;)topt(;|$)#i"),
            'Status__c' => 'Active',
            "_cond1" => array($sys_brand_name => "topt"),
        ),
        "defpage" => array('type' => "obj", "obj" => "TT_User__c"),
        "menu" => array(
            "Users" => array("_obj" => "TT_User__c", "_html" => "TT Users"),
            "Client Orders" => array("_obj" => "TT_Client_Order__c", "_html" => "TT Client Orders"),
            "Supplier Orders" => array("_obj" => "TT_Supplier_Order__c", "_html" => "TT Supplier Orders"),
            "Credit Reports" => array("_obj" => "TT_Credit_Report__c", "_html" => "TT Credit Reports"),
            "Supplier Payments" => array("_obj" => "TT_Supplier_Payment__c", "_html" => "TT Payments"),
            "Express Posting" => array("_obj" => "TT_Express_Posting__c", "_html" => "TT Express Posting"),
            "Tradelines" => array("_obj" => "TT_Tradeline__c", "_html" => "TT Tradelines"),
            "Packages" => array("_obj" => "TT_Package__c", "_html" => "TT Packages"),
            "Chargebacks" => array("_obj" => "TT_Chargebacks__c", "_html" => "TT Chargebacks"),
            "Creditors" => array("_obj" => "TT_Creditor__c", "_html" => "TT Creditors"),
            "Tradeline Groups" => array("_obj" => "TT_Tradeline_Group__c", "_html" => "TT Tradeline Groups"),
            "Penalty" => array("_obj" => "TT_Penalty__c", "_html" => "TT Penaltys"),
            "Supplier Referral Fees" => array("_obj" => "TT_Supplier_Referral_Fees__c", "_html" => "TT Supplier Referral Fees"),
            "Tradeline Statements" => array("_obj" => "TT_Tradeline_Statements__c", "_html" => "TT Tradeline Statements"),
            "User Sessions" => array("_obj" => "TT_User_Sessions__c", "_html" => "TT User Sessions"),
        ),
        "objs" => array(
          "Note" => array(
//            "user_filter"=>"rel_list",
          'no_user_filter_allowed' => true,
          "acts"=>array("i","u"),
          "edit"=>array(
              "_l"=>array("r1"=>array("c1"=>array("Title","Body"))),
            ),
        ),
            "TT_User__c" => array(
                'no_user_filter_allowed' => true,
								"sLabel"=>"TT User",
                "acts" => array("i","u"),
                "list" => array('Name' => array(), 'User_Number__c' => array(), 'User_Type__c' => array(), 'Status__c' => array(), 'Status_Date__c' => array(), 'Inactive_Reason__c' => array(), 'Broker__c' => array(), "_colFilters" => array("User_Type__c" => array('_onTop' => true))),
                "viewSel" => array(
                    'Broker - Affiliate' => array('User_Type__c' => 'Broker','Broker_Type__c' => 'Affiliate'),
                    'Broker - Credits' => array('User_Type__c' => 'Broker','Broker_Type__c' => 'Credits'),
                    'Broker - Discount' => array('User_Type__c' => 'Broker','Broker_Type__c' => 'Discount'),
                    'Supplier' => array('User_Type__c' => 'Supplier','Status__c'=>'Active'),
                    'Prospective - Supplier'=>array('User_Type__c'=>'Supplier'),
                    'Client' => array('User_Type__c' => 'Client'),
                    'Admin' => array('User_Type__c' => 'Admin'),
                ),
                "editSel" => array(
                    'Broker - Affiliate' => array('User_Type__c' => 'Broker','Broker_Type__c' => 'Affiliate'),
                    'Broker - Credits' => array('User_Type__c' => 'Broker','Broker_Type__c' => 'Credits'),
                    'Broker - Discount' => array('User_Type__c' => 'Broker','Broker_Type__c' => 'Discount'),
                    'Supplier' => array('User_Type__c' => 'Supplier','Status__c'=>'Active'),
                    'Prospective - Supplier'=>array('User_Type__c'=>'Supplier'),
                    'Client' => array('User_Type__c' => 'Client'),
                    'Admin' => array('User_Type__c' => 'Admin'),
                ),
                "view" => array(
                    "_flat" => true,
                    "_l" => $_sfl__d_TT_User__c_Master,
                    'Broker - Affiliate' => $_sfl__d_TT_User__c_Broker_Affiliate,
                    'Broker - Credits' => $_sfl__d_TT_User__c_Broker_Credits,
                    'Broker - Discount' => $_sfl__d_TT_User__c_Broker_Discount,
                    'Supplier' => $_sfl__d_TT_User__c_Supplier,
                    'Prospective - Supplier'=>$_sfl__d_TT_User__c_Prospective_Supplier,
                    'Client' => $_sfl__d_TT_User__c_Client,
                    'Admin' => $_sfl__d_TT_User__c_Admin,
                ),
                "edit" => array(
                    "_flat" => true,
                    "_l" => $_sfl__e_TT_User__c_Master,
                    'Broker - Affiliate' => $_sfl__e_TT_User__c_Broker_Affiliate,
                    'Broker - Credits' => $_sfl__e_TT_User__c_Broker_Credits,
                    'Broker - Discount' => $_sfl__e_TT_User__c_Broker_Discount,
                    'Supplier' => $_sfl__e_TT_User__c_Supplier,
                    'Prospective - Supplier'=>$_sfl__e_TT_User__c_Prospective_Supplier,
                    'Client' => $_sfl__d_TT_User__c_Client,
                    'Admin' => $_sfl__d_TT_User__c_Admin,
                ),
            ),
            "TT_Chargebacks__c" => array(
                'no_user_filter_allowed' => true,
								"sLabel"=>"TT Chargebacks",
                "acts" => array("i","u"),
                "list" => array('Name' => array(), 'User__c' => array(), 'Chargeback_Reason__c' => array(), 'Response_Deadline__c' => array()),
                "view" => array(
                    "_flat" => true,
                    "_l" => $_sfl__d_TT_Chargebacks__c_Master,
                ),
                "edit" => array(
                    "_flat" => true,
                    "_l" => $_sfl__e_TT_Chargebacks__c_Master,
                ),
            ),
            "TT_Client_Order__c" => array(
                'no_user_filter_allowed' => true,
								"sLabel"=>"TT Client",
                "acts" => array("i","u"),
                "list" => array('Name' => array(), 'Client__c' => array(), 'Reseller__c' => array(), 'Sales_Rep_Team_Member__c' => array(), 'Sales_Manager_from_Team_Member__c' => array(), 'Payment_Method__c' => array()),
                "view" => array(
                    "_flat" => true,
                    "_l" => $_sfl__d_TT_Client_Order__c_Master,
                ),
                "edit" => array(
                    "_flat" => true,
                    "_l" => $_sfl__e_TT_Client_Order__c_Master,
                ),
            ),
            "TT_Credit_Report__c" => array(
                'no_user_filter_allowed' => true,
								"sLabel"=>"TT Credit Report",
                "acts" => array("i","u"),
                "list" => array('Name' => array(), 'User__c' => array(), 'Source_Type__c' => array(), 'Client_Cost__c' => array(), 'Internal_Source__c' => array(), 'Inquiry_Text__c' => array()),
                "view" => array(
                    "_flat" => true,
                    "_l" => $_sfl__d_TT_Credit_Report__c_Master,
                ),
                "edit" => array(
                    "_flat" => true,
                    "_l" => $_sfl__e_TT_Credit_Report__c_Master,
                ),
            ),
            "TT_Creditor__c" => array(
                'no_user_filter_allowed' => true,
								"sLabel"=>"TT Creditor",
                "acts" => array("i","u"),
                "list" => array('Name' => array(), 'Creditor_Number__c' => array(), 'Average_Max_Spots__c' => array(), 'Image__c' => array()),
                "view" => array(
                    "_flat" => true,
                    "_l" => $_sfl__d_TT_Creditor__c_Master,
                ),
                "edit" => array(
                    "_flat" => true,
                    "_l" => $_sfl__e_TT_Creditor__c_Master,
                ),
            ),
            "TT_Express_Posting__c" => array(
                'no_user_filter_allowed' => true,
								"sLabel"=>"TT Express Posting",
                "acts" => array("i","u"),
                "list" => array('Name' => array(), 'Supplier__c' => array(), 'Tradeline__c' => array(), 'Tradeline_Description__c' => array()),
                "view" => array(
                    "_flat" => true,
                    "_l" => $_sfl__d_TT_Express_Posting__c_Master,
                ),
                "edit" => array(
                    "_flat" => true,
                    "_l" => $_sfl__e_TT_Express_Posting__c_Master,
                ),
            ),
            "TT_Package__c" => array(
                'no_user_filter_allowed' => true,
								"sLabel"=>"TT Package",
                "acts" => array("i","u"),
                "list" => array('Name' => array(), 'Package_No__c' => array(), 'Type__c' => array(), 'Cycles__c' => array(), "_colFilters" => array("Type__c" => array('_onTop' => true))),
                "viewSel" => array(
                    'client' => array('Type__c' => 'Client'),
                    'broker' => array('Type__c' => 'Broker'),
                ),
                "editSel" => array(
                    'client' => array('Type__c' => 'Client'),
                    'broker' => array('Type__c' => 'Broker'),
                ),
                "view" => array(
                    "_flat" => true,
                    "_l" => $_sfl__d_TT_Package__c_Master,
                    'client' => $_sfl__d_TT_Package__c_Client_Package,
                    'broker' => $_sfl__d_TT_Package__c_Broker_Package
                ),
                "edit" => array(
                    "_flat" => true,
                    "_l" => $_sfl__e_TT_Package__c_Master,
                    'client' => $_sfl__e_TT_Package__c_Client_Package,
                    'broker' => $_sfl__e_TT_Package__c_Broker_Package
                ),
            ),
            "TT_Supplier_Payment__c" => array(
                'no_user_filter_allowed' => true,
								"sLabel"=>"TT Supplier Payment",
                "acts" => array("i","u"),
                "list" => array('Name' => array(), 'Supplier__c' => array(), 'User_Type__c' => array(), 'ScreenShot_Proof__c' => array(), "_colFilters" => array("User_Type__c" => array('_onTop' => true))),
                "viewSel" => array(
                    'Broker - Affiliate' => array('User_Type__c' => 'Broker - Affiliate'),
                    'Broker - Credits' => array('User_Type__c' => 'Broker - Credits'),
                    'Client' => array('User_Type__c' => 'Client'),
                    'Supplier' => array('User_Type__c' => 'Supplier'),
                ),
                "editSel" => array(
                    'Broker - Affiliate' => array('User_Type__c' => 'Broker - Affiliate'),
                    'Broker - Credits' => array('User_Type__c' => 'Broker - Credits'),
                    'Client' => array('User_Type__c' => 'Client'),
                    'Supplier' => array('User_Type__c' => 'Supplier'),
                ),
                "view" => array(
                    "_flat" => true,
                    "_l" => $_sfl__d_TT_Supplier_Payment__c_Master,
                    'Broker - Affiliate' => $_sfl__d_TT_Supplier_Payment__c_Broker_Affiliate,
                    'Broker - Credits' => $_sfl__d_TT_Supplier_Payment__c_Broker_Credits,
                    'Client' => $_sfl__d_TT_Supplier_Payment__c_Client,
                    'Supplier' => $_sfl__d_TT_Supplier_Payment__c_Supplier,
                ),
                "edit" => array(
                    "_flat" => true,
                    "_l" => $_sfl__e_TT_Supplier_Payment__c_Master,
                    'Broker - Affiliate' => $_sfl__e_TT_Supplier_Payment__c_Broker_Affiliate,
                    'Broker - Credits' => $_sfl__e_TT_Supplier_Payment__c_Broker_Credits,
                    'Client' => $_sfl__e_TT_Supplier_Payment__c_Client,
                    'Supplier' => $_sfl__e_TT_Supplier_Payment__c_Supplier,

                ),
            ),
            "TT_Penalty__c" => array(
                'no_user_filter_allowed' => true,
								"sLabel"=>"TT Penalty",
                "acts" => array("i","u"),
                "list" => array('Name' => array(), 'TT_User__c' => array()),
                "view" => array(
                    "_flat" => true,
                    "_l" => $_sfl__d_TT_Penalty__c_Master,
                ),
                "edit" => array(
                    "_flat" => true,
                    "_l" => $_sfl__e_TT_Penalty__c_Master,
                ),
            ),
            "TT_Supplier_Order__c" => array(
                'no_user_filter_allowed' => true,
								"sLabel"=>"TT Supplier Order",
                "acts" => array("i","u"),
                "list" => array('Name' => array(), 'Tradeline__c' => array(), 'Client_Order__c' => array(), 'Supplier__c' => array()),
                "view" => array(
                    "_flat" => true,
                    "_l" => $_sfl__d_TT_Supplier_Order__c_Master,
                ),
                "edit" => array(
                    "_flat" => true,
                    "_l" => $_sfl__e_TT_Supplier_Order__c_Master,
                ),
            ),
            "TT_Supplier_Referral_Fees__c" => array(
                'no_user_filter_allowed' => true,
								"sLabel"=>"TT Supplier Referral Fee",
                "acts" => array("i","u"),
                "list" => array('Name' => array(), 'TT_Payment__c' => array(), 'Supplier__c' => array(), 'Amount__c' => array(), 'Supplier_Fees_Debit_Description__c' => array()),
                "view" => array(
                    "_flat" => true,
                    "_l" => $_sfl__d_TT_Supplier_Referral_Fees__c_Master,
                ),
                "edit" => array(
                    "_flat" => true,
                    "_l" => $_sfl__e_TT_Supplier_Referral_Fees__c_Master,
                ),
            ),
            "TT_Tradeline__c" => array(
                'no_user_filter_allowed' => true,
								"sLabel"=>"TT Tradeline",
                "acts" => array("i","u"),
                "list" => array('Name' => array(), 'Supplier__c' => array(), 'Creditor__c' => array(), 'Credit_Limit__c' => array(), 'Open_Date__c' => array()),
                "view" => array(
                    "_flat" => true,
                    "_l" => $_sfl__d_TT_Tradeline__c_Master,
                ),
                "edit" => array(
                    "_flat" => true,
                    "_l" => $_sfl__e_TT_Tradeline__c_Master,
                ),
            ),
            "TT_Tradeline_Group__c" => array(
                'no_user_filter_allowed' => true,
								"sLabel"=>"TT Tradeline Group",
                "acts" => array("i","u"),
                "list" => array('Name' => array(), 'Street_Address__c' => array(), 'Supplier__c' => array()),
                "view" => array(
                    "_flat" => true,
                    "_l" => $_sfl__d_TT_Tradeline_Group__c_Master,
                ),
                "edit" => array(
                    "_flat" => true,
                    "_l" => $_sfl__e_TT_Tradeline_Group__c_Master,
                ),
            ),
            "TT_Tradeline_Statements__c" => array(
                'no_user_filter_allowed' => true,
								"sLabel"=>"TT Tradeline Statement",
                "acts" => array("i","u"),
                "list" => array('Name' => array(), 'TT_Tradeline__c' => array(), 'Balance__c' => array(), 'Statement_Date__c' => array(), 'Status__c' => array(), 'Statement_Screenshot__c' => array(), 'Status_Date__c' => array()),
                "view" => array(
                    "_flat" => true,
                    "_l" => $_sfl__d_TT_Tradeline_Statements__c_Master,
                ),
                "edit" => array(
                    "_flat" => true,
                    "_l" => $_sfl__d_TT_Tradeline_Statements__c_Master,
                ),
            ),
            "TT_User_Sessions__c" => array(
                'no_user_filter_allowed' => true,
								"sLabel"=>"TT User Session",
                "acts" => array(),
                "list" => array('Name' => array(), 'TT_User__c' => array(), 'IP__c' => array(),),
                "view" => array(
                    "_flat" => true,
                    "_l" => $_sfl__d_TT_User_Sessions__c_Master,
                ),
                "edit" => array(
                    "_flat" => true,
                    "_l" => $_sfl__e_TT_User_Sessions__c_Master,
                ),
            ),
        ),

    ),
    "IB" => array(
        /*
      'IB_User__c',
	'IB_Order__c',
	'IB_Dispute__c',
	'IB_Creditor__c',
	'IB_Payment__c',
        */
        "sel_on" => array(
            "_regexp0" => array("Brand_App_Access__c" => "#(^|;)ib(;|$)#i"),
            'Status__c' => 'Active',
            "_cond1" => array($sys_brand_name => "ib"),
        ),
        "defpage" => array('type' => "obj", "obj" => "IB_User__c"),
        "menu" => array(
            "Users" => array("_obj" => "IB_User__c", "_html" => "IB Users"),
            "Orders" => array("_obj" => "IB_Order__c", "_html" => "IB  Orders"),
            "Disputes" => array("_obj" => "IB_Dispute__c", "_html" => "IB  Disputes"),
            "Creditors" => array("_obj" => "IB_Creditor__c", "_html" => "IB  Creditors"),
            "Payments" => array("_obj" => "IB_Payment__c", "_html" => "IB  Payments"),
        ),
        "objs" => array(
          "Note" => array(
//            "user_filter"=>"rel_list",
          'no_user_filter_allowed' => true,
          "acts"=>array("i","u","d"),
          "edit"=>array(
              "_l"=>array("r1"=>array("c1"=>array("Title","Body"))),
            ),
        ),
            "IB_User__c" => array(
                'no_user_filter_allowed' => true,
								"sLabel"=>"IB User",
                "acts" => array("i","u"),
                "list" => array('Name' => array(), 'User_Number__c' => array(), 'User_Type__c' => array(), 'Status__c' => array(), 'Status_Date__c' => array(), "_colFilters" => array("User_Type__c" => array('_onTop' => true))),
                "viewSel" => array(
                    'Broker - Affiliate' => array('User_Type__c' => 'Broker','Broker_Type__c' => 'Affiliate'),
                    'Broker - Credits' => array('User_Type__c' => 'Broker','Broker_Type__c' => 'Credits'),
                    'Broker - Discount' => array('User_Type__c' => 'Broker','Broker_Type__c' => 'Discount'),
                    'Client' => array('User_Type__c' => 'Client'),
                    'Admin' => array('User_Type__c' => 'Admin'),
                ),
                "editSel" => array(
                    'Broker - Affiliate' => array('User_Type__c' => 'Broker','Broker_Type__c' => 'Affiliate'),
                    'Broker - Credits' => array('User_Type__c' => 'Broker','Broker_Type__c' => 'Credits'),
                    'Broker - Discount' => array('User_Type__c' => 'Broker','Broker_Type__c' => 'Discount'),
                    'Client' => array('User_Type__c' => 'Client'),
                    'Admin' => array('User_Type__c' => 'Admin'),
                ),
                "view" => array(
                    "_flat" => true,
                    "_l" => $_sfl__d_IB_User__c_Master,
                    'Broker - Affiliate' => $_sfl__d_IB_User__c_Broker_Affiliate,
                    'Broker - Credits' => $_sfl__d_IB_User__c_Broker_Credits,
                    'Broker - Discount' => $_sfl__d_IB_User__c_Broker_Discount,
                    'Client' => $_sfl__d_IB_User__c_Client,
                    'Admin' => $_sfl__d_IB_User__c_Admin,
                ),
                "edit" => array(
                    "_flat" => true,
                    "_l" => $_sfl__e_IB_User__c_Master,
                    'Broker - Affiliate' => $_sfl__e_IB_User__c_Broker_Affiliate,
                    'Broker - Credits' => $_sfl__e_IB_User__c_Broker_Credits,
                    'Broker - Discount' => $_sfl__e_IB_User__c_Broker_Discount,
                    'Client' => $_sfl__e_IB_User__c_Client,
                    'Admin' => $_sfl__e_IB_User__c_Admin,
                ),
            ),
            "IB_Order__c" => array(
                'no_user_filter_allowed' => true,
								"sLabel"=>"IB Order",
                "acts" => array("i","u"),
                "list" => array('Name' => array(), 'Client__c' => array(), 'Broker__c' => array(), 'Sales_Rep_from_Team_Member__c' => array(), 'Sales_Manager_from_Team_Member__c' => array()),
                "view" => array(
                    "_flat" => true,
                    "_l" => $_sfl__d_IB_Order__c_Master,
                ),
                "edit" => array(
                    "_flat" => true,
                    "_l" => $_sfl__e_IB_Order__c_Master,
                ),
            ),
            "IB_Dispute__c" => array(
                'no_user_filter_allowed' => true,
								"sLabel"=>"IB Dispute",
                "acts" => array("i","u"),
                "list" => array('Order__c' => array(), 'Name' => array(), 'Dispute_Type__c' => array(), 'Bureau__c' => array(), 'Inquiry_Date_Formula__c' => array(), "_colFilters" => array("Dispute_Type__c" => array('_onTop' => true))),
                "viewSel" => array(
                    'Bureau' => array('Dispute_Type__c' => 'Bureau'),
                    'Creditor' => array('Dispute_Type__c' => 'Creditor'),
                ),
                "editSel" => array(
                    'Bureau' => array('Dispute_Type__c' => 'Bureau'),
                    'Creditor' => array('Dispute_Type__c' => 'Creditor'),
                ),
                "view" => array(
                    "_flat" => true,
                    "_l" => $_sfl__d_IB_Dispute__c_Master,
                    'Bureau' => $_sfl__d_IB_Dispute__c_Bureau,
                    'Creditor' => $_sfl__d_IB_Dispute__c_Creditor,
                ),
                "edit" => array(
                    "_flat" => true,
                    "_l" => $_sfl__e_IB_Dispute__c_Master,
                    'Bureau' => $_sfl__e_IB_Dispute__c_Bureau,
                    'Creditor' => $_sfl__e_IB_Dispute__c_Creditor,
                ),
            ),
            "IB_Creditor__c" => array(
                'no_user_filter_allowed' => true,
								"sLabel"=>"IB Creditor",
                "acts" => array("i","u"),
                "list" => array('Creditor_Number__c' => array(), 'Name' => array(), 'Phone__c' => array()),
                "view" => array(
                    "_flat" => true,
                    "_l" => $_sfl__d_IB_Creditor__c_Master,
                ),
                "edit" => array(
                    "_flat" => true,
                    "_l" => $_sfl__e_IB_Creditor__c_Master,
                ),
            ),
            "IB_Payment__c" => array(
                'no_user_filter_allowed' => true,
								"sLabel"=>"IB Payment",
                "acts" => array("i","u"),
                "list" => array('Name' => array(), 'Payment_By__c' => array(), 'IB_User__c' => array()),
                "view" => array(
                    "_flat" => true,
                    "_l" => $_sfl__d_IB_Payment__c_Master,
                ),
                "edit" => array(
                    "_flat" => true,
                    "_l" => $_sfl__e_IB_Payment__c_Master,
                ),
            ),
        ),

    ),
    "WSC" => array(
        /*'SC_User__c',
	'SC_Corp__c',
	'SC_Client_Order__c',
	'SC_Corp_Calls__c',*/
        "sel_on" => array(
            "_regexp0" => array("Brand_App_Access__c" => "#(^|;)wsc(;|$)#i"),
            'Status__c' => 'Active',
            "_cond1" => array($sys_brand_name => "wsc"),
        ),
        "defpage" => array('type' => "obj", "obj" => "SC_User__c"),
        "menu" => array(
            "Users" => array("_obj" => "SC_User__c", "_html" => "SC Users"),
            "Corp" => array("_obj" => "SC_Corp__c", "_html" => "SC Corp"),
            "Client Orders" => array("_obj" => "SC_Client_Order__c", "_html" => "SC Client Orders"),
            "Corp Calls" => array("_obj" => "SC_Corp_Calls__c", "_html" => "SC Corp Calls"),
        ),
        "objs" => array(
          "Note" => array(
//            "user_filter"=>"rel_list",
          'no_user_filter_allowed' => true,
          "acts"=>array("i","u"),
          "edit"=>array(
              "_l"=>array("r1"=>array("c1"=>array("Title","Body"))),
            ),
        ),
            "SC_User__c" => array(
                'no_user_filter_allowed' => true,
								"sLabel"=>"SC User",
                "acts" => array("i","u"),
                "list" => array('Name' => array(), 'User_Type__c' => array(), 'User_Number__c' => array(), "_colFilters" => array("User_Type__c" => array('_onTop' => true))),
                "viewSel" => array(
                    'Client' => array('User_Type__c' => 'Client'),
                    'Broker' => array('User_Type__c' => 'Broker'),
                ),
                "editSel" => array(
                    'Client' => array('User_Type__c' => 'Client'),
                    'Broker' => array('User_Type__c' => 'Broker'),
                ),
                "view" => array(
                    "_flat" => true,
                    "_l" => $_sfl__d_SC_User__c_Master,
                    'Client' => $_sfl__d_SC_User__c_Client,
                    'Broker' => $_sfl__d_SC_User__c_Broker,
                ),
                "edit" => array(
                    "_flat" => true,
                    "_l" => $_sfl__e_SC_User__c_Master,
                    'Client' => $_sfl__e_SC_User__c_Client,
                    'Broker' => $_sfl__e_SC_User__c_Broker,
                ),
            ),
            "SC_Corp__c" => array(
                'no_user_filter_allowed' => true,
								"sLabel"=>"SC Corp",
                "acts" => array("i","u"),
                "list" => array('Corp__c' => array(), 'Name' => array(), 'Jurisdiction__c' => array(), 'Incorporation_Date__c' => array(), 'Age__c' => array()),
                "view" => array(
                    "_flat" => true,
                    "_l" => $_sfl__d_SC_Corp__c_Master,
                ),
                "edit" => array(
                    "_flat" => true,
                    "_l" => $_sfl__e_SC_Corp__c_Master,
                ),
            ),
            "SC_Client_Order__c" => array(
                'no_user_filter_allowed' => true,
								"sLabel"=>"SC Client Order",
                "acts" => array("i","u"),
                "list" => array('SC_Corp__c' => array(), 'AStatus__c' => array(), 'Juridiction__c' => array()),
                "view" => array(
                    "_flat" => true,
                    "_l" => $_sfl__d_SC_Client_Order__c_Master,
                ),
                "edit" => array(
                    "_flat" => true,
                    "_l" => $_sfl__e_SC_Client_Order__c_Master,
                ),
            ),
            "SC_Corp_Calls__c" => array(
                'no_user_filter_allowed' => true,
								"sLabel"=>"SC Corp Call",
                "acts" => array("i","u"),
                "list" => array('Name' => array(), 'Corp__c' => array(), 'Call_Type__c' => array(), 'Client__c' => array(), 'SC_Client_Order__c' => array()),
                "view" => array(
                    "_flat" => true,
                    "_l" => $_sfl__d_SC_Corp_Calls__c_Master,
                ),
                "edit" => array(
                    "_flat" => true,
                    "_l" => $_sfl__e_SC_Corp_Calls__c_Master,
                ),
            ),

        ),

    ),
);


//$objviews['FS']=$objviews['FU'];
$objviews['FS']=array("defpage" => array('type' => "obj", "obj" => "FU_User__c"));
$objviews['FS']['sel_on']=array("_cond1" => array($sys_brand_name => "fs"),"localLogin"=>'fund_spec');
$objviews['FS']['menu']= array(
            "FU Users" => array("_obj" => "FU_User__c", "_html" => "FU Users"),
            "FU Analysis" => array("_obj" => "FU_Analysis__c", "_html" => "FU Analysis"),
            "FU Apps" => array("_obj" => "FU_App__c", "_html" => "FU Apps"),
            "FU Creditors" => array("_obj" => "FU_Creditor__c", "_html" => "FU Creditors"),
		);

$objviews['FS']['objs']=array();
$objviews['FS']['objs']["FU_User__c"]=$objviews['FU']['objs']["FU_User__c"];
$objviews['FS']['objs']["FU_User__c"]['no_user_filter_allowed']=true;
$objviews['FS']['objs']["FU_User__c"]["acts"] = array("u");
$objviews['FS']['objs']["FU_User__c"]['list']["Status_Date__c"]=array();

$objviews['FS']['objs']["FU_Analysis__c"]=$objviews['FU']['objs']["FU_Analysis__c"];
$objviews['FS']['objs']["FU_Analysis__c"]['no_user_filter_allowed']=true;
$objviews['FS']['objs']["FU_Analysis__c"]["acts"] = array("u");

$objviews['FS']['objs']["FU_App__c"]=$objviews['FU']['objs']["FU_App__c"];
$objviews['FS']['objs']["FU_App__c"]['no_user_filter_allowed']=true;
$objviews['FS']['objs']["FU_App__c"]["acts"] = array("u");

$objviews['FS']['objs']["FU_Creditor__c"]=$objviews['FU']['objs']["FU_Creditor__c"];
$objviews['FS']['objs']["FU_Creditor__c"]['no_user_filter_allowed']=true;
$objviews['FS']['objs']["FU_Creditor__c"]["acts"] = array("u");

$objviews['FS']['objs']["Note"]=$objviews['FU']['objs']["Note"];


