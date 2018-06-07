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
		$this->data = include '/home/dani/orbix_local/conf/'.$database.'.inc';
	}
}