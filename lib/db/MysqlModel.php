<?php

abstract class Model
{
    protected $_db;

    public function __construct()
    {
        $this->_db = DB::getInstance();
        $this->_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
}
