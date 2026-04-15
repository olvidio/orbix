<?php

namespace src\ubis\application;

use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CasaPeriodoRepositoryInterface;
use src\ubis\domain\entity\CasaPeriodo;

final class CalendarioPeriodoGuardar
{
    public static function execute(int $idItem, int $idUbi, string $fIni, string $fFin, int $sfsv): string
    {
        $repo = $GLOBALS['container']->get(CasaPeriodoRepositoryInterface::class);
        if ($idItem > 0) {
            $oCasaPeriodo = $repo->findById($idItem);
        } else {
            $oCasaPeriodo = new CasaPeriodo();
            $oCasaPeriodo->setId_item($repo->getNewId());
        }
        if ($idUbi > 0) {
            $oCasaPeriodo->setId_ubi($idUbi);
        }
        if ($fIni !== '') {
            $oCasaPeriodo->setF_ini(DateTimeLocal::createFromLocal($fIni));
        }
        if ($fFin !== '') {
            $oCasaPeriodo->setF_fin(DateTimeLocal::createFromLocal($fFin));
        }
        if ($sfsv > 0) {
            $oCasaPeriodo->setSfsv($sfsv);
        }
        if ($repo->Guardar($oCasaPeriodo) === false) {
            return _("hay un error, no se ha guardado") . "\n" . $repo->getErrorTxt();
        }
        return '';
    }
}
