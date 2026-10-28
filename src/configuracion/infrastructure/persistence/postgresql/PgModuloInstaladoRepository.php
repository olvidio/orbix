<?php

namespace src\configuracion\infrastructure\persistence\postgresql;
use src\shared\infrastructure\GlobalPdo;

use src\shared\infrastructure\persistence\ClaseRepository;
use PDO;
use src\configuracion\domain\contracts\ModuloInstaladoRepositoryInterface;
use src\configuracion\domain\criteria\ModuloInstaladoField;
use src\configuracion\domain\entity\ModuloInstalado;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\LegacyWhereAdapter;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\shared\traits\HandlesPdoErrors;
use src\shared\traits\RunsCriteriaQueries;

/**
 * Clase que adapta la tabla m0_mods_installed_dl a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 13/11/2025
 */
class PgModuloInstaladoRepository extends ClaseRepository implements ModuloInstaladoRepositoryInterface
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
        $this->setNomTabla('m0_mods_installed_dl');
    }

    public function getArrayModulosInstalados(): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_mod, nom
                FROM $nom_tabla
                ORDER BY nom";
        $stmt = $this->pdoPrepare($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $aOpciones = [];
        foreach ($stmt->fetchAll(PDO::FETCH_NUM) as $aClave) {
            if (!is_array($aClave)) {
                continue;
            }
            $clave = $aClave[0];
            $val = $aClave[1];
            if (!is_scalar($clave) || !is_scalar($val)) {
                continue;
            }
            $aOpciones[(int)$clave] = (string)$val;
        }
        return $aOpciones;
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * Módulos instalados que cumplen el criterio.
     *
     * @return list<ModuloInstalado>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(ModuloInstaladoField::class, self::COLUMNAS);
        $moduloInstalados = [];
        foreach ($this->rowsMatching($criteria, $conv) as $aDatos) {
            $moduloInstalados[] = ModuloInstalado::fromArray($aDatos);
        }

        return $moduloInstalados;
    }

    /**
     * devuelve una colección (array) de objetos de tipo ModuloInstalado
     *
     * @deprecated usar matching(Criteria)
     * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
     * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return list<ModuloInstalado> Una colección de objetos de tipo ModuloInstalado
     */
    public function getModuloInstalados(array $aWhere = [], array $aOperators = []): array
    {
        $adapter = new LegacyWhereAdapter(ModuloInstaladoField::class, self::COLUMNAS);
        return $this->matching($adapter->toCriteria($aWhere, $aOperators));
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(ModuloInstalado $ModuloInstalado): bool
    {
        $id_mod = $ModuloInstalado->getIdModVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_mod = $id_mod";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(ModuloInstalado $ModuloInstalado): bool
    {
        $id_mod = $ModuloInstalado->getIdModVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_mod);

        $aDatos = $ModuloInstalado->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_mod']);
            $update = "
					active                   = :active";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_mod = $id_mod";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            //INSERT
            $campos = "(id_mod,active)";
            $valores = "(:id_mod,:active)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_mod): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_mod = $id_mod";
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
     * @param int $id_mod
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_mod): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_mod = $id_mod";
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
     * Busca la clase con id_mod en la base de datos .
     */
    public function findById(int $id_mod): ?ModuloInstalado
    {
        $aDatos = $this->datosById($id_mod);
        if ($aDatos === false) {
            return null;
        }
        return ModuloInstalado::fromArray($aDatos);
    }

    /**
     * @return never
     */
    public function getNewId()
    {
        throw new \LogicException('ModuloInstalado usa id_mod del catálogo de módulos.');
    }
}