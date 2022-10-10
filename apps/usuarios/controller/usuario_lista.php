<?php

use usuarios\model\entity as usuarios;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos por esta url  **********************************************
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();


$Qid_sel = (string)filter_input(INPUT_POST, 'id_sel');
$Qscroll_id = (string)filter_input(INPUT_POST, 'scroll_id');
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

// Se usa al buscar:
$Qusername = (string)filter_input(INPUT_POST, 'username');

$oPosicion->setParametros(array('username' => $Qusername), 1);

$oMiUsuario = new usuarios\Usuario(core\ConfigGlobal::mi_id_usuario());
$miRole = $oMiUsuario->getId_role();
$miSfsv = core\ConfigGlobal::mi_sfsv();

if ($miRole > 3) exit(_("no tiene permisos para ver esto")); // no es administrador
$aWhere = array();
$aOperador = array();
if ($miRole != 1) { // id_role=1 => SuperAdmin.
    $aWhere['id_role'] = 1;
    $aOperador['id_role'] = '!='; // para no tocar al administrador
}


if (!empty($Qusername)) {
    $aWhere['usuario'] = $Qusername;
    $aOperador['usuario'] = 'sin_acentos';
}
$aWhere['_ordre'] = 'usuario';

$oRole = new usuarios\Role();
$oGesUsuarios = new usuarios\GestorUsuario();
$oUsuarioColeccion = $oGesUsuarios->getUsuarios($aWhere, $aOperador);
/*
   *** FASES ***
$oGesFases = new GestorActividadFase();
$oDesplFases= $oGesFases->getListaActividadFases();
$oDesplFases->setNombre('fase');
*/

//default:
$id_usuario = '';
$usuario = '';
$nom_usuario = '';
$email = '';
$role = '';
$permiso = 1;

$a_cabeceras = array('usuario', 'nombre a mostrar', 'role', 'email', array('name' => 'accion', 'formatter' => 'clickFormatter'));
$a_botones[] = array('txt' => _("borrar"), 'click' => "fnjs_eliminar()");

$a_valores = array();
$i = 0;
foreach ($oUsuarioColeccion as $oUsuario) {
    $i++;
    $id_usuario = $oUsuario->getId_usuario();
    $usuario = $oUsuario->getUsuario();
    $nom_usuario = $oUsuario->getNom_usuario();
    $email = $oUsuario->getEmail();
    $id_role = $oUsuario->getId_role();

    if (!empty($id_role)) {
        // Cuando se ha eliminado el Role, el usuario todavia tiene el id, pero no existe:
        $oRole->setId_role($id_role);
        $oRole->DBCarregar();
        $role = $oRole->getRole();
        // filtro por sf/sv
        if ($miSfsv == 1) {
            $role_sv = $oRole->getSv();
            if ($role_sv === FALSE) {
                continue;
            }
        }
        if ($miSfsv == 2) {
            $role_sf = $oRole->getSf();
            if ($role_sf === FALSE) {
                continue;
            }
        }
    } else {
        $role = '?';
    }

    $pagina = web\Hash::link(core\ConfigGlobal::getWeb() . '/apps/usuarios/controller/usuario_form.php?' . http_build_query(array('quien' => 'usuario', 'id_usuario' => $id_usuario)));

    $a_valores[$i]['sel'] = "$id_usuario#";
    $a_valores[$i][1] = $usuario;
    $a_valores[$i][2] = $nom_usuario;
    $a_valores[$i][3] = $role;
    $a_valores[$i][5] = $email;
    $a_valores[$i][6] = array('ira' => $pagina, 'valor' => 'editar');
}
if (isset($Qid_sel) && !empty($Qid_sel)) {
    $a_valores['select'] = $Qid_sel;
}
if (isset($Qscroll_id) && !empty($Qscroll_id)) {
    $a_valores['scroll_id'] = $Qscroll_id;
}

$oTabla = new web\Lista();
$oTabla->setId_tabla('usuario_lista');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$oHash = new web\Hash();
$oHash->setCamposForm('username');
$oHash->setcamposNo('scroll_id');
$oHash->setArraycamposHidden(array('quien' => 'usuario'));

$oHash1 = new web\Hash();
$oHash1->setCamposForm('sel');
$oHash1->setcamposNo('scroll_id');
$oHash1->setArraycamposHidden(array('que' => 'eliminar'));

$aQuery = ['nuevo' => 1, 'quien' => 'usuario'];
$url_nuevo = web\Hash::link(core\ConfigGlobal::getWeb() . '/apps/usuarios/controller/usuario_form.php?' . http_build_query($aQuery));

$a_campos = [
    'oHash' => $oHash,
    'username' => $Qusername,
    'oHash1' => $oHash1,
    'oTabla' => $oTabla,
    'permiso' => $permiso,
    'url_nuevo' => $url_nuevo,
];

$oView = new core\View('usuarios/controller');
echo $oView->render('usuario_lista.phtml', $a_campos);
