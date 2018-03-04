
<?php
// à ajouter dans functions.php 
    /*
	 * remplace la template de Tribe Event
	 */
	function replace_tribe_events_calendar_stylesheet() {
   		$styleUrl = get_bloginfo('template_url') . '/cma-manid-tribe-events-theme.css';
   		return $styleUrl;
	}
	//add_filter('tribe_events_stylesheet_url', 'replace_tribe_events_calendar_stylesheet');

	//tribe_event_featured_image_size filter to modify image size return thumbnail
	//tribe_events_featured_image_wrap filter to wrap image return true
	//tribe_event_featured_image filter to modify HTML return html
	function cma_manid_tribe_events_event_schedule_details( $schedule = null, $event = null, $before = '', $after = '' ) {
		// la fonction est modifiée pour n'afficher que l'heure
		if ( is_null( $event ) ) {
			global $post;
			$event = $post;
		}

		if ( is_numeric( $event ) ) {
			$event = get_post( $event );
		}

		$inner                    = '<span class="tribe-event-date-start">';
		$format                   = '';
		$date_without_year_format = tribe_get_date_format();
		$date_with_year_format    = tribe_get_date_format( true );
		$time_format              = get_option( 'time_format' );
		$datetime_separator       = tribe_get_option( 'dateTimeSeparator', ' @ ' );
		$time_range_separator     = tribe_get_option( 'timeRangeSeparator', ' - ' );

		$settings = array(
			'show_end_time' => true,
			'time'          => true,
		);

		$settings = wp_parse_args( apply_filters( 'tribe_events_event_schedule_details_formatting', $settings ), $settings );
		if ( ! $settings['time'] ) {
			$settings['show_end_time'] = false;
		}

		/**
		 * @var $show_end_time
		 * @var $time
		 */
		extract( $settings );

		$format = $date_with_year_format;

		/**
		 * If a yearless date format should be preferred.
		 *
		 * By default, this will be true if the event starts and ends in the current year.
		 *
		 * @param bool    $use_yearless_format
		 * @param WP_Post $event
		 */
		$use_yearless_format = apply_filters( 'tribe_events_event_schedule_details_use_yearless_format',
			(
				tribe_get_start_date( $event, false, 'Y' ) === date_i18n( 'Y' )
				&& tribe_get_end_date( $event, false, 'Y' ) === date_i18n( 'Y' )
			),
			$event
		);

		if ( $use_yearless_format ) {
			$format = $date_without_year_format;
		}

		if ( tribe_event_is_multiday( $event ) ) { // multi-date event

			$format2ndday = apply_filters( 'tribe_format_second_date_in_range', $format, $event );

			if ( tribe_event_is_all_day( $event ) ) {
				$inner .= tribe_get_start_date( $event, true, $format );
				$inner .= '</span>' . $time_range_separator;
				$inner .= '<span class="tribe-event-date-end">';

				$end_date_full = tribe_get_end_date( $event, true, Tribe__Date_Utils::DBDATETIMEFORMAT );
				$end_date_full_timestamp = strtotime( $end_date_full );

				// if the end date is <= the beginning of the day, consider it the previous day
				if ( $end_date_full_timestamp <= strtotime( tribe_beginning_of_day( $end_date_full ) ) ) {
					$end_date = tribe_format_date( $end_date_full_timestamp - DAY_IN_SECONDS, false, $format2ndday );
				} else {
					$end_date = tribe_get_end_date( $event, false, $format2ndday );
				}

				$inner .= $end_date;
			} else {
				$inner .= tribe_get_start_date( $event, false, $format ) . ( $time ? $datetime_separator . tribe_get_start_date( $event, false, $time_format ) : '' );
				$inner .= '</span>' . $time_range_separator;
				$inner .= '<span class="tribe-event-date-end">';
				$inner .= tribe_get_end_date( $event, false, $format2ndday ) . ( $time ? $datetime_separator . tribe_get_end_date( $event, false, $time_format ) : '' );
			}
		} elseif ( tribe_event_is_all_day( $event ) ) { // all day event
			// Modif CMA
			//$inner .= tribe_get_start_date( $event, true, $format );
			$inner .= "Toute la journée";
		} else { // single day event
			if ( tribe_get_start_date( $event, false, 'g:i A' ) === tribe_get_end_date( $event, false, 'g:i A' ) ) { // Same start/end time
				// Modif CMA
				//$inner .= tribe_get_start_date( $event, false, $format ) . ( $time ? $datetime_separator . tribe_get_start_date( $event, false, $time_format ) : '' );
				$inner .= ( $time ? tribe_get_start_date( $event, false, $time_format ) : 'Toute la journée' );
			} else { // defined start/end time
				// Modif CMA
				//$inner .= tribe_get_start_date( $event, false, $format ) . ( $time ? $datetime_separator . tribe_get_start_date( $event, false, $time_format ) : '' );
				$inner .= ( $time ? tribe_get_start_date( $event, false, $time_format ) : '' );
				$inner .= '</span>' . ( $show_end_time ? $time_range_separator : '' );
				$inner .= '<span class="tribe-event-time">';
				$inner .= ( $show_end_time ? tribe_get_end_date( $event, false, $time_format ) : '' );
			}
		}

		$inner .= '</span>';

		/**
		 * Provides an opportunity to modify the *inner* schedule details HTML (ie before it is
		 * wrapped).
		 *
		 * @param string $inner_html  the output HTML
		 * @param int    $event_id    post ID of the event we are interested in
		 */
		$inner = apply_filters( 'tribe_events_event_schedule_details_inner', $inner, $event->ID );

		// Wrap the schedule text
		$schedule = $before . $inner . $after;

		return $schedule;
	}

	add_filter( 'tribe_events_event_schedule_details', 'cma_manid_tribe_events_event_schedule_details');

	function cma_manid_tribe_events_list_show_date_headers() {
		return false;
	}

    add_filter('tribe_events_list_show_date_headers', 'cma-manid_tribe_events_list_show_date_headers');
    
    /**
	 * Redirect event category requests to list view.
	 *
	 * @param $query
	 */
	function cma_manid_use_list_view_for_categories( $query ) {
		// Disregard anything except a main archive query
		if ( is_admin() || ! $query->is_main_query() || ! is_archive() ) return;

		// We only want to catch *event* category requests being issued
		// against something other than list view
		if ( ! $query->get( 'tribe_events_cat' ) ) return;
		if ( tribe_is_list_view() ) return;

		// Get the term object
		$term = get_term_by( 'slug', $query->get( 'tribe_events_cat' ), Tribe__Events__Main::TAXONOMY );

		// If it's invalid don't go any further
		if ( ! $term ) return;

		// Get the list-view taxonomy link and redirect to it
		header( 'Location: ' . tribe_get_listview_link( $term->term_id ) );
		exit();
	}

	// Use list view for category requests by hooking into pre_get_posts for event queries
    add_action( 'tribe_events_pre_get_posts', 'cma_manid_use_list_view_for_categories' );
    
?>
