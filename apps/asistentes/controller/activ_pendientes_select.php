<?php
use actividades\model as actividades;
use asistentes\model as asistentes;
use personas\model as personas;
/**
* Esta página muestra una tabla con las personas con el ca pendiente.
*
*
*@package	delegacion
*@subpackage	estudios
*@author	Daniel Serrabou
*@since		7/11/03.
*@ajax		23/8/2007.		
*		
*/

/**
* Para asegurar que inicia la sesion, y poder acceder a los permisos
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************
 
//Si vengo por medio de Posicion, borro la última
if (isset($_POST['stack'])) {
	$stack = \filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
	if ($stack != '') {
		// No me sirve el de global_object, sino el de la session
		$oPosicion2 = new Posicion();
		if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
			$Qid_sel=$oPosicion2->getParametro('id_sel');
			$Qscroll_id = $oPosicion2->getParametro('scroll_id');
			$oPosicion2->olvidar($stack);
		}
	}
} 
$Qany = empty($_POST['any'])? '' : $_POST['any'];
$Qtipo_personas = empty($_POST['tipo_personas'])? '' : $_POST['tipo_personas'];
$Qsactividad = empty($_POST['sactividad'])? '' : $_POST['sactividad'];

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
		$cPersonas = $GesPersonas->getPersonas(array('situacion'=>'A'));
		$obj_pau = 'PersonaN';
		break;
	case "agd":
		$GesPersonas = new personas\GestorPersonaAgd();
		$cPersonas = $GesPersonas->getPersonas(array('situacion'=>'A'));
		$obj_pau = 'PersonaAgd';
		break;
	case "sacd":
		$GesPersonas = new personas\GestorPersonaDl();
		$cPersonas = $GesPersonas->getPersonas(array('sacd'=>'t','situacion'=>'A'));
		$obj_pau = 'PersonaDl';
		break;
}

foreach ($cPersonas as $oPersona) {
	$id_nomP = $oPersona->getId_nom();
	if (in_array($id_nomP, $aAsistentes)) continue;
	$ap_nom = $oPersona->getApellidosNombre();
	$aFaltan[$ap_nom] = $id_nomP;
}
ksort($aFaltan);

$titulo=ucfirst(sprintf(_("lista de %s sin %s en el curso %s"),$Qtipo_personas,$Qsactividad,$txt_curso));

/*
* Defino un array con los datos actuales, para saber volver después de navegar un rato
*/
$aGoBack = array (
				'any'=>$Qany,
				'tipo_personas'=>$Qtipo_personas,
				'sactividad'=>$Qsactividad
				);
$oPosicion->setParametros($aGoBack);
$oPosicion->recordar();

$a_cabeceras=array( _("nº"),array('name'=>ucfirst(_("nombre de la persona")),'formatter'=>'clickFormatter'));
$i=0;
$a_valores[$i] = array();
foreach ($aFaltan as $ap_nom=>$id_nom) {
	$i++;
	
	//$pagina="programas/dossiers/home_persona.php?id_nom=$id_nom&tabla_pau=$tabla_p";

	$pagina=web\Hash::link('apps/personas/controller/home_persona.php?'.http_build_query(array('obj_pau'=>$obj_pau,'id_nom'=>$id_nom)));

	$a_valores[$i][1]=$i;
	$a_valores[$i][2]= array( 'ira'=>$pagina, 'valor'=>$ap_nom);
}
// Al final añado la lista de personas que no estan en la dl, pero dependen de aqui. (probablemente haran la actividad en su region actual)
/*
switch ($Qtipo_personas) {
	case "n":
		$GesPersonas = new personas\GestorPersonaN();
		$cPersonas = $GesPersonas->getPersonas(array('situacion'=>'A','situacion'=>'z'));
		break;
	case "agd":
		$GesPersonas = new personas\GestorPersonaAgd();
		$cPersonas = $GesPersonas->getPersonas(array('situacion'=>'A','situacion'=>'z'));
		break;
	case "sacd":
		$GesPersonas = new personas\GestorPersonaDl();
		$cPersonas = $GesPersonas->getPersonas(array('sacd'=>'t','situacion'=>'A','situacion'=>'z'));
		break;
}

$j=0;
$a_valores_2[$j] = array();
foreach ($cPersonas as $oPersona) {
	$j++;
	$id_nom = $oPersona->getId_nom();
	$ap_nom = $oPersona->getApellidosNombre();
	
	$pagina="programas/dossiers/home_persona.php?id_nom=$id_nom&tabla_pau=$tabla_p";
	
	$a_valores_2[$j][1]=$j;
	$a_valores_2[$j][2]= array( 'ira'=>$pagina, 'valor'=>$ap_nom);
}
*/
$a_valores_2[0] = array();



$oHash = new web\Hash();
$oHash->setcamposForm('tipo_personas!sactividad!any');

// ------------------- seleccion de parámetros ---------------
?>
<form id="que_pdte" action="<?= core\ConfigGlobal::getWeb() ?>/apps/asistentes/controller/activ_pendientes_select.php" method="post"  onkeypress="fnjs_enviar(event,this);" >
<?= $oHash->getCamposHtml(); ?>
<table>
<thead>
<th class=titulo_inv colspan=4><?php echo ucfirst(_("personas")); ?>
&nbsp;&nbsp;&nbsp;
<select name="tipo_personas" size="1">
	<option value="n" label="n" <?php echo $chk_n; ?>>n</option>
	<option value="agd" label="agd" <?php echo $chk_agd; ?>>agd</option>
	<option value="sacd" label="sacd" <?php echo $chk_sacd; ?>>sacd</option>
</select>
</th>
<th class=titulo_inv colspan=4><?php echo ucfirst(_("actividad")); ?>
&nbsp;&nbsp;&nbsp;
<select name="sactividad" size="1">
	<option value="ca" label="ca" <?php echo $chk_ca; ?>>ca</option>
	<option value="crt" label="crt" <?php echo $chk_crt; ?>>crt</option>
</select>
</th>
<th class=titulo_inv colspan=4><?php echo ucfirst(_("curso")); ?>
&nbsp;&nbsp;&nbsp;
<select name="any" size="1">
	<option value='<?= $any_real ?>' <?= $chk_any_1 ?>><?= $txt_curso_1 ?></option>
	<option value='<?= ($any_real+1) ?>' <?= $chk_any_2 ?>><?= $txt_curso_2 ?></option>
</select>
</th>
<th colspan=4><input type="button" onclick="fnjs_enviar_formulario('#que_pdte')" name="ok" value="<?php echo ucfirst(_("buscar")); ?>" class="btn_ok"></th>
</thead>
</TABLE>
</FORM>
<!--  *********************  Listado   *********************     -->
<h2 class=subtitulo><?= $titulo ?></h2>
<?php
$oTabla = new web\Lista();
$oTabla->setId_tabla('activ_pendientes_select');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setDatos($a_valores);
echo $oTabla->mostrar_tabla();
?>
<h2 class=subtitulo><?= _("personas en otras r") ?></h2>
<?php
$oTabla = new web\Lista();
$oTabla->setId_tabla('activ_pendientes_select_otras');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setDatos($a_valores_2);
echo $oTabla->mostrar_tabla();
?>
