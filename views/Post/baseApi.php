<?php
$posted = array_merge($posts,$official_posts);
/*usort($posted, function($a, $b) {
    return $a['id'] - $b['id'];
});*/
json_encode($posted);
//print_r($post);
//require dirname(__FILE__).'/../_includes/JSON_POST.php';
?>
