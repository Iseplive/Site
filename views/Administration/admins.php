<h1><?php echo __('ADMIN_ADMINISTRATEUR'); ?></h1>

<div id="admins">
	<?php foreach($admins as $admin){ ?>
		<img id="<?php echo $admin['username']; ?>" alt="" src="<?php echo Config::URL_STATIC."images/icons/delete.png";?>" class="image_icon"/>
		<?php echo $admin['firstname'].' '. $admin['lastname'].'<br/>';?>
	<?php } ?>
	<br/>
</div>
<form action="<?php echo Config::URL_ROOT.Routes::getPage('admin',array("nav"=> "admins")); ?>" method="post" id="form_admins">
	<input type="hidden" name="type" value="students" id="type" />
	<label for="admin_edit_add_admin"><?php echo __('GROUP_EDIT_FORM_ADD_MEMBER'); ?></label>
	<input type="text" size="25" style="margin: 5px;" name="admin_edit_add_admin" id="admin_edit_add_admin" value="" class="autocomplete" autocomplete="off"/>
	
	<span id="error-com" class="error hidden" ><?php echo __('ISEPOR_ERROR_AUTOCOMPLETE'); ?></span>
	<span id="error-nan" class="error hidden" ><?php echo __('ISEPOR_ERROR_NOT_EXIST'); ?></span>
	
	<input type="hidden" name="url" id="admin_edit_add_admin_url" value="<?php echo Config::URL_ROOT.Routes::getPage('autocomplete_isepor'); ?>" />
	<input class="valid" type="hidden" name="valid-students" id="valid" value="" />
	
	<br/><input type="submit" value="<?php echo __('ADMIN_ENVOYER'); ?>"/>
</form>
<br/><br/>