<?php
// si lo ejecuto dese el crontab.
use cambios\model\entity\Cambio;
use cambios\model\entity\GestorCambioUsuario;
use core\ConfigGlobal;
use usuarios\model\entity\Usuario;
use web\Lista;
use cambios\model\entity\CambioDl;

if(!empty($argv[1])) {
	$_POST['username'] = $argv[1];
	$_POST['password'] = $argv[2];
}

// INICIO Cabecera global de URL de controlador *********************************

require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// Crea los objectos para esta url  **********************************************
// FIN de  Cabecera global de URL de controlador ********************************

/* se ejecuta desde un cron (de momento) en el servidor exterior, que es el que tiene conexión al servidor de correo.
   Hay que hacerlo para todos los usuarios.
   Comprobar que tengan e-mail
*/

$dele = ConfigGlobal::mi_dele();
$delef = $dele.'f';
$aSecciones = array(1=>$dele,2=>$delef);

$email = '';
$aviso_tipo = 3; //e-mail

$aWhere = array();
$aWhere['_ordre'] = 'id_usuario,id_item_cambio';
$aWhere['aviso_tipo'] = $aviso_tipo;
$aWhere['avisado'] = 'f';
$GesCambiosUsuario = new GestorCambioUsuario();
$cCambiosUsuario = $GesCambiosUsuario->getCambiosUsuario($aWhere);
$i = 0;
$id_usuario_anterior = '';
$datos = array();
$id = array();
foreach ($cCambiosUsuario as $oCambioUsuario) {
	$id_usuario = $oCambioUsuario->getId_usuario();
	if ($id_usuario != $id_usuario_anterior) {
		// solo en el primer caso no lo hago
		if (!empty($id_usuario_anterior)) {
			enviar_mail($email,$datos,$id);
			$datos = array();
			$id = array();
		}
		$oMiUsuario = new Usuario($id_usuario);
		$mySecc = $oMiUsuario->getSfsv();
		$email = $oMiUsuario->getEmail();
		$id_usuario_anterior = $id_usuario;
	}
	if (empty($email)) continue;
	$id_item_cmb = $oCambioUsuario->getId_item_cambio();
	$oCambio = new CambioDl($id_item_cmb);
	$quien_cambia = $oCambio->getQuienCambia();
	$sfsv_quien_cambia = $oCambio->getSfsv_quienCambia();
	$timestamp_cambio = $oCambio->getTimestamp_cambio();
	$aviso_txt=$oCambio->getAvisoTxt();
	if ($aviso_txt === false) continue;
	$i++;
	if ($sfsv_quien_cambia == $mi_sfsv) {
	    $oUsuarioCmb = new usuario($quien_cambia);
	    $quien = $oUsuarioCmb->getUsuario();
	} else {
	    $quien = $aSecciones[$sfsv_quien_cambia] ;
	}
	$datos[$i][1] = $timestamp_cambio;
	$datos[$i][2] = $quien;
	$datos[$i][3] = $aviso_txt;
	$id[$i] = "$id_item_cmb,$id_usuario,$aviso_tipo";
}
// El último de la lista no se envia.
if (!empty($email)) enviar_mail($email,$datos,$id);

function enviar_mail($email,$datos,$id){
	$a_cabeceras=array( ucfirst(_("fecha cambio")),
						ucfirst(_("quien")),
						ucfirst(_("cambio"))
						);
	$oTabla = new Lista();
	$oTabla->setCabeceras($a_cabeceras);
	$oTabla->setDatos($datos);

	$asunto = _("Avisos de cambios en actividades"); 
	$cuerpo = ' 
	<html> 
	<head> 
	<title>Prueba de correo electronico</title> 
	</head> 
	<body>';
	$cuerpo .= $oTabla->lista();
	$cuerpo .= '</body></html>'; 

	//Envío en formato HTML 
	$headers = "MIME-Version: 1.0\r\n"; 
	$headers .= "Content-type: text/html; charset=utf-8\r\n"; 

	//Dirección del remitente 
	$headers .= "From: Actividades <no-Reply@moneders.net>\r\n"; 
	//Dirección de respuesta
	$headers .= "Reply-To: no-Reply@moneders.net\r\n"; 
	//Ruta del mensaje desde origen a destino 
	$headers .= "Return-path: no-Reply@moneders.net\r\n";


	// sólo si no lo ejecuto dese el crontab.
	if(empty($argv[1])) {
		// Me lo envia a root (el que ejecuta crontab).
		//echo "($email<br>$asunto<br>$cuerpo<br>$headers)<br>"; 
	}
	mail($email,$asunto,$cuerpo,$headers);
	eliminar_enviado($id);
}

function eliminar_enviado($a_id){
	foreach ($a_id as $id) {
		$ids = explode(',',$id);
		$id_item_cmb = $ids[0];
		$id_usuario = $ids[1];
		$aviso_tipo = $ids[2];
		$GesCambioUsuario = new GestorCambioUsuario();
		$cCambiosUsuario = $GesCambioUsuario->getCambiosUsuario(array('id_item_cambio'=>$id_item_cmb,'id_usuario'=>$id_usuario,'aviso_tipo'=>$aviso_tipo));
		foreach($cCambiosUsuario as $oCambioUsuario) {
			if ($oCambioUsuario ->DBEliminar() === false) {
				echo _("Hay un error, no se ha eliminado");
			}
		}
	}
}