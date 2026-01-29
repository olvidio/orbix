<?php

namespace src\zonassacd\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\Set;
use PDO;
use src\shared\traits\HandlesPdoErrors;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;
use src\zonassacd\domain\entity\Zona;


/**
 * Clase que adapta la tabla zonas a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 24/12/2025
 */
class PgZonaRepository extends ClaseRepository implements ZonaRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $oDbl = $GLOBALS['oDBE'];
        $this->setoDbl($oDbl);
        $oDbl_Select = $GLOBALS['oDBE_Select'];
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('zonas');
    }

     public function isJefeZona(int $id_nom): bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();

        //SELECT EXISTS(SELECT 1 FROM contact WHERE id=12)
        $sQuery = "SELECT EXISTS(SELECT 1
					FROM $nom_tabla
					WHERE id_nom = $id_nom)";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);

        $row = $stmt->fetch();
        return $row[0];
    }


    public function getArrayZonas(?int $iid_nom_jefe = null): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sCondicion = '';
        if ($iid_nom_jefe !== null) {
            $sCondicion = "WHERE id_nom = $iid_nom_jefe";
        }
        $sQuery = "SELECT id_zona, nombre_zona
					FROM $nom_tabla $sCondicion
					ORDER BY orden";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);

        $aOpciones = [];
        foreach ($stmt as $aDades) {
            $id_zona = $aDades['id_zona'];
            $nombre_zona = $aDades['nombre_zona'];
            $aOpciones[$id_zona] = $nombre_zona;
        }
        return $aOpciones;
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Zona
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo Zona
     */
    public function getZonas(array $aWhere = [], array $aOperators = []): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $ZonaSet = new Set();
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
            $Zona = Zona::fromArray($aDatos);
            $ZonaSet->add($Zona);
        }
        return $ZonaSet->getTot();
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Zona $Zona): bool
    {
        $id_zona = $Zona->getId_zona();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_zona = $id_zona";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(Zona $Zona): bool
    {
        $id_zona = $Zona->getId_zona();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_zona);

        $aDatos = $Zona->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_zona']);
            $update = "
					nombre_zona              = :nombre_zona,
					orden                    = :orden,
					id_grupo                 = :id_grupo,
					id_nom                   = :id_nom";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_zona = $id_zona";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            // INSERT
            $campos = "(id_zona,nombre_zona,orden,id_grupo,id_nom)";
            $valores = "(:id_zona,:nombre_zona,:orden,:id_grupo,:id_nom)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_zona): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_zona = $id_zona";
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
     * @param int $id_zona
     * @return array|bool
     */
    public function datosById(int $id_zona): array|bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_zona = $id_zona";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        return $aDatos;
    }


    /**
     * Busca la clase con id_zona en la base de datos .
     */
    public function findById(int $id_zona): ?Zona
    {
        $aDatos = $this->datosById($id_zona);
        if (empty($aDatos)) {
            return null;
        }
        return Zona::fromArray($aDatos);
    }

    public function getNewId()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('zonas_id_zona_seq'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
    }
}