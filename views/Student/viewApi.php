<?php
if (isset($student)) {
    echo json_encode($student);
}
if (isset($errors)&&!empty($errors)) {
    echo json_encode(array("error"=>$errors));
}
?>