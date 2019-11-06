<?php
namespace core;

/**
 * Básicamente la conexión a la base de datos, con los passwd para cada esquema.
 * @author dani
 *
 */
class ConfigDB {
	
	private $data;
	
	public function __construct($database) {
	    $this->setDataBase($database);
	}
	
	public function getEsquema($esquema) {
		$data = $this->data['default'];
		$data['schema'] = $esquema;
		//sobreescribir los valores default
		foreach ($this->data[$esquema] as $key => $value){
			$data[$key] = $value;
		}
		
		return $data;
	}

	public function setDataBase($database) {
		if (ConfigGlobal::WEBDIR == 'pruebas') {
			$database = 'pruebas-'.$database;
		}
		$this->data = include ConfigGlobal::DIR_PWD.'/'.$database.'.inc';
	}
	
	public function addEsquema($database,$esquema,$esquema_pwd){
	    $this->setDataBase($database);
        $this->data[$esquema] = ['user' => $esquema, 'password' => $esquema_pwd ];
        
		$filename = ConfigGlobal::DIR_PWD.'/'.$database.'.inc';
	    file_put_contents($filename, '<?php return ' . var_export($this->data, true) . ' ;');
	}
}