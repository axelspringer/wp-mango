<?php

class Mango_REST {

  public static function check_rest_auth() {
    $nonce = $_SERVER['HTTP_X_WP_NONCE'] ?? null;

    if (empty($nonce)) {
        return new \WP_Error('403', 'Missing or empty X-WP-Nonce header.', ['status' => 403]);
    }

    $result = wp_verify_nonce( $nonce, 'wp_rest' );

    if (!$result) {
        return new \WP_Error('403', 'X-WP-Nonce not verified.', ['status' => 403]);
    }

    return true;
  }

}
