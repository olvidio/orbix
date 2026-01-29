<?php

namespace src\menus\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\Set;
use PDO;
use src\menus\domain\contracts\MenuDbRepositoryInterface;
use src\menus\domain\entity\MenuDb;
use src\shared\traits\HandlesPdoErrors;
use function core\array_pgInteger2php;
use function core\array_php2pg;
use function core\is_true;

/**
 * Clase que adapta la tabla aux_menus a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 15/4/2025
 */
class PgMenuDbRepository extends ClaseRepository implements MenuDbRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $oDbl = $GLOBALS['oDBE'];
        $oDbl_Select = $GLOBALS['oDBE_Select'];
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('aux_menus');
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo MenuDb
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo MenuDb
     */
    public function getMenuDbs(array $aWhere = [], array $aOperators = []): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $MenuDbSet = new Set();
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
            // para los array del postgres
            $aDatos['orden'] = array_pgInteger2php($aDatos['orden']);
            $MenuDb = MenuDb::fromArray($aDatos);
            $MenuDbSet->add($MenuDb);
        }
        return $MenuDbSet->getTot();
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(MenuDb $MenuDb): bool
    {
        $id_menu = $MenuDb->getId_menu();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_menu = $id_menu";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(MenuDb $MenuDb): bool
    {
        $id_menu = $MenuDb->getId_menu();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_menu);

        $aDatos = $MenuDb->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_menu']);
            $update = "
					orden                    = :orden,
					menu                     = :menu,
					parametros               = :parametros,
					id_metamenu              = :id_metamenu,
					menu_perm                = :menu_perm,
					id_grupmenu              = :id_grupmenu,
					ok                       = :ok";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_menu = $id_menu";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            //INSERT
            $campos = "(id_menu,orden,menu,parametros,id_metamenu,menu_perm,id_grupmenu,ok)";
            $valores = "(:id_menu,:orden,:menu,:parametros,:id_metamenu,:menu_perm,:id_grupmenu,:ok)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_menu): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_menu = $id_menu";
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
     * @param int $id_menu
     * @return array|bool
     */
    public function datosById(int $id_menu): array|bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_menu = $id_menu";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        // para los array del postgres
        if ($aDatos !== false) {
            $aDatos['orden'] = array_pgInteger2php($aDatos['orden']);
        }
        return $aDatos;
    }


    /**
     * Busca la clase con id_menu en la base de datos .
     */
    public function findById(int $id_menu): ?MenuDb
    {
        $aDatos = $this->datosById($id_menu);
        if (empty($aDatos)) {
            return null;
        }
        return MenuDb::fromArray($aDatos);
    }

    public function getNewId()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('aux_menus_id_menu_seq'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
    }
}