<?php
use personas\model as personas;
use ubis\model as ubis;
/**
* Página de selección de las personas para las que se trazará un planning 
* Presenta una lista de personas que cumplen la condición fijada en el formulario
* podemos venir de la página plannig_que.php o por go_to (atrás).
* Condiciones:
*	por formulario:
*		apellido1, apellido2, nombre, centro
*		periodo, year -> se calcula $inicio y $fin que son las que se pasan.
*	por menu:
*		na -> 'n' o 'a' para distinguir numerarios o agregados de paso
*		tabla-> 'p_de_paso' (de momento sólo he encontrado esta condicion)
* 
* 
*
*@package	delegacion
*@subpackage	actividades
*@author	Josep Companys
*@since		15/5/02.
*		
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$stack = (integer)  filter_input(INPUT_POST, 'stack');
//Si vengo de vuelta de un go_to:
if (!empty($stack)) {
	$oPosicion->goStack($stack);
	$Qtipo = $oPosicion->getParametro('tipo');
	$Qobj_pau = $oPosicion->getParametro('obj_pau');
	$Qna = $oPosicion->getParametro('na');
	$Qinicio = $oPosicion->getParametro('inicio');
	$Qfin=$oPosicion->getParametro('fin');
	$Qperiodo=$oPosicion->getParametro('periodo');
	$Qyear=$oPosicion->getParametro('year');
	$QsaWhere= $oPosicion->getParametro('saWhere');
	$QsaOperador=$oPosicion->getParametro('saOperador');
	$QsaWhereCtr=$oPosicion->getParametro('saWhereCtr');
	$QsaOperadorCtr=$oPosicion->getParametro('saOperadorCtr');
	$Qid_sel=$oPosicion->getParametro('id_sel');
	$Qscroll_id = $oPosicion->getParametro('scroll_id');

	$aWhere=unserialize(base64_decode($QsaWhere));
	$aOperador=unserialize(base64_decode($QsaOperador));
	$aWhereCtr=unserialize(base64_decode($QsaWhereCtr));
	$aOperadorCtr=unserialize(base64_decode($QsaOperadorCtr));
} else { //si no vengo por goto.
	$Qmodo = empty($_POST['modo'])? '' : $_POST['modo'];
	$Qtipo=empty($_POST['tipo'])? '' : $_POST['tipo'];
	$Qobj_pau=empty($_POST['obj_pau'])? '' : $_POST['obj_pau'];
	$Qna=empty($_POST['na'])? '' : $_POST['na'];
	$Qinicio=empty($_POST['inicio'])? '' : $_POST['inicio'];
	$Qfin=empty($_POST['fin'])? '' : $_POST['fin'];
	$Qyear=empty($_POST['year'])? '' : $_POST['year'];
	$Qperiodo = empty($_POST['periodo'])? '' : $_POST['periodo'];
	$Qempiezamin = empty($_POST['empiezamin'])? date('d/m/Y',mktime(0, 0, 0, date('m'), date('d')-40, date('Y'))) : $_POST['empiezamin'];
	$Qempiezamax = empty($_POST['empiezamax'])? date('d/m/Y',mktime(0, 0, 0, date('m')+9, 0, date('Y'))) : $_POST['empiezamax'];

	if (empty($Qperiodo) || $Qperiodo == 'otro') {
		$Qinicio = empty($Qinicio)? $Qempiezamin : $Qinicio;
		$Qfin = empty($Qfin)? $Qempiezamax : $Qfin;
	} else {
		$oPeriodo = new web\Periodo();
		$any=empty($Qyear)? date('Y')+1 : $Qyear;
		$oPeriodo->setAny($any);
		$oPeriodo->setPeriodo($Qperiodo);
		$inicio = $oPeriodo->getF_ini();
		$fin = $oPeriodo->getF_fin();
	}

	/*miro las condiciones. las variables son: num, agd, sup, nombre, apellido1, apellido2 */
	$aWhere = array();	
	$aOperador = array();	
	$aWhereCtr = array();	
	$aOperadorCtr = array();	
	$aWhere['situacion']= 'A';	
	$aWhere['_ordre']= 'apellido1,apellido2,nom';	
	if (!empty($_POST['apellido1'])){ 
		$aWhere['apellido1']= "^".$_POST['apellido1'];	
		$aOperador['apellido1']='sin_acentos';
	}
	if (!empty($_POST['apellido2'])){ 
		$aWhere['apellido2']= "^".$_POST['apellido2'];	
		$aOperador['apellido2']='sin_acentos';
	}
	if (!empty($_POST['nombre'])){ 
		$aWhere['nom']= "^".$_POST['nombre'];	
		$aOperador['nom']='sin_acentos';
	}
		
	/*Si está puesto el nombre del centro, saco una lista de todos los del centro*/
	if (!empty($_POST['centro'])){ 
		$nom_ubi = str_replace("+", "\+", $_POST['centro']); // para los centros de la sss+
		$aWhereCtr['nombre_ubi']= $nom_ubi;	
		$aOperadorCtr['nombre_ubi']='sin_acentos';
	}
	// Estos valores vienen por el menu
	if (!empty($_POST['na'])) {
		$aWhere['id_tabla']='p'.$_POST['na'] ;
	}
	$QsaWhere=base64_encode(serialize($aWhere));
	$QsaOperador=base64_encode(serialize($aOperador));
	$QsaWhereCtr=base64_encode(serialize($aWhereCtr));
	$QsaOperadorCtr=base64_encode(serialize($aOperadorCtr));
}
?>
<?php

if (!empty($aWhereCtr)) { // si busco por centro sólo puede ser de casa
	$GesCentroDl = new ubis\GestorCentroDl();
	$cCentros = $GesCentroDl->getCentros($aWhereCtr,$aOperadorCtr);
	// por si hay más de uno.
	$cPersonas=array();
	foreach ($cCentros as $oCentro) {
		$id_ubi=$oCentro->getId_ubi();
		$aWhere['id_ctr']=$id_ubi;
		if (!isset($aOperador))  $aOperador=array();
		$GesPersonas = new personas\GestorPersonaDl();
		$cPersonas2 = $GesPersonas->getPersonasDl($aWhere,$aOperador);
		if (is_array($cPersonas2) && count($cPersonas2)>=1) {
			if (is_array($cPersonas)) {
				$cPersonas = $cPersonas + $cPersonas2;
			} else {
				$cPersonas = $cPersonas2;
			}
		}
	}
} else {
	switch ($Qobj_pau) {
		case 'PersonaN':
			$GesPersonas = new personas\GestorPersonaN();
		break;
		case 'PersonaAgd':
			$GesPersonas = new personas\GestorPersonaAgd();
		break;
		case 'PersonaNax':
			$GesPersonas = new personas\GestorPersonaNax();
		break;
		case 'PersonaS':
			$GesPersonas = new personas\GestorPersonaS();
		break;
		case 'PersonaSSSC':
			$GesPersonas = new personas\GestorPersonaSSSC();
		break;
		case 'PersonaDl':
			$GesPersonas = new personas\GestorPersonaDl();
		break;
		default:
			$GesPersonas = new personas\GestorPersonaDl();
	}
	$cPersonas = $GesPersonas->getPersonas($aWhere,$aOperador);
}

/*
* Defino un array con los datos actuales, para saber volver después de navegar un rato
*/
$aGoBack = array (
				'tipo'=>$Qtipo,
				'obj_pau'=>$Qobj_pau,
				'na'=>$Qna,
				'inicio'=>$Qinicio,
				'fin'=>$Qfin,
				'periodo'=>$Qperiodo,
				'year'=>$Qyear,
				'saWhere'=>$QsaWhere,
				'saOperador'=>$QsaOperador,
				'saWhereCtr'=>$QsaWhereCtr,
				'saOperadorCtr'=>$QsaOperadorCtr
				 );
$oPosicion->setParametros($aGoBack);
$oPosicion->recordar();

$a_botones=array(
				array( 'txt' => _('vista tabla'), 'click' =>"fnjs_ver_planning(\"#seleccionados\",1)" ) ,
				array( 'txt' => _('vista grid'), 'click' =>"fnjs_ver_planning(\"#seleccionados\",3)" ) ,
				array( 'txt' => _('vista para imprimir'), 'click' =>"fnjs_planning_print(\"#seleccionados\")" ) ,
				array( 'txt' => _('ver actividades'), 'click' =>"fnjs_actividades(\"#seleccionados\")" )
			);
$a_cabeceras=array( _("tipo"),
					array('name'=>_("nombre y apellidos"),'formatter'=>'clickFormatter'),
					_("centro")
			);

$i=0;
$a_valores=array();
if (isset($Qid_sel) &&!empty($Qid_sel)) { $a_valores['select'] = $Qid_sel; }
if (isset($Qscroll_id) && !empty($Qscroll_id)) { $a_valores['scroll_id'] = $Qscroll_id; }
foreach ($cPersonas as $oPersona) {
	$i++;
	$id_nom=$oPersona->getId_nom();
	$id_tabla=$oPersona->getId_tabla();
	$nom=$oPersona->getApellidosNombre();
	$ctr_o_dl=$oPersona->getCentro_o_dl();
	$condicion_2="Where id_nom='".$id_nom."'";
	$condicion_2=urlencode($condicion_2);
	$pagina="programas/dossiers/home_persona.php?id_nom=$id_nom&condicion=$condicion_2&id_tabla=$id_tabla";

	$a_valores[$i]['sel']="$id_nom";
	$a_valores[$i][1]=$id_tabla;
	$a_valores[$i][2]= array( 'ira'=>$pagina, 'valor'=>$nom);
	$a_valores[$i][3]=$ctr_o_dl;
}

$oHash = new web\Hash();
$oHash->setcamposNo('sel!scroll_id!modelo!que!id_dossier');
$a_camposHidden = array(
		'tipo' => $Qtipo,
		'obj_pau' => $Qobj_pau,
		'na' => $Qna,
		'inicio' => $Qinicio,
		'fin' => $Qfin,
		'periodo' => $Qperiodo,
		'year' => $Qyear,
		'pau' => 'p',
		);
$oHash->setArraycamposHidden($a_camposHidden);

/* ---------------------------------- html --------------------------------------- */
$resultado=sprintf( _("%s personas encontradas"),$i);
?>
<script>

fnjs_ver_planning=function(formulario,n){
	$('#modelo').val(n);
	$(formulario).attr('action',"apps/asistentes/controller/planning_crida_calendari.php");
	fnjs_enviar_formulario(formulario);
}
fnjs_planning_print=function(formulario){
	rta=fnjs_solo_uno(formulario);
	if (rta==1) {
		$('#modelo').val('2');
		$(formulario).attr('target','print');
  		$(formulario).attr('action',"apps/asistentes/controller/planning_crida_calendari.php");
  		fnjs_enviar_formulario(formulario);
  	}
}
fnjs_actividades=function(formulario){
	rta=fnjs_solo_uno(formulario);
	if (rta==1) {
		$('#que').val("activ");
		$('#id_dossier').val("1301y1302");
  		$(formulario).attr('action',"apps/dossiers/controller/dossiers_ver.php");
  		fnjs_enviar_formulario(formulario);
  	}
}
</script>
<h3><?= $resultado ?></h3>
<form id='seleccionados' name='seleccionados' action='' method='post'>
<?= $oHash->getCamposHtml(); ?>
<input type="hidden" id="modelo" name="modelo" value="">
<input type='hidden' id='que' name='que' value=''>
<input type='hidden' id='id_dossier' name='id_dossier' value=''>
<?php
$oTabla = new web\Lista();
$oTabla->setId_tabla('planning_select');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);
echo $oTabla->mostrar_tabla();
?>
</form>
