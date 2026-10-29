<?php
/**
 * Service page: Microcurrent Biofeedback Therapy (SCENAR or BioModulator).
 * Replaces /scenar-therapy/ and /biomodulator-therapy/ (both 301 here).
 * Source: Natalie's update (Oct 2026).
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return [
	'title'            => 'Microcurrent Biofeedback Therapy',
	'subtitle'         => 'Harnessing the Body’s Electrical Language for Pain Relief, Nervous System Regulation, and Functional Recovery',
	'eyebrow'          => 'Our Services · SCENAR or BioModulator',
	'auto_create_page' => true,
	'page_status'      => 'publish',
	'hero_image'       => 'https://helixona.com/wp-content/uploads/2025/08/e6964580520b6b5604d797fc352f15c27ab83d6e-scaled.jpg',
	'device_image'     => 'https://helixona.com/wp-content/uploads/2025/11/scenar.png',
	'device_image_alt' => 'SCENAR device used in Microcurrent Biofeedback Therapy at Helixona',
	'seo_description'  => 'Microcurrent Biofeedback Therapy at Helixona: a non-invasive, drug-free approach using SCENAR and BioModulator technology to support pain management, nervous system regulation, and functional recovery.',

	'cta_primary'   => [ 'label' => 'Book therapy', 'url' => 'https://healow.com/apps/provider/-energeticsteam-3835148' ],
	'cta_secondary' => [ 'label' => 'Become a patient', 'url' => 'https://form.typeform.com/to/xNOaiQME' ],

	// Old URLs → this page (301).
	'redirect_from' => [ '/scenar-therapy/', '/biomodulator-therapy/' ],

	// One-time content updates applied by functions.php (menu + services page card).
	'menu' => [
		'rename' => [ [ 'match' => '/scenar-therapy/', 'title' => 'Microcurrent Biofeedback Therapy' ] ],
		'remove' => [ '/biomodulator-therapy/' ],
	],
	'services_card' => [
		'page'     => 'services-and-tech',
		'match'    => 'scenar-therapy',
		'remove'   => [ 'biomodulator-therapy' ],
		'aria'     => 'Microcurrent Biofeedback Therapy',
		'title'    => 'Microcurrent Biofeedback Therapy',
		'subtitle' => 'SCENAR or BioModulator',
		'text'     => 'A personalized, hands-on electrical stimulation treatment that adapts to the body’s responses. Practitioners use SCENAR or BioModulator technology to address areas of discomfort, support neuromuscular function, and promote physical recovery.',
	],

	// --- Copy ---------------------------------------------------------------
	'intro' => [
		'The human body communicates through an intricate network of electrical signals connecting the brain, nerves, muscles, and surrounding tissues. These signals help regulate movement, sensation, and the body’s responses to stress and injury.',
		'When these communication pathways become altered by injury, chronic pain, or prolonged stress, the body may experience discomfort, muscular tension, and changes in physical function.',
		'Microcurrent Biofeedback Therapy is a non-invasive, drug-free approach that uses gentle electrical stimulation and real-time feedback to support pain management, nervous system regulation, and functional recovery.',
		'At Helixona, our Biofrequency Specialists utilize advanced technologies, including SCENAR and BioModulator devices, to provide personalized treatments tailored to each patient’s needs.',
	],

	'facts' => [
		[ 'label' => 'Session',  'value' => '50 minutes' ],
		[ 'label' => 'Approach', 'value' => 'Hands-on, adaptive' ],
		[ 'label' => 'Devices',  'value' => 'SCENAR · BioModulator' ],
		[ 'label' => 'Profile',  'value' => 'Non-invasive, drug-free' ],
	],

	'what_is' => [
		'heading'    => 'What Is Microcurrent Biofeedback Therapy?',
		'paragraphs' => [
			'Microcurrent Biofeedback Therapy combines bioelectrical stimulation with interactive feedback to influence nervous system activity and support the body’s natural regulatory processes.',
			'Unlike conventional electrical stimulation therapies that deliver a fixed signal, SCENAR and BioModulator technologies use feedback from the electrical characteristics of the skin to help guide treatment.',
			'As the device interacts with the skin, it measures changes in electrical impedance and delivers controlled electrical impulses. This creates an adaptive interaction between the technology and the tissues being treated.',
			'The goal is to support more comfortable nervous system signaling, reduce pain, ease muscular tension, and improve functional mobility.',
			'Because treatment is guided by both device feedback and practitioner assessment, each session is individualized rather than following a standardized protocol.',
		],
	],

	'how' => [
		'heading' => 'How It Works',
		'intro'   => [
			'During treatment, your practitioner applies a handheld SCENAR or BioModulator device directly to the skin, working along targeted areas such as the spine, joints, muscles, scars, and nerve pathways.',
			'The device delivers gentle electrical impulses while monitoring the skin’s electrical response. Your practitioner interprets this feedback alongside your symptoms, tissue sensitivity, and physical responses to guide treatment.',
			'These electrical impulses interact with sensory nerve pathways and may help support several physiological functions:',
		],
		'items'   => [
			[ 'title' => 'Pain Modulation',               'text' => 'Influencing sensory nerve signaling to help reduce discomfort and improve pain tolerance.' ],
			[ 'title' => 'Nervous System Regulation',     'text' => 'Supporting relaxation and helping reduce stress-related physical tension.' ],
			[ 'title' => 'Muscular and Fascial Function', 'text' => 'Addressing protective muscle guarding and discomfort that may restrict movement.' ],
			[ 'title' => 'Circulation and Tissue Support','text' => 'Electrical stimulation may influence local circulation and physiological responses involved in recovery.' ],
			[ 'title' => 'Scar and Injury Recovery',      'text' => 'Working around healed scars and areas of altered sensitivity to support comfort and functional mobility.' ],
		],
		'outro'   => [
			'By combining adaptive electrical stimulation with skilled hands-on techniques, treatment can be adjusted to the individual’s needs and tolerance.',
		],
	],

	'expect' => [
		'heading'    => 'What to Expect During a Session',
		'paragraphs' => [
			'Each 50-minute session begins with a brief assessment of your symptoms, health history, and treatment goals.',
			'You’ll rest comfortably on a treatment table while your practitioner applies the SCENAR or BioModulator device to targeted areas of the body.',
			'Depending on your needs, treatment may focus on areas of pain, the spine, joints, muscular restrictions, healed scars, or specific energy currents associated with traditional acupuncture meridians.',
			'Most patients experience a gentle tapping, tingling, or vibrating sensation as the device interacts with the skin. The intensity is adjusted throughout the session to maintain comfort.',
			'Your practitioner may use a combination of dynamic movements across the skin and focused techniques over specific areas to address discomfort and tissue sensitivity.',
			'Many patients describe feeling relaxed, lighter, or more mobile following treatment. Some notice immediate improvements in comfort or range of motion, while others experience gradual changes over a series of sessions.',
			'Treatment frequency and techniques are individualized based on your response and overall wellness goals.',
		],
	],

	'benefit' => [
		'heading' => 'Who Can Benefit from Microcurrent Biofeedback Therapy?',
		'intro'   => 'Microcurrent Biofeedback Therapy may benefit individuals seeking a non-invasive approach to pain management, nervous system support, and functional recovery.',
		'groups'  => [
			[
				'title' => 'Pain Management and Injury Recovery',
				'items' => [ 'Chronic neck, back, and joint discomfort', 'Muscle tension and stiffness', 'Post-injury and post-surgical recovery support', 'Certain types of neuropathic pain', 'Tension-related headaches', 'Discomfort associated with healed scar tissue' ],
			],
			[
				'title' => 'Nervous System and Stress Support',
				'items' => [ 'Stress-related muscular tension', 'Difficulty relaxing', 'Heightened physical sensitivity', 'Physical symptoms associated with prolonged stress', 'Supportive care during complex chronic illness' ],
			],
			[
				'title' => 'Mobility and Physical Performance',
				'items' => [ 'Restricted range of motion', 'Muscular guarding and movement limitations', 'Athletic recovery', 'Chronic musculoskeletal discomfort', 'Functional rehabilitation support' ],
			],
		],
		'outro'   => [
			'Microcurrent Biofeedback Therapy can be incorporated into an integrative treatment plan alongside other medical and supportive therapies.',
			'For patients with significant electrical sensitivity or those who require a more subtle approach, BioTransducer Therapy may be recommended as a separate, gentler treatment option.',
			'Treatment suitability should be reviewed individually, particularly for patients with implanted electronic devices, pregnancy, or certain neurological conditions.',
		],
	],

	'team' => [
		'heading'    => 'Personalized Care Through Experienced Biofrequency Specialists',
		'paragraphs' => [
			'At Helixona, Microcurrent Biofeedback Therapy is provided by experienced Biofrequency Specialists with advanced training in bioelectrical therapies, neuromuscular function, and nervous system regulation.',
			'Our practitioners combine advanced training in SCENAR and BioModulator technologies with an understanding of neuromuscular function, tissue response, and individualized therapeutic care.',
			'We recognize that no two patients experience pain, stress, or recovery in exactly the same way. Some individuals benefit from focused, hands-on neuromodulation, while others require a gentler approach because of heightened electrical sensitivity or complex health histories.',
			'For patients who need a more subtle treatment, BioTransducer Therapy is available as a separate service, offering a gentler, indirect approach to bioelectrical stimulation.',
			'Our goal is to meet each patient where they are, selecting appropriate techniques and treatment intensity to support comfort, function, and overall well-being.',
		],
		'members'    => [
			[
				'name' => 'Danielle Palmer, CNMT',
				'role' => 'Biofrequency Specialist',
				'bio'  => 'Danielle discovered SCENAR after a lifetime of navigating chronic pain from Juvenile Rheumatoid Arthritis and later, a debilitating neck injury. After just one session, her chronic headaches of three years vanished — igniting her passion for frequency medicine. With more than 15 years of experience in SCENAR and bioelectric medicine, Danielle trained under the Revenko SCENAR Academy of Russia, where she achieved Master Class certifications in Advanced Spinal Disorders, Immune Support, Joint Flexibility, and Neuromodulation. She is now recognized internationally as one of the most advanced SCENAR practitioners in the United States. At Helixona, Danielle blends her deep technical expertise with intuition and compassion, helping patients restore function, reduce pain, and reawaken their body’s capacity to heal.',
			],
			[
				'name' => 'Natalie Sakamoto',
				'role' => 'Biofrequency Specialist',
				'bio'  => 'Natalie is known for her precision and empathy in biofrequency therapy. With a background in integrative care and nervous system regulation, she specializes in tailoring sessions to the unique energy patterns and sensitivities of each patient. Her patients often describe her approach as grounding, gentle, and effective — particularly for those recovering from chronic illness, stress-related tension, or trauma-held patterns in the body. Together, Danielle and Natalie form a powerful team dedicated to the art and science of energetic medicine.',
			],
		],
	],

	'philosophy' => [
		'heading' => 'Microcurrent Biofeedback Therapy and the Helixona Philosophy: Eliminate. Nourish. Repair.™',
		'intro'   => 'Microcurrent Biofeedback Therapy complements Helixona’s integrative approach by addressing the nervous system and physical function as part of a broader wellness strategy.',
		'pillars' => [
			[ 'title' => 'Eliminate', 'tag' => 'Reduce Barriers to Recovery',           'text' => 'Support pain management, ease muscular tension, and address physical discomfort that may interfere with movement, rest, and rehabilitation.' ],
			[ 'title' => 'Nourish',   'tag' => 'Support Communication and Regulation',  'text' => 'Use targeted electrical stimulation to interact with sensory pathways, encourage relaxation, and support more comfortable movement.' ],
			[ 'title' => 'Repair',    'tag' => 'Encourage Functional Recovery',          'text' => 'Complement the body’s natural recovery processes by supporting pain management, mobility, and physical function during healing and rehabilitation.' ],
		],
		'outro'   => [
			'This therapy can be incorporated alongside other Helixona services, including IV therapies, lymphatic support, Erchonia Laser, and BioCharger sessions, as clinically appropriate.',
			'Together, these approaches provide opportunities to address different aspects of a patient’s recovery and wellness goals.',
		],
	],

	'closing' => [
		'heading' => 'Experience Microcurrent Biofeedback Therapy at Helixona',
		'text'    => 'Microcurrent Biofeedback Therapy is where technology meets the body’s innate intelligence — guiding your system back to balance, one signal at a time. Whether you’re seeking relief from pain, recovery after illness, or better regulation of your body’s stress response, our Biofrequency Specialists will tailor each session to you. Schedule your session at Helixona to experience how adaptive, feedback-guided stimulation can support your body’s recovery.',
	],
];
