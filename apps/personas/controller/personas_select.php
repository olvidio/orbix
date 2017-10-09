<?php
use personas\model as personas;
use ubis\model as ubis;
use usuarios\model as usuarios;
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
//Si vengo de vuelta de un go_to:
if (!empty($_POST['atras'])) {
	$tabla = $oPosicion->getParametro('tabla');
	$breve = $oPosicion->getParametro('breve');
	$tipo = $oPosicion->getParametro('tipo');
	$es_sacd = $oPosicion->getParametro('es_sacd');
	$sWhere = $oPosicion->getParametro('sWhere');
	$sOperador = $oPosicion->getParametro('sOperador');
	$sWhereCtr = $oPosicion->getParametro('sWhereCtr');
	$sOperadorCtr = $oPosicion->getParametro('sOperadorCtr');
	$Qid_sel = $oPosicion->getParametro('id_sel');
	$Qscroll_id = $oPosicion->getParametro('scroll_id');
} else {
	$tabla = empty($_POST['tabla'])? '' : $_POST['tabla'];
	$breve = empty($_POST['breve'])? '' : $_POST['breve'];
	$tipo = empty($_POST['tipo'])? '' : $_POST['tipo'];
	$es_sacd = empty($_POST['es_sacd'])? '' : $_POST['es_sacd'];
}

/*miro las condiciones. las variables son: num, agd, sup, nombre, apellido1, apellido2 */
if (empty($sWhere)) {
	$aWhere=array();
	$aOperador=array();
	$aWhereCtr=array();
	$aOperadorCtr=array();

	if (!empty($_POST['apellido1'])){ 
		$aWhere['apellido1'] = $_POST['apellido1'];
		if (empty($_POST['exacto'])){
			$aWhere['apellido1'] = '^'.$aWhere['apellido1'];
			$aOperador['apellido1'] = 'sin_acentos';
		}
	}

	if (!empty($_POST['apellido2'])){ 
		$aWhere['apellido2'] = $_POST['apellido2'];
		if (empty($_POST['exacto'])){
			$aWhere['apellido2'] = '^'.$aWhere['apellido2'];
			$aOperador['apellido2'] = 'sin_acentos';
		}
	}
	if (!empty($_POST['nombre'])){ 
		$aWhere['nom'] = $_POST['nombre'];
		if (empty($_POST['exacto'])){
			$aWhere['nom'] = '^'.$aWhere['nom'];
			$aOperador['nom'] = 'sin_acentos';
		}
	}
		
	/*Si está puesto el nombre del centro, saco una lista de todos los del centro*/
	if (!empty($_POST['centro'])){ 
		if (!empty($_POST['exacto'])){
			$_POST['centro']=addslashes(strtr($_POST['centro'],"+","."));
			if ($_POST['tabla']=="p_cp_ae_sssc") {
			$condicion=$condicion . " p.ctr_depende = '".$_POST['centro']."' AND";
			} else {
			$condicion=$condicion . " u.nombre_ubi = '".$_POST['centro']."' AND";
			}	
		} else {
			$nom_ubi = str_replace("+", "\+", $_POST['centro']); // para los centros de la sss+
			$nom_ubi = addslashes($nom_ubi);
			$aWhereCtr['nombre_ubi'] = '^'.$nom_ubi;
			$aOperadorCtr['nombre_ubi'] = 'sin_acentos';
		}
	}
	if (empty($_POST['cmb'])){
		$aWhere['situacion'] = 'A';
	} else {
		if (!$_SESSION['oPerm']->have_perm("dtor")) {
			$aWhere['situacion'] = 'B';
			$aOperador['situacion'] = '!=';
		}
	}
	//añado una condición más, para cuando me interesan solo los que son
	//sacd. La variable es sacd=1 la hago llegar a través de menús
	empty($_POST['es_sacd'])? $es_sacd="" : $es_sacd=$_POST['es_sacd'];
	if ($es_sacd==1){ 
		$aWhere['sacd'] = 't';
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
	$id_ctrs = '';
	foreach ($cCentros as $oCentro) {
		$id_ubi = $oCentro->getId_ubi();
		$id_ctrs .= empty($id_ctrs)? '' : '|';
		$id_ctrs .= $id_ubi;
	}
	if (!empty($id_ctrs)) {
		$aWhere['id_ctr'] = $id_ctrs;
		$aOperador['id_ctr'] = '~';
	} else {
		$tabla = 'nada';
	}
}

// por defecto no pongo valor, que lo coja de la base de datos. Sólo sirve para los de paso.
$id_tabla = '';
switch ($tabla) {
	case "p_sssc":
		$obj_pau = 'PersonaSSSC';
		$GesPersona = new personas\GestorPersonaSSSC();
		$cPersonas = $GesPersona->getPersonasDl($aWhere,$aOperador);
	break;
	case "p_supernumerarios":
		$obj_pau = 'PersonaS';
		$GesPersona = new personas\GestorPersonaS();
		$cPersonas = $GesPersona->getPersonasDl($aWhere,$aOperador);
	break;
	case "p_numerarios":
		$obj_pau = 'PersonaN';
		$GesPersona = new personas\GestorPersonaN();
		$cPersonas = $GesPersona->getPersonasDl($aWhere,$aOperador);
	break;
	case "p_nax":
		$obj_pau = 'PersonaNax';
		$GesPersona = new personas\GestorPersonaNax();
		$cPersonas = $GesPersona->getPersonasDl($aWhere,$aOperador);
	break;
	case "p_agregados":
		$obj_pau = 'PersonaAgd';
		$GesPersona = new personas\GestorPersonaAgd();
		$cPersonas = $GesPersona->getPersonasDl($aWhere,$aOperador);
	break;
	case "p_de_paso":
		if (!empty($_POST['na'])) {
			$aWhere['id_tabla'] = 'p'.$_POST['na'];
			$id_tabla = 'p'.$_POST['na'];
		}
		$obj_pau = 'PersonaEx';
		$GesPersona = new personas\GestorPersonaEx();
		$cPersonas = $GesPersona->getPersonas($aWhere,$aOperador);
	break;
	case 'nada':
		$cPersonas = array();
		break;
}

$sWhere = core\urlsafe_b64encode(serialize($aWhere));
$sOperador = core\urlsafe_b64encode(serialize($aOperador));
$sWhereCtr = core\urlsafe_b64encode(serialize($aWhereCtr));
$sOperadorCtr = core\urlsafe_b64encode(serialize($aOperadorCtr));
/*
* Defino un array con los datos actuales, para saber volver después de navegar un rato
*/
$aGoBack = array (
				'tabla' => $tabla,
				'breve' => $breve,
				'tipo' => $tipo,
				'es_sacd' => $es_sacd,
				'sWhere' => $sWhere,
				'sOperador' => $sOperador,
				'sWhereCtr' => $sWhereCtr,
				'sOperadorCtr' => $sOperadorCtr
				 );
$oPosicion->setParametros($aGoBack);
$oPosicion->recordar();

$a_botones[] = array( 'txt' => _('cambio de ctr'), 'click' =>"fnjs_modificar_ctr(\"#seleccionados\")" );
$script['fnjs_modificar_ctr'] = 1;
$a_botones[] = array( 'txt' => _('ver dossiers'), 'click' =>"fnjs_dossiers(\"#seleccionados\")" );
$script['fnjs_dossiers'] = 1;
$a_botones[] = array( 'txt' => _('ficha'), 'click' =>"fnjs_ficha(\"#seleccionados\")" );
$script['fnjs_ficha'] = 1;

if (core\configGlobal::is_app_installed('asistentes')) {
	$a_botones[] = array( 'txt' => _('ver actividades'), 'click' =>"fnjs_actividades(\"#seleccionados\")" );
	$script['fnjs_actividades'] = 1;
}

if (core\configGlobal::is_app_installed('notas')) {
	if (($tabla=="p_numerarios") or ($tabla=="p_agregados") or ($tabla=="p_de_paso")) {   
		$a_botones[]= array( 'txt' => _('ver tessera'), 'click' =>"fnjs_tessera(\"#seleccionados\")" ) ;
		$script['fnjs_tessera'] = 1;
	}
	// en el caso de los de estudios añado la posibilidad de modificar el campo stgr
	if ($_SESSION['oPerm']->have_perm("est")){
		$a_botones[]=array( 'txt' => _('modificar stgr'), 'click' =>"fnjs_modificar(\"#seleccionados\")" );
		$script['fnjs_modificar'] = 1;
		$a_botones[]=array( 'txt' => _('imprimir tessera'), 'click' =>"fnjs_imp_tessera(\"#seleccionados\")" );
		$script['fnjs_imp_tessera'] = 1;
	}
}
if (core\configGlobal::is_app_installed('actividadestudios')) {
	if (($tabla=="p_numerarios") or ($tabla=="p_agregados") or ($tabla=="p_de_paso")) {   
		$a_botones[]= array( 'txt' => _('posibles ca'), 'click' =>"fnjs_posibles_ca(\"#seleccionados\")" ) ;
		$script['fnjs_posibles_ca'] = 1;
	}
}
if (core\configGlobal::is_app_installed('actividadplazas')) {
	if (($tabla=="p_numerarios") or ($tabla=="p_agregados") or ($tabla=="p_de_paso")) {   
		$sactividad = 'ca'; //ca
		$a_botones[]= array( 'txt' => _('petición ca'), 'click' =>"fnjs_peticion_activ(\"#seleccionados\",\"$sactividad\")" ) ;
		$sactividad = 'crt'; //crt
		$a_botones[]= array( 'txt' => _('petición crt'), 'click' =>"fnjs_peticion_activ(\"#seleccionados\",\"$sactividad\")" ) ;
		$script['fnjs_posibles_activ'] = 1;
	}
}
if ($_SESSION['oPerm']->have_perm("est")){
	if (core\configGlobal::is_app_installed('actividadestudios')) {
		$a_botones[]=array( 'txt' => _("plan estudios"), 'click' =>"fnjs_matriculas(\"#seleccionados\")" );
		$script['fnjs_matriculas'] = 1;
	}
	if (core\configGlobal::is_app_installed('profesores')) {
		$a_botones[]=array( 'txt' => _('ficha profesor stgr'), 'click' =>"fnjs_ficha_profe(\"#seleccionados\")" );
		$script['fnjs_ficha_profe'] = 1;
	}
}

// en el caso de los de dre añado la posibilidad de listar la atencion a las actividades
if (core\configGlobal::is_app_installed('atnsacd')) {
	if ($_SESSION['oPerm']->have_perm("des")){
		$a_botones[]=array( 'txt' => _('atención actividades'), 'click' =>"fnjs_lista_activ(\"#seleccionados\")" );
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
	$a_cabeceras[]=ucfirst(_("situacion"));
	$a_cabeceras[]= array('name'=>ucfirst(_("fecha cambio situacion")),'class'=>'fecha');

} 

$i = 0;
$a_valores = array();
$a_personas = array();
if (isset($Qid_sel) && !empty($Qid_sel)) { $a_valores['select'] = $Qid_sel; }
if (isset($Qscroll_id) && !empty($Qscroll_id)) { $a_valores['scroll_id'] = $Qscroll_id; }

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

		$oCentroDl = new ubis\CentroDl($id_ctr);
		$nombre_ubi = $oCentroDl->getNombre_ubi();
	} else {
		$nombre_ubi = $oPersona->getDl();
	}

	$condicion_2="Where id_nom='".$id_nom."'";
	$condicion_2=urlencode($condicion_2);
	
	$a_val['sel']="$id_nom#$id_tabla";
	$a_val[1]=$id_tabla;
	if ($sPrefs == 'html') {
		$pagina=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/personas/controller/home_persona.php?'.http_build_query(array('id_nom'=>$id_nom,'id_tabla'=>$id_tabla,'obj_pau'=>$obj_pau,'breve'=>$breve,'es_sacd'=>$es_sacd)));
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
	$a_personas[$nom] = $a_val;
}
uksort($a_personas,"core\strsinacentocmp");
$c = 0;
foreach ($a_personas as $nom => $val) {
	$c++;
	$a_valores[$c] = $val;
}

$pagina=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/personas/controller/personas_editar.php?'.http_build_query(array('obj_pau'=>$obj_pau,'id_tabla'=>$id_tabla,'nuevo'=>1)));
	
$resultado=sprintf( _("%s personas encontradas"),$i);

$oHash = new web\Hash();
$oHash->setcamposForm('sel!que!id_dossier');
$oHash->setcamposNo('que!id_dossier!scroll_id');
$a_camposHidden = array(
		'pau' => 'p',
		'obj_pau' => $obj_pau,
		'permiso' => '3',
		'breve' => $breve,
		'es_sacd' => $es_sacd,
		'tabla' => $tabla
		);
$oHash->setArraycamposHidden($a_camposHidden);
/* ---------------------------------- html --------------------------------------- */
?>
<script>
<?php if (!empty($script['fnjs_modificar_ctr'])) { ?>
fnjs_modificar_ctr=function(formulario){
	rta=fnjs_solo_uno(formulario);
	if (rta==1) {
  		$(formulario).attr('action',"apps/personas/controller/traslado_form.php");
  		fnjs_enviar_formulario(formulario);
  	}
}
<?php } ?>
<?php if (!empty($script['fnjs_dossiers'])) { ?>
fnjs_dossiers=function(formulario){
	rta=fnjs_solo_uno(formulario);
	if (rta==1) {
  		$(formulario).attr('action',"apps/dossiers/controller/dossiers_ver.php");
  		fnjs_enviar_formulario(formulario);
  	}
}
<?php } ?>
<?php if (!empty($script['fnjs_ficha'])) { ?>
fnjs_ficha=function(formulario){
	rta=fnjs_solo_uno(formulario);
	if (rta==1) {
		$('#que').val("titulo");
  		$(formulario).attr('action','apps/personas/controller/personas_editar.php');
  		fnjs_enviar_formulario(formulario);
  	}
}
<?php } ?>
<?php if (!empty($script['fnjs_actividades'])) { ?>
fnjs_actividades=function(formulario){
	rta=fnjs_solo_uno(formulario);
	if (rta==1) {
		$('#que').val("activ");
		$('#id_dossier').val("1301y1302");
  		$(formulario).attr('action',"apps/dossiers/controller/dossiers_ver.php");
  		fnjs_enviar_formulario(formulario);
  	}
}
<?php } ?>
<?php if (!empty($script['fnjs_tessera'])) { ?>
fnjs_tessera=function(formulario){
	/*rta=fnjs_solo_uno(formulario);
	if (rta==1) {
	*/
  		$(formulario).attr('action',"apps/notas/controller/tessera_ver.php");
  		fnjs_enviar_formulario(formulario);
  	/* } */
}
<?php } ?>
<?php if (!empty($script['fnjs_lista_activ'])) { ?>
fnjs_lista_activ=function(formulario){
	rta=fnjs_solo_uno(formulario);
	if (rta==1) {
		$('#que').val("un_sacd");
  		$(formulario).attr('action',"des/com_sacd_activ.php");
  		fnjs_enviar_formulario(formulario);
  	}
}
<?php } ?>
<?php if (!empty($script['fnjs_matriculas'])) { ?>
fnjs_matriculas=function(formulario){
	rta=fnjs_solo_uno(formulario);
	if (rta==1) {
		$('#que').val("matriculas");
		$('#id_dossier').val("1303");
  		$(formulario).attr('action',"apps/dossiers/controller/dossiers_ver.php");
  		fnjs_enviar_formulario(formulario);
  	}
}
<?php } ?>
<?php if (!empty($script['fnjs_posibles_ca'])) { ?>
fnjs_posibles_ca=function(formulario){
	rta=fnjs_solo_uno(formulario);
	if (rta==1) {
		$(formulario).attr('action',"apps/actividadestudios/controller/ca_posibles.php");
  		fnjs_enviar_formulario(formulario);
	}
}
<?php } ?>
<?php if (!empty($script['fnjs_posibles_activ'])) { ?>
fnjs_peticion_activ=function(formulario,sactividad){
	rta=fnjs_solo_uno(formulario);
	if (rta==1) {
		$('#que').val(sactividad);
		$(formulario).attr('action',"apps/actividadplazas/controller/peticiones_activ.php");
  		fnjs_enviar_formulario(formulario);
	}
}
<?php } ?>
<?php if (!empty($script['fnjs_imp_tessera'])) { ?>
fnjs_imp_tessera=function(formulario){
	rta=fnjs_solo_uno(formulario);
	if (rta==1) {
  		$(formulario).attr('action',"apps/notas/controller/tessera_imprimir.php");
  		$(formulario).target="print";
  		fnjs_enviar_formulario(formulario);
  	}
}
<?php } ?>
<?php if (!empty($script['fnjs_ficha_profe'])) { ?>
fnjs_ficha_profe=function(formulario){
	rta=fnjs_solo_uno(formulario);
	if (rta==1) {
  		$(formulario).attr('action',"apps/profesores/controller/ficha_profesor_stgr.php");
  		fnjs_enviar_formulario(formulario);
  	}
}
<?php } ?>
<?php if (!empty($script['fnjs_modificar'])) { ?>
fnjs_modificar=function(formulario){
	rta=fnjs_solo_uno(formulario);
	if (rta==1) {
  		$(formulario).attr('action',"apps/personas/controller/stgr_cambio.php");
  		fnjs_enviar_formulario(formulario);
  	}
}
<?php } ?>
</script>
<h3><?= $resultado ?></h3>
<form id='seleccionados' name='seleccionados' action='' method='post'>
<?= $oHash->getCamposHtml(); ?>
	<input type='hidden' id='que' name='que' value=''>
	<input type='hidden' id='id_dossier' name='id_dossier' value=''>

<?php
$oTabla = new web\Lista();
$oTabla->setId_tabla("personas_select_$tabla");
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);
echo $oTabla->mostrar_tabla();
?>
</form>
<br>
<table><tr><th class="no_print">
	<span class="link_inv" onclick="fnjs_update_div('#main','<?= $pagina ?>');"><?= core\strtoupper_dlb(_("añadir persona")) ?></span>
</th></tr></table>
