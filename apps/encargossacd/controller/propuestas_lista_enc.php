<?php

use function core\is_true;
use encargossacd\model\GestorPropuestas;
use encargossacd\model\entity\GestorPropuestaEncargoSacdHorario;

// INICIO Cabecera global de URL de controlador *********************************

require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qfiltro_ctr = (integer) \filter_input(INPUT_POST, 'filtro_ctr');

$error_txt = '';
$gesPropuestaEncargoSacdHorario = new GestorPropuestaEncargoSacdHorario();
$existe = $gesPropuestaEncargoSacdHorario->existenLasTablas();

if (is_true($existe)) {
    $gesPropuestas = new GestorPropuestas();
    $rta = $gesPropuestas->getListaSimple($Qfiltro_ctr);
} else {
    $error_txt = _("Debe crear la tabla de propuestas");
}

if (!empty($error_txt)) {
    echo $error_txt;
} else {
    echo $rta;
}