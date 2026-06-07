<?php

declare(strict_types=1);

namespace src\notas\application;

use src\actividades\domain\value_objects\NivelStgrId;
use src\notas\domain\value_objects\NotaSituacion;

/**
 * Constantes para {@see frontend/notas/controller/comprobar_notas.php} (SQL legacy),
 * serializadas desde VO para no duplicar números mágicos en frontend.
 */
final class ComprobarNotasConstantsData
{
    /**
     * @return array{vo: array{NivelStgrId: array<string, int>, NotaSituacion: array<string, int>}}
     */
    public function execute(): array
    {
        return [
            'vo' => [
                'NivelStgrId' => [
                    'B' => NivelStgrId::B,
                    'C1' => NivelStgrId::C1,
                    'C2' => NivelStgrId::C2,
                    'R' => NivelStgrId::R,
                    'N' => NivelStgrId::N,
                ],
                'NotaSituacion' => [
                    'NUMERICA' => NotaSituacion::NUMERICA,
                    'CURSADA' => NotaSituacion::CURSADA,
                ],
            ],
        ];
    }
}
