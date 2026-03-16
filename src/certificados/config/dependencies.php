<?php

use src\certificados\domain\contracts\CertificadoEmitidoRepositoryInterface;
use src\certificados\domain\contracts\CertificadoRecibidoRepositoryInterface;
use src\certificados\infrastructure\persistence\postgresql\PgCertificadoEmitidoRepository;
use src\certificados\infrastructure\persistence\postgresql\PgCertificadoRecibidoRepository;
use function DI\autowire;

return [
    // Mapeos de Interfaces a Implementaciones
    CertificadoEmitidoRepositoryInterface::class => autowire(PgCertificadoEmitidoRepository::class),
    CertificadoRecibidoRepositoryInterface::class => autowire(PgCertificadoRecibidoRepository::class),
];
