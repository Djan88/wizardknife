<?php 
	
	/**
	 *
	 * Template footer
	 *
	 **/
	
	// create an access to the template main object
	global $tpl;
	
	// disable direct access to the file	
	defined('GAVERN_WP') or die('Access denied');
	
?>

		<footer id="gk-footer">			
			<?php gavern_menu('footermenu', 'gk-footer-menu'); ?>
			
			<div class="gk-copyrights">
				<?php echo str_replace('\\', '', htmlspecialchars_decode(get_option($tpl->name . '_template_footer_content', ''))); ?>
			</div>
		</footer>
		
		<!--[if IE 8]>
		<div class="ie8clear"></div>
		<![endif]-->
	</div><!-- end of the .gk-page-wrapper div -->	
	
	<div class="gk-page">
		<?php if(get_option($tpl->name . '_styleswitcher_state', 'Y') == 'Y') : ?>
		<div id="gk-style-area" class="hidden">
			<?php for($i = 0; $i < count($tpl->styles); $i++) : ?>
			<div class="gk-style-switcher-<?php echo $tpl->styles[$i]; ?>">
				<?php 
					$j = 1;
					foreach($tpl->style_colors[$tpl->styles[$i]] as $stylename => $link) : 
				?> 
				<a href="#<?php echo $link; ?>" id="gk-color<?php echo $j++; ?>"><?php echo $stylename; ?></a>
				<?php endforeach; ?>
			</div>
			<?php endfor; ?>
		</div>
		<?php endif; ?>
		
		<?php if(get_option($tpl->name . '_template_footer_logo', 'Y') == 'Y') : ?>
		<img src="<?php echo gavern_file_uri('images/gavernwp.png'); ?>" class="gk-framework-logo" alt="GavernWP" />
		<?php endif; ?>
	</div>
	
	<?php gk_load('popups'); ?>
	<?php gk_load('social'); ?>
	
	<?php do_action('gavernwp_footer'); ?>
	
	<?php 
		echo stripslashes(
			htmlspecialchars_decode(
				str_replace( '&#039;', "'", get_option($tpl->name . '_footer_code', ''))
			)
		); 
	?>
	
	<?php wp_footer(); ?>
	
	<?php do_action('gavernwp_ga_code'); ?>
	<script src='<?php bloginfo('template_url'); ?>/js/script.js'></script>
</body>
</html>
