<?php

namespace src\actividades\application;

use src\shared\config\ConfigGlobal;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\contracts\ActividadExRepositoryInterface;
use src\actividades\domain\contracts\ImportadaRepositoryInterface;
use src\actividades\domain\value_objects\StatusId;

/**
 * Servicio de aplicacion para eliminar (o marcar como borrable) una actividad.
 *
 * - Si la actividad es de la propia dl y esta en PROYECTO, se elimina fisicamente.
 * - Si es de la propia dl y no esta en PROYECTO, se marca como BORRABLE.
 * - Si es de otra dl y esta importada (id_tabla='dl'), se elimina de Importada.
 * - Si es de otra dl (id_tabla='ex'), se marca como BORRABLE.
 *
 * Devuelve cadena con el texto de error si falla, o cadena vacia si ok
 * (manteniendo compatibilidad con el comportamiento legacy que hacia echo).
 */
class BorrarActividad
{
    public static function ejecutar(int $id_activ): string
    {
        $error_txt = '';
        $ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
        $oActividad = $ActividadAllRepository->findById($id_activ);

        $dl_org = $oActividad->getDl_org();
        $id_tabla = $oActividad->getId_tabla();

        // para des => dl y dlf:
        $dl_org_no_f = preg_replace('/(\.*)f$/', '\1', $dl_org);
        $dl_propia = ConfigGlobal::mi_dele() === $dl_org_no_f;

        if ($dl_propia) {
            $repoActividad = $GLOBALS['container']->get(ActividadDlRepositoryInterface::class);
            $status = $oActividad->getStatus();
            if (!empty($status) && $status === StatusId::PROYECTO) {
                if ($repoActividad->Eliminar($oActividad) === false) {
                    $error_txt = _("hay un error, no se ha eliminado") . "\n" . $repoActividad->getErrorTxt();
                }
            } else {
                $oActividad->setStatus(StatusId::BORRABLE);
                if ($repoActividad->Guardar($oActividad) === false) {
                    $error_txt = _("hay un error, no se ha guardado") . "\n" . $repoActividad->getErrorTxt();
                }
            }
        } else {
            if ($id_tabla === 'dl') {
                // No se puede eliminar una actividad de otra dl. Hay que borrarla como importada
                $ImportadaRepository = $GLOBALS['container']->get(ImportadaRepositoryInterface::class);
                $oImportada = $ImportadaRepository->findById($id_activ);
                $ImportadaRepository->Eliminar($oImportada);
            } else {
                $repoActividad = $GLOBALS['container']->get(ActividadExRepositoryInterface::class);
                $oActividad->setStatus(StatusId::BORRABLE);
                if ($repoActividad->Guardar($oActividad) === false) {
                    $error_txt = _("hay un error, no se ha guardado") . "\n" . $repoActividad->getErrorTxt();
                }
            }
        }

        return $error_txt;
    }
}
