<?php

namespace src\personas\infrastructure\persistence\postgresql;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\postgresql\Condicion;
use src\shared\infrastructure\persistence\postgresql\Set;
use PDO;
use src\personas\domain\contracts\SituacionRepositoryInterface;
use src\personas\domain\entity\Situacion;
use src\shared\infrastructure\GlobalPdo;
use src\shared\traits\HandlesPdoErrors;

/**
 * Clase que adapta la tabla xp_situacion a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 17/11/2025
 */
class PgSituacionRepository extends ClaseRepository implements SituacionRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $this->setoDbl(GlobalPdo::get('oDBPC'));
        $this->setoDbl_select(GlobalPdo::get('oDBPC_Select'));
        $this->setNomTabla('xp_situacion');
    }

    /**
     * @return array<string, string>
     */
    public function getArraySituaciones(bool $traslado = false): array
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $Condicion = '';
        if ($traslado) {
            $Condicion = " WHERE situacion IN ('A','D','E','L','T','X')";
        }
        $sQuery = "SELECT situacion,nombre_situacion FROM $nom_tabla $Condicion ORDER BY situacion";
        $stmt = $this->PdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $aOpciones = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $aClave) {
            if (!is_array($aClave) || !isset($aClave['situacion'], $aClave['nombre_situacion'])) {
                continue;
            }
            $aOpciones[(string)$aClave['situacion']] = (string)$aClave['nombre_situacion'];
        }

        return $aOpciones;
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Situacion
     *
     * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
     * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return list<Situacion> Una colección de objetos de tipo Situacion
     */
    public function getSituaciones(array $aWhere = [], array $aOperators = []): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $SituacionSet = new Set();
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
        $ordreVal = $aWhere['_ordre'] ?? null;
        if (is_string($ordreVal) && $ordreVal !== '') {
            $sOrdre = ' ORDER BY ' . $ordreVal;
        }
        if (isset($aWhere['_ordre'])) {
            unset($aWhere['_ordre']);
        }
        $limitVal = $aWhere['_limit'] ?? null;
        if ((is_string($limitVal) || is_int($limitVal)) && (string) $limitVal !== '') {
            $sLimit = ' LIMIT ' . $limitVal;
        }
        if (isset($aWhere['_limit'])) {
            unset($aWhere['_limit']);
        }
        $sQry = "SELECT * FROM $nom_tabla " . $sCondicion . $sOrdre . $sLimit;
        $stmt = $this->prepareAndExecute($oDbl, $sQry, $aWhere, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as $aDatos) {
            $Situacion = Situacion::fromArray($aDatos);
            $SituacionSet->add($Situacion);
        }
        return array_values($SituacionSet->getTot());
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Situacion $Situacion): bool
    {
        $situacion = $Situacion->getSituacionVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE situacion = '$situacion'";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(Situacion $Situacion): bool
    {
        $situacion = $Situacion->getSituacionVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($situacion);

        $aDatos = $Situacion->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['situacion']);
            $update = "
					nombre_situacion         = :nombre_situacion";
            $sql = "UPDATE $nom_tabla SET $update WHERE situacion = '$situacion'";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            //INSERT
            $campos = "(situacion,nombre_situacion)";
            $valores = "(:situacion,:nombre_situacion)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(string $situacion): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE situacion = '$situacion'";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return true;
        }
        if (!$stmt->rowCount()) {
            return TRUE;
        }
        return false;
    }

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param string $situacion
     * @return array<string, mixed>|false
     */
    public function datosById(string $situacion): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE situacion = '$situacion'";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }
        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!is_array($aDatos)) {
            return false;
        }
        $result = [];
        foreach ($aDatos as $key => $value) {
            $result[(string) $key] = $value;
        }
        return $result;

    }

    /**
     * Busca la clase con situacion en la base de datos .
     */
    public function findById(string $situacion): ?Situacion
    {
        $aDatos = $this->datosById($situacion);
        if ($aDatos === false) {
            return null;
        }
        return Situacion::fromArray($aDatos);
    }
}