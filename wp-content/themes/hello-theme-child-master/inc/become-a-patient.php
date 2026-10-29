<?php
/**
 * BECOME A PATIENT (replaces the Typeform intake link)
 *
 * Page    : /become-a-patient/ (created automatically, template page-templates/become-a-patient.php)
 * Assets  : assets/css/become-a-patient.css · assets/js/become-a-patient.js
 * Flow    : step-by-step form. The visitor picks Chronic or Complex Care or Health Optimization,
 *   then gives contact details.
 *   - Chronic/Complex: support needed + how/when to reach them → a Patient Coordinator calls for a
 *     screening (then onboarding link, interview, EOC/SPD, Admissions Committee — outside the site).
 *   - Health Optimization: either explore therapies (therapy, time, simulated checkout; sample data
 *     until the catalog/scheduling/payment systems are connected) or meet a provider (Mira →
 *     New Patient Coordinator; anyone else → scheduling preferences). The "Health Optimization
 *     Path" link is e-mailed automatically when configured.
 * WordPress only receives contact details and preferences. Support needed and free-text notes
 * go to AWS only (when HX_PHI_ENDPOINT is set); card details never leave the browser.
 * Dormant until the Website 2026 switch is on (Settings → Reading), like the rest of stage 1.
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'HX_PATIENT_TEMPLATE', 'page-templates/become-a-patient.php' );
define( 'HX_PATIENT_SLUG', 'become-a-patient' );
define( 'HX_PATIENT_TYPEFORM', 'https://form.typeform.com/to/xNOaiQME' );

// Where inquiries go (comma-separated). Empty = the site inbox (HX_SITE_INBOX).
if ( ! defined( 'HX_PATIENT_INQUIRY_EMAIL' ) ) {
	define( 'HX_PATIENT_INQUIRY_EMAIL', '' );
}
// "Health Optimization Path" link e-mailed to B1/B2 requests. Empty = not sent yet (pending confirmation).
// The front end keeps its own copy (HO_PATH_URL in assets/js/become-a-patient-api.js).
if ( ! defined( 'HX_HO_PATH_URL' ) ) {
	define( 'HX_HO_PATH_URL', '' );
}

// Display-only mockup (default ON): forms show the final message and the B1 redirect but
// send nothing (no e-mails, no AWS). Turn real sending on with define( 'HX_PATIENT_MOCKUP', false ); in wp-config.php.
if ( ! defined( 'HX_PATIENT_MOCKUP' ) ) {
	define( 'HX_PATIENT_MOCKUP', true );
}

/** Final message per path (also used by the display-only mode). */
function hx_patient_messages() {
	return [
		'chronic'      => 'Thank you. Our Patient Coordinator will contact you soon for a brief screening call.',
		'ho-therapies' => 'Thank you. We will email you your Health Optimization Path. Taking you to online booking...',
		'ho-provider'  => 'Thank you. Please complete the new-patient survey; our New Patient Coordinator will then book your provider visit.',
	];
}

/**
 * PHI feature flag: the AWS API Gateway URL that receives health information
 * (wp-config.php: define( 'HX_PHI_ENDPOINT', 'https://...' ); or the HX_PHI_ENDPOINT
 * environment variable). Empty or non-HTTPS = the "Describe your condition" field
 * stays hidden and disabled. PHI never goes through WordPress either way.
 */
function hx_patient_phi_endpoint() {
	$url = defined( 'HX_PHI_ENDPOINT' ) ? (string) HX_PHI_ENDPOINT : (string) getenv( 'HX_PHI_ENDPOINT' );
	return ( $url && 0 === strpos( $url, 'https://' ) ) ? esc_url_raw( $url ) : '';
}

function hx_patient_is_page() {
	return is_page() && is_page_template( HX_PATIENT_TEMPLATE );
}

function hx_patient_page_url() {
	$page = get_page_by_path( HX_PATIENT_SLUG, OBJECT, 'page' );
	return ( $page && 'publish' === $page->post_status ) ? get_permalink( $page ) : '';
}

/** Create the page once (draft while the switch is off, published when it's on). */
function hx_patient_ensure_page() {
	$page = get_page_by_path( HX_PATIENT_SLUG, OBJECT, 'page' );
	if ( ! $page ) {
		$id = hx_ensure_page( HX_PATIENT_SLUG, 'Become a Patient', HX_PATIENT_TEMPLATE, hx_nav_2026_on() ? 'publish' : 'draft' );
		if ( $id ) {
			update_post_meta( $id, '_hx_landing_auto', 1 );
		}
	} elseif ( hx_nav_2026_on() && 'draft' === $page->post_status && get_post_meta( $page->ID, '_hx_landing_auto', true ) ) {
		wp_update_post( [ 'ID' => $page->ID, 'post_status' => 'publish' ] );
	}
}
add_action( 'init', 'hx_patient_ensure_page', 21 );

add_action( 'wp_enqueue_scripts', function () {
	if ( ! hx_patient_is_page() ) {
		return;
	}
	wp_enqueue_style( 'hx-become-patient-font', 'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600&display=swap', [], null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
	wp_enqueue_style( 'hx-become-patient', get_stylesheet_directory_uri() . '/assets/css/become-a-patient.css', [ 'hello-elementor-child-style', 'hx-become-patient-font' ], hx_asset_ver( 'assets/css/become-a-patient.css' ) );
	wp_enqueue_script( 'hx-become-patient-api', get_stylesheet_directory_uri() . '/assets/js/become-a-patient-api.js', [], hx_asset_ver( 'assets/js/become-a-patient-api.js' ), true );
	wp_enqueue_script( 'hx-become-patient-catalog', get_stylesheet_directory_uri() . '/assets/js/become-a-patient-catalog.js', [], hx_asset_ver( 'assets/js/become-a-patient-catalog.js' ), true );
	wp_enqueue_script( 'hx-become-patient', get_stylesheet_directory_uri() . '/assets/js/become-a-patient.js', [ 'hx-become-patient-api', 'hx-become-patient-catalog' ], hx_asset_ver( 'assets/js/become-a-patient.js' ), true );
}, 30 );

add_filter( 'body_class', function ( $classes ) {
	if ( hx_patient_is_page() ) {
		$classes[] = 'hx-become-patient';
	}
	return $classes;
} );

add_action( 'wp_head', function () {
	if ( ! hx_patient_is_page() || defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) ) {
		return;
	}
	echo '<meta name="description" content="' . esc_attr( 'Become a patient at Helixona: choose Chronic/Complex Recovery or Health Optimization and tell us how to reach you. A treatment-focused medical clinic in Irvine, California.' ) . '">' . "\n";
}, 6 );

/* --------------------------------------------------------------------------
   Submission (AJAX, or a plain POST without JavaScript)
   -------------------------------------------------------------------------- */

function hx_patient_paths() {
	return [
		'chronic'      => 'Chronic/Complex Recovery',
		'ho-therapies' => 'Health Optimization — therapies (online booking)',
		'ho-provider'  => 'Health Optimization — provider appointment',
	];
}

function hx_patient_handle() {
	// PHI must never reach WordPress (AWS only). Drop it if a client ever sends it; it is not read, stored, logged or e-mailed.
	unset( $_POST['hx_condition'], $_REQUEST['hx_condition'] ); // phpcs:ignore WordPress.Security.NonceVerification

	$is_ajax = wp_doing_ajax();
	$back    = wp_get_referer() ? remove_query_arg( 'hx_sent', wp_get_referer() ) : home_url( '/' . HX_PATIENT_SLUG . '/' );

	$respond = function ( $ok, $message, $code = 200, $redirect = '' ) use ( $is_ajax, $back ) {
		if ( $is_ajax ) {
			$ok ? wp_send_json_success( [ 'message' => $message ], $code ) : wp_send_json_error( [ 'message' => $message ], $code );
		}
		wp_safe_redirect( $redirect ? $redirect : add_query_arg( 'hx_sent', $ok ? '1' : rawurlencode( $message ), $back ) . '#hx-patient-form' );
		exit;
	};

	// Honeypot: bots fill it, people never see it.
	if ( ! empty( $_POST['hx_website'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		$respond( true, 'Thank you.' );
	}
	$token = isset( $_POST['hx_token'] ) ? sanitize_text_field( wp_unslash( $_POST['hx_token'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	if ( ! hx_job_verify_token( HX_PATIENT_SLUG, $token ) ) {
		$respond( false, 'Your session expired. Please reload the page and try again.', 403 );
	}

	// Rate limit: 5 per connection per 10 minutes.
	$ip   = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$key  = 'hx_patient_rl_' . md5( $ip );
	$hits = (int) get_transient( $key );
	if ( $hits >= 5 ) {
		$respond( false, 'Too many requests from this connection. Please call us at (949) 257-2644.', 429 );
	}
	set_transient( $key, $hits + 1, 10 * MINUTE_IN_SECONDS );

	$paths = hx_patient_paths();
	$f     = function ( $name ) {
		return isset( $_POST[ $name ] ) ? sanitize_text_field( wp_unslash( $_POST[ $name ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	};
	$path      = $f( 'hx_path' );
	$first     = $f( 'hx_first' );
	$last      = $f( 'hx_last' );
	$email     = sanitize_email( $f( 'hx_email' ) );
	$phone     = $f( 'hx_phone' );
	// Preferences from the step form (whitelisted; unknown values are dropped).
	$pick = function ( $name, $allowed ) use ( $f ) {
		return array_values( array_intersect( array_keys( $allowed ), explode( ',', $f( $name ) ) ) );
	};
	$names = function ( $ids, $allowed ) {
		return implode( ', ', array_map( function ( $id ) use ( $allowed ) { return $allowed[ $id ]; }, $ids ) );
	};
	$channel_opts  = [ 'phone' => 'Phone call', 'email' => 'Email', 'text' => 'Text message' ];
	$time_opts     = [ 'morning' => 'Mornings', 'afternoon' => 'Afternoons', 'evening' => 'Early evenings' ];
	$provider_opts = [ 'mira' => 'Mira', 'other' => 'Another provider / No preference' ];
	$format_opts   = [ 'in_person' => 'In person', 'virtual' => 'Virtual visit', 'either' => 'Either' ];
	$channels      = $names( $pick( 'hx_channels', $channel_opts ), $channel_opts );
	$times         = $names( $pick( 'hx_times', $time_opts ), $time_opts );
	$provider      = $names( array_slice( $pick( 'hx_provider', $provider_opts ), 0, 1 ), $provider_opts );
	$format        = $names( array_slice( $pick( 'hx_format', $format_opts ), 0, 1 ), $format_opts );
	$therapy       = mb_substr( $f( 'hx_therapy' ), 0, 80 );
	$slot          = mb_substr( $f( 'hx_slot' ), 0, 80 );
	// Reference shared with the PHI record in AWS (generated in the browser; server fallback without JS).
	$reference = strtoupper( $f( 'hx_reference' ) );
	if ( ! preg_match( '/^HX-[A-Z0-9]{6,32}$/', $reference ) ) {
		$reference = 'HX-' . strtoupper( wp_generate_password( 12, false ) );
	}

	if ( ! isset( $paths[ $path ] ) ) {
		$respond( false, 'Please choose the option that best describes you.', 400 );
	}
	if ( mb_strlen( $first ) < 1 || mb_strlen( $last ) < 1 || mb_strlen( $first . $last ) > 120 ) {
		$respond( false, 'Please enter your first and last name.', 400 );
	}
	if ( ! $email || ! is_email( $email ) ) {
		$respond( false, 'Please enter a valid e-mail address.', 400 );
	}
	if ( strlen( preg_replace( '/\D+/', '', $phone ) ) < 7 ) {
		$respond( false, 'Please enter a phone number we can call.', 400 );
	}
	// Dev Spec v1: no patient-facing provider selection (T-01) and no contact-channel step; staff route requests.

	// Display-only mockup: nothing is sent or stored.
	if ( HX_PATIENT_MOCKUP ) {
		$m = hx_patient_messages();
		$respond( true, $m[ $path ], 200, 'ho-therapies' === $path ? home_url( '/booking/' ) : '' );
	}

	$to = function_exists( 'hx_job_split_emails' ) ? hx_job_split_emails( HX_PATIENT_INQUIRY_EMAIL ? HX_PATIENT_INQUIRY_EMAIL : HX_SITE_INBOX ) : [ HX_SITE_INBOX ];
	if ( ! $to ) {
		$to = [ get_option( 'admin_email' ) ];
	}
	$domain    = preg_replace( '/^www\./', '', (string) wp_parse_url( home_url(), PHP_URL_HOST ) );
	$from      = 'From: Helixona <email@' . $domain . '>';
	$name      = trim( $first . ' ' . $last );
	$next_steps = [
		'chronic'      => 'Screening call, then send the "Chronic/Complex Onboarding" link -> patient interview -> EOC/SPD -> Admissions Committee review.',
		'ho-therapies' => 'Send the "Health Optimization Path" link. Patient continues to online booking (cash modalities, card); no provider review.',
		'ho-provider'  => 'New Patient Coordinator: send the new-patient survey link; book the provider visit in ECW once the survey is complete.',
	];
	$next_step = $next_steps[ $path ];

	$rows = [
		'Reference'  => $reference,
		'Path'       => $paths[ $path ],
		'Name'       => $name,
		'Email'      => $email,
		'Phone'      => $phone,
	];
	if ( 'chronic' === $path ) {
		$rows['Preferred contact'] = $channels;
		$rows['Best times']        = $times ? $times : 'No preference';
	} elseif ( 'ho-provider' === $path ) {
		$rows['Survey'] = 'Send the new-patient survey link; book the provider visit in ECW once it is complete.';
	} else {
		$rows['Therapy (sample)']     = $therapy ? $therapy : '—';
		$rows['Appointment (sample)'] = $slot ? $slot : '—';
	}
	$rows['Next step'] = $next_step;
	$rows['Submitted'] = wp_date( 'F j, Y g:i a' ) . ' · ' . $back;
	$body = '<div style="font-family:Arial,Helvetica,sans-serif;font-size:15px;line-height:1.5;color:#1a1a1a;"><h2 style="font-weight:normal;margin:0 0 16px;">New patient inquiry: ' . esc_html( $paths[ $path ] ) . '</h2><table cellpadding="8" cellspacing="0" style="border-collapse:collapse;max-width:640px;width:100%;">';
	foreach ( $rows as $label => $value ) {
		$body .= '<tr><th align="left" valign="top" style="border-bottom:1px solid #e9e1d1;color:#8a6d1c;font-weight:normal;width:170px;">' . esc_html( $label ) . '</th><td valign="top" style="border-bottom:1px solid #e9e1d1;">' . esc_html( $value ) . '</td></tr>';
	}
	$body .= '</table></div>';

	$sent = wp_mail( $to, sprintf( 'New patient inquiry (%s): %s', $paths[ $path ], $name ), $body, [ 'Content-Type: text/html; charset=UTF-8', $from, 'Reply-To: ' . $name . ' <' . $email . '>' ] );
	if ( ! $sent ) {
		$respond( false, 'We could not send your request right now. Please call us at (949) 257-2644.', 500 );
	}

	// Confirmation to the patient.
	$intro = [
		'chronic'      => 'Our Patient Coordinator will contact you for a brief screening call. After the call, we will send you the next steps to begin onboarding.',
		'ho-therapies' => 'You can book your therapies online at any time: <a href="' . esc_url( home_url( '/booking/' ) ) . '">' . esc_html( home_url( '/booking/' ) ) . '</a>',
		'ho-provider'  => 'Before we can schedule your provider visit, please complete the new-patient survey. Our New Patient Coordinator will then contact you to book your visit.',
	];
	$lines = [ 'Thank you for reaching out to Helixona.', $intro[ $path ] ];
	if ( 'chronic' !== $path && HX_HO_PATH_URL ) {
		$lines[] = 'To get started, please complete your Health Optimization Path: <a href="' . esc_url( HX_HO_PATH_URL ) . '">' . esc_html( HX_HO_PATH_URL ) . '</a>';
	}
	$lines[] = 'If you have any questions, call us at (949) 257-2644.';
	$confirm = '<div style="font-family:Arial,Helvetica,sans-serif;font-size:15px;line-height:1.6;color:#1a1a1a;"><p>Hi ' . esc_html( $first ) . ',</p><p>' . implode( '</p><p>', $lines ) . '</p><p>Warm regards,<br>The Helixona Team</p></div>';
	wp_mail( $email, 'We received your request – Helixona', $confirm, [ 'Content-Type: text/html; charset=UTF-8', $from ] );

	do_action( 'hx_patient_inquiry_sent', $path, compact( 'reference', 'first', 'last', 'email', 'phone', 'channels', 'times', 'provider', 'format', 'therapy', 'slot' ) ); // PII only

	$messages = hx_patient_messages();
	// Without JavaScript, B1 goes straight on to booking.
	$respond( true, $messages[ $path ], 200, 'ho-therapies' === $path ? home_url( '/booking/' ) : '' );
}
add_action( 'wp_ajax_hx_patient_inquiry', 'hx_patient_handle' );
add_action( 'wp_ajax_nopriv_hx_patient_inquiry', 'hx_patient_handle' );
add_action( 'admin_post_hx_patient_inquiry', 'hx_patient_handle' );
add_action( 'admin_post_nopriv_hx_patient_inquiry', 'hx_patient_handle' );

/* --------------------------------------------------------------------------
   Point every "Become a Patient" Typeform link (Elementor header + front page)
   at the new page. Runs once when the switch is on and the page is published.
   -------------------------------------------------------------------------- */

function hx_cu_become_patient_links() {
	$url = hx_patient_page_url();
	if ( ! $url ) {
		return false; // page not published yet; retry later
	}
	$ids   = wp_list_pluck( get_posts( [
		'post_type'   => 'elementor_library',
		'post_status' => 'any',
		'numberposts' => -1,
		'meta_query'  => [ [ 'key' => '_elementor_template_type', 'value' => [ 'header', 'footer' ], 'compare' => 'IN' ] ], // phpcs:ignore WordPress.DB.SlowDBQuery
	] ), 'ID' );
	$ids[] = (int) get_option( 'page_on_front' );
	foreach ( array_filter( array_unique( $ids ) ) as $id ) {
		$raw = get_post_meta( $id, '_elementor_data', true );
		if ( ! is_string( $raw ) || false === strpos( $raw, 'xNOaiQME' ) ) {
			continue;
		}
		$data = json_decode( $raw, true );
		if ( ! is_array( $data ) ) {
			continue;
		}
		$walk = function ( &$nodes ) use ( &$walk, $url ) {
			foreach ( $nodes as &$node ) {
				if ( isset( $node['settings']['link']['url'] ) && false !== strpos( (string) $node['settings']['link']['url'], 'xNOaiQME' ) ) {
					$node['settings']['link']['url']         = $url;
					$node['settings']['link']['is_external'] = '';
				}
				if ( ! empty( $node['elements'] ) && is_array( $node['elements'] ) ) {
					$walk( $node['elements'] );
				}
			}
		};
		$walk( $data );
		update_post_meta( $id, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );
		delete_post_meta( $id, '_elementor_css' );
		delete_post_meta( $id, '_elementor_element_cache' );
	}
	hx_cu_flush_elementor();
	return true;
}
add_filter( 'hx_content_updates', function ( $updates ) {
	if ( hx_nav_2026_on() ) {
		$updates['patient:become-a-patient-links'] = 'hx_cu_become_patient_links';
	}
	return $updates;
} );
