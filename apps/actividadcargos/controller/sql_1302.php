<?php
use actividades\model as actividades;
use actividadcargos\model as actividadcargos;
use dossiers\model as dossiers;
use personas\model as personas;
/**
 * Esta página muestra una tabla con los cargos en actividades de una persona.
 *  Con los botones de modificar y quitar cargo.
 * abajo se añaden los botones para añadir una nueva persona-cargo.
 *
 * OJO Está como include de dossiers_ver.php
 *
 * @package	delegacion
 * @subpackage	actividades
 * @author	Daniel Serrabou
 * @since		15/5/02.
 * @ajax		23/8/2007.
 * @version 1.0
 * @created 23/09/2010
 *		
 * @param integer $_POST['tabla_pau']  Se pasa a otras páginas.
 * @
 */

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

	
//pongo aqui el $go_to porque al ir al mismo update que las actividaes, no se donde voler
$a_dataUrl = array('permiso'=>$permiso,'pau'=>$pau,'id_pau'=>$id_pau,'obj_pau'=>$_POST['obj_pau'],'id_dossier'=>$id_dossier);

$go_to=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/dossiers/controller/dossiers_ver.php?'.http_build_query($a_dataUrl));
			
$mi_sfsv = core\ConfigGlobal::mi_sfsv();
/* Pongo en la variable $curso el periodo del curso */
$mes=date('m');
if ($mes>9) { $any=date('Y')+1; } else { $any=date("Y"); }
$inicurs_ca=core\curso_est("inicio",$any);
$fincurs_ca=core\curso_est("fin",$any);

//$curso="f_ini BETWEEN '$inicurs_ca' AND '$fincurs_ca' ";

//$aWhere['id_nom'] = $id_pau;
$aWhere = array();
$aOperator = array();
$aWhere['_ordre'] = 'f_ini';

if(empty($_POST['status'])) { $_POST['status']=0; }
switch ($_POST['status']) {
	case "7" :
		$chk_1="";
		$chk_2="checked";
		$chk_3="";
		//$condicion=$curso;
		$aWhere['f_ini'] =  "'$inicurs_ca','$fincurs_ca'";
		$aOperator['f_ini'] = 'BETWEEN';
		break;
	case 10:
		$chk_1="";
		$chk_2="";
		$chk_3="checked";
		//$condicion="";
		break;
	case 2:
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


$oPersona=personas\Persona::NewPersona($id_pau);
if (!is_object($oPersona)) {
	$msg_err = "<br>$oPersona con id_nom: $id_pau";
	exit ($msg_err);
}
$nom=$oPersona->getApellidosNombre();
$ctr_dl=$oPersona->getCentro_o_dl();

// permisos Según el tipo de persona: n, agd, s
$id_tabla=$oPersona->getId_tabla();
$oPermDossier = new dossiers\PermDossier();
$ref_perm = $oPermDossier->perm_activ_pers($id_tabla);

// Para los de des, elimino el cargo y la asistencia. Para el resto, sólo el cargo (no la asistencia).
if (($_SESSION['oPerm']->have_perm("des")) or ($_SESSION['oPerm']->have_perm("vcsd"))) { $eliminar=2; } else { $eliminar=1; }

$oCargosEnActividad=new actividadcargos\GestorActividadCargo();

$a_botones=array(
				array( 'txt' => _('modificar cargo'), 'click' =>"fnjs_mod_cargo(this.form)" ) ,
				array( 'txt' => _('quitar cargo'), 'click' =>"fnjs_borrar_cargo(this.form)" ) 
	);

$a_cabeceras=array( _("cargo"),	array('name'=>_("actividad"),'width'=>300),_("puede ser agd?"),_("observaciones.")  );
$c=0;
$a_valores=array();
$mi_sfsv = core\ConfigGlobal::mi_sfsv();
$cCargosEnActividad = $oCargosEnActividad->getActividadCargosDeAsistente(array('id_nom'=>$id_pau),$aWhere,$aOperator);
foreach($cCargosEnActividad as $oActividadCargo) {
	$c++;
	$id_activ=$oActividadCargo->getId_activ();
	$id_cargo=$oActividadCargo->getId_cargo();
	$oCargo = new actividadcargos\Cargo(array('id_cargo'=>$id_cargo));
	$tipo_cargo=$oCargo->getTipo_cargo();		
	// para los sacd en sf
	if ($tipo_cargo == 'sacd' && $mi_sfsv == 2) {
		continue;
	}

	$oActividad=new actividades\Actividad($id_activ);
	$nom_activ = $oActividad->getNom_activ();
	$id_tipo_activ = $oActividad->getId_tipo_activ();

	$oCargo=new actividadcargos\Cargo($id_cargo);
	$cargo=$oCargo->getCargo();
	$puede_agd=$oActividadCargo->getPuede_agd();
	$observ=$oActividadCargo->getObserv();

	$puede_agd=='t' ? $chk_puede_agd="si" : $chk_puede_agd="no" ;

	// para modificar.
	$id_tipo=substr($id_tipo_activ,0,3); //cojo los 3 primeros dígitos
	$act=!empty($ref_perm[$id_tipo])? $ref_perm[$id_tipo] : '';

	if (!empty($act["perm"])) { $permiso=3; } else { $permiso=1; }
	

	if ($permiso==3) {
		$a_valores[$c]['sel']="$id_activ#$id_cargo#$eliminar";
	} else {
		$a_valores[$c]['sel']="";
	}

	$a_valores[$c][1]=$cargo;
	$a_valores[$c][2]="$nom_activ";
	$a_valores[$c][3]=$chk_puede_agd;
	$a_valores[$c][4]=$observ;
}

$oHash = new web\Hash();
$oHash->setcamposForm('status');
$oHash->setCamposNo('sel!mod!scroll_id');
$a_camposHidden = array(
		'pau' => $pau,
		'id_pau' => $id_pau,
		'obj_pau' => $_POST['obj_pau'],
		'id_dossier' => $id_dossier,
		'permiso' => 3,
		'go_to' => $go_to
		);
		//'tabla_pau' => $_POST['tabla_pau'],
$oHash->setArraycamposHidden($a_camposHidden);
?>
<script>
fnjs_actuales=function(formulario){
  $(formulario).attr('action',"apps/dossiers/controller/dossiers_ver.php");
  fnjs_enviar_formulario(formulario);
}
fnjs_mod_cargo=function(formulario){
	rta=fnjs_solo_uno(formulario);
	if (rta==1) {
		$('#mod').val("editar");
  		$(formulario).attr('action',"apps/actividadcargos/controller/form_1302.php");
  		fnjs_enviar_formulario(formulario,'#ficha_personas');
  	}
}
fnjs_borrar_cargo=function(formulario){
	rta=fnjs_solo_uno(formulario);
	if (rta==1) {
		<?php
		$txt = '';
		if (($_SESSION['oPerm']->have_perm("des")) or ($_SESSION['oPerm']->have_perm("vcsd"))) { 
			$txt= _("Esto también borrará a esta persona de la lista de asistentes?");
		}
		?>
		if (confirm("<?php echo _("¿Está seguro que desea quitar este cargo a esta persona?");?><?= $txt ?>") ) {
			$('#mod').val("eliminar");
			go=$('#go_to').val();
			$(formulario).attr('action',"apps/actividadcargos/controller/update_3102.php");
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
<h2 class=titulo><?php echo ucfirst(_("relación de cargos")); ?></h2>
<form id="seleccionados" name="seleccionados" action="" method="post">
<?= $oHash->getCamposHtml(); ?>
<input type='hidden' id='mod' name='mod' value=''>

<table><tr><td>
<input type='Radio' id='status' name='status' value=2 <?= $chk_1 ?> onclick=fnjs_actuales(this.form)><?= ucfirst(_("actuales")) ?>
<input type='Radio' id='status' name='status' value=7 <?= $chk_2 ?> onclick=fnjs_actuales(this.form)><?= ucfirst(_("todas las de este curso")) ?>
<input type='Radio' id='status' name='status' value=10 <?= $chk_3 ?> onclick=fnjs_actuales(this.form)><?= ucfirst(_("todos los cursos")) ?>
</td></tr></table><br>

<?php
$oTabla = new web\Lista();
$oTabla->setId_tabla('sql_3102');
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
	while (list ($clave, $val) = each ($ref_perm)) {
		$permis=$val["perm"];
		$nom=$val["nom"];
		if (!empty($permis)) {
			$mi_dele = core\ConfigGlobal::mi_dele();
			$pagina=web\Hash::link('apps/actividadcargos/controller/form_1302.php?'.http_build_query(array('mod'=>'nuevo','que_dl'=>$mi_dele,'pau'=>$pau,'id_tipo'=>$clave,'obj_pau'=>$obj_pau,'id_pau'=>$id_pau,'go_to'=>$go_to)));
			echo "<td class=botones><span class=link_inv onclick=\"fnjs_update_div('#ficha_personas','$pagina');\">$nom</span></td>";
		}
	}
	echo "</tr><tr class=botones><th  width=25 align=RIGHT>"._("otros").":</th>";
	reset ($ref_perm);
	while (list ($clave, $val) = each ($ref_perm)) {
		$permis=$val["perm"];
		$nom=$val["nom"];
		if (!empty($permis)) {
			$pagina=web\Hash::link('apps/actividadcargos/controller/form_1302.php?'.http_build_query(array('mod'=>'nuevo','pau'=>$pau,'id_tipo'=>$clave,'obj_pau'=>$obj_pau,'id_pau'=>$id_pau,'go_to'=>$go_to)));
			echo "<td class=botones><span class=link_inv onclick=\"fnjs_update_div('#ficha_personas','$pagina');\">$nom</span></td>";
		}
	}
	echo "</tr></table></form>";
}

