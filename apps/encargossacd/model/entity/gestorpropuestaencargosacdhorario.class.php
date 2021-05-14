<?php
namespace encargossacd\model\entity;
use core\ClaseGestor;
use core\Condicion;
use core\ConfigGlobal;
use core\Set;
/**
 * GestorPropuestaEncargoSacdHorario
 *
 * Classe per gestionar la llista d'objectes de la clase PropuestaEncargoSacdHorario
 *
 * @package orbix
 * @subpackage encargossacd
 * @author Daniel Serrabou
 * @version 1.0
 * @created 29/04/2021
 */

class GestorPropuestaEncargoSacdHorario Extends ClaseGestor {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */


	/**
	 * Constructor de la classe.
	 *
	 * @return $gestor
	 *
	 */
	function __construct() {
		$oDbl = $GLOBALS['oDBE'];
		$this->setoDbl($oDbl);
		$this->setNomTabla('propuesta_encargo_sacd_horario');
	}


	/* METODES PUBLICS -----------------------------------------------------------*/
	
	public function existenLasTablas() {
	    $oDbl = $this->getoDbl();
	    $nom_tabla = $this->getNomTabla();
	    
	    $schema_name = ConfigGlobal::mi_region_dl();
	    // comprobar que existen las tablas
	    //How to check whether a table (or view) exists, and the current user has access to it?
	    $sql = "SELECT EXISTS (
	        SELECT FROM information_schema.tables
	        WHERE  table_schema = '$schema_name'
	        AND    table_name   = '$nom_tabla'
	        );";
	    
	    if (($oDblSt = $oDbl->query($sql)) === FALSE) {
	        $sClauError = 'GestorProuestaEncargoSacdHorario.dropTabla';
	        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	        return FALSE;
	    }
	    
	    $a_rta = $oDblSt->fetch(\PDO::FETCH_ASSOC);
	    
	    return $a_rta['exists'];
	}
	public function cambiarSacd($id_enc, $id_sacd_old, $id_sacd_new) {
	    $oDbl = $this->getoDbl();
	    $nom_tabla = $this->getNomTabla();
	    $sQuery="UPDATE $nom_tabla SET id_nom=$id_sacd_new WHERE id_enc=$id_enc AND id_nom = $id_sacd_old";
	    if (($oDbl->query($sQuery)) === FALSE) {
	        $sClauError = 'GestorProuestaEncargoSacdHorario.dropTabla';
	        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	        return FALSE;
	    }
	    
	}

	/**
	 * Crea la nueva tabla de propuestas
	 */
	public function borrarTabla() {
	    $oDbl = $this->getoDbl();
	    $nom_tabla = $this->getNomTabla();
	    
	    // Borrar lo que exista:
        $sQuery="DROP TABLE IF EXISTS $nom_tabla CASCADE";
	    if (($oDbl->query($sQuery)) === FALSE) {
	        $sClauError = 'GestorProuestaEncargoSacdHorario.dropTabla';
	        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	        return FALSE;
	    }
	}

	/**
	 * Crea la nueva tabla de propuestas
	 */
	public function crearTabla() {
	    $oDbl = $this->getoDbl();
	    $nom_tabla = $this->getNomTabla();
	    
	    // Borrar lo que exista:
	   $this->borrarTabla();
	   
	    $sQuery="CREATE TABLE $nom_tabla AS (
            SELECT h.* 
            FROM encargo_sacd_horario h JOIN propuesta_encargos_sacd e ON (h.id_item_tarea_sacd = e.id_item)
            WHERE h.f_fin IS NULL )";
	    if (($oDbl->query($sQuery)) === FALSE) {
	        $sClauError = 'GestorProuestaEncargoSacdHorario.crearTabla';
	        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	        return FALSE;
	    }
	    // Añadir una nueva sequencia:
	    //secuencia
	    $esquema_sfsv = ConfigGlobal::mi_region_dl();
	    //$esquema = substr($esquema_sfsv,0,-1); // quito la v o la f.
	    
	    $id_seq = 'propuesta_encargo_sacd_horario_id_item_seq';
	    $campo_seq = 'id_item';
	    $a_sql[] = "CREATE SEQUENCE IF NOT EXISTS $id_seq;";
	    $a_sql[] = "ALTER SEQUENCE $id_seq
                    INCREMENT BY 1
                    MINVALUE 1
                    MAXVALUE 9223372036854775807
                    START WITH 1
                    NO CYCLE;";
	    $a_sql[] = "ALTER TABLE $nom_tabla ALTER $campo_seq SET DEFAULT nextval('$id_seq'::regclass); ";
	    
	    $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$esquema_sfsv'::text)";
	    
	    $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT propuesta_encargo_sacd_horario_ukey
                    UNIQUE ($campo_seq); ";
	    $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_enc, id_item, id_nom); ";
	    
	    $nom_tabla_ref = "\"$esquema_sfsv\".propuesta_encargos_sacd";
	    $a_sql[] = "ALTER TABLE ONLY $nom_tabla ADD CONSTRAINT propuesta_encargo_sacd_horario_id_item_tarea_sacd_fkey
                     FOREIGN KEY (id_item_tarea_sacd) REFERENCES $nom_tabla_ref(id_item) ON DELETE CASCADE; ";
	    
	    $oDbl->beginTransaction();
	    foreach ($a_sql as $sql) {
	        if ($oDbl->exec($sql) === false) {
	            $sClauError = 'Procesos.DBEsquema.query';
	            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	            $oDbl->rollback();
	            return FALSE;
	        }
	    }
	    $oDbl->commit();
	    
	    return TRUE;
	}
	
	/**
	 * retorna l'array d'objectes de tipus EncargoSacdHorario
	 *
	 * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
	 * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
	 * @return array Una col·lecció d'objectes de tipus EncargoSacdHorario
	 */
	function getEncargoSacdHorarios($aWhere=array(),$aOperators=array()) {
	    $oDbl = $this->getoDbl();
	    $nom_tabla = $this->getNomTabla();
	    $oEncargoSacdHorarioSet = new Set();
	    $oCondicion = new Condicion();
	    $aCondi = array();
	    foreach ($aWhere as $camp => $val) {
	        if ($camp == '_ordre') continue;
	        $sOperador = isset($aOperators[$camp])? $aOperators[$camp] : '';
	        if ($a = $oCondicion->getCondicion($camp,$sOperador,$val)) $aCondi[]=$a;
	        // operadores que no requieren valores
	        if ($sOperador == 'BETWEEN' || $sOperador == 'IS NULL' || $sOperador == 'IS NOT NULL' || $sOperador == 'OR') unset($aWhere[$camp]);
	        if ($sOperador == 'IN' || $sOperador == 'NOT IN') unset($aWhere[$camp]);
	        if ($sOperador == 'TXT') unset($aWhere[$camp]);
	    }
	    $sCondi = implode(' AND ',$aCondi);
	    if ($sCondi!='') $sCondi = " WHERE ".$sCondi;
	    if (isset($GLOBALS['oGestorSessioDelegación'])) {
	        $sLimit = $GLOBALS['oGestorSessioDelegación']->getLimitPaginador('a_actividades',$sCondi,$aWhere);
	    } else {
	        $sLimit='';
	    }
	    if ($sLimit === FALSE) return;
	    $sOrdre = '';
	    if (isset($aWhere['_ordre']) && $aWhere['_ordre']!='') $sOrdre = ' ORDER BY '.$aWhere['_ordre'];
	    if (isset($aWhere['_ordre'])) unset($aWhere['_ordre']);
	    $sQry = "SELECT * FROM $nom_tabla ".$sCondi.$sOrdre.$sLimit;
	    if (($oDblSt = $oDbl->prepare($sQry)) === FALSE) {
	        $sClauError = 'GestorEncargoSacdHorario.llistar.prepare';
	        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	        return FALSE;
	    }
	    if (($oDblSt->execute($aWhere)) === FALSE) {
	        $sClauError = 'GestorEncargoSacdHorario.llistar.execute';
	        $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
	        return FALSE;
	    }
	    foreach ($oDblSt as $aDades) {
	        $a_pkey = array('id_item' => $aDades['id_item']);
	        $oEncargoSacdHorario= new PropuestaEncargoSacdHorario($a_pkey);
	        $oEncargoSacdHorario->setAllAtributes($aDades);
	        $oEncargoSacdHorarioSet->add($oEncargoSacdHorario);
	    }
	    return $oEncargoSacdHorarioSet->getTot();
	}
	
	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/
}
