# Bottom Line Consultancy — WordPress conversion

The installable theme is in `bottomline/`. Use the separately delivered `bottomline-wordpress-theme.zip` in WordPress → Appearance → Themes → Add New → Upload Theme.

The supplied GitHub repository was empty when inspected. The implementation uses the supplied **BL F3.zip**, with its six original pages, original stylesheet, interactive behavior, logos, and regional map. The repository is the release source for WordPress theme updates.

## Lightweight version 1.1

Gutenberg is disabled for posts, pages, custom post types and widgets while this theme is active. Existing saved block content can still render. Block styles are omitted on pages without block content. The only packaged raster image is `screenshot.jpg`, the WordPress theme preview. All site images live in the Media Library. Existing attachments and image fields are preserved; on new sites, upload and assign images in ACF after importing text content.

## GitHub updates

Version 1.3.1 checks automatically when an administrator opens WordPress, with a five-minute interval between checks. WordPress’s scheduled checks also use the GitHub release feed. Available updates appear under **Dashboard → Updates** and **Appearance → Themes**; there is no custom updater page or check button. No access token or updater plugin is needed for this public repository. Updates preserve saved content. Use a child theme for custom PHP/CSS modifications. Sites on an older updater can install this release once through the standard WordPress update screen or by uploading the ZIP.

Push theme changes to `main` to publish an update automatically. GitHub Actions validates and packages the theme, creates a tag, and publishes the ZIP and manifest. If the version in `style.css` is already published, the workflow increments its patch version and commits that change to `main`. You can set a higher minor or major version yourself before pushing. Pull `main` before your next edit to include automated version commits. GitHub Actions must have permission to push to `main` and create releases. Update notices appear after the release finishes and the site's next scheduled or administrator check; this is polling, not an instant GitHub webhook. The theme must be active (or the parent of an active child theme).

## Installation

1. Install WordPress 6.6 or later on PHP 8.1 or later. Use the current maintained WordPress/PHP releases supported by your host.
2. Install and activate **ACF Pro**. ACF Free does not supply the required repeaters/options pages. ACF Pro is a separate dependency and is not included in the theme ZIP.
3. Upload and activate the Bottom Line Consultancy theme.
4. Open **Appearance → Bottom Line Setup** and click **Import missing content**. Review the result. This is an explicit operation: activation, theme updates, and ordinary page loads never run the importer.
5. Review **Settings → Reading**, **Settings → Permalinks**, and **Appearance → Menus**. On a new site the importer selects the Home page and assigns both menus. Existing reading/menu choices are preserved.
6. Edit content in Pages, Team, and Theme Settings. Exclude the Contact page and `wp-admin/admin-post.php` from any full-page/CDN cache; forms and private feedback require fresh requests.
7. Submit a test enquiry and confirm it appears under **Form Submissions**. Set **Theme Settings → Contact and Social → Form notification email** to receive new enquiry notifications. Leave blank to disable.

The importer creates seven pages: Home, About, Services, Team, Clients, Contact, and Industries. Industries is a new source page for the section that existed only on the original homepage. Existing unrelated pages with matching slugs are left intact; manually assign their template and populate fields if you choose to reuse them.

## Editing guide

| Content | Editing location |
|---|---|
| Site logo | Theme Settings → General, or Appearance → Customize → Site Identity; both stay synchronized |
| Homepage client/team counts | Theme Settings → General |
| Main navigation, CTA menu item, footer navigation | Appearance → Menus; retain `nav-cta` CSS class on a primary CTA menu item |
| Contact details and social links | Theme Settings → Contact and Social |
| Form service choices | Theme Settings → Contact and Social |
| Branches, country grouping, pin positions | Theme Settings → Branches; drag rows to reorder |
| Consultation CTA | Theme Settings → CTA |
| Reversed footer logo, descriptions and labels | Theme Settings → Footer |
| Client names, logos, website links and order | Theme Settings → Clients |
| Quotes, authors, positions, companies and photos | Theme Settings → Testimonials |
| Hero slides and other homepage sections | Pages → Home; drag hero rows to reorder; choose dashboard style or supply an image |
| About, Services, Clients and Industries homepage sections | Select source pages on Home, then edit each source page’s **Homepage summary** tab |
| Full source-page copy | Edit the relevant page’s other tabs |
| Team names | Native Team post titles |
| Team positions, biographies, photos and social links | Team post fields |
| Team order | Team → edit member → Page Attributes → Order (lower numbers first) |
| Form labels, introductory copy and placeholder | Pages → Contact → Contact details and form |
| Search descriptions | Page Search description tab; WordPress titles remain native |

Fields use plain text/textarea, media attachment IDs, page selectors, links, groups and repeaters. Markup and decorative SVG paths live in PHP. Built-in card styles preserve the original distinct arrangements; icon selectors choose theme-owned SVG. Dashboard numeric fields control both the initial PHP value and JavaScript animation. Optional images are omitted when missing, and empty optional sections/lists are hidden.

Shared homepage content is intentionally managed on source pages. Shorter homepage summaries are separate from the longer page copy. Clients, team, testimonials and branches always read their single shared source. Homepage section destinations are generated from selected source-page permalinks.

## Architecture

- Traditional `header.php`, `footer.php`, `front-page.php`, `page.php`, six selectable templates in `page-templates/`, `archive-team.php`, `single-team.php`, and reusable template parts.
- `inc/field-schema.php`: version-controlled PHP ACF definitions; registered on `acf/init`.
- `inc/core.php`: theme support, menus, enqueues, Team CPT, safe rendering helpers and missing-dependency notice.
- `inc/logo.php`: bidirectional attachment-ID synchronization, including removal and recursion protection.
- `inc/forms.php`: WordPress `admin-post.php` handlers, validation, nonce checks, honeypot, hashed-IP rate limits, deduplication, private submissions and CSV export.
- `inc/migration.php`: administrator-only, nonce-protected importer. `inc/migration-data.php` is used only by this importer, never as frontend fallback content.
- `assets/`: original CSS and JavaScript; content images are deliberately excluded from the theme package. `wordpress.css` supplies WordPress/accessibility adjustments.

The original JavaScript demo form handler has been removed. JavaScript now handles interactions: menu, slider, counters, reveal/tilt effects and map highlighting. Server-rendered PHP supplies all content.

## Submission administration

**Form Submissions** shows form type, name, email and site-local submission date. Click an ID to view all submitted fields. **CSV Export** exports stored submissions with clear headings, UTF-8 encoding, correct CSV quoting and neutralization of leading spreadsheet formula characters, including whitespace-prefixed formulas.

Only users with `manage_options` can view or export submissions. Export requires a valid nonce. The storage post type is private, has no public routes or REST collection, and cannot be viewed through the normal editor. Feedback uses an expiring random HttpOnly cookie and short-lived server storage; submitted personal data is not placed in URLs. Validation errors preserve safe input. Duplicate delivery is suppressed briefly; successful requests are rate limited to five per IP hash per 15 minutes.

## Import safety

The importer uses persistent page/member identity metadata and recognizes existing attachment source metadata. It does not download or copy image files. It creates missing content, fills only absent field roots, and skips existing fields even when deliberately cleared. It never replaces an existing repeater with defaults, preserving removals and ordering. Trashed migrated pages/team members are respected. Image references resolve to already migrated Media Library attachments when available; otherwise image fields remain empty while surrounding text and rows are imported. A lock prevents overlapping imports. Existing native logos are preserved.

Page/menu/reading setup runs only during the first explicit import. Subsequent imports do not reset site choices. Permanently deleting an imported page, member or attachment removes its identity record; a later explicit import can recreate it. Restore from a backup if a destructive cleanup was unintended.

## Validation and limits

See `VALIDATION.md` for executed checks. The theme was tested locally with WordPress 7.0.2, PHP 8.5.7, ACF Pro and the official SQLite integration. Production MySQL/MariaDB, your cache/security plugins and your hosting configuration still need a deployment smoke test. No production site was modified.

The supplied design has initials instead of team photos and two unattributed testimonial quotes. Those were preserved; author/position/company/photo fields are available but were not invented. Team detail routes are added so administrators can publish biographies. The original hero financial illustration is preserved as demonstration content, not live financial data. Branch map pin positions are manually editable percentages.

Cairo is loaded from Google Fonts. Maps open the supplied external map links. PHP content does not depend on a JavaScript framework or build step. ACF Pro is required for normal editing and rendering; disabling it produces an admin notice and safe, sparse pages without fatal errors. Submissions and team records are registered by this theme and remain in the database after switching themes, but their theme-provided administration is unavailable until it is restored or those registrations are moved to a site plugin.

## API references

Implementation follows the official [ACF PHP field registration documentation](https://www.advancedcustomfields.com/resources/register-fields-via-php/) and [ACF update-value filter documentation](https://www.advancedcustomfields.com/resources/acf-update_value/). WordPress Customizer saves use native theme modifications; see [Customizer save lifecycle](https://developer.wordpress.org/reference/hooks/customize_save_after/).

## Template maintenance

PHP and HTML ship as readable source, with WordPress file docblocks and descriptive loop variables. The six selectable templates are `page-templates/about.php`, `services.php`, `clients.php`, `team.php`, `contact.php`, and `industries.php`. WordPress hierarchy files stay at the theme root.

The header uses native custom-logo, menu, document and body hooks. The footer renders its own logo, menu and contact details and calls `wp_footer()`. Fourteen substantial shared sections remain in `template-parts/`; page-only sections and small logo/menu/contact fragments are inline.

Version 1.3 migrates previous page-template assignments on an administrator visit. Legacy assignments also resolve immediately on the frontend. This changes template paths only and preserves saved page and ACF content.

## Icon controls

Each icon keeps its built-in dropdown with a live visual preview underneath. The adjacent optional image override uses the WordPress Media Library. An uploaded image takes priority; removing it restores the dropdown selection. This applies to card icons, button icons and editable decorative illustrations.

## Contact validation

The contact page loads WordPress jQuery and the locally bundled jQuery Validation plugin 1.21.0 (MIT). Required fields, email format and length limits show inline accessible feedback before the normal form POST. Company, phone and service remain optional. PHP validation and submission protections remain active, including when JavaScript is unavailable.

## Version 1.3.4 notification email

The optional Form notification email setting receives contact details and a private admin link after a new entry is saved. Replies go to the validated visitor address. Blank or invalid recipients disable delivery. Duplicate submissions do not resend notifications. Submission details show the mail handoff result; acceptance by WordPress mail does not guarantee inbox delivery. Configure the host’s mail service or SMTP as needed. Tests intercept mail and verify recipient, content, reply address, failures, saved entries and duplicate suppression without sending external email.

## Version 1.3.5 release details

WordPress’s View version details dialog now uses a local, administrator-only view with version requirements and escaped release notes. GitHub opens separately in a new tab. Cached GitHub details URLs are repaired without changing package URLs. Tests cover the native update link, cached links, unrelated themes, escaped notes and offline feedback; the live details endpoint was verified in the browser.
