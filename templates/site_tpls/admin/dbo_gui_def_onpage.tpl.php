<div id = "<?php echo $this->hID("main_list_div", false) ?>" >
    <?php

    echo $this->listDef("{$this->opts['listInc']}"); ?>
</div >

<input type = "button" value = "Add <?php echo $this->copts['adminOneItemTitle']; ?>"
       onclick = "$.get(<?php $this->aL("index.php", "a=dbo_{$this->copts['gui_slaves_parent']}&f=gui_slaves_{$this->tbl}&rid=") ?>+<?php echo $this->hID("counter", false) ?>,function(d){$('#<?php echo $this->hID("main_list_div", false) ?>').append(d);<?php echo $this->hID("counter", false) ?>++;});" >

<script type = 'text/javascript' >
    var <?php echo $this->hID("counter",false)?>=
    2;
</script >



