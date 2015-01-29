<?php
	// ISEPLIVE export android module
	// Emeric Baveux 7/11/12
        // Basé sur le site de ISEPLive

// No cache
header('Content-Type: text/xml');
header('Content-Type: application/xml');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
define('APP_DIR', realpath('..').'/');
define('CF_DIR', realpath('../../confeature').'/');
define('DATA_DIR', realpath('../data').'/');

	function getAttachedFileURL($file_id, $ext, $suffix=''){
		$extended_file_id = str_pad((string) $file_id, 6, '0', STR_PAD_LEFT);
		return Config::URL_STORAGE.'files/'.substr($extended_file_id, 0, 2).'/'.substr($extended_file_id, 2, 2).'/'.$file_id.($suffix=='' ? '' : '_'.$suffix).'.'.$ext;
	}

	
try{
	
	// Loading Confeature and User from iseplive
	require_once '../../confeature/init.php';
	require_once '../models/User.php';

        $username = $_GET['user'];
        $pass = $_GET['pass'];
		
		
        
       // Création du XML
        $xml = new DOMDocument('1.0', 'utf-8');
        $MainNode = $xml->createElement('iseplive');
        $plateform = $xml->createElement('plateform',"android");
        $MainNode->appendChild($plateform);
        // 
        // Authentification du membre
	$user = new User_Model();
	if ($user->authenticate($username,$pass) == true) {
                $user = $xml->createElement('user');
                $node = $xml->createElement('nom',User_Model::$auth_data['lastname']);
                $user->appendChild($node);
                $node = $xml->createElement('prenom',User_Model::$auth_data['firstname']);
                $user->appendChild($node);
                $node = $xml->createElement('student_number',User_Model::$auth_data['student_number']);
                $user->appendChild($node);
                $node = $xml->createElement('avatar',User_Model::$auth_data['avatar_url']);
                $user->appendChild($node);
                $MainNode->appendChild($user);
                		
	}
        else
        {
            // Renvoi un login echec
                $user = $xml->createElement('user');
                $node = $xml->createElement('nom',"false");
                $user->appendChild($node);
                $node = $xml->createElement('prenom',"false");
                $user->appendChild($node);
                $node = $xml->createElement('student_number',"0");
                $user->appendChild($node);
                $node = $xml->createElement('avatar',"");
                $user->appendChild($node);
                $MainNode->appendChild($user);
                
        }
        if (isset($_GET['install']) && $_GET['install'] == "true")
        {
            // Envoi des données de base pour l'installation sur l'appli
            
            $Categories = new Category_Model();
            $tableauDesCategories = $Categories->getAll();
            foreach($tableauDesCategories as $cat) {
                    $GroupXml = $xml->createElement('category');
                    $node = $xml->createElement('name',$cat['name']);
                    $GroupXml->appendChild($node);    
                    $node = $xml->createElement('id',$cat['id']);
                    $GroupXml->appendChild($node);
                    $MainNode->appendChild($GroupXml);

            }
        }
        
        
                // Lecture des posts demandés et 
                $model = new Post_Model();
                // Parametres de base :
                $postParam = array(
                                    'restricted'	=> true,
                                    'show_private'	=> true
                );
                
                if (isset($_GET['id']) && $_GET['id'] != "-1") {
                    $postParam['id'] = $_GET['id'];
                }
                
                if (isset($_GET['official']) && $_GET['official'] == "true") {
                    $postParam['official'] = true; }
                elseif (isset($_GET['official']) && $_GET['official'] == "false") {
                    $postParam['official'] = false; }
                
                    
                if (isset($_GET['category']) && $_GET['category'] != "" ) {
                    $postParam['category_id'] = $_GET['category'];
                }
                $posts = $model->getPosts($postParam, 5);

                    foreach($posts as $post){
                        $postXml = $xml->createElement('post');
                        $node = $xml->createElement('group',$post['group_name']);
                        $postXml->appendChild($node);
                        $node = $xml->createElement('user',$post['username']);
                        $postXml->appendChild($node); 
                        try {
                             $message = $post['message'];
                        }
                        catch (Exception $e)  {
                            $message = "";
                        }
                        $node = $xml->createElement('message',$message);
                        $postXml->appendChild($node);
                        $node = $xml->createElement('id',$post['id']);
                        $postXml->appendChild($node);
                        $node = $xml->createElement('time',Date::easy((int) $post['time']));
                        $postXml->appendChild($node);

                        $MainNode->appendChild($postXml);
                        // Si on demande un post en particulier,on va chercher en envoyer les medias attachés
                        if (isset($_GET['id']) && $_GET['id'] != "-1") {
                           $Medias = new Media_Model;
                           $MediasTableau = $Medias->getPhotos();
                           
                        $attachments = DB::select('
				SELECT post_id, id, name, ext
				FROM attachments
				WHERE post_id IN ('.$post['id'].')
				ORDER BY ext, id ASC
			');
                            $nb=0;
                          
                           foreach($attachments as $media){
                               $mediaXml = $xml->createElement('media');
				$media['thumb'] = getAttachedFileURL((int) $media['id'], 'jpg', 'thumb');
                                $media['url'] = getAttachedFileURL((int) $media['id'], $media['ext']);
     
									
									
									
                                   $node = $xml->createElement('path',$media['url']);
                                   $mediaXml->appendChild($node);
                                    $node = $xml->createElement('type',"image");
                                   $mediaXml->appendChild($node);
                                   $nb++;
                               $MainNode->appendChild($mediaXml);
                               
                           }
                           

                         }
                         
                         
                }
                
                
                
                $xml->appendChild($MainNode);
                echo $xml->saveXML();
        
	
}catch(Exception $e){
	if(Config::DEBUG)
		echo $e->getMessage();
        
}
	
?>
