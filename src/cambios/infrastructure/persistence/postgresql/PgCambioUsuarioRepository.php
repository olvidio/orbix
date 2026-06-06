<?php

namespace src\cambios\infrastructure\persistence\postgresql;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\postgresql\Condicion;
use src\shared\infrastructure\persistence\ConverterDate;
use src\shared\infrastructure\persistence\postgresql\Set;
use PDO;
use src\cambios\domain\contracts\CambioUsuarioRepositoryInterface;
use src\cambios\domain\entity\CambioUsuario;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\infrastructure\GlobalPdo;
use src\shared\traits\HandlesPdoErrors;


/**
 * Clase que adapta la tabla av_cambios_usuario a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 20/12/2025
 */
class PgCambioUsuarioRepository extends ClaseRepository implements CambioUsuarioRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $this->setoDbl(GlobalPdo::get('oDBC'));
        $this->setoDbl_select(GlobalPdo::get('oDBC_Select'));
        $this->setNomTabla('av_cambios_usuario');
    }

    /**
     * para eliminar avisos masivamente, anteriores a una fecha.
     */
    public function eliminarHastaFecha(DateTimeLocal|string $df_fin): bool
    {
        if (empty($df_fin)) return false;
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $nom_tabla_cambios = 'public.av_cambios';

        $oConverter = new ConverterDate('date', $df_fin);
        $sf_fin = $oConverter->toPg();

        $sql = "DELETE FROM $nom_tabla u USING $nom_tabla_cambios c 
                WHERE u.id_schema_cambio=c.id_schema AND u.id_item_cambio=c.id_item_cambio 
                    AND c.timestamp_cambio < '$sf_fin'
                ";

        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<CambioUsuario>
     */
    public function getCambiosUsuario(array $aWhere = [], array $aOperators = []): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $CambioUsuarioSet = new Set();
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
            $CambioUsuario = CambioUsuario::fromArray($aDatos);
            $CambioUsuarioSet->add($CambioUsuario);
        }
        return array_values($CambioUsuarioSet->getTot());
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(CambioUsuario $CambioUsuario): bool
    {
        $id_item = $CambioUsuario->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_item = $id_item";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(CambioUsuario $CambioUsuario): bool
    {
        $id_item = $CambioUsuario->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_item);

        $aDatos = $CambioUsuario->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_item']);
            $update = "
					id_schema_cambio         = :id_schema_cambio,
					id_item_cambio           = :id_item_cambio,
					id_usuario               = :id_usuario,
					sfsv                     = :sfsv,
					aviso_tipo               = :aviso_tipo,
					avisado                  = :avisado";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_item = $id_item";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            // INSERT
            $campos = "(id_item,id_schema_cambio,id_item_cambio,id_usuario,sfsv,aviso_tipo,avisado)";
            $valores = "(:id_item,:id_schema_cambio,:id_item_cambio,:id_usuario,:sfsv,:aviso_tipo,:avisado)";
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

    public function findById(int $id_item): ?CambioUsuario
    {
        $aDatos = $this->datosById($id_item);
        if ($aDatos === false) {
            return null;
        }
        return CambioUsuario::fromArray($aDatos);
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('av_cambios_usuario_id_item_seq'::regclass)";
        $stmt = $oDbl->query($sQuery);
        if ($stmt === false) {
            return 0;
        }
        $id = $stmt->fetchColumn();

        return is_numeric($id) ? (int) $id : 0;
    }
}