<?php
/*
* si lo ejecuto dese el crontab.
* OJO: poner en  '/etc/php/7.2/cli/php.ini'
*       include_path = ".:/usr/share/php:/home/dani/orbix_local/orbix"
*
*/


require_once ('/var/www/orbix/apps/cambios/model/entity/gestorcambiousuario.class.php');
require_once ('/var/www/orbix/apps/cambios/model/entity/cambiodl.class.php');
require_once ('/var/www/orbix/apps/core/configglobal.class.php');
require_once ('/var/www/orbix/apps/core/configdb.class.php');
require_once ('/var/www/orbix/apps/core/configdbconnection.class.php');
require_once ('/var/www/orbix/apps/usuarios/model/entity/usuario.class.php');
require_once ('/var/www/orbix/apps/web/lista.class.php');


// public para todo el mundo
$oConfigDB = new ConfigDB('comun'); //de la database comun

$config = $oConfigDB->getEsquema('public');
$oConexion = new dbConnection($config);
$oDBPC = $oConexion->getPDO();

$config = $oConfigDB->getEsquema('resto');
$oConexion = new dbConnection($config);
$oDBRC = $oConexion->getPDO();

//sv
$esquemav = "H-dlbv";
$esquema = \substr($esquemav, 0, -1);
$esquemaf = $esquema.'f';
//comun
$oConfigDB->setDataBase('comun');
$config = $oConfigDB->getEsquema($esquema);
$oConexion = new dbConnection($config);
$oDBC = $oConexion->getPDO();
//sv exterior
$oConfigDB->setDataBase('sv-e');
$config = $oConfigDB->getEsquema($esquemav);
$oConexion = new dbConnection($config);
$oDBE = $oConexion->getPDO();

$config = $oConfigDB->getEsquema('publicv');
$oConexion = new dbConnection($config);
$oDBEP = $oConexion->getPDO();
        
/* Hay que pasarle los argumentos que no tienen si se le llama por command line:
 $username;
 $password;
 $dir_web = orbix | pruebas;
 document_root = /home/dani/orbix_local
 $esquema_web = 'H-dlbv';
 $ubicacion = 'sv';
 */
$username = '';
if(!empty($argv[1])) {
    $_POST['username'] = $argv[1];
    $_POST['password'] = $argv[2];
    $username = $argv[1];
}


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
	if ($sfsv_quien_cambia == ConfigGlobal::mi_sfsv()) {
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
	$headers .= "From: Aquinate <no-Reply@moneders.net>\r\n"; 
	//Dirección de respuesta
	$headers .= "Reply-To: no-Reply@moneders.net\r\n"; 
	//Ruta del mensaje desde origen a destino 
	$headers .= "Return-path: no-Reply@moneders.net\r\n";


	// sólo si no lo ejecuto dese el crontab.
	if(empty($username)) {
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
