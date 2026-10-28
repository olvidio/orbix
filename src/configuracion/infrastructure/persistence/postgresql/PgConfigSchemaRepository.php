<?php

namespace src\configuracion\infrastructure\persistence\postgresql;
use src\shared\infrastructure\GlobalPdo;

use src\shared\infrastructure\persistence\ClaseRepository;
use PDO;
use src\configuracion\domain\contracts\ConfigSchemaRepositoryInterface;
use src\configuracion\domain\criteria\ConfigSchemaField;
use src\configuracion\domain\entity\ConfigSchema;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\LegacyWhereAdapter;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\shared\traits\HandlesPdoErrors;
use src\shared\traits\RunsCriteriaQueries;

/**
 * Clase que adapta la tabla x_config_schema a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/11/2025
 */
class PgConfigSchemaRepository extends ClaseRepository implements ConfigSchemaRepositoryInterface
{
    use HandlesPdoErrors;
    use RunsCriteriaQueries;

    /** Campo (valor del enum) => columna, solo si difieren: aquí coinciden todos. */
    private const COLUMNAS = [];

    public function __construct()
    {
        try {
            $oDbl = GlobalPdo::get('oDBC');
            $oDbl_Select = GlobalPdo::get('oDBC_Select');
        } catch (\RuntimeException) {
            $oDbl = GlobalPdo::get('oDBPC');
            $oDbl_Select = GlobalPdo::get('oDBPC_Select');
        }
        $this->setoDbl($oDbl);
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('x_config_schema');
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * Parámetros de configuración del esquema que cumplen el criterio.
     *
     * @return list<ConfigSchema>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(ConfigSchemaField::class, self::COLUMNAS);
        $configSchemas = [];
        foreach ($this->rowsMatching($criteria, $conv) as $aDatos) {
            $configSchemas[] = ConfigSchema::fromArray($aDatos);
        }

        return $configSchemas;
    }

    /**
     * devuelve una colección (array) de objetos de tipo ConfigSchema
     *
     * @deprecated usar matching(Criteria)
     * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
     * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return list<ConfigSchema> Una colección de objetos de tipo ConfigSchema
     */
    public function getConfigSchemas(array $aWhere = [], array $aOperators = []): array
    {
        $adapter = new LegacyWhereAdapter(ConfigSchemaField::class, self::COLUMNAS);
        return $this->matching($adapter->toCriteria($aWhere, $aOperators));
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(ConfigSchema $ConfigSchema): bool
    {
        $parametro = $ConfigSchema->getParametroVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE parametro = '$parametro'";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(ConfigSchema $ConfigSchema): bool
    {
        $parametro = $ConfigSchema->getParametroVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($parametro);

        $aDatos = $ConfigSchema->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['parametro']);
            $update = "
					valor                    = :valor";
            $sql = "UPDATE $nom_tabla SET $update WHERE parametro = '$parametro'";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            //INSERT
            $campos = "(parametro,valor)";
            $valores = "(:parametro,:valor)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(string $parametro): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE parametro = '$parametro'";
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
     * @param string $parametro
     * @return array<string, mixed>|false
     */
    public function datosById(string $parametro): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE parametro = '$parametro'";
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
     * Busca la clase con parametro en la base de datos .
     */
    public function findById(string $parametro): ?ConfigSchema
    {
        $aDatos = $this->datosById($parametro);
        if ($aDatos === false) {
            return null;
        }
        return ConfigSchema::fromArray($aDatos);
    }
}