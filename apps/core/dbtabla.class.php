<?php
namespace core;
class DBTabla {
	/**
	 * oDbl de Esquema
	 *
	 * @var object
	 */
	 private $oDbl;
	/**
	 * Tablas de Esquema
	 *
	 * @var array
	 */
	 private $aTablas;
	/**
	 * Esquema de Referencia de Esquema
	 *
	 * @var string
	 */
	 private $sRef;
	/**
	 * Esquema a Crear de Esquema
	 *
	 * @var string
	 */
	 private $sNew;
	/**
	 * Directorio donde poner los logs de Esquema
	 *
	 * @var string
	 */
	 private $sdir;
	/**
	 * Fichero con el volcado del esquema de referencia de Esquema
	 *
	 * @var string
	 */
	 private $sfileRef;
	/**
	 * Fichero con el volcado del nuevo esquema de Esquema
	 *
	 * @var string
	 */
	 private $sfileNew;
	/**
	 * Fichero con el log de la accion de Esquema
	 *
	 * @var string
	 */
	 private $sfileLog;

	 /* CONSTRUCTOR -------------------------------------------------------------- */
 
	 /**
	 * Constructor de la classe.
	 *
	 */
	function __construct() {
		
	}

	/* METODES GET i SET --------------------------------------------------------*/
	/**
	 * Recupera l'atribut oDbl de Grupo
	 *
	 * @return object oDbl
	 */
	private function getoDbl() {
		return $this->oDbl;
	}
	private function getUserDb() {
		$this->sUserDb = 'dbCreate';
		return $this->sUserDb;
	}
	public function getDir() {
		$this->sdir = empty($this->sdir)? ConfigGlobal::$directorio.'/log/db' : $this->sdir;
		return $this->sdir;
	}
	public function setDir($dir) {
		$this->sdir = $dir;
	}
	public function getTablas() {
		return $this->aTablas;
	}
	public function setTablas($tablas) {
		$this->aTablas = $tablas;
	}
	public function getNew() {
		return $this->sNew;
	}
	public function setNew($esquema) {
		$this->sNew = $esquema;
	}
	public function getRef() {
		return $this->sRef;
	}
	public function setRef($esquema) {
		$this->sRef = $esquema;
	}
	public function getDb() {
		return $this->sDb;
	}
	public function setDb($db) {
		$this->sDb = $db;
		$this->setFileRef($this->getDir().'/dbRef.'.$db.'.sql');
	}
	public function getFileRef() {
		$this->sfileRef = empty($this->sfileRef)? $this->getDir().'/dbRef.'.$this->getDb().'.sql': $this->sfileRef;
		return $this->sfileRef;
	}
	public function setFileRef($fileRef) {
		$this->sfileRef = $fileRef;
	}
	public function getFileLog() {
		$this->sfileLog = empty($this->sfileLog)? $this->getDir().'/pg_dump_eror.sql': $this->sfileLog;
		return $this->sfileLog;
	}
	public function setFileLog($fileLog) {
		$this->sfileLog = $fileLog;
	}
	public function getFileNew() {
		$this->sfileNew = empty($this->sfileNew)? $this->getDir().'/dbNew.'.$this->getDb().'.sql': $this->sfileNew;
		return $this->sfileNew;
	}
	public function setFileNew($fileNew) {
		$this->sfileNew = $fileNew;
	}

	public function cambiar_nombre() {
		$dump = file_get_contents($this->getFileRef());
		// cambiar nombre esquema
		$dump_nou = str_replace($this->getRef(),$this->getNew(),$dump);
		$d = file_put_contents($this->getFileNew(), $dump_nou);
		if ($d === false) exit (_("Error al escribir el fichero"));

	}
	public function leer() {
		$sTablas = '';
		foreach ($this->aTablas as $tabla) {
			$sTablas .= "-t \\\"".$this->getRef()."\\\".$tabla ";
		}
		// leer esquema
		$command = "/usr/bin/pg_dump -a $sTablas";
		$command .= "--file=".$this->getFileRef()." ";
		$command .= "--user=dbCreate ";
		$command .= " ".$this->getDb()." > ".$this->getFileLog()." 2>&1"; 
		passthru($command); // no output to capture so no need to store it
		// read the file, if empty all's well
		$error = file_get_contents($this->getFileLog());
		if(trim($error) != '')
			exit("PG_DUMP ERRROR IN COMMAND: $command\n-----\n$error\n");
	}

	public function importar() {
		$filename = $this->getFileRef();
		$command = "/usr/bin/psql -q ";
		$command .= "--pset pager=off ";
		$command .= "--file=".$this->getFileNew()." ";
		$command .= "--user=dbCreate ";
		$command .= " ".$this->getDb()." > ".$this->getFileLog()." 2>&1"; 
		passthru($command); // no output to capture so no need to store it
		// read the file, if empty all's well
		$error = file_get_contents($this->getFileLog());
	}

	public function copiar() {
		$this->leer();
		$this->cambiar_nombre();
		$this->importar();
	}

}
?>
