<?php

namespace src\configuracion\infrastructure\persistence\postgresql;
use src\shared\infrastructure\GlobalPdo;

use src\shared\infrastructure\persistence\ClaseRepository;
use PDO;
use src\configuracion\domain\contracts\AppRepositoryInterface;
use src\configuracion\domain\criteria\AppField;
use src\configuracion\domain\entity\App;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\LegacyWhereAdapter;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\shared\traits\HandlesPdoErrors;
use src\shared\traits\RunsCriteriaQueries;

/**
 * Clase que adapta la tabla m0_apps a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 10/11/2025
 */
class PgAppRepository extends ClaseRepository implements AppRepositoryInterface
{
    use HandlesPdoErrors;
    use RunsCriteriaQueries;

    /** Campo (valor del enum) => columna, solo si difieren: aquí coinciden todos. */
    private const COLUMNAS = [];

    public function __construct()
    {
        $oDbl = GlobalPdo::get('oDBPC');
        $this->setoDbl($oDbl);
        $oDbl_Select = GlobalPdo::get('oDBPC_Select');
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('m0_apps');
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * Apps que cumplen el criterio.
     *
     * @return list<App>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(AppField::class, self::COLUMNAS);
        $apps = [];
        foreach ($this->rowsMatching($criteria, $conv) as $aDatos) {
            $apps[] = App::fromArray($aDatos);
        }

        return $apps;
    }

    /**
     * devuelve una colección (array) de objetos de tipo App
     *
     * @deprecated usar matching(Criteria)
     * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
     * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return list<App> Una colección de objetos de tipo App
     */
    public function getApps(array $aWhere = [], array $aOperators = []): array
    {
        $adapter = new LegacyWhereAdapter(AppField::class, self::COLUMNAS);
        return $this->matching($adapter->toCriteria($aWhere, $aOperators));
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(App $App): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $id_app = $App->getIdAppVo()->value();

        $sql = "DELETE FROM $nom_tabla WHERE id_app=$id_app";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    public function Guardar(App $App): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();

        $id_app = $App->getIdAppVo()->value();
        $nom = $App->getNomVo()->value();
        $bInsert = $this->isNew($id_app);

        $aDatos = $App->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_app']);
            $update = " nom=:nom";
            $sql = "UPDATE $nom_tabla SET  $update WHERE id_app=$id_app";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            $campos = '(id_app,nom)';
            $valores = '(:id_app,:nom)';
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_app): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_app = $id_app";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return true;
        }
        if (!$stmt->rowCount()) {
            return TRUE;
        }
        return false;
    }

    public function datosById(int $id_app): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT * FROM $nom_tabla WHERE id_app = $id_app";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
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

    public function findById(int $id_app): ?App
    {
        $aDatos = $this->datosById($id_app);
        if (!is_array($aDatos)) {
            return null;
        }
        return App::fromArray($aDatos);
    }

    /**
     * @return int|string
     */
    public function getNewId()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('m0_apps_id_app_seq'::regclass)";
        $queryResult = $oDbl->query($sQuery);
        if ($queryResult === false) {
            return 0;
        }
        $result = $queryResult->fetchColumn();
        if ($result === false || $result === null) {
            return 0;
        }

        return $result;
    }
}
