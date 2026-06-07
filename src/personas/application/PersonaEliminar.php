<?php

namespace src\personas\application;

use src\personas\application\support\PersonaRepositoryResolver;
use src\shared\config\ConfigGlobal;

/**
 * Elimina una persona si pertenece a la dl del usuario actual.
 *
 * Migrado desde la rama "eliminar" de `apps/personas/controller/personas_update.php`
 * (slice 2 de la migracion del modulo `personas`).
 */
final class PersonaEliminar
{
    public function __construct(
        private PersonaRepositoryResolver $personaRepositoryResolver,
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
            $repoPersona = $this->personaRepositoryResolver->repositorio($obj_pau);
        } catch (\InvalidArgumentException) {
            return _("No existe la clase de la persona");
        }

        $oPersona = $repoPersona->findById($id_nom);
        if ($oPersona === null) {
            return _("No se encuentra la persona");
        }

        // Solo se permite borrar a personas de la misma dl.
        if (ConfigGlobal::mi_delef() !== $oPersona->getDl()) {
            return _("No se ha eliminado, porque no es de mi dl");
        }

        if ($repoPersona->Eliminar($oPersona) === false) {
            $err = _("hay un error, no se ha eliminado");
            $detalle = $repoPersona->getErrorTxt();
            return $detalle ? $err . "\n" . $detalle : $err;
        }

        return '';
    }
}
