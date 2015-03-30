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
            $infos = array();
            try {
                if(!preg_match('#^[a-z0-9-]+$#', $username))
                    $errors = 'Invalid username';
                if($user_model->authenticate($username, $password)){
                    $connected = "true";
                    $infos = array(
                        'firstname'		=> User_Model::$auth_data['firstname'],
                        'lastname'		=> User_Model::$auth_data['lastname'],
                        'avatar_url'	=> User_Model::$auth_data['avatar_url'],
                        'number'        => User_Model::$auth_data['student_number']
                    );
                }else{
                    $errors = 'Bad username or password';
                }

            }catch(Exception $e){
                $errors = 'An exception occurred while logging in';
            }
        } else {
            $errors = 'Please enter an username and a password';
        }

        $infos['errors'] = $errors;
        $infos['connected'] = $connected;
        echo json_encode($infos);
    }

    public function testGCM($params) {
        $this->setView('sendTest.php');

        $deviceModel = new Devices_Model();
        $res = Group_Model::getInfoByIds(array(1));
        print_r($res[0]["name"]);

        if (isset($_POST["message"])) {
            $apiKey = "AIzaSyBfcJCOBIwjY-7Mnzoh3hPTRurD7_2CgsE";

            $devices = $deviceModel->listRegisredDevices();
            $realDevices = array();

            foreach ($devices as $device) {
                $realDevices[] = $device["registerid"];
            }


            print_r($realDevices);
            echo "<br>";

            $gcpm = new GCMPushMessage($apiKey);
            $gcpm->setDevices($realDevices);
            $response = $gcpm->send($_POST["message"], array('title' => $_POST["title"]));
            print_r($response);
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