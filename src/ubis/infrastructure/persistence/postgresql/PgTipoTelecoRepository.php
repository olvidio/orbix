<?php

namespace src\ubis\infrastructure\persistence\postgresql;
use src\shared\infrastructure\GlobalPdo;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\postgresql\Condicion;
use src\shared\infrastructure\persistence\postgresql\Set;
use PDO;
use src\shared\traits\HandlesPdoErrors;
use src\ubis\domain\contracts\TipoTelecoRepositoryInterface;
use src\ubis\domain\entity\TipoTeleco;
use function src\shared\domain\helpers\is_true;

/**
 * Clase que adapta la tabla xd_tipo_teleco a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 17/11/2025
 */
class PgTipoTelecoRepository extends ClaseRepository implements TipoTelecoRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $oDbl = GlobalPdo::get('oDBPC');
        $this->setoDbl($oDbl);
        $oDbl_Select = GlobalPdo::get('oDBPC_Select');
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('xd_tipo_teleco');
    }

    /**
     * @return array<int|string, string>
     */
    public function getArrayTiposTelecoPersona(): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $oTipoCentroSet = new Set();
        $sQuery = "SELECT id, nombre_teleco
				FROM $nom_tabla
				WHERE persona='t'
				ORDER BY nombre_teleco";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $aOpciones = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (!is_array($row) || !isset($row['id'], $row['nombre_teleco'])) {
                continue;
            }
            $aOpciones[(string) $row['id']] = (string) $row['nombre_teleco'];
        }

        return $aOpciones;
    }

    /**
     * @return array<int|string, string>
     */
    public function getArrayTiposTelecoUbi(): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $oTipoCentroSet = new Set();
        $sQuery = "SELECT id, nombre_teleco
				FROM $nom_tabla
				WHERE ubi='t'
				ORDER BY nombre_teleco";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $aOpciones = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (!is_array($row) || !isset($row['id'], $row['nombre_teleco'])) {
                continue;
            }
            $aOpciones[(string) $row['id']] = (string) $row['nombre_teleco'];
        }

        return $aOpciones;
    }

    /**
     * @return array<int|string, string>
     */
    public function getArrayTiposTeleco(): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $oTipoCentroSet = new Set();
        $sQuery = "SELECT id, nombre_teleco
				FROM $nom_tabla
				ORDER BY nombre_teleco";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $aOpciones = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (!is_array($row) || !isset($row['id'], $row['nombre_teleco'])) {
                continue;
            }
            $aOpciones[(string) $row['id']] = (string) $row['nombre_teleco'];
        }

        return $aOpciones;
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo TipoTeleco
     *
     * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
     * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return list<TipoTeleco> Una colección de objetos de tipo TipoTeleco
     */
    public function getTiposTeleco(array $aWhere = [], array $aOperators = []): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $TipoTelecoSet = new Set();
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
            if (!is_array($aDatos)) {
                continue;
            }
            $TipoTeleco = TipoTeleco::fromArray($aDatos);
            $TipoTelecoSet->add($TipoTeleco);
        }
        return array_values($TipoTelecoSet->getTot());
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(TipoTeleco $TipoTeleco): bool
    {
        $id = $TipoTeleco->getId();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id = $id";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(TipoTeleco $TipoTeleco): bool
    {
        $id = $TipoTeleco->getId();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id);

        $aDatos = $TipoTeleco->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id']);
            $update = "
					tipo_teleco              = :tipo_teleco,
					nombre_teleco            = :nombre_teleco,
					ubi                      = :ubi,
					persona                  = :persona";
            $sql = "UPDATE $nom_tabla SET $update WHERE id = $id";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        else {
            //INSERT
            $campos = "(tipo_teleco,nombre_teleco,ubi,persona,id)";
            $valores = "(:tipo_teleco,:nombre_teleco,:ubi,:persona,:id)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id = $id";
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
     * @param int $id
     * @return array<string, mixed>|false
     */
    public function datosById(int $id): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id = $id";
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
     * Busca la clase con id en la base de datos .
     */
    public function findById(int $id): ?TipoTeleco
    {
        $aDatos = $this->datosById($id);
        if ($aDatos === false) {
            return null;
        }
        return TipoTeleco::fromArray($aDatos);
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('xd_tipo_teleco_id_seq'::regclass)";
        $stmt = $oDbl->query($sQuery);
        if ($stmt === false) {
            return 0;
        }
        $id = $stmt->fetchColumn();

        return is_numeric($id) ? (int) $id : 0;
    }
}