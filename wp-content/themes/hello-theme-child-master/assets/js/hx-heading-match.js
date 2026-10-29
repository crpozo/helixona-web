/* Helixona – Home: "Our core values." takes the exact size of the "Experience True
   Healing" heading (Oct 2026 review). Matches the headings by their text, so it works
   whatever Elementor widget builds them, and re-measures on resize (Elementor changes
   the size per breakpoint). Does nothing on pages without both headings. */
(function () {
	'use strict';

	var norm = function (el) { return (el.textContent || '').replace(/\s+/g, ' ').trim().toLowerCase(); };

	/** Deepest element whose whole text matches (so "Our core" + "values." spans count as one block). */
	function find(re) {
		var all = document.querySelectorAll('main *, .elementor *');
		for (var i = all.length - 1; i >= 0; i--) {
			var el = all[i];
			if (/^(SCRIPT|STYLE|SVG|svg)$/.test(el.tagName) || !re.test(norm(el))) { continue; }
			var childMatches = Array.prototype.some.call(el.children, function (c) { return re.test(norm(c)); });
			if (!childMatches) { return el; }
		}
		return null;
	}

	function run() {
		var source = find(/^experience true healing$/);
		var target = find(/^our core values\.?$/);
		if (!source || !target) { return; }

		var apply = function () {
			var cs = window.getComputedStyle(source);
			target.style.setProperty('font-size', cs.fontSize, 'important');
			target.style.setProperty('line-height', cs.lineHeight, 'important');
			target.querySelectorAll('*').forEach(function (el) {
				el.style.setProperty('font-size', 'inherit', 'important');
				el.style.setProperty('line-height', 'inherit', 'important');
			});
		};
		apply();

		var t;
		window.addEventListener('resize', function () { clearTimeout(t); t = setTimeout(apply, 150); });
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', run);
	} else {
		run();
	}
})();
