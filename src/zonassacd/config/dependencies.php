<?php

use src\zonassacd\application\ZonaCtrLista;
use src\zonassacd\application\ZonaCtrPage;
use src\zonassacd\application\ZonaCtrUpdate;
use src\zonassacd\application\ZonaSacdLista;
use src\zonassacd\application\ZonaSacdListaTot;
use src\zonassacd\application\ZonaSacdPage;
use src\zonassacd\application\ZonaSacdUpdate;
use src\zonassacd\domain\InfoZona;
use src\zonassacd\domain\contracts\ZonaGrupoRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;
use src\zonassacd\infrastructure\persistence\postgresql\PgZonaGrupoRepository;
use src\zonassacd\infrastructure\persistence\postgresql\PgZonaRepository;
use src\zonassacd\infrastructure\persistence\postgresql\PgZonaSacdRepository;
use function DI\autowire;

return [
    // Mapeos de Interfaces a Implementaciones
    ZonaRepositoryInterface::class => autowire(PgZonaRepository::class),
    ZonaGrupoRepositoryInterface::class => autowire(PgZonaGrupoRepository::class),
    ZonaSacdRepositoryInterface::class => autowire(PgZonaSacdRepository::class),

    // Casos de uso / Application classes
    ZonaSacdUpdate::class => autowire(ZonaSacdUpdate::class),
    ZonaCtrUpdate::class => autowire(ZonaCtrUpdate::class),
    ZonaSacdPage::class => autowire(ZonaSacdPage::class),
    ZonaCtrPage::class => autowire(ZonaCtrPage::class),
    ZonaSacdLista::class => autowire(ZonaSacdLista::class),
    ZonaSacdListaTot::class => autowire(ZonaSacdListaTot::class),
    ZonaCtrLista::class => autowire(ZonaCtrLista::class),
    InfoZona::class => autowire(InfoZona::class),
];
