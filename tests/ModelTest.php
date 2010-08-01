<?php

require_once 'PHPUnit/Framework.php';

define('PHP_EXTENSION', '.php');
define('DB_NAME', 'demo');

require dirname(__FILE__) . '/../lib/DB.php';
require dirname(__FILE__) . '/../lib/Model.php';
 
class ModelTest extends PHPUnit_Framework_TestCase
{
    public function testLoadMongoInstance()
    {
        $demoObj = new DemoModel;
        $data = array('foo' => 'bar');
        $demoObj->insert($data);
        $results = $demoObj->findOne($data, array('foo'));
        $this->assertEquals('bar', $results['foo']);
        $demoObj->drop();
    }
}


class DemoModel extends Model
{
    protected $_collectionName = 'demo';
}
