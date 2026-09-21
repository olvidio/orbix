<?php

namespace src\dbextern\application;

use src\dbextern\domain\VincularIdMatch;

class UnirPersonaUseCase
{
    public function __construct(
        private VincularIdMatch $vincularIdMatch,
    ) {
    }

    /**
     * Vincula una persona de BDU con una persona de Orbix.
     *
     * @return string Error text (empty on success)
     */
    public function __invoke(int $id_nom_listas, int $id_orbix, string $tipo_persona): string
    {
        return $this->vincularIdMatch->vincular($id_nom_listas, $id_orbix, $tipo_persona);
    }
}
