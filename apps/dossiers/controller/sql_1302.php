<?php
/**
 * Esta página muestra una tabla con los cargos en actividades de una persona.
 * Al principio tiene un filtro para ver parte de las actividades: actuales,curso,todas
 *  Con los botones de modificar y quitar cargo, y borrar asistencia.
 * abajo se añaden los botones para añadir una nueva actividad-cargo.
 *
 *
 * @package	delegacion
 * @subpackage	actividades
 * @author	Daniel Serrabou
 * @since		15/5/02.
 * @ajax		23/8/2007.
 * @version 1.0
 * @created 23/09/2010
 *
 * @param integer $id_pau En este caso corresponde al id_nom
 * @param integer $_POST['filtro']  valores: 2:actuales,7:este curso,10:todas
 * @param string $_POST['tabla_pau'] se pasa a otras páginas.
 *		
 */

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************
	require_once ("classes/personas/personas.class");
//	require_once ("classes/personas/xd_orden_cargo.class");
	require_once ("classes/actividades/ext_a_actividades_gestor.class");
	require_once ("classes/activ-personas/d_cargos_activ_gestor.class");
	require_once ('classes/web/tipo_actividad.class');
	require_once ('classes/web/listas.class');

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

include_once("./func_dossiers.php");

//pongo aqui el $go_to porque al ir al mismo update que las actividaes, no se donde voler
$go_to=core\ConfigGlobal::getWeb()."/programas/dossiers/dossiers_ver.php?pau=$pau&id_pau=$id_pau&tabla_pau=${_POST['tabla_pau']}&id_dossier=$id_dossier";

/* Pongo en la variable $curso el periodo del curso */
$mes=date('m');
if ($mes>9) { $any=date('Y')+1; } else { $any=date('Y'); }
$inicurs_ca=curso_est("inicio",$any);
$fincurs_ca=curso_est("fin",$any);

$curso="f_ini BETWEEN '$inicurs_ca' AND '$fincurs_ca' ";

if(empty($_POST['filtro_2'])) { $_POST['filtro_2']=0; }
switch ($_POST['filtro_2']) {
	case 7 :
		$chk_1="";
		$chk_2="checked";
		$chk_3="";
		$condicion=$curso;
		break;
	case 10:
		$chk_1="";
		$chk_2="";
		$chk_3="checked";
		$condicion="";
		break;
	case 2:
	default :
		$chk_1="checked";
		$chk_2="";
		$chk_3="";
		$condicion="status=2 AND ".$curso;
		break;
}

$oPersona=new Persona($id_pau);
// permisos Según el tipo de persona: n, agd, s
$id_tabla=$oPersona->getId_tabla();
$ref_perm = perm_activ_pers($id_tabla,1);

$a_botones=array(
				array( 'txt' => _('modificar cargo'), 'click' =>"fnjs_modificar_c(this.form)" ) ,
				array( 'txt' => _('borrar cargo'), 'click' =>"fnjs_borrar_c(this.form,1)" ) ,
				array( 'txt' => _('borrar asistencia'), 'click' =>"fnjs_borrar_c(this.form,2)" ) 
	);

$a_cabeceras=array( array('name'=>_("fechas"),'width'=>150),array('name'=>_("nombre"),'width'=>300),_("cargo"),_("observ.")  );
$a_valores=array();
$i=0;
$oCargosActividades=new GestorActividadCargo();
foreach ($oCargosActividades->getActividadCargoPersona($id_pau,$condicion) as $oActividadCargo) {
	$i++;
	$id_cargo=$oActividadCargo->getId_cargo();
	$id_activ=$oActividadCargo->getId_activ();
	$observ=$oActividadCargo->getObserv();

	$oCargo=new Cargo($id_cargo);
	$cargo=$oCargo->getCargo();

	$oActividad=new Actividad($id_activ);
	$nom_activ=$oActividad->getNom_activ();
	$id_tipo_activ=$oActividad->getId_tipo_activ();
	$dl_org=$oActividad->getDl_org();
	$f_ini=$oActividad->getF_ini();
	$f_fin=$oActividad->getF_fin();

	// para ver el nombre en caso de la sf.
	$oTipoActividad = new TiposActividades($id_tipo_activ);
	$ssfsv=$oTipoActividad->getSfsvText();
	$sactividad=$oTipoActividad->getActividadText();

	if ($ssfsv == "sf" && !($_SESSION['oPerm']->have_perm("des")) ) {
	    $nom_activ="$ssfsv $sactividad";
	}

	// para modificar.
	$id_tipo=substr($id_tipo_activ,0,3)."..."; //cojo los 3 primeros dígitos y "..."
	// para la sf, todos los tipos
	if(substr($id_tipo_activ,0,1)==2) $id_tipo="2.....";
	$act=$ref_perm[$id_tipo];
	if ($act["perm"]) { $permiso=3; } else { $permiso=1; }
	
	if ($permiso==3) {
		$a_valores[$i]['sel']="$id_activ#$id_cargo";
	} else {
		$a_valores[$i]['sel']="";
	}
	$a_valores[$i][1]="$f_ini-$f_fin";
	$a_valores[$i][2]=$nom_activ;
	$a_valores[$i][3]=$cargo;
	$a_valores[$i][4]=$observ;

}
?>
<script>
fnjs_filtro_2=function(formulario){
  $(formulario).attr('action',"programas/dossiers/dossiers_ver.php");
  fnjs_enviar_formulario(formulario);
}
fnjs_modificar_c=function(formulario){
	rta=fnjs_solo_uno(formulario);
	if (rta==1) {
		$('#mod_2').val("editar");
  		$(formulario).attr('action',"programas/dossiers/form_1302.php");
  		fnjs_enviar_formulario(formulario,'#ficha_personas');
  	}
}

fnjs_borrar_c=function(formulario,elim){
	var mensaje;
	rta=fnjs_solo_uno(formulario);
	if (rta==1) {
		if (elim==1) { mensaje="<?php echo _("¿Esta Seguro que desea quitar este cargo a esta persona?");?>"; }
		if (elim==2) { mensaje="<?php echo _("¿Esta Seguro que desea borrar a esta persona de esta actividad?");?>"; }
		if (confirm(mensaje) ) {
	  		$('#mod_2').val("eliminar");
	  		$('#elim_asis').val(elim);
			go=$('#go_to_2').val();
			$(formulario).attr('action',"programas/dossiers/update_3102.php");
			$(formulario).submit(function() {
				$.ajax({
					data: $(this).serialize(),
					url: $(this).attr('action'),
					type: 'post',
					complete: function (rta_txt) {
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
<h3 class=subtitulo><?php echo ucfirst(_("relación de cargos en actividades")); ?></h3>
<form id="seleccionados_2" name="seleccionados_2" action="" method="post">
<input type='hidden' id='mod_2' name='mod' value=''>
<input type='hidden' id='elim_asis' name='elim_asis' value=''>
<input type='hidden' id='pau_2' name='pau' value='<?= $pau ?>'>
<input type='hidden' id='id_pau_2' name='id_pau' value='<?= $id_pau ?>'>
<input type='hidden' id='tabla_pau_2' name='tabla_pau' value='<?= $_POST['tabla_pau'] ?>'>
<input type='hidden' id='id_dossier_2' name='id_dossier' value="1301y1302">
<input type='hidden' id='permiso_2' name='permiso' value='3'>
<input type='hidden' id='db_2' name='db' value='<?= $db_dl ?>'>
<input type='hidden' id='go_to_2' name='go_to' value='<?= $go_to ?>'>

<table><tr><td>
<input type='Radio' id='filtro_2' name='filtro_2' value=2 <?= $chk_1 ?> onclick=fnjs_filtro_2(this.form)><?= ucfirst(_("actuales")) ?>
<input type='Radio' id='filtro_2' name='filtro_2' value=7 <?= $chk_2 ?> onclick=fnjs_filtro_2(this.form)><?= ucfirst(_("todas las de este curso")) ?>
<input type='Radio' id='filtro_2' name='filtro_2' value=10 <?= $chk_3 ?> onclick=fnjs_filtro_2(this.form)><?= ucfirst(_("todos los cursos")) ?>
</td></tr></table><br>

<?php
$oTabla = new web\Lista();
$oTabla->setId_tabla('sql_1302');
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

if (!empty($ref_perm)) { // si es nulo, no tengo permisos de ningún tipo
	reset ($ref_perm);
	echo "<br><table cellspacing=3  class=botones><tr class=botones><th align=RIGHT>"._("dl").":</th>";
	while (list ($clave, $val) = each ($ref_perm)) {
		$permi=$val["perm"];
		$nom=$val["nom"];
		if (!empty($permi)) {
			$pagina="programas/dossiers/form_1302.php?mod=nuevo&que_dl=".core\ConfigGlobal::mi_dele()."&pau=$pau&db=".core\ConfigGlobal::$db_dl."&id_tipo=$clave&id_pau=$id_pau&go_to=$go_to";
			echo "<td class=botones><span class=link_inv onclick=\"fnjs_update_div('#ficha_personas','$pagina');\">$nom</span></td>";
		}
	}
	echo "</tr><tr><th align=RIGHT>"._("otros").":</th>";
	reset ($ref_perm);
	while (list ($clave, $val) = each ($ref_perm)) {
		$permi=$val["perm"];
		$nom=$val["nom"];
		if (!empty($permi)) {
			$pagina="programas/dossiers/form_1302.php?mod=nuevo&pau=$pau&db=".core\ConfigGlobal::$db_dl."&id_tipo=$clave&id_pau=$id_pau&go_to=$go_to";
			echo "<td class=botones><span class=link_inv onclick=\"fnjs_update_div('#ficha_personas','$pagina');\">$nom</span></td>";
		}
	}
	echo "</tr></table></form>";
}
 
