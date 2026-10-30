<?php

namespace src\ubis\infrastructure\persistence\postgresql;
use src\shared\infrastructure\GlobalPdo;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\ubis\domain\criteria\TipoCentroField;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\shared\traits\RunsCriteriaQueries;
use PDO;
use src\shared\traits\HandlesPdoErrors;
use src\ubis\domain\contracts\TipoCentroRepositoryInterface;
use src\ubis\domain\entity\TipoCentro;

/**
 * Clase que adapta la tabla xu_tipo_ctr a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 14/11/2025
 */
class PgTipoCentroRepository extends ClaseRepository implements TipoCentroRepositoryInterface
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
        $this->setNomTabla('xu_tipo_ctr');
    }

    /**
     * @return array<int|string, string>
     */
    public function getArrayTiposCentro(): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT tipo_ctr, nombre_tipo_ctr
				FROM $nom_tabla
				ORDER BY tipo_ctr";
        $stmt = $this->PdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $aOpciones = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (!is_array($row) || !isset($row['tipo_ctr'], $row['nombre_tipo_ctr'])) {
                continue;
            }
            $aOpciones[(string) $row['tipo_ctr']] = (string) $row['nombre_tipo_ctr'];
        }
        return $aOpciones;
    }

    /**
     * TipoCentro que cumplen el criterio.
     *
     * @return list<TipoCentro>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(TipoCentroField::class, self::COLUMNAS);
        return array_map(TipoCentro::fromArray(...), $this->rowsMatching($criteria, $conv));
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(TipoCentro $TipoCentro): bool
    {
        $tipo_ctr = $TipoCentro->getTipoCtrVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE tipo_ctr = '$tipo_ctr'";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(TipoCentro $TipoCentro): bool
    {
        $tipo_ctr = $TipoCentro->getTipoCtrVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($tipo_ctr);

        $aDatos = $TipoCentro->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['tipo_ctr']);
            $update = "
					nombre_tipo_ctr          = :nombre_tipo_ctr";
            $sql = "UPDATE $nom_tabla SET $update WHERE tipo_ctr = '$tipo_ctr'";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        else {
            //INSERT
            $campos = "(tipo_ctr,nombre_tipo_ctr)";
            $valores = "(:tipo_ctr,:nombre_tipo_ctr)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(string $tipo_ctr): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE tipo_ctr = '$tipo_ctr'";
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
     * @param string $tipo_ctr
     * @return array<string, mixed>|false
     */
    public function datosById(string $tipo_ctr): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE tipo_ctr = '$tipo_ctr'";
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
     * Busca la clase con tipo_ctr en la base de datos .
     */
    public function findById(string $tipo_ctr): ?TipoCentro
    {
        $aDatos = $this->datosById($tipo_ctr);
        if ($aDatos === false) {
            return null;
        }
        return TipoCentro::fromArray($aDatos);
    }

}