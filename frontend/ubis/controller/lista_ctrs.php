<?php

use frontend\shared\PostRequest;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/ubis_support.php';
require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$data = ubis_post_data(PostRequest::getDataFromUrl('/src/ubis/lista_ctrs_data', []));
$error = ubis_api_error($data);
if ($error !== '') {
    exit($error);
}

$lista = ubis_lista_from_payload($data);

$oTabla = new Lista();
$oTabla->setId_tabla('lista_ctrs');
$oTabla->setCabeceras($lista['cabeceras']);
$oTabla->setDatos($lista['valores']);

$num_total_s = tessera_imprimir_int($data['num_total_s'] ?? 0);

echo "<h3>" . ucfirst(sprintf(_("número total de s: %s"), $num_total_s)) . "</h3>";
echo $oTabla->mostrar_tabla();
