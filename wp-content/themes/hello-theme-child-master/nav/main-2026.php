<?php
/**
 * Header menu 2026: three primary items (Become a Patient + Book Now are the header buttons).
 * Reduced from the sitemap's five after the Oct 2026 review so logo, menu and buttons fit on one line:
 * Conditions moved under Find Your Path, Treatments + Programs & Access merged.
 * Built once into the "Main Menu 2026" menu by inc/website-2026.php when the Website 2026 switch is on (Settings → Reading).
 * 'page' links by page slug (falls back to /slug/ if the page is missing); 'url' for anything else.
 * Health Optimization lives only under Find Your Path; the Helixona Method only under About.
 * Source: Website 2026 Navigation & Sitemap v3 (Oct 20, 2026).
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return [
	[
		'title'    => 'Find Your Path',
		'page'     => 'find-your-path',
		'children' => [
			[ 'title' => 'Chronic/Complex Recovery', 'page' => 'complex-chronic-recovery' ],
			[ 'title' => 'Health Optimization', 'page' => 'health-optimization' ],
			[ 'title' => 'Conditions We Help', 'page' => 'conditions' ],
			[ 'title' => 'Becoming a Patient', 'page' => 'what-to-expect' ],
		],
	],
	[
		'title'    => 'Treatments & Programs',
		'page'     => 'services-and-tech',
		'children' => [
			[ 'title' => 'Treatments & Technologies', 'page' => 'services-and-tech' ],
			[ 'title' => 'Diagnostics', 'page' => 'advanced-diagnostics' ],
			[ 'title' => 'Treatment Bundles', 'page' => 'treatment-bundles' ],
			[ 'title' => 'Plan of Care Access Pass', 'page' => 'plan-of-care-access-pass' ],
			[ 'title' => 'Insurance & Payment', 'page' => 'understanding-insurance-at-helixona' ],
		],
	],
	[
		'title'    => 'About Helixona',
		'url'      => '#',
		'children' => [
			[ 'title' => 'The Helixona Method', 'page' => 'the-helixona-healing-journey' ], // stage 5: new Method hub
			[ 'title' => 'Our Story', 'page' => 'about-us' ],
			[ 'title' => 'Physicians & Providers', 'page' => 'team-founders' ],
			[ 'title' => 'Our Team', 'page' => 'team' ],
			[ 'title' => 'Education & Resources', 'page' => 'blog' ], // stage 5: new Education directory
			[ 'title' => 'Contact', 'page' => 'contact-us' ],
			[ 'title' => 'Careers', 'page' => 'careers' ],
			[ 'title' => 'Search', 'url' => '/?s=' ],
		],
	],
];
