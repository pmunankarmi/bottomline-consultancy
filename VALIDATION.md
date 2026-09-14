# Validation record

Completed on 14 September 2026 in an isolated local installation. The production WordPress site was not modified. The GitHub repository is used for theme releases.

## Version 1.2 updater

Added native WordPress theme update integration for the specified public GitHub repository, a capability/nonce-protected manual check screen, cached stable release detection, strict package URL validation and matching release manifests. A tagged-release workflow builds installable ZIPs. Targeted updater tests cover successful detection, cache reuse, unrelated themes, missing assets, foreign URLs, prereleases, version mismatch and network errors.

## Version 1.1 changes

Added the WordPress theme screenshot; disabled Gutenberg and block widgets; removed 86 content-image files from the theme; retained Media Library content; omitted block CSS when no block content is present. The importer now creates text content even when image attachments are unavailable. Original conversion tests below predate the media-free packaging change; see `tests/lightweight-results.txt` for targeted checks.

## Original conversion checks

- PHP syntax: all 65 theme PHP files pass `php -l`.
- JavaScript syntax: `node --check` passes.
- 119 integration assertions pass, including content counts, ACF fields, four source-page selectors, source summary rendering, both directions of logo synchronization and removal, form validation, CSV formula handling, create-only import behavior, preserved edits/clears/order, media deduplication and administrator permissions.
- The same integration suite passes against a separately initialized fresh database after importing the theme's bundled content.
- 33 HTTP checks pass across all pages and team routes, valid/invalid form submissions, nonce failures, spam traps, retained validation input, duplicate submissions, restricted public access, authenticated submission details, export headings/quoting/formula protection and invalid export nonce rejection.
- 8 additional assertions pass for adding and reordering hero slides, matching slider controls, PHP-rendered slide content, and hiding empty clients, testimonials, branches and CTA sections.
- Each of the six page templates was rendered in a separate PHP process with ACF disabled; rendering completed and the dependency notice appeared.
- Browser checks at 1440px desktop and 390px mobile widths: navigation, hero controls, successful mobile submission feedback, branch pin interaction and no horizontal overflow on the inspected pages.
- WordPress admin save check: changing homepage client count from 40 to 39 yielded 39 rendered client tiles; the value was restored to 40.
- Visual inspection of homepage hero, statistics, regional map and mobile contact layout. Original stylesheet, visual card arrangements, illustrations and assets retained. Native WordPress image markup, menus and semantic headings cause minor DOM differences from the static original.

## Source inventory

| Original page | Migrated sections |
|---|---|
| Home | Two hero slides and editable financial/growth illustration data; four statistics; eight reasons to choose the firm; About summary; four featured team members; six service summaries; 40 client logos; 12 industries; four-branch map; two testimonials; consultation CTA |
| About | Page introduction; approach; six values; three target-market cards; shared CTA |
| Services | Introduction; six services; five process steps; shared CTA |
| Team | Introduction; 32 members, with initials/positions and available branch labels; shared CTA |
| Clients | Introduction; all 82 client logos; shared CTA |
| Contact | Introduction; global contact details; four offices; first/last name, email, company, phone, service and message form |
| Shared | Native menu locations, synchronized logo, reversed footer logo, footer copy, branch links and contact details |

The ZIP also contains decorative CSS for some unused earlier design variants; it is retained with the original stylesheet. There were no original team detail pages, real submission handlers or attributed testimonial authors. Team profiles and secure form storage have been implemented; missing author/photo data has not been fabricated.

## Remaining deployment checks

- Install and license ACF Pro on the intended host.
- Run the explicit importer, review its report and verify page/menu assignment against any existing content.
- Test your production MySQL/MariaDB, caching/security plugins and HTTPS configuration. Local tests used the official SQLite integration, not your production database.
- Exclude the contact page/handler from full-page caching and perform one production form/storage/export smoke test using appropriate test data.
- The source map is a raster image with baked-in labels: replacing/revising it is necessary if its geographic labels change. Interactive branch data and overlay positions are editable separately.
- Cairo uses Google Fonts; map destinations are external links. Version 1.1 removes bundled content imagery; upload and assign it in the Media Library on new installations. Optional notification emails use WordPress mail; successful submissions are always stored in WordPress and visible to administrators.

ACF Pro, WordPress core, the test database, user credentials and test submissions are not included in the installable theme package.

## Version 1.3 template refactor

Verified all six custom templates are registered by WordPress; old assignments resolve to their new files; assignment migration preserves all other page metadata (24 checks). The existing content integration (119), optional sections/slider (8), lightweight theme (10), updater (12), and authenticated HTTP/form/export (33) checks pass. All six custom pages also render without ACF. PHP syntax and JavaScript syntax are checked during packaging. Formatting follows WordPress spacing conventions; this is not a claim of full WPCS compliance.

## Version 1.3.1 automatic updates

Verified an administrator check populates the native WordPress update transient, repeated visits reuse the check interval, and the custom updater page/action are removed. The existing GitHub release-validation tests still pass. GitHub publishing now runs on theme changes pushed to main and assigns a new patch version when needed.

## Version 1.3.2 icon controls

Verified all 16 icon dropdowns retain their choices and have paired image overrides. Nested repeater values render the image when selected and restore the built-in icon when removed. Confirmed image uploaders and SVG dropdown previews render in the Services editor. PHP and JavaScript syntax checks pass.

## Version 1.3.3 contact validation

Browser checks verified empty required fields, whitespace-only names, invalid email, first-error focus, ARIA error association, and successful valid submission. All 33 HTTP checks passed, including server-side rejection, storage and authenticated CSV export.

## Version 1.3.4 notification email

The optional Form notification email setting receives contact details and a private admin link after a new entry is saved. Replies go to the validated visitor address. Blank or invalid recipients disable delivery. Duplicate submissions do not resend notifications. Submission details show the mail handoff result; acceptance by WordPress mail does not guarantee inbox delivery. Configure the host’s mail service or SMTP as needed. Tests intercept mail and verify recipient, content, reply address, failures, saved entries and duplicate suppression without sending external email.

## Version 1.3.5 release details

WordPress’s View version details dialog now uses a local, administrator-only view with version requirements and escaped release notes. GitHub opens separately in a new tab. Cached GitHub details URLs are repaired without changing package URLs. Tests cover the native update link, cached links, unrelated themes, escaped notes and offline feedback; the live details endpoint was verified in the browser.

## Version 1.3.6 styled notifications

Theme Settings → Contact and Social includes Notification sender name and Notification sender email. Blank values retain WordPress mail defaults; SMTP plugins may enforce their configured sender. These overrides apply only to contact notifications. Emails use an escaped HTML template with inline styles, a green heading, submitted details, message panel and an admin button. Reply-To remains the visitor. Mocked delivery tests verify sender scope, HTML content, escaping, duplicate handling and failures. Layout was reviewed in the browser; actual inbox rendering depends on the email client.
