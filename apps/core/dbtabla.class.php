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
		$this->setFileRef($this->getDir().'/dbRef'.$this->getRef().'.'.$db.'.sql');
	}
	public function getFileRef() {
		$this->sfileRef = empty($this->sfileRef)? $this->getDir().'/dbRef'.$this->getRef().'.'.$this->getDb().'.sql': $this->sfileRef;
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
		$this->sfileNew = empty($this->sfileNew)? $this->getDir().'/dbNew'.$this->getNew().'.'.$this->getDb().'.sql': $this->sfileNew;
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
	            $oConfigDB = new ConfigDB('comun'); //de la database comun
	            $config = $oConfigDB->getEsquema($esquema); //de la database comun
	            break;
	        case 'sv':
	            $oConfigDB = new ConfigDB('sv'); //de la database sv
	            $config = $oConfigDB->getEsquema($esquema); //de la database sv
	            break;
	        case 'sf':
	            $oConfigDB = new ConfigDB('sf'); //de la database sf
	            $config = $oConfigDB->getEsquema($esquema); //de la database sf
	            break;
	    }
	    
	    return $config;
	}
	protected function getConexion($new='') {
	    $config = $this->getParamConexion($new);

	    $host = $config['host'];
	    $port = $config['port'];
	    $dbname = $config['dbname'];
	    $user = $config['user'];
	    $password = $config['password'];
	    //opcionales
	    $str_conexio = '';
	    if (!empty($config['sslmode'])) {
	        $str_conexio .= empty($str_conexio)? '' : '&';
	        $str_conexio .= "sslmode=".$config['sslmode'];
	    }
	    if (!empty($config['sslcert'])) {
	        $str_conexio .= empty($str_conexio)? '' : '&';
	        $str_conexio .= "sslcert=".$config['sslcert'];
	    }
	    if (!empty($config['sslkey'])) {
	        $str_conexio .= empty($str_conexio)? '' : '&';
	        $str_conexio .= "sslkey=".$config['sslkey'];
	    }
	    if (!empty($config['sslrootcert'])) {
	        $str_conexio .= empty($str_conexio)? '' : '&';
	        $str_conexio .= "sslrootcert=".$config['sslrootcert'];
	    }
	    if (!empty($str_conexio)) {
	        $str_conexio = '?'.$str_conexio;
	    }
	    
	    $password_encoded = urlencode ($password);
	    $dsn = "postgresql://$user:$password_encoded@$host:$port/".$dbname.$str_conexio;
	    
	    $this->dbname = $dbname;
	    return $dsn;
	}
	
	protected function getDbName() {
	    $this->getConexion();
	    return $this->dbname;
	}

	protected function getConexionPDO($new='') {
	    $config = $this->getParamConexion($new);

	    $host = $config['host'];
	    $port = $config['port'];
	    $dbname = $config['dbname'];
	    $user = $config['user'];
	    $password = $config['password'];
	    //opcionales
	    $str_conexio = '';
	    if (!empty($config['sslmode'])) {
	        $str_conexio .= empty($str_conexio)? '' : ';';
	        $str_conexio .= "sslmode=".$config['sslmode'];
	    }
	    if (!empty($config['sslcert'])) {
	        $str_conexio .= empty($str_conexio)? '' : ';';
	        $str_conexio .= "sslcert=".$config['sslcert'];
	    }
	    if (!empty($config['sslkey'])) {
	        $str_conexio .= empty($str_conexio)? '' : ';';
	        $str_conexio .= "sslkey=".$config['sslkey'];
	    }
	    if (!empty($config['sslrootcert'])) {
	        $str_conexio .= empty($str_conexio)? '' : ';';
	        $str_conexio .= "sslrootcert=".$config['sslrootcert'];
	    }
	    
	    $password_encoded = urlencode ($password);
	    $dsn = "postgresql://$user:$password_encoded@$host:$port/".$dbname.$str_conexio;
	    
	    // OJO Con las comillas dobles para algunos caracteres del password ($...)
	    //$dsn = 'pgsql:host='.$host.' port='.$port.' dbname=\''.$dbname.'\' user=\''.$user.'\' password=\''.$password.'\'';
	    $dsn = 'pgsql:host='.$host.';port='.$port.';dbname=\''.$dbname.'\';user=\''.$user.'\';password=\''.$password.'\';'.$str_conexio;
	    
	    return $dsn;
	}
	
	public function leer_remote() {
		$sTablas = '';
		foreach ($this->aTablas as $tabla => $param) {
			$sTablas .= " -t \\\\\\\"".$this->getRef()."\\\\\\\".$tabla ";
		}
	    //ssh user@remote_machine "pg_dump -U dbuser -h localhost -C --column-inserts" \
	    //    > backup_file_on_your_local_machine.sql
	    //  /usr/bin/ssh aquinate@192.168.200.16 "/usr/bin/pg_dump -s --schema=\\\"Acse-crAcse\\\"
	    //          -U postgres -h 127.0.0.1 pruebas-comun" > /var/www/pruebas/log/db/Acse-crAcse.comun.sql

	    $dbname = $this->getDbName();
	    
	    // leer esquema
	    $command_ssh = "/usr/bin/ssh aquinate@192.168.200.16";
	    $command_db = "/usr/bin/pg_dump -a".$sTablas." ";
	    $command_db .= "-U postgres -h 127.0.0.1 $dbname";
	    
	    //$command .= " > ".$this->getFileLog()." 2>&1";
	    
	    $command = "$command_ssh \"$command_db\" > ".$this->getFileRef();
	    //echo "$command<br>";
	    passthru($command); // no output to capture so no need to store it
	    // read the file, if empty all's well
	    /*
	     $error = file_get_contents($this->getFileLog());
	     if(trim($error) != '') {
	     if (ConfigGlobal::is_debug_mode()) {
	     echo sprintf("PSQL ERROR IN COMMAND: %s<br> mirar: %s<br>",$command,$this->getFileLog());
	     }
	     }
	     */
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
	    
		$command = "PGOPTIONS='--client-min-messages=warning' /usr/bin/psql -q -X -t ";
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

	/**
	 * Para la base de datos comun, que está en otro servidor y además con otra versión,
	 * No sirve el pg_dump (solo funciona con versiones iguales en los dos extremos)
	 */
	public function copiar_remote() {
		$this->leer_remote();
		$this->cambiar_nombre();
		$this->importar();
		$this->actualizar_schema();
	}
	public function copiar() {
		$this->leer();
		$this->cambiar_nombre();
		$this->importar();
		$this->actualizar_schema();
	}

}
