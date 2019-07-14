<?php
namespace core;

/**
 * Básicamente la conexión a la base de datos, con los passwd para cada esquema.
 * @author dani
 *
 */
class ConfigDB {
	
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
		echo "<br>db: $database";
		$this->data = include ConfigGlobal::DIR_PWD.'/'.$database.'.inc';
	}
}