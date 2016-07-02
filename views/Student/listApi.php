<?php
if (isset($students)) {
    echo json_encode($students);
}
if (isset($errors)&&!empty($errors)) {
    echo json_encode(array("error"=>$errors));
}
?>