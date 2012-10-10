<h1><?php echo __('ADMIN_ADMINISTRATEUR'); ?></h1>

<?php foreach($admins as $admin){ ?>
	<a href="?username=<?php echo $admin['username']; ?>" onclick="if(confirm('<?php echo __('ADMIN_ISEPDOR_CONFIRM');?>')){}">
		<img alt="" src="<?php echo Config::URL_STATIC."images/icons/delete.png";?>"/>
	</a>
	<?php echo $admin['firstname'].' '. $admin['lastname'].'<br/>';?>
<?php } ?>
<br/>
<form action="?" method="post" enctype="multipart/form-data">
	<label for="admin_edit_add_admin"><?php echo __('GROUP_EDIT_FORM_ADD_MEMBER'); ?></label>
	<input type="text" name="" id="admin_edit_add_admin" value="" class="autocomplete" autocomplete="off"/>
	<input type="hidden" name="" id="admin_edit_add_admin_url" value="<?php echo Config::URL_ROOT.Routes::getPage('autocompletion_student_name'); ?>" />
	<input type="submit" value="<?php echo __('ADMIN_ENVOYER'); ?>"/>
</form>