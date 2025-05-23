<?php

namespace devel\model\entity;

use core\ClaseGestor;
use core\Condicion;
use core\ConfigDB;
use core\DBConnection;
use core\Set;

/**
 * GestorDbSchema
 *
 * Classe per gestionar la llista d'objectes de la clase DbSchema
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 19/06/2018
 */
class GestorDbSchema extends ClaseGestor
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */


    
    function __construct()
    {
        $oDbl = $GLOBALS['oDBPC'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('db_idschema');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    /**
     * Conecta con la base de datos: comun, sf, sv
     *
     * @param string $database
     * @return \PDO
     */
    function connectar(string $database)
    {
        switch ($database) {
            case 'comun':
                $oDB = $this->getoDbl();
                break;
            case 'sv':
                $oConfigDB = new ConfigDB('sv');
                $config = $oConfigDB->getEsquema('public');
                $oConexion = new DBConnection($config);
                $oDB = $oConexion->getPDO();
                break;
            case 'sv-e':
                $oConfigDB = new ConfigDB('sv-e');
                $config = $oConfigDB->getEsquema('public');
                $oConexion = new DBConnection($config);
                $oDB = $oConexion->getPDO();
                break;
            case 'sf':
                // Conectar Db df
                $oConfigDB = new ConfigDB('sf');
                $config = $oConfigDB->getEsquema('public');
                $oConexion = new DBConnection($config);
                $oDB = $oConexion->getPDO();
                $this->setoDbl($oDB);
                break;
        }
        return $oDB;
    }

    /**
     * camir el nombre de un esquema existente: mantener el número.
     *     las tablas de las tres bases de datos (comun, sv, sf)
     */
    function cambiarNombre($old, $new, $database)
    {

        $oDbl = $this->connectar($database);
        $oDbSchema = new DbSchema();
        $oDbSchema->setoDbl($oDbl);
        $oDbSchema->DBCambiarNombre($old, $new);
        $oDbSchema = new DbSchema();
        $oDbSchema->setoDbl($oDbl);
        $oDbSchema->DBCambiarNombre($old . 'f', $new . 'f');
        $oDbSchema = new DbSchema();
        $oDbSchema->setoDbl($oDbl);
        $oDbSchema->DBCambiarNombre($old . 'v', $new . 'v');
    }

    /**
     * llenar con los nuevos id, las tablas de las tres bases de datos (comun, sv, sf)
     */
    function llenarNuevo($schema, $database)
    {
        $newId = $this->getNext($schema);
        $newIdSf = $newId - 1000;
        $newIdSv = $newId - 2000;

        // No se puede, porque e posible que todavía no exista el esquema
        // foreach (['comun','sv','sf'] as $database) {

        $oDbl = $this->connectar($database);
        $oDbSchema = new DbSchema();
        $oDbSchema->setoDbl($oDbl);
        $oDbSchema->setId($newId);
        $oDbSchema->setSchema($schema);
        $oDbSchema->DBGuardar();
        $oDbSchema = new DbSchema();
        $oDbSchema->setoDbl($oDbl);
        $oDbSchema->setId($newIdSf);
        $oDbSchema->setSchema($schema . 'f');
        $oDbSchema->DBGuardar();
        $oDbSchema = new DbSchema();
        $oDbSchema->setoDbl($oDbl);
        $oDbSchema->setId($newIdSv);
        $oDbSchema->setSchema($schema . 'v');
        $oDbSchema->DBGuardar();
    }

    /**
     *  retorna el id_schema següent per un esquema.
     *  primer mira si ja hi és
     *
     * @param string schema
     * @return integer id_schema
     */
    function getNext($schema)
    {
        // comprobar si existe
        $cSchema = $this->getDbSchemas(['schema' => $schema]);
        if (empty($cSchema)) {
            $netxId = $this->getLast() + 1;
        } else {
            $oDbSchema = $cSchema[0];
            $netxId = $oDbSchema->getId();
        }
        return $netxId;
    }

    /**
     *  retorna el id_schema ultim del comun.
     *  primer mira si ja hi és
     *
     * @return integer id_schema
     */
    function getLast()
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sQry = "SELECT id FROM $nom_tabla 
            WHERE id BETWEEN 3000 AND 4000
            ORDER BY id DESC
            LIMIT 1";
        foreach ($oDbl->query($sQry) as $aDades) {
            $lastId = $aDades['id'];
        }
        return $lastId;
    }

    /**
     * retorna l'array d'objectes de tipus DbSchema
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array|void
     */
    function getDbSchemas($aWhere = [], $aOperators = array())
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $oDbSchemaSet = new Set();
        $oCondicion = new Condicion();
        $aCondi = [];
        foreach ($aWhere as $camp => $val) {
            if ($camp === '_ordre') continue;
            $sOperador = isset($aOperators[$camp]) ? $aOperators[$camp] : '';
            if ($a = $oCondicion->getCondicion($camp, $sOperador, $val)) $aCondi[] = $a;
            // operadores que no requieren valores
            if ($sOperador === 'BETWEEN' || $sOperador === 'IS NULL' || $sOperador === 'IS NOT NULL' || $sOperador === 'OR') unset($aWhere[$camp]);
            if ($sOperador === 'IN' || $sOperador === 'NOT IN') unset($aWhere[$camp]);
            if ($sOperador === 'TXT') unset($aWhere[$camp]);
        }
        $sCondi = implode(' AND ', $aCondi);
        if ($sCondi != '') $sCondi = " WHERE " . $sCondi;
        if (isset($GLOBALS['oGestorSessioDelegación'])) {
            $sLimit = $GLOBALS['oGestorSessioDelegación']->getLimitPaginador('a_actividades', $sCondi, $aWhere);
        } else {
            $sLimit = '';
        }
        if ($sLimit === false) return;
        $sOrdre = '';
        if (isset($aWhere['_ordre']) && $aWhere['_ordre'] != '') $sOrdre = ' ORDER BY ' . $aWhere['_ordre'];
        if (isset($aWhere['_ordre'])) unset($aWhere['_ordre']);
        $sQry = "SELECT * FROM $nom_tabla " . $sCondi . $sOrdre . $sLimit;
        if (($oDblSt = $oDbl->prepare($sQry)) === false) {
            $sClauError = 'GestorDbSchema.llistar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        if (($oDblSt->execute($aWhere)) === false) {
            $sClauError = 'GestorDbSchema.llistar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDblSt as $aDades) {
            $a_pkey = array('schema' => $aDades['schema']);
            $oDbSchema = new DbSchema($a_pkey);
            $oDbSchemaSet->add($oDbSchema);
        }
        return $oDbSchemaSet->getTot();
    }

    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/
}
