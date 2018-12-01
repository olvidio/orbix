<?php
namespace core;

class Config {
	
	private $data;
	
	public function getEsquema($esquema) {
		$data = $this->data['default'];
		$data['schema'] = $esquema;
		//sobreescribir los valores default
		foreach ($this->data[$esquema] as $key => $value){
			$data[$key] = $value;
		}
		
		return $data;
	}

	public function __construct($database)
	{
		if (ConfigGlobal::WEBDIR == 'pruebas') {
			$database = 'pruebas-'.$database;
		}
		$this->data = include ConfigGlobal::DIR_PWD.'/'.$database.'.inc';
	}
}