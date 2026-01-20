<?php

use src\shared\application\listeners\RegistrarCambioListener;
use src\shared\domain\contracts\ColaMailRepositoryInterface;
use src\shared\domain\contracts\EventBusInterface;
use src\shared\domain\contracts\UnitOfWorkInterface;
use src\shared\domain\events\EntidadModificada;
use src\shared\infrastructure\InMemoryEventBus;
use src\shared\infrastructure\PdoUnitOfWork;
use src\shared\infrastructure\repositories\PgColaMailRepository;
use function DI\autowire;
use function DI\factory;
use function DI\get;

return [
// Mapeos de Interfaces a Implementaciones
    ColaMailRepositoryInterface::class => autowire(PgColaMailRepository::class),

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
    UnitOfWorkInterface::class => factory(function (EventBusInterface $eventBus) {
        $pdo = $GLOBALS['oDBE'];
        return new PdoUnitOfWork($pdo, $eventBus);
    }),
];
