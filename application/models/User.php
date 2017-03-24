<?php
//C:\AppServ\www\cca\application\modules\exam\models\Exam.php

class Exam_Model_User{ 

	protected $_exam_id;
	protected $_name;

	public function __construct(array $options = null)
	{
	    if (is_array($options)) {
	        $this->setOptions($options);
	    }
	}
	public function __set($name, $value)
	{
	    $method = 'set' . $name;
	    if (('mapper' == $name) || !method_exists($this, $method)) {
	        throw new Exception('Invalid user property'); 
	   }
	    $this->$method($value);
	}
	public function __get($name)
	{
	    $method = 'get' . $name;
	    if (('mapper' == $name) || !method_exists($this, $method)) {
	        throw new Exception('Invalid user property'); 
	    };
	    return $this->$method();
	}
	public function setOptions(array $options)
	{
	    $methods = get_class_methods($this);
	    foreach ($options as $key => $value) {
	        $method = 'set' . ucfirst($key);
	        if (in_array($method, $methods)) {
	            $this->$method($value);
	        }
	    }
	    return $this;
	}
	

	public function setUser_id($text){ 
		$this->_user_id=$text;
	return $this;
	}

	public function getUser_id(){ 
	return $this->_user_id;
	}

	public function setName($text){ 
		$this->_name=$text;
	return $this;
	}

	public function getName(){ 
	return $this->_name;
	}



}