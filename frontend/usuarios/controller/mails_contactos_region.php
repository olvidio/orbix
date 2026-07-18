<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\AjaxJsonSupport;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\usuarios\helpers\UsuariosPayload;

require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();

$Qregion = (string)(filter_input(INPUT_GET, 'region') ?? '');
if ($Qregion === '') {
    $Qregion = (string)(filter_input(INPUT_POST, 'region') ?? '');
}

$url_lista_backend = HashFront::cmdSinParametros(AppUrlConfig::srcBrowserUrl('/src/usuarios/mails_contactos_region')
);
$oHash = new HashFront();
$oHash->setUrl($url_lista_backend);
$oHash->setArrayCamposHidden(['region' => $Qregion]);
$hash_params = $oHash->getArrayCampos();

$resp = UsuariosPayload::postData(PostRequest::getData($url_lista_backend, $hash_params));
$aContactos = UsuariosPayload::contactosFromPayload($resp['contactos'] ?? null);

ob_start();
if (!empty($resp['success']) && $resp['success'] === true) {
    echo '<div class="mails-region">';
    $titulo = empty($Qregion) ? 'Contactos' : 'Contactos de ' . htmlspecialchars($Qregion);
    echo '<h3>' . $titulo . '</h3>';
    if ($aContactos === []) {
        echo '<p>' . _("No hay datos de contactos para esta región") . '.</p>';
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
    $msg = \frontend\shared\helpers\PayloadCoercion::string($resp['mensaje'] ?? 'Error al obtener los mails');
    echo '<div class="mails-region-error">' . htmlspecialchars($msg) . '</div>';
}
AjaxJsonSupport::html((string)ob_get_clean());
