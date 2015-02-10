<?php
if(isset($post))
	echo json_encode($post);

if (isset($errors)&&!empty($errors)) {
    echo json_encode(array("error"=>$errors));
}
?>

