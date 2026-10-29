<?php
/**
 * Template Name: Service Page
 * Template Post Type: page
 *
 * Service/therapy page driven by /services/{page-slug}.php (or the slug in the
 * page's "hx_service" custom field). Pages are created automatically for every
 * file in /services.
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$svc = function_exists( 'hx_service_get_data' ) ? hx_service_get_data() : null;

get_header();

if ( ! $svc ) :
	?>
	<main id="content" class="hx-job hx-svc">
		<div class="hx-job__container hx-center" style="padding:120px 24px;">
			<h1 class="hx-job__h2">Service not found</h1>
			<?php if ( current_user_can( 'edit_theme_options' ) ) : ?>
				<p class="hx-job__p">Add <code>services/<?php echo esc_html( get_post_field( 'post_name', get_queried_object_id() ) ); ?>.php</code> to the child theme, or set this page's <code>hx_service</code> custom field.</p>
			<?php endif; ?>
		</div>
	</main>
	<?php
	get_footer();
	return;
endif;

$hx_tick = '<span class="hx-tick" aria-hidden="true"></span>';
?>
<main id="content" class="hx-job hx-svc">

	<!-- Hero -->
	<section class="hx-job__hero" id="hx-hero" style="background-image:url('<?php echo esc_url( $svc['hero_image'] ); ?>');">
		<div class="hx-job__hero-inner">
			<?php if ( ! empty( $svc['eyebrow'] ) ) : ?>
				<p class="hx-job__eyebrow"><?php echo esc_html( $svc['eyebrow'] ); ?></p>
			<?php endif; ?>
			<h1 class="hx-job__title"><?php echo esc_html( $svc['title'] ); ?></h1>
			<p class="hx-job__lead"><?php echo esc_html( $svc['subtitle'] ); ?></p>
			<div class="hx-job__actions">
				<?php if ( ! empty( $svc['cta_primary'] ) ) : ?>
					<a class="hx-btn hx-btn--gold" href="<?php echo esc_url( $svc['cta_primary']['url'] ); ?>"><?php echo esc_html( $svc['cta_primary']['label'] ); ?></a>
				<?php endif; ?>
				<?php if ( ! empty( $svc['cta_secondary'] ) ) : ?>
					<a class="hx-btn hx-btn--ghost" href="<?php echo esc_url( $svc['cta_secondary']['url'] ); ?>"><?php echo esc_html( $svc['cta_secondary']['label'] ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<!-- Facts strip -->
	<?php if ( ! empty( $svc['facts'] ) ) : ?>
	<div class="hx-job__facts-wrap">
	<div class="hx-job__container">
		<ul class="hx-job__facts" aria-label="At a glance">
			<?php foreach ( $svc['facts'] as $hx_f ) : ?>
				<li><span class="hx-job__fact-label"><?php echo esc_html( $hx_f['label'] ); ?></span><span class="hx-job__fact-value"><?php echo esc_html( $hx_f['value'] ); ?></span></li>
			<?php endforeach; ?>
		</ul>
	</div>
	</div>
	<?php endif; ?>

	<!-- Intro + device -->
	<section class="hx-job__section" id="hx-intro">
		<div class="hx-job__container hx-svc__intro">
			<div class="hx-svc__intro-text">
				<span class="hx-divider hx-divider--left" aria-hidden="true"></span>
				<?php foreach ( $svc['intro'] as $hx_p ) : ?>
					<p class="hx-job__p"><?php echo wp_kses_post( $hx_p ); ?></p>
				<?php endforeach; ?>
			</div>
			<?php if ( ! empty( $svc['device_image'] ) ) : ?>
				<figure class="hx-svc__figure">
					<img src="<?php echo esc_url( $svc['device_image'] ); ?>" alt="<?php echo esc_attr( $svc['device_image_alt'] ); ?>" loading="lazy">
				</figure>
			<?php endif; ?>
		</div>
	</section>

	<!-- What is -->
	<section class="hx-job__section hx-job__section--cream" id="hx-what">
		<div class="hx-job__container hx-job__container--narrow hx-center">
			<span class="hx-divider" aria-hidden="true"></span>
			<h2 class="hx-job__h2"><?php echo esc_html( $svc['what_is']['heading'] ); ?></h2>
			<?php foreach ( $svc['what_is']['paragraphs'] as $hx_p ) : ?>
				<p class="hx-job__p"><?php echo wp_kses_post( $hx_p ); ?></p>
			<?php endforeach; ?>
		</div>
	</section>

	<!-- How it works -->
	<section class="hx-job__section" id="hx-how">
		<div class="hx-job__container">
			<header class="hx-job__section-head hx-job__section-head--wide">
				<span class="hx-divider" aria-hidden="true"></span>
				<h2 class="hx-job__h2"><?php echo esc_html( $svc['how']['heading'] ); ?></h2>
				<?php foreach ( $svc['how']['intro'] as $hx_p ) : ?>
					<p class="hx-job__section-sub"><?php echo wp_kses_post( $hx_p ); ?></p>
				<?php endforeach; ?>
			</header>
			<ul class="hx-job__traits hx-svc__functions">
				<?php foreach ( $svc['how']['items'] as $hx_it ) : ?>
					<li class="hx-job__trait">
						<span class="hx-tick" aria-hidden="true"></span>
						<h3 class="hx-job__h3"><?php echo esc_html( $hx_it['title'] ); ?></h3>
						<p><?php echo wp_kses_post( $hx_it['text'] ); ?></p>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php foreach ( $svc['how']['outro'] as $hx_p ) : ?>
				<p class="hx-job__p hx-center hx-svc__outro"><?php echo wp_kses_post( $hx_p ); ?></p>
			<?php endforeach; ?>
		</div>
	</section>

	<!-- What to expect -->
	<section class="hx-job__section hx-job__section--cream" id="hx-expect">
		<div class="hx-job__container hx-job__container--narrow">
			<span class="hx-divider hx-divider--left" aria-hidden="true"></span>
			<h2 class="hx-job__h2"><?php echo esc_html( $svc['expect']['heading'] ); ?></h2>
			<?php foreach ( $svc['expect']['paragraphs'] as $hx_p ) : ?>
				<p class="hx-job__p"><?php echo wp_kses_post( $hx_p ); ?></p>
			<?php endforeach; ?>
		</div>
	</section>

	<!-- Who can benefit -->
	<section class="hx-job__section hx-job__section--dark" id="hx-benefit">
		<div class="hx-job__container">
			<header class="hx-job__section-head hx-job__section-head--wide">
				<span class="hx-divider" aria-hidden="true"></span>
				<h2 class="hx-job__h2"><?php echo esc_html( $svc['benefit']['heading'] ); ?></h2>
				<p class="hx-job__section-sub"><?php echo wp_kses_post( $svc['benefit']['intro'] ); ?></p>
			</header>
			<div class="hx-svc__groups">
				<?php foreach ( $svc['benefit']['groups'] as $hx_g ) : ?>
					<div class="hx-svc__group">
						<h3 class="hx-job__h3"><?php echo esc_html( $hx_g['title'] ); ?></h3>
						<ul class="hx-job__checklist hx-svc__checklist">
							<?php foreach ( $hx_g['items'] as $hx_item ) : ?>
								<li><?php echo $hx_tick; // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo wp_kses_post( $hx_item ); ?></span></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endforeach; ?>
			</div>
			<div class="hx-svc__notes">
				<?php foreach ( $svc['benefit']['outro'] as $hx_p ) : ?>
					<p><?php echo wp_kses_post( $hx_p ); ?></p>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- Team -->
	<section class="hx-job__section" id="hx-team">
		<div class="hx-job__container">
			<header class="hx-job__section-head hx-job__section-head--wide">
				<span class="hx-divider" aria-hidden="true"></span>
				<h2 class="hx-job__h2"><?php echo esc_html( $svc['team']['heading'] ); ?></h2>
			</header>
			<div class="hx-job__container--narrow hx-svc__team-text">
				<?php foreach ( $svc['team']['paragraphs'] as $hx_p ) : ?>
					<p class="hx-job__p"><?php echo wp_kses_post( $hx_p ); ?></p>
				<?php endforeach; ?>
			</div>
			<div class="hx-svc__members">
				<?php foreach ( $svc['team']['members'] as $hx_m ) : ?>
					<article class="hx-svc__member">
						<h3 class="hx-job__h3"><?php echo esc_html( $hx_m['name'] ); ?></h3>
						<?php if ( ! empty( $hx_m['role'] ) ) : ?><p class="hx-svc__role"><?php echo esc_html( $hx_m['role'] ); ?></p><?php endif; ?>
						<p><?php echo wp_kses_post( $hx_m['bio'] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- Philosophy -->
	<section class="hx-job__section hx-job__section--cream" id="hx-philosophy">
		<div class="hx-job__container">
			<header class="hx-job__section-head hx-job__section-head--wide">
				<span class="hx-divider" aria-hidden="true"></span>
				<h2 class="hx-job__h2"><?php echo esc_html( $svc['philosophy']['heading'] ); ?></h2>
				<p class="hx-job__section-sub"><?php echo wp_kses_post( $svc['philosophy']['intro'] ); ?></p>
			</header>
			<ul class="hx-job__traits hx-svc__pillars">
				<?php foreach ( $svc['philosophy']['pillars'] as $hx_pl ) : ?>
					<li class="hx-job__trait">
						<span class="hx-tick" aria-hidden="true"></span>
						<h3 class="hx-job__h3"><?php echo esc_html( $hx_pl['title'] ); ?></h3>
						<?php if ( ! empty( $hx_pl['tag'] ) ) : ?><p class="hx-svc__tag"><?php echo esc_html( $hx_pl['tag'] ); ?></p><?php endif; ?>
						<p><?php echo wp_kses_post( $hx_pl['text'] ); ?></p>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php foreach ( $svc['philosophy']['outro'] as $hx_p ) : ?>
				<p class="hx-job__p hx-center hx-svc__outro"><?php echo wp_kses_post( $hx_p ); ?></p>
			<?php endforeach; ?>
		</div>
	</section>

	<!-- Closing CTA -->
	<section class="hx-job__section hx-job__section--apply" id="hx-apply" style="background-image:url('<?php echo esc_url( $svc['hero_image'] ); ?>');">
		<div class="hx-job__container hx-job__container--narrow hx-center">
			<span class="hx-divider" aria-hidden="true"></span>
			<h2 class="hx-job__h2"><?php echo esc_html( $svc['closing']['heading'] ); ?></h2>
			<p class="hx-job__p hx-svc__closing"><?php echo wp_kses_post( $svc['closing']['text'] ); ?></p>
			<div class="hx-job__actions hx-job__actions--center">
				<?php if ( ! empty( $svc['cta_primary'] ) ) : ?>
					<a class="hx-btn hx-btn--gold hx-btn--lg" href="<?php echo esc_url( $svc['cta_primary']['url'] ); ?>"><?php echo esc_html( $svc['cta_primary']['label'] ); ?></a>
				<?php endif; ?>
				<?php if ( ! empty( $svc['cta_secondary'] ) ) : ?>
					<a class="hx-btn hx-btn--ghost" href="<?php echo esc_url( $svc['cta_secondary']['url'] ); ?>"><?php echo esc_html( $svc['cta_secondary']['label'] ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</section>

</main>
<?php
get_footer();
