<?php

namespace src\actividadestudios\application;

use src\asistentes\application\services\AsistenteActividadService;

/**
 * Guarda el texto `observ` de un Asistente. Sustituye al case `observ`
 * de `update_3103.php`.
 */
final class AsistenteObserv
{
    public static function execute(array $input): string
    {
        $Qid_activ = (int) ($input['id_activ'] ?? 0);
        $Qid_nom = (int) ($input['id_pau'] ?? 0);
        if (empty($Qid_nom)) {
            $Qid_nom = (int) ($input['id_nom'] ?? 0);
        }
        $Qobserv = (string) ($input['observ'] ?? '');

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
        $oAsistente->setObserv($Qobserv);
        if ($AsistenteRepository->Guardar($oAsistente) === false) {
            return _("hay un error, no se ha guardado");
        }
        return '';
    }
}
