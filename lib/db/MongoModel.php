<?php

abstract class Model
{
    protected $_db;
    protected $_collectionName;

    public function __construct()
    {
        $connection = DB::getInstance();
        $this->_db = $connection->{DB_NAME};
    }

    public function __call($method, array $arguments = array())
    {
        return call_user_func_array(array($this->_db->{$this->_collectionName}, $method), $arguments);
    }
}
