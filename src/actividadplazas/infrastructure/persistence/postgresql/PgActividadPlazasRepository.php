<?php

namespace src\actividadplazas\infrastructure\persistence\postgresql;

use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ClaseRepository;
use src\actividadplazas\domain\criteria\ActividadPlazasField;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\shared\traits\RunsCriteriaQueries;
use src\shared\infrastructure\persistence\ConverterDate;
use src\shared\infrastructure\persistence\ConverterJson;
use JsonException;
use PDO;
use src\actividadplazas\domain\contracts\ActividadPlazasRepositoryInterface;
use src\actividadplazas\domain\entity\ActividadPlazas;
use src\shared\traits\HandlesPdoErrors;


/**
 * Clase que adapta la tabla da_plazas a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 16/12/2025
 */
class PgActividadPlazasRepository extends ClaseRepository implements ActividadPlazasRepositoryInterface
{
    use HandlesPdoErrors;
    use RunsCriteriaQueries;

    /** Campo (valor del enum) => columna, solo si difieren: aquí coinciden todos. */
    private const COLUMNAS = [];

    public function __construct()
    {
        $oDbl = GlobalPdo::get('oDBP');
        $this->setoDbl($oDbl);
        $this->setoDbl_select($oDbl);
        $this->setNomTabla('da_plazas');
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * ActividadPlazas que cumplen el criterio.
     *
     * @return list<ActividadPlazas>
     * @throws JsonException
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(ActividadPlazasField::class, self::COLUMNAS);
        $items = [];
        foreach ($this->rowsMatching($criteria, $conv) as $aDatos) {
            $cedidasRaw = $aDatos['cedidas'] ?? null;
            $aDatos['cedidas'] = (new ConverterJson(
                is_array($cedidasRaw) || is_string($cedidasRaw) || $cedidasRaw instanceof \stdClass ? $cedidasRaw : null,
                true
            ))->fromPg();
            $items[] = ActividadPlazas::fromArray($aDatos);
        }
        return $items;
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(ActividadPlazas $ActividadPlazas): bool
    {
        $id_activ = $ActividadPlazas->getId_activ();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_activ = $id_activ";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     * @throws JsonException
     */
    public function Guardar(ActividadPlazas $ActividadPlazas): bool
    {
        $id_activ = $ActividadPlazas->getId_activ();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_activ);

        $aDatos = $ActividadPlazas->toArrayForDatabase([
            'cedidas' => fn($v) => (new ConverterJson($ActividadPlazas->getArrayCedidas(), false))->toPg(false),
        ]);
        // id_dl es columna de da_plazas/da_plazas_dl; Hydratable::toArrayForDatabase() la omite.
        $aDatos['id_dl'] = $ActividadPlazas->getId_dl();

        /*
        $aDatos = [];
        $aDatos['id_dl'] = $ActividadPlazas->getId_dl();
        $aDatos['plazas'] = $ActividadPlazas->getPlazasVo()->value();
        $aDatos['cl'] = $ActividadPlazas->getClVo()->value();
        $aDatos['dl_tabla'] = $ActividadPlazas->getDlTablaVo()->value();
        // para los json
        $aDatos['cedidas'] = (new ConverterJson($ActividadPlazas->getArrayCedidas(), false))->toPg(false);
        array_walk($aDatos, [\src\shared\domain\helpers\FuncTablasSupport::class, 'ponerNull']);
        */

        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_activ']);
            $update = "
					id_dl                    = :id_dl,
					plazas                   = :plazas,
					cl                       = :cl,
					dl_tabla                 = :dl_tabla,
					cedidas                  = :cedidas";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_activ = $id_activ";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            // INSERT
            $campos = "(id_activ,id_dl,plazas,cl,dl_tabla,cedidas)";
            $valores = "(:id_activ,:id_dl,:plazas,:cl,:dl_tabla,:cedidas)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_activ): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_activ = $id_activ";
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
     * @throws JsonException
     */
    public function datosById(int $id_activ): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_activ = $id_activ";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }

        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!is_array($aDatos)) {
            return false;
        }
        $cedidasRaw = $aDatos['cedidas'] ?? null;
        $aDatos['cedidas'] = (new ConverterJson(
            is_array($cedidasRaw) || is_string($cedidasRaw) || $cedidasRaw instanceof \stdClass ? $cedidasRaw : null,
            true
        ))->fromPg();
        $result = [];
        foreach ($aDatos as $key => $value) {
            $result[(string)$key] = $value;
        }

        return $result;
    }


    /**
     * Busca la clase con id_activ en la base de datos .
     * @throws JsonException
     */
    public function findById(int $id_activ): ?ActividadPlazas
    {
        $aDatos = $this->datosById($id_activ);
        if ($aDatos === false) {
            return null;
        }
        return ActividadPlazas::fromArray($aDatos);
    }
}