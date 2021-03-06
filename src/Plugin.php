<?php

namespace AxelSpringer\WP\Mango;

abstract class Plugin
{
    const SLUG          = 'wp_mango';
    const VERSION       = '1.0.10-dev';
    const NAMESPACE     = 'mango/v1';

    const TEXT_DOMAIN   = 'wp_mango';
    const SETTINGS_PAGE = 'wp_mango_settings_page';
    const SETTINGS_PERMISSION = 'manage_options';
}
