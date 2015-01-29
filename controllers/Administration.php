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
			case "isepdor":
				$this->isepdorPage($param);
				break;
			default:
				$this->setView('index.php');
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
						chmod($avatarpath, 0777);
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
	public function isepdorPage($param){
			$this->setView('isepdor.php');
			
			$questions=$this->model->getquestions();
			for($i=0;$i<count($questions) ;$i++){
				$type=explode(',',$questions[$i]["type"]);
				$tab=array("students","associations","employees","events");
				$result=array_intersect($type,$tab);
				if(in_array("students",$result)){
					$questions[$i]["students"]=1;
				}
				else{
					$questions[$i]["students"]=0;
				}
				if(in_array("events",$result)){
					$questions[$i]["events"]=1;
				}
				else{
					$questions[$i]["events"]=0;
				}
				if(in_array("associations",$result)){
					$questions[$i]["associations"]=1;
				}
				else{
					$questions[$i]["associations"]=0;
				}
				if(in_array("employees",$result)){
					$questions[$i]["employees"]=1;
				}
				else{
					$questions[$i]["employees"]=0;
				}
				
				if($questions[$i]["extra"]==null){
					$questions[$i]["extra"]=" ";
				}
			}
			
			$events=$this->model->getevents();
			for($i=0;$i<count($events) ;$i++){
				if($events[$i]['extra']=="soiree"){
					$events[$i]['extra']=1;
				}
				else{
					$events[$i]['extra']=0;
				}
			}
			
			$myFile=DATA_DIR.Config::DIR_DATA_STORAGE.Config::DIR_DATA_ADMIN."/diplome.json";
			$file = fopen($myFile, 'r');
			$positions = fread($file,filesize($myFile));
			fclose($file);
			
			$this->addJSCode('
				Admin.loadjscssfile("'.Config::URL_STATIC.'js/jqx/jqxcore.js","js");
				Admin.loadjscssfile("'.Config::URL_STATIC.'js/jqx/jqxdata.js","js");
				Admin.loadjscssfile("'.Config::URL_STATIC.'js/jqx/jqxbuttons.js","js");
				Admin.loadjscssfile("'.Config::URL_STATIC.'js/jqx/jqxscrollbar.js","js");
				Admin.loadjscssfile("'.Config::URL_STATIC.'js/jqx/jqxmenu.js","js");
				Admin.loadjscssfile("'.Config::URL_STATIC.'js/jqx/jqxgrid.js","js");
				Admin.loadjscssfile("'.Config::URL_STATIC.'js/jqx/jqxgrid.edit.js","js");
				Admin.loadjscssfile("'.Config::URL_STATIC.'js/jqx/jqxgrid.selection.js","js");
				Admin.loadjscssfile("'.Config::URL_STATIC.'js/jqx/jqxgrid.sort.js","js");
				Admin.loadjscssfile("'.Config::URL_STATIC.'js/jqx/jqxgrid.filter.js","js");
				Admin.loadjscssfile("'.Config::URL_STATIC.'js/jqx/jqxgrid.columnsresize.js","js");
				Admin.loadjscssfile("'.Config::URL_STATIC.'js/jqx/jqxlistbox.js","js");
				Admin.loadjscssfile("'.Config::URL_STATIC.'js/jqx/jqxdropdownlist.js","js");
				Admin.loadjscssfile("'.Config::URL_STATIC.'js/jqx/jqxcheckbox.js","js");
				Admin.loadjscssfile("'.Config::URL_STATIC.'js/jqx/jqxcombobox.js","js");
				Admin.loadjscssfile("'.Config::URL_STATIC.'js/jqx/jqxgrid.pager.js","js");
				Admin.loadjscssfile("'.Config::URL_STATIC.'js/jqx/jqxdragdrop.js","js");
				Admin.loadjscssfile("'.Config::URL_STATIC.'js/jqx/jqxcalendar.js","js");
				Admin.loadjscssfile("'.Config::URL_STATIC.'js/jqx/jqxtooltip.js","js");				
				Admin.loadjscssfile("'.Config::URL_STATIC.'js/jqx/jqxdatetimeinput.js","js");
				Admin.loadjscssfile("'.Config::URL_STATIC.'js/jqx/jquery.global.js","js");
				Admin.loadjscssfile("'.Config::URL_STATIC.'js/jqx/jquery.glob.fr-FR.js","js");
				Admin.loadjscssfile("'.Config::URL_STATIC.'js/jqx/jqxtabs.js","js");
				
				Admin.loadjscssfile("'.Config::URL_STATIC.'js/crop/jquery.Jcrop.min.js","js");
				Admin.loadjscssfile("'.Config::URL_STATIC.'js/crop/jquery.color.js","js");
				
				jQuery(document).ready(function () {
					diplomeData=new Array();
					Admin.loadTab();
					Admin.loadCrop();
					Admin.loadCatGrid('.json_encode($questions).');
					Admin.loadEventGrid('.json_encode($events).');
					Admin.loadEmployGrid('.json_encode($this->model->getemployees()).');
					Admin.loadDate('.json_encode($this->model->getDate()).');
					jQuery(".jcrop-holder").ready(function () {
						Admin.loadDiplome('.$positions.');
					});
					jQuery("#adminIsepdorTab").removeClass("hidden");
				});
			');
			
			/* Code qui met à jour le questionnaire pour les ISEP D'or
			*
			*/
			if(isset($_POST['categories'])){
				$id=array();
				$post=json_decode($_POST['categories'],true);
				for($i=0;$i<count($post);$i++){
					if(is_numeric($post[$i]['id'])){
						array_push ($id,$post[$i]['id']);
					}
				}
				$toDelete=$this->model->checkIsepdorQuestions($id);
				if(count($toDelete)>0){
					for($i=0;$i<count($toDelete);$i++){
						$this->model->deleteQuestions($toDelete[$i]);
					}
				}
				for($i=0;$i<count($post);$i++){
					if($post[$i]['extra']==""){
						$post[$i]['extra']=NULL;
					}
					if($post[$i]['id']!=""){
						$this->model->updateisepdor($post[$i]['type'],$post[$i]['extra'],$post[$i]['questions'],$post[$i]['id'],$post[$i]['position']);
					}
					elseif($post[$i]['id']==""){
						$this->model->insertisepdor($post[$i]['type'],$post[$i]['extra'],$post[$i]['questions'],$post[$i]['position']);
					}
				}				
			}
			
			
			/*Code qui met à jour la table isepdor_employees
			*
			*/
			if(isset($_POST['employees'])){
				$id=array();
				$post=json_decode($_POST['employees'],true);
				for($i=0;$i<count($post);$i++){
					if(is_numeric($post[$i]['id'])){
						array_push ($id,$post[$i]['id']);
					}
				}
				$toDelete=$this->model->checkIsepdorEmployees($id);
				if(count($toDelete)>0){
					for($i=0;$i<count($toDelete);$i++){
						$this->model->deleteEmployees($toDelete[$i]);
					}
				}
				
				for($i=0;$i<count($post);$i++){
					$username=$this->makeusername($post[$i]['lastname'],$post[$i]['firstname']);
					if($post[$i]['id']!=""){
						$this->model->updateEmployees($post[$i]['lastname'],$post[$i]['firstname'],$post[$i]['id'],$username);
					}
					elseif($post[$i]['id']==""){
						$this->model->insertemployees($post[$i]['lastname'],$post[$i]['firstname'],$username);
					}
				}					
			}			

			/*Code qui met à jour la table isepdor_event
			*
			*/
			if(isset($_POST['events'])){
				$id=array();
				$post=json_decode($_POST['events'],true);
				for($i=0;$i<count($post);$i++){
					if(is_numeric($post[$i]['id'])){
						array_push ($id,$post[$i]['id']);
					}
				}
				$toDelete=$this->model->checkIsepdorEvents($id);
				if(count($toDelete)>0){
					for($i=0;$i<count($toDelete);$i++){
						$this->model->deleteEvents($toDelete[$i]);
					}
				}
				
				for($i=0;$i<count($post);$i++){
					if($post[$i]['extra']==1){
						$post[$i]['extra']="soiree";
					}
					else{
						$post[$i]['extra']=NULL;
					}
					if($post[$i]['id']!=""){
						$this->model->updateEvent($post[$i]['name'],$post[$i]['id'],$post[$i]['extra']);
					}
					elseif($post[$i]['id']==""){
						$this->model->insertEvent($post[$i]['name'],$post[$i]['extra']);
					}
				}					
			}
			
			/*Code qui met à jour les date de vote des isep d'or
			*
			*/
			if(isset($_POST['dates'])){
				$post=json_decode($_POST['dates'],true);
				$this->model->insertDate($post[0][0],$post[0][1],$post[1][0],$post[1][1],$post[2][0],$post[2][1]);
			}
			/*
			* Change l'image diplome
			*/
			if(isset($_FILES['diplome']) && !is_array($_FILES['diplome']['name'])){
				if($_FILES['diplome']['size'] > Config::UPLOAD_MAX_SIZE_PHOTO)
					throw new FormException('size');
				if($avatarpath = File::upload('diplome')){
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
						
						if($img->getHeight() !=794 || $img->getWidth() !=1122){
							throw new FormException('width');
						}
						$img->setType($type);
						$img->save($avatarpath);
						
						unset($img);
						if(isset($avatarpath) && File::exists($avatarpath)){
							$avatar_path = DATA_DIR.Config::DIR_DATA_STORAGE.Config::DIR_DATA_ADMIN."diplomeIsepDOr9652.png";
							$avatar_dir = File::getPath($avatar_path)."/diplomeIsepDOr9652.png";
							File::rename($avatarpath, $avatar_dir);
						}
					}catch(FormException $e){
						$this->set('form_error', $e->getError());
					}
					
					foreach($uploaded_files as $uploaded_file)
						File::delete($uploaded_file);
				}
				Post_Model::clearCache();
			}
			/*
			* Enregistre les coordonnées
			*/
			if(isset($_POST['diplomeData'])){
				$post=$_POST['diplomeData'];
				$file = fopen($myFile, 'w');
				fwrite($file,$post);
				fclose($file);
			}
			/*
			* Envoie les diplomes
			*/
			if(isset($_GET['getDiplome'])){
				$template=DATA_DIR.Config::DIR_DATA_STORAGE.Config::DIR_DATA_ADMIN."diplomeIsepDOr9652.png";
				$font = DATA_DIR.Config::DIR_DATA_STORAGE.Config::DIR_DATA_ADMIN."font2354.ttf";  
				$files=Array();
				$positions=json_decode($positions,true);//récupere les coordonnées précédament demandées
				for($i=0;$i<count($positions);$i++){
					$coord[$positions[$i]['index']]=$positions[$i];
				}
				$questions = IsepOr_Model::fetchQuestions();
				foreach ($questions as $value) {
					if (strpos($value['type'], ',')) {
						$data = array();
						foreach (explode(',', $value['type']) as $type) {
							$data = IsepOr_Controller::__array_rePad($data, IsepOr_Model::fetchFinals($value['id'], $type, 2));
						}
						$finalList[$value['id']] = array_slice(IsepOr_Controller::__array_orderby($data, 'cmpt', SORT_DESC), 0, 3);
					} else
						$finalList[$value['id']] = IsepOr_Model::fetchFinals($value['id'], $value['type'], 2);
				}
				for($i=0;$i<count($questions);$i++){
					for($j=0;$j<count($finalList[$questions[$i]['id']]);$j++){
						File::copy($template, DATA_DIR.Config::DIR_DATA_TMP."diplome".$i.$j.".png");
						chmod(DATA_DIR.Config::DIR_DATA_TMP."diplome".$i.$j.".png", 0777);
						array_push($files,DATA_DIR.Config::DIR_DATA_TMP."diplome".$i.$j.".png");
						$im = @imageCreateFromPng(DATA_DIR.Config::DIR_DATA_TMP."diplome".$i.$j.".png"); // Path Images 
						$color = ImageColorAllocate($im, 0, 0, 0); // Text Color 
						$champs[0]=$questions[$i]['questions'];
						$champs[1]=$finalList[$questions[$i]['id']][$j]["name"];
						$champs[2]="";
						if(!is_numeric($finalList[$questions[$i]['id']][$j]["valid"])){
							$champs[2]=$this->model->getBirthDay($finalList[$questions[$i]['id']][$j]["valid"]);
						}
						for($a=0;$a<3;$a++){
							$pxX = round($coord[$a]['x1']); // X  
							$pxY = round($coord[$a]['y2']); // Y  
							ImagettfText($im, round($coord[$a]['h']), 0, $pxX, $pxY, $color,$font, $champs[$a]);  
						}
						imagePng($im,DATA_DIR.Config::DIR_DATA_TMP."diplome".$i.$j.".png",9);  
						ImageDestroy($im); 
						if($finalList[$questions[$i]['id']][$j]['cmpt']!=$finalList[$questions[$i]['id']][$j+1]['cmpt']){
							break;
						}
					}
				}

				if(self::create_zip($files,DATA_DIR.Config::DIR_DATA_TMP."diplomesIsepDor.zip",true)){
					foreach($files as $file){
						File::delete($file);
					}
					header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
					header("Cache-Control: public"); // needed for i.e.
					header("Content-Type: application/zip");
					header("Content-Transfer-Encoding: Binary");
					header("Content-Length:".filesize(DATA_DIR.Config::DIR_DATA_TMP."diplomesIsepDor.zip"));
					header("Content-Disposition: attachment; filename=diplomesIsepDor.zip");
					readfile(DATA_DIR.Config::DIR_DATA_TMP."diplomesIsepDor.zip");
					File::delete(DATA_DIR.Config::DIR_DATA_TMP."diplomesIsepDor.zip");
					die();      
				} 	
				foreach($files as $file){
					File::delete($file);
				}
			}
			/*Code qui export les résultats des isep d'or
			*
			*/
				if(isset($_GET['export'])){
					$db=$this->model->getResult();

				 header('Content-Type: application/vnd.ms-excel');
				 header('Content-Disposition: filename='.'Résultats_Isepdor'.'.xls');
				 header('Pragma: no-cache');
				 header('Expires: 0');
				 
				 print '<table border=1 >
						<!-- impression des titres de colonnes -->
							<TR>
								<TD bgcolor="#3366CC">Tour</TD>
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
							print '<TD>'.$champs['round'].'</TD>';
							print '<TD>'.$champs['username'].'</TD>';
							print '<TD>'.utf8_decode($champs['questions']).'</TD>';
							print '<TD>'.$champs['student_username'].'</TD>';
							print '<TD>'.utf8_decode($champs['admin']).'</TD>';
							print '<TD>'.utf8_decode($champs['assoce']).'</TD>';
							print '<TD>'.utf8_decode($champs['name']).'</TD>';	
						print  '</TR>';
					}
						print '</table>';
						exit();
					
				}
			/*
			* Ajout de la police
			*/
			if(isset($_FILES['font']) && $_FILES['font']['name']!=null ){
				if($_FILES['font']['size'] > Config::UPLOAD_MAX_SIZE_FILE)
						throw new Exception(__('POST_ADD_ERROR_FILE_SIZE', array('size' => File::humanReadableSize(Config::UPLOAD_MAX_SIZE_FILE))));
				 
				if($filepaths = File::upload('font')){
					if(!preg_match('#\.ttf$#i', $filepaths))
						throw new Exception(__('POST_ADD_ERROR_FILE_FORMAT'));
						
					$avatar_path = DATA_DIR.Config::DIR_DATA_STORAGE.Config::DIR_DATA_ADMIN."font2354.ttf";
					$avatar_dir = File::getPath($avatar_path)."/font2354.ttf";
					File::rename($filepaths, $avatar_dir);
				}
				else{											
					throw new Exception(__('ADMIN_UPLOAD_ERROR'));
				}
			}
			/*Code qui met supprime les champs de la table résultat des isep d'or
			*
			*/
			if(isset($_GET['delete_result'])){
				$this->model->deleteresult();
				header("Location: ".Config::URL_ROOT.Routes::getPage('admin',array("nav"=> "isepdor")));
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
			$this->model->deleteQuestions($params['id']);
		}
		if($params['type']==2){
			$this->model->deleteemployees($params['id']);
		}
		if($params['type']==3){
			$this->model->deleteevent($params['id']);
		}
	}
	public function create_zip($files = array(),$destination = '',$overwrite = false) {
	  //if the zip file already exists and overwrite is false, return false
	  if(file_exists($destination) && !$overwrite) { return false; }
	  //vars
	  $valid_files = array();
		  //if files were passed in...
		  if(is_array($files)) {
			//cycle through each file
			foreach($files as $file) {
			  //make sure the file exists
			  if(file_exists($file)) {
				$valid_files[] = $file;
			  }
			}
		  }
	  //if we have good files...
		if(count($valid_files)) {
			//create the archive
			$zip = new ZipArchive();
			if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
			  return false;
			}
			//add the files
			foreach($valid_files as $file) {
			  $zip->addFile($file,basename($file));
			}
			//debug
			//echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
			
			//close the zip -- done!
			$zip->close();
			
			//check to make sure the file exists
			return file_exists($destination);
		}else{
			return false;
		}
	}

	
}