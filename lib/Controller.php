<?php
/**
 * Controller 
 * 
 * @abstract
 */
abstract class Controller
{
    //protected $_auth; // Zend_Auth::getInstance()

    public function __construct()
    { }

    protected function before()
    { }

    protected function after()
    { }

    private function checkFileName($fileName)
    {
        if (!ctype_alnum(str_replace('_', '', $fileName))) {
            throw new Exception('invalid view file name.', 500);
        }
        return true;
    }

    protected function render($fileName)
    {
        $this->checkFileName($fileName);
        require VIEWS_PATH . '/header.php';
        require VIEWS_PATH . "/$fileName.php";
        require VIEWS_PATH . '/footer.php';
    }
    protected function renderOnly($fileName)
    {
        $this->checkFileName($fileName);
        require VIEWS_PATH . "/$fileName.php";
    }

    protected function renderXml($fileName)
    {
        $this->checkFileName($fileName);
        header("Content-Type: application/xml");
        require VIEWS_PATH . "/$fileName.php";
    }

    protected function renderJson($data, $callback = null)
    {
        header('Content-Type: application/json');
        if (!empty($callback)) {
            echo $callback, '(', json_encode($data), ')';
        } else {
            echo json_encode($data);
        }
    }

    protected function __call($methodName, $args)
    {
        if ('Action' === substr($methodName, -6)) {
            throw new NotFoundException;
        }
    }
}
