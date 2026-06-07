<?php

namespace src\misas\application;

use src\zonassacd\domain\contracts\ZonaRepositoryInterface;

class ModificarInicialesSacdZonaData
{

    public function __construct(
        private readonly ZonaRepositoryInterface $zonaRepository,
    ) {
    }
    /**
     * @return array<string, mixed>
     */

    public function getData(): array
    {

        return [
            'a_opciones' => $this->zonaRepository->getArrayZonas(),
        ];
    }
}
