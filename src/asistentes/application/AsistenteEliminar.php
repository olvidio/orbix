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
    public static function execute(array $input): string
    {
        $Qpau = (string) ($input['pau'] ?? '');
        $a_sel = (array) ($input['sel'] ?? []);

        $id_activ = 0;
        $id_nom = 0;
        if (!empty($a_sel)) {
            if ($Qpau === 'p') {
                $id_activ = (int) strtok($a_sel[0], '#');
                $id_nom = (int) ($input['id_pau'] ?? 0);
            } elseif ($Qpau === 'a') {
                $id_nom = (int) strtok($a_sel[0], '#');
                $id_activ = (int) ($input['id_pau'] ?? 0);
            }
        } else {
            $id_activ = (int) ($input['id_activ'] ?? 0);
            $id_nom = (int) ($input['id_nom'] ?? 0);
        }

        if ($id_activ === 0 || $id_nom === 0) {
            return _("faltan parametros id_activ / id_nom");
        }

        $asistenteAppService = $GLOBALS['container']->get(AsistenteApplicationService::class);
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

        self::cerrarDossier1301($id_nom);

        $MatriculaRepository = $GLOBALS['container']->get(MatriculaRepositoryInterface::class);
        foreach ($MatriculaRepository->getMatriculas(['id_activ' => $id_activ, 'id_nom' => $id_nom]) as $oMatricula) {
            if ($oMatricula->DBEliminar() === false) {
                $msg_err .= _("hay un error, no se ha eliminado");
            }
        }
        return $msg_err;
    }

    private static function cerrarDossier1301(int $id_nom): void
    {
        $DossierRepository = $GLOBALS['container']->get(DossierRepositoryInterface::class);
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
