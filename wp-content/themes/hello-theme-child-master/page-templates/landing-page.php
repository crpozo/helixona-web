<?php
/**
 * Template Name: Landing Page
 * Template Post Type: page
 *
 * Website 2026 landing (care paths, Health Optimization, Bundles, Access Pass…)
 * driven by /landings/{page-slug}.php (or the slug in the page's "hx_landing"
 * custom field). A landing is a hero plus a list of sections; any copy value can
 * be a placeholder ( [ 'slot' => 'what goes here' ] ) until copy is approved.
 *
 * Section types: tiles · people · text · list · table · media · cta
 * Hero: optional 'hero_video' (MP4, plays muted in a loop; 'hero_image' is its poster).
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$lp = function_exists( 'hx_landing_get_data' ) ? hx_landing_get_data() : null;

get_header();

if ( ! $lp ) :
	?>
	<main id="content" class="hx-job hx-lp">
		<div class="hx-job__container hx-center" style="padding:120px 24px;">
			<h1 class="hx-job__h2">Page not found</h1>
			<?php if ( current_user_can( 'edit_theme_options' ) ) : ?>
				<p class="hx-job__p">Add <code>landings/<?php echo esc_html( get_post_field( 'post_name', get_queried_object_id() ) ); ?>.php</code> to the child theme, or set this page's <code>hx_landing</code> custom field.</p>
			<?php endif; ?>
		</div>
	</main>
	<?php
	get_footer();
	return;
endif;

$hx_tick = '<span class="hx-tick" aria-hidden="true"></span>';
?>
<main id="content" class="hx-job hx-lp">

	<!-- Hero -->
	<section class="hx-job__hero<?php echo empty( $lp['hero_image'] ) ? ' hx-lp__hero--plain' : ''; ?><?php echo ! empty( $lp['hero_video'] ) ? ' hx-lp__hero--video' : ''; ?>" id="hx-hero"<?php if ( ! empty( $lp['hero_image'] ) ) : ?> style="background-image:url('<?php echo esc_url( $lp['hero_image'] ); ?>');"<?php endif; ?>>
		<?php if ( ! empty( $lp['hero_video'] ) ) : ?>
			<video class="hx-lp__hero-video" autoplay muted loop playsinline preload="metadata" aria-hidden="true"<?php if ( ! empty( $lp['hero_image'] ) ) : ?> poster="<?php echo esc_url( $lp['hero_image'] ); ?>"<?php endif; ?>>
				<source src="<?php echo esc_url( $lp['hero_video'] ); ?>" type="video/mp4">
			</video>
		<?php endif; ?>
		<div class="hx-job__hero-inner">
			<?php if ( ! empty( $lp['eyebrow'] ) ) : ?>
				<p class="hx-job__eyebrow"><?php echo esc_html( $lp['eyebrow'] ); ?></p>
			<?php endif; ?>
			<h1 class="hx-job__title"><?php echo esc_html( $lp['title'] ); ?></h1>
			<?php echo hx_landing_text( $lp['lead'] ?? '', 'p', 'hx-job__lead' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php echo hx_landing_buttons( $lp['ctas'] ?? [] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
	</section>

	<?php
	$hx_alt = 0;
	foreach ( (array) ( $lp['sections'] ?? [] ) as $hx_s ) :
		$hx_type = $hx_s['type'] ?? 'text';
		// Media sections with only placeholders are for editors; visitors skip them entirely.
		if ( 'media' === $hx_type && ! current_user_can( 'edit_pages' ) && ! array_filter( array_column( $hx_s['items'], 'src' ) ) ) {
			continue;
		}
		$hx_variant = $hx_s['variant'] ?? ( $hx_alt++ % 2 ? 'cream' : '' );
		$hx_classes = 'hx-job__section hx-lp__section hx-lp__section--' . $hx_type . ( $hx_variant ? ' hx-job__section--' . $hx_variant : '' );
		$hx_center  = ! empty( $hx_s['center'] ) || in_array( $hx_type, [ 'tiles', 'cta', 'media', 'people' ], true );
		?>
		<section class="<?php echo esc_attr( $hx_classes ); ?>"<?php if ( ! empty( $hx_s['id'] ) ) : ?> id="<?php echo esc_attr( $hx_s['id'] ); ?>"<?php endif; ?>>
			<div class="hx-job__container<?php echo in_array( $hx_type, [ 'text', 'cta' ], true ) ? ' hx-job__container--narrow' : ''; ?>">

				<?php if ( ! empty( $hx_s['heading'] ) || ! empty( $hx_s['intro'] ) ) : ?>
					<header class="hx-job__section-head hx-job__section-head--wide<?php echo $hx_center ? '' : ' hx-lp__head--left'; ?>">
						<span class="hx-divider<?php echo $hx_center ? '' : ' hx-divider--left'; ?>" aria-hidden="true"></span>
						<?php if ( ! empty( $hx_s['kicker'] ) ) : ?><p class="hx-lp__kicker"><?php echo esc_html( $hx_s['kicker'] ); ?></p><?php endif; ?>
						<?php if ( ! empty( $hx_s['heading'] ) ) : ?><h2 class="hx-job__h2"><?php echo esc_html( $hx_s['heading'] ); ?></h2><?php endif; ?>
						<?php foreach ( hx_landing_list( $hx_s['intro'] ?? [] ) as $hx_p ) : ?>
							<?php echo hx_landing_text( $hx_p, 'p', 'hx-job__section-sub' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<?php endforeach; ?>
					</header>
				<?php endif; ?>

				<?php if ( 'tiles' === $hx_type ) : ?>
					<ul class="hx-lp__tiles hx-lp__tiles--<?php echo (int) ( $hx_s['cols'] ?? count( $hx_s['items'] ) ); ?>">
						<?php foreach ( $hx_s['items'] as $hx_t ) : ?>
							<?php $hx_link = ! empty( $hx_t['url'] ); // no url = destination not built yet: plain tile, "Coming soon" ?>
							<li class="hx-lp__tile">
								<<?php echo $hx_link ? 'a' : 'div'; ?> class="hx-lp__tile-link<?php echo $hx_link ? '' : ' hx-lp__tile-link--soon'; ?>"<?php if ( $hx_link ) : ?> href="<?php echo esc_url( hx_landing_url( $hx_t['url'] ) ); ?>"<?php endif; ?>>
									<?php if ( ! empty( $hx_t['question'] ) ) : ?><span class="hx-lp__tile-q"><?php echo esc_html( $hx_t['question'] ); ?></span><?php endif; ?>
									<span class="hx-lp__tile-title"><?php echo esc_html( $hx_t['title'] ); ?></span>
									<?php echo hx_landing_text( $hx_t['text'] ?? '', 'span', 'hx-lp__tile-text' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
									<?php if ( $hx_link ) : ?>
										<span class="hx-lp__tile-more"><?php echo esc_html( $hx_t['label'] ?? 'Explore' ); ?> <span aria-hidden="true">&rarr;</span></span>
									<?php else : ?>
										<span class="hx-lp__tile-more">Coming soon</span>
									<?php endif; ?>
								</<?php echo $hx_link ? 'a' : 'div'; ?>>
							</li>
						<?php endforeach; ?>
					</ul>

				<?php elseif ( 'people' === $hx_type ) : ?>
					<ul class="hx-lp__people">
						<?php foreach ( $hx_s['items'] as $hx_m ) : ?>
							<li class="hx-lp__person">
								<figure class="hx-lp__portrait">
									<?php if ( ! empty( $hx_m['image'] ) ) : ?>
										<img src="<?php echo esc_url( $hx_m['image'] ); ?>" alt="<?php echo esc_attr( $hx_m['alt'] ?? $hx_m['name'] ); ?>" loading="lazy">
									<?php else : ?>
										<?php echo hx_landing_slot( 'Authentic portrait photo' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
									<?php endif; ?>
								</figure>
								<h3 class="hx-job__h3 hx-lp__person-name"><?php echo esc_html( $hx_m['name'] ); ?></h3>
								<?php if ( ! empty( $hx_m['role'] ) ) : ?><p class="hx-lp__person-role"><?php echo esc_html( $hx_m['role'] ); ?></p><?php endif; ?>
								<?php echo hx_landing_text( $hx_m['text'] ?? '', 'p', 'hx-lp__person-text' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
								<?php if ( ! empty( $hx_m['url'] ) ) : ?>
									<a class="hx-lp__tile-more" href="<?php echo esc_url( hx_landing_url( $hx_m['url'] ) ); ?>"><?php echo esc_html( $hx_m['label'] ?? 'Read biography' ); ?> <span aria-hidden="true">&rarr;</span></a>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>

				<?php elseif ( 'list' === $hx_type ) : ?>
					<ul class="hx-job__checklist<?php echo ( count( $hx_s['items'] ) > 4 ) ? ' hx-job__checklist--2col' : ''; ?>">
						<?php foreach ( $hx_s['items'] as $hx_item ) : ?>
							<?php if ( is_array( $hx_item ) && isset( $hx_item['slot'] ) ) : ?>
								<li class="hx-lp__slot-li"><?php echo hx_landing_text( $hx_item ); // phpcs:ignore WordPress.Security.EscapeOutput ?></li>
							<?php else : ?>
								<li><?php echo $hx_tick; // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo wp_kses_post( $hx_item ); ?></span></li>
							<?php endif; ?>
						<?php endforeach; ?>
					</ul>

				<?php elseif ( 'table' === $hx_type ) : ?>
					<?php if ( ! empty( $hx_s['rows'] ) && ! isset( $hx_s['rows']['slot'] ) ) : ?>
						<div class="hx-lp__table-wrap">
							<table class="hx-lp__table">
								<thead><tr><?php foreach ( $hx_s['columns'] as $hx_c ) : ?><th scope="col"><?php echo esc_html( $hx_c ); ?></th><?php endforeach; ?></tr></thead>
								<tbody>
									<?php foreach ( $hx_s['rows'] as $hx_r ) : ?>
										<tr><?php foreach ( $hx_r as $hx_cell ) : ?><td><?php echo wp_kses_post( $hx_cell ); ?></td><?php endforeach; ?></tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					<?php else : ?>
						<?php echo hx_landing_text( $hx_s['rows'] ?? [ 'slot' => 'Table' ] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php endif; ?>

				<?php elseif ( 'media' === $hx_type ) : ?>
					<div class="hx-lp__media hx-lp__media--<?php echo (int) count( $hx_s['items'] ); ?>">
						<?php foreach ( $hx_s['items'] as $hx_m ) : ?>
							<?php if ( ! empty( $hx_m['src'] ) ) : ?>
								<figure class="hx-job__photo"><img src="<?php echo esc_url( $hx_m['src'] ); ?>" alt="<?php echo esc_attr( $hx_m['alt'] ?? '' ); ?>" loading="lazy"></figure>
							<?php elseif ( current_user_can( 'edit_pages' ) ) : ?>
								<div class="hx-slot hx-slot--media"><span class="hx-slot__tag"><?php echo 'video' === ( $hx_m['kind'] ?? 'photo' ) ? 'Video slot' : 'Photo slot'; ?></span><?php echo esc_html( $hx_m['slot'] ?? '' ); ?></div>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<?php foreach ( hx_landing_list( $hx_s['paragraphs'] ?? [] ) as $hx_p ) : ?>
					<?php echo hx_landing_text( $hx_p ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php endforeach; ?>

				<?php if ( ! empty( $hx_s['note'] ) ) : ?>
					<?php echo hx_landing_text( $hx_s['note'], 'p', 'hx-lp__note' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php endif; ?>

				<?php echo hx_landing_buttons( $hx_s['ctas'] ?? [], $hx_center ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
		</section>
	<?php endforeach; ?>

</main>
<?php
get_footer();
