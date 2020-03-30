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
	<div class="cma-manid-event-type vc_col-sm-1">
        <?php
                $type_events = get_field('type_evenement');
                if( $type_events ): 
                    foreach( $type_events as $event ):
                        switch ($event) {
                            case "Rencontre":
                                ?><i class="fas fa-user"></i><?php
                                break;
                            case "Dialogue":
                                ?><i class="fas fa-user-friends"></i><?php
                                break;
                            case "Débat":
                                ?><i class="fas fa-users"></i><?php
                                break;
                            case "Conférence":
                                ?><i class="fas fa-chalkboard-teacher"></i><?php
                                break;
                            case "Lecture":
                                ?><i class="fas fa-book-reader"></i><?php
                                break;
                            case "Spectacle":
                                ?><i class="fas fa-theater-masks"></i><?php
                                break;
                            case "Repas":
                                ?><i class="fas fa-utensils"></i><?php
                                break;
                            case "Film":
                                ?><i class="fas fa-film"></i><?php
                                break;
                            case "Concert":
                                ?><i class="fas fa-music"></i><?php
                                break;                        
                            case "Performance":
                                ?><i class="fas fa-star"></i><?php
                            break;                        
                            case "Synthèse":
                                ?><i class="far fa-handshake"></i><?php
                            break;                        
                        }
                    endforeach;
                endif;
            ?>
	</div>

	<div class="cma-manid-event-content entry-content vc_col-sm-9">
        <h4>
        <?php echo get_the_title(); ?>
        </h4>
        <p><?php 
            $posts = get_field('invites');

            if( $posts ):
                $count = 0;
                foreach( $posts as $post): // variable must be called $post (IMPORTANT)
                    setup_postdata($post); 
                    if ($count > 0) {
                        echo ', ';
                    }
                    $count++;
                    the_title();
                endforeach;
                wp_reset_postdata(); // IMPORTANT - reset the $post object so the rest of the page works correctly 
            endif; 
        ?></p>
    </div><!--.entry-content-->		
</article><!--.entry-preview-->

