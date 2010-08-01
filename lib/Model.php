<?php
/**
 * Model 
 * 
 */
defined('DATABASE') || define('DATABASE', 'Mongo');
define('DATABASE_DRIVER',   DATABASE . 'Driver');
define('DATABASE_MODEL',    DATABASE . 'Model');

//  Database connection class like Mongo or PDO
require_once dirname(__FILE__) . '/db/' . DATABASE_DRIVER . PHP_EXTENSION;

//  Model class itself declares here
require_once dirname(__FILE__) . '/db/' . DATABASE_MODEL . PHP_EXTENSION;
