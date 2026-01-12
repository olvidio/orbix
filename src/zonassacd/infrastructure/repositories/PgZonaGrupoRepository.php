<?php

namespace src\zonassacd\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\Set;
use PDO;
use src\shared\traits\HandlesPdoErrors;
use src\zonassacd\domain\contracts\ZonaGrupoRepositoryInterface;
use src\zonassacd\domain\entity\ZonaGrupo;


/**
 * Clase que adapta la tabla zonas_grupos a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 24/12/2025
 */
class PgZonaGrupoRepository extends ClaseRepository implements ZonaGrupoRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $oDbl = $GLOBALS['oDBE'];
        $this->setoDbl($oDbl);
        $oDbl_Select = $GLOBALS['oDBE_Select'];
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('zonas_grupos');
    }

    /**
     * retorna un array
     * Els posibles grups de zones
     *
     * @param string optional $sCondicion Condició de búsqueda (amb el WHERE).
     * @return array|false
     */
    public function getArrayZonaGrupos(string $sCondicion = ''): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_grupo, nombre_grupo
				FROM $nom_tabla
				$sCondicion
				ORDER BY orden";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);

        $aOpciones = [];
        while ($aRow = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $aOpciones[$aRow['id_grupo']] = $aRow['nombre_grupo'];
        }
        return $aOpciones;
    }


    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo ZonaGrupo
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo ZonaGrupo
     */
    public function getZonasGrupo(array $aWhere = [], array $aOperators = []): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $ZonaGrupoSet = new Set();
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
            $ZonaGrupo = ZonaGrupo::fromArray($aDatos);
            $ZonaGrupoSet->add($ZonaGrupo);
        }
        return $ZonaGrupoSet->getTot();
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(ZonaGrupo $ZonaGrupo): bool
    {
        $id_grupo = $ZonaGrupo->getId_grupo();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_grupo = $id_grupo";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(ZonaGrupo $ZonaGrupo): bool
    {
        $id_grupo = $ZonaGrupo->getId_grupo();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_grupo);

        $aDatos = $ZonaGrupo->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            $update = "
					nombre_grupo             = :nombre_grupo,
					orden                    = :orden";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_grupo = $id_grupo";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            // INSERT
            $aDatos['id_grupo'] = $ZonaGrupo->getId_grupo();
            $campos = "(id_grupo,nombre_grupo,orden)";
            $valores = "(:id_grupo,:nombre_grupo,:orden)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_grupo): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_grupo = $id_grupo";
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
     * @param int $id_grupo
     * @return array|bool
     */
    public function datosById(int $id_grupo): array|bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_grupo = $id_grupo";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        return $aDatos;
    }


    /**
     * Busca la clase con id_grupo en la base de datos .
     */
    public function findById(int $id_grupo): ?ZonaGrupo
    {
        $aDatos = $this->datosById($id_grupo);
        if (empty($aDatos)) {
            return null;
        }
        return ZonaGrupo::fromArray($aDatos);
    }

    public function getNewId()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('zonas_grupos_id_grupo_seq'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
    }
}