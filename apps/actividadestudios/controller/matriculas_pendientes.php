<?php

use actividades\model\entity as actividades;
use actividadestudios\model\entity as actividadestudios;
use asignaturas\model\entity as asignaturas;
use core\ConfigGlobal;
use personas\model\entity as personas;
use web\Hash;
use web\Lista;
use web\Posicion;
use function core\curso_est;
/**
* Para asegurar que inicia la sesion, y poder acceder a los permisos
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

/* Pongo en la variable $curso el periodo del curso */
$mes=date('m');
$any=date('Y');
if ($mes>9) { $any=$any+1; } 
$inicurs_ca=curso_est("inicio",$any);
$fincurs_ca=curso_est("fin",$any);

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
$oPosicion->recordar();

if (empty($_POST['go_to'])) {
	//pongo aqui el $go_to porque al ir al mismo update que las actividaes, no se donde voler
	$a_dataUrl = array('queSel'=>'matriculas','pau'=>'p','id_pau'=>'id_pau');
	$go_to=Hash::link(ConfigGlobal::getWeb().'/apps/actividadestudios/controller/matriculas_pendientes_ver.php?'.http_build_query($a_dataUrl));
} else {
	$go_to = $_POST['go_to'];
}

$aviso = '';
$form = '';
$traslados = '';
if (!empty($traslados)) {
	// personas trasladadas con matriculas pendientes
	// Periodo??

} else {
	$gesMatriculas = new actividadestudios\gestorMatricula();
	$cMatriculasPendientes = $gesMatriculas->getMatriculasPendientes();
}

$titulo = _("Lista de matrículas pendientes de poner nota");
$a_botones=array(
			array( 'txt' => _('ver asignaturas ca'), 'click' =>"fnjs_ver_ca(this.form)" ) ,
			array( 'txt' => _('borrar matricula'), 'click' =>"fnjs_borrar(this.form)" ) 
);

$a_cabeceras=array(_("actividad"),_("asignatura"),_("alumno"),_('p'));

$i=0;
$a_valores=array();
if (isset($Qid_sel) && !empty($Qid_sel)) { $a_valores['select'] = $Qid_sel; }
if (isset($Qscroll_id) && !empty($Qscroll_id)) { $a_valores['scroll_id'] = $Qscroll_id; }
$msg_err = '';
foreach ($cMatriculasPendientes as $oMatricula) {
	$i++;
	$id_nom=$oMatricula->getId_nom();
	$id_activ=$oMatricula->getId_activ();
	$id_asignatura=$oMatricula->getId_asignatura();
	$preceptor=$oMatricula->getPreceptor();
	if ($preceptor == "t") { $preceptor="x"; } else {$preceptor="";}

	//echo "id_activ: $id_activ<br>";
	//echo "id_asignatura: $id_asignatura<br>";

	$oActividad = new actividades\Actividad($id_activ);
	$nom_activ=$oActividad->getNom_activ();
	$oPersona = personas\Persona::newPersona($id_nom);
	if (!is_object($oPersona)) {
		$msg_err .= "<br>$oPersona con id_nom: $id_nom en  ".__FILE__.": line ". __LINE__;
		continue;
	}
	$apellidos_nombre=$oPersona->getApellidosNombre();
	$oAsignatura = new asignaturas\Asignatura($id_asignatura);
	$nombre_corto=$oAsignatura->getNombre_corto();
	
	$a_valores[$i]['sel']="$id_activ#$id_asignatura#$id_nom";
	$a_valores[$i][1]=$nom_activ;
	$a_valores[$i][2]=$nombre_corto;
	$a_valores[$i][3]=$apellidos_nombre;
	$a_valores[$i][4]=$preceptor;
}


$oHash = new Hash();
$oHash->setCamposNo('sel!mod!pau!scroll_id');
$a_camposHidden = array(
		'id_dossier' => 3005,
		'permiso' => 3,
		'obj_pau' => 'Actividad',
		'queSel' => 'asig',
		'go_to' => $go_to
		);
$oHash->setArraycamposHidden($a_camposHidden);

if (!empty($msg_err)) { echo $msg_err; }
echo $oPosicion->mostrar_left_slide(1);

?>
<script>
fnjs_ver_ca=function(formulario,n){
	rta=fnjs_solo_uno(formulario);
	if (rta==1) {
		var mod="#mod";
		//$(mod).val("editar");
		$("#pau").val("a");
		$(formulario).attr('action',"apps/dossiers/controller/dossiers_ver.php");
  		fnjs_enviar_formulario(formulario,'#main');
  	}
}

fnjs_borrar=function(formulario){
	var mensaje;
	mensaje="<?= _("¿Esta Seguro que desea borrar todas las matrículas seleccionadas?");?>"; 
	if (confirm(mensaje) ) {
		var mod="#mod";
		$(mod).val("eliminar");
		$(formulario).attr('action',"apps/actividadestudios/controller/update_3103.php");
		fnjs_enviar_formulario(formulario,'#main');
	}
}
</script>
<h2 class=titulo><?= $titulo ?></h2>
<h3><?= $aviso; ?></h3>
<form id="seleccionados" name="seleccionados" action="" method="post">
<?= $oHash->getCamposHtml(); ?>
<input type="hidden" id="pau" name="pau" value="p">
<input type="hidden" id="mod" name="mod" value="">
<?php
$oTabla = new Lista();
$oTabla->setId_tabla('mtr_pdte');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);
echo $oTabla->mostrar_tabla();
?>
</form>
