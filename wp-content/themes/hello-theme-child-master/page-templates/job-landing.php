<?php
/**
 * Template Name: Job Landing
 * Template Post Type: page
 *
 * Landing page for one job opening. Content comes from /jobs/{page-slug}.php
 * (or /jobs/{hx_job}.php when the page has an "hx_job" custom field).
 * The page itself is created automatically for every file in /jobs.
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$job = function_exists( 'hx_job_get_data' ) ? hx_job_get_data() : null;

get_header();

if ( ! $job ) :
	?>
	<main id="content" class="hx-job">
		<div class="hx-job__container hx-center" style="padding:120px 24px;">
			<h1 class="hx-job__h2">Job posting not found</h1>
			<?php if ( current_user_can( 'edit_theme_options' ) ) : ?>
				<p class="hx-job__p">Add <code>jobs/<?php echo esc_html( get_post_field( 'post_name', get_queried_object_id() ) ); ?>.php</code> to the child theme, or set this page's <code>hx_job</code> custom field to an existing job file.</p>
			<?php endif; ?>
		</div>
	</main>
	<?php
	get_footer();
	return;
endif;

$hx_slug   = $job['slug'];
$hx_check  = '<span class="hx-tick" aria-hidden="true"></span>';
$hx_state  = isset( $_GET['hx_apply'] ) ? sanitize_text_field( wp_unslash( $_GET['hx_apply'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
$hx_sent   = ( 'sent' === $hx_state );
$hx_error  = ( $hx_state && ! $hx_sent ) ? $hx_state : '';
$hx_max_mb = ! empty( $job['resume_max_mb'] ) ? (int) $job['resume_max_mb'] : 10;
?>
<main id="content" class="hx-job">

	<!-- Hero -->
	<section class="hx-job__hero" id="hx-hero" style="background-image:url('<?php echo esc_url( $job['hero_image'] ); ?>');">
		<div class="hx-job__hero-inner">
			<p class="hx-job__eyebrow">Careers &middot; <?php echo esc_html( $job['department'] ); ?> &middot; <?php echo esc_html( $job['location'] ); ?></p>
			<h1 class="hx-job__title"><?php echo esc_html( $job['title'] ); ?></h1>
			<p class="hx-job__lead"><?php echo esc_html( $job['summary'] ); ?></p>
			<?php if ( ! empty( $job['bonus'] ) ) : ?>
				<p class="hx-job__bonus"><?php echo esc_html( $job['bonus'] ); ?></p>
			<?php endif; ?>
			<div class="hx-job__actions">
				<a class="hx-btn hx-btn--gold" href="#hx-apply" data-hx-scroll>Apply Now</a>
				<a class="hx-btn hx-btn--ghost" href="#hx-role" data-hx-scroll>Explore the Role</a>
			</div>
		</div>
	</section>

	<!-- Facts strip -->
	<div class="hx-job__facts-wrap">
	<div class="hx-job__container">
		<ul class="hx-job__facts" aria-label="Position details">
			<li><span class="hx-job__fact-label">Department</span><span class="hx-job__fact-value"><?php echo esc_html( $job['department'] ); ?></span></li>
			<li><span class="hx-job__fact-label">Reports To</span><span class="hx-job__fact-value"><?php echo esc_html( $job['reports_to'] ); ?></span></li>
			<li><span class="hx-job__fact-label">Employment Type</span><span class="hx-job__fact-value"><?php echo esc_html( $job['employment_type'] ); ?></span></li>
			<li><span class="hx-job__fact-label">Pay</span><span class="hx-job__fact-value"><?php echo esc_html( $job['pay'] ); ?></span></li>
		</ul>
	</div>
	</div>

	<!-- About -->
	<section class="hx-job__section hx-job__section--cream" id="hx-about">
		<div class="hx-job__container hx-job__container--narrow hx-center">
			<span class="hx-divider" aria-hidden="true"></span>
			<h2 class="hx-job__h2">About Helixona</h2>
			<?php foreach ( $job['about'] as $hx_p ) : ?>
				<p class="hx-job__p"><?php echo wp_kses_post( $hx_p ); ?></p>
			<?php endforeach; ?>
			<?php if ( ! empty( $job['about_quote'] ) ) : ?>
				<blockquote class="hx-job__quote"><?php echo wp_kses_post( $job['about_quote'] ); ?></blockquote>
			<?php endif; ?>
		</div>
	</section>

	<!-- Opportunity -->
	<section class="hx-job__section" id="hx-role">
		<div class="hx-job__container hx-job__split">
			<div class="hx-job__split-main">
				<span class="hx-divider hx-divider--left" aria-hidden="true"></span>
				<h2 class="hx-job__h2">The Opportunity</h2>
				<?php foreach ( $job['opportunity'] as $hx_p ) : ?>
					<p class="hx-job__p"><?php echo wp_kses_post( $hx_p ); ?></p>
				<?php endforeach; ?>
			</div>
			<?php if ( ! empty( $job['opportunity_callout'] ) ) : $hx_c = $job['opportunity_callout']; ?>
				<aside class="hx-job__callout">
					<span class="material-symbols-outlined hx-job__callout-icon" aria-hidden="true"><?php echo esc_html( $hx_c['icon'] ); ?></span>
					<h3 class="hx-job__h3"><?php echo esc_html( $hx_c['title'] ); ?></h3>
					<p><?php echo wp_kses_post( $hx_c['text'] ); ?></p>
				</aside>
			<?php endif; ?>
		</div>
	</section>

	<!-- Responsibilities -->
	<section class="hx-job__section hx-job__section--cream" id="hx-responsibilities">
		<div class="hx-job__container">
			<header class="hx-job__section-head">
				<span class="hx-divider" aria-hidden="true"></span>
				<h2 class="hx-job__h2">What You Will Do</h2>
			</header>
			<ul class="hx-job__checklist hx-job__checklist--2col">
				<?php foreach ( $job['responsibilities'] as $hx_item ) : ?>
					<li><?php echo $hx_check; // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo wp_kses_post( $hx_item ); ?></span></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>

	<!-- Gallery (new suite & IV prep area) -->
	<?php if ( ! empty( $job['gallery'] ) ) : ?>
	<section class="hx-job__section" id="hx-gallery">
		<div class="hx-job__container">
			<header class="hx-job__section-head">
				<span class="hx-divider" aria-hidden="true"></span>
				<h2 class="hx-job__h2"><?php echo esc_html( $job['gallery_heading'] ?? 'Where You’ll Work' ); ?></h2>
				<?php if ( ! empty( $job['gallery_intro'] ) ) : ?><p class="hx-job__section-sub"><?php echo esc_html( $job['gallery_intro'] ); ?></p><?php endif; ?>
			</header>
			<div class="hx-job__gallery hx-job__gallery--<?php echo (int) min( 3, max( 1, count( $job['gallery'] ) ) ); ?>">
				<?php foreach ( $job['gallery'] as $hx_img ) : ?>
					<figure class="hx-job__photo">
						<img src="<?php echo esc_url( $hx_img['src'] ); ?>" alt="<?php echo esc_attr( $hx_img['alt'] ?? '' ); ?>" loading="lazy">
						<?php if ( ! empty( $hx_img['caption'] ) ) : ?><figcaption><?php echo esc_html( $hx_img['caption'] ); ?></figcaption><?php endif; ?>
					</figure>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<!-- Who will thrive -->
	<section class="hx-job__section hx-job__section--dark" id="hx-thrive">
		<div class="hx-job__container">
			<header class="hx-job__section-head">
				<span class="hx-divider" aria-hidden="true"></span>
				<h2 class="hx-job__h2">Who Will Thrive Here</h2>
				<?php if ( ! empty( $job['thrive_intro'] ) ) : ?>
					<p class="hx-job__section-sub"><?php echo esc_html( $job['thrive_intro'] ); ?></p>
				<?php endif; ?>
			</header>
			<ul class="hx-job__traits">
				<?php foreach ( $job['thrive'] as $hx_card ) : ?>
					<li class="hx-job__trait">
						<span class="hx-tick" aria-hidden="true"></span>
						<h3 class="hx-job__h3"><?php echo esc_html( $hx_card['title'] ); ?></h3>
						<p><?php echo wp_kses_post( $hx_card['text'] ); ?></p>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>

	<!-- Qualifications -->
	<section class="hx-job__section" id="hx-qualifications">
		<div class="hx-job__container">
			<header class="hx-job__section-head">
				<span class="hx-divider" aria-hidden="true"></span>
				<h2 class="hx-job__h2">Qualifications</h2>
			</header>
			<div class="hx-job__quals">
				<div class="hx-job__qual">
					<h3 class="hx-job__h3">Required</h3>
					<ul class="hx-job__checklist">
						<?php foreach ( $job['required'] as $hx_item ) : ?>
							<li><?php echo $hx_check; // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo wp_kses_post( $hx_item ); ?></span></li>
						<?php endforeach; ?>
					</ul>
				</div>
				<div class="hx-job__qual hx-job__qual--preferred">
					<h3 class="hx-job__h3">Preferred</h3>
					<ul class="hx-job__checklist">
						<?php foreach ( $job['preferred'] as $hx_item ) : ?>
							<li><?php echo $hx_check; // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo wp_kses_post( $hx_item ); ?></span></li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</div>
	</section>

	<!-- Benefits -->
	<section class="hx-job__section hx-job__section--cream" id="hx-benefits">
		<div class="hx-job__container">
			<header class="hx-job__section-head hx-job__section-head--wide">
				<span class="hx-divider" aria-hidden="true"></span>
				<h2 class="hx-job__h2">Why Join Helixona?</h2>
				<p class="hx-job__section-sub"><?php echo wp_kses_post( $job['why_join'] ); ?></p>
			</header>
			<div class="hx-job__benefits">
				<?php foreach ( $job['benefits'] as $hx_b ) : ?>
					<article class="hx-job__benefit">
						<span class="material-symbols-outlined hx-job__benefit-icon" aria-hidden="true"><?php echo esc_html( $hx_b['icon'] ); ?></span>
						<h3 class="hx-job__h3"><?php echo esc_html( $hx_b['title'] ); ?></h3>
						<p><?php echo wp_kses_post( $hx_b['text'] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- Apply -->
	<section class="hx-job__section hx-job__section--apply" id="hx-apply" style="background-image:url('<?php echo esc_url( $job['hero_image'] ); ?>');">
		<div class="hx-job__container hx-job__container--narrow hx-center">
			<span class="hx-divider" aria-hidden="true"></span>
			<h2 class="hx-job__h2">How to Apply</h2>
			<ol class="hx-job__steps">
				<?php foreach ( $job['apply_steps'] as $hx_step ) : ?>
					<li>
						<h3 class="hx-job__h3"><?php echo esc_html( $hx_step['title'] ); ?></h3>
						<p><?php echo wp_kses_post( $hx_step['text'] ); ?></p>
					</li>
				<?php endforeach; ?>
			</ol>

			<div class="hx-form" id="hx-form">
				<form class="hx-form__form" id="hx-apply-form" method="post" enctype="multipart/form-data" novalidate
					action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
					data-ajax="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>"
					data-max-mb="<?php echo esc_attr( $hx_max_mb ); ?>"
					<?php echo $hx_sent ? 'hidden' : ''; ?>>
					<input type="hidden" name="action" value="hx_job_apply">
					<input type="hidden" name="hx_job" value="<?php echo esc_attr( $hx_slug ); ?>">
					<input type="hidden" name="hx_token" value="<?php echo esc_attr( hx_job_token( $hx_slug ) ); ?>">
					<p class="hx-form__hp" aria-hidden="true">
						<label>Website <input type="text" name="hx_website" tabindex="-1" autocomplete="off"></label>
					</p>

					<div class="hx-form__grid">
						<label class="hx-field">
							<span class="hx-field__label">Full name <b>*</b></span>
							<input type="text" name="hx_name" required autocomplete="name" maxlength="120">
						</label>
						<label class="hx-field">
							<span class="hx-field__label">Email <b>*</b></span>
							<input type="email" name="hx_email" required autocomplete="email">
						</label>
						<label class="hx-field">
							<span class="hx-field__label">Phone</span>
							<input type="tel" name="hx_phone" autocomplete="tel">
						</label>
						<label class="hx-field">
							<span class="hx-field__label">Position</span>
							<input type="text" value="<?php echo esc_attr( $job['title'] ); ?>" readonly tabindex="-1">
						</label>
						<label class="hx-field hx-field--full">
							<span class="hx-field__label">Resume <b>*</b> <small>PDF or Word &middot; up to <?php echo esc_html( $hx_max_mb ); ?> MB</small></span>
							<input type="file" name="hx_resume" required accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
						</label>
						<label class="hx-field hx-field--full">
							<span class="hx-field__label">Message</span>
							<textarea name="hx_message" rows="5" maxlength="5000" placeholder="Tell us why this role interests you (optional)"></textarea>
						</label>
					</div>

					<p class="hx-form__alert" role="alert" <?php echo $hx_error ? '' : 'hidden'; ?>><?php echo esc_html( $hx_error ); ?></p>

					<button type="submit" class="hx-btn hx-btn--gold hx-btn--lg">Submit application</button>
					<p class="hx-form__privacy">By submitting, you agree that Helixona may contact you about this position.</p>
				</form>

				<div class="hx-form__success" role="status" <?php echo $hx_sent ? '' : 'hidden'; ?>>
					<span class="hx-tick" aria-hidden="true"></span>
					<h3 class="hx-job__h3">Application received</h3>
					<p>Thank you for applying for the <?php echo esc_html( $job['title'] ); ?> position. If your qualifications align with what we&rsquo;re looking for, a member of our team will contact you to discuss the next steps.</p>
				</div>
			</div>

			<p class="hx-job__all"><a href="<?php echo esc_url( $job['all_positions_url'] ); ?>">View all open positions</a></p>
			<p class="hx-job__eeo"><?php echo esc_html( $job['eeo'] ); ?></p>
		</div>
	</section>

	<!-- Sticky apply bar (shown after the hero scrolls away) -->
	<div class="hx-job__sticky" id="hx-sticky" hidden>
		<div class="hx-job__sticky-inner">
			<span class="hx-job__sticky-text"><strong><?php echo esc_html( $job['title'] ); ?></strong> &middot; <?php echo esc_html( $job['location'] ); ?> &middot; <?php echo esc_html( $job['employment_type'] ); ?><?php if ( ! empty( $job['bonus'] ) ) : ?> &middot; <em><?php echo esc_html( $job['bonus'] ); ?></em><?php endif; ?></span>
			<a class="hx-btn hx-btn--gold hx-btn--sm" href="#hx-apply" data-hx-scroll>Apply Now</a>
		</div>
	</div>

</main>
<?php
get_footer();
