<?php

namespace src\planning\application;

use src\personas\application\support\PersonaRepositoryResolver;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaNaxRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\personas\domain\contracts\PersonaSSSCRepositoryInterface;

/**
 * Elige el repositorio de personas según `obj_pau` (misma lógica que el controlador legacy).
 */
final class PlanningPersonaRepositoryPicker
{
    public function __construct(
        private PersonaDlRepositoryInterface $personaDlRepository,
        private PersonaSacdRepositoryInterface $personaSacdRepository,
        private PersonaRepositoryResolver $personaRepositoryResolver,
    ) {
    }

    /**
     * @return PersonaDlRepositoryInterface|PersonaSacdRepositoryInterface|PersonaNRepositoryInterface|PersonaAgdRepositoryInterface|PersonaNaxRepositoryInterface|PersonaSRepositoryInterface|PersonaSSSCRepositoryInterface|PersonaExRepositoryInterface
     */
    public function get(string $obj_pau): PersonaDlRepositoryInterface|PersonaSacdRepositoryInterface|PersonaNRepositoryInterface|PersonaAgdRepositoryInterface|PersonaNaxRepositoryInterface|PersonaSRepositoryInterface|PersonaSSSCRepositoryInterface|PersonaExRepositoryInterface
    {
        if ($obj_pau === '' || $obj_pau === 'PersonaDl') {
            return $this->personaDlRepository;
        }
        if ($obj_pau === 'PersonaSacd') {
            return $this->personaSacdRepository;
        }

        return $this->personaRepositoryResolver->repositorio($obj_pau);
    }

    /**
     * @return PersonaDlRepositoryInterface|PersonaSacdRepositoryInterface|PersonaNRepositoryInterface|PersonaAgdRepositoryInterface|PersonaNaxRepositoryInterface|PersonaSRepositoryInterface|PersonaSSSCRepositoryInterface|PersonaExRepositoryInterface
     */
    public function getSafe(string $obj_pau): PersonaDlRepositoryInterface|PersonaSacdRepositoryInterface|PersonaNRepositoryInterface|PersonaAgdRepositoryInterface|PersonaNaxRepositoryInterface|PersonaSRepositoryInterface|PersonaSSSCRepositoryInterface|PersonaExRepositoryInterface
    {
        try {
            return $this->get($obj_pau);
        } catch (\InvalidArgumentException) {
            return $this->personaDlRepository;
        }
    }
}
