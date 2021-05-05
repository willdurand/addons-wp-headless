<?php
/**
 * Plugin Name: Add-ons WP Headless
 * Description: Restrict access to the WordPress frontend for AMO needs
 * Version: 2.2.0
 * License: MPL-2
 */

function addons_disable_frontend()
{
    global $wp;

    $isAuth0Callback =
        strpos($_SERVER['REQUEST_URI'], '/index.php') !== false &&
        !empty($_COOKIE['auth0_state']);

    // We allow CRON tasks, REST API requests, requests to the admin interface,
    // requests to complete the Auth0 dance, and requests from an authenticated
    // user.  Everything else should be restricted.
    $isRequestForbbiden =
        !defined('DOING_CRON') &&
        !defined('REST_REQUEST') &&
        !is_admin() & !is_user_logged_in() &&
        !$isAuth0Callback;

    if ($isRequestForbbiden) {
        // We use a 401 to "allow" the WPEngine smoketest to pass, see:
        // https://github.com/mozilla/addons-wp-headless/issues/9
        wp_die('Access is restricted, sorry.', 401);
    }
}

add_action('parse_request', 'addons_disable_frontend', 99);
