<?php

use src\shared\application\listeners\RegistrarCambioListener;
use src\shared\domain\contracts\EventBusInterface;
use src\shared\domain\events\EntidadModificada;
use src\shared\infrastructure\InMemoryEventBus;
use function DI\autowire;
use function DI\factory;

return [
// Event Bus - Infraestructura compartida
    EventBusInterface::class => factory(function () {
        $eventBus = new InMemoryEventBus();

        // Registrar listeners
        $eventBus->subscribe(
            EntidadModificada::class,
            new RegistrarCambioListener()
        );

        return $eventBus;
    }),
];
