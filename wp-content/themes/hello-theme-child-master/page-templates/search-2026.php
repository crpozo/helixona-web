<?php
/**
 * Search results (Website 2026 §8). Loaded by template_include in
 * inc/website-2026.php while the Website 2026 switch is on.
 * 1) Best matches from search/keywords-2026.php, 2) pages and articles that
 * contain the words, 3) suggestions when nothing matches.
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$hx_q    = trim( (string) get_search_query( false ) );
$hx_best = $hx_q ? hx_search_best_matches( $hx_q ) : [];

$hx_results = [];
if ( $hx_q ) {
	$hx_skip  = array_filter( wp_list_pluck( $hx_best, 'id' ) );
	$hx_query = new WP_Query( [
		's'                   => $hx_q,
		'post_type'           => [ 'page', 'post' ],
		'post_status'         => 'publish',
		'posts_per_page'      => 12,
		'post__not_in'        => $hx_skip,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	] );
	$hx_results = $hx_query->posts;
}

$hx_suggest = [
	[ 'title' => 'Find Your Path', 'url' => '/find-your-path/', 'text' => 'Three ways to begin care at Helixona.' ],
	[ 'title' => 'Treatments & Technologies', 'url' => '/services-and-tech/', 'text' => 'Every therapy and diagnostic we offer.' ],
	[ 'title' => 'Conditions We Help', 'url' => '/conditions/', 'text' => 'Find your diagnosis or symptoms.' ],
	[ 'title' => 'Book a Therapy', 'url' => '/booking/', 'text' => 'Book eligible therapies online.' ],
];

get_header();
?>
<main id="content" class="hx-job hx-lp hx-search">

	<section class="hx-job__hero hx-lp__hero--plain hx-search__hero" id="hx-hero">
		<div class="hx-job__hero-inner">
			<p class="hx-job__eyebrow">Search</p>
			<h1 class="hx-job__title"><?php echo $hx_q ? esc_html( sprintf( '“%s”', $hx_q ) ) : 'How can we help?'; ?></h1>
			<form class="hx-search__form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<label class="screen-reader-text" for="hx-search-input">Search Helixona</label>
				<input id="hx-search-input" type="search" name="s" value="<?php echo esc_attr( $hx_q ); ?>" placeholder="Try “salt room”, “IV”, “brain fog”…">
				<button type="submit" class="hx-btn hx-btn--gold">Search</button>
			</form>
		</div>
	</section>

	<?php if ( $hx_best ) : ?>
		<section class="hx-job__section hx-lp__section" id="best-matches">
			<div class="hx-job__container">
				<header class="hx-job__section-head hx-job__section-head--wide">
					<span class="hx-divider" aria-hidden="true"></span>
					<h2 class="hx-job__h2">Best Matches</h2>
				</header>
				<ul class="hx-lp__tiles hx-lp__tiles--<?php echo (int) min( 3, count( $hx_best ) ); ?>">
					<?php foreach ( $hx_best as $hx_item ) : ?>
						<li class="hx-lp__tile">
							<a class="hx-lp__tile-link" href="<?php echo esc_url( $hx_item['url'] ); ?>">
								<span class="hx-lp__tile-title"><?php echo esc_html( $hx_item['title'] ); ?></span>
								<?php if ( $hx_item['text'] ) : ?><span class="hx-lp__tile-text"><?php echo esc_html( $hx_item['text'] ); ?></span><?php endif; ?>
								<span class="hx-lp__tile-more">View <span aria-hidden="true">&rarr;</span></span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( $hx_results ) : ?>
		<section class="hx-job__section hx-lp__section<?php echo $hx_best ? ' hx-job__section--cream' : ''; ?>" id="results">
			<div class="hx-job__container hx-job__container--narrow">
				<header class="hx-job__section-head hx-lp__head--left">
					<span class="hx-divider hx-divider--left" aria-hidden="true"></span>
					<h2 class="hx-job__h2"><?php echo $hx_best ? 'More Results' : 'Results'; ?></h2>
				</header>
				<ol class="hx-search__list">
					<?php foreach ( $hx_results as $hx_post ) : ?>
						<li>
							<a href="<?php echo esc_url( get_permalink( $hx_post ) ); ?>"><?php echo esc_html( get_the_title( $hx_post ) ); ?></a>
							<span class="hx-search__type"><?php echo 'post' === $hx_post->post_type ? 'Article' : 'Page'; ?></span>
							<p><?php echo esc_html( hx_search_summary( $hx_post ) ); ?></p>
						</li>
					<?php endforeach; ?>
				</ol>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( ! $hx_best && ! $hx_results ) : ?>
		<section class="hx-job__section hx-lp__section" id="suggestions">
			<div class="hx-job__container">
				<header class="hx-job__section-head hx-job__section-head--wide">
					<span class="hx-divider" aria-hidden="true"></span>
					<h2 class="hx-job__h2"><?php echo $hx_q ? 'We couldn’t find that' : 'Popular Destinations'; ?></h2>
					<?php if ( $hx_q ) : ?>
						<p class="hx-job__section-sub">Try a therapy name (“salt room”, “red light”), a symptom (“brain fog”) or a condition (“Lyme”, “MCAS”). Or start here:</p>
					<?php endif; ?>
				</header>
				<ul class="hx-lp__tiles hx-lp__tiles--4">
					<?php foreach ( $hx_suggest as $hx_item ) : ?>
						<li class="hx-lp__tile">
							<a class="hx-lp__tile-link" href="<?php echo esc_url( hx_landing_url( $hx_item['url'] ) ); ?>">
								<span class="hx-lp__tile-title"><?php echo esc_html( $hx_item['title'] ); ?></span>
								<span class="hx-lp__tile-text"><?php echo esc_html( $hx_item['text'] ); ?></span>
								<span class="hx-lp__tile-more">Explore <span aria-hidden="true">&rarr;</span></span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
				<div class="hx-job__actions hx-job__actions--center">
					<a class="hx-btn hx-btn--gold" href="tel:9492572644">Call (949) 257-2644</a>
				</div>
			</div>
		</section>
	<?php endif; ?>

</main>
<?php
get_footer();
