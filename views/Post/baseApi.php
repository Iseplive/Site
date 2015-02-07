<?php
//$posted = array_merge($posts,$official_posts);
/*usort($posted, function($a, $b) {
    return $a['id'] - $b['id'];
});*/
if (isset($posts)) {
    if (isset($official_posts)) {
        $posted = array_merge($posts,$official_posts);
        usort($posted, function($a, $b) {
            return $a['id'] - $b['id'];
        });
        echo json_encode($posted);
    } else {
        echo json_encode($posts);
    }
}
if (isset($errors)&&!empty($errors)) {
    echo json_encode(array("error"=>$errors));
}
//print_r($post);
//require dirname(__FILE__).'/../_includes/JSON_POST.php';
?>
