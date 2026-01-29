<?php

namespace src\asignaturas\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\Set;
use PDO;
use src\asignaturas\domain\contracts\DepartamentoRepositoryInterface;
use src\asignaturas\domain\entity\Departamento;
use src\shared\traits\HandlesPdoErrors;

/**
 * Clase que adapta la tabla xe_departamentos a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 14/11/2025
 */
class PgDepartamentoRepository extends ClaseRepository implements DepartamentoRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $oDbl = $GLOBALS['oDBPC'];
        $this->setoDbl($oDbl);
        $oDbl_Select = $GLOBALS['oDBPC_Select'];
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('xe_departamentos');
    }

    public function getArrayDepartamentos(): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_departamento,departamento 
				FROM $nom_tabla
				ORDER BY departamento";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        $aOpciones = [];
        foreach ($stmt as $aClave) {
            $clave = $aClave[0];
            $val = $aClave[1];
            $aOpciones[$clave] = $val;
        }
        return $aOpciones;
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Departamento
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo Departamento
     */
    public function getDepartamentos(array $aWhere = [], array $aOperators = []): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $DepartamentoSet = new Set();
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
            $Departamento =  Departamento::fromArray($aDatos);
            $DepartamentoSet->add($Departamento);
        }
        return $DepartamentoSet->getTot();
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Departamento $Departamento): bool
    {
        $id_departamento = $Departamento->getIdDepartamentoVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_departamento = $id_departamento";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(Departamento $Departamento): bool
    {
        $id_departamento = $Departamento->getIdDepartamentoVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_departamento);

        $aDatos = $Departamento->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_departamento']);
            $update = "
                    departamento             = :departamento";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_departamento = $id_departamento";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            //INSERT
            $campos = "(id_departamento,departamento)";
            $valores = "(:id_departamento,:departamento)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_departamento): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_departamento = $id_departamento";
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
     * @param int $id_departamento
     * @return array|bool
     */
    public function datosById(int $id_departamento): array|bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_departamento = $id_departamento";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        return $stmt->fetch(PDO::FETCH_ASSOC);

    }

    /**
     * Busca la clase con id_departamento en la base de datos .
     */
    public function findById(int $id_departamento): ?Departamento
    {
        $aDatos = $this->datosById($id_departamento);
        if (empty($aDatos)) {
            return null;
        }
        return Departamento::fromArray($aDatos);
    }

    public function getNewId()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('xe_departamen_id_departamen_seq'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
    }
}