<?php

use src\certificados\application\CertificadoEmitidoAdjuntarFormData;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\ubis\domain\RegionStgrAviso;
use src\shared\domain\helpers\FuncTablasSupport;


/** @var CertificadoEmitidoAdjuntarFormData $useCase */
$useCase = DependencyResolver::get(CertificadoEmitidoAdjuntarFormData::class);

$error = '';
$data = [];
try {
    $data = $useCase->execute(FuncTablasSupport::inputInt($_POST, 'id_nom'));
} catch (\Throwable $e) {
    if (RegionStgrAviso::esMensajeSuave($e->getMessage())) {
        $data = [
            'aviso' => $e->getMessage(),
            'nom' => '',
            'f_enviado' => (new DateTimeLocal())->getFromLocal(),
        ];
    } else {
        $error = $e->getMessage();
    }
}

ContestarJson::enviar($error, $data);
