<div id='adminIsepdorTab' class="hidden">
	<ul>
		<li style="margin-left: 30px;"><?php echo __('ADMIN_ISEPDOR_NAV1'); ?></li>
		<li><?php echo __('ADMIN_ISEPDOR_NAV3'); ?></li>
		<li><?php echo __('ADMIN_ISEPDOR_NAV2'); ?></li>
		<li><?php echo __('ADMIN_ISEPDOR_NAV4'); ?></li>
	</ul>

	<div id="isepdorcat" style="margin:10px;" >
		<a id="addrowCat" style="cursor:pointer;margin-right:10px">
			<img alt="" style="position:relative;top:3px;"src="<?php echo Config::URL_STATIC."images/icons/add.png";?>"/>
			<?php echo __('ADMIN_ISEPDOR_ADDROW')?> 
		</a>
		<a id="delrowCat" style="cursor:pointer;margin-right:10px">
			<img alt="" style="position:relative;top:3px;"src="<?php echo Config::URL_STATIC."images/icons/delete.png";?>"/>
			<?php echo __('ADMIN_ISEPDOR_DELROW')?> 
		</a>
		<a id="saveOrderCat" style="cursor:pointer;float:right;margin-right:10px">
			<img alt="" style="position:relative;top:3px;"src="<?php echo Config::URL_STATIC."images/icons/post.png";?>"/>
			<?php echo __('ADMIN_ISEPDOR_SAVEORDER')?> 
		</a>
		<a id="saveNoOrderCat" style="cursor:pointer;float:right;margin-right:10px" >
			<img alt="" style="position:relative;top:3px;"src="<?php echo Config::URL_STATIC."images/icons/post.png";?>"/>
			<?php echo __('ADMIN_ISEPDOR_SAVENOORDER')?> 
		</a>
		<br/>
		<span id="errorsCat" class="emptyError hidden" ></span><br/>
		<div id="categorieGrid" style="float:left"></div>
		<div id="categorieReorderGrid" style="float:left;margin-left:50px;"></div>
	</div>

	<div id="tableadmin" style="margin:10px;">
		<a  id="addrowEmploy" style="cursor:pointer;margin-right:10px">
			<img alt="" style="position:relative;top:3px;"src="<?php echo Config::URL_STATIC."images/icons/add.png";?>"/>
			<?php echo __('ADMIN_ISEPDOR_ADDROW')?> 
		</a>
		<a  id="delrowEmploy" style="cursor:pointer;margin-right:10px">
			<img alt="" style="position:relative;top:3px;"src="<?php echo Config::URL_STATIC."images/icons/delete.png";?>"/>
			<?php echo __('ADMIN_ISEPDOR_DELROW')?> 
		</a>
		<a id="saveEmploy" style="cursor:pointer;margin-right:10px" >
			<img alt="" style="position:relative;top:3px;"src="<?php echo Config::URL_STATIC."images/icons/post.png";?>"/>
			<?php echo __('USER_EDIT_FORM_SUBMIT')?> 
		</a>
		<br/><br/>
		<span id="errorsEmploy" class="emptyError hidden" ></span><br/>
		<div id="employGrid" ></div>
						
	</div>
	
	<div id="tableevent" style="margin:10px;">
		<a  id="addrowEvent" style="cursor:pointer;margin-right:10px">
			<img alt="" style="position:relative;top:3px;"src="<?php echo Config::URL_STATIC."images/icons/add.png";?>"/>
			<?php echo __('ADMIN_ISEPDOR_ADDROW')?> 
		</a>
		<a  id="delrowEvent" style="cursor:pointer;margin-right:10px">
			<img alt="" style="position:relative;top:3px;"src="<?php echo Config::URL_STATIC."images/icons/delete.png";?>"/>
			<?php echo __('ADMIN_ISEPDOR_DELROW')?> 
		</a>
		<a id="saveEvent" style="cursor:pointer;margin-right:10px" >
			<img alt="" style="position:relative;top:3px;" src="<?php echo Config::URL_STATIC."images/icons/post.png";?>"/>
			<?php echo __('USER_EDIT_FORM_SUBMIT')?> 
		</a>
		<br/><br/>
		<span id="errorsEvent" class="emptyError hidden" ></span><br/>
		<div id="eventGrid" ></div>
	</div>
		
	<div id="isepdor_admin" style="margin:10px;">
		<h2 style="width:100%;border-bottom:solid #020022 2px;"><?php echo __('ADMIN_ISEPDOR_GESTION')?></h2><br/>
		<span style="float:right;">
			<a href="?export=1" ><img alt="" src="<?php echo Config::URL_STATIC."images/filetypes/db.png";?>"/> <?php echo __('ADMIN_ISEPDOR_EXPORT');?></a><BR/><BR/>
			<a href="?delete_result=1"> <strong><?php echo __('ADMIN_ISEPDOR_RESET')?></strong></a><br/><br/>
			<a href="?getDiplome=1"> <strong><?php echo __('ADMIN_ISEPDOR_GETDIPLOME')?></strong></a>
		</span>
		<span >
			<strong><?php echo __('ADMIN_ISEPDOR_FIRST')?>&nbsp;&nbsp;&nbsp; </strong>
			Du: <div  id="first1" class="jqxDate"> </div>
			Au: <div  id="first2" class="jqxDate"> </div><br/><br/><br/><br/>
			
			<strong><?php echo __('ADMIN_ISEPDOR_SECOND')?> &nbsp;&nbsp;&nbsp;</strong>
			Du: <div  id="second1" class="jqxDate"> </div>
			Au: <div  id="second2" class="jqxDate"> </div><br/><br/><br/><br/>
			
			<strong><?php echo __('ADMIN_ISEPDOR_THIRD')?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>
			Du: <div  id="third1" class="jqxDate"> </div>
			Au: <div  id="third2" class="jqxDate"> </div><br/><br/>
			<a id="saveDate" style="cursor:pointer;margin-right:10px" >
				<img alt="" style="position:relative;top:3px;"src="<?php echo Config::URL_STATIC."images/icons/post.png";?>"/>
				<?php echo __('USER_EDIT_FORM_SUBMIT')?> 
			</a><br/><br/>
			<span id="errorsDate" class="emptyError hidden" ></span><br/>
		</span>
		<br/>
		<h2 style="width:100%;border-bottom:solid #020022 2px;"><?php echo __('ADMIN_ISEPDOR_DIPLOME')?></h2><br/>
		<img id="adminCrop" src="<?php echo Config::URL_STORAGE.Config::DIR_DATA_ADMIN."/diplomeIsepDOr9652.png";?>" width="785px" height="555px" />
		<div id="coordMaker" style="width:320px;padding-left:10px;float:right;">
			<form id="" action="?" method="post" enctype="multipart/form-data"  >
				<fieldset id="publish-stock-attachment-photo" class="publish-attachment">
					<legend><img src="<?php echo Config::URL_STATIC; ?>images/icons/attachment_photo.png" alt="" class="icon" /> <?php echo __('ADMIN_CHANGE_DIPLOME'); ?></legend>
					<?php echo __('PUBLISH_ATTACHMENT_SEND'); ?> <input type="file" name="diplome"  /><br />
					<span class="publish-attachment-info"><?php echo __('PUBLISH_ATTACHMENT_PHOTO_INFO', array('size' => File::humanReadableSize(Config::UPLOAD_MAX_SIZE_PHOTO))); ?></span>
					<?php if(isset($form_error) && $form_error == 'size'){ ?>
						<br/><span class="emptyError">
							<?php echo __('USER_EDIT_ERROR_AVATAR', array('size' => File::humanReadableSize(Config::UPLOAD_MAX_SIZE_PHOTO))); ?>
						</span>
					<?php } ?>
					<?php if(isset($form_error) && $form_error == 'width'){ ?>
						<br/><span class="emptyError">
							<?php echo __('ADMIN_ISEPDOR_TOLARGE'); ?>
						</span>
					<?php } ?>
					<input type="submit" id="publish-submit" value="<?php echo __('PUBLISH_SUBMIT'); ?>" />
				</fieldset>
			</form>	
			<br/>
			<div id='diplomeTab' >
				<ul>
					<li style="margin-left: 5px;"><?php echo __('ADMIN_QUESTIONS'); ?></li>
					<li><?php echo __('ADMIN_ISEPDOR_LASTNAME'); ?></li>
					<li><?php echo __('ADMIN_ISEPDOR_BORN'); ?></li>
				</ul>
				<div id="diplomeCat"> </div>
				<div id="diplomeName"> </div>
				<div id="diplomeBirth"> </div>
			</div>
			<a id="saveDiplome" style="cursor:pointer;margin-right:10px,float:right" >
				<img alt="" style="position:relative;top:3px;"src="<?php echo Config::URL_STATIC."images/icons/post.png";?>"/>
				<?php echo __('USER_EDIT_FORM_SUBMIT')?> 
			</a><br/>
		</div>
	</div>
</div>
<input id="pageUrl" type="hidden" value="<?php echo Config::URL_ROOT.Routes::getPage('admin',array("nav"=> "isepdor")); ?>" />