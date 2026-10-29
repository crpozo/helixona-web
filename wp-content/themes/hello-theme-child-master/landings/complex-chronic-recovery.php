<?php
/**
 * Landing: Complex Chronic Recovery (care path 1 of 3).
 * Source: Website 2026 Navigation & Sitemap v3 (Oct 20, 2026). Copy pending approval.
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return [
	'title'           => 'Complex Chronic Recovery',
	'eyebrow'         => 'Find Your Path',
	'lead'            => [ 'slot' => 'One-sentence promise for people living with complex chronic illness (root-cause, whole-person care).' ],
	'seo_description' => 'Complex Chronic Recovery at Helixona: root-cause, whole-person medical care for complex chronic illness in Irvine, California.',
	'ctas'            => [
		[ 'label' => 'Become a Patient', 'url' => '/become-a-patient/' ],
		[ 'label' => 'Call (949) 257-2644', 'url' => 'tel:9492572644' ],
	],

	'sections' => [
		[
			'type'       => 'text',
			'id'         => 'who',
			'heading'    => 'Who This Path Is For',
			'paragraphs' => [ [ 'slot' => 'Description of patients this path serves (physician copy to refine).' ] ],
		],
		[
			'type'    => 'list',
			'id'      => 'conditions',
			'heading' => 'Conditions We Help',
			'items'   => [
				[ 'slot' => 'Short list of complex chronic conditions with links to existing condition pages (MCAS, EDS, POTS & dysautonomia, chronic fatigue, Lyme & co-infections, long COVID, mold illness).' ],
			],
			'ctas'    => [ [ 'label' => 'All Conditions', 'url' => '/conditions/', 'style' => 'ghost' ] ],
		],
		[
			'type'       => 'text',
			'id'         => 'how',
			'heading'    => 'How Care Begins',
			'paragraphs' => [ [ 'slot' => 'Staff interview first, then a provider appointment scheduled by our team; what the first visits and the roadmap look like.' ] ],
		],
		[
			'type'    => 'media',
			'id'      => 'media',
			'items'   => [
				[ 'kind' => 'photo', 'slot' => 'Actual Helixona photography (no stock or generated imagery).' ],
				[ 'kind' => 'video', 'slot' => 'Optional November video (founders / roadmap).' ],
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
