<?php 
	
	/**
	 *
	 * Template elements before the page content
	 *
	 **/
	
	// create an access to the template main object
	global $tpl;
	
	// disable direct access to the file	
	defined('GAVERN_WP') or die('Access denied');
	
	$gk_content_class = '';
	
	if(get_option($tpl->name . '_page_layout', 'right') == 'left') {
		$gk_content_class = ' gk-column-left';
	}
	
	if(!gk_is_active_sidebar('header')) {
		$gk_content_class .= ' gk-content-top-border';
	}
	
	if(
		!gk_is_active_sidebar('sidebar') || 
		get_option($tpl->name . '_page_layout', 'left') == 'none' || 
		is_page_template('template.fullwidth.php')
	) {
		$gk_content_class .= ' gk-content-no-sidebar';
	}
	
	if($gk_content_class != '') {
		$gk_content_class = ' class="' . $gk_content_class . '"';
	}
	
?>

	<div class="gk-page-wrap<?php if(get_option($tpl->name . '_template_homepage_mainbody', 'N') == 'N' && is_home()) : ?> gk-is-homepage<?php endif; ?>">
		<?php if(gk_is_active_sidebar('header_bottom')) : ?>
		<div id="gk-header-bottom">
			<?php gk_dynamic_sidebar('header_bottom'); ?>
			
			<!--[if IE 8]>
			<div class="ie8clear"></div>
			<![endif]-->
		</div>
		<?php endif; ?>
	
		<div id="gk-mainbody-columns" <?php echo $gk_content_class; ?>>			
			<section>
				<?php if(gk_is_active_sidebar('top1')) : ?>
				<div id="gk-top1">
					<div class="gk-page widget-area">
						<?php gk_dynamic_sidebar('top1'); ?>
						
						<!--[if IE 8]>
						<div class="ie8clear"></div>
						<![endif]-->
					</div>
				</div>
				<?php endif; ?>
				
				<?php if(gk_is_active_sidebar('top2')) : ?>
				<div id="gk-top2">
					<div class="gk-page widget-area">
						<?php gk_dynamic_sidebar('top2'); ?>
						
						<!--[if IE 8]>
						<div class="ie8clear"></div>
						<![endif]-->
					</div>
				</div>
				<?php endif; ?>
			
				<?php if(gk_is_active_sidebar('mainbody_top')) : ?>
				<div id="gk-mainbody-top">
					<?php gk_dynamic_sidebar('mainbody_top'); ?>
					
					<!--[if IE 8]>
					<div class="ie8clear"></div>
					<![endif]-->
				</div>
				<?php endif; ?>
				
				<!-- Mainbody, breadcrumbs -->
				<?php if(gk_show_breadcrumbs()) : ?>
					<?php if(!is_front_page()) : ?>
						<div id="gk-breadcrumb-area">
							<?php gk_breadcrumbs_output(); ?>
						</div>
					<?php endif; ?>
				<?php endif; ?>
