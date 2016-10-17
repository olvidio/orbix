<?php
use actividades\model as actividades;
use asignaturas\model as asignaturas;
use personas\model as personas;
use actividadestudios\model as actividadestudios;
/**
* Para asegurar que inicia la sesion, y poder acceder a los permisos
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

//pongo aqui el $go_to porque al ir al mismo update que las actividaes, no se donde voler
//$go_to=core\ConfigGlobal::getWeb()."/apps/dossiers/controller/dossiers_ver.php?pau=$pau&id_pau=$id_pau&tabla_pau=".$_POST['tabla_pau']."&id_dossier=$id_dossier&permiso=$permiso";
$a_dataUrl = array('pau'=>$pau,'id_pau'=>$id_pau,'obj_pau'=>$_POST['obj_pau'],'id_dossier'=>$id_dossier,'permiso'=>$permiso);
$go_to=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/dossiers/controller/dossiers_ver.php?'.http_build_query($a_dataUrl));

$a_botones=array(
		            array( 'txt' => _('modificar'), 'click' =>"fnjs_modificar(this.form)" ) ,
		            array( 'txt' => _('quitar asignatura'), 'click' =>"fnjs_borrar_asignatura(this.form)" ) ,
		            array( 'txt' => _('actas'), 'click' =>"fnjs_actas(this.form)" ) 
		);
	
$a_cabeceras=array( _("interés"),_("asignatura"),_("créditos"),_("tipo"),_("profesor"),_("prof. avisado"),_("inicio"),_("fin")  );

$GesActivAsignaturas = new actividadestudios\GestorActividadAsignatura();
$cActivAsignaturas = $GesActivAsignaturas->getActividadAsignaturas(array('id_activ'=>$id_pau,'_ordre'=>'id_asignatura')); 
$c=0;
$a_valores=array();
foreach ($cActivAsignaturas as $oActividadAsignatura) {
	$c++;
	$id_activ=$oActividadAsignatura->getId_activ();
	$id_asignatura=$oActividadAsignatura->getId_asignatura();
	$oAsignatura = new asignaturas\Asignatura($id_asignatura);
	$nombre_corto=$oAsignatura->getNombre_corto();
	$creditos=$oAsignatura->getCreditos();
	$interes=$oActividadAsignatura->getInteres();
	$id_profesor=$oActividadAsignatura->getId_profesor();
	if (!empty($id_profesor)) {
		$oPersona = personas\Persona::NewPersona($id_profesor);
		$nom = $oPersona->getApellidosNombre();
	} else {
		$nom='';
	}
	switch($oActividadAsignatura->getAvis_profesor()) {
		case "a": $aviso=_("avisado"); break;
		case "c": $aviso=_("confirmado"); break;
		default: $aviso="";
	}
	$tipo=$oActividadAsignatura->getTipo();
	$f_ini=$oActividadAsignatura->getF_ini();
	$f_fin=$oActividadAsignatura->getF_fin();
	
	if ($permiso==3) {
		$a_valores[$c]['sel']="$id_activ#$id_asignatura";
	} else {
		$a_valores[$c]['sel']="";
	}
			
	$a_valores[$c][1]="$interes";
	$a_valores[$c][2]="$nombre_corto";
	$a_valores[$c][3]=$creditos;
	$a_valores[$c][4]=$tipo;
	$a_valores[$c][5]=$nom;
	$a_valores[$c][6]=$aviso;
	$a_valores[$c][7]=$f_ini;
	$a_valores[$c][8]=$f_fin;
}

$oHash = new web\Hash();
$oHash->setcamposForm('');
$oHash->setCamposNo('sel!mod');
$a_camposHidden = array(
		'pau' => $pau,
		'id_pau' => $id_pau,
		'id_dossier' => $id_dossier,
		'permiso' => 3,
		'go_to' => $go_to
		);
		//'obj_pau' => $_POST['obj_pau'],
/*<input type='hidden' id='tabla_pau' name='tabla_pau' value='<?= $_POST['tabla_pau'] ?>'> */

$oHash->setArraycamposHidden($a_camposHidden);

/* ---------------------------------- html --------------------------------------- */
?>
<script>
fnjs_actas=function(formulario){
	rta=fnjs_solo_uno(formulario);
	if (rta==1) {
  		$(formulario).attr('action',"apps/actividadestudios/controller/acta_notas.php");
  		fnjs_enviar_formulario(formulario,'#ficha_activ');
  	}
}
fnjs_modificar=function(formulario){
	rta=fnjs_solo_uno(formulario);
	if (rta==1) {
		$('#mod').val("editar");
  		$(formulario).attr('action',"apps/actividadestudios/controller/form_3005.php");
  		fnjs_enviar_formulario(formulario,'#ficha_activ');
  	}
}
fnjs_borrar_asignatura=function(formulario){
	rta=fnjs_solo_uno(formulario);
	if (rta==1) {
		if (confirm("<?php echo _("¿Esta Seguro que desea quitar este asignatura de esta actividad?");?>") ) {
			$('#mod').val("eliminar");
			go=$('#go_to').val();
			$(formulario).attr('action',"apps/actividadestudios/controller/update_3005.php");
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
<h2 class=titulo><?php echo ucfirst(_("relación de asignaturas")); ?></h2>
<form id="seleccionados" name="seleccionados" action="" method="post">
<?= $oHash->getCamposHtml(); ?>
<input type='hidden' id='mod' name='mod' value=''>
<?php
$oTabla = new web\Lista();
$oTabla->setId_tabla('sql_3005');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);
echo $oTabla->mostrar_tabla();
?>
</form>
<?php
// --------------  boton insert ----------------------

if ($permiso==3) {
	$a_dataUrl = array('pau'=>$pau,'id_pau'=>$id_pau,'go_to'=>$go_to);
	$pagina=web\Hash::link(core\ConfigGlobal::getWeb()."/apps/actividadestudios/controller/form_3005.php?".http_build_query($a_dataUrl));
	echo "<br><table cellspacing=3  class=botones><tr class=botones>";
	echo "<td class=botones><span class=link_inv onclick=\"fnjs_update_div('#ficha_activ','$pagina');\" >".sprintf(_("añadir asignatura"))."</span></td>";
	echo "</tr></table>";
}
?>
