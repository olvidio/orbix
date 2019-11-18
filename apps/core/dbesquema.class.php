<?php
namespace core;
class DBEsquema {
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
	/**
	 * Fichero con la lista de secuencias a actualizar del Esquema
	 *
	 * @var string
	 */
	 private $sfileSeq;
	 
	 private $sDb;
	 private $user;
	 private $password;

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
	public function getRegionRef() {
		return $this->sRegionRef;
	}
	public function setRegionRef($region) {
		$this->sRegionRef = $region;
	}
	public function getDlRef() {
		return $this->sDlRef;
	}
	public function setDlRef($dl) {
		$this->sDlRef = $dl;
	}
	public function getRegionNew() {
		return $this->sRegionNew;
	}
	public function setRegionNew($region) {
		$this->sRegionNew = $region;
	}
	public function getDlNew() {
		return $this->sDlNew;
	}
	public function setDlNew($dl) {
		$this->sDlNew = $dl;
	}

	public function getNew() {
		$this->sNew = $this->getRegionNew().'-'.$this->getDlNew();
		switch($this->getDb()) {
			case 'comun':
				break;
			case 'sv':
				$this->sNew .= 'v';
				break;
			case 'sf':
				$this->sNew .= 'f';
				break;
		}
		return $this->sNew;
	}
	public function setNew($esquema) {
		$this->sNew = $esquema;
	}
	public function getRef() {
		$this->sRef = $this->getRegionRef().'-'.$this->getDlRef();
		switch($this->getDb()) {
			case 'comun':
				break;
			case 'sv':
				$this->sRef .= 'v';
				break;
			case 'sf':
				$this->sRef .= 'f';
				break;
		}
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
	}
	public function getDbRef() {
		if (empty($this->sDbRef)) {
			$this->sDbRef = $this->sDb;
		}
		return $this->sDbRef;
	}
	public function setDbRef($db) {
		$this->sDbRef = $db;
	}
	public function getFileRef() {
		$this->sfileRef = empty($this->sfileRef)? $this->getDir().'/'.$this->getRef().'.'.$this->getDb().'.sql': $this->sfileRef;
		return $this->sfileRef;
	}
	public function setFileRef($fileRef) {
		$this->sfileRef = $fileRef;
	}
	public function getFileLog() {
		$this->sfileLog = empty($this->sfileLog)? $this->getDir().'/'.$this->getNew().'.pg_error.sql': $this->sfileLog;
		return $this->sfileLog;
	}
	public function setFileLog($fileLog) {
		$this->sfileLog = $fileLog;
	}
	public function getFileNew() {
		$this->sfileNew = empty($this->sfileNew)? $this->getDir().'/'.$this->getNew().'.'.$this->getDb().'.sql': $this->sfileNew;
		return $this->sfileNew;
	}
	public function setFileNew($fileNew) {
		$this->sfileNew = $fileNew;
	}
	public function getFileSeq() {
		$this->sfileSeq = empty($this->sfileSeq)? $this->getDir().'/'.$this->getNew().'.reset_seq.sql': $this->sfileSeq;
		return $this->sfileSeq;
	}
	public function setFileSeq($fileSeq) {
		$this->sfileSeq = $fileSeq;
	}

	public function cambiar_nombre() {
		$dump = file_get_contents($this->getFileRef());
		// cambiar nombre esquema
		$dump_nou = str_replace($this->getRef(),$this->getNew(),$dump);
		// comentar "CREATE SCHEMA; que ya está creado
		$dump_nou = str_replace('CREATE SCHEMA','-- CREATE SCHEMA',$dump_nou);
		// cambiar nombre por defecto de la dl i r
		$pattern = "/(SET DEFAULT\s*')".$this->getRegionRef()."(')/";
		$replacement = "$1".$this->getRegionNew()."$2";
		$dump_nou = preg_replace($pattern, $replacement, $dump_nou);
		$pattern = "/(SET DEFAULT\s*')".$this->getDlRef()."(')/";
		$replacement = "$1".$this->getDlNew()."$2";
		$dump_nou = preg_replace($pattern, $replacement, $dump_nou);

		$d = file_put_contents($this->getFileNew(), $dump_nou);
		if ($d === false) printf(_("error al escribir el fichero"));
	}

	protected function getConexionSSH() {
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
	    $host = $config['host'];
	    $port = $config['port'];
	    $dbname = $config['dbname'];
	    $user = $config['user'];
	    $password = $config['password'];
	    
	    $password_encoded = urlencode ($password);
	    $dsn = "postgresql://$user:$password_encoded@$host:$port/".$dbname.$str_conexio;
	    
	    return $dsn;
	}
	
	protected function getConexion($new='') {
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
	    
	    return $dsn;
	}
	
	public function leer_remote() {
	    //ssh user@remote_machine "pg_dump -U dbuser -h localhost -C --column-inserts" \
	    //    > backup_file_on_your_local_machine.sql
	    
	    //pg_dump --dbname=postgresql://username:password@host:port/database > file.sql
	    // crear archivo con el password
	    $dsn = $this->getConexion();
		// leer esquema
		$command_ssh = "/usr/bin/ssh aquinate@192.168.200.16";
		$command_db = "/usr/bin/pg_dump -s --schema=\\\"".$this->getRef()."\\\" ";
		$command_db .= "\"".$dsn."\"";
		$command_db .= " > ".$this->getFileRef()." ";
		//$command .= " > ".$this->getFileLog()." 2>&1"; 
	    $command = "$command_ssh \"$command_db\"";	
		echo "$command<br>";
		passthru($command); // no output to capture so no need to store it
		// read the file, if empty all's well
		$error = file_get_contents($this->getFileLog());
		if(trim($error) != '') {
			if (ConfigGlobal::is_debug_mode()) {
				echo sprintf("PSQL ERROR IN COMMAND: %s<br> mirar: %s<br>",$command,$this->getFileLog());
			}
		}
	}
	public function leer() {
	    //pg_dump --dbname=postgresql://username:password@host:port/database > file.sql
	    // crear archivo con el password
	    $dsn = $this->getConexion();
		// leer esquema
		$command = "/usr/bin/pg_dump -s --schema=\\\"".$this->getRef()."\\\" ";
		$command .= "--file=".$this->getFileRef()." ";
		$command .= "\"".$dsn."\"";
		$command .= " > ".$this->getFileLog()." 2>&1"; 
		passthru($command); // no output to capture so no need to store it
		// read the file, if empty all's well
		$error = file_get_contents($this->getFileLog());
		if(trim($error) != '') {
			if (ConfigGlobal::is_debug_mode()) {
				echo sprintf("PSQL ERROR IN COMMAND: %s<br> mirar: %s<br>",$command,$this->getFileLog());
			}
		}
	}

	public function importar() {
	    // crear archivo con el password
	    $dsn = $this->getConexion(1);
		// Importar el esquema en la base de datos comun
		$command = "PGOPTIONS='--client-min-messages=warning' /usr/bin/psql -q  -X -t --pset pager=off ";
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

	public function crear() {
		$this->leer();
		$this->cambiar_nombre();
		$this->importar();
	}
	/**
	 * Para la base de datos comun, que está en otro servidor y además con otra versión,
	 * No sirve el pg_dump (solo funciona con versiones iguales en los dos extremos)
	 */
	public function crear_remote() {
		$this->leer_remote();
		$this->cambiar_nombre();
		$this->importar();
	}
	
	public function eliminar() {
	    $dsn = $this->getConexion(1);
		$esquema = $this->getNew();
		$sql = "DROP SCHEMA IF EXISTS \\\"".$esquema."\\\" CASCADE;";
	
		$command = "PGOPTIONS='--client-min-messages=warning' /usr/bin/psql -q -X -t --pset pager=off";
		$command .= " -c \"".$sql."\" ";
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
		//Quizá hay que hacerlo dos veces:
		passthru($command); // no output to capture so no need to store it
		// read the file, if empty all's well
		$error = file_get_contents($this->getFileLog());
		if(trim($error) != '') {
			if (ConfigGlobal::is_debug_mode()) {
				echo sprintf("PSQL ERROR IN COMMAND(2): %s<br> mirar en: %s<br>",$command,$this->getFileLog());
			}
		}
		
	}
	
	private function deleteFile($file) {
		$command = "/bin/rm -f ".$file; 
		passthru($command); // no output to capture so no need to store it
	}
}
