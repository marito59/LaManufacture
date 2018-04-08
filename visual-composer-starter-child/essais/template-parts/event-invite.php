<?php
/**
 * The template part for displaying content
 *
 * @package WordPress
 * @subpackage Visual Composer Starter
 * @since Visual Composer Starter 1.0
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry-preview' ); ?>>
	<div class="cma-manid-event-content vc_col-sm-2">
		<?php the_post_thumbnail('thumbnail'); ?>
	</div>
	<div class="entry-content vc_col-sm-10">
		<?php the_title( sprintf( '<h4 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h4>' ); ?>
		<?php the_excerpt(); ?>

		<?php if ( ! is_singular() ) :?>
			<a href="<?php echo esc_url( get_permalink( get_the_ID() ) ) ?>" class="blue-button read-more"><?php echo esc_html__( 'Read More', 'visual-composer-starter' ) ?></a>
		<?php endif;?>
	</div><!--.entry-content-->
</article><!--.entry-preview-->
