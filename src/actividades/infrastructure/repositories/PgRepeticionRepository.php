<?php

namespace src\actividades\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\Set;
use PDO;
use src\actividades\domain\contracts\RepeticionRepositoryInterface;
use src\actividades\domain\entity\Repeticion;
use src\actividades\domain\value_objects\RepeticionId;
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
        $oDbl = $GLOBALS['oDBPC'];
        $this->setoDbl($oDbl);
        $oDbl_Select = $GLOBALS['oDBPC_Select'];
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
        $aRepeticion = [];
        foreach ($stmt as $aDades) {
            $id_repeticion = $aDades['id_repeticion'];
            $repeticion = $aDades['repeticion'];
            $aRepeticion[$id_repeticion] = $repeticion;
        }
        return $aRepeticion;
    }

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Repeticion
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo Repeticion
     */
    public function getRepeticiones(array $aWhere = [], array $aOperators = []): array|false
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
            $Repeticion =  Repeticion::fromArray($aDatos);
            $RepeticionSet->add($Repeticion);
        }
        return $RepeticionSet->getTot();
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

        $aDatos = [];
        $aDatos['repeticion'] = $Repeticion->getRepeticion();
        $aDatos['temporada'] = $Repeticion->getTemporada();
        $aDatos['tipo'] = $Repeticion->getTipo();
        array_walk($aDatos, 'core\poner_null');

        if ($bInsert === false) {
            //UPDATE
            $update = "
					repeticion               = :repeticion,
					temporada                = :temporada,
					tipo                     = :tipo";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_repeticion = $id_repeticion";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        } else {
            //INSERT
            $aDatos['id_repeticion'] = $Repeticion->getId_repeticion();
            $campos = "(id_repeticion,repeticion,temporada,tipo)";
            $valores = "(:id_repeticion,:repeticion,:temporada,:tipo)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_repeticion): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_repeticion = $id_repeticion";
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
     * @param int $id_repeticion
     * @return array|bool
     */
    public function datosById(int $id_repeticion): array|bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_repeticion = $id_repeticion";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        return $stmt->fetch(PDO::FETCH_ASSOC);

    }

    public function datosByIdVo(RepeticionId $id): array|bool
    {
        return $this->datosById($id->value());
    }

    /**
     * Busca la clase con id_repeticion en la base de datos .
     */
    public function findById(int $id_repeticion): ?Repeticion
    {
        $aDatos = $this->datosById($id_repeticion);
        if (empty($aDatos)) {
            return null;
        }
        return Repeticion::fromArray($aDatos);
    }

    public function findByIdVo(RepeticionId $id): ?Repeticion
    {
        return $this->findById($id->value());
    }

    public function getNewId()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('xa_tipo_repeticion_id_repeticion_seq'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
    }

    public function getNewIdVo(): RepeticionId
    {
        return new RepeticionId((int)$this->getNewId());
    }
}