<?php

use frontend\notas\helpers\NotasPayload;
use frontend\shared\helpers\AjaxJsonSupport;

/**
 * Dialogo "buscar actividad" que abre `form_notas_de_una_persona.phtml` al pulsar
 * "añadir ca".
 */

use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$Qdl_org = (string)filter_input(INPUT_POST, 'dl_org');
$Qf_acta_iso = (string)filter_input(INPUT_POST, 'f_acta_iso');
$Qid_activ = (int)filter_input(INPUT_POST, 'id_activ');

$data = PostRequest::getDataFromUrl('/src/notas/actividades_buscar_data', [
    'dl_org' => $Qdl_org,
    'f_acta_iso' => $Qf_acta_iso,
    'id_activ' => $Qid_activ,
]);
$buscar = NotasPayload::actividadesBuscarFromPayload($data);

$oDesplDelegaciones = Desplegable::desdeOpciones($buscar['delegaciones'], 'dl_org');
$oDesplDelegaciones->setOpcion_sel($buscar['dl_org_sel']);
$oDesplDelegaciones->setAction('fnjs_buscar_ca()');

$oDesplActividades = new Desplegable();
$oDesplActividades->setOpciones($buscar['actividades']);
$oDesplActividades->setBlanco(true);
$oDesplActividades->setNombre('id_activ_sel');
$oDesplActividades->setOpcion_sel($buscar['id_activ_sel']);

$oHash = new HashFront();
$oHash->setCamposForm('pres_nom!pres_telf!pres_mail!zona!observ');
$oHash->setCamposNo('scroll_id!sel');

$a_campos = [
    'oHash' => $oHash,
    'oDesplDelegaciones' => $oDesplDelegaciones,
    'oDesplActividades' => $oDesplActividades,
];

AjaxJsonSupport::renderPhtml('frontend\\notas\\controller', 'actividad_buscar_form.phtml', $a_campos);
