<?php

class Api_Controller extends Controller {

    public function login() {
        $this->setView('baseApi.php');
        $errors = "";
        $connected = "false";
        if(isset($_POST['username']) && isset($_POST['password'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $mcrypt = new MCrypt();
            $password = $mcrypt->decrypt($password);

            $user_model = new User_Model();
            try {
                if(!preg_match('#^[a-z0-9-]+$#', $username))
                    $errors = 'Invalid username';
                if($user_model->authenticate($username, $password)){
                    $connected = "true";
                }else{
                    $errors = 'Bad username or password';
                }

            }catch(Exception $e){
                $errors = 'An exception occurred while logging in';
            }
        } else {
            $errors = 'Please enter an username and a password';
        }

        echo json_encode(array('errors' => $errors, 'connected' => $connected));
    }

    public function testGCM($params) {
        $this->setView('sendTest.php');

        $deviceModel = new Devices_Model();

        print_r($devices = $deviceModel->listRegisredDevices());
        if (isset($_POST["message"])) {
            $apiKey = "AIzaSyBfcJCOBIwjY-7Mnzoh3hPTRurD7_2CgsE";

            $devices = $deviceModel->listRegisredDevices();

            $gcpm = new GCMPushMessage($apiKey);
            $gcpm->setDevices($devices);
            $response = $gcpm->send($_POST["message"], array('title' => $_POST["title"]));
            echo "sent !";
        }

    }

    public function register($params) {
        $this->setView('baseApi.php');

        $deviceModel = new Devices_Model();
        $result = "error";

        if (isset($_POST["regid"])) {
            if (!$deviceModel->exist($_POST["regid"])) {
                $deviceModel->register($_POST["regid"]);
                $result = "ok";
            } else {
                $result = "ok";
                $alreadyExist = true;
            }
        }

        $arr = array("result"=> $result);
        if (isset($alreadyExist)&&$alreadyExist) {
            $arr['alreadyexist'] = "true";
        }

        echo json_encode($arr);
    }

}