<?php

namespace your\space;

/**
 * This file is required during in a class AJAX. It is designed that inside of this file you can register your AJAX
 * request handlers which will be attached during WP resolved AJAX request with a default AJAX file
 * wp-admin/admin-ajax.php.
 *
 * Register your own AJAX request actions and handles in this file.
 *
 *
 * @var AJAX $this
 */

$this->add_ajax_actions( new Front_Page_AJAX_Actions() );