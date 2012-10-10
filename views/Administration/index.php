<!--Page ajouté par Philippe !-->
<?php if($is_logged){ ?>
	<?php// if($is_admin){ ?>
		<?php //if($username=="pvaz"){ 
		?>

		<div id="headermedia">
			<nav>
				<a href="javascript:;" onclick="Admin.navAdminChange(1);"><?php echo __('ADMIN_UPDATE'); ?></a>
				<a href="javascript:;" ><?php echo __('ADMIN_ANNUAIRE'); ?></a>
				<a href="javascript:;" onclick="Admin.navAdminChange(3);"><?php echo __('ADMIN_ISEPDOR'); ?></a>
				<a href="javascript:;" onclick="Admin.navAdminChange(4);"><?php echo __('ADMIN_CAMPAGNE'); ?></a>
				<a href="javascript:;" onclick="Admin.navAdminChange(5);"><?php echo __('ADMIN_ADMINISTRATEUR'); ?></a>
			</nav>
			<br/>
		</div>
		<div id="updatestudent" style="position:relative;left:5%;">
			
			<span id="success" class="hidden"><br/><img alt="" src="<?php echo Config::URL_STATIC."images/icons/add.png";?>"/> L'upload a &#233;t&#233; r&#233;alis&#233; avec succ&#232;s!<br/><br/></span>
			
			<?php echo __('ADMIN_HELP');?>
			<br/>
			<img src=<?php echo Config::URL_STATIC."images/others/helpexcel.png";?> alt="" />
			<br/><br/>
			<form action="?" method="post" enctype="multipart/form-data">
				<?php echo __('ADMIN_TXTENVOIE') ;?>
				<input type="file" name="uploadzip" />
				 <input type="submit" value="<?php echo __('ADMIN_ENVOYER'); ?>"/>
			</form>
		</div>
		<div id="isepdor" class="hidden" >
		
			<div id="isepdornavbar"  style="position:relative;left:30%;">
				<a href="javascript:;" onclick="Admin.isepdornav(1);"><?php echo __('ADMIN_ISEPDOR_NAV1'); ?></a>
				<a href="javascript:;" onclick="Admin.isepdornav(2);"><?php echo __('ADMIN_ISEPDOR_NAV2'); ?></a>
				<a href="javascript:;" onclick="Admin.isepdornav(3);"><?php echo __('ADMIN_ISEPDOR_NAV3'); ?></a>
				<a href="javascript:;" onclick="Admin.isepdornav(4);"><?php echo __('ADMIN_ISEPDOR_NAV4'); ?></a>
				<br/><br/><br/>
			</div>
			
			
			<span id="isepdorcat" style="position:relative;left:40%;">
				<h3><?php echo __('ADMIN_ISEPDOR_CATEGORIE'); ?></h3> <br/>
				<?php foreach($questions as $question){
						echo $question['extra'].'&nbsp;&nbsp;'.$question['questions'].'&nbsp;&nbsp;'.$question['position'].'&nbsp;&nbsp;'.$question["type"].'<br/>';
				 } ?>
				 <br/>
				 <a href="javascript:;" onclick="Admin.modif(1);"><?php echo __('ADMIN_ISEPDOR_MODIFIER'); ?>
					<img alt="" src="<?php echo Config::URL_STATIC."images/icons/add.png";?>"/>
				</a>
			</span>
			
			
			<span id="isepdormodif" class="hidden">
				<form action="?" method="post" id="form_isepdor">
					<?php $i=0;$count=count($questions);
					foreach($questions as $question){ ?>
						<span id="post_isepdor<?php echo $i; ?>">
						
									<a href="javascript:;" onclick="if(confirm('<?php echo __('ADMIN_ISEPDOR_CONFIRM');?>')){Admin.deletecat(<?php echo $i; ?>,<?php echo $question['id']; ?>,1);}">
										<img alt="" src="<?php echo Config::URL_STATIC."images/icons/delete.png";?>"/>
									</a>
									
									<select name="extra<?php echo $i;?>">
										<option value="<?php echo $question['extra'];?>"><?php echo $question['extra'];?></option>
										<option value=""></option>
										<option value="soiree"><?php echo __('ADMIN_ISEPDOR_TEXT5');?></option>
										<option value="<?php echo $promo;?>"><?php echo $promo;?></option>
										<option value="<?php echo $promo-1;?>"><?php echo $promo-1;?></option>
										<option value="<?php echo $promo-2;?>"><?php echo $promo-2;?></option>
										<option value="<?php echo $promo-3;?>"><?php echo $promo-3;?></option>
										<option value="<?php echo $promo-4;?>"><?php echo $promo-4;?></option>
									</select>&nbsp;&nbsp; 
									
									<input type="text" value="<?php echo $question['questions']; ?>" name="quest<?php echo $i;?>"/>&nbsp;&nbsp;
									
									<select id="select<?php echo $i;?>" name="position<?php echo $i;?>">
										<option value="<?php echo $question['position'];?>"><?php echo $question['position'];?></option>
										<?php for ($a=1;$a<=$count;$a++){ ?>
											<option value="<?php echo $a;?>"><?php echo $a;?></option>
										<?php } ?>
									</select>&nbsp;&nbsp;
									
									<?php 	
											$type=explode(',',$question["type"]);
											$tab=array("students","associations","employees","events");
											$result=array_intersect($type,$tab);
											if(in_array("students",$result)){
												?><input type="checkbox" name="students<?php echo $i;?>" checked="checked"/><?php
											}
											else{
												?><input type="checkbox" name="students<?php echo $i;?>" /><?php
											}
											echo __('ADMIN_ISEPDOR_TEXT1'); 
											if(in_array("events",$result)){
												?><input type="checkbox" name="events<?php echo $i;?>" checked="checked"/><?php
											}
											else{
												?><input type="checkbox" name="events<?php echo $i;?>" /><?php
											}
											echo __('ADMIN_ISEPDOR_TEXT2');
											if(in_array("students",$result)){
												?><input type="checkbox" name="associations<?php echo $i;?>" checked="checked"/><?php
											}
											else{
												?><input type="checkbox" name="associations<?php echo $i;?>" /><?php
											}
											echo __('ADMIN_ISEPDOR_TEXT3');
											if(in_array("employees",$result)){
												?><input type="checkbox" name="employees<?php echo $i;?>" checked="checked"/><?php
											}
											else{
												?><input type="checkbox" name="employees<?php echo $i;?>" /><?php
											}
											echo __('ADMIN_ISEPDOR_TEXT4');
									?>
									<input type="hidden" value="<?php echo $question['id']; ?>" name="id<?php echo $i;?>"/>
									<br/><br/>
							</span>
						<?php 	$i++;
							} ?>
					<input type="hidden" value="<?php echo $i; ?>" name="nbquestion" id="nbquestion"/>
					<input type="submit" value="<?php echo __('ADMIN_ENVOYER'); ?>"/><br/><br/>
				</form>
				<a href="javascript:;" onclick="Admin.ajout(<?php echo $promo;?>,1);"><?php echo __('ADMIN_ISEPDOR_AJOUTER'); ?>
					<img alt="" src="<?php echo Config::URL_STATIC."images/icons/add.png";?>"/>
				</a><br/>
			</span>
			
			
			<span id="tableadmin" class="hidden" style="position:relative;left:40%;">
				<span id="table_admin_view">
					<?php foreach($employees as $admin){ 
							echo $admin['lastname'].' '.$admin['firstname'].'<br/>';
					 } ?>
					 <br/>
					 <a href="javascript:;" onclick="Admin.modif(2);"><?php echo __('ADMIN_ISEPDOR_MODIFIER'); ?>
						<img alt="" src="<?php echo Config::URL_STATIC."images/icons/add.png";?>"/>
					</a>
				</span>
				
				<span id="table_admin_modif" class="hidden">
					<form action="?" method="post" id="form_table_admin">
						<?php 	$a=0;$count2=count($employees);
								foreach($employees as $admin){ ?>
									<span id="post_table_admin<?php echo $a; ?>">
										<a href="javascript:;" onclick="if(confirm('<?php echo __('ADMIN_ISEPDOR_CONFIRM');?>')){Admin.deletecat(<?php echo $a; ?>,<?php echo $admin['id']; ?>,2);}">
											<img alt="" src="<?php echo Config::URL_STATIC."images/icons/delete.png";?>"/>
										</a>
										<input type="text" value="<?php echo $admin['lastname']; ?>" name="lastname<?php echo $a;?>"/>
										<input type="text" value="<?php echo $admin['firstname']; ?>" name="firstname<?php echo $a;?>"/>
										<input type="hidden" value="<?php echo $admin['id']; ?>" name="id<?php echo $a;?>"/><br/>
									</span>
						<?php	$a++;
								} ?>
					<input type="hidden" value="<?php echo $count2; ?>" name="nbchamps" id="nbchamps"/>	
					<input type="submit" value="<?php echo __('ADMIN_ENVOYER'); ?>"/><br/><br/>
					</form>
					<a href="javascript:;" onclick="Admin.ajout('',2);"><?php echo __('ADMIN_ISEPDOR_AJOUTER'); ?>
						<img alt="" src="<?php echo Config::URL_STATIC."images/icons/add.png";?>"/>
					</a><br/>
				</span>
				
			</span>
			
			
			<span id="tableevent" class="hidden" style="position:relative;left:40%;">
				<span id="table_event_view">
					<?php foreach($events as $event){ 
								echo $event['name'].' type('. $event['extra'].')<br/>';
					 } ?>
					 <br/>
					 <a href="javascript:;" onclick="Admin.modif(3);"><?php echo __('ADMIN_ISEPDOR_MODIFIER'); ?>
						<img alt="" src="<?php echo Config::URL_STATIC."images/icons/add.png";?>"/>
					</a>
				</span>
				
				<span id="table_event_modif" class="hidden">
					<form action="?" method="post" id="form_table_event">
							<?php 	$b=0;$count3=count($events);
									foreach($events as $event){ ?>
										<span id="post_table_event<?php echo $b; ?>">
											<a href="javascript:;" onclick="if(confirm('<?php echo __('ADMIN_ISEPDOR_CONFIRM');?>')){Admin.deletecat(<?php echo $b; ?>,<?php echo $event['id']; ?>,3);}">
												<img alt="" src="<?php echo Config::URL_STATIC."images/icons/delete.png";?>"/>
											</a>
											<input type="text" value="<?php echo $event['name']; ?>" name="name<?php echo $b;?>"/>
											<?php
												if($event['extra']=="soiree"){
													?><input type="checkbox" name="soiree<?php echo $b;?>" checked="checked"/><?php
												}
												else{
													?><input type="checkbox" name="soiree<?php echo $b;?>" /><?php
												}
												echo __('ADMIN_ISEPDOR_TEXT5');
											?>
											<input type="hidden" value="<?php echo $event['id']; ?>" name="id<?php echo $b;?>"/><br/>
										</span>
							<?php $b++;
								} ?>
						<input type="hidden" value="<?php echo $count3; ?>" name="nbevent" id="nbevent"/>	
						<input type="submit" value="<?php echo __('ADMIN_ENVOYER'); ?>"/><br/><br/>
					</form>
					<a href="javascript:;" onclick="Admin.ajout('',3);"><?php echo __('ADMIN_ISEPDOR_AJOUTER'); ?>
						<img alt="" src="<?php echo Config::URL_STATIC."images/icons/add.png";?>"/>
					</a><br/>
				</span>
			</span>
			
			
			<span id="isepdor_admin" class="hidden">
				<span style="float:right;">
					<strong><?php echo __('ADMIN_ISEPDOR_FIRST')?></strong> <a href="?export=1&type=1" ><img alt="" src="<?php echo Config::URL_STATIC."images/filetypes/db.png";?>"/> <?php echo __('ADMIN_ISEPDOR_EXPORT');?></a><BR/>
					<strong><?php echo __('ADMIN_ISEPDOR_SECOND')?></strong> <a href="?export=1&type=2"><img alt="" src="<?php echo Config::URL_STATIC."images/filetypes/db.png";?>"/> <?php echo __('ADMIN_ISEPDOR_EXPORT');?></a><BR/>
					<a href="?delete_result=1"><img alt="" src="<?php echo Config::URL_STATIC."images/icons/tool.png";?>"/> <strong><?php echo __('ADMIN_ISEPDOR_RESET')?></strong></a>
				</span>
				<span style="float:left;">
					<form action="?" method="post">
						<?php $title0=0;$title=0;?>
						<?php foreach($date as $dates){ 
								if($dates['tour']==1){
									if($title0==0){
										?><h2><?php echo __('ADMIN_ISEPDOR_FIRST')?></h2><?php
										$title0=1;
									}
									if($dates['type']==1){
										?> Du: <input type="text" id="first1" name="first1" value="<?php echo $dates['date'];?>"/><?php
									}
									if($dates['type']==2){
										?> Au: <input type="text" id="first2" name="first2" value="<?php echo $dates['date'];?>"/><br/><?php
									}
								}
								if($dates['tour']==2){
									if($title==0){
										?><br/><h2><?php echo __('ADMIN_ISEPDOR_SECOND')?></h2><?php
										$title=1;
									}
									if($dates['type']==1){
										?> Du: <input type="text"  name="second1" id="second1" value="<?php echo $dates['date'];?>"/><?php
									}
									if($dates['type']==2){
										?> Au: <input type="text" name="second2" id="second2" value="<?php echo $dates['date'];?>"/><br/><?php
									}
								}
							} ?>
						<br/>
						<input type="submit" value="<?php echo __('ADMIN_ENVOYER'); ?>"/><br/><br/>
					</form>
				</span>
			</span>
			
		</div>
		
		
		<div id="campagne" class="hidden">campagne</div>
		
	
		<?php //} ?>
	<?php //} ?>
<?php } ?>