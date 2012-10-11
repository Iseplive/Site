<?php

class Administration_Model extends Model {
	/*	Insère les données dans la table students
	
		*@param string $username    	user's Pseudo 
		*@param string $lastname		user's last name
		*@param string $firstname		user's first name
		*@param int $student_number		student number
		*@param int $promo				promo date
		*@param int $cesure				si il est en césure
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
	
	/*	Insère les données dans la table users
	
		*@param string $username    	user's Pseudo 
		*@param string $lastname		user's last name
		*@param string $firstname		user's first name
		*@param int $student_number		student number
		*@param int $promo				promo date
		*@param int $cesure				si il est en césure
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
	 * update les catégorie des ISEP d'or
	 *
	 * @param string $type	 	type de personnes touchées
	 * @param string $extra	 	sous-type de personnes/events touchées
	 * @param string $question	nom de la catégorie
	 * @param int $idate		id de la catégorie
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
	 * insere des catégorie dans ISEP d'or
	 *
	 * @param string $type	 	type de personnes touchées
	 * @param string $extra	 	sous-type de personnes/events touchées
	 * @param string $question	nom de la catégorie
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
	 public function updateemployees($lastname,$firstname,$id,$username){
		DB::createQuery('isepdor_employees')
			->set(array(
				'lastname'		=>$lastname,
				'firstname'		=>$firstname,
				'username'		=>$username
			
			))
			->where(array('id' => $id))
			->update();
	 }
	 
	 /* insere entrée dans la table isepdor_employees
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
	 public function updateevent($name,$id,$soiree){
		DB::createQuery('isepdor_event')
			->set(array(
				'name'			=>$name,
				'extra'		=>$soiree
			))
			->where(array('id' => $id))
			->update();
	 }
	 
	 /* insere entrée dans la table isepdor_event
	 *
	 * @param string $lastname	 	
	 * @param string $firstname	 	
	 * @param string $username
	 */	
	 public function insertevent($name,$soiree){
		DB::createQuery('isepdor_event')
			->set(array(
				'name'		=>$name,
				'extra'		=>$soiree
			))
			->insert();
	 }
	 
	 /* load isepdor_date
	 *
	 * @return  resulte
	 */	
	 public function getdate(){
		$date=DB::select('
			SELECT * FROM isepdor_date ORDER BY tour
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
	 public function insertdate($first1,$first2,$second1,$second2){
		DB::createQuery('isepdor_date')
			->set(array(
				'date'			=>$first1
			))
			->where(array('tour' => 1, 'type'=>1))
			->update();
			
		DB::createQuery('isepdor_date')
			->set(array(
				'date'			=>$first2
			))
			->where(array('tour' => 1, 'type'=>2))
			->update();
		
		DB::createQuery('isepdor_date')
			->set(array(
				'date'			=>$second1
			))
			->where(array('tour' => 2, 'type'=>1))
			->update();
		
		DB::createQuery('isepdor_date')
			->set(array(
				'date'			=>$second2
			))
			->where(array('tour' => 2, 'type'=>2))
			->update();
	 
	 }
	 
	 /* Récupère les résultats des vote isepd'or 
	 *
	 *tour 1
	 */
	 public function getresult1(){
		$result=DB::select('
			SELECT i.student_username,i.isepdor_associations_id,u.username,q.questions,e.name,em.username as admin,a.name as assoce
			FROM isepdor_round1 i
			LEFT JOIN users u ON u.id=i.voting_user_id 
			LEFT JOIN isepdor_questions q ON q.id=i.isepdor_questions_id
			LEFT JOIN isepdor_event e ON e.id=i.isepdor_event_id
			LEFT JOIN isepdor_associations a ON a.id=i.isepdor_associations_id
			LEFT JOIN  isepdor_employees em ON em.id=i.isepdor_employees_id
			ORDER BY i.voting_user_id 
		');
		return $result;
	 }
	 
	 /* Récupère les résultats des vote isepd'or 
	 *
	 *tour 2
	 */
	 public function getresult2(){
		$result=DB::select('
			SELECT i.student_username,i.isepdor_associations_id,u.username,q.questions,e.name,em.username as admin
			FROM isepdor_round2 i
			LEFT JOIN users u ON u.id=i.voting_user_id 
			LEFT JOIN isepdor_questions q ON q.id=i.isepdor_questions_id
			LEFT JOIN isepdor_event e ON e.id=i.isepdor_event_id
			LEFT JOIN isepdor_associations a ON a.id=i.isepdor_associations_id
			LEFT JOIN  isepdor_employees em ON em.id=i.isepdor_employees_id
			ORDER BY i.voting_user_id 
		');
		return $result;
	 }
	 
	 /*Supprime dans isepdor_event
	 *
	 * @param $id
	 */
	 public function deleteevent($id){
		DB::createQuery('isepdor_event')
			->where(array('id' => $id))
			->delete();
	 }
	 
	 /*Supprime dans isepdor_questions
	 *
	 * @param $id
	 */
	 public function deletequestions($id){
		DB::createQuery('isepdor_questions')
			->where(array('id' => $id))
			->delete();
	 }
	 
	 /*Supprime dans isepdor_employees
	 *
	 * @param $id
	 */
	 public function deleteemployees($id){
		DB::createQuery('isepdor_employees')
			->where(array('id' => $id))
			->delete();
	 }
	 
	 /*Supprime les résultats des isep d'or
	 *
	 * @param $id
	 */
	 public function deleteresult(){
		DB::createQuery('isepdor_round1')
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
}
?>