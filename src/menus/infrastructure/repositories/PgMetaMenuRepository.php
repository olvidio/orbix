<?php

namespace src\menus\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\Set;
use PDO;
use src\menus\domain\contracts\MetaMenuRepositoryInterface;
use src\menus\domain\entity\MetaMenu;
use src\shared\traits\HandlesPdoErrors;

/**
 * Clase que adapta la tabla aux_metamenus a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 15/4/2025
 */
class PgMetaMenuRepository extends ClaseRepository implements MetaMenuRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $oDbl = $GLOBALS['oDBPC'];
        $oDbl_Select = $GLOBALS['oDBPC_Select'];
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('aux_metamenus');
    }

    function getArrayMetaMenus(array $a_modulos = []): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $Where = implode(' AND ', $a_modulos);
        if (!empty($Where)) $Where = "WHERE $Where";
        $sQuery = "SELECT id_metamenu,descripcion FROM $nom_tabla $Where ORDER BY descripcion";
        $stmt = $this->PdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);

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
     * devuelve una colección (array) de objetos de tipo MetaMenu
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo MetaMenu
     */
    public function getMetaMenus(array $aWhere = [], array $aOperators = []): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $MetaMenuSet = new Set();
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
            $MetaMenu = MetaMenu::fromArray($aDatos);
            $MetaMenuSet->add($MetaMenu);
        }
        return $MetaMenuSet->getTot();
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(MetaMenu $MetaMenu): bool
    {
        $id_metamenu = $MetaMenu->getId_metamenu();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_metamenu = $id_metamenu";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(MetaMenu $MetaMenu): bool
    {
        $id_metamenu = $MetaMenu->getId_metamenu();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_metamenu);

        $aDatos = $MetaMenu->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_metamenu']);
            $update = "
					id_mod                   = :id_mod,
					url                      = :url,
					parametros               = :parametros,
					descripcion              = :descripcion";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_metamenu = $id_metamenu";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        } else {
            //INSERT
            $campos = "(id_metamenu,id_mod,url,parametros,descripcion)";
            $valores = "(:id_metamenu,:id_mod,:url,:parametros,:descripcion)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_metamenu): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_metamenu = $id_metamenu";
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
     * @param int $id_metamenu
     * @return array|bool
     */
    public function datosById(int $id_metamenu): array|bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_metamenu = $id_metamenu";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        return $stmt->fetch(PDO::FETCH_ASSOC);

    }

    /**
     * Busca la clase con id_metamenu en la base de datos .
     */
    public function findById(int $id_metamenu): ?MetaMenu
    {
        $aDatos = $this->datosById($id_metamenu);
        if (empty($aDatos)) {
            return null;
        }
        return MetaMenu::fromArray($aDatos);
    }

    public function getNewId()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('metamenus_id_metamenu_seq'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
    }
}