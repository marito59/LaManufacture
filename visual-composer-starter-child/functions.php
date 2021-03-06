<?php

	/*
	 * Fonction de base pour un child theme qui permet d'enregistrer le lien vers le thème parent
	 */
	function my_theme_enqueue_styles() {

		$parent_style = 'vc-starter-style'; // This is 'twentyfifteen-style' for the Twenty Fifteen theme.

		wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
		wp_enqueue_style( 'child-style',
			get_stylesheet_directory_uri() . '/style.css',
			array( $parent_style ),
			wp_get_theme()->get('Version')
		);
	}
	add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );

	function my_child_theme_setup() {
		load_child_theme_textdomain( 'visual-composer-starter', get_stylesheet_directory() . '/languages' );
	}
	add_action( 'after_setup_theme', 'my_child_theme_setup' );
	
	// utilisation d'une template part dans Display Post Shortcode pour afficher les évènements
	function cma_manid_dps_template_part( $output, $original_atts ) {
	
		// Return early if our "layout" attribute is not specified
		if( empty( $original_atts['layout'] ) )
			return $output;
		ob_start();
		get_template_part( 'template-parts/event', $original_atts['layout'] );
		$new_output = ob_get_clean();
		if( !empty( $new_output ) )
			$output = $new_output;
		return $output;
	}

	add_action( 'display_posts_shortcode_output', 'cma_manid_dps_template_part', 10, 2 );

	// enlever les boutons sharing de Jetpack qui s'affichent dans l'excerpt
	// from : https://jetpack.com/2013/06/10/moving-sharing-icons/
	function cma_manid_remove_share() {
		remove_filter( 'the_excerpt', 'sharing_display', 19 );
	}
	
	add_action( 'loop_start', 'cma_manid_remove_share' );

	/*
	 * Enregistrement des formats personnalisés de thumbnail
	 */
	add_action('init', 'my_init_function');
	
    function my_init_function() {
		add_theme_support( 'post-thumbnails' );
		add_image_size( 'document-thumb', 166, 93, true ); // Hard Crop Mode
   }	

   	add_filter( 'image_size_names_choose', 'my_custom_sizes' );
   
  	function my_custom_sizes( $sizes ) {
	  	return array_merge( $sizes, array(
			'document-thumb' => __( 'Image 166x93' ),
	  	) );
	}	

	/*
	 * Définition d'une longueur personnalisée de l'extrait
	 */
	function my_custom_excerpt_length( $length ) {
		return 150; // set the number to the amount of words you want to appear in the excerpt
	}
	//add_filter( 'excerpt_length', 'my_custom_excerpt_length');
	
	/**
	 * Filter the "read more" excerpt string link to the post.
	 *
	 * @param string $more "Read more" excerpt string.
	 * @return string (Maybe) modified "read more" excerpt string.
	 */
	function my_custom_excerpt_more( $more ) {
		return sprintf( '<a class="read-more" href="%1$s">%2$s</a>',
			get_permalink( get_the_ID() ),
			__( 'Read More', 'textdomain' )
		);
	}
	//add_filter( 'excerpt_more', 'my_custom_excerpt_more' );

	// filtre pour éviter le scroll quand on clique sur Lire la suite
	function remove_more_link_scroll( $link ) {
		$link = preg_replace( '|#more-[0-9]+|', '', $link );
		return $link;
	}
	add_filter( 'the_content_more_link', 'remove_more_link_scroll' );

	/*
	 * Function pour permettre de remonter en haut de la page (back to top)
	 */
	function my_scripts_method() {
		wp_enqueue_script(
			'custom-script',
			get_stylesheet_directory_uri() . '/js/topbutton.js',
			array( 'jquery' )
		);
	}
	
	add_action( 'wp_enqueue_scripts', 'my_scripts_method' );

	/*
	 * Ajout du bouton back to top 
	 * Tout est défini dans le CSS
	 */
	add_action( 'visualcomposerstarter_hook_before_footer', my_backtotop_tag_hook);

	function my_backtotop_tag_hook () {
		echo "<a href=\"#\" class=\"topbutton\"></a>";
	}

	/* 
	 * Ajout des icones Font Awesome
	 */
	function my_enqueue_icon_stylesheet() {
		wp_register_style( 'fontawesome', 'http:////maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css' );
		wp_enqueue_style( 'fontawesome');
	}
	add_action( 'wp_enqueue_scripts', 'my_enqueue_icon_stylesheet' );


	/*
	 * Modifie le fonctionement par défaut des Meta tags du thème Visual Composer Starter
	 */
	if ( ! function_exists( 'visualcomposerstarter_single_meta' ) ) :
		/**
		 * Single meta
		 */
		function visualcomposerstarter_single_meta() {
			?>
			<div class="entry-meta">
				<?php echo esc_html_x( 'On', 'Post meta', 'visual-composer-starter' ); ?>
				<?php if ( in_array( get_post_type(), array( 'post', 'attachment' ), true ) ) : ?>
					<span class="date"><?php visualcomposerstarter_entry_date(); ?></span>
				<?php endif;?>
				<?php echo esc_html_x( 'to', 'Post meta', 'visual-composer-starter' ); ?>
				<?php the_category( _x( ', ', 'Used between list items, there is a space after the comma.', 'visual-composer-starter' ) ); ?>
			</div>
			<?php
		}
	endif;

	// Empécher la publication dans Facebook et Twitter par défaut
	add_filter( 'publicize_checkbox_default', '__return_false' );

	/* 
	 * Mofifie l'affichage de la Featured image en fonction des paramètres spécifiques de l'article définis via ACF
	 * Description : par défaut, la featured image est utilisée dans les listes et en en-tête de la page.
	 *   - Supress Featured Image : supprime l'affichage de la Featured image dans la page (pas dans la liste)
	 *   - Extra Featured Image : utilise une image différente de la Featured image dans la page (pas dans la liste)
	 */

	if ( ! function_exists( 'visualcomposerstarter_header_featured_content' ) ) :
		/**
		 * Header featured content.
		 */
		function visualcomposerstarter_header_featured_content() {
			if ( 'gallery' === get_post_format() ) {
				?>
				<div class="<?php echo esc_attr( visualcomposerstarter_get_header_image_container_class() ); ?>">
					<div class="row">
						<div class="gallery-slider">
							<?php
							$gallery = get_post_gallery_images( get_the_ID() );
	
							foreach ( $gallery as $key => $src ) :
								?>
								<div class="gallery-item">
									<div class="fade-in-img">
										<div class="fade-in-img-inner-wrap">
											<img src="<?php echo esc_url( $src );?>" data-src="<?php echo esc_url( $src );?>">
											<noscript>
												<img src="<?php echo esc_url( $src );?>">
											</noscript>
										</div>
									</div><!--.fade-in-img-->
								</div><!--.gallery-item-->
								<?php
							endforeach;
							?>
						</div><!--.gallery-slider-->
					</div>
				</div>
	
				<?php
			} elseif ( post_password_required() || is_attachment() || ! has_post_thumbnail() || ! get_theme_mod( 'vct_overall_site_featured_image', true ) ) {
				return;
			} else {
				// Modif CMA : supprimer l'affichage de la featured image
				if ( !get_field('suppress_featured_image') ) {
					// end modif CMA
					?>
					<div class="<?php echo esc_attr( visualcomposerstarter_get_header_image_container_class() ); ?>">
						<div class="row">
							<div class="fade-in-img">
								<div class="fade-in-img-inner-wrap">
									<?php
									// Modified by CMA
									// affiche une autre featured image si elle existe
									$extra_featured_image = get_field ('extra_featured_image');
									if ( $extra_featured_image ) {
										$size = "full";
										echo wp_get_attachment_image( $extra_featured_image, $size );
									} else {
									// end modif CMA + parenthèse à la fin
										if ( 'full_width' === get_theme_mod( 'vct_overall_site_featured_image_width', 'full_width' ) ) {
											the_post_thumbnail( 'vct-featured-single-image-full', array(
													'data-src' => get_the_post_thumbnail_url( null, 'vct-featured-single-image-full' ),
												) );
										} else {
											the_post_thumbnail( 'vct-featured-single-image-boxed', array(
													'data-src' => get_the_post_thumbnail_url( null, 'vct-featured-single-image-boxed' ),
												) );
										}
									}
									?>
									<noscript>
										<?php the_post_thumbnail(); ?>
									</noscript>
								</div>
							</div>
						</div>
					</div>
	
				<?php
				}
			} // End if().
		}
	endif;

	function cma_manid_widgets_init() {
		register_sidebar( array(
			'name'          => 'Widget area 1',
			'id'            => 'widget_1',
			'before_widget' => '<div>',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="rounded">',
			'after_title'   => '</h2>',
		) );
	}
	add_action( 'widgets_init', 'cma_manid_widgets_init' );


	/* vérifie si l'élément de programme est de l'année en cours */
	function manid_check_current_edition( $postid ) {
		$categories = get_the_category($postid);
		$year = "Programme " . date("Y");
		$found = false;
		foreach( $categories as $category ) {
			$pos =  strpos($category->name, $year);
			if ($pos === 0) {
				$found = true;
				break;
			}
		}
		return $found;
	}

	/* Affiche les participations d'un invité */
	function manid_get_participations_list( $inviteID ) {
		$output = "";
		/*
		*  Query posts for a relationship value.
		*  This method uses the meta_query LIKE to match the string "123" to the database value a:1:{i:0;s:3:"123";} (serialized array)
		*  From : https://www.advancedcustomfields.com/resources/querying-relationship-fields/
		*/

 		$participations = get_posts(array(
			'post_type' => 'post',
			'meta_query' => array(
				array(
					'key' => 'invites', // name of custom field
					'value' => '"' . $inviteID . '"', // matches exactly "123", not just 123. This prevents a match for "1234"
					'compare' => 'LIKE'
				)
				),
			'category__not_in' => '87' // suppression de l'édition de mai reportée à août en 2020
		)); 
		if( $participations ) {
			$output .= "<div class='manid-particpations'><h4>Interventions à La Manufacture d'idées</h4><ul>";
 			foreach( $participations as $participation ) {
				$output .= "<li><a href='" . get_permalink( $participation->ID ) . "'>";
				$prog_date = strtotime(get_field('prog_date', $participation->ID));
				if ($prog_date) {
					$dateformatstring = "j F Y";
					$prog_date = date_i18n($dateformatstring, $prog_date);
					$output .= $prog_date . " : ";
				}
				$output .= get_the_title( $participation->ID );
				$output .= "</a></li>";
			}
			$output .= "</ul></div>";
		}
		return $output;
	}

	function display_tarifs() {
		$tarif = "";
		$tarif_plein = "";
		$tarif_reduit = "";
		$tarif_adherent = "";
		$tarif_enfant = "";

		$type_tarif = get_field('tarif');
		switch ($type_tarif) {
			case "gratuit":
				$tarif = get_field( 'message_entree_gratuite', 'option' );
			break;

			case "tarif unique":
				while( have_rows('groupe_tarifs') ) {
					the_row(); 
        			// Get sub field values.
					$tarif = sprintf( "Tarif unique &nbsp; %s €", get_sub_field("tarif_plein") );
				}
			break;

			case "tarifs multiples":
				while( have_rows('groupe_tarifs') ) {
					the_row(); 
        			// Get sub field values.
					$tarif_plein = get_sub_field("tarif_plein");
					$tarif_reduit = get_sub_field('tarif_reduit');
					$tarif_adherent = get_sub_field('tarif_adherent');
					$tarif_enfant = get_sub_field('tarif_enfant');
					$msg_reduit = null;
					
					if( $tarif_plein ) {
						$tarif .= sprintf ('Plein tarif &nbsp; %s €', $tarif_plein);
					} 
					if( $tarif_adherent ) {
						$tarif .= sprintf ("%s Adhérent &nbsp; %s €", ($tarif === "")?"":" / ", $tarif_adherent);
					}
					if( $tarif_reduit ) {
						$tarif .= sprintf ("%s Tarif réduit &nbsp; %s €", ($tarif === "")?"":" / ", $tarif_reduit);
						$msg_reduit = get_field("message_tarif_reduit", "option");
					}
					if( $tarif_enfant ) {
						$tarif .= sprintf ("%s -16 ans &nbsp; %s €", ($tarif === "")?"":" / ", $tarif_enfant);
					}
				}
			break;
				
			case "none":
			break;
		}
		if ($tarif <> '') {
			$tarif = sprintf('<p class="tarif">%s</p>', $tarif);
			if ($msg_reduit) {
				$tarif .= sprintf("<span class='msg_tarif_reduit'>%s</span>", $msg_reduit);
			}
		//	$tarif .= "</p>";
		}
		return $tarif;
	}

	function lien_intervenants($content) {
		$invites = get_field('invites');
		if( $invites ): 
			foreach( $invites as $inviteID ): 
				$i_permalink = get_permalink( $inviteID );
				$i_title = get_the_title( $inviteID );
				$len = strlen($i_title);
				$pos = strpos($content, $i_title);
				$replacestr = sprintf("<a class='lien_invite' href='%s' title='%s'>%s</a>", esc_url( $i_permalink ), $i_title, $i_title);
				$content = substr_replace($content, $replacestr, $pos, $len);
			endforeach; 
		endif;
		return $content;
	}

	function extra_content( $content ) {
		$content_billetterie = "";
		$content_programme = "";
		$content_invites = "";

		if ( is_single() && in_the_loop() && is_main_query() ) {
			if (in_category("Programme")) {
				$content = lien_intervenants($content);
				if (manid_check_current_edition(get_the_ID())) {
					// Ajout du tarif
					$tarifs = display_tarifs();
					if($tarifs <> "") {
						$content_billetterie .= $tarifs;
					}
					/* ajout du bouton de billetterie 
					 * si l'édition de l'évènement est l'édition en cours et si l'évènement n'est pas passé*/
					$prog_date = strtotime(get_field('prog_date'));
					$billetterie_date = strtotime( get_field( 'date_ouverture_billetterie', 'option'));
					if ($prog_date > time()) {
						/* la date est à venir */
						if ( $billetterie_date > time()) { 
							/* la billetterie n'est pas encore ouverte */ 
							$content_billetterie .= do_shortcode(get_field( 'message_billetterie_pre-ouverture', 'option'));
						} else {
							// affiche le bouton billetterie
							$content_billetterie .= do_shortcode( '[display-posts post_type="page" id="4758" include_title="false" include_content="true" wrapper="div"]' );
						}
					} else {
						$content_billetterie .= get_field( 'message_billetterie_fermee', 'option' );
					}
				}
			}	
			/* ajout des podcasts et vidéos */
		
			// Ajouté par CMA : affiche le podcast dans les pages Programme si il existe
			if ( in_category('Programme') && get_field('podcast_id') ) {
				$audio_player = "";
				$title = "";
	
				$title = get_field( 'titre_podcast');
				if ($title) {
					$content_programme .= "<h4>" . $title . "</h4>";
				}
				global $ss_podcasting;
				$episode_id = get_field('podcast_id');
				$audio_player = $ss_podcasting->episode_meta( $episode_id );
				$content_programme .= $audio_player;
			}

			//Ajouté par CMA : affiche le contenus additionel des invités
			if (in_category('Invités')) {
				$content_invites .= display_invites_content();
			
				/* Ajouté par CMA : afficher la liste des participations d'un invité 
				* TODO : filtrer sur invité
				*/
				if (get_field('display_title') && get_field("hide_previous_occurrence") != "Oui") {
					$content_invites .= manid_get_participations_list (get_the_ID());
				}

				if ($content_invites !== "") {
					$content_invites = sprintf("<div class='cma-extra-content'>%s</div>", $content_invites);
				}
			}
			$content .= $content_billetterie . $content_programme . $content_invites;
		}
		return $content;
	}

	add_filter('the_content','extra_content');

	// Ajout du Caption de la featured image dans le Grid Builder de WPBakery Page Builder
	// code extrait de https://kb.wpbakery.com/docs/developers-how-tos/adding-custom-shortcode-to-grid-builder/

	add_filter( 'vc_grid_item_shortcodes', 'mfid_add_grid_shortcodes' );
	
	function mfid_add_grid_shortcodes( $shortcodes ) {
	 	$shortcodes['vc_featured_caption'] = array(
			'name' => __( 'Légende de l\'image', 'my-text-domain' ),
			'base' => 'vc_featured_caption',
			'category' => __( 'Perso', 'my-text-domain' ),
			'description' => __( 'Affiche la légende de l\'image mise en avant de l\'article', 'my-text-domain' ),
			'post_type' => Vc_Grid_Item_Editor::postType(),
		);
		$shortcodes['vc_content'] = array(
			'name' => __( 'Contenu de l\'article', 'my-text-domain' ),
			'base' => 'vc_content',
			'category' => __( 'Perso', 'my-text-domain' ),
			'description' => __( 'Affiche la contenu de l\'article', 'my-text-domain' ),
			'post_type' => Vc_Grid_Item_Editor::postType(),
		);	 
		 return $shortcodes;
	}
	// output function
	add_shortcode( 'vc_featured_caption', 'vc_featured_caption_render' );
	add_shortcode( 'vc_content', 'vc_content_render' );
	
	function vc_featured_caption_render($atts, $content, $tag) {
	 	return '<span class="mfid-featured-caption">{{ featured_caption }}</span>';
	}
	function vc_content_render($atts, $content, $tag) {
		return '<span class="mfid-content">{{ content }}</span>';
   }
	   
	add_filter( 'vc_gitem_template_attribute_featured_caption', 'mfid_template_attribute_featured_caption', 10, 2 );
	
	function mfid_template_attribute_featured_caption( $value, $data ) {
		/**
			* @var Wp_Post $post
			* @var string $data
			*/
	 	extract( array_merge( array(
			'post' => null,
			'data' => '',
	 	), $data ) );
	 
	 	return get_the_excerpt( get_post_thumbnail_id( $post->ID ));
	}
	add_filter( 'vc_gitem_template_attribute_content', 'mfid_template_attribute_content', 10, 2 );
	
	function mfid_template_attribute_content ( $value, $data ) {
		/**
			* @var Wp_Post $post
			* @var string $data
			*/
	 	extract( array_merge( array(
			'post' => null,
			'data' => '',
	 	), $data ) );
	 
	 	return get_the_content("");
	}

	// allow recurring calls to display-posts
	// from : https://displayposts.com/2019/01/04/enable-display-posts-within-post-listing/
	add_filter( 'display_posts_shortcode_inception_override', '__return_false' );
	

	// AJout CMA : Champs en savoir plus dans les pages invités
	function display_invites_content () {
		// loop autour des liens externes
		$extra_more_content = "";
		$extra_more_content_externes = "";
		$extra_more_content_festival = "";
		if( have_rows('externes') ) {
			$articles = "";
			$videos = "";
			while( have_rows('externes') ): the_row(); 
				if( get_row_layout() == 'articles_media_' ) {
					if( have_rows('article_media') ) {
						$articles .= "<div class='mfid-more_articles'><ul>";
						while( have_rows('article_media') ): the_row();
							$articles .= sprintf( "<li><a href='%s' title='%s' target='_blank'>%s</a>", get_sub_field("media"), get_sub_field('titre'), get_sub_field("titre") );
						endwhile;
						$articles .= "</ul></div>";
					}	
				} elseif( get_row_layout() == 'articles_externes_' ) {
					if( have_rows('article_externe') ) {
						$articles .= "<div class='mfid-more_articles'><ul>";
						while( have_rows('article_externe') ): the_row();
							$articles .= sprintf( "<li><a href='%s' title='%s' target='_blank'>%s</a>", get_sub_field("url"), get_sub_field('titre'), get_sub_field("titre") );
						endwhile;
						$articles .= "</ul></div>";
					}	
				} elseif( get_row_layout() == 'audio_media_' ) {
					if( have_rows('audio_media') ) {
						$videos .= "<div class='mfid-more_audio'><ul>";
						while( have_rows('audio_media') ): the_row();
//							$videos .= sprintf( "<li><a href='%s' title='%s' target='_blank'>%s</a>", get_sub_field("media"), get_sub_field('titre'), get_sub_field("titre") );
						endwhile;
						$videos .= "</ul></div>";
					}	
				} elseif( get_row_layout() == 'audio_externe_' ) { 
					if( have_rows('audio_externe') ) {
						$videos .= "<div class='mfid-more_audio'><ul>";
						while( have_rows('audio_externe') ): the_row();
							$videos .= sprintf( "<li>%s</li><div class='embed-container'>%s</div>", get_sub_field("titre"), get_sub_field('data') );
						endwhile;
						$videos .= "</ul></div>";
					}	
				} elseif( get_row_layout() == 'video_externe_' ) { 
					if( have_rows('video_externe') ) {
						$videos .= "<div class='mfid-more_video'><ul>";
						while( have_rows('video_externe') ): the_row();
							$embed = get_sub_field('url');
							if ($embed) {
								$title = get_sub_field( 'titre');
								if ($title) {
									$videos .= "<li>" . $title . "</li><p>&nbsp;</p>";
								}
								if ( preg_match( "[audio", $embed) ) {
									$videos .= do_shortcode( $embed );
								} else { 
									$videos .= "<div class='embed-container'>" . $embed . "</div>";
								}
							}
						endwhile;
						$videos .= "</ul></div>";
					}	
				} 
			endwhile;
			if( $articles <> "") {
				$extra_more_content_externes .= "<h4>A lire</h4>" . $articles;
			}
			if( $videos <> "") {
				$extra_more_content_externes .= "<h4>A voir, à écouter</h4>" . $videos;
			}
		} elseif( have_rows('festival') ) {
			$videos = "";
			$audios = "";
			while( have_rows('festival') ): the_row(); 
				if( get_row_layout() == 'audio_festival_' ) {
					if( have_rows('audio_festival') ) {
						$audios .= "<div class='mfid-more_audio'><ul>";
						while( have_rows('audio_festival') ): the_row();
							$audios .= "<li>" . get_sub_field( 'titre') . "</li>";

							global $ss_podcasting;
							$episode_id = get_sub_field('podcast_id');
							$audio_player = $ss_podcasting->episode_meta( $episode_id );
							$audios .= $audio_player;
						endwhile;
						$audios .= "</ul></div>";
					}	
				} elseif( get_row_layout() == 'video_festival_' ) { 
					if( have_rows('video_festival') ) {
						$videos .= "<div class='mfid-more_video'><ul>";
						while( have_rows('video_festival') ): the_row();
							$embed = get_sub_field('url');
							if ($embed) {
								$title = get_sub_field( 'titre');
								if ($title) {
									$videos .= "<li>" . $title . "</li><p>&nbsp;</p>";
								}
								if ( preg_match( "[audio", $embed) ) {
									$videos .= do_shortcode( $embed );
								} else { 
									$videos .= "<div class='embed-container'>" . $embed . "</div>";
								}
							}
						endwhile;
						$videos .= "</ul></div>";
					}	
				} 
			endwhile;
			if( $audios <> "" || $videos <> "") {
				$extra_more_content_festival = "<h4>A (re)voir, à (ré)écouter</h4>" . $videos . $audios;
			}
		}
		if ($extra_more_content_externes <> "" || $extra_more_content_festival <> "") {
			$extra_more_content = "<h3>En savoir plus</h3>" . $extra_more_content_externes . $extra_more_content_festival;
		}
		return $extra_more_content; 
	}	

	
	/* option de menu Admin pour La Manufacture 
	 * via ACF
	 */

	add_action('acf/init', 'manid_opt_init');
	function manid_opt_init() {
	
		// Check function exists.
		if( function_exists('acf_add_options_page') ) {
	
			// Register options page.
			$option_page = acf_add_options_page(array(
				'page_title'    => __('Options La Manufacture d\'idées'),
				'menu_title'    => __('La Manufacture d\'idées'),
				'menu_slug'     => 'manid-options',
				'capability'    => 'edit_posts',
				'redirect'      => false
			));
		}
	}
?>