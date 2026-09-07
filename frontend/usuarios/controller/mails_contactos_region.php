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
    $esAgregado = str_contains($Qregion, ',');
    if ($esAgregado) {
        $titulo = _('Contactos de todas las regiones');
    } elseif ($Qregion === '') {
        $titulo = _('Contactos');
    } else {
        $titulo = _('Contactos de') . ' ' . htmlspecialchars($Qregion, ENT_QUOTES, 'UTF-8');
    }
    echo '<h3>' . $titulo . '</h3>';
    if ($aContactos === []) {
        echo '<p>' . _("No hay datos de contactos para esta región") . '.</p>';
    } else {
        echo '<ul>';
        foreach ($aContactos as $nombre => $info) {
            $nombreVisible = $info['nombre'] !== '' ? $info['nombre'] : $nombre;
            $nombre_safe = htmlspecialchars($nombreVisible, ENT_QUOTES, 'UTF-8');
            $cargo = htmlspecialchars($info['cargo'], ENT_QUOTES, 'UTF-8');
            $email = htmlspecialchars($info['email'], ENT_QUOTES, 'UTF-8');
            $regionLabel = htmlspecialchars($info['region'], ENT_QUOTES, 'UTF-8');
            $linea = '<a href="mailto:' . $email . '">' . $email . '</a>';
            if ($nombre_safe !== '' || $cargo !== '') {
                $det = trim($nombre_safe . ($cargo !== '' ? ' - ' . $cargo : ''));
                if ($det !== '') {
                    $linea .= ' (' . $det . ')';
                }
            }
            if ($regionLabel !== '') {
                $linea .= ' [' . $regionLabel . ']';
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
