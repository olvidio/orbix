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
	
	/**
	 * Destructor de la classe.
	 *
	 * Elimina todos los ficheros creados
	 *
	 */
	function __destruct() {
	    // No los borro si estoy en debug
	    if (!ConfigGlobal::is_debug_mode()) {
	        $this->deleteFile($this->getFileNew());
	        $this->deleteFile($this->getFileRef());
	        $this->deleteFile($this->getFileLog());
	    }
	}
	/* METODES GET i SET --------------------------------------------------------*/
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
		$this->sfileLog = empty($this->sfileLog)? $this->getDir().'/pg_error.'.$this->getDb().'.sql': $this->sfileLog;
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
		$esqemaRef = $this->getRef();
		$crRef = strtok($esqemaRef,'-');
		$dlRef = strtok('-');
		$esqemaNew = $this->getNew();
		$crNew = strtok($esqemaNew,'-');
		$dlNew = strtok('-');

		$dump = file_get_contents($this->getFileRef());
		// cambiar nombre esquema
		$dump_nou = str_replace($this->getRef(),$this->getNew(),$dump);
		// cambiar nombre por defecto de la dl i r
		$pattern = "/(SET DEFAULT\s*')$crRef(')/";
		$replacement = "$1$crNew$2";
		$dump_nou = preg_replace($pattern, $replacement, $dump_nou);
		$pattern = "/(SET DEFAULT\s*')$dlRef(')/";
		$replacement = "$1$dlNew$2";
		$dump_nou = preg_replace($pattern, $replacement, $dump_nou);

		$d = file_put_contents($this->getFileNew(), $dump_nou);
		if ($d === false) exit (_("error al escribir el fichero"));
	}
	
	protected function getParamConexion($new='') {
	    // No he conseguido que funcione con ~/.pgpass.
	    if (empty($new)) {
	        $esquema = $this->getRef();
	    } else {
	        $esquema = $this->getNew();
	    }
	    switch ($this->sDb) {
	        case 'comun':
	            $oConfig = new Config('comun'); //de la database comun
	            $config = $oConfig->getEsquema($esquema); //de la database comun
	            break;
	        case 'sv':
	            $oConfig = new Config('sv'); //de la database sv
	            $config = $oConfig->getEsquema($esquema); //de la database sv
	            break;
	        case 'sf':
	            $oConfig = new Config('sf'); //de la database sf
	            $config = $oConfig->getEsquema($esquema); //de la database sf
	            break;
	    }
	    
	    return $config;
	}
	protected function getConexion($new='') {
	    $config = $this->getParamConexion($new);

	    $host = $config['host'];
	    //$sslmode = $config['sslmode'];
	    $port = $config['port'];
	    $dbname = $config['dbname'];
	    $user = $config['user'];
	    $password = $config['password'];
	    
	    $password_encoded = rawurlencode ($password);
	    $dsn = "postgresql://$user:$password_encoded@$host:$port/".$dbname;
	    
	    return $dsn;
	}

	protected function getConexionPDO($new='') {
	    $config = $this->getParamConexion($new);

	    $host = $config['host'];
	    //$sslmode = $config['sslmode'];
	    $port = $config['port'];
	    $dbname = $config['dbname'];
	    $user = $config['user'];
	    $password = $config['password'];
	    
	    // OJO Con las comillas dobles para algunos caracteres del password ($...)
	    $str_conexio = 'pgsql:host='.$host.' port='.$port.' dbname=\''.$dbname.'\' user=\''.$user.'\' password=\''.$password.'\'';

	    return $str_conexio;
	}

	public function leer() {
		$sTablas = '';
		foreach ($this->aTablas as $tabla => $param) {
			$sTablas .= "-t \\\"".$this->getRef()."\\\".$tabla ";
		}
		//pg_dump --dbname=postgresql://username:password@host:port/database > file.sql
		// crear archivo con el password
		$dsn = $this->getConexion();
		// leer esquema
		$command = "/usr/bin/pg_dump -a $sTablas";
		$command .= "--file=".$this->getFileRef()." ";
		$command .= "\"".$dsn."\"";
		$command .= " > ".$this->getFileLog()." 2>&1"; 
		passthru($command); // no output to capture so no need to store it
		// read the file, if empty all's well
		$error = file_get_contents($this->getFileLog());
		if(trim($error) != '') {
			if (ConfigGlobal::is_debug_mode()) {
			    echo sprintf("PG_DUMP ERROR IN COMMAND: %s<br> mirar: %s<br>",$command,$this->getFileLog());
			}
		}
	}

	public function importar() {
	    // crear archivo con el password
	    $dsn = $this->getConexion(1);
	    
		$command = "/usr/bin/psql -q ";
		$command .= "--pset pager=off ";
		$command .= "--file=".$this->getFileNew()." ";
		$command .= "\"".$dsn."\"";
		$command .= " > ".$this->getFileLog()." 2>&1"; 
		passthru($command); // no output to capture so no need to store it
		// read the file, if empty all's well
		$error = file_get_contents($this->getFileLog());
		if(trim($error) != '') {
			if (ConfigGlobal::is_debug_mode()) {
			    echo sprintf("PSQL ERROR IN COMMAND: %s<br> mirar en: %s<br>",$command,$this->getFileLog());
			}
		}
	}
    /**
     * Para actualizar el campo id_schema
     */	
	public function actualizar_schema() {
        $str_conexio = $this->getConexionPDO(1);
        $oDbl = new \PDO($str_conexio);
		foreach ($this->aTablas as $tabla => $param) {
		    if (!empty($param['id_schema']) && $param['id_schema'] == 'yes') {
    			$sqlSchema = "UPDATE $tabla SET id_schema = DEFAULT;";
                if ($oDbl->query($sqlSchema) === false) {
                    $sClauError = 'DBTabla.schema.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                    return false;
                }
		    }
		}
	}

	public function copiar() {
		$this->leer();
		$this->cambiar_nombre();
		$this->importar();
		$this->actualizar_schema();
	}

}
