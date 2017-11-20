<?php

// disable direct access to the file	
defined('GAVERN_WP') or die('Access denied');

global $tpl;

$cartActive = gk_is_active_sidebar('cart');

?>

<?php if(get_option($tpl->name . '_login_popup_state', 'Y') == 'Y') : ?>
<div id="gk-popup-login" class="gk-popup">	
	<div class="gk-popup-wrap">
		<?php if ( is_user_logged_in() ) : ?>
			<h3><?php _e('Your Account', GKTPLNAME); ?></h3>
			
			<?php 
				
				global $current_user;
				get_currentuserinfo();
			
			?>
			
			<p>
				<?php echo __('Hi, ', GKTPLNAME) . ($current_user->user_firstname) . ' ' . ($current_user->user_lastname) . ' (' . ($current_user->user_login) . ') '; ?>
			</p>
			<p>
				 <a href="<?php echo wp_logout_url(); ?>" class="btn button-primary" title="<?php _e('Logout', GKTPLNAME); ?>">
					 <?php _e('Logout', GKTPLNAME); ?>
				 </a>
			</p>
		
		<?php else : ?>
		    <h3><?php _e('Log in', GKTPLNAME); ?></h3>
		    
			<?php 
				wp_login_form(
					array(
						'echo' => true,
						'form_id' => 'loginform',
						'label_username' => __( 'Username', GKTPLNAME ),
						'label_password' => __( 'Password', GKTPLNAME ),
						'label_remember' => __( 'Remember Me', GKTPLNAME ),
						'label_log_in' => __( 'Log In', GKTPLNAME ),
						'id_username' => 'user_login',
						'id_password' => 'user_pass',
						'id_remember' => 'rememberme',
						'id_submit' => 'wp-submit',
						'remember' => true,
						'value_username' => NULL,
						'value_remember' => false 
					)
				); 
			?>		
		<?php endif; ?>
	</div>
</div>
<?php endif; ?>

<?php if($cartActive) : ?>
<div id="gk-popup-cart" class="gk-popup">	
	<div class="gk-popup-wrap">
		<?php gk_dynamic_sidebar('cart'); ?>
	</div>
</div>
<?php endif; ?>

<?php if(get_option($tpl->name . '_login_popup_state', 'Y') == 'Y' || $cartActive) : ?>
<div id="gk-popup-overlay"></div>
<?php endif; ?>