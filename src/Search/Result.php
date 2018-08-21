<?php

namespace AxelSpringer\WP\Mango\Search;


/**
 * Class SearchResult
 *
 * @package Wp\Mango\Routes
 */
class Result {

    /**
     * Data
     * 
     * @var array 
     */
    public $result = [];

    /**
     * Results per page
     * 
     * @var int
     */
    public $per_page = 1;

    /**
     * Result page
     * 
     * @var int
     */
    public $page = 1;
}

