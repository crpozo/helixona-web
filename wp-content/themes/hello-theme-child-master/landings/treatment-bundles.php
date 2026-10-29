<?php
/**
 * Landing: Treatment Bundles — the ONE canonical bundles page.
 * Linked from Health Optimization, Programs & Access, modality pages and booking cards.
 * Separate from the Plan of Care Access Pass and from Vitalize programs.
 * Source: Website 2026 Navigation & Sitemap v3 (Oct 20, 2026). Copy and rates pending approval.
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return [
	'title'           => 'Treatment Bundles',
	'eyebrow'         => 'Programs & Access',
	'lead'            => [ 'slot' => 'One sentence: several sessions of a modality at a better price.' ],
	'seo_description' => 'Treatment Bundles at Helixona: multi-session packages for individual therapies, with booking instructions and clinical-clearance notes.',
	'ctas'            => [
		[ 'label' => 'Book a Therapy', 'url' => '/booking/' ],
		[ 'label' => 'Call (949) 257-2644', 'url' => 'tel:9492572644' ],
	],

	'sections' => [
		[
			'type'       => 'text',
			'id'         => 'about-bundles',
			'heading'    => 'How Bundles Work',
			'paragraphs' => [
				[ 'slot' => 'Individual modality bundles and mix-and-match eligibility.' ],
			],
		],
		[
			'type'    => 'table',
			'id'      => 'rates',
			'heading' => 'Bundle Rates',
			'columns' => [ 'Therapy', 'Single session', 'Bundle', 'Bundle price' ],
			'rows'    => [ 'slot' => 'Approved cash rate table (reconcile with the latest pricing version). Retail/session comparison only where approved.', 'public' => 'Bundle pricing coming soon. Please call us for current rates.' ],
		],
		[
			'type'       => 'text',
			'id'         => 'purchase',
			'heading'    => 'Purchasing & Booking',
			'paragraphs' => [ [ 'slot' => 'How to purchase a bundle and book each session.' ] ],
			'ctas'       => [ [ 'label' => 'Book a Therapy', 'url' => '/booking/' ] ],
		],
		[
			'type'    => 'list',
			'id'      => 'notes',
			'heading' => 'Exclusions & Scheduling Notes',
			'items'   => [
				[ 'slot' => 'Exclusions.' ],
				[ 'slot' => 'Scheduling notes. IV and EBOO require clinical clearance before booking.' ],
			],
		],
		[
			'type'    => 'tiles',
			'id'      => 'other-options',
			'variant' => 'dark',
			'heading' => 'Looking for Something Else?',
			'items'   => [
				[
					'title' => 'Plan of Care Access Pass',
					'text'  => [ 'slot' => 'One line: how the Access Pass differs from bundles.' ],
					'url'   => '/plan-of-care-access-pass/',
				],
				[
					'title' => 'Vitalize Programs',
					'text'  => [ 'slot' => 'One line: structured multi-modality programs around a goal.' ],
					'url'   => '/health-optimization/#vitalize-programs',
				],
				[
					'title' => 'Health Optimization',
					'text'  => [ 'slot' => 'One line: all four ways to begin.' ],
					'url'   => '/health-optimization/',
				],
			],
		],
	],
];
