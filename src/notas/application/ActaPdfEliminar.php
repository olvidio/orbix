<?php

namespace src\notas\application;

use src\notas\domain\contracts\ActaRepositoryInterface;

/**
 * Elimina el PDF firmado asociado a un `Acta` (sin borrar el acta).
 */
final class ActaPdfEliminar
{
    public static function execute(array $input): string
    {
        $acta = (string)($input['acta_num'] ?? '');
        if (empty($acta)) {
            return _("No se encuentra el acta");
        }

        $ActaRepository = $GLOBALS['container']->get(ActaRepositoryInterface::class);
        $oActa = $ActaRepository->findById($acta);
        if ($oActa === null) {
            return _("No se encuentra el acta");
        }
        $oActa->setPdf('');
        if ($ActaRepository->Guardar($oActa) === false) {
            return (string)$ActaRepository->getErrorTxt();
        }

        return '';
    }
}
