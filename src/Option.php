<?php

namespace AxelSpringer\WP\Mango;

use AxelSpringer\WP\Bootstrap\User\Role;

abstract class Option
{
    // General
    const WP_MANGO_ENABLED          = false;
    const WP_MANGO_SECRET           = null;
    const WP_MANGO_NAV              = false;
    const WP_MANGO_CUSTOMIZER       = false;
    const WP_MANGO_POSTS            = false;
    const WP_MANGO_REDIRECT         = false;
    const WP_MANGO_REWRITE_URL      = false;
    const WP_MANGO_PREVIEW_URL      = false;
    const WP_MANGO_HEALTH_CHECK     = false;
    const WP_MANGO_PREVIEW          = false;
    const WP_MANGO_PREVIEW_SAMPLE   = false;
    const WP_MANGO_PREVIEW_SAMPLE_URL = false;
    const WP_MANGO_REST_MEDIA       = false;
    const WP_MANGO_REST_SLUGS       = false;
    const WP_MANGO_REST_PERMALINK   = false;
    const WP_MANGO_SEARCH           = false;
    const WP_MANGO_SEARCH_RESULTS_PER_PAGE = false;
    const WP_MANGO_SEARCH_EXCLUDE_POST_TYPES = false;

    // Filters
    const WP_MANGO_FILTERS_CATEGORY_PARENT = false;

    // Role
    const WP_MANGO_ROLE             = Role::EDITOR;

    // SSR
    const WP_MANGO_SSR_URL          = null;

    // Credentials
    const WP_MANGO_CREDENTIALS_TOKEN    = null;
    const WP_MANGO_CREDENTIALS_SECRET   = null;

    // JWT
    const WP_MANGO_JWT              = false;
    const WP_MANGO_JWT_SECRET_KEY   = false;
}


