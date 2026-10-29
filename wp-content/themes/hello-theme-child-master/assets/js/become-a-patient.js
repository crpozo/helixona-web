/* Helixona – Become a Patient / Book a Treatment (Dev Spec v1, Oct 29 2026).
   Screen 00 → Chronic A1–A8 (inquiry for human review, never a booking) or Health
   Optimization: H1 catalog → H5 time → B1 details → B2 consent → B3 note → B4 confirm,
   or provider-guided H9 → contact → H10 (PPO review or card hold) → survey link.
   #book-a-treatment opens straight on H1 (Individual Therapies). Catalog in
   become-a-patient-catalog.js; data handling in become-a-patient-api.js: contact details
   to WordPress, health answers only to AWS, card data never leaves the page.
   No console output, no storage. */
(function () {
	'use strict';

	var root = document.querySelector('.hxp');
	var shell = root && root.querySelector('[data-hxp-shell]');
	var CAT = window.HxCatalog;
	if (!shell || !CAT) { return; }

	var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	var HEADINGS = {
		path: 'What Brings You to Helixona?', a1: 'Healing Is a Team Effort', a2: 'Are You Ready to Take an Active Role in Your Recovery?',
		a3: "Let's Make Sure Your Care Is Accessible", a4: "Let's Review Your Insurance", a5: 'Just a Few More Details',
		a7: "Let's Help You Find the Right Care", a8: "Let's Review Your Care Options",
		h1: 'How Would You Like to Experience Helixona?', h5: "Let's Find a Time That Works for You",
		b1: 'Your Details', b2: 'Informed Consent', b3: 'Anything We Should Know?', b4: 'Review and Confirm',
		h9: "Let's Explore Your Health Goals", contact: "First, let's get to know you.", h10: 'How Would You Like to Cover Your Visit?'
	};
	var NEXT_LABEL = { a5: 'Submit My Inquiry', h1: 'Continue to Scheduling', b4: 'Confirm Booking' };
	var CAT_INTRO = {
		experience: 'Explore thoughtfully paired experiences designed to make time for yourself at Helixona. Select an experience to view what is included, approximate duration, preparation, and availability.',
		individual: 'Choose an eligible individual therapy. Bundle options appear alongside the single session so you can choose what works for you.',
		bundle: 'Enjoy more visits with a multi-session bundle. Select a bundle, then book your first eligible appointment. You can plan the remaining sessions later.'
	};
	var WINDOWS = { early: [8, 0], morning: [10, 0], afternoon: [13, 30], late: [16, 0] };
	var WINDOW_ORDER = ['early', 'morning', 'afternoon', 'late'];

	var s = {
		step: 'path', entry: 'patient', path: null, ho: 'direct',
		readiness: null, city: '', zip: '', transport: null, payer: null, docs: null, files: {},
		first: '', last: '', email: '', phone: '', dob: '', address: '', card: '', exp: '', cvc: '',
		tab: null, query: '', item: null, sessions: 1,
		slot: null, assist: false, calendar: false, prefDays: [], prefWindows: [], prefDate: '', filtered: false,
		consents: {}, occasion: '', policy: false,
		hoGoal: null, hoInterest: null, hoAttend: null, payment: null,
		touched: {}, attempted: {}, submitting: false, error: null, done: null, reference: ''
	};

	var $ = function (sel, ctx) { return (ctx || shell).querySelector(sel); };
	var $$ = function (sel, ctx) { return (ctx || shell).querySelectorAll(sel); };
	var esc = function (t) { return String(t == null ? '' : t).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); };
	var money = function (c) { return c == null ? 'Price on request' : '$' + (c / 100).toLocaleString('en-US', { minimumFractionDigits: c % 100 ? 2 : 0 }); };

	/* ---- step configuration (a small state machine) ---- */
	function steps() {
		var list = s.entry === 'book' ? [] : ['path'];
		if (s.path === 'chronic') {
			list.push('a1', 'a2');
			if (s.readiness === 'office_visits_only') { list.push('a7'); }
			else { list.push('a3', 'a4', s.payer === 'medical_only' ? 'a8' : 'a5'); }
		} else if (s.path === 'optimize' || s.entry === 'book') {
			if (s.ho === 'provider') { list.push('h9', 'contact', 'h10'); }
			else { list.push('h1', 'h5', 'b1', 'b2', 'b3', 'b4'); }
		}
		return list;
	}
	var TERMINAL = { a7: true, a8: true }; // informational end screens, no submit
	var SUBMIT = { a5: true, h10: true, b4: true };

	/* ---- selection helpers ---- */
	function item() { return s.item ? CAT.byId(s.item) : null; }
	function itemPrice() {
		var it = item();
		if (!it) { return null; }
		if (it.category === 'individual' && s.sessions > 1) {
			var opt = it.bundle_options.filter(function (o) { return o.sessions === s.sessions; })[0];
			return opt ? opt.price_cents : null;
		}
		return it.price_cents;
	}
	function itemLabel() {
		var it = item();
		if (!it) { return ''; }
		return it.name + (it.category === 'individual' && s.sessions > 1 ? ' · ' + s.sessions + ' sessions' : '');
	}
	function consentServices() {
		var it = item();
		if (!it) { return []; }
		return it.includes.length ? it.includes.map(function (id) { return CAT.byId(id); }).filter(Boolean) : [it];
	}

	/* ---- sample availability (ECW in production). Never Sunday. ---- */
	function isSunday(d) { return d.getDay() === 0; }
	function slotLabel(d) {
		return d.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' }) + ' · ' + d.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
	}
	function slots() {
		var out = [];
		var want = s.calendar ? 12 : 4;
		var days = s.filtered && s.prefDays.length ? s.prefDays.map(Number) : null;
		var wins = s.filtered && s.prefWindows.length ? s.prefWindows : null;
		var only = s.filtered && s.prefDate ? s.prefDate : null;
		var d = new Date();
		d.setHours(0, 0, 0, 0);
		if (only) { d = new Date(only + 'T00:00:00'); d.setDate(d.getDate() - 1); }
		for (var i = 0; i < 60 && out.length < want; i++) {
			d.setDate(d.getDate() + 1);
			if (isSunday(d)) { continue; } // hard rule: no Sunday appointments
			if (days && days.indexOf(d.getDay()) < 0) { continue; }
			if (only && i > 0) { break; }
			// Two sample openings per day, rotating through the windows.
			var pick = wins ? wins.slice() : [WINDOW_ORDER[i % 4], WINDOW_ORDER[(i + 2) % 4]];
			pick.sort(function (a, b) { return WINDOW_ORDER.indexOf(a) - WINDOW_ORDER.indexOf(b); });
			for (var w = 0; w < pick.length && out.length < want; w++) {
				var t = new Date(d);
				t.setHours(WINDOWS[pick[w]][0], WINDOWS[pick[w]][1]);
				out.push({ id: t.toISOString(), label: slotLabel(t) });
			}
		}
		return out;
	}

	/* ---- validation ---- */
	var emailOk = function () { return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(s.email.trim()); };
	function errors() {
		var e = {};
		if (!s.path) { e.path = true; }
		if (!s.first.trim()) { e.first = true; }
		if (!s.last.trim()) { e.last = true; }
		if (!emailOk()) { e.email = true; }
		if (s.phone.replace(/\D/g, '').length < 7) { e.phone = true; }
		if (!s.dob) { e.dob = true; }
		if (s.address.trim().length < 5) { e.address = true; }
		if (s.card.replace(/\D/g, '').length < 12 || !s.exp.trim() || !s.cvc.trim()) { e.card = true; }
		if (!s.readiness) { e.readiness = true; }
		if (!s.city.trim()) { e.city = true; }
		if (!/^\d{5}$/.test(s.zip.trim())) { e.zip = true; }
		if (!s.transport) { e.transport = true; }
		if (!s.payer) { e.payer = true; }
		if (!s.item) { e.item = true; }
		if (!s.slot && !s.assist) { e.slot = true; }
		if (s.prefDate && isSunday(new Date(s.prefDate + 'T00:00:00'))) { e.prefDate = true; }
		if (consentServices().some(function (x) { return !s.consents[x.id]; })) { e.consent = true; }
		if (!s.policy) { e.policy = true; }
		if (!s.hoGoal) { e.hoGoal = true; }
		if (!s.hoInterest) { e.hoInterest = true; }
		if (!s.hoAttend) { e.hoAttend = true; }
		if (!s.payment) { e.payment = true; }
		return e;
	}
	var STEP_FIELDS = {
		path: ['path'], a2: ['readiness'], a3: ['city', 'zip', 'transport'], a4: ['payer'],
		a5: ['first', 'last', 'email', 'phone'], h1: ['item'], h5: ['slot'],
		b1: ['first', 'last', 'email', 'phone', 'dob', 'address', 'card'], b2: ['consent'], b4: ['policy'],
		h9: ['hoGoal', 'hoInterest', 'hoAttend'], contact: ['first', 'last', 'email', 'phone'], h10: ['payment']
	};
	function stepFields(step) {
		var f = (STEP_FIELDS[step] || []).slice();
		if (step === 'h9' && s.hoInterest === 'therapies') { f = ['hoInterest']; }
		if (step === 'h10' && s.payment === 'card_hold') { f.push('card'); }
		return f;
	}
	function stepValid(step) {
		var e = errors();
		return stepFields(step).every(function (k) { return !e[k]; });
	}

	function go(step) {
		s.step = step;
		s.error = null;
		if (step === 'h1' && !s.tab) { s.tab = s.entry === 'book' ? 'individual' : 'experience'; }
		render(true);
	}

	function advance() {
		var list = steps();
		var i = list.indexOf(s.step);
		if (!stepValid(s.step)) {
			s.attempted[s.step] = true;
			render();
			return;
		}
		// H9: "Individual therapies or curated experiences" → direct selection, not the provider path.
		if (s.step === 'h9' && s.hoInterest === 'therapies') { s.ho = 'direct'; go('h1'); return; }
		if (SUBMIT[s.step]) { submit(); return; }
		if (i < list.length - 1) { go(list[i + 1]); }
	}

	/* ---- submission: only the branch the patient is on ---- */
	function submit() {
		var api = window.HxPatientAPI;
		var kind = s.path === 'chronic' && s.entry !== 'book' ? 'chronic' : s.ho === 'provider' ? 'provider' : 'booking';
		var path = kind === 'chronic' ? 'chronic' : kind === 'provider' ? 'ho-provider' : 'ho-therapies';
		var reference = api ? api.newReferenceId() : '';

		// Contact + logistics (no health details) → WordPress.
		var pii = {
			reference_id: reference,
			path: path,
			first_name: s.first.trim(), last_name: s.last.trim(), email: s.email.trim(), phone: s.phone.trim(),
			channels: [], times: [], provider: '', format: '',
			therapy: kind === 'booking' ? itemLabel() : '',
			slot: kind === 'booking' ? (s.assist ? 'Team to help schedule' : (slots().filter(function (x) { return x.id === s.slot; })[0] || {}).label || '') : ''
		};
		// Screening answers are health information → AWS only (skipped while the endpoint is off).
		var phi = null;
		if (kind === 'chronic') {
			phi = { reference_id: reference, readiness: s.readiness, city: s.city.trim(), zip: s.zip.trim(), transport: s.transport, payer: s.payer, docs: s.docs };
		} else if (kind === 'provider') {
			phi = { reference_id: reference, goal: s.hoGoal, interest: s.hoInterest, attend: s.hoAttend, payment: s.payment };
		} else if (s.occasion.trim()) {
			phi = { reference_id: reference, booking_note: s.occasion.trim(), dob: s.dob };
		}

		s.submitting = true;
		s.error = null;
		render();

		var finish = function () {
			s.submitting = false;
			s.done = kind;
			s.reference = reference;
			// Card fields are only part of the simulated checkout: clear them once done.
			s.card = ''; s.exp = ''; s.cvc = '';
			$$('[data-hxp-input="card"],[data-hxp-input="exp"],[data-hxp-input="cvc"]').forEach(function (el) { el.value = ''; });
			go('done');
			if (window.gtag) { window.gtag('event', 'become_a_patient_submit', { path: path }); } // path only, no personal data
		};
		var fail = function (msg) {
			s.submitting = false;
			s.error = msg || "We couldn't send your inquiry just now. Please try again in a moment.";
			render();
		};

		if (!api) { fail('Something went wrong. Please call (949) 257-2644.'); return; }
		if (root.getAttribute('data-mode') === 'mockup') {
			// Display-only mockup: payloads are built (structure ready) but nothing is sent anywhere.
			window.setTimeout(finish, 900);
			return;
		}

		(async function () {
			try {
				await api.sendPHIToAWS(phi, root.getAttribute('data-phi-endpoint') || '');
				var hp = $('[data-hxp-honeypot]');
				var res = await api.sendInquiry(root.getAttribute('data-ajax'), root.getAttribute('data-token'), hp ? hp.value : '', pii);
				if (!res || !res.success) {
					fail((res && res.data && res.data.message) || "We couldn't send your inquiry just now. Please try again in a moment.");
					return;
				}
				if (path !== 'chronic') { await api.sendHealthOptimizationPath(pii); }
				finish();
			} catch (err) {
				fail(err && err.message ? err.message + ' Please call (949) 257-2644.' : "We couldn't send your inquiry just now. Please try again in a moment.");
			}
		})();
	}

	/* ---- catalog (H1–H4) ---- */
	function matches(it, q) {
		if (!q) { return true; }
		var hay = [it.name, it.short_description].concat(it.includes.map(function (id) { var x = CAT.byId(id); return x ? x.name : ''; })).join(' ').toLowerCase();
		return q.split(/\s+/).every(function (w) { return hay.indexOf(w) >= 0; });
	}
	var TAG = { individual: 'Individual Therapy', experience: 'Curated Experience', bundle: 'Treatment Bundle' };

	function renderCatalog() {
		var q = s.query.trim().toLowerCase();
		var list = (q ? CAT.all : CAT[s.tab] || []).filter(function (it) { return it.active && matches(it, q); });
		var html = list.map(function (it) {
			var on = s.item === it.id;
			var price = money(it.price_cents);
			var meta = it.duration_minutes ? it.duration_minutes + ' min' : 'Duration TBC';
			var extra = '';
			if (it.category === 'experience' && it.compare_cents) {
				extra = '<span class="hxp-item__save">Individual total ' + money(it.compare_cents) + ' · save ' + money(it.compare_cents - it.price_cents) + '</span>';
			}
			if (it.category === 'bundle' && it.sessions && it.price_cents) {
				extra = '<span class="hxp-item__save">' + money(Math.round(it.price_cents / it.sessions)) + ' per session' + (it.compare_cents ? ' · save ' + money(it.compare_cents - it.price_cents) : '') + '</span>';
			}
			if (it.category === 'individual' && it.bundle_options.length && it.price_cents) {
				extra = '<span class="hxp-item__save">5 and 10-session bundles available</span>';
			}
			var body = '';
			if (it.requires_provider_clearance) {
				body = '<div class="hxp-item__gate"><span>Requires provider evaluation.</span> <button type="button" class="hxp-link" data-hxp-clearance>Start here</button></div>';
			} else if (on) {
				var inc = it.includes.length ? '<p><span class="hxp-label">Includes</span><br>' + it.includes.map(function (id) { var x = CAT.byId(id); return esc(x ? x.name : id); }).join(' · ') + '</p>' : '';
				var opts = '';
				if (it.category === 'individual' && it.bundle_options.length && it.price_cents) {
					opts = '<div class="hxp-group"><span class="hxp-label">Sessions</span><div class="hxp-wrap-row">' +
						[{ sessions: 1, price_cents: it.price_cents }].concat(it.bundle_options).map(function (o) {
							var per = o.sessions > 1 ? ' (' + money(Math.round(o.price_cents / o.sessions)) + '/session)' : '';
							return '<button type="button" class="hxp-chip" aria-pressed="' + (s.sessions === o.sessions) + '" data-hxp-sessions="' + o.sessions + '"><span aria-hidden="true" class="hxp-dot"></span><span>' + (o.sessions === 1 ? 'Single session' : o.sessions + ' sessions') + ' · ' + money(o.price_cents) + per + '</span></button>';
						}).join('') + '</div></div>';
				}
				body = '<div class="hxp-item__more">' + inc + opts +
					'<p><span class="hxp-label">Preparation</span><br>' + esc(it.preparation_instructions) + '</p>' +
					'<p><span class="hxp-label">Availability</span><br>The earliest available times are shown on the next step.' + (it.category === 'experience' ? ' Your experience is booked as one arrival time.' : '') + '</p></div>';
			} else if (it.clearance_tbc) {
				body = '';
			}
			return '<div role="listitem" class="hxp-item' + (on ? ' is-on' : '') + (it.requires_provider_clearance ? ' is-gated' : '') + '">' +
				'<button type="button" class="hxp-item__head" aria-pressed="' + on + '"' + (it.requires_provider_clearance ? ' disabled' : '') + ' data-hxp-item="' + esc(it.id) + '">' +
				'<span class="hxp-therapy__main">' + (q ? '<span class="hxp-item__tag">' + TAG[it.category] + '</span>' : '') +
				'<span class="hxp-serif hxp-therapy__name">' + esc(it.name) + '</span>' +
				'<span class="hxp-therapy__desc">' + esc(it.short_description) + '</span>' + extra + '</span>' +
				'<span class="hxp-therapy__meta">' + (it.requires_provider_clearance ? '' : '<span class="hxp-therapy__price">' + price + '</span><span class="hxp-therapy__duration">' + meta + '</span>') + '</span>' +
				'</button>' + body + '</div>';
		}).join('');
		if (!list.length) { html = '<p class="hxp-small">No results for “' + esc(s.query) + '”. Try another word, or <button type="button" class="hxp-link" data-hxp-clear>clear the search</button>.</p>'; }
		$('[data-hxp-catalog]').innerHTML = html;
		$$('[data-hxp-tab]').forEach(function (t) {
			var sel = !q && t.getAttribute('data-hxp-tab') === s.tab;
			t.setAttribute('aria-selected', sel ? 'true' : 'false');
		});
		bind('catIntro', q ? 'Showing results across Curated Experiences, Individual Therapies and Treatment Bundles.' : CAT_INTRO[s.tab] || '');
	}

	function renderSlots() {
		var list = slots();
		$('[data-hxp-slots]').innerHTML = list.map(function (x) {
			var p = x.label.split(' · ');
			return '<button type="button" class="hxp-chip" aria-pressed="' + (s.slot === x.id) + '" data-hxp-slot="' + esc(x.id) + '"><span aria-hidden="true" class="hxp-dot"></span><span class="hxp-slot"><span class="hxp-slot__day">' + esc(p[0]) + '</span><span class="hxp-slot__time">' + esc(p[1]) + ' · Irvine</span></span></button>';
		}).join('') || '<p class="hxp-small">No times match those preferences. Try fewer filters or ask our team to help.</p>';
		$('[data-hxp-calendar]').textContent = s.calendar ? 'Show Fewer Times' : 'View Full Calendar';
	}

	function renderConsents() {
		var wrap = $('[data-hxp-consents]');
		wrap.innerHTML = consentServices().map(function (x) {
			return '<div class="hxp-consent"><span class="hxp-serif hxp-consent__title">' + esc(x.name) + '</span>' +
				'<div class="hxp-consent__text" tabindex="0">' + esc(x.consent_text) + '<br><br><span class="hxp-small">Version ' + esc(x.consent_version) + '</span></div>' +
				'<label class="hxp-check"><input type="checkbox" data-hxp-consent="' + esc(x.id) + '"' + (s.consents[x.id] ? ' checked' : '') + '><span>I have read and understand this consent.</span></label></div>';
		}).join('');
	}

	/* ---- rendering ---- */
	function pad(n) { return n < 10 ? '0' + n : '' + n; }

	function bind(name, text) {
		$$('[data-hxp-bind="' + name + '"]').forEach(function (el) { el.textContent = text; });
	}
	function show(name, on) {
		$$('[data-hxp-show="' + name + '"]').forEach(function (el) { el.hidden = !on; });
	}

	function render(stepChanged) {
		var list = steps();
		var isDone = s.step === 'done';
		var idx = Math.max(0, list.indexOf(s.step));
		var total = list.length;
		var current = isDone ? total : idx + 1;
		var e = errors();
		var att = s.attempted[s.step];

		// Progress
		$('[data-hxp-progress]').hidden = isDone;
		bind('stepLabel', 'Step ' + pad(current) + (total > 1 ? ' of ' + pad(total) : '')); // the total is known once a path is chosen
		bind('flowLabel', s.entry === 'book' && s.ho !== 'provider' ? 'Book a Treatment' : s.path === 'chronic' ? 'Chronic / Complex Chronic Recovery' : s.path === 'optimize' || s.entry === 'book' ? 'Health Optimization' : 'Become a Patient');
		var bars = $('[data-hxp-bars]');
		bars.setAttribute('aria-valuemax', total);
		bars.setAttribute('aria-valuenow', current);
		if (bars.children.length !== total) {
			bars.innerHTML = '';
			for (var b = 0; b < total; b++) { var bar = document.createElement('div'); bar.className = 'hxp-bar'; bars.appendChild(bar); }
		}
		Array.prototype.forEach.call(bars.children, function (bar, i) { bar.classList.toggle('is-on', i < current); });

		// Steps and confirmations
		$$('[data-hxp-step]').forEach(function (el) { el.hidden = el.getAttribute('data-hxp-step') !== s.step; });
		$$('[data-hxp-done]').forEach(function (el) { el.hidden = !(isDone && el.getAttribute('data-hxp-done') === s.done); });
		$('[data-hxp-restart-wrap]').hidden = !isDone;

		// Choice state
		$$('[data-hxp-key]').forEach(function (el) {
			var key = el.getAttribute('data-hxp-key');
			var val = el.getAttribute('data-hxp-val');
			var on = el.hasAttribute('data-hxp-multi') ? s[key].indexOf(val) >= 0 : s[key] === val;
			el.setAttribute('aria-pressed', on ? 'true' : 'false');
		});

		// Errors: only on the visible step, after a blur or a Continue attempt
		var stepEl = $('[data-hxp-step="' + s.step + '"]');
		if (stepEl) {
			$$('[data-hxp-err]', stepEl).forEach(function (el) {
				var k = el.getAttribute('data-hxp-err');
				var bad = !!e[k] && (k === 'prefDate' || !!att || !!s.touched[k]) && (k === 'prefDate' || stepFields(s.step).indexOf(k) >= 0);
				el.hidden = !bad;
				var input = $('[data-hxp-input="' + k + '"]', stepEl);
				if (input) { input.classList.toggle('is-invalid', bad); input.setAttribute('aria-invalid', bad ? 'true' : 'false'); }
			});
		}

		// Conditional blocks
		show('docsUpload', s.docs === 'upload');
		show('payPPO', s.payment === 'ppo_review');
		show('payCard', s.payment === 'card_hold');
		show('toTherapies', s.hoInterest === 'therapies');
		show('assist', s.assist);

		// Screen content
		if (s.step === 'h1') { renderCatalog(); }
		if (s.step === 'h5') { renderSlots(); }
		if (s.step === 'b2' && stepChanged) { renderConsents(); }

		// Text bindings
		var it = item();
		var slot = slots().filter(function (x) { return x.id === s.slot; })[0];
		bind('first', s.first.trim());
		bind('email', s.email.trim());
		bind('fullName', (s.first + ' ' + s.last).trim());
		bind('itemName', it ? itemLabel() : '[Selection]');
		bind('itemCoord', it && it.category === 'experience' ? ' — one arrival time, with rooms, equipment and staff coordinated.' : '.');
		bind('itemPrice', money(itemPrice()));
		bind('slotLabel', s.assist ? 'Our team will help you schedule' : slot ? slot.label : (s.slotLabel || '[Date and time]'));
		bind('cardLabel', s.card.replace(/\D/g, '').length >= 4 ? '•••• ' + s.card.replace(/\D/g, '').slice(-4) : '—');
		bind('policy', CAT.policy);
		bind('occasionCount', String(s.occasion.length));
		bind('paymentLabel', s.payment === 'ppo_review' ? 'PPO insurance · benefits review' : s.payment === 'card_hold' ? 'Credit card hold' : '—');
		bind('findNote', s.filtered ? 'Showing the next times that match your preferences.' : '');
		bind('reference', s.reference || '[REFERENCE]');
		Object.keys(s.files).forEach(function (k) { bind('files_' + k, s.files[k]); });
		var find = $('[data-hxp-find]');
		if (find) { find.disabled = !!e.prefDate; }

		// Error box
		$('[data-hxp-alert]').hidden = !s.error;
		bind('errorText', s.error || '');

		// Navigation
		var canBack = idx > 0 && !isDone;
		$('[data-hxp-nav]').hidden = isDone;
		$('[data-hxp-back]').hidden = !canBack;
		$('[data-hxp-noback]').hidden = canBack;
		var next = $('[data-hxp-next]');
		next.hidden = !!TERMINAL[s.step];
		next.textContent = s.submitting ? 'Sending…' : NEXT_LABEL[s.step] || 'Continue';
		next.disabled = s.submitting;
		next.setAttribute('aria-busy', s.submitting ? 'true' : 'false');

		$('[data-hxp-announce]').textContent = isDone ? 'Submission complete.' : 'Step ' + current + ' of ' + total + ': ' + (HEADINGS[s.step] || '');

		if (stepChanged) {
			var r = shell.getBoundingClientRect();
			if (r.top < 0 || r.top > window.innerHeight * 0.6) {
				shell.scrollIntoView({ block: 'start', behavior: reduce ? 'auto' : 'smooth' });
			}
		}
	}

	/* ---- events ---- */
	$$('[data-hxp-key]').forEach(function (el) {
		el.addEventListener('click', function () {
			var key = el.getAttribute('data-hxp-key');
			var val = el.getAttribute('data-hxp-val');
			if (el.hasAttribute('data-hxp-multi')) {
				var i = s[key].indexOf(val);
				if (i >= 0) { s[key].splice(i, 1); } else { s[key].push(val); }
			} else {
				s[key] = val;
			}
			render();
		});
	});

	// The same field can live on several screens (A5, B1, contact): keep every copy in sync.
	$$('[data-hxp-input]').forEach(function (el) {
		var key = el.getAttribute('data-hxp-input');
		el.addEventListener('input', function () {
			s[key] = el.value;
			$$('[data-hxp-input="' + key + '"]').forEach(function (o) { if (o !== el) { o.value = el.value; } });
			render();
		});
		el.addEventListener('blur', function () { s.touched[key] = true; render(); });
	});

	$$('[data-hxp-file]').forEach(function (el) {
		el.addEventListener('change', function () {
			var names = Array.prototype.map.call(el.files || [], function (f) { return f.name; });
			s.files[el.getAttribute('data-hxp-file')] = names.length ? names.join(', ') + ' (selected — not uploaded in this preview)' : 'Images or PDF · front and back of card';
			render();
		});
	});

	// Catalog: search, tabs, selection, sessions, clearance gate
	$('[data-hxp-search]').addEventListener('input', function (ev) { s.query = ev.target.value; render(); });
	$$('[data-hxp-tab]').forEach(function (t) {
		t.addEventListener('click', function () {
			s.tab = t.getAttribute('data-hxp-tab');
			s.query = '';
			$('[data-hxp-search]').value = '';
			render();
		});
	});
	$('[data-hxp-catalog]').addEventListener('click', function (ev) {
		var head = ev.target.closest('[data-hxp-item]');
		var sess = ev.target.closest('[data-hxp-sessions]');
		if (ev.target.closest('[data-hxp-clearance]')) {
			// Clearance-required therapy → Become a Patient flow (provider evaluation first).
			s.entry = 'patient'; s.path = null; s.ho = 'direct'; s.item = null;
			go('path');
			return;
		}
		if (ev.target.closest('[data-hxp-clear]')) { s.query = ''; $('[data-hxp-search]').value = ''; render(); return; }
		if (sess) { s.sessions = Number(sess.getAttribute('data-hxp-sessions')); render(); return; }
		if (head && !head.disabled) {
			var id = head.getAttribute('data-hxp-item');
			if (s.item !== id) { s.item = id; s.sessions = 1; s.slot = null; s.consents = {}; } // default is ONE session, never a bundle
			render();
		}
	});
	$('[data-hxp-provider]').addEventListener('click', function () { s.ho = 'provider'; if (!s.path) { s.path = 'optimize'; } go('h9'); });

	// Scheduling
	$('[data-hxp-slots]').addEventListener('click', function (ev) {
		var b = ev.target.closest('[data-hxp-slot]');
		if (!b) { return; }
		s.slot = b.getAttribute('data-hxp-slot');
		s.assist = false;
		render();
	});
	$('[data-hxp-find]').addEventListener('click', function () { s.filtered = true; s.slot = null; render(); });
	$('[data-hxp-calendar]').addEventListener('click', function () { s.calendar = !s.calendar; render(); });
	$('[data-hxp-assist]').addEventListener('click', function () { s.assist = true; s.slot = null; render(); });

	// Consent, policy, B4
	$('[data-hxp-consents]').addEventListener('change', function (ev) {
		var id = ev.target.getAttribute('data-hxp-consent');
		if (id) { s.consents[id] = ev.target.checked; render(); }
	});
	$('[data-hxp-check="policy"]').addEventListener('change', function (ev) { s.policy = ev.target.checked; render(); });

	// A7 "Reconsider My Options" → A2
	$$('[data-hxp-goto]').forEach(function (b) {
		b.addEventListener('click', function () { s.readiness = null; go(b.getAttribute('data-hxp-goto')); });
	});

	$('[data-hxp-next]').addEventListener('click', function () { if (!s.submitting) { advance(); } });
	$('[data-hxp-back]').addEventListener('click', function () {
		if (s.step === 'h9' && s.entry === 'book') { s.ho = 'direct'; go('h1'); return; }
		var list = steps();
		var i = list.indexOf(s.step);
		if (s.step === 'h9') { s.ho = 'direct'; go('h1'); return; }
		if (i > 0) { go(list[i - 1]); }
	});
	$('[data-hxp-restart]').addEventListener('click', function () {
		s.step = s.entry === 'book' ? 'h1' : 'path'; s.done = null; s.submitting = false; s.error = null; s.attempted = {};
		s.item = null; s.slot = null; s.assist = false; s.consents = {}; s.policy = false; s.occasion = '';
		$('[data-hxp-check="policy"]').checked = false;
		render(true);
	});

	/* Deep links: #chronic, #health-optimization, #book-a-treatment (Flow B: straight to therapies) */
	var hash = (window.location.hash || '').replace('#', '');
	if (hash === 'chronic' || hash === 'chronic-complex') { s.path = 'chronic'; s.step = 'a1'; }
	if (hash === 'health-optimization') { s.path = 'optimize'; s.tab = 'experience'; s.step = 'h1'; }
	if (hash === 'book-a-treatment' || hash === 'book') { s.entry = 'book'; s.tab = 'individual'; s.step = 'h1'; }

	render();
})();
