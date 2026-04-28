<?php

use frontend\shared\web\ContestarJson;
use src\dossiers\application\DossiersVerPantallaData;
use src\dossiers\infrastructure\ui\http\SignPublicFrontendLink;

$result = DossiersVerPantallaData::build($_POST);
$error = (string)($result['error'] ?? '');
unset($result['error']);

$urlSpecs = $result['url_specs'] ?? [];
unset($result['url_specs']);
foreach ($urlSpecs as $placeholder => $spec) {
    if (!is_array($spec)) {
        continue;
    }
    $signed = SignPublicFrontendLink::fromSpec($spec);
    $esc = addslashes($signed);
    foreach (['top_html', 'cuerpo_html'] as $hk) {
        if (!empty($result[$hk]) && is_string($result[$hk])) {
            $result[$hk] = str_replace((string) $placeholder, $esc, $result[$hk]);
        }
    }
}
if (!empty($result['lista_a_filas']) && is_array($result['lista_a_filas'])) {
    $result['lista_a_filas'] = SignPublicFrontendLink::resolveDossiersListaFichasFilas($result['lista_a_filas']);
}

ContestarJson::enviar($error, $result);
