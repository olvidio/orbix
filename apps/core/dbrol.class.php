<?php
namespace core;
class DBRol {
	/**
	 * oDbl de Grupo
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * Password de DBRol
	 *
	 * @var string
	 */
	 protected $sPwd;
	/**
	 * Usuario a Crear de DBRol
	 *
	 * @var string
	 */
	 protected $sUser;
	/**
	 * Opciones para a Crear el Role de DBRol
	 *
	 * @var string
	 */
	 protected $sOptions;
	 	
	 /* CONSTRUCTOR -------------------------------------------------------------- */

	 /**
	 * Constructor de la classe.
	 * Si només necessita un valor, se li pot passar un integer.
	 * En general se li passa un array amb les claus primàries.
	 *
	 * @param integer|array $iid_ubi
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct() {
		/*
		$str_conexio = "pgsql:host=localhost port=5432  dbname='comun' user='dani' password='system'";
		$oDbl = new \PDO($str_conexio);
		$this->setoDbl($oDbl);
		*/
	}
 

	/* METODES GET i SET --------------------------------------------------------*/
	public function setDbName($dbname) {
		$this->sdbname = $dbname;
	}
	public function setDbUser($dbuser) {
		$this->sdbuser = $dbuser;
	}
	public function setDbPwd($dbpwd) {
		$this->sdbpwd = $dbpwd;
	}
	public function setDbConexion() {
		$str_conexio = "pgsql:host=localhost port=5432  dbname='".$this->sdbname."' user='".$this->sdbuser."' password='".$this->sdbpwd."'";
		$oDbl = new \PDO($str_conexio);
		$this->setoDbl($oDbl);
	}

	/**
	 * Recupera l'atribut oDbl de Grupo
	 *
	 * @return object oDbl
	 */
	protected function setoDbl($oDbl) {
		$this->oDbl = $oDbl;
	}
	/**
	 * Recupera l'atribut oDbl de Grupo
	 *
	 * @return object oDbl
	 */
	protected function getoDbl() {
		return $this->oDbl;
	}
	public function setUser($user) {
		$this->sUser = $user;
	}
	public function setPwd($password) {
		$this->sPwd = $password;
	}
	public function setOptions($options) {
		$this->sOptions = $options;
	}


	// usuarios:	
	public function addGrupo($grupo) {
		$oDbl = $this->getoDbl();
		$sql = "GRANT \"$grupo\" TO \"$this->sUser\" ";

		if (($qRs = $oDbl->prepare($sql)) === false) {
			$sClauError = 'DBRol.addGrupo.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		} else {
			if ($qRs->execute() === false) {
				$sClauError = 'DBRol.addGrupo.execute';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			}
		}
	}
	public function delGrupo($grupo) {
		$oDbl = $this->getoDbl();
		$sql = "REVOKE \"$grupo\" FROM \"$this->sUser\"";

		if (($qRs = $oDbl->prepare($sql)) === false) {
			$sClauError = 'DBRol.delGrupo.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		} else {
			if ($qRs->execute() === false) {
				$sClauError = 'DBRol.delGrupo.execute';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			}
		}
	}
	public function crearSchema() {
		$oDbl = $this->getoDbl();
		$sql = "CREATE SCHEMA \"$this->sUser\" AUTHORIZATION \"$this->sUser\";";

		if (($qRs = $oDbl->prepare($sql)) === false) {
			$sClauError = 'DBRol.crearSchema.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		} else {
			if ($qRs->execute() === false) {
				$sClauError = 'DBRol.crearSchema.execute';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			}
		}
	}
	public function crearUsuario() {
		$oDbl = $this->getoDbl();
		$this->sOptions = empty($this->sOptions)? 'NOSUPERUSER NOCREATEDB NOCREATEROLE INHERIT LOGIN': $this->sOptions;

		$sql = "CREATE ROLE \"$this->sUser\" PASSWORD '$this->sPwd' $this->sOptions;";

		if (($qRs = $oDbl->prepare($sql)) === false) {
			$sClauError = 'DBRol.crear.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		} else {
			if ($qRs->execute() === false) {
				$sClauError = 'DBRol.crear.execute';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			}
		}
	}
	public function eliminarSchema() {
		$oDbl = $this->getoDbl();
		$sql = "DROP SCHEMA \"$this->sUser\" CASCADE";

		if (($qRs = $oDbl->prepare($sql)) === false) {
			$sClauError = 'DBRol.eliminarSchema.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		} else {
			if ($qRs->execute() === false) {
				$sClauError = 'DBRol.eliminarSchema.execute';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			}
		}
	}
	public function eliminarUsuario() {
		$oDbl = $this->getoDbl();

		$sql = "DROP ROLE IF EXISTS \"$this->sUser\";";

		if (($qRs = $oDbl->prepare($sql)) === false) {
			$sClauError = 'DBRol.eliminar.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		} else {
			if ($qRs->execute() === false) {
				$sClauError = 'DBRol.eliminar.execute';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			}
		}
	}
}
?>
