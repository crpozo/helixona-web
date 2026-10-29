<?php
/**
 * Team member on the home page ("Meet Our Physicians"): Mira Octa Aguilar.
 *
 * Added by hx_cu_home_team_member() in functions.php as a copy of Eduard's row
 * (photo left with the gold frame, bold text under the photo, bio on the right),
 * placed after Thomas's row so the layout alternates left / right / left.
 *
 * Nothing is added to the site until 'image' has a URL. Upload the photo to
 * Media → Add New, copy its "File URL" and paste it below.
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return [
	// Photo: paste the Media Library "File URL" here, e.g.
	// 'https://helixona.com/wp-content/uploads/2026/11/Mira-Aguilar-headshot.jpg'
	'image'     => 'https://helixona.com/wp-content/uploads/2026/10/Mira-Aguilar.png?v=2', // ?v=2: file replaced under the same name (cut-out → photo with background); busts browser caches
	'image_alt' => 'Mira Octa Aguilar, MSN, APRN, FNP-C',

	'name' => 'Meet Mira Octa Aguilar, MSN, APRN, FNP-C',
	'role' => 'Board-Certified Family Nurse Practitioner | Functional & Integrative Medicine',

	// Bold text under the photo.
	'photo_text' => 'For Mira, exceptional healthcare is more than treating a condition. It is about understanding the person, honoring their experience, and helping them build the knowledge, resilience, and confidence to move toward a healthier, more vibrant life.',

	'bio' => [
		'<em>Connecting the dots. Understanding the whole person. Helping patients find their path toward better health.</em>',
		'Mira Aguilar is a board-certified Family Nurse Practitioner who brings a thoughtful, compassionate, and deeply curious approach to patient care. With a background spanning community health, preventive medicine, outpatient surgery, and functional medicine, Mira believes that meaningful healing begins by looking beyond symptoms to understand the underlying factors affecting a person’s health.',
		'Mira’s journey into functional medicine was shaped by her experience caring for patients with complex chronic conditions, particularly those experiencing cognitive decline, neurodegenerative diseases, and challenges involving brain health. During her years working alongside naturopathic and integrative medicine providers, she developed a deeper appreciation for the intricate connections between the brain, immune system, hormones, nutrition, and overall health.',
		'This experience reinforced a belief that guides her practice today: the body is interconnected, and understanding those connections is essential to creating a meaningful path toward healing.',
		'Mira earned her Master of Science in Nursing with a concentration in Family Nurse Practitioner studies from United States University in San Diego. She is board-certified through the American Association of Nurse Practitioners (AANP) and has completed the Institute for Functional Medicine’s Applying Functional Medicine in Clinical Practice (AFMCP) training.',
		'Her clinical background includes chronic disease management, preventive care, functional medicine, IV nutrient therapy, advanced laboratory testing, and personalized nutrition and lifestyle guidance. She is especially passionate about translating complex medical information into clear, practical steps that help patients feel informed, confident, and actively involved in their care.',
		'At Helixona, Mira brings her experience and dedication to helping patients navigate chronic illness while also supporting those seeking to improve their long-term health and vitality. She recognizes that many patients arrive after years of searching for answers, often feeling overwhelmed by conflicting information or disconnected care.',
		'Her approach is grounded in listening carefully, looking at the whole picture, and working collaboratively to identify the next meaningful steps.',
	],
];
