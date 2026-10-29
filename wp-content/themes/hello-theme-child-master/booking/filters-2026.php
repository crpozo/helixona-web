<?php
/**
 * Booking directory (/booking/) filters, Website 2026 §7.
 * Visitor-centered filters replace Diagnostic / Eliminate / Nourish / Repair.
 * Applied once by hx_cu_booking_filters() (inc/website-2026.php) to the
 * booking page's HTML widget; Book Now / Learn More / Call to Book links,
 * shared calendars and phone numbers are left exactly as they are.
 *
 * 'cards': first matching fragment of the card title (lowercase) wins.
 * PROPOSED placement — the sitemap asks to verify each modality.
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return [
	'filters' => [
		'all'                => 'All Services',
		'water-salt'         => 'Water & Salt',
		'light-laser'        => 'Light & Laser',
		'frequency-energy'   => 'Frequency & Energy',
		'inhalation'         => 'Inhalation',
		'iv-blood'           => 'IV & Blood',
		'bodywork-lymphatic' => 'Bodywork & Lymphatic',
		'diagnostics'        => 'Diagnostics',
		'provider'           => 'Provider Appointments',
	],

	'cards' => [
		'mead'            => 'diagnostics frequency-energy',
		'diagnostic'      => 'diagnostics', // Advanced Diagnostics, RightEye Diagnostics
		'inbody'          => 'diagnostics',
		'eboo'            => 'iv-blood',
		'iv therapy'      => 'iv-blood',
		'biocharger'      => 'frequency-energy',
		'biomodul'        => 'frequency-energy',
		'scenar'          => 'frequency-energy',
		'microcurrent'    => 'frequency-energy',
		'bemer'           => 'frequency-energy',
		'pemf'            => 'frequency-energy',
		'neuromuscular'   => 'bodywork-lymphatic',
		'lymph'           => 'bodywork-lymphatic',
		'erchonia'        => 'light-laser',
		'laser'           => 'light-laser',
		'red light'       => 'light-laser',
		'salt'            => 'water-salt',
		'halo'            => 'water-salt',
		'bath'            => 'water-salt',
		'nanovi'          => 'inhalation',
		'hydrogen inhal'  => 'inhalation',
		'neuro exam'      => 'provider diagnostics',
		'office visit'    => 'provider',
		'prp'             => 'provider',
		'platelet'        => 'provider',
	],

	// Cards that need clinical clearance get this label (booking stays blocked when uncleared, as today).
	'clearance' => [
		'match' => [ 'eboo', 'iv therapy' ],
		'label' => 'Requires clinical clearance before booking',
	],
];
