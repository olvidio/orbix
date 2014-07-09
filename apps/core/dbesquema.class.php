<?php
namespace core;
class DBEsquema {
	/**
	 * oDbl de Esquema
	 *
	 * @var object
	 */
	 private $oDbl;
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
		$this->deleteFile($this->getFileNew());
		$this->deleteFile($this->getFileRef());
		$this->deleteFile($this->getFileLog());
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
		$this->sfileRef = empty($this->sfileRef)? $this->getDir().'/'.$this->getRef().'.'.$this->getDbRef().'.sql': $this->sfileRef;
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
		$this->sfileSeq = empty($this->sfileSeq)? $this->getDir().'/'.$this->getNew().'.reset.sql': $this->sfileSeq;
		return $this->sfileSeq;
	}
	public function setFileSeq($fileSeq) {
		$this->sfileSeq = $fileSeq;
	}

	public function cambiar_nombre() {
		$dump = file_get_contents($this->getFileRef());
		// cambiar nombre esquema
		$dump_nou = str_replace($this->getRef(),$this->getNew(),$dump);
		$d = file_put_contents($this->getFileNew(), $dump_nou);
		if ($d === false) exit (_("Error al escribir el fichero"));

	}
	public function leer() {
		// leer esquema
		$command = "/usr/bin/pg_dump -s --schema=\\\"".$this->getRef()."\\\" ";
		$command .= "--file=".$this->getFileRef()." ";
		$command .= "--user=dbCreate ";
		$command .= " ".$this->getDbRef()." > ".$this->getFileLog()." 2>&1"; 
		passthru($command); // no output to capture so no need to store it
		// read the file, if empty all's well
		$error = file_get_contents($this->getFileLog());
		if(trim($error) != '')
			exit("PG_DUMP ERROR IN COMMAND: $command\n-----\n$error\n");
	}

	public function importar() {
		$filename = $this->getFileRef();
		// Importar el esquema en la base de datos comun
		$command = "/usr/bin/psql -q ";
		$command .= "--file=".$this->getFileNew()." ";
		$command .= "--user=dbCreate ";
		$command .= " ".$this->getDb()." > ".$this->getFileLog()." 2>&1"; 
		passthru($command); // no output to capture so no need to store it
		// read the file, if empty all's well
		$error = file_get_contents($this->getFileLog());
		if(trim($error) != '')
			exit("PSQL ERROR IN COMMAND: $command\n-----\n$error\n");
	}

	public function crear() {
		$this->leer();
		$this->cambiar_nombre();
		$this->importar();
	}
	
	public function eliminar() {
		$esquema = $this->getNew();
		$sql = "DROP SCHEMA IF EXISTS \\\"".$esquema."\\\" CASCADE;";
		$command = "/usr/bin/psql -q ";
		$command .= " -c \"".$sql."\" ";
		$command .= "--user=dbCreate ";
		$command .= " ".$this->getDb()." > ".$this->getFileLog()." 2>&1"; 
		passthru($command); // no output to capture so no need to store it
		// read the file, if empty all's well
		$error = file_get_contents($this->getFileLog());
		if(trim($error) != '')
			exit("PSQL ERRROR IN COMMAND: $command\n-----\n$error\n");
	}
	
	 /**
	 * Fija las secuencias de un esquema
	 * Busca todas las secuencias del esquema New, busca su valor máximo y cambia la secuencia a este valor
	 *
	 */
	public function fix_seq() {
		$esquema = $this->getNew();
		// buscar todas las secuencias del esquema y crear la instruccion sql para poner el valor MAX.
		// Guardo las instrucciones en un fichero.
		$sql = "COPY (SELECT  'SELECT SETVAL(' ||quote_literal(quote_ident(PGT.schemaname)|| '.'||quote_ident(S.relname))|| ', MAX(' ||quote_ident(C.attname)|| ') ) FROM ' ||quote_ident(PGT.schemaname)|| '.'||quote_ident(T.relname)|| ';'
			FROM pg_class AS S, pg_depend AS D, pg_class AS T, pg_attribute AS C, pg_tables AS PGT
			WHERE S.relkind = 'S'
				AND S.oid = D.objid
				AND D.refobjid = T.oid
				AND D.refobjid = C.attrelid
				AND D.refobjsubid = C.attnum
				AND T.relname = PGT.tablename
			AND PGT.schemaname='$esquema'
			ORDER BY S.relname)
			TO '".$this->getFileSeq()."'
			";
		$command = "/usr/bin/psql -q ";
		$command .= " -c \"".$sql."\" ";
		$command .= "--user=dbCreate ";
		$command .= " ".$this->getDb()." > ".$this->getFileLog()." 2>&1"; 
		passthru($command); // no output to capture so no need to store it
		// read the file, if empty all's well
		$error = file_get_contents($this->getFileLog());
		if(trim($error) != '')
			exit("PSQL ERRROR IN COMMAND: $command\n-----\n$error\n");
		// Ejecutar el fichero
		$command = "/usr/bin/psql -Atq ";
		$command .= " -f '".$this->getFileSeq()."' ";
		$command .= "--user=dbCreate ";
		$command .= " -o '".$this->getFileLog()."' ";
		$command .= " ".$this->getDb(); 
		passthru($command); // no output to capture so no need to store it

		$this->deleteFile($this->getFileSeq());
		$this->deleteFile($this->getFileLog());
	}
	private function deleteFile($file) {
		$command = "/bin/rm -f ".$file; 
		passthru($command); // no output to capture so no need to store it
	}
}
?>
