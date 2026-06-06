<?php

namespace src\cambios\infrastructure\persistence\postgresql;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\postgresql\Condicion;
use src\shared\infrastructure\persistence\postgresql\Set;
use PDO;
use src\cambios\domain\contracts\CambioAnotadoRepositoryInterface;
use src\cambios\domain\entity\CambioAnotado;
use src\shared\infrastructure\GlobalPdo;
use src\shared\traits\HandlesPdoErrors;
use function src\shared\domain\helpers\is_true;


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
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<CambioAnotado>
     */
    public function getCambiosAnotados(array $aWhere = [], array $aOperators = []): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $CambioAnotadoSet = new Set();
        $oCondicion = new Condicion();
        $aCondicion = [];
        foreach ($aWhere as $camp => $val) {
            if ($camp === '_ordre') {
                continue;
            }
            if ($camp === '_limit') {
                continue;
            }
            $sOperador = $aOperators[$camp] ?? '';
            if ($a = $oCondicion->getCondicion($camp, $sOperador, $val)) {
                $aCondicion[] = $a;
            }
            // operadores que no requieren valores
            if ($sOperador === 'BETWEEN' || $sOperador === 'IS NULL' || $sOperador === 'IS NOT NULL' || $sOperador === 'OR') {
                unset($aWhere[$camp]);
            }
            if ($sOperador === 'IN' || $sOperador === 'NOT IN') {
                unset($aWhere[$camp]);
            }
            if ($sOperador === 'TXT') {
                unset($aWhere[$camp]);
            }
        }
        $sCondicion = implode(' AND ', $aCondicion);
        if ($sCondicion !== '') {
            $sCondicion = " WHERE " . $sCondicion;
        }
        $sOrdre = '';
        $sLimit = '';
        $ordreVal = $aWhere['_ordre'] ?? null;
        if (is_string($ordreVal) && $ordreVal !== '') {
            $sOrdre = ' ORDER BY ' . $ordreVal;
        }
        if (isset($aWhere['_ordre'])) {
            unset($aWhere['_ordre']);
        }
        $limitVal = $aWhere['_limit'] ?? null;
        if ((is_string($limitVal) || is_int($limitVal)) && (string) $limitVal !== '') {
            $sLimit = ' LIMIT ' . $limitVal;
        }
        if (isset($aWhere['_limit'])) {
            unset($aWhere['_limit']);
        }
        $sQry = "SELECT * FROM $nom_tabla " . $sCondicion . $sOrdre . $sLimit;
        $stmt = $this->prepareAndExecute($oDbl, $sQry, $aWhere, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as $aDatos) {
            if (!is_array($aDatos)) {
                continue;
            }
            $CambioAnotado = CambioAnotado::fromArray($aDatos);
            $CambioAnotadoSet->add($CambioAnotado);
        }
        return array_values($CambioAnotadoSet->getTot());
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