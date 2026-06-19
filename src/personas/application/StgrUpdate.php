<?php

namespace src\personas\application;

use src\personas\application\support\PersonaRepositoryResolver;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaNaxRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\personas\domain\contracts\PersonaSSSCRepositoryInterface;
use src\personas\domain\entity\PersonaAgd;
use src\personas\domain\entity\PersonaEx;
use src\personas\domain\entity\PersonaN;
use src\personas\domain\entity\PersonaNax;
use src\personas\domain\entity\PersonaS;
use src\personas\domain\entity\PersonaSacd;
use src\personas\domain\entity\PersonaSSSC;

/**
 * Actualiza el nivel `stgr` de una persona.
 *
 * Migrado desde `apps/personas/controller/stgr_update.php` (slice 1 de
 * la migracion del modulo `personas`). El controlador HTTP (`src/personas/
 * infrastructure/ui/http/controllers/stgr_update.php`) lee el POST y
 * responde con `{success, mensaje}`; esta clase solo gestiona la persistencia.
 */
final class StgrUpdate
{
    public function __construct(
        private PersonaRepositoryResolver $personaRepositoryResolver,
    ) {
    }

    /**
     * @return string  cadena vacia si todo ha ido bien; mensaje de error si no.
     */
    public function execute(int $id_nom, string $id_tabla, int|string $nivel_stgr): string
    {
        $map = PersonaRepositoryResolver::entityTypeByIdTabla();
        $obj_pau = $map[$id_tabla] ?? null;
        if ($obj_pau === null || $obj_pau === 'PersonaDl') {
            return _("No existe la clase de la persona");
        }

        try {
            $repository = match ($obj_pau) {
                'PersonaN' => $this->personaRepositoryResolver->personaNRepository(),
                'PersonaAgd' => $this->personaRepositoryResolver->personaAgdRepository(),
                'PersonaNax' => $this->personaRepositoryResolver->personaNaxRepository(),
                'PersonaS' => $this->personaRepositoryResolver->personaSRepository(),
                'PersonaSSSC' => $this->personaRepositoryResolver->personaSSSCRepository(),
                'PersonaEx' => $this->personaRepositoryResolver->personaExRepository(),
                'PersonaSacd' => $this->personaRepositoryResolver->personaSacdRepository(),
                default => throw new \InvalidArgumentException("obj_pau '$obj_pau' no reconocido"),
            };
        } catch (\InvalidArgumentException) {
            return _("No existe la clase de la persona");
        }

        $oPersona = $repository->findById($id_nom);
        if ($oPersona === null) {
            return _("No se encuentra la persona");
        }

        $oPersona->setNivel_stgr(is_int($nivel_stgr) ? $nivel_stgr : (int)$nivel_stgr);
        if ($this->guardarPersona($repository, $obj_pau, $oPersona) === false) {
            $err = _("hay un error, no se ha guardado");
            $detalle = $repository->getErrorTxt();
            return $detalle ? $err . "\n" . $detalle : $err;
        }

        return '';
    }

    /**
     * @param PersonaNRepositoryInterface|PersonaAgdRepositoryInterface|PersonaNaxRepositoryInterface|PersonaSRepositoryInterface|PersonaSSSCRepositoryInterface|PersonaExRepositoryInterface|PersonaSacdRepositoryInterface $repository
     */
    private function guardarPersona(
        PersonaNRepositoryInterface|PersonaAgdRepositoryInterface|PersonaNaxRepositoryInterface|PersonaSRepositoryInterface|PersonaSSSCRepositoryInterface|PersonaExRepositoryInterface|PersonaSacdRepositoryInterface $repository,
        string $obj_pau,
        object $persona,
    ): bool {
        return match ($obj_pau) {
            'PersonaN' => $repository instanceof PersonaNRepositoryInterface && $persona instanceof PersonaN
                ? $repository->Guardar($persona) : false,
            'PersonaAgd' => $repository instanceof PersonaAgdRepositoryInterface && $persona instanceof PersonaAgd
                ? $repository->Guardar($persona) : false,
            'PersonaNax' => $repository instanceof PersonaNaxRepositoryInterface && $persona instanceof PersonaNax
                ? $repository->Guardar($persona) : false,
            'PersonaS' => $repository instanceof PersonaSRepositoryInterface && $persona instanceof PersonaS
                ? $repository->Guardar($persona) : false,
            'PersonaSSSC' => $repository instanceof PersonaSSSCRepositoryInterface && $persona instanceof PersonaSSSC
                ? $repository->Guardar($persona) : false,
            'PersonaEx' => $repository instanceof PersonaExRepositoryInterface && $persona instanceof PersonaEx
                ? $repository->Guardar($persona) : false,
            'PersonaSacd' => $repository instanceof PersonaSacdRepositoryInterface && $persona instanceof PersonaSacd
                ? $repository->Guardar($persona) : false,
            default => false,
        };
    }
}
