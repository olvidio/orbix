<?php

use frontend\notas\helpers\NotasFormSupport;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewTwig;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;
use frontend\procesos\helpers\ProcesosPostInput;
use frontend\procesos\helpers\ProcesosPayload;
use frontend\shared\helpers\ListNavSupport;
use frontend\shared\helpers\FuncTablasSupport;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
$navState = ListNavSupport::buildReturnParametrosFromPost();
$oPosicion->nav()->enter(
    (string) ($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    [],
    $navState,
);
ListNavSupport::syncNavStateAt($oPosicion, 1, []);


$apiBase = AppUrlConfig::getApiBaseUrl();

$sel = ProcesosPostInput::selTokensFromPost();
$Qid_usuario = $sel['id_usuario'];
$Qid_item = \frontend\shared\helpers\PayloadCoercion::string($sel['id_item']);
$Qid_tipo_activ_txt = $sel['id_tipo_activ_txt'];
$Qdl_propia = $sel['dl_propia'];

$Qquien = ProcesosPostInput::postString('quien');
$Qque = ProcesosPostInput::postString('que');

$data = PostRequest::getDataFromUrl(AppUrlConfig::srcBrowserUrl('/src/procesos/usuario_perm_activ_data'), [
    'id_usuario' => $Qid_usuario,
    'id_tipo_activ_txt' => $Qid_tipo_activ_txt,
    'dl_propia' => $Qdl_propia,
]);

$nombre = \frontend\shared\helpers\PayloadCoercion::string($data['nombre'] ?? '');
$Qdl_propia = \frontend\shared\helpers\PayloadCoercion::string($data['dl_propia'] ?? 't');
$tipo_actividad_html = \frontend\shared\helpers\PayloadCoercion::string($data['tipo_actividad_html'] ?? '');
$a_fases = NotasFormSupport::desplegableOpciones($data['a_fases'] ?? []);
$a_acciones = NotasFormSupport::desplegableOpciones($data['a_acciones'] ?? []);
$aPermData = ProcesosPayload::usuarioPermRows($data['aPerm'] ?? null);

$aPerm = [];
foreach ($aPermData as $i => $fila) {
    $oDesplFases = new Desplegable();
    $oDesplFases->setOpciones($a_fases);
    $oDesplFases->setBlanco(true);
    $oDesplFases->setNombre('fase_ref[]');
    $oDesplFases->setOpcion_sel($fila['fase_ref']);

    $oDesplPermOn = new Desplegable('perm_on[]', $a_acciones, $fila['perm_on'], false);
    $oDesplPermOff = new Desplegable('perm_off[]', $a_acciones, $fila['perm_off'], false);

    $aPerm[] = [
        'afecta_a' => $fila['afecta_a'],
        'nameAfecta_a' => "afecta_a[$i]",
        'num' => $fila['num'],
        'chk' => $fila['marcado'] ? 'checked' : '',
        'oDesplFases' => $oDesplFases,
        'oDesplPermOff' => $oDesplPermOff,
        'oDesplPermOn' => $oDesplPermOn,
    ];
}

$oHash = new HashFront();
$oHash->setCamposForm('dl_propia!fase_ref!extendida!iactividad_val!iasistentes_val!inom_tipo_val!isfsv_val!perm_on!perm_off');
$oHash->setCamposNo('afecta_a!id_tipo_activ');
$a_camposHidden = [
    'id_usuario' => $Qid_usuario,
    'quien' => $Qquien,
    'extendida' => true,
];
$oHash->setArraycamposHidden($a_camposHidden);

$url_actualizar = AppUrlConfig::srcBrowserUrl('/src/procesos/usuario_perm_activ_ajax');
$oHash1 = new HashFront();
$oHash1->setUrl($url_actualizar);
$oHash1->setCamposForm('dl_propia!id_tipo_activ');
$h_actualizar = $oHash1->linkSinValParams();

if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($Qdl_propia)) {
    $chk_propia = 'checked';
    $chk_otra = '';
} else {
    $chk_propia = '';
    $chk_otra = 'checked';
}

$titulo = _("Añadir nuevo permiso a");
if ($Qid_item !== '') {
    $titulo = _("Modificar el permiso para");
}

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'url_perm_activ_guardar' => AppUrlConfig::srcBrowserUrl('/src/usuarios/perm_activ_guardar'),
    'url_actualizar' => $url_actualizar,
    'h_actualizar' => $h_actualizar,
    'nombre' => $nombre,
    'chk_propia' => $chk_propia,
    'chk_otra' => $chk_otra,
    'tipo_actividad_html' => $tipo_actividad_html,
    'aPerm' => $aPerm,
    'titulo' => $titulo,
];

$oView = new ViewNewTwig('frontend/procesos/controller');
$oView->renderizar('usuario_perm_activ.html.twig', $a_campos);
