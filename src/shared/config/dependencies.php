<?php

use src\shared\application\listeners\RegistrarCambioListener;
use src\shared\domain\contracts\ColaMailRepositoryInterface;
use src\shared\domain\contracts\ConnectionRepositoryFactoryInterface;
use src\shared\domain\contracts\EventBusInterface;
use src\shared\domain\contracts\UnitOfWorkInterface;
use src\shared\domain\events\EntidadModificada;
use src\shared\infrastructure\ConnectionRepositoryFactory;
use src\shared\infrastructure\InMemoryEventBus;
use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\PdoUnitOfWork;
use src\shared\infrastructure\persistence\postgresql\PgColaMailRepository;
use function DI\autowire;
use function DI\factory;
use function DI\get;

return [
// Mapeos de Interfaces a Implementaciones
    ColaMailRepositoryInterface::class => autowire(PgColaMailRepository::class),
    ConnectionRepositoryFactoryInterface::class => autowire(ConnectionRepositoryFactory::class),

// Event Bus - Infraestructura compartida
    RegistrarCambioListener::class => autowire(RegistrarCambioListener::class),

    EventBusInterface::class => factory(function (RegistrarCambioListener $registrarCambioListener) {
        $eventBus = new InMemoryEventBus();

        $eventBus->subscribe(
            EntidadModificada::class,
            $registrarCambioListener
        );

        return $eventBus;
    }),

    // Unit of Work - Gestión de transacciones y eventos
    UnitOfWorkInterface::class => factory(function (EventBusInterface $eventBus) {
        return new PdoUnitOfWork(GlobalPdo::get('oDBE'), $eventBus);
    }),
];
