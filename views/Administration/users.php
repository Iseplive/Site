<h1><?php echo __('ADMIN_UPDATE');?></h1>

<div id="updatestudent" >
	<br/>
	<div id="users" style="width:45%;float:left;border-right:solid #020022; 1px;">
		<div style="width:20%;margin:auto"><h2><?php echo __('ADMIN_USERS_TABLE') ;?></h2></div>
		<br/>
		<a href="<?php echo Config::URL_STORAGE.Config::DIR_DATA_ADMIN."template_users.xlsx"; ?>" />
			<img src="<?php echo Config::URL_STATIC."images/filetypes/xls.png";?>" class="image_icon" /> <?php echo __("ADMIN_TEMPLATE_USERS");?>
		</a>
		<br/><br/><br/>
		<form action="?" method="post" enctype="multipart/form-data">
			<?php echo __('ADMIN_TXTENVOIE') ;?>
			<input type="file" name="uploadxml1" />
			 <input type="submit" value="<?php echo __('ADMIN_ENVOYER'); ?>"/>
		</form>
	</div>
	<div id="students" style="width:45%;float:right;">
		<div style="width:20%;margin:auto"><h2><?php echo __('ADMIN_STUDENTS_TABLE') ;?></h2></div>
		<br/>
		<a href="<?php echo Config::URL_STORAGE.Config::DIR_DATA_ADMIN."template_students.xlsx"; ?>" />
			<img src="<?php echo Config::URL_STATIC."images/filetypes/xls.png";?>" class="image_icon" /> <?php echo __("ADMIN_TEMPLATE_STUDENTS");?>
		</a>	
		<br/><br/><br/>
		<form action="?" method="post" enctype="multipart/form-data">
			<?php echo __('ADMIN_TXTENVOIE') ;?>
			<input type="file" name="uploadxml2" />
			 <input type="submit" value="<?php echo __('ADMIN_ENVOYER'); ?>"/>
		</form>
	</div>
	
	<div id="avatar" style="clear:both">
		<br/><br/><br/>
		<form id="publish-form" action="?" method="post" enctype="multipart/form-data"  >
			<fieldset id="publish-stock-attachment-photo" class="publish-attachment">
				<legend><img src="<?php echo Config::URL_STATIC; ?>images/icons/attachment_photo.png" alt="" class="icon" /> <?php echo __('STUDENT_EDIT_FORM_AVATAR'); ?></legend>
				<?php echo __('PUBLISH_ATTACHMENT_SEND'); ?> <input type="file" name="avatar_photo[]" multiple /><br />
				<span class="publish-attachment-info"><?php echo __('PUBLISH_ATTACHMENT_PHOTO_INFO', array('size' => File::humanReadableSize(Config::UPLOAD_MAX_SIZE_PHOTO))); ?></span>
				<input type="submit" id="publish-submit" value="<?php echo __('PUBLISH_SUBMIT'); ?>" />
			</fieldset>
		</form>		
	</div>
	<div id="publish-errors" style="clear:both">
		<?php
			if(isset($fail2) && count($fail2)>0 ){
				echo __("ADMIN_ERROR_AVATAR")."<br/><br/>";
				echo "<span>";
				foreach($fail2 as $avatar){
					echo $avatar." &nbsp;&nbsp;&nbsp;";
				}
				echo "</span>";
			}
			if(isset($fail) && count($fail)>0){
				echo __("ADMIN_ERROR_EXIST")."<br/><br/>";
				echo "<span>";
				foreach($fail as $student){
					echo $student." &nbsp;&nbsp;&nbsp;";
				}
				echo "</span>";
			}
		?>
	</div>
</div>