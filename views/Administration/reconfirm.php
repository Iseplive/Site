<h1><?php echo __('ADMIN_RECONFIRM')." ". $user; ?></h1>

<form action="<?php echo Config::URL_ROOT.Routes::getPage('admin',array("nav"=> $url)); ?>" method="post" id="form_admins">
	<label><?php echo __('SIGNIN_PASSWORD'); ?></label><input type="password" name="reconfpassword"/>
</form>