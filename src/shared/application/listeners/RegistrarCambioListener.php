<?php

namespace src\shared\application\listeners;

use cambios\model\GestorAvisoCambios;
use src\shared\domain\events\EntidadModificada;

/**
 * Listener que registra los cambios en las entidades
 * Delega en GestorAvisoCambios para la persistencia del cambio
 */
class RegistrarCambioListener
{
    private GestorAvisoCambios $gestorCambios;

    public function __construct()
    {
        $this->gestorCambios = new GestorAvisoCambios();
    }

    /**
     * Procesa el evento EntidadModificada y registra el cambio
     *
     * @param EntidadModificada $event
     * @return void
     */
    public function __invoke(EntidadModificada $event): void
    {
        $this->gestorCambios->addCanvi(
            $event->objeto,
            $event->tipoCambio,
            $event->idActiv,
            $event->datosNuevos,
            $event->datosActuales
        );
    }
}
