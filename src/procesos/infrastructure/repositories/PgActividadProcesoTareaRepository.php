<?php

namespace src\procesos\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\ConfigGlobal;
use core\Set;
use PDO;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\actividades\domain\value_objects\StatusId;
use src\procesos\application\ProcesoActividadService;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\procesos\domain\contracts\ProcesoTipoRepositoryInterface;
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;
use src\procesos\domain\entity\ActividadFase;
use src\procesos\domain\entity\ActividadProcesoTarea;
use src\procesos\domain\value_objects\FaseId;
use src\shared\traits\HandlesPdoErrors;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use web\DateTimeLocal;
use function core\is_true;


/**
 * Clase que adapta la tabla a_actividad_proceso_sv a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 26/12/2025
 */
class PgActividadProcesoTareaRepository extends ClaseRepository implements ActividadProcesoTareaRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $oDbl = $GLOBALS['oDBC'];
        $this->setoDbl($oDbl);
        $oDbl_Select = $GLOBALS['oDBC_Select'];
        $this->setoDbl_select($oDbl_Select);
         if (ConfigGlobal::mi_sfsv() === 1) {
            $this->setNomTabla('a_actividad_proceso_sv');
        } else {
            $this->setNomTabla('a_actividad_proceso_sf');
        }
    }

    public function borrarFaseTareaInexistente($id_tipo_proceso, $id_fase, $id_tarea): void
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $nom_tabla_procesos = 'a_tareas_proceso';

        $temp_table = "tmp_borrar";
        $sQuery = "CREATE TEMPORARY TABLE $temp_table AS ";
        $sQuery .= "SELECT a.id_activ,a.id_fase,id_tarea
                    FROM $nom_tabla a LEFT JOIN $nom_tabla_procesos p USING (id_tipo_proceso,id_fase,id_tarea)
                    WHERE id_tipo_proceso=$id_tipo_proceso AND p.id_fase IS NULL";
        $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);

        // Borrar:
        $sQry_INSERT = "DELETE FROM $nom_tabla a
                        USING $temp_table t
                        WHERE  a.id_activ = t.id_activ
                            AND a.id_fase = t.id_fase
                            AND a.id_tarea = t.id_tarea
                       ";
        $this->pdoQuery($oDbl, $sQry_INSERT, __METHOD__, __FILE__, __LINE__);
    }

    public function addFaseTarea(int $id_tipo_proceso, int $id_fase, int $id_tarea): void
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();

        $temp_table = "tmp_proceso_" . $id_fase . "_" . $id_tarea;
        $sQuery = "CREATE TEMPORARY TABLE $temp_table AS ";
        $sQuery .= "(SELECT DISTINCT id_activ FROM $nom_tabla WHERE id_tipo_proceso=$id_tipo_proceso)";
        $sQuery .= " EXCEPT ";
        $sQuery .= "(SELECT DISTINCT id_activ FROM $nom_tabla 
                    WHERE id_tipo_proceso=$id_tipo_proceso AND id_fase=$id_fase AND id_tarea=$id_tarea)";
        $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);

        // Añadir fase:
        $sQry_INSERT = "INSERT INTO $nom_tabla (id_tipo_proceso,id_activ,id_fase,id_tarea)    
                        SELECT $id_tipo_proceso, id_activ, $id_fase, $id_tarea FROM $temp_table";
        $this->pdoQuery($oDbl, $sQry_INSERT, __METHOD__, __FILE__, __LINE__);
    }

    /**
     * retorna un array amb les fases i el seu estat.
     *
     * @param integer $iid_activ
     * @return array|false
     */
    public function getListaFaseEstado(int $iid_activ): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT * FROM $nom_tabla WHERE id_activ=$iid_activ
                ";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);

        $aFasesEstado = [];
        foreach ($stmt as $aDades) {
            $id_fase = $aDades['id_fase'];
            $id_tarea = $aDades['id_tarea'];
            $completado = $aDades['completado'];
            $f = "$id_fase#$id_tarea";
            $aFasesEstado[$f] = $completado;
        }
        return $aFasesEstado;
    }

    /**
     * Devuelve el estado de la fase ("ok atn sacd" que es la 5)
     * o FALSE si falla.
     *
     * @param $iid_activ
     * @return 't'|'f'|FALSE
     */
    public function getSacdAprobado(int $iid_activ): ?bool
    {
        $oDbl = $this->getoDbl_Select();
        // Mirar el proceso de la sv
        $this->setNomTabla('a_actividad_proceso_sv');
        // La fase ok sacd es la 5. Por definición
        $id_fase_atn_sacd = FaseId::FASE_OK_SACD;
        $nom_tabla = $this->getNomTabla();

        $sQry = "SELECT completado FROM $nom_tabla WHERE id_activ=" . $iid_activ . " AND id_fase=$id_fase_atn_sacd ";
        $stmt = $this->pdoQuery($oDbl, $sQry, __METHOD__, __FILE__, __LINE__);

        if ($stmt->rowCount() === 1) {
            $aDades = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $aDades['completado'];
        }
        return null;
    }

    /**
     * En general genera los dos procesos, para sv y sf.
     * Si se le pasa el parámetro isfsv, sólo genera el proceso correspondiente.
     *
     * @param string $iid_activ
     * @param integer|string $isfsv
     * @param boolean $force para forzar a borrar el proceso y generarlo de nuevo
     * @return boolean|int id_fase.
     */
    public function generarProceso(string $iid_activ = '', int|string $isfsv = '', bool $force = FALSE)
    {
        // Si se genera al crear una actividad Ex. El objeto Actividad no la encuentra
        // porque todavía no se ha importado (y no está en su grupo de actividades).
        // Para evitar errores accedo directamente a los datos sin esperar a importarla,
        // En principio la dl que la crea es porque va a importarla...
        /*
        if ($iid_activ < 0) {
            $oActividad = new ActividadEx(array('id_activ' => $iid_activ));
        } else {
            $ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
            $oActividad = $ActividadAllRepository->findById($iid_activ);
        }
        */
        $ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
        $oActividad = $ActividadAllRepository->findById($iid_activ);
        $iid_tipo_activ = $oActividad->getIdTipoActividadVo()->value();
        $TipoDeActividadRepository = $GLOBALS['container']->get(TipoDeActividadRepositoryInterface::class);
        $oTipoDeActividad = $TipoDeActividadRepository->findById($iid_tipo_activ);

        if (empty($oTipoDeActividad)) {
            echo sprintf(_("No existe este tipo de actividad: %s"), $iid_tipo_activ) . "\n";
            return TRUE;

        }
        // Creo que cuando pasa es que no existe la actividad (pero se tiene el id_activ)
        if (empty($oActividad) || empty($iid_tipo_activ)) {
            echo sprintf(_("La actividad: %s ya no existe"), $iid_activ) . "\n";
            return TRUE;
        }
        $dl_org = $oActividad->getDl_org();
        $dl_org_no_f = preg_replace('/(\.*)f$/', '\1', $dl_org);

        if (empty($isfsv)) {
            $a_sfsv = [1, 2];
            $isfsv = ConfigGlobal::mi_sfsv();
        } else {
            $a_sfsv = [$isfsv];
        }
        $iid_fase = [];
        foreach ($a_sfsv as $sfsv) {
            if ($sfsv === 1) {
                $this->setNomTabla('a_actividad_proceso_sv');
            } else {
                $this->setNomTabla('a_actividad_proceso_sf');
            }
            if ($dl_org_no_f === ConfigGlobal::mi_dele()) {
                $id_tipo_proceso = $oTipoDeActividad->getId_tipo_proceso($sfsv);
            } else {
                // NO se genera si:
                // - es una actividad de otra dl,
                // - y de la otra sección
                // - y no se hace en una casa de la dl.
                if ($isfsv != $sfsv) {
                    $id_ubi = $oActividad->getId_ubi();
                    $dl_casa = $GLOBALS['container']->get(CasaRepositoryInterface::class)->findById($id_ubi)?->getDlVo()->value();
                    if ($dl_casa != ConfigGlobal::mi_dele()) {
                        continue;
                    }
                }
                $id_tipo_proceso = $oTipoDeActividad->getId_tipo_proceso_ex($sfsv);
            }
            if (empty($id_tipo_proceso)) {
                echo sprintf(_("No tiene definido el proceso para este tipo de actividad: %s de sv/sf: %s"), $iid_tipo_activ, $sfsv);
                return TRUE;
            }
            // Asegurar que no existe, a veces al hacerlo para las dos secciones, una lo tiene y otra no:
            // >> Cuando se hace manual, es porque se quiere regenerar y hay que forzar:
            if ($force === FALSE) {
                $cActividadProcesoTarea = $this->getActividadProcesoTareas(['id_activ' => $iid_activ]);
                if (empty($cActividadProcesoTarea)) {
                    $iid_fase[$sfsv] = $this->generar($iid_activ, $id_tipo_proceso, $sfsv, $force);
                } else {
                    $iid_fase[$sfsv] = $cActividadProcesoTarea[0]->getIdFaseVo()->value();
                }
            } else {
                $iid_fase[$sfsv] = $this->generar($iid_activ, $id_tipo_proceso, $sfsv, $force);
            }
        }

        // devuelve la fase del proceso propio
        return $iid_fase[$isfsv];
    }

    /**
     * retorna un array amb les fases completades.
     *
     * @param integer iid_activ
     * @return array|false
     */
    public function getFasesCompletadas(int $iid_activ): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        // No puedo hacer la consulta con WHERE completado='t',
        // porque hay que distinguirlo de si existe el proceso o no, y hay que crearlo.
        //$sQuery = "SELECT * FROM $nom_tabla WHERE id_activ=$iid_activ
        //        AND completado='t' ";
        $sQuery = "SELECT * FROM $nom_tabla WHERE id_activ=$iid_activ ";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);

        $aFasesCompletadas = [];
        if ($stmt->rowCount() > 0) {
            $aFasesCompletadas = [];
            foreach ($stmt as $aDades) {
                if (is_true($aDades['completado'])) {
                    $aFasesCompletadas[] = $aDades['id_fase'];
                }
            }
            return $aFasesCompletadas;
        }

        // no existe el proceso:
        $id_fase_primera = $this->generarProceso($iid_activ);
        return [$id_fase_primera];
    }

    /**
     * retorna si té la fase completada o no.
     *
     * @param integer iid_activ
     * @param integer iid_fase
     * @return bool
     */
    public function faseCompletada(int $iid_activ, int $iid_fase): bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        // No puedo hacer la consulta con WHERE completado='t',
        // porque hay que distinguirlo de si existe el proceso o no, y hay que crearlo.
        $sQry = "SELECT * FROM $nom_tabla WHERE id_activ=$iid_activ AND id_fase=$iid_fase ";
        $stmt = $this->pdoQuery($oDbl, $sQry, __METHOD__, __FILE__, __LINE__);

        if ($stmt->rowCount() == 1) {
            // aunque realmente solo debería existir un fila
            foreach ($stmt as $aDades) {
                return is_true($aDades['completado']);
            }
        } else {
            // no existe el proceso:
            $this->generarProceso($iid_activ);
            return FALSE;
        }
        return FALSE;
    }

    /**
     * Borra el procés per l'activitat.
     *
     * @param integer iid_activ
     */
    private function borrar(int $iid_activ): void
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (!empty($iid_activ)) {
            $sQry = "DELETE FROM $nom_tabla WHERE id_activ=$iid_activ";
            $this->pdoQuery($oDbl, $sQry, __METHOD__, __FILE__, __LINE__);
        }
    }

    /**
     * Genera el procés per l'activitat, segons el tipus de procés.
     * retorna el id_fase de la primera fase.
     *
     * @param integer iid_activ
     * @param integer iid_tipo_proceso
     * @return int id_fase.
     */
    private function generar($iid_activ = '', $iid_tipo_proceso = '', $isfsv = '', $force = FALSE)
    {
        $this->borrar($iid_activ);

        // ordena por fases previas, no importa la fase: simplemente las vacias primero
        // para saber cual es la primera: que no depende de ninguna.
        $aWhere = [
            'id_tipo_proceso' => $iid_tipo_proceso,
            '_ordre' => '(json_fases_previas::json->0)::text DESC'
        ];
        $TareaProcesoRepository = $GLOBALS['container']->get(TareaProcesoRepositoryInterface::class);
        $cTareasProceso = $TareaProcesoRepository->getTareasProceso($aWhere);

        $ActividadProcesoTareaRepository = $GLOBALS['container']->get(ActividadProcesoTareaRepositoryInterface::class);

        // OJO: cuando se accede actividades ya existentes,
        // hay que intentar conservar el status que tenga. Para las actividades anteriores
        // (antes de instalar el módulo de procesos), se marcarán todas las fases del status.
        // Para las posteriores, sólo la primera fase del status.
        // Vamos a establecer la fecha de hoy como criterio para distinguir entre
        // actividades anteriores y posteriores.
        $ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
        $oActividad = $ActividadAllRepository->findById($iid_activ);
        $nom_activ = $oActividad->getNom_activ();
        $statusActividad = $oActividad->getStatusVo()->value();

        // Si es borrable, hay que ver que hacemos: de momento nada.
        if ($statusActividad === StatusId::BORRABLE) {
            $nom_activ = empty($nom_activ) ? $iid_activ : $nom_activ;
            $msg = sprintf(_("error al generar el proceso de la actividad: '%s'. Está para borrar."), $nom_activ);
            $msg .= "\n";
            $msg .= "<br>";
            echo $msg;
            return FALSE;
        }
        // Si es anterior a hoy, mantengo el status de la actividad.
        $oFini = $oActividad->getF_ini();
        $oHoy = new DateTimeLocal();
        if ($oFini < $oHoy && $force === FALSE) {
            // Anterior
            foreach ($cTareasProceso as $oTareaProceso) {
                $id_fase = $oTareaProceso->getIdFaseVo()?->value();
                $id_tarea = $oTareaProceso->getIdTareaVo()?->value();
                $statusFase = $oTareaProceso->getStatusVo()->value();
                if ($statusFase <= $statusActividad) {
                    $completado = 't';
                } else {
                    $completado = 'f';
                }
                $newIdItem = $ActividadProcesoTareaRepository->getNewId();
                $oActividadProcesoTarea = new ActividadProcesoTarea();
                $oActividadProcesoTarea->setId_item($newIdItem);
                //??? $oActividadProcesoTarea->setSfsvVo($isfsv);
                $oActividadProcesoTarea->setIdTipoProcesoVo($iid_tipo_proceso);
                $oActividadProcesoTarea->setIdActividadVo($iid_activ);
                $oActividadProcesoTarea->setIdFaseVo($id_fase);
                $oActividadProcesoTarea->setIdTareaVo($id_tarea);
                $oActividadProcesoTarea->setCompletado($completado);
                if ($ActividadProcesoTareaRepository->Guardar($oActividadProcesoTarea) === false) {
                    echo "1.error: No se ha guardado el proceso: $iid_activ,$iid_tipo_proceso,$id_fase,$id_tarea<br>";
                    //return false;
                }
            }
        } else {
            // Posterior.
            // para el caso de forzar, pongo todo a 0.
            if (is_true($force)) {
                $p = 0;
                $statusNew = '';
                foreach ($cTareasProceso as $oTareaProceso) {
                    $p++;
                    $id_fase = $oTareaProceso->getIdFaseVo()?->value();
                    $id_tarea = $oTareaProceso->getIdTareaVo()?->value();
                    $statusFase = $oTareaProceso->getStatusVo()->value();
                    $newIdItem = $ActividadProcesoTareaRepository->getNewId();
                    $oActividadProcesoTarea = new ActividadProcesoTarea();
                    $oActividadProcesoTarea->setId_item($newIdItem);
                    //??? $oActividadProcesoTarea->setSfsvVo($isfsv);
                    $oActividadProcesoTarea->setIdTipoProcesoVo($iid_tipo_proceso);
                    $oActividadProcesoTarea->setIdActividadVo($iid_activ);
                    $oActividadProcesoTarea->setIdFaseVo($id_fase);
                    $oActividadProcesoTarea->setIdTareaVo($id_tarea);
                    if ($p === 1) {
                        $oActividadProcesoTarea->setCompletado('t'); // Marco la primera fase como completado.
                        // marco el status correspondiente en la actividad. Hay que hacerlo al final para no entrar en
                        // un bucle recurrente al modificar una actividad nueva que todavía no tienen el proceso.
                        $statusNew = $statusFase;
                    }
                    if ($ActividadProcesoTareaRepository->Guardar($oActividadProcesoTarea) === false) {
                        echo "2.error: No se ha guardado el proceso: $iid_activ,$iid_tipo_proceso,$id_fase,$id_tarea<br>";
                        //return false;
                    }
                }
                // cambiar el status de la actividad para que se ajuste al de la fase.
                $dl_org = $oActividad->getDl_org();
                $dl_org_no_f = preg_replace('/(\.*)f$/', '\1', $dl_org);
                // El status solo se puede guardar si la actividad es de la propia dl (o des desde sv).
                if ($dl_org_no_f === ConfigGlobal::mi_delef() && $_SESSION['oPerm']->have_perm_oficina('des')) {
                    $oActividad->setStatusVo($statusNew);
                    $quiet = 1; // Para que no anote el cambio.
                    $oActividad->DBGuardar($quiet);
                }
            } else {
                // conservo el status
                // al hacer 'insert' no marca dependencias (sólo con 'update').
                // por tanto doy dos vueltas, una para crear las fases y otra para marcar las completadas
                foreach ($cTareasProceso as $oTareaProceso) {
                    $id_fase = $oTareaProceso->getIdFaseVo()?->value();
                    $id_tarea = $oTareaProceso->getIdTareaVo()?->value();
                    $statusFase = $oTareaProceso->getStatusVo()->value();
                    $newIdItem = $ActividadProcesoTareaRepository->getNewId();
                    $oActividadProcesoTarea = new ActividadProcesoTarea();
                    $oActividadProcesoTarea->setId_item($newIdItem);
                    //??? $oActividadProcesoTarea->setSfsvVo($isfsv);
                    $oActividadProcesoTarea->setIdTipoProcesoVo($iid_tipo_proceso);
                    $oActividadProcesoTarea->setIdActividadVo($iid_activ);
                    $oActividadProcesoTarea->setIdFaseVo($id_fase);
                    $oActividadProcesoTarea->setIdTareaVo($id_tarea);
                    if ($ActividadProcesoTareaRepository->Guardar($oActividadProcesoTarea) === false) {
                        echo "3.error: No se ha guardado el proceso: $iid_activ,$iid_tipo_proceso,$id_fase,$id_tarea<br>";
                        //return false;
                    }
                }
                $aWhere = [
                    'id_activ' => $iid_activ,
                    '_ordre' => 'id_fase',
                ];
                $cActividadProcesoTarea = $this->getActividadProcesoTareas($aWhere);
                foreach ($cActividadProcesoTarea as $oActividadProcesoTarea) {
                    $id_fase = $oActividadProcesoTarea->getIdFaseVo()?->value();
                    $completado = 'f';
                    // marco el status correspondiente en la actividad.
                    if ($statusActividad === StatusId::PROYECTO) {
                        if ($id_fase <= FaseId::FASE_PROYECTO) {
                            $completado = 't';
                        }
                    }
                    if ($statusActividad === StatusId::ACTUAL) {
                        if ($id_fase <= FaseId::FASE_APROBADA) {
                            $completado = 't';
                        }
                    }
                    if ($statusActividad === StatusId::TERMINADA) {
                        if ($id_fase <= FaseId::FASE_TERMINADA) {
                            $completado = 't';
                        }
                    }
                    if (is_true($completado)) {
                        $oActividadProcesoTarea->setCompletado($completado);
                        if ($this->Guardar($oActividadProcesoTarea) === false) {
                            echo "4.error: No se ha guardado el proceso: $iid_activ,$iid_tipo_proceso,$id_fase,$id_tarea<br>";
                            //return false;
                        }
                    }
                }
            }
        }

        if (!empty($cTareasProceso[0])) {
            return $cTareasProceso[0]->getIdFaseVo()->value();
        } else {
            $ProcesoTipoRepository = $GLOBALS['container']->get(ProcesoTipoRepositoryInterface::class);
            $oProcesoTipo = $ProcesoTipoRepository->findById($iid_tipo_proceso);
            $nom_proceso = empty($oProcesoTipo->getNom_proceso()) ? $iid_tipo_proceso : $oProcesoTipo->getNom_proceso();
            $nom_activ = empty($nom_activ) ? $iid_activ : $nom_activ;

            $msg = sprintf(_("error al generar el proceso de la actividad: '%s'. Tipo de proceso: '%s' para sf/sv: %s."), $nom_activ, $nom_proceso, $isfsv);
            $msg .= "\n";
            $msg .= _("Probablemente no esté definido el proceso");
            $msg .= "\n";
            $msg .= "<br>";
            echo $msg;
        }
        return true;
    }

    /**
     * Creo esta nueva función para poder guardar sin volver a repetir el proceso de mirar si hay que cambiar el
     * estado de la actividad. Sólo sirve para hacer UPDATE de completada.
     *
     */
    public function DBMarcar(ActividadProcesoTarea $ActividadProcesoTarea): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();

        $id_item = $ActividadProcesoTarea->getId_item();

        $aDades = [];
        $aDades['id_tipo_proceso'] = $ActividadProcesoTarea->getIdTipoProcesoVo()->value();
        $aDades['id_activ'] = $ActividadProcesoTarea->getId_activ();
        $aDades['id_fase'] = $ActividadProcesoTarea->getIdFaseVo()?->value();
        $aDades['id_tarea'] = $ActividadProcesoTarea->getIdTareaVo()?->value();
        $aDades['completado'] = $ActividadProcesoTarea->isCompletado();
        $aDades['observ'] = $ActividadProcesoTarea->getObservVo()?->value();
        array_walk($aDades, 'core\poner_null');
        //para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (is_true($aDades['completado'])) {
            $aDades['completado'] = 'true';
        } else {
            $aDades['completado'] = 'false';
        }

        //UPDATE
        $update = "
                id_tipo_proceso          = :id_tipo_proceso,
                id_activ                 = :id_activ,
                id_fase                  = :id_fase,
                id_tarea                 = :id_tarea,
                completado               = :completado,
                observ                   = :observ";
        if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item='$id_item'")) === FALSE) {
            $sClauError = 'ActividadProcesoTarea.update.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        } else {
            try {
                $oDblSt->execute($aDades);
            } catch (\PDOException $e) {
                $err_txt = $e->errorInfo[2];
                $this->setErrorTxt($err_txt);
                $sClauError = 'ActividadProcesoTarea.update.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                return FALSE;
            }
        }
        return TRUE;
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo ActividadProcesoTarea
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo ActividadProcesoTarea
     */
    public function getActividadProcesoTareas(array $aWhere = [], array $aOperators = []): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $ActividadProcesoTareaSet = new Set();
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
            $ActividadProcesoTarea = ActividadProcesoTarea::fromArray($aDatos);
            $ActividadProcesoTareaSet->add($ActividadProcesoTarea);
        }
        return $ActividadProcesoTareaSet->getTot();
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(ActividadProcesoTarea $ActividadProcesoTarea): bool
    {
        $id_item = $ActividadProcesoTarea->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_item = $id_item";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(ActividadProcesoTarea $ActividadProcesoTarea): bool
    {
        $id_item = $ActividadProcesoTarea->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_item);

        $aDatos = $ActividadProcesoTarea->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_item']);
            $update = "
					id_tipo_proceso          = :id_tipo_proceso,
					id_activ                 = :id_activ,
					id_fase                  = :id_fase,
					id_tarea                 = :id_tarea,
					completado               = :completado,
					observ                   = :observ";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_item = $id_item";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            // INSERT
            $campos = "(id_item,id_tipo_proceso,id_activ,id_fase,id_tarea,completado,observ)";
            $valores = "(:id_item,:id_tipo_proceso,:id_activ,:id_fase,:id_tarea,:completado,:observ)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);    }
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
     */
    public function datosById(int $id_item): array|bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_item = $id_item";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        return $aDatos;
    }


    /**
     * Busca la clase con id_item en la base de datos .
     */
    public function findById(int $id_item): ?ActividadProcesoTarea
    {
        $aDatos = $this->datosById($id_item);
        if (empty($aDatos)) {
            return null;
        }
        return ActividadProcesoTarea::fromArray($aDatos);
    }

    public function getNewId()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('a_actividad_proceso_sv_id_item_seq'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
    }
}