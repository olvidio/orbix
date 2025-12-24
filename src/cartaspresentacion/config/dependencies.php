<?php

use src\cartaspresentacion\domain\contracts\CartaPresentacionDlRepositoryInterface;
use src\cartaspresentacion\domain\contracts\CartaPresentacionExRepositoryInterface;
use src\cartaspresentacion\domain\contracts\CartaPresentacionRepositoryInterface;
use src\cartaspresentacion\infrastructure\repositories\PgCartaPresentacionDlRepository;
use src\cartaspresentacion\infrastructure\repositories\PgCartaPresentacionExRepository;
use src\cartaspresentacion\infrastructure\repositories\PgCartaPresentacionRepository;
use function DI\autowire;

return [
// Mapeos de Interfaces a Implementaciones
    CartaPresentacionDlRepositoryInterface::class => autowire(PgCartaPresentacionDlRepository::class),
    CartaPresentacionExRepositoryInterface::class => autowire(PgCartaPresentacionExRepository::class),
    CartaPresentacionRepositoryInterface::class => autowire(PgCartaPresentacionRepository::class),
];
