<?php
use personas\model\entity as personas;
use ubis\model\entity as ubis;
use usuarios\model\entity as usuarios;
/**
* Esta página muestra una tabla con las personas que cumplen con la condicion.
*
* Es llamado desde personas_que.php
*
*@package	delegacion
*@subpackage	fichas
*@author	Daniel Serrabou
*@since		15/5/02.
*@ajax		27/8/2007.		
*
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$titulo = 0;

$oPosicion->recordar();

//Si vengo de vuelta de un go_to:
$tabla = (string) \filter_input(INPUT_POST, 'tabla');
$Qna = (string) \filter_input(INPUT_POST, 'na');
$tipo = (string) \filter_input(INPUT_POST, 'tipo');
$sWhere = (string) \filter_input(INPUT_POST, 'sWhere');
$sOperador = (string) \filter_input(INPUT_POST, 'sOperador');
$sWhereCtr = (string) \filter_input(INPUT_POST, 'sWhereCtr');
$sOperadorCtr = (string) \filter_input(INPUT_POST, 'sOperadorCtr');

$Qid_sel = (string) \filter_input(INPUT_POST, 'id_sel');
$Qscroll_id = (string) \filter_input(INPUT_POST, 'scroll_id');
$Qque = (string) \filter_input(INPUT_POST, 'que');
$Qexacto = (string) \filter_input(INPUT_POST, 'exacto');
$Qcmb = (string) \filter_input(INPUT_POST, 'cmb');
$Qnombre = (string) \filter_input(INPUT_POST, 'nombre');
$Qapellido1 = (string) \filter_input(INPUT_POST, 'apellido1');
$Qapellido2 = (string) \filter_input(INPUT_POST, 'apellido2');
$Qcentro = (string) \filter_input(INPUT_POST, 'centro');

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

/*
* Defino un array con los datos actuales, para saber volver 
*/
$aGoBack = array (
				'que' => $Qque,
				'exacto' => $Qexacto,
				'cmb' => $Qcmb,
				'nombre' => $Qnombre,
				'apellido1' => $Qapellido1,
				'apellido2' => $Qapellido2,
				'centro' => $Qcentro,
				'tabla' => $tabla,
				'na' => $Qna,
				'tipo' => $tipo,
				'sWhere' => $sWhere,
				'sOperador' => $sOperador,
				'sWhereCtr' => $sWhereCtr,
				'sOperadorCtr' => $sOperadorCtr
				 );
$oPosicion->setParametros($aGoBack,1);

/*miro las condiciones. las variables son: num, agd, sup, nombre, apellido1, apellido2 */
if (empty($sWhere)) {
	$aWhere=array();
	$aOperador=array();
	$aWhereCtr=array();
	$aOperadorCtr=array();

	if (!empty($Qapellido1)){ 
		$aWhere['apellido1'] = $Qapellido1;
		if (empty($Qexacto)){
			$aWhere['apellido1'] = '^'.$aWhere['apellido1'];
			$aOperador['apellido1'] = 'sin_acentos';
		}
	}

	if (!empty($Qapellido2)){ 
		$aWhere['apellido2'] = $Qapellido2;
		if (empty($Qexacto)){
			$aWhere['apellido2'] = '^'.$aWhere['apellido2'];
			$aOperador['apellido2'] = 'sin_acentos';
		}
	}
	if (!empty($Qnombre)){ 
		$aWhere['nom'] = $Qnombre;
		if (empty($Qexacto)){
			$aWhere['nom'] = '^'.$aWhere['nom'];
			$aOperador['nom'] = 'sin_acentos';
		}
	}
		
	/*Si está puesto el nombre del centro, saco una lista de todos los del centro*/
	if (!empty($Qcentro)){ 
		if (!empty($Qexacto)){
			$Qcentro=addslashes(strtr($Qcentro,"+","."));
			if ($Qtabla=="p_cp_ae_sssc") {
			$condicion=$condicion . " p.ctr_depende = '".$Qcentro."' AND";
			} else {
			$condicion=$condicion . " u.nombre_ubi = '".$Qcentro."' AND";
			}	
		} else {
			$nom_ubi = str_replace("+", "\+", $Qcentro); // para los centros de la sss+
			$nom_ubi = addslashes($nom_ubi);
			$aWhereCtr['nombre_ubi'] = '^'.$nom_ubi;
			$aOperadorCtr['nombre_ubi'] = 'sin_acentos';
		}
	}
	if (empty($Qcmb)){
		$aWhere['situacion'] = 'A';
	} else {
		if (!$_SESSION['oPerm']->have_perm("dtor")) {
			$aWhere['situacion'] = 'B';
			$aOperador['situacion'] = '!=';
		}
	}
} else {
	$aWhere = unserialize(core\urlsafe_b64decode($sWhere));
	$aOperador = unserialize(core\urlsafe_b64decode($sOperador));
	$aWhereCtr = unserialize(core\urlsafe_b64decode($sWhereCtr));
	$aOperadorCtr = unserialize(core\urlsafe_b64decode($sOperadorCtr));
}

if (!empty($aWhereCtr)) {
	$gesCentros = new ubis\GestorCentroDl();
	$cCentros = $gesCentros->getCentros($aWhereCtr,$aOperadorCtr);
	$aId_ctrs = [];
	foreach ($cCentros as $oCentro) {
		$aId_ctrs[] = $oCentro->getId_ubi();
	}
	if (!empty($aId_ctrs)) {
		$v = "{".implode(', ',$aId_ctrs)."}";
        $aWhere['id_ctr'] = $v;
        $aOperador['id_ctr'] = 'ANY';
	} else {
		$tabla = 'nada';
	}
}

// por defecto no pongo valor, que lo coja de la base de datos. Sólo sirve para los de paso.
$id_tabla = '';
$permiso = 1;
switch ($tabla) {
	case "p_sssc":
		$obj_pau = 'PersonaSSSC';
		$GesPersona = new personas\GestorPersonaSSSC();
		$cPersonas = $GesPersona->getPersonasDl($aWhere,$aOperador);
		if ($_SESSION['oPerm']->have_perm("des")){
			$permiso = 3;
		}
	break;
	case "p_supernumerarios":
		$obj_pau = 'PersonaS';
		$GesPersona = new personas\GestorPersonaS();
		$cPersonas = $GesPersona->getPersonasDl($aWhere,$aOperador);
		if ($_SESSION['oPerm']->have_perm("sg")){
			$permiso = 3;
		}
	break;
	case "p_numerarios":
		$obj_pau = 'PersonaN';
		$GesPersona = new personas\GestorPersonaN();
		$cPersonas = $GesPersona->getPersonasDl($aWhere,$aOperador);
		if ($_SESSION['oPerm']->have_perm("sm")){
			$permiso = 3;
		}
	break;
	case "p_nax":
		$obj_pau = 'PersonaNax';
		$GesPersona = new personas\GestorPersonaNax();
		if (($cPersonas = $GesPersona->getPersonasDl($aWhere,$aOperador)) === false) {
			$cPersonas = array();
		}
		if ($_SESSION['oPerm']->have_perm("nax")){
			$permiso = 3;
		}
	break;
	case "p_agregados":
		$obj_pau = 'PersonaAgd';
		$GesPersona = new personas\GestorPersonaAgd();
		$cPersonas = $GesPersona->getPersonasDl($aWhere,$aOperador);
		if ($_SESSION['oPerm']->have_perm("agd")){
			$permiso = 3;
		}
	break;
	case "p_de_paso":
		if (!empty($Qna)) {
			$aWhere['id_tabla'] = 'p'.$Qna;
			$id_tabla = 'p'.$Qna;
		}
		$obj_pau = 'PersonaEx';
		$GesPersona = new personas\GestorPersonaEx();
		$cPersonas = $GesPersona->getPersonas($aWhere,$aOperador);
		if ($_SESSION['oPerm']->have_perm("sm") OR $_SESSION['oPerm']->have_perm("agd")){
			$permiso = 3;
		}
	break;
	case 'nada':
		$cPersonas = array();
		break;
}

$sWhere = core\urlsafe_b64encode(serialize($aWhere));
$sOperador = core\urlsafe_b64encode(serialize($aOperador));
$sWhereCtr = core\urlsafe_b64encode(serialize($aWhereCtr));
$sOperadorCtr = core\urlsafe_b64encode(serialize($aOperadorCtr));

$a_botones[] = array( 'txt' => _("cambio de ctr"),
					'click' =>"fnjs_modificar_ctr(\"#seleccionados\")" );
$script['fnjs_modificar_ctr'] = 1;
$a_botones[] = array( 'txt' => _("ver dossiers"),
					'click' =>"fnjs_dossiers(\"#seleccionados\")" );
$script['fnjs_dossiers'] = 1;
$a_botones[] = array( 'txt' => _("ficha"),
					'click' =>"fnjs_ficha(\"#seleccionados\")" );
$script['fnjs_ficha'] = 1;

if (core\configGlobal::is_app_installed('asistentes')) {
	$a_botones[] = array( 'txt' => _("ver actividades"),
						'click' =>"fnjs_actividades(\"#seleccionados\")" );
	$script['fnjs_actividades'] = 1;
}

if (core\configGlobal::is_app_installed('notas')) {
	if (($tabla=="p_numerarios") or ($tabla=="p_agregados") or ($tabla=="p_de_paso")) {   
		$a_botones[]= array( 'txt' => _("ver tessera"),
							'click' =>"fnjs_tessera(\"#seleccionados\")" ) ;
		$script['fnjs_tessera'] = 1;
	}
	// en el caso de los de estudios añado la posibilidad de modificar el campo stgr
	if ($_SESSION['oPerm']->have_perm("est")){
		$a_botones[]=array( 'txt' => _("modificar stgr"),
							'click' =>"fnjs_modificar(\"#seleccionados\")" );
		$script['fnjs_modificar'] = 1;
		$a_botones[]=array( 'txt' => _("imprimir tessera"),
							'click' =>"fnjs_imp_tessera(\"#seleccionados\")" );
		$script['fnjs_imp_tessera'] = 1;
	}
}
if (core\configGlobal::is_app_installed('actividadestudios')) {
	if (($tabla=="p_numerarios") or ($tabla=="p_agregados") or ($tabla=="p_de_paso")) {   
		$a_botones[]= array( 'txt' => _("posibles ca"),
							'click' =>"fnjs_posibles_ca(\"#seleccionados\")" ) ;
		$script['fnjs_posibles_ca'] = 1;
	}
}
if (core\configGlobal::is_app_installed('actividadplazas')) {
	if (($tabla=="p_numerarios") or ($tabla=="p_agregados") or ($tabla=="p_de_paso")) {   
		$sactividad = 'ca'; //ca
		$a_botones[]= array( 'txt' => _("petición ca"),
							'click' =>"fnjs_peticion_activ(\"#seleccionados\",\"$sactividad\")" ) ;
		$sactividad = 'crt'; //crt
		$a_botones[]= array( 'txt' => _("petición crt"),
							'click' =>"fnjs_peticion_activ(\"#seleccionados\",\"$sactividad\")" ) ;
		$script['fnjs_posibles_activ'] = 1;
	}
}
if ($_SESSION['oPerm']->have_perm("est")){
	if (core\configGlobal::is_app_installed('actividadestudios')) {
		$a_botones[]=array( 'txt' => _("plan estudios"),
							'click' =>"fnjs_matriculas(\"#seleccionados\")" );
		$script['fnjs_matriculas'] = 1;
		$permiso = 3;
	}
	if (core\configGlobal::is_app_installed('profesores')) {
		$a_botones[]=array( 'txt' => _("ficha profesor stgr"),
							'click' =>"fnjs_ficha_profe(\"#seleccionados\")" );
		$script['fnjs_ficha_profe'] = 1;
	}
}

// Solo ver e imprimir tessera
if (core\ConfigGlobal::mi_dele() === core\ConfigGlobal::mi_region()) {
	$a_botones = [];
	$a_botones[]= array( 'txt' => _("ver tessera"),
						'click' =>"fnjs_tessera(\"#seleccionados\")" ) ;
	$script['fnjs_tessera'] = 1;
	$a_botones[]=array( 'txt' => _("imprimir tessera"),
						'click' =>"fnjs_imp_tessera(\"#seleccionados\")" );
	$script['fnjs_imp_tessera'] = 1;
	$a_botones[]=array( 'txt' => _("ficha profesor stgr"),
						'click' =>"fnjs_ficha_profe(\"#seleccionados\")" );
	$script['fnjs_ficha_profe'] = 1;
}
// en el caso de los de dre añado la posibilidad de listar la atencion a las actividades
if (core\configGlobal::is_app_installed('atnsacd')) {
	if ($_SESSION['oPerm']->have_perm("des")){
		$a_botones[]=array( 'txt' => _("atención actividades"),
							'click' =>"fnjs_lista_activ(\"#seleccionados\")" );
		$script['fnjs_lista_activ'] = 1;
	}
}

$a_cabeceras=array( ucfirst(_("tabla")),
					array('name'=>_("nombre y apellidos"),'width'=>250,'formatter'=>'clickFormatter')
				);

if ($tabla=="p_sssc") {   
	$a_cabeceras[]=ucfirst(_("socio"));
}   

$a_cabeceras[]=ucfirst(_("centro"));

if (($tabla=="p_numerarios") or ($tabla=="p_agregados") or ($tabla=="p_de_paso")) {   
	$a_cabeceras[]=ucfirst(_("stgr"));
}   
if (!empty($situacion)) { 
	$a_cabeceras[]=ucfirst(_("situación"));
	$a_cabeceras[]= array('name'=>ucfirst(_("fecha cambio situación")),'class'=>'fecha');
} 

$i = 0;
$a_valores = array();
$a_personas = array();

$sPrefs = '';
$id_usuario= core\ConfigGlobal::mi_id_usuario();
$tipo = 'tabla_presentacion';
$oPref = new usuarios\Preferencia(array('id_usuario'=>$id_usuario,'tipo'=>$tipo));
$sPrefs=$oPref->getPreferencia();
foreach ($cPersonas as $oPersona) {
	$i++;
	$id_tabla=$oPersona->getId_tabla();
	$id_nom=$oPersona->getId_nom();
	$nom=$oPersona->getApellidosNombre();

	if ($obj_pau != 'PersonaEx') {
		$id_ctr=$oPersona->getId_ctr();

		if (core\ConfigGlobal::mi_dele() === core\ConfigGlobal::mi_region()) {
			$oCentroDl = new ubis\Centro($id_ctr);
		} else {
			$oCentroDl = new ubis\CentroDl($id_ctr);
		}
		$nombre_ubi = $oCentroDl->getNombre_ubi();
	} else {
		$nombre_ubi = $oPersona->getDl();
	}

	$condicion_2="Where id_nom='".$id_nom."'";
	$condicion_2=urlencode($condicion_2);
	
	$a_val['sel']="$id_nom#$id_tabla";
	$a_val[1]=$id_tabla;
	if ($sPrefs == 'html') {
		$pagina=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/personas/controller/home_persona.php?'.http_build_query(array('id_nom'=>$id_nom,'id_tabla'=>$id_tabla,'obj_pau'=>$obj_pau)));
		$a_val[2]= array( 'ira'=>$pagina, 'valor'=>$nom);
	} else {
		$pagina='fnjs_ficha("#seleccionados")';
		$a_val[2]= array( 'script'=>$pagina, 'valor'=>$nom);
	}
	if ($tabla=="p_sssc") {
		$a_val[3]=$row['socio'];
	}
	$a_val[4]=$nombre_ubi;
	/*la siguiente instrucción es para que el campo stgr sólo se visualice
	para los n y agd siempre que no estemos ante una selección para ver
	un planning*/
	if ((($tabla=='p_numerarios') or ($tabla=='p_agregados'))and ($tipo!='planning')) {
		$a_val[5]=$oPersona->getStgr();
	} 
	if (!empty($situacion)) { 
		$a_val[6]=$row['situacion'];
		$a_val[7]=$row['f_situacion'];
	} 
	$key_sort = $nom."_".$id_nom;
	$a_personas[$key_sort] = $a_val;
}
uksort($a_personas,"core\strsinacentocmp");
$c = 0;
foreach ($a_personas as $key_sort => $val) {
	$c++;
	$a_valores[$c] = $val;
}
if (isset($Qid_sel) && !empty($Qid_sel)) { $a_valores['select'] = $Qid_sel; }
if (isset($Qscroll_id) && !empty($Qscroll_id)) { $a_valores['scroll_id'] = $Qscroll_id; }

$oTabla = new web\Lista();
$oTabla->setId_tabla("personas_select_$tabla");
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$pagina=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/personas/controller/personas_editar.php?'.http_build_query(array('obj_pau'=>$obj_pau,'id_tabla'=>$id_tabla,'nuevo'=>1,'apellido1'=>$Qapellido1)));
	
$resultado=sprintf( _("%s personas encontradas"),$i);

$oHash = new web\Hash();
$oHash->setcamposForm('sel!que!id_dossier');
$oHash->setcamposNo('que!id_dossier!scroll_id');
$a_camposHidden = array(
		'pau' => 'p',
		'obj_pau' => $obj_pau,
		'tabla' => $tabla,
		'na' => $Qna,
		'permiso' => $permiso,
		);
$oHash->setArraycamposHidden($a_camposHidden);

$a_campos = ['oPosicion' => $oPosicion,
			'oHash' => $oHash,
			'script' => $script,
			'resultado' => $resultado,
			'oTabla' => $oTabla,
			'pagina' => $pagina,
			'permiso' => $permiso,
			];

$oView = new core\View('personas/controller');
echo $oView->render('personas_select.phtml',$a_campos);
