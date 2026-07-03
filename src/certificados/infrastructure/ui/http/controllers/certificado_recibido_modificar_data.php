<?php

use src\certificados\application\CertificadoRecibidoModificarFormData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var CertificadoRecibidoModificarFormData $useCase */
$useCase = DependencyResolver::get(CertificadoRecibidoModificarFormData::class);

$error = '';
$data = [];
try {
    $data = $useCase->execute(\src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_item'));
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
