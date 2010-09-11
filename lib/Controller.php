<?php
/**
 * Controller 
 * 
 * @abstract
 */
abstract class Controller
{
    protected function before()
    { }

    protected function after()
    { }

    private function checkFileName($fileName)
    {
        if (!ctype_alnum(str_replace('_', '', $fileName))) {
            throw new Exception('invalid view file name.');
        }
        return true;
    }

    protected function redirect($path)
    {
        if (strpos($path, 'http') === 0) {
            header("Location: $path");
            exit;
        } else {
            header("Location: " . SERVER_URL . $path);
            exit;
        }
    }

    protected function render($fileName)
    {
        $this->checkFileName($fileName);
        require VIEWS_PATH . DIRECTORY_SEPARATOR . VIEW_HEADER_FILE_NAME . PHP_EXTENSION;
        require VIEWS_PATH . DIRECTORY_SEPARATOR . $fileName . PHP_EXTENSION;
        require VIEWS_PATH . DIRECTORY_SEPARATOR . VIEW_FOOTER_FILE_NAME . PHP_EXTENSION;
    }

    protected function renderOnly($fileName)
    {
        $this->checkFileName($fileName);
        require VIEWS_PATH . DIRECTORY_SEPARATOR . $fileName . PHP_EXTENSION;
    }

    protected function renderXml($fileName)
    {
        $this->checkFileName($fileName);
        header('Content-Type: application/xml');
        require VIEWS_PATH . DIRECTORY_SEPARATOR . $fileName . PHP_EXTENSION;
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

    public function __call($methodName, $args)
    {
        if ('Action' === substr($methodName, -6)) {
            throw new NotFoundException;
        }
    }
}
