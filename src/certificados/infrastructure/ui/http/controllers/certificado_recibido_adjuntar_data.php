<?php

use src\certificados\application\CertificadoRecibidoAdjuntarFormData;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\web\ContestarJson;
use src\ubis\domain\RegionStgrAviso;

require_once 'frontend/shared/global_header_front.inc';

$error = '';
$data = [];
try {
    $id_nom = (int)filter_input(INPUT_POST, 'id_nom');
    $data = CertificadoRecibidoAdjuntarFormData::execute($id_nom);
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
