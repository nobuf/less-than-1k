<?php
/**
 *  You may need to add some lines to httpd.conf like this:
 *
<VirtualHost 127.0.0.1:80>
    DocumentRoot "/Users/nobu/work/less-than-1k/samples/deadsimple/pub"
    ServerName deadsimple.less-than-1k
    ErrorLog "/Users/nobu/logs/less-than-1k.error_log"
    CustomLog "/Users/nobu/logs/less-than-1k.access_log" common
    <Directory /Users/nobu/work/less-than-1k/samples/deadsimple/pub>
    allow from all

    SetEnv APPLICATION_ENV development

    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} -s [OR]
    RewriteCond %{REQUEST_FILENAME} -l [OR]
    RewriteCond %{REQUEST_FILENAME} -d
    RewriteRule ^.*$ - [NC,L]
    RewriteRule ^.*$ index.php [NC,L]
    </Directory>
</VirtualHost>

 *  Also, this line may be needed in /etc/hosts:
 *      127.0.0.1 deadsimple.less-than-1k
 */

defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../app'));

define('VIEWS_PATH', APPLICATION_PATH . '/views');

set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../../../lib'),
    realpath(APPLICATION_PATH . '/models'),
    get_include_path(),
)));

require 'Bootstrap.php';
Bootstrap::run();
