<?php
use usuarios\model\entity as usuarios;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// Crea los objectos por esta url  **********************************************
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();
	

$Qid_sel = (string) \filter_input(INPUT_POST, 'id_sel');
$Qscroll_id = (string) \filter_input(INPUT_POST, 'scroll_id');	
//Si vengo por medio de Posicion, borro la última
if (isset($_POST['stack'])) {
	$stack = \filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
	if ($stack != '') {
		$oPosicion2 = new web\Posicion();
		if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
			$Qid_sel=$oPosicion2->getParametro('id_sel');
			$Qscroll_id = $oPosicion2->getParametro('scroll_id');
			$oPosicion2->olvidar($stack);
		}
	}
}

$Qusername = empty($_POST['Qusername'])? '' : $_POST['Qusername'];

$oPosicion->setParametros(array('Qusername'=>$Qusername),1);

$oMiUsuario = new usuarios\Usuario(core\ConfigGlobal::mi_id_usuario());
$miRole=$oMiUsuario->getId_role();
$miSfsv=core\ConfigGlobal::mi_sfsv();

if ($miRole > 3) exit(_('no tiene permisos para ver esto')); // no es administrador
// filtro por sf/sv
$cond=array();
$operator = array();
if ($miRole != 1) {
	$cond['id_role'] = 1;
	$operator['id_role'] = '>=';
} else {
	$cond['id_role'] = 1;
	$operator['id_role'] = '>'; // para no tocar al administrador
}

if (!empty($Qusername)) {
	$cond['usuario'] = $Qusername;
	$operator['usuario'] = 'sin_acentos';
}

$oRole = new usuarios\Role();
$oGesUsuarios = new usuarios\GestorUsuario();
$oUsuarioColeccion= $oGesUsuarios->getUsuarios($cond,$operator);
/*
   *** FASES ***
$oGesFases = new GestorActividadFase();
$oDesplFases= $oGesFases->getListaActividadFases();
$oDesplFases->setNombre('fase');
*/

//default:
$id_usuario='';
$usuario='';
$nom_usuario='';
$miSfsv='';
$email='';
$role='';
$permiso = 1;

$a_cabeceras=array('usuario','nombre a mostrar','role','email',array('name'=>'accion','formatter'=>'clickFormatter'));
$a_botones[]=array( 'txt'=> _('borrar'), 'click'=>"fnjs_eliminar()");

$a_valores=array();
$i=0;
foreach ($oUsuarioColeccion as $oUsuario) {
	$i++;
	$id_usuario=$oUsuario->getId_usuario();
	$usuario=$oUsuario->getUsuario();
	$nom_usuario=$oUsuario->getNom_usuario();
	$email=$oUsuario->getEmail();
	$id_role=$oUsuario->getId_role();

	$oRole->setId_role($id_role);
	$oRole->DBCarregar();
	$role= $oRole->getRole();

	$pagina=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/usuarios/controller/usuario_form.php?'.http_build_query(array('quien'=>'usuario','id_usuario'=>$id_usuario)));

	$a_valores[$i]['sel']="$id_usuario#";
	$a_valores[$i][1]=$usuario;
	$a_valores[$i][2]=$nom_usuario;
	$a_valores[$i][3]=$role;
	$a_valores[$i][5]=$email;
	$a_valores[$i][6]= array( 'ira'=>$pagina, 'valor'=>'editar');
}
if (isset($Qid_sel) && !empty($Qid_sel)) { $a_valores['select'] = $Qid_sel; }
if (isset($Qscroll_id) && !empty($Qscroll_id)) { $a_valores['scroll_id'] = $Qscroll_id; }

$oTabla = new web\Lista();
$oTabla->setId_tabla('usuario_lista');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$oHash = new web\Hash();
$oHash->setcamposForm('Qusername');
$oHash->setcamposNo('scroll_id');
$oHash->setArraycamposHidden(array('quien'=>'usuario'));

$oHash1 = new web\Hash();
$oHash1->setcamposForm('sel');
$oHash1->setcamposNo('scroll_id');
$oHash1->setArraycamposHidden(array('que'=>'eliminar'));

$url_nuevo = web\Hash::link(core\ConfigGlobal::getWeb().'/apps/usuarios/controller/usuario_form.php?'.http_build_query(array('nuevo'=>1)));

$a_campos = [
			'oHash' => $oHash,
			'username' => $Qusername,
			'oHash1' => $oHash1,
			'oTabla' => $oTabla,
			'permiso' => $permiso,
			'url_nuevo' => $url_nuevo,
 			];

$oView = new core\View('usuarios/controller');
echo $oView->render('usuario_lista.phtml',$a_campos);