<?php

use src\dossiers\domain\contracts\TipoDossierRepositoryInterface;
use src\dossiers\infrastructure\persistence\postgresql\PgTipoDossierRepository;
use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\dossiers\infrastructure\persistence\postgresql\PgDossierRepository;
use function DI\autowire;

return [
// Mapeos de Interfaces a Implementaciones
    TipoDossierRepositoryInterface::class => autowire(PgTipoDossierRepository::class),
    DossierRepositoryInterface::class => autowire(PgDossierRepository::class),
];
