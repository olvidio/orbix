<?php
use asignaturas\model as asignaturas;
use notas\model as notas;
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
$id_tabla = $oPersona->getId_Tabla();

$a_botones=array(
				array( 'txt' => _('modificar nota'), 'click' =>"fnjs_modificar(this.form)" ) ,
				array( 'txt' => _('borrar nota'), 'click' =>"fnjs_borrar(this.form)" ) 
	);

$a_cabeceras=array( _("asignatura"),_("nota"),_("acta"),
		array('name'=>ucfirst(_("fecha acta")),'class'=>'fecha'),
		_("preceptor"),_("época")  );

$i=0;
$a_valores = array();
foreach ($cPersonaNotas as $oPersonaNota) {
	$clase = "impar";
	$i % 2  ? 0: $clase = "par";
	$i++;
	$id_nivel=$oPersonaNota->getId_nivel();
	$id_asignatura=$oPersonaNota->getId_asignatura();
	
	$id_situacion=$oPersonaNota->getId_situacion();
	$acta=$oPersonaNota->getActa();
	$f_acta=$oPersonaNota->getF_acta();
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
		$nom_op=$cOpcionales[0]->getNombre_corto();
		$nombre_corto=$nom_op." (".$nombre_corto.")";
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


$oHash = new web\Hash();
$oHash->setcamposForm('sel!mod');
$oHash->setcamposNo('mod');
$a_camposHidden = array(
		'pau' => $pau,
		'id_pau' => $id_pau,
		'obj_pau' => $_POST['obj_pau'],
		'id_dossier' => '1011',
		'permiso' => '3',
		'go_to' => $go_to
		);
$oHash->setArraycamposHidden($a_camposHidden);

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
<h3 class=subtitulo><?php echo ucfirst(_("notas del stgr")); ?></h3>
<form id="seleccionados" name="seleccionados" action="" method="post">
<?= $oHash->getCamposHtml(); ?>
<input type="hidden" id="mod" name="mod" value="">
<?php
$oTabla = new web\Lista();
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
	$pagina = web\Hash::link(core\ConfigGlobal::getWeb().'/apps/notas/controller/form_1011.php?'.http_build_query(array('pau'=>$pau,'id_pau'=>$id_pau,'obj_pau'=>$_POST['obj_pau'],'id_dossier'=>1011,'permiso'=>3,'mod'=>'nuevo','id_asignatura'=>'nueva')));
	?>
	<br><table><tr>
	<td class=botones><span class=link_inv onclick="fnjs_update_div('#ficha_personas','<?= $pagina ?>');">
		<?= _("añadir nota") ?></span></td>
	</tr></table>
	<?php
}
?>
