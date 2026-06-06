<?php

use src\asignaturas\application\AsignaturasConSeparadorOpcionesData;
use src\asignaturas\application\AsignaturasMapData;
use src\asignaturas\domain\InfoAsignaturas;
use src\asignaturas\domain\InfoAsignaturaTipo;
use src\asignaturas\domain\InfoDepartamentos;
use src\asignaturas\domain\InfoOpcionales;
use src\asignaturas\domain\InfoSectores;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaTipoRepositoryInterface;
use src\asignaturas\domain\contracts\DepartamentoRepositoryInterface;
use src\asignaturas\domain\contracts\SectorRepositoryInterface;
use src\asignaturas\infrastructure\persistence\postgresql\PgAsignaturaRepository;
use src\asignaturas\infrastructure\persistence\postgresql\PgAsignaturaTipoRepository;
use src\asignaturas\infrastructure\persistence\postgresql\PgDepartamentoRepository;
use src\asignaturas\infrastructure\persistence\postgresql\PgSectorRepository;
use function DI\autowire;

return [
    // Mapeos de Interfaces a Implementaciones
    AsignaturaRepositoryInterface::class => autowire(PgAsignaturaRepository::class),
    AsignaturaTipoRepositoryInterface::class => autowire(PgAsignaturaTipoRepository::class),
    DepartamentoRepositoryInterface::class => autowire(PgDepartamentoRepository::class),
    SectorRepositoryInterface::class => autowire(PgSectorRepository::class),

    // Casos de uso / Application classes
    AsignaturasMapData::class => autowire(AsignaturasMapData::class),
    AsignaturasConSeparadorOpcionesData::class => autowire(AsignaturasConSeparadorOpcionesData::class),

    // Domain Info* (dossier / mod_tabla)
    InfoAsignaturas::class => autowire(InfoAsignaturas::class),
    InfoAsignaturaTipo::class => autowire(InfoAsignaturaTipo::class),
    InfoDepartamentos::class => autowire(InfoDepartamentos::class),
    InfoOpcionales::class => autowire(InfoOpcionales::class),
    InfoSectores::class => autowire(InfoSectores::class),
];
