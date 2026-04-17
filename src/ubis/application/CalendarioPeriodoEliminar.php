<?php

namespace src\ubis\application;

use src\ubis\domain\contracts\CasaPeriodoRepositoryInterface;

final class CalendarioPeriodoEliminar
{
    public static function execute(int $idItem): string
    {
        if ($idItem <= 0) {
            return _("no sé cuál he de borar");
        }
        $repo = $GLOBALS['container']->get(CasaPeriodoRepositoryInterface::class);
        $oCasaPeriodo = $repo->findById($idItem);
        if ($oCasaPeriodo === null) {
            return _("no se encuentra el periodo a borrar");
        }
        if ($repo->Eliminar($oCasaPeriodo) === false) {
            return _("hay un error, no se ha eliminado") . "\n" . $repo->getErrorTxt();
        }
        return '';
    }
}
