<?php
/**
 *  Database Driver
 *
 *  @since  2007.3.26
 */
class DB
{
    private static $instance;

    /**
     * __construct 
     * 
     * @access private
     * @return void
     */
    private function __construct()
    { }

    /**
     * singleton 
     * 
     * @static
     * @access public
     * @return object
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            $className = DATABASE_DRIVER;
            self::$instance = $className::factory();
        }
        return self::$instance;
    }
}
