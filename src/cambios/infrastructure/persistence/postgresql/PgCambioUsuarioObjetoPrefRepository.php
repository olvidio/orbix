<?php

namespace src\cambios\infrastructure\persistence\postgresql;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\cambios\domain\criteria\CambioUsuarioObjetoPrefField;
use PDO;
use src\cambios\domain\contracts\CambioUsuarioObjetoPrefRepositoryInterface;
use src\cambios\domain\entity\CambioUsuarioObjetoPref;
use src\shared\infrastructure\GlobalPdo;
use src\shared\traits\HandlesPdoErrors;
use src\shared\traits\RunsCriteriaQueries;

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
    use RunsCriteriaQueries;

    /** Campo (valor del enum) => columna, solo si difieren: aquí coinciden todos. */
    private const COLUMNAS = [];

    public function __construct()
    {
        $this->setoDbl(GlobalPdo::get('oDBE'));
        $this->setoDbl_select(GlobalPdo::get('oDBE_Select'));
        $this->setNomTabla('av_cambios_usuario_objeto_pref');
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * CambioUsuarioObjetoPref que cumplen el criterio.
     *
     * @return list<CambioUsuarioObjetoPref>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(CambioUsuarioObjetoPrefField::class, self::COLUMNAS);
        $result = [];
        foreach ($this->rowsMatching($criteria, $conv) as $aDatos) {
            $result[] = CambioUsuarioObjetoPref::fromArray($this->normalizeAssocRow($aDatos));
        }

        return $result;
    }

    /**
     * @return list<CambioUsuarioObjetoPref>
     */
    public function findPorTipoActividad(string $objeto, string $dlOrg, string $idTipoActiv): array
    {
        $nom_tabla = $this->getNomTabla();
        $sQry = "SELECT * FROM $nom_tabla WHERE objeto = :objeto AND dl_org = :dl_org"
            . " AND :id_tipo_activ::text ~ id_tipo_activ_txt"
            . " ORDER BY aviso_tipo,id_usuario,id_tipo_activ_txt DESC";
        $stmt = $this->prepareAndExecute(
            $this->getoDbl_Select(),
            $sQry,
            ['objeto' => $objeto, 'dl_org' => $dlOrg, 'id_tipo_activ' => $idTipoActiv],
            __METHOD__,
            __FILE__,
            __LINE__
        );
        if ($stmt === false) {
            return [];
        }

        $result = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $aDatos) {
            if (!is_array($aDatos)) {
                continue;
            }
            $result[] = CambioUsuarioObjetoPref::fromArray($this->normalizeAssocRow($aDatos));
        }

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