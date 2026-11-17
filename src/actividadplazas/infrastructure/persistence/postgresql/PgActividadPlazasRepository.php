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

    /**
     * La clave de `da_plazas` / `da_plazas_dl` es (id_activ, id_dl, dl_tabla): cada dl tiene su fila
     * en la actividad. Borrar, guardar o buscar solo por id_activ tocaría las filas de todas las dl.
     */
    public function Eliminar(ActividadPlazas $ActividadPlazas): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_activ = :id_activ AND id_dl = :id_dl AND dl_tabla = :dl_tabla";
        return $this->prepareAndExecute($oDbl, $sql, self::clave($ActividadPlazas), __METHOD__, __FILE__, __LINE__) !== false;
    }

    /**
     * Si no existe el registro (por su clave completa), hace un insert; si existe, el update.
     * @throws JsonException
     */
    public function Guardar(ActividadPlazas $ActividadPlazas): bool
    {
        $aClave = self::clave($ActividadPlazas);
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->datosById($aClave['id_activ'], $aClave['id_dl'], $aClave['dl_tabla']) === false;

        $aDatos = $ActividadPlazas->toArrayForDatabase([
            'cedidas' => fn($v) => (new ConverterJson($ActividadPlazas->getArrayCedidas(), false))->toPg(false),
        ]);
        // id_dl es columna de da_plazas/da_plazas_dl; Hydratable::toArrayForDatabase() la omite.
        $aDatos['id_dl'] = $aClave['id_dl'];

        if ($bInsert === false) {
            // La clave no cambia: solo los datos.
            $aDatos = [
                'plazas' => $aDatos['plazas'] ?? null,
                'cl' => $aDatos['cl'] ?? null,
                'cedidas' => $aDatos['cedidas'] ?? null,
            ] + $aClave;
            $sql = "UPDATE $nom_tabla SET plazas = :plazas, cl = :cl, cedidas = :cedidas
                    WHERE id_activ = :id_activ AND id_dl = :id_dl AND dl_tabla = :dl_tabla";
        } else {
            $sql = "INSERT INTO $nom_tabla (id_activ,id_dl,plazas,cl,dl_tabla,cedidas)
                    VALUES (:id_activ,:id_dl,:plazas,:cl,:dl_tabla,:cedidas)";
        }
        $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    /** @return array{id_activ: int, id_dl: int, dl_tabla: string} */
    private static function clave(ActividadPlazas $ActividadPlazas): array
    {
        return [
            'id_activ' => $ActividadPlazas->getId_activ(),
            'id_dl' => $ActividadPlazas->getId_dl(),
            'dl_tabla' => (string) $ActividadPlazas->getDlTablaVo()->value(),
        ];
    }

    /**
     * @return array<string, mixed>|false
     * @throws JsonException
     */
    public function datosById(int $id_activ, int $id_dl, string $dl_tabla): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_activ = :id_activ AND id_dl = :id_dl AND dl_tabla = :dl_tabla";
        $stmt = $this->prepareAndExecute($oDbl, $sql, ['id_activ' => $id_activ, 'id_dl' => $id_dl, 'dl_tabla' => $dl_tabla], __METHOD__, __FILE__, __LINE__);
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
     * La fila de una dl en una actividad.
     * @throws JsonException
     */
    public function findById(int $id_activ, int $id_dl, string $dl_tabla): ?ActividadPlazas
    {
        $aDatos = $this->datosById($id_activ, $id_dl, $dl_tabla);
        if ($aDatos === false) {
            return null;
        }
        return ActividadPlazas::fromArray($aDatos);
    }
}