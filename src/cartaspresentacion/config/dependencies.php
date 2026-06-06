<?php

use src\cartaspresentacion\application\CartaPresentacionEliminar;
use src\cartaspresentacion\application\CartaPresentacionFormData;
use src\cartaspresentacion\application\CartaPresentacionUpdate;
use src\cartaspresentacion\application\CartasPresentacionBuscarOpcionesData;
use src\cartaspresentacion\application\CartasPresentacionListaData;
use src\cartaspresentacion\application\CartasPresentacionPoblacionesData;
use src\cartaspresentacion\application\CartasPresentacionShellData;
use src\cartaspresentacion\application\CartasPresentacionUbisListaData;
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

    // Casos de uso / Application classes
    CartaPresentacionEliminar::class => autowire(CartaPresentacionEliminar::class),
    CartaPresentacionFormData::class => autowire(CartaPresentacionFormData::class),
    CartaPresentacionUpdate::class => autowire(CartaPresentacionUpdate::class),
    CartasPresentacionBuscarOpcionesData::class => autowire(CartasPresentacionBuscarOpcionesData::class),
    CartasPresentacionListaData::class => autowire(CartasPresentacionListaData::class),
    CartasPresentacionPoblacionesData::class => autowire(CartasPresentacionPoblacionesData::class),
    CartasPresentacionShellData::class => autowire(CartasPresentacionShellData::class),
    CartasPresentacionUbisListaData::class => autowire(CartasPresentacionUbisListaData::class),
];
