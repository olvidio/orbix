<?php

namespace src\utils_database\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\ConfigDB;
use core\DBConnection;
use core\Set;
use PDO;
use PDOException;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;
use src\utils_database\domain\entity\DbSchema;
use src\utils_database\domain\value_objects\DbSchemaCode;
use src\utils_database\domain\value_objects\DbSchemaId;

/**
 * Clase que adapta la tabla db_idschema a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 13/11/2025
 */
class PgDbSchemaRepository extends ClaseRepository implements DbSchemaRepositoryInterface
{
    public function __construct()
    {
        $oDbl = $GLOBALS['oDBPC'];
        $this->setoDbl($oDbl);
        $oDbl_Select = $GLOBALS['oDBPC_Select'];
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('db_idschema');
    }

    /* -------------------- Otras Funciones ---------------------------------------- */

    /**
     * cambiar el nombre de un esquema existente: mantener el número.
     *     las tablas de las tres bases de datos (comun, sv, sf)
     */
    public function cambiarNombre($old, $new, $database): void
    {
        $this->DBCambiarNombre($old, $new, $database);
        $this->DBCambiarNombre($old . 'f', $new . 'f', $database);
        $this->DBCambiarNombre($old . 'v', $new . 'v', $database);
    }

    /**
     * llenar con los nuevos id, las tablas de las tres bases de datos (comun, sv, sf)
     */
    public function llenarNuevo($schema, $database): void
    {
        $Id = $this->getNext($schema);
        $newId = new DbSchemaId($Id);
        $newIdSf = new DbSchemaId($Id - 1000);
        $newIdSv = new DbSchemaId($Id - 2000);

        // No se puede, porque e posible que todavía no exista el esquema
        // foreach (['comun','sv','sf'] as $database) {

        $oDbl = $this->connectar($database);
        $this->setoDbl($oDbl);

        // comun
        $oDbSchema = new DbSchema();
        $oDbSchema->setIdVo($newId);
        $oDbSchema->setSchemaVo(DbSchemaCode::fromString($schema));
        $this->Guardar($oDbSchema);

        // sf
        $oDbSchema = new DbSchema();
        $oDbSchema->setIdVo($newIdSf);
        $oDbSchema->setSchemaVo(DbSchemaCode::fromString($schema . 'f'));
        $this->Guardar($oDbSchema);

        // sv
        $oDbSchema = new DbSchema();
        $oDbSchema->setIdVo($newIdSv);
        $oDbSchema->setSchemaVo(DbSchemaCode::fromString($schema . 'v'));
        $this->Guardar($oDbSchema);
    }

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo DbSchema
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo DbSchema
     */
    public function getDbSchemas(array $aWhere = [], array $aOperators = []): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $DbSchemaSet = new Set();
        $oCondicion = new Condicion();
        $aCondicion = [];
        foreach ($aWhere as $camp => $val) {
            if ($camp === '_ordre') {
                continue;
            }
            if ($camp === '_limit') {
                continue;
            }
            $sOperador = $aOperators[$camp] ?? '';
            if ($a = $oCondicion->getCondicion($camp, $sOperador, $val)) {
                $aCondicion[] = $a;
            }
            // operadores que no requieren valores
            if ($sOperador === 'BETWEEN' || $sOperador === 'IS NULL' || $sOperador === 'IS NOT NULL' || $sOperador === 'OR') {
                unset($aWhere[$camp]);
            }
            if ($sOperador === 'IN' || $sOperador === 'NOT IN') {
                unset($aWhere[$camp]);
            }
            if ($sOperador === 'TXT') {
                unset($aWhere[$camp]);
            }
        }
        $sCondicion = implode(' AND ', $aCondicion);
        if ($sCondicion !== '') {
            $sCondicion = " WHERE " . $sCondicion;
        }
        $sOrdre = '';
        $sLimit = '';
        if (isset($aWhere['_ordre']) && $aWhere['_ordre'] !== '') {
            $sOrdre = ' ORDER BY ' . $aWhere['_ordre'];
        }
        if (isset($aWhere['_ordre'])) {
            unset($aWhere['_ordre']);
        }
        if (isset($aWhere['_limit']) && $aWhere['_limit'] !== '') {
            $sLimit = ' LIMIT ' . $aWhere['_limit'];
        }
        if (isset($aWhere['_limit'])) {
            unset($aWhere['_limit']);
        }
        $sQry = "SELECT * FROM $nom_tabla " . $sCondicion . $sOrdre . $sLimit;
        if (($oDblSt = $oDbl->prepare($sQry)) === false) {
            $sClaveError = 'PgDbSchemaRepository.listar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return false;
        }
        if (($oDblSt->execute($aWhere)) === false) {
            $sClaveError = 'PgDbSchemaRepository.listar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
            return false;
        }

        $filas = $oDblSt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as $aDatos) {
            $DbSchema = new DbSchema();
            $DbSchema->setAllAttributes($aDatos);
            $DbSchemaSet->add($DbSchema);
        }
        return $DbSchemaSet->getTot();
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(DbSchema $DbSchema): bool
    {
        $schema = $DbSchema->getSchemaVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE schema = '$schema'")) === false) {
            $sClaveError = 'PgDbSchemaRepository.eliminar';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return false;
        }
        return TRUE;
    }

    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(DbSchema $DbSchema): bool
    {
        $schema = $DbSchema->getSchemaVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($schema);

        $aDatos = [];
        $aDatos['id'] = $DbSchema->getIdVo()->value();
        array_walk($aDatos, 'core\poner_null');

        if ($bInsert === false) {
            //UPDATE
            $update = "
					id                       = :id";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE schema = '$schema'")) === false) {
                $sClaveError = 'PgDbSchemaRepository.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
                return false;
            }

            try {
                $oDblSt->execute($aDatos);
            } catch (PDOException $e) {
                $err_txt = $e->errorInfo[2];
                $this->setErrorTxt($err_txt);
                $sClaveError = 'PgDbSchemaRepository.update.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
                return false;
            }
        } else {
            // INSERT
            $aDatos['schema'] = $DbSchema->getSchemaVo()->value();
            $campos = "(schema,id)";
            $valores = "(:schema,:id)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClaveError = 'PgDbSchemaRepository.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
                return false;
            }
            try {
                $oDblSt->execute($aDatos);
            } catch (PDOException $e) {
                $err_txt = $e->errorInfo[2];
                $this->setErrorTxt($err_txt);
                $sClaveError = 'PgDbSchemaRepository.insertar.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
                return false;
            }
        }
        return TRUE;
    }

    private function isNew(string $schema): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE schema = '$schema'")) === false) {
            $sClaveError = 'PgDbSchemaRepository.isNew';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return false;
        }
        if (!$oDblSt->rowCount()) {
            return TRUE;
        }
        return false;
    }

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param string $schema
     * @return array|bool
     */
    public function datosById(string $schema): array|bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE schema = '$schema'")) === false) {
            $sClaveError = 'PgDbSchemaRepository.getDatosById';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return false;
        }
        $aDatos = $oDblSt->fetch(PDO::FETCH_ASSOC);
        return $aDatos;
    }

    /**
     * Busca la clase con schema en la base de datos .
     */
    public function findById(string $schema): ?DbSchema
    {
        $aDatos = $this->datosById($schema);
        if (empty($aDatos)) {
            return null;
        }
        return (new DbSchema())->setAllAttributes($aDatos);
    }

    /* -------------------- MÉTODOS PRIVADOS ---------------------------------------- */

    /**
     * Conecta con la base de datos: comun, sf, sv
     *
     * @param string $database
     * @return \PDO
     */
    private function connectar(string $database)
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
     *  retorna el id_schema següent per un esquema.
     *  primer mira si ja hi és
     *
     * @param string schema
     * @return integer id_schema
     */
    private function getNext($schema)
    {
        // comprobar si existe
        $cSchema = $this->getDbSchemas(['schema' => $schema]);
        if (empty($cSchema)) {
            $netxId = $this->getLast() + 1;
        } else {
            $oDbSchema = $cSchema[0];
            $netxId = $oDbSchema->getIdVo()->value();
        }
        return $netxId;
    }

    /**
     *  retorna el id_schema ultim del comun.
     *  primer mira si ja hi és
     *
     * @return integer id_schema
     */
    private function getLast()
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
     * Cambiar nombre: al reves que lo normal, uso de clave el id
     *
     */
    private function DBCambiarNombre($old, $new, $database)
    {
        $oDbl = $this->connectar($database);
        $nom_tabla = $this->getNomTabla();
        //UPDATE
        $update = "UPDATE $nom_tabla SET schema='$new' WHERE schema='$old'";

        try {
            $oDbl->query($update);
        } catch (\PDOException $e) {
            $err_txt = $e->errorInfo[2];
            $this->setErrorTxt($err_txt);
            $sClauError = 'DbSchema.update.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }

        return true;
    }

}