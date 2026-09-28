<?php

namespace src\ubis\application;

use src\shared\security\HashB;
use src\ubis\domain\contracts\CasaPeriodoRepositoryInterface;

final class CalendarioPeriodosFormPeriodoData
{
    public function __construct(
        private CasaPeriodoRepositoryInterface $casaPeriodoRepository,
    ) {
    }
    /**
     * @return array<string, mixed>
     */
    public function execute(int $idItem): array
    {
        $repo = $this->casaPeriodoRepository;
        $oCasaPeriodo = $repo->findById($idItem);
        $ctx_guardar = HashB::sign('calendario_periodo_guardar', ['id_item' => $idItem, 'id_ubi' => 0]);
        $ctx_eliminar = HashB::sign('calendario_periodo_eliminar', ['id_item' => $idItem]);
        if ($oCasaPeriodo === null) {
            return [
                'id_item' => $idItem,
                'f_ini' => '',
                'f_fin' => '',
                'sel_sv' => '',
                'sel_sf' => '',
                'sel_res' => '',
                'ctx_guardar' => $ctx_guardar,
                'ctx_eliminar' => $ctx_eliminar,
            ];
        }
        $f_ini = $oCasaPeriodo->getF_ini()?->getFromLocal() ?? '';
        $f_fin = $oCasaPeriodo->getF_fin()?->getFromLocal() ?? '';
        $sfsv = $oCasaPeriodo->getSfsv();
        $sel_sv = $sfsv === 1 ? 'selected' : '';
        $sel_sf = $sfsv === 2 ? 'selected' : '';
        $sel_res = $sfsv === 3 ? 'selected' : '';

        return [
            'id_item' => $idItem,
            'f_ini' => $f_ini,
            'f_fin' => $f_fin,
            'sel_sv' => $sel_sv,
            'sel_sf' => $sel_sf,
            'sel_res' => $sel_res,
            'ctx_guardar' => $ctx_guardar,
            'ctx_eliminar' => $ctx_eliminar,
        ];
    }
}
