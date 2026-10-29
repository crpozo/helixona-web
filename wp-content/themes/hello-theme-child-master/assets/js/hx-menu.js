/* Helixona – mobile menu: rebuilds the Elementor off-canvas drawer into a flat,
   one-level accordion (deep sub-menus promoted to sections), with logo, CTAs
   and a proper close button. Reads the WordPress menu already in the DOM, so
   it stays in sync with Appearance → Menus. */
(function () {
	'use strict';

	function build() {
		var oc = document.querySelector('.e-off-canvas');
		if (!oc || oc.querySelector('.hx-mnav')) { return; }
		var nav = oc.querySelector('.elementor-widget-nav-menu ul.elementor-nav-menu');
		var content = oc.querySelector('.e-off-canvas__content') || oc.querySelector('.e-off-canvas__main');
		if (!nav || !content) { return; }

		var home = (document.querySelector('link[rel="home"]') || {}).href || '/';
		var path = window.location.pathname.replace(/\/+$/, '') || '/';

		function text(a) { return (a.textContent || '').replace(/\s+/g, ' ').trim(); }
		function isCurrent(href) {
			if (!href || href === '#') { return false; }
			try { return (new URL(href, window.location.href).pathname.replace(/\/+$/, '') || '/') === path; } catch (e) { return false; }
		}
		function parse(ul) {
			return Array.prototype.filter.call(ul.children, function (li) { return li.tagName === 'LI'; }).map(function (li) {
				var a = li.querySelector(':scope > a');
				var sub = li.querySelector(':scope > ul');
				return { label: a ? text(a) : '', href: a ? (a.getAttribute('href') || '#') : '#', children: sub ? parse(sub) : [] };
			});
		}

		/* Promote any second-level item that has its own children to a top-level section. */
		var sections = [];
		parse(nav).forEach(function (item) {
			var leaves = item.children.filter(function (c) { return !c.children.length; });
			var groups = item.children.filter(function (c) { return c.children.length; });
			sections.push({ label: item.label, href: item.href, items: leaves });
			groups.forEach(function (g) { sections.push({ label: g.label, href: g.href, items: g.children }); });
		});

		function el(tag, cls, attrs) {
			var n = document.createElement(tag);
			if (cls) { n.className = cls; }
			if (attrs) { Object.keys(attrs).forEach(function (k) { n.setAttribute(k, attrs[k]); }); }
			return n;
		}
		function link(item, cls) {
			var a = el('a', cls, { href: item.href });
			a.textContent = item.label;
			if (isCurrent(item.href)) { a.classList.add('is-current'); a.setAttribute('aria-current', 'page'); }
			return a;
		}
		function list(items) {
			var ul = el('ul', 'hx-mnav__list');
			items.forEach(function (item) {
				var li = el('li');
				if (item.children.length) {
					li.className = 'hx-mnav__group';
					var head = (item.href && item.href !== '#') ? link(item, 'hx-mnav__group-label') : el('span', 'hx-mnav__group-label');
					if (!head.textContent) { head.textContent = item.label; }
					li.appendChild(head);
					li.appendChild(list(item.children));
				} else {
					li.appendChild(link(item, 'hx-mnav__link'));
				}
				ul.appendChild(li);
			});
			return ul;
		}

		var root = el('nav', 'hx-mnav', { 'aria-label': 'Main menu' });

		/* Top bar: logo + close */
		var bar = el('div', 'hx-mnav__bar');
		var logoImg = document.querySelector('.elementor-location-header .elementor-widget-image img');
		var logo = el('a', 'hx-mnav__logo', { href: home, 'aria-label': 'Helixona' });
		if (logoImg) { var img = el('img', '', { src: logoImg.currentSrc || logoImg.src, alt: 'Helixona' }); logo.appendChild(img); }
		else { logo.textContent = 'HELIXONA'; }
		bar.appendChild(logo);
		var close = el('button', 'hx-mnav__close', { type: 'button', 'aria-label': 'Close menu' });
		close.innerHTML = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>';
		close.addEventListener('click', function () {
			var native = oc.querySelector('a[href*="off_canvas%3Aclose"], a[href*="off_canvas:close"]');
			if (native) { native.click(); } else { oc.setAttribute('aria-hidden', 'true'); }
		});
		bar.appendChild(close);
		root.appendChild(bar);

		/* CTAs: reuse the header buttons (text + href), de-duplicated */
		var seen = {}, ctas = el('div', 'hx-mnav__ctas');
		var buttons = Array.prototype.slice.call(document.querySelectorAll('.elementor-location-header .elementor-widget-button a'));
		var visible = buttons.filter(function (a) { return a.offsetParent !== null && a.getBoundingClientRect().width > 0; });
		(visible.length ? visible : buttons).forEach(function (a) {
			var t = text(a).toLowerCase();
			if (!t || seen[t] || Object.keys(seen).length >= 2) { return; }
			seen[t] = true;
			var b = el('a', 'hx-mnav__cta' + (Object.keys(seen).length === 1 ? ' hx-mnav__cta--ghost' : ' hx-mnav__cta--gold'), { href: a.getAttribute('href') || '#' });
			b.textContent = text(a);
			ctas.appendChild(b);
		});
		if (ctas.children.length) { root.appendChild(ctas); }

		/* Sections */
		var body = el('div', 'hx-mnav__sections');
		var openDefault = null;
		var panels = []; // [button, panel] pairs
		sections.forEach(function (sec, i) {
			var wrap = el('div', 'hx-mnav__sec');
			if (!sec.items.length) {
				wrap.appendChild(link(sec, 'hx-mnav__toggle hx-mnav__toggle--link'));
				body.appendChild(wrap);
				return;
			}
			var id = 'hx-mnav-panel-' + i;
			var btn = el('button', 'hx-mnav__toggle', { type: 'button', 'aria-expanded': 'false', 'aria-controls': id });
			btn.innerHTML = '<span></span><svg class="hx-mnav__chev" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 9l6 6 6-6" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>';
			btn.firstChild.textContent = sec.label;
			var panel = el('div', 'hx-mnav__panel', { id: id, hidden: '' });
			panel.appendChild(list(sec.items));
			panels.push([btn, panel]);
			if (!openDefault && panel.querySelector('.is-current')) { openDefault = [btn, panel]; }
			btn.addEventListener('click', function () { toggle(btn, panel); });
			wrap.appendChild(btn);
			wrap.appendChild(panel);
			body.appendChild(wrap);
		});
		root.appendChild(body);

		function toggle(btn, panel, force) {
			var open = typeof force === 'boolean' ? force : btn.getAttribute('aria-expanded') !== 'true';
			if (open) {
				panels.forEach(function (pair) {
					if (pair[0] !== btn) { pair[0].setAttribute('aria-expanded', 'false'); pair[1].hidden = true; }
				});
			}
			btn.setAttribute('aria-expanded', open ? 'true' : 'false');
			panel.hidden = !open;
			if (open) { setTimeout(function () { btn.scrollIntoView({ block: 'nearest', behavior: 'smooth' }); }, 50); }
		}
		content.appendChild(root);
		oc.classList.add('hx-mnav-ready');
		if (openDefault) { toggle(openDefault[0], openDefault[1], true); }
	}

	if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', build); } else { build(); }
	/* Elementor can render the off-canvas late; retry briefly. */
	var tries = 0, t = setInterval(function () { build(); if (document.querySelector('.hx-mnav') || ++tries > 20) { clearInterval(t); } }, 250);
})();
