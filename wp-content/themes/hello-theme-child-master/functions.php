<?php
/**
 * Theme functions and definitions.
 *
 * For additional information on potential customization options,
 * read the developers' documentation:
 *
 * https://developers.elementor.com/docs/hello-elementor-theme/
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'HELLO_ELEMENTOR_CHILD_VERSION', '2.1.0' );

/**
 * Cache-busting version for a child-theme asset (file mtime), so every upload invalidates browser/CDN caches.
 */
function hx_asset_ver( $relative ) {
	$file = get_stylesheet_directory() . '/' . ltrim( $relative, '/' );
	return file_exists( $file ) ? (string) filemtime( $file ) : HELLO_ELEMENTOR_CHILD_VERSION;
}

/**
 * Load child theme scripts & styles.
 *
 * @return void
 */
function hello_elementor_child_scripts_styles() {

	wp_enqueue_style(
		'hello-elementor-child-style',
		get_stylesheet_directory_uri() . '/style.css',
		[
			'hello-elementor-theme-style',
		],
		hx_asset_ver( 'style.css' )
	);

}
add_action( 'wp_enqueue_scripts', 'hello_elementor_child_scripts_styles', 20 );

/**
 * Navigation polish (mobile drawer rebuild + desktop dropdown styling), site-wide.
 */
function hx_menu_assets() {
	wp_enqueue_style(
		'hx-menu',
		get_stylesheet_directory_uri() . '/assets/css/hx-menu.css',
		[ 'hello-elementor-child-style' ],
		hx_asset_ver( 'assets/css/hx-menu.css' )
	);
	wp_enqueue_script(
		'hx-menu',
		get_stylesheet_directory_uri() . '/assets/js/hx-menu.js',
		[],
		hx_asset_ver( 'assets/js/hx-menu.js' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'hx_menu_assets', 21 );

// FontAwesome
function load_font_awesome() {
    // Usamos la versión 6.5.2 que es súper estable
    wp_enqueue_style('font-awesome-6', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css', array(), '6.5.2');
}
add_action('wp_enqueue_scripts', 'load_font_awesome');

// JOB OPENINGS
add_action('wp_footer', function () {
    ?>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
      const select = document.querySelector("#form-field-job_list");
      const hidden = document.querySelector("#form-field-job_list_value");

      if (!select || !hidden) return;

      // Reset options
      select.innerHTML = "";
      const ph = document.createElement("option");
      ph.value = "";
      ph.textContent = "Select a position";
      ph.disabled = true;
      ph.selected = true;
      select.appendChild(ph);

      // Add job openings dynamically
      const jobs = <?php
        $posts = get_posts([
          'post_type'   => 'job-openings',
          'post_status' => 'publish',
          'numberposts' => -1,
          'orderby'     => 'title',
          'order'       => 'ASC'
        ]);
        $out = [];
        foreach ($posts as $p) {
            $out[] = esc_js(get_the_title($p));
        }
        echo json_encode($out);
      ?>;

      jobs.forEach(title => {
        const opt = document.createElement("option");
        opt.value = title;
        opt.textContent = title;
        select.appendChild(opt);
      });

      // Pre-select the position when arriving from a job landing (?position=...)
      const wanted = new URLSearchParams(window.location.search).get("position");
      if (wanted && jobs.includes(wanted)) {
        select.value = wanted;
        hidden.value = wanted;
      }

      // Mirror selection into hidden field
      select.addEventListener("change", function () {
        hidden.value = this.value;
      });

      // Ensure select is required
      select.setAttribute("required", "required");
    });
    </script>
    <?php
});


// ACCORDION FOR CAREERS
add_shortcode('job_openings_accordion', function() {
    $posts = get_posts([
        'post_type'      => 'job-openings',
        'post_status'    => 'publish',
        'numberposts'    => -1,
        'orderby'        => 'date',
        'order'          => 'ASC'
    ]);

    // Positions published as job landings (/jobs/*.php) are listed too, linking to their page.
    $landing_jobs = [];
    if (function_exists('hx_job_get_jobs')) {
        $listed = [];
        foreach ($posts as $post) {
            $listed[] = $post->post_name;
            $listed[] = sanitize_title($post->post_title);
        }
        foreach (hx_job_get_jobs() as $slug => $job) {
            $page = get_page_by_path($slug, OBJECT, 'page');
            if (!$page || 'publish' !== $page->post_status) continue;
            if (in_array($slug, $listed, true) || in_array(sanitize_title($job['title']), $listed, true)) continue;
            $job['url']     = get_permalink($page);
            $landing_jobs[] = $job;
        }
    }

    if (!$posts && !$landing_jobs) return '<p>No job openings available.</p>';

    ob_start();
    ?>
    <div class="job-accordion">
        <?php foreach ($landing_jobs as $job): ?>
            <div class="job-item" style="border-bottom:1px solid var(--e-global-color-secondary); padding:10px 0;">
                <div class="job-header" style="cursor:pointer; display:flex; justify-content:space-between; align-items:center;">
						<div>
							<h3 class="job-title" style="margin:0;"><?php echo esc_html($job['title']); ?></h3>
							<p class="job-department" style="margin:5px 0;"><?php echo esc_html($job['location'] . ' · ' . $job['employment_type'] . (!empty($job['bonus']) ? ' · ' . $job['bonus'] : '')); ?></p>
							<span class="job-toggle-indicator" style="font-size:14px; opacity:0.7;">
								Open for more info
							</span>
						</div>
					</div>
				<div class="job-content" style="max-height:0; overflow:hidden; transition:max-height 0.4s ease; margin:0;">
                    <p style="margin:10px 0 0;"><?php echo esc_html($job['summary']); ?></p>
                    <p style="margin:10px 0 0;"><strong>Department:</strong> <?php echo esc_html($job['department']); ?> &middot; <strong>Pay:</strong> <?php echo esc_html($job['pay']); ?></p>
                    <p style="margin:10px 0 0;"><a href="<?php echo esc_url($job['url']); ?>">View full job description &amp; apply &rarr;</a></p>
                </div>
            </div>
        <?php endforeach; ?>
        <?php foreach ($posts as $i => $post): ?>
            <div class="job-item" style="border-bottom:1px solid var(--e-global-color-secondary); padding:10px 0;">
                <div class="job-header" style="cursor:pointer; display:flex; justify-content:space-between; align-items:center;">
						<div>
							<h3 class="job-title" style="margin:0;"><?php echo esc_html($post->post_title); ?></h3>
							<p class="job-department" style="margin:5px 0;"><?php echo esc_html(get_the_excerpt($post)); ?></p>
							<span class="job-toggle-indicator" style="font-size:14px; opacity:0.7;">
								Open for more info
							</span>
						</div>
					</div>
				<div class="job-content" style="max-height:0; overflow:hidden; transition:max-height 0.4s ease; margin:0;">
                    <p style="margin:10px 0 0;"><?php echo wp_kses_post($post->post_content); ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const items = document.querySelectorAll(".job-accordion .job-item");

        items.forEach((item, idx) => {
            const header = item.querySelector(".job-header");
            const content = item.querySelector(".job-content");

            header.addEventListener("click", function() {
                items.forEach((other, j) => {
                    const otherContent = other.querySelector(".job-content");
                    if (j === idx) {
                        if (otherContent.style.maxHeight === "0px" || !otherContent.style.maxHeight) {
                            otherContent.style.maxHeight = otherContent.scrollHeight + "px";
                        } else {
                            otherContent.style.maxHeight = "0px";
                        }
                    } else {
                        otherContent.style.maxHeight = "0px";
                    }
                });
            });
        });
    });
    </script>
    <?php
    return ob_get_clean();
});

/* Popup Entire Site User Role */

function mft_custom_popup_html() {
    // Job landings are for candidates, not patients: skip the popup there.
    if ( function_exists( 'hx_job_is_landing' ) && hx_job_is_landing() ) {
        return;
    }
    ?>
    <style>
        /* Overlay a pantalla completa */
        .mft-popup-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.9);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 999999;
        }

        .mft-popup-overlay.is-visible {
            display: flex;
        }

        /* Caja central */
        .mft-popup-box {
            background: rgba(0,0,0,0.95);
            border: 1px solid #f1d6a0;
            padding: 40px 32px;
            max-width: 900px;
            width: 90%;
            position: relative;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 24px;
        }

        .mft-popup-close {
            position: absolute;
            top: 12px;
            right: 16px;
            cursor: pointer;
            font-size: 24px;
            line-height: 1;
            color: #f1d6a0;
            background: transparent;
            border: none;
            transition: opacity 0.2s;
        }

        .mft-popup-close:hover {
            opacity: 0.7;
        }

        .mft-popup-title {
            font-size: 26px;
            letter-spacing: 0.3em;
            text-transform: uppercase;
            color: #f1d6a0;
            margin: 0 0 8px;
        }

        .mft-popup-subtitle {
            font-size: 13px;
            color: #f1d6a0;
            opacity: 0.8;
            margin: 0;
        }

        .mft-popup-buttons {
            margin-top: 16px;
            display: flex;
            flex-wrap: wrap;
            gap: 24px;
            justify-content: center;
            width: 100%;
        }

        .mft-popup-button-link {
            text-decoration: none !important;
            display: inline-block;
        }

        .mft-popup-button {
            min-width: 240px;
            padding: 18px 40px;
            border-radius: 0;
            border: 2px solid #f1d6a0 !important;
            background: rgba(241, 214, 160, 0.1) !important;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            letter-spacing: 0.45em;
            text-transform: uppercase;
            color: #f1d6a0 !important;
            text-align: center;
            white-space: nowrap;
            display: block !important;
            transition: all 0.3s ease;
            box-shadow: 0 0 10px rgba(241, 214, 160, 0.1);
        }

        .mft-popup-button:hover,
        .mft-popup-button:focus {
            outline: none !important;
            background: #f1d6a0 !important;
            color: #000000 !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(241, 214, 160, 0.3);
        }

        @media (max-width: 600px) {
            .mft-popup-box {
                padding: 32px 20px;
            }

            .mft-popup-buttons {
                flex-direction: column;
                gap: 16px;
            }

            .mft-popup-button {
                width: 100%;
                letter-spacing: 0.25em;
            }
        }
    </style>

    <div id="mft-popup" class="mft-popup-overlay" aria-hidden="true">
        <div class="mft-popup-box" role="dialog" aria-modal="true" aria-labelledby="mft-popup-title">
            <button type="button" class="mft-popup-close" aria-label="Close popup">&times;</button>

            <h2 id="mft-popup-title" class="mft-popup-title">
                HOW CAN WE HELP YOU TODAY?
            </h2>
            <p class="mft-popup-subtitle">
                Please choose the option that best describes you:
            </p>

            <div class="mft-popup-buttons">
                <a href="#" class="mft-popup-button-link" data-option="chronic_illness">
                    <span class="mft-popup-button">CHRONIC ILLNESS</span>
                </a>

                <a href="#" class="mft-popup-button-link" data-option="referring_doctor">
                    <span class="mft-popup-button">REFERRING DOCTOR</span>
                </a>

                <a href="#" class="mft-popup-button-link" data-option="wellness_patient">
                    <span class="mft-popup-button">WELLNESS PATIENT</span>
                </a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var STORAGE_KEY = 'mft_popup_shown';
            var CHOICE_KEY  = 'mft_popup_choice';

            // Verificar si ya se mostró
            if (window.localStorage && localStorage.getItem(STORAGE_KEY)) {
                return;
            }

            var popup = document.getElementById('mft-popup');
            if (!popup) return;

            // Mostrar popup después de 500ms
            setTimeout(function() {
                popup.classList.add('is-visible');
                popup.setAttribute('aria-hidden', 'false');
            }, 500);

            function closePopup() {
                popup.classList.remove('is-visible');
                popup.setAttribute('aria-hidden', 'true');
            }

            // Cerrar con X
            var closeBtn = popup.querySelector('.mft-popup-close');
            if (closeBtn) {
                closeBtn.addEventListener('click', function() {
                    closePopup();
                    if (window.localStorage) {
                        localStorage.setItem(STORAGE_KEY, 'yes');
                    }
                });
            }

            // Cerrar al hacer clic en el overlay
            popup.addEventListener('click', function(e) {
                if (e.target === popup) {
                    closePopup();
                    if (window.localStorage) {
                        localStorage.setItem(STORAGE_KEY, 'yes');
                    }
                }
            });

            // Cerrar y guardar opción al hacer clic en un botón
            var optionLinks = popup.querySelectorAll('.mft-popup-button-link');
            optionLinks.forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.preventDefault(); // evitar salto al top por el #
                    var option = link.getAttribute('data-option');

                    if (window.localStorage) {
                        localStorage.setItem(STORAGE_KEY, 'yes');
                        if (option) {
                            localStorage.setItem(CHOICE_KEY, option);
                        }
                    }

                    closePopup();
                });
            });
        });
    </script>
    <?php
}
// Popup disabled (Oct 2026). Re-enable with: add_action('wp_footer', 'mft_custom_popup_html');

// SERVICE CARD SEARCH & FILTER FUNCTIONALITY
add_action('wp_footer', function () {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('service-search');
        const servicesGrid = document.querySelector('.mft-services-grid');
        const serviceCards = servicesGrid ? servicesGrid.querySelectorAll('.mft-service-card') : [];
        const filterButtons = document.querySelectorAll('.mft-filters .filter-btn');
        const clearButton = document.getElementById('search-clear-btn');

        if (!searchInput || serviceCards.length === 0) return;

        // --- Search Functionality ---
        searchInput.addEventListener('keyup', function() {
            const searchTerm = searchInput.value.toLowerCase().trim();

            // Toggle clear button visibility
            clearButton.style.display = (searchTerm.length > 0) ? 'inline-block' : 'none';

            serviceCards.forEach(card => {
                // Get the text from the title and all paragraphs
                const title = card.querySelector('h3') ? card.querySelector('h3').textContent.toLowerCase() : '';
                const paragraphs = card.querySelectorAll('p');
                let cardText = title;
                paragraphs.forEach(p => {
                    cardText += ' ' + p.textContent.toLowerCase();
                });

                // Check if the card matches the search term
                if (cardText.includes(searchTerm)) {
                    // Use 'flex' for display style as per your card structure
                    card.style.display = 'flex'; 
                } else {
                    card.style.display = 'none'; 
                }
            });

            // Deactivate filters when searching
            filterButtons.forEach(btn => btn.classList.remove('active'));
        });

        // --- Clear Button Functionality ---
        if (clearButton) {
            clearButton.addEventListener('click', function() {
                searchInput.value = '';
                clearButton.style.display = 'none';
                
                // Show all cards again
                serviceCards.forEach(card => {
                    card.style.display = 'flex';
                });

                // Reactivate 'All' filter
                const allButton = document.querySelector('.filter-btn[data-filter="all"]');
                if (allButton) {
                    allButton.classList.add('active');
                }
            });
        }

        // --- Filter Functionality (Adjusted for search integration) ---
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Clear search bar when filtering
                searchInput.value = '';
                clearButton.style.display = 'none';
                
                // Standard filter logic
                filterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');

                const filter = this.getAttribute('data-filter');

                serviceCards.forEach(card => {
                    const categories = card.getAttribute('data-category');
                    
                    if (filter === 'all' || categories.includes(filter)) {
                        card.style.display = 'flex';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    });
    </script>
    <?php
});


// FAQ Accordion Script
function helixona_faq_accordion_script() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.faq-question').forEach(button => {
            button.addEventListener('click', () => {
                const faqItem = button.parentElement;
                const isActive = faqItem.classList.contains('active');
                
                document.querySelectorAll('.faq-item').forEach(item => {
                    item.classList.remove('active');
                });
                
                if (!isActive) {
                    faqItem.classList.add('active');
                }
            });
        });
    });
    </script>
    <?php
}
add_action('wp_footer', 'helixona_faq_accordion_script');

// Timeline

function timeline_animation_script() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const targets = document.querySelectorAll(".timeline ol li");
        const ANIMATED_CLASS = "in-view";
        
        // El primer item aparece inmediatamente
        if (targets.length > 0) {
            targets[0].classList.add(ANIMATED_CLASS);
        }
        
        // Los demás items tienen animación con scroll
        function callback(entries, observer) {
            entries.forEach((entry) => {
                const elem = entry.target;
                if (entry.isIntersecting) {
                    elem.classList.add(ANIMATED_CLASS);
                    observer.unobserve(elem);
                }
            });
        }
        
        const observer = new IntersectionObserver(callback, { 
			threshold: 0.05,
			rootMargin: '0px 0px -10% 0px'
		});
        
        // Observar solo desde el segundo item en adelante
        for (let i = 1; i < targets.length; i++) {
            observer.observe(targets[i]);
        }
    });
    </script>
    <?php
}
add_action('wp_footer', 'timeline_animation_script');

// Helixona Custom Page — Icon Toggle Details
function helixona_page_toggle_script() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var icons = document.querySelectorAll('.hlx-page__icon-item[data-target]');
        
        icons.forEach(function(icon) {
            icon.addEventListener('click', function() {
                var targetId = this.getAttribute('data-target');
                var detail = document.getElementById(targetId);
                var wasActive = this.classList.contains('is-active');
                
                // Reset all
                icons.forEach(function(i) { i.classList.remove('is-active'); });
                document.querySelectorAll('.hlx-page__detail').forEach(function(d) { 
                    d.classList.remove('is-visible'); 
                });
                
                // Toggle if wasn't already active
                if (!wasActive && detail) {
                    this.classList.add('is-active');
                    detail.classList.add('is-visible');
                    
                    // Smooth scroll to detail
                    setTimeout(function() {
                        detail.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }, 50);
                }
            });
        });
    });
    </script>
    <?php
}
add_action('wp_footer', 'helixona_page_toggle_script');


/**
 * Helixona Roadmap Assets
 */
function hx_page_uses( $key, $pattern ) {
    if ( ! is_singular() ) {
        return false;
    }
    $post = get_queried_object();
    if ( ! $post || empty( $post->ID ) ) {
        return false;
    }
    $cache_key = 'hx_uses_' . $key . '_' . $post->ID . '_' . strtotime( $post->post_modified_gmt );
    $cached    = get_transient( $cache_key );
    if ( false !== $cached ) {
        return (bool) $cached;
    }
    $haystack = (string) get_post_meta( $post->ID, '_elementor_data', true ) . ' ' . (string) $post->post_content;
    $uses     = (bool) preg_match( $pattern, $haystack );
    set_transient( $cache_key, $uses ? 1 : 0, WEEK_IN_SECONDS );
    return $uses;
}
function hx_page_uses_tailwind() {
    return apply_filters( 'hx_page_uses_tailwind', hx_page_uses( 'tw', '/(?:\\b(?:sm|md|lg|xl|2xl):[a-z-]|\\bgrid-cols-\\d|\\bbg-primary\\b|\\btext-primary\\b|\\bbg-background|\\bfont-serif\\b|\\brounded-(?:xl|2xl)\\b|\\bpx-\\d|\\bpy-\\d)/' ) );
}
function hx_page_uses_lucide() {
    return apply_filters( 'hx_page_uses_lucide', hx_page_uses( 'lucide', '/data-lucide=|lucide\\.createIcons/' ) );
}

function enqueue_hx_roadmap_assets() {
    // Lucide only on pages that render data-lucide icons (pinned version; @latest was unversioned)
    if ( ! hx_page_uses_lucide() ) {
        return;
    }
    wp_enqueue_script('lucide-cdn', 'https://unpkg.com/lucide@0.469.0/dist/umd/lucide.min.js', array(), '0.469.0', true);
    
    // Inicializar iconos al cargar el DOM
    wp_add_inline_script('lucide-cdn', '
        document.addEventListener("DOMContentLoaded", function() {
            if(typeof lucide !== "undefined") {
                lucide.createIcons();
            }
        });
    ');
}
add_action('wp_enqueue_scripts', 'enqueue_hx_roadmap_assets');

/**
 * Añadir Tailwind CSS CDN y configuración personalizada al <head>
 */
function agregar_tailwind_cdn_y_config() {
    // Tailwind Play CDN is ~400 KB of render-blocking JS: load it only on pages that use utility classes.
    if ( ! hx_page_uses_tailwind() ) {
        return;
    }
    ?>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    
    <!-- Configuración personalizada de Tailwind -->
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#d6b981", // Emerald green
                        secondary: "#d4af37", // Gold accent
                        "background-light": "#fcfdfc",
                        "background-dark": "#0a1f14",
                        "surface-light": "#f0fdf4",
                        "surface-dark": "#132a1e",
                        "text-main": "#064e3b", 
                        "text-muted": "#3f6251",
                    },
                    fontFamily: {
                        sans: ["Manrope", "sans-serif"],
                        serif: ["Playfair Display", "serif"],
                    },
                    backgroundImage: {
                        'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
                    }
                },
            },
        }
    </script>
    <?php
}
add_action('wp_head', 'agregar_tailwind_cdn_y_config', 100);

// 1. Encolar las fuentes de Google y la librería de iconos Material Symbols
function cargar_fuentes_e_iconos_personalizados() {
    // Cargar Material Symbols Outlined
    wp_enqueue_style(
        'material-symbols-outlined',
        'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap',
        array(),
        null
    );

    // Cargar fuentes Manrope y Playfair Display
    wp_enqueue_style(
        'fuentes-personalizadas',
        'https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap',
        array(),
        null
    );
}
add_action( 'wp_enqueue_scripts', 'cargar_fuentes_e_iconos_personalizados' );

/* ==========================================================================
   JOB LANDING PAGES
   Template : page-templates/job-landing.php
   Content  : jobs/{slug}.php  (one file per position)
   Upload the theme and the page for every job in /jobs is created
   automatically (slug = file name, template = "Job Landing"). The landing
   embeds its own application form; submissions are e-mailed to the same
   recipient as the Careers form on /careers/ (or HX_JOB_APPLY_EMAIL below).
   ========================================================================== */

define( 'HX_JOB_TEMPLATE', 'page-templates/job-landing.php' );

// Optional override, comma-separated. Empty = reuse the Careers form recipient.
if ( ! defined( 'HX_JOB_APPLY_EMAIL' ) ) {
	define( 'HX_JOB_APPLY_EMAIL', 'info@helixona.com' );
}

/**
 * Is the current request a job landing page?
 */
function hx_job_is_landing() {
	return is_page() && is_page_template( HX_JOB_TEMPLATE );
}

/**
 * All job files in /jobs, keyed by slug.
 *
 * @return array<string,array>
 */
function hx_job_get_jobs() {
	static $jobs = null;
	if ( null !== $jobs ) {
		return $jobs;
	}
	$jobs = [];
	foreach ( (array) glob( get_stylesheet_directory() . '/jobs/*.php' ) as $file ) {
		$slug = sanitize_key( basename( $file, '.php' ) );
		$job  = include $file;
		if ( $slug && is_array( $job ) && ! empty( $job['title'] ) ) {
			$job['slug']   = $slug;
			$jobs[ $slug ] = $job;
		}
	}
	return $jobs;
}

/**
 * Job data for a slug, or for the current page (custom field hx_job, else page slug).
 *
 * @param string|null $slug
 * @return array|null
 */
function hx_job_get_data( $slug = null ) {
	if ( null === $slug ) {
		$page_id = get_queried_object_id();
		$slug    = get_post_meta( $page_id, 'hx_job', true );
		if ( ! $slug ) {
			$slug = get_post_field( 'post_name', $page_id );
		}
	}
	$slug = sanitize_key( (string) $slug );
	$jobs = hx_job_get_jobs();
	return isset( $jobs[ $slug ] ) ? $jobs[ $slug ] : null;
}

/**
 * Create the WordPress page for every job once (slug = file name).
 * Runs on init and remembers what it created, so a page you delete stays deleted.
 */
function hx_job_ensure_pages() {
	$done    = (array) get_option( 'hx_job_pages_created', [] );
	$changed = false;

	foreach ( hx_job_get_jobs() as $slug => $job ) {
		if ( in_array( $slug, $done, true ) || empty( $job['auto_create_page'] ) ) {
			continue;
		}

		$page = get_page_by_path( $slug, OBJECT, 'page' );
		if ( ! $page ) {
			$page_id = wp_insert_post( [
				'post_type'    => 'page',
				'post_name'    => $slug,
				'post_title'   => $job['title'],
				'post_status'  => ! empty( $job['page_status'] ) ? $job['page_status'] : 'publish',
				'post_content' => '',
			], true );
			if ( is_wp_error( $page_id ) || ! $page_id ) {
				continue;
			}
			update_post_meta( $page_id, '_wp_page_template', HX_JOB_TEMPLATE );
		} elseif ( ! get_page_template_slug( $page ) ) {
			// Page created by hand without picking the template: assign it.
			update_post_meta( $page->ID, '_wp_page_template', HX_JOB_TEMPLATE );
		}

		$done[]  = $slug;
		$changed = true;
	}

	if ( $changed ) {
		update_option( 'hx_job_pages_created', array_values( array_unique( $done ) ) );
	}
}
add_action( 'init', 'hx_job_ensure_pages', 20 );

/**
 * Styles & scripts, only on job landings.
 */
function hx_job_enqueue_assets() {
	if ( ! hx_job_is_landing() ) {
		return;
	}
	wp_enqueue_style(
		'hx-job-landing',
		get_stylesheet_directory_uri() . '/assets/css/job-landing.css',
		[ 'hello-elementor-child-style' ],
		hx_asset_ver( 'assets/css/job-landing.css' )
	);
	wp_enqueue_script(
		'hx-job-landing',
		get_stylesheet_directory_uri() . '/assets/js/job-landing.js',
		[],
		hx_asset_ver( 'assets/js/job-landing.js' ),
		true
	);
	wp_localize_script( 'hx-job-landing', 'hxJob', [
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
	] );
}
add_action( 'wp_enqueue_scripts', 'hx_job_enqueue_assets', 30 );

add_filter( 'body_class', function ( $classes ) {
	if ( hx_job_is_landing() ) {
		$classes[] = 'hx-job-landing';
	}
	return $classes;
} );

/* --------------------------------------------------------------------------
   Application form
   -------------------------------------------------------------------------- */

/**
 * Cache-safe form token (valid 7 days, no session needed).
 */
function hx_job_token( $slug ) {
	$ts = time();
	return $ts . '.' . hash_hmac( 'sha256', 'hx_job|' . $slug . '|' . $ts, wp_salt( 'nonce' ) );
}

function hx_job_verify_token( $slug, $token ) {
	$parts = explode( '.', (string) $token, 2 );
	if ( 2 !== count( $parts ) || ! ctype_digit( $parts[0] ) ) {
		return false;
	}
	if ( time() - (int) $parts[0] > 7 * DAY_IN_SECONDS ) {
		return false;
	}
	$expected = hash_hmac( 'sha256', 'hx_job|' . $slug . '|' . $parts[0], wp_salt( 'nonce' ) );
	return hash_equals( $expected, $parts[1] );
}

/**
 * Find the Elementor form widget on a page's _elementor_data tree.
 */
function hx_job_find_elementor_form( $elements, $prefer_name = 'Careers Form' ) {
	$found = [];
	$walk  = function ( $nodes ) use ( &$walk, &$found ) {
		foreach ( (array) $nodes as $node ) {
			if ( ! is_array( $node ) ) {
				continue;
			}
			if ( isset( $node['widgetType'] ) && 'form' === $node['widgetType'] ) {
				$found[] = $node;
			}
			if ( ! empty( $node['elements'] ) ) {
				$walk( $node['elements'] );
			}
		}
	};
	$walk( $elements );

	foreach ( $found as $form ) {
		if ( isset( $form['settings']['form_name'] ) && $form['settings']['form_name'] === $prefer_name ) {
			return $form;
		}
	}
	return $found ? $found[0] : null;
}

/**
 * Where applications go: HX_JOB_APPLY_EMAIL, else the Careers form's
 * Elementor "Email" action settings on /careers/, else the WP admin e-mail
 * (which is also Elementor's default).
 */
function hx_job_mail_settings() {
	$domain   = preg_replace( '/^www\./', '', (string) wp_parse_url( home_url(), PHP_URL_HOST ) );
	$settings = [
		'to'        => get_option( 'admin_email' ),
		'from'      => 'email@' . $domain,
		'from_name' => wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ),
		'cc'        => '',
		'bcc'       => '',
		'source'    => 'admin_email',
	];

	if ( HX_JOB_APPLY_EMAIL ) {
		$settings['to']     = HX_JOB_APPLY_EMAIL;
		$settings['source'] = 'constant';
	} else {
		$careers = get_page_by_path( 'careers', OBJECT, 'page' );
		if ( $careers ) {
			$raw  = get_post_meta( $careers->ID, '_elementor_data', true );
			$tree = is_string( $raw ) ? json_decode( $raw, true ) : $raw;
			$form = $tree ? hx_job_find_elementor_form( $tree ) : null;
			if ( $form ) {
				$s = isset( $form['settings'] ) ? $form['settings'] : [];
				if ( ! empty( $s['email_to'] ) ) {
					$settings['to']     = $s['email_to'];
					$settings['source'] = 'careers_form';
				}
				if ( ! empty( $s['email_from'] ) ) {
					$settings['from'] = $s['email_from'];
				}
				if ( ! empty( $s['email_from_name'] ) ) {
					$settings['from_name'] = $s['email_from_name'];
				}
				if ( ! empty( $s['email_to_cc'] ) ) {
					$settings['cc'] = $s['email_to_cc'];
				}
				if ( ! empty( $s['email_to_bcc'] ) ) {
					$settings['bcc'] = $s['email_to_bcc'];
				}
			}
		}
	}

	return apply_filters( 'hx_job_mail_settings', $settings );
}

function hx_job_split_emails( $list ) {
	$out = [];
	foreach ( preg_split( '/[,;]+/', (string) $list ) as $e ) {
		$e = sanitize_email( trim( $e ) );
		if ( $e && is_email( $e ) ) {
			$out[] = $e;
		}
	}
	return $out;
}

/**
 * Handle a submission (AJAX from the landing, or a plain POST without JS).
 */
function hx_job_handle_apply() {
	$is_ajax = wp_doing_ajax();
	$back    = wp_get_referer() ? wp_get_referer() : home_url( '/' );
	$back    = remove_query_arg( 'hx_apply', $back );

	$respond = function ( $ok, $message, $code = 200 ) use ( $is_ajax, $back ) {
		if ( $is_ajax ) {
			if ( $ok ) {
				wp_send_json_success( [ 'message' => $message ], $code );
			}
			wp_send_json_error( [ 'message' => $message ], $code );
		}
		$url = add_query_arg( 'hx_apply', $ok ? 'sent' : rawurlencode( $message ), $back ) . '#hx-apply';
		wp_safe_redirect( $url );
		exit;
	};

	// Honeypot: bots fill it, humans never see it. Pretend it worked.
	if ( ! empty( $_POST['hx_website'] ) ) {
		$respond( true, 'Thank you! Your application has been received.' );
	}

	$slug = isset( $_POST['hx_job'] ) ? sanitize_key( wp_unslash( $_POST['hx_job'] ) ) : '';
	$job  = $slug ? hx_job_get_data( $slug ) : null;
	if ( ! $job ) {
		$respond( false, 'This position is no longer available.', 400 );
	}

	$token = isset( $_POST['hx_token'] ) ? sanitize_text_field( wp_unslash( $_POST['hx_token'] ) ) : '';
	if ( ! hx_job_verify_token( $slug, $token ) ) {
		$respond( false, 'Your session expired. Please reload the page and try again.', 403 );
	}

	// Rate limit: 5 submissions per IP per 10 minutes.
	$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$key = 'hx_job_rl_' . md5( $ip );
	$hits = (int) get_transient( $key );
	if ( $hits >= 5 ) {
		$respond( false, 'Too many submissions from this connection. Please try again later.', 429 );
	}
	set_transient( $key, $hits + 1, 10 * MINUTE_IN_SECONDS );

	$name    = isset( $_POST['hx_name'] ) ? sanitize_text_field( wp_unslash( $_POST['hx_name'] ) ) : '';
	$email   = isset( $_POST['hx_email'] ) ? sanitize_email( wp_unslash( $_POST['hx_email'] ) ) : '';
	$phone   = isset( $_POST['hx_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['hx_phone'] ) ) : '';
	$message = isset( $_POST['hx_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['hx_message'] ) ) : '';

	if ( mb_strlen( $name ) < 2 || mb_strlen( $name ) > 120 ) {
		$respond( false, 'Please enter your full name.', 400 );
	}
	if ( ! $email || ! is_email( $email ) ) {
		$respond( false, 'Please enter a valid e-mail address.', 400 );
	}
	if ( mb_strlen( $message ) > 5000 ) {
		$respond( false, 'Your message is too long (5,000 characters max).', 400 );
	}

	// Resume (required): PDF or Word.
	$max_mb = ! empty( $job['resume_max_mb'] ) ? (int) $job['resume_max_mb'] : 10;
	$file   = isset( $_FILES['hx_resume'] ) ? $_FILES['hx_resume'] : null;
	if ( ! $file || empty( $file['tmp_name'] ) || ! empty( $file['error'] ) ) {
		$respond( false, sprintf( 'Please attach your resume (PDF or Word, up to %d MB).', $max_mb ), 400 );
	}
	if ( (int) $file['size'] > $max_mb * MB_IN_BYTES ) {
		$respond( false, sprintf( 'Your resume is too large. Please upload a file under %d MB.', $max_mb ), 400 );
	}
	$allowed = [
		'pdf'  => 'application/pdf',
		'doc'  => 'application/msword',
		'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
	];
	$check = wp_check_filetype_and_ext( $file['tmp_name'], $file['name'], $allowed );
	if ( empty( $check['ext'] ) || ! isset( $allowed[ $check['ext'] ] ) ) {
		$respond( false, 'Please upload your resume as a PDF or Word document.', 400 );
	}

	// Keep the applicant's file name on the attachment.
	$tmp_dir  = trailingslashit( get_temp_dir() ) . 'hx-job-' . wp_generate_password( 12, false );
	$filename = sanitize_file_name( $file['name'] );
	if ( ! $filename || false === strpos( $filename, '.' ) ) {
		$filename = 'resume.' . $check['ext'];
	}
	if ( ! wp_mkdir_p( $tmp_dir ) || ! move_uploaded_file( $file['tmp_name'], $tmp_dir . '/' . $filename ) ) {
		$respond( false, 'We could not read your resume. Please try again.', 500 );
	}
	$attachment = $tmp_dir . '/' . $filename;

	$mail = hx_job_mail_settings();
	$to   = hx_job_split_emails( $mail['to'] );
	if ( ! $to ) {
		$to = [ get_option( 'admin_email' ) ];
	}

	$page_url = $back;
	$rows     = [
		'Position'  => $job['title'],
		'Name'      => $name,
		'Email'     => $email,
		'Phone'     => $phone ? $phone : '—',
		'Message'   => $message ? nl2br( esc_html( $message ) ) : '—',
		'Resume'    => $filename . ' (attached)',
		'Submitted' => wp_date( 'F j, Y g:i a' ) . ' · ' . esc_html( $page_url ),
	];

	$body  = '<div style="font-family:Arial,Helvetica,sans-serif;font-size:15px;line-height:1.5;color:#1a1a1a;">';
	$body .= '<h2 style="font-weight:normal;margin:0 0 16px;">New application: ' . esc_html( $job['title'] ) . '</h2>';
	$body .= '<table cellpadding="8" cellspacing="0" style="border-collapse:collapse;max-width:640px;width:100%;">';
	foreach ( $rows as $label => $value ) {
		$value = ( 'Message' === $label ) ? $value : esc_html( $value );
		$body .= '<tr><th align="left" valign="top" style="border-bottom:1px solid #e9e1d1;color:#8a6d1c;font-weight:normal;width:120px;">' . esc_html( $label ) . '</th>'
			. '<td valign="top" style="border-bottom:1px solid #e9e1d1;">' . $value . '</td></tr>';
	}
	$body .= '</table></div>';

	$headers = [
		'Content-Type: text/html; charset=UTF-8',
		'From: ' . $mail['from_name'] . ' <' . $mail['from'] . '>',
		'Reply-To: ' . $name . ' <' . $email . '>',
	];
	foreach ( hx_job_split_emails( $mail['cc'] ) as $cc ) {
		$headers[] = 'Cc: ' . $cc;
	}
	foreach ( hx_job_split_emails( $mail['bcc'] ) as $bcc ) {
		$headers[] = 'Bcc: ' . $bcc;
	}

	$subject = sprintf( 'New application: %s – %s', $job['title'], $name );
	$sent    = wp_mail( $to, $subject, $body, $headers, [ $attachment ] );

	@unlink( $attachment ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
	@rmdir( $tmp_dir );     // phpcs:ignore WordPress.PHP.NoSilencedErrors

	if ( ! $sent ) {
		$respond( false, 'We could not send your application right now. Please try again in a few minutes or apply through our Careers page.', 500 );
	}

	if ( ! empty( $job['applicant_confirmation'] ) ) {
		$confirm  = '<div style="font-family:Arial,Helvetica,sans-serif;font-size:15px;line-height:1.6;color:#1a1a1a;">';
		$confirm .= '<p>Hi ' . esc_html( $name ) . ',</p>';
		$confirm .= '<p>Thank you for applying for the <strong>' . esc_html( $job['title'] ) . '</strong> position at Helixona. We have received your resume.</p>';
		$confirm .= '<p>If your qualifications align with what we are looking for, a member of our team will contact you to discuss the next steps in the hiring process.</p>';
		$confirm .= '<p>Warm regards,<br>The Helixona Team</p></div>';
		wp_mail(
			$email,
			'We received your application – Helixona',
			$confirm,
			[ 'Content-Type: text/html; charset=UTF-8', 'From: ' . $mail['from_name'] . ' <' . $mail['from'] . '>' ]
		);
	}

	do_action( 'hx_job_application_sent', $job, compact( 'name', 'email', 'phone', 'message', 'filename' ) );

	$respond( true, 'Thank you! Your application has been received.' );
}
add_action( 'wp_ajax_hx_job_apply', 'hx_job_handle_apply' );
add_action( 'wp_ajax_nopriv_hx_job_apply', 'hx_job_handle_apply' );
add_action( 'admin_post_hx_job_apply', 'hx_job_handle_apply' );
add_action( 'admin_post_nopriv_hx_job_apply', 'hx_job_handle_apply' );

/* --------------------------------------------------------------------------
   SEO: JobPosting structured data + social meta
   -------------------------------------------------------------------------- */
function hx_job_schema_description( $job ) {
	$html = '<p>' . esc_html( $job['summary'] ) . '</p>';
	foreach ( (array) $job['opportunity'] as $p ) {
		$html .= '<p>' . wp_kses_post( $p ) . '</p>';
	}
	$lists = [
		'What You Will Do'         => $job['responsibilities'],
		'Required Qualifications'  => $job['required'],
		'Preferred Qualifications' => $job['preferred'],
	];
	foreach ( $lists as $heading => $items ) {
		$html .= '<h3>' . esc_html( $heading ) . '</h3><ul>';
		foreach ( (array) $items as $item ) {
			$html .= '<li>' . wp_kses_post( $item ) . '</li>';
		}
		$html .= '</ul>';
	}
	$html .= '<h3>Benefits</h3><ul>';
	foreach ( (array) $job['benefits'] as $b ) {
		$html .= '<li>' . esc_html( $b['title'] ) . ' – ' . wp_kses_post( $b['text'] ) . '</li>';
	}
	$html .= '</ul><p>' . esc_html( $job['eeo'] ) . '</p>';
	return $html;
}

function hx_job_print_schema() {
	if ( ! hx_job_is_landing() ) {
		return;
	}
	$job = hx_job_get_data();
	if ( ! $job || empty( $job['schema'] ) ) {
		return;
	}
	$s    = $job['schema'];
	$data = [
		'@context'           => 'https://schema.org/',
		'@type'              => 'JobPosting',
		'title'              => $job['title'],
		'description'        => hx_job_schema_description( $job ),
		'datePosted'         => $job['date_posted'],
		'employmentType'     => $s['employment_type'],
		'directApply'        => true,
		'url'                => get_permalink(),
		'hiringOrganization' => [
			'@type'  => 'Organization',
			'name'   => $s['org_name'],
			'sameAs' => $s['org_url'],
			'logo'   => $s['org_logo'],
		],
		'jobLocation'        => [
			'@type'   => 'Place',
			'address' => [
				'@type'           => 'PostalAddress',
				'addressLocality' => $s['locality'],
				'addressRegion'   => $s['region'],
				'addressCountry'  => $s['country'],
			],
		],
		'baseSalary'         => [
			'@type'    => 'MonetaryAmount',
			'currency' => $s['currency'],
			'value'    => [
				'@type'    => 'QuantitativeValue',
				'minValue' => $s['salary_min'],
				'maxValue' => $s['salary_max'],
				'unitText' => 'YEAR',
			],
		],
	];
	if ( ! empty( $job['valid_through'] ) ) {
		$data['validThrough'] = $job['valid_through'];
	}
	if ( ! empty( $job['bonus'] ) ) {
		$data['incentiveCompensation'] = $job['bonus'];
	}
	echo "\n" . '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'hx_job_print_schema', 5 );

function hx_job_print_meta() {
	if ( ! hx_job_is_landing() ) {
		return;
	}
	if ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' ) ) {
		return; // an SEO plugin owns these tags
	}
	$job = hx_job_get_data();
	if ( ! $job ) {
		return;
	}
	$desc = $job['title'] . ' · ' . $job['employment_type'] . ' · ' . $job['location'] . '. ' . $job['summary'];
	?>
	<meta name="description" content="<?php echo esc_attr( $desc ); ?>">
	<meta property="og:type" content="website">
	<meta property="og:title" content="<?php echo esc_attr( $job['title'] . ' – Careers at Helixona' ); ?>">
	<meta property="og:description" content="<?php echo esc_attr( $desc ); ?>">
	<meta property="og:image" content="<?php echo esc_url( $job['hero_image'] ); ?>">
	<meta property="og:url" content="<?php echo esc_url( get_permalink() ); ?>">
	<meta name="twitter:card" content="summary_large_image">
	<?php
}
add_action( 'wp_head', 'hx_job_print_meta', 6 );

/* ==========================================================================
   SERVICE PAGES
   Template : page-templates/service-page.php  ·  Data: services/{slug}.php
   Like job landings: upload the theme and the page is created automatically.
   A service file can also declare old URLs to 301, a menu rename/removal and
   a services-page card update; those run once (see CONTENT UPDATES below).
   ========================================================================== */

define( 'HX_SERVICE_TEMPLATE', 'page-templates/service-page.php' );

// Website 2026 · stage 1: landing pages, new header menu, redirects (dormant until switched on in Settings → Reading).
require_once get_stylesheet_directory() . '/inc/website-2026.php';
// Become a Patient page + intake form (replaces the Typeform link).
require_once get_stylesheet_directory() . '/inc/become-a-patient.php';

function hx_service_is_page() {
	return is_page() && is_page_template( HX_SERVICE_TEMPLATE );
}

function hx_service_get_services() {
	static $services = null;
	if ( null !== $services ) {
		return $services;
	}
	$services = [];
	foreach ( (array) glob( get_stylesheet_directory() . '/services/*.php' ) as $file ) {
		$slug = sanitize_key( basename( $file, '.php' ) );
		$svc  = include $file;
		if ( $slug && is_array( $svc ) && ! empty( $svc['title'] ) ) {
			$svc['slug']        = $slug;
			$services[ $slug ]  = $svc;
		}
	}
	return $services;
}

function hx_service_get_data( $slug = null ) {
	if ( null === $slug ) {
		$page_id = get_queried_object_id();
		$slug    = get_post_meta( $page_id, 'hx_service', true );
		if ( ! $slug ) {
			$slug = get_post_field( 'post_name', $page_id );
		}
	}
	$slug     = sanitize_key( (string) $slug );
	$services = hx_service_get_services();
	return isset( $services[ $slug ] ) ? $services[ $slug ] : null;
}

/**
 * Shared: make sure a page exists for a data file (used by jobs and services).
 *
 * @return int|null page ID
 */
function hx_ensure_page( $slug, $title, $template, $status = 'publish' ) {
	$page = get_page_by_path( $slug, OBJECT, 'page' );
	if ( $page ) {
		if ( ! get_page_template_slug( $page ) ) {
			update_post_meta( $page->ID, '_wp_page_template', $template );
		}
		return (int) $page->ID;
	}
	$page_id = wp_insert_post( [
		'post_type'    => 'page',
		'post_name'    => $slug,
		'post_title'   => $title,
		'post_status'  => $status,
		'post_content' => '',
	], true );
	if ( is_wp_error( $page_id ) || ! $page_id ) {
		return null;
	}
	update_post_meta( $page_id, '_wp_page_template', $template );
	return (int) $page_id;
}

function hx_service_ensure_pages() {
	$done    = (array) get_option( 'hx_service_pages_created', [] );
	$changed = false;
	foreach ( hx_service_get_services() as $slug => $svc ) {
		if ( in_array( $slug, $done, true ) || empty( $svc['auto_create_page'] ) ) {
			continue;
		}
		if ( hx_ensure_page( $slug, $svc['title'], HX_SERVICE_TEMPLATE, ! empty( $svc['page_status'] ) ? $svc['page_status'] : 'publish' ) ) {
			$done[]  = $slug;
			$changed = true;
		}
	}
	if ( $changed ) {
		update_option( 'hx_service_pages_created', array_values( array_unique( $done ) ) );
	}
}
add_action( 'init', 'hx_service_ensure_pages', 20 );

function hx_service_page_url( $slug ) {
	$page = get_page_by_path( $slug, OBJECT, 'page' );
	return ( $page && 'publish' === $page->post_status ) ? get_permalink( $page ) : '';
}

/**
 * 301 old service URLs to the new page.
 */
function hx_service_redirects() {
	if ( ! is_page() || is_admin() ) {
		return;
	}
	$current = trailingslashit( '/' . trim( (string) wp_parse_url( add_query_arg( [] ), PHP_URL_PATH ), '/' ) );
	foreach ( hx_service_get_services() as $slug => $svc ) {
		foreach ( (array) ( $svc['redirect_from'] ?? [] ) as $old ) {
			$old = trailingslashit( '/' . trim( $old, '/' ) );
			if ( $current !== $old ) {
				continue;
			}
			$target = hx_service_page_url( $slug );
			if ( $target && untrailingslashit( (string) wp_parse_url( $target, PHP_URL_PATH ) ) !== untrailingslashit( $current ) ) {
				wp_safe_redirect( $target, 301 );
				exit;
			}
		}
	}
}
add_action( 'template_redirect', 'hx_service_redirects', 1 );

function hx_service_enqueue_assets() {
	if ( ! hx_service_is_page() ) {
		return;
	}
	wp_enqueue_style( 'hx-job-landing', get_stylesheet_directory_uri() . '/assets/css/job-landing.css', [ 'hello-elementor-child-style' ], hx_asset_ver( 'assets/css/job-landing.css' ) );
	wp_enqueue_style( 'hx-service-page', get_stylesheet_directory_uri() . '/assets/css/service-page.css', [ 'hx-job-landing' ], hx_asset_ver( 'assets/css/service-page.css' ) );
}
add_action( 'wp_enqueue_scripts', 'hx_service_enqueue_assets', 30 );

add_filter( 'body_class', function ( $classes ) {
	if ( hx_service_is_page() ) {
		$classes[] = 'hx-service-page';
	}
	return $classes;
} );

function hx_service_print_meta() {
	if ( ! hx_service_is_page() ) {
		return;
	}
	if ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' ) ) {
		return;
	}
	$svc = hx_service_get_data();
	if ( ! $svc ) {
		return;
	}
	$desc = ! empty( $svc['seo_description'] ) ? $svc['seo_description'] : $svc['subtitle'];
	?>
	<meta name="description" content="<?php echo esc_attr( $desc ); ?>">
	<meta property="og:type" content="website">
	<meta property="og:title" content="<?php echo esc_attr( $svc['title'] . ' – Helixona' ); ?>">
	<meta property="og:description" content="<?php echo esc_attr( $desc ); ?>">
	<meta property="og:image" content="<?php echo esc_url( ! empty( $svc['device_image'] ) ? $svc['device_image'] : $svc['hero_image'] ); ?>">
	<meta property="og:url" content="<?php echo esc_url( get_permalink() ); ?>">
	<?php
}
add_action( 'wp_head', 'hx_service_print_meta', 6 );

/* ==========================================================================
   CONTENT UPDATES (run once each, after upload)
   - services: menu rename/removal + services-page bento card
   - blog: featured image + excerpt for existing posts
   Progress is stored in option hx_content_updates_done; a failed step retries
   after an hour. Delete the option to re-run everything.
   ========================================================================== */

function hx_content_updates() {
	$updates = [];

	foreach ( hx_service_get_services() as $slug => $svc ) {
		if ( ! empty( $svc['menu'] ) ) {
			$updates[ 'menu:' . $slug ] = function () use ( $slug, $svc ) {
				return hx_cu_update_menu( $slug, $svc['menu'] );
			};
		}
		if ( ! empty( $svc['services_card'] ) ) {
			$updates[ 'card:' . $slug ] = function () use ( $slug, $svc ) {
				return hx_cu_update_services_card( $slug, $svc['services_card'] );
			};
		}
	}

	// Blog: "The Plastics We Don't Notice" — proper featured image + excerpt.
	$updates['blog:the-plastics-we-dont-notice'] = function () {
		return hx_cu_fix_post(
			'the-plastics-we-dont-notice',
			get_stylesheet_directory() . '/assets/img/blog-plastics-featured.jpg',
			'Glass jars with beans, lentils and rice on a wooden kitchen table',
			'Microplastics are turning up in places we never expected, and a few everyday habits put plastic in contact with heat, food and skin. Not a reason for fear, but a reason to make easy swaps: glass, wood, steel and natural fibers, one worn-out item at a time.'
		);
	};

	return apply_filters( 'hx_content_updates', $updates );
}

function hx_run_content_updates() {
	if ( wp_doing_ajax() || wp_doing_cron() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return;
	}
	$updates = hx_content_updates();
	$done    = (array) get_option( 'hx_content_updates_done', [] );
	$pending = array_diff( array_keys( $updates ), $done );
	if ( ! $pending || get_transient( 'hx_cu_lock' ) ) {
		return;
	}
	set_transient( 'hx_cu_lock', 1, 2 * MINUTE_IN_SECONDS );

	foreach ( $pending as $id ) {
		if ( get_transient( 'hx_cu_retry_' . md5( $id ) ) ) {
			continue;
		}
		$ok = false;
		try {
			$ok = (bool) call_user_func( $updates[ $id ] );
		} catch ( \Throwable $e ) {
			$ok = false;
		}
		if ( $ok ) {
			$done[] = $id;
			update_option( 'hx_content_updates_done', array_values( array_unique( $done ) ) );
		} else {
			set_transient( 'hx_cu_retry_' . md5( $id ), 1, HOUR_IN_SECONDS );
		}
	}
	delete_transient( 'hx_cu_lock' );
}
add_action( 'init', 'hx_run_content_updates', 30 );

/**
 * Rename / remove nav menu items by URL fragment.
 */
function hx_cu_update_menu( $slug, $cfg ) {
	$target_url = hx_service_page_url( $slug );
	if ( ! $target_url ) {
		return false; // page not published yet; retry later
	}
	$target = get_page_by_path( $slug, OBJECT, 'page' );
	$left   = 0;

	foreach ( wp_get_nav_menus() as $menu ) {
		$items = wp_get_nav_menu_items( $menu->term_id, [ 'post_status' => 'any' ] );
		if ( ! $items ) {
			continue;
		}
		foreach ( $items as $item ) {
			$url = (string) $item->url;

			foreach ( (array) ( $cfg['remove'] ?? [] ) as $frag ) {
				if ( $frag && false !== strpos( $url, $frag ) ) {
					wp_delete_post( $item->ID, true );
					continue 2;
				}
			}
			foreach ( (array) ( $cfg['rename'] ?? [] ) as $rule ) {
				if ( empty( $rule['match'] ) || false === strpos( $url, $rule['match'] ) ) {
					continue;
				}
				wp_update_post( [ 'ID' => $item->ID, 'post_title' => $rule['title'] ] );
				update_post_meta( $item->ID, '_menu_item_type', 'post_type' );
				update_post_meta( $item->ID, '_menu_item_object', 'page' );
				update_post_meta( $item->ID, '_menu_item_object_id', $target->ID );
				update_post_meta( $item->ID, '_menu_item_url', '' );
				continue 2;
			}
		}
	}

	// Anything still pointing at the old URLs?
	foreach ( wp_get_nav_menus() as $menu ) {
		foreach ( (array) wp_get_nav_menu_items( $menu->term_id, [ 'post_status' => 'any' ] ) as $item ) {
			foreach ( array_merge( (array) ( $cfg['remove'] ?? [] ), wp_list_pluck( (array) ( $cfg['rename'] ?? [] ), 'match' ) ) as $frag ) {
				if ( $frag && false !== strpos( (string) $item->url, $frag ) ) {
					$left++;
				}
			}
		}
	}
	hx_cu_flush_elementor();
	return 0 === $left;
}

/**
 * Update the bento card on the services page (Elementor HTML widgets).
 */
function hx_cu_update_services_card( $slug, $cfg ) {
	$target_url = hx_service_page_url( $slug );
	$page       = get_page_by_path( $cfg['page'], OBJECT, 'page' );
	if ( ! $target_url || ! $page ) {
		return false;
	}
	$raw = get_post_meta( $page->ID, '_elementor_data', true );
	if ( ! $raw ) {
		return true; // nothing to update on this install
	}
	$data = json_decode( $raw, true );
	if ( ! is_array( $data ) ) {
		return false;
	}

	$transform = function ( $html ) use ( $cfg, $target_url ) {
		// 1) remove folded-in cards
		foreach ( (array) ( $cfg['remove'] ?? [] ) as $frag ) {
			$html = preg_replace( '#\s*<a\b[^>]*class="bento-helixona__item[^"]*"[^>]*href="[^"]*' . preg_quote( $frag, '#' ) . '[^"]*"[^>]*>.*?</a>#s', '', $html );
		}
		// 2) rewrite the matched card
		$pattern = '#<a\b[^>]*class="bento-helixona__item[^"]*"[^>]*href="[^"]*' . preg_quote( $cfg['match'], '#' ) . '[^"]*"[^>]*>.*?</a>#s';
		$html    = preg_replace_callback( $pattern, function ( $m ) use ( $cfg, $target_url ) {
			$card = $m[0];
			$card = preg_replace( '#href="[^"]*"#', 'href="' . esc_url( $target_url ) . '"', $card, 1 );
			$card = preg_replace( '#aria-label="[^"]*"#', 'aria-label="' . esc_attr( $cfg['aria'] ) . '"', $card, 1 );
			$card = preg_replace( '#<strong>.*?</strong>#s', '<strong>' . esc_html( $cfg['title'] ) . '</strong>', $card, 1 );
			$desc = ( ! empty( $cfg['subtitle'] ) ? '<em>' . esc_html( $cfg['subtitle'] ) . '.</em> ' : '' ) . esc_html( $cfg['text'] );
			$card = preg_replace( '#(<span class="bento-helixona__desc">).*?(</span>)#s', '$1' . $desc . '$2', $card, 1 );
			return $card;
		}, $html );
		return $html;
	};

	$changed = false;
	$walk    = function ( &$nodes ) use ( &$walk, &$changed, $transform ) {
		foreach ( $nodes as &$node ) {
			if ( isset( $node['settings']['html'] ) && is_string( $node['settings']['html'] ) && false !== strpos( $node['settings']['html'], 'bento-helixona' ) ) {
				$new = $transform( $node['settings']['html'] );
				if ( $new !== $node['settings']['html'] ) {
					$node['settings']['html'] = $new;
					$changed                  = true;
				}
			}
			if ( ! empty( $node['elements'] ) && is_array( $node['elements'] ) ) {
				$walk( $node['elements'] );
			}
		}
	};
	$walk( $data );

	if ( $changed ) {
		update_post_meta( $page->ID, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );
		delete_post_meta( $page->ID, '_elementor_css' );
		delete_post_meta( $page->ID, '_elementor_element_cache' );
		hx_cu_flush_elementor();
	}

	// Done when no old links remain in the page data.
	$json = wp_json_encode( $data );
	foreach ( array_merge( [ $cfg['match'] ], (array) ( $cfg['remove'] ?? [] ) ) as $frag ) {
		if ( false !== strpos( $json, $frag ) ) {
			return false;
		}
	}
	return true;
}

/**
 * Set a featured image (from a file shipped in the theme) and the excerpt of an existing post.
 */
function hx_cu_fix_post( $post_slug, $image_path, $alt, $excerpt ) {
	$posts = get_posts( [ 'name' => $post_slug, 'post_type' => 'post', 'post_status' => 'any', 'numberposts' => 1 ] );
	if ( ! $posts ) {
		return true; // post does not exist on this install: nothing to do
	}
	$post = $posts[0];

	$already = false;
	$thumb   = get_post_thumbnail_id( $post->ID );
	if ( $thumb ) {
		$file    = (string) get_post_meta( $thumb, '_wp_attached_file', true );
		$already = ( false !== strpos( basename( $file ), $post_slug ) );
	}

	if ( ! $already ) {
		if ( ! is_readable( $image_path ) ) {
			return false;
		}
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$tmp = wp_tempnam( $post_slug . '.jpg' );
		if ( ! $tmp || ! copy( $image_path, $tmp ) ) {
			return false;
		}
		$attachment_id = media_handle_sideload(
			[ 'name' => $post_slug . '.jpg', 'tmp_name' => $tmp, 'type' => 'image/jpeg' ],
			$post->ID,
			$alt
		);
		if ( is_wp_error( $attachment_id ) ) {
			@unlink( $tmp ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
			return false;
		}
		update_post_meta( $attachment_id, '_wp_attachment_image_alt', $alt );
		set_post_thumbnail( $post->ID, $attachment_id );
	}

	$update = [ 'ID' => $post->ID ];
	if ( $excerpt && $post->post_excerpt !== $excerpt ) {
		$update['post_excerpt'] = $excerpt;
	}
	if ( ! $already ) { // first run: surface it as the newest post
		$update['post_date']     = current_time( 'mysql' );
		$update['post_date_gmt'] = current_time( 'mysql', true );
	}
	if ( count( $update ) > 1 ) {
		wp_update_post( $update );
	}
	return true;
}

function hx_cu_flush_elementor() {
	if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->files_manager ) ) {
		\Elementor\Plugin::$instance->files_manager->clear_cache();
	}
	delete_metadata( 'post', 0, '_elementor_element_cache', '', true );
}

/**
 * Admin check: open any job landing with ?hx_mail_check=1 while logged in as
 * administrator to see where applications are sent (resolved recipient, From, source).
 */
function hx_job_mail_check_banner() {
	if ( ! hx_job_is_landing() || ! isset( $_GET['hx_mail_check'] ) || ! current_user_can( 'manage_options' ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return;
	}
	$m = hx_job_mail_settings();
	$source = [
		'constant'     => 'HX_JOB_APPLY_EMAIL (functions.php)',
		'careers_form' => 'Formulario de Careers en /careers/ (acción Email → To)',
		'admin_email'  => 'Email de administrador de WordPress (Ajustes → Generales)',
	];
	?>
	<div style="position:fixed;left:16px;bottom:16px;z-index:99999;max-width:420px;padding:16px 18px;background:#000;color:#fff;border:1px solid #d6b981;font:14px/1.5 system-ui,sans-serif;">
		<strong style="color:#d6b981;">Formulario de aplicación · destino</strong><br>
		Para: <?php echo esc_html( $m['to'] ); ?><br>
		<?php if ( $m['cc'] ) : ?>CC: <?php echo esc_html( $m['cc'] ); ?><br><?php endif; ?>
		<?php if ( $m['bcc'] ) : ?>BCC: <?php echo esc_html( $m['bcc'] ); ?><br><?php endif; ?>
		De: <?php echo esc_html( $m['from_name'] . ' <' . $m['from'] . '>' ); ?><br>
		Origen: <?php echo esc_html( $source[ $m['source'] ] ?? $m['source'] ); ?><br>
		<span style="opacity:.6">Solo visible para administradores con ?hx_mail_check=1</span>
	</div>
	<?php
}
add_action( 'wp_footer', 'hx_job_mail_check_banner' );

/* ==========================================================================
   NO MORE KARMA SNACK
   The previous agency's addresses (@karmasnack.com / @karmasnackdev.com) must
   not receive anything. Two layers:
   1) one-time runner: rewrite every Elementor form (any page, template or
      popup) whose Email actions point at those domains → HX_SITE_INBOX;
   2) safety net on wp_mail(): any To/Cc/Bcc still pointing there is rewritten
      at send time, whatever plugin or form produced it.
   ========================================================================== */

define( 'HX_SITE_INBOX', 'info@helixona.com' );
define( 'HX_BLOCKED_MAIL_DOMAINS', 'karmasnack.com,karmasnackdev.com' );

function hx_is_blocked_email( $email ) {
	$email = strtolower( trim( (string) $email ) );
	foreach ( explode( ',', HX_BLOCKED_MAIL_DOMAINS ) as $domain ) {
		if ( $domain && substr( $email, -strlen( '@' . $domain ) ) === '@' . $domain ) {
			return true;
		}
	}
	return false;
}

/**
 * Rewrite a comma-separated recipient list; returns [new list, changed?].
 */
function hx_rewrite_recipients( $list ) {
	$out     = [];
	$changed = false;
	foreach ( preg_split( '/[,;]+/', (string) $list ) as $raw ) {
		$raw = trim( $raw );
		if ( '' === $raw ) {
			continue;
		}
		$addr = preg_match( '/<([^>]+)>/', $raw, $m ) ? $m[1] : $raw;
		if ( hx_is_blocked_email( $addr ) ) {
			$raw     = HX_SITE_INBOX;
			$changed = true;
		}
		if ( ! in_array( $raw, $out, true ) ) {
			$out[] = $raw;
		}
	}
	return [ implode( ', ', $out ), $changed ];
}

/** 2) Safety net at send time. */
add_filter( 'wp_mail', function ( $args ) {
	$to = is_array( $args['to'] ) ? implode( ',', $args['to'] ) : (string) $args['to'];
	list( $new_to, $changed ) = hx_rewrite_recipients( $to );
	if ( $changed ) {
		$args['to'] = $new_to;
	}
	$headers = is_array( $args['headers'] ) ? $args['headers'] : preg_split( '/\r\n|\r|\n/', (string) $args['headers'] );
	foreach ( $headers as $i => $h ) {
		if ( preg_match( '/^(cc|bcc|reply-to):\s*(.+)$/i', trim( (string) $h ), $m ) ) {
			list( $new, $ch ) = hx_rewrite_recipients( $m[2] );
			if ( $ch ) {
				$headers[ $i ] = $m[1] . ': ' . $new;
			}
		} elseif ( preg_match( '/^from:\s*(.+)$/i', trim( (string) $h ), $m ) ) {
			$addr = preg_match( '/<([^>]+)>/', $m[1], $mm ) ? $mm[1] : trim( $m[1] );
			if ( hx_is_blocked_email( $addr ) ) {
				$domain        = preg_replace( '/^www\./', '', (string) wp_parse_url( home_url(), PHP_URL_HOST ) );
				$headers[ $i ] = 'From: Helixona <email@' . $domain . '>';
			}
		}
	}
	$args['headers'] = $headers;
	return $args;
} );

/** 1) One-time rewrite of every Elementor form's e-mail settings. */
function hx_cu_fix_elementor_form_recipients() {
	global $wpdb;
	$rows = $wpdb->get_results( "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_elementor_data' AND meta_value LIKE '%karmasnack%'" );
	$log  = (array) get_option( 'hx_forms_rewritten', [] );

	foreach ( (array) $rows as $row ) {
		$data = json_decode( $row->meta_value, true );
		if ( ! is_array( $data ) ) {
			continue;
		}
		$changed = false;
		$walk    = function ( &$nodes ) use ( &$walk, &$changed, &$log, $row ) {
			foreach ( $nodes as &$node ) {
				if ( ! empty( $node['settings'] ) && is_array( $node['settings'] ) ) {
					foreach ( $node['settings'] as $key => $val ) {
						if ( 0 === strpos( (string) $key, 'email_' ) && is_string( $val ) && false !== stripos( $val, 'karmasnack' ) ) {
							if ( 0 === strpos( $key, 'email_from' ) ) {
								$domain = preg_replace( '/^www\./', '', (string) wp_parse_url( home_url(), PHP_URL_HOST ) );
								$new    = ( false !== strpos( $key, 'name' ) ) ? 'Helixona' : 'email@' . $domain;
							} else {
								list( $new ) = hx_rewrite_recipients( $val );
							}
							if ( $new !== $val ) {
								$node['settings'][ $key ] = $new;
								$changed                  = true;
								$log[]                    = sprintf( 'post %d · %s · %s → %s', $row->post_id, isset( $node['settings']['form_name'] ) ? $node['settings']['form_name'] : ( $node['widgetType'] ?? 'widget' ), $key, $new );
							}
						}
					}
				}
				if ( ! empty( $node['elements'] ) && is_array( $node['elements'] ) ) {
					$walk( $node['elements'] );
				}
			}
		};
		$walk( $data );
		if ( $changed ) {
			update_post_meta( $row->post_id, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );
			delete_post_meta( $row->post_id, '_elementor_css' );
			delete_post_meta( $row->post_id, '_elementor_element_cache' );
		}
	}
	update_option( 'hx_forms_rewritten', array_values( array_unique( $log ) ) );
	hx_cu_flush_elementor();

	$left = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->postmeta} WHERE meta_key = '_elementor_data' AND meta_value LIKE '%karmasnack%'" );
	return 0 === $left;
}
add_filter( 'hx_content_updates', function ( $updates ) {
	$updates['forms:no-karmasnack'] = 'hx_cu_fix_elementor_form_recipients';
	return $updates;
} );

/**
 * Home: "(Coming summer 2026)" → "(Coming November 2026)" on the Halo Therapy
 * and Nano Oxygen & Hydrogen Baths cards (Elementor heading widgets).
 */
function hx_cu_home_coming_date() {
	$home_id = (int) get_option( 'page_on_front' );
	$raw     = $home_id ? get_post_meta( $home_id, '_elementor_data', true ) : '';
	if ( ! $raw ) {
		return true; // no Elementor home on this install
	}
	$data = json_decode( $raw, true );
	if ( ! is_array( $data ) ) {
		return false;
	}
	$changed = false;
	$walk    = function ( &$nodes ) use ( &$walk, &$changed ) {
		foreach ( $nodes as &$node ) {
			if ( isset( $node['settings']['title'] ) && is_string( $node['settings']['title'] ) ) {
				$new = preg_replace( '/Coming\s+summer\s+2026/i', 'Coming November 2026', $node['settings']['title'] );
				if ( $new !== $node['settings']['title'] ) {
					$node['settings']['title'] = $new;
					$changed                   = true;
				}
			}
			if ( ! empty( $node['elements'] ) && is_array( $node['elements'] ) ) {
				$walk( $node['elements'] );
			}
		}
	};
	$walk( $data );
	if ( $changed ) {
		update_post_meta( $home_id, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );
		delete_post_meta( $home_id, '_elementor_css' );
		delete_post_meta( $home_id, '_elementor_element_cache' );
		hx_cu_flush_elementor();
	}
	return ! preg_match( '/Coming\s+summer\s+2026/i', (string) get_post_meta( $home_id, '_elementor_data', true ) );
}
add_filter( 'hx_content_updates', function ( $updates ) {
	$updates['home:coming-november-2026'] = 'hx_cu_home_coming_date';
	return $updates;
} );

/**
 * Home "Meet Our Physicians": add a team member from /team/{slug}.php as a copy
 * of Eduard's row (photo left, bold text under it, bio right), after Thomas's row.
 */
define( 'HX_TEAM_SOURCE_ROW', '2f87787' ); // Eduard: photo left
define( 'HX_TEAM_AFTER_ROW', 'd0eaafc' );  // Thomas: photo right

function hx_team_get_member( $slug ) {
	$file = get_stylesheet_directory() . '/team/' . $slug . '.php';
	$m    = file_exists( $file ) ? include $file : null;
	return is_array( $m ) ? $m : null;
}

function hx_cu_home_team_member( $slug ) {
	$m       = hx_team_get_member( $slug );
	$home_id = (int) get_option( 'page_on_front' );
	$raw     = $home_id ? get_post_meta( $home_id, '_elementor_data', true ) : '';
	if ( ! $m || ! $raw ) {
		return true; // nothing to do on this install
	}
	if ( false !== strpos( $raw, $m['name'] ) ) {
		return true; // already on the page
	}
	$data = json_decode( $raw, true );
	if ( ! is_array( $data ) ) {
		return false;
	}

	// Find Eduard's row and the container that holds the rows.
	$source = null;
	$find   = function ( $nodes ) use ( &$find, &$source ) {
		foreach ( $nodes as $node ) {
			if ( ( $node['id'] ?? '' ) === HX_TEAM_SOURCE_ROW ) {
				$source = $node;
				return;
			}
			if ( ! empty( $node['elements'] ) ) {
				$find( $node['elements'] );
			}
		}
	};
	$find( $data );
	if ( ! $source ) {
		return false; // home layout changed; retry later
	}

	// Copy it with fresh element ids and Mira's content.
	preg_match_all( '/"id":"([0-9a-f]{7,8})"/', $raw, $found );
	$taken = array_flip( $found[1] );
	$bio   = '<p>' . implode( '</p><p>', $m['bio'] ) . '</p>';
	$img   = [
		'url'    => esc_url_raw( $m['image'] ),
		'id'     => (int) attachment_url_to_postid( $m['image'] ),
		'alt'    => $m['image_alt'],
		'source' => 'library',
	];
	$copy = function ( $node ) use ( &$copy, &$taken, $m, $bio, $img ) {
		$old = $node['id'] ?? '';
		do {
			$id = substr( md5( uniqid( '', true ) ), 0, 7 );
		} while ( isset( $taken[ $id ] ) );
		$taken[ $id ] = true;
		$node['id']   = $id;

		$type = $node['widgetType'] ?? '';
		if ( 'image' === $type ) {
			$node['settings']['image'] = $img;
		} elseif ( 'f3d95ae' === $old ) {
			$node['settings']['editor'] = '<p>' . esc_html( $m['photo_text'] ) . '</p>'; // same as Eduard: plain <p>, style comes from the widget
		} elseif ( 'a06c6e9' === $old ) {
			$node['settings']['title'] = $m['name'];
		} elseif ( '65d05af' === $old ) {
			$node['settings']['title'] = $m['role'];
		} elseif ( '5e86b71' === $old ) {
			$node['settings']['editor'] = $bio;
		}
		if ( ! empty( $node['elements'] ) ) {
			$node['elements'] = array_map( $copy, $node['elements'] );
		}
		return $node;
	};
	$row = $copy( $source );

	// Insert after Thomas's row (or after Eduard's if Thomas's is gone).
	$inserted = false;
	$insert   = function ( &$nodes ) use ( &$insert, &$inserted, $row ) {
		foreach ( [ HX_TEAM_AFTER_ROW, HX_TEAM_SOURCE_ROW ] as $after ) {
			foreach ( $nodes as $i => $node ) {
				if ( ( $node['id'] ?? '' ) === $after ) {
					array_splice( $nodes, $i + 1, 0, [ $row ] );
					$inserted = true;
					return;
				}
			}
		}
		foreach ( $nodes as &$node ) {
			if ( ! empty( $node['elements'] ) ) {
				$insert( $node['elements'] );
				if ( $inserted ) {
					return;
				}
			}
		}
	};
	$insert( $data );
	if ( ! $inserted ) {
		return false;
	}

	update_post_meta( $home_id, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );
	delete_post_meta( $home_id, '_elementor_css' );
	delete_post_meta( $home_id, '_elementor_element_cache' );
	hx_cu_flush_elementor();
	return true;
}
/**
 * The first version wrapped the text under the photo in <strong>; make it a plain <p> like Eduard's.
 */
function hx_cu_home_team_photo_text( $slug ) {
	$m       = hx_team_get_member( $slug );
	$home_id = (int) get_option( 'page_on_front' );
	$raw     = $home_id ? get_post_meta( $home_id, '_elementor_data', true ) : '';
	if ( ! $m || ! $raw ) {
		return true;
	}
	$data = json_decode( $raw, true );
	if ( ! is_array( $data ) ) {
		return false;
	}
	$plain   = '<p>' . esc_html( $m['photo_text'] ) . '</p>';
	$changed = false;
	$walk    = function ( &$nodes ) use ( &$walk, &$changed, $plain ) {
		foreach ( $nodes as &$node ) {
			if ( isset( $node['settings']['editor'] ) && is_string( $node['settings']['editor'] )
				&& false !== strpos( $node['settings']['editor'], '<strong>' )
				&& wp_strip_all_tags( $node['settings']['editor'] ) === wp_strip_all_tags( $plain ) ) {
				$node['settings']['editor'] = $plain;
				$changed                    = true;
			}
			if ( ! empty( $node['elements'] ) && is_array( $node['elements'] ) ) {
				$walk( $node['elements'] );
			}
		}
	};
	$walk( $data );
	if ( $changed ) {
		update_post_meta( $home_id, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );
		delete_post_meta( $home_id, '_elementor_css' );
		delete_post_meta( $home_id, '_elementor_element_cache' );
		hx_cu_flush_elementor();
	}
	return true;
}

add_filter( 'hx_content_updates', function ( $updates ) {
	$mira = hx_team_get_member( 'mira-aguilar' );
	if ( $mira && ! empty( $mira['image'] ) ) { // waits until the photo URL is set
		$updates['home:team-mira-aguilar'] = function () {
			return hx_cu_home_team_member( 'mira-aguilar' );
		};
		$updates['home:team-mira-photo-text'] = function () {
			return hx_cu_home_team_photo_text( 'mira-aguilar' );
		};
	}
	return $updates;
} );

/** Show what was rewritten in the admin mail-check banner. */
add_action( 'wp_footer', function () {
	if ( ! isset( $_GET['hx_mail_check'] ) || ! current_user_can( 'manage_options' ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return;
	}
	$log = (array) get_option( 'hx_forms_rewritten', [] );
	echo '<div style="position:fixed;right:16px;bottom:16px;z-index:99999;max-width:460px;max-height:40vh;overflow:auto;padding:14px 16px;background:#000;color:#fff;border:1px solid #d6b981;font:13px/1.5 system-ui,sans-serif;"><strong style="color:#d6b981;">Formularios de Elementor corregidos (karmasnack → ' . esc_html( HX_SITE_INBOX ) . ')</strong><br>';
	echo $log ? esc_html( implode( "\n", $log ) ) : 'Ninguno pendiente: ningún formulario apunta a karmasnack.';
	echo '</div>';
}, 20 );
