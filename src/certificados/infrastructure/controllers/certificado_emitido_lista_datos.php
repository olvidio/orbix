<?php
/**
 * Esta página muestra una tabla con los certificados.
 *
 *
 * @package    delegacion
 * @subpackage    estudios
 * @author    Daniel Serrabou
 * @since        25/2/23.
 *
 */

use src\certificados\domain\CertificadoEmitidoSelect;
use web\ContestarJson;

$Qcertificado = (string)filter_input(INPUT_POST, 'certificado');
$inicurs_ca_iso = (string)filter_input(INPUT_POST, 'inicurs_ca_iso');
$fincurs_ca_iso = (string)filter_input(INPUT_POST, 'fincurs_ca_iso');

$error_txt = '';
$data = CertificadoEmitidoSelect::getCamposVista($Qcertificado, $inicurs_ca_iso, $fincurs_ca_iso);
if ($data['success'] === false) {
    if (strstr($data['error_txt'], '"e_certificados_rstgr" does not exis') !== false) {
        $error_txt .= _("No se encuentra la tabla. ¿Seguro que es una región del stgr?");
        $error_txt .= "<br>";
    }
    $error_txt .= $data['error_txt'];
    $data = [];
}

// envía una Response
ContestarJson::enviar($error_txt, $data);