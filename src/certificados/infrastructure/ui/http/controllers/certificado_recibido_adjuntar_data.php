<?php

use function src\shared\domain\helpers\input_int;

use src\certificados\application\CertificadoRecibidoAdjuntarFormData;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\ubis\domain\RegionStgrAviso;


/** @var CertificadoRecibidoAdjuntarFormData $useCase */
$useCase = DependencyResolver::get(CertificadoRecibidoAdjuntarFormData::class);

$error = '';
$data = [];
try {
    $data = $useCase->execute(input_int($_POST, 'id_nom'));
} catch (\Throwable $e) {
    if (RegionStgrAviso::esMensajeSuave($e->getMessage())) {
        $data = [
            'aviso' => $e->getMessage(),
            'nom' => '',
            'f_recibido' => (new DateTimeLocal())->getFromLocal(),
        ];
    } else {
        $error = $e->getMessage();
    }
}

ContestarJson::enviar($error, $data);
