<?php
use usuarios\model\entity as usuarios;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// Crea los objectos por esta url  **********************************************
// FIN de  Cabecera global de URL de controlador ********************************

$oMiUsuario = new usuarios\Usuario(core\ConfigGlobal::mi_id_usuario());
$miRole=$oMiUsuario->getId_role();
$sfsv = core\ConfigGlobal::mi_sfsv();

if ($miRole > 3) { 
	exit(_("no tiene permisos para ver esto")); // no es administrador
}

$Qusername = (string) \filter_input(INPUT_POST, 'username');

$oPosicion->setParametros(array('Qusername'=>$Qusername));
$oPosicion->recordar();

$aWhere = array();
$aOperador = array();
if (!empty($Qusername)) {
	$aWhere['usuario'] = $Qusername;
	$aOperador['usuario'] = 'sin_acentos';
}

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

$a_cabeceras=array(_("grupo"));
$a_botones[] = array( 'txt'=> _("borrar"),
					'click'=>"fnjs_eliminar(\"#seleccionados\")");
$a_botones[] = array( 'txt' => _("modificar"),
					'click' =>"fnjs_modificar(\"#seleccionados\")" );

$a_valores=array();
$i=0;
foreach ($oGrupoColeccion as $oGrupo) {
	$i++;
	$id_usuario=$oGrupo->getId_usuario();
	$usuario=$oGrupo->getUsuario();

	$a_valores[$i]['sel']="$id_usuario#";
	$a_valores[$i][1]=$usuario;
}
if (isset($Qid_sel) && !empty($Qid_sel)) { $a_valores['select'] = $Qid_sel; }
if (isset($Qscroll_id) && !empty($Qscroll_id)) { $a_valores['scroll_id'] = $Qscroll_id; }


$oTabla = new web\Lista();
$oTabla->setId_tabla('usuario_grupo_lista');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$oHashBuscar = new web\Hash();
$oHashBuscar->setcamposForm('Qusername');
$oHashBuscar->setcamposNo('scroll_id');
$oHashBuscar->setArraycamposHidden(array('quien'=>'grupo'));

$oHashSelect = new web\Hash();
$oHashSelect->setcamposForm('sel!que');
$oHashSelect->setcamposNo('scroll_id');
$oHashSelect->setArraycamposHidden(array('quien'=>'grupo'));

$url_nuevo = web\Hash::link(core\ConfigGlobal::getWeb().'/apps/usuarios/controller/grupo_form.php?'.http_build_query(array('nuevo'=>1)));
	
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