<center >

    <h1 >Baners</h1 >

    <?php
    $cn = unserialize($this->getConfig("banners"));
    if (!is_array($cn))
        $cn = array();

    $b = array(
        "top" => array(1 => array("t" => "Top Banner")),
        "left" => array(
            1 => array("t" => "Left Banner 1"),
            2 => array("t" => "Left Banner 2"),
            3 => array("t" => "Left Banner 3"),
            4 => array("t" => "Left Banner 4"),
            5 => array("t" => "Left Banner 5"),
        )
    ); ?>

    <form action = 'index.php' method = 'POST' enctype = 'multipart/form-data' >
        <input type = 'hidden' name = 'a' value = 'p_banners' >
        <input type = 'hidden' name = 'redirect_url' value = 'index.php?a=a_banners' >
        <input type = 'submit' value = "Save" ><br />

        <?php foreach($b as $bp=>$ba){
foreach($ba as $bn=>$v){
?>
<h2><?php echo $v['t']?></h2>

<?php $this->drawABanner($bp,$bn)?> 

</br>
Link: <input name="banners[<?php echo $bp;?>][<?php echo $bn;?>][link]" value="<?php echo $cn[$bp][$bn]['link'];?>"> Banner: <input type='file' name='banner[<?php echo $bp;?>][<?php echo $bn;?>]' ><br/>

<hr/>
<?php } } ?>


    </form >
</center >
