<?php

namespace src\menus\infrastructure\persistence\postgresql;

use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ClaseRepository;
use PDO;
use src\menus\domain\contracts\MetaMenuRepositoryInterface;
use src\menus\domain\criteria\MetaMenuField;
use src\menus\domain\entity\MetaMenu;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\LegacyWhereAdapter;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\shared\traits\HandlesPdoErrors;
use src\shared\traits\RunsCriteriaQueries;

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
    use RunsCriteriaQueries;

    /** Campo (valor del enum) => columna, solo si difieren: aquí coinciden todos. */
    private const COLUMNAS = [];

    public function __construct()
    {
        $oDbl = GlobalPdo::get('oDBPC');
        $oDbl_Select = GlobalPdo::get('oDBPC_Select');
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('aux_metamenus');
    }

    /**
     * @param list<string> $a_modulos
     * @return array<int|string, string>
     */
    public function getArrayMetaMenus(array $a_modulos = []): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $Where = implode(' AND ', $a_modulos);
        if (!empty($Where)) $Where = "WHERE $Where";
        $sQuery = "SELECT id_metamenu,descripcion FROM $nom_tabla $Where ORDER BY descripcion";
        $stmt = $this->PdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $aOpciones = [];
        foreach ($stmt as $aClave) {
            if (!is_array($aClave)) {
                continue;
            }
            $clave = $aClave[0];
            $val = $aClave[1];
            if ((!is_int($clave) && !is_string($clave)) || (!is_scalar($val) && $val !== null)) {
                continue;
            }
            $aOpciones[(int) $clave] = (string) $val;
        }
        return $aOpciones;
    }
    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * Metamenús que cumplen el criterio.
     *
     * @return list<MetaMenu>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(MetaMenuField::class, self::COLUMNAS);
        $items = [];
        foreach ($this->rowsMatching($criteria, $conv) as $aDatos) {
            $items[] = MetaMenu::fromArray($aDatos);
        }

        return $items;
    }

    /**
     * devuelve una colección (array) de objetos de tipo MetaMenu
     *
     * @deprecated usar matching(Criteria)
     * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
     * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return list<MetaMenu> Una colección de objetos de tipo MetaMenu
     */
    public function getMetaMenus(array $aWhere = [], array $aOperators = []): array
    {
        $adapter = new LegacyWhereAdapter(MetaMenuField::class, self::COLUMNAS);
        return $this->matching($adapter->toCriteria($aWhere, $aOperators));
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
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_metamenu): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_metamenu = $id_metamenu";
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
     * @param int $id_metamenu
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_metamenu): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_metamenu = $id_metamenu";
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
        return $result;

    }

    /**
     * Busca la clase con id_metamenu en la base de datos .
     */
    public function findById(int $id_metamenu): ?MetaMenu
    {
        $aDatos = $this->datosById($id_metamenu);
        if ($aDatos === false) {
            return null;
        }
        return MetaMenu::fromArray($aDatos);
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('metamenus_id_metamenu_seq'::regclass)";
        $stmt = $oDbl->query($sQuery);
        if ($stmt === false) {
            return 0;
        }
        $id = $stmt->fetchColumn();

        return is_numeric($id) ? (int) $id : 0;
    }
}