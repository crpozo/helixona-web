# Helixona Website

WordPress child theme for helixona.com (parent theme: Hello Elementor).

## Structure

- `wp-content/themes/hello-theme-child-master/`: the child theme (upload as a zip under Appearance → Themes)
  - `page-templates/become-a-patient.php`: Become a Patient / Book a Treatment intake (Dev Spec v1)
  - `assets/js/become-a-patient.js`: step logic (Screen 00, A1–A8, H1–H5, B1–B4, H9–H10)
  - `assets/js/become-a-patient-catalog.js`: service catalog (sample data until the clinic catalog is connected)
  - `assets/js/become-a-patient-api.js`: submission (contact → WordPress, health answers → AWS only)
  - `inc/become-a-patient.php`: page setup, AJAX handler and e-mails
- `local-wp/`: Docker setup to run WordPress locally with the theme mounted

## Run locally

```bash
cd local-wp && docker compose up -d
```

Then open http://localhost:8080.

## Entry points

- `/become-a-patient/`: Become a Patient (Screen 00)
- `/become-a-patient/#book-a-treatment`: Book a Treatment (straight to Individual Therapies)
- `/become-a-patient/#chronic`, `#health-optimization`: deep links into each branch

The form runs in display-only mode by default (`HX_PATIENT_MOCKUP`): nothing is sent, charged or uploaded.
Turn real sending on with `define( 'HX_PATIENT_MOCKUP', false );` in `wp-config.php`.
