<?php
/** Small GitHub Releases integration using WordPress's native theme updater. */
defined('ABSPATH') || exit;
const BL_UPDATE_REPOSITORY = 'https://github.com/pmunankarmi/bottomline-consultancy';
const BL_UPDATE_API = 'https://api.github.com/repos/pmunankarmi/bottomline-consultancy/releases/latest';

function bl_update_request($url) {
    $response = wp_safe_remote_get($url, ['timeout' => 10, 'redirection' => 5, 'limit_response_size' => 131072, 'headers' => ['Accept' => 'application/vnd.github+json', 'User-Agent' => 'Bottomline-WordPress-Theme-Updater']]);
    if (is_wp_error($response)) { return $response; }
    $status = wp_remote_retrieve_response_code($response);
    if ($status !== 200) { return new WP_Error('bl_release_http', $status === 404 ? __('No published theme release is available yet.', 'bottomline') : __('GitHub could not be reached or its request limit was reached. Try again later.', 'bottomline')); }
    $data = json_decode(wp_remote_retrieve_body($response), true);
    return is_array($data) ? $data : new WP_Error('bl_release_json', __('GitHub returned invalid release information.', 'bottomline'));
}
function bl_release_asset($release, $name) {
    $tag = $release['tag_name'] ?? '';
    $expected = BL_UPDATE_REPOSITORY . '/releases/download/' . rawurlencode($tag) . '/' . $name;
    foreach (bl_rows($release['assets'] ?? []) as $asset) {
        if (($asset['name'] ?? '') === $name && ($asset['state'] ?? '') === 'uploaded' && ($asset['browser_download_url'] ?? '') === $expected) { return $expected; }
    }
    return '';
}
function bl_github_release($force = false) {
    if (!$force) {
        $cached = get_site_transient('bl_github_release');
        if (is_array($cached)) { return isset($cached['error']) ? new WP_Error('bl_release_cached', $cached['error']) : $cached; }
    }
    $release = bl_update_request(BL_UPDATE_API);
    if (!is_wp_error($release)) {
        $tag = $release['tag_name'] ?? '';
        if (!is_string($tag) || !preg_match('/^v?(\d+\.\d+\.\d+)$/', $tag, $match) || !empty($release['draft']) || !empty($release['prerelease'])) {
            $release = new WP_Error('bl_release_version', __('No stable theme release is available.', 'bottomline'));
        } else {
            $package = bl_release_asset($release, 'bottomline.zip');
            $manifest_url = bl_release_asset($release, 'bottomline-update.json');
            if (!$package || !$manifest_url) {
                $release = new WP_Error('bl_release_assets', __('The release is missing its installable theme ZIP or update manifest.', 'bottomline'));
            } else {
                $manifest = bl_update_request($manifest_url);
                if (is_wp_error($manifest)) { $release = $manifest; }
                elseif (($manifest['version'] ?? '') !== $match[1] || !is_string($manifest['requires'] ?? null) || !preg_match('/^\d+\.\d+(\.\d+)?$/', $manifest['requires']) || !is_string($manifest['requires_php'] ?? null) || !preg_match('/^\d+\.\d+(\.\d+)?$/', $manifest['requires_php'])) {
                    $release = new WP_Error('bl_release_manifest', __('The release manifest is invalid or does not match the release tag.', 'bottomline'));
                } else {
                    $release = ['version' => $match[1], 'url' => BL_UPDATE_REPOSITORY . '/releases/tag/' . rawurlencode($tag), 'package' => $package, 'requires' => $manifest['requires'], 'requires_php' => $manifest['requires_php']];
                }
            }
        }
    }
    set_site_transient('bl_github_release', is_wp_error($release) ? ['error' => $release->get_error_message()] : $release, is_wp_error($release) ? 15 * MINUTE_IN_SECONDS : 6 * HOUR_IN_SECONDS);
    return $release;
}
add_filter('update_themes_github.com', function ($update, $theme_data, $stylesheet) {
    if (($theme_data['UpdateURI'] ?? '') !== BL_UPDATE_REPOSITORY || $stylesheet !== get_template()) { return $update; }
    $release = bl_github_release();
    if (is_wp_error($release)) { return $update; }
    return array_merge($release, ['id' => BL_UPDATE_REPOSITORY, 'theme' => $stylesheet]);
}, 10, 3);

add_action('admin_menu', function () {
    add_theme_page(__('Theme Updates', 'bottomline'), __('Theme Updates', 'bottomline'), 'update_themes', 'bl-theme-updates', 'bl_theme_updates_page');
});
function bl_theme_updates_page() {
    if (!current_user_can('update_themes')) { return; }
    $theme = wp_get_theme(get_template());
    $cached = get_site_transient('bl_github_release');
    echo '<div class="wrap"><h1>' . esc_html__('Theme Updates', 'bottomline') . '</h1><p>' . esc_html(sprintf(__('Installed version: %s', 'bottomline'), $theme->get('Version'))) . '</p>';
    if (is_array($cached) && isset($cached['version'])) {
        echo '<p>' . esc_html(sprintf(__('Latest GitHub release: %s', 'bottomline'), $cached['version'])) . '</p>';
        echo '<p>' . esc_html(version_compare($cached['version'], $theme->get('Version'), '>') ? __('An update is available. Install it from Dashboard → Updates.', 'bottomline') : __('Your theme is up to date.', 'bottomline')) . '</p>';
    } elseif (is_array($cached) && isset($cached['error'])) { echo '<p>' . esc_html($cached['error']) . '</p>'; }
    echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '"><input type="hidden" name="action" value="bl_check_updates">';
    wp_nonce_field('bl_check_updates'); submit_button(__('Check for updates', 'bottomline')); echo '</form>';
    echo '<p><a class="button" href="' . esc_url(admin_url('update-core.php')) . '">' . esc_html__('Open WordPress Updates', 'bottomline') . '</a> <a href="' . esc_url(BL_UPDATE_REPOSITORY . '/releases') . '" target="_blank" rel="noopener noreferrer">' . esc_html__('Release history', 'bottomline') . '</a></p></div>';
}
add_action('admin_post_bl_check_updates', function () {
    if (!current_user_can('update_themes')) { wp_die(esc_html__('Access denied.', 'bottomline'), '', ['response' => 403]); }
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') { wp_die(esc_html__('Method not allowed.', 'bottomline'), '', ['response' => 405]); }
    check_admin_referer('bl_check_updates');
    bl_github_release(true);
    delete_site_transient('update_themes');
    wp_update_themes();
    wp_safe_redirect(admin_url('themes.php?page=bl-theme-updates')); exit;
});
add_action('upgrader_process_complete', function ($upgrader, $options) {
    if (($options['type'] ?? '') === 'theme' && in_array(get_template(), (array) ($options['themes'] ?? []), true)) { delete_site_transient('bl_github_release'); }
}, 10, 2);
