<h1><?php echo __('STUDENTS_TITLE'); ?></h1>

<?php
$i = 0;
$birth_date="2010";
$nb_panels=round(($last_promo-$birth_date)/5,0,PHP_ROUND_HALF_UP);

if($nb_panels>0){
	?>
	<div id="sliderStudent" style="visibility:hidden"></div><br/>	
	<input id="prev" type="hidden" value="0"/>
	<input id="loadPannels" type="hidden" value="1"/>
	<input id="url" type="hidden" value="<?php echo Config::URL_ROOT.Routes::getPage('students_promo');?>"/>
	<?php
}
?>
<div id="sliderContainer" style="visibility:hidden">
<?php
	for($year = $last_promo; $year > $last_promo-5; $year--){
		$i++;
	?>
	<div class="students-promo" >
		<h2><?php echo __('STUDENTS_PROMO'.$i, array('year' => $year)); ?></h2>
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
	?>
</div>
<div id="thumbNailer" class="hidden">
	<span style="float:left;width:70px;"></span>
	<span style="padding-left:5px;display:inline-block;"></span>
</div>


