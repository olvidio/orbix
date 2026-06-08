<?php

namespace src\actividades\infrastructure\persistence\postgresql;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\postgresql\Condicion;
use src\shared\infrastructure\persistence\postgresql\Set;
use PDO;
use src\actividades\domain\contracts\RepeticionRepositoryInterface;
use src\actividades\domain\entity\Repeticion;
use src\actividades\domain\value_objects\RepeticionId;
use src\shared\infrastructure\GlobalPdo;
use src\shared\traits\HandlesPdoErrors;

/**
 * Clase que adapta la tabla xa_tipo_repeticion a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 18/11/2025
 */
class PgRepeticionRepository extends ClaseRepository implements RepeticionRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $oDbl = GlobalPdo::get('oDBPC');
        $this->setoDbl($oDbl);
        $oDbl_Select = GlobalPdo::get('oDBPC_Select');
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('xa_tipo_repeticion');
    }

    public function getArrayRepeticion(): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_repeticion, repeticion
				FROM $nom_tabla
				ORDER BY repeticion";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }
        $aRepeticion = [];
        foreach ($stmt as $aDades) {
            if (!is_array($aDades)) {
                continue;
            }
            $id_repeticion = $aDades['id_repeticion'] ?? null;
            $repeticion = $aDades['repeticion'] ?? '';
            if (is_int($id_repeticion) || is_string($id_repeticion)) {
                $aRepeticion[(int) $id_repeticion] = is_scalar($repeticion) ? (string) $repeticion : '';
            }
        }
        return $aRepeticion;
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<Repeticion>
     */
    public function getRepeticiones(array $aWhere = [], array $aOperators = []): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $RepeticionSet = new Set();
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
        /** @var list<Repeticion> $items */
        $items = [];
        foreach ($filas as $aDatos) {
            if (!is_array($aDatos)) {
                continue;
            }
            $normalized = [];
            foreach ($aDatos as $key => $value) {
                $normalized[(string) $key] = $value;
            }
            $items[] = Repeticion::fromArray($normalized);
        }
        return $items;
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Repeticion $Repeticion): bool
    {
        $id_repeticion = $Repeticion->getId_repeticion();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_repeticion = $id_repeticion";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(Repeticion $Repeticion): bool
    {
        $id_repeticion = $Repeticion->getId_repeticion();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_repeticion);

        $aDatos = $Repeticion->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_repeticion']);
            $update = "
					repeticion               = :repeticion,
					temporada                = :temporada,
					tipo                     = :tipo";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_repeticion = $id_repeticion";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            $campos = "(id_repeticion,repeticion,temporada,tipo)";
            $valores = "(:id_repeticion,:repeticion,:temporada,:tipo)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->pdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_repeticion): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_repeticion = $id_repeticion";
        $stmt = $this->pdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
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
     * @param RepeticionId $id_repeticion
     * @return array<string, mixed>|false
     */
    public function datosById(RepeticionId $id_repeticion): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $idVal = $id_repeticion->value();
        $sql = "SELECT * FROM $nom_tabla WHERE id_repeticion = $idVal";
        $stmt = $this->pdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
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
     * Busca la clase con id_repeticion en la base de datos .
     */
    public function findById(RepeticionId $id_repeticion): ?Repeticion
    {
        $aDatos = $this->datosById($id_repeticion);
        if ($aDatos === false) {
            return null;
        }
        return Repeticion::fromArray($aDatos);
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('xa_tipo_repeticion_id_repeticion_seq'::regclass)";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return 0;
        }
        return (int) $stmt->fetchColumn();
    }

    public function getNewIdVo(): RepeticionId
    {
        return new RepeticionId((int)$this->getNewId());
    }
}