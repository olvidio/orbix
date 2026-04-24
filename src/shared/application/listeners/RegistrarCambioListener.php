<?php

namespace src\shared\application\listeners;

use src\cambios\application\RegistrarCambio;
use src\shared\domain\events\EntidadModificada;

/**
 * Listener que reacciona a `EntidadModificada` y delega el registro
 * del cambio en el caso de uso `RegistrarCambio` del modulo cambios.
 */
class RegistrarCambioListener
{
    private RegistrarCambio $registrarCambio;

    public function __construct()
    {
        $this->registrarCambio = new RegistrarCambio();
    }

    public function __invoke(EntidadModificada $event): void
    {
        $this->registrarCambio->execute(
            $event->objeto,
            $event->tipoCambio,
            $event->idActiv,
            $event->datosNuevos,
            $event->datosActuales
        );
    }
}
