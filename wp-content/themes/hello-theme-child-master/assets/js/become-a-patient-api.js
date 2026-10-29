/* Helixona – Become a Patient: data service (HIPAA-ready structure).
 *
 * Every submission is split into two objects that never travel together:
 *   pii_payload  – identification / contact (name, email, phone, preferences).
 *                  Goes to WordPress (admin-ajax) so the coordinator is notified.
 *   phi_payload  – health information (what brings them in and their notes).
 *                  NEVER goes to WordPress, its logs or e-mail. It is sent from the
 *                  browser straight to AWS (API Gateway → EC2 with Nitro Enclaves),
 *                  and only when that endpoint is configured (feature flag).
 * Both carry the same reference_id (generated here) so the coordinator can match
 * the contact request with the clinical intake inside the secure system.
 *
 * Client rules: no console.* of form data, nothing written to localStorage,
 * sessionStorage or cookies, HTTPS required for any PHI request.
 */
(function (window) {
	'use strict';

	/* "Health Optimization Path" link sent to B1 and B2. Update here when confirmed. */
	const HO_PATH_URL = "URL_PENDIENTE";

	/* Pipeline each path starts (read by the coordinator / future backend). */
	const FLOWS = {
		'chronic': {
			flow: 'chronic_complex',
			screening_call_required: true,
			send_link: 'chronic_complex_onboarding',
			pipeline: ['screening_call', 'onboarding_link', 'patient_interview', 'eoc_spd', 'admissions_committee_review'],
			notify: 'patient_coordinator'
		},
		'ho-therapies': {
			flow: 'health_optimization_therapies',
			screening_call_required: false,
			send_link: 'health_optimization_path',
			online_booking: 'cash_modalities_cc',
			provider_review: false,
			notify: 'patient_coordinator'
		},
		'ho-provider': {
			flow: 'health_optimization_provider_plan',
			screening_call_required: false,
			send_link: 'health_optimization_path',
			provider: 'Mira',
			notify: 'new_patient_coordinator'
		}
	};

	/** Random, non-identifying reference shared by the PII and PHI records. */
	function newReferenceId() {
		var rand = (window.crypto && window.crypto.randomUUID) ? window.crypto.randomUUID()
			: Array.prototype.map.call(window.crypto.getRandomValues(new Uint8Array(16)), function (b) { return ('0' + b.toString(16)).slice(-2); }).join('');
		return 'HX-' + rand.replace(/-/g, '').slice(0, 12).toUpperCase();
	}

	/**
	 * Current integration: notify the coordinator through WordPress.
	 * Sends ONLY the PII + reference_id + preferences (+ the anti-spam token and honeypot).
	 * Health details (what brings them in, notes) are never part of this request.
	 */
	function sendInquiry(url, token, honeypot, pii) {
		var body = new FormData();
		body.append('action', 'hx_patient_inquiry');
		body.append('hx_token', token || '');
		body.append('hx_website', honeypot || '');
		body.append('hx_reference', pii.reference_id);
		body.append('hx_path', pii.path);
		body.append('hx_first', pii.first_name);
		body.append('hx_last', pii.last_name);
		body.append('hx_email', pii.email);
		body.append('hx_phone', pii.phone);
		body.append('hx_channels', (pii.channels || []).join(','));
		body.append('hx_times', (pii.times || []).join(','));
		body.append('hx_provider', pii.provider || '');
		body.append('hx_format', pii.format || '');
		body.append('hx_therapy', pii.therapy || '');
		body.append('hx_slot', pii.slot || '');

		return fetch(url, { method: 'POST', body: body, credentials: 'same-origin', cache: 'no-store' })
			.then(function (r) { return r.json().catch(function () { return { success: false }; }); });
	}

	/**
	 * PHI → AWS (MOCK until the endpoint exists).
	 *
	 * Target architecture (to be built in AWS under a signed BAA, HIPAA-eligible services only):
	 *   Browser ──HTTPS/TLS 1.2+──▶ Amazon API Gateway (regional, WAF, throttling, no request/body logging)
	 *     ──▶ private integration (VPC link / NLB) ──▶ EC2 parent instance (no PHI handling, no PHI logs)
	 *       ──vsock──▶ AWS Nitro Enclave: decrypts and processes the PHI in isolation. The KMS key policy
	 *                  only allows decrypt with a valid enclave attestation document (PCR values), so no
	 *                  operator or administrator can read the plain-text PHI outside the enclave.
	 *   Recommended: encrypt the PHI in the browser too (envelope / public key from the enclave) so API
	 *   Gateway and the parent instance only ever see ciphertext.
	 *
	 * @param {Object|null} phi      phi_payload ({ reference_id, support_needed, notes } or { reference_id, scheduling_notes })
	 * @param {string}      endpoint AWS API Gateway URL from data-phi-endpoint ('' = feature off)
	 */
	async function sendPHIToAWS(phi, endpoint) {
		if (!phi) { return { sent: false, reason: 'no_phi' }; }
		if (!endpoint) { return { sent: false, reason: 'phi_endpoint_not_configured' }; } // feature flag off
		var url;
		try { url = new URL(endpoint); } catch (e) { throw new Error('Invalid secure endpoint.'); }
		if (url.protocol !== 'https:' || window.location.protocol !== 'https:') {
			throw new Error('A secure (HTTPS) connection is required to send health information.');
		}
		var res = await fetch(url.toString(), {
			method: 'POST',
			mode: 'cors',
			credentials: 'omit',          // no cookies to AWS
			cache: 'no-store',
			referrerPolicy: 'no-referrer',
			headers: { 'Content-Type': 'application/json', 'X-Hx-Reference': phi.reference_id },
			body: JSON.stringify(phi)
		});
		if (!res.ok) { throw new Error('Secure submission failed.'); }
		return { sent: true };
	}

	/**
	 * "Health Optimization Path" link (MOCK). Simulates the automatic send for B1/B2;
	 * the real e-mail will go out from the backend (WordPress today: HX_HO_PATH_URL, or the
	 * AWS pipeline later) using the same reference_id. Never sends PHI.
	 */
	async function sendHealthOptimizationPath(pii) {
		await new Promise(function (r) { setTimeout(r, 300); });
		return { simulated: true, reference_id: pii.reference_id, link: HO_PATH_URL, link_configured: HO_PATH_URL !== 'URL_PENDIENTE' };
	}

	window.HxPatientAPI = {
		HO_PATH_URL: HO_PATH_URL,
		FLOWS: FLOWS,
		newReferenceId: newReferenceId,
		sendInquiry: sendInquiry,
		sendPHIToAWS: sendPHIToAWS,
		sendHealthOptimizationPath: sendHealthOptimizationPath
	};
})(window);
