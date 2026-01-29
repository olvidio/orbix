<?php

namespace src\inventario\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\Set;
use PDO;
use src\inventario\domain\contracts\WhereisRepositoryInterface;
use src\inventario\domain\entity\Whereis;
use src\shared\traits\HandlesPdoErrors;

/**
 * Clase que adapta la tabla i_whereis_dl a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/3/2025
 */
class PgWhereisRepository extends ClaseRepository implements WhereisRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $oDbl = $GLOBALS['oDB'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('i_whereis_dl');
    }

    public function getArrayIdFromIdEgms(array $aEgms): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();

        $sQuery = "SELECT id_doc,id_item_egm FROM $nom_tabla ORDER BY id_item_egm";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);

        $aOpciones = [];
        foreach ($stmt as $aClave) {
            if (in_array($aClave[1], $aEgms, true)) {
                $aOpciones[] = $aClave[0];
            }
        }
        return $aOpciones;
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Whereis
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo Whereis
     */
    public function getWhereare(array $aWhere = [], array $aOperators = []): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $WhereisSet = new Set();
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
            $Whereis = Whereis::fromArray($aDatos);
            $WhereisSet->add($Whereis);
        }
        return $WhereisSet->getTot();
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Whereis $Whereis): bool
    {
        $id_item_whereis = $Whereis->getId_item_whereis();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_item_whereis = $id_item_whereis";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(Whereis $Whereis): bool
    {
        $id_item_whereis = $Whereis->getId_item_whereis();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_item_whereis);

        $aDatos = $Whereis->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_item_whereis']);
            $update = "
                    id_item_egm              = :id_item_egm,
                    id_doc                   = :id_doc";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_item_whereis = $id_item_whereis";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            //INSERT
            $campos = "(id_item_whereis,id_item_egm,id_doc)";
            $valores = "(:id_item_whereis,:id_item_egm,:id_doc)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_item_whereis): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_item_whereis = $id_item_whereis";
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
     * @param int $id_item_whereis
     * @return array|bool
     */
    public function datosById(int $id_item_whereis): array|bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_item_whereis = $id_item_whereis";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    /**
     * Busca la clase con id_item_whereis en la base de datos .
     */
    public function findById(int $id_item_whereis): ?Whereis
    {
        $aDatos = $this->datosById($id_item_whereis);
        if (empty($aDatos)) {
            return null;
        }
        return Whereis::fromArray($aDatos);
    }

    public function getNewId()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('i_whereis_dl_id_item_whereis_seq'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
    }
}