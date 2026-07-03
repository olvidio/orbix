<?php

use frontend\ubis\helpers\UbisPayload;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\PostRequest;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$data = UbisPayload::postData(PostRequest::getDataFromUrl('/src/ubis/lista_ctrs_data', []));
$error = UbisPayload::apiError($data);
if ($error !== '') {
    exit($error);
}

$lista = UbisPayload::listaFromPayload($data);

$oTabla = new Lista();
$oTabla->setId_tabla('lista_ctrs');
$oTabla->setCabeceras($lista['cabeceras']);
$oTabla->setDatos($lista['valores']);

$num_total_s = \frontend\shared\helpers\PayloadCoercion::int($data['num_total_s'] ?? 0);

echo "<h3>" . ucfirst(sprintf(_("número total de s: %s"), $num_total_s)) . "</h3>";
echo $oTabla->mostrar_tabla();
