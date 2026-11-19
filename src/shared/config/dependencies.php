<?php

use src\shared\application\copias\AvisarErrorCopias;
use src\shared\application\PermisoTablaGenerica;
use src\shared\application\AyudaConsultaRegistrar;
use src\shared\application\AyudaConsultasCambiarEstado;
use src\shared\application\AyudaConsultasListaData;
use src\shared\application\AyudaConsultaValorar;
use src\shared\application\copias\CopiasResincronizar;
use src\shared\application\listeners\RegistrarCambioListener;
use src\shared\domain\contracts\AyudaConsultaRepositoryInterface;
use src\shared\domain\contracts\ColaMailRepositoryInterface;
use src\shared\domain\contracts\ConnectionRepositoryFactoryInterface;
use src\shared\domain\contracts\EventBusInterface;
use src\shared\domain\contracts\UnitOfWorkInterface;
use src\shared\domain\events\EntidadModificada;
use src\shared\infrastructure\ConnectionRepositoryFactory;
use src\shared\infrastructure\InMemoryEventBus;
use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\PdoUnitOfWork;
use src\shared\infrastructure\persistence\postgresql\PgAyudaConsultaRepository;
use src\shared\infrastructure\persistence\postgresql\PgColaMailRepository;
use function DI\autowire;
use function DI\factory;
use function DI\get;

return [
// Mapeos de Interfaces a Implementaciones
    AvisarErrorCopias::class => autowire(AvisarErrorCopias::class),
    PermisoTablaGenerica::class => autowire(PermisoTablaGenerica::class),
    CopiasResincronizar::class => autowire(CopiasResincronizar::class),
    AyudaConsultaRegistrar::class => autowire(AyudaConsultaRegistrar::class),
    AyudaConsultaValorar::class => autowire(AyudaConsultaValorar::class),
    AyudaConsultasListaData::class => autowire(AyudaConsultasListaData::class),
    AyudaConsultasCambiarEstado::class => autowire(AyudaConsultasCambiarEstado::class),
    ColaMailRepositoryInterface::class => autowire(PgColaMailRepository::class),
    AyudaConsultaRepositoryInterface::class => autowire(PgAyudaConsultaRepository::class),
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
