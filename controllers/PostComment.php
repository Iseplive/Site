<?php

class PostComment_Controller extends Controller {
	
	/**
	 * Add a comment to a post
	 */
	public function add($params){
		$this->setView('add.php');
		
		if(!isset(User_Model::$auth_data))
			throw new Exception('You must be logged in');
		if(!isset(User_Model::$auth_data['student_number']))
			throw new Exception('You must be a student to post a comment');
		
		$message = isset($_POST['message']) ? trim($_POST['message']) : '';
		if($message == '')
			throw new Exception('You must write a message');
		
		$attachment_id = isset($_POST['attachment']) && ctype_digit($_POST['attachment']) ? (int) $_POST['attachment'] : null;
		
		try {
			$id = $this->model->add($params['post_id'], (int) User_Model::$auth_data['id'], $message, $attachment_id);
			$this->set(array(
				'id'			=> $id,
				'username'		=> User_Model::$auth_data['username'],
				'firstname'		=> User_Model::$auth_data['firstname'],
				'lastname'		=> User_Model::$auth_data['lastname'],
				'avatar_url'            => User_Model::$auth_data['avatar_url'],
				'message'		=> $message,
				'attachment_id'         => $attachment_id,
                                'post_id'               => (int) $params['post_id']
			));
		}catch(Exception $e){
			echo $e->getMessage();
		}
	}

    public function addApi($params){
        $this->setView('baseApi.php');
        $errors = "";
        $connected = "false";
        if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['post_id']) &&isset($_POST['message'])) {
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

                    $message = isset($_POST['message']) ? trim($_POST['message']) : '';

                    $id = $this->model->add(trim($_POST['post_id']), (int) User_Model::$auth_data['id'], $message, null);
                    $this->set(array(
                        'id'			=> $id,
                        'username'		=> User_Model::$auth_data['username'],
                        'firstname'		=> User_Model::$auth_data['firstname'],
                        'lastname'		=> User_Model::$auth_data['lastname'],
                        'avatar_url'            => User_Model::$auth_data['avatar_url'],
                        'message'		=> $message,
                        'attachment_id'         => null,
                        'post_id'               => (int) trim($_POST['post_id'])
                    ));

                    $errors = 'success';
                }else{
                    $errors = 'Bad username or password';
                }

            }catch(Exception $e){
                $errors = 'An exception occurred while logging in';
            }
        } else {
            $errors = 'Please enter an username and a password';
        }

        echo json_encode(array("result" => $errors));
    }
	
	
	/**
	 * Delete a post
	 */
	public function delete($params){
		$this->setView('delete.php');
		
		try {
			$comment = $this->model->get((int) $params['id']);
			
			$is_logged = isset(User_Model::$auth_data);
			$is_admin = $is_logged && User_Model::$auth_data['admin']=='1';
			$groups_auth = isset($is_logged) ? Group_Model::getAuth() : array();
			
			if(($is_logged && User_Model::$auth_data['id'] == $comment['user_id'])
			|| $is_admin
			|| (isset($post['group_id']) && isset($groups_auth[(int) $post['group_id']])) && $groups_auth[(int) $post['group_id']]['admin']){
				
				$this->model->delete((int) $params['id']);
				$this->set('success', true);
				
			}else{
				$this->set('success', false);
			}
		}catch(Exception $e){
			// Post not found
			$this->set('success', true);
		}
	}
	
	
}
