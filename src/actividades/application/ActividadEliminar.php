<?php

namespace src\actividades\application;

use src\shared\config\ConfigGlobal;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\permisos\domain\PermisosActividades;

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
        $a_sel = \src\shared\domain\helpers\FuncTablasSupport::inputStringList($input, 'sel');
        $Qid_activ = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_activ');

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
            if (
                $oPermActiv->have_perm_activ('borrar') === true
                || $this->esImportadaDeOtraDl($oActividad)
            ) {
                return $this->borrarActividad->ejecutar($id_activ);
            }

            return _("No tiene permiso para borrar esta actividad");
        }

        return $this->borrarActividad->ejecutar($id_activ);
    }

    /**
     * Actividad de otra dl visible solo por importación local (tabla dl).
     * En ese caso "borrar" quita el registro Importada, no la actividad origen.
     */
    private function esImportadaDeOtraDl(ActividadAll $oActividad): bool
    {
        $dlOrg = $oActividad->getDl_org() ?? '';
        $dlOrgNoF = (string) preg_replace('/(\.*)f$/', '\1', $dlOrg);
        $dlPropia = ConfigGlobal::mi_dele() === $dlOrgNoF;

        return !$dlPropia && $oActividad->getId_tabla() === 'dl';
    }
}
