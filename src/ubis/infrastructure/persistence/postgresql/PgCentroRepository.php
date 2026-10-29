<?php

namespace src\ubis\infrastructure\persistence\postgresql;
use src\shared\infrastructure\GlobalPdo;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\ubis\domain\criteria\CentroField;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\LegacyWhereAdapter;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\shared\traits\RunsCriteriaQueries;
use src\shared\infrastructure\persistence\ConverterDate;
use PDO;
use src\shared\traits\HandlesPdoErrors;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\entity\Centro;

/**
 * Clase que adapta la tabla u_centros a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 20/11/2025
 */
class PgCentroRepository extends ClaseRepository implements CentroRepositoryInterface
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
        $this->setNomTabla('u_centros');
    }

    /**
     * @return array<int|string, string>
     */
public function getArrayCentrosCdc(string $condicion = ''): array
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        //$orden = 'nombre_ubi';

        $sWhere = "WHERE active = 't' AND cdc='t' ";
        if (!empty($condicion)) {
            $sWhere .= 'AND ' . $condicion;
        }
        $sQuery = "SELECT id_ubi, nombre_ubi FROM $nom_tabla $sWhere";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);        if ($stmt === false) {
            return [];
        }

        $a_ctr = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (!is_array($row) || !isset($row['id_ubi'], $row['nombre_ubi'])) {
                continue;
            }
            $id_ubi = $row['id_ubi'];
            $nombre_ubi = $row['nombre_ubi'];

            $a_ctr[$id_ubi] = $nombre_ubi;
        }

        return $a_ctr;
    }

    /**
     * @return array<int|string, string>
     */
public function getArrayCentros(string $condicion = ''): array
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $orden = 'nombre_ubi';
        if (empty($condicion))
            $condicion = "WHERE active = 't'";
        $sQuery = "SELECT id_ubi, nombre_ubi FROM $nom_tabla $condicion ORDER BY $orden";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);        if ($stmt === false) {
            return [];
        }

        $aCentros = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (!is_array($row) || !isset($row['id_ubi'], $row['nombre_ubi'])) {
                continue;
            }
            $id_ubi = $row['id_ubi'];
            $nombre_ubi = $row['nombre_ubi'];

            $aCentros[$id_ubi] = $nombre_ubi;
        }

        return $aCentros;
    }
    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * Centro que cumplen el criterio.
     *
     * @return list<Centro>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(CentroField::class, self::COLUMNAS);
        $items = [];
        foreach ($this->rowsMatching($criteria, $conv) as $aDatos) {
            $aDatos['f_active'] = (new ConverterDate('date', $aDatos['f_active']))->fromPg();
            $items[] = Centro::fromArray($aDatos);
        }
        return $items;
    }

    /**
     * @deprecated usar matching(Criteria)
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<Centro>
     */
    public function getCentros(array $aWhere = [], array $aOperators = []): array
    {
        $adapter = new LegacyWhereAdapter(CentroField::class, self::COLUMNAS);
        return $this->matching($adapter->toCriteria($aWhere, $aOperators));
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Centro $Centro): bool
    {
        $id_ubi = $Centro->getId_ubi();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_ubi = $id_ubi";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(Centro $Centro): bool
    {
        $id_ubi = $Centro->getId_ubi();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_ubi);

        $aDatos = $Centro->toArrayForDatabase([
            'f_active' => fn($v) => (new ConverterDate('date', $v))->toPg(),
        ]);
        // es posible que tenga los parametros de: repoCasaDireccion y repoDIreccion
        unset($aDatos['repoCasaDireccion'], $aDatos['repoDireccion']);

        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_ubi']);
            $update = "
					tipo_ubi                 = :tipo_ubi,
					nombre_ubi               = :nombre_ubi,
					dl                       = :dl,
					pais                     = :pais,
					region                   = :region,
					active                   = :active,
					f_active                 = :f_active,
					sv                       = :sv,
					sf                       = :sf,
					tipo_ctr                 = :tipo_ctr,
					tipo_labor               = :tipo_labor,
					cdc                      = :cdc,
					id_ctr_padre             = :id_ctr_padre";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_ubi = $id_ubi";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        else {
            //INSERT
            $campos = "(tipo_ubi,id_ubi,nombre_ubi,dl,pais,region,active,f_active,sv,sf,tipo_ctr,tipo_labor,cdc,id_ctr_padre)";
            $valores = "(:tipo_ubi,:id_ubi,:nombre_ubi,:dl,:pais,:region,:active,:f_active,:sv,:sf,:tipo_ctr,:tipo_labor,:cdc,:id_ctr_padre)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_ubi): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_ubi = $id_ubi";
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
     * @param int $id_ubi
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_ubi): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_ubi = $id_ubi";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }
        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        // para las fechas del postgres (texto iso)
        if (!is_array($aDatos)) {
            return false;
        }
            $aDatos['f_active'] = (new ConverterDate('date', $aDatos['f_active']))->fromPg();
        $result = [];
        foreach ($aDatos as $key => $value) {
            $result[(string) $key] = $value;
        }
        return $result;
    }

    /**
     * Busca la clase con id_ubi en la base de datos .
     */
    public function findById(int $id_ubi): ?Centro
    {
        $aDatos = $this->datosById($id_ubi);
        if ($aDatos === false) {
            return null;
        }
        return Centro::fromArray($aDatos);
    }
}