<?php
/**
 * Landing: Health Recovery (care path 2 of 3).
 * Source: Website 2026 Navigation & Sitemap v3 (Oct 20, 2026). Copy pending approval.
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return [
	'title'           => 'Health Recovery',
	'eyebrow'         => 'Find Your Path',
	'lead'            => [ 'slot' => 'One-sentence promise for people treating a specific condition or health goal.' ],
	'seo_description' => 'Health Recovery at Helixona: treatment-focused medical care for specific conditions and health goals, for cash and insurance patients.',
	'ctas'            => [
		[ 'label' => 'Become a Patient', 'url' => '/become-a-patient/' ],
		[ 'label' => 'Call (949) 257-2644', 'url' => 'tel:9492572644' ],
	],

	'sections' => [
		[
			'type'       => 'text',
			'id'         => 'who',
			'heading'    => 'Who This Path Is For',
			'paragraphs' => [ [ 'slot' => 'Description of patients this path serves (physician/NP copy to refine).' ] ],
		],
		[
			'type'       => 'text',
			'id'         => 'how',
			'heading'    => 'How Care Begins',
			'paragraphs' => [
				[ 'slot' => 'Every Health Recovery patient (cash or insurance) starts with a staff interview; provider appointments are scheduled by our team.' ],
				[ 'slot' => 'When seeking insurance-supported care, explain the benefits review.' ],
			],
			'ctas'       => [ [ 'label' => 'Insurance & Payment', 'url' => '/understanding-insurance-at-helixona/', 'style' => 'ghost' ] ],
		],
		[
			'type'    => 'list',
			'id'      => 'what-we-treat',
			'heading' => 'What We Help With',
			'items'   => [ [ 'slot' => 'Examples of conditions / health goals treated in this path (no efficacy claims).' ] ],
		],
		[
			'type'  => 'media',
			'id'    => 'media',
			'items' => [
				[ 'kind' => 'photo', 'slot' => 'Actual Helixona photography (Irvine clinic / providers).' ],
				[ 'kind' => 'photo', 'slot' => 'Actual Helixona photography.' ],
			],
		],
		[
			'type'    => 'cta',
			'variant' => 'dark',
			'heading' => 'Start Your Recovery',
			'intro'   => [ 'slot' => 'Closing invitation.' ],
			'ctas'    => [
				[ 'label' => 'Become a Patient', 'url' => '/become-a-patient/' ],
				[ 'label' => 'Explore Other Paths', 'url' => '/find-your-path/' ],
			],
		],
	],
];
