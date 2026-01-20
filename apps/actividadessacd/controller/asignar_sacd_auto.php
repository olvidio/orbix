<?php

use actividadessacd\model\AsignarSacd;
use src\shared\domain\value_objects\DateTimeLocal;
use web\Hash;

// INICIO Cabecera global de URL de controlador *********************************

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");

// FIN de  Cabecera global de URL de controlador ********************************


//corrijo el dato que está en config, porque este programa se usará para el próximo curso:
//TODO
$any_final_curs = $_SESSION['oConfig']->any_final_curs();
$oF_inicurs_des = new DateTimeLocal('@' . mktime(0, 0, 0, 9, 2, $any_final_curs));
$inicurs_des = $oF_inicurs_des->getFromLocal();
$inicurs_des_iso = $oF_inicurs_des->format('Y-m-d');


$Qconfirm = (string)filter_input(INPUT_POST, 'confirm');

if ($Qconfirm == 'yes') {
    $oAsigSacd = new AsignarSacd();
    $oAsigSacd->setF_ini($inicurs_des_iso);
    $rta = $oAsigSacd->asignarAuto();
    $asig = $rta['asignadas'];
    $sin_asig = $rta['sin_asignar'];
    echo sprintf(_("Ya está. Se ha asignado %s actividades. Quedan %s por asignar (el centro)."), $asig, $sin_asig);
} else {

    $oHash = new Hash();
    $a_camposHidden = array(
        'confirm' => 'yes',
    );
    $oHash->setArraycamposHidden($a_camposHidden);

    $html = '<p>';
    $html .= ucfirst(_("esto asignará el sacd titular del centro a las actividades que tengan un centro encargado"));
    $html .= '.</p><p>';
    $html .= ucfirst(sprintf(_("limitado a las actividades de sr y sg a partir de %s y marcadas como actuales"), $inicurs_des));
    $html .= '.</p><p>';
    $html .= ucfirst(_("en el campo observaciones aparece la palabra 'auto' para indicar la asignación automática"));
    $html .= '.</p><br>';
    ?>
    <?= $html ?>
    <form id="frm_confirm" name="frm_confirm" action="apps/actividadessacd/controller/asignar_sacd_auto.php">
        <?= $oHash->getCamposHtml() ?>
        <input type=button onclick=fnjs_enviar_formulario(this.form) value="continuar">
    </form>
    <?php
}
