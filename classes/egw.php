<?php
$D=dirname(__FILE__);
require_once("$D/user.class.php");
session_start();
global $user;
$user=new User();
if(isset($_SERVER['argv'][1]) && $_SERVER['argv'][1]!=false){
	$mm=$_SERVER['argv'][1];
if(method_exists($user,$mm))
	die($user->{$mm}());
}
die('ERR');

?>
