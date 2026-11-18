<?php

namespace src\personas\application;

use src\personas\application\support\PersonaRepositoryResolver;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaDePasoDatosRepositoryInterface;
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
use src\shared\config\ConfigGlobal;

/**
 * Elimina una persona si pertenece a la dl del usuario actual; una persona de paso, si no tiene
 * datos en ninguna dl ({@see PersonaDePasoDatosRepositoryInterface}).
 *
 * Migrado desde la rama "eliminar" de `apps/personas/controller/personas_update.php`
 * (slice 2 de la migracion del modulo `personas`).
 */
final class PersonaEliminar
{
    public function __construct(
        private PersonaRepositoryResolver $personaRepositoryResolver,
        private PersonaDePasoDatosRepositoryInterface $datosDePaso,
    ) {
    }

    /**
     * @return string cadena vacia si ok, mensaje de error si falla o no tiene permiso.
     */
    public function execute(int $id_nom, string $obj_pau): string
    {
        if (empty($id_nom)) {
            return _("No se ha pasado el id_nom");
        }

        try {
            $repoPersona = match ($obj_pau) {
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

        $oPersona = $repoPersona->findById($id_nom);
        if ($oPersona === null) {
            return _("No se encuentra la persona");
        }

        if ($obj_pau === 'PersonaEx') {
            // Una persona de paso es de fuera y su tabla es común a todas las dl: se borra si no
            // tiene datos en ninguna (asistencias, cargos, encargos, notas…).
            try {
                $datos = $this->datosDePaso->datosDe($id_nom);
            } catch (\RuntimeException $e) {
                // Sin poder mirar todas las tablas no se borra; el mensaje dice qué ha fallado.
                return sprintf(
                    _("No se ha eliminado: no se ha podido comprobar si la persona de paso tiene datos (%s)"),
                    $e->getMessage()
                );
            }
            if ($datos !== []) {
                return sprintf(
                    _("No se ha eliminado: esta persona de paso tiene %s"),
                    implode(', ', array_map(self::etiquetaDatos(...), $datos))
                );
            }
        } elseif (ConfigGlobal::mi_delef() !== $oPersona->getDl()) {
            // El resto, solo personas de la misma dl.
            return _("No se ha eliminado, porque no es de mi dl");
        }

        if ($this->eliminarPersona($repoPersona, $obj_pau, $oPersona) === false) {
            $err = _("hay un error, no se ha eliminado");
            $detalle = $repoPersona->getErrorTxt();
            return $detalle ? $err . "\n" . $detalle : $err;
        }

        return '';
    }

    private static function etiquetaDatos(string $tipo): string
    {
        return match ($tipo) {
            PersonaDePasoDatosRepositoryInterface::ASISTENCIAS => _("asistencias"),
            PersonaDePasoDatosRepositoryInterface::CARGOS => _("cargos"),
            PersonaDePasoDatosRepositoryInterface::ENCARGOS => _("encargos"),
            PersonaDePasoDatosRepositoryInterface::NOTAS => _("notas"),
            PersonaDePasoDatosRepositoryInterface::MATRICULAS => _("matrículas"),
            PersonaDePasoDatosRepositoryInterface::DOCENCIA => _("docencia"),
            PersonaDePasoDatosRepositoryInterface::ZONAS => _("zonas"),
            default => $tipo,
        };
    }

    /**
     * @param PersonaNRepositoryInterface|PersonaAgdRepositoryInterface|PersonaNaxRepositoryInterface|PersonaSRepositoryInterface|PersonaSSSCRepositoryInterface|PersonaExRepositoryInterface|PersonaSacdRepositoryInterface $repo
     */
    private function eliminarPersona(
        PersonaNRepositoryInterface|PersonaAgdRepositoryInterface|PersonaNaxRepositoryInterface|PersonaSRepositoryInterface|PersonaSSSCRepositoryInterface|PersonaExRepositoryInterface|PersonaSacdRepositoryInterface $repo,
        string $obj_pau,
        object $persona,
    ): bool {
        return match ($obj_pau) {
            'PersonaN' => $repo instanceof PersonaNRepositoryInterface && $persona instanceof PersonaN
                ? $repo->Eliminar($persona) : false,
            'PersonaAgd' => $repo instanceof PersonaAgdRepositoryInterface && $persona instanceof PersonaAgd
                ? $repo->Eliminar($persona) : false,
            'PersonaNax' => $repo instanceof PersonaNaxRepositoryInterface && $persona instanceof PersonaNax
                ? $repo->Eliminar($persona) : false,
            'PersonaS' => $repo instanceof PersonaSRepositoryInterface && $persona instanceof PersonaS
                ? $repo->Eliminar($persona) : false,
            'PersonaSSSC' => $repo instanceof PersonaSSSCRepositoryInterface && $persona instanceof PersonaSSSC
                ? $repo->Eliminar($persona) : false,
            'PersonaEx' => $repo instanceof PersonaExRepositoryInterface && $persona instanceof PersonaEx
                ? $repo->Eliminar($persona) : false,
            'PersonaSacd' => $repo instanceof PersonaSacdRepositoryInterface && $persona instanceof PersonaSacd
                ? $repo->Eliminar($persona) : false,
            default => false,
        };
    }
}
