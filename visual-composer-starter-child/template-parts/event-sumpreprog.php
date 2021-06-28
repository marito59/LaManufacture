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
		<h4><?php echo get_field('prog_time'); ?></h4>
	</div>

	<div class="cma-manid-event-content entry-content vc_col-sm-10">
			<?php the_title( '<h4 class="entry-title">', '</h4>' ); ?>
		<?php the_content(''); ?>
	</div><!--.entry-content-->		
</article><!--.entry-preview-->

