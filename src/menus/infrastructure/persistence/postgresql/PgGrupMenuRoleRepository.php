<?php

namespace src\menus\infrastructure\persistence\postgresql;

use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ClaseRepository;
use PDO;
use src\menus\domain\contracts\GrupMenuRoleRepositoryInterface;
use src\menus\domain\criteria\GrupMenuRoleField;
use src\menus\domain\entity\GrupMenuRole;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\shared\traits\HandlesPdoErrors;
use src\shared\traits\RunsCriteriaQueries;

/**
 * Clase que adapta la tabla aux_grupmenu_rol a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 24/4/2025
 */
class PgGrupMenuRoleRepository extends ClaseRepository implements GrupMenuRoleRepositoryInterface
{
    use HandlesPdoErrors;
    use RunsCriteriaQueries;

    /** Campo (valor del enum) => columna, solo si difieren: aquí coinciden todos. */
    private const COLUMNAS = [];

    public function __construct()
    {
        $oDbl = GlobalPdo::get('oDBE');
        $this->setoDbl($oDbl);
        $oDbl_Select = GlobalPdo::get('oDBE_Select');
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('aux_grupmenu_rol');
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * Relaciones grupo de menú - role que cumplen el criterio.
     *
     * @return list<GrupMenuRole>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(GrupMenuRoleField::class, self::COLUMNAS);
        $items = [];
        foreach ($this->rowsMatching($criteria, $conv) as $aDatos) {
            $items[] = GrupMenuRole::fromArray($aDatos);
        }

        return $items;
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(GrupMenuRole $GrupMenuRole): bool
    {
        $id_item = $GrupMenuRole->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_item = $id_item";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(GrupMenuRole $GrupMenuRole): bool
    {
        $id_item = $GrupMenuRole->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_item);

        $aDatos = $GrupMenuRole->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_item']);
            $update = "
					id_grupmenu              = :id_grupmenu,
					id_role                  = :id_role";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_item = $id_item";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        } else {
            //INSERT
            $campos = "(id_item,id_grupmenu,id_role)";
            $valores = "(:id_item,:id_grupmenu,:id_role)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_item): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_item = $id_item";
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
     * @param int $id_item
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_item): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_item = $id_item";
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
     * Busca la clase con id_item en la base de datos .
     */
    public function findById(int $id_item): ?GrupMenuRole
    {
        $aDatos = $this->datosById($id_item);
        if ($aDatos === false) {
            return null;
        }
        return GrupMenuRole::fromArray($aDatos);
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('aux_grupmenu_rol_id_item_seq'::regclass)";
        $stmt = $oDbl->query($sQuery);
        if ($stmt === false) {
            return 0;
        }
        $id = $stmt->fetchColumn();

        return is_numeric($id) ? (int) $id : 0;
    }
}