<?php 



/*	"products"=>array(
	"table"=>"products",
	"fields"=>array("f1","f2","f3"), 
	"fctrls"=>array(
		"fname"=>array("c"=>text|select|date|textarea|htmltextarea,"t"=>"admin title"),
	),
	"def_lang"=>$def_lang,
	"langs"=>array("fr","en",etc),
	"opts"=>array(options),
	"rels"=>array(
		"curFieldName"=>array("tbl"=>"joinTableName","fld"=>"joinFieldtoUSE","on"=>"joinTblFIELDToJoin","join"=>"left join"),
	),
	"uprels"=array("curFieldName"=>array("tbl"=>"joinTableName","fld"=>"joinFieldtoUSE","langs"=>false = use def lang row always|true = use cur lang row)),
	"downrels"=>array(
		"TableName"=>array("fld"=>"joinTableFIELDName","on"=>"curFIELDname","ajax"=>true|false,"editTpl"=>"def","listTpl"=>"def"),
		"addresses"=>array("fld"=>"company","lang"=>false,"onNew"=>false,"on"=>"id","ajaxEdit"=>true,"editTpl"=>"def_list","listTpl"=>"def"),
	),
	),
 */


$dbobjs=array(

	"pages"=>array(
		"table"=>"pages",
//		"fields"=>array("slug","title","body","script"),
		"fctrls"=>array(
			"slug"=>array("t"=>"Slug","c"=>"text"),
			"title"=>array("t"=>"Title","c"=>"htmltextarea"/*,"cols"=>60,"rows"=>2*/),
			"body"=>array("t"=>"Page content","c"=>"htmltextarea"),
			"script"=>array("t"=>"Script","c"=>"textarea","cols"=>120,"rows"=>10),
		),
		"def_lang"=>$def_lang,
		"langs"=>$glangs,
		"opts"=>array("sel_titleFld"=>"ct.slug","sel_titleLen"=>25,"adminListFlds"=>array('slug'),
		"sys_files"=>array("url"=>"files/","path"=>"files/",
		"types"=>array(
			"bgpic"=>array("c"=>"img","t"=>"BG Image","autoResize"=>false,"onNew"=>true,"afterFld"=>"body","admShow"=>true),
		)),
	),
),
	
"menu"=>array(
	"table"=>"menu",
	"fields"=>array("name"),
	"def_lang"=>$def_lang,
	"langs"=>$glangs,
	"opts"=>array("listTpl"=>"menu","editTpl"=>"menu_edit",
		"sys_links"=>array(
			"link"=>array("types"=>array("news","jobs","videos","pages","url"),"num"=>1,"t"=>"Link:","noname"=>true),
		),
		"sys_prios"=>array("hierarhy"=>true,"afterFld"=>false,'t'=>'First/after appear'),		
	),
),

"index_page"=>array(
	"fields"=>array("name","val"),
	"table"=>"sys_config",
	"opts"=>array("sys_queryWhere"=>"ct.name='index_page'","listTpl"=>"sys_conf_index","redirect"=>"index.php",),
	"fctrls"=>array("name"=>array("def"=>"index_page","c"=>"hidden"),"val"=>array("t"=>"Index page","c"=>"select")),
	"rels"=>array(
		"val"=>array("tbl"=>"pages","on"=>"id","fld"=>"slug"),
	),
	"def_lang"=>$def_lang,
	"langs"=>$glangs,
),

"classes"=>array(
	"table"=>"classes",
	"fields"=>array("course","roster_id","teacher","presenter_email","presenter_id","presenter_name","start_time","title",
		"duration","time_zone","attendee_limit","control_category_id","create_recording","return_url","status_ping_url",
		"language_culture_name","wid","status","present_cnt","absent_cnt"),
	"fctrls"=>array(
			"course"=>array("t"=>"Курс","c"=>"string"),
			"roster_id"=>array("t"=>"","c"=>"hidden"),
			"teacher"=>array("t"=>"Спикер","c"=>"string"),
			"presenter_email"=>array("t"=>"WizIQ Email спикера","c"=>"string"),
			"presenter_id"=>array("t"=>"WizIQ ID спикера","c"=>"string"),
			"presenter_name"=>array("t"=>"WizIQ Имя спикера","c"=>"string"),
			"start_time"=>array("t"=>"WizIQ Начало","c"=>"string"),
			"title"=>array("t"=>"WizIQ Название","c"=>"string"),
			"duration"=>array("t"=>"WizIQ Длительность","c"=>"string"),
			"time_zone"=>array("t"=>"WizIQ Часовой пояс","c"=>"string"),
			"attendee_limit"=>array("t"=>"WizIQ Лимит студентов","c"=>"string"),
			"control_category_id"=>array("t"=>"WizIQ ID категории","c"=>"string"),
			"create_recording"=>array("t"=>"WizIQ Записывать ли","c"=>"string"),
			"return_url"=>array("t"=>"WizIQ URL","c"=>"string"),
			"status_ping_url"=>array("t"=>"WizIQ URL статуса","c"=>"string"),
			"language_culture_name"=>array("t"=>"WizIQ язык","c"=>"string"),
			"wid"=>array("t"=>"??","c"=>"string"),
			"status"=>array("t"=>"Статус","c"=>"string"),
			"present_cnt"=>array("t"=>"Присутствовало","c"=>"string"),
			"absent_cnt"=>array("t"=>"Отсутствовало","c"=>"string")
		),

	"def_lang"=>$def_lang,
	"langs"=>$glangs,
	"opts"=>array("sel_titleFld"=>"ct.title"),
	"rels"=>array(
			"course"=>array("tbl"=>"courses","fld"=>"name","on"=>"id"),
			"teacher"=>array("tbl"=>"teachers","fld"=>"nickname","on"=>"id"),
			"roster_id"=>array("tbl"=>"roster","fld"=>"event_date","on"=>"id"),
	),

),

"courses"=>array(
	"table"=>"courses",
	"fields"=>array("slug","name","start","end","days","teacher","descr","price","max_students","registered_studs","teacher_price","total_price","lessons_cnt","total_hours","hidden","status"),
	"fctrls"=>array(
		"slug"=>array("t"=>"Slug","c"=>"text"),
		"name"=>array("t"=>"Название","c"=>"text"),
		"start"=>array("t"=>"Начало","c"=>"date"),
		"end"=>array("t"=>"Конец","c"=>"date"),
//		"days"=>array("t"=>"Дни","c"=>"checkbox","sopts"=>array(1=>"Понедельник",2=>"Вторник",3=>"Среда",4=>"Четверг",5=>"Пятница",6=>"Суббота",7=>"Воскресенье")),
		"days"=>array("t"=>"Время","c"=>"days","from_name"=>"Начало:","to_name"=>"Конец:"),
		"teacher"=>array("t"=>"Ведущий","c"=>"select"),
		"descr"=>array("t"=>"Описание","c"=>"textarea"),
		"price"=>array("t"=>"Цена за час (грн)","c"=>"text","_s"=>true),
		"max_students"=>array("t"=>"Макс. кол-во студентов","c"=>"text","_s"=>true),
		"registered_studs"=>array("t"=>"Зарегистрировалось","c"=>"string","_s"=>true),
		"teacher_price"=>array("t"=>"Оплата спикеру за час (грн)","c"=>"text","_s"=>true),
		"total_price"=>array("t"=>"Цена курса с человека (грн)","c"=>"string","_s"=>true),
		"lessons_cnt"=>array("t"=>"Кол-во уроков","c"=>"string","_s"=>true),
		"total_hours"=>array("t"=>"Всего часов","c"=>"string","_s"=>true),
		"hidden"=>array("t"=>"Скрыт","c"=>"radio","opts"=>array(0=>"Открыт для пользователей",1=>"Скрыт от всех"),"_s"=>true),
		"status"=>array("t"=>"Статус","c"=>"string","opts"=>array(0=>"Открыт для регистрации",1=>"Идёт сейчас",2=>"Окончен"),"_s"=>true)
		),

	"def_lang"=>$def_lang,
	"langs"=>$glangs,
	"opts"=>array("sel_titleFld"=>"ct.name"),
	"rels"=>array(
			"teacher"=>array("tbl"=>"teachers","fld"=>"nickname","on"=>"id"),
	),
),

"roster"=>array(
	"table"=>"roster",
	"fields"=>array("event_date","events_cnt"),
	"fctrls"=>array(
		"event_date"=>array("t"=>"","c"=>"text"),
		"events_cnt"=>array("t"=>"","c"=>"text")
		),

	"def_lang"=>$def_lang,
	"langs"=>$glangs,
	"opts"=>array("sel_titleFld"=>"ct.event_date"),
),

"studs"=>array(
	"table"=>"studs",
	"fields"=>array("login","pass","nickname","name","surname","patronymic","sex","birthday","phone1","phone2","address","city","descr","teacher_descr",
	"admin_descr","courses_reg","courses_in","courses_done","spent","regdate"),
	"fctrls"=>array(
		"login"=>array("t"=>"Логин","c"=>"text"),
		"pass"=>array("t"=>"Пароль","c"=>"password"),
		"nickname"=>array("t"=>"Ник","c"=>"text"),
		"name"=>array("t"=>"Имя","c"=>"text"),
		"surname"=>array("t"=>"Фамилия","c"=>"text"),
		"patronymic"=>array("t"=>"Отчество","c"=>"text"),
		"sex"=>array("t"=>"Пол","c"=>"select","opts"=>array(1=>"Мужской",0=>"Женский")),
		"birthday"=>array("t"=>"Дата рождения","c"=>"date"),
		"phone1"=>array("t"=>"Тел.","c"=>"text"),
		"phone2"=>array("t"=>"Доп. тел.","c"=>"text"),
		"address"=>array("t"=>"Адрес","c"=>"text"),
		"city"=>array("t"=>"Город","c"=>"city"),
		"descr"=>array("t"=>"Доп. инфо","c"=>"textarea"),
		"teacher_descr"=>array("t"=>"Заметки спикера","c"=>"textarea"),
		"admin_descr"=>array("t"=>"Заметки админа","c"=>"textarea"),
		"courses_reg"=>array("t"=>"Зарегистрирован на","c"=>"string"),
		"courses_in"=>array("t"=>"Проходит ","c"=>"string"),
		"courses_done"=>array("t"=>"Окончил","c"=>"string"),
		"spent"=>array("t"=>"Всего проплачено","c"=>"string"),
		"regdate"=>array("t"=>"Дата регистрации","c"=>"string")
		),

	"def_lang"=>$def_lang,
	"langs"=>array("ru"),
	"opts"=>array("sel_titleFld"=>"ct.nickname"),
),

"teachers"=>array(
	"table"=>"teachers",
	"fields"=>array("email","pass","nickname","name","surname","patronymic","birthday","descr","admin_descr","rating",
	"courses_reg","courses_in","courses_done","earned","regdate"),
	"fctrls"=>array(
		"email"=>array("t"=>"E-Mail","c"=>"text"),
		"pass"=>array("t"=>"пароль","c"=>"text"),
		"nickname"=>array("t"=>"Ник","c"=>"text"),
		"name"=>array("t"=>"Имя","c"=>"text"),
		"surname"=>array("t"=>"Фамилия","c"=>"text"),
		"patronymic"=>array("t"=>"Отчество","c"=>"text"),
		"birthday"=>array("t"=>"Дата рождения","c"=>"text"),
		"descr"=>array("t"=>"Информация","c"=>"text"),
		"admin_descr"=>array("t"=>"Заметки админа","c"=>"text"),
		"rating"=>array("t"=>"Рейтинг","c"=>"text"),
		"courses_reg"=>array("t"=>"Назначен на","c"=>"text"),
		"courses_in"=>array("t"=>"Ведет","c"=>"text"),
		"courses_done"=>array("t"=>"Завершил","c"=>"text"),
		"earned"=>array("t"=>"Заработано","c"=>"text"),
		"regdate"=>array("t"=>"Дата регистрации","c"=>"text")
		),

	"def_lang"=>$def_lang,
	"langs"=>$glangs,
	"opts"=>array("sel_titleFld"=>"ct.nickname"),
),

/*
"index_page"=>array(
	"fields"=>array("name","val"),
	"table"=>"sys_config",
	"opts"=>array("sys_queryWhere"=>"ct.name='index_page'","listTpl"=>"sys_conf_index","redirect"=>"index.php",
		"sys_files"=>array(	"url"=>"files/","path"=>"files/",
		"types"=>array(
			"bgpic"=>array("c"=>"image","t"=>"BG Image","autoResize"=>false,"onNew"=>true,"afterFld"=>false),
		)),
),

	"fctrls"=>array("name"=>array("def"=>"index_page","c"=>"hidden"),"val"=>array("t"=>"Index body","c"=>"htmltextarea")),
	"def_lang"=>$def_lang,
	"langs"=>$glangs,
),
 */

/*"news"=>array(
	"table"=>"news",
	"fields"=>array("title","summary","body","date","type","section","is_index"),
	"fctrls"=>array("title"=>array("t"=>"Title","c"=>"textarea","cols"=>60,"rows"=>2),
		"summary"=>array("t"=>"Summary","c"=>"textarea","cols"=>60,"rows"=>2),
		"body"=>array("t"=>"Page content","c"=>"htmltextarea"),
		"date"=>array("t"=>"Date","c"=>"date","f"=>"d/m/Y"),
		"type"=>array("t"=>"Type","c"=>"select"),
		"section"=>array("t"=>"Section","c"=>"select"),
		"is_index"=>array("t"=>"Show in index page","c"=>"radio","opts"=>array("0"=>"No","1"=>"Yes")),
	),
	"def_lang"=>$def_lang,
	"langs"=>$glangs,
	"opts"=>array("sel_titleFld"=>"ct.title","sel_titleLen"=>25,"adminListFlds"=>array('title'),"rowsPerPage"=>5,
		"sys_files"=>array("url"=>"files/","path"=>"files/",
			"types"=>array(
				"pic"=>array("c"=>"img","t"=>"Image","trim"=>array("w"=>269,"h"=>154),"autoResize"=>false,"onNew"=>true,"afterFld"=>true,"admShow"=>true),
		)),
	),
	"rels"=>array(
			"type"=>array("tbl"=>"news_type","fld"=>"name","on"=>"id"),
			"section"=>array("tbl"=>"sections","fld"=>"name","on"=>"id"),
	),
),

"news_type"=>array(
	"table"=>"news_type",
	"fields"=>array("name"),
	"fctrls"=>array("name"=>array("t"=>"Name","c"=>"text")),
	"def_lang"=>$def_lang,
	"langs"=>$glangs,
	"opts"=>array("sel_titleFld"=>"ct.name","sel_titleLen"=>25,"adminListFlds"=>array('name'),),
),

"jobs"=>array(
	"table"=>"jobs",
	"fields"=>array("title","summary","body","date"),
	"fctrls"=>array("title"=>array("t"=>"Title","c"=>"textarea","cols"=>60,"rows"=>2),
		"summary"=>array("t"=>"Summary","c"=>"textarea","cols"=>60,"rows"=>2),
		"body"=>array("t"=>"Page content","c"=>"htmltextarea"),
		"date"=>array("t"=>"Date","c"=>"date","f"=>"d/m/Y"),
	),
	"def_lang"=>$def_lang,
	"langs"=>$glangs,
	"opts"=>array("sel_titleFld"=>"ct.title","sel_titleLen"=>25,"adminListFlds"=>array('title'),"rowsPerPage"=>5),
),

"videos"=>array(
	"table"=>"videos",
	"fields"=>array("title","iframe","body","date","section","is_index"),
	"fctrls"=>array("title"=>array("t"=>"Title","c"=>"textarea","cols"=>60,"rows"=>2),
		"iframe"=>array("t"=>"IFRAME code","c"=>"textarea","cols"=>60,"rows"=>2),
		"body"=>array("t"=>"Page content","c"=>"htmltextarea"),
		"date"=>array("t"=>"Date","c"=>"date","f"=>"d/m/Y"),
		"section"=>array("t"=>"Section","c"=>"select"),
		"is_index"=>array("t"=>"Show in index page","c"=>"radio","opts"=>array("0"=>"No","1"=>"Yes")),
	),
	"def_lang"=>$def_lang,
	"langs"=>$glangs,
	"opts"=>array("sel_titleFld"=>"ct.title","sel_titleLen"=>25,"adminListFlds"=>array('title'),"rowsPerPage"=>5),
	"rels"=>array(
			"section"=>array("tbl"=>"sections","fld"=>"name","on"=>"id"),
	),
),
 */

/*
"sections"=>array(
	"table"=>"sections",
	"fields"=>array("name"),
	"fctrls"=>array("name"=>array("t"=>"Name","c"=>"text")),
	"def_lang"=>$def_lang,
	"langs"=>$glangs,
	"opts"=>array("sel_titleFld"=>"ct.name","sel_titleLen"=>25,"adminListFlds"=>array('name'),),
),*/


/*"topmenu"=>array(
	"table"=>"topmenu",
	"fields"=>array("name"),
	"def_lang"=>$def_lang,
	"langs"=>$glangs,
	"opts"=>array("adminListFlds"=>array('name'),
		"sys_links"=>array(
			"link"=>array("types"=>array("news","jobs","videos","pages","url"),"num"=>1,"t"=>"Link:","noname"=>true),
		),
		"sys_prios"=>array("hierarhy"=>false,"afterFld"=>false,'t'=>'First/after appear'),		
		"sys_files"=>array("url"=>"files/","path"=>"files/",
			"types"=>array(
				"pic"=>array("c"=>"img","t"=>"Image","trim"=>array("w"=>110,"h"=>86),"autoResize"=>false,"onNew"=>true,"afterFld"=>true,"admShow"=>true),
		)),
),
),

"botmenu"=>array(
	"table"=>"botmenu",
	"fields"=>array("name"),
	"def_lang"=>$def_lang,
	"langs"=>$glangs,
	"opts"=>array("listTpl"=>"menu","editTpl"=>"menu_edit",
		"sys_links"=>array(
			"link"=>array("types"=>array("news","jobs","videos","pages","url"),"num"=>1,"t"=>"Link:","noname"=>true),
		),
		"sys_prios"=>array("hierarhy"=>true,"afterFld"=>false,'t'=>'First/after appear'),		
),
),

 */



)
?>
