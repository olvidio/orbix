<?php
use actividades\model as actividades;
use actividadestudios\model as actividadestudios;
use asignaturas\model as asignaturas;
use personas\model as personas;
/**
* En el fichero config tenemos las variables genéricas del sistema
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

//pongo aqui el $go_to porque al ir al mismo update que las actividaes, no se donde voler
$a_dataUrl = array('pau'=>$pau,'id_pau'=>$id_pau,'obj_pau'=>$_POST['obj_pau'],'id_dossier'=>$id_dossier);
$go_to=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/dossiers/controller/dossiers_ver.php?'.http_build_query($a_dataUrl));
	
/*
if (!empty($_POST['sel'])) { //vengo de un checkbox
	//$id_nom=$sel[0];
	$id_nom=strtok($_POST['sel'][0],"#");
	$id_tabla=strtok("#");
}
*/

$oActividad = new actividades\Actividad($id_pau);
$nom_activ = $oActividad->getNom_activ();

$GesActividadAsignaturas = new actividadestudios\GestorActividadAsignatura();
$cActividadAsignaturas = $GesActividadAsignaturas->getActividadAsignaturas(array('id_activ'=>$id_pau));

if (is_array($cActividadAsignaturas) && count($cActividadAsignaturas)==0) {
	echo _("Esta actividad no tiene ninguna asignatura");
	exit;
}

$a_botones=array(
			array( 'txt' => _('borrar matricula'), 'click' =>"fnjs_borrar(this.form)" ) 
);

$a_cabeceras=array(_("asignatura"),_("alumno"));

	
$oHash = new web\Hash();
$oHash->setcamposForm('');
$oHash->setCamposNo('sel!scroll_id!mod!nuevo');
$a_camposHidden = array(
		'pau' => $pau,
		'id_pau' => $id_pau,
		'obj_pau' => $_POST['obj_pau'],
		'id_dossier' => $id_dossier,
		'permiso' => 3,
		'go_to' => $go_to
		);
$oHash->setArraycamposHidden($a_camposHidden);

echo $oPosicion->atras();
?>	
<script>
fnjs_modificar=function(formulario){
	rta=fnjs_solo_uno(formulario);
	if (rta==1) {
		$(nuevo).val(2);
  		$(formulario).attr('action',"apps/actividadestudios/controller/form_1303.php");
  		fnjs_enviar_formulario(formulario);
  	}
}

fnjs_borrar=function(formulario){
	var mensaje;
	rta=fnjs_solo_uno(formulario);
	if (rta==1) {
		mensaje='<?php echo _("¿Esta Seguro que desea quitar esta matricula?");?>'; 
		if (confirm(mensaje) ) {
			$('#mod').val("eliminar");
			go=$('#go_to').val();
			$(formulario).attr('action',"apps/actividadestudios/controller/update_3103.php");
			$(formulario).submit(function() {
				$.ajax({
					data: $(this).serialize(),
					url: $(this).attr('action'),
					type: 'post',
					complete: function (rta) {
						rta_txt = rta.responseText;
						if (rta_txt.search('id="ir_a"') != -1) {
							fnjs_mostra_resposta(rta,'#main'); 
						} else {
							alert (rta_txt);
							if (go) fnjs_update_div('#main',go); 
						}
					}
				});
				return false;
			});
			$(formulario).submit();
			$(formulario).off();
		}
  	}
}
</script>
<h3 class=subtitulo><?php echo ucfirst(_($nom_activ)); ?></h3>
<form id="seleccionados" name="seleccionados" action="" method="post">
<?= $oHash->getCamposHtml(); ?>
<input type="hidden" id="nuevo" name="nuevo" value="">
<input type="hidden" id="mod" name="mod" value="">

<?php
// por cada asignatura
$a=0;
$msg_err = '';
foreach ($cActividadAsignaturas as $oActividadAsignatura) {
	$a++;
	$id_asignatura=$oActividadAsignatura->getId_asignatura();
	$id_profesor=$oActividadAsignatura->getId_profesor();
	if (!empty($id_profesor)) {
		$oPersona = personas\Persona::NewPersona($id_profesor);
		if (!is_object($oPersona)) {
			$msg_err .= "<br>$oPersona con id_nom: $id_profesor";
			continue;
		}
		$nom_profesor=$oPersona->getApellidosNombre();
	} else {
		$nom_profesor = '';
	}

	$oAsignatura = new asignaturas\Asignatura($id_asignatura);
	$nombre_corto=$oAsignatura->getNombre_corto();

	//busco los matriculados:
	$GesMatriculas = new actividadestudios\GestorMatriculaDl();
	$cMatriculas = $GesMatriculas->getMatriculas(array('id_activ'=>$id_pau,'id_asignatura'=>$id_asignatura));
	$i=0;
	$a_valores=array();
	foreach($cMatriculas as $oMatricula) {
		$id_nom=$oMatricula->getId_nom();
		$oPersona = personas\Persona::NewPersona($id_nom);
		if (!is_object($oPersona)) {
			$msg_err .= "<br>$oPersona con id_nom: $id_nom";
			continue;
		}
		$nom_persona=$oPersona->getApellidosNombre();
		$ctr=$oPersona->getCentro_o_dl();
		$stgr=$oPersona->getStgr();

		$clase = "impar";
		$i % 2  ? 0: $clase = "par";
		$i++;
		
		$a_valores[$i]['sel']="$id_nom#$id_asignatura";
		$a_valores[$i][1]="$nombre_corto";
		$a_valores[$i][2]="$nom_persona ($ctr)";
	}
	$oTabla = new web\Lista();
	$oTabla->setId_tabla('sql_3103'.$a);
	$oTabla->setCabeceras($a_cabeceras);
	$oTabla->setBotones($a_botones);
	$oTabla->setDatos($a_valores);
	echo $oTabla->mostrar_tabla();
}
if (!empty($msg_err)) { echo $msg_err; }
?>
