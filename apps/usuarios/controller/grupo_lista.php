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
	
$oMiUsuario = new usuarios\Usuario(core\ConfigGlobal::mi_id_usuario());
$miRole=$oMiUsuario->getId_role();
$sfsv = core\ConfigGlobal::mi_sfsv();

if ($miRole > 3) { 
	exit(_("no tiene permisos para ver esto")); // no es administrador
}

// Se usa al buscar:
$Qusername = (string) \filter_input(INPUT_POST, 'username');

$oPosicion->setParametros(array('username'=>$Qusername),1);

$aWhere = array();
$aOperador = array();
if (!empty($Qusername)) {
	$aWhere['usuario'] = $Qusername;
	$aOperador['usuario'] = 'sin_acentos';
}
$aWhere['_ordre'] = 'usuario';

$oGesGrupos = new usuarios\GestorGrupo();
$oGrupoColeccion= $oGesGrupos->getGrupos($aWhere,$aOperador);

//default:
$id_usuario='';
$usuario='';
$permiso = 1;


if (isset($oGrupo) && is_object($oGrupo)) {
	$id_usuario=$oGrupo->getId_usuario();
	$usuario=$oGrupo->getUsuario();
}

$a_cabeceras=array(_("grupo"),array('name'=>'accion','formatter'=>'clickFormatter'));

$a_botones[] = array( 'txt'=> _("borrar"), 'click'=>"fnjs_eliminar()");

$a_valores=array();
$i=0;
foreach ($oGrupoColeccion as $oGrupo) {
	$i++;
	$id_usuario=$oGrupo->getId_usuario();
	$usuario=$oGrupo->getUsuario();

	
	$pagina=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/usuarios/controller/grupo_form.php?'.http_build_query(array('quien'=>'grupo','id_usuario'=>$id_usuario)));
	
	$a_valores[$i]['sel']="$id_usuario#";
	$a_valores[$i][1]=$usuario;
	$a_valores[$i][2]= array( 'ira'=>$pagina, 'valor'=>'editar');
}
if (isset($Qid_sel) && !empty($Qid_sel)) { $a_valores['select'] = $Qid_sel; }
if (isset($Qscroll_id) && !empty($Qscroll_id)) { $a_valores['scroll_id'] = $Qscroll_id; }


$oTabla = new web\Lista();
$oTabla->setId_tabla('usuario_grupo_lista');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$oHashBuscar = new web\Hash();
$oHashBuscar->setcamposForm('username');
$oHashBuscar->setcamposNo('scroll_id');
$oHashBuscar->setArraycamposHidden(array('quien'=>'grupo'));

$oHashSelect = new web\Hash();
$oHashSelect->setcamposForm('sel');
$oHashSelect->setcamposNo('scroll_id');
$oHashSelect->setArraycamposHidden(array('que'=>'eliminar_grupo'));

$aQuery = [ 'nuevo' => 1, 'quien' => 'grupo' ];
$url_nuevo = web\Hash::link(core\ConfigGlobal::getWeb().'/apps/usuarios/controller/grupo_form.php?'.http_build_query($aQuery));
	
$a_campos = [
			'oHashBuscar' => $oHashBuscar,
			'username' => $Qusername,
			'oHashSelect' => $oHashSelect,
			'oTabla' => $oTabla,
			'permiso' => $permiso,
			'url_nuevo' => $url_nuevo,
 			];

$oView = new core\View('usuarios/controller');
echo $oView->render('grupo_lista.phtml',$a_campos);