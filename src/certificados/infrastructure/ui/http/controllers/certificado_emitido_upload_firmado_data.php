<?php

use src\certificados\application\CertificadoEmitidoUploadFirmadoFormData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var CertificadoEmitidoUploadFirmadoFormData $useCase */
$useCase = DependencyResolver::get(CertificadoEmitidoUploadFirmadoFormData::class);

$error = '';
$data = [];
try {
    $data = $useCase->execute(\src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_item'));
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
