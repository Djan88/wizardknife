<?php

/**
 * 
 * GK ImageShow Widget class
 *
 **/

class GK_ImageShow_Widget extends WP_Widget {
	// static field used to detect if the CSS code was generated
	static public $css_generated = false;
	/**
	 *
	 * Constructor
	 *
	 * @return void
	 *
	 **/
	function GK_ImageShow_Widget() {
		$this->WP_Widget(
			'widget_gk_image_show', 
			__( 'GK Image Show', GKTPLNAME ), 
			array( 
				'classname' => 'widget_gk_image_show', 
				'description' => __( 'Use this widget to show animated header', GKTPLNAME) 
			),
			array(
				'width' => 250, 
				'height' => 300
			)
		);
		
		$this->alt_option_name = 'widget_gk_image_show';
	}

	/**
	 *
	 * Outputs the HTML code of this widget.
	 *
	 * @param array An array of standard parameters for widgets in this theme
	 * @param array An array of settings for this widget instance
	 * @return void
	 *
	 **/
	function widget($args, $instance) {
		$cache = wp_cache_get('widget_gk_image_show', 'widget');
		
		//check the cache
		if(!is_array($cache)) {
			$cache = array();
		}

		if(!isset($args['widget_id'])) {
			$args['widget_id'] = null;
		}

		if(isset($cache[$args['widget_id']])) {
			echo $cache[$args['widget_id']];
			return;
		}

		ob_start();
		//
		extract($args, EXTR_SKIP);
		//
		$images = empty($instance['images']) ? '' : $instance['images'];
		$images = explode(',', $images);
		$titles = array();
		$text_x = empty($instance['text_x']) ? '18' : $instance['text_x'];
		$text_y = empty($instance['text_y']) ? '50' : $instance['text_y'];
		$pagination = empty($instance['pagination']) ? 'on' : $instance['pagination'];
		$speed = empty($instance['animation_speed']) ? '500' : $instance['animation_speed'];
		$interval = empty($instance['animation_interval']) ? '5000' : $instance['animation_interval'];
		$autoanim = empty($instance['autoanimation']) ? 'on' : $instance['autoanimation'];
		
		// if there are some images
		if(count($images) > 0) {
			// get the images data
			for($i = 0; $i < count($images); $i++) {
				$images[$i] = get_page_by_title($images[$i], 'OBJECT', 'attachment');
				$titles[$i] = get_post_meta($images[$i]->ID, '_wp_attachment_image_alt', true);
			}
			//
			echo $before_widget;
			// render the opening wrappers
			echo '<div id="gk-is-'.$args['widget_id'].'" class="gk-is-wrapper-gk-storebox" data-speed="'.$speed.'" data-interval="'.$interval.'" data-autoanim="'.$autoanim.'">';
			// preloader
			echo '<div class="gk-is-preloader"></div>';
			echo '<div class="gk-is-overlay"></div>';
			// generate images
			for($i = 0; $i < count($images); $i++) {									
				echo '<figure>';
				echo '<div class="gk-is-slide" data-style="z-index: '.$i.';" data-url="'.$images[$i]->guid.'" data-link="'.$titles[$i].'">'.$images[$i]->post_content.'</div>';
				
				if($images[$i]->post_content != '') {
					echo '<figcaption class="left" style="top: '.$text_y.'%; left: '.$text_x.'%;">';
					echo $images[$i]->post_content;
					echo '</figcaption>';
				}
				
				echo '</figure>';
			}
			// arrows		
			if($pagination == 'on') {					
				// arrows
				echo '<a href="#" class="gk-is-prev"><span>&laquo;</span></a>';	
				echo '<a href="#" class="gk-is-next"><span>&raquo;</span></a>';
			}
			// the last wrapper
			echo '</div>';
			// 
			echo $after_widget;
		}
		// save the cache results
		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('widget_gk_image_show', $cache, 'widget');
	}

	/**
	 *
	 * Used in the back-end to update the module options
	 *
	 * @param array new instance of the widget settings
	 * @param array old instance of the widget settings
	 * @return updated instance of the widget settings
	 *
	 **/
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['images'] = strip_tags($new_instance['images']);
		$instance['text_x'] = strip_tags($new_instance['text_x']);
		$instance['text_y'] = strip_tags($new_instance['text_y']);
		$instance['pagination'] = strip_tags($new_instance['pagination']);
		$instance['animation_speed'] = strip_tags($new_instance['animation_speed']);
		$instance['animation_interval'] = strip_tags($new_instance['animation_interval']);
		$instance['autoanimation'] = strip_tags($new_instance['autoanimation']);

		$this->refresh_cache();

		$alloptions = wp_cache_get('alloptions', 'options');
		if(isset($alloptions['widget_gk_image_show'])) {
			delete_option( 'widget_gk_image_show' );
		}

		return $instance;
	}

	/**
	 *
	 * Refreshes the widget cache data
	 *
	 * @return void
	 *
	 **/

	function refresh_cache() {
		wp_cache_delete( 'widget_gk_image_show', 'widget' );
	}

	/**
	 *
	 * Outputs the HTML code of the widget in the back-end
	 *
	 * @param array instance of the widget settings
	 * @return void - HTML output
	 *
	 **/
	function form($instance) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		$images = isset($instance['images']) ? esc_attr($instance['images']) : '';
		$text_x = isset($instance['text_x']) ? esc_attr($instance['text_x']) : '18';
		$text_y = isset($instance['text_y']) ? esc_attr($instance['text_y']) : '50';
		$pagination = isset($instance['pagination']) ? esc_attr($instance['pagination']) : 'On';
		$animation_speed = isset($instance['animation_speed']) ? esc_attr($instance['animation_speed']) : '500';
		$animation_interval = isset($instance['animation_interval']) ? esc_attr($instance['animation_interval']) : '5000';
		$autoanimation = isset($instance['autoanimation']) ? esc_attr($instance['autoanimation']) : 'On';

	?>
		<div class="gk-is">
			<p>
				<label class="left" for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', GKTPLNAME ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>
			
			<p>
				<label class="left" for="<?php echo esc_attr( $this->get_field_id( 'images' ) ); ?>"><?php _e( 'Slides:', GKTPLNAME ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'images' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'images' ) ); ?>" type="text" value="<?php echo esc_attr( $images ); ?>" />
			</p>
			
			<p>
				<label class="left" for="<?php echo esc_attr( $this->get_field_id( 'text_x' ) ); ?>"><?php _e( 'Text block X position (%):', GKTPLNAME ); ?></label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'text_x' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'text_x' ) ); ?>" type="text" value="<?php echo esc_attr( $text_x ); ?>" />
			</p>
			
			<p>
				<label class="left" for="<?php echo esc_attr( $this->get_field_id( 'text_y' ) ); ?>"><?php _e( 'Text block Y position (%):', GKTPLNAME ); ?></label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'text_y' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'text_y' ) ); ?>" type="text" value="<?php echo esc_attr( $text_y ); ?>" />
			</p>
			
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'pagination' ) ); ?>"><?php _e('Pagination', GKTPLNAME); ?></label>
				
				<select id="<?php echo esc_attr( $this->get_field_id('pagination')); ?>" name="<?php echo esc_attr( $this->get_field_name('pagination')); ?>">
					<option value="on"<?php echo (esc_attr($pagination) == 'on') ? ' selected="selected"' : ''; ?>>
						<?php _e('On', GKTPLNAME); ?>
					</option>
					<option value="off"<?php echo (esc_attr($pagination) == 'off') ? ' selected="selected"' : ''; ?>>
						<?php _e('Off', GKTPLNAME); ?>
					</option>
				</select>
			</p>
			
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'animation_speed' ) ); ?>"><?php _e( 'Animation speed:', GKTPLNAME ); ?></label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'animation_speed' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'animation_speed' ) ); ?>" type="text" value="<?php echo esc_attr( $animation_speed ); ?>" />ms
			</p>
			
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'animation_interval' ) ); ?>"><?php _e( 'Animation interval:', GKTPLNAME ); ?></label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'animation_interval' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'animation_interval' ) ); ?>" type="text" value="<?php echo esc_attr( $animation_interval ); ?>" />ms
			</p>
			
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'autoanimation' ) ); ?>"><?php _e('Autoanimation', GKTPLNAME); ?></label>
				
				<select id="<?php echo esc_attr( $this->get_field_id('autoanimation')); ?>" name="<?php echo esc_attr( $this->get_field_name('autoanimation')); ?>">
					<option value="on"<?php echo (esc_attr($autoanimation) == 'on') ? ' selected="selected"' : ''; ?>>
						<?php _e('On', GKTPLNAME); ?>
					</option>
					<option value="off"<?php echo (esc_attr($autoanimation) == 'off') ? ' selected="selected"' : ''; ?>>
						<?php _e('Off', GKTPLNAME); ?>
					</option>
				</select>
			</p>
		</div>
		
		<hr class="clear" />
	<?php
	}
}

// EOF