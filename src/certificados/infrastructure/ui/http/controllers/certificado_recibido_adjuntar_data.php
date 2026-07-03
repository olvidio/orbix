<?php

use src\certificados\application\CertificadoRecibidoAdjuntarFormData;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\ubis\domain\RegionStgrAviso;
use src\shared\domain\helpers\FuncTablasSupport;


/** @var CertificadoRecibidoAdjuntarFormData $useCase */
$useCase = DependencyResolver::get(CertificadoRecibidoAdjuntarFormData::class);

$error = '';
$data = [];
try {
    $data = $useCase->execute(FuncTablasSupport::inputInt($_POST, 'id_nom'));
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
