<?php

namespace src\asistentes\application;

use src\actividadestudios\domain\contracts\MatriculaRepositoryInterface;
use src\asistentes\application\services\AsistenteApplicationService;
use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\dossiers\domain\value_objects\DossierPk;

/**
 * Elimina un `Asistente` y las `Matricula`s asociadas, cerrando tambien el dossier 1301.
 *
 * Sustituye al case `eliminar` del antiguo `apps/asistentes/controller/update_3101.php`.
 */
final class AsistenteEliminar
{
    public function __construct(
        private AsistenteApplicationService $asistenteApplicationService,
        private MatriculaRepositoryInterface $matriculaRepository,
        private DossierRepositoryInterface $dossierRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        $Qpau = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'pau');
        $a_sel = \src\shared\domain\helpers\FuncTablasSupport::inputStringList($input, 'sel');

        $id_activ = 0;
        $id_nom = 0;
        if ($a_sel !== []) {
            $selKey = $a_sel[0];
            if ($Qpau === 'p') {
                $id_activ = (int) strtok($selKey, '#');
                $id_nom = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_pau');
            } elseif ($Qpau === 'a') {
                $id_nom = (int) strtok($selKey, '#');
                $id_activ = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_pau');
            }
        } else {
            $id_activ = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_activ');
            $id_nom = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_nom');
        }

        if ($id_activ === 0 || $id_nom === 0) {
            return _("faltan parametros id_activ / id_nom");
        }

        $asistenteAppService = $this->asistenteApplicationService;
        $oAsistente = $asistenteAppService->findById($id_activ, $id_nom);
        if ($oAsistente === null) {
            return sprintf(_("no se encuentra el asistente (id_nom: %s, id_activ: %s)"), $id_nom, $id_activ);
        }

        if ($oAsistente->perm_modificar() === false) {
            return _("los datos de asistencia los modifica la dl del asistente");
        }

        $msg_err = '';
        if ($asistenteAppService->eliminar($oAsistente) === false) {
            return _("hay un error, no se ha eliminado");
        }

        $this->cerrarDossier1301($id_nom);

        $MatriculaRepository = $this->matriculaRepository;
        foreach ($MatriculaRepository->getMatriculas(['id_activ' => $id_activ, 'id_nom' => $id_nom]) as $oMatricula) {
            if ($MatriculaRepository->Eliminar($oMatricula) === false) {
                $msg_err .= _("hay un error, no se ha eliminado");
            }
        }
        return $msg_err;
    }

    private function cerrarDossier1301(int $id_nom): void
    {
        $DossierRepository = $this->dossierRepository;
        $oDossier = $DossierRepository->findByPk(DossierPk::fromArray([
            'tabla' => 'p',
            'id_pau' => $id_nom,
            'id_tipo_dossier' => 1301,
        ]));
        if ($oDossier === null) {
            return;
        }
        $oDossier->cerrar();
        $DossierRepository->Guardar($oDossier);
    }
}
