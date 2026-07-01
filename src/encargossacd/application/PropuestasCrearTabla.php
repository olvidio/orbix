<?php

namespace src\encargossacd\application;

use src\encargossacd\domain\contracts\PropuestaEncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\PropuestaEncargoSacdRepositoryInterface;

final class PropuestasCrearTabla
{
    public function __construct(
        private PropuestaEncargoSacdRepositoryInterface $propuestaEncargoSacdRepository,
        private PropuestaEncargoSacdHorarioRepositoryInterface $propuestaHorarioRepository,
    ) {
    }

    /**
     * @return array{success: bool, mensaje?: string}
     */
    public function execute(): array
    {
        if (!$this->propuestaEncargoSacdRepository->crearTabla()) {
            return ['success' => false, 'mensaje' => _('No se puede crear la tabla')];
        }
        if (!$this->propuestaHorarioRepository->crearTabla()) {
            return ['success' => false, 'mensaje' => _('No se puede crear la tabla')];
        }

        return ['success' => true];
    }
}
