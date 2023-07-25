<?php

if (!defined('ABSPATH')) {
    exit;
}

use Quick\Blade\Containers\HelloWorld;

/* Template Name: Profile */

HelloWorld::render(['includeHeader' => true, 'includeFooter' => true]);
