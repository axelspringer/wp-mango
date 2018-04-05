<?php

namespace Wp\Mango\Routes;

/**
 * Class Posts
 *
 * @package Wp\Mango\Routes
 */
class Customizer
{
    /**
     * @var Routes $routes
     */
    protected $routes;

    /**
     * @var string
     */
    protected $base = 'customizer';

    /**
     * Posts constructor.
     *
     * @param Routes $routes
     */
    public function __construct(Routes $routes)
    {
        $this->routes = $routes;

        $this->init();
    }

    /**
     * @return \WP_REST_Response
     */
    public function get_settings()
    {
        $settings = get_theme_mods();

        return new \WP_REST_Response($settings);
    }

    protected function init()
    {
        $this->routes->get($this->base, [$this, 'get_settings']);
    }
}
