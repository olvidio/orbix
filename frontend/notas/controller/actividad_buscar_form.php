<?php

/**
 * Dialogo "buscar actividad" que abre `form_notas_de_una_persona.phtml` al pulsar
 * "añadir ca". Muestra dos desplegables (delegacion + actividades en
 * +/- 3 meses respecto a `f_acta_iso`) y recoge la seleccion.
 *
 * Sustituye al case `frm_buscar` del antiguo `apps/notas/controller/notas_ajax.php`.
 * El HTML del formulario se arma aqui, no en `src/`.
 */

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use web\Desplegable;
use web\Hash;

require_once 'frontend/shared/global_header_front.inc';

$Qdl_org = (string)filter_input(INPUT_POST, 'dl_org');
$Qf_acta_iso = (string)filter_input(INPUT_POST, 'f_acta_iso');
$Qid_activ = (int)filter_input(INPUT_POST, 'id_activ');

$data = PostRequest::getDataFromUrl('/src/notas/actividades_buscar_data', [
    'dl_org' => $Qdl_org,
    'f_acta_iso' => $Qf_acta_iso,
    'id_activ' => $Qid_activ,
]);

$aDelegaciones = $data['delegaciones'] ?? [];
$aActividades = $data['actividades'] ?? [];
$dl_org_sel = $data['dl_org_sel'] ?? '';
$id_activ_sel = $data['id_activ_sel'] ?? '';

$oDesplDelegaciones = Desplegable::desdeOpciones($aDelegaciones, 'dl_org');
$oDesplDelegaciones->setOpcion_sel($dl_org_sel);
$oDesplDelegaciones->setAction('fnjs_buscar_ca()');

$oDesplActividades = new Desplegable();
$oDesplActividades->setOpciones($aActividades);
$oDesplActividades->setBlanco(1);
$oDesplActividades->setNombre('id_activ_sel');
$oDesplActividades->setOpcion_sel($id_activ_sel);

$oHash = new Hash();
$oHash->setCamposForm('pres_nom!pres_telf!pres_mail!zona!observ');
$oHash->setCamposNo('scroll_id!sel');

$a_campos = [
    'oHash' => $oHash,
    'oDesplDelegaciones' => $oDesplDelegaciones,
    'oDesplActividades' => $oDesplActividades,
];

$oView = new ViewNewPhtml('frontend\\notas\\controller');
$oView->renderizar('actividad_buscar_form.phtml', $a_campos);
