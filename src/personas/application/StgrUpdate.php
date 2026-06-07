<?php

namespace src\personas\application;

use src\personas\application\support\PersonaRepositoryResolver;

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
        try {
            $repository = $this->personaRepositoryResolver->repositorioPorIdTabla($id_tabla);
        } catch (\InvalidArgumentException) {
            return _("No existe la clase de la persona");
        }

        $oPersona = $repository->findById($id_nom);
        if ($oPersona === null) {
            return _("No se encuentra la persona");
        }

        $oPersona->setNivel_stgr(is_int($nivel_stgr) ? $nivel_stgr : (int)$nivel_stgr);
        if ($repository->Guardar($oPersona) === false) {
            $err = _("hay un error, no se ha guardado");
            $detalle = $repository->getErrorTxt();
            return $detalle ? $err . "\n" . $detalle : $err;
        }

        return '';
    }
}
