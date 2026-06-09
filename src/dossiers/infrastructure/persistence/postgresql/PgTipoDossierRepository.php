<?php

namespace src\dossiers\infrastructure\persistence\postgresql;

use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\postgresql\Condicion;
use src\shared\infrastructure\persistence\postgresql\Set;
use PDO;
use src\dossiers\domain\contracts\TipoDossierRepositoryInterface;
use src\dossiers\domain\entity\TipoDossier;
use src\dossiers\domain\value_objects\TipoDossierId;
use src\shared\traits\HandlesPdoErrors;
use function src\shared\domain\helpers\is_true;


/**
 * Clase que adapta la tabla d_tipos_dossiers a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 2/12/2025
 */
class PgTipoDossierRepository extends ClaseRepository implements TipoDossierRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $this->setoDbl(GlobalPdo::get('oDBPC'));
        $this->setoDbl_select(GlobalPdo::get('oDBPC_Select'));
        $this->setNomTabla('d_tipos_dossiers');
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo TipoDossier
     *
     * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
     * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return list<TipoDossier> Una colección de objetos de tipo TipoDossier
     */
    public function getTiposDossiers(array $aWhere = [], array $aOperators = []): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
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
        if (isset($aWhere['_ordre']) && is_scalar($aWhere['_ordre']) && (string) $aWhere['_ordre'] !== '') {
            $sOrdre = ' ORDER BY ' . (string) $aWhere['_ordre'];
        }
        if (isset($aWhere['_ordre'])) {
            unset($aWhere['_ordre']);
        }
        if (isset($aWhere['_limit']) && is_scalar($aWhere['_limit']) && (string) $aWhere['_limit'] !== '') {
            $sLimit = ' LIMIT ' . (string) $aWhere['_limit'];
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
        $tiposDossiers = [];
        foreach ($filas as $aDatos) {
            if (!is_array($aDatos)) {
                continue;
            }
            $normalized = [];
            foreach ($aDatos as $key => $value) {
                $normalized[(string) $key] = $value;
            }
            $tiposDossiers[] = TipoDossier::fromArray($normalized);
        }
        return $tiposDossiers;
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function findByIdVo(TipoDossierId $id): ?TipoDossier
    {
        return $this->findById($id->value());
    }

    public function datosByIdVo(TipoDossierId $id): array|false
    {
        return $this->datosById($id->value());
    }

    public function Eliminar(TipoDossier $TipoDossier): bool
    {
        $id_tipo_dossier = $TipoDossier->getId_tipo_dossier();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_tipo_dossier = $id_tipo_dossier";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(TipoDossier $TipoDossier): bool
    {
        $id_tipo_dossier = $TipoDossier->getId_tipo_dossier();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_tipo_dossier);

        $aDatos = $TipoDossier->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_tipo_dossier']);
            $update = "
					descripcion              = :descripcion,
					tabla_from               = :tabla_from,
					tabla_to                 = :tabla_to,
					campo_to                 = :campo_to,
					id_tipo_dossier_rel      = :id_tipo_dossier_rel,
					permiso_lectura          = :permiso_lectura,
					permiso_escritura        = :permiso_escritura,
					depende_modificar        = :depende_modificar,
					app                      = :app,
					class                    = :class,
					db                       = :db,
					codigo                   = :codigo";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_tipo_dossier = $id_tipo_dossier";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            // INSERT
            $campos = "(id_tipo_dossier,descripcion,tabla_from,tabla_to,campo_to,id_tipo_dossier_rel,permiso_lectura,permiso_escritura,depende_modificar,app,class,db,codigo)";
            $valores = "(:id_tipo_dossier,:descripcion,:tabla_from,:tabla_to,:campo_to,:id_tipo_dossier_rel,:permiso_lectura,:permiso_escritura,:depende_modificar,:app,:class,:db,:codigo)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_tipo_dossier): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_tipo_dossier = $id_tipo_dossier";
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
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_tipo_dossier): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_tipo_dossier = $id_tipo_dossier";
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

    public function findById(int $id_tipo_dossier): ?TipoDossier
    {
        $aDatos = $this->datosById($id_tipo_dossier);
        if (empty($aDatos)) {
            return null;
        }
        return TipoDossier::fromArray($aDatos);
    }

    public function findByCodigo(string $codigo): ?TipoDossier
    {
        $codigo = trim($codigo);
        if ($codigo === '') {
            return null;
        }
        $a = $this->getTiposDossiers(['codigo' => $codigo], ['codigo' => '=']);
        if ($a === []) {
            return null;
        }
        return $a[array_key_first($a)] ?? null;
    }
}