<?php
$dn=dirname(__FILE__);
require("{$dn}/common_funcs.php");
if(aurl("/index.php")=="/index.php"){
	if(isset($_SERVER['argv'][1]) && $_SERVER['argv'][1]!=false && isset($_SERVER['argv'][2]) && $_SERVER['argv'][2]!=false && isset($_SERVER['argv'][3]) && $_SERVER['argv'][3]!=false){
		$_REQUEST=array(
					'a'=>'p_async',
					'func'=>$_SERVER['argv'][1],
					'artp'=>$_SERVER['argv'][2],
					'artpn'=>$_SERVER['argv'][3],
					'v2'=>true,
					'cmd'=>true
				);

		require_once("{$dn}/../index.php");
	}else{
		die("Wrong args");
	}
}else{
	die('DENIED');
}

?>
