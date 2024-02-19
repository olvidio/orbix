<?php


// INICIO Cabecera global de URL de controlador *********************************
use encargossacd\model\EncargoConstants;
use encargossacd\model\Encargo;
use encargossacd\model\entity\GestorEncargo;
use encargossacd\model\entity\GestorEncargoTipo;
use misas\domain\repositories\EncargoDiaRepository;
use misas\model\EncargosZona;
use web\Hash;
use web\Lista;
use ubis\model\entity\Ubi;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_zona = (integer)filter_input(INPUT_POST, 'id_zona');
$Qid_zona=3;

$aWhere = array();
$aOperador = array();
$a_tipo_enc = [8010, 8011];
$cond_tipo_enc = "{" . implode(', ', $a_tipo_enc) . "}";
$aWhere['id_tipo_enc'] = $cond_tipo_enc;
$aOperador['id_tipo_enc'] = 'ANY';
$aWhere['id_zona'] = $Qid_zona;

$aWhere['_ordre'] = 'desc_enc';

$GesEncargos = new GestorEncargo();
$cEncargos = $GesEncargos->getEncargos($aWhere, $aOperador);

$a_botones = array(array('txt' => _("horario"), 'click' => "fnjs_horario(\"#seleccionados\")"),
    array('txt' => _("modificar"), 'click' => "fnjs_modificar(\"#seleccionados\")"),
    array('txt' => _("eliminar"), 'click' => "fnjs_borrar(\"#seleccionados\")")
);

$a_cabeceras = array(_("sección"), array('name' => _("descripción"), 'formatter' => 'clickFormatter'), _("lugar"), _("descripción lugar"), _("idioma"));

$i = 0;
foreach ($cEncargos as $oEncargo) {
    $i++;
    $id_enc = $oEncargo->getId_enc();
    $sf_sv = $oEncargo->getSf_sv();
    $idioma_enc = $oEncargo->getIdioma_enc();
    $id_ubi = $oEncargo->getId_ubi();
    $desc_enc = $oEncargo->getDesc_enc();
    $desc_lugar = $oEncargo->getDesc_lugar();

    $idioma_enc = empty($idioma_enc) ? 'ca_ES' : $idioma_enc;

    $aQuery = ['que' => 'editar',
        'id_enc' => $id_enc,
    ];
    if (is_array($aQuery)) {
        array_walk($aQuery, 'core\poner_empty_on_null');
    }
    $pagina = Hash::link('apps/encargossacd/controller/encargo_ver.php?' . http_build_query($aQuery));

    $seccion = '';
    if (!empty($sf_sv)) {
        $oGesEncargoTipo = new GestorEncargoTipo();
        $a_seccion = $oGesEncargoTipo->getArraySeccion();
        $seccion = $a_seccion[$sf_sv];
    }

    $idioma = '';
    $GesLocales = new usuarios\model\entity\GestorLocal();
    $cIdiomas = $GesLocales->getLocales(['idioma' => $idioma_enc]);
    if (is_array($cIdiomas) && count($cIdiomas) > 0) {
        $idioma = $cIdiomas[0]->getNom_idioma();
    }

    if ($sf_sv == 2) $a_valores[$i]['clase'] = "tono2";

    if (!empty($id_ubi)) {
        $oUbi = Ubi::newUbi($id_ubi);
        $nombre_ubi = $oUbi->getNombre_ubi();
    } else {
        $nombre_ubi = '';
    }

    $a_valores[$i]['sel'] = $id_enc;
    $a_valores[$i][1] = $seccion;
    $a_valores[$i][2] = array('ira' => $pagina, 'valor' => $desc_enc);
    $a_valores[$i][3] = $nombre_ubi;
    $a_valores[$i][4] = $desc_lugar;
    $a_valores[$i][5] = $idioma;
}


//$aQuery = ['que' => 'nuevo',
//    'id_tipo_enc' => $Qid_tipo_enc,
//];
// el hppt_build_query no pasa los valores null
if (is_array($aQuery)) {
    array_walk($aQuery, 'core\poner_empty_on_null');
}
$pagina_nuevo = Hash::link('apps/encargossacd/controller/encargo_ver.php?' . http_build_query($aQuery));

$txt_eliminar = _("¿Esta Seguro que desea borrar este encargo?");

$oTabla = new Lista();
$oTabla->setId_tabla('encargo_select');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$no_tipo_enc = empty($Qid_tipo_enc) ? TRUE : FALSE;

$url_horario = "apps/encargossacd/controller/encargo_horario_select.php";
$oHashHorario = new Hash();
$oHashHorario->setUrl($url_horario);
$oHashHorario->setCamposForm('que!id_activ!id_nom');
$h_horario = $oHashHorario->linkSinVal();

$url_modificar = "apps/encargossacd/controller/encargo_ver.php";
$oHashMod = new Hash();
$oHashMod->setUrl($url_modificar);
$oHashMod->setCamposForm('que!scroll_id!sel');
$h_modificar = $oHashMod->linkSinVal();

$url_borrar = "apps/encargossacd/controller/encargo_ajax.php";
$oHashBorrar = new Hash();
$oHashBorrar->setUrl($url_borrar);
$oHashBorrar->setCamposForm('que!id_activ!id_nom');
$h_borrar = $oHashBorrar->linkSinVal();

$oHash = new Hash();
$oHash->setCamposForm('que');
$oHash->setcamposNo('scroll_id!sel');
/*
$a_camposHidden = array(
    'go_to' => $go_to,
);
$oHash->setArraycamposHidden($a_camposHidden);
*/
$Qtitulo = 'TITUL';

$a_campos = ['oPosicion' => $oPosicion,
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

$oView = new core\ViewTwig('encargossacd/controller');
$oView->renderizar('encargo_select.html.twig', $a_campos);
