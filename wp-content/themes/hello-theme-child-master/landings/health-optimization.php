<?php
/**
 * Landing: Health Optimization (care path 3 of 3), four-choice landing.
 * Tiles: Individual Therapies · Curated Experiences · Vitalize Programs · Treatment Bundles.
 * Scroll order (approved): Curated → Vitalize → Individual → Bundles, then NP-guided care.
 * Source: Website 2026 Navigation & Sitemap v3 (Oct 20, 2026). Copy pending approval.
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return [
	'title'           => 'Health Optimization',
	'eyebrow'         => 'Find Your Path',
	'lead'            => [ 'slot' => 'Brief opening: proactive, whole-person health optimization at Helixona, including nurse practitioner-guided care and independently accessible eligible therapies. Avoid "wellness".' ],
	'seo_description' => 'Health Optimization at Helixona: individual therapies, curated experiences, Vitalize programs and treatment bundles, plus nurse practitioner-guided care in Irvine.',

	'sections' => [
		[
			'type'    => 'tiles',
			'id'      => 'choices',
			'heading' => 'Choose How You Want to Begin',
			'cols'    => 4,
			'items'   => [
				[
					'question' => '“I know the service I want.”',
					'title'    => 'Individual Therapies',
					'url'      => '/booking/',
					'label'    => 'Browse therapies',
				],
				[
					'question' => '“I want a thoughtfully paired visit or day.”',
					'title'    => 'Curated Experiences',
					'url'      => '#curated-experiences',
				],
				[
					'question' => '“I want a multi-modality program around a goal.”',
					'title'    => 'Vitalize Programs',
					'url'      => '#vitalize-programs',
				],
				[
					'question' => '“I want several sessions of a modality and a better price.”',
					'title'    => 'Treatment Bundles',
					'url'      => '/treatment-bundles/',
					'label'    => 'See bundles',
				],
			],
		],

		// 1) Curated Experiences
		[
			'type'       => 'text',
			'id'         => 'curated-experiences',
			'kicker'     => 'Curated Experiences',
			'heading'    => 'Thoughtfully Paired Visits',
			'paragraphs' => [
				[ 'slot' => 'Intro to Curated Experiences.' ],
				[ 'slot' => 'Approved experiences with inclusions, duration and price (from prior pricing work; final check before publishing).', 'public' => 'Curated experiences coming soon.' ],
			],
			'ctas'       => [ [ 'label' => 'Book a Therapy', 'url' => '/booking/' ] ],
		],

		// 2) Vitalize Programs
		[
			'type'       => 'text',
			'id'         => 'vitalize-programs',
			'variant'    => 'dark',
			'kicker'     => 'Vitalize Programs',
			'heading'    => 'Structured Programs Around a Goal',
			'paragraphs' => [
				[ 'slot' => 'Vitalize: structured, multi-modality, time-bound programs (e.g. Vitalize Your Brain, Vitalize Your Gut), not one-off discounted sessions.' ],
				[ 'slot' => 'Per program: inclusions, eligibility/screening, schedule, duration, price and clinician-reviewed claims. Hypothetical numbers are NOT approved prices.', 'public' => 'Vitalize programs coming soon.' ],
			],
		],

		// 3) Individual Therapies
		[
			'type'       => 'text',
			'id'         => 'individual-therapies',
			'kicker'     => 'Individual Therapies',
			'heading'    => 'Book the Therapy You Want',
			'paragraphs' => [
				[ 'slot' => 'Intro: eligible standalone modalities can be booked online. IV and EBOO require clinical clearance first.' ],
			],
			'ctas'       => [
				[ 'label' => 'Book a Therapy', 'url' => '/booking/' ],
				[ 'label' => 'Treatments & Technologies', 'url' => '/services-and-tech/' ],
			],
		],

		// 4) Treatment Bundles
		[
			'type'       => 'text',
			'id'         => 'treatment-bundles',
			'kicker'     => 'Treatment Bundles',
			'heading'    => 'Several Sessions, a Better Price',
			'paragraphs' => [
				[ 'slot' => 'One-paragraph teaser for bundles. Full details live only on the Treatment Bundles page.' ],
			],
			'ctas'       => [ [ 'label' => 'See Treatment Bundles', 'url' => '/treatment-bundles/' ] ],
		],

		// NP-guided Health Optimization
		[
			'type'       => 'text',
			'id'         => 'np-guided-care',
			'heading'    => 'Nurse Practitioner-Guided Health Optimization',
			'paragraphs' => [
				[ 'slot' => 'NP-guided care: what it includes; appointments are scheduled by our staff after a short conversation.' ],
			],
			'ctas'       => [
				[ 'label' => 'Become a Patient', 'url' => '/become-a-patient/' ],
				[ 'label' => 'Call (949) 257-2644', 'url' => 'tel:9492572644' ],
			],
		],
		[
			'type'    => 'media',
			'id'      => 'media',
			'variant' => 'dark',
			'heading' => 'Inside the Irvine Clinic',
			'items'   => [
				[ 'kind' => 'video', 'slot' => 'November introductory Health Optimization video. Captions/transcript required.' ],
				[ 'kind' => 'photo', 'slot' => 'Actual Irvine clinic photography.' ],
			],
		],
		[
			'type'    => 'cta',
			'heading' => 'Ready When You Are',
			'intro'   => [ 'slot' => 'Closing invitation.' ],
			'ctas'    => [
				[ 'label' => 'Book a Therapy', 'url' => '/booking/' ],
				[ 'label' => 'Explore Other Paths', 'url' => '/find-your-path/' ],
			],
		],
	],
];
