<?php
/**
 * Job data: Registered Nurse (Clinic · Irvine, CA)
 *
 * Loaded by page-templates/job-landing.php when a page with slug
 * "registered-nurse" (or custom field hx_job = registered-nurse) uses the
 * "Job Landing" template. Duplicate this file to publish another position.
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) && ! defined( 'HX_JOB_PREVIEW' ) ) {
	exit;
}

return [
	// --- Header facts -------------------------------------------------------
	'title'            => 'Registered Nurse (RN) – IV Infusion Therapy',
	'bonus'            => '$2,500 Sign-On Bonus', // optional badge in the hero & sticky bar
	'department'       => 'Clinic',
	'reports_to'       => 'IV Department Manager',
	'employment_type'  => 'Full-Time',
	'pay'              => '$90,000 – $120,000 / year',
	'location'         => 'Irvine, CA',
	'date_posted'      => '2026-10-13',

	// --- Page & form --------------------------------------------------------
	'auto_create_page'       => true,       // creates /registered-nurse/ with the Job Landing template
	'page_status'            => 'publish',  // or 'draft' to review before going live
	'applicant_confirmation' => true,       // e-mail the candidate a short confirmation
	'resume_max_mb'          => 10,
	'all_positions_url'      => 'https://helixona.com/careers/#join-form',
	'hero_image'             => 'https://helixona.com/wp-content/uploads/2025/08/e6964580520b6b5604d797fc352f15c27ab83d6e-scaled.jpg',
	// Photos of the new suite and IV prep area (section hidden while empty).
	// Each item: [ 'src' => 'https://helixona.com/wp-content/uploads/....jpg', 'alt' => '...', 'caption' => '...' ]
	'gallery_heading'        => 'Where You’ll Work',
	'gallery_intro'          => 'Our new suite and IV prep area in Irvine.',
	'gallery'                => [],

	// --- Schema.org (Google for Jobs) --------------------------------------
	'schema' => [
		'employment_type' => 'FULL_TIME',
		'salary_min'      => 90000,
		'salary_max'      => 120000,
		'currency'        => 'USD',
		'locality'        => 'Irvine',
		'region'          => 'CA',
		'country'         => 'US',
		'org_name'        => 'Helixona',
		'org_url'         => 'https://helixona.com',
		'org_logo'        => 'https://helixona.com/wp-content/uploads/2025/08/Screenshot-2025-11-03-at-18.32.49-Photoroom.png',
	],

	// --- Copy ---------------------------------------------------------------
	'summary' => 'Provide exceptional infusion nursing care while supporting Helixona’s whole-person approach to healing, with structured cross-training in EBOO.',

	'about' => [
		'At Helixona, we are redefining healthcare by restoring its humanity. Our mission is to create an experience where patients feel seen, understood, and supported throughout their healing journey.',
		'We believe exceptional care happens when compassionate providers, dedicated team members, and thoughtful systems work together to remove unnecessary complexity and allow healing to remain the focus. Every member of our team plays an essential role in creating an exceptional patient experience and supporting whole-person care.',
	],
	'about_quote' => 'We are looking for people who feel called to this work—people who lead with compassion, remain curious, and want to contribute to a different kind of healthcare experience.',

	'opportunity' => [
		'We are seeking a compassionate, highly skilled, and detail-oriented Registered Nurse to provide exceptional infusion nursing care while supporting Helixona’s whole-person approach to healing.',
		'The Registered Nurse will administer physician-ordered IV therapies, injections, and other nursing treatments while being cross-trained to perform Extracorporeal Blood Oxygenation and Ozonation (EBOO) after successful completion of Helixona’s training and competency validation.',
		'This role is ideal for a nurse who values precision, patient education, continuous learning, and collaborative care for medically complex patients.',
	],
	'opportunity_callout' => [
		'icon'  => 'vital_signs',
		'title' => 'Structured EBOO training',
		'text'  => 'EBOO experience is preferred but not required. You will train in Helixona’s EBOO protocols and join treatments once competency requirements are complete.',
	],

	'responsibilities' => [
		'Start, administer, monitor, and discontinue IV infusions in accordance with provider orders, clinic policies, and the RN scope of practice.',
		'Support and collaborate with Helixona’s nurses during infusion services, including patient assessment, IV access, monitoring, escalation, and clinical oversight as appropriate.',
		'Train in Helixona’s EBOO protocols and participate in EBOO treatments once competency requirements have been completed.',
		'Review orders, verify patient and treatment information, and follow medication-administration and safety procedures with consistency and care.',
		'Assess patients before, during, and after treatment; recognize changes in condition; respond appropriately; and promptly involve the provider or emergency resources when needed.',
		'Educate patients on what to expect, answer questions within the RN’s scope, and help patients feel informed and supported throughout their visit.',
		'Document care accurately and promptly in the electronic health record.',
		'Maintain treatment-room readiness, infection-control standards, supplies, equipment checks, and regulatory compliance.',
		'Participate in training across Helixona’s treatment model and remain willing to assist the broader clinical team when patient-care needs require it.',
	],

	'thrive_intro' => 'Clinical precision and genuine warmth, in the same person.',
	'thrive' => [
		[
			'title' => 'A heart for healing',
			'text'  => 'You have a heart for helping people heal and understand that patients with chronic or complex illness may need patience, reassurance, and careful attention.',
		],
		[
			'title' => 'Precise, without losing warmth',
			'text'  => 'You notice details, communicate clearly, and remain steady when a patient is anxious or a situation changes.',
		],
		[
			'title' => 'Genuinely curious',
			'text'  => 'You want to learn about new modalities, understand treatment plans, ask thoughtful questions, and continue growing rather than relying only on what you already know.',
		],
		[
			'title' => 'A true team player',
			'text'  => 'You value teamwork and respect the knowledge each person brings, while understanding the RN’s responsibility to assess, protect patient safety, and escalate concerns.',
		],
		[
			'title' => 'Comfortable building something new',
			'text'  => 'You are comfortable in a growing organization where protocols are rigorous and the team is still building something meaningful together.',
		],
	],

	'required' => [
		'Active, unrestricted California Registered Nurse license in good standing.',
		'Current certifications and continuing education required for the position, scope of practice, and assigned duties.',
		'Experience caring for chronically ill, medically complex, or highly sensitive patients.',
		'Demonstrated competency in IV access, infusion therapy, patient assessment, medication safety, and clinical documentation.',
		'Ability to meet the physical demands of direct patient care, including standing for extended periods and assisting patients safely.',
	],
	'preferred' => [
		'Prior EBOO, apheresis, ozone therapy, specialty infusion, or integrative medicine experience. EBOO experience is preferred but not required; structured training will be provided.',
		'Experience working collaboratively with nurses, physicians, and advanced practice providers in an outpatient setting.',
	],

	'why_join' => 'At Helixona, you’ll be part of a team committed to transforming healthcare through compassionate, patient-centered care. We believe healing happens when exceptional people work together within thoughtful systems that prioritize patients, support providers, and continuously improve the way care is delivered.',
	'benefits' => [
		[
			'icon'  => 'health_and_safety',
			'title' => 'Comprehensive health benefits',
			'text'  => 'Medical, dental, and vision insurance for full-time employees.',
		],
		[
			'icon'  => 'beach_access',
			'title' => 'Paid time off & holidays',
			'text'  => 'Paid time off and paid holidays in accordance with Helixona policy.',
		],
		[
			'icon'  => 'workspace_premium',
			'title' => 'Competitive bonus opportunities',
			'text'  => 'Performance-based bonus structure available upon successful completion of the probationary and training period.',
		],
		[
			'icon'  => 'payments',
			'title' => '$2,500 sign-on bonus',
			'text'  => 'Available upon successful completion of the probationary and training period.',
		],
		[
			'icon'  => 'event_available',
			'title' => 'Consistent work schedule',
			'text'  => 'Enjoy a generally set and predictable schedule that supports work-life balance.',
		],
		[
			'icon'  => 'school',
			'title' => 'Specialized training & development',
			'text'  => 'Receive advanced, hands-on training in Helixona’s innovative therapies, treatments, and technologies.',
		],
		[
			'icon'  => 'favorite',
			'title' => 'Meaningful patient relationships',
			'text'  => 'Build lasting connections with patients and play an important role in supporting their long-term healing journeys.',
		],
		[
			'icon'  => 'volunteer_activism',
			'title' => 'Purpose-driven work',
			'text'  => 'Be part of a passionate, collaborative team dedicated to restoring humanity to healthcare and making a meaningful difference in the lives of patients with complex chronic illnesses.',
		],
		[
			'icon'  => 'trending_up',
			'title' => 'Career growth opportunities',
			'text'  => 'Opportunities for continued professional development, expanded responsibilities, and advancement as Helixona grows.',
		],
		[
			'icon'  => 'add_circle',
			'title' => 'Additional benefits',
			'text'  => 'Further benefits and incentives will be discussed during the interview process.',
		],
	],

	'apply_steps' => [
		[
			'title' => 'Submit your resume',
			'text'  => 'Use the application form below. Attach your resume as a PDF or Word document.',
		],
		[
			'title' => 'We review your application',
			'text'  => 'If your qualifications align with what we’re looking for, a member of our team will contact you to discuss the next steps in the hiring process.',
		],
		[
			'title' => 'Let’s talk',
			'text'  => 'If selected for an interview, be prepared to discuss why whole-person healthcare, IV therapy, advanced clinical technologies, and patient-centered care interest you, along with your approach to patient safety, teamwork, and continuous learning.',
		],
	],
	'eeo' => 'Helixona is an equal opportunity employer. Employment decisions are based on qualifications, merit, and business needs, without unlawful discrimination.',
];
