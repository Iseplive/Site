<?php

class Administration_Model extends Model {
	/*	Ins�re les donn�es dans la table students
	
		*@param string $username    	user's Pseudo 
		*@param string $lastname		user's last name
		*@param string $firstname		user's first name
		*@param int $student_number		student number
		*@param int $promo				promo date
		*@param int $cesure				si il est en c�sure
	*/
	public function insertStudents($username,$lastname,$firstname,$student_number,$promo,$cesure){
		DB::createQuery('students')
			->set(array(
				'username'			=> $username,
				'lastname'			=> $lastname,
				'firstname'			=> $firstname,
				'student_number'	=> $student_number,
				'promo'				=> $promo,
				'cesure'			=> $cesure
			))
			->insert();
	}
	
	/*	Ins�re les donn�es dans la table users
	
		*@param string $username    	user's Pseudo 
		*@param string $lastname		user's last name
		*@param string $firstname		user's first name
		*@param int $student_number		student number
		*@param int $promo				promo date
		*@param int $cesure				si il est en c�sure
	*/
	public function insertUsers($username,$admin,$mail,$msn,$jabber,$address,$zipcode,$city,$cellphone,$phone,$birthday){
		DB::createQuery('users')
			->set(array(
				'username'		=> $username,
				'admin'			=> $admin,
				'mail'			=> $mail,
				'msn'			=> $msn,
				'jabber'		=> $jabber,
				'address'		=> $address,
				'zipcode'		=> $zipcode,
				'city'			=> $city,
				'cellphone'		=> $cellphone,
				'phone'			=> $phone,
				'birthday'		=> $birthday
				
			))
			->insert();
	}
	
	/**
	 * Load data of an user into the $auth_data static var
	 *
	 * @param string $username	User name
	 * @param int $type	User type of DB
	 * @return boolean	True on success, false on failure
	 */
	 public function checkuser($username,$type){
		if($type==1){
			$users = DB::select('
				SELECT username
				FROM users 
				WHERE username="'.$username.'"
			');
		}
		elseif($type==2){
			$users = DB::select('
				SELECT username
				FROM students 
				WHERE username="'.$username.'"
			');
		}
		if(!empty($users)){
			return true;
		}
		return false;
	 }
	 
	 /**
	 * Load data of ISEP d'or's questions
	 *
	 * @return result
	 */
	 public function getquestions(){
		$questions=DB::select('
			SELECT * FROM isepdor_questions ORDER BY position
		');
		return $questions;
	}
	/**
	 * update les cat�gorie des ISEP d'or
	 *
	 * @param string $type	 	type de personnes touch�es
	 * @param string $extra	 	sous-type de personnes/events touch�es
	 * @param string $question	nom de la cat�gorie
	 * @param int $idate		id de la cat�gorie
	 * @param int $position		ordre d'affichage
	 */	
	 public function updateisepdor($type,$extra,$question,$id,$position){
		DB::createQuery('isepdor_questions')
			->set(array(
				'questions'		=>$question,
				'extra'			=>$extra,
				'type'			=>$type,
				'position'		=>$position
			
			))
			->where(array('id' => $id))
			->update();
	 
	 }
	 
	 /**
	 * insere des cat�gorie dans ISEP d'or
	 *
	 * @param string $type	 	type de personnes touch�es
	 * @param string $extra	 	sous-type de personnes/events touch�es
	 * @param string $question	nom de la cat�gorie
	 * @param int $position		ordre d'affichage
	 */	
	 public function insertisepdor($type,$extra,$question,$position){
		DB::createQuery('isepdor_questions')
			->set(array(
				'questions'		=>$question,
				'extra'			=>$extra,
				'type'			=>$type,
				'position'		=>$position
			
			))
			->insert();
	 
	 }
	 /**
	 *
	 *@param string $id	 	tableau contenant les ids des nouvelles questions
	 */
	  public function checkIsepdorQuestions($id){
		$ids=DB::select('
			SELECT id FROM isepdor_questions WHERE id NOT IN ('. implode(',',$id) . ');
		');
		return $ids;
	  }
	 /**
	 *
	 *@param string $id	 	tableau contenant les ids des nouvelles donn�es
	 */
	  public function checkIsepdorEvents($id){
		$ids=DB::select('
			SELECT id FROM isepdor_event WHERE id NOT IN ('. implode(',',$id) . ');
		');
		return $ids;
	  }	
	 /**
	 *
	 *@param string $id	 	tableau contenant les ids des nouvelles donn�es
	 */
	  public function checkIsepdorEmployees($id){
		$ids=DB::select('
			SELECT id FROM isepdor_employees WHERE id NOT IN ('. implode(',',$id) . ');
		');
		return $ids;
	  }		  
	 /**
	 * Load data of ISEP d'or's employees tables
	 *
	 * @return result
	 */
	 public function getemployees(){
		$employees=DB::select('
			SELECT * FROM isepdor_employees 
		');
		return $employees;
	 }
	 
	 /**
	 * Load data of ISEP d'or's events tables
	 *
	 * @return result
	 */
	 public function getevents(){
		$events=DB::select('
			SELECT * FROM isepdor_event
		');
		return $events;
	 }
	 
	 /* update la table isepdor_employees
	 *
	 * @param string $lastname	 	
	 * @param string $firstname	 	
	 * @param int $id
	 * @param string $username
	 */	
	 public function updateEmployees($lastname,$firstname,$id,$username){
		DB::createQuery('isepdor_employees')
			->set(array(
				'lastname'		=>$lastname,
				'firstname'		=>$firstname,
				'username'		=>$username
			
			))
			->where(array('id' => $id))
			->update();
	 }
	 
	 /* insere entr�e dans la table isepdor_employees
	 *
	 * @param string $lastname	 	
	 * @param string $firstname	 	
	 * @param string $username
	 */	
	 public function insertemployees($lastname,$firstname,$username){
		DB::createQuery('isepdor_employees')
			->set(array(
				'lastname'		=>$lastname,
				'firstname'		=>$firstname,
				'username'		=>$username
			
			))
			->insert();
	 }
	 
	 /* update la table isepdor_event
	 *
	 * @param string $name	 	
	 * @param string $soiree	 	
	 * @param int $id
	 */	
	 public function updateEvent($name,$id,$soiree){
		DB::createQuery('isepdor_event')
			->set(array(
				'name'			=>$name,
				'extra'		=>$soiree
			))
			->where(array('id' => $id))
			->update();
	 }
	 
	 /* insere entr�e dans la table isepdor_event
	 *
	 * @param string $lastname	 	
	 * @param string $firstname	 	
	 * @param string $username
	 */	
	 public function insertEvent($name,$soiree){
		DB::createQuery('isepdor_event')
			->set(array(
				'name'		=>$name,
				'extra'		=>$soiree
			))
			->insert();
	 }
	 
	 /* load event_date
	 *
	 * @return  results
	 */	
	 public function getDate(){
		$date=DB::select('
			SELECT * FROM event_date WHERE nom="isepdor" ORDER BY tour
		');
		return $date;
	 }
	 
	 /* met a jour les date de vote des isepdor
	 *
	 *@param $first1  1er tour debut
	 *@param $first2  1er tour fin
	 *@param $second1  2nd tour debut
	 *@param $second1  2nd tour fin
	 */
	 public function insertdate($first1,$first2,$second1,$second2,$third1,$third2){
		DB::createQuery('event_date')
			->set(array(
				'start'			=>$first1,
				'end'			=>$first2
			))
			->where(array('tour' => 1, 'nom'=>"isepdor"))
			->update();
			
		DB::createQuery('event_date')
			->set(array(
				'start'			=>$second1,
				'end'			=>$second2
			))
			->where(array('tour' => 2, 'nom'=>"isepdor"))
			->update();
		
		DB::createQuery('event_date')
			->set(array(
				'start'			=>$third1,
				'end'			=>$third2
			))
			->where(array('tour' => 3, 'nom'=>"isepdor"))
			->update();
	 }
	 
	 /* R�cup�re les r�sultats des vote isepd'or 
	 *
	 */
	 public function getResult(){
		$result=DB::select('
			SELECT i.round,i.student_username,i.isepdor_associations_id,u.username,q.questions,e.name,em.username as admin,a.name as assoce
			FROM isepdor_round i
			LEFT JOIN users u ON u.id=i.voting_user_id 
			LEFT JOIN isepdor_questions q ON q.id=i.isepdor_questions_id
			LEFT JOIN isepdor_event e ON e.id=i.isepdor_event_id
			LEFT JOIN groups a ON a.id=i.isepdor_associations_id
			LEFT JOIN  isepdor_employees em ON em.id=i.isepdor_employees_id
			ORDER BY i.voting_user_id 
		');
		return $result;
	 }
	 /*Supprime dans isepdor_event
	 *
	 * @param $id
	 */
	 public function deleteEvents($id){
		DB::createQuery('isepdor_event')
			->where(array('id' => $id))
			->delete();
	 }
	 
	 /*Supprime dans isepdor_questions
	 *
	 * @param $id
	 */
	 public function deleteQuestions($id){
		DB::createQuery('isepdor_questions')
			->where(array('id' => $id))
			->delete();
	 }
	 
	 /*Supprime dans isepdor_employees
	 *
	 * @param $id
	 */
	 public function deleteEmployees($id){
		DB::createQuery('isepdor_employees')
			->where(array('id' => $id))
			->delete();
	 }
	 
	 /*Supprime les r�sultats des isep d'or
	 *
	 * @param $id
	 */
	 public function deleteresult(){
		DB::createQuery('isepdor_round')
		->force()
		->delete();
	 }
	 
	 /*Charge les admins du site
	 *
	 * @return name
	 */
	 public function getadmin(){
		$result=DB::select('
			SELECT s.lastname, s.firstname, s.username
			FROM users u, students s
			WHERE u.admin=1
			AND s.username=u.username
		');
		return $result;
	 }
	 
	  /*Supprime un admin
	 *
	 * @param $username
	 */
	 public function deleteadmin($username){
		if($this->checkuser($username,1)){
			DB::createQuery('users')
				->set(array(
					'admin'			=>0
				))
				->where(array('username' => $username))
				->update();
		}
	 }
	 /*ajouteun admin
	 *
	 * @param $username
	 */
	 public function addadmin($username){
		if($this->checkuser($username,1)){
			DB::createQuery('users')
				->set(array(
					'admin'			=>1
				))
				->where(array('username' => $username))
				->update();
		}
	 }
	 
	 /*
	 * R�cupere la date d'anniversaire d'un utilisateur
	*/
	public function getBirthDay($username){
		$result=DB::select('
			SELECT birthday
			FROM users 
			WHERE username="'.$username.'"
		');
		return date("d/m/Y", strtotime($result[0]['birthday']));
	}
}
?>