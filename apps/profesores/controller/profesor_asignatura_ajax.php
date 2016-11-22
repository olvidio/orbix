<?php
use profesores\model as profesores;
use personas\model as personas;

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

/*
$aGoBack = array (
				'loc'=>$loc,
				'que_lista'=>$que_lista,
				 );
$oPosicion->setParametros($aGoBack);
$oPosicion->recordar();
*/
	
$id_asignatura = empty($_POST['id_asignatura'])? '' : $_POST['id_asignatura'];
$GesProfesores = new profesores\GestorProfesor();
$cProfesores = $GesProfesores->getListaProfesoresAsignatura($id_asignatura);
/* $cProfesores es un array amb dos llistes:
	$Opciones['departamento']
	$Opciones['ampliacion']
 * 
 */

// De momento no se hace ninguna accion
//$a_botones=array( array( 'txt' => _('modificar'), 'click' =>"fnjs_modificar(this.form)" ) );
$a_botones=array();

$a_cabeceras[]= array('name'=>ucfirst(_('apellidos, nombre')),'formatter'=>'clickFormatter');
$a_cabeceras[]= ucfirst(_('centro'));
$a_cabeceras[]= ucfirst(_('teléfono'));
$a_cabeceras[]= ucfirst(_('mail'));
	  
$i=0;
$a_valores = array();
foreach ($cProfesores['departamento'] as $id_nom => $ap_nom) {
	$i++;
	$oPersonaDl = new personas\PersonaDl($id_nom);
	$centro = $oPersonaDl->getCentro_o_dl();
	$gesTelecoPersona = new personas\GestorTelecoPersonaDl();
	$cTelecoPersona = $gesTelecoPersona->getTelecos(array('id_nom'=>$id_nom));
	$telfs = '';	
	$mails = '';
	foreach($cTelecoPersona as $oTelecoPersona) {
		$tipo = $oTelecoPersona->getTipo_teleco();
		switch ($tipo){
			case 'mail':
			case 'e-mail':
				$mails .= $oTelecoPersona->getNum_teleco();
				break;
			case 'movil':
			case 'telf':
				$telfs .= $oTelecoPersona->getNum_teleco();
				break;
		}
	}
	
	$pagina = '';
	
	$a_valores[$i]['sel']="$id_nom";
	$a_valores[$i][1]= array( 'ira'=>$pagina, 'valor'=>$ap_nom);
	$a_valores[$i][2]=$centro;
	$a_valores[$i][3]=$telfs;
	$a_valores[$i][4]=$mails;

}

/*
$oHash = new web\Hash();
$oHash->setcamposForm('nombre!apellido1!apellido2!centro!exacto!cmb');
$oHash->setcamposNo('exacto!cmb');
$a_camposHidden = array(
		'tipo' => $_POST['tipo'],
		'tabla' => $tabla,
		'na' => $_POST['na'],
		'breve' => $_POST['breve'],
		'es_sacd' => $_POST['es_sacd'],
		'que' => $_POST['que']
		);
$oHash->setArraycamposHidden($a_camposHidden);
*/
$oTabla = new web\Lista();
$oTabla->setId_tabla('list_profe_asig');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);
echo $oTabla->mostrar_tabla();