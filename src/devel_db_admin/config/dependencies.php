<?php

declare(strict_types=1);

use src\devel_db_admin\application\AbsorberEsquema;
use src\devel_db_admin\application\ApptablesAppsData;
use src\devel_db_admin\application\ApptablesUpdate;
use src\devel_db_admin\application\CorregirEstadoRenombrarEsquema;
use src\devel_db_admin\application\CrearEsquema;
use src\devel_db_admin\application\DbPropiedadesFormData;
use src\devel_db_admin\application\MigracionesEjecutar;
use src\devel_db_admin\application\MigracionesListaData;
use src\devel_db_admin\application\MigracionesQuitarRegistro;
use src\devel_db_admin\application\RenombrarEsquema;
use src\devel_db_admin\domain\contracts\MigracionAplicadaRepositoryInterface;
use src\devel_db_admin\infrastructure\persistence\postgresql\PgMigracionAplicadaRepository;
use function DI\autowire;

return [
    MigracionAplicadaRepositoryInterface::class => autowire(PgMigracionAplicadaRepository::class),

    AbsorberEsquema::class => autowire(AbsorberEsquema::class),
    ApptablesAppsData::class => autowire(ApptablesAppsData::class),
    ApptablesUpdate::class => autowire(ApptablesUpdate::class),
    CorregirEstadoRenombrarEsquema::class => autowire(CorregirEstadoRenombrarEsquema::class),
    CrearEsquema::class => autowire(CrearEsquema::class),
    DbPropiedadesFormData::class => autowire(DbPropiedadesFormData::class),
    MigracionesEjecutar::class => autowire(MigracionesEjecutar::class),
    MigracionesListaData::class => autowire(MigracionesListaData::class),
    MigracionesQuitarRegistro::class => autowire(MigracionesQuitarRegistro::class),
    RenombrarEsquema::class => autowire(RenombrarEsquema::class),
];
