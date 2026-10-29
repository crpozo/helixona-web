<?php
/**
 * Site search: keyword → destination map, Website 2026 §8.
 * A query that matches any of an entry's 'terms' (whole words, small typos
 * tolerated) shows that entry's destinations first, before general results.
 * Destinations: 'page' => slug (title/excerpt read from the page) or
 * 'url' + 'title' (+ optional 'text'). Symptom searches lead with education
 * and assessment options, never treatment claims.
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return [
	[
		'terms'   => [ 'salt', 'salt room', 'halotherapy', 'halo therapy', 'halo' ],
		'targets' => [
			[ 'page' => 'halo-therapy-salt-room' ],
			[ 'url' => '/booking/?filter=water-salt', 'title' => 'Book: Water & Salt therapies' ],
		],
	],
	[
		'terms'   => [ 'nano bath', 'oxygen bath', 'hydrogen bath', 'bubbles', 'bath', 'baths' ],
		'targets' => [ [ 'page' => 'nano-oxygen-and-hydrogen-bathsa-coming-january-2026' ] ],
	],
	[
		'terms'   => [ 'foot soak', 'nano foot bath', 'foot bath' ],
		'targets' => [ [ 'page' => 'nano-oxygen-and-hydrogen-bathsa-coming-january-2026', 'title' => 'Nano Foot Bath' ] ],
	],
	[
		'terms'   => [ 'red light', 'light bed', 'photobiomodulation', 'red light therapy' ],
		'targets' => [ [ 'page' => 'red-light-therapy' ] ],
	],
	[
		'terms'   => [ 'laser', 'cold laser', 'erchonia' ],
		'targets' => [ [ 'page' => 'erchonia-laser' ] ],
	],
	[
		'terms'   => [ 'rife', 'frequencies', 'frequency', 'energy therapy', 'energy', 'biocharger', 'scenar', 'bemer', 'pemf', 'microcurrent', 'biomodulator' ],
		'targets' => [
			[ 'url' => '/booking/?filter=frequency-energy', 'title' => 'Frequency & Energy therapies', 'text' => 'Browse and book the frequency and energy modalities.' ],
			[ 'page' => 'biocharger-therapy' ],
			[ 'page' => 'microcurrent-biofeedback-therapy' ],
			[ 'page' => 'bemer-therapy' ],
			[ 'page' => 'pemf-therapy' ],
		],
	],
	[
		'terms'   => [ 'hydrogen inhalation', 'h2 breathing', 'h2', 'nanovi', 'inhalation' ],
		'targets' => [
			[ 'page' => 'nanovi' ],
			[ 'url' => '/booking/?filter=inhalation', 'title' => 'Inhalation therapies' ],
		],
	],
	[
		'terms'   => [ 'iv', 'infusion', 'infusions', 'nad', 'glutathione', 'vitamin c', 'iv therapy' ],
		'targets' => [ [ 'page' => 'iv-nutrient-therapy', 'text' => 'Education and booking. IV therapy requires clinical clearance before scheduling.' ] ],
	],
	[
		'terms'   => [ 'eboo', 'blood ozone', 'ozone blood treatment', 'ozone' ],
		'targets' => [ [ 'page' => 'eboo-therapy', 'text' => 'Education and booking. EBOO requires clinical clearance before scheduling.' ] ],
	],
	[
		'terms'   => [ 'sessions', 'package', 'packages', 'discount', 'five pack', 'ten pack', 'bundle', 'bundles' ],
		'targets' => [ [ 'page' => 'treatment-bundles' ] ],
	],
	[
		'terms'   => [ 'access pass', 'monthly fee', 'plan of care', 'membership' ],
		'targets' => [ [ 'page' => 'plan-of-care-access-pass' ] ],
	],
	[
		'terms'   => [ 'brain fog', 'brain health', 'cognitive health', 'memory', 'focus' ],
		'targets' => [
			[ 'page' => 'conditions-we-commonly-evaluate-in-complex-chronic-illness' ],
			[ 'url' => '/health-optimization/#vitalize-programs', 'title' => 'Vitalize Your Brain', 'text' => 'Structured program, coming soon.' ],
		],
	],
	[
		'terms'   => [ 'bloating', 'constipation', 'gut', 'digestion', 'digestive' ],
		'targets' => [
			[ 'page' => 'conditions-we-commonly-evaluate-in-complex-chronic-illness' ],
			[ 'url' => '/health-optimization/#vitalize-programs', 'title' => 'Vitalize Your Gut', 'text' => 'Structured program, coming soon.' ],
		],
	],
	[
		'terms'   => [ 'lyme', 'tick' ],
		'targets' => [ [ 'page' => 'lyme-disease-chronic-co-infections-treatment' ], [ 'page' => 'complex-chronic-recovery' ] ],
	],
	[
		'terms'   => [ 'mold', 'mould', 'mycotoxin', 'mycotoxins' ],
		'targets' => [ [ 'page' => 'mold-illness-mycotoxin-exposure-treatment' ], [ 'page' => 'complex-chronic-recovery' ] ],
	],
	[
		'terms'   => [ 'mcas', 'mast cell' ],
		'targets' => [ [ 'page' => 'mast-cell-activation-syndrome-mcas-2' ], [ 'page' => 'complex-chronic-recovery' ] ],
	],
	[
		'terms'   => [ 'pots', 'dysautonomia' ],
		'targets' => [ [ 'page' => 'pots-dysautonomia-treatment' ], [ 'page' => 'complex-chronic-recovery' ] ],
	],
	[
		'terms'   => [ 'long covid', 'post viral', 'covid' ],
		'targets' => [ [ 'page' => 'long-covid-post-viral-syndrome-treatment' ], [ 'page' => 'complex-chronic-recovery' ] ],
	],
	[
		'terms'   => [ 'chronic fatigue', 'fatigue', 'mitochondrial' ],
		'targets' => [ [ 'page' => 'chronic-fatigue-mitochondrial-dysfunction-treatment' ], [ 'page' => 'complex-chronic-recovery' ] ],
	],
	[
		'terms'   => [ 'ehlers danlos', 'eds', 'hypermobility' ],
		'targets' => [ [ 'page' => 'ehlers-danlos-syndrome' ], [ 'page' => 'complex-chronic-recovery' ] ],
	],
	[
		'terms'   => [ 'wellness', 'longevity', 'optimization', 'optimize', 'prevention' ],
		'targets' => [ [ 'page' => 'health-optimization' ] ],
	],
	[
		'terms'   => [ 'insurance', 'cost', 'price', 'pricing', 'payment' ],
		'targets' => [ [ 'page' => 'understanding-insurance-at-helixona' ], [ 'page' => 'treatment-bundles' ] ],
	],
	[
		'terms'   => [ 'book', 'booking', 'appointment', 'schedule' ],
		'targets' => [ [ 'page' => 'booking' ], [ 'page' => 'what-to-expect' ] ],
	],
];
