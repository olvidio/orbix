<?php

namespace src\menus\infrastructure\persistence\postgresql;

use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ClaseRepository;
use PDO;
use src\menus\domain\contracts\TemplateMenuRepositoryInterface;
use src\menus\domain\criteria\TemplateMenuField;
use src\menus\domain\entity\TemplateMenu;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\LegacyWhereAdapter;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\shared\traits\HandlesPdoErrors;
use src\shared\traits\RunsCriteriaQueries;

/**
 * Clase que adapta la tabla aux_templates_menus a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 15/4/2025
 */
class PgTemplateMenuRepository extends ClaseRepository implements TemplateMenuRepositoryInterface
{
    use HandlesPdoErrors;
    use RunsCriteriaQueries;

    /** Campo (valor del enum) => columna, solo si difieren: aquí coinciden todos. */
    private const COLUMNAS = [];

    public function __construct()
    {
        $oDbl = GlobalPdo::get('oDBPC');
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl);
        $this->setNomTabla('aux_templates_menus');
    }

    public function getArrayTemplates(): array
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_template_menu,nombre FROM $nom_tabla ORDER BY nombre";
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
     * Plantillas de menú que cumplen el criterio.
     *
     * @return list<TemplateMenu>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(TemplateMenuField::class, self::COLUMNAS);
        $items = [];
        foreach ($this->rowsMatching($criteria, $conv) as $aDatos) {
            $items[] = TemplateMenu::fromArray($aDatos);
        }

        return $items;
    }

    /**
     * devuelve una colección (array) de objetos de tipo TemplateMenu
     *
     * @deprecated usar matching(Criteria)
     * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
     * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return list<TemplateMenu> Una colección de objetos de tipo TemplateMenu
     */
    public function getTemplatesMenus(array $aWhere = [], array $aOperators = []): array
    {
        $adapter = new LegacyWhereAdapter(TemplateMenuField::class, self::COLUMNAS);
        return $this->matching($adapter->toCriteria($aWhere, $aOperators));
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(TemplateMenu $TemplateMenu): bool
    {
        $id_template_menu = $TemplateMenu->getId_template_menu();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_template_menu = $id_template_menu";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(TemplateMenu $TemplateMenu): bool
    {
        $id_template_menu = $TemplateMenu->getId_template_menu();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_template_menu);

        $aDatos = $TemplateMenu->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_template_menu']);
            $update = "
					nombre                   = :nombre";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_template_menu = $id_template_menu";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        } else {
            //INSERT
            $campos = "(id_template_menu,nombre)";
            $valores = "(:id_template_menu,:nombre)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_template_menu): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_template_menu = $id_template_menu";
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
     * @param int $id_template_menu
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_template_menu): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_template_menu = $id_template_menu";
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
     * Busca la clase con id_template_menu en la base de datos .
     */
    public function findById(int $id_template_menu): ?TemplateMenu
    {
        $aDatos = $this->datosById($id_template_menu);
        if ($aDatos === false) {
            return null;
        }
        return TemplateMenu::fromArray($aDatos);
    }

    public function findByName(string $nombre): ?TemplateMenu
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE nombre = '$nombre'";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return null;
        }
        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!is_array($aDatos)) {
            return null;
        }
        $normalized = [];
        foreach ($aDatos as $key => $value) {
            $normalized[(string) $key] = $value;
        }
        return TemplateMenu::fromArray($normalized);
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('aux_templates_menus_id_template_menu_seq'::regclass)";
        $stmt = $oDbl->query($sQuery);
        if ($stmt === false) {
            return 0;
        }
        $id = $stmt->fetchColumn();

        return is_numeric($id) ? (int) $id : 0;
    }
}