<?php

use src\cartaspresentacion\domain\contracts\CartaPresentacionDlRepositoryInterface;
use src\cartaspresentacion\domain\contracts\CartaPresentacionExRepositoryInterface;
use src\cartaspresentacion\domain\contracts\CartaPresentacionRepositoryInterface;
use src\cartaspresentacion\infrastructure\persistence\postgresql\PgCartaPresentacionDlRepository;
use src\cartaspresentacion\infrastructure\persistence\postgresql\PgCartaPresentacionExRepository;
use src\cartaspresentacion\infrastructure\persistence\postgresql\PgCartaPresentacionRepository;
use function DI\autowire;

return [
// Mapeos de Interfaces a Implementaciones
    CartaPresentacionDlRepositoryInterface::class => autowire(PgCartaPresentacionDlRepository::class),
    CartaPresentacionExRepositoryInterface::class => autowire(PgCartaPresentacionExRepository::class),
    CartaPresentacionRepositoryInterface::class => autowire(PgCartaPresentacionRepository::class),
];
