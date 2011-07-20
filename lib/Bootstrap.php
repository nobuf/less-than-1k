<?php
defined('PHP_EXTENSION') || define('PHP_EXTENSION', '.php');
defined('VIEW_HEADER_FILE_NAME') || define('VIEW_HEADER_FILE_NAME', 'header');
defined('VIEW_FOOTER_FILE_NAME') || define('VIEW_FOOTER_FILE_NAME', 'footer');
defined('VIEW_ERROR_FILE_NAME') || define('VIEW_ERROR_FILE_NAME', 'error');



function bootstrap_autoload($className)
{
    $filePath = str_replace('_', DIRECTORY_SEPARATOR, $className) . PHP_EXTENSION;
    $includePaths = explode(PATH_SEPARATOR, get_include_path());
    foreach ($includePaths as $includePath) {
        if (file_exists($includePath . DIRECTORY_SEPARATOR . $filePath)) {
            require $filePath;
            return;
        }
    }
}
spl_autoload_register('bootstrap_autoload');

//  standardize the error handling
function exception_error_handler($errno, $errstr, $errfile, $errline) {
    if (error_reporting() === 0) {
        return;
    }
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}
set_error_handler("exception_error_handler");

function fatal_error_handler()
{
    $error = error_get_last();
    if (isset($error['type']) && ($error['type'] === E_ERROR || $error['type'] === E_PARSE || $error['type'] === E_COMPILE_ERROR)) {
        ob_clean();
        header($_SERVER['SERVER_PROTOCOL'] . " 500 Internal Server Error");
        require VIEWS_PATH . DIRECTORY_SEPARATOR . VIEW_HEADER_FILE_NAME . PHP_EXTENSION;
        require VIEWS_PATH . DIRECTORY_SEPARATOR . VIEW_ERROR_FILE_NAME . PHP_EXTENSION;
        require VIEWS_PATH . DIRECTORY_SEPARATOR . VIEW_FOOTER_FILE_NAME . PHP_EXTENSION;
        exit;
    }
}
register_shutdown_function('fatal_error_handler');



class Bootstrap
{
    public static function run()
    {
        ob_start();

        //  URL routing
        try {
            $idx = strpos($_SERVER['REQUEST_URI'], '?');
            $requestUri = explode('/', ($idx !== false ? substr($_SERVER['REQUEST_URI'], 0, $idx) : $_SERVER['REQUEST_URI']));
            $count = count($requestUri);
            if ($count < 2) {
                throw new Exception('invalid request uri.');
            } else {
                if (isset($requestUri[2])) {
                    $exIdx = strrpos($requestUri[2], '.');
                    if ($exIdx !== false) {
                        $actionName = substr($requestUri[2], 0, -1*$exIdx);
                        $extension = strtolower(substr($requestUri[2], -1*$exIdx+1));
                    } else {
                        $actionName = $requestUri[2];
                    }
                } else {
                    $actionName = 'index';
                }
                if (ctype_alnum($requestUri[1])) {
                    $controllerName = ucfirst(strtolower($requestUri[1]));
                    if ($controllerName === 'Error') { // pre-defined controller name
                        throw new NotFoundException;
                    }
                } else if (empty($requestUri[1])) {
                    $controllerName = 'Index';
                } else {
                    throw new NotFoundException;
                }
                $className = "{$controllerName}Controller";
                $actionFuncName = "{$actionName}Action"; 
                $filePath = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . $className . PHP_EXTENSION;
                //  Mac OS X probably ignores cases, but CentOS doesn't
                //  /getInfo controllers/GetinfoController.php OK
                //  /getInfo controllers/GetInfoController.php works on Mac, but fails on CentOS
                if (!file_exists(realpath($filePath))) {
                    throw new NotFoundException;
                }
                require $filePath;
                try {
                    $obj = new $className($controllerName, $actionName);
                    $obj->before();
                    $obj->$actionFuncName();
                    $obj->after();
                } catch (ApplicationException $e) {
                    self::dispatchError($e, (isset($extension) ? $extension : null), $e->getMessage(), 500);
                }
            }
        } catch (NotFoundException $e) {
            self::dispatchError($e, (isset($extension) ? $extension : null), 'Not Found', 404);
        } catch (Exception $e) {
            self::dispatchError($e, (isset($extension) ? $extension : null), 'Internal Server Error', 500);
        }

        ob_end_flush();
    }

    private static function dispatchError(Exception $e, $renderType = null, $message = null, $status = 500)
    {
        Log::error($e);
        ob_clean();
        header(sprintf('%s %d %s', $_SERVER['SERVER_PROTOCOL'], $status, self::getMessageByStatus($status)));

        require APPLICATION_PATH . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . 'ErrorController' . PHP_EXTENSION;
        $error = new ErrorController;
        if (!empty($renderType)) {
            $error->setRenderType($renderType);
        }
        $error->indexAction($message);
    }

    private static function getMessageByStatus($status)
    {
        switch ($status) {
        case 404:
            return 'Not Found';
        case 500:
        default:
            return 'Internal Server Error';
        }
    }
}
