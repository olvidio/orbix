<?php
use actividades\model as actividades;
use actividadestudios\model as actividadestudios;
use asignaturas\model as asignaturas;
use asistentes\model as asistentes;
use personas\model as personas;
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
$inicurs_ca=core\curso_est("inicio",$any);
$fincurs_ca=core\curso_est("fin",$any);

//$curso="AND f_ini BETWEEN '$inicurs_ca' AND '$fincurs_ca' ";
$todos = empty($_POST['todos'])? '' : $_POST['todos'];

if (!empty($_POST['sel'])) { //vengo de un checkbox
	$id_pau=strtok($_POST['sel'][0],"#");
	$id_tabla=strtok("#");
}

if (empty($_POST['go_to'])) {
	//pongo aqui el $go_to porque al ir al mismo update que las actividaes, no se donde voler
	$a_dataUrl = array('queSel'=>'matriculas','pau'=>$pau,'id_pau'=>$id_pau,'obj_pau'=>$_POST['obj_pau'],'id_dossier'=>$id_dossier);
	$go_to=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/dossiers/controller/dossiers_ver.php?'.http_build_query($a_dataUrl));
} else {
	$go_to = $_POST['go_to'];
}

$aviso = '';
// Compruebo si està de repaso...
$oPersona = new personas\PersonaDl(array('id_nom'=>$id_pau));
$stgr = $oPersona->getStgr();
if ($stgr == 'r') $aviso .= _("Está de repaso")."<br>";

echo $oPosicion->atras();

if (!empty($_POST['id_activ'])) {  // ¿? ya tengo una actividad concreta (vengo del dossier de esa actividad).
	$sql_tabla = "SELECT a.nom_activ, asis.id_activ, asis.est_ok
			FROM a_actividades a, d_asistentes_activ asis
			WHERE a.id_activ=$id_activ AND asis.id_nom='$id_pau' AND asis.id_activ=a.id_activ
			";
	echo "$sql_tabla";
} else {
	$GesAsistentes = new asistentes\GestorAsistente();
	if (empty($todos)) {
		$aWhere['f_ini'] = "'$inicurs_ca','$fincurs_ca'";
		$aOperadores['f_ini'] = 'BETWEEN';
	}
	$aWhere['id_tipo_activ'] = '^[12][13][23]';
	$aOperadores['id_tipo_activ'] = '~';

	$cAsistencias = $GesAsistentes-> getActividadesDeAsistente(array('id_nom'=>$id_pau,'propio'=>'t'),$aWhere,$aOperadores,true);
}
if (is_array($cAsistencias)) {
	$n = count($cAsistencias);
   	if ( $n == 0 && empty($todos)) {
		$oHashA = new web\Hash();
		$oHashA->setcamposForm('sel');
		$oHashA->setcamposNo('scroll_id');
		$a_camposHidden = array(
					'pau' => 'p',
					'obj_pau' => $_POST['obj_pau'],
					'permiso' => '3',
					'breve' => $_POST['breve'],
					'es_sacd' => $_POST['es_sacd'],
					'tabla' => $_POST['tabla'],
					'que' => 'matriculas',
					'id_dossier' => 1303,
					'todos' => 1
					);
		$oHashA->setArraycamposHidden($a_camposHidden);
		
		$aviso .= _(sprintf("No tiene asignado ningún ca como propio este curso: %s - %s.",$inicurs_ca,$fincurs_ca)); 
		$aviso .= "<form action='apps/dossiers/controller/dossiers_ver.php' method='post'>";
		$aviso .= $oHashA->getCamposHtml();
		$aviso .= "<input type=hidden name='sel[]' value='".$_POST['sel'][0]."' >";
		$aviso .= "<input type=hidden name='scroll_id' value='".$_POST['scroll_id']."' >";
		$aviso .= "<input type=button onclick=fnjs_enviar_formulario(this.form) value='"._("ver anteriores")."'>";
		$aviso .= "</form>";
	}
   	if ( $n == 0 && !empty($todos)) {
		$aviso .= _("No tiene asignado ningún ca."); 
	}
   	if ( $n > 1 && empty($todos)) { $aviso .= _(sprintf("¡¡ojo!! tiene %s actividades de estudios asignadas como propias.",$n)); }
}
?>
<script>
fnjs_grabar_est=function(formulario,n){
	var mod="#mod"+n;
	var go1="#go_to"+n;
	var go2="#go_to2"+n;
	$(mod).val("plan");
	go = $(go2).val();
	$(go1).val(go);
	$(formulario).attr('action',"apps/actividadestudios/controller/update_3103.php");
	fnjs_enviar_formulario(formulario,'#ficha_personas');
}
fnjs_modificar=function(formulario,n){
	rta=fnjs_solo_uno(formulario);
	if (rta==1) {
		var mod="#mod"+n;
		$(mod).val("editar");
		$(formulario).attr('action',"apps/actividadestudios/controller/form_1303.php");
  		fnjs_enviar_formulario(formulario,'#ficha_personas');
  	}
}

fnjs_borrar=function(formulario,n){
	var mensaje;
	rta=fnjs_solo_uno(formulario);
	if (rta==1) {
		mensaje="<?php echo _("¿Esta Seguro que desea quitar esta asignatura?");?>"; 
		if (confirm(mensaje) ) {
			var mod="#mod"+n;
	  		$(mod).val("eliminar");
			$(formulario).attr('action',"apps/actividadestudios/controller/update_3103.php");
	  		fnjs_enviar_formulario(formulario,'#ficha_personas');
		}
  	}
}
</script>
<h3><?php echo $aviso; ?></h3>
<?php
// para más de un ca
$ca=0;
foreach ($cAsistencias as $oAsistente) {
	$ca++;
	$id_activ=$oAsistente->getId_activ();
	$propio=$oAsistente->getPropio();
	if ($propio != 't')  echo "Dani t'has colat";
	$est_ok=$oAsistente->getEst_ok();
	$oActividad = new actividades\Actividad(array('id_activ'=>$id_activ));
	extract($oActividad->getTot());
	$GesMatriculas = new actividadestudios\GestorMatricula();
	$cMatriculas = $GesMatriculas->getMatriculas(array('id_nom'=>$id_pau,'id_activ'=>$id_activ));
	$form="seleccionados".$ca;
	
	if ($est_ok=="t") {
			$chk_1="checked";
			$chk_2="";
	} else { 
			$chk_1="";
			$chk_2="checked";
	}

	$a_botones=array(
				array( 'txt' => _('modificar tipo'), 'click' =>"fnjs_modificar(this.form,$ca)" ) ,
				array( 'txt' => _('borrar matricula'), 'click' =>"fnjs_borrar(this.form,$ca)" ) 
	);
	
	$a_cabeceras=array(_("preceptor"),_("asignatura"));
	
	$i=0;
	$a_valores=array();
	foreach ($cMatriculas as $oMatricula) {
		$i++;
		$id_asignatura=$oMatricula->getId_asignatura();
		$preceptor=$oMatricula->getPreceptor();
		if ($preceptor == "t") { $preceptor="x"; } else {$preceptor="";}

		$oAsignatura = new asignaturas\Asignatura($id_asignatura);
		$nombre_corto=$oAsignatura->getNombre_corto();
		
		$a_valores[$i]['sel']="$id_activ#$id_asignatura";
		$a_valores[$i][1]=$preceptor;
		$a_valores[$i][2]=$nombre_corto;
	}
	
	$oHash = new web\Hash();
	$oHash->setcamposForm('est_ok');
	$oHash->setCamposNo('sel!mod!go_to!go_to2');
	$a_camposHidden = array(
			'pau' => $pau,
			'id_pau' => $id_pau,
			'id_activ' => $id_activ,
			'obj_pau' => $_POST['obj_pau'],
			'id_dossier' => $id_dossier,
			'permiso' => 3
			);
	$oHash->setArraycamposHidden($a_camposHidden);

	// al grabar estidios vuelvo a la lista de gente
	$a_dataUrl = array('queSel'=>'asis','pau'=>'a','id_pau'=>$id_activ,'obj_pau'=>'ActividadDl','id_dossier'=>3103);
	$go_to2=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/dossiers/controller/dossiers_ver.php?'.http_build_query($a_dataUrl));

	?>
	<h3 class=subtitulo><?php echo ucfirst($nom_activ); ?></h3>
	<form id="<?= $form; ?>" name="<?= $form; ?>" action="" method="post">
	<?= $oHash->getCamposHtml(); ?>
	<input type="hidden" id="mod<?= $ca ?>" name="mod" value="">
	<input type="hidden" id="go_to<?= $ca ?>" name="go_to" value="<?= $go_to ?>">
	<input type="hidden" id="go_to2<?= $ca ?>" name="go_to2" value="<?= $go_to2 ?>">
	<table><tr><td>
	<?= ucfirst(_("plan de estudios confirmado")) ?>
	<input type="Radio" id="est_ok" name="est_ok" value="t" <?= $chk_1 ?> onclick=fnjs_grabar_est(this.form,<?= $ca ?>)><?= ucfirst(_("si")) ?>
	<input type="Radio" id="est_ok" name="est_ok" value="f" <?= $chk_2 ?> onclick=fnjs_grabar_est(this.form,<?= $ca ?>)><?= ucfirst(_("no")) ?>
	</td></tr></table><br>
	<?php
	$oTabla = new web\Lista();
	$oTabla->setId_tabla('sql_1303'.$ca);
	$oTabla->setCabeceras($a_cabeceras);
	$oTabla->setBotones($a_botones);
	$oTabla->setDatos($a_valores);
	echo $oTabla->mostrar_tabla();
	?>
	</form>
	<?php
	// --------------  boton insert ----------------------
	echo "<br><table cellspacing=3  class=botones><tr class=botones>";
	$a_dataUrl = array('mod'=>'nuevo','pau'=>$pau,'id_pau'=>$id_pau,'id_activ'=>$id_activ,'go_to'=>$go_to);
	$pagina=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/actividadestudios/controller/form_1303.php?'.http_build_query($a_dataUrl));
	echo "<td class=botones><span class=link_inv onclick=\"fnjs_update_div('#ficha_personas','$pagina');\">"._("añadir asignatura")."</span></td>";
	$a_dataUrl = array('mod'=>'nuevo','pau'=>$pau,'id_nom'=>$id_pau,'id_activ'=>$id_activ,'go_to'=>$go_to);
	$pagina=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/actividadestudios/controller/matricular.php?'.http_build_query($a_dataUrl));	
//	$pagina="programas/matricular.php?mod=nuevo&pau=$pau&id_nom=$id_pau&id_activ=$id_activ&go_to=".urlencode("dossiers/$go_to");
	echo "<td class=botones><span class=link_inv onclick=\"fnjs_update_div('#ficha_personas','$pagina');\">"._("matricular automáticamente")."</span></td>";
	
	echo "</tr></table></form>";
	unset($a_botones);
	unset($a_cabeceras);
	unset($a_valores);
}// fin de más de un ca
?>
