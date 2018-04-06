<?php

use actividades\model as actividades;
use asignaturas\model as asignaturas;
use core\ConfigGlobal;
use notas\model as notas;
use personas\model as personas;
use web\Hash;
use web\Lista;
use web\Posicion;
/**
* En el fichero config tenemos las variables genéricas del sistema
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

$sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($sel)) { //vengo de un checkbox
	// el scroll id es de la página anterior, hay que guardarlo allí
	$oPosicion->addParametro('id_sel',$sel,1);
	$scroll_id = empty($_POST['scroll_id'])? 0 : $_POST['scroll_id'];
	$oPosicion->addParametro('scroll_id',$scroll_id,1);
}

$oPosicion->recordar();

// Aviso si le faltan notas por poner
$gesMatriculas = new actividadestudios\model\gestorMatricula();
$cMatriculasPendientes = $gesMatriculas->getMatriculasPendientes($id_pau);
if (count($cMatriculasPendientes) > 0) {
	$msg = '';
	foreach ($cMatriculasPendientes as $oMatricula) {
		$id_activ = $oMatricula->getId_activ();
		$id_asignatura = $oMatricula->getId_asignatura();
		$oActividad = new actividades\ActividadAll($id_activ);
		$nom_activ = $oActividad->getNom_activ();
		$oAsignatura = new asignaturas\Asignatura($id_asignatura);
		$nombre_corto=$oAsignatura->getNombre_corto();
		$msg .= empty($msg)? '' : '<br>';
		$msg .= sprintf(_("ca: %s, asignatura: %s"),$nom_activ,$nombre_corto);
	}
	if (!empty($msg)) {
		$msg = _("Tiene pendiente de poner las notas de:") .'<br>'.$msg;
	}
}


$gesPersonaNotas = new notas\GestorPersonaNota();
$cPersonaNotas = $gesPersonaNotas->getPersonaNotas(array('id_nom'=>$id_pau,'id_asignatura'=>9000,'_ordre'=>'id_nivel'),array('id_asignatura'=>'<'));

$gesNotas = new notas\GestorNota();
$cNotas = $gesNotas->getNotas();
$a_notas = array();
foreach ($cNotas as $oNota) {
	$id_situacion = $oNota->getId_situacion();
	$breve = $oNota->getBreve();
	$a_notas[$id_situacion] = $breve;
}


//Según el tipo de persona: n, agd, s
$oPersona = personas\Persona::NewPersona($id_pau);
if (!is_object($oPersona)) {
	$msg_err = "<br>$oPersona con id_nom: $id_pau";
	exit($msg_err);
}
$id_tabla = $oPersona->getId_Tabla();

$a_botones=array(
				array( 'txt' => _('modificar nota'), 'click' =>"fnjs_modificar(this.form)" ) ,
				array( 'txt' => _('borrar asignatura'), 'click' =>"fnjs_borrar(this.form)" ) 
	);

$a_cabeceras=array( _("asignatura"),_("nota"),_("acta"),
		array('name'=>ucfirst(_("fecha acta")),'class'=>'fecha'),
		_("preceptor"),_("época")  );

$i=0;
$a_valores = array();
if (isset($Qid_sel) && !empty($Qid_sel)) { $a_valores['select'] = $Qid_sel; }
if (isset($Qscroll_id) && !empty($Qscroll_id)) { $a_valores['scroll_id'] = $Qscroll_id; }
foreach ($cPersonaNotas as $oPersonaNota) {
	$clase = "impar";
	$i % 2  ? 0: $clase = "par";
	$i++;
	$id_nivel=$oPersonaNota->getId_nivel();
	$id_asignatura=$oPersonaNota->getId_asignatura();
	
	$id_situacion=$oPersonaNota->getId_situacion();
	$f_acta=$oPersonaNota->getF_acta();
	$acta=$oPersonaNota->getActa();
	$preceptor=$oPersonaNota->getPreceptor();
	$id_preceptor=$oPersonaNota->getId_preceptor();
	$epoca=$oPersonaNota->getEpoca();
	$id_activ=$oPersonaNota->getId_activ();

	//$nota = $a_notas[$id_situacion];
	$nota = $oPersonaNota->getNota_txt();
	
	$oAsignatura = new asignaturas\Asignatura($id_asignatura);
	$nombre_corto=$oAsignatura->getNombre_corto();
	$id_sector=$oAsignatura->getId_sector();

	// opcionales
	if ($id_asignatura > 3000) {
		$gesOpcionales = new asignaturas\GestorAsignatura();
		$cOpcionales = $gesOpcionales->getAsignaturas(array('id_nivel'=>$id_nivel));
        if (empty($cOpcionales)) {
            $nombre_corto= _("Opcional de extra");
        } else {
			$nom_op=$cOpcionales[0]->getNombre_corto();
			$nombre_corto=$nom_op." (".$nombre_corto.")";
		}
	}
	
	if ($preceptor=="t") { $preceptor=_("si"); } else { $preceptor=_("no");}	
	// preceptor
	if ($id_preceptor && $preceptor=="t") {
		$oPersonaDl = new personas\PersonaDl($id_preceptor);
		$nom_precptor=$oPeronaDl->getApellidosNombre();
		if (empty($nom_precptor)) {
			$nom_precptor=_("no lo encuentro");
		}
		$preceptor.=" (".$nom_precptor.")";
	}

	if ($permiso==3) {
		$a_valores[$i]['sel']="$id_nivel#$id_asignatura";
	} else {
		$a_valores[$i]['sel']="";
	}
	$a_valores[$i][1]="$nombre_corto";
	$a_valores[$i][2]=$nota;
	$a_valores[$i][3]=$acta;
	$a_valores[$i][4]=$f_acta;
	$a_valores[$i][5]=$preceptor;
	$a_valores[$i][6]=$epoca;

}


$oHash = new Hash();
$oHash->setcamposForm('sel!mod');
$oHash->setcamposNo('mod!scroll_id');
$a_camposHidden = array(
		'pau' => $pau,
		'id_pau' => $id_pau,
		'obj_pau' => $_POST['obj_pau'],
		'id_dossier' => '1011',
		'permiso' => '3',
		'go_to' => $go_to
		);
$oHash->setArraycamposHidden($a_camposHidden);

echo $oPosicion->mostrar_left_slide(1);
?>
<script>
fnjs_modificar=function(formulario){
	rta=fnjs_solo_uno(formulario);
	if (rta==1) {
		$('#mod').val("editar");
  		$(formulario).attr('action',"apps/notas/controller/form_1011.php");
  		fnjs_enviar_formulario(formulario,'#ficha_personas');
  	}
}
fnjs_borrar=function(formulario){
	rta=fnjs_solo_uno(formulario);
	var seguro;
	if (rta==1) {
		seguro=confirm("<?php echo _("¿Esta Seguro que desea borrar la nota de esta asignatura?");?>");
		if (seguro) {
			$('#mod').val("eliminar");
	  		$(formulario).attr('action',"apps/notas/controller/update_1011.php");
	  		fnjs_enviar_formulario(formulario,'#ficha_personas');
		}
  	}
}
</script>
<h2 class=titulo><?php echo ucfirst(_("notas del stgr")); ?></h2>
<?php if (!empty($msg)) { ?>
	<h3 class=subtitulo><?= $msg ?></h3>
<?php } ?>
<form id="seleccionados" name="seleccionados" action="" method="post">
<?= $oHash->getCamposHtml(); ?>
<input type="hidden" id="mod" name="mod" value="">
<?php
$oTabla = new Lista();
$oTabla->setId_tabla('sql_1011');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);
echo $oTabla->mostrar_tabla();
?>
</form>
<?php
// --------------  boton insert ----------------------


//boton insert
$go_to=urlencode($go_to);

if ($permiso==3) {
	$pagina = Hash::link(ConfigGlobal::getWeb().'/apps/notas/controller/form_1011.php?'.http_build_query(array('pau'=>$pau,'id_pau'=>$id_pau,'obj_pau'=>$_POST['obj_pau'],'id_dossier'=>1011,'permiso'=>3,'mod'=>'nuevo','id_asignatura'=>'nueva')));
	?>
	<br><table><tr>
	<td class=botones><span class=link_inv onclick="fnjs_update_div('#ficha_personas','<?= $pagina ?>');">
		<?= _("añadir nota") ?></span></td>
	</tr></table>
	<?php
}