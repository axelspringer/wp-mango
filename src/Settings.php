<?php

namespace AxelSpringer\WP\Mango;

use AxelSpringer\WP\Bootstrap\Settings\AbstractSettings;
use AxelSpringer\WP\Bootstrap\Settings\Page;
use AxelSpringer\WP\Bootstrap\Settings\Field;
use AxelSpringer\WP\Bootstrap\Settings\Section;
use AxelSpringer\WP\Bootstrap\User\Roles;
use AxelSpringer\WP\Bootstrap\User\RoleName;
use AxelSpringer\WP\Bootstrap\User\Role;
use AxelSpringer\WP\Mango\Services\Credentials;

/**
 * Class Settings
 *
 * @package AxelSpringer\WP\Bootstrap
 */
class Settings extends AbstractSettings {

    /**
     * Loading the settings for the plugin
     */
    public function load_settings()
    {
      // general
      $args     = array(
        'id'          => 'wp_mango_general',
        'title'       => __( Translate::SETTINGS_SECTION_GENERAL, Plugin::TEXT_DOMAIN ),
        'page'        => Plugin::SETTINGS_PAGE,
        'description' => __( 'These settings control the general setup of the Mango plugin.', Plugin::TEXT_DOMAIN ),
      );
      $settings = new Section( $args );

      $args    = array(
        'id'           => 'wp_mango_enabled',
        'title'        => __( Translate::SETTINGS_FIELD_ENABLED, Plugin::TEXT_DOMAIN ),
        'page'         => $this->page,
        'section'      => 'wp_mango_general',
        'description'  => __( '' ),
        'type'         => 'checkbox', // text, textarea, password, checkbox
        'option_group' => $this->page,
      );
      $enabled = new Field( $args );

      $args    = array(
        'id'           => 'wp_mango_redirect',
        'title'        => __( Translate::SETTINGS_FIELD_REDIRECT, Plugin::TEXT_DOMAIN ),
        'page'         => $this->page,
        'section'      => 'wp_mango_general',
        'description'  => __( '' ),
        'type'         => 'checkbox', // text, textarea, password, checkbox
        'option_group' => $this->page,
      );
      $enabled = new Field( $args );

      $args    = array(
        'id'           => 'wp_mango_rewrite_url',
        'title'        => __( Translate::SETTINGS_FIELD_REWRITE_URL, Plugin::TEXT_DOMAIN ),
        'page'         => $this->page,
        'section'      => 'wp_mango_general',
        'description'  => __( '' ),
        'type'         => 'checkbox', // text, textarea, password, checkbox
        'option_group' => $this->page,
      );
      $rewrite_url = new Field( $args );

      $args    = array(
        'id'           => 'wp_mango_role',
        'title'        => __( Translate::SETTINGS_FIELD_ROLE_CAPABILITIES, Plugin::TEXT_DOMAIN ),
        'page'         => $this->page,
        'section'      => 'wp_mango_general',
        'description'  => __( Translate::SETTINGS_DESCRIPTION_ROLE_CAPABILITIES, Plugin::TEXT_DOMAIN ),
        'type'         => 'dropdown', // text, textarea, password, checkbox
        'option_group' => $this->page,
        'options'       => array( Role::NONE => RoleName::NONE, Role::EDITOR => RoleName::EDITOR )
      );
      $role = new Field( $args );

      $args    = array(
        'id'           => 'wp_mango_health_check',
        'title'        => __( Translate::SETTINGS_FIELD_HEALTH, Plugin::TEXT_DOMAIN ),
        'page'         => $this->page,
        'section'      => 'wp_mango_general',
        'description'  => __( '' ),
        'type'         => 'checkbox', // text, textarea, password, checkbox
        'option_group' => $this->page,
      );
      $health = new Field( $args );

      // credentials
      $args        = array(
        'id'          => 'wp_mango_credentials',
        'title'       => __( Translate::SETTINGS_SECTION_CREDENTIALS, Plugin::TEXT_DOMAIN ),
        'page'        => Plugin::SETTINGS_PAGE,
        'description' => 'These are authentication credentials.'
      );
      $credentials = new Section( $args );

      $args             = array(
        'id'          => 'wp_mango_credentials_token',
        'title'       => __( Translate::SETTINGS_FIELD_TOKEN, Plugin::TEXT_DOMAIN ),
        'page'        => $this->page,
        'section'     => 'wp_mango_credentials',
        'description' => __( 'User' ),
        'type'        => 'callback',
        'option_group' => $this->page,
        'callback'    => array( &$this, 'callback_info' )
      );
      $credentials_user = new Field( $args );

      $args               = array(
        'id'          => 'wp_mango_credentials_secret',
        'title'       => __( Translate::SETTINGS_FIELD_SECRET, Plugin::TEXT_DOMAIN ),
        'page'        => $this->page,
        'section'     => 'wp_mango_credentials',
        'description' => __( 'Secret' ),
        'type'        => 'callback',
        'option_group' => $this->page,
        'callback'    => array( &$this, 'callback_info' )
      );
      $credentials_secret = new Field( $args );

      // credentials
      $args        = array(
        'id'          => 'wp_mango_preview',
        'title'       => __( Translate::SETTINGS_SECTION_PREVIEW, Plugin::TEXT_DOMAIN ),
        'page'        => Plugin::SETTINGS_PAGE,
        'description' => 'These are the settings for the Preview.'
      );
      $preview = new Section( $args );

      $args = array(
        'id'           => 'wp_mango_preview',
        'title'        => __( Translate::SETTINGS_FIELD_PREVIEW, Plugin::TEXT_DOMAIN ),
        'page'         => $this->page,
        'section'      => 'wp_mango_preview',
        'option_group' => $this->page,
        'description'  => __( '' ),
        'type'         => 'checkbox', // text, textarea, password, checkbox
        'option_group' => Plugin::SETTINGS_PAGE,
      );
      $preview_enable  = new Field( $args );

      $args = array(
        'id'           => 'wp_mango_preview_url',
        'title'        => __( Translate::SETTINGS_FIELD_PREVIEW_URL, Plugin::TEXT_DOMAIN ),
        'page'         => $this->page,
        'section'      => 'wp_mango_preview',
        'option_group' => $this->page,
        'description'  => __( 'This is the preview url (e.g. https://example.com/preview/%post_type%/%id%)' ),
        'type'         => 'text', // text, textarea, password, checkbox
        'option_group' => Plugin::SETTINGS_PAGE,
      );
      $preview_url  = new Field( $args );

      $args = array(
        'id'           => 'wp_mango_preview_sample',
        'title'        => __( Translate::SETTINGS_FIELD_PREVIEW_SAMPLE, Plugin::TEXT_DOMAIN ),
        'page'         => $this->page,
        'section'      => 'wp_mango_preview',
        'option_group' => $this->page,
        'description'  => __( '' ),
        'type'         => 'checkbox', // text, textarea, password, checkbox
        'option_group' => Plugin::SETTINGS_PAGE,
      );
      $sample  = new Field( $args );

      $args = array(
        'id'           => 'wp_mango_preview_sample_url',
        'title'        => __( Translate::SETTINGS_FIELD_PREVIEW_SAMPLE_URL, Plugin::TEXT_DOMAIN ),
        'page'         => $this->page,
        'section'      => 'wp_mango_preview',
        'option_group' => $this->page,
        'description'  => __( 'This is the sample url (e.g. https://example.com)' ),
        'type'         => 'text', // text, textarea, password, checkbox
        'option_group' => Plugin::SETTINGS_PAGE,
      );
      $sample_url  = new Field( $args );

      // resources
      $args      = array(
        'id'          => 'wp_mango_resources',
        'title'       => __( Translate::SETTINGS_SECTION_RESOURCES, Plugin::TEXT_DOMAIN ),
        'page'        => Plugin::SETTINGS_PAGE,
        'description' => 'These are all the additional resources Mango provides to Wordpress.',
      );
      $resources = new Section( $args );

      $args = array(
        'id'           => 'wp_mango_posts',
        'title'        => __( Translate::SETTINGS_FIELD_POSTS, Plugin::TEXT_DOMAIN ),
        'page'         => $this->page,
        'section'      => 'wp_mango_resources',
        'option_group' => $this->page,
        'description'  => __( '' ),
        'type'         => 'checkbox', // text, textarea, password, checkbox
        'option_group' => Plugin::SETTINGS_PAGE,
      );
      $posts  = new Field( $args );

      $args = array(
        'id'           => 'wp_mango_rest_slugs',
        'title'        => __( Translate::SETTINGS_FIELD_SLUGS, Plugin::TEXT_DOMAIN ),
        'page'         => $this->page,
        'section'      => 'wp_mango_resources',
        'option_group' => $this->page,
        'description'  => __( '' ),
        'type'         => 'checkbox', // text, textarea, password, checkbox
        'option_group' => Plugin::SETTINGS_PAGE,
      );
      $slugs  = new Field( $args );

      $args = array(
        'id'           => 'wp_mango_rest_media',
        'title'        => __( Translate::SETTINGS_FIELD_MEDIA, Plugin::TEXT_DOMAIN ),
        'page'         => $this->page,
        'section'      => 'wp_mango_resources',
        'option_group' => $this->page,
        'description'  => __( '' ),
        'type'         => 'checkbox', // text, textarea, password, checkbox
        'option_group' => Plugin::SETTINGS_PAGE,
      );
      $media  = new Field( $args );

      $args = array(
        'id'           => 'wp_mango_nav',
        'title'        => __( Translate::SETTINGS_FIELD_NAVIGATION, Plugin::TEXT_DOMAIN ),
        'page'         => $this->page,
        'section'      => 'wp_mango_resources',
        'option_group' => $this->page,
        'description'  => __( '' ),
        'type'         => 'checkbox', // text, textarea, password, checkbox
        'option_group' => Plugin::SETTINGS_PAGE,
      );
      $nav  = new Field( $args );

      $args       = array(
        'id'           => 'wp_mango_customizer',
        'title'        => __( Translate::SETTINGS_FIELD_CUSTOMIZER, Plugin::TEXT_DOMAIN ),
        'page'         => $this->page,
        'section'      => 'wp_mango_resources',
        'description'  => __( '' ),
        'type'         => 'checkbox', // text, textarea, password, checkbox
        'option_group' => $this->page
      );
      $customizer = new Field( $args );

      // filters
      $args      = array(
        'id'          => 'wp_mango_filters',
        'title'       => __( Translate::SETTINGS_SECTION_FILTERS, Plugin::TEXT_DOMAIN ),
        'page'        => Plugin::SETTINGS_PAGE,
        'description' => 'Filters to enable for certain features.',
      );
      $resources = new Section( $args );

      $args       = array(
        'id'           => 'wp_mango_filters_category_parent',
        'title'        => __( Translate::SETTINGS_FIELD_FILTER_CATEGORY_PARENT, Plugin::TEXT_DOMAIN ),
        'page'         => $this->page,
        'section'      => 'wp_mango_filters',
        'description'  => __( '' ),
        'type'         => 'checkbox', // text, textarea, password, checkbox
        'option_group' => $this->page
      );
      $filter_page_link = new Field( $args );
    }

    /**
     * Info callback (legacy)
     *
     */
    public function callback_info( $args )
    {
        $option = get_option( $args['id'] );
        $env = getenv( strtoupper( $args['id'] ) );

        ?>
        <input type="hidden" name="<?= $args['id']?>" id="<?= $args['id']?>" value="<?= get_option( $args['id'] ) ?>">
        <p>
            <?= ! $env ? $option : $env ?>
        </p>
        <?php
    }
}
