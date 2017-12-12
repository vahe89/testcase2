<?php
$dn=dirname(__FILE__);
require_once("{$dn}/classes/user.class.php");
session_start();
global $user;
$user=new User();

$v=array(
    "no_user_filter_allowed"=>true,
    "list"=>array("test","test2"),
    );

$data=array(
    array("test"=>1,"test2"=>2),
    );

$user->new_vobj("test",$data,array(),$v);


$user->run();

?>
