<?php
/**
 * Landing: Find Your Path (care paths hub). Replaces /three-levels-of-care/ (301).
 * Source: Website 2026 Navigation & Sitemap v3 (Oct 20, 2026). Copy pending approval.
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return [
	'title'           => 'Find Your Path',
	'eyebrow'         => 'How Can We Help You Today?',
	'lead'            => [ 'slot' => 'One or two sentences: three ways to begin care at Helixona, one whole-person, root-cause philosophy.' ],
	'seo_description' => 'Three ways to begin care at Helixona: Complex Chronic Recovery, Health Recovery and Health Optimization. A treatment-focused medical clinic in Irvine.',
	'redirect_from'   => [ '/three-levels-of-care/' ],
	'ctas'            => [
		[ 'label' => 'Become a Patient', 'url' => '/become-a-patient/' ],
		[ 'label' => 'Book a Therapy', 'url' => '/booking/' ],
	],

	'sections' => [
		[
			'type'    => 'tiles',
			'id'      => 'paths',
			'heading' => 'Three Paths, One Philosophy',
			'intro'   => [ 'slot' => 'Short intro to the three equal care paths (physician/NP copy to refine).' ],
			'items'   => [
				[
					'title' => 'Complex Chronic Recovery',
					'text'  => [ 'slot' => 'Who this path is for (complex chronic illness: e.g. MCAS, POTS, Lyme, mold, long COVID).' ],
					'url'   => '/complex-chronic-recovery/',
				],
				[
					'title' => 'Health Recovery',
					'text'  => [ 'slot' => 'Who this path is for (specific conditions and health goals; cash or insurance).' ],
					'url'   => '/health-recovery/',
				],
				[
					'title' => 'Health Optimization',
					'text'  => [ 'slot' => 'Who this path is for (proactive, whole-person optimization; NP-guided care and individual therapies).' ],
					'url'   => '/health-optimization/',
				],
			],
		],
		[
			'type'       => 'text',
			'id'         => 'how-care-begins',
			'heading'    => 'How Care Begins',
			'paragraphs' => [
				[ 'slot' => 'All provider appointments are scheduled by our staff after a conversation/interview. Explain the interview and, when seeking insurance-supported care, the benefits review.' ],
				[ 'slot' => 'Treatment-focused positioning: "We are not a primary care practice. We are a treatment-focused medical clinic." plus continuity with the patient\'s own primary care clinician.' ],
			],
		],
		[
			'type'    => 'media',
			'id'      => 'video',
			'variant' => 'dark',
			'heading' => 'Inside Helixona',
			'items'   => [
				[ 'kind' => 'video', 'slot' => 'November video: three paths / one philosophy (founder, Irvine clinic, patient experience). Captions/transcript required.' ],
			],
		],
		[
			'type'    => 'cta',
			'heading' => 'Becoming a Patient',
			'intro'   => [ 'slot' => 'Closing invitation: what happens next and how to start.' ],
			'ctas'    => [
				[ 'label' => 'Becoming a Patient', 'url' => '/what-to-expect/' ],
				[ 'label' => 'Call (949) 257-2644', 'url' => 'tel:9492572644' ],
			],
		],
	],
];
