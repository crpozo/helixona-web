<?php
/**
 * WEBSITE 2026 · STAGE 1 (navigation & sitemap v3)
 *
 * Template : page-templates/landing-page.php  ·  Data: landings/{slug}.php
 * Menu     : nav/main-2026.php
 *
 * Everything here stays dormant until the switch is on: pages are created as
 * drafts, the old menu and URLs keep working. Turn it on per site in
 * Settings → Reading → "Website 2026 (stage 1)" (stored in that site's
 * database, so a staging copy can be on while production stays off), or with
 * define( 'HX_NAV_2026', true ); in wp-config.php. Turning it on publishes the
 * pages, swaps the header menu and turns on the redirects.
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'HX_LANDING_TEMPLATE', 'page-templates/landing-page.php' );

function hx_nav_2026_on() {
	return ( defined( 'HX_NAV_2026' ) && HX_NAV_2026 ) || '1' === (string) get_option( 'hx_nav_2026', '0' );
}

/** Settings → Reading: the stage 1 switch. */
add_action( 'admin_init', function () {
	register_setting( 'reading', 'hx_nav_2026', [
		'type'              => 'string',
		'default'           => '0',
		'sanitize_callback' => function ( $v ) {
			return $v ? '1' : '0';
		},
	] );
	add_settings_field( 'hx_nav_2026', 'Website 2026 (stage 1)', function () {
		$forced = defined( 'HX_NAV_2026' ) && HX_NAV_2026;
		?>
		<label>
			<input type="checkbox" name="hx_nav_2026" value="1" <?php checked( hx_nav_2026_on() ); ?> <?php disabled( $forced ); ?>>
			Publish the new pages, switch the header to "Main Menu 2026" and turn on the redirects
		</label>
		<p class="description">
			<?php if ( $forced ) : ?>
				Forced on by HX_NAV_2026 in wp-config.php.
			<?php else : ?>
				Only affects this site. Turning it off again stops the redirects, but pages stay published and the header keeps the new menu (switch it back in Elementor if needed).
			<?php endif; ?>
		</p>
		<?php
	}, 'reading' );
} );

define( 'HX_NAV_2026_MENU', 'Main Menu 2026' );

function hx_landing_is_page() {
	return is_page() && is_page_template( HX_LANDING_TEMPLATE );
}

function hx_landing_get_all() {
	static $landings = null;
	if ( null !== $landings ) {
		return $landings;
	}
	$landings = [];
	foreach ( (array) glob( get_stylesheet_directory() . '/landings/*.php' ) as $file ) {
		$slug = sanitize_key( basename( $file, '.php' ) );
		$lp   = include $file;
		if ( $slug && is_array( $lp ) && ! empty( $lp['title'] ) ) {
			$lp['slug']        = $slug;
			$landings[ $slug ] = $lp;
		}
	}
	return $landings;
}

function hx_landing_get_data( $slug = null ) {
	if ( null === $slug ) {
		$page_id = get_queried_object_id();
		$slug    = get_post_meta( $page_id, 'hx_landing', true );
		if ( ! $slug ) {
			$slug = get_post_field( 'post_name', $page_id );
		}
	}
	$slug     = sanitize_key( (string) $slug );
	$landings = hx_landing_get_all();
	return isset( $landings[ $slug ] ) ? $landings[ $slug ] : null;
}

/**
 * Create a page per landing file: draft while the switch is off, published
 * once it is on (only pages this theme created get published automatically).
 * A landing with 'page_status' => 'draft' always stays a draft (review it with
 * Preview; publish it by hand once approved).
 */
function hx_landing_is_draft_only( $lp ) {
	return 'draft' === ( $lp['page_status'] ?? '' );
}

function hx_landing_ensure_pages() {
	foreach ( hx_landing_get_all() as $slug => $lp ) {
		$page = get_page_by_path( $slug, OBJECT, 'page' );
		if ( ! $page ) {
			$page_id = hx_ensure_page( $slug, $lp['title'], HX_LANDING_TEMPLATE, ( hx_nav_2026_on() && ! hx_landing_is_draft_only( $lp ) ) ? 'publish' : 'draft' );
			if ( $page_id ) {
				update_post_meta( $page_id, '_hx_landing_auto', 1 );
			}
		} elseif ( hx_nav_2026_on() && ! hx_landing_is_draft_only( $lp ) && 'draft' === $page->post_status && get_post_meta( $page->ID, '_hx_landing_auto', true ) ) {
			wp_update_post( [ 'ID' => $page->ID, 'post_status' => 'publish' ] );
		}
	}
}
add_action( 'init', 'hx_landing_ensure_pages', 20 );

/** Internal paths ("/booking/") → site URL; anything else as is. */
function hx_landing_url( $url ) {
	return ( is_string( $url ) && 0 === strpos( $url, '/' ) && 0 !== strpos( $url, '//' ) ) ? home_url( $url ) : $url;
}

/**
 * Copy placeholder. Editors see a dashed box saying what goes there;
 * visitors see $public (e.g. "Pricing coming soon") or nothing.
 */
function hx_landing_slot( $label, $public = '' ) {
	if ( current_user_can( 'edit_pages' ) ) {
		return '<div class="hx-slot"><span class="hx-slot__tag">Pending copy</span>' . esc_html( $label ) . '</div>';
	}
	return $public ? '<p class="hx-slot-public">' . esc_html( $public ) . '</p>' : '';
}

/**
 * Render a copy value: a string (wrapped in $tag) or [ 'slot' => '…', 'public' => '…' ].
 */
function hx_landing_text( $value, $tag = 'p', $class = 'hx-job__p' ) {
	if ( is_array( $value ) && isset( $value['slot'] ) ) {
		return hx_landing_slot( $value['slot'], $value['public'] ?? '' );
	}
	if ( '' === (string) $value ) {
		return '';
	}
	return '<' . $tag . ( $class ? ' class="' . esc_attr( $class ) . '"' : '' ) . '>' . wp_kses_post( $value ) . '</' . $tag . '>';
}

/** A copy field as a list: one string or slot → [ it ]; a list stays a list. */
function hx_landing_list( $value ) {
	if ( '' === $value || [] === $value || null === $value ) {
		return [];
	}
	return ( is_array( $value ) && ! isset( $value['slot'] ) ) ? $value : [ $value ];
}

function hx_landing_buttons( $ctas, $center = false ) {
	if ( empty( $ctas ) ) {
		return '';
	}
	$html = '<div class="hx-job__actions' . ( $center ? ' hx-job__actions--center' : '' ) . '">';
	foreach ( $ctas as $i => $cta ) {
		$style = ! empty( $cta['style'] ) ? $cta['style'] : ( 0 === $i ? 'gold' : 'ghost' );
		$html .= '<a class="hx-btn hx-btn--' . esc_attr( $style ) . '" href="' . esc_url( hx_landing_url( $cta['url'] ) ) . '">' . esc_html( $cta['label'] ) . '</a>';
	}
	return $html . '</div>';
}

function hx_landing_enqueue_assets() {
	if ( ! hx_landing_is_page() ) {
		return;
	}
	wp_enqueue_style( 'hx-job-landing', get_stylesheet_directory_uri() . '/assets/css/job-landing.css', [ 'hello-elementor-child-style' ], hx_asset_ver( 'assets/css/job-landing.css' ) );
	wp_enqueue_style( 'hx-landing-page', get_stylesheet_directory_uri() . '/assets/css/landing-page.css', [ 'hx-job-landing' ], hx_asset_ver( 'assets/css/landing-page.css' ) );
}
add_action( 'wp_enqueue_scripts', 'hx_landing_enqueue_assets', 30 );

add_filter( 'body_class', function ( $classes ) {
	if ( hx_landing_is_page() ) {
		$classes[] = 'hx-landing-page';
	}
	if ( hx_nav_2026_on() ) {
		$classes[] = 'hx-nav-2026'; // header layout for the 5-item menu (assets/css/hx-menu.css)
	}
	return $classes;
} );

function hx_landing_print_meta() {
	if ( ! hx_landing_is_page() ) {
		return;
	}
	if ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' ) ) {
		return;
	}
	$lp = hx_landing_get_data();
	if ( ! $lp || empty( $lp['seo_description'] ) ) {
		return;
	}
	?>
	<meta name="description" content="<?php echo esc_attr( $lp['seo_description'] ); ?>">
	<meta property="og:type" content="website">
	<meta property="og:title" content="<?php echo esc_attr( $lp['title'] . ' – Helixona' ); ?>">
	<meta property="og:description" content="<?php echo esc_attr( $lp['seo_description'] ); ?>">
	<meta property="og:url" content="<?php echo esc_url( get_permalink() ); ?>">
	<?php
}
add_action( 'wp_head', 'hx_landing_print_meta', 6 );

/**
 * 301 retired URLs (landing files' redirect_from) to their new page.
 * Runs on 404s too, so it still works once the old page is deleted.
 */
function hx_landing_redirects() {
	if ( ! hx_nav_2026_on() || is_admin() || ! ( is_page() || is_404() ) ) {
		return;
	}
	$current = trailingslashit( '/' . trim( (string) wp_parse_url( add_query_arg( [] ), PHP_URL_PATH ), '/' ) );
	foreach ( hx_landing_get_all() as $slug => $lp ) {
		foreach ( (array) ( $lp['redirect_from'] ?? [] ) as $old ) {
			if ( trailingslashit( '/' . trim( $old, '/' ) ) !== $current ) {
				continue;
			}
			$target = hx_service_page_url( $slug );
			if ( $target && untrailingslashit( (string) wp_parse_url( $target, PHP_URL_PATH ) ) !== untrailingslashit( $current ) ) {
				wp_safe_redirect( $target, 301 );
				exit;
			}
		}
	}
}
add_action( 'template_redirect', 'hx_landing_redirects', 1 );

/* --------------------------------------------------------------------------
   Header menu 2026: built once from nav/main-2026.php as a NEW menu (the old
   one is left untouched as a backup), then assigned to the theme's header
   location and to the nav-menu widgets of the Elementor header template.
   The header's "Schedule now" button becomes "Book Now".
   -------------------------------------------------------------------------- */

function hx_nav_2026_add_items( $menu_id, $items, $parent = 0, &$position = 0 ) {
	foreach ( $items as $item ) {
		$args = [
			'menu-item-title'     => $item['title'],
			'menu-item-status'    => 'publish',
			'menu-item-parent-id' => $parent,
			'menu-item-position'  => ++$position, // one running order across levels (parent, its children, next parent…)
		];
		$page = ! empty( $item['page'] ) ? get_page_by_path( $item['page'], OBJECT, 'page' ) : null;
		if ( $page ) {
			$args['menu-item-type']      = 'post_type';
			$args['menu-item-object']    = 'page';
			$args['menu-item-object-id'] = $page->ID;
		} else {
			$args['menu-item-type'] = 'custom';
			$args['menu-item-url']  = ! empty( $item['url'] ) ? hx_landing_url( $item['url'] ) : ( ! empty( $item['page'] ) ? home_url( '/' . $item['page'] . '/' ) : '#' );
		}
		$id = wp_update_nav_menu_item( $menu_id, 0, $args );
		if ( is_wp_error( $id ) ) {
			return false;
		}
		if ( ! empty( $item['children'] ) && ! hx_nav_2026_add_items( $menu_id, $item['children'], $id, $position ) ) {
			return false;
		}
	}
	return true;
}

function hx_cu_nav_2026() {
	$file  = get_stylesheet_directory() . '/nav/main-2026.php';
	$items = file_exists( $file ) ? include $file : null;
	if ( ! is_array( $items ) ) {
		return false;
	}
	// The new pages must be live first, so menu items link to them (not to custom URLs).
	foreach ( hx_landing_get_all() as $slug => $lp ) {
		if ( ! hx_landing_is_draft_only( $lp ) && ! hx_service_page_url( $slug ) ) {
			return false;
		}
	}

	$menu = wp_get_nav_menu_object( HX_NAV_2026_MENU );
	if ( ! $menu ) {
		$menu_id = wp_create_nav_menu( HX_NAV_2026_MENU );
		if ( is_wp_error( $menu_id ) ) {
			return false;
		}
		if ( ! hx_nav_2026_add_items( $menu_id, $items ) ) {
			wp_delete_nav_menu( $menu_id ); // half-built: start over next time
			return false;
		}
		$menu = wp_get_nav_menu_object( $menu_id );
	}

	// Theme location (Hello Elementor's own header, used when no Elementor header is active).
	$locations           = (array) get_theme_mod( 'nav_menu_locations', [] );
	$locations['menu-1'] = (int) $menu->term_id;
	set_theme_mod( 'nav_menu_locations', $locations );

	// Elementor header template(s): nav-menu widgets → new menu, "Schedule now" → "Book Now".
	return hx_cu_edit_header_nodes( function ( &$node ) use ( $menu ) {
		$type = $node['widgetType'] ?? '';
		if ( 'nav-menu' === $type && ( $node['settings']['menu'] ?? '' ) !== $menu->slug ) {
			$node['settings']['menu'] = $menu->slug;
			return true;
		}
		if ( 'button' === $type && isset( $node['settings']['text'] ) && preg_match( '/^\s*schedule\s+now\s*$/i', (string) $node['settings']['text'] ) ) {
			$node['settings']['text'] = 'Book Now';
			return true;
		}
		return false;
	} );
}
add_filter( 'hx_content_updates', function ( $updates ) {
	if ( hx_nav_2026_on() ) {
		$updates['nav:main-2026'] = 'hx_cu_nav_2026';
	}
	return $updates;
} );

/* --------------------------------------------------------------------------
   Home "Meet Our Physicians" (stage 1 review):
   - bios: Eduard's used blank lines, Thomas's <br><br>, Mira's bare <p>s.
     Normalize all three to plain paragraphs and tag the widget "hx-bio",
     which style.css spaces evenly (between the old wide and tight looks).
   - Mira's photo: the file was replaced under the same URL (cut-out → photo
     with background); browsers that cached the old cut-out keep showing it.
     Point the widget at the versioned URL from team/mira-aguilar.php.
   -------------------------------------------------------------------------- */

function hx_bio_normalize( $html ) {
	$html = preg_replace( '#<(p|div)>(?:\s|&nbsp;|\x{00A0}|<br\s*/?>)*</\1>#iu', '', $html ); // blank lines
	$html = preg_replace( '#<div>(.*?)</div>#is', '<p>$1</p>', $html );
	$html = preg_replace( '#(?:\s*<br\s*/?>\s*){2,}#i', '</p><p>', $html );
	return trim( $html );
}

function hx_cu_home_physicians() {
	$home_id = (int) get_option( 'page_on_front' );
	$raw     = $home_id ? get_post_meta( $home_id, '_elementor_data', true ) : '';
	if ( ! $raw ) {
		return true; // no Elementor home on this install
	}
	$data = json_decode( $raw, true );
	if ( ! is_array( $data ) ) {
		return false;
	}
	$mira    = function_exists( 'hx_team_get_member' ) ? hx_team_get_member( 'mira-aguilar' ) : null;
	$markers = [ 'Dr. Eduard Drannikov is', 'Dr. Thomas Bakman', 'Connecting the dots' ];
	$found   = 0;
	$changed = false;
	$walk    = function ( &$nodes ) use ( &$walk, &$changed, &$found, $markers, $mira ) {
		foreach ( $nodes as &$node ) {
			$type = $node['widgetType'] ?? '';
			if ( 'text-editor' === $type && isset( $node['settings']['editor'] ) ) {
				$text = wp_strip_all_tags( (string) $node['settings']['editor'] );
				foreach ( $markers as $marker ) {
					if ( false === strpos( $text, $marker ) ) {
						continue;
					}
					$found++;
					$html    = hx_bio_normalize( (string) $node['settings']['editor'] );
					$classes = trim( (string) ( $node['settings']['_css_classes'] ?? '' ) );
					if ( false === strpos( ' ' . $classes . ' ', ' hx-bio ' ) ) {
						$node['settings']['_css_classes'] = trim( $classes . ' hx-bio' );
						$changed                          = true;
					}
					if ( $html !== $node['settings']['editor'] ) {
						$node['settings']['editor'] = $html;
						$changed                    = true;
					}
					break;
				}
			} elseif ( 'image' === $type && $mira && ! empty( $node['settings']['image']['url'] )
				&& false !== strpos( (string) $node['settings']['image']['url'], 'Mira-Aguilar' )
				&& $node['settings']['image']['url'] !== $mira['image'] ) {
				$node['settings']['image']['url'] = esc_url_raw( $mira['image'] );
				$changed                           = true;
			}
			if ( ! empty( $node['elements'] ) && is_array( $node['elements'] ) ) {
				$walk( $node['elements'] );
			}
		}
	};
	$walk( $data );
	if ( $changed ) {
		update_post_meta( $home_id, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );
		delete_post_meta( $home_id, '_elementor_css' );
		delete_post_meta( $home_id, '_elementor_element_cache' );
		hx_cu_flush_elementor();
	}
	return $found > 0 || false === strpos( $raw, 'Drannikov' ); // retry later if the bios aren't on the page yet
}
add_filter( 'hx_content_updates', function ( $updates ) {
	if ( hx_nav_2026_on() ) {
		$updates['home:physician-bios-v1'] = 'hx_cu_home_physicians';
	}
	return $updates;
} );

/* --------------------------------------------------------------------------
   Home hero: background video from YouTube → self-hosted MP4.
   YouTube overlays its own title, channel badge and pause button on embeds
   (even with controls off); a hosted MP4 plays with no UI. The MP4 must be in
   this site's Media Library at the path below (uploads/…); until it is, the
   update waits and retries.
   -------------------------------------------------------------------------- */

define( 'HX_HERO_VIDEO', '2026/10/Healthcare-Video.mp4' ); // relative to wp-content/uploads

function hx_cu_home_hero_video() {
	$home_id = (int) get_option( 'page_on_front' );
	$raw     = $home_id ? get_post_meta( $home_id, '_elementor_data', true ) : '';
	if ( ! $raw ) {
		return true; // no Elementor home on this install
	}
	$uploads = wp_upload_dir();
	if ( ! file_exists( trailingslashit( $uploads['basedir'] ) . HX_HERO_VIDEO ) ) {
		return false; // video not uploaded on this site yet
	}
	$mp4  = trailingslashit( $uploads['baseurl'] ) . HX_HERO_VIDEO;
	$data = json_decode( $raw, true );
	if ( ! is_array( $data ) ) {
		return false;
	}
	$changed = false;
	$walk    = function ( &$nodes ) use ( &$walk, &$changed, $mp4 ) {
		foreach ( $nodes as &$node ) {
			$link = (string) ( $node['settings']['background_video_link'] ?? '' );
			if ( $link && preg_match( '#youtu\.?be#i', $link ) ) {
				$node['settings']['background_video_link'] = esc_url_raw( $mp4 );
				$changed                                   = true;
			}
			if ( ! empty( $node['elements'] ) && is_array( $node['elements'] ) ) {
				$walk( $node['elements'] );
			}
		}
	};
	$walk( $data );
	if ( $changed ) {
		update_post_meta( $home_id, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );
		delete_post_meta( $home_id, '_elementor_css' );
		delete_post_meta( $home_id, '_elementor_element_cache' );
		hx_cu_flush_elementor();
	}
	return true;
}
add_filter( 'hx_content_updates', function ( $updates ) {
	if ( hx_nav_2026_on() ) {
		$updates['home:hero-video-mp4'] = 'hx_cu_home_hero_video';
	}
	return $updates;
} );

/* --------------------------------------------------------------------------
   Header buttons: one label everywhere. The tablet/laptop header said
   "Become a Client" while desktop said "Become a Patient" (the sitemap's term).
   -------------------------------------------------------------------------- */

/**
 * Edit every widget of the Elementor header template(s): $edit( &$node ) changes a node and
 * returns true if it changed it. Saves only the headers that changed, then flushes Elementor.
 */
function hx_cu_edit_header_nodes( callable $edit ) {
	$headers = get_posts( [
		'post_type'   => 'elementor_library',
		'post_status' => 'any',
		'numberposts' => -1,
		'meta_query'  => [ [ 'key' => '_elementor_template_type', 'value' => 'header' ] ], // phpcs:ignore WordPress.DB.SlowDBQuery
	] );
	foreach ( $headers as $header ) {
		$raw  = get_post_meta( $header->ID, '_elementor_data', true );
		$data = is_string( $raw ) ? json_decode( $raw, true ) : null;
		if ( ! is_array( $data ) ) {
			continue;
		}
		$changed = false;
		$walk    = function ( &$nodes ) use ( &$walk, &$changed, $edit ) {
			foreach ( $nodes as &$node ) {
				if ( $edit( $node ) ) {
					$changed = true;
				}
				if ( ! empty( $node['elements'] ) && is_array( $node['elements'] ) ) {
					$walk( $node['elements'] );
				}
			}
		};
		$walk( $data );
		if ( $changed ) {
			update_post_meta( $header->ID, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );
			delete_post_meta( $header->ID, '_elementor_css' );
			delete_post_meta( $header->ID, '_elementor_element_cache' );
		}
	}
	hx_cu_flush_elementor();
	return true;
}

/** Header button widgets whose label matches $label (regex); $edit( &$settings ) as above. */
function hx_cu_edit_header_buttons( $label, callable $edit ) {
	return hx_cu_edit_header_nodes( function ( &$node ) use ( $label, $edit ) {
		return 'button' === ( $node['widgetType'] ?? '' ) && isset( $node['settings']['text'] )
			&& preg_match( $label, (string) $node['settings']['text'] ) && $edit( $node['settings'] );
	} );
}

function hx_cu_header_become_patient() {
	return hx_cu_edit_header_buttons( '/^\s*become\s+a\s+client\s*$/i', function ( &$settings ) {
		$settings['text'] = 'Become a Patient';
		return true;
	} );
}
add_filter( 'hx_content_updates', function ( $updates ) {
	if ( hx_nav_2026_on() ) {
		$updates['nav:become-a-patient'] = 'hx_cu_header_become_patient';
	}
	return $updates;
} );

/* --------------------------------------------------------------------------
   Header "Book Now" → Book a Treatment flow (Dev Spec v1, Flow B): straight to
   the therapies list (/become-a-patient/#book-a-treatment) instead of /booking/.
   -------------------------------------------------------------------------- */

function hx_cu_header_book_treatment() {
	$page = function_exists( 'hx_patient_page_url' ) ? hx_patient_page_url() : '';
	if ( ! $page ) {
		return false; // page not published yet: retry later
	}
	$url = $page . '#book-a-treatment';
	return hx_cu_edit_header_buttons( '/^\s*(book\s+now|schedule\s+now)\s*$/i', function ( &$settings ) use ( $url ) {
		if ( ( $settings['link']['url'] ?? '' ) === $url ) {
			return false;
		}
		$settings['link']['url'] = $url;
		return true;
	} );
}
add_filter( 'hx_content_updates', function ( $updates ) {
	if ( hx_nav_2026_on() ) {
		$updates['nav:book-a-treatment'] = 'hx_cu_header_book_treatment';
	}
	return $updates;
} );

/* --------------------------------------------------------------------------
   Booking directory (/booking/): new filters (booking/filters-2026.php).
   Rewrites the filter strip and each card's data-category in the page's
   HTML widget; adds the clearance label to IV / EBOO cards. Links, calendars
   and phone numbers are untouched.
   -------------------------------------------------------------------------- */

function hx_booking_config() {
	$file = get_stylesheet_directory() . '/booking/filters-2026.php';
	$cfg  = file_exists( $file ) ? include $file : null;
	return is_array( $cfg ) ? $cfg : null;
}

/** Rewrite one HTML block of the booking directory. */
function hx_booking_transform_html( $html, $cfg ) {
	// 1) Filter strip.
	$buttons = '';
	foreach ( $cfg['filters'] as $key => $label ) {
		$buttons .= "\n\t\t" . '<button class="filter-btn' . ( 'all' === $key ? ' active' : '' ) . '" data-filter="' . esc_attr( $key ) . '">' . esc_html( $label ) . '</button>';
	}
	$html = preg_replace( '#<div class="mft-filters">.*?</div>#s', '<div class="mft-filters">' . $buttons . "\n\t</div>", $html, 1 );

	// 2) Cards: category from the title; clearance label on IV / EBOO.
	return preg_replace_callback(
		'#(<div class="mft-service-card" data-category=")([^"]*)(">)(.*?<h3\b[^>]*>(.*?)</h3>)#s',
		function ( $m ) use ( $cfg ) {
			$title = strtolower( wp_strip_all_tags( $m[5] ) );
			$cats  = $m[2];
			foreach ( $cfg['cards'] as $frag => $new ) {
				if ( false !== strpos( $title, $frag ) ) {
					$cats = $new;
					break;
				}
			}
			$head = $m[4];
			foreach ( $cfg['clearance']['match'] as $frag ) {
				if ( false !== strpos( $title, $frag ) ) {
					$head .= "\n\t\t" . '<p class="mft-clearance">' . esc_html( $cfg['clearance']['label'] ) . '</p>';
					break;
				}
			}
			return $m[1] . esc_attr( $cats ) . $m[3] . $head;
		},
		$html
	);
}

function hx_cu_booking_filters() {
	$cfg  = hx_booking_config();
	$page = get_page_by_path( 'booking', OBJECT, 'page' );
	if ( ! $cfg || ! $page ) {
		return true; // nothing to do on this install
	}
	$raw  = get_post_meta( $page->ID, '_elementor_data', true );
	$data = is_string( $raw ) ? json_decode( $raw, true ) : null;
	if ( ! is_array( $data ) ) {
		return ! $raw;
	}
	$changed = false;
	$walk    = function ( &$nodes ) use ( &$walk, &$changed, $cfg ) {
		foreach ( $nodes as &$node ) {
			$html = $node['settings']['html'] ?? null;
			if ( is_string( $html ) && false !== strpos( $html, 'mft-service-card' ) && false === strpos( $html, 'data-filter="iv-blood"' ) ) {
				$node['settings']['html'] = hx_booking_transform_html( $html, $cfg );
				$changed                  = true;
			}
			if ( ! empty( $node['elements'] ) && is_array( $node['elements'] ) ) {
				$walk( $node['elements'] );
			}
		}
	};
	$walk( $data );
	if ( $changed ) {
		update_post_meta( $page->ID, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );
		delete_post_meta( $page->ID, '_elementor_css' );
		delete_post_meta( $page->ID, '_elementor_element_cache' );
		hx_cu_flush_elementor();
	}
	return true;
}
add_filter( 'hx_content_updates', function ( $updates ) {
	if ( hx_nav_2026_on() ) {
		$updates['booking:filters-2026'] = 'hx_cu_booking_filters';
	}
	return $updates;
} );

/**
 * Booking page, front end: hide filters with no cards yet, and open a filter
 * from the URL (/booking/?filter=frequency-energy) — used by search results.
 * Runs after the existing filter script has bound its click handlers.
 */
add_action( 'wp_footer', function () {
	if ( ! hx_nav_2026_on() || ! is_page( 'booking' ) ) {
		return;
	}
	?>
	<script>
	document.addEventListener('DOMContentLoaded', function () {
		setTimeout(function () {
			var cards = document.querySelectorAll('.mft-services-grid .mft-service-card');
			var buttons = document.querySelectorAll('.mft-filters .filter-btn');
			if (!cards.length || !buttons.length) { return; }
			buttons.forEach(function (btn) {
				var f = btn.getAttribute('data-filter');
				if (f === 'all') { return; }
				var any = Array.prototype.some.call(cards, function (c) {
					return (' ' + (c.getAttribute('data-category') || '') + ' ').indexOf(' ' + f + ' ') !== -1;
				});
				if (!any) { btn.hidden = true; }
			});
			var wanted = new URLSearchParams(window.location.search).get('filter');
			var target = wanted && document.querySelector('.mft-filters .filter-btn[data-filter="' + wanted.replace(/[^a-z-]/g, '') + '"]');
			if (target && !target.hidden) {
				target.click();
				target.scrollIntoView({ block: 'center' });
			}
		}, 0);
	});
	</script>
	<?php
}, 30 );

/* --------------------------------------------------------------------------
   Site search (Website 2026 §8). Our own results page (page-templates/
   search-2026.php) replaces the blog-only archive while the switch is on:
   1) "Best matches" from search/keywords-2026.php (synonyms, nicknames,
      small typos), 2) pages and articles that contain the words,
   3) helpful suggestions when nothing matches.
   -------------------------------------------------------------------------- */

function hx_search_keywords() {
	static $map = null;
	if ( null === $map ) {
		$file = get_stylesheet_directory() . '/search/keywords-2026.php';
		$map  = file_exists( $file ) ? (array) include $file : [];
	}
	return $map;
}

/** Lowercase, no accents or punctuation, single spaces. */
function hx_search_normalize( $s ) {
	$s = strtolower( remove_accents( wp_strip_all_tags( (string) $s ) ) );
	$s = preg_replace( '/[^a-z0-9]+/', ' ', $s );
	return trim( preg_replace( '/\s+/', ' ', $s ) );
}

/**
 * Does query word $q stand for term word $t? Exact, singular/plural, two
 * swapped letters (slat → salt), or a small typo in a longer word (5+ letters:
 * 1 edit, 8+: 2). Words of 3 letters or less (iv, nad, h2, gut) must be exact.
 */
function hx_search_word_matches( $q, $t ) {
	if ( $q === $t || rtrim( $q, 's' ) === rtrim( $t, 's' ) ) {
		return true;
	}
	$len = strlen( $t );
	if ( $len < 4 || strlen( $q ) < 4 ) {
		return false;
	}
	if ( strlen( $q ) === $len ) {
		$diff = array_keys( array_diff_assoc( str_split( $q ), str_split( $t ) ) );
		if ( 2 === count( $diff ) && $diff[1] === $diff[0] + 1 && $q[ $diff[0] ] === $t[ $diff[1] ] && $q[ $diff[1] ] === $t[ $diff[0] ] ) {
			return true; // transposition
		}
	}
	if ( $len < 5 ) {
		return false; // cold ≠ mold
	}
	return levenshtein( $q, $t ) <= ( $len >= 8 ? 2 : 1 );
}

/**
 * Keyword-map entries matching a query, best first.
 *
 * @return array[] targets (deduplicated)
 */
function hx_search_best_matches( $query ) {
	$words = array_filter( explode( ' ', hx_search_normalize( $query ) ) );
	if ( ! $words ) {
		return [];
	}
	$scored = [];
	foreach ( hx_search_keywords() as $i => $entry ) {
		$best = 0;
		foreach ( (array) $entry['terms'] as $term ) {
			$tw    = explode( ' ', hx_search_normalize( $term ) );
			$score = 0;
			foreach ( $tw as $t ) {
				$hit = 0;
				foreach ( $words as $q ) {
					if ( $q === $t ) {
						$hit = 3;
						break;
					}
					if ( hx_search_word_matches( $q, $t ) ) {
						$hit = max( $hit, 2 );
					}
				}
				if ( ! $hit ) {
					$score = 0;
					break;
				}
				$score += $hit;
			}
			$best = max( $best, $score );
		}
		if ( $best ) {
			$scored[ $i ] = $best;
		}
	}
	arsort( $scored );

	$out  = [];
	$seen = [];
	$map  = hx_search_keywords();
	foreach ( array_keys( $scored ) as $i ) {
		foreach ( (array) $map[ $i ]['targets'] as $target ) {
			$item = hx_search_target( $target );
			if ( $item && empty( $seen[ $item['url'] ] ) ) {
				$seen[ $item['url'] ] = true;
				$out[]                = $item;
			}
		}
	}
	return array_slice( $out, 0, 6 );
}

/** Resolve a map target to [ title, url, text, id ]; null if its page isn't published. */
function hx_search_target( $target ) {
	if ( ! empty( $target['page'] ) ) {
		$page = get_page_by_path( $target['page'], OBJECT, 'page' );
		if ( ! $page || 'publish' !== $page->post_status ) {
			return null;
		}
		return [
			'id'    => (int) $page->ID,
			'title' => ! empty( $target['title'] ) ? $target['title'] : get_the_title( $page ),
			'url'   => get_permalink( $page ),
			'text'  => ! empty( $target['text'] ) ? $target['text'] : hx_search_summary( $page ),
		];
	}
	return [
		'id'    => 0,
		'title' => $target['title'] ?? '',
		'url'   => hx_landing_url( $target['url'] ),
		'text'  => $target['text'] ?? '',
	];
}

/** One-line summary of a page/post: theme data description, excerpt, SEO description, or content. */
function hx_search_summary( $post ) {
	$slug = $post->post_name;
	foreach ( [ 'hx_landing_get_data', 'hx_service_get_data' ] as $fn ) {
		$d = function_exists( $fn ) ? $fn( $slug ) : null;
		if ( $d && ! empty( $d['seo_description'] ) ) {
			return $d['seo_description'];
		}
	}
	if ( has_excerpt( $post ) ) {
		return get_the_excerpt( $post );
	}
	foreach ( [ '_yoast_wpseo_metadesc', 'rank_math_description' ] as $key ) {
		$meta = (string) get_post_meta( $post->ID, $key, true );
		if ( $meta && false === strpos( $meta, '%%' ) ) {
			return $meta;
		}
	}
	return wp_trim_words( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ), 26 );
}

/** Use the theme's results page for every search (also an empty ?s=). */
add_filter( 'template_include', function ( $template ) {
	if ( hx_nav_2026_on() && ! is_admin() && ( is_search() || isset( $_GET['s'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		$ours = get_stylesheet_directory() . '/page-templates/search-2026.php';
		if ( file_exists( $ours ) ) {
			return $ours;
		}
	}
	return $template;
}, 99 );

add_action( 'wp_enqueue_scripts', function () {
	if ( ! hx_nav_2026_on() || ! ( is_search() || isset( $_GET['s'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return;
	}
	wp_enqueue_style( 'hx-job-landing', get_stylesheet_directory_uri() . '/assets/css/job-landing.css', [ 'hello-elementor-child-style' ], hx_asset_ver( 'assets/css/job-landing.css' ) );
	wp_enqueue_style( 'hx-landing-page', get_stylesheet_directory_uri() . '/assets/css/landing-page.css', [ 'hx-job-landing' ], hx_asset_ver( 'assets/css/landing-page.css' ) );
}, 30 );

/* --------------------------------------------------------------------------
   Header menu v2 (Oct 2026 review): five items → three. Empties the theme's
   "Main Menu 2026" and rebuilds it from nav/main-2026.php (other menus untouched).
   -------------------------------------------------------------------------- */

function hx_cu_nav_2026_v2() {
	$menu = wp_get_nav_menu_object( HX_NAV_2026_MENU );
	if ( ! $menu ) {
		return hx_cu_nav_2026(); // not built yet: build it fresh from the current data
	}
	$file  = get_stylesheet_directory() . '/nav/main-2026.php';
	$items = file_exists( $file ) ? include $file : null;
	if ( ! is_array( $items ) ) {
		return false;
	}
	foreach ( (array) wp_get_nav_menu_items( $menu->term_id, [ 'post_status' => 'any' ] ) as $item ) {
		wp_delete_post( $item->ID, true ); // menu entries only; pages are untouched
	}
	$ok = hx_nav_2026_add_items( $menu->term_id, $items );
	hx_cu_flush_elementor();
	return $ok;
}
add_filter( 'hx_content_updates', function ( $updates ) {
	if ( hx_nav_2026_on() ) {
		$updates['nav:main-2026-v2-three-items'] = 'hx_cu_nav_2026_v2';
	}
	return $updates;
} );

/* --------------------------------------------------------------------------
   Home (Oct 2026 review): the "Home 2026" template draft is dropped; we
   iterate on a copy of the current Elementor home instead, so the live home
   stays untouched. 1) move the old draft to the Trash (restorable);
   2) create a draft copy of the front page: "Home – working copy".
   -------------------------------------------------------------------------- */

define( 'HX_HOME_COPY_SLUG', 'home-working-copy' );

function hx_cu_trash_home_2026_draft() {
	$page = get_page_by_path( 'home-2026', OBJECT, 'page' );
	if ( $page && 'trash' !== $page->post_status && get_post_meta( $page->ID, '_hx_landing_auto', true ) ) {
		wp_trash_post( $page->ID );
	}
	return true;
}

function hx_cu_home_working_copy() {
	$home_id = (int) get_option( 'page_on_front' );
	$home    = $home_id ? get_post( $home_id ) : null;
	if ( ! $home ) {
		return true; // no static front page on this install
	}
	if ( get_page_by_path( HX_HOME_COPY_SLUG, OBJECT, 'page' ) ) {
		return true; // already copied
	}
	$copy_id = wp_insert_post( wp_slash( [
		'post_type'      => 'page',
		'post_status'    => 'draft',
		'post_name'      => HX_HOME_COPY_SLUG,
		'post_title'     => 'Home – working copy',
		'post_content'   => $home->post_content,
		'post_excerpt'   => $home->post_excerpt,
		'post_author'    => $home->post_author,
		'menu_order'     => $home->menu_order,
		'comment_status' => 'closed',
	] ), true );
	if ( is_wp_error( $copy_id ) || ! $copy_id ) {
		return false;
	}
	$skip = [ '_edit_lock', '_edit_last', '_wp_old_slug', '_elementor_css', '_elementor_element_cache', '_elementor_page_assets' ];
	foreach ( get_post_meta( $home_id ) as $key => $values ) {
		if ( in_array( $key, $skip, true ) ) {
			continue;
		}
		foreach ( $values as $value ) {
			add_post_meta( $copy_id, $key, wp_slash( maybe_unserialize( $value ) ) );
		}
	}
	hx_cu_flush_elementor();
	return true;
}
add_filter( 'hx_content_updates', function ( $updates ) {
	if ( hx_nav_2026_on() ) {
		$updates['home:trash-home-2026-draft'] = 'hx_cu_trash_home_2026_draft';
		$updates['home:working-copy']          = 'hx_cu_home_working_copy';
	}
	return $updates;
} );

/* Home + "Home – working copy": "Our core values." at the size of "Experience True Healing". */
add_action( 'wp_enqueue_scripts', function () {
	if ( ! is_front_page() && ! is_page( HX_HOME_COPY_SLUG ) && HX_HOME_COPY_SLUG !== get_post_field( 'post_name', get_queried_object_id() ) ) {
		return;
	}
	wp_enqueue_script( 'hx-heading-match', get_stylesheet_directory_uri() . '/assets/js/hx-heading-match.js', [], hx_asset_ver( 'assets/js/hx-heading-match.js' ), true );
}, 30 );
