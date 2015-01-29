
<div id="post-<?php echo $post['id']; ?>" class="post">
    
    <?php
    if (isset($post['group_id']) && $post['official'] == '1') {
        $post_user_url = Config::URL_ROOT . Routes::getPage('group', array('group' => $post['group_url']));
        $post_user_name = $post['group_name'];
    } else {
        $post_user_url = Config::URL_ROOT . Routes::getPage('student', array('username' => $post['username']));
        $post_user_name = isset($post['firstname']) ? $post['firstname'] . ' ' . $post['lastname'] : $post['username'];
    }

    if (isset($post['avatar_url'])) {
        ?>
        <a href="<?php echo $post_user_url; ?>" class="avatar"><img src="<?php echo $post['avatar_url']; ?>" alt="" /></a>
        <?php
    }


// Post delete button
    if (($is_logged && $username == $post['username'])
            || $is_admin
            || (isset($post['group_id']) && isset($groups_auth)
            && isset($groups_auth[(int) $post['group_id']])) && $groups_auth[(int) $post['group_id']]['admin']) {
        ?>
        <a href="<?php echo Config::URL_ROOT . Routes::getPage('post_delete', array('id' => $post['id'])); ?>" class="post-delete">x</a>
        <?php
    }
    ?>

    <div class="post-message">
        <a href="<?php echo $post_user_url; ?>" class="post-username"><?php echo htmlspecialchars($post_user_name); ?></a>
        <?php echo Text::inHTML($post['message']); ?>

        <?php
// Event
        if (isset($post['event'])) {
            ?>
            <div class="event">
                <img src="<?php echo Config::URL_STATIC; ?>images/icons/event.png" alt="" class="icon" /> <strong><?php echo htmlspecialchars($post['event']['title']); ?></strong><br />
                <?php echo Date::event(strtotime($post['event']['date_start']), strtotime($post['event']['date_end'])); ?>
            </div>
            <?php
        }



// Survey
        if (isset($post['survey'])) {
            ?>
            <form action="<?php echo Config::URL_ROOT . Routes::getPage('survey_vote', array('id' => $post['survey']['id'])); ?>" class="survey" method="post">
                <img src="<?php echo Config::URL_STATIC; ?>images/icons/survey.png" alt="" class="icon" /> <strong><?php echo htmlspecialchars($post['survey']['question']); ?></strong><br />
                <ul>
                    <?php
                    $ended = strtotime($post['survey']['date_end']) < time();
                    $total_votes = 0;
                    $voting = array();
                    foreach ($post['survey']['answers'] as &$answer) {
                        $total_votes += (int) $answer['nb_votes'];
                        $answer['votes'] = $answer['votes'] == '' ? array() : json_decode($answer['votes'], true);
                        $voting = array_unique(array_merge($voting, $answer['votes']));
                    }
                    $nb_voting = count($voting);

                    foreach ($post['survey']['answers'] as &$answer) {
                        // Results
                        if ($post['survey']['multiple'] == '1')
                            $perc = $nb_voting == 0 ? 0 : ((int) $answer['nb_votes']) / $nb_voting;
                        else
                            $perc = $total_votes == 0 ? 0 : ((int) $answer['nb_votes']) / $total_votes;
                        $perc_s = round(100 * $perc);
                        /*
                         * 	Graph of colors
                         * 	|\  /\  /    <-- blue, then red
                         * 	| \/  \/
                         * 	| /\  /\
                         * 	|/  \/  \    <-- green
                         * 	------------
                         */
                        if ($perc < 0.5) {
                            $red = '00';
                            $green = str_pad(dechex(255 * $perc * 2), 2, '0', STR_PAD_LEFT);
                            $blue = str_pad(dechex(255 * (1 - $perc * 2)), 2, '0', STR_PAD_LEFT);
                        } else {
                            $red = str_pad(dechex(255 * ($perc - 0.5) * 2), 2, '0', STR_PAD_LEFT);
                            $green = str_pad(dechex(255 * (1 - ($perc - 0.5) * 2)), 2, '0', STR_PAD_LEFT);
                            $blue = '00';
                        }
                        ?>
                        <li class="survey-answer-result">
                            <?php echo htmlspecialchars($answer['answer']); ?><br />
                            <div class="answer-bar">
                                <div style="width: <?php echo $perc_s; ?>%; background-color: #<?php echo $red . $green . $blue; ?>;">
                                    &nbsp;<?php echo $perc_s; ?>%
                                </div>
                            </div>
                            <?php echo __('POST_SURVEY_NB_VOTES', array('votes' => $answer['nb_votes'])); ?>
                        </li>
                        <?php
                        // Form to votes
                        if ($is_student && !$ended) {
                            ?>
                            <li class="survey-answer-vote">
                                <label>
                                    <input type="<?php echo $post['survey']['multiple'] == '1' ? 'checkbox' : 'radio'; ?>" name="answer<?php echo $post['survey']['multiple'] == '1' ? '-' . $answer['id'] : ''; ?>" value="<?php echo $answer['id']; ?>"<?php if (in_array($username, $answer['votes']))
                    echo ' checked="checked"'; ?> />
                                           <?php echo htmlspecialchars($answer['answer']); ?>
                                </label>
                            </li>
                            <?php
                        }
                    }
                    ?>
                </ul>

                <?php
                if ($is_student && !$ended) {
                    ?>
                    <div class="survey-choice-vote">
                        <input type="submit" value="<?php echo __('POST_SURVEY_SUBMIT'); ?>" />
                                                                		- <a href="javascript:;"><?php echo __('POST_SURVEY_SHOW_RESULTS'); ?></a>
                    </div>
                    <div class="survey-choice-results">
                        <a href="javascript:;"><?php echo __('POST_SURVEY_SHOW_VOTE'); ?></a>
                    </div>
                    <?php
                }
                ?>
                <?php echo __('POST_SURVEY_ENDING_DATE') . ' ' . Date::dateHour(strtotime($post['survey']['date_end'])); ?>
            </form>
            <?php
        }



// Attachments
		$classhidden="";
		if($post["category_id"]==1 && isset($one_post) && $post['attachments_nb_photos'] != 0){
			$classhidden="hidden";
			?>
				<br/><br/><br/>
				<?php
				if ($is_admin){
					?><a id="adminView" style="cursor:pointer">
						<img alt="" style="position:relative;top:3px;" src="<?php echo Config::URL_STATIC."images/icons/edit.png";?>"/>
						<?php echo __("ADMIN_POST_PHOTO");?>
					</a><br/><br/><?php
				}
				?>
				<div id="galleria"></div>
			<?php
		}
        if (!isset($post['attachments']))
            $post['attachments'] = array();
        $nb_photos = 0;
		
        foreach ($post['attachments'] as $attachment) {
            switch ($attachment['ext']) {
                // Photo
                // see: http://flash-mp3-player.net/players/maxi/
                case 'jpg':
                case 'gif':
                case 'png':
                    if ($nb_photos == 0) {
						if ($is_admin && $post["category_id"]==1 && isset($one_post) && $post['attachments_nb_photos'] != 0 ){
							?>	
								<div id="addAdmin" style="display:none">
									<form id="publish-form" action="<?php echo Config::URL_ROOT.Routes::getPage('attachment_add',array('id'=>$post['id'])); ?>" method="post" enctype="multipart/form-data" target="publish_iframe" onsubmit="return Post.submitForm();">
										<fieldset id="publish-stock-attachment-photo" class="publish-attachment">
											<legend><img src="<?php echo Config::URL_STATIC; ?>images/icons/attachment_photo.png" alt="" class="icon" /> <?php echo __('ADD_ATTACHMENT_PHOTO'); ?></legend>
											<?php echo __('PUBLISH_ATTACHMENT_SEND'); ?> <input type="file" name="attachment_photo[]" multiple /><br />
											<span class="publish-attachment-info"><?php echo __('PUBLISH_ATTACHMENT_PHOTO_INFO', array('size' => File::humanReadableSize(Config::UPLOAD_MAX_SIZE_PHOTO))); ?></span>
											<input type="submit" id="publish-submit" value="<?php echo __('PUBLISH_SUBMIT'); ?>" />
										</fieldset>
									</form>
									<div id="publish-error" class="hidden"></div>
									<iframe name="publish_iframe" class="hidden"></iframe>
								</div>
							<?php
						}
                        ?>
                        <div class="photos <?php echo $classhidden;?>" >
                            <?php
                    }
							?>
							<?php if($classhidden==""){?><a href="<?php echo Config::URL_ROOT . Routes::getPage('post', array('id' => $post['id'])) . '#photo-' . $attachment['id']; ?>"><?php }?>
								<img src="<?php echo $attachment['thumb']; ?>" alt="" id="thumb<?php echo $attachment['id'];?>"/>
								<?php if ($is_admin && $post["category_id"]==1){?>
									<span id="link<?php echo $attachment['id'];?>" href="<?php echo Config::URL_ROOT . Routes::getPage('attachment_delete', array('id' => $attachment['id'],'post_id'=>$post['id'])); ?>" style="cursor:pointer" class="photo-delete">.</span>
								<?php } ?>
							<?php if($classhidden==""){?></a><?php }?>
						   
							<?php
							$nb_photos++;
							if (!isset($one_post) && $nb_photos == Config::PHOTOS_PER_POST && Config::PHOTOS_PER_POST < $post['attachments_nb_photos']) {
								?>
								<a href="<?php echo Config::URL_ROOT . Routes::getPage('post', array('id' => $post['id'])); ?>" class="photos-more"><?php echo __('POST_LINK_PHOTOS', array('nb' => $post['attachments_nb_photos'])); ?></a>
						</div>
                        <?php
                    } else if ($nb_photos == $post['attachments_nb_photos']) {
                        ?>
                    </div>
                    <?php
                }
                break;




            // Video
            // see: http://flv-player.net/players/maxi/
            case 'flv':
                ?>
                <object data="<?php echo Config::URL_STATIC; ?>players/player_flv_maxi.swf" width="480" height="360" type="application/x-shockwave-flash" class="video">
                    <param name="movie" value="<?php echo Config::URL_STATIC; ?>players/player_flv_maxi.swf" />
                    <param name="allowfullscreen" value="true" />
                    <param name="wmode" value="transparent" />
                    <param name="base" value="." />
                    <param name="flashvars" value="flv=<?php echo urlencode($attachment['url']); ?>&amp;startimage=<?php echo urlencode($attachment['thumb']); ?>&amp;margin=0&amp;showvolume=1&amp;showtime=1&amp;showtime=autohide&amp;showloading=always&amp;showfullscreen=1&amp;showmouse=always&amp;showiconplay=1&amp;iconplaycolor=7F82CA&amp;iconplaybgcolor=E4E5FF&amp;iconplaybgalpha=80&amp;playercolor=E4E5FF&amp;bgcolor1=E4E5FF&amp;bgcolor2=D4D6FF&amp;slidercolor1=#3F3D6A&amp;slidercolor2=242153&amp;sliderovercolor=15123C&amp;buttoncolor=242153&amp;buttonovercolor=15123C&amp;textcolor=242153" />
                </object>
                <?php
                break;

			case 'mp4':
				?>
				<script type="text/javascript">
				jQuery(document).ready(function($) {
					jQuery('video').mediaelementplayer({
						defaultVideoWidth: "100%",
						defaultVideoHeight: "100%",
						features: ['playpause','progress','tracks','volume','fullscreen'],
						videoVolume: 'horizontal'
					});
				});
				</script>
				<div>
					<br/>
					<video	width="<?php if(isset($one_post)){ echo '800';}else{ echo'400';} ?>" height="<?php if(isset($one_post)){echo '450' ;}else{ echo '250' ;}?>" poster="<?php echo $attachment['thumb']; ?>" controls="controls" preload="none"  >
						<source src="<?php echo $attachment['url']; ?>" type="video/mp4" />
						<!-- Fallback flash player for no-HTML5 browsers with JavaScript turned off -->
						<object class="video" width="800" height="600"  type="application/x-shockwave-flash" data="<?php echo Config::URL_STATIC; ?>players/flashmediaelement.swf"> 		
							<param name="movie" value="<?php echo Config::URL_STATIC; ?>players/flashmediaelement.swf" /> 
							<param name="allowfullscreen" value="true" />
							<param name="flashvars" value="controls=true&amp;file=<?php echo urlencode($attachment['url']); ?>" /> 		
							<img src="<?php echo $attachment['thumb']; ?>" width="100%" height="100%" />
						</object> 	
					</video>
				</div>
				<br/>
				<?php
				break;


            // Audio
            case 'mp3':
                ?>
                <object data="<?php echo Config::URL_STATIC; ?>players/player_mp3_maxi.swf" width="300" height="20" type="application/x-shockwave-flash" class="audio">
                    <param name="movie" value="<?php echo Config::URL_STATIC; ?>players/player_mp3_maxi.swf" />
                    <param name="wmode" value="transparent" />
                    <param name="flashvars" value="mp3=<?php echo urlencode($attachment['url']); ?>&amp;width=300&amp;showstop=0&amp;showinfo=0&amp;showvolume=1&amp;showloading=autohide&amp;volumewidth=40&amp;bgcolor1=E4E5FF&amp;bgcolor2=D4D6FF&amp;slidercolor1=#3F3D6A&amp;slidercolor2=242153&amp;sliderovercolor=15123C&amp;buttoncolor=242153&amp;buttonovercolor=15123C&amp;textcolor=242153"/>
                </object>
                <?php
                break;


            // Document
            default:
                ?>
                <div class="attachment">
                    <a href="<?php echo $attachment['url']; ?>"><img src="<?php echo Mimetype::getIcon($attachment['ext']); ?>" alt="" class="icon" /> <?php echo htmlspecialchars($attachment['name']); ?></a>
                </div>
            <?php
        }
    }


// Si on affiche uniquement ce post, on prépare l'affichage des photos en grand
    if (isset($one_post) && $post['attachments_nb_photos'] != 0 && $post['category_id']!=1) {
        ?>
        <div id="attachment-photo" class="hidden">
            <a href="javascript:;" id="attachment-photo-prev"><?php echo __('POST_PHOTO_PREV'); ?></a>
            <a href="javascript:;" id="attachment-photo-next"><?php echo __('POST_PHOTO_NEXT'); ?></a>
            <a href="javascript:;" id="attachment-photo-album"><?php echo __('POST_PHOTOS_ALBUM'); ?></a>
        </div>
        <?php
    }
    ?>

    <div class="post-info">
        <?php echo Date::easy((int) $post['time']); ?>
        <?php
        if (isset($post['group_id']) && $post['official'] != '1') {
            ?>
            &#183; <a href="<?php echo Config::URL_ROOT . Routes::getPage('group', array('group' => $post['group_url'])); ?>"><?php echo htmlspecialchars($post['group_name']); ?></a>
            <?php
        }
        if ($is_student) {
            ?>
            &#183; <a href="javascript:;" onclick="Comment.write(<?php echo $post['id']; ?>);"><?php echo __('POST_COMMENT_LINK'); ?></a>
            <!-- Like Links -->
            <?php
            $has_liked = (empty($post['likes']['users'][0])) ? false : in_array(User_Model::$auth_data['id'], array_unique($post['likes']['users'][0], SORT_REGULAR), true);
            $has_disliked = (empty($post['dislikes']['users'][0])) ? false : in_array(User_Model::$auth_data['id'], array_unique($post['dislikes']['users'][0], SORT_REGULAR), true);
            $dislikeEnable=$post['dislike'];
            if (!$has_liked) {
                ?>
                &#183; <a href="javascript:;" onclick="Like.initPostLike(<?php echo $post['id'] ?>)" class="like-link" id="post-like-link-<?php echo $post['id'] ?>" ><?php echo __('POST_LIKE_LINK'); ?></a>
                <a href="javascript:;" onclick="Like.initPostUnlike(<?php echo $post['id'] ?>)" class="unlike-link hidden" id="post-unlike-link-<?php echo $post['id'] ?>"><?php echo __('POST_UNLIKE_LINK'); ?></a>
            <?php } else { ?>
                &#183; <a href="javascript:;" onclick="Like.initPostUnlike(<?php echo $post['id'] ?>)" class="unlike-link" id="post-unlike-link-<?php echo $post['id'] ?>" ><?php echo __('POST_UNLIKE_LINK'); ?></a>
                <a href="javascript:;" onclick="Like.initPostLike(<?php echo $post['id'] ?>)" class="like-link hidden" id="post-like-link-<?php echo $post['id'] ?>" ><?php echo __('POST_LIKE_LINK'); ?></a>
                <?php
            }
            if ($dislikeEnable == "1") {
                
                if (!$has_disliked) {
                    ?>
                    &#183; <a href="javascript:;" onclick="Dislike.initPostDislike(<?php echo $post['id'] ?>)" class="dislike-link" id="post-dislike-link-<?php echo $post['id'] ?>" ><?php echo __('POST_DISLIKE_LINK'); ?></a>
                    <a href="javascript:;" onclick="Dislike.initPostUndislike(<?php echo $post['id'] ?>)" class="undislike-link hidden" id="post-undislike-link-<?php echo $post['id'] ?>"><?php echo __('POST_UNDISLIKE_LINK'); ?></a>
                <?php } else { ?>
                    &#183; <a href="javascript:;" onclick="Dislike.initPostUndislike(<?php echo $post['id'] ?>)" class="undislike-link" id="post-undislike-link-<?php echo $post['id'] ?>" ><?php echo __('POST_UNDISLIKE_LINK'); ?></a>
                    <a href="javascript:;" onclick="Dislike.initPostDislike(<?php echo $post['id'] ?>)" class="dislike-link hidden" id="post-dislike-link-<?php echo $post['id'] ?>" ><?php echo __('POST_DISLIKE_LINK'); ?></a>
                    <?php
                }
         }
        }
        if ($post['private'] == '1') {
            ?>
            <br /><?php echo __('POST_PRIVATE');
        }
        ?>
    </div>
<?php if (isset($post['likes'])) { 
    // Affichage en mode Single Post.
    foreach ($post['likes']['data'] as $key => $like) {
        $modifier = ($key == 0) ? '' : ' hidden'; ?>
        <div id="post-like-<?php echo $post['id'] ?>-<?php echo $key ?>" class="post-like post-like-attachment-<?php echo $key.$modifier; ?>" style="min-height: 16px;  width: 370px;">
     
   <?php 
            $name = array();
            $has_liked = false;
            // On Range des utilisateur pour pouvoir mieux les afficher.
            foreach (array_unique($post['likes']['data'][$key], SORT_REGULAR) as $like) {
                if($like['like_user_id'] == User_Model::$auth_data['id']){ ?>
                    <span id="like-it-<?php echo $key; ?>"class="hidden"></span>
          <?php }
                $like_user_url = Config::URL_ROOT . Routes::getPage('student', array('username' => $like['username']));
                if ($like['username'] != User_Model::$auth_data['username'])
                    $name[] = '<a href="' . $like_user_url . '" class="post-comment-username">' . htmlspecialchars($like['firstname'] . ' ' . $like['lastname']) . '</a>';
                else
                    $has_liked = true;
            }
            // On compte combient ils sont
            $last = count($name);
            echo '<span id="like-last-'.$post['id'].'-'.$key.'" class="hidden">'.$last.'</span>';
            
            $separator = '';
            if($last == 1)
                $separator = ' '.__('POST_LIKE_LASTSEP');
            else if($last > 1)
                $separator = __('POST_LIKE_SEPARATOR');
            if($has_liked){
                // On le met en premier !
                $string = '<span id="new-like-container-'.$post['id'].'-'.$key.'" class="">'.__('POST_LIKE_USER').$separator.'</span>';
                array_unshift($name, $string);
            } else {
                $string = '<span id="new-like-container-'.$post['id'].'-'.$key.'" class="hidden">'.__('POST_LIKE_USER').$separator.'</span>';
                array_unshift($name, $string);
            }
            $last = ($has_liked) ? $last + 1 : $last;
            // On fait de belle phrase !
            if (($last > 1 && !$has_liked) || ($last > 2 && $has_liked)){
                $name[$last - 1] .= ' ' . __('POST_LIKE_LASTSEP') . ' ' . array_pop($name);
                unset($name[$last--]);
                for($i = 1; $i < $last; $i++)
                    $name[$i] .= ',';
            }
            // Rendering !
            $stringNb = __('POST_LIKE_CONJ');
            $modificateur = ($has_liked) ? __('POST_LIKE_END_LIKE').'<span id="like-grammar-'.$post['id'].'-'.$key.'">'.$stringNb[0].'</span> '.__('POST_LIKE_END_THIS') :
                                           __('POST_LIKE_END_LIKE').'<span id="like-grammar-'.$post['id'].'-'.$key.'">'.(($last > 1)?$stringNb[1]:'').'</span> '.__('POST_LIKE_END_THIS');
            switch ($last):
                case 0:
                    echo implode(' ', $name);
                    break;
                case 1:
                    echo implode(' ', $name).' '.$modificateur;
                    break;
                case 2:
                    echo implode(' ', $name).' '.$modificateur;
                    break;
                default: ?>
                    <span id="like-show-short-<?php echo $post['id'] ?>-<?php echo $key ?>"><?php echo implode(' ', array_slice($name, 0, ($has_liked) ? Config::LIKE_DISPLAYED : Config::LIKE_DISPLAYED +1)) . ' ' . __('POST_LIKE_LASTSEP') ?>
                        <a href="javascript:;"  onclick="Like.showAll(<?php echo $post['id']; ?>)"><?php echo (($last > Config::LIKE_DISPLAYED) ? ($last-Config::LIKE_DISPLAYED+1).' ': '').__('POST_LIKE_OTHER_'.(($last > 1)?'PLURAL':'SING')) ; ?></a> <?php echo $modificateur?></span>
                    <span class="hidden" id="like-show-all-<?php echo $post['id']; ?>-<?php echo $key ?>"><?php echo implode(' ', $name) . ' ' . $modificateur; ?></span>       
              <?php break;
            endswitch;
            unset($name);
        ?>
        </div>
    <?php }  ?>      
<?php }?>
    <?php $conj = __('POST_LIKE_CONJ'); ?>
    <div id="post-like-<?php echo $post['id'] ?>-all"class="post-like hidden" style="min-height: 16px; width: 370px;">
        <span class="hidden like-last">0</span>
        <?php echo __('POST_LIKE_USER') ?> <?php echo __('POST_LIKE_END_LIKE').$conj[0].' '.__('POST_LIKE_END_THIS'); ?>
    </div>
        <!-- Dislike -->
        <?php if (isset($post['dislikes'])) { 
    // Affichage en mode Single Post.
    foreach ($post['dislikes']['data'] as $key => $dislike) {
        $modifier = ($key == 0) ? '' : ' hidden'; ?>
        <div id="post-dislike-<?php echo $post['id'] ?>-<?php echo $key ?>" class="post-dislike post-dislike-attachment-<?php echo $key.$modifier; ?>" style="min-height: 16px;  width: 370px;">
     
   <?php 
            $name = array();
            $has_disliked = false;
            // On Range des utilisateur pour pouvoir mieux les afficher.
            foreach (array_unique($post['dislikes']['data'][$key], SORT_REGULAR) as $dislike) {
                if($dislike['dislike_user_id'] == User_Model::$auth_data['id']){ ?>
                    <span id="dislike-it-<?php echo $key; ?>" class="hidden"></span>
          <?php }
                $dislike_user_url = Config::URL_ROOT . Routes::getPage('student', array('username' => $dislike['username']));
                if ($dislike['username'] != User_Model::$auth_data['username'])
                    $name[] = '<a href="' . $dislike_user_url . '" class="post-comment-username">' . htmlspecialchars($dislike['firstname'] . ' ' . $dislike['lastname']) . '</a>';
                else
                    $has_disliked = true;
            }
            // On compte combient ils sont
            $last = count($name);
            echo '<span id="dislike-last-'.$post['id'].'-'.$key.'" class="hidden">'.$last.'</span>';
            
            $separator = '';
            if($last == 1)
                $separator = ' '.__('POST_DISLIKE_LASTSEP');
            else if($last > 1)
                $separator = __('POST_DISLIKE_SEPARATOR');
            if($has_disliked){
                // On le met en premier !
                $string = '<span id="new-dislike-container-'.$post['id'].'-'.$key.'" class="">'.__('POST_DISLIKE_USER').$separator.'</span>';
                array_unshift($name, $string);
            } else {
                $string = '<span id="new-dislike-container-'.$post['id'].'-'.$key.'" class="hidden">'.__('POST_DISLIKE_USER').$separator.'</span>';
                array_unshift($name, $string);
            }
            $last = ($has_disliked) ? $last + 1 : $last;
            // On fait de belle phrase !
            if (($last > 1 && !$has_disliked) || ($last > 2 && $has_disliked)){
                $name[$last - 1] .= ' ' . __('POST_DISLIKE_LASTSEP') . ' ' . array_pop($name);
                unset($name[$last--]);
                for($i = 1; $i < $last; $i++)
                    $name[$i] .= ',';
            }
            // Rendering !
            $stringNb = __('POST_DISLIKE_CONJ');
            $modificateur = ($has_disliked) ? __('POST_DISLIKE_END_DISLIKE').'<span id="dislike-grammar-'.$post['id'].'-'.$key.'">'.$stringNb[0].'</span> '.__('POST_DISLIKE_END_THIS') :
                                           __('POST_DISLIKE_END_DISLIKE').'<span id="dislike-grammar-'.$post['id'].'-'.$key.'">'.(($last > 1)?$stringNb[1]:'').'</span> '.__('POST_DISLIKE_END_THIS');
            switch ($last):
                case 0:
                    echo implode(' ', $name);
                    break;
                case 1:
                    echo implode(' ', $name).' '.$modificateur;
                    break;
                case 2:
                    echo implode(' ', $name).' '.$modificateur;
                    break;
                default: ?>
                    <span id="dislike-show-short-<?php echo $post['id'] ?>-<?php echo $key ?>"><?php echo implode(' ', array_slice($name, 0, ($has_disliked) ? Config::DISLIKE_DISPLAYED : Config::DISLIKE_DISPLAYED +1)) . ' ' . __('POST_DISLIKE_LASTSEP') ?>
                        <a href="javascript:;"  onclick="Dislike.showAll(<?php echo $post['id']; ?>)"><?php echo (($last > Config::DISLIKE_DISPLAYED) ? ($last-Config::DISLIKE_DISPLAYED+1).' ': '').__('POST_DISLIKE_OTHER_'.(($last > 1)?'PLURAL':'SING')) ; ?></a> <?php echo $modificateur?></span>
                    <span class="hidden" id="dislike-show-all-<?php echo $post['id']; ?>-<?php echo $key ?>"><?php echo implode(' ', $name) . ' ' . $modificateur; ?></span>       
              <?php break;
            endswitch;
            unset($name);
        ?>
        </div>
    <?php }  ?>      
<?php }?>

        
    <?php $conj = __('POST_DISLIKE_CONJ'); ?>
    <div id="post-dislike-<?php echo $post['id'] ?>-all" class="post-dislike hidden" style="min-height: 16px; width: 370px;">
        <span class="hidden dislike-last">0</span>
        <?php echo __('POST_DISLIKE_USER') ?> <?php echo __('POST_DISLIKE_END_DISLIKE').$conj[0].' '.__('POST_DISLIKE_END_THIS'); ?>
    </div>
        
        
        
        
        <!-- fin dislike -->
    <!--  COMMENTS  -->
    <div class="post-comments">
        <?php
        if (!isset($post['comments']) || !$is_logged)
            $post['comments'] = array();
        $nb_comments = count($post['comments']);
        $n = 0;
        $comment_hidden = false;
        $comments_at_the_beginning = floor(Config::COMMENTS_PER_POST / 2);
        $comments_at_the_end = Config::COMMENTS_PER_POST - $comments_at_the_beginning;
        foreach ($post['comments'] as $comment) {
            $n++;
            /* Cas ou il y a Trop de comment. */
            if ($nb_comments > Config::COMMENTS_PER_POST && !isset($one_post)) {
                if ($n == $comments_at_the_beginning + 1) {
                    $comment_hidden = true;
                    ?>
                    <div id="post-<?php echo $post['id']; ?>-comment-show-all" class="post-comment">
                        <a href="javascript:;" onclick="Comment.showAll(<?php echo $post['id']; ?>)"><?php echo __('POST_COMMENT_SHOW_ALL', array('nb' => $nb_comments)); ?></a>
                    </div>
                    <?php
                } else if ($n == $nb_comments - $comments_at_the_end + 1) {
                    $comment_hidden = false;
                }
            }
            ?>
            <div id="post-comment-<?php echo $comment['id']; ?>" class="post-comment<?php
        if ($comment_hidden)
            echo ' hidden';
        if (isset($one_post))
            echo ' post-comment-attachment' . (isset($comment['attachment_id']) ? $comment['attachment_id'] . ' hidden' : '0');
            ?>">
                     <?php $comment_user_url = Config::URL_ROOT . Routes::getPage('student', array('username' => $comment['username'])); ?>     
                <a href="<?php echo $comment_user_url; ?>" class="avatar"><img src="<?php echo $comment['avatar_url']; ?>" alt="" /></a>
                <?php
                // Post delete button
                if (($is_logged && $username == $comment['username'])
                        || $is_admin
                        || (isset($post['group_id']) && isset($groups_auth)
                        && isset($groups_auth[(int) $post['group_id']])) && $groups_auth[(int) $post['group_id']]['admin']) {
                    ?>
                    <a href="<?php echo Config::URL_ROOT . Routes::getPage('post_comment_delete', array('id' => $comment['id'])); ?>" class="post-comment-delete">x</a>
                    <?php
                }
                ?>
                <div class="post-comment-message">
                    <a href="<?php echo $comment_user_url; ?>" class="post-comment-username"><?php echo htmlspecialchars($comment['firstname'] . ' ' . $comment['lastname']); ?></a>
                    <?php echo Text::inHTML($comment['message']); ?>
                    <div class="post-comment-info">
                        <?php echo Date::easy((int) $comment['time']);
                        /* In Comment Likes */
                            $has_liked = (empty($comment['user_liked'])) ? false : in_array(User_Model::$auth_data['id'], $comment['user_liked'], true);
                            if (!$has_liked) { // Si la personne n'aime pas encore, on affiche "J'aime". ?>
                             &#183; <a href="javascript:;" onclick="Like.initPostComLike(<?php echo $post['id'] ?>, <?php echo $comment['id'] ?>)" id="post-com-like-link-<?php echo $comment['id'] ?>" title=""><?php echo __('POST_LIKE_LINK'); ?></a>
                                    <a href="javascript:;" onclick="Like.initPostComUnlike(<?php echo $post['id'] ?>, <?php echo $comment['id'] ?>)" class="hidden" id="post-com-unlike-link-<?php echo $comment['id'] ?>"><?php echo __('POST_UNLIKE_LINK'); ?></a>
                            <?php } else { // Si la personne a "Aimer" alors on affiche Je n'aime plus. ?>
                             &#183; <a href="javascript:;" onclick="Like.initPostComUnlike(<?php echo $post['id'] ?>, <?php echo $comment['id'] ?>)" id="post-com-unlike-link-<?php echo $comment['id'] ?>"><?php echo __('POST_UNLIKE_LINK'); ?></a>
                                    <a href="javascript:;" onclick="Like.initPostComLike(<?php echo $post['id'] ?>, <?php echo $comment['id'] ?>)" class="hidden" id="post-com-like-link-<?php echo $comment['id'] ?>"><?php echo __('POST_LIKE_LINK'); ?></a>
                            <?php } ?>&#183;
                        <?php
                        if (empty($comment['user_liked'])){ ?>
                            <a name="<?php echo $comment['id'] ?>" id="post-com-like-new-<?php echo $comment['id'] ?>" class="inline-like hidden has-value likeTooltips" title="Vous"><span id="post-com-like-val-<? echo $comment['id'] ?>">
                                 0</span> <?php echo __('POST_LIKE_STRING'); ?></a>
                            <div id="post-com-like-all-<?php echo $comment['id']; ?>" class="hidden-like-box hidden">Vous</div>
                  <?php } else {
                            $nb = count(array_unique($comment['user_liked'], SORT_NUMERIC));
                            // On compte le nombre personne qui Aime ce comment. 
                            $string = ($nb < 2) ? 'hidden' : ''; ?>
                            <?php 
                            $name = array();
                            foreach ($comment['like'] as $key => $comment_like) {
                                if($comment['like'][$key]['username'] ==  User_Model::$auth_data['username'])
                                    $name[] = __('POST_LIKE_USER');
                                else{
                                    $name[] = htmlspecialchars($comment_like['firstname'] . ' ' . $comment_like['lastname']);
                                }
                            } ?>
                            <a name="<?php echo $comment['id'] ?>" id="post-com-like-new-<?php echo $comment['id'] ?>" class="inline-like has-value likeTooltips" title="<?php echo implode('<br />', $name); ?>"><span id="post-com-like-val-<? echo $comment['id'] ?>">
                            <?php echo $nb ?></span> <?php echo __('POST_LIKE_STRING'); ?><span id="like-com-conj-<?php echo $comment['id'] ?>" class="<?php echo $string; ?>"><?php echo __('POST_LIKE_PLURAL_CONJ'); ?></span></a>
                    <?php } ?>
                        </div>
                    </div>
                </div>
                <?php }
                // Dans le cas ou il est Etudiant, alors on affiche le champs de reponse.
            if ($is_student) {
                ?>
                <form action="<?php echo Config::URL_ROOT . Routes::getPage('post_comment', array('id' => $post['id'])); ?>" method="post" class="post-comment-write">
                    <span class="avatar hidden"><img src="<?php echo $avatar_url; ?>" alt="<?php echo $firstname . ' ' . $lastname; ?>" /></span>
                    <div class="post-comment-write-message hidden">
                        <textarea name="comment" rows="1" cols="50"></textarea>
                        <input type="submit" value="<?php echo __('POST_COMMENT_SUBMIT'); ?>" />
                    </div>
                    <div class="post-comment-write-placeholder" onmouseup="Comment.write(<?php echo $post['id']; ?>);"><?php echo __('POST_COMMENT_PLACEHOLDER'); ?></div>
                </form>
        <?php } ?>
    </div>
  </div>
</div>