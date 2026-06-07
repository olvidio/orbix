<?php

use src\dossiers\application\DossiersListaFichasData;
use src\dossiers\application\DossiersVerPantallaData;
use src\dossiers\application\DossierTipoFileSuffixResolver;
use src\dossiers\application\DossierTipoPublicUrls;
use src\dossiers\application\PermDossierVerFormData;
use src\dossiers\application\PermDossiersListaData;
use src\dossiers\application\PermisoDossier;
use src\dossiers\application\support\DossierFichaSelectRunner;
use src\dossiers\application\TipoDossierEliminar;
use src\dossiers\application\TipoDossierGuardar;
use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\dossiers\domain\contracts\TipoDossierRepositoryInterface;
use src\dossiers\infrastructure\persistence\postgresql\PgDossierRepository;
use src\dossiers\infrastructure\persistence\postgresql\PgTipoDossierRepository;
use function DI\autowire;

return [
    TipoDossierRepositoryInterface::class => autowire(PgTipoDossierRepository::class),
    DossierRepositoryInterface::class => autowire(PgDossierRepository::class),

    DossierTipoFileSuffixResolver::class => static fn (): DossierTipoFileSuffixResolver => DossierTipoFileSuffixResolver::fromDefaultProjectRoot(),

    DossierFichaSelectRunner::class => autowire(DossierFichaSelectRunner::class),
    DossierTipoPublicUrls::class => autowire(DossierTipoPublicUrls::class),
    DossiersListaFichasData::class => autowire(DossiersListaFichasData::class),
    DossiersVerPantallaData::class => autowire(DossiersVerPantallaData::class),
    PermDossierVerFormData::class => autowire(PermDossierVerFormData::class),
    PermDossiersListaData::class => autowire(PermDossiersListaData::class),
    PermisoDossier::class => autowire(PermisoDossier::class),
    TipoDossierEliminar::class => autowire(TipoDossierEliminar::class),
    TipoDossierGuardar::class => autowire(TipoDossierGuardar::class),
];
