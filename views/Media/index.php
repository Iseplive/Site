<!--Page ajouté par Philippe !-->
<?php if($is_logged){ ?>
	<div id="liste">
		<div id="headermedia">
			<nav>
				<a href="javascript:;" id="video" onclick="Media.navMediaChange(1);"><?php echo __('MEDIA_VIDEO'); ?></a>
				<a href="javascript:;" id="photos" onclick="Media.navMediaChange(2);"><?php echo __('MEDIA_PHOTOS'); ?></a>
				<a href="javascript:;" id="journaux" onclick="Media.navMediaChange(3);"><?php echo __('MEDIA_JOURNAUX'); ?></a>
				<a href="javascript:;" id="podcast" onclick="Media.navMediaChange(4);"><?php echo __('MEDIA_PODCAST'); ?></a>
			</nav>
		</div>
		<div id="showlistall" >
			<?php for($i=0;$i<count($annee);$i++){ 
						for($a=0;$a<=count($mediaannee[''.$annee[$i].'']);$a++){
							$id=$mediaannee[''.$annee[$i].''][$a];
							$route=Config::URL_ROOT . Routes::getPage('post', array('id' => $id));
							$title=str_split($mediamessage[$id],35);
							$category=$categorie[$id];
							if($title[1]!=""){ $etc="...";}
							else{ $etc="";}
							$spanline=round(count($mediaannee[''.$annee[$i].''])/3);
							if($category==1){
								$icon="<img src='".Config::URL_STATIC."images/icons/attachment_photo.png' alt='' class='icon' /> ";
								
							}
							elseif($category==2){
								$icon="<img src='".Config::URL_STATIC."images/icons/attachment_video.png' alt='' class='icon' /> ";
								
							}
							elseif($category==3 || $category==10){
								$icon="<img src='".Config::URL_STATIC."images/icons/attachment_journaux.png' alt='' class='icon' /> ";
								
							}
							elseif($category==4){
								$icon="<img src='".Config::URL_STATIC."images/icons/attachment_podcast.png' alt='' class='icon' /> ";
								
							}
							if($id!=0){
								if($a<=$spanline){
									$bloc.=$icon."<a href='".$route."' title=\"".$mediamessage[$id]."\">".$title[0].$etc."</a><br/>";
								}
								elseif($a<=2*$spanline){
									$bloc2.=$icon."<a href='".$route."' title=\"".$mediamessage[$id]."\">".$title[0].$etc."</a><br/>";
								}
								else{
									$bloc3.=$icon."<a href='".$route."' title=\"".$mediamessage[$id]."\">".$title[0].$etc."</a><br/>";
								}
							}
						} ?>
					<div  id="<?php echo $annee[$i];?>" style="clear:both;">
						<br/><br/><h2 style="width:1200px;border-bottom:solid #020022 2px;"><?php echo $annee[$i];?></h2><br/>
						<div style="clear:both;position:relative;left:70px">
						<span style="width:400px;float:left;"><?php echo $bloc;?></span>
						<span style="width:400px;float:left;"><?php echo $bloc2;?></span>
						<span style="width:400px;float:left;"><?php echo $bloc3;?></span>
						</div>
					</div>
			<?php 
					unset($bloc);
					unset($bloc2);
					unset($bloc3);
				} ?>
		</div>
		<div id="showlistvideo" class="hidden">
				<?php for($i=0;$i<count($annee2);$i++){ 
						for($a=0;$a<=count($mediaannee2[''.$annee2[$i].'']);$a++){
							$id=$mediaannee2[''.$annee2[$i].''][$a];
							$route=Config::URL_ROOT . Routes::getPage('post', array('id' => $id));
							$title=str_split($mediamessage2[$id],35);
							$category=$categorie[$id];
							if($title[1]!=""){ $etc="...";}
							else{ $etc="";}
							$spanline=round(count($mediaannee2[''.$annee2[$i].''])/3);
								$icon="<img src='".Config::URL_STATIC."images/icons/attachment_video.png' alt='' class='icon' /> ";
								if($id!=0){
									if($a<=$spanline){
										$bloc.=$icon."<a href='".$route."' title=\"".$mediamessage2[$id]."\">".$title[0].$etc."</a><br/>";
									}
									elseif($a<=2*$spanline){
										$bloc2.=$icon."<a href='".$route."' title=\"".$mediamessage2[$id]."\">".$title[0].$etc."</a><br/>";
									}
									else{
										$bloc3.=$icon."<a href='".$route."' title=\"".$mediamessage2[$id]."\">".$title[0].$etc."</a><br/>";
									}
								}
							
						} ?>
					<div  id="<?php echo $annee2[$i];?>" style="clear:both;">
						<br/><br/><h2 style="width:1200px;border-bottom:solid #020022 2px;"><?php echo $annee2[$i];?></h2><br/>
						<div style="clear:both;position:relative;left:70px">
						<span style="width:400px;float:left;"><?php echo $bloc;?></span>
						<span style="width:400px;float:left;"><?php echo $bloc2;?></span>
						<span style="width:400px;float:left;"><?php echo $bloc3;?></span>
						</div>
					</div>
			<?php 
					unset($bloc);
					unset($bloc2);
					unset($bloc3);
				} ?>
		</div>
		<div id="showlistphotos" class="hidden">
			<?php for($i=0;$i<count($annee1);$i++){ 
						for($a=0;$a<=count($mediaannee1[''.$annee1[$i].'']);$a++){
							$id=$mediaannee1[''.$annee1[$i].''][$a];
							$route=Config::URL_ROOT . Routes::getPage('post', array('id' => $id));
							$title=str_split($mediamessage1[$id],35);
							$category=$categorie[$id];
							if($title[1]!=""){ $etc="...";}
							else{ $etc="";}
							$spanline=round(count($mediaannee1[''.$annee1[$i].''])/3);
								$icon="<img src='".Config::URL_STATIC."images/icons/attachment_photo.png' alt='' class='icon' /> ";
								if($id!=0){
									if($a<=$spanline){
										$bloc.=$icon."<a href='".$route."' title=\"".$mediamessage1[$id]."\">".$title[0].$etc."</a><br/>";
									}
									elseif($a<=2*$spanline){
										$bloc2.=$icon."<a href='".$route."' title=\"".$mediamessage1[$id]."\">".$title[0].$etc."</a><br/>";
									}
									else{
										$bloc3.=$icon."<a href='".$route."' title=\"".$mediamessage1[$id]."\">".$title[0].$etc."</a><br/>";
									}
								}
							
						} ?>
					
					<div  id="<?php echo $annee1[$i];?>" style="clear:both;">
						<br/><br/><h2 style="width:1200px;border-bottom:solid #020022 2px;"><?php echo $annee1[$i];?></h2><br/>
						<div style="clear:both;position:relative;left:70px">
						<span style="width:400px;float:left;"><?php echo $bloc;?></span>
						<span style="width:400px;float:left;"><?php echo $bloc2;?></span>
						<span style="width:400px;float:left;"><?php echo $bloc3;?></span>
						</div>
					</div>
			<?php 
					unset($bloc);
					unset($bloc2);
					unset($bloc3);
				} ?>
		</div>
		<div id="showlistjournaux" class="hidden">
				<?php for($i=0;$i<count($annee3);$i++){ 
						for($a=0;$a<=count($mediaannee3[''.$annee3[$i].'']);$a++){
							$id=$mediaannee3[''.$annee3[$i].''][$a];
							$route=Config::URL_ROOT . Routes::getPage('post', array('id' => $id));
							$title=str_split($mediamessage3[$id],35);
							$category=$categorie[$id];
							if($title[1]!=""){ $etc="...";}
							else{ $etc="";}
							$spanline=round(count($mediaannee3[''.$annee3[$i].''])/3);
								$icon="<img src='".Config::URL_STATIC."images/icons/attachment_journaux.png' alt='' class='icon' /> ";
								if($id!=0){
									if($a<=$spanline){
										$bloc.=$icon."<a href='".$route."' title=\"".$mediamessage3[$id]."\">".$title[0].$etc."</a><br/>";
									}
									elseif($a<=2*$spanline){
										$bloc2.=$icon."<a href='".$route."' title=\"".$mediamessage3[$id]."\">".$title[0].$etc."</a><br/>";
									}
									else{
										$bloc3.=$icon."<a href='".$route."' title=\"".$mediamessage3[$id]."\">".$title[0].$etc."</a><br/>";
									}
								}
							
						} ?>
					<div  id="<?php echo $annee3[$i];?>" style="clear:both;">
						<br/><br/><h2 style="width:1200px;border-bottom:solid #020022 2px;"><?php echo $annee3[$i];?></h2><br/>
						<div style="clear:both;position:relative;left:70px">
						<span style="width:400px;float:left;"><?php echo $bloc;?></span>
						<span style="width:400px;float:left;"><?php echo $bloc2;?></span>
						<span style="width:400px;float:left;"><?php echo $bloc3;?></span>
						</div>
					</div>
			<?php 
					unset($bloc);
					unset($bloc2);
					unset($bloc3);
				} ?>
		</div>
		<div id="showlistpodcast" class="hidden">
				<?php for($i=0;$i<count($annee4);$i++){ 
						for($a=0;$a<=count($mediaannee4[''.$annee4[$i].'']);$a++){
							$id=$mediaannee4[''.$annee4[$i].''][$a];
							$route=Config::URL_ROOT . Routes::getPage('post', array('id' => $id));
							$title=str_split($mediamessage4[$id],35);
							$category=$categorie[$id];
							if($title[1]!=""){ $etc="...";}
							else{ $etc="";}
							$spanline=round(count($mediaannee4[''.$annee4[$i].''])/3);
								$icon="<img src='".Config::URL_STATIC."images/icons/attachment_podcast.png' alt='' class='icon' /> ";
								if($id!=0){
									if($a<=$spanline){
										$bloc.=$icon."<a href='".$route."' title=\"".$mediamessage4[$id]."\">".$title[0].$etc."</a><br/>";
									}
									elseif($a<=2*$spanline){
										$bloc2.=$icon."<a href='".$route."' title=\"".$mediamessage4[$id]."\">".$title[0].$etc."</a><br/>";
									}
									else{
										$bloc3.=$icon."<a href='".$route."' title=\"".$mediamessage4[$id]."\">".$title[0].$etc."</a><br/>";
									}
								}
							
						} ?>
					<div  id="<?php echo $annee4[$i];?>" style="clear:both;">
						<br/><br/><h2 style="width:1200px;border-bottom:solid #020022 2px;"><?php echo $annee4[$i];?></h2><br/>
						<div style="clear:both;position:relative;left:70px">
						<span style="width:400px;float:left;"><?php echo $bloc;?></span>
						<span style="width:400px;float:left;"><?php echo $bloc2;?></span>
						<span style="width:400px;float:left;"><?php echo $bloc3;?></span>
						</div>
					</div>
			<?php 
					unset($bloc);
					unset($bloc2);
					unset($bloc3);
				} ?>
		</div>
	</div>
<?php } ?>