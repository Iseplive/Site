{"posts":[
<?php
$ijklmn = 0;
foreach($posts as $post){
    require dirname(__FILE__).'/../_includes/JSON_POST.php';
    $ijklmn++;
    if ($ijklmn!=count($posts)) {?>,<?php}
}
?>
]}
