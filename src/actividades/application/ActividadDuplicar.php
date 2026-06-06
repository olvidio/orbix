<?php

namespace src\actividades\application;

use src\shared\config\ConfigGlobal;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\value_objects\StatusId;
use src\permisos\domain\XPermisos;
use function src\shared\domain\helpers\input_string_list;

/**
 * Duplica la primera actividad seleccionada dentro de la propia delegación.
 * Sustituye la lógica del antiguo case `duplicar` de actividad_update.php.
 */
final class ActividadDuplicar
{
    public function __construct(
        private ActividadDlRepositoryInterface $actividadDlRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        $a_sel = input_string_list($input, 'sel');

        if ($a_sel === []) {
            return _("no se ha seleccionado ninguna actividad");
        }

        $id_activ = (int) strtok($a_sel[0], '#');
        $oActividadAll = $this->actividadDlRepository->findById($id_activ);
        if ($oActividadAll === null) {
            return _("actividad no encontrada");
        }
        $dl = $oActividadAll->getDl_org();

        $oPerm = $_SESSION['oPerm'] ?? null;
        $tienePermOficina = $oPerm instanceof XPermisos && $oPerm->have_perm_oficina('des');

        if ($dl !== ConfigGlobal::mi_delef()
            && !($tienePermOficina && $dl === ConfigGlobal::mi_dele() . 'f')
        ) {
            return _("no se puede duplicar actividades que no sean de la propia dl");
        }

        $oActividad = $this->actividadDlRepository->findById($id_activ);
        if ($oActividad === null) {
            return _("actividad no encontrada");
        }
        $newId = $this->actividadDlRepository->getNewId();
        $newIdActiv = $this->actividadDlRepository->getNewIdActividad($newId);
        $oActividad->setId_activ($newIdActiv);
        $nom = _("dup") . ' ' . $oActividad->getNom_activ();
        $oActividad->setNom_activ($nom);
        $oActividad->setStatus(StatusId::PROYECTO);
        if ($this->actividadDlRepository->Guardar($oActividad) === false) {
            $error_txt = _("hay un error, no se ha guardado");
            $error_txt .= "\n" . $this->actividadDlRepository->getErrorTxt();
            return $error_txt;
        }

        return '';
    }
}
