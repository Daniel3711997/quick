<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<h1>{{$HelloWorldVersion}}</h1>

<?php

do_shortcode('[quick-framework id="rootProfile" echo="true"]');
