<?php

namespace src\procesos\application;

use core\ConfigGlobal;
use src\menus\domain\PermisoMenu;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\value_objects\StatusId;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;
use src\procesos\domain\entity\ActividadFase;
use src\procesos\domain\entity\ActividadProcesoTarea;
use src\procesos\domain\value_objects\FaseId;
use function core\is_true;

/**
 * Servicio de aplicación para gestionar los procesos de actividades
 *
 * @package orbix
 * @subpackage procesos
 * @author Daniel Serrabou
 * @version 1.0
 * @created 27/12/2025
 */
class ProcesoActividadService
{
    private array $aFasesPrevias = [];
    private array $aFasesPosteriores = [];
    private array $aFasesEstado = [];
    private array $aFasesTareasEncadenadas = [];
    private array $aOpcionesOficinas = [];
    private array $aStack = [];
    private bool $bForce = false;

    public function __construct(
        private ActividadProcesoTareaRepositoryInterface $actividadProcesoTareaRepository,
        private ActividadFaseRepositoryInterface $actividadFaseRepository,
        private TareaProcesoRepositoryInterface $tareaProcesoRepository,
        private ActividadAllRepositoryInterface $actividadAllRepository
    ) {
    }

    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function guardar(ActividadProcesoTarea $ActividadProcesoTarea): bool
    {
        $id_tipo_proceso = $ActividadProcesoTarea->getId_tipo_proceso();
        $id_activ = $ActividadProcesoTarea->getId_activ();
        $id_fase = $ActividadProcesoTarea->getId_fase();
        $id_tarea = $ActividadProcesoTarea->getId_tarea();

        $completado = $ActividadProcesoTarea->isCompletado();

        // comprobar si hay que cambiar el estado (status) de la actividad.
        // en caso de completar la fase. Si se quita el 'completado' habría que buscar la fase anterior para saber que status corresponde.
        $permitido = TRUE;
        $oActividad = $this->actividadAllRepository->findById($id_activ);
        $statusActividad = $oActividad->getStatus();
        $cTareasProceso = $this->tareaProcesoRepository->getTareasProceso(['id_tipo_proceso' => $id_tipo_proceso, 'id_fase' => $id_fase, 'id_tarea' => $id_tarea]);
        // sólo debería haber uno
        if (!empty($cTareasProceso)) {
            $oTareaProceso = $cTareasProceso[0];
        } else {
            $msg_err = sprintf(_("error: La fase del proceso tipo: %s, fase: %s, tarea: %s"), $id_tipo_proceso, $id_fase, $id_tarea);
            exit($msg_err);
        }
        $fase_tarea = $id_fase . '#' . $id_tarea;
        // comprobar que tengo permiso
        if (!$this->tiene_permiso($id_tipo_proceso, $fase_tarea)) {
            // No se puede marcar por alguna razón.
            echo _("No tiene permiso para marcar o desmarcar esta fase");
            exit();
        }
        $this->cargarFases($id_activ, $id_tipo_proceso);
        if (is_true($completado)) {
            $statusProceso = $oTareaProceso->getStatus();
            $this->marcar($id_activ, $id_tipo_proceso, $fase_tarea);
        } else {
            $this->desmarcar($id_activ, $id_tipo_proceso, $fase_tarea);
            $statusProceso = $this->tareaProcesoRepository->getStatusProceso($id_tipo_proceso, $this->getFasesEstado());
        }
        if ($statusProceso !== $statusActividad) { // cambiar el status de la actividad.
            // OJO si la actividad no es de la dl, no puedo cambiarla.
            $dl_org = $oActividad->getDl_org();
            $id_tabla = $oActividad->getId_tabla();
            // Sólo dre puede aprobar (pasar de proyecto a actual) las actividades
            // ojo marcha atrás tampoco debería poderse.
            if (($statusProceso == StatusId::ACTUAL && $statusActividad < StatusId::ACTUAL)
                || ($statusActividad == StatusId::ACTUAL && $statusProceso < StatusId::ACTUAL)) {
                // para dl y dlf:
                $dl_org_no_f = preg_replace('/(\.*)f$/', '\1', $dl_org);
                if ($dl_org === ConfigGlobal::mi_delef() || $dl_org_no_f === ConfigGlobal::mi_dele()) {
                    if ($_SESSION['oPerm']->have_perm_oficina('des')) {
                        $oActividad->setStatus($statusProceso);
                        $oActividad->DBGuardar();
                        // además debería marcar como completado la fase correspondiente del proceso de la sf.
                        $this->marcarFaseEnSf($id_activ, $statusProceso, $statusActividad);
                    } else {
                        echo _("no se puede cambiar el status de la actividad a 'actual', porque debe hacerlo dre");
                        $permitido = FALSE;
                    }
                } else { // para el resto.
                    if ($id_tabla === 'dl') {
                        // No se puede modificar una actividad de otra dl
                        echo sprintf(_("no se puede modificar el status de una actividad de otra dl (%s)"), $dl_org);
                        $permitido = FALSE;
                    } else {
                        $oActividad->setStatus($statusProceso);
                        $oActividad->DBGuardar();
                    }
                }
            } else {
                if ($dl_org === ConfigGlobal::mi_delef()) {
                    $oActividad->setStatus($statusProceso);
                    $oActividad->DBGuardar();
                } else {
                    if ($id_tabla === 'dl') {
                        // No se puede modificar una actividad de otra dl
                        echo sprintf(_("no se puede modificar el status de una actividad de otra dl (%s)"), $dl_org);
                        //$permitido = FALSE;
                    } else {
                        $oActividad->setStatus($statusProceso);
                        $oActividad->DBGuardar();
                    }
                }
            }
        }

        if ($permitido) {
            return $this->actividadProcesoTareaRepository->Guardar($ActividadProcesoTarea);
        }
        return FALSE;
    }

    /**
     * Desmarca una fase/tarea de una actividad
     */
    public function desmarcar(int $id_activ, int $id_tipo_proceso, string $fase_tarea): void
    {
        // comprobar si hay dependencias insatisfechas
        $rta = $this->comprobar_dependientes($id_activ, $id_tipo_proceso, $fase_tarea);
        if ($rta['marcada'] === false) {
            // No se puede marcar por alguna razón.
            echo $rta['mensaje'];
            exit();
        }

        $id_fase = strtok($fase_tarea, '#');
        $id_tarea = strtok('#');

        $aWhere = [
            'id_activ' => $id_activ,
            'id_fase' => $id_fase,
            'id_tarea' => $id_tarea,
        ];
        $cActividadProcesoTarea = $this->actividadProcesoTareaRepository->getActividadProcesoTareas($aWhere);
        $oActividadProcesoTarea = $cActividadProcesoTarea[0];

        $oActividadProcesoTarea->setCompletado(false);
        $this->actividadProcesoTareaRepository->DBMarcar($oActividadProcesoTarea);
        // Hay que cambiarlo en el array, porque sino no se actualiza:
        $this->aFasesEstado[$fase_tarea] = false;
    }

    /**
     * Marca una fase/tarea de una actividad como completada
     */
    public function marcar(int $id_activ, int $id_tipo_proceso, string $fase_tarea): void
    {
        // Cuando un proceso está mal y se da el caso de referencias circulares en las dependencias,
        // se emplea una variable global para poder detectar cuando se está intentando marcar una fase por segunda vez.
        if (in_array($fase_tarea, $this->aStack)) {
            $msg = _("Hay un error en el diseño del proceso: referencias circulares.");
            exit($msg);
        }
        $this->aStack[] = $fase_tarea;
        // comprobar si hay dependencias insatisfechas
        $this->bForce = true;
        $rta = $this->comprobar_dependencia($id_activ, $id_tipo_proceso, $fase_tarea);
        if ($rta['marcada'] === false) {
            // No se puede marcar por alguna razón.
            echo $rta['mensaje'];
            exit();
        }

        $id_fase = strtok($fase_tarea, '#');
        $id_tarea = strtok('#');

        $aWhere = [
            'id_activ' => $id_activ,
            'id_fase' => $id_fase,
            'id_tarea' => $id_tarea,
        ];
        $cActividadProcesoTarea = $this->actividadProcesoTareaRepository->getActividadProcesoTareas($aWhere);
        if (empty($cActividadProcesoTarea[0])) {
            // Daba error, no sé exactamente...
        } else {
            $oActividadProcesoTarea = $cActividadProcesoTarea[0];

            $oActividadProcesoTarea->setCompletado(true);
            $this->actividadProcesoTareaRepository->DBMarcar($oActividadProcesoTarea);
            // Hay que cambiarlo en el array, porque sino no se actualiza:
            $this->aFasesEstado[$fase_tarea] = true;
        }
    }

    /**
     * Comprueba las dependencias previas de una fase/tarea
     */
    private function comprobar_dependencia(int $id_activ, int $id_tipo_proceso, string $fase_tarea): array
    {
        $msg = '';
        $bMarcada = true;
        // Si no tiene ninguna fase previa, devuelve directamente TRUE.
        if (array_key_exists($fase_tarea, $this->aFasesPrevias)) {
            // comprobar el estado de cada una:
            $aFasesPrevias = $this->aFasesPrevias[$fase_tarea];
            foreach ($aFasesPrevias as $fase_tarea_previa => $mensaje) {
                if (!$this->is_completa($fase_tarea_previa)) {
                    // Si es forzado, solo me aseguro de tener permiso.
                    if ($this->bForce) {
                        if ($this->tiene_permiso($id_tipo_proceso, $fase_tarea_previa)) {
                            $this->marcar($id_activ, $id_tipo_proceso, $fase_tarea_previa);
                            continue;
                        }
                    }
                    $msg .= empty($mensaje) ? $this->getMensaje($fase_tarea_previa, 'marcar') : $mensaje;
                    $bMarcada = false;
                }
            }
        }
        return ['marcada' => $bMarcada, 'mensaje' => $msg];
    }

    /**
     * Obtiene el mensaje de error para una fase/tarea
     */
    public function getMensaje(string $fase_tarea, string $para): string
    {
        $id_fase = strtok($fase_tarea, '#');

        $oFase = $this->actividadFaseRepository->findById($id_fase);
        $descFase = $oFase->getDesc_fase();
        switch ($para) {
            case 'marcar':
                $mensaje = sprintf(_("No tienen completada la fase: %s"), $descFase);
                break;
            case 'desmarcar':
                $mensaje = sprintf(_("La fase: %s está marcada, y depende de esta."), $descFase);
                break;
            default:
                $mensaje = '';
                break;
        }
        return $mensaje;
    }

    /**
     * Verifica si una fase/tarea está completada
     */
    private function is_completa(string $fase_tarea): bool
    {
        $completado = empty($this->aFasesEstado[$fase_tarea]) ? false : $this->aFasesEstado[$fase_tarea];
        return $completado;
    }

    /**
     * Verifica si el usuario tiene permiso para completar una fase/tarea
     */
    public function tiene_permiso(int $id_tipo_proceso, string $fase_tarea): bool
    {
        $id_fase = strtok($fase_tarea, '#');
        $id_tarea = strtok('#');

        $aWhere = [
            'id_tipo_proceso' => $id_tipo_proceso,
            'id_fase' => $id_fase,
            'id_tarea' => $id_tarea,
        ];
        $cTareaProceso = $this->tareaProcesoRepository->getTareasProceso($aWhere);
        if (empty($cTareaProceso)) {
            // la fase de la que depende no está en el proceso
            $msg = sprintf(_("Proceso mal diseñado. La fase %s, con tarea %s no está en el proceso"), $id_fase, $id_tarea);
            exit($msg);
        }
        $oTareaProceso = $cTareaProceso[0];
        $of_responsable_txt = $oTareaProceso->getOf_responsable_txt();
        if (empty($of_responsable_txt)) {
            return true;
        }
        if ($_SESSION['oPerm']->have_perm_oficina($of_responsable_txt)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Carga las fases previas, posteriores y el estado actual
     */
    public function cargarFases(int $id_activ, int $id_tipo_proceso): void
    {
        $this->aFasesPrevias = $this->tareaProcesoRepository->arbolPrevio($id_tipo_proceso);
        $this->aFasesPosteriores = $this->tareaProcesoRepository->getArrayFasesDependientes($id_tipo_proceso);
        $this->aFasesEstado = $this->actividadProcesoTareaRepository->getListaFaseEstado($id_activ);
    }

    /**
     * Carga los permisos de oficinas
     */
    public function cargar_permisos(): array
    {
        //para crear un desplegable de oficinas. Uso los de los menus
        $oPermMenus = new PermisoMenu;
        $this->aOpcionesOficinas = $oPermMenus->lista_array();
        return $this->aOpcionesOficinas;
    }

    /**
     * Comprueba las fases/tareas que dependen de la actual
     */
    private function comprobar_dependientes(int $id_activ, int $id_tipo_proceso, string $fase_tarea): array
    {
        $msg = '';
        $bMarcada = true;

        $this->aFasesTareasEncadenadas = [];
        $this->agregar_dependientes($fase_tarea);

        foreach ($this->aFasesTareasEncadenadas as $fase_tarea_anterior) {
            $completado = false;
            if (array_key_exists($fase_tarea_anterior, $this->aFasesEstado)) {
                $completado = $this->aFasesEstado[$fase_tarea_anterior];
                if (is_true($completado)) {
                    // Si es forzado, solo me aseguro de tener permiso.
                    if ($this->bForce) {
                        if ($this->tiene_permiso($id_tipo_proceso, $fase_tarea_anterior)) {
                            $this->desmarcar($id_activ, $id_tipo_proceso, $fase_tarea_anterior);
                            continue;
                        }
                    }
                    $msg .= empty($msg) ? $this->getMensaje($fase_tarea_anterior, 'desmarcar') : $msg;
                    $bMarcada = false;
                }
            }
        }
        return ['marcada' => $bMarcada, 'mensaje' => $msg];
    }

    /**
     * Agrega las fases/tareas dependientes de forma recursiva
     */
    private function agregar_dependientes(string $fase_tarea_org): array
    {
        // buscar id_fase_org en array
        $b = $this->dependientes_de($fase_tarea_org);
        $a = $this->aFasesTareasEncadenadas;
        $this->aFasesTareasEncadenadas = array_unique(array_merge($a, $b));

        if (!empty($b)) {
            foreach ($b as $fase_tarea) {
                $this->agregar_dependientes($fase_tarea);
            }
        }
        return [];
    }

    /**
     * Obtiene las fases/tareas que dependen de la indicada
     */
    private function dependientes_de(string $fase_tarea_org): array
    {
        // buscar id_fase_org en array
        $b = [];
        foreach ($this->aFasesPosteriores as $fase_tarea => $aaFase_tarea_previa) {
            foreach ($aaFase_tarea_previa as $aFase_tarea_previa) {
                foreach ($aFase_tarea_previa as $fase_tarea_previa => $mensaje) {
                    if ($fase_tarea_org == $fase_tarea_previa) {
                        $b[] = $fase_tarea;
                    }
                }
            }
        }
        return $b;
    }

    /**
     * Marca una fase en la tabla de la otra sección (SF/SV)
     */
    public function marcarFaseEnSf(int $id_activ, int $statusProceso, int $statusActividad): void
    {
        // buscar el id_tipo_proceso para esta actividad de la otra sección
        if (ConfigGlobal::mi_sfsv() == 1) {
            $this->actividadProcesoTareaRepository->setNomTabla('a_actividad_proceso_sf');
        } else {
            $this->actividadProcesoTareaRepository->setNomTabla('a_actividad_proceso_sv');
        }

        $cActividadProcesoTareas = $this->actividadProcesoTareaRepository->getActividadProcesoTareas(['id_activ' => $id_activ]);

        // Puede ser que el proceso no exista (para sfsv=2):
        if (empty($cActividadProcesoTareas)) {
            $this->actividadProcesoTareaRepository->generarProceso($id_activ, 2);
        }

        /* Para no andar buscando que fase corresponde a status, finalmente he decidido
         * que las id_fase para el cambio de status son fijas, e iguales al status de la actividad.
         */
        if ($statusActividad == 1 && $statusProceso == 2) {
            $id_fase = FaseId::FASE_APROBADA;
            $aWhere = ['id_activ' => $id_activ, 'id_fase' => $id_fase];
            $cActividadProcesoTareas = $this->actividadProcesoTareaRepository->getActividadProcesoTareas($aWhere);
            if (!empty($cActividadProcesoTareas)) {
                $oActividadProcessorTarea = $cActividadProcesoTareas[0];
                $oActividadProcessorTarea->setCompletado('t');
                $this->actividadProcesoTareaRepository->Guardar($oActividadProcessorTarea);
            }
        }
        if ($statusActividad > $statusProceso) {
            $id_fase = FaseId::FASE_APROBADA;
            $aWhere = ['id_activ' => $id_activ, 'id_fase' => $id_fase];
            $cActividadProcesoTareas = $this->actividadProcesoTareaRepository->getActividadProcesoTareas($aWhere);
            if (!empty($cActividadProcesoTareas)) {
                $oActividadProcessorTarea = $cActividadProcesoTareas[0];
                $oActividadProcessorTarea->setCompletado('f');
                $this->actividadProcesoTareaRepository->Guardar($oActividadProcessorTarea);
            }
        }
    }

    /**
     * Resetea el estado interno del servicio
     */
    public function resetState(): void
    {
        $this->aFasesPrevias = [];
        $this->aFasesPosteriores = [];
        $this->aFasesEstado = [];
        $this->aFasesTareasEncadenadas = [];
        $this->aStack = [];
        $this->bForce = false;
    }

    public function getOpcionesOficinas(): array
    {
        return $this->aOpcionesOficinas;
    }

    public function getFasesEstado(): array
    {
        return $this->aFasesEstado;
    }
}
