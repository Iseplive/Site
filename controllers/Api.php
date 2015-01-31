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

}