<?php

namespace AxelSpringer\WP\Mango;

use AxelSpringer\WP\Bootstrap\User\Roles;

abstract class __PLUGIN__
{
    const SLUG          = 'wp_mango';
    const VERSION       = '1.0.0-dev';
    const NAMESPACE     = 'mango/v1';

    const ROLE              = 'mango';
    const ROLE_DISPLAYNAME  = 'Mango';
    const ROLE_CAPABILITIES = Roles::EDITOR;

    const TEXT_DOMAIN   = 'wp_mango';
    const SETTINGS_PAGE = 'wp_mango_settings_page';
    const SETTINGS_PERMISSION = 'manage_options';
}
