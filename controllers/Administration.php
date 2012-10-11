<?php

class Administration_Controller extends Controller {
	public function index($param){
		switch($param['nav']){
			case "admins":
				$this->adminPage($param);
				break;
			
			default:
				$this->setView('index.php');
		}
		
		$is_logged = isset(User_Model::$auth_data);
		$is_student = $is_logged && isset(User_Model::$auth_data['student_number']);
		$is_admin = $is_logged && User_Model::$auth_data['admin']=='1';
		
		if(!$is_logged)
			throw new ActionException('User', 'signin', array('redirect' => $_SERVER['REQUEST_URI']));
		if(!$is_admin)
			throw new ActionException('Page', 'error404');
		
		$last_promo = ((int) date('Y')) + 5;
		if((int) date('m') < 9){
			$last_promo -= 1;
		}
		
		switch($param['nav']){
			case "admins":
				$s=0;
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
		
	
		/*Code qui met à jour l'annuaire dans mysql et ajoute les avatars
		*
		*/
			if(isset($_FILES['uploadzip']) && $_FILES['uploadzip']['name']!=null ){
				if($_FILES['uploadzip']['size'] > Config::UPLOAD_MAX_SIZE_FILE)
						throw new Exception(__('POST_ADD_ERROR_FILE_SIZE', array('size' => File::humanReadableSize(Config::UPLOAD_MAX_SIZE_FILE))));
				 
				//On déplace le fichier zipper vers le serveur
				if($filepaths = File::upload('uploadzip')){
						if(!preg_match('#\.zip$#', $filepaths))
							throw new Exception(__('POST_ADD_ERROR_FILE_FORMAT'));
						$name = $filepaths;
				}
				$path=DATA_DIR.Config::DIR_DATA_TMP.'annuaire/';
				// On dézip celui-ci
				if(FILE::exists($path)){
					FILE::delete($path);
				}
				File::makeDir($path);
				$zip = new ZipArchive;
				 $res = $zip->open($name);
				 if ($res === TRUE) {
					$zip->extractTo($path);
					$zip->close();
					unlink($name);
				 }
				 else{
					throw new Exception(__('ADMIN_POST_ZIPERROR'));
				}
				if(File::delete(DATA_DIR.Config::DIR_DATA_TMP.$name)){
					// On aplique le chmod a tous les dossiers et fichiers du zip
						FILE::chmodDirectory($path,0);
					// on traite les fichiers students.csv et users.csv
													
						if (file_exists($path.'users.csv')){
							$fp = fopen($path.'users.csv', "r"); 
						}
						else{											
							throw new Exception(__('ADMIN_POST_CSVERROR1'));
						}

						$i=0;
							 while (!feof($fp)) {
									$i = $i+1;
										// Tant qu'on n'atteint pas la fin du fichier 
										$ligne = fgets($fp,4096); /* On lit une ligne */
										// On récupère les champs séparés par ; dans liste
										$liste = explode( ";",$ligne);   
										// On assigne les variables
										if(strlen($liste[0])>1){
											if (isset($liste[0])){ 
												$username = $liste[0];
											}
											if (isset($liste[1])){ 
												$admin = $liste[1];
											}
											 if (isset($liste[2])){ 
												$mail = $liste[2];
											}
											if (isset($liste[3])){ 
												$msn = $liste[3];
											}
											if (isset($liste[4])){ 
												$jabber = $liste[4];
											}
											if (isset($liste[5])){ 
												$address = $liste[5];
											}
											if (isset($liste[6])){ 
												$zipcode = $liste[6];
											}
											if (isset($liste[7])){ 
												$city = $liste[7];
											}
											if (isset($liste[8])){ 
												$cellphone = $liste[8];
											}
											if (isset($liste[9])){ 
												$phone = $liste[9];
											}
											if (isset($liste[10])){ 
												$birthday = $liste[10];
											}
											
											if(!$this->model->checkuser($username,1)){
												$this->model->insertUsers(trim($username),trim($admin),trim($mail),trim($msn),trim($jabber),trim($address),trim($zipcode),trim($city),trim($cellphone),trim($phone),trim($birthday));	
											}
											
										}							
								}
								fclose($fp);
								
						if (file_exists($path.'students.csv')){
							$fp = fopen($path.'students.csv', "r"); 
						}
						else{											
							throw new Exception(__('ADMIN_POST_CSVERROR2'));
						}

						$i=0;
							 while (!feof($fp)) {
									$i = $i+1;
										// Tant qu'on n'atteint pas la fin du fichier 
										$ligne = fgets($fp,4096); /* On lit une ligne */
										// On récupère les champs séparés par ; dans liste
										$liste = explode( ";",$ligne);   
										// On assigne les variables
										if(strlen($liste[0])>1){
											if (isset($liste[0])){ 
												$username = $liste[0];
											}
											if (isset($liste[1])){ 
												$lastname = $liste[1];
											}
											 if (isset($liste[2])){ 
												$firstname = $liste[2];
											}
											if (isset($liste[3])){ 
												$student_number = $liste[3];
											}
											if (isset($liste[4])){ 
												$promo = $liste[4];
											}
											if (isset($liste[5])){ 
												$cesure = $liste[5];
											}
											
											if(!$this->model->checkuser($username,2)){
												$this->model->insertStudents(trim($username),trim($lastname),trim($firstname),trim($student_number),trim($promo),trim($cesure));
											}
																		
										// On déplace et formate les photos dans le dossier avatars
											
										$avatarpath = $path.'photos_students/'.$student_number.'.jpg';
										if(File::exists($avatarpath)){
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
											$avatarthumbpath = $path.'photos_students/'.$student_number.'_thumb.jpg';
											$img->thumb(Config::$AVATARS_THUMBS_SIZES[0], Config::$AVATARS_THUMBS_SIZES[1]);
											$img->setType(IMAGETYPE_JPEG);
											$img->save($avatarthumbpath);
											
											if(FILE::exists(DATA_DIR.Config::DIR_DATA_STORAGE.'avatars/'.substr($student_number, 0, -2).'/')){
												FILE::move($avatarthumbpath,DATA_DIR.Config::DIR_DATA_STORAGE.'avatars/'.substr($student_number, 0, -2).'/');
												FILE::move($avatarpath,DATA_DIR.Config::DIR_DATA_STORAGE.'avatars/'.substr($student_number, 0, -2).'/');
											}
											else{
												FILE::makeDir(DATA_DIR.Config::DIR_DATA_STORAGE.'avatars/'.substr($student_number, 0, -2).'/');
												FILE::move($avatarthumbpath,DATA_DIR.Config::DIR_DATA_STORAGE.'avatars/'.substr($student_number, 0, -2).'/');
												FILE::move($avatarpath,DATA_DIR.Config::DIR_DATA_STORAGE.'avatars/'.substr($student_number, 0, -2).'/');
											}
											unset($img);	
										}
									}			
														
								}
								fclose($fp);
								// On supprime le tout du dossier temp
								 FILE::delete($path);
										
				}
				$this->addJSCode('Admin.success();');
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
		
		$is_logged = isset(User_Model::$auth_data);
		$is_student = $is_logged && isset(User_Model::$auth_data['student_number']);
		$is_admin = $is_logged && User_Model::$auth_data['admin']=='1';
		
		if(!$is_logged)
			throw new ActionException('User', 'signin', array('redirect' => $_SERVER['REQUEST_URI']));
		if(!$is_admin)
			throw new ActionException('Page', 'error404');
			
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
			'username'			=> User_Model::$auth_data['username'],
			'is_logged'			=> $is_logged,
			'is_student'		=> $is_student,
			'is_admin'			=> $is_admin,
			'admins'			=> $this->model->getadmin()
		));
		
		$this->addJSCode('Admin.adminsInit();');
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