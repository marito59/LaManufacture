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
	<div class="mfid-ressource-image vc_col-sm-3">
			<?php visualcomposerstarter_post_thumbnail(); ?>
	</div>
	<div class="entry-content vc_col-sm-9">
		<?php the_title( sprintf( '<h4 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h4>' ); ?>
		<?php the_content('<span class="green-button">En savoir plus...</span>'); ?>  	
	</div><!--.entry-content-->

</article><!--.entry-preview-->
