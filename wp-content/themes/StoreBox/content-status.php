<?php

/**
 *
 * The template for displaying posts in the Status Post Format on index and archive pages
 *
 **/

global $tpl; 

?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<header class="status">
			<?php get_template_part( 'layouts/content.post.header' ); ?>
		</header>
		
		<?php get_template_part( 'layouts/content.post.featured' ); ?>

		<?php if ( (!is_single() && get_option($tpl->name . '_readmore_on_frontpage', 'Y') == 'Y') || is_search() || is_archive() || is_tag() ) : ?>
		<section class="summary">
			<?php the_excerpt(); ?>
			
			<a href="<?php echo get_permalink(get_the_ID()); ?>" class="btn"><?php _e('Read more', GKTPLNAME); ?></a>
		</section>
		<?php else : ?>
		<section class="content">
			<?php if(is_single()) : ?>
				<?php the_content(); ?>
			<?php else : ?>
				<?php the_content( __( 'Read more', GKTPLNAME ) ); ?>
			<?php endif; ?>
			
			<?php gk_post_fields(); ?>
			<?php gk_post_links(); ?>
		</section>
		<?php endif; ?>

		<?php get_template_part( 'layouts/content.post.footer' ); ?>
	</article>