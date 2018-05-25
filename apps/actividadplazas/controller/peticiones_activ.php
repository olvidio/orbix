<?php
use actividades\model\entity as actividades;
use personas\model\entity as personas;
use ubis\model\entity as ubis;

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qtodos = (string) \filter_input(INPUT_POST, 'todos');

$oPosicion->recordar();
//Si vengo de actualizar borro la ultima posicion
if (isset($_POST['stack'])) {
	$stack2 = \filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
	if ($stack2 != '') {
		$oPosicion2 = new web\Posicion();
		if ($oPosicion2->goStack($stack2)) { // devuelve false si no puede ir
			$Qid_sel=$oPosicion2->getParametro('id_sel');
			$Qscroll_id = $oPosicion2->getParametro('scroll_id');
			$oPosicion2->olvidar($stack2);
		}
	}
}

$a_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
	$id_nom = strtok($a_sel[0],"#");
	$na=strtok("#");
	// el scroll id es de la página anterior, hay que guardarlo allí
	$oPosicion->addParametro('id_sel',$a_sel,1);
	$scroll_id = empty($_POST['scroll_id'])? 0 : $_POST['scroll_id'];
	$oPosicion->addParametro('scroll_id',$scroll_id,1);
	$sactividad = empty($_POST['que'])? '' : $_POST['que'];
	$Qtodos = empty($Qtodos)? 1 : $Qtodos;
} else { // vengo de actualizar
	$id_nom = empty($_POST['id_nom'])? '' : $_POST['id_nom'];
	$na = empty($_POST['na'])? '' : $_POST['na'];
	$sactividad = empty($_POST['sactividad'])? '' : $_POST['sactividad'];
	
}

if (($na == 'a' || $na == 'agd') && $sactividad == 'ca') {
	$sactividad = 'cv';
}

$oPersona = new personas\PersonaDl($id_nom);
$ap_nom = $oPersona->getApellidosNombre();

//Miro los actuales
$gesPlazasPeticion = new \actividadplazas\model\entity\GestorPlazaPeticion();
$cPlazasPeticion = $gesPlazasPeticion->getPlazasPeticion(array('id_nom'=>$id_nom,'tipo'=>$sactividad,'_ordre'=>'orden'));
$sid_activ = '';
foreach ($cPlazasPeticion as $oPlazaPeticion) {
	$id_activ = $oPlazaPeticion->getId_activ();
	$sid_activ .= empty($sid_activ)? $id_activ : ','.$id_activ;
}

// Posibles:
if (!empty($Qtodos) && $Qtodos != 1) {
	$grupo_estudios = $Qtodos;
	$GesGrupoEst = new ubis\GestorDelegacion();
	$cDelegaciones = $GesGrupoEst->getDelegaciones(array('grupo_estudios'=>$grupo_estudios));
	if (count($cDelegaciones) > 1) $aOperador['dl_org'] = 'OR';
	$mi_grupo = '';
	foreach ($cDelegaciones as $oDelegacion) {
		$mi_grupo .= empty($mi_grupo)? '' : ',';
		$mi_grupo .= "'".$oDelegacion->getDl()."'";
	}
	$aWhere['dl_org'] = $mi_grupo;
}
//periodo
switch ($sactividad) {
	case 'ca':
	case 'cv':
		$any=  core\ConfigGlobal::any_final_curs('est');
		$inicurs=core\curso_est("inicio",$any,"est");
		$fincurs=core\curso_est("fin",$any,"est");
		break;
	case 'crt':
		$any=  core\ConfigGlobal::any_final_curs('crt');
		$inicurs=core\curso_est("inicio",$any,"crt");
		$fincurs=core\curso_est("fin",$any,"crt");
		break;
}

$aWhere['f_ini'] = "'$inicurs','$fincurs'";
$aOperador['f_ini'] = 'BETWEEN';
$aWhere['status'] = 2;
$aWhere['_ordre'] = 'f_ini,nivel_stgr';

$cActividades = array();
$sfsv = core\ConfigGlobal::mi_sfsv();
$mi_dele = core\ConfigGlobal::mi_dele();
switch ($na) {
	case "agd":
	case "a":
		//caso de agd
		$id_ctr = empty($_POST['id_ctr_agd'])? '' : $_POST['id_ctr_agd'];
		if ($id_ctr==1) $id_ctr = ''; //es todos los ctr.
		$id_tabla_persona='a'; //el id_tabla entra en conflicto con el de actividad
		$tabla_pau='p_agregados';

		switch ($sactividad) {
			case 'ca': //133
			case 'cv': //133
				$Qid_tipo_activ = '^'.$sfsv.'33';
				break;
			case 'crt':
				$Qid_tipo_activ = '^'.$sfsv.'31';
				break;
		}
		$aWhere['id_tipo_activ'] = $Qid_tipo_activ;
		$aOperador['id_tipo_activ'] = '~';
		//inicialmente estaba sólo con las activiades publicadas. 
		//Ahora añado las no publicadas de midl.
		$GesActividadesDl = new actividades\GestorActividadDl();
		$cActividadesDl = $GesActividadesDl->getActividades($aWhere,$aOperador);
		// Añado la condición para que no duplique las de midele:
		$aWhere['dl_org'] = $mi_dele;
		$aOperador['dl_org'] = '!=';
		$GesActividadesPub = new actividades\GestorActividadPub();
		$cActividadesPub = $GesActividadesPub->getActividades($aWhere,$aOperador);
		
		$cActividades = array_merge($cActividadesDl,array('-------'),$cActividadesPub);
		break;
	case "n":
		// caso de n
		$id_ctr = empty($_POST['id_ctr_n'])? '' : $_POST['id_ctr_n'];
		if ($id_ctr==1) $id_ctr = ''; //es todos los ctr.
		$id_tabla_persona='n';
		$tabla_pau='p_numerarios';
	
		switch ($sactividad) {
			case 'ca': //112
				$Qid_tipo_activ = '^'.$sfsv.'12';
				break;
			case 'crt':
				$Qid_tipo_activ = '^'.$sfsv.'11';
				break;
		}
		$aWhere['id_tipo_activ'] = $Qid_tipo_activ;
		$aOperador['id_tipo_activ'] = '~';
		//inicialmente estaba sólo con las activiades publicadas. 
		//Ahora añado las no publicadas de midl.
		$GesActividadesDl = new actividades\GestorActividadDl();
		$cActividadesDl = $GesActividadesDl->getActividades($aWhere,$aOperador);
		// Añado la condición para que no duplique las de midele:
		$aWhere['dl_org'] = $mi_dele;
		$aOperador['dl_org'] = '!=';
		$GesActividadesPub = new actividades\GestorActividadPub();
		$cActividadesPub = $GesActividadesPub->getActividades($aWhere,$aOperador);
		
		$cActividades = array_merge($cActividadesDl,array('-------'),$cActividadesPub);
	break;
}

$aOpciones = array();
foreach ($cActividades as $oActividad) {
	// para el separador '-------'
	if (is_object($oActividad)) {
		$id_activ = $oActividad->getId_activ();
		$nom_activ = $oActividad->getNom_activ();
		$aOpciones[$id_activ] = $nom_activ;
	} else {
		$aOpciones[1] = '--------';
	}
}

$oSelects = new web\DesplegableArray($sid_activ,$aOpciones,'actividades');
$oSelects->setBlanco('t');
$oSelects->setAccionConjunto('fnjs_mas_actividades(event)');

// En el caso de actualizar la misma página (fnjs_actualizar) solo me quedo con la última.
$stack = $oPosicion->getStack(0);

$oHash = new web\Hash();
$camposForm = 'actividades!actividades_mas!actividades_num';
$oHash->setcamposForm($camposForm);
$oHash->setcamposNo('que!actividades');
$a_camposHidden = array(
		'id_nom' => $id_nom,
		'na' => $na,
		'sactividad' => $sactividad,
		'que' => '',
		'stack' => $stack
		);
$oHash->setArraycamposHidden($a_camposHidden);

$txt_guardar=_("guardar peticiones");


$a_campos = [
			'oPosicion' => $oPosicion,
			'oHash' => $oHash,
			'oSelects' => $oSelects,
			'ap_nom' => $ap_nom,
			'txt_guardar' =>$txt_guardar,
			];

$oView = new core\View('actividadplazas/controller');
echo $oView->render('peticiones_activ.phtml',$a_campos);