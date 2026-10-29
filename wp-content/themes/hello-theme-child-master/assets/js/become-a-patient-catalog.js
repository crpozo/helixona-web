/* Helixona – Become a Patient / Book a Treatment: service catalog (Dev Spec v1, Section 7).
   SAMPLE DATA. Service names follow the staging "Services & Tech" page; prices are the
   illustrative figures from the Oct 27 screen collection and are NOT approved. Durations,
   descriptions, preparation and consent text are placeholders until the catalog owner
   confirms them. requires_provider_clearance follows the proposed defaults in Section 7
   ([CLARIFY] rows are marked clearance_tbc). Replace this file with the catalog feed. */
(function (window) {
	'use strict';

	var PREP = 'Preparation instructions for this service will appear here once confirmed by the clinic.';
	var CONSENT = 'Informed consent text for this service will load here from the clinic\'s service catalog (versioned). Please read it carefully before booking.';

	function svc(id, name, short, price, bundles, opts) {
		opts = opts || {};
		return {
			id: id,
			name: name,
			short_description: short,
			category: opts.category || 'individual',
			includes: opts.includes || [],
			price_cents: price,
			bundle_options: bundles || [],
			duration_minutes: opts.duration || null,
			requires_provider_clearance: !!opts.clearance,
			clearance_tbc: !!opts.tbc,
			preparation_instructions: PREP,
			consent_text: CONSENT,
			consent_version: 'draft-0',
			active: true
		};
	}
	var b = function (s5, s10) { return [{ sessions: 5, price_cents: s5 }, { sessions: 10, price_cents: s10 }]; };

	var individual = [
		svc('salt-room', 'Halo Therapy (Salt Room)', 'Relax in our salt room while breathing dry, micronized salt air.', 6500, b(29000, 52000)),
		svc('nano-bath', 'Oxygen or Hydrogen Nano Bath', 'A full-body soak in water enriched with oxygen or hydrogen nanobubbles.', 15000, b(67500, 120000)),
		svc('nano-foot-bath', 'Oxygen / Hydrogen Nano Foot Bath', 'A seated foot soak with oxygen or hydrogen nanobubble water.', 6500, b(29000, 52000)),
		svc('hydrogen-inhalation', 'Hydrogen Inhalation', 'Breathe molecular hydrogen through a comfortable nasal cannula.', 6500, b(29000, 52000)),
		svc('biocharger', 'BioCharger Therapy', 'A relaxing session with the BioCharger energy platform.', 4000, b(18000, 32000)),
		svc('rife', 'Rife Frequency Therapy', 'A frequency-based session delivered through a Rife device.', 7500, b(33500, 60000)),
		svc('nanovi', 'NanoVi', 'Breathe humidified air from the NanoVi device during a quiet session.', 3500, b(15500, 28000)),
		svc('bemer', 'BEMER / PEMF Therapy', 'A pulsed electromagnetic field session while you rest.', null),
		svc('lightstem', 'LightStem Red Light Bed', 'A full-body red and near-infrared light session.', null),
		svc('lymphstar', 'LymphStar LET (Lymphatic Electrotherapy)', 'Lymphatic electrotherapy delivered by our team.', null, null, { tbc: true }),
		svc('microcurrent', 'Microcurrent Biofeedback (SCENAR / BioModulator)', 'Microcurrent biofeedback session with a trained practitioner.', null, null, { tbc: true }),
		svc('nmt', 'Neuromuscular Therapy (NMT)', 'Hands-on neuromuscular therapy session.', null, null, { tbc: true }),
		svc('erchonia', 'Erchonia Laser Therapy', 'Low-level laser therapy. Requires a provider evaluation first.', null, null, { clearance: true }),
		svc('eboo', 'EBOO Therapy', 'Extracorporeal blood oxygenation and ozonation. Requires a provider evaluation first.', null, null, { clearance: true }),
		svc('iv', 'IV Nutrient Therapy', 'Intravenous nutrient therapy. Requires a provider evaluation first.', null, null, { clearance: true }),
		svc('prp', 'Platelet-Rich Plasma (PRP)', 'PRP therapy. Requires a provider evaluation first.', null, null, { clearance: true })
	];

	var experiences = [
		svc('exp-reset', 'Reset', 'Salt Room + BioCharger in one visit.', 9500, null, { category: 'experience', includes: ['salt-room', 'biocharger'], compare: 10500 }),
		svc('exp-restore', 'Restore', 'Oxygen or Hydrogen Nano Bath + Salt Room in one visit.', 20500, null, { category: 'experience', includes: ['nano-bath', 'salt-room'] }),
		svc('exp-renewal', 'Renewal', 'Nano Bath + Hydrogen Inhalation + BioCharger in one visit.', 24500, null, { category: 'experience', includes: ['nano-bath', 'hydrogen-inhalation', 'biocharger'] })
	];
	experiences[0].compare_cents = 10500;
	experiences[1].compare_cents = 21500;
	experiences[2].compare_cents = 25500;

	var bundles = [
		svc('bundle-salt-5', '5 Salt Room Sessions', 'Five Halo Therapy sessions to use over time.', 29000, null, { category: 'bundle', includes: ['salt-room'] }),
		svc('bundle-salt-10', '10 Salt Room Sessions', 'Ten Halo Therapy sessions to use over time.', 52000, null, { category: 'bundle', includes: ['salt-room'] }),
		svc('bundle-mix-5', 'Mix & Match – 5 Sessions', 'Any five sessions of Salt Room, Nano Foot Bath or Hydrogen Inhalation.', 29500, null, { category: 'bundle', includes: ['salt-room', 'nano-foot-bath', 'hydrogen-inhalation'] })
	];
	bundles[0].sessions = 5; bundles[0].compare_cents = 32500;
	bundles[1].sessions = 10; bundles[1].compare_cents = 65000;
	bundles[2].sessions = 5;

	window.HxCatalog = {
		individual: individual,
		experience: experiences,
		bundle: bundles,
		all: individual.concat(experiences, bundles),
		byId: function (id) {
			for (var i = 0; i < this.all.length; i++) { if (this.all[i].id === id) { return this.all[i]; } }
			return null;
		},
		// Cancellation / no-show policy comes from configuration (fee amount pending: Q9).
		policy: 'If you need to cancel or reschedule, please let us know at least [notice window] before your appointment. Late cancellations and missed appointments may be charged a fee of [fee amount] to the card on file.',
		address: '114 Pacifica, Suite 130, Irvine, CA 92618'
	};
})(window);
