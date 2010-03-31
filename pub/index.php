<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../app'));

// Define application environment
// Setting sample in httpd.conf:
// <Directory /var/www/html>
// SetEnv APPLICATION_ENV development
// </Directory>
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

define('VIEWS_PATH', APPLICATION_PATH . '/views');

switch (APPLICATION_ENV) {
case 'development':
    set_include_path(implode(PATH_SEPARATOR, array(
        realpath(APPLICATION_PATH . '/../lib'),
        realpath(APPLICATION_PATH . '/models'),
        get_include_path(),
    )));
    break;
case 'production':
    //  set include_path in php.ini might be fater
default:
    break;
}



function __autoload($className)
{
    $filePath = str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
    $includePaths = explode(PATH_SEPARATOR, get_include_path());
    foreach ($includePaths as $includePath) {
        if (file_exists($includePath . DIRECTORY_SEPARATOR . $filePath)) {
            require $filePath;
            return;
        }
    }
}

//  standardize the error handling
function exception_error_handler($errno, $errstr, $errfile, $errline) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}
set_error_handler("exception_error_handler");

function fatal_error_handler()
{
    $error = error_get_last();
    if (isset($error['type']) && ($error['type'] === E_ERROR || $error['type'] === E_PARSE || $error['type'] === E_COMPILE_ERROR)) {
        ob_clean();
        header($_SERVER['SERVER_PROTOCOL'] . " 500 Internal Server Error");
        require VIEWS_PATH . '/header.php';
        require VIEWS_PATH . '/error.php';
        require VIEWS_PATH . '/footer.php';
        exit;
    }
}
register_shutdown_function('fatal_error_handler');





ob_start();

//  URL routing
try {
    $idx = strpos($_SERVER['REQUEST_URI'], '?');
    if ($idx !== false) {
        $_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], 0, $idx);
    }
    $requestUri = explode('/', $_SERVER['REQUEST_URI']);
    $count = count($requestUri);
    if ($count < 2) {
        throw new Exception('invalid request uri.', 500);
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
        $filePath = APPLICATION_PATH . '/controllers/' . $className . '.php';
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
            dispatch_error($e, (isset($extension) ? $extension : null), $e->getMessage());
        }
    }
} catch (Exception $e) {
    dispatch_error($e, (isset($extension) ? $extension : null), 'Internal Server Error');
}

ob_end_flush();





function dispatch_error($e, $renderType = null, $message = null)
{
    Log::error($e);
    ob_clean();
    header($_SERVER['SERVER_PROTOCOL'] . " 500 Internal Server Error");

    require APPLICATION_PATH . '/controllers/ErrorController.php';
    $error = new ErrorController;
    if (!empty($renderType)) {
        $error->setRenderType($renderType);
    }
    $error->indexAction($message);
}
