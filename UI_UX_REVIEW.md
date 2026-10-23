# CRM UI consistency review — 23 October 2026

Shared screen components now load through Admin and Employee headers and the Lead portal entry point. The source inventory found 111 Admin/Employee pages using those headers, including 92 pages with forms. This is a source coverage count, not a claim that every authenticated page was exercised.

Changes:

- Corrected heading/back-link source order on 23 pages, including assets, tasks, projects, finance and HR. Page headings appear on the left, with navigation alongside or below them on mobile.
- Added shared card, heading, label, field, spacing, focus, validation and button styles in `dist/css/crm-ui.css`.
- Primary buttons and submit inputs share the same appearance. Cancel is secondary. Recognized actions share icons across buttons, links and submit inputs; icon-only row actions have consistent sizes and accessible names.
- Removed decorative label/section icons and noninteractive field icons while retaining calendar controls and meaningful currency units.
- Converted legacy Employee Add/Profile span captions to labels, associated unambiguous labels with controls, and added persistent labels to client mini forms.
- Compact master forms align fields and actions and wrap on mobile. Dynamic table rows and edit/add action changes receive the shared presentation.

Validation:

- PHP syntax checks passed for all 28 PHP files changed in this review; JavaScript syntax and Git whitespace checks passed.
- Twelve headless Chrome component checks passed using the Admin, Employee and Lead stylesheets at 1440px and 390px. Checks cover headings, field/button heights, overflow, labels, submission values, add/update icons, dynamically inserted controls, primary colors, field decoration and exclusion of shared screen styles from print.
- Desktop/mobile screenshots were visually inspected. Reproducible component fixture, checks, source inventory and screenshots are under `tmp/crm-ui-review/`.

Limits: browser validation used representative components with the portal stylesheets. Authenticated, data-dependent workflows in every module were not exercised. Existing module-specific field layouts remain responsive to their content.
