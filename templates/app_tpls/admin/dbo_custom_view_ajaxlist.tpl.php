<?php
$cV = $this->gOA('cV');
if($cV==false || ! (isset($cV['list']) && is_array($cV['list']) && count($cV['list'])>0 )){
echo "<script type='text/javascript'>location.href='" . aurl("/") . "'</script>";
    die('');
}

if (!isset($cV['list']['_ajaxList'])) {
    $cV['list']['_ajaxList']=true;
}
$this->setOpts(array('cV'=>$cV));
require("dbo_custom_view_list.tpl.php");

?>
