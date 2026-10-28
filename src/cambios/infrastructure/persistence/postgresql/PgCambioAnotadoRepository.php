<?php

namespace src\cambios\infrastructure\persistence\postgresql;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\LegacyWhereAdapter;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\cambios\domain\criteria\CambioAnotadoField;
use PDO;
use src\cambios\domain\contracts\CambioAnotadoRepositoryInterface;
use src\cambios\domain\entity\CambioAnotado;
use src\shared\infrastructure\GlobalPdo;
use src\shared\traits\HandlesPdoErrors;
use src\shared\traits\RunsCriteriaQueries;

/**
 * Clase que adapta la tabla av_cambios_anotados_dl a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 20/12/2025
 */
class PgCambioAnotadoRepository extends ClaseRepository implements CambioAnotadoRepositoryInterface
{
    use HandlesPdoErrors;
    use RunsCriteriaQueries;

    /** Campo (valor del enum) => columna, solo si difieren: aquí coinciden todos. */
    private const COLUMNAS = [];

    public function __construct()
    {
        $this->setoDbl(GlobalPdo::get('oDBC'));
        $this->setoDbl_select(GlobalPdo::get('oDBC_Select'));
    }

    /**
     * Se añade esta funcion para cambiar de tabla. Si se tienen una instalación en
     * la dmz, hay desfases en la sincronización de la tabla y ocasiona algunos problemas.
     * Se tiene una tabla distinta para sv y sf.
     *
     * @param string $ubicacion
     */
    public function setTabla(string $ubicacion): void
    {
        if ($ubicacion === 'sv') {
            $this->setNomTabla('av_cambios_anotados_dl');
        }
        if ($ubicacion === 'sf') {
            $this->setNomTabla('av_cambios_anotados_dl_sf');
        }
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * CambioAnotado que cumplen el criterio.
     *
     * @return list<CambioAnotado>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(CambioAnotadoField::class, self::COLUMNAS);
        $result = [];
        foreach ($this->rowsMatching($criteria, $conv) as $aDatos) {
            $result[] = CambioAnotado::fromArray($this->normalizeAssocRow($aDatos));
        }

        return $result;
    }

    /**
     * @deprecated usar matching(Criteria)
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<CambioAnotado>
     */
    public function getCambiosAnotados(array $aWhere = [], array $aOperators = []): array
    {
        $adapter = new LegacyWhereAdapter(CambioAnotadoField::class, self::COLUMNAS);
        return $this->matching($adapter->toCriteria($aWhere, $aOperators));
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(CambioAnotado $CambioAnotado): bool
    {
        $id_item = $CambioAnotado->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_item = $id_item";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(CambioAnotado $CambioAnotado): bool
    {
        $id_item = $CambioAnotado->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_item);

        $aDatos = $CambioAnotado->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_item']);
            $update = "
					id_schema_cambio         = :id_schema_cambio,
					id_item_cambio           = :id_item_cambio,
					anotado                  = :anotado,
					server                   = :server";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_item = $id_item";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            // INSERT
            $campos = "(id_item,id_schema_cambio,id_item_cambio,anotado,server)";
            $valores = "(:id_item,:id_schema_cambio,:id_item_cambio,:anotado,:server)";
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
            return true;
        }
        return false;
    }

    /**
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

    public function findById(int $id_item): ?CambioAnotado
    {
        $aDatos = $this->datosById($id_item);
        if ($aDatos === false) {
            return null;
        }
        return CambioAnotado::fromArray($aDatos);
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $tabla = $this->getNomTabla();

        if ($tabla === 'av_cambios_anotados_dl_sf') {
            $sQuery = "select nextval('av_cambios_anotados_dl_sf_id_item_seq'::regclass)";
        } else {
            $sQuery = "select nextval('av_cambios_anotados_dl_id_item_seq'::regclass)";
        }
        $stmt = $oDbl->query($sQuery);
        if ($stmt === false) {
            return 0;
        }
        $id = $stmt->fetchColumn();

        return is_numeric($id) ? (int) $id : 0;
    }
}