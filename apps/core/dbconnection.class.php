<?php
namespace core;

class dbConnection {

	private $config;
	private $esquema;
	
	public function setEsquema($esquema) {
		$this->esquema = $esquema;
	}

	public function setConfig($config) {
		$this->config = $config;
	}

	public function __construct($config){
		$this->config = $config;
    }

	private function getStrConexio() {
		$config = $this->config;

        $str_conexio = "pgsql:";
		$str_conexio .= "host='".$config['host']."'";
		$str_conexio .= " sslmode='".$config['sslmode']."'";
		$str_conexio .= " port='".$config['port']."'";
		$str_conexio .= " dbname='".$config['dbname']."'";
		$str_conexio .= " user='".$config['user']."'";
		$str_conexio .= " password='".$config['password']."'";

		return $str_conexio;
	}
	public function getPDO() {
		$datestyle =$this->config['datestyle']; 
		$esquema =$this->config['schema']; 
		$str_conexio = $this->getStrConexio();		
		$oDB = new \PDO($str_conexio);
		$oDB->exec("SET search_path TO \"$esquema\"");
		$oDB->exec("SET DATESTYLE TO '$datestyle'");
		
		return $oDB;
	}
	
	public function getPDOListas() {
		$config = $this->config;

        $str_conexio = $config['driver'].":".$config['dbname'];
		/* No se porque no va todo en una variable...
		$str_conexio .= ", user='".$config['user']."'";
		$str_conexio .= ", password='".$config['password']."'";
		$str_conexio .= ", array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_WARNING)";
		$oDB = new \PDO($str_conexio);
		*/
		$oDB = new \PDO($str_conexio,$config['user'], $config['password'], array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_WARNING, \PDO::ATTR_TIMEOUT => 3));
		
		return $oDB;
	}
}