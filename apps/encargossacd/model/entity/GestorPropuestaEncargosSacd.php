<?php

namespace encargossacd\model\entity;

use core\ClaseGestor;
use core\Condicion;
use core\ConfigGlobal;
use core\Set;

/**
 * GestorEncargoSacd
 *
 * Classe per gestionar la llista d'objectes de la clase EncargoSacd
 *
 * @package orbix
 * @subpackage encargossacd
 * @author Daniel Serrabou
 * @version 1.0
 * @created 29/04/2021
 */
class GestorPropuestaEncargosSacd extends ClaseGestor
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */


    
    function __construct()
    {
        $oDbl = $GLOBALS['oDBE'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('propuesta_encargos_sacd');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    /**
     * Crea la nueva tabla de propuestas
     */
    public function borrarTabla()
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();

        // Borrar lo que exista:
        $sQuery = "DROP TABLE IF EXISTS $nom_tabla CASCADE";
        if (($oDbl->query($sQuery)) === FALSE) {
            $sClauError = 'GestorEncargoSacd.dropTabla';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
    }

    /**
     * Crea la nueva tabla de propuestas
     */
    public function crearTabla()
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();

        // Borrar lo que exista:
        $this->borrarTabla();

        $sQuery = "CREATE TABLE $nom_tabla AS 
                SELECT id_schema, id_item, id_enc, id_nom, modo, f_ini, f_fin, id_nom AS id_nom_new
                FROM encargos_sacd WHERE f_fin IS NULL ";
        if (($oDbl->query($sQuery)) === FALSE) {
            $sClauError = 'GestorEncargoSacd.crearTabla';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        // Añadir una nueva sequencia:
        //secuencia
        $esquema_sfsv = ConfigGlobal::mi_region_dl();
        //$esquema = substr($esquema_sfsv,0,-1); // quito la v o la f.

        $id_seq = 'propuesta_encargos_sacd_id_item_seq';
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

        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT propuesta_encargos_sacd_ukey
                    UNIQUE ($campo_seq); ";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY ($campo_seq); ";

        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT propuesta_encargos_sacd_id_enc_ukey
                    UNIQUE (id_enc, id_nom_new, modo, f_ini); ";

        // ajustar la sequencia:
        $a_sql[] = "SELECT setval('$id_seq', COALESCE((SELECT MAX($campo_seq)+1 FROM $nom_tabla), 1), FALSE)
                    FROM information_schema.key_column_usage
                    WHERE constraint_name LIKE '%pkey%'
                    ORDER BY table_name";


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

        /*
        // Añadir el campo de nuevo encargado:
        $sQuery="ALTER TABLE $nom_tabla ADD COLUMN id_nom_new integer ";
        if (($oDbl->query($sQuery)) === FALSE) {
            $sClauError = 'GestorEncargoSacd.crearTabla';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        */

        return TRUE;
    }

    /* MÉTODOS PÚBLICOS COPIADOS DE EncargosSacd  -------------------------------------*/

    /**
     * Elimina los sacd encargados de encargos inexistentes
     */
    public function deleteEncargos()
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "DELETE FROM $nom_tabla s USING encargos e WHERE s.id_enc=e.id_enc AND e.id_enc is null ";
        if (($res = $oDbl->query($sQuery)) === FALSE) {
            $sClauError = 'GestorEncargoSacd.deleteEncargos';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        $count = $res->rowCount();
        return sprintf(_("se han eliminado %s sacd de encargos inexistentes \n"), $count);
    }

    /**
     * retorna l'array d'objectes de tipus EncargoSacd
     *
     * @param string sQuery la query a executar.
     * @return array Una col·lecció d'objectes de tipus EncargoSacd
     */
    function getEncargosSacdQuery($sQuery = '')
    {
        $oDbl = $this->getoDbl();
        $oEncargoSacdSet = new Set();
        if (($oDbl->query($sQuery)) === FALSE) {
            $sClauError = 'GestorEncargoSacd.query';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        foreach ($oDbl->query($sQuery) as $aDades) {
            $a_pkey = array('id_item' => $aDades['id_item']);
            $oEncargoSacd = new PropuestaEncargoSacd($a_pkey);
            $oEncargoSacd->setAllAtributes($aDades);
            $oEncargoSacdSet->add($oEncargoSacd);
        }
        return $oEncargoSacdSet->getTot();
    }

    /**
     * retorna l'array d'objectes de tipus EncargoSacd
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array Una col·lecció d'objectes de tipus EncargoSacd
     */
    function getEncargosSacd($aWhere = array(), $aOperators = array())
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $oEncargoSacdSet = new Set();
        $oCondicion = new Condicion();
        $aCondi = array();
        foreach ($aWhere as $camp => $val) {
            if ($camp == '_ordre') continue;
            $sOperador = isset($aOperators[$camp]) ? $aOperators[$camp] : '';
            if ($a = $oCondicion->getCondicion($camp, $sOperador, $val)) $aCondi[] = $a;
            // operadores que no requieren valores
            if ($sOperador == 'BETWEEN' || $sOperador == 'IS NULL' || $sOperador == 'IS NOT NULL' || $sOperador == 'OR') unset($aWhere[$camp]);
            if ($sOperador == 'IN' || $sOperador == 'NOT IN') unset($aWhere[$camp]);
            if ($sOperador == 'TXT') unset($aWhere[$camp]);
        }
        $sCondi = implode(' AND ', $aCondi);
        if ($sCondi != '') $sCondi = " WHERE " . $sCondi;
        if (isset($GLOBALS['oGestorSessioDelegación'])) {
            $sLimit = $GLOBALS['oGestorSessioDelegación']->getLimitPaginador('a_actividades', $sCondi, $aWhere);
        } else {
            $sLimit = '';
        }
        if ($sLimit === FALSE) return;
        $sOrdre = '';
        if (isset($aWhere['_ordre']) && $aWhere['_ordre'] != '') $sOrdre = ' ORDER BY ' . $aWhere['_ordre'];
        if (isset($aWhere['_ordre'])) unset($aWhere['_ordre']);
        $sQry = "SELECT * FROM $nom_tabla " . $sCondi . $sOrdre . $sLimit;
        if (($oDblSt = $oDbl->prepare($sQry)) === FALSE) {
            $sClauError = 'GestorEncargoSacd.llistar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        if (($oDblSt->execute($aWhere)) === FALSE) {
            $sClauError = 'GestorEncargoSacd.llistar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        foreach ($oDblSt as $aDades) {
            $a_pkey = array('id_item' => $aDades['id_item']);
            $oEncargoSacd = new PropuestaEncargoSacd($a_pkey);
            $oEncargoSacd->setAllAtributes($aDades);
            $oEncargoSacdSet->add($oEncargoSacd);
        }
        return $oEncargoSacdSet->getTot();
    }

}