<?php
$rc=$this->db->getRow($q="select sum(SR_Commision__c) as am from Online_Payment__c where Sales_Rep__c = {$this->p->curUsrId}");
$cc=$this->db->getRow("select sum(SR_Commision__c) as am from Online_Payment__c where Sales_Rep__c = {$this->p->curUsrId} and Status__c='Successfully Paid'");
$fc=$this->db->getRow("select sum(SR_Commision__c) as am from Online_Payment__c where Sales_Rep__c = {$this->p->curUsrId} and Status__c='Failed to Pay'");
echo "ALL Comissions: <b>$".number_format($rc['am'])."</b><br>";
echo "Earned Comissions: <b>$".number_format($cc['am'])."</b><br>";
echo "Failed Comissions: <b>$".number_format($fc['am'])."</b><br>";

?>
