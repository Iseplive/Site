<?php
$posts = array_merge($posts,$official_posts);
usort($posts, function($a, $b) {
    return $a['id'] - $b['id'];
});
json_encode($posts);
//print_r($post);
//require dirname(__FILE__).'/../_includes/JSON_POST.php';
?>
