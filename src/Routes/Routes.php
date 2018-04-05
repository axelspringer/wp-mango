<?php

namespace Wp\Mango\Routes;

/**
 * Class Routes
 *
 * @package Wp\Mango\Routes
 */
class Routes
{
    const REST_NAMESPACE = 'mango/v1';

    /**
     * @param string $route
     * @param callable $callback
     *
     * @return bool
     */
    public function get(string $route, callable $callback):bool
    {
        return register_rest_route(
            Routes::REST_NAMESPACE,
            $route,
            [
                'methods' => \WP_REST_Server::READABLE,
                'callback' => $callback,
                'permission_callback' => [$this, 'permissions_check']
            ]
        );
    }

    /**
     * @return bool|\WP_Error
     */
    public function permissions_check()
    {
        $nonce = $_SERVER['HTTP_X_WP_NONCE'] ?? null;
        $token = $_SERVER['HTTP_X_MANGO_TOKEN'] ?? null;
        $secret = $_SERVER['HTTP_X_MANGO_SECRET'] ?? null;

        // by pass if nonce is set
        if ($nonce !== null) {
            return false;
        }

        $current_user = wp_get_current_user();

        // bypass if logged in user
        if (!is_null($current_user) && $current_user->ID !== 0) {
            return false;
        }

        if ($token !== get_option('mango_credentials_token')
            || $secret !== get_option('mango_credentials_secret')) // if not credentials
            return new \WP_Error('invalid_credentials', 'Invalid Credentials', array('status' => 403));

        return true;
    }


}
