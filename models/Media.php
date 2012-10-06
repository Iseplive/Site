<?php

class Media_Model extends Model {
	public function getMedias(){
		$medias = DB::select('
				SELECT id, message, time,category_id
				FROM posts 
				WHERE official=1
				AND	category_id=1
				OR category_id=2
				OR category_id=3
				OR category_id=4
				OR category_id=10
				ORDER BY time DESC
			');
		return $medias;
	
	}
	public function getPhotos(){
		$photos = DB::select('
				SELECT id, message, time,category_id
				FROM posts 
				WHERE official=1
				AND	category_id=1
				ORDER BY time DESC
			');
		return $photos;
	
	}
	public function getVideos(){
		$videos = DB::select('
				SELECT id, message, time,category_id
				FROM posts 
				WHERE official=1
				AND	category_id=2
				ORDER BY time DESC
			');
		return $videos;
	
	}
	public function getJournaux(){
		$journaux = DB::select('
				SELECT id, message, time,category_id
				FROM posts 
				WHERE official=1
				AND	category_id=3
				OR	category_id=10
				ORDER BY time DESC
			');
		return $journaux;
	
	}
	public function getPodcast(){
		$podcast = DB::select('
				SELECT id, message, time,category_id
				FROM posts 
				WHERE official=1
				AND	category_id=4
				ORDER BY time DESC
			');
		return $podcast;
	
	}
	

}
?>