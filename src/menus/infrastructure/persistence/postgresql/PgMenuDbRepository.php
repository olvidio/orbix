<?php

namespace src\menus\infrastructure\persistence\postgresql;

use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ClaseRepository;
use PDO;
use src\menus\domain\contracts\MenuDbRepositoryInterface;
use src\menus\domain\criteria\MenuDbField;
use src\menus\domain\entity\MenuDb;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\shared\traits\HandlesPdoErrors;
use src\shared\traits\RunsCriteriaQueries;

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
    use RunsCriteriaQueries;

    /** Campo (valor del enum) => columna, solo si difieren: aquí coinciden todos. */
    private const COLUMNAS = [];

    public function __construct()
    {
        $oDbl = GlobalPdo::get('oDBE');
        $oDbl_Select = GlobalPdo::get('oDBE_Select');
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('aux_menus');
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * Menús que cumplen el criterio.
     *
     * @return list<MenuDb>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(MenuDbField::class, self::COLUMNAS);
        $items = [];
        foreach ($this->rowsMatching($criteria, $conv) as $aDatos) {
            // para los array del postgres
            if (isset($aDatos['orden']) && is_string($aDatos['orden'])) {
                $aDatos['orden'] = \src\shared\domain\helpers\FuncTablasSupport::arrayPgInteger2php($aDatos['orden']);
            }
            $items[] = MenuDb::fromArray($aDatos);
        }

        return $items;
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
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_menu): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_menu = $id_menu";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return true;
        }
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
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_menu): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_menu = $id_menu";
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
        if (isset($result['orden']) && is_string($result['orden'])) {
            $result['orden'] = \src\shared\domain\helpers\FuncTablasSupport::arrayPgInteger2php($result['orden']);
        }
        return $result;
    }


    /**
     * Busca la clase con id_menu en la base de datos .
     */
    public function findById(int $id_menu): ?MenuDb
    {
        $aDatos = $this->datosById($id_menu);
        if ($aDatos === false) {
            return null;
        }
        return MenuDb::fromArray($aDatos);
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('aux_menus_id_menu_seq'::regclass)";
        $stmt = $oDbl->query($sQuery);
        if ($stmt === false) {
            return 0;
        }
        $id = $stmt->fetchColumn();

        return is_numeric($id) ? (int) $id : 0;
    }
}