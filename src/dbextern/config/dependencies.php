<?php

use src\dbextern\application\support\SincroDBFactory;
use src\dbextern\application\BajaPersonaUseCase;
use src\dbextern\application\CrearPersonaDesdeListasUseCase;
use src\dbextern\application\CrearTodosDesdeListasUseCase;
use src\dbextern\application\DesunirPersonaUseCase;
use src\dbextern\application\RefrescarBduUseCase;
use src\dbextern\application\SincroIndexData;
use src\dbextern\application\SincroPersonas;
use src\dbextern\application\TrasladarPersonaUseCase;
use src\dbextern\application\UnirPersonaUseCase;
use src\dbextern\application\VerDesaparecidosDeListasData;
use src\dbextern\application\VerDesaparecidosDeOrbixData;
use src\dbextern\application\VerListasData;
use src\dbextern\application\VerOrbixData;
use src\dbextern\application\VerOrbixOtraDlData;
use src\dbextern\application\VerTrasladosData;
use src\dbextern\domain\contracts\IdMatchPersonaRepositoryInterface;
use src\dbextern\domain\contracts\PersonaBDURepositoryInterface;
use src\dbextern\domain\CopiarBDU;
use src\dbextern\domain\SincroDB;
use src\dbextern\infrastructure\persistence\postgresql\OdbcDlListasRepository;
use src\dbextern\infrastructure\persistence\postgresql\PgIdMatchPersonaRepository;
use src\dbextern\infrastructure\persistence\postgresql\PgPersonaBDURepository;
use function DI\autowire;
use function DI\create;

return [
    PersonaBDURepositoryInterface::class => autowire(PgPersonaBDURepository::class),
    IdMatchPersonaRepositoryInterface::class => autowire(PgIdMatchPersonaRepository::class),

    OdbcDlListasRepository::class => autowire(OdbcDlListasRepository::class),
    CopiarBDU::class => autowire(CopiarBDU::class),
    SincroDB::class => create(SincroDB::class),
    SincroDBFactory::class => autowire(SincroDBFactory::class),

    BajaPersonaUseCase::class => autowire(BajaPersonaUseCase::class),
    CrearPersonaDesdeListasUseCase::class => autowire(CrearPersonaDesdeListasUseCase::class),
    CrearTodosDesdeListasUseCase::class => autowire(CrearTodosDesdeListasUseCase::class),
    DesunirPersonaUseCase::class => autowire(DesunirPersonaUseCase::class),
    RefrescarBduUseCase::class => autowire(RefrescarBduUseCase::class),
    SincroIndexData::class => autowire(SincroIndexData::class),
    SincroPersonas::class => autowire(SincroPersonas::class),
    TrasladarPersonaUseCase::class => autowire(TrasladarPersonaUseCase::class),
    UnirPersonaUseCase::class => autowire(UnirPersonaUseCase::class),
    VerDesaparecidosDeListasData::class => autowire(VerDesaparecidosDeListasData::class),
    VerDesaparecidosDeOrbixData::class => autowire(VerDesaparecidosDeOrbixData::class),
    VerListasData::class => autowire(VerListasData::class),
    VerOrbixData::class => autowire(VerOrbixData::class),
    VerOrbixOtraDlData::class => autowire(VerOrbixOtraDlData::class),
    VerTrasladosData::class => autowire(VerTrasladosData::class),
];
