<?php
namespace sf\db;
use sf\db;
use sf\base\Component; 

class ConnectionSf extends Component
{
    public static function createObject($name)
    {
        $config = require(SF_PATH. '/config/'. $name .'.php');
        $instance = new  $config['class']();;
		unset($config['class']);
		foreach($config as $key => $val) {
			$instance->$key = $val;
		}
        $instance->init();
		return $instance;
    }

}
