<?php

namespace src\inventario\infrastructure\persistence\postgresql;
use src\shared\infrastructure\GlobalPdo;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\inventario\domain\criteria\TipoDocField;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\shared\traits\RunsCriteriaQueries;
use PDO;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\entity\TipoDoc;
use src\shared\traits\HandlesPdoErrors;

/**
 * Clase que adapta la tabla i_tipo_documento_dl a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/3/2025
 */
class PgTipoDocRepository extends ClaseRepository implements TipoDocRepositoryInterface
{
    use HandlesPdoErrors;
    use RunsCriteriaQueries;

    /** Campo (valor del enum) => columna, solo si difieren: aquí coinciden todos. */
    private const COLUMNAS = [];

    public function __construct()
    {
        $oDbl = GlobalPdo::get('oDB');
        $this->setoDbl($oDbl);
        $this->setoDbl_select($oDbl);
        $this->setNomTabla('i_tipo_documento_dl');
    }

    public function getArrayTipoDoc(): array
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_tipo_doc, CASE WHEN nom_doc IS NOT NULL THEN sigla ||' ('||nom_doc||')'
            ELSE sigla
       		END
	   		FROM $nom_tabla
			WHERE vigente = 't'
			ORDER BY sigla,nom_doc ";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }
        $aOpciones = [];
        foreach ($stmt as $aClave) {
            if (!is_array($aClave)) {
                continue;
            }
            $clave = $aClave[0];
            $val = $aClave[1];
            if ((!is_int($clave) && !is_string($clave)) || (!is_scalar($val) && $val !== null)) {
                continue;
            }
            $aOpciones[(int) $clave] = (string) $val;
        }
        return $aOpciones;
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * TipoDoc que cumplen el criterio.
     *
     * @return list<TipoDoc>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(TipoDocField::class, self::COLUMNAS);
        return array_map(TipoDoc::fromArray(...), $this->rowsMatching($criteria, $conv));
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(TipoDoc $TipoDoc): bool
    {
        $id_tipo_doc = $TipoDoc->getIdTipoDocVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_tipo_doc = $id_tipo_doc";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(TipoDoc $TipoDoc): bool
    {
        $id_tipo_doc = $TipoDoc->getIdTipoDocVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_tipo_doc);

        $aDatos = $TipoDoc->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_tipo_doc']);
            $update = "
                    nom_doc                  = :nom_doc,
                    sigla                    = :sigla,
                    observ                   = :observ,
                    id_coleccion             = :id_coleccion,
                    bajo_llave               = :bajo_llave,
                    vigente                  = :vigente,
                    numerado                 = :numerado";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_tipo_doc = $id_tipo_doc";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            //INSERT
            $campos = "(id_tipo_doc,nom_doc,sigla,observ,id_coleccion,bajo_llave,vigente,numerado)";
            $valores = "(:id_tipo_doc,:nom_doc,:sigla,:observ,:id_coleccion,:bajo_llave,:vigente,:numerado)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }
    }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_tipo_doc): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_tipo_doc = $id_tipo_doc";
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
     * @param int $id_tipo_doc
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_tipo_doc): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_tipo_doc = $id_tipo_doc";
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
     * Busca la clase con id_tipo_doc en la base de datos .
     */
    public function findById(int $id_tipo_doc): ?TipoDoc
    {
        $aDatos = $this->datosById($id_tipo_doc);
        if ($aDatos === false) {
            return null;
        }
        return TipoDoc::fromArray($aDatos);
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('i_tipo_documento_dl_id_tipo_doc_seq'::regclass)";
        $stmt = $oDbl->query($sQuery);
        if ($stmt === false) {
            return 0;
        }
        $id = $stmt->fetchColumn();

        return is_numeric($id) ? (int) $id : 0;
    }
}