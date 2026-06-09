<?php

namespace src\cambios\infrastructure\persistence\postgresql;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\postgresql\Condicion;
use src\shared\infrastructure\persistence\postgresql\Set;
use PDO;
use src\cambios\domain\contracts\CambioUsuarioObjetoPrefRepositoryInterface;
use src\cambios\domain\entity\CambioUsuarioObjetoPref;
use src\shared\infrastructure\GlobalPdo;
use src\shared\traits\HandlesPdoErrors;
use function src\shared\domain\helpers\is_true;


/**
 * Clase que adapta la tabla av_cambios_usuario_objeto_pref a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 20/12/2025
 */
class PgCambioUsuarioObjetoPrefRepository extends ClaseRepository implements CambioUsuarioObjetoPrefRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $this->setoDbl(GlobalPdo::get('oDBE'));
        $this->setoDbl_select(GlobalPdo::get('oDBE_Select'));
        $this->setNomTabla('av_cambios_usuario_objeto_pref');
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<CambioUsuarioObjetoPref>
     */
    public function getCambioUsuarioObjetoPrefs(array $aWhere = [], array $aOperators = []): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $CambioUsuarioObjetoPrefSet = new Set();
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
            $aDatos = $this->normalizeAssocRow($aDatos);
            $CambioUsuarioObjetoPref = CambioUsuarioObjetoPref::fromArray($aDatos);
            $CambioUsuarioObjetoPrefSet->add($CambioUsuarioObjetoPref);
        }
        /** @var list<CambioUsuarioObjetoPref> $result */
        $result = array_values($CambioUsuarioObjetoPrefSet->getTot());
        return $result;
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(CambioUsuarioObjetoPref $CambioUsuarioObjetoPref): bool
    {
        $id_item_usuario_objeto = $CambioUsuarioObjetoPref->getId_item_usuario_objeto();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_item_usuario_objeto = $id_item_usuario_objeto";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(CambioUsuarioObjetoPref $CambioUsuarioObjetoPref): bool
    {
        $id_item_usuario_objeto = $CambioUsuarioObjetoPref->getId_item_usuario_objeto();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_item_usuario_objeto);

        $aDatos = $CambioUsuarioObjetoPref->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_item_usuario_objeto']);
            $update = "
					id_usuario               = :id_usuario,
					dl_org                   = :dl_org,
					id_tipo_activ_txt        = :id_tipo_activ_txt,
					id_fase_ref              = :id_fase_ref,
					aviso_off                = :aviso_off,
					aviso_on                 = :aviso_on,
					aviso_outdate            = :aviso_outdate,
					objeto                   = :objeto,
					aviso_tipo               = :aviso_tipo,
					csv_id_pau               = :csv_id_pau";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_item_usuario_objeto = $id_item_usuario_objeto";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            // INSERT
            $campos = "(id_item_usuario_objeto,id_usuario,dl_org,id_tipo_activ_txt,id_fase_ref,aviso_off,aviso_on,aviso_outdate,objeto,aviso_tipo,csv_id_pau)";
            $valores = "(:id_item_usuario_objeto,:id_usuario,:dl_org,:id_tipo_activ_txt,:id_fase_ref,:aviso_off,:aviso_on,:aviso_outdate,:objeto,:aviso_tipo,:csv_id_pau)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_item_usuario_objeto): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_item_usuario_objeto = $id_item_usuario_objeto";
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
    public function datosById(int $id_item_usuario_objeto): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_item_usuario_objeto = $id_item_usuario_objeto";
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

    public function findById(int $id_item_usuario_objeto): ?CambioUsuarioObjetoPref
    {
        $aDatos = $this->datosById($id_item_usuario_objeto);
        if ($aDatos === false) {
            return null;
        }
        return CambioUsuarioObjetoPref::fromArray($aDatos);
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('av_cambios_usuario_objeto_pref_id_item_usuario_objeto_seq'::regclass)";
        $stmt = $oDbl->query($sQuery);
        if ($stmt === false) {
            return 0;
        }
        $id = $stmt->fetchColumn();

        return is_numeric($id) ? (int) $id : 0;
    }
}