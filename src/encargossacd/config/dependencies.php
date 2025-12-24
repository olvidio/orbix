<?php

use src\encargossacd\domain\contracts\EncargoHorarioRepositoryInterface;
use src\encargossacd\infrastructure\repositories\PgEncargoHorarioRepository;
use src\encargossacd\domain\contracts\EncargoSacdObservRepositoryInterface;
use src\encargossacd\infrastructure\repositories\PgEncargoSacdObservRepository;
use src\encargossacd\domain\contracts\EncargoTextoRepositoryInterface;
use src\encargossacd\infrastructure\repositories\PgEncargoTextoRepository;
use src\encargossacd\domain\contracts\EncargoTipoRepositoryInterface;
use src\encargossacd\infrastructure\repositories\PgEncargoTipoRepository;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\infrastructure\repositories\PgEncargoRepository;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\encargossacd\infrastructure\repositories\PgEncargoSacdRepository;
use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\encargossacd\infrastructure\repositories\PgEncargoSacdHorarioRepository;
use function DI\autowire;

return [
// Mapeos de Interfaces a Implementaciones
    EncargoHorarioRepositoryInterface::class => autowire(PgEncargoHorarioRepository::class),
    EncargoSacdObservRepositoryInterface::class => autowire(PgEncargoSacdObservRepository::class),
    EncargoTextoRepositoryInterface::class => autowire(PgEncargoTextoRepository::class),
    EncargoTipoRepositoryInterface::class => autowire(PgEncargoTipoRepository::class),
    EncargoRepositoryInterface::class => autowire(PgEncargoRepository::class),
    EncargoSacdRepositoryInterface::class => autowire(PgEncargoSacdRepository::class),
    EncargoSacdHorarioRepositoryInterface::class => autowire(PgEncargoSacdHorarioRepository::class),
];
