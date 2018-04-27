<?php
	namespace luxury\base;
	
	use luxury\Db;
	
	abstract class Model
	{
		public $attributes = [];
		public $rules = [];
		
		public function __construct()
		{
			Db::instance();
		}
		
		public function load($data)
		{
			foreach($this->attributes as $name => $value)
			{
				if(isset($data[$name]))
				{
					$this->attributes[$name] = $data[$name];
				}
			}
		}
		
		public function save($table, $valid = true)
		{
			if($valid)
			{
				$tbl = \R::dispense($table);
			}
			else
			{
				$tbl = \R::xdispense($table);
			}
			
			foreach($this->attributes as $name => $value)
			{
				$tbl->$name = $value;
			}
			return \R::store($tbl);
		}
		
		public function update($table, $id)
		{
			$bean = \R::load($table, $id);
			foreach($this->attributes as $name => $value)
			{
				$bean->$name = $value;
			}
			return \R::store($bean);
		}
	}