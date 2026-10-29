/* Helixona – Job landing: sticky "Apply" bar, smooth anchors, AJAX application form. */
(function () {
	'use strict';

	var hero   = document.getElementById('hx-hero');
	var apply  = document.getElementById('hx-apply');
	var sticky = document.getElementById('hx-sticky');

	/* ---- Sticky bar: visible once the hero is gone, hidden over the apply section ---- */
	if (hero && sticky) {
		var heroVisible = true, applyVisible = false, shown = false, hideTimer = null;

		var render = function () {
			var show = !heroVisible && !applyVisible;
			if (show === shown) { return; }
			shown = show;
			window.clearTimeout(hideTimer);
			if (show) {
				sticky.hidden = false;
				void sticky.offsetWidth;
				sticky.classList.add('is-visible');
			} else {
				sticky.classList.remove('is-visible');
				hideTimer = window.setTimeout(function () { if (!shown) { sticky.hidden = true; } }, 260);
			}
		};
		var refresh = function () {
			var vh = window.innerHeight || document.documentElement.clientHeight;
			heroVisible  = hero.getBoundingClientRect().bottom > 80;
			applyVisible = apply ? apply.getBoundingClientRect().top < vh - 80 : false;
			render();
		};

		if ('IntersectionObserver' in window) {
			new IntersectionObserver(function (e) { heroVisible = e[0].isIntersecting; render(); }, { threshold: 0.1 }).observe(hero);
			if (apply) {
				new IntersectionObserver(function (e) { applyVisible = e[0].isIntersecting; render(); }, { threshold: 0.1 }).observe(apply);
			}
		}
		window.addEventListener('scroll', refresh, { passive: true });
		window.addEventListener('resize', refresh);
		refresh();
	}

	/* ---- Smooth scroll for in-page links ---- */
	document.querySelectorAll('[data-hx-scroll]').forEach(function (link) {
		link.addEventListener('click', function (e) {
			var target = document.querySelector(link.getAttribute('href'));
			if (!target) { return; }
			e.preventDefault();
			target.scrollIntoView({ behavior: 'smooth', block: 'start' });
			if (link.getAttribute('href') === '#hx-apply') {
				var first = document.querySelector('#hx-apply-form input[name="hx_name"]');
				if (first) { window.setTimeout(function () { first.focus({ preventScroll: true }); }, 600); }
			}
		});
	});

	/* ---- Application form ---- */
	var form = document.getElementById('hx-apply-form');
	if (!form || !window.FormData || !window.fetch) { return; }

	var alertBox = form.querySelector('.hx-form__alert');
	var success  = document.querySelector('.hx-form__success');
	var button   = form.querySelector('button[type="submit"]');
	var maxBytes = (parseInt(form.getAttribute('data-max-mb'), 10) || 10) * 1024 * 1024;
	var ajaxUrl  = form.getAttribute('data-ajax') || (window.hxJob && window.hxJob.ajaxUrl);

	var showError = function (msg) {
		alertBox.textContent = msg;
		alertBox.hidden = false;
		alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
	};
	var clearError = function () {
		alertBox.hidden = true;
		alertBox.textContent = '';
	};

	var validate = function () {
		var fields = form.querySelectorAll('[required]');
		for (var i = 0; i < fields.length; i++) {
			var f = fields[i];
			f.classList.remove('is-invalid');
			if (f.type === 'file') {
				if (!f.files || !f.files.length) { f.classList.add('is-invalid'); return 'Please attach your resume (PDF or Word).'; }
				var file = f.files[0];
				if (file.size > maxBytes) { f.classList.add('is-invalid'); return 'Your resume is too large. Please upload a file under ' + Math.round(maxBytes / 1048576) + ' MB.'; }
				if (!/\.(pdf|docx?)$/i.test(file.name)) { f.classList.add('is-invalid'); return 'Please upload your resume as a PDF or Word document.'; }
			} else if (!f.value.trim() || (f.type === 'email' && !f.checkValidity())) {
				f.classList.add('is-invalid');
				f.focus();
				return f.type === 'email' ? 'Please enter a valid e-mail address.' : 'Please fill in the required fields.';
			}
		}
		return '';
	};

	form.addEventListener('submit', function (e) {
		e.preventDefault();
		clearError();

		var problem = validate();
		if (problem) { showError(problem); return; }

		form.classList.add('is-busy');
		button.disabled = true;
		var label = button.textContent;
		button.textContent = 'Sending…';

		fetch(ajaxUrl, {
			method: 'POST',
			body: new FormData(form),
			credentials: 'same-origin'
		})
		.then(function (r) { return r.json().catch(function () { return { success: false }; }); })
		.then(function (res) {
			if (res && res.success) {
				form.hidden = true;
				if (success) {
					success.hidden = false;
					success.scrollIntoView({ behavior: 'smooth', block: 'center' });
				}
				return;
			}
			showError((res && res.data && res.data.message) || 'Something went wrong. Please try again.');
		})
		.catch(function () {
			showError('We could not reach the server. Please check your connection and try again.');
		})
		.then(function () {
			form.classList.remove('is-busy');
			button.disabled = false;
			button.textContent = label;
		});
	});
})();
