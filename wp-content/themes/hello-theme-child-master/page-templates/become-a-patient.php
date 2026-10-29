<?php
/**
 * Template Name: Become a Patient
 * Template Post Type: page
 *
 * Patient intake and booking (Dev Spec v1, Oct 29 2026), right under the site header.
 * Two front doors on one page:
 *   - Become a Patient (default): Screen 00 → Chronic A1–A8 (human review, no booking)
 *     or Health Optimization H1 → H5 → B1–B4 (direct booking) / H9 → H10 (provider-guided).
 *   - Book a Treatment (#book-a-treatment): straight to H1 with Individual Therapies.
 * Steps, validation and submission live in assets/js/become-a-patient.js; the catalog in
 * assets/js/become-a-patient-catalog.js; logic and e-mails in inc/become-a-patient.php.
 * Patient-facing copy is the approved screen-collection text (Section 6) — do not rewrite.
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$hx_phi = function_exists( 'hx_patient_phi_endpoint' ) ? hx_patient_phi_endpoint() : ''; // AWS API Gateway URL; '' = health details are not sent

/** Single-choice / multi-choice chips. */
$hx_chips = function ( $key, $options, $multi = false ) {
	foreach ( $options as $value => $label ) {
		?>
		<button type="button" class="hxp-chip" aria-pressed="false" data-hxp-key="<?php echo esc_attr( $key ); ?>" data-hxp-val="<?php echo esc_attr( $value ); ?>"<?php echo $multi ? ' data-hxp-multi' : ''; ?>><span aria-hidden="true" class="hxp-dot<?php echo $multi ? ' hxp-dot--multi' : ''; ?>"></span><span><?php echo esc_html( $label ); ?></span></button>
		<?php
	}
};

/** Option with a bold title and a description (A2, H10…). */
$hx_options = function ( $key, $options ) {
	foreach ( $options as $value => $o ) {
		?>
		<button type="button" class="hxp-chip hxp-chip--option" aria-pressed="false" data-hxp-key="<?php echo esc_attr( $key ); ?>" data-hxp-val="<?php echo esc_attr( $value ); ?>"><span aria-hidden="true" class="hxp-dot"></span><span class="hxp-option"><span class="hxp-option__title"><?php echo esc_html( $o[0] ); ?></span><?php if ( ! empty( $o[1] ) ) : ?><span class="hxp-option__text"><?php echo esc_html( $o[1] ); ?></span><?php endif; ?></span></button>
		<?php
	}
};

/** Large choice card (care path). $icon = inline SVG markup. */
$hx_card = function ( $key, $value, $title, $text, $icon ) {
	?>
	<button type="button" class="hxp-card" aria-pressed="false" data-hxp-key="<?php echo esc_attr( $key ); ?>" data-hxp-val="<?php echo esc_attr( $value ); ?>">
		<span class="hxp-card__top">
			<?php echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG ?>
			<span aria-hidden="true" class="hxp-tick"><svg width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="#080808" stroke-width="1.8"><path d="M2 6.5l2.6 2.5L10 3.5"></path></svg></span>
		</span>
		<span class="hxp-serif hxp-card__title"><?php echo esc_html( $title ); ?></span>
		<span class="hxp-card__text"><?php echo esc_html( $text ); ?></span>
	</button>
	<?php
};

/** Step heading + lead. */
$hx_head = function ( $title, $lead = '' ) {
	?>
	<div class="hxp-head">
		<h2 class="hxp-serif hxp-h2"><?php echo esc_html( $title ); ?></h2>
		<?php if ( $lead ) : ?><p class="hxp-lead hxp-measure"><?php echo wp_kses( $lead, [ 'span' => [ 'class' => true, 'data-hxp-bind' => true ] ] ); ?></p><?php endif; ?>
	</div>
	<?php
};

/** Sample-data note (dashed box). */
$hx_sample = function ( $title, $text ) {
	?>
	<div class="hxp-sample">
		<span aria-hidden="true" class="hxp-sample__dot"></span>
		<p><strong><?php echo esc_html( $title ); ?></strong> <?php echo esc_html( $text ); ?></p>
	</div>
	<?php
};

/** Summary row (label / value). */
$hx_row = function ( $label, $bind, $extra = '' ) {
	?>
	<div class="hxp-row"><span class="hxp-row__label"><?php echo esc_html( $label ); ?></span><span class="hxp-row__value<?php echo $extra ? ' ' . esc_attr( $extra ) : ''; ?>"<?php echo $bind ? ' data-hxp-bind="' . esc_attr( $bind ) . '"' : ''; ?>></span></div>
	<?php
};

/** Text input with its error line. */
$hx_input = function ( $key, $label, $type, $autocomplete, $error, $inputmode = '', $extra = '' ) {
	static $n = 0; // the same field can appear on more than one screen (A5, B1, contact)
	$id = 'hx-' . $key . '-' . ( ++$n );
	?>
	<div class="hxp-field">
		<label for="<?php echo esc_attr( $id ); ?>" class="hxp-label"><?php echo esc_html( $label ); ?></label>
		<input id="<?php echo esc_attr( $id ); ?>" class="hxp-input" type="<?php echo esc_attr( $type ); ?>" autocomplete="<?php echo esc_attr( $autocomplete ); ?>"<?php echo $inputmode ? ' inputmode="' . esc_attr( $inputmode ) . '"' : ''; ?><?php echo $extra; // phpcs:ignore WordPress.Security.EscapeOutput -- static attributes ?> data-hxp-input="<?php echo esc_attr( $key ); ?>" aria-invalid="false" aria-describedby="<?php echo esc_attr( $id ); ?>-err">
		<span id="<?php echo esc_attr( $id ); ?>-err" class="hxp-error" data-hxp-err="<?php echo esc_attr( $key ); ?>" hidden><?php echo esc_html( $error ); ?></span>
	</div>
	<?php
};

/** Simulated card field (Stripe Elements goes here). */
$hx_card_fields = function ( $prefix ) {
	?>
	<div class="hxp-group hxp-group--pay">
		<span class="hxp-label">Credit card</span>
		<div class="hxp-field">
			<label for="hx-<?php echo esc_attr( $prefix ); ?>card" class="hxp-label-sm">Card number</label>
			<input id="hx-<?php echo esc_attr( $prefix ); ?>card" class="hxp-input" type="text" inputmode="numeric" autocomplete="cc-number" placeholder="•••• •••• •••• ••••" data-hxp-input="card">
		</div>
		<div class="hxp-grid2 hxp-grid2--tight">
			<div class="hxp-field">
				<label for="hx-<?php echo esc_attr( $prefix ); ?>exp" class="hxp-label-sm">Expiry</label>
				<input id="hx-<?php echo esc_attr( $prefix ); ?>exp" class="hxp-input" type="text" inputmode="numeric" autocomplete="cc-exp" placeholder="MM / YY" data-hxp-input="exp">
			</div>
			<div class="hxp-field">
				<label for="hx-<?php echo esc_attr( $prefix ); ?>cvc" class="hxp-label-sm">Security code</label>
				<input id="hx-<?php echo esc_attr( $prefix ); ?>cvc" class="hxp-input" type="text" inputmode="numeric" autocomplete="cc-csc" placeholder="CVC" data-hxp-input="cvc">
			</div>
		</div>
		<span class="hxp-error" data-hxp-err="card" hidden>Please enter your card details.</span>
	</div>
	<?php
};

/** Secure upload (demo: files stay in the browser, nothing is uploaded). */
$hx_upload = function ( $key, $label ) {
	?>
	<label class="hxp-upload">
		<input type="file" accept="image/*,application/pdf" multiple data-hxp-file="<?php echo esc_attr( $key ); ?>">
		<span class="hxp-upload__icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="#B99A5D" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12V3M5 7l4-4 4 4"></path><path d="M3 12v3h12v-3"></path></svg></span>
		<span class="hxp-upload__text"><span class="hxp-upload__label"><?php echo esc_html( $label ); ?></span><span class="hxp-upload__files" data-hxp-bind="files_<?php echo esc_attr( $key ); ?>">Images or PDF · front and back of card</span></span>
	</label>
	<?php
};

$hx_icon_chronic  = '<svg aria-hidden="true" width="34" height="34" viewBox="0 0 34 34" fill="none" stroke="#B99A5D" stroke-width="1.4"><circle cx="13" cy="17" r="8"></circle><circle cx="21" cy="17" r="8"></circle></svg>';
$hx_icon_optimize = '<svg aria-hidden="true" width="34" height="34" viewBox="0 0 34 34" fill="none" stroke="#B99A5D" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="17" cy="17" r="13"></circle><path d="M10 21l5-5 3 3 6-7"></path><path d="M20 12h4v4"></path></svg>';
$hx_ext           = '<svg aria-hidden="true" width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.3"><path d="M5 2H2v8h8V7M7 2h3v3M10 2L5.5 6.5"></path></svg>';

get_header();
?>
<main id="content" class="hxp" data-ajax="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" data-token="<?php echo esc_attr( hx_job_token( HX_PATIENT_SLUG ) ); ?>" data-phi-endpoint="<?php echo esc_url( $hx_phi ); ?>" data-mode="<?php echo ( defined( 'HX_PATIENT_MOCKUP' ) && HX_PATIENT_MOCKUP ) ? 'mockup' : 'live'; ?>">

	<section id="become-a-patient-form" aria-label="Become a Patient form" class="hxp-section">
		<div class="hxp-shell" data-hxp-shell>

			<noscript><p class="hxp-lead">This form needs JavaScript. Please call us at <a href="tel:9492572644">(949) 257-2644</a> and we’ll guide you to the right next step.</p></noscript>

			<p aria-live="polite" class="sr-only" data-hxp-announce></p>
			<div class="hxp-hp" aria-hidden="true"><label>Website <input type="text" name="hx_website" tabindex="-1" autocomplete="off" data-hxp-honeypot></label></div>

			<!-- Top: step label + progress -->
			<div class="hxp-progress" data-hxp-progress hidden>
				<div class="hxp-progress__labels">
					<span class="hxp-progress__step" data-hxp-bind="stepLabel"></span>
					<span class="hxp-progress__flow" data-hxp-bind="flowLabel"></span>
				</div>
				<div role="progressbar" aria-valuemin="1" aria-label="Form progress" class="hxp-bars" data-hxp-bars></div>
			</div>

			<!-- ===== Screen 00: What Brings You to Helixona? ===== -->
			<div class="hxp-step hxp-step--wide" data-hxp-step="path" hidden>
				<div class="hxp-head hxp-head--path">
					<h2 class="hxp-serif hxp-h2">What Brings You to Helixona?</h2>
					<p class="hxp-lead hxp-measure">At Helixona, we help people actively work toward better health. <strong class="hxp-strong">We are a treatment-focused advanced medical clinic, not a primary care practice.</strong> Whether you are seeking help with chronic illness or looking to improve and protect your health, we offer different ways to begin.</p>
				</div>
				<div class="hxp-grid2" role="group" aria-label="Care path">
					<?php
					$hx_card( 'path', 'chronic', 'Chronic or Complex Chronic Recovery', 'I am living with an ongoing or complex health condition and want to explore a deeper, treatment-focused approach.', $hx_icon_chronic );
					$hx_card( 'path', 'optimize', 'Health Optimization', 'I want to improve or protect my health through individual therapies, thoughtfully designed experiences, or targeted treatment programs.', $hx_icon_optimize );
					?>
				</div>
				<span class="hxp-error" data-hxp-err="path" hidden>Please choose the option that best describes you.</span>
			</div>

			<!-- ===== A1: Healing Is a Team Effort ===== -->
			<div class="hxp-step" data-hxp-step="a1" hidden>
				<?php $hx_head( 'Healing Is a Team Effort' ); ?>
				<div class="hxp-prose">
					<p>At Helixona, we believe meaningful healing requires more than an office visit, a prescription, or a treatment plan written on paper. It takes a partnership.</p>
					<p><strong>We are a treatment-focused advanced medical clinic, not a primary care practice.</strong> Our mission is to help people work toward meaningful, lasting improvements in their health.</p>
					<p>We know many of our patients have spent years searching for answers. You have seen doctors, tried treatments, and invested enormous amounts of time, energy, and hope into getting well. We understand how exhausting that journey can be.</p>
					<p>That is why we have built a different approach to care: comprehensive investigation, individualized treatment plans, advanced therapies, education, and a care team committed to walking beside you. Healing is not something our providers can do for you alone. It is something we work toward together.</p>
					<h3 class="hxp-serif hxp-h3">What Does Active Participation Look Like?</h3>
					<ul class="hxp-list">
						<li><strong>Following your personalized treatment plan.</strong> Attend recommended therapies and appointments, follow instructions carefully, and communicate before changing medications, supplements, or routines.</li>
						<li><strong>Staying connected.</strong> Regularly report symptoms, complete brief daily check-ins in the Helixona patient system, and share how you respond to treatment.</li>
						<li><strong>Learning about your health.</strong> Participate in classes, videos, reading materials, and book clubs.</li>
						<li><strong>Making changes at home.</strong> When appropriate, this may involve nutrition, sleep, stress, or your home environment, including relevant air-quality concerns.</li>
						<li><strong>Including loved ones.</strong> Educational opportunities for partners, caregivers, and loved ones may help them support you.</li>
						<li><strong>Working together over time.</strong> Attend check-ins, share what is and is not working, ask questions, and participate in decisions.</li>
					</ul>
					<p>Active participation does not mean pushing beyond your limits. It means engaging in ways that are realistic and safe for you.</p>
					<h3 class="hxp-serif hxp-h3">Why We Begin With a Screening Process</h3>
					<p>Our physicians can support only a limited number of patients through this intensive approach. We receive many inquiries and want to dedicate that capacity to people who understand recovery is a partnership. A Patient Advisor will help you understand the expectations before you decide whether to move forward.</p>
					<p>If you are ready to be an active partner in your recovery, we are ready to explore what is possible with you.</p>
				</div>
			</div>

			<!-- ===== A2: Readiness ===== -->
			<div class="hxp-step" data-hxp-step="a2" hidden>
				<?php $hx_head( 'Are You Ready to Take an Active Role in Your Recovery?', 'Our treatment programs involve more than medical appointments. They may include recommended therapies, regular symptom check-ins, education, changes at home, and ongoing communication with your care team. Which best describes where you are today?' ); ?>
				<div role="group" aria-label="Where you are today" class="hxp-stack">
					<?php
					$hx_options(
						'readiness',
						[
							'ready'              => [ 'I am ready to participate.', 'I understand that my involvement, follow-through, communication, and willingness to learn are important parts of this process.' ],
							'questions'          => [ 'I am interested, but I would like to learn more.', 'I have questions about the time, treatments, education, daily check-ins, or financial commitment.' ],
							'limitations'        => [ 'I am willing, but I have limitations.', 'My health, energy, finances, transportation, or other circumstances may affect how much I can participate.' ],
							'office_visits_only' => [ 'I am looking for a different type of care.', 'I primarily want occasional office visits, prescriptions, or medication management rather than an ongoing treatment program.' ],
						]
					);
					?>
				</div>
				<span class="hxp-error" data-hxp-err="readiness" hidden>Please choose one option to continue.</span>
			</div>

			<!-- ===== A3: Accessibility / travel ===== -->
			<div class="hxp-step" data-hxp-step="a3" hidden>
				<?php $hx_head( "Let's Make Sure Your Care Is Accessible", 'Depending on your individual treatment plan, you may be asked to visit our Irvine clinic two or three times per week. We want to make sure traveling to Helixona is manageable and safe for you.' ); ?>
				<div class="hxp-grid2">
					<?php
					$hx_input( 'city', 'City', 'text', 'address-level2', 'Please enter your city.' );
					$hx_input( 'zip', 'ZIP code', 'text', 'postal-code', 'Please enter a 5-digit ZIP code.', 'numeric', ' maxlength="5"' );
					?>
				</div>
				<div class="hxp-group">
					<span id="hx-tr-label" class="hxp-label">If frequent visits are recommended, how would you manage transportation?</span>
					<div role="group" aria-labelledby="hx-tr-label" class="hxp-grid2 hxp-grid2--tight">
						<?php $hx_chips( 'transport', [ 'drive_self' => 'I can usually drive myself.', 'driver' => 'I have a reliable person who can drive me.', 'difficult' => 'My symptoms sometimes make travel difficult.', 'discuss' => 'I would need to discuss transportation options.' ] ); ?>
					</div>
					<span class="hxp-error" data-hxp-err="transport" hidden>Please choose one option to continue.</span>
				</div>
			</div>

			<!-- ===== A4: Insurance (intake only, not payment) ===== -->
			<div class="hxp-step" data-hxp-step="a4" hidden>
				<?php $hx_head( "Let's Review Your Insurance", 'Helixona accepts Medicare and participates with certain PPO insurance plans. Because every plan is different, our team reviews coverage before scheduling intensive medical evaluations. This helps us explain expected costs and available options before you begin.' ); ?>
				<div class="hxp-group">
					<span id="hx-payer-label" class="hxp-label">What type of coverage do you have?</span>
					<div role="group" aria-labelledby="hx-payer-label" class="hxp-grid2 hxp-grid2--tight">
						<?php
						$hx_chips(
							'payer',
							[
								'ppo'                => 'PPO health insurance',
								'medicare'           => 'Original Medicare',
								'medicare_advantage' => 'Medicare Advantage',
								'medicare_medical'   => 'Medicare with Medi-Cal or another secondary plan',
								'medical_only'       => 'Medi-Cal only',
								'hmo_other'          => 'HMO or another insurance arrangement',
								'self_pay'           => 'I do not have insurance / I am interested in cash-pay options',
								'unknown'            => 'I am not sure',
							]
						);
						?>
					</div>
					<span class="hxp-error" data-hxp-err="payer" hidden>Please choose one option to continue.</span>
				</div>
			</div>

			<!-- ===== A5: Contact + documents ===== -->
			<div class="hxp-step" data-hxp-step="a5" hidden>
				<?php $hx_head( 'Just a Few More Details', 'Thank you for sharing a little about yourself. Our Patient Advisor will use this information to review your inquiry and talk through the next steps with you.' ); ?>
				<div class="hxp-grid2">
					<?php
					$hx_input( 'first', 'First name', 'text', 'given-name', 'Please enter your first name.' );
					$hx_input( 'last', 'Last name', 'text', 'family-name', 'Please enter your last name.' );
					$hx_input( 'email', 'Email address', 'email', 'email', 'Please enter a valid email address.', 'email' );
					$hx_input( 'phone', 'Phone number', 'tel', 'tel', 'Please enter a phone number we can reach you at.', 'tel' );
					?>
				</div>
				<div class="hxp-group">
					<span class="hxp-label">Insurance documents</span>
					<p class="hxp-small">If your plan requires benefits review, please provide your insurance card and the relevant plan documents through a secure upload. If you cannot locate your Evidence of Coverage (EOC) or Summary Plan Description (SPD), our team can help you understand what is needed.</p>
					<div role="group" aria-label="Insurance documents" class="hxp-stack">
						<?php $hx_chips( 'docs', [ 'upload' => 'I have my insurance documents available. Upload them securely now.', 'needs_help' => 'I need help finding my coverage documents.', 'unsure' => 'I am not sure which documents apply to my plan.' ] ); ?>
					</div>
					<div data-hxp-show="docsUpload" hidden><?php $hx_upload( 'a5', 'Upload insurance card and EOC / SPD' ); ?></div>
				</div>
				<?php $hx_sample( 'Secure upload (demo).', 'In production, documents go to encrypted, access-logged storage under a BAA. Nothing is uploaded from this preview, and records are never requested by ordinary email.' ); ?>
			</div>

			<!-- ===== A7: Find the right care (A2 option 4) ===== -->
			<div class="hxp-step" data-hxp-step="a7" hidden>
				<?php $hx_head( "Let's Help You Find the Right Care" ); ?>
				<div class="hxp-prose">
					<p>Thank you for sharing what you are looking for.</p>
					<p>Helixona is a treatment-focused advanced medical clinic. Our chronic recovery programs involve a collaborative approach that goes beyond occasional office visits and prescription management.</p>
					<p>If you are primarily looking for ongoing medical consultations or medication management, another practice may be a better fit for your needs right now.</p>
					<p>We want you to find care that supports you. These directories may be helpful places to begin:</p>
				</div>
				<ul class="hxp-links">
					<?php
					$hx_dirs = [
						'Find an Institute for Functional Medicine Practitioner' => 'https://www.ifm.org/find-a-practitioner/',
						'Find an ILADS Provider (ILADS.org)'     => 'https://www.ilads.org/patient-care/provider-search',
						'Find an ISEAI Professional (ISEAI.org)' => 'https://iseai.org/find-a-professional',
						'Neil Nathan, MD Trained Practitioners'  => 'https://neilnathanmd.com/books',
					];
					foreach ( $hx_dirs as $hx_label => $hx_url ) :
						?>
						<li><a href="<?php echo esc_url( $hx_url ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $hx_label ); ?><?php echo $hx_ext; // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG ?></a></li>
					<?php endforeach; ?>
				</ul>
				<p class="hxp-small"><em>Provider participation, availability, and insurance acceptance vary. These directories are starting points, not endorsements.</em></p>
				<p class="hxp-lead">If your goals change and you would like to explore a treatment-focused approach in the future, you are welcome to return.</p>
				<div><button type="button" class="hxp-continue" data-hxp-goto="a2">Reconsider My Options</button></div>
			</div>

			<!-- ===== A8: Medi-Cal only ===== -->
			<div class="hxp-step" data-hxp-step="a8" hidden>
				<?php $hx_head( "Let's Review Your Care Options" ); ?>
				<div class="hxp-prose">
					<p>Thank you for sharing your insurance information.</p>
					<p>Helixona is not currently a participating Medi-Cal provider. If Medi-Cal is your only coverage, we may not be able to provide the insurance-supported medical care you are seeking.</p>
					<p>We want you to have clear information before moving forward. You can explore providers who participate with your coverage or contact our team with questions.</p>
				</div>
				<div class="hxp-actions">
					<a class="hxp-continue" href="https://www.healthcareoptions.dhcs.ca.gov/" target="_blank" rel="noopener">Find a Medi-Cal Provider</a>
					<a class="hxp-outline" href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>">Ask Our Team a Question</a>
				</div>
			</div>

			<!-- ===== H1–H4: Therapies, experiences and bundles (catalog-driven) ===== -->
			<div class="hxp-step hxp-step--wide" data-hxp-step="h1" hidden>
				<?php $hx_head( 'How Would You Like to Experience Helixona?', 'Explore therapies and experiences that fit your goals. You can choose something to enjoy today or learn about a more personalized program.' ); ?>
				<div class="hxp-search">
					<svg aria-hidden="true" width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="#6B6B6B" stroke-width="1.4" stroke-linecap="round"><circle cx="8" cy="8" r="5.5"></circle><path d="M12.2 12.2L16 16"></path></svg>
					<label for="hx-search" class="sr-only">Search therapies</label>
					<input id="hx-search" class="hxp-input hxp-search__input" type="search" placeholder="Search therapies, experiences and bundles" autocomplete="off" data-hxp-search>
				</div>
				<div class="hxp-tabs" role="tablist" aria-label="Category">
					<button type="button" role="tab" class="hxp-tab" data-hxp-tab="experience" aria-selected="false"><span class="hxp-tab__title">Curated Experiences</span><span class="hxp-tab__text">Thoughtfully combined therapies in one visit.</span></button>
					<button type="button" role="tab" class="hxp-tab" data-hxp-tab="individual" aria-selected="false"><span class="hxp-tab__title">Individual Therapies</span><span class="hxp-tab__text">Explore a single treatment that interests you.</span></button>
					<button type="button" role="tab" class="hxp-tab" data-hxp-tab="bundle" aria-selected="false"><span class="hxp-tab__title">Treatment Bundles</span><span class="hxp-tab__text">Purchase several sessions at a savings and plan your visits over time.</span></button>
				</div>
				<p class="hxp-lead hxp-measure" data-hxp-bind="catIntro"></p>
				<div class="hxp-stack" data-hxp-catalog role="list"></div>
				<span class="hxp-error" data-hxp-err="item" hidden>Please select a therapy, experience or bundle to continue.</span>
				<?php $hx_sample( 'Sample catalog.', 'Names follow the Services & Tech page. Prices shown are illustrative only and are not approved; descriptions, durations, eligibility and clearance rules will load from the clinic\'s service catalog.' ); ?>
				<p class="hxp-more" data-hxp-show="moreLink">Looking for something more comprehensive? <button type="button" class="hxp-link" data-hxp-provider>Explore Vitalize Programs and provider-guided Health Optimization.</button></p>
			</div>

			<!-- ===== H5: Scheduling ===== -->
			<div class="hxp-step" data-hxp-step="h5" hidden>
				<?php $hx_head( "Let's Find a Time That Works for You", 'Choose one of the earliest available appointments below, or tell us which days and times work best for you.' ); ?>
				<p class="hxp-small">Availability for <span class="hxp-strong" data-hxp-bind="itemName"></span><span data-hxp-bind="itemCoord"></span></p>
				<div role="group" aria-label="Available appointments" class="hxp-grid2 hxp-grid2--tight" data-hxp-slots></div>
				<span class="hxp-error" data-hxp-err="slot" hidden>Please select an appointment time, or ask our team to help you schedule.</span>

				<details class="hxp-details" data-hxp-prefs>
					<summary>Not seeing a time that works?</summary>
					<div class="hxp-details__body">
						<div class="hxp-group">
							<span id="hx-wd-label" class="hxp-label">Preferred weekdays <span class="hxp-optional">(choose any)</span></span>
							<div role="group" aria-labelledby="hx-wd-label" class="hxp-wrap-row">
								<?php $hx_chips( 'prefDays', [ '1' => 'Mon', '2' => 'Tue', '3' => 'Wed', '4' => 'Thu', '5' => 'Fri', '6' => 'Sat' ], true ); ?>
							</div>
						</div>
						<div class="hxp-group">
							<span id="hx-tw-label" class="hxp-label">Preferred time windows <span class="hxp-optional">(choose any)</span></span>
							<div role="group" aria-labelledby="hx-tw-label" class="hxp-wrap-row">
								<?php $hx_chips( 'prefWindows', [ 'early' => 'Early morning', 'morning' => 'Morning', 'afternoon' => 'Afternoon', 'late' => 'Late afternoon' ], true ); ?>
							</div>
						</div>
						<div class="hxp-field hxp-field--date">
							<label for="hx-prefdate" class="hxp-label">Specific date <span class="hxp-optional">(optional)</span></label>
							<input id="hx-prefdate" class="hxp-input" type="date" data-hxp-input="prefDate">
							<span class="hxp-error" data-hxp-err="prefDate" hidden>We are closed on Sundays. Please choose another day.</span>
						</div>
						<div><button type="button" class="hxp-outline" data-hxp-find>Find Times That Work for Me</button></div>
						<p class="hxp-small" data-hxp-bind="findNote"></p>
					</div>
				</details>
				<div class="hxp-actions hxp-actions--links">
					<button type="button" class="hxp-link" data-hxp-calendar>View Full Calendar</button>
					<button type="button" class="hxp-link" data-hxp-assist>Ask Our Team to Help Me Schedule</button>
				</div>
				<p class="hxp-callout" data-hxp-show="assist" hidden>Our team will contact you to find a time. You can continue with your details now.</p>
				<?php $hx_sample( 'Sample availability.', 'Real times load from eClinicalWorks (rooms, equipment and staff are checked together). Sundays are never offered.' ); ?>
			</div>

			<!-- ===== B1: Your details ===== -->
			<div class="hxp-step" data-hxp-step="b1" hidden>
				<?php $hx_head( 'Your Details', 'We use your email to send your confirmation and a secure link to manage your booking.' ); ?>
				<div class="hxp-grid2">
					<?php
					$hx_input( 'first', 'First name', 'text', 'given-name', 'Please enter your first name.' );
					$hx_input( 'last', 'Last name', 'text', 'family-name', 'Please enter your last name.' );
					$hx_input( 'email', 'Email address', 'email', 'email', 'Please enter a valid email address.', 'email' );
					$hx_input( 'phone', 'Phone number', 'tel', 'tel', 'Please enter a phone number we can reach you at.', 'tel' );
					$hx_input( 'dob', 'Date of birth', 'date', 'bday', 'Please enter your date of birth.' );
					$hx_input( 'address', 'Address', 'text', 'street-address', 'Please enter your address.' );
					?>
				</div>
				<?php
				$hx_card_fields( 'b1' );
				$hx_sample( 'Simulated card field.', 'In production this is a Stripe Elements field: card data never touches Helixona servers; only the Stripe token and last four digits are stored.' );
				?>
			</div>

			<!-- ===== B2: Informed consent ===== -->
			<div class="hxp-step" data-hxp-step="b2" hidden>
				<?php $hx_head( 'Informed Consent', 'Please read the consent for each service you are booking.' ); ?>
				<div class="hxp-stack" data-hxp-consents></div>
				<span class="hxp-error" data-hxp-err="consent" hidden>Please confirm you have read each consent to continue.</span>
			</div>

			<!-- ===== B3: Anything we should know? ===== -->
			<div class="hxp-step" data-hxp-step="b3" hidden>
				<?php $hx_head( 'Anything We Should Know?' ); ?>
				<div class="hxp-field">
					<label for="hx-occasion" class="hxp-label">Is this for a special occasion, or is there anything you want us to know? <span class="hxp-optional">(optional)</span></label>
					<textarea id="hx-occasion" class="hxp-input hxp-textarea" rows="4" maxlength="500" data-hxp-input="occasion"></textarea>
					<span class="hxp-small hxp-count"><span data-hxp-bind="occasionCount">0</span> / 500</span>
				</div>
			</div>

			<!-- ===== B4: Review & confirm ===== -->
			<div class="hxp-step" data-hxp-step="b4" hidden>
				<?php $hx_head( 'Review and Confirm', 'Please review your booking before confirming.' ); ?>
				<div class="hxp-rows">
					<?php
					$hx_row( 'Selection', 'itemName' );
					$hx_row( 'Appointment', 'slotLabel' );
					$hx_row( 'Patient', 'fullName' );
					$hx_row( 'Card', 'cardLabel' );
					$hx_row( 'Total due today', 'itemPrice', 'hxp-serif hxp-row__total' );
					?>
				</div>
				<div class="hxp-policy">
					<span class="hxp-label">Cancellation and no-show policy</span>
					<p class="hxp-small" data-hxp-bind="policy"></p>
					<label class="hxp-check"><input type="checkbox" data-hxp-check="policy"><span>I have read and accept the cancellation and no-show policy, and authorize Helixona to keep my card on file for this purpose.</span></label>
					<span class="hxp-error" data-hxp-err="policy" hidden>Please accept the cancellation and no-show policy to confirm.</span>
				</div>
			</div>

			<!-- ===== H9: Provider-guided Health Optimization ===== -->
			<div class="hxp-step" data-hxp-step="h9" hidden>
				<?php $hx_head( "Let's Explore Your Health Goals" ); ?>
				<div class="hxp-prose">
					<p>Helixona offers provider-guided Health Optimization for people who want a more personalized approach to improving or protecting their health.</p>
					<p>Our providers focus on specific health goals and may recommend evaluations, targeted therapies, or a structured treatment program.</p>
					<p><strong>We are not a primary care practice, and provider-guided care is not designed around occasional office visits alone.</strong></p>
				</div>
				<div class="hxp-group">
					<span id="hx-q1-label" class="hxp-label">What would you most like to work on?</span>
					<div role="group" aria-labelledby="hx-q1-label" class="hxp-grid2 hxp-grid2--tight">
						<?php $hx_chips( 'hoGoal', [ 'energy' => 'Energy and vitality', 'brain' => 'Brain health and mental clarity', 'digestive' => 'Digestive health', 'aging' => 'Healthy aging and longevity', 'intimate' => 'Intimate health', 'other' => 'Another specific health goal' ] ); ?>
					</div>
					<span class="hxp-error" data-hxp-err="hoGoal" hidden>Please choose one option.</span>
				</div>
				<div class="hxp-group">
					<span id="hx-q2-label" class="hxp-label">What are you interested in?</span>
					<div role="group" aria-labelledby="hx-q2-label" class="hxp-grid2 hxp-grid2--tight">
						<?php $hx_chips( 'hoInterest', [ 'program' => 'A structured treatment program', 'therapies' => 'Individual therapies or curated experiences', 'plan' => 'A provider-guided plan of care', 'unsure' => 'I am not sure yet and would like guidance' ] ); ?>
					</div>
					<span class="hxp-error" data-hxp-err="hoInterest" hidden>Please choose one option.</span>
					<p class="hxp-callout" data-hxp-show="toTherapies" hidden>Great — you can choose individual therapies or curated experiences directly. Continue to explore them.</p>
				</div>
				<div class="hxp-group">
					<span id="hx-q3-label" class="hxp-label">Can you attend appointments at our Irvine clinic if recommended?</span>
					<div role="group" aria-labelledby="hx-q3-label" class="hxp-wrap-row">
						<?php $hx_chips( 'hoAttend', [ 'yes' => 'Yes', 'considerations' => 'Yes, with some scheduling considerations', 'discuss' => 'I am unsure and would like to discuss this' ] ); ?>
					</div>
					<span class="hxp-error" data-hxp-err="hoAttend" hidden>Please choose one option.</span>
				</div>
			</div>

			<!-- ===== Contact (provider-guided path) ===== -->
			<div class="hxp-step" data-hxp-step="contact" hidden>
				<?php $hx_head( "First, let's get to know you.", 'A few details so our team can follow up with you.' ); ?>
				<div class="hxp-grid2">
					<?php
					$hx_input( 'first', 'First name', 'text', 'given-name', 'Please enter your first name.' );
					$hx_input( 'last', 'Last name', 'text', 'family-name', 'Please enter your last name.' );
					$hx_input( 'email', 'Email address', 'email', 'email', 'Please enter a valid email address.', 'email' );
					$hx_input( 'phone', 'Phone number', 'tel', 'tel', 'Please enter a phone number we can reach you at.', 'tel' );
					?>
				</div>
			</div>

			<!-- ===== H10: How would you like to cover your visit? ===== -->
			<div class="hxp-step" data-hxp-step="h10" hidden>
				<?php $hx_head( 'How Would You Like to Cover Your Visit?', 'Provider-guided visits can be covered through a participating PPO insurance plan or paid directly. Choose how you would like to proceed.' ); ?>
				<div role="group" aria-label="How to cover your visit" class="hxp-stack">
					<?php $hx_options( 'payment', [ 'ppo_review' => [ 'PPO health insurance', 'Upload your insurance card and we will review your benefits before scheduling.' ], 'card_hold' => [ 'Credit card', 'Hold your appointment with a card on file.' ] ] ); ?>
				</div>
				<span class="hxp-error" data-hxp-err="payment" hidden>Please choose one option to continue.</span>
				<div data-hxp-show="payPPO" hidden><?php $hx_upload( 'h10', 'Upload your insurance card (front and back)' ); ?></div>
				<div data-hxp-show="payCard" hidden><?php $hx_card_fields( 'h10' ); ?></div>
				<p class="hxp-small">Have other coverage? <a href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>">Ask Our Team a Question</a>.</p>
				<?php $hx_sample( 'Demo.', 'Insurance cards go to secure storage for benefits review only; the card hold is a Stripe Elements field. Nothing is uploaded or charged from this preview.' ); ?>
			</div>

			<!-- ===== Error state ===== -->
			<div role="alert" class="hxp-alert" data-hxp-alert hidden><p data-hxp-bind="errorText"></p></div>

			<!-- ===== Bottom navigation ===== -->
			<div class="hxp-nav" data-hxp-nav hidden>
				<button type="button" class="hxp-ghost" data-hxp-back hidden><svg aria-hidden="true" width="14" height="12" viewBox="0 0 14 12" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M13 6H1M6 1L1 6l5 5"></path></svg>Back</button>
				<span data-hxp-noback></span>
				<button type="button" class="hxp-continue" data-hxp-next aria-busy="false" hidden>Continue</button>
			</div>

			<!-- ===== A6: Thank you (Chronic) ===== -->
			<div class="hxp-step hxp-step--done" data-hxp-done="chronic" hidden>
				<div class="hxp-head hxp-head--done">
					<span class="hxp-eyebrow">Inquiry received</span>
					<h2 class="hxp-serif hxp-h2">Thank You for Taking This First Step</h2>
				</div>
				<div class="hxp-prose">
					<p>We know reaching out for help with your health is an important decision, and we are glad you have shared a little about yourself with us.</p>
					<p><strong>Your next step is a conversation with a Helixona Patient Advisor.</strong></p>
					<p>Our team will review the information you have provided, including your care goals, travel considerations, and insurance details. Then we will connect with you to discuss what our treatment approach involves, answer your questions, and explore whether Helixona is the right fit.</p>
					<p>There is no expectation that you have all the answers today. We want you to understand the journey before deciding whether to begin it.</p>
					<p>We look forward to getting to know you.</p>
				</div>
				<div class="hxp-next">
					<span class="hxp-label hxp-next__label">What happens next</span>
					<ol class="hxp-timeline">
						<?php
						$hx_tl = [ 'Inquiry received', 'Screening call with Patient Coordinator', 'Receive Chronic / Complex Onboarding link', 'Patient interview', 'Submit EOC / SPD documentation', 'Admissions Committee review' ];
						foreach ( $hx_tl as $hx_i => $hx_title ) :
							?>
							<li class="<?php echo 0 === $hx_i ? 'is-current' : ''; ?>">
								<span class="hxp-timeline__rail"><span aria-hidden="true" class="hxp-timeline__dot"></span><span aria-hidden="true" class="hxp-timeline__line"></span></span>
								<span class="hxp-timeline__body"><span class="hxp-timeline__title"><?php echo esc_html( $hx_title ); ?></span><span class="hxp-timeline__status"><?php echo 0 === $hx_i ? 'Completed today' : 'Upcoming'; ?></span></span>
							</li>
						<?php endforeach; ?>
					</ol>
				</div>
			</div>

			<!-- ===== Provider-guided: survey next ===== -->
			<div class="hxp-step hxp-step--done hxp-step--tight" data-hxp-done="provider" hidden>
				<span class="hxp-eyebrow">Request received</span>
				<h2 class="hxp-serif hxp-h2">Thank you, <span data-hxp-bind="first"></span>.</h2>
				<p class="hxp-lead hxp-measure">Before we can schedule your provider visit, please complete the new-patient survey. We have sent a link to your email.</p>
				<div class="hxp-rows hxp-rows--narrow">
					<?php $hx_row( 'Coverage', 'paymentLabel' ); ?>
					<div class="hxp-row"><span class="hxp-row__label">New-patient survey</span><span class="hxp-row__value">Link sent · pending</span></div>
					<div class="hxp-row"><span class="hxp-row__label">Provider visit</span><span class="hxp-row__value">Booked by our New Patient Coordinator after the survey</span></div>
					<?php $hx_row( "We'll reach you at", 'email' ); ?>
				</div>
			</div>

			<!-- ===== Booking confirmed ===== -->
			<div class="hxp-step hxp-step--done hxp-step--tight" data-hxp-done="booking" hidden>
				<span class="hxp-eyebrow">Booking confirmed</span>
				<h2 class="hxp-serif hxp-h2">You're booked, <span data-hxp-bind="first"></span>.</h2>
				<p class="hxp-lead hxp-measure">A confirmation with your appointment details, preparation instructions and a secure link to manage your booking has been sent to <span data-hxp-bind="email"></span> and by text message.</p>
				<div class="hxp-rows hxp-rows--narrow">
					<?php
					$hx_row( 'Selection', 'itemName' );
					$hx_row( 'Appointment', 'slotLabel' );
					$hx_row( 'Paid', 'itemPrice' );
					?>
					<div class="hxp-row"><span class="hxp-row__label">Location</span><span class="hxp-row__value">114 Pacifica, Suite 130, Irvine, CA 92618</span></div>
					<div class="hxp-row"><span class="hxp-row__label">Booking reference</span><span class="hxp-row__value" data-hxp-bind="reference">[REFERENCE]</span></div>
				</div>
			</div>

			<!-- Start over, on any confirmation -->
			<div class="hxp-restart" data-hxp-restart-wrap hidden>
				<button type="button" class="hxp-ghost" data-hxp-restart>Start a new inquiry</button>
			</div>

		</div>
	</section>

</main>
<?php
get_footer();
