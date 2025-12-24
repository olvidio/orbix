<?php

use src\zonassacd\domain\contracts\ZonaRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;
use src\zonassacd\infrastructure\repositories\PgZonaRepository;
use src\zonassacd\domain\contracts\ZonaGrupoRepositoryInterface;
use src\zonassacd\infrastructure\repositories\PgZonaGrupoRepository;
use src\zonassacd\infrastructure\repositories\PgZonaSacdRepository;
use function DI\autowire;

return [
// Mapeos de Interfaces a Implementaciones
    ZonaRepositoryInterface::class => autowire(PgZonaRepository::class),
    ZonaGrupoRepositoryInterface::class => autowire(PgZonaGrupoRepository::class),
    ZonaSacdRepositoryInterface::class => autowire(PgZonaSacdRepository::class),
];
