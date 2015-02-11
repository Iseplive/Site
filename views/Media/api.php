<?php
if (isset($mediaannee)) {
    echo json_encode($mediaannee);
}
if (isset($errors)&&!empty($errors)) {
echo json_encode(array("error"=>$errors));
}