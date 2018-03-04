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
	<div class="cma-manid-event-meta vc_col-sm-2">
		<h2><?php echo get_field('prog_time'); ?></h2>
	</div>

	<div class="cma-manid-event-content vc-col-sm-10">
		<div class="vc_col-sm-3">
			<?php visualcomposerstarter_post_thumbnail(); ?>
		</div>
		<div class="entry-content vc_col-sm-7">
			<?php the_title( sprintf( '<h4 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h4>' ); ?>
			<?php the_excerpt(); ?>
		</div><!--.entry-content-->

		<?php if ( ! is_singular() ) :?>
			<a href="<?php echo esc_url( get_permalink( get_the_ID() ) ) ?>" class="blue-button read-more"><?php echo esc_html__( 'Read More', 'visual-composer-starter' ) ?></a>
		<?php endif;?>
	</div>
</article><!--.entry-preview-->
