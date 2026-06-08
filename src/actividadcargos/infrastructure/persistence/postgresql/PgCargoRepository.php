<?php

namespace src\actividadcargos\infrastructure\persistence\postgresql;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\postgresql\Condicion;
use PDO;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividadcargos\domain\entity\Cargo;
use src\actividadcargos\domain\value_objects\TipoCargoCode;
use src\shared\infrastructure\GlobalPdo;
use src\shared\traits\HandlesPdoErrors;

/**
 * Clase que adapta la tabla xd_orden_cargo a la interfaz del repositorio
 */
class PgCargoRepository extends ClaseRepository implements CargoRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $oDbl = GlobalPdo::get('oDBPC');
        $this->setoDbl($oDbl);
        $oDbl_Select = GlobalPdo::get('oDBPC_Select');
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('xd_orden_cargo');
    }

    /**
     * @return list<int>
     */
    public function getArrayIdCargosSacd(): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();

        $tipo_cargo = TipoCargoCode::SACD;
        $where = " WHERE tipo_cargo = '$tipo_cargo' ";
        $sQuery = "SELECT id_cargo,cargo 
                FROM $nom_tabla
                $where
                ORDER BY orden_cargo";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $aIdCargo = [];
        foreach ($stmt as $aDades) {
            if (!is_array($aDades) || !isset($aDades['id_cargo'])) {
                continue;
            }
            $aIdCargo[] = is_numeric($aDades['id_cargo']) ? (int) $aDades['id_cargo'] : 0;
        }
        return $aIdCargo;
    }

    /**
     * @return array<int|string, string>
     */
    public function getArrayCargos(string $tipo_cargo = ''): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();

        $where = $tipo_cargo === '' ? '' : " WHERE tipo_cargo = '$tipo_cargo' ";
        $sQuery = "SELECT id_cargo,cargo 
                FROM $nom_tabla
                $where
                ORDER BY orden_cargo";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $aIdCargo = [];
        foreach ($stmt as $aDades) {
            if (!is_array($aDades) || !isset($aDades['id_cargo'], $aDades['cargo'])) {
                continue;
            }
            $id_cargo = $aDades['id_cargo'];
            $cargo = (string) $aDades['cargo'];
            $aIdCargo[$id_cargo] = $cargo;
        }
        return $aIdCargo;
    }

    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<Cargo>
     */
    public function getCargos(array $aWhere = [], array $aOperators = []): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $oCondicion = new Condicion();
        $aCondicion = [];
        foreach ($aWhere as $camp => $val) {
            if ($camp === '_ordre' || $camp === '_limit') {
                continue;
            }
            $sOperador = $aOperators[$camp] ?? '';
            if ($a = $oCondicion->getCondicion($camp, $sOperador, $val)) {
                $aCondicion[] = $a;
            }
            if ($sOperador === 'BETWEEN' || $sOperador === 'IS NULL' || $sOperador === 'IS NOT NULL' || $sOperador === 'OR') {
                unset($aWhere[$camp]);
            }
            if ($sOperador === 'IN' || $sOperador === 'NOT IN' || $sOperador === 'TXT') {
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
        $cargos = [];
        foreach ($filas as $aDatos) {
            if (!is_array($aDatos)) {
                continue;
            }
            $normalized = [];
            foreach ($aDatos as $key => $value) {
                $normalized[(string) $key] = $value;
            }
            $cargos[] = Cargo::fromArray($normalized);
        }

        return $cargos;
    }

    public function Eliminar(Cargo $Cargo): bool
    {
        $id_cargo = $Cargo->getId_cargo();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_cargo = $id_cargo";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    public function Guardar(Cargo $Cargo): bool
    {
        $id_cargo = $Cargo->getId_cargo();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_cargo);

        $aDatos = $Cargo->toArrayForDatabase();
        if ($bInsert === false) {
            unset($aDatos['id_cargo']);
            $update = "
					cargo                    = :cargo,
					orden_cargo              = :orden_cargo,
					sf                       = :sf,
					sv                       = :sv,
					tipo_cargo               = :tipo_cargo";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_cargo = $id_cargo";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            $campos = "(id_cargo,cargo,orden_cargo,sf,sv,tipo_cargo)";
            $valores = "(:id_cargo,:cargo,:orden_cargo,:sf,:sv,:tipo_cargo)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_cargo): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_cargo = $id_cargo";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return true;
        }
        if (!$stmt->rowCount()) {
            return true;
        }
        return false;
    }

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_cargo): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_cargo = $id_cargo";
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

    public function findById(int $id_cargo): ?Cargo
    {
        $aDatos = $this->datosById($id_cargo);
        if ($aDatos === false) {
            return null;
        }
        return Cargo::fromArray($aDatos);
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('xd_orden_cargo_id_cargo_seq'::regclass)";
        $stmt = $oDbl->query($sQuery);
        if ($stmt === false) {
            return 0;
        }
        $id = $stmt->fetchColumn();

        return is_numeric($id) ? (int) $id : 0;
    }
}
