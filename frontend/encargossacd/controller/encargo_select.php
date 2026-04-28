<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;

/**
 * Listado de encargos. Los datos de cada fila vienen del backend
 * ({@see \src\encargossacd\application\EncargoSelectData}) via
 * `/src/encargossacd/encargo_select_data`; aqui solo armamos la `frontend\shared\web\Lista`.
 *
 * @package    delegacion
 * @subpackage    des
 */

// INICIO Cabecera global de URL de controlador (frontend) *********************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

if (isset($_POST['stack'])) {
    $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack !== '') {
        $oPosicion2 = new frontend\shared\web\Posicion();
        if ($oPosicion2->goStack($stack)) {
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
}

$Qtitulo = (string)filter_input(INPUT_POST, 'titulo');
$Qid_tipo_enc = (int)filter_input(INPUT_POST, 'id_tipo_enc');
$Qdesc_enc = (string)filter_input(INPUT_POST, 'desc_enc');

/** @var array<string, mixed> $data */
$data = PostRequest::getDataFromUrl('/src/encargossacd/encargo_select_data', [
    'desc_enc' => $Qdesc_enc,
    'id_tipo_enc' => $Qid_tipo_enc,
]);
$filas = is_array($data['filas'] ?? null) ? $data['filas'] : [];

$a_botones = [
    ['txt' => _("horario"), 'click' => "fnjs_horario(\"#seleccionados\")"],
    ['txt' => _("modificar"), 'click' => "fnjs_modificar(\"#seleccionados\")"],
    ['txt' => _("eliminar"), 'click' => "fnjs_borrar(\"#seleccionados\")"],
];

$a_cabeceras = [
    _("sección"),
    ['name' => _("descripción"), 'formatter' => 'clickFormatter'],
    _("lugar"),
    _("descripción lugar"),
    _("idioma"),
];

$a_valores = [];
if (!empty($Qid_sel)) {
    $a_valores['select'] = $Qid_sel;
}
if (!empty($Qscroll_id)) {
    $a_valores['scroll_id'] = $Qscroll_id;
}
$i = 0;
foreach ($filas as $fila) {
    $i++;
    $id_enc = (int)($fila['id_enc'] ?? 0);
    $sf_sv = (int)($fila['sf_sv'] ?? 0);
    $desc_enc = (string)($fila['desc_enc'] ?? '');

    $aQuery = ['que' => 'editar', 'id_enc' => $id_enc];
    array_walk($aQuery, 'src\shared\domain\helpers\poner_empty_on_null');
    $pagina = HashFront::link('frontend/encargossacd/controller/encargo_ver.php?' . http_build_query($aQuery));

    if ($sf_sv === 2) {
        $a_valores[$i]['clase'] = 'tono2';
    }
    $a_valores[$i]['sel'] = $id_enc;
    $a_valores[$i][1] = (string)($fila['seccion'] ?? '');
    $a_valores[$i][2] = ['ira' => $pagina, 'valor' => $desc_enc];
    $a_valores[$i][3] = (string)($fila['nombre_ubi'] ?? '');
    $a_valores[$i][4] = (string)($fila['desc_lugar'] ?? '');
    $a_valores[$i][5] = (string)($fila['idioma'] ?? '');
}

$aQuery = ['que' => 'nuevo', 'id_tipo_enc' => $Qid_tipo_enc];
array_walk($aQuery, 'src\shared\domain\helpers\poner_empty_on_null');
$pagina_nuevo = HashFront::link('frontend/encargossacd/controller/encargo_ver.php?' . http_build_query($aQuery));

$txt_eliminar = _("¿Esta Seguro que desea borrar este encargo?");

$oTabla = new Lista();
$oTabla->setId_tabla('encargo_select');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$no_tipo_enc = empty($Qid_tipo_enc);

$url_horario = "frontend/encargossacd/controller/encargo_horario_select.php";
$oHashHorario = new HashFront();
$oHashHorario->setUrl($url_horario);
$oHashHorario->setCamposForm('que!id_activ!id_nom');
$h_horario = $oHashHorario->linkSinValParams();

$url_modificar = "frontend/encargossacd/controller/encargo_ver.php";
$oHashMod = new HashFront();
$oHashMod->setUrl($url_modificar);
$oHashMod->setCamposForm('que!scroll_id!sel');
$h_modificar = $oHashMod->linkSinValParams();

$url_borrar = AppUrlConfig::getApiBaseUrl() . '/src/encargossacd/encargo_ver_eliminar';
$oHashBorrar = new HashFront();
$oHashBorrar->setUrl($url_borrar);
$oHashBorrar->setCamposForm('que!sel');
$h_borrar = $oHashBorrar->linkSinValParams();

$oHash = new HashFront();
$oHash->setCamposForm('que');
$oHash->setcamposNo('scroll_id!sel');

$a_campos = [
    'oPosicion' => $oPosicion,
    'url_horario' => $url_horario,
    'h_horario' => $h_horario,
    'url_modificar' => $url_modificar,
    'h_modificar' => $h_modificar,
    'url_borrar' => $url_borrar,
    'h_borrar' => $h_borrar,
    'oHash' => $oHash,
    'oTabla' => $oTabla,
    'titulo' => $Qtitulo,
    'txt_eliminar' => $txt_eliminar,
    'pagina_nuevo' => $pagina_nuevo,
    'no_tipo_enc' => $no_tipo_enc,
];

$oView = new ViewNewPhtml('frontend\\encargossacd\\controller');
$oView->renderizar('encargo_select.phtml', $a_campos);
