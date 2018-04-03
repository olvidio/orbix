<?php
use actividades\model as actividades;
use personas\model as personas;
use asistentes\model as asistentes;
use dossiers\model as dossiers;
/**
* En el fichero config tenemos las variables genéricas del sistema
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$periodo = empty($_POST['periodo'])? 1 : $_POST['periodo'];
//pongo aqui el $go_to porque al ir al mismo update que las actividaes, no se donde voler
$go_to=core\ConfigGlobal::getWeb().'/apps/dossiers/controller/dossiers_ver.php?'.http_build_query(array('pau'=>$pau,'id_pau'=>$id_pau,'id_dossier'=>$id_dossier,'obj_pau' => $_POST['obj_pau'],'periodo'=>$periodo)); 

$mi_sfsv = core\ConfigGlobal::mi_sfsv();
/* Pongo en la variable $curso el periodo del curso */
$mes=date('m');
if ($mes>9) { $any=date('Y')+1; } else { $any=date("Y"); }
$inicurs_ca=core\curso_est("inicio",$any);
$fincurs_ca=core\curso_est("fin",$any);

$aWhere = array();
$aOperator = array();
$aWhere['_ordre'] = 'f_ini';

switch ($periodo) {
	case 2 :
		$chk_1="";
		$chk_2="checked";
		$chk_3="";
		//$condicion=$curso;
		$aWhere['f_ini'] =  "'$inicurs_ca','$fincurs_ca'";
		$aOperator['f_ini'] = 'BETWEEN';
		break;
	case 3:
		$chk_1="";
		$chk_2="";
		$chk_3="checked";
		//$condicion="";
		break;
	case 1:
	default:
		$chk_1="checked";
		$chk_2="";
		$chk_3="";
		//$condicion="status=2 AND ".$curso;
		$aWhere['status'] = 2;
		$aWhere['f_ini'] =  "'$inicurs_ca','$fincurs_ca'";
		$aOperator['f_ini'] = 'BETWEEN';
		break;
}
$gesAsistente=new asistentes\GestorAsistente();
$oPersona = personas\Persona::newPersona($id_pau);
if (!is_object($oPersona)) {
	$msg_err = "<br>$oPersona con id_nom: $id_pau";
	exit($msg_err);
}
// permisos Según el tipo de persona: n, agd, s
$id_tabla=$oPersona->getId_tabla();
$oPermDossier = new dossiers\PermDossier();
$ref_perm = $oPermDossier->perm_activ_pers($id_tabla);
$a_botones=array(
				array( 'txt' => _('modificar asistencia'), 'click' =>"fnjs_modificar(\"#seleccionados\")" ) ,
				array( 'txt' => _('borrar asistencia'), 'click' =>"fnjs_borrar(\"#seleccionados\")" ) 
	);

$a_cabeceras=array( array('name'=>_("fechas"),'width'=>150),array('name'=>_("nombre"),'width'=>300),_("propio"),_("est. ok"),_("falta"),_("observ.")  );
$a_valores=array();

$i=0;
$cActividadesAsistente = $gesAsistente->getActividadesDeAsistente(array('id_nom'=>$id_pau),$aWhere,$aOperator,TRUE);
foreach ($cActividadesAsistente as $oActividadAsistente) {
	$i++;
	$id_activ=$oActividadAsistente->getId_activ();
	$id_tabla_asist=$oActividadAsistente->getId_tabla();
	$oActividad=new actividades\Actividad($id_activ);
	$nom_activ=$oActividad->getNom_activ();
	$id_tipo_activ=$oActividad->getId_tipo_activ();
	$dl_org=$oActividad->getDl_org();
	$f_ini=$oActividad->getF_ini();
	$f_fin=$oActividad->getF_fin();

	$propio=$oActividadAsistente->getPropio();
	$falta=$oActividadAsistente->getFalta();
	$est_ok=$oActividadAsistente->getEst_ok();
	$observ=$oActividadAsistente->getObserv();

	$oTipoActividad = new web\TiposActividades($id_tipo_activ);
	$isfsv=$oTipoActividad->getSfsvId();
	// para ver el nombre en caso de la otra sección
	if ($mi_sfsv != $isfsv && !($_SESSION['oPerm']->have_perm("des")) ) {
		$ssfsv=$oTipoActividad->getSfsvText();
		$sactividad=$oTipoActividad->getActividadText();
		$nom_activ="$ssfsv $sactividad";
	}
	// para modificar.
	$id_tipo=substr($id_tipo_activ,0,3); //cojo los 3 primeros dígitos
	$act=!empty($ref_perm[$id_tipo])? $ref_perm[$id_tipo] : '';

	if (!empty($act["perm"])) { $permiso=3; } else { $permiso=1; }
	
	$propio=='t' ? $chk_propio="si" : $chk_propio="no" ;
	$falta=='t' ? $chk_falta="si" : $chk_falta="no" ;
	$est_ok=='t' ? $chk_est_ok="si" : $chk_est_ok="no" ;

	if ($permiso==3) {
		$a_valores[$i]['sel']="$id_activ";
	} else {
		$a_valores[$i]['sel']="";
	}
	$a_valores[$i][1]="$f_ini-$f_fin";
	$a_valores[$i][2]=$nom_activ;
	$a_valores[$i][3]=$chk_propio;
	$a_valores[$i][4]=$chk_est_ok;
	$a_valores[$i][5]=$chk_falta;
	$a_valores[$i][6]=$observ;
}
// Estas dos variables vienen de la pagina 'padre' dossiers_ver.php
// las pongo al final, porque al contar los valores del array se despista.
if (isset($Qid_sel) && !empty($Qid_sel)) { $a_valores['select'] = $Qid_sel; }
if (isset($Qscroll_id) && !empty($Qscroll_id)) { $a_valores['scroll_id'] = $Qscroll_id; }

$oHash = new web\Hash();
$oHash->setcamposForm('periodo');
$oHash->setCamposNo('mod!sel!scroll_id');
$a_camposHidden = array(
		'pau' => $pau,
		'id_pau' => $id_pau,
		'obj_pau' => $_POST['obj_pau'],
		'id_dossier' => $id_dossier,
		'permiso' => 3,
		'go_to' => $go_to
		);
$oHash->setArraycamposHidden($a_camposHidden);

echo $oPosicion->mostrar_left_slide();
?>
<script>
fnjs_actuales=function(formulario){
  $(formulario).attr('action',"apps/dossiers/controller/dossiers_ver.php");
  fnjs_enviar_formulario(formulario);
}
fnjs_modificar=function(formulario){
	rta=fnjs_solo_uno(formulario);
	if (rta==1) {
		$('#mod').val("editar");
  		$(formulario).attr('action',"apps/asistentes/controller/form_1301.php");
  		fnjs_enviar_formulario(formulario,'#ficha_personas');
  	}
}
fnjs_borrar=function(formulario){
	rta=fnjs_solo_uno(formulario);
	var seguro;
	if (rta==1) {
		seguro=confirm("<?php echo _("¿Esta Seguro que desea borrar a esta persona de esta actividad?");?>");
		if (seguro) {
			$('#mod').val("eliminar");
	  		$(formulario).attr('action',"apps/asistentes/controller/update_3101.php");
	  		fnjs_enviar_formulario(formulario,'#ficha_personas');
		}
  	}
}

</script>
<h3 class=subtitulo><?php echo ucfirst(_("relación de actividades a las que asiste")); ?></h3>
<form id="seleccionados" name="seleccionados" action="" method="post">
<?= $oHash->getCamposHtml(); ?>
<input type='hidden' id='mod' name='mod' value=''>

<table><tr><td>
<input type='Radio' id='periodo' name='periodo' value=1 <?= $chk_1 ?> onclick=fnjs_actuales(this.form)><?= ucfirst(_("actuales")) ?>
<input type='Radio' id='periodo' name='periodo' value=2 <?= $chk_2 ?> onclick=fnjs_actuales(this.form)><?= ucfirst(_("todas las de este curso")) ?>
<input type='Radio' id='periodo' name='periodo' value=3 <?= $chk_3 ?> onclick=fnjs_actuales(this.form)><?= ucfirst(_("todos los cursos")) ?>
</td></tr></table><br>

<?php
$oTabla = new web\Lista();
$oTabla->setId_tabla('sql_1301');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);
echo $oTabla->mostrar_tabla();
?>
</form>
<?php
// --------------  boton insert ----------------------

if (!empty($ref_perm)) { // si es nulo, no tengo permisos de ningún tipo
	reset($ref_perm);
	echo "<br><table cellspacing=3  class=botones><tr class=botones><th width=25 align=RIGHT>"._("dl").":</th>";
	foreach ($ref_perm as $clave => $val) {
		$permis=$val["perm"];
		$nom=$val["nom"];
		if (!empty($permis)) {
			$mi_dele = core\ConfigGlobal::mi_dele();
			$pagina=web\Hash::link('apps/asistentes/controller/form_1301.php?'.http_build_query(array('mod'=>'nuevo','que_dl'=>$mi_dele,'pau'=>$pau,'id_tipo'=>$clave,'obj_pau'=>$obj_pau,'id_pau'=>$id_pau,'go_to'=>$go_to)));
			echo "<td class=botones><span class=link_inv onclick=\"fnjs_update_div('#ficha_personas','$pagina');\">$nom</span></td>";
		}
	}
	echo "</tr><tr class=botones><th  width=25 align=RIGHT>"._("otros").":</th>";
	reset ($ref_perm);
	foreach ($ref_perm as $clave => $val) {
		$permis=$val["perm"];
		$nom=$val["nom"];
		if (!empty($permis)) {
			$pagina=web\Hash::link('apps/asistentes/controller/form_1301.php?'.http_build_query(array('mod'=>'nuevo','pau'=>$pau,'id_tipo'=>$clave,'obj_pau'=>$obj_pau,'id_pau'=>$id_pau,'go_to'=>$go_to)));
			echo "<td class=botones><span class=link_inv onclick=\"fnjs_update_div('#ficha_personas','$pagina');\">$nom</span></td>";
		}
	}
	echo "</tr></table></form>";
}
 
