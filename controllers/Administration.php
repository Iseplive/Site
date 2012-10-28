<?php

class Administration_Controller extends Controller {
	public function index($param){
		$is_logged = isset(User_Model::$auth_data);
		$is_student = $is_logged && isset(User_Model::$auth_data['student_number']);
		$is_admin = $is_logged && User_Model::$auth_data['admin']=='1';
		
		if(!$is_logged)
			throw new ActionException('User', 'signin', array('redirect' => $_SERVER['REQUEST_URI']));
		if(!$is_admin)
			throw new ActionException('Page', 'error404');
		
		//revérifie le compte admin
		/*$user_model = new User_Model();
		if(isset($_POST['reconfpassword']) ){
			$username = User_Model::$auth_data['username'];
			$password = $_POST['reconfpassword'];
			try {
				if(!preg_match('#^[a-z0-9-]+$#', $username))
					throw new Exception('Invalid username');
				if($user_model->authenticate($username, $password)){
					User_Model::$auth_status = User_Model::AUTH_STATUS_LOGGED;
					// Write session and cookie to remember sign-in
					Cookie::write('login', Encryption::encode($username.':'.$password), 60*24*3600);
					Session::write('username', $username);
					
				}else{
					throw new Exception('Bad username or password');
				}
				
			}catch(Exception $e){
				User_Model::$auth_status = User_Model::AUTH_STATUS_BAD_USERNAME_OR_PASSWORD;
				Cookie::delete('login');
				Session::delete('username');
				throw new ActionException('User', 'signin', array('redirect' => $_SERVER['REQUEST_URI']));
			}
		}
		if(($auth_admin = Cache::read('auth_admin')) && $is_admin ){
			$this->setView('reconfirm.php');
			$this->set(array(
							'user'=>User_Model::$auth_data['firstname']." ".User_Model::$auth_data['lastname'],
							'url'=>$param['nav']
			));
			return;
		}
		else{
			throw new ActionException('User', 'signin', array('redirect' => $_SERVER['REQUEST_URI']));
		}*/
		
		switch($param['nav']){
			case "admins":
				$this->adminPage($param);
				break;
			case "users":
				$this->usersPage($param);
				break;
			case "bde":
				$this->bdePage($param);
				break;
			default:
				$this->setView('index.php');
		}
		
		$last_promo = ((int) date('Y')) + 5;
		if((int) date('m') < 9){
			$last_promo -= 1;
		}
		
		switch($param['nav']){
			case "admins":
				$s=0;//fallait mettre qqch
				break;
			default:
				$this->set(array(
					'username'			=> User_Model::$auth_data['username'],
					'is_logged'			=> $is_logged,
					'is_student'		=> $is_student,
					'is_admin'			=> $is_admin,
					'questions'			=> $this->model->getquestions(),
					'employees'			=> $this->model->getemployees(),
					'events'			=> $this->model->getevents(),
					'promo'				=> $last_promo,
					'date'				=> $this->model->getdate(),
					'admins'			=> $this->model->getadmin()
				));
		}
				
		/**/
		switch($param['nav']){
			case "admins":
				$s=0;
				break;
			
			default:
				$this->addJSCode('Admin.init();');
			
				if(isset($param['nav']) && $param['nav']==1){
					$this->addJSCode('Admin.navAdminChange(3);');
				}
				if(isset($param['nav']) && $param['nav']==2){
					$this->addJSCode('Admin.navAdminChange(3);Admin.isepdornav(2);');
				}
				if(isset($param['nav']) && $param['nav']==3){
					$this->addJSCode('Admin.navAdminChange(3);Admin.isepdornav(3);');
				}
				if(isset($param['nav']) && $param['nav']==4){
					$this->addJSCode('Admin.navAdminChange(3);Admin.isepdornav(4);');
				}
				if(isset($param['nav']) && $param['nav']==5){
					$this->addJSCode('Admin.navAdminChange(5);');
				}
				
		}
		
			
			/* Code qui met à jour le questionnaire pour les ISEP D'or
			*
			*/
			if(isset($_POST['nbquestion'])){
				for($i=0;$i<$_POST['nbquestion'];$i++){
					$type="";
					if(isset($_POST['students'.$i])){
						$type.="students";
					}
					if(isset($_POST['events'.$i])){
						if($type==""){
							$type.="events";
						}
						else{
							$type.=",events";
						}
					
					}
					if(isset($_POST['associations'.$i])){
						if($type==""){
							$type.="associations";
						}
						else{
							$type.=",associations";
						}
					
					}
					if(isset($_POST['employees'.$i])){
						if($type==""){
							$type.="employees";
						}
						else{
							$type.=",employees";
						}
					}
					if( isset($_POST['quest'.$i]) && isset($_POST['id'.$i])){
						$this->model->updateisepdor($type,$_POST['extra'.$i],$_POST['quest'.$i],$_POST['id'.$i],$_POST['position'.$i]);
					}
					elseif(isset($_POST['quest'.$i])){
						$this->model->insertisepdor($type,$_POST['extra'.$i],$_POST['quest'.$i],$_POST['position'.$i]);
					}
				}
				header('Location: '.Config::URL_ROOT.Routes::getPage("admin",array("nav"=> 1)));
			}
			
			/*Code qui met à jour la table isepdor_employees
			*
			*/
			if(isset($_POST['nbchamps'])){
				for($i=0;$i<$_POST['nbchamps'];$i++){
					if(isset($_POST['lastname'.$i]) && isset($_POST['id'.$i])){
						$username=$this->makeusername($_POST['lastname'.$i],$_POST['firstname'.$i]);
						$this->model->updateemployees($_POST['lastname'.$i],$_POST['firstname'.$i],$_POST['id'.$i],$username);
					}
					elseif(isset($_POST['lastname'.$i])){
						$username=$this->makeusername($_POST['lastname'.$i],$_POST['firstname'.$i]);
						$this->model->insertemployees($_POST['lastname'.$i],$_POST['firstname'.$i],$username);
					}
				}
				header('Location: '.Config::URL_ROOT.Routes::getPage("admin",array("nav"=> 3)));
			}	

			/*Code qui met à jour la table isepdor_event
			*
			*/
			if(isset($_POST['nbevent'])){
				for($i=0;$i<$_POST['nbevent'];$i++){
					$soiree="";
					if(isset($_POST['soiree'.$i])){
						$soiree="soiree";
					}
					if(isset($_POST['id'.$i]) && isset($_POST['name'.$i])){
						$this->model->updateevent($_POST['name'.$i],$_POST['id'.$i],$soiree);
					}
					elseif(isset($_POST['name'.$i])){
						$this->model->insertevent($_POST['name'.$i],$soiree);
					}
				
				}
				header('Location: '.Config::URL_ROOT.Routes::getPage("admin",array("nav"=> 2)));
			}
			
			/*Code qui met à jour les date de vote des isep d'or
			*
			*/
			if(isset($_POST['first1']) && isset($_POST['first2']) && isset($_POST['second1']) && isset($_POST['second1'])){
				$this->model->insertdate($_POST['first1'],$_POST['first2'],$_POST['second1'],$_POST['second2']);
				header('Location: '.Config::URL_ROOT.Routes::getPage("admin",array("nav"=> 4)));
			}
			
			/*Code qui export les résultats des isep d'or
			*
			*/
				if(isset($_GET['export'])){
					if($_GET['type']==1){
						$db=$this->model->getresult1();
						
					}
					if($_GET['type']==2){
						$db=$this->model->getresult2();
					}
				 header('Content-Type: application/vnd.ms-excel');
				 header('Content-Disposition: filename='.'Résultats_Isepdor'.'.xls');
				 header('Pragma: no-cache');
				 header('Expires: 0');
				 
				 print '<table border=1 >
						<!-- impression des titres de colonnes -->
							<TR>
								<TD bgcolor="#3366CC">Nom du votant</TD>
								<TD bgcolor="#3366CC">Catégorie</TD>
								<TD bgcolor="#3366CC">Réponse(student)</TD>
								<TD bgcolor="#3366CC">Réponse(admin)</TD>
								<TD bgcolor="#3366CC">Réponse(assoce)</TD>
								<TD bgcolor="#3366CC">Réponse(event)</TD>						
							</TR>
							';
					foreach ($db as $champs){
						print '<TR>';
							print '<TD>'.$champs['username'].'</TD>';
							print '<TD>'.$champs['questions'].'</TD>';
							print '<TD>'.$champs['student_username'].'</TD>';
							print '<TD>'.$champs['admin'].'</TD>';
							print '<TD>'.$champs['assoce'].'</TD>';
							print '<TD>'.$champs['name'].'</TD>';	
						print  '</TR>';
					}
						print '</table>';
						exit();
				}
				
			/*Code qui met supprime les champs de la table résultat des isep d'or
			*
			*/
			if(isset($_GET['delete_result'])){
				$this->model->deleteresult();
			}
			
				
			
		
	}
	
	public function adminPage($param){
		$this->setView('admins.php');

		/* Permet de supprimer un admin */
		if(isset($_GET['del']) && $_GET["del"]!=""){
			$this->model->deleteadmin($_GET['del']);
			header('Location: '.Config::URL_ROOT.Routes::getPage("admin",array("nav"=> "admins")));
		}
		/* Permet d'ajouter un admin	*/
		if(isset($_POST['valid-students']) && $_POST["valid-students"]!=""){
			$this->model->addadmin($_POST['valid-students']);
			header('Location: '.Config::URL_ROOT.Routes::getPage("admin",array("nav"=> "admins")));
		}
			
		$this->set(array(
			'admins'			=> $this->model->getadmin()
		));
		
		$this->addJSCode('Admin.adminsInit();');
	}
	
	public function usersPage($param){
		$this->setView('users.php');
		
		/*
		* Enregistrement du post dans la table users	
		*/
		if(isset($_FILES['uploadxml1']) && $_FILES['uploadxml1']['name']!=null ){
			if($_FILES['uploadxml1']['size'] > Config::UPLOAD_MAX_SIZE_FILE)
					throw new Exception(__('POST_ADD_ERROR_FILE_SIZE', array('size' => File::humanReadableSize(Config::UPLOAD_MAX_SIZE_FILE))));
			 
			//On déplace le fichier vers le serveur
			if($filepaths = File::upload('uploadxml1')){
					if(!preg_match('#\.xml$#', $filepaths))
						throw new Exception(__('POST_ADD_ERROR_FILE_FORMAT'));
					$name = $filepaths;
			}
			$student=array();
			$path=DATA_DIR.Config::DIR_DATA_TMP.$_FILES['uploadxml1']['name'];
			if (file_exists($path)){
				$data = simplexml_load_file($path); 
				foreach ($data->data as $data) {  
					if(isset($data->username) && isset($data->admin) && isset($data->mail) && isset($data->msn) && isset($data->jabber) && isset($data->address) 
						&& isset($data->address) && isset($data->zipcode) && isset($data->city) && isset($data->cellphone) &&isset($data->phone) &&isset($data->birthday)){
						$username=$data->username;
						$admin=$data->admin;
						$mail=$data->mail;
						$msn=$data->msn;
						$jabber=$data->jabber;
						$address=$data->address;
						$zipcode=$data->zipcode;
						$city=$data->city;
						$cellphone=$data->cellphone;
						$phone=$data->phone;
						$birthday=$data->birthday;
						
						if(!$this->model->checkuser($username,1)){
							$this->model->insertUsers($username,$admin,utf8_decode($mail),utf8_decode($msn),utf8_decode($jabber),utf8_decode($address),$zipcode,$city,$cellphone,$phone,$birthday);	
						}
						else{
							array_push($student,$username);
						}
					}
					else{
						throw new Exception(__('ADMIN_UPLOAD_ERROR2'));
					}
				}				
			}
			else{											
				throw new Exception(__('ADMIN_UPLOAD_ERROR'));
			}
			FILE::delete($path);
			$this->set('fail', $student);
		}
		
		/*
		* Enregistrement du post dans la table students
		*/
		if(isset($_FILES['uploadxml2']) && $_FILES['uploadxml2']['name']!=null ){
			if($_FILES['uploadxml2']['size'] > Config::UPLOAD_MAX_SIZE_FILE)
					throw new Exception(__('POST_ADD_ERROR_FILE_SIZE', array('size' => File::humanReadableSize(Config::UPLOAD_MAX_SIZE_FILE))));
			 
			if($filepaths = File::upload('uploadxml2')){
					if(!preg_match('#\.xml$#', $filepaths))
						throw new Exception(__('POST_ADD_ERROR_FILE_FORMAT'));
					$name = $filepaths;
			}
			$student=array();
			$path=DATA_DIR.Config::DIR_DATA_TMP.$_FILES['uploadxml2']['name'];
			if (file_exists($path)){
				$data = simplexml_load_file($path); 
				foreach ($data->data as $data) {  
					if(isset($data->username) && isset($data->lastname) && isset($data->firstname) && isset($data->student_number) && isset($data->promo) && isset($data->cesure)){
						$username=$data->username;
						$lastname=$data->lastname;
						$firstname=$data->firstname;
						$student_number=$data->student_number;
						$promo=$data->promo;
						$cesure=$data->cesure;
						
						if(!$this->model->checkuser($username,2)){
							$this->model->insertStudents($username,utf8_decode($lastname),utf8_decode($firstname),$student_number,$promo,$cesure);	
						}
						else{
							array_push($student,$username);
						}
					}
					else{
						throw new Exception(__('ADMIN_UPLOAD_ERROR2'));
					}
				}				
			}
			else{											
				throw new Exception(__('ADMIN_UPLOAD_ERROR'));
			}
			FILE::delete($path);
			
			$this->set('fail', $student);
		}
		/*
		* Enregistrement des avatars
		*/
		if(isset($_FILES['avatar_photo']) && is_array($_FILES['avatar_photo']['name'])){
			foreach($_FILES['avatar_photo']['size'] as $size){
				if($size > Config::UPLOAD_MAX_SIZE_PHOTO)
					throw new Exception(__('POST_ADD_ERROR_PHOTO_SIZE', array('size' => File::humanReadableSize(Config::UPLOAD_MAX_SIZE_PHOTO))));
			}
			$student=array();
			if($avatarpaths = File::upload('avatar_photo')){
				foreach($avatarpaths as $avatarpath)
					$uploaded_files[] = $avatarpath;
				foreach($avatarpaths as $i => $avatarpath){
					$name = $_FILES['avatar_photo']['name'][$i];
					try {
						$img = new Image();
						$img->load($avatarpath);
						$type = $img->getType();
						if($type==IMAGETYPE_JPEG)
							$ext = 'jpg';
						else if($type==IMAGETYPE_GIF)
							$ext = 'gif';
						else if($type==IMAGETYPE_PNG)
							$ext = 'png';
						else
							throw new Exception();
						
						if($img->getWidth() > 800)
							$img->setWidth(800, true);
						$img->setType(IMAGETYPE_JPEG);
						$img->save($avatarpath);
						
						// Thumb
						$avatarthumbpath = $avatarpath.'.thumb';
						$img->thumb(Config::$AVATARS_THUMBS_SIZES[0], Config::$AVATARS_THUMBS_SIZES[1]);
						$img->setType(IMAGETYPE_JPEG);
						$img->save($avatarthumbpath);
						
						unset($img);
						$uploaded_files[] = $avatarthumbpath;
						
						$student_data['avatar_path'] = $avatarthumbpath;
						$student_data['avatar_big_path'] = $avatarpath;
						$student_data['student_number']=preg_replace( '/\.[a-z0-9]+$/i' , '' , $name );
						if(isset($student_data['avatar_path']) && isset($student_data['student_number']) && File::exists($student_data['avatar_path'])){
							$avatar_path = Student_Model::getAvatarPath((int) $student_data['student_number'], true);
							$avatar_dir = File::getPath($avatar_path);
							if(!is_dir($avatar_dir))
								File::makeDir($avatar_dir, 0777, true);
							File::rename($student_data['avatar_path'], $avatar_path);
						}
						if(isset($student_data['avatar_big_path']) && isset($student_data['student_number']) && File::exists($student_data['avatar_big_path'])){
							$avatar_path = Student_Model::getAvatarPath((int) $student_data['student_number'], false);
							$avatar_dir = File::getPath($avatar_path);
							if(!is_dir($avatar_dir))
								File::makeDir($avatar_dir, 0777, true);
							File::rename($student_data['avatar_big_path'], $avatar_path);
						}
						
					}catch(Exception $e){
						array_push($student,$name);
					}	
				}
				$this->set('fail2', $student);
				foreach($uploaded_files as $uploaded_file)
					File::delete($uploaded_file);
			}
		}
	}
	
	public function bdePage($param){
		$this->setView('bde.php');
		
		if(isset($_FILES['logo']) && !is_array($_FILES['logo']['name'])){
			if($_FILES['logo']['size'] > Config::UPLOAD_MAX_SIZE_PHOTO)
				throw new FormException('logo');
			if($avatarpath = File::upload('logo')){
				$uploaded_files[] = $avatarpath;
				try {
					$img = new Image();
					$img->load($avatarpath);
					$type = $img->getType();
					if($type==IMAGETYPE_JPEG)
						$ext = 'jpg';
					else if($type==IMAGETYPE_GIF)
						$ext = 'gif';
					else if($type==IMAGETYPE_PNG)
						$ext = 'png';
					else
						throw new Exception();
					
					if($img->getHeight() > 80)
						$img->setHeight(80, true);	
						
					$img->setType($type);
					$img->save($avatarpath);
					
					unset($img);
					if(isset($avatarpath) && File::exists($avatarpath)){
						$avatar_path = APP_DIR.Config::DIR_APP_STATIC."images/header/logo_bde.png";
						$avatar_dir = File::getPath($avatar_path)."/logo_bde.png";
						File::rename($avatarpath, $avatar_dir);
					}
				}catch(Exception $e){
					throw new FormException('avatar');
				}
				
				foreach($uploaded_files as $uploaded_file)
					File::delete($uploaded_file);
			}
			Post_Model::clearCache();
		}
	}
	
	//Fonction qui formate nom et prénom pou en faire un username
	public function makeusername($last,$first){
		$name1=str_split(strtolower($last),7);
		$name2=str_split(strtolower($first),1);
		return $name2[0].$name1[0];
	}
	
	//Fonction qui supprime des champs des DB isepdor event, employees,questions
	public function delete($params){
		if($params['type']==1){
			$this->model->deletequestions($params['id']);
		}
		if($params['type']==2){
			$this->model->deleteemployees($params['id']);
		}
		if($params['type']==3){
			$this->model->deleteevent($params['id']);
		}
	}
	// Function qui exporte les résultats des isep d'or
	public function exportDB($params){
		if($params['type']==1){
			$db=$this->model->getresult1();
			
		}
		if($params['type']==2){
			$db=$this->model->getresult2();
		}
		 header('Content-Type: application/vnd.ms-excel');
		 header('Content-Disposition: attachment; filename='.'Résultats_Isepdor'.'.xls');
		 header('Pragma: no-cache');
		 header('Expires: 0');
		 
		 print '<table border=1 >
				<!-- impression des titres de colonnes -->
					<TR>';
					// while($nomcol=mysql_fetch_field($db)){
						// print '<TD bgcolor="#3366CC">'. $nomcol->name .'</TD>';
					// }	
			print  '</TR>
					<TR>';
			foreach ($db as $champs){
				print '<TD>'.$champs['voting_user_id'].'</TD>';
				print '<TD>'.$champs['isepdor_questions_id'].'</TD>';
				print '<TD>'.$champs['student_username'].'</TD>';
				print '<TD>'.$champs['isepdor_employees_id'].'</TD>';
				print '<TD>'.$champs['isepdor_associations_id'].'</TD>';
				print '<TD>'.$champs['isepdor_event_id'].'</TD>';	
			}
			print  '</TR>
				</table>';
				// return $db;
		
			
	}

	
}