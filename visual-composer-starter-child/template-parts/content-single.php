<?php
/**
 * The template part for displaying single posts
 *
 * @package WordPress
 * @subpackage Visual Composer Starter
 * @since Visual Composer Starter 1.0
 */

?>
<?php if ( vct_is_the_title_displayed() && get_the_title() ) : ?>
<h1 class="entry-title"><?php the_title(); ?></h1>
<?php endif; ?>
<div class="entry-content">
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<?php /* modified by CMA 
			   * But : contrôler comment est affiché le contenu du post avec le tag more, on veut que le contenu s'affiche
		       * previous : the_content( '', true );
			   * modification : on met un champ ACF dont on récupère la valeur (par défaut, même comportement)
			   */?>
		<?php the_content( '', get_field('strip_teaser') ); ?>
		<?php // fin modif CMA ?>
		<?php
			/* Modified by CMA
			 * But : supprimer la navigation de bas de page
			 * 
			wp_link_pages(
				array(
					'before' => '<div class="nav-links post-inner-navigation">',
					'after' => '</div>',
					'link_before' => '<span>',
					'link_after' => '</span>',
				)
			);
			*
			* End Modified by CMA
			*/
		?>
	</article>
	<?php visualcomposerstarter_entry_tags(); ?>
</div><!--.entry-content-->
