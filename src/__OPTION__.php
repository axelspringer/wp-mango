<?php

namespace AxelSpringer\WP\Mango;

use AxelSpringer\WP\Bootstrap\User\Role;

abstract class __OPTION__
{
    // General
    const WP_MANGO_ENABLED          = false;
    const WP_MANGO_SECRET           = null;
    const WP_MANGO_NAV              = false;
    const WP_MANGO_CUSTOMIZER       = false;
    const WP_MANGO_POSTS            = false;

    // Role
    const WP_MANGO_ROLE             = Role::EDITOR;

    // SSR
    const WP_MANGO_SSR_URL          = null;

    // Credentials
    const WP_MANGO_CREDENTIALS_TOKEN    = null;
    const WP_MANGO_CREDENTIALS_SECRET   = null;
}


