<?php

namespace src\actividadplazas\infrastructure\persistence\postgresql;

use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ClaseRepository;
use src\actividadplazas\domain\criteria\PlazaPeticionField;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\LegacyWhereAdapter;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\shared\traits\RunsCriteriaQueries;
use PDO;
use src\actividadplazas\domain\contracts\PlazaPeticionRepositoryInterface;
use src\actividadplazas\domain\entity\PlazaPeticion;
use src\actividadplazas\domain\value_objects\PlazaPeticionPk;
use src\shared\traits\HandlesPdoErrors;


/**
 * Clase que adapta la tabla dap_plazas_peticion_dl a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 29/12/2025
 */
class PgPlazaPeticionRepository extends ClaseRepository implements PlazaPeticionRepositoryInterface
{
    use HandlesPdoErrors;
    use RunsCriteriaQueries;

    /** Campo (valor del enum) => columna, solo si difieren: aquí coinciden todos. */
    private const COLUMNAS = [];

    public function __construct()
    {
        $oDbl = GlobalPdo::get('oDB');
        $this->setoDbl($oDbl);
        $this->setoDbl_select($oDbl);
        $this->setNomTabla('dap_plazas_peticion_dl');
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * PlazaPeticion que cumplen el criterio.
     *
     * @return list<PlazaPeticion>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(PlazaPeticionField::class, self::COLUMNAS);
        return array_map(PlazaPeticion::fromArray(...), $this->rowsMatching($criteria, $conv));
    }

    /**
     * @deprecated usar matching(Criteria)
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<PlazaPeticion>
     */
    public function getPlazasPeticion(array $aWhere = [], array $aOperators = []): array
    {
        $adapter = new LegacyWhereAdapter(PlazaPeticionField::class, self::COLUMNAS);
        return $this->matching($adapter->toCriteria($aWhere, $aOperators));
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(PlazaPeticion $PlazaPeticion): bool
    {
        $id_nom = $PlazaPeticion->getId_nom();
        $id_activ = $PlazaPeticion->getId_activ();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_nom=$id_nom AND id_activ=$id_activ";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(PlazaPeticion $PlazaPeticion): bool
    {
        $id_nom = $PlazaPeticion->getId_nom();
        $id_activ = $PlazaPeticion->getId_activ();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_nom, $id_activ);

        $aDatos = $PlazaPeticion->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_nom']);
            unset($aDatos['id_activ']);
            $update = "
					orden                    = :orden,
					tipo                     = :tipo";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_nom=$id_nom AND id_activ=$id_activ";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            // INSERT
            $campos = "(id_nom,id_activ,orden,tipo)";
            $valores = "(:id_nom,:id_activ,:orden,:tipo)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_nom, int $id_activ): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_nom=$id_nom AND id_activ=$id_activ";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return true;
        }
        if (!$stmt->rowCount()) {
            return true;
        }
        return false;
    }

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_nom, int $id_activ): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_nom=$id_nom AND id_activ=$id_activ";
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
            $result[(string)$key] = $value;
        }

        return $result;
    }

    public function datosByPk(PlazaPeticionPk $pk): array|false
    {
        return $this->datosById($pk->idNom(), $pk->idActiv());
    }

    /**
     * Busca la clase con id_nom en la base de datos .
     */
    public function findById(int $id_nom, int $id_activ): ?PlazaPeticion
    {
        $aDatos = $this->datosById($id_nom, $id_activ);
        if ($aDatos === false) {
            return null;
        }
        return PlazaPeticion::fromArray($aDatos);
    }

    public function findByPk(PlazaPeticionPk $pk): ?PlazaPeticion
    {
        return $this->findById($pk->idNom(), $pk->idActiv());
    }
}