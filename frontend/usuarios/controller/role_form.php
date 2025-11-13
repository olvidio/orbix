<?php

use core\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use web\Desplegable;
use web\Hash;
use web\Lista;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qrefresh = (integer)filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

$Qid_role = (string)filter_input(INPUT_POST, 'id_role');

$Qid_sel = '';
$Qscroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
// Hay que usar isset y empty porque puede tener el valor =0.
// Si vengo por medio de Posicion, borro la última
if (isset($_POST['stack'])) {
    $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack != '') {
        // No me sirve el de global_object, sino el de la session
        $oPosicion2 = new web\Posicion();
        if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
            $a_sel = $oPosicion2->getParametro('sel');
            $Qid_role = (integer)strtok($a_sel[0], "#");
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
} elseif (!empty($a_sel)) { //vengo de un checkbox
    $Qque = (string)filter_input(INPUT_POST, 'que');
    if ($Qque !== 'grupmenu_del') { //En el caso de venir de borrar un grupmenu, no hago nada
        $Qid_role = (integer)strtok($a_sel[0], "#");
        // el scroll id es de la página anterior, hay que guardarlo allí
        $oPosicion->addParametro('id_sel', $a_sel, 1);
        $Qscroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
        $oPosicion->addParametro('scroll_id', $Qscroll_id, 1);
    }
}
$oPosicion->setParametros(array('id_role' => $Qid_role), 1);


$url_backend = '/src/usuarios/infrastructure/controllers/role_info.php';
$a_campos_backend = ['id_role' => $Qid_role];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);
if (isset($data['error'])) {
    exit($data['error']);
}

$a_cabeceras = $data['a_cabeceras'];
$a_botones = $data['a_botones'];
$a_valores = $data['a_valores'];

$role = $data['role'];
$sf = $data['sf'];
$chk_sf = $data['chk_sf'];
$sv = $data['sv'];
$chk_sv = $data['chk_sv'];
$pau = $data['pau'];
$dmz = $data['dmz'];
$chk_dmz = $data['chk_dmz'];
$permiso = $data['permiso'];
$txt_sfsv = $data['txt_sfsv'];
$aOpcionesPau = $data['aOpcionesPau'];

$oDesplPau = new Desplegable();
$oDesplPau->setNombre('pau');
$oDesplPau->setBlanco(TRUE);
$oDesplPau->setOpciones($aOpcionesPau);
$oDesplPau->setOpcion_sel($pau);

$oTabla = new Lista();
$oTabla->setId_tabla('role_grupmenu');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$oHash = new Hash();
$oHash->setCamposForm('que!role!sf!sv!pau!dmz');
$oHash->setcamposNo('sf!sv!dmz!refresh');
$a_camposHidden = array(
    'id_role' => $Qid_role,
);
$oHash->setArraycamposHidden($a_camposHidden);

$oHash1 = new Hash();
$oHash1->setCamposForm('que!sel');
$oHash1->setcamposNo('scroll_id!refresh');
$a_camposHidden = array(
    'id_role' => $Qid_role,
);
$oHash1->setArraycamposHidden($a_camposHidden);


$txt_guardar = _("guardar datos rol");
$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'oHash1' => $oHash1,
    'Qid_role' => $Qid_role,
    'role' => $role,
    'txt_sfsv' => $txt_sfsv,
    'permiso' => $permiso,
    'chk_sf' => $chk_sf,
    'chk_sv' => $chk_sv,
    'oDesplPau' => $oDesplPau,
    'chk_dmz' => $chk_dmz,
    'txt_guardar' => $txt_guardar,
    'oTabla' => $oTabla,
];

$oView = new ViewNewPhtml('frontend\usuarios\controller');
$oView->renderizar('role_form.phtml', $a_campos);