<?php

use function src\shared\domain\helpers\input_string;

use src\certificados\domain\CertificadoEmitidoSelect;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var CertificadoEmitidoSelect $useCase */
$useCase = DependencyResolver::get(CertificadoEmitidoSelect::class);

$Qcertificado = input_string($_POST, 'certificado');
$inicurs_ca_iso = input_string($_POST, 'inicurs_ca_iso');
$fincurs_ca_iso = input_string($_POST, 'fincurs_ca_iso');

$error_txt = '';
$data = $useCase->getCamposVista($Qcertificado, $inicurs_ca_iso, $fincurs_ca_iso);
if (($data['success'] ?? true) === false) {
    $errorTxt = is_string($data['error_txt'] ?? null) ? $data['error_txt'] : '';
    if (str_contains($errorTxt, '"e_certificados_rstgr" does not exis')) {
        $error_txt .= _('No se encuentra la tabla. ¿Seguro que es una región del stgr?');
        $error_txt .= '<br>';
    }
    $error_txt .= $errorTxt;
    $data = [];
}

ContestarJson::enviar($error_txt, $data);
