<?php

namespace src\encargossacd\application;

use src\encargossacd\application\services\PropuestasEncargosUbiHtml;
use src\encargossacd\domain\contracts\PropuestaEncargoSacdRepositoryInterface;

final class PropuestasListaEncData
{
    public function __construct(
        private PropuestaEncargoSacdRepositoryInterface $propuestaEncargoSacdRepository,
        private PropuestasCentrosPorFiltro $centrosPorFiltro,
        private PropuestasEncargosUbiHtml $encargosUbiHtml,
    ) {
    }

    /**
     * @return array{html: string, error?: string}
     */
    public function execute(int $filtro_ctr): array
    {
        if (!$this->propuestaEncargoSacdRepository->existenLasTablas()) {
            return ['html' => '', 'error' => _('Debe crear la tabla de propuestas')];
        }

        $html = '';
        foreach ($this->centrosPorFiltro->execute($filtro_ctr, todosEnDefault: true) as $oCentro) {
            $html .= $this->encargosUbiHtml->simple($oCentro->getId_ubi());
        }

        return ['html' => $html];
    }
}
