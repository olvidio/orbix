<?php

use src\shared\application\listeners\RegistrarCambioListener;
use src\shared\domain\contracts\EventBusInterface;
use src\shared\domain\contracts\UnitOfWorkInterface;
use src\shared\domain\events\EntidadModificada;
use src\shared\infrastructure\InMemoryEventBus;
use src\shared\infrastructure\PdoUnitOfWork;
use function DI\autowire;
use function DI\factory;
use function DI\get;

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

    // Unit of Work - Gestión de transacciones y eventos
    UnitOfWorkInterface::class => factory(function () {
        $pdo = $GLOBALS['oDBE'];
        $eventBus = \DI\ContainerSingleton::getInstance()->get(EventBusInterface::class);
        return new PdoUnitOfWork($pdo, $eventBus);
    }),
];
