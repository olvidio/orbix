<?php

namespace src\menus\infrastructure\persistence\postgresql;

use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ClaseRepository;
use PDO;
use PDOException;
use src\menus\domain\contracts\GrupMenuRepositoryInterface;
use src\menus\domain\criteria\GrupMenuField;
use src\menus\domain\entity\GrupMenu;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\shared\traits\HandlesPdoErrors;
use src\shared\traits\RunsCriteriaQueries;

/**
 * Clase que adapta la tabla aux_grupmenu a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 15/4/2025
 */
class PgGrupMenuRepository extends ClaseRepository implements GrupMenuRepositoryInterface
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
        $this->setNomTabla('aux_grupmenu');
    }

    public function getArrayGrupMenus(): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_grupmenu,grup_menu FROM $nom_tabla ORDER BY orden,grup_menu";
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
     * Grupos de menú que cumplen el criterio.
     *
     * @return list<GrupMenu>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(GrupMenuField::class, self::COLUMNAS);
        $items = [];
        foreach ($this->rowsMatching($criteria, $conv) as $aDatos) {
            $items[] = GrupMenu::fromArray($aDatos);
        }

        return $items;
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(GrupMenu $GrupMenu): bool
    {
        $id_grupmenu = $GrupMenu->getId_grupmenu();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_grupmenu = $id_grupmenu";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(GrupMenu $GrupMenu): bool
    {
        $id_grupmenu = $GrupMenu->getId_grupmenu();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_grupmenu);

        $aDatos = $GrupMenu->toArrayForDatabase();
        unset($aDatos['aEquivalencias']);
        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_grupmenu']);
            $update = "
					grup_menu                = :grup_menu,
					orden                    = :orden";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_grupmenu = $id_grupmenu";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            //INSERT
            $campos = "(id_grupmenu,grup_menu,orden)";
            $valores = "(:id_grupmenu,:grup_menu,:orden)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_grupmenu): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_grupmenu = $id_grupmenu";
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
     * @param int $id_grupmenu
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_grupmenu): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_grupmenu = $id_grupmenu";
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
     * Busca la clase con id_grupmenu en la base de datos .
     */
    public function findById(int $id_grupmenu): ?GrupMenu
    {
        $aDatos = $this->datosById($id_grupmenu);
        if ($aDatos === false) {
            return null;
        }
        return GrupMenu::fromArray($aDatos);
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('aux_grupmenu_id_grupmenu_seq'::regclass)";
        $stmt = $oDbl->query($sQuery);
        if ($stmt === false) {
            return 0;
        }
        $id = $stmt->fetchColumn();

        return is_numeric($id) ? (int) $id : 0;
    }
}