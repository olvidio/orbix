<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/usuarios_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/ajax_json_support.php';
FrontBootstrap::boot();

$Qregion = (string)(filter_input(INPUT_GET, 'region') ?? '');
if ($Qregion === '') {
    $Qregion = (string)(filter_input(INPUT_POST, 'region') ?? '');
}

$url_lista_backend = HashFront::cmdSinParametros(AppUrlConfig::getPublicAppBaseUrl()
    . '/src/usuarios/mails_contactos_region'
);
$oHash = new HashFront();
$oHash->setUrl($url_lista_backend);
$oHash->setArrayCamposHidden(['region' => $Qregion]);
$hash_params = $oHash->getArrayCampos();

$resp = usuarios_post_data(PostRequest::getData($url_lista_backend, $hash_params));
$aContactos = usuarios_contactos_from_payload($resp['contactos'] ?? null);

ob_start();
if (!empty($resp['success']) && $resp['success'] === true) {
    echo '<div class="mails-region">';
    $titulo = empty($Qregion) ? 'Contactos' : 'Contactos de ' . htmlspecialchars($Qregion);
    echo '<h3>' . $titulo . '</h3>';
    if ($aContactos === []) {
        echo '<p>No hay datos de contactos para esta región.</p>';
    } else {
        echo '<ul>';
        foreach ($aContactos as $nombre => $info) {
            $nombre_safe = htmlspecialchars($nombre);
            $cargo = htmlspecialchars($info['cargo']);
            $email = htmlspecialchars($info['email']);
            $linea = $email;
            if ($nombre_safe !== '' || $cargo !== '') {
                $det = trim($nombre_safe . ($cargo !== '' ? ' - ' . $cargo : ''));
                if ($det !== '') {
                    $linea .= ' (' . $det . ')';
                }
            }
            echo '<li>' . $linea . '</li>';
        }
        echo '</ul>';
    }
    echo '</div>';
} else {
    $msg = tessera_imprimir_string($resp['mensaje'] ?? 'Error al obtener los mails');
    echo '<div class="mails-region-error">' . htmlspecialchars($msg) . '</div>';
}
ajax_json_html((string) ob_get_clean());
