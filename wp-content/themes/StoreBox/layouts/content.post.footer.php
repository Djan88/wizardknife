<?php

/**
 *
 * The template fragment to show post footer
 *
 **/

// disable direct access to the file	
defined('GAVERN_WP') or die('Access denied');

global $tpl; 

$tag_list = get_the_tag_list('', '', '');

$params = get_post_custom();
$params_aside = isset($params['gavern-post-params-aside']) ? $params['gavern-post-params-aside'][0] : false;

$param_aside = true;
$param_tags = true;

 if($params_aside) {
  $params_aside = unserialize(unserialize($params_aside));
  $param_aside = $params_aside['aside'] == 'Y';
  $param_tags = $params_aside['tags'] == 'Y';
 }

?>

<?php do_action('gavernwp_after_post_content'); ?>

<?php if(is_singular()) : ?>
	
	<?php if($tag_list != '' && $param_tags): ?>
	<div class="item-tags">
		<span><?php _e('Tagged under:', GKTPLNAME); ?></span>
		<?php echo $tag_list; ?>
	</div>
	<?php endif; ?>

	<?php 
		// variable for the social API HTML output
		$social_api_output = gk_social_api(get_the_title(), get_the_ID()); 
	?>
		
	<?php if($social_api_output != '' || gk_author(false, true)): ?>
	<footer>
		<?php echo $social_api_output; ?>
		<?php gk_author(false, false); ?>
	</footer>
	<?php endif; ?>
<?php endif; ?>