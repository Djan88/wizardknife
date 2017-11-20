<?php

/**
 *
 * Page
 *
 **/

global $tpl;

$fullwidth = true;

if(get_option($tpl->name . '_woocommerce_show_sidebar', 'N') == 'Y') :
	$fullwidth = false;
endif;

gk_load('header');

if(get_option($tpl->name . '_woocommerce_show_sidebar', 'N') == 'Y') :
	$fullwidth = false;
	gk_load('before');
else :
	gk_load('before', null, array('sidebar' => false));
endif;

?>

<section id="gk-mainbody">
	<?php do_action('woocommerce_before_main_content'); ?>

	<?php woocommerce_content(); ?>
	
	<?php do_action('woocommerce_after_main_content'); ?>
</section>

<?php

if(get_option($tpl->name . '_woocommerce_show_sidebar', 'N') == 'Y') :
	gk_load('after');
else :
	gk_load('after', null, array('sidebar' => false));
endif;

gk_load('footer');

// EOF