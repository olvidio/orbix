<?php

namespace src\actividadestudios\application;

use src\asistentes\application\services\AsistenteActividadService;
use function core\is_true;

/**
 * Marca el flag `est_ok` (plan de estudios confirmado) de un Asistente.
 * Sustituye al case `plan` de `update_3103.php`.
 */
final class AsistentePlanEstOk
{
    public static function execute(array $input): string
    {
        $Qid_activ = (int) ($input['id_activ'] ?? 0);
        $Qid_nom = (int) ($input['id_pau'] ?? 0);
        if (empty($Qid_nom)) {
            $Qid_nom = (int) ($input['id_nom'] ?? 0);
        }
        $Qest_ok = (string) ($input['est_ok'] ?? '');

        if (empty($Qid_activ) || empty($Qid_nom)) {
            return _("falta id_activ o id_nom");
        }

        $AsistenteActividadService = $GLOBALS['container']->get(AsistenteActividadService::class);
        $AsistenteRepositoryInterface = $AsistenteActividadService->getRepoAsistente($Qid_nom, $Qid_activ);
        $AsistenteRepository = $GLOBALS['container']->get($AsistenteRepositoryInterface);
        $oAsistente = $AsistenteRepository->findById($Qid_activ, $Qid_nom);
        if ($oAsistente === null) {
            return _("no encuentro al asistente");
        }
        $oAsistente->setEst_ok(is_true($Qest_ok));
        if ($AsistenteRepository->Guardar($oAsistente) === false) {
            return _("hay un error, no se ha guardado");
        }
        return '';
    }
}
