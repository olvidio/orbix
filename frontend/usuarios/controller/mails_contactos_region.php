<?php

use core\ConfigGlobal;
use frontend\shared\PostRequest;
use web\Hash;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

// región por GET o POST
$Qregion = (string)(filter_input(INPUT_GET, 'region') ?? '');
if ($Qregion === '') {
    $Qregion = (string)(filter_input(INPUT_POST, 'region') ?? '');
}

$url_lista_backend = Hash::cmdSinParametros(ConfigGlobal::getWeb()
    . '/src/usuarios/infrastructure/controllers/mails_contactos_region.php'
);
$oHash = new Hash();
$oHash->setUrl($url_lista_backend);
$oHash->setArrayCamposHidden(['region' => $Qregion]);
$hash_params = $oHash->getArrayCampos();

$resp = PostRequest::getData($url_lista_backend, $hash_params);

$aContactos = $resp['contactos'] ?? [];

if (!empty($resp['success']) && $resp['success'] === true) {
    echo '<div class="mails-region">';
    $titulo = empty($Qregion) ? 'Contactos' : 'Contactos de ' . htmlspecialchars($Qregion);
    echo '<h3>' . $titulo . '</h3>';
    if (empty($aContactos)) {
        echo '<p>No hay datos de contactos para esta región.</p>';
    } else {
        echo '<ul>';
        foreach ($aContactos as $nombre => $info) {
            $nombre_safe = htmlspecialchars((string)$nombre);
            $cargo = htmlspecialchars((string)($info['cargo'] ?? ''));
            $email = htmlspecialchars((string)($info['email'] ?? ''));
            $linea = $email;
            if ($nombre_safe !== '' || $cargo !== '') {
                $det = trim($nombre_safe . (!empty($cargo) ? ' - ' . $cargo : ''));
                if (!empty($det)) {
                    $linea .= ' (' . $det . ')';
                }
            }
            echo '<li>' . $linea . '</li>';
        }
        echo '</ul>';
    }
    echo '</div>';
} else {
    $msg = $resp['mensaje'] ?? 'Error al obtener los mails';
    echo '<div class="mails-region-error">' . htmlspecialchars((string)$msg) . '</div>';
}