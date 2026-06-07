<?php

use function src\shared\domain\helpers\input_int;

use src\certificados\application\CertificadoRecibidoModificarFormData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var CertificadoRecibidoModificarFormData $useCase */
$useCase = DependencyResolver::get(CertificadoRecibidoModificarFormData::class);

$error = '';
$data = [];
try {
    $data = $useCase->execute(input_int($_POST, 'id_item'));
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
