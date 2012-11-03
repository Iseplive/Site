<h2 style="width:100%;border-bottom:solid #020022 2px;"><?php echo __('ADMIN_LOGOBDE')?></h2><br/>
<div id="logoBde" style="width:100%">
	<form id="publish-form" action="?" method="post" enctype="multipart/form-data"  >
		<fieldset id="publish-stock-attachment-photo" class="publish-attachment">
			<legend><img src="<?php echo Config::URL_STATIC; ?>images/icons/attachment_photo.png" alt="" class="icon" /> <?php echo __('ADMIN_CHANGE_LOGOBDE'); ?></legend>
			<?php echo __('PUBLISH_ATTACHMENT_SEND'); ?> <input type="file" name="logo"  /><br />
			<span class="publish-attachment-info"><?php echo __('PUBLISH_ATTACHMENT_PHOTO_INFO', array('size' => File::humanReadableSize(Config::UPLOAD_MAX_SIZE_PHOTO))); ?></span>
			<input type="submit" id="publish-submit" value="<?php echo __('PUBLISH_SUBMIT'); ?>" />
		</fieldset>
	</form>	
</div>

<h2 style="width:100%;border-bottom:solid #020022 2px;"><?php echo __('ADMIN_ANNUAIRE')?></h2><br/>
