<?php

namespace src\ubis\application;

use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CasaPeriodoRepositoryInterface;
use src\ubis\domain\entity\CasaPeriodo;

final class CalendarioPeriodoGuardar
{
    public function __construct(
        private CasaPeriodoRepositoryInterface $casaPeriodoRepository,
    ) {
    }

    public function execute(int $idItem, int $idUbi, string $fIni, string $fFin, int $sfsv): string
    {
        $repo = $this->casaPeriodoRepository;
        if ($idItem > 0) {
            $oCasaPeriodo = $repo->findById($idItem);
            if ($oCasaPeriodo === null) {
                $oCasaPeriodo = new CasaPeriodo();
                $oCasaPeriodo->setId_item($repo->getNewId());
            }
        } else {
            $oCasaPeriodo = new CasaPeriodo();
            $oCasaPeriodo->setId_item($repo->getNewId());
        }
        if ($idUbi > 0) {
            $oCasaPeriodo->setId_ubi($idUbi);
        }
        if ($fIni !== '') {
            $dtIni = DateTimeLocal::createFromLocal($fIni);
            $oCasaPeriodo->setF_ini($dtIni instanceof DateTimeLocal ? $dtIni : null);
        }
        if ($fFin !== '') {
            $dtFin = DateTimeLocal::createFromLocal($fFin);
            $oCasaPeriodo->setF_fin($dtFin instanceof DateTimeLocal ? $dtFin : null);
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
