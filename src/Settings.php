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
        'title'       => __( __TRANSLATE__::SETTINGS_SECTION_GENERAL, __PLUGIN__::TEXT_DOMAIN ),
        'page'        => __PLUGIN__::SETTINGS_PAGE,
        'description' => __( 'These settings control the general setup of the Mango plugin.', __PLUGIN__::TEXT_DOMAIN ),
      );
      $settings = new Section( $args );

      $args    = array(
        'id'           => 'wp_mango_enabled',
        'title'        => __( __TRANSLATE__::SETTINGS_FIELD_ENABLED, __PLUGIN__::TEXT_DOMAIN ),
        'page'         => $this->page,
        'section'      => 'wp_mango_general',
        'description'  => __( '' ),
        'type'         => 'checkbox', // text, textarea, password, checkbox
        'option_group' => $this->page,
      );
      $enabled = new Field( $args );

      $args    = array(
        'id'           => 'wp_mango_role',
        'title'        => __( __TRANSLATE__::SETTINGS_FIELD_ROLE_CAPABILITIES, __PLUGIN__::TEXT_DOMAIN ),
        'page'         => $this->page,
        'section'      => 'wp_mango_general',
        'description'  => __( '' ),
        'type'         => 'dropdown', // text, textarea, password, checkbox
        'option_group' => $this->page,
        'options'       => array( Role::NONE => RoleName::NONE, Role::EDITOR => RoleName::EDITOR )

      );
      $role = new Field( $args );

      // credentials
      $args        = array(
        'id'          => 'wp_mango_credentials',
        'title'       => __( __TRANSLATE__::SETTINGS_SECTION_CREDENTIALS, __PLUGIN__::TEXT_DOMAIN ),
        'page'        => __PLUGIN__::SETTINGS_PAGE,
        'description' => 'These are authentication credentials.'
      );
      $credentials = new Section( $args );

      $args             = array(
        'id'          => Credentials::OPTION_TOKEN,
        'title'       => __( __TRANSLATE__::SETTINGS_FIELD_TOKEN, __PLUGIN__::TEXT_DOMAIN ),
        'page'        => $this->page,
        'section'     => 'wp_mango_credentials',
        'description' => __( 'User' ),
        'type'        => 'callback',
        'option_group' => $this->page,
        'callback'    => array( &$this, 'callback_info' )
      );
      $credentials_user = new Field( $args );

      $args               = array(
        'id'          => Credentials::OPTION_SECRET_KEY,
        'title'       => __( __TRANSLATE__::SETTINGS_FIELD_SECRET, __PLUGIN__::TEXT_DOMAIN ),
        'page'        => $this->page,
        'section'     => 'wp_mango_credentials',
        'description' => __( 'Secret' ),
        'type'        => 'callback',
        'option_group' => $this->page,
        'callback'    => array( &$this, 'callback_info' )
      );
      $credentials_secret = new Field( $args );

      // resources
      $args      = array(
        'id'          => 'wp_mango_resources',
        'title'       => __( __TRANSLATE__::SETTINGS_SECTION_RESOURCES, __PLUGIN__::TEXT_DOMAIN ),
        'page'        => __PLUGIN__::SETTINGS_PAGE,
        'description' => 'These are all the additional resources Mango provides to Wordpress.',
      );
      $resources = new Section( $args );

      $args = array(
        'id'           => 'wp_mango_posts',
        'title'        => __( __TRANSLATE__::SETTINGS_FIELD_POSTS, __PLUGIN__::TEXT_DOMAIN ),
        'page'         => $this->page,
        'section'      => 'wp_mango_resources',
        'option_group' => $this->page,
        'description'  => __( '' ),
        'type'         => 'checkbox', // text, textarea, password, checkbox
        'option_group' => __PLUGIN__::SETTINGS_PAGE,
      );
      $posts  = new Field( $args );

      $args = array(
        'id'           => 'wp_mango_nav',
        'title'        => __( __TRANSLATE__::SETTINGS_FIELD_NAVIGATION, __PLUGIN__::TEXT_DOMAIN ),
        'page'         => $this->page,
        'section'      => 'wp_mango_resources',
        'option_group' => $this->page,
        'description'  => __( '' ),
        'type'         => 'checkbox', // text, textarea, password, checkbox
        'option_group' => __PLUGIN__::SETTINGS_PAGE,
      );
      $nav  = new Field( $args );

      $args       = array(
        'id'           => 'wp_mango_customizer',
        'title'        => __( __TRANSLATE__::SETTINGS_FIELD_CUSTOMIZER, __PLUGIN__::TEXT_DOMAIN ),
        'page'         => $this->page,
        'section'      => 'wp_mango_resources',
        'description'  => __( '' ),
        'type'         => 'checkbox', // text, textarea, password, checkbox
        'option_group' => $this->page
      );
      $customizer = new Field( $args );
    }

     /**
     * Info callback (legacy)
     *
     */
    public function callback_info( $args )
    {
        ?>
        <p>
            <?= get_option( $args['id'] ) ?>
        </p>
        <?php
    }
}
