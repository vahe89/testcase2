<?php
global $slave;
$slave = true;

$cV = $this->gO("cV");
$mo = $this->gO("gui_slaves_parent_obj");
if ($mo != false && is_array($cV) && is_object($this->p->t[$mo])) {
    $moCV = $this->p->t[$mo]->gO("gui_slave_cV_edit");
    if ($moCV)
        $cV['edit'] = $moCV;
}

$this->setCopts(array("echo" => true, "echoINS" => true, 'cV' => $cV));

$this->doInc("dbo_custom_view_edit");

$this->resetOpts();

?>
