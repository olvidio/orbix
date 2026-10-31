<?php

namespace src\ubis\infrastructure\persistence\postgresql;
use src\shared\infrastructure\GlobalPdo;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\ubis\domain\criteria\TipoCasaField;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\shared\traits\RunsCriteriaQueries;
use PDO;
use src\shared\traits\HandlesPdoErrors;
use src\ubis\domain\contracts\TipoCasaRepositoryInterface;
use src\ubis\domain\entity\TipoCasa;

/**
 * Clase que adapta la tabla xu_tipo_casa a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 14/11/2025
 */
class PgTipoCasaRepository extends ClaseRepository implements TipoCasaRepositoryInterface
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
        $this->setNomTabla('xu_tipo_casa');
    }

    /**
     * @return array<int|string, string>
     */
public function getArrayTiposCasa(): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT tipo_casa, nombre_tipo_casa
				FROM $nom_tabla
				ORDER BY tipo_casa";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);        if ($stmt === false) {
            return [];
        }

        $aOpciones = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (!is_array($row) || !isset($row['tipo_casa'], $row['nombre_tipo_casa'])) {
                continue;
            }
            $aOpciones[(string) $row['tipo_casa']] = (string) $row['nombre_tipo_casa'];
        }
        return $aOpciones;
    }

    /**
     * TipoCasa que cumplen el criterio.
     *
     * @return list<TipoCasa>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(TipoCasaField::class, self::COLUMNAS);
        return array_map(TipoCasa::fromArray(...), $this->rowsMatching($criteria, $conv));
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(TipoCasa $TipoCasa): bool
    {
        $tipo_casa = $TipoCasa->getTipoCasaVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE tipo_casa = '$tipo_casa'";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(TipoCasa $TipoCasa): bool
    {
        $tipo_casa = $TipoCasa->getTipoCasaVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($tipo_casa);

        $aDatos = $TipoCasa->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['tipo_casa']);
            $update = "
					nombre_tipo_casa         = :nombre_tipo_casa";
            $sql = "UPDATE $nom_tabla SET $update WHERE tipo_casa = '$tipo_casa'";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            //INSERT
            $campos = "(tipo_casa,nombre_tipo_casa)";
            $valores = "(:tipo_casa,:nombre_tipo_casa)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(string $tipo_casa): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE tipo_casa = '$tipo_casa'";
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
     * @param string $tipo_casa
     * @return array<string, mixed>|false
     */
    public function datosById(string $tipo_casa): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE tipo_casa = '$tipo_casa'";
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
     * Busca la clase con tipo_casa en la base de datos .
     */
    public function findById(string $tipo_casa): ?TipoCasa
    {
        $aDatos = $this->datosById($tipo_casa);
        if ($aDatos === false) {
            return null;
        }
        return TipoCasa::fromArray($aDatos);
    }
}