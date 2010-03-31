<?php

class ErrorController extends Controller
{
    private $_renderType;

    public function indexAction($message = null)
    {
        if (!empty($this->_renderType)) {
            call_user_func(array($this, 'render' . ucfirst($this->_renderType)), $message);
        } else {
            $this->render('error');
        }
    }

    public function setRenderType($type)
    {
        if (in_array($type, array('json', 'xml'))) {
            $this->_renderType = $type;
        }
    }
}
