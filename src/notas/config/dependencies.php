<?php

use src\notas\domain\contracts\ActaDlRepositoryInterface;
use src\notas\domain\contracts\ActaExRepositoryInterface;
use src\notas\domain\contracts\ActaTribunalExRepositoryInterface;
use src\notas\domain\contracts\ActaTribunalRepositoryInterface;
use src\notas\domain\contracts\NotaRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaCertificadoRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaDlRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaOtraRegionStgrRepositoryInterface;
use src\notas\infrastructure\persistence\postgresql\PgPersonaNotaCertificadoRepository;
use src\notas\infrastructure\persistence\postgresql\PgPersonaNotaDlRepository;
use src\notas\infrastructure\persistence\postgresql\PgPersonaNotaRepository;
use src\notas\infrastructure\persistence\postgresql\PgPersonaNotaOtraRegionStgrRepository;
use src\notas\infrastructure\persistence\postgresql\PgActaDlRepository;
use src\notas\infrastructure\persistence\postgresql\PgActaExRepository;
use src\notas\infrastructure\persistence\postgresql\PgActaTribunalExRepository;
use src\notas\infrastructure\persistence\postgresql\PgActaTribunalRepository;
use src\notas\infrastructure\persistence\postgresql\PgNotaRepository;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\notas\infrastructure\persistence\postgresql\PgActaRepository;
use src\notas\domain\contracts\ActaTribunalDlRepositoryInterface;
use src\notas\infrastructure\persistence\postgresql\PgActaTribunalDlRepository;
use function DI\autowire;

return [
    // Mapeo simple: Interfaz => Clase
    NotaRepositoryInterface::class => autowire(PgNotaRepository::class),
    ActaRepositoryInterface::class => autowire(PgActaRepository::class),
    ActaDlRepositoryInterface::class => autowire(PgActaDlRepository::class),
    ActaExRepositoryInterface::class => autowire(PgActaExRepository::class),
    ActaTribunalRepositoryInterface::class => autowire(PgActaTribunalRepository::class),
    ActaTribunalDlRepositoryInterface::class => autowire(PgActaTribunalDlRepository::class),
    ActaTribunalExRepositoryInterface::class => autowire(PgActaTribunalExRepository::class),
    PersonaNotaDlRepositoryInterface::class => autowire(PgPersonaNotaDlRepository::class),
    PersonaNotaCertificadoRepositoryInterface::class => autowire(PgPersonaNotaCertificadoRepository::class),
    PersonaNotaRepositoryInterface::class => autowire(PgPersonaNotaRepository::class),
    PersonaNotaOtraRegionStgrRepositoryInterface::class => autowire(PgPersonaNotaOtraRegionStgrRepository::class),
];
