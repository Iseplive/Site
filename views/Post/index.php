
<?php
if($is_student)
	require dirname(__FILE__).'/../_includes/publish.php';
?>


<div id="posts-official" class="timeline">
	<h1><?php echo __('POST_TITLE_OFFICIAL'); ?></h1>
<?php 
foreach($official_posts as $post){
	require dirname(__FILE__).'/../_includes/view_post.php';
}
if(isset($current_category)){
?>
	<a href="<?php echo Config::URL_ROOT.Routes::getPage('posts_category_ajax_page', array('category' => $current_category, 'page' => '{page}', 'official' => '1')); ?>" class="posts-more-link official"><?php echo __('POST_SHOW_MORE'); ?></a>
<?php
}else{
?>
	<a href="<?php echo Config::URL_ROOT.Routes::getPage('posts_ajax_page', array('page' => '{page}', 'official' => '1')); ?>" class="posts-more-link official"><?php echo __('POST_SHOW_MORE'); ?></a>
<?php
}
?>
</div>


<div id="posts-nonofficial" class="timeline">
	<h1><?php echo __('POST_TITLE_NONOFFICIAL'); ?></h1>
<?php
if($is_logged){
	foreach($posts as $post){
		require dirname(__FILE__).'/../_includes/view_post.php';
	}
	if(isset($current_category)){
?>
	<a href="<?php echo Config::URL_ROOT.Routes::getPage('posts_category_ajax_page', array('category' => $current_category, 'page' => '{page}', 'official' => '0')); ?>" class="posts-more-link"><?php echo __('POST_SHOW_MORE'); ?></a>
<?php
	}else{
?>
	<a href="<?php echo Config::URL_ROOT.Routes::getPage('posts_ajax_page', array('page' => '{page}', 'official' => '0')); ?>" class="posts-more-link"><?php echo __('POST_SHOW_MORE'); ?></a>
<?php
	}

}else{
	require dirname(__FILE__).'/../User/signin.php';
}
?>
</div>


<div id="posts-sidebar">
	<div id="posts-sidebar-content">
		<h2><?php echo __('POST_CATEGORIES'); ?></h2>
		<ul>
			<li><a href="<?php echo Config::URL_ROOT.Routes::getPage('posts'); ?>"<?php if(!isset($current_category)) echo ' class="active"'; ?>><?php echo __('POST_CATEGORIES_ALL'); ?></a></li>
<?php
foreach($categories as $category){
?>
			<li><a href="<?php echo Config::URL_ROOT.Routes::getPage('posts_category', array('category' => $category['url_name'])); ?>"<?php if($category['url_name'] == $current_category) echo ' class="active"'; ?>><?php echo $category['name']; ?></a></li>
<?php
}
?>
		</ul>
		<br /><br />
	
		<div id="calendar">
			<?php
			require dirname(__FILE__).'/../_includes/calendar.php';
			?>
		</div>
		<br /><br />
		
		<h2><?php echo __('POST_SPONSOR_TITLE'); ?></h2>
		<a href="http://www.st.com/"><img src="<?php echo Config::URL_STATIC; ?>images/logo-st.png" alt="STMicroelectronics" /></a>
		<br /><br />
		
		<h2><?php echo __('POST_USEFUL_LINKS'); ?></h2>
		<ul>
			<li><a href="http://www.isep.fr/">Site de l'ISEP</a></li>
			<li><a href="http://planning.isep.fr/">Plannings ISEP</a></li>
			<li><a href="http://eleves.isep.fr/moodle/">Moodle ISEP</a></li>
			<li><a href="http://webmail.isep.fr/">Webmail ISEP</a></li>
			<li><a href="http://gcma.isep.fr">Annuaire ISEP</a></li>
			<li><a href="http://logement.isep.fr/">Logements ISEP</a></li>
			<li><a href="http://forge.isep.fr/">Forge ISEP</a></li>
			<li><a href="http://admsys.isep.fr/docpost/">Upload de documents ISEP</a></li>
		</ul>

		
	</div>
	
</div>
