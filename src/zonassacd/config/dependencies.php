<?php

use src\zonassacd\domain\contracts\ZonaRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;
use src\zonassacd\infrastructure\persistence\postgresql\PgZonaRepository;
use src\zonassacd\domain\contracts\ZonaGrupoRepositoryInterface;
use src\zonassacd\infrastructure\persistence\postgresql\PgZonaGrupoRepository;
use src\zonassacd\infrastructure\persistence\postgresql\PgZonaSacdRepository;
use function DI\autowire;

return [
// Mapeos de Interfaces a Implementaciones
    ZonaRepositoryInterface::class => autowire(PgZonaRepository::class),
    ZonaGrupoRepositoryInterface::class => autowire(PgZonaGrupoRepository::class),
    ZonaSacdRepositoryInterface::class => autowire(PgZonaSacdRepository::class),
];
