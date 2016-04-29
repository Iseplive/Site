<?php

if (isset($errors)&&!empty($errors)) {
echo json_encode(array("error"=>$errors));
}
?>
<?php
$arr = array();
for($i=0;$i<count($annee);$i++){

    for($a=0;$a<=count($mediaannee[''.$annee[$i].'']);$a++){
        $arrA = array();
        if(isset($mediaannee[''.$annee[$i].''][$a]) ){
            $id=$mediaannee[''.$annee[$i].''][$a];
            if(isset($mediamessage[$id]) && isset($categorie[$id])){
                $route=Config::URL_ROOT . Routes::getPage('post', array('id' => $id));
                $title=str_split($mediamessage[$id],35);
                $category=$categorie[$id];
                if(isset($title[1]) && $title[1]!=""){ $etc="...";}
                else{ $etc="";}
                $spanline=round(count($mediaannee[''.$annee[$i].''])/3);
                $arrA["category_id"] = $category;
                $arrA["id"] = $id;
                $arrA["title"] = count($title[0])>0 ? $title[0]: "Surprise";
            }
        }

        if (!isset($arr[$annee[$i]])) {
            $arr[$annee[$i]] = array();
        }
        if (count($arrA)>0) {
            $arr[$annee[$i]][] = $arrA;
        }
    }

    ?>

<?php
}
echo json_encode($arr);
?>