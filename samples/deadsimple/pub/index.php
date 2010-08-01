<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../app'));

define('VIEWS_PATH', APPLICATION_PATH . '/views');

set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../../../lib'),
    realpath(APPLICATION_PATH . '/models'),
    get_include_path(),
)));

require 'Bootstrap.php';
Bootstrap::run();
