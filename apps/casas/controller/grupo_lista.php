<?php
// INICIO Cabecera global de URL de controlador *********************************
use casas\model\entity\GestorGrupoCasa;
use web\Hash;
use web\Lista;
use ubis\model\entity\CasaDl;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos por esta url  **********************************************
// FIN de  Cabecera global de URL de controlador ********************************


$Qrefresh = (integer)filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

//Si vengo por medio de Posicion, borro la última
if (isset($_POST['stack'])) {
    $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack != '') {
        $oPosicion2 = new web\Posicion();
        if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
}

$aWhere = array();
$aOperador = array();

$GesGrupoCasa = new GestorGrupoCasa();
$cGrupoCasas = $GesGrupoCasa->getGrupoCasas($aWhere, $aOperador);

$a_cabeceras = [
    _("casa padre"),
    _("casa hijo"),
    ['name' => 'accion', 'formatter' => 'clickFormatter']
];

$a_botones = [];
$a_botones[] = array('txt' => _("eliminar"), 'click' => "fnjs_eliminar(\"#seleccionados\")");

$a_valores = array();
$i = 0;
foreach ($cGrupoCasas as $oGrupoCasa) {
    $i++;
    $id_item = $oGrupoCasa->getId_item();
    $id_ubi_padre = $oGrupoCasa->getId_ubi_padre();
    $oCasaPadre = new CasaDl($id_ubi_padre);
    $casa_padre = $oCasaPadre->getNombre_ubi();

    $id_ubi_hijo = $oGrupoCasa->getId_ubi_hijo();
    $oCasaHijo = new CasaDl($id_ubi_hijo);
    $casa_hijo = $oCasaHijo->getNombre_ubi();


    $pagina = web\Hash::link(core\ConfigGlobal::getWeb() . '/apps/casas/controller/grupo_form.php?' . http_build_query(array('id_item' => $id_item)));

    $a_valores[$i]['sel'] = "$id_item#";
    $a_valores[$i][1] = $casa_padre;
    $a_valores[$i][2] = $casa_hijo;
    $a_valores[$i][3] = array('ira' => $pagina, 'valor' => 'editar');
}
if (isset($Qid_sel) && !empty($Qid_sel)) {
    $a_valores['select'] = $Qid_sel;
}
if (isset($Qscroll_id) && !empty($Qscroll_id)) {
    $a_valores['scroll_id'] = $Qscroll_id;
}


$oTabla = new Lista();
$oTabla->setId_tabla('usuario_grupo_lista');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$oHash = new Hash();
$oHash->setCamposForm('sel');
$oHash->setCamposNo('sel!scroll_id!refresh');
$oHash->setArraycamposHidden(array('que' => 'eliminar'));


$aQuery = ['nuevo' => 1, 'quien' => 'grupo'];
$url_nuevo = web\Hash::link(core\ConfigGlobal::getWeb() . '/apps/casas/controller/grupo_form.php?' . http_build_query($aQuery));

$txt_eliminar = _("¿está seguro?");

$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'txt_eliminar' => $txt_eliminar,
    'oTabla' => $oTabla,
    'url_nuevo' => $url_nuevo,
];

$oView = new core\ViewTwig('casas/controller');
$oView->renderizar('grupo_lista.html.twig', $a_campos);