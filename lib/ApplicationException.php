<?php
/**
 * ApplicationException: This exception is for application level error
 * 
 * @uses Exception
 */
class ApplicationException extends Exception
{
    private $renderType = null;

    public function __construct($message = null, $renderType = null)
    {
        $this->renderType = $renderType;
        parent::__construct($message);
    }
    public function getRenderType()
    {
        return $this->renderType;
    }
}
