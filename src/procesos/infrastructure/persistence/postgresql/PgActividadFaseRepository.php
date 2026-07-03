<?php

namespace src\procesos\infrastructure\persistence\postgresql;

use src\shared\infrastructure\GlobalPdo;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\postgresql\Condicion;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\persistence\postgresql\Set;
use PDO;
use src\permisos\domain\PermDl;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;
use src\procesos\domain\entity\ActividadFase;
use src\shared\traits\HandlesPdoErrors;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;

/**
 * Clase que adapta la tabla a_fases a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 24/12/2025
 */
class PgActividadFaseRepository extends ClaseRepository implements ActividadFaseRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct(
        private readonly UsuarioRepositoryInterface $usuarioRepository,
        private readonly RoleRepositoryInterface $roleRepository,
        private readonly TareaProcesoRepositoryInterface $tareaProcesoRepository,
    ) {
        $oDbl = GlobalPdo::get('oDBC');
        $this->setoDbl($oDbl);
        $oDbl_Select = GlobalPdo::get('oDBC_Select');
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('a_fases');
    }
    /**
     * @param list<int> $a_id_tipo_proceso
     * @return list<int>
     */
    public function getTodasActividadFases(array $a_id_tipo_proceso): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();

        $cond = '';
        // filtro por sf/sv
        $miSfsv = ConfigGlobal::mi_sfsv();
        switch ($miSfsv) {
            case 1: // sv
                $cond = "(sv ='t') ";
                break;
            case 2: //sf
                $cond = "(sf ='t') ";
                break;
        }
        $cond .= ' AND';

        $aFases = [];
        foreach ($a_id_tipo_proceso as $idTipoProceso) {
            $sQuery = "SELECT f.id_fase, f.desc_fase
                    FROM $nom_tabla f JOIN a_tareas_proceso p USING (id_fase)
                    WHERE $cond id_tipo_proceso = $idTipoProceso
                    ";
            $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

            foreach ($stmt as $row) {
                if (!is_array($row)) {
                    continue;
                }
                if (isset($row['id_fase']) && is_numeric($row['id_fase'])) {
                    $aFases[] = (int) $row['id_fase'];
                }
            }
        }
        return $aFases;
    }
/**
     * @return array<string, int>
     */
    public function getArrayActividadFasesTodas(array $aProcesos): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();

        $oMiUsuario = $this->usuarioRepository->findById(ConfigGlobal::mi_id_usuario());
        $id_role = $oMiUsuario?->getId_role() ?? 0;
        $miSfsv = ConfigGlobal::mi_sfsv();

        $aRoles = $this->roleRepository->getArrayRoles();

        $cond = '';
        if (!empty($aRoles[$id_role]) && ($aRoles[$id_role] === 'SuperAdmin')) {
            $cond = "(sf = 't' OR sv ='t') ";
        } else {
            // filtro por sf/sv
            switch ($miSfsv) {
                case 1: // sv
                    $cond = "(sv ='t') ";
                    break;
                case 2: //sf
                    $cond = "(sf ='t') ";
                    break;
            }
        }

        // intentar ordenar. No se puede por que los num de orden son distintos para cada proceso
        $aDescFases = [];
        $aFasesComunes = [];
        foreach ($aProcesos as $id_tipo_proceso) {
            $sCondicion = "WHERE $cond AND id_tipo_proceso = $id_tipo_proceso";
            $sQuery = "SELECT f.id_fase, f.desc_fase
					FROM $nom_tabla f JOIN a_tareas_proceso p USING (id_fase)
					$sCondicion
					";
            $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

            $aFasesProceso = [];
            foreach ($stmt as $row) {
                if (!is_array($row)) {
                    continue;
                }
                if (!isset($row['id_fase']) || !is_numeric($row['id_fase'])) {
                    continue;
                }
                $id_fase = (int) $row['id_fase'];
                $descRaw = $row['desc_fase'] ?? '';
                $desc_fase = is_scalar($descRaw) ? (string) $descRaw : '';
                $aDescFases[$id_fase] = $desc_fase;

                $aFasesProceso[] = $id_fase;

            }
            // la primera vuelta no hay nada y hay que saltarlo:
            if (empty($aFasesComunes)) {
                $aFasesComunes = $aFasesProceso;
                continue;
            }
            $aFasesComunes = $aFasesComunes + $aFasesProceso;
        }
        // poner la descripción de la fase en el array resultante.
        $aFasesComunesOrden = [];
        foreach ($aFasesComunes as $id_fase) {
            $desc_fase = (string) ($aDescFases[$id_fase] ?? '');
            if ($desc_fase !== '') {
                $aFasesComunesOrden[$desc_fase] = $id_fase;
            }
        }

        return $aFasesComunesOrden;
    }

    /**
     * Para ver una cuadricula con todas las fases de un conjunto de procesos y
     * poder marcarlas. para sustituir a la funcion de getArrayActividadFases.
     *
     * @param list<int> $aProcesos
     * @return array<int|string, int>
     */
    public function getArrayFasesProcesos(array $aProcesos = []): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();

        $miSfsv = ConfigGlobal::mi_sfsv();

        $cond = '';
        // filtro por sf/sv
        switch ($miSfsv) {
            case 1: // sv
                $cond = "(sv = 't') ";
                break;
            case 2: //sf
                $cond = "(sf = 't') ";
                break;
        }

        $num_procesos = count($aProcesos);
        $aFasesProcesoDesc = [];
        if ($num_procesos > 0) {
            foreach ($aProcesos as $id_tipo_proceso) {
                $sCondicion = "WHERE $cond AND id_tipo_proceso = $id_tipo_proceso";
                $sQuery = "SELECT f.id_fase, f.desc_fase
					FROM $nom_tabla f JOIN a_tareas_proceso p USING (id_fase)
					$sCondicion
					GROUP BY f.id_fase, f.desc_fase
					ORDER BY desc_fase";
                $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }
                foreach ($stmt as $aDades) {
                    if (!is_array($aDades)) {
                        continue;
                    }
                    if (!isset($aDades['id_fase']) || !is_numeric($aDades['id_fase'])) {
                        continue;
                    }
                    $descRaw = $aDades['desc_fase'] ?? '';
                    $desc = is_scalar($descRaw) ? (string) $descRaw : '';
                    $aFasesProcesoDesc[$desc] = (int) $aDades['id_fase'];
                }
            }
        } else {
            $sQuery = "SELECT id_fase, desc_fase
					FROM $nom_tabla
					WHERE $cond
					ORDER BY desc_fase";
            $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }
            foreach ($stmt as $aDades) {
                if (!is_array($aDades)) {
                    continue;
                }
                if (!isset($aDades['id_fase']) || !is_numeric($aDades['id_fase'])) {
                    continue;
                }
                $descRaw = $aDades['desc_fase'] ?? '';
                $desc = is_scalar($descRaw) ? (string) $descRaw : '';
                if ($desc === '') {
                    continue;
                }
                $aFasesProcesoDesc[$desc] = (int) $aDades['id_fase'];
            }
        }
        return $aFasesProcesoDesc;
    }

    public function getFaseAnterior(int $id_tipo_proceso,int $iFase):?int
    {
        $a_fases_proceso = $this->getArrayFasesProcesos([$id_tipo_proceso]);

        $id_fase_anterior = null;
        reset($a_fases_proceso);
        while (current($a_fases_proceso) !== $iFase) {
            $current = current($a_fases_proceso);
            if ($current === false) {
                return null;
            }
            $id_fase_anterior = $current;
            next($a_fases_proceso);
        }
        return $id_fase_anterior;

    }
/**
     * @return array<int|string, string>
     */
    public function getArrayActividadFases(array $aProcesos = [], bool $bresp = false): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();

        $oMiUsuario = $this->usuarioRepository->findById(ConfigGlobal::mi_id_usuario());
        $id_role = $oMiUsuario?->getId_role() ?? 0;
        $miSfsv = ConfigGlobal::mi_sfsv();

        $aRoles = $this->roleRepository->getArrayRoles();

        if ($bresp) {
            //$miPerm=$oMiUsuario->getPerm_oficinas();
            $oPermiso = new PermDl();
        }

        $cond = '';
        if (!empty($aRoles[$id_role]) && ($aRoles[$id_role] === 'SuperAdmin')) {
            $cond = "(sf = 't' OR sv ='t') ";
        } else {
            // filtro por sf/sv
            switch ($miSfsv) {
                case 1: // sv
                    $cond = "(sv = 't') ";
                    break;
                case 2: //sf
                    $cond = "(sf = 't') ";
                    break;
            }
        }

        $num_procesos = count($aProcesos);
        if ($num_procesos > 0) {
            $sCondicion = "WHERE $cond AND id_tipo_proceso =";
            $sCondicion .= implode(' OR id_tipo_proceso = ', $aProcesos);
            $sQuery = "SELECT f.id_fase, f.desc_fase
					FROM $nom_tabla f JOIN a_tareas_proceso p USING (id_fase)
					$sCondicion
					GROUP BY f.id_fase, f.desc_fase
					HAVING Count(p.id_tipo_proceso) = $num_procesos
					ORDER BY desc_fase";
        } else {
            $sQuery = "SELECT id_fase, desc_fase
					FROM $nom_tabla
					WHERE $cond
					ORDER BY desc_fase";
        }
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }
        $aFasesComunes = [];
        foreach ($stmt as $aDades) {
            if (!is_array($aDades)) {
                continue;
            }
            if (!isset($aDades['id_fase']) || !is_numeric($aDades['id_fase'])) {
                continue;
            }
            $descRaw = $aDades['desc_fase'] ?? '';
            $aFasesComunes[(int) $aDades['id_fase']] = is_scalar($descRaw) ? (string) $descRaw : '';
        }

        // Si no hay proceso se muestra todo.
        if (empty($aProcesos)) {
            return $aFasesComunes;
        } else {
            // Ordenar según el primer proceso (si hay más de uno).
            reset($aProcesos);
            $id_tipo_proceso = current($aProcesos);
            $aFasesProceso = $this->tareaProcesoRepository->getFasesProceso($id_tipo_proceso);
            $aFasesProcesoDesc = [];
            foreach ($aFasesProceso as $id_item => $id_fase) {
                // compruebo que está en la lista de las fases comunes.
                if (!array_key_exists($id_fase, $aFasesComunes)) {
                    continue;
                }
                $desc_fase = (string)$aFasesComunes[$id_fase];
                // compruebo si soy el responsable
                if ($bresp) {
                    $oTareaProceso = $this->tareaProcesoRepository->findById($id_item);
                    if ($oTareaProceso === null) {
                        continue;
                    }
                    $of_responsable_txt = $oTareaProceso->getOf_responsable_txt();

                    // Si no hay oficina responsable, pueden todos:
                    if (empty($of_responsable_txt)
                        || $oPermiso->have_perm_oficina($of_responsable_txt)
                    ) {
                        $aFasesProcesoDesc[$id_fase] = $desc_fase;
                    }
                } else {
                    $aFasesProcesoDesc[$id_fase] = $desc_fase;
                }
            }
            return $aFasesProcesoDesc;
        }
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo ActividadFase
     *
     * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
     * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return list<ActividadFase> Una colección de objetos de tipo ActividadFase
     */
    public function getActividadFases(array $aWhere = [], array $aOperators = []): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $ActividadFaseSet = new Set();
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
        /** @var list<ActividadFase> $items */
        $items = [];
        foreach ($filas as $aDatos) {
            if (!is_array($aDatos)) {
                continue;
            }
            $normalized = [];
            foreach ($aDatos as $key => $value) {
                $normalized[(string) $key] = $value;
            }
            $items[] = ActividadFase::fromArray($normalized);
        }
        return $items;
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(ActividadFase $ActividadFase): bool
    {
        $id_fase = $ActividadFase->getIdFaseVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_fase = $id_fase";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(ActividadFase $ActividadFase): bool
    {
        $id_fase = $ActividadFase->getIdFaseVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_fase);

        $aDatos = $ActividadFase->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_fase']);
            $update = "
					desc_fase                = :desc_fase,
					sf                       = :sf,
					sv                       = :sv";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_fase = $id_fase";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            // INSERT
            $campos = "(id_fase,desc_fase,sf,sv)";
            $valores = "(:id_fase,:desc_fase,:sf,:sv)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        /** @var \PDOStatement $stmt */
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_fase): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_fase = $id_fase";
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
     * @param int $id_fase
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_fase): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_fase = $id_fase";
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
     * Busca la clase con id_fase en la base de datos .
     */
    public function findById(int $id_fase): ?ActividadFase
    {
        $aDatos = $this->datosById($id_fase);
        if ($aDatos === false) {
            return null;
        }
        return ActividadFase::fromArray($aDatos);
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('a_fases_id_fase_seq'::regclass)";
        $stmt = $oDbl->query($sQuery);
        if ($stmt === false) {
            return 0;
        }
        $id = $stmt->fetchColumn();

        return is_numeric($id) ? (int) $id : 0;
    }
}