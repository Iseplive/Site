<?php
	$i = 0;
	for($year = $last_promo; $year > $last_promo-5; $year--){
		if($year>=2010){
			$i++;
			?>
			<div class="students-promo" >
				<h2><?php echo __('STUDENTS_PROMO').' '. $year; ?></h2>
				<?php
				if(!isset($students[$year]))
					$students[$year] = array();
				foreach($students[$year] as $student){
				?>
				<a id="<?php echo $student['student_number'];?>" href="<?php echo Config::URL_ROOT.Routes::getPage('student', array('username' => $student['username'])); ?>" onmouseover="Student.showThumb(this,'<?php echo $student['avatar_url'];?>','<?php echo $student['student_number'];?>','<?php echo $student['promo'];?>')" onmouseout="Student.hiddeThumb();">
					<?php echo htmlspecialchars($student['firstname'].' '.$student['lastname']); ?>
				</a><br />
				<?php
				}
				?>
			</div>
			<?php
		}
	}
	?>