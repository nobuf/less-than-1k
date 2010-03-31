<?php
class NotFoundException extends Exception
{
    public function __construct()
    {
        $this->message = $_SERVER['REQUEST_URI'] . ' Not Found';
        parent::__construct();
    }
}
