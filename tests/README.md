# Test harness

Use a disposable WordPress installation with ACF Pro, this theme, and imported original content. These tests create synthetic submissions/users and temporarily edit and restore content. Never point them at production.

Set `BL_WP_ROOT` to that installation's absolute directory. The PHP suites use real WordPress/ACF APIs:

```sh
BL_WP_ROOT=/absolute/path/to/test-wordpress php integration.php
BL_WP_ROOT=/absolute/path/to/test-wordpress php optional-and-slider.php
BL_WP_ROOT=/absolute/path/to/test-wordpress php no-acf.php about
```

Repeat `no-acf.php` with services, team, clients, contact and industries; each runs separately because WordPress loads header/footer templates once per request. ACF is excluded only from that process, with no persisted plugin deactivation.

The HTTP suite requires Python 3 plus beautifulsoup4, a local test web server, and an administrator test account. Configure `BL_TEST_URL`, `BL_TEST_ADMIN`, and `BL_TEST_PASSWORD`, then run `python3 http_checks.py`. Defaults refer to the disposable local setup used for development. Results from the completed run are included as text files.
