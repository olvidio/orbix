<?php

namespace src\utils_database\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\Set;
use PDO;
use PDOException;
use src\shared\traits\HandlesPdoErrors;
use src\utils_database\domain\contracts\MapIdRepositoryInterface;
use src\utils_database\domain\entity\MapId;

/**
 * Clase que adapta la tabla map_id a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 13/11/2025
 */
class PgMapIdRepository extends ClaseRepository implements MapIdRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $oDbl = $GLOBALS['oDBRC'];
        $this->setoDbl($oDbl);
        $oDbl_Select = $GLOBALS['oDBRC_Select'];
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('map_id');
    }

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo MapId
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo MapId
     */
    public function getMapIdes(array $aWhere = [], array $aOperators = []): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $MapIdSet = new Set();
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
        $stmt = $this->prepareAndExecute($oDbl, $sQry, $aWhere, __METHOD__, __FILE__, __LINE__);

        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as $aDatos) {
            $MapId = MapId::fromArray($aDatos);
            $MapIdSet->add($MapId);
        }
        return $MapIdSet->getTot();
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(MapId $MapId): bool
    {
        $objeto = $MapId->getObjetoVo()->value();
        $id_resto = $MapId->getIdRestoVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE objeto = '$objeto' AND id_resto = $id_resto ";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(MapId $MapId): bool
    {
        $objeto = $MapId->getObjetoVo()->value();
        $id_resto = $MapId->getIdRestoVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($objeto, $id_resto);

        $aDatos = $MapId->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            $update = "
					id_dl                    = :id_dl";
            $sql = "UPDATE $nom_tabla SET $update WHERE objeto = '$objeto' AND id_resto = $id_resto ";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            //INSERT
            $aDatos['objeto'] = $MapId->getObjetoVo()->value();
            $aDatos['id_resto'] = $MapId->getIdRestoVo()->value();
            $campos = "(objeto,id_resto,id_dl)";
            $valores = "(:objeto,:id_resto,:id_dl)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(string $objeto, int $id_resto): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE objeto = '$objeto' AND id_resto = $id_resto";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if (!$stmt->rowCount()) {
            return TRUE;
        }
        return false;
    }

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param string $objeto
     * @return array|bool
     */
    public function datosById(string $objeto, int $id_resto): array|bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE objeto = '$objeto' AND id_resto = $id_resto";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        return $stmt->fetch(PDO::FETCH_ASSOC);

    }

    /**
     * Busca la clase con objeto en la base de datos .
     */
    public function findById(string $objeto, int $id_resto): ?MapId
    {
        $aDatos = $this->datosById($objeto, $id_resto);
        if (empty($aDatos)) {
            return null;
        }
        return MapId::fromArray($aDatos);
    }
}