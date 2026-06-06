<?php

namespace src\actividades\application;

use src\shared\config\ConfigGlobal;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\permisos\domain\PermisosActividades;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string_list;

/**
 * Elimina actividades indicadas por selección masiva o por id único (planning).
 * Sustituye la lógica del antiguo case `eliminar` de actividad_update.php.
 */
final class ActividadEliminar
{
    public function __construct(
        private ActividadAllRepositoryInterface $actividadAllRepository,
        private BorrarActividad $borrarActividad,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        $error_txt = '';
        $a_sel = input_string_list($input, 'sel');
        $Qid_activ = input_int($input, 'id_activ');

        if ($a_sel !== []) {
            foreach ($a_sel as $id) {
                $id_activ = (int) strtok($id, '#');
                $error_txt .= $this->eliminarUna($id_activ);
            }
        }

        if ($Qid_activ > 0) {
            $error_txt .= $this->eliminarUna($Qid_activ);
        }

        return $error_txt;
    }

    private function eliminarUna(int $id_activ): string
    {
        $oActividad = $this->actividadAllRepository->findById($id_activ);
        if ($oActividad === null) {
            return _("actividad no encontrada");
        }
        $id_tipo_activ = $oActividad->getId_tipo_activ();
        $dl_org = $oActividad->getDl_org();

        if (ConfigGlobal::is_app_installed('procesos')) {
            $oPermSesion = $_SESSION['oPermActividades'] ?? null;
            if (!($oPermSesion instanceof PermisosActividades)) {
                return _('sesión de permisos no disponible');
            }
            $oPermSesion->setActividad($id_activ, (string) $id_tipo_activ, $dl_org);
            $oPermActiv = $oPermSesion->getPermisoActual('datos');
            if ($oPermActiv->have_perm_activ('borrar') === true) {
                return $this->borrarActividad->ejecutar($id_activ);
            }

            return _("No tiene permiso para borrar esta actividad");
        }

        return $this->borrarActividad->ejecutar($id_activ);
    }
}
