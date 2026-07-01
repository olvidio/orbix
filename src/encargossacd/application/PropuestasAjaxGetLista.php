<?php

namespace src\encargossacd\application;

use src\encargossacd\application\services\PropuestasEncargosUbiHtml;
use src\encargossacd\domain\contracts\PropuestaEncargoSacdRepositoryInterface;

final class PropuestasAjaxGetLista
{
    public function __construct(
        private PropuestaEncargoSacdRepositoryInterface $propuestaEncargoSacdRepository,
        private PropuestasCentrosPorFiltro $centrosPorFiltro,
        private PropuestasEncargosUbiHtml $encargosUbiHtml,
    ) {
    }

    /**
     * @return array{success: bool, lista?: string, mensaje?: string}
     */
    public function execute(int $filtro_ctr): array
    {
        if (!$this->propuestaEncargoSacdRepository->existenLasTablas()) {
            return ['success' => false, 'mensaje' => _('Debe crear la tabla de propuestas')];
        }

        $html = '';
        foreach ($this->centrosPorFiltro->execute($filtro_ctr) as $oCentro) {
            $html .= $oCentro->getNombre_ubi();
            $html .= '<br>';
            $html .= $this->encargosUbiHtml->editable($oCentro->getId_ubi());
        }

        return ['success' => true, 'lista' => $html];
    }
}
