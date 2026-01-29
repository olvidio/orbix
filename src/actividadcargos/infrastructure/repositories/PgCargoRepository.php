<?php

namespace src\actividadcargos\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\Set;
use PDO;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividadcargos\domain\entity\Cargo;
use src\actividadcargos\domain\value_objects\TipoCargoCode;
use src\shared\traits\HandlesPdoErrors;
use function core\is_true;

/**
 * Clase que adapta la tabla xd_orden_cargo a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 17/11/2025
 */
class PgCargoRepository extends ClaseRepository implements CargoRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $oDbl = $GLOBALS['oDBPC'];
        $this->setoDbl($oDbl);
        $oDbl_Select = $GLOBALS['oDBPC_Select'];
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('xd_orden_cargo');
    }

    public function getArrayIdCargosSacd(): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();

        $tipo_cargo = TipoCargoCode::SACD;
        $where = empty($tipo_cargo) ? '' : " WHERE tipo_cargo = '$tipo_cargo' ";
        $sQuery = "SELECT id_cargo,cargo 
                FROM $nom_tabla
                $where
                ORDER BY orden_cargo";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);

        $aIdCargo = [];
        foreach ($oDbl->query($sQuery) as $aDades) {
            $id_cargo = $aDades['id_cargo'];
            $aIdCargo[] = $id_cargo;
        }
        return $aIdCargo;

    }
    public function getArrayCargos(string $tipo_cargo = ''): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();

        $where = empty($tipo_cargo) ? '' : " WHERE tipo_cargo = '$tipo_cargo' ";
        $sQuery = "SELECT id_cargo,cargo 
                FROM $nom_tabla
                $where
                ORDER BY orden_cargo";
        if (($oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorCargo.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        $aIdCargo = [];
        foreach ($oDbl->query($sQuery) as $aDades) {
            $id_cargo = $aDades['id_cargo'];
            $cargo = $aDades['cargo'];
            $aIdCargo[$id_cargo] = $cargo;
        }
        return $aIdCargo;

    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Cargo
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo Cargo
     */
    public function getCargos(array $aWhere = [], array $aOperators = []): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $CargoSet = new Set();
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
        $stmt = $this->prepareAndExecute($oDbl, $sQry, $aWhere,__METHOD__, __FILE__, __LINE__);

        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as $aDatos) {
            $Cargo = Cargo::fromArray($aDatos);
            $CargoSet->add($Cargo);
        }
        return $CargoSet->getTot();
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Cargo $Cargo): bool
    {
        $id_cargo = $Cargo->getId_cargo();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_cargo = $id_cargo";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(Cargo $Cargo): bool
    {
        $id_cargo = $Cargo->getId_cargo();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_cargo);

        $aDatos = $Cargo->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
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
            //INSERT
            $campos = "(id_cargo,cargo,orden_cargo,sf,sv,tipo_cargo)";
            $valores = "(:id_cargo,:cargo,:orden_cargo,:sf,:sv,:tipo_cargo)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_cargo): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_cargo = $id_cargo";
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
     * @param int $id_cargo
     * @return array|bool
     */
    public function datosById(int $id_cargo): array|bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_cargo = $id_cargo";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        return $stmt->fetch(PDO::FETCH_ASSOC);

    }


    /**
     * Busca la clase con id_cargo en la base de datos .
     */
    public function findById(int $id_cargo): ?Cargo
    {
        $aDatos = $this->datosById($id_cargo);
        if (empty($aDatos)) {
            return null;
        }
        return Cargo::fromArray($aDatos);
    }

    public function getNewId()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('xd_orden_cargo_id_cargo_seq'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
    }
}