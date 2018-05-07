<?php
/**
 * Este controlador muestra una tabla con las personas que tienen la actividad
 * (ca|crt) pendiente para este curso.
 *
 *
 *@package	orbix
 *@subpackage	asistentes
 *@author	Daniel Serrabou
 *@since		7/11/03.
 *@ajax		23/8/2007.		
 *		
 */

use actividades\model\entity as actividades;
use asistentes\model\entity as asistentes;
use personas\model\entity as personas;

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************
 
$Qany = (integer)  \filter_input(INPUT_POST, 'any');
$Qtipo_personas = (string)  \filter_input(INPUT_POST, 'tipo_personas');
$Qsactividad = (string)  \filter_input(INPUT_POST, 'sactividad');

/*miro las condiciones. Si es la primera vez muestro las de este año */
if (empty($Qany)) { $any=date("Y"); } else { $any=$Qany; }
// curso
switch ($any) {
	case date("Y"):
		$txt_curso_1=($any-1)."/".$any;
		$chk_any_1="selected";
		$chk_any_2="";
		break;
	case (date("Y")+1):
		$chk_any_1="";
		$chk_any_2="selected";
		break;
}
$any_real=date("Y");
$txt_curso_1=($any_real-1)."/".$any_real;
$txt_curso_2=($any_real)."/".($any_real+1);
$txt_curso=($any-1)."/".$any;

// tipo de personas
$chk_n = '';
$chk_agd = '';
$chk_sacd = '';
switch ($Qtipo_personas) {
	case "n":
		$chk_n="selected";
		break;
	case "agd":
		$chk_agd="selected";
		break;
	case "sacd":
		$chk_sacd="selected";
		break;
}

$mi_dele = core\ConfigGlobal::mi_dele();
// tipo de actividad
$chk_ca = '';
$chk_crt = '';
switch ($Qsactividad) {
	case 'ca':
		if ($Qtipo_personas=='n') $id_tipo_activ='(112...)|(133...)';
		if ($Qtipo_personas=='sacd') $id_tipo_activ='(112...)|(133...)';
		if ($Qtipo_personas=='agd') $id_tipo_activ='133...';
		if ($Qtipo_personas=='stgr') $id_tipo_activ='(112...)|(133...)';
		$chk_ca="selected";
		$inicurs=core\curso_est("inicio",$any,"est");
		$fincurs=core\curso_est("fin",$any,"est");
		break;
	case 'crt':
		// 22.1.09 quito a los que han hecho el crt con sr
		if ($Qtipo_personas=='n') $id_tipo_activ='1[137]1...';
		if ($Qtipo_personas=='agd') $id_tipo_activ='131...';
		if ($Qtipo_personas=='sacd') $id_tipo_activ='1[13]1...';
		$chk_crt='selected';
		$inicurs=core\curso_est('inicio',$any,'crt');
		$fincurs=core\curso_est('fin',$any,'crt');
		break;
}
// Actividades del curso y tipo:
$aWhereA['id_tipo_activ'] = $id_tipo_activ;
$aOperadorA['id_tipo_activ'] = '~';
$aWhereA['f_ini'] = "'$inicurs','$fincurs'";
$aOperadorA['f_ini'] = 'BETWEEN';
$GesActividades = new actividades\GestorActividad();
$cActividades = $GesActividades->getActividades($aWhereA,$aOperadorA);
$aAsistentes = array();
foreach ($cActividades as $oActividad) {
	$id_activ = $oActividad->getId_activ();
	// Asistentes:
	$GesAsistentes = new asistentes\GestorAsistente();
	$cAsistentes = $GesAsistentes->getAsistentes(array('id_activ'=>$id_activ,'propio'=>'t')); 
	foreach ($cAsistentes as $oAsistente) {
		$aAsistentes[]=$oAsistente->getId_nom();
	}
}
// Personas que deberían haber hecho la actividad:
switch ($Qtipo_personas) {
	case "n":
		$GesPersonas = new personas\GestorPersonaN();
		$cPersonas = $GesPersonas->getPersonas(array('situacion'=>'A', 'dl'=>$mi_dele));
		$obj_pau = 'PersonaN';
		break;
	case "agd":
		$GesPersonas = new personas\GestorPersonaAgd();
		$cPersonas = $GesPersonas->getPersonas(array('situacion'=>'A', 'dl'=>$mi_dele));
		$obj_pau = 'PersonaAgd';
		break;
	case "sacd":
		$GesPersonas = new personas\GestorPersonaDl();
		$cPersonas = $GesPersonas->getPersonas(array('sacd'=>'t','situacion'=>'A', 'dl'=>$mi_dele));
		$obj_pau = 'PersonaDl';
		break;
}

foreach ($cPersonas as $oPersona) {
	$id_nomP = $oPersona->getId_nom();
	if (in_array($id_nomP, $aAsistentes)) continue;
	$ap_nom = $oPersona->getApellidosNombre();
	$aFaltan[$ap_nom] = $id_nomP;
}
uksort($aFaltan,"core\strsinacentocmp");

$titulo=ucfirst(sprintf(_("lista de %s sin %s en el curso %s"),$Qtipo_personas,$Qsactividad,$txt_curso));

$a_cabeceras=array( _("nº"),array('name'=>ucfirst(_("nombre de la persona")),'formatter'=>'clickFormatter'));
$i=0;
foreach ($aFaltan as $ap_nom=>$id_nom) {
	$i++;
	
	$aQuery = array('obj_pau'=>$obj_pau,'id_nom'=>$id_nom);
	$pagina=web\Hash::link('apps/personas/controller/home_persona.php?'.http_build_query($aQuery));

	$a_valores[$i][1]=$i;
	$a_valores[$i][2]= array( 'ira'=>$pagina, 'valor'=>$ap_nom);
}


// Al final añado la lista de personas que no estan en la dl, pero dependen de aqui.
//  (probablemente haran la actividad en su region actual)
$aWhere['situacion'] = 'A';
$aWhere['dl'] = $mi_dele;
$aOperador['dl'] = '!=';
switch ($Qtipo_personas) {
	case "n":
		$GesPersonas = new personas\GestorPersonaN();
		$cPersonasOtras = $GesPersonas->getPersonas($aWhere,$aOperador);
		break;
	case "agd":
		$GesPersonas = new personas\GestorPersonaAgd();
		$cPersonasOtras = $GesPersonas->getPersonas($aWhere,$aOperador);
		break;
	case "sacd":
		$aWhere['sacd'] = 't';
		$GesPersonas = new personas\GestorPersonaDl();
		$cPersonasOtras = $GesPersonas->getPersonas($aWhere,$aOperador);
		break;
}

foreach ($cPersonasOtras as $oPersona) {
	$id_nomP = $oPersona->getId_nom();
	if (in_array($id_nomP, $aAsistentes)) continue;
	$ap_nom = $oPersona->getApellidosNombre();
	$aFaltanOtras[$ap_nom] = $id_nomP;
}
uksort($aFaltanOtras,"core\strsinacentocmp");

$i=0;
foreach ($aFaltanOtras as $ap_nom=>$id_nom) {
	$i++;
	
	$aQuery = array('obj_pau'=>$obj_pau,'id_nom'=>$id_nom);
	$pagina=web\Hash::link('apps/personas/controller/home_persona.php?'.http_build_query($aQuery));

	$a_valores_2[$i][1]=$i;
	$a_valores_2[$i][2]= array( 'ira'=>$pagina, 'valor'=>$ap_nom);
}



$oHash = new web\Hash();
$oHash->setcamposForm('tipo_personas!sactividad!any');

$oTablaDl = new web\Lista();
$oTablaDl->setId_tabla('activ_pendientes_select');
$oTablaDl->setCabeceras($a_cabeceras);
$oTablaDl->setDatos($a_valores);

$oTablaOtrasDl = new web\Lista();
$oTablaOtrasDl->setId_tabla('activ_pendientes_select_otras');
$oTablaOtrasDl->setCabeceras($a_cabeceras);
$oTablaOtrasDl->setDatos($a_valores_2);

$a_campos = [
			'oHash' => $oHash,
			'chk_n' => $chk_n,
			'chk_agd' => $chk_agd,
			'chk_sacd' => $chk_sacd,
			'chk_ca' => $chk_ca,
			'chk_crt' => $chk_crt,
			'any_real' => $any_real,
			'chk_any_1' => $chk_any_1,
			'txt_curso_1' => $txt_curso_1,
			'chk_any_2' => $chk_any_2,
			'txt_curso_2' => $txt_curso_2,
			'titulo' => $titulo,
			'oTablaDl' => $oTablaDl,
			'oTablaOtrasDl' => $oTablaOtrasDl,
			];

$oView = new core\View('asistentes/controller');
echo $oView->render('activ_pendientes.phtml',$a_campos);