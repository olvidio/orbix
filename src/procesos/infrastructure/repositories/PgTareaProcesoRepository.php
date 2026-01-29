<?php

namespace src\procesos\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\ConverterDate;
use core\ConverterJson;
use core\Set;
use JsonException;
use PDO;
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;
use src\procesos\domain\entity\TareaProceso;
use src\shared\traits\HandlesPdoErrors;
use function core\is_true;


/**
 * Clase que adapta la tabla a_tareas_proceso a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 26/12/2025
 */
class PgTareaProcesoRepository extends ClaseRepository implements TareaProcesoRepositoryInterface
{
    use HandlesPdoErrors;

    private array $aFases = [];

    public function __construct()
    {
        $oDbl = $GLOBALS['oDBC'];
        $this->setoDbl($oDbl);
        $oDbl_Select = $GLOBALS['oDBC_Select'];
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('a_tareas_proceso');
    }

    /**
     * retorna array de fase#traea => fase_previa, tarea_previa.
     *
     * @param integer iid_tipo_proceso tipus de procés.
     * @return array|false
     */
    public function getArrayFasesDependientes(int $iid_tipo_proceso): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT * FROM $nom_tabla 
                    WHERE id_tipo_proceso = $iid_tipo_proceso
                    ";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);

        $aFases = [];
        foreach ($stmt as $aDades) {
            $id_fase = $aDades['id_fase'];
            $id_tarea = $aDades['id_tarea'];
            // tarea puede ser empty en vez de 0:
            $id_tarea = empty($id_tarea) ? 0 : $id_tarea;
            $fase_tarea = $id_fase . '#' . $id_tarea;

            $aJson_fases_previas = json_decode($aDades['json_fases_previas']);
            $aFases2 = [];
            foreach ($aJson_fases_previas as $json_fase_previa) {
                $id_fase_previa = $json_fase_previa->id_fase;
                $id_tarea_previa = $json_fase_previa->id_tarea;
                $mensaje = $json_fase_previa->mensaje;
                // tarea puede ser empty en vez de 0:
                $id_tarea_previa = empty($id_tarea_previa) ? 0 : $id_tarea_previa;

                $aFases2[] = [$id_fase_previa . '#' . $id_tarea_previa => $mensaje];
            }
            $aFases[$fase_tarea] = $aFases2;
        }
        return $aFases;
    }

    /**
     * Añade una fase y su mensaje al arbolPrevio
     *
     * @param string $fase_tarea_org
     * @param array $aFase_previa
     */
    private function add(string $fase_tarea_org, array $aFase_previa): void
    {
        $fase_tarea = key($aFase_previa);
        $mensaje = current($aFase_previa);
        $this->aFasesArbol[$fase_tarea_org][$fase_tarea] = $mensaje;
    }

    /**
     * añade a la fase original, las fases previas de las que depende.
     * recursivamente.
     *
     * @param string $fase_tarea_org
     * @param array $aaFase_previa
     */
    private function ar(string $fase_tarea_org, array $aaFase_previa): void
    {
        foreach ($aaFase_previa as $aFase_previa) {
            $fase_tarea_previa = key($aFase_previa);
            // evitar loops infinitos:
            if ($fase_tarea_org == $fase_tarea_previa) continue;
            $this->add($fase_tarea_org, $aFase_previa);
            if (array_key_exists($fase_tarea_previa, $this->aFases)) {
                $aaFase_previa = $this->aFases[$fase_tarea_previa];
                $this->ar($fase_tarea_org, $aaFase_previa);
            }
        }
    }

    /**
     * Devuelve un array donde la clave son todas las fase_tarea del proceso.
     *     Para cada fase tarea se le pone un array con todas las fase_tareas de las que depende
     *     (con el mensaje de si no se cumple el requisito).
     *
     * @param integer $iid_tipo_proceso
     * @return array
     */
    public function arbolPrevio(int $iid_tipo_proceso): array
    {
        $this->aFases = $this->getArrayFasesDependientes($iid_tipo_proceso);
        foreach ($this->aFases as $fase_tarea_org => $aaFase_previa) {
            $this->aFasesArbol[$fase_tarea_org] = [];
            $this->ar($fase_tarea_org, $aaFase_previa);
        }
        return $this->aFasesArbol;
    }

    /**
     * retorna un array
     *
     * @param integer iid_tipo_proceso tipus de procés.
     * @param integer id_fase.
     * @param integer id_tarea.
     * @param integer $f nº de fila
     * @return array|false
     */
    public function zzzgetListaFasesDependientes(int $iid_tipo_proceso, int $id_fase, int $id_tarea = 0, int $f = 0): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT * FROM $nom_tabla 
                    WHERE id_tipo_proceso = $iid_tipo_proceso
                    ";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);

        $aFases = [$f => "$id_fase#$id_tarea"];
        foreach ($stmt as $aDades) {
            $id_fase_i = $aDades['id_fase'];
            $id_tarea_i = $aDades['id_tarea'];
            // tarea puede ser empty en vez de 0:
            $id_tarea_i = empty($id_tarea_i) ? 0 : $id_tarea_i;

            if ($id_fase == $id_fase_i && $id_tarea == $id_tarea_i) {

                $aJson_fases_previas = json_decode($aDades['json_fases_previas']);
                foreach ($aJson_fases_previas as $json_fase_previa) {
                    $id_fase_previa = $json_fase_previa->id_fase;
                    $id_tarea_previa = $json_fase_previa->id_tarea;
                    if (!empty($id_fase_previa)) {
                        $f++;
                        $aF2 = $this->zzzgetListaFasesDependientes($iid_tipo_proceso, $id_fase_previa, $id_tarea_previa, $f);
                        $aFases = $aFases + $aF2;
                    }
                }
                return $aFases;
            }
        }
        return true;
    }

    /**
     * retorna integer de la id_fase anterior
     *
     * @param integer id_item la fase tarea.
     * @return integer id_true o false si hi ha un error
     */
    public function getStatusProceso(int $iid_tipo_proceso, array $aFasesEstado): int
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();

        $sQuery = "SELECT id_fase,id_tarea,status FROM $nom_tabla
				WHERE id_tipo_proceso=$iid_tipo_proceso ";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);

        $aFasesOn = [];
        foreach ($stmt as $aDades) {
            $id_fase = $aDades['id_fase'];
            $id_tarea = $aDades['id_tarea'];
            $fase_tarea = $id_fase . '#' . $id_tarea;
            $status = $aDades['status'];
            if (!array_key_exists($fase_tarea, $aFasesEstado)) {
                exit (_("Hay que regenerar el proceso de la actividad"));
            } else {
                if (is_true($aFasesEstado[$fase_tarea])) {
                    $aFasesOn[$id_fase] = $status;
                }
            }
        }
        // los status de la actividad si son ordenados. 1,2,3,4.
        asort($aFasesOn);
        $ultimo_status = end($aFasesOn);

        return $ultimo_status;
    }

    /**
     * retorna l'array amb la lista de fases ordenadas. Per utilitzar a 'a_fases_gestor.class'
     *
     */
    public function getFasesProceso(int $iid_tipo_proceso): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT * FROM $nom_tabla WHERE id_tipo_proceso = $iid_tipo_proceso ";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);

        $aFases = [];
        foreach ($stmt as $aDades) {
            $aFases[$aDades['id_item']] = $aDades['id_fase'];
        }
        return $aFases;
    }

    public function getFaseIndependiente(int $id_tipo_proceso): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQry = "SELECT * FROM $nom_tabla 
                WHERE id_tipo_proceso = $id_tipo_proceso AND json_fases_previas::text = '[]'::text ";
        $stmt = $this->pdoQuery($oDbl, $sQry, __METHOD__, __FILE__, __LINE__);

        $i = 0;
        $aTareaProceso = [];
        foreach ($stmt as $aDatos) {
            $i++;
            $aDatos['json_fases_previas'] = (new ConverterJson($aDatos['json_fases_previas'], true))->fromPg();
            $aTareaProceso[] = TareaProceso::fromArray($aDatos);
        }
        if ($i === 0) {
            $txt = _("No se puede encontrar una fase independiente (que no tenga fase previa) para el proceso: %s");
            $msg = sprintf($txt, $id_tipo_proceso);
            throw new \RuntimeException($msg);
        }
        if ($i > 1) {
            $txt = _("No debería haber más de una fase independiente (que no tenga fase previa) en un proceso. Hay %s para el id_proceso: %s");
            $msg = sprintf($txt, $i, $id_tipo_proceso);
            throw new \RuntimeException($msg);
        }

        return $aTareaProceso;
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo TareaProceso
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo TareaProceso
     * @throws JsonException
     */
    public function getTareasProceso(array $aWhere = [], array $aOperators = []): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $TareaProcesoSet = new Set();
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
        if (isset($aWhere['_ordre']) && $aWhere['_ordre'] !== '') {
            $sOrdre = ' ORDER BY ' . $aWhere['_ordre'];
        }
        if (isset($aWhere['_ordre'])) {
            unset($aWhere['_ordre']);
        }
        if (isset($aWhere['_limit']) && $aWhere['_limit'] !== '') {
            $sLimit = ' LIMIT ' . $aWhere['_limit'];
        }
        if (isset($aWhere['_limit'])) {
            unset($aWhere['_limit']);
        }
        $sQry = "SELECT * FROM $nom_tabla " . $sCondicion . $sOrdre . $sLimit;
        $stmt = $this->prepareAndExecute($oDbl, $sQry, $aWhere, __METHOD__, __FILE__, __LINE__);

        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as $aDatos) {
            // para los json
            $aDatos['json_fases_previas'] = (new ConverterJson($aDatos['json_fases_previas'], true))->fromPg();
            $TareaProceso = TareaProceso::fromArray($aDatos);
            $TareaProcesoSet->add($TareaProceso);
        }
        return $TareaProcesoSet->getTot();
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(TareaProceso $TareaProceso): bool
    {
        $id_item = $TareaProceso->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_item = $id_item";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     * @throws JsonException
     */
    public function Guardar(TareaProceso $TareaProceso): bool
    {
        $id_item = $TareaProceso->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_item);

        $aDatos = $TareaProceso->toArrayForDatabase([
            'json_fases_previas' => fn($v) =>  (new ConverterJson($v,false))->toPg(false),
        ]);

        /*
        $aDatos = [];
        $aDatos['id_tipo_proceso'] = $TareaProceso->getIdTipoProcesoVo()->value();
        $aDatos['id_fase'] = $TareaProceso->getIdFaseVo()->value();
        $aDatos['id_tarea'] = $TareaProceso->getIdTareaVo()->value();
        $aDatos['status'] = $TareaProceso->getStatusVo()->value();
        $aDatos['id_of_responsable'] = $TareaProceso->getId_of_responsable();
        // para los json
        $aDatos['json_fases_previas'] = (new ConverterJson($TareaProceso->getJson_fases_previas(),false))->toPg(false);
        array_walk($aDatos, 'core\poner_null');
        */

        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_item']);
            $update = "
					id_tipo_proceso          = :id_tipo_proceso,
					id_fase                  = :id_fase,
					id_tarea                 = :id_tarea,
					status                   = :status,
					id_of_responsable        = :id_of_responsable,
					json_fases_previas       = :json_fases_previas";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_item = $id_item";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            // INSERT
            $campos = "(id_item,id_tipo_proceso,id_fase,id_tarea,status,id_of_responsable,json_fases_previas)";
            $valores = "(:id_item,:id_tipo_proceso,:id_fase,:id_tarea,:status,:id_of_responsable,:json_fases_previas)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_item): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_item = $id_item";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if (!$stmt->rowCount()) {
            return TRUE;
        }
        return false;
    }

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_item
     * @return array|bool
     * @throws JsonException
     */
    public function datosById(int $id_item): array|bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_item = $id_item";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        // para los json
        if ($aDatos !== false) {
            $aDatos['json_fases_previas'] = (new ConverterJson($aDatos['json_fases_previas'], true))->fromPg();
        }
        return $aDatos;
    }


    /**
     * Busca la clase con id_item en la base de datos .
     * @throws JsonException
     */
    public function findById(int $id_item): ?TareaProceso
    {
        $aDatos = $this->datosById($id_item);
        if (empty($aDatos)) {
            return null;
        }
        return TareaProceso::fromArray($aDatos);
    }

    public function getNewId()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('a_tareas_proceso_id_item_seq'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
    }
}