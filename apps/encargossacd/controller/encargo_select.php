<?php

use core\ViewTwig;
use encargossacd\model\entity\GestorEncargo;
use encargossacd\model\entity\GestorEncargoTipo;
use src\usuarios\application\repositories\LocalRepository;
use web\Hash;
use web\Lista;
use ubis\model\entity\Ubi;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

//Si vengo por medio de Posicion, borro la última
if (isset($_POST['stack'])) {
    $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack !== '') {
        // No me sirve el de global_object, sino el de la session
        $oPosicion2 = new web\Posicion();
        if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
            //$id_ubi = $oPosicion2->getParametro('id_ubi');
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
}

$Qtitulo = (string)filter_input(INPUT_POST, 'titulo');
$Qid_tipo_enc = (integer)filter_input(INPUT_POST, 'id_tipo_enc');
$Qdesc_enc = (string)filter_input(INPUT_POST, 'desc_enc');

/*miro las condiciones. Si es la primera vez muestro las de este año */
$aWhere = [];
$aOperador = [];
$aWhere['_ordre'] = 'desc_enc';
if (!empty($Qdesc_enc)) {
    $aWhere['desc_enc'] = $Qdesc_enc;
    $aOperador['desc_enc'] = 'sin_acentos';
}
// si busco un tipo determinado de encargo (collatios)
if (!empty($Qid_tipo_enc)) {
    $aWhere['id_tipo_enc'] = $Qid_tipo_enc;
}

$GesEncargos = new GestorEncargo();
$cEncargos = $GesEncargos->getEncargos($aWhere, $aOperador);

$a_botones = array(array('txt' => _("horario"), 'click' => "fnjs_horario(\"#seleccionados\")"),
    array('txt' => _("modificar"), 'click' => "fnjs_modificar(\"#seleccionados\")"),
    array('txt' => _("eliminar"), 'click' => "fnjs_borrar(\"#seleccionados\")")
);

$a_cabeceras = array(_("sección"), array('name' => _("descripción"), 'formatter' => 'clickFormatter'), _("lugar"), _("descripción lugar"), _("idioma"));

$i = 0;
$a_valores = [];
if (isset($Qid_sel) && !empty($Qid_sel)) {
    $a_valores['select'] = $Qid_sel;
}
if (isset($Qscroll_id) && !empty($Qscroll_id)) {
    $a_valores['scroll_id'] = $Qscroll_id;
}
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
        $seccion = $a_seccion[$sf_sv]?? '?¿?';
    }

    $idioma = '';
    $LocalRepository = new LocalRepository();
    $cIdiomas = $LocalRepository->getLocales(['idioma' => $idioma_enc]);
    if (is_array($cIdiomas) && count($cIdiomas) > 0) {
        $idioma = $cIdiomas[0]->getNom_idioma();
    }

    if ($sf_sv === 2) $a_valores[$i]['clase'] = "tono2";

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

$aQuery = ['que' => 'nuevo',
    'id_tipo_enc' => $Qid_tipo_enc,
];
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

$oView = new ViewTwig('encargossacd/controller');
$oView->renderizar('encargo_select.html.twig', $a_campos);