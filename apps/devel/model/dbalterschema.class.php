<?php
namespace devel\model;

class DBAlterSchema {
	/**
	 * oDbl
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * 
	 *
	 * @var string
	 */
	 protected $schema;
	 	
	 /* CONSTRUCTOR -------------------------------------------------------------- */

	 /**
	 * Constructor de la classe.
	 */
	function __construct() {
	}
 

	/* METODES GET i SET --------------------------------------------------------*/
	
	public function setDbConexion($oDbl) {
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
	public function setSchema($schema) {
		$this->schema = $schema;
	}
	public function setPwd($password) {
	    //$password_encoded = urlencode ($password);
		$this->sPwd = $password;
	}
	public function setOptions($options) {
		$this->sOptions = $options;
	}

    /**
     * Comprobar si existe la tabla, para evitar errores.
     * 
     * @param string $tabla
     * @return boolean
     */
	public function existeTabla($full_name) {
	    
		$oDbl = $this->getoDbl();
		$sql = "SELECT to_regclass('$full_name'); ";
		
		foreach($oDbl->query($sql) as $row) {
		    if (!empty($row[0])) {
		        return TRUE;
		    }
		} 
	    return FALSE;
	}
	
	/**
	 * 
	 * @param array $aDefaults
	 *       ['tabla' => 'a_actividad_proceso_sf', 'campo' => 'id_schema', 'valor' => "idschema('H-dlx'::text)"],
	 */
	public function setDefaults($aDefaults) {
	    foreach ($aDefaults as $cambio) {
	        $tabla = $cambio['tabla'];
	        $campo = $cambio['campo'];
	        $valor = $cambio['valor'];
	        
            $full_name = "\"$this->schema\".$tabla";
	        if ($this->existeTabla($full_name)) {
                $this->setColumnDefault($full_name,$campo,$valor);
	        }
	    }
	    
	}

	public function setColumnDefault($nom_tabla,$nom_column,$default) {
		$oDbl = $this->getoDbl();
		$sql = "ALTER TABLE $nom_tabla ALTER COLUMN $nom_column SET DEFAULT $default";
		
		if (($oDblSt = $oDbl->prepare($sql)) === false) {
			$sClauError = 'DBRol.crearSchema.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		} else {
			if ($oDblSt->execute() === false) {
				$sClauError = 'DBRol.crearSchema.execute';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
				return false;
			}
		}
	}
	
	/**
	 * 
	 * @param array $aDatos
	 *     ['tabla' => 'a_actividades_dl', 'campo' => 'dl_org', 'pattern' => "$dl_old(.*)", 'replacement' => "$DlNew\1"]
	 */
	public function updateDatos($aDatos) {
	    foreach ($aDatos as $cambio) {
	        $tabla = $cambio['tabla'];
	        $campo = $cambio['campo'];
	        $pattern = $cambio['pattern'];
	        $replacement = $cambio['replacement'];
	        
            $full_name = "\"$this->schema\".$tabla";
	        if ($this->existeTabla($full_name)) {
                $this->updateColumn($full_name,$campo,$pattern,$replacement);
	        }
	    }
	}
	
	public function updateColumn($full_name,$campo,$pattern,$replacement) {
	    $oDbl = $this->getoDbl();
	    $sql = "UPDATE $full_name SET $campo = REGEXP_REPLACE($campo, '$pattern', '$replacement') ";
	    
	    if (($oDblSt = $oDbl->prepare($sql)) === false) {
	        $sClauError = 'DBRol.crearSchema.prepare';
	        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	        return false;
	    } else {
	        if ($oDblSt->execute() === false) {
	            $sClauError = 'DBRol.crearSchema.execute';
	            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
	            return false;
	        }
	    }
	}
	
	public function updateCedidasAll($old,$new) {
	    $oDbl = $this->getoDbl();
	    $sql = "UPDATE publicv.da_plazas set cedidas =  replace(cedidas::text, '$old', '$new')::jsonb where cedidas is not null;";
	    
	    if (($oDblSt = $oDbl->prepare($sql)) === false) {
	        $sClauError = 'DBRol.crearSchema.prepare';
	        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	        return false;
	    } else {
	        if ($oDblSt->execute() === false) {
	            $sClauError = 'DBRol.crearSchema.execute';
	            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
	            return false;
	        }
	    }
	}
	
	public function updatePropietarioAll($old,$new) {
	    $oDbl = $this->getoDbl();
	    $sql = "UPDATE global.d_asistentes_dl set propietario = replace(propietario::text, '$old', '$new')::text where propietario is not null;";
	    
	    if (($oDblSt = $oDbl->prepare($sql)) === false) {
	        $sClauError = 'DBRol.crearSchema.prepare';
	        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	        return false;
	    } else {
	        if ($oDblSt->execute() === false) {
	            $sClauError = 'DBRol.crearSchema.execute';
	            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
	            return false;
	        }
	    }
	    $sql = "UPDATE publicv.d_asistentes_de_paso set propietario = replace(propietario::text, '$old', '$new')::text where propietario is not null;";
	    
	    if (($oDblSt = $oDbl->prepare($sql)) === false) {
	        $sClauError = 'DBRol.crearSchema.prepare';
	        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	        return false;
	    } else {
	        if ($oDblSt->execute() === false) {
	            $sClauError = 'DBRol.crearSchema.execute';
	            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
	            return false;
	        }
	    }
	}
	
	/**
	 * 
	 * @param array $aDatos
	 *     ['tabla' => 'a_actividades_dl', 'campo' => 'dl_org', 'pattern' => "$dl_old(.*)", 'replacement' => "$DlNew\1"]
	 */
	public function updateDatosTodos($aDatos) {
	    foreach ($aDatos as $cambio) {
	        $tabla = $cambio['tabla'];
	        $campo = $cambio['campo'];
	        $pattern = $cambio['pattern'];
	        $replacement = $cambio['replacement'];
	        
	        if ($this->existeTabla($tabla)) {
                $this->updateColumn($tabla,$campo,$pattern,$replacement);
	        }
	    }
	}
	
}
