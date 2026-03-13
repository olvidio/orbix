<?php

namespace src\ubiscamas\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\Set;
use PDO;
use src\shared\traits\HandlesPdoErrors;
use src\ubiscamas\domain\contracts\CamaRepositoryInterface;
use src\ubiscamas\domain\entity\Cama;

/**
 * Clase que adapta la tabla du_camas a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage ubiscamas
 * @author Daniel Serrabou
 * @version 1.0
 * @created 12/03/2026
 */
class PgCamaRepository extends ClaseRepository implements CamaRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $oDbl = $GLOBALS['oDBPC'];
        $this->setoDbl($oDbl);
        $oDbl_Select = $GLOBALS['oDBPC_Select'];
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('du_camas');
    }

    public function getArrayCamas($sCondicion = ''): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        if (empty($sCondicion)) $sCondicion = "";
        $sQuery = "SELECT id_cama, descripcion FROM $nom_tabla $sCondicion ORDER BY descripcion";
        $stmt = $this->prepareAndExecute($oDbl, $sQuery, [], __METHOD__, __FILE__, __LINE__);
        $aCamas = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $id_cama = $row['id_cama'];
            $descripcion = $row['descripcion'];

            $aCamas[$id_cama] = $descripcion;
        }

        return $aCamas;
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Cama
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo Cama
     */
    public function getCamas(array $aWhere = [], array $aOperators = []): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $CamaSet = new Set();
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
            $Cama = Cama::fromArray($aDatos);
            $CamaSet->add($Cama);
        }
        return $CamaSet->getTot();
    }

    /**
     * devuelve una colección (array) de objetos de tipo Cama para una habitación específica
     *
     * @param int $id_habitacion
     * @return array|false Una colección de objetos de tipo Cama
     */
    public function getCamasByHabitacion(int $id_habitacion): array|false
    {
        return $this->getCamas(['id_habitacion' => $id_habitacion], ['_ordre' => 'descripcion']);
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Cama $Cama): bool
    {
        $id_cama = $Cama->getIdCama();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_cama = :id_cama";
        $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        $aDatos = ['id_cama' => $id_cama];
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(Cama $Cama): bool
    {
        $id_cama = $Cama->getIdCama();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_cama);

        $aDatos = $Cama->toArrayForDatabase();

        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_cama']);
            $update = "
                    id_schema            = :id_schema,
                    id_habitacion        = :id_habitacion,
                    descripcion          = :descripcion,
                    larga                = :larga,
                    vip                  = :vip";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_cama = :id_cama_where";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
            $aDatos['id_cama_where'] = $id_cama;
        } else {
            //INSERT
            $campos = "(id_schema,id_cama,id_habitacion,descripcion,larga,vip)";
            $valores = "(:id_schema,:id_cama,:id_habitacion,:descripcion,:larga,:vip)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    protected function isNew(string $id_cama): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_cama = :id_cama";
        $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        $stmt->execute(['id_cama' => $id_cama]);
        if (!$stmt->rowCount()) {
            return TRUE;
        }
        return false;
    }

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param string $id_cama
     * @return array|bool
     */
    public function datosById(string $id_cama): array|bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_cama = :id_cama";
        $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        $stmt->execute(['id_cama' => $id_cama]);
        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        return $aDatos;
    }

    /**
     * Busca la clase con id_cama en la base de datos .
     */
    public function findById(string $id_cama): ?Cama
    {
        $aDatos = $this->datosById($id_cama);
        if (empty($aDatos)) {
            return null;
        }
        return Cama::fromArray($aDatos);
    }

    public function getNewId()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "SELECT gen_random_uuid()";
        return $oDbl->query($sQuery)->fetchColumn();
    }
}
