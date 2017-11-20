<?php

/**
 * 
 * GK News Blocks Widget class
 *
 **/

class GK_NewsBlocks_Widget extends WP_Widget {
	// variable used to store the object configuration
	private $wdgt_config;
	// variable uset to store the object query results
	private $wdgt_results;
	
	/**
	 *
	 * Constructor
	 *
	 * @return void
	 *
	 **/
	function GK_NewsBlocks_Widget() {
		$this->WP_Widget(
			'widget_gk_news_blocks', 
			__('GK News Blocks', GKTPLNAME), 
			array( 
				'classname' => 'widget_gk_news_bolocks', 
				'description' => __( 'Use this widget to show recent items as blocks', GKTPLNAME) 
			),
			array(
				'width' => 320, 
				'height' => 350
			)
		);
		
		$this->alt_option_name = 'widget_gk_news_blocks';
		//
		add_action('edit_post', array(&$this, 'refresh_cache'));
		add_action('delete_post', array(&$this, 'refresh_cache'));
		add_action('trashed_post', array(&$this, 'refresh_cache'));
		add_action('save_post', array(&$this, 'refresh_cache'));
		//
		add_action('wp_enqueue_scripts', array('GK_NewsBlocks_Widget', 'add_scripts'));
	}
	
	static function add_scripts() {
		wp_register_script( 'gk-news-blocks', gavern_file_uri('js/widgets/news_blocks.js'), array('jquery'));
		wp_enqueue_script('gk-news-blocks');
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
		$cache = get_transient(md5($this->id));
		// the part with the title and widget wrappers cannot be cached! 
		// in order to avoid problems with the calculating columns
		//
		extract($args, EXTR_SKIP);
		
		$title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
		
		$ops = array('data_source_type', 'data_source', 'orderby', 'order', 'offset', 'article_rows', 'article_cols', 'article_image_w', 'article_image_h', 'cache_time');
		
		foreach($ops as $option) {
			$config[$option] =  empty($instance[$option]) ? null : $instance[$option];
		}
		
		echo $before_widget;
		
		if($title != '') {
			echo $before_title;
			echo $title;
			echo $after_title;
		}
		
		if($cache) {
			echo $cache;
			echo $after_widget;
			return;
		}
		// start cache buffering
		ob_start();
		// get the posts data
		// let's save the global $post variable
		global $post;
		$tmp_post = $post;
		//
		// other options for the query
		//
		// total amount of the posts
		$amount_of_posts = $config['article_rows'] * $config['article_cols'];
		// resutls array
		$results = array();
		// data source
		if($config['data_source_type'] == 'latest') {
			$results = get_posts(array(
				'posts_per_page' => $amount_of_posts,
				'offset' => $config['offset'], 
				'orderby' => $config['orderby'],
				'order' => $config['order']
			));
		} else if($config['data_source_type'] == 'category') {
			$results = get_posts(array(
				'category_name' => $config['data_source'],
				'posts_per_page' => $amount_of_posts,
				'offset' => $config['offset'], 
				'orderby' => $config['orderby'],
				'order' => $config['order']
			));
		} else if($config['data_source_type'] == 'post') {
			$post_slugs = explode(',', $config['data_source']);
			foreach($post_slugs as $slug) {
				array_push($results, get_posts(array('name' => $slug)));
			}
		} else if($config['data_source_type'] == 'custom') {
			$post_type = explode(',', $config['data_source']);
			array_push($results, get_posts(array('post_type' => $post_type, 'numberposts' => $amount_of_posts)));
		}
		// restore the global $post variable
		$post = $tmp_post;
		// parse the data into a widget code		
		// generate the articles
		$amount_of_articles = $config['article_rows'] * $config['article_cols'];
		$amount_of_articles = $amount_of_articles > count($results) ? count($results) : $amount_of_articles; 
		$amount_of_art_rows = ($amount_of_articles > count($results)) ? ceil(count($results) / $config['article_cols']) : $config['article_rows'];
		// generate the widget wrapper
		echo '<div class="gk-news-blocks" data-cols="'.$config['article_cols'].'">';
		// iterate
		$this->wdgt_config = $config;
		$this->wdgt_results = $results;
		// amount
		$amount = 0;	
		// iterate through posts
		for($r = 0; $r < $config['article_cols'] * $amount_of_art_rows; $r++) {		
			if(isset($this->wdgt_results[$r]) || (is_array($this->wdgt_results[0]) && isset($this->wdgt_results[0][$r]))) {
				// get important article fields
				$art_ID = '';
				$art_title = '';
				$art_url = '';
				// from the retrieved results
				if($this->wdgt_config['data_source_type'] == 'post' || $this->wdgt_config['data_source_type'] == 'custom') {
					$art_ID = $this->wdgt_results[0][$r]->ID;
					$art_title = $this->wdgt_results[0][$r]->post_title;
				} else {
					$art_ID = $this->wdgt_results[$r]->ID;
					$art_title = $this->wdgt_results[$r]->post_title;
				}
				// get the article image
				$art_image = $this->generate_art_image($r, $art_ID);
				// if there is image
				if($art_image !== FALSE) {	
					// URL
					$art_url = get_permalink($art_ID);		
					// process the title
					$title_parts = explode(' ', trim($art_title));
					$title_part_one = '';
					$title_part_two = '';
					//
					if(count($title_parts) > 0) {
						$title_part_one = $title_parts[0];
						$title_parts[0] = '';
						$title_part_two = implode(' ', $title_parts);
					}
					// calculate the inverse class
					$inverse_class = '';
					$row = floor($r / $config['article_cols']) + 1;
					$offset = 0;
					//
					if($row % 2 == 0) {
						$offset = 1;
					}
					//
					if(($r % $config['article_cols']) % 2 == $offset) {
						$inverse_class = ' class="inverse"';
					}
					// output
					echo '<figure'.$inverse_class.'>';
					echo '<img src="'.$art_image.'" alt="'.strip_tags($art_title).'" />';
					echo '<figcaption>';
					echo '<h3><strong>'.$title_part_one.'</strong>'.$title_part_two.'</h3>';
					echo '<a href="'.$art_url.'" title="'.strip_tags($art_title).'" class="gk-image show '.(($r+1 <= $config['article_cols']) ? ' active' : ''). '">'.__('More details', GKTPLNAME).'</a>';
					echo '</figcaption>';
					echo '</figure>';
					// increase the amount
					$amount++;
				}
			}
		}
		// closing main wrapper
		echo '</div>';
		// save the cache results
		$cache_output = ob_get_flush();
		$cache_time = ($this->wdgt_config['cache_time'] == '' || !is_numeric($this->wdgt_config['cache_time'])) ? 60 : (int) $this->wdgt_config['cache_time'];
		set_transient(md5($this->id) , $cache_output, $cache_time * 60);
		// 
		echo $after_widget;
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
		$instance['title'] = strip_tags( $new_instance['title'] );
		$ops = array('data_source_type', 'data_source', 'orderby', 'order', 'offset', 'article_rows', 'article_cols', 'article_image_w', 'article_image_h', 'cache_time');
		
		foreach($ops as $option) {
			$instance[$option] = strip_tags( $new_instance[$option] );	
		}
		
		$this->refresh_cache();

		$alloptions = wp_cache_get('alloptions', 'options');
		if(isset($alloptions['widget_gk_news_blocks'])) {
			delete_option( 'widget_gk_news_blocks' );
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
	    if(is_array(get_option('widget_widget_gk_news_blocks'))) {
	    	$ids = array_keys(get_option('widget_widget_gk_news_blocks'));
	    	for($i = 0; $i < count($ids); $i++) {
	    		if(is_numeric($ids[$i])) {
	    			delete_transient(md5('widget_gk_news_blocks-' . $ids[$i]));
	    		}
	    	}
	    } else {
	    	delete_transient(md5('widget_gk_news_blocks-' . $this->id));
	    }
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
		
		// data source
		$data_source_type = isset($instance['data_source_type']) ? esc_attr($instance['data_source_type']) : 'latest';
		$data_source = isset($instance['data_source']) ? esc_attr($instance['data_source']) : '';
		$orderby = isset($instance['orderby']) ? esc_attr($instance['orderby']) : 'ID';
		$order = isset($instance['order']) ? esc_attr($instance['order']) : 'DESC';
		$offset = isset($instance['offset']) ? esc_attr($instance['offset']) : '0';
		
		// articles amount
		$article_rows = isset($instance['article_rows']) ? esc_attr($instance['article_rows']) : '1';
		$article_cols = isset($instance['article_cols']) ? esc_attr($instance['article_cols']) : '1';
		
		// article text format
		$article_image_w = isset($instance['article_image_w']) ? esc_attr($instance['article_image_w']) : '310';
		$article_image_h = isset($instance['article_image_h']) ? esc_attr($instance['article_image_h']) : '310';
		
		// cache time
		$cache_time = isset($instance['cache_time']) ? esc_attr($instance['cache_time']) : '60';
	?>	
		<div class="gk-nsp-col gk-ng-col">
			<h3><?php _e('Basic settings', GKTPLNAME); ?></h3>
			
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', GKTPLNAME ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>
			
			<h3><?php _e('Data source settings', GKTPLNAME); ?></h3>
			
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'data_source_type' ) ); ?>"><?php _e( 'Data source:', GKTPLNAME ); ?></label>
				
				<select id="<?php echo esc_attr( $this->get_field_id('data_source_type')); ?>" name="<?php echo esc_attr( $this->get_field_name('data_source_type')); ?>">
					<option value="latest"<?php echo (esc_attr($data_source_type) == 'latest') ? ' selected="selected"' : ''; ?>>
						<?php _e('Latest posts', GKTPLNAME); ?>
					</option>
					<option value="category"<?php echo (esc_attr($data_source_type) == 'category') ? ' selected="selected"' : ''; ?>>
						<?php _e('Categories slugs', GKTPLNAME); ?>
					</option>
					<option value="post"<?php echo (esc_attr($data_source_type) == 'post') ? ' selected="selected"' : ''; ?>>
						<?php _e('Posts slugs', GKTPLNAME); ?>
					</option>
					<option value="custom"<?php echo (esc_attr($data_source_type) == 'custom') ? ' selected="selected"' : ''; ?>>
						<?php _e('Custom post types', GKTPLNAME); ?>
					</option>
				</select>
				
				<textarea id="<?php echo esc_attr( $this->get_field_id('data_source')); ?>" name="<?php echo esc_attr( $this->get_field_name('data_source')); ?>"><?php echo esc_attr($data_source); ?></textarea>
			</p>
			
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>"><?php _e( 'Order by:', GKTPLNAME ); ?></label>
				
				<select id="<?php echo esc_attr( $this->get_field_id('orderby')); ?>" name="<?php echo esc_attr( $this->get_field_name('orderby')); ?>">
					<option value="ID"<?php echo (esc_attr($orderby) == 'ID') ? ' selected="selected"' : ''; ?>>
						<?php _e('ID', GKTPLNAME); ?>
					</option>
					
					<option value="date"<?php echo (esc_attr($orderby) == 'date') ? ' selected="selected"' : ''; ?>>
						<?php _e('Date', GKTPLNAME); ?>
					</option>
					
					<option value="title"<?php echo (esc_attr($orderby) == 'title') ? ' selected="selected"' : ''; ?>>
						<?php _e('Title', GKTPLNAME); ?>
					</option>
					
					<option value="modified"<?php echo (esc_attr($orderby) == 'modified') ? ' selected="selected"' : ''; ?>>
						<?php _e('Modified', GKTPLNAME); ?>
					</option>
					
					<option value="rand"<?php echo (esc_attr($orderby) == 'rand') ? ' selected="selected"' : ''; ?>>
						<?php _e('Random', GKTPLNAME); ?>
					</option>
				</select>
				
				<select id="<?php echo esc_attr( $this->get_field_id('order')); ?>" name="<?php echo esc_attr( $this->get_field_name('order')); ?>">
					<option value="ASC"<?php echo (esc_attr($order) == 'ASC') ? ' selected="selected"' : ''; ?>>
						<?php _e('ASC', GKTPLNAME); ?>
					</option>
					<option value="DESC"<?php echo (esc_attr($order) == 'DESC') ? ' selected="selected"' : ''; ?>>
						<?php _e('DESC', GKTPLNAME); ?>
					</option>
				</select>
			</p>
			
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'offset' ) ); ?>"><?php _e( 'Offset:', GKTPLNAME ); ?></label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'offset' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'offset' ) ); ?>" type="text" value="<?php echo esc_attr( $offset ); ?>" class="short" />
			</p>
			
			<p>
				<h3><?php _e('Articles amount', GKTPLNAME); ?></h3>
				<label for="<?php echo esc_attr( $this->get_field_id( 'article_rows' ) ); ?>"><?php _e( 'rows:', GKTPLNAME ); ?></label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'article_rows' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'article_rows' ) ); ?>" type="text" value="<?php echo esc_attr( $article_rows ); ?>" class="short" />
	
				<label for="<?php echo esc_attr( $this->get_field_id( 'article_cols' ) ); ?>"><?php _e( 'columns:', GKTPLNAME ); ?></label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'article_cols' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'article_cols' ) ); ?>" type="text" value="<?php echo esc_attr( $article_cols ); ?>" class="short" />
			</p>
			
			<p>			
				<label for="<?php echo esc_attr( $this->get_field_id( 'article_image_w' ) ); ?>"><?php _e( 'Image size:', GKTPLNAME ); ?></label>
				
				<input id="<?php echo esc_attr( $this->get_field_id( 'article_image_w' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'article_image_w' ) ); ?>" type="text" value="<?php echo esc_attr( $article_image_w ); ?>" class="short" />
				&times;
				<input id="<?php echo esc_attr( $this->get_field_id( 'article_image_h' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'article_image_h' ) ); ?>" type="text" value="<?php echo esc_attr( $article_image_h ); ?>" class="short" />
			</p>
			
			<h3><?php _e('Cache settings', GKTPLNAME); ?></h3>
			
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'cache_time' ) ); ?>"><?php _e( 'Cache time (min):', GKTPLNAME ); ?></label>
				<input class="medium" id="<?php echo esc_attr( $this->get_field_id( 'cache_time' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'cache_time' ) ); ?>" type="text" value="<?php echo esc_attr( $cache_time ); ?>" />
			</p>
		</div>
	<?php
	}
	
	/**
	 *
	 * Functions used to generate the article elements
	 *
	 **/
	 
	 function generate_art_image($i, $art_ID) {
	 	$image = wp_get_attachment_image_src( get_post_thumbnail_id( $art_ID ), 'single-post-thumbnail' );
	 	$image_path = $image[0];
	 	$upload_dir = wp_upload_dir();
	 	$image_path = str_replace($upload_dir['baseurl'] . '/', '', $image_path);
	 	
	 	if($image_path != '') {
	 		$img_editor = wp_get_image_editor( $upload_dir['basedir'] . '/' . $image_path);
	 		
	 		if(!is_wp_error($img_editor)) {
	 	 		$img_editor->resize($this->wdgt_config['article_image_w'], $this->wdgt_config['article_image_h'], true);
	 	 		$img_filename = $img_editor->generate_filename( $this->id, dirname(__FILE__) . '/' . 'cache_nsp');
	 	 		$img_editor->save($img_filename);
	 	 		
	 	 	    $new_path = basename($img_filename);  
	 	 	    $cache_uri = get_template_directory_uri() . '/gavern/cache_nsp/';	
	 	 		
	 	 		if(is_string($new_path)) {
	 		 		$new_path = $cache_uri . $new_path;
	 	 		
	 	 			return $new_path;
	 			} else {
	 				return false;
	 			}
	 		} else {
	 			return false;
	 		}
	 	} else {
	 		return false;
	 	} 
	 }
}

// EOF