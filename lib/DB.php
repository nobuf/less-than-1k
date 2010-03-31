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
    {
    }

    /**
     * singleton 
     * 
     * @param string $dbname 
     * @static
     * @access public
     * @return object
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            /*
            $db = new PDO(DATABASE_DSN, DATABASE_USER, DATABASE_PASSWORD);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$instance = $db;
             */
            /*
            self::$instance = new Mongo;
             */
        }
        return self::$instance;
    }
}
