<?php
namespace frontend\personas\controller;

use frontend\personas\helpers\PersonasPayload;
use frontend\personas\helpers\PersonasPostInput;
use frontend\shared\PostRequest;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\web\Posicion;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;

/**
 * Formulario para trasladar una persona de centro y/o delegacion.
 */
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
/** @var Posicion $oPosicion */
ListNavSupport::bootRecordar($oPosicion);
ListNavSupport::persistRecordarEntry($oPosicion, ListNavSupport::buildReturnParametrosFromPost());


$Qcabecera = (string)filter_input(INPUT_POST, 'cabecera');
$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');

$id_pau = PersonasPostInput::idPauFromSelPost()['id_pau'];

$campos = [
    'id_pau' => $id_pau,
];

$data = PostRequest::getDataFromUrl('/src/personas/traslado_form_data', $campos);
$payload = PersonasPayload::postPayload($data);
$view = PersonasPayload::trasladoFormFromPayload($payload);

$titulo = $view['titulo'];
$id_ctr = $view['id_ctr'];
$nombre_ctr = $view['nombre_ctr'];
$dl = $view['dl'];
$hoy = $view['hoy'];
$opciones_centros = $view['opciones_centros'];
$opciones_dl = $view['opciones_dl'];
$opciones_situacion = $view['opciones_situacion'];

$oDesplCentroDl = new Desplegable();
$oDesplCentroDl->setNombre('new_ctr');
$oDesplCentroDl->setOpciones($opciones_centros);
$oDesplCentroDl->setBlanco(true);

$oDesplDlyR = Desplegable::desdeOpciones($opciones_dl, 'new_dl');

$oDesplSituacion = new Desplegable();
$oDesplSituacion->setOpciones($opciones_situacion);
$oDesplSituacion->setNombre('situacion');

$oHash = new HashFront();
$oHash->setCamposForm('new_ctr!f_ctr!new_dl!f_dl!situacion');
$oHash->setArraycamposHidden([
    'obj_pau' => $Qobj_pau,
    'id_pau' => $id_pau,
    'id_ctr_o' => $id_ctr,
    'ctr_o' => $nombre_ctr,
    'dl' => $dl,
]);

$a_parametros = ['pau' => 'p', 'id_nom' => $id_pau, 'obj_pau' => $Qobj_pau];
$gohome = HashFront::link(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/personas/controller/home_persona.php?' . http_build_query($a_parametros));
$a_parametros_dossier = ['pau' => 'p', 'id_pau' => $id_pau, 'obj_pau' => $Qobj_pau];
$godossiers = HashFront::link('frontend/dossiers/controller/dossiers_ver.php?' . http_build_query($a_parametros_dossier));

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'gohome' => $gohome,
    'godossiers' => $godossiers,
    'titulo' => $titulo,
    'nombre_ctr' => $nombre_ctr,
    'hoy' => $hoy,
    'dl' => $dl,
    'oDesplDlyR' => $oDesplDlyR,
    'oDesplCentros' => $oDesplCentroDl,
    'oDesplSituacion' => $oDesplSituacion,
    'Qcabecera' => $Qcabecera,
];

$oView = new ViewNewPhtml('frontend\personas\controller');
$oView->renderizar('traslado_form.phtml', $a_campos);
