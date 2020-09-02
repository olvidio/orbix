<?php
namespace core;
use ubis\model\entity\GestorDelegacion;

class DBView {
	/**
	 * oDbl de Grupo
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	/**
	 * Schema de la region stgr
	 *
	 * @var string
	 */
	 protected $sSchema;
	/**
	 * RegionStgr de DBRol
	 *
	 * @var string
	 */
	 protected $sRegionStgr;
	/**
	 * Nombre de la tabla para crear la vista
	 *
	 * @var string
	 */
	 protected $sView;
	 	
	 /* CONSTRUCTOR -------------------------------------------------------------- */

	 /**
	 * Constructor de la classe.
	 */
	function __construct($schema) {
        // Necesito permisos de superusuario para poder acceder a los distintos esquemas
        // que pertenecen a la region del stgr.
        $oConfigDB = new ConfigDB('importar');
	    $mi_sfsv = ConfigGlobal::mi_sfsv();
	    if ($mi_sfsv === 1) {
            $config = $oConfigDB->getEsquema('publicv');
	    } elseif ($mi_sfsv === 1) {
            $config = $oConfigDB->getEsquema('publicf');
	    }
        $oConexion = new dbConnection($config);
        $oDbl = $oConexion->getPDO();
        
        $this->setoDbl($oDbl);
        $this->setSchema($schema);

        // region stgr:
        $a_reg = explode('-',$schema);
        $reg = $a_reg[0]; 
        $this->setRegionStgr($reg);
     }

	/* METODES GET i SET --------------------------------------------------------*/
	
	public function setDbConexion($oDbl) {
	    $this->setoDbl($oDbl);
	}
	public function setSchema($schema) {
		$this->sSchema = $schema;
	}
	public function setRegionStgr($regionStgr) {
		$this->sRegionStgr = $regionStgr;
	}
	public function setView($view) {
		$this->sView = $view;
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


	public function normalizarTexto($str) {
	   $str = trim ($str);
	   // saltos de linea y tabuladores
	   $search = ["\n","\t","\r"];
	   $replace = ' ';
	   $new_str = str_replace($search, $replace, $str); 
	   // espacios extra:
	   $new_str = preg_replace('/\s\s+/', ' ', $new_str);
	   $lower = strtolower($new_str);

	   $string = preg_replace( '/[^[:print:]]/', '',$lower);
	   
	   return $string;
	}
	public function Existe() {
	    // definicion teórica
	    $defNew = $this->getDefView($this->sView);
	    // quitar espacios, tabuladores, returns...
	    $defNew = $this->normalizarTexto($defNew);
	    // definicion real
	    $defActual = $this->getSqlView($this->sView);
	    $defActual = $this->normalizarTexto($defActual);
	    
	    if ($defActual == $defNew) {
	        return TRUE;
	    } else {
	        return FALSE;
	    }
	}
	
	public function Create() {
	    /*
	     * OJO, hay que dar permisos...
	     */
	    $oDbl = $this->getoDbl(); 
	    $nameView = " \"$this->sSchema\".$this->sView";
	    
	    $this->Drop();
	    
	    $sql = "CREATE MATERIALIZED VIEW $nameView AS ";
        $sql .= $this->getDefView($this->sView);
        
	    if (($oDbl->exec($sql)) === false) {
	        $sClauError = 'Refresh';
	        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	        return FALSE;
	    } else {
	        $sql = "ALTER MATERIALIZED VIEW $nameView OWNER TO \"$this->sSchema\"";
    	    if (($oDbl->exec($sql)) === false) {
                $sClauError = 'Refresh';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            }
	        return TRUE;
	    }
        
	}
	
	public function Drop() {
	    // Sólo puede el propietario. Por eso hay que emplera la conexión oDB
	    $oDbl = $GLOBALS['oDB'];
	    $nameView = " \"$this->sSchema\".$this->sView";
	    
	    $sql = "DROP MATERIALIZED VIEW IF EXISTS $nameView CASCADE ";
        
	    if (($oDbl->exec($sql)) === false) {
	        $sClauError = 'Drop';
	        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	        return FALSE;
	    } else {
	        return TRUE;
	    }
        
	}
	
	public function Refresh() {
	    $oDbl = $this->getoDbl(); 
	    $nameView = " \"$this->sSchema\".$this->sView";
	    
	    $sql = "REFRESH MATERIALIZED VIEW $nameView";
	    if (($oDbl->exec($sql)) === false) {
	        $sClauError = 'Refresh';
	        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	        return FALSE;
	    } else {
	        return TRUE;
	    }
	}
	
	private function getSchemasGrupStgr() {
	    $RegionStgr = $this->sRegionStgr;
	    $gesDl = new GestorDelegacion();
	    $mi_sfsv = ConfigGlobal::mi_sfsv();
	    
	    $a_schemas = $gesDl->getArraySchemasRegionStgr($RegionStgr,$mi_sfsv);
	   return $a_schemas; 
	}
	
	private function getDefView($view) {
	    $a_schemas = $this->getSchemasGrupStgr();
	   
	    $schema1 = current($a_schemas);
	    $columns = $this->getNameColumns($schema1, $view);
	    
	    $sql_def_view = '';
	    foreach ($a_schemas as $id_dl => $schema) {
	        $sql_def_view .= empty($sql_def_view)? '' : " UNION ALL "; 
	        $sql_def_view .= "SELECT $columns FROM \"$schema\".$view " ;
	    }
	    return $sql_def_view;
	}
	
	private function getNameColumns($schema1, $view) {
	    $oDbl = $this->getoDbl();
	    // coger la primera dl como referencia para el nombre de los campos 

	    $definicion = '';

	    $sQuery = "SELECT column_name
	               FROM information_schema.columns
	               WHERE table_schema = '$schema1'
	               AND table_name = '$view' ";
	    
	    foreach ($oDbl->query($sQuery) as $row) {
	        $column_name = $row['column_name'];
	        if ($column_name == 'id_schema') { continue; }
	        $definicion .= "$view.$column_name, ";
	    }
	    // borrar la última coma
	    $definicion = substr($definicion,0,-2);
	    return $definicion;
	}
	
	private function getSqlView($view) {
	    $oDbl = $this->getoDbl();
	    $schemaName = "$this->sSchema";
	    $definicion = '';
	    //SELECT definition FROM pg_matviews WHERE schemaname='H-Hv' AND matviewname='d_profesor_stgr';
	    
        $sQuery="SELECT definition 
                FROM pg_matviews 
                WHERE schemaname='$schemaName' AND matviewname='$view'; 
                ";
	    foreach ($oDbl->query($sQuery) as $row) {
	        $definicion = $row['definition'];
	    }
	    
	    // borrar el último punto y coma
	    $definicion = substr($definicion,0,-1);
	    return $definicion;
	}
}
