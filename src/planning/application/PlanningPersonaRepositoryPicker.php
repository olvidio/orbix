<?php

namespace src\planning\application;

use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\shared\infrastructure\ProvidesRepositories;

/**
 * Elige el repositorio de personas según `obj_pau` (misma lógica que el controlador legacy).
 */
final class PlanningPersonaRepositoryPicker
{
    use ProvidesRepositories;

    public function get(string $obj_pau): object
    {
        if ($obj_pau === '' || $obj_pau === 'PersonaDl') {
            return $GLOBALS['container']->get(PersonaDlRepositoryInterface::class);
        }
        if ($obj_pau === 'PersonaSacd') {
            return $GLOBALS['container']->get(PersonaSacdRepositoryInterface::class);
        }

        return $this->getRepository($obj_pau);
    }

    public function getSafe(string $obj_pau): object
    {
        try {
            return $this->get($obj_pau);
        } catch (\InvalidArgumentException) {
            return $GLOBALS['container']->get(PersonaDlRepositoryInterface::class);
        }
    }
}
