<?php
namespace core;
class DBTrasvase {

	/**
	 * oDbl de Esquema
	 *
	 * @var object
	 */
	 private $oDbl;

	 /* CONSTRUCTOR -------------------------------------------------------------- */
 
	 /**
	 * Constructor de la classe.
	 *
	 */
	function __construct() {
		
	}


	/* METODES GET i SET --------------------------------------------------------*/

	public function setDbName($dbname) {
		$this->sdbname = $dbname;
	}
	public function getDbName() {
		return $this->sdbname;
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
	private function getoDbl() {
		return $this->oDbl;
	}

	public function getDir() {
		$this->sdir = empty($this->sdir)? ConfigGlobal::$directorio.'/log/db' : $this->sdir;
		return $this->sdir;
	}
	public function setDir($dir) {
		$this->sdir = $dir;
	}
	public function getRegion() {
		return $this->sregion;
	}
	public function setRegion($region) {
		$this->sregion = $region;
	}
	public function getDl() {
		return $this->sdl;
	}
	public function setDl($dl) {
		$this->sdl = $dl;
	}
	public function getEsquema() {
		switch ($this->getDbName()) {
			case 'sv':	$seccion='v'; break;
			case 'sf':	$seccion='f'; break;
			case 'comun':	$seccion=''; break;
		}
		$this->sEsquema = $this->getRegion().'-'.$this->getDl().$seccion;
		return $this->sEsquema;
	}
	public function getResto() {
		switch ($this->getDbName()) {
			case 'sv':	$seccion='v'; break;
			case 'sf':	$seccion='f'; break;
			case 'comun':	$seccion=''; break;
		}
		$this->sEsquema = 'resto'.$seccion;
		return $this->sEsquema;
	}



	 /**
	 * Fija las secuencias de un esquema
	 * Busca todas las secuencias del esquema New, busca su valor máximo y cambia la secuencia a este valor
	 *
	 */
	public function fix_seq() {
		$oDbl = $this->getoDbl();
		$esquema = $this->getEsquema();
		$dl = $this->getDl();
		// buscar todas las secuencias del esquema y crear la instruccion sql para poner el valor MAX.
		// Guardo las instrucciones en un fichero <== No se puede si no soy superusuario: Lo hago una por una:
		$sql = "SELECT  'SELECT SETVAL(' ||quote_literal(quote_ident(PGT.schemaname)|| '.'||quote_ident(S.relname))|| ', MAX(' ||quote_ident(C.attname)|| ') ) FROM ' ||quote_ident(PGT.schemaname)|| '.'||quote_ident(T.relname)|| ';'
			FROM pg_class AS S, pg_depend AS D, pg_class AS T, pg_attribute AS C, pg_tables AS PGT
			WHERE S.relkind = 'S'
				AND S.oid = D.objid
				AND D.refobjid = T.oid
				AND D.refobjid = C.attrelid
				AND D.refobjsubid = C.attnum
				AND T.relname = PGT.tablename
			AND PGT.schemaname='$esquema'
			ORDER BY S.relname
			";
		foreach ($oDbl->query($sql) as $row) {
			$oDbl->query($row[0]);
    	}
	}




	// COMUN
	//-------------- Actividades ----------------------
	public function actividades($que) {
		$oDbl = $this->getoDbl();
		$esquema = $this->getEsquema();
		$dl = $this->getDl();
		switch ($que) {
			case 'resto2dl':
				$oDbl->beginTransaction();
				$sql = "INSERT INTO \"$esquema\".a_actividades_dl SELECT * FROM resto.a_actividades_ex WHERE dl_org = '$dl';";
				$oDbl->exec($sql);
				$sql = "UPDATE \"$esquema\".a_actividades_dl SET id_tabla='dl';";
				$oDbl->exec($sql);
				$sql = "DELETE FROM resto.a_actividades_ex WHERE dl_org = '$dl'";
				$oDbl->exec($sql);
				if ($oDbl->commit() === false) {
					$sClauError = 'DBTrasvase.actividades.resto2dl';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
				break;
			case 'dl2resto':
				$oDbl->beginTransaction();
				$sql = "UPDATE \"$esquema\".a_actividades_dl SET id_tabla='ex';";
				$oDbl->exec($sql);
				$sql = "INSERT INTO resto.a_actividades_ex SELECT * FROM \"$esquema\".a_actividades_dl;";
				$oDbl->exec($sql);
				$sql = "TRUNCATE \"$esquema\".a_actividades_dl";
				$oDbl->exec($sql);
				if ($oDbl->commit() === false) {
					$sClauError = 'DBTrasvase.actividades.dl2resto';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
				break;
		}
	}

	//---------------- CDC --------------------
	public function cdc($que) {
		$oDbl = $this->getoDbl();
		$esquema = $this->getEsquema();
		$dl = $this->getDl();
		$region = $this->getRegion();
		switch ($que) {
			case 'resto2dl':
				$sql = "INSERT INTO \"$esquema\".u_cdc_dl SELECT * FROM resto.u_cdc_ex WHERE dl = '$dl' AND region='$region'; ";
				if ($oDbl->query($sql) === false) {
					$sClauError = 'DBTrasvase.cdc.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				} else {
					// actualizar el tipo_ubi.
					$sql = "UPDATE \"$esquema\".u_cdc_dl SET tipo_ubi='cdcdl'";
					if ($oDbl->query($sql) === false) {
						$sClauError = 'DBTrasvase.cdc.execute';
						$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
						return false;
					}
					// primero las direcciones porque 'u_cross' tiene como foreign key id_direccion e id_ubi.
					$sql = "INSERT INTO \"$esquema\".u_dir_cdc_dl SELECT  DISTINCT rd.* 
						FROM  resto.u_dir_cdc_ex rd JOIN resto.u_cross_cdc_ex_dir rx USING (id_direccion), \"$esquema\".u_cdc_dl u 
						WHERE u.id_ubi = rx.id_ubi";
					if ($oDbl->query($sql) === false) {
						$sClauError = 'DBTrasvase.cdc.execute';
						$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
						return false;
					}
					$sql = "INSERT INTO \"$esquema\".u_cross_cdc_dl_dir SELECT r.* FROM  resto.u_cross_cdc_ex_dir r JOIN \"$esquema\".u_cdc_dl a USING (id_ubi)";
					if ($oDbl->query($sql) === false) {
						$sClauError = 'DBTrasvase.cdc.execute';
						$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
						return false;
					}
					// delete cdc
					$sql = "DELETE FROM resto.u_cdc_ex WHERE dl = '$dl' AND region='$region'";
					if ($oDbl->query($sql) === false) {
						$sClauError = 'DBCopiar.cdc.execute';
						$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
						return false;
					}
					// delete dir
					$sql = "DELETE FROM resto.u_dir_cdc_ex
							WHERE id_direccion IN (SELECT id_direccion FROM \"$esquema\".u_dir_cdc_dl)"; 
					if ($oDbl->query($sql) === false) {
						$sClauError = 'DBTrasvase.cdc.execute';
						$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
						return false;
					}
					// delete cross (deberia borrarse sólo; por el foreign key).
				}
				break;
			case 'dl2resto':
				// actualizar el tipo_ubi.
				$sql = "UPDATE \"$esquema\".u_cdc_dl SET tipo_ubi='cdcex'";
				if ($oDbl->query($sql) === false) {
					$sClauError = 'DBTrasvase.cdc.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
				//cdc
				$sql = "INSERT INTO resto.u_cdc_ex SELECT * FROM \"$esquema\".u_cdc_dl";
				if ($oDbl->query($sql) === false) {
					$sClauError = 'DBTrasvase.cdc.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				} else {
					// primero las direcciones porque 'u_cross' tiene como foreign key id_direccion e id_ubi.
					$sql = "INSERT INTO resto.u_dir_cdc_ex SELECT * FROM  \"$esquema\".u_dir_cdc_dl ";
					if ($oDbl->query($sql) === false) {
						$sClauError = 'DBTrasvase.cdc.execute';
						$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
						return false;
					}
					$sql = "INSERT INTO  resto.u_cross_cdc_ex_dir  SELECT * FROM \"$esquema\".u_cross_cdc_dl_dir";
					if ($oDbl->query($sql) === false) {
						$sClauError = 'DBTrasvase.cdc.execute';
						$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
						return false;
					}
					// delete cdc
					$sql = "TRUNCATE \"$esquema\".u_cdc_dl RESTART IDENTITY CASCADE";
					if ($oDbl->query($sql) === false) {
						$sClauError = 'DBTrasvase.cdc.execute';
						$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
						return false;
					}
					// delete dir
					$sql = "TRUNCATE \"$esquema\".u_dir_cdc_dl RESTART IDENTITY CASCADE";
					if ($oDbl->query($sql) === false) {
						$sClauError = 'DBTrasvase.cdc.execute';
						$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
						return false;
					}
					// delete cross (deberia borrarse sólo; por el foreign key).
				}
				break;
		}
	}
	//---------------- Teleco CDC --------------------
	public function teleco_cdc($que) {
		$oDbl = $this->getoDbl();
		$esquema = $this->getEsquema();
		$dl = $this->getDl();
		switch ($que) {
			case 'resto2dl':
				$sql = "INSERT INTO \"$esquema\".d_teleco_cdc_dl SELECT r.* FROM  resto.d_teleco_cdc_ex r JOIN \"$esquema\".u_cdc_dl a USING (id_ubi)";
				if ($oDbl->query($sql) === false) {
					$sClauError = 'DBTrasvase.telecocdc.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				} else {
					$sql = "DELETE FROM resto.d_teleco_cdc_ex  WHERE id_ubi IN (SELECT id_ubi FROM \"$esquema\".u_cdc_dl)";
					if ($oDbl->query($sql) === false) {
						$sClauError = 'DBTrasvase.telecocdc.execute';
						$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
						return false;
					}
				}
				break;
			case 'dl2resto':
				$sql = "INSERT INTO resto.d_teleco_cdc_ex SELECT * FROM \"$esquema\".d_teleco_cdc_dl";
				if ($oDbl->query($sql) === false) {
					$sClauError = 'DBTrasvase.telecocdc.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				} else {
					$sql = "TRUNCATE \"$esquema\".d_teleco_cdc_dl";
					if ($oDbl->query($sql) === false) {
						$sClauError = 'DBTrasvase.telecocdc.execute';
						$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
						return false;
					}
				}
				break;
		}
	}

	// SV o SF
	//---------------- Ctr --------------------
	public function ctr($que) {
		$oDbl = $this->getoDbl();
		$esquema = $this->getEsquema();
		$resto = $this->getResto();
		$dl = $this->getDl();
		$region = $this->getRegion();
		switch ($que) {
			case 'resto2dl':
				$sql = "INSERT INTO \"$esquema\".u_centros_dl SELECT * FROM \"$resto\".u_centros_ex WHERE dl = '$dl' AND region='$region'; ";
				if ($oDbl->query($sql) === false) {
					$sClauError = 'DBTrasvase.ctr.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				} else {
					// actualizar el tipo_ubi.
					$sql = "UPDATE \"$esquema\".u_centros_dl SET tipo_ubi='ctrdl'";
					if ($oDbl->query($sql) === false) {
						$sClauError = 'DBTrasvase.ctr.execute';
						$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
						return false;
					}
					// primero las direcciones porque 'u_cross' tiene como foreign key id_direccion e id_ubi.
					$sql = "INSERT INTO \"$esquema\".u_dir_ctr_dl SELECT DISTINCT rd.* 
						FROM  \"$resto\".u_dir_ctr_ex rd JOIN \"$resto\".u_cross_ctr_ex_dir rx USING (id_direccion), \"$esquema\".u_centros_dl u 
						WHERE u.id_ubi = rx.id_ubi";
					if ($oDbl->query($sql) === false) {
						$sClauError = 'DBTrasvase.ctr.execute';
						$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
						return false;
					}
					$sql = "INSERT INTO \"$esquema\".u_cross_ctr_dl_dir SELECT r.* FROM  \"$resto\".u_cross_ctr_ex_dir r JOIN \"$esquema\".u_centros_dl a USING (id_ubi)";
					if ($oDbl->query($sql) === false) {
						$sClauError = 'DBTrasvase.ctr.execute';
						$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
						return false;
					}
					// delete ctr
					$sql = "DELETE FROM \"$resto\".u_centros_ex WHERE dl = '$dl' AND region='$region'";
					if ($oDbl->query($sql) === false) {
						$sClauError = 'DBTrasvase.ctr.execute';
						$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
						return false;
					}
					// delete dir
					$sql = "DELETE FROM \"$resto\".u_dir_ctr_ex
							WHERE id_direccion IN (SELECT id_direccion FROM \"$esquema\".u_dir_ctr_dl)"; 
					if ($oDbl->query($sql) === false) {
						$sClauError = 'DBTrasvase.ctr.execute';
						$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
						return false;
					}
					// delete cross (deberia borrarse sólo; por el foreign key).
				}
				break;
			case 'dl2resto':
				// actualizar el tipo_ubi.
				$sql = "UPDATE \"$esquema\".u_centros_dl SET tipo_ubi='ctrex'";
				if ($oDbl->query($sql) === false) {
					$sClauError = 'DBTrasvase.ctr.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
				$sql = "INSERT INTO \"$resto\".u_centros_ex SELECT tipo_ubi,id_ubi,nombre_ubi,dl,pais,region,status,f_status,sv,sf,tipo_ctr,tipo_labor,cdc,id_ctr_padre,id_auto FROM \"$esquema\".u_centros_dl";
			   if ($oDbl->query($sql) === false) {
					$sClauError = 'DBEliminar.ctr.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				} else {
					// primero las direcciones porque 'u_cross' tiene como foreign key id_direccion e id_ubi.
					$sql = "INSERT INTO \"$resto\".u_dir_ctr_ex SELECT * FROM  \"$esquema\".u_dir_ctr_dl";
					if ($oDbl->query($sql) === false) {
						$sClauError = 'DBTrasvase.ctr.execute';
						$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
						return false;
					}
					$sql = "INSERT INTO \"$resto\".u_cross_ctr_ex_dir SELECT * FROM \"$esquema\".u_cross_ctr_dl_dir ";
					if ($oDbl->query($sql) === false) {
						$sClauError = 'DBTrasvase.ctr.execute';
						$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
						return false;
					}
					// delete ctr
					$sql = "TRUNCATE \"$esquema\".u_centros_dl RESTART IDENTITY CASCADE";
					if ($oDbl->query($sql) === false) {
						$sClauError = 'DBTrasvase.ctr.execute';
						$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
						return false;
					}
					// delete dir
					$sql = "TRUNCATE \"$esquema\".u_dir_ctr_dl RESTART IDENTITY CASCADE";
					if ($oDbl->query($sql) === false) {
						$sClauError = 'DBTrasvase.ctr.execute';
						$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
						return false;
					}
					// delete cross (deberia borrarse sólo; por el foreign key).
				}
			   break;
		}
	}
	//---------------- Teleco Ctr --------------------
	public function teleco_ctr($que) {
		$oDbl = $this->getoDbl();
		$esquema = $this->getEsquema();
		$resto = $this->getResto();
		switch ($que) {
			case 'resto2dl':
				$sql = "INSERT INTO \"$esquema\".d_teleco_ctr_dl SELECT r.* FROM  \"$resto\".d_teleco_ctr_ex r JOIN \"$esquema\".u_centros_dl a USING (id_ubi)";
				if ($oDbl->query($sql) === false) {
					$sClauError = 'DBTrasvase.telecoctr.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				} else {
					$sql = "DELETE FROM \"$resto\".d_teleco_ctr_ex  WHERE id_ubi IN (SELECT id_ubi FROM \"$esquema\".u_centros_dl)";
					if ($oDbl->query($sql) === false) {
						$sClauError = 'DBTrasvase.telecoctr.execute';
						$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
						return false;
					}
				}
				break;
			case 'dl2resto':
				$sql = "INSERT INTO \"$resto\".d_teleco_ctr_ex SELECT * FROM \"$esquema\".d_teleco_ctr_dl ";
				if ($oDbl->query($sql) === false) {
					$sClauError = 'DBTrasvase.telecoctr.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				} else {
					$sql = "TRUNCATE \"$esquema\".d_teleco_ctr_dl";
					if ($oDbl->query($sql) === false) {
						$sClauError = 'DBTrasvase.telecoctr.execute';
						$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
						return false;
					}
				}
				break;
		}
	}

}
?>
