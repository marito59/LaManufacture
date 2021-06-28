<?php
/**
 * The template part for displaying single posts
 *
 * @package WordPress
 * @subpackage Visual Composer Starter
 * @since Visual Composer Starter 1.0
 */

?>
<?php if ( visualcomposerstarter_is_the_title_displayed() && get_the_title() ) : ?>
<h1 class="entry-title"><?php the_title(); ?></h1>
<?php endif; ?>
<div class="entry-content">
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<?php /* Added by CMA
			   * Si le post appartient à la catégorie Programme, alors on affiche la date (aussi dans la categorie) et l'heure (dans ACF)
			   */
			if (in_category('Programme')) {
				$prog_date = strtotime(get_field('prog_date'));
				$prog_time = get_field('prog_time');
				if ($prog_date) {
					$dateformatstring = "l j F Y";
					$prog_date = date_i18n($dateformatstring, $prog_date);
		?>
		<div class="cma-manid-prog-date-time"><span class="cma-manid-prog-date"><?php echo $prog_date;?></span> - <span class="cma-manid-prog-time"><?php echo $prog_time;?></span></div>
		<?php
				}
			}
				/* fin added by CMA */
		?>	   
		<?php /* Added by CMA : ajout de la vidéo si elle existe */
			if (get_field("video")) {
				?>
				<div class='embed-container'><?php the_field('video');?></div>
				<?php
			}
		?>
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
