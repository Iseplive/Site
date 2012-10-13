<?php

class Media_Controller extends Controller {
	public function index(){
		$this->setView('index.php');
		
		$is_logged = isset(User_Model::$auth_data);
		$is_student = $is_logged && isset(User_Model::$auth_data['student_number']);
		$is_admin = $is_logged && User_Model::$auth_data['admin']=='1';
		
		if (!$is_student)
            throw new ActionException('User', 'signin', array('redirect' => $_SERVER['REQUEST_URI']));
			
		//Pour tout les média
		$medias=$this->model->getMedias();
		$annee=array();
		$mediaannee=array(array());
		foreach($medias as $media){
			$date=date('Y',$media['time']); 
			$ok=0;
			for($i=0;$i<=count($annee);$i++){
				if($date==$annee[$i]){
					$ok=1;
				}
			}
			if($ok==0){
				$annee[count($annee)]=$date;	
			}
			$mediaannee[''.$date.''][0]=0;
			array_push($mediaannee[''.$date.''],$media['id']);
			$mediamessage[''.$media['id'].'']=$media['message'];
			$categorie[''.$media['id'].'']=$media['category_id'];
		}
		$this->set(array(
			'annee'				=> $annee,
			'mediaannee'		=>$mediaannee,
			'mediamessage'		=>$mediamessage,
			'categorie'			=>$categorie
		));
		
		//Pour les photos
		$photos=$this->model->getPhotos();
		$annee1=array();
		$mediaannee1=array(array());
		foreach($photos as $photo){
			$date=date('Y',$photo['time']); 
			$ok=0;
			for($i=0;$i<=count($annee1);$i++){
				if($date==$annee1[$i]){
					$ok=1;
				}
			}
			if($ok==0){
				$annee1[count($annee1)]=$date;	
			}
			$mediaannee1[''.$date.''][0]=0;
			array_push($mediaannee1[''.$date.''],$photo['id']);
			$mediamessage1[''.$photo['id'].'']=$photo['message'];
		}
		$this->set(array(
			'annee1'			=> $annee1,
			'mediaannee1'		=>$mediaannee1,
			'mediamessage1'		=>$mediamessage1
		));
		
		//Pour les videos
		$videos=$this->model->getVideos();
		$annee2=array();
		$mediaannee2=array(array());
		foreach($videos as $video){
			$date=date('Y',$video['time']); 
			$ok=0;
			for($i=0;$i<=count($annee2);$i++){
				if($date==$annee2[$i]){
					$ok=1;
				}
			}
			if($ok==0){
				$annee2[count($annee2)]=$date;	
			}
			$mediaannee2[''.$date.''][0]=0;
			array_push($mediaannee2[''.$date.''],$video['id']);
			$mediamessage2[''.$video['id'].'']=$video['message'];
		}
		$this->set(array(
			'annee2'			=> $annee2,
			'mediaannee2'		=>$mediaannee2,
			'mediamessage2'		=>$mediamessage2
		));
		
		//Pour les journaux
		$journaux=$this->model->getJournaux();
		$annee3=array();
		$mediaannee3=array(array());
		foreach($journaux as $journal){
			$date=date('Y',$journal['time']); 
			$ok=0;
			for($i=0;$i<=count($annee3);$i++){
				if($date==$annee3[$i]){
					$ok=1;
				}
			}
			if($ok==0){
				$annee3[count($annee3)]=$date;	
			}
			$mediaannee3[''.$date.''][0]=0;
			array_push($mediaannee3[''.$date.''],$journal['id']);
			$mediamessage3[''.$journal['id'].'']=$journal['message'];
		}
		$this->set(array(
			'annee3'			=> $annee3,
			'mediaannee3'		=>$mediaannee3,
			'mediamessage3'		=>$mediamessage3
		));
		
		//Pour les Podcast
		$podcasts=$this->model->getPodcast();
		$annee4=array();
		$mediaannee4=array(array());
		foreach($podcasts as $podcast){
			$date=date('Y',$podcast['time']); 
			$ok=0;
			for($i=0;$i<=count($annee4);$i++){
				if($date==$annee4[$i]){
					$ok=1;
				}
			}
			if($ok==0){
				$annee4[count($annee4)]=$date;	
			}
			$mediaannee4[''.$date.''][0]=0;
			array_push($mediaannee4[''.$date.''],$podcast['id']);
			$mediamessage4[''.$podcast['id'].'']=$podcast['message'];
		}
		$this->set(array(
			'annee4'			=> $annee4,
			'mediaannee4'		=>$mediaannee4,
			'mediamessage4'		=>$mediamessage4
		));
	}
	
	
}
?>
