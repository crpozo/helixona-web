<?php
/**
 * Landing: Plan of Care Access Pass.
 * Not concierge medicine; does not include provider office visits. Do not publish
 * unconfirmed pricing. Separate from Treatment Bundles.
 * Source: Website 2026 Navigation & Sitemap v3 (Oct 20, 2026). Copy, terms and price pending approval.
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return [
	'title'           => 'Plan of Care Access Pass',
	'eyebrow'         => 'Programs & Access',
	'lead'            => [ 'slot' => 'One sentence: what the Access Pass is for (qualifying doctor-recommended plan-of-care cash therapies).' ],
	'seo_description' => 'The Helixona Plan of Care Access Pass: access to qualifying doctor-recommended plan-of-care therapies. Not concierge medicine.',
	'ctas'            => [
		[ 'label' => 'Call (949) 257-2644', 'url' => 'tel:9492572644' ],
		[ 'label' => 'Become a Patient', 'url' => '/become-a-patient/' ],
	],

	'sections' => [
		[
			'type'       => 'text',
			'id'         => 'purpose',
			'heading'    => 'What the Access Pass Is',
			'paragraphs' => [ [ 'slot' => 'Purpose and who qualifies.' ] ],
		],
		[
			'type'    => 'list',
			'id'      => 'included',
			'heading' => 'What It Includes',
			'items'   => [ [ 'slot' => 'Qualifying plan-of-care therapies included.' ] ],
		],
		[
			'type'    => 'list',
			'id'      => 'not-included',
			'heading' => 'What It Does Not Include',
			'items'   => [ [ 'slot' => 'Exclusions: not concierge medicine; does not include provider office visits; other exclusions.' ] ],
		],
		[
			'type'       => 'text',
			'id'         => 'pricing',
			'heading'    => 'Pricing & Terms',
			'paragraphs' => [ [ 'slot' => 'Final active pricing and terms (reconcile before publishing; do not publish unconfirmed pricing).', 'public' => 'Pricing and terms coming soon. Please call us for details.' ] ],
		],
		[
			'type'    => 'tiles',
			'id'      => 'other-options',
			'variant' => 'dark',
			'heading' => 'Other Ways to Access Care',
			'items'   => [
				[
					'title' => 'Treatment Bundles',
					'text'  => [ 'slot' => 'One line: several sessions of one modality at a better price.' ],
					'url'   => '/treatment-bundles/',
				],
				[
					'title' => 'Insurance & Payment',
					'text'  => [ 'slot' => 'One line: insurance and payment options.' ],
					'url'   => '/understanding-insurance-at-helixona/',
				],
			],
		],
	],
];
