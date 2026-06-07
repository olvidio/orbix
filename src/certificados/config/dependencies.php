<?php

use src\certificados\application\CertificadoEmitidoAdjuntarFormData;
use src\certificados\application\CertificadoEmitidoUploadFirmadoFormData;
use src\certificados\application\CertificadoRecibidoAdjuntarFormData;
use src\certificados\application\CertificadoRecibidoModificarFormData;
use src\certificados\domain\CertificadoEmitidoDelete;
use src\certificados\domain\CertificadoEmitidoEnviar;
use src\certificados\domain\CertificadoEmitidoSelect;
use src\certificados\domain\CertificadoEmitidoUpload;
use src\certificados\domain\CertificadoRecibidoDelete;
use src\certificados\domain\CertificadoRecibidoUpload;
use src\certificados\domain\contracts\CertificadoEmitidoRepositoryInterface;
use src\certificados\domain\contracts\CertificadoRecibidoRepositoryInterface;
use src\certificados\domain\Select_certificados_de_una_persona;
use src\certificados\infrastructure\persistence\postgresql\PgCertificadoEmitidoRepository;
use src\certificados\infrastructure\persistence\postgresql\PgCertificadoRecibidoRepository;
use function DI\autowire;

return [
    CertificadoEmitidoRepositoryInterface::class => autowire(PgCertificadoEmitidoRepository::class),
    CertificadoRecibidoRepositoryInterface::class => autowire(PgCertificadoRecibidoRepository::class),

    CertificadoEmitidoAdjuntarFormData::class => autowire(CertificadoEmitidoAdjuntarFormData::class),
    CertificadoEmitidoUploadFirmadoFormData::class => autowire(CertificadoEmitidoUploadFirmadoFormData::class),
    CertificadoRecibidoAdjuntarFormData::class => autowire(CertificadoRecibidoAdjuntarFormData::class),
    CertificadoRecibidoModificarFormData::class => autowire(CertificadoRecibidoModificarFormData::class),

    CertificadoEmitidoDelete::class => autowire(CertificadoEmitidoDelete::class),
    CertificadoEmitidoEnviar::class => autowire(CertificadoEmitidoEnviar::class),
    CertificadoEmitidoSelect::class => autowire(CertificadoEmitidoSelect::class),
    CertificadoEmitidoUpload::class => autowire(CertificadoEmitidoUpload::class),
    CertificadoRecibidoDelete::class => autowire(CertificadoRecibidoDelete::class),
    CertificadoRecibidoUpload::class => autowire(CertificadoRecibidoUpload::class),
    Select_certificados_de_una_persona::class => autowire(Select_certificados_de_una_persona::class),
];
