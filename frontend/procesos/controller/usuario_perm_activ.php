<?php

use core\ConfigGlobal;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewTwig;
use web\Desplegable;
use web\Hash;
use web\TiposActividades;
use function core\is_true;

require_once("frontend/shared/global_header_front.inc");

$oPosicion->recordar();

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) {
    $Qid_usuario = (int)strtok($a_sel[0], "#");
    $Qid_item = (string)strtok("#");
    $Qid_tipo_activ_txt = (string)strtok("#");
    $Qdl_propia = (string)strtok("#");
} else {
    $Qid_usuario = (int)filter_input(INPUT_POST, 'id_usuario');
    $Qid_item = '';
    $Qid_tipo_activ_txt = (string)filter_input(INPUT_POST, 'id_tipo_activ_txt');
    $Qdl_propia = (string)filter_input(INPUT_POST, 'dl_propia');
}

$Qquien = (string)filter_input(INPUT_POST, 'quien');
$Qque = (string)filter_input(INPUT_POST, 'que');

$oTipoActiv = new TiposActividades($Qid_tipo_activ_txt, true);
$id_tipo_activ = $oTipoActiv->getId_tipo_activ();
$sfsv = $oTipoActiv->getSfsvText();
$asistentes = $oTipoActiv->getAsistentesText();
$actividad = $oTipoActiv->getActividadText();
$nom_tipo = $oTipoActiv->getNom_tipoText();

$data = PostRequest::getDataFromUrl('/src/procesos/usuario_perm_activ_data', [
    'id_usuario' => $Qid_usuario,
    'id_tipo_activ_txt' => $Qid_tipo_activ_txt,
    'id_tipo_activ' => $id_tipo_activ,
    'dl_propia' => $Qdl_propia,
]);

$nombre = $data['nombre'] ?? '';
$Qdl_propia = $data['dl_propia'] ?? 't';
$perm_jefe = (bool)($data['perm_jefe'] ?? false);
$a_fases = (array)($data['a_fases'] ?? []);
$a_acciones = (array)($data['a_acciones'] ?? []);
$aPermData = (array)($data['aPerm'] ?? []);

$oActividadTipo = new \src\actividades\application\ActividadTipo();
if (!empty($id_tipo_activ)) {
    $oActividadTipo->setId_tipo_activ($id_tipo_activ);
}
$oActividadTipo->setAsistentes($asistentes);
$oActividadTipo->setActividad($actividad);
$oActividadTipo->setNom_tipo($nom_tipo);
$oActividadTipo->setPara('procesos');
$oActividadTipo->setPerm_jefe($perm_jefe);

$aPerm = [];
foreach ($aPermData as $i => $fila) {
    $oDesplFases = new Desplegable();
    $oDesplFases->setOpciones($a_fases);
    $oDesplFases->setBlanco(true);
    $oDesplFases->setNombre("fase_ref[]");
    $oDesplFases->setOpcion_sel((string)$fila['fase_ref']);

    $oDesplPermOn = new Desplegable('perm_on[]', $a_acciones, (string)$fila['perm_on'], false);
    $oDesplPermOff = new Desplegable('perm_off[]', $a_acciones, (string)$fila['perm_off'], false);

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

$oHash = new Hash();
$oHash->setCamposForm('dl_propia!fase_ref!extendida!iactividad_val!iasistentes_val!inom_tipo_val!isfsv_val!perm_on!perm_off');
$oHash->setCamposNo('afecta_a!id_tipo_activ');
$a_camposHidden = [
    'id_usuario' => $Qid_usuario,
    'quien' => $Qquien,
    'extendida' => true,
];
$oHash->setArraycamposHidden($a_camposHidden);

$url_actualizar = rtrim(ConfigGlobal::getWeb(), '/') . '/src/procesos/usuario_perm_activ_ajax';
$oHash1 = new Hash();
$oHash1->setUrl($url_actualizar);
$oHash1->setCamposForm('dl_propia!id_tipo_activ');
$h_actualizar = $oHash1->linkSinValParams();

if (is_true($Qdl_propia)) {
    $chk_propia = 'checked';
    $chk_otra = '';
} else {
    $chk_propia = '';
    $chk_otra = 'checked';
}

$titulo = _("Añadir nuevo permiso a");
if (!empty($Qid_item)) {
    $titulo = _("Modificar el permiso para");
}

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'url_actualizar' => $url_actualizar,
    'h_actualizar' => $h_actualizar,
    'nombre' => $nombre,
    'chk_propia' => $chk_propia,
    'chk_otra' => $chk_otra,
    'oActividadTipo' => $oActividadTipo,
    'aPerm' => $aPerm,
    'extendida' => true,
    'titulo' => $titulo,
];

$oView = new ViewNewTwig('procesos/controller');
$oView->renderizar('usuario_perm_activ.html.twig', $a_campos);
