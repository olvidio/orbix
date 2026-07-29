<?php

declare(strict_types=1);

namespace src\cambios\application;

use src\personas\application\services\PersonaFinderService;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\DependencyResolver;

/**
 * Resuelve id_nom → nombre legible para textos de aviso.
 *
 * En DMZ (SV) no hay oDB/oDBP: solo cp_sacd vía comun. En interior usa
 * {@see PersonaFinderService} (lazy, para no exigir oDBP al construir avisos en DMZ).
 */
final class PersonaNombreParaAviso implements PersonaNombreParaAvisoInterface
{
    public function __construct(
        private readonly PersonaSacdRepositoryInterface $personaSacdRepository,
    ) {
    }

    public function resolve(int $id_nom): ?string
    {
        if ($id_nom <= 0) {
            return null;
        }

        if (!ConfigGlobal::is_dmz()) {
            $persona = DependencyResolver::get(PersonaFinderService::class)->findPersonaEnGlobal($id_nom);
            if ($persona !== null) {
                return $persona->getPrefApellidosNombre();
            }
        }

        return $this->personaSacdRepository->findById($id_nom)?->getPrefApellidosNombre();
    }
}
