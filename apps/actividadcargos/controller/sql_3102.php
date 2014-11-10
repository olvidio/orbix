<?php
/**
 * Esta página muestra una tabla con los cargos de una actividad.
 *  Con los botones de modificar y quitar cargo.
 *  En el caso de ser "des" o "vcsd" al quitar cargo, también elimino la asistencia.
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
	require_once ("global_header.inc");
// Arxivos requeridos por esta url **********************************************
	require_once ("classes/personas/personas.class");
	require_once ("classes/personas/xd_orden_cargo.class");
	require_once ("classes/actividades/ext_a_actividades_gestor.class");
	require_once ("classes/activ-personas/d_cargos_activ_gestor.class");
	require_once ('classes/web/listas.class');

// Crea los objectos de uso global **********************************************
	require_once ("global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

include_once(ConfigGlobal::$dir_programas.'/dossiers/func_dossiers.php');


//pongo aqui el $go_to porque al ir al mismo update que las actividaes, no se donde voler
$go_to=ConfigGlobal::$web."/programas/dossiers/dossiers_ver.php?pau=$pau&id_pau=$id_pau&tabla_pau=${_POST['tabla_pau']}&id_dossier=$id_dossier";
	
$oCargosEnActividad=new GestorActividadCargo();

// Permisos según el tipo de actividad
$oActividad=new Actividad($id_pau);
$a_ref_perm = perm_pers_activ($oActividad->getId_tipo_activ());

$a_botones=array(
				array( 'txt' => _('modificar cargo'), 'click' =>"fnjs_mod_cargo(this.form)" ) ,
				array( 'txt' => _('quitar cargo'), 'click' =>"fnjs_borrar_cargo(this.form)" ) 
	);

$a_cabeceras=array( _("cargo"),	array('name'=>_("nombre y apellidos"),'width'=>300),_("puede ser agd?"),_("observaciones.")  );
$c=0;
$a_valores=array();
foreach($oCargosEnActividad->getActividadCargos($id_pau) as $oActividadCargo) {
	$c++;
	$id_nom=$oActividadCargo->getId_nom();
	$id_cargo=$oActividadCargo->getId_cargo();
	$oPersona=new Persona($id_nom);
	$oCargo=new Cargo($id_cargo);

	$nom=$oPersona->getApellidosNombre();

	$cargo=$oCargo->getCargo();
	$puede_agd=$oActividadCargo->getPuede_agd();
	$observ=$oActividadCargo->getObserv();
	$ctr_dl=$oPersona->getCentro_o_dl();
	// permisos (añado caso de cargo sin nombre = todos permiso)
	if ($id_tabla=$oPersona->getId_tabla()) {
		$a_act=$a_ref_perm[$id_tabla];
		if ($a_act["perm"]) { $permiso=3; } else { $permiso=1; }
	} else {
		$permiso=3;
	}
	$puede_agd=='t' ? $chk_puede_agd="si" : $chk_puede_agd="no" ;

	// Para los de des, elimino el cargo y la asistencia. Para el resto, sólo el cargo (no la asistencia).
	if (($GLOBALS['oPerm']->have_perm("des")) or ($GLOBALS['oPerm']->have_perm("vcsd"))) { $eliminar=2; } else { $eliminar=1; }
	if ($permiso==3) {
		$a_valores[$c]['sel']="$id_nom#$id_cargo#$eliminar";
	} else {
		$a_valores[$c]['sel']="";
	}

	$a_valores[$c][1]=$cargo;
	$a_valores[$c][2]="$nom  ($ctr_dl)";
	$a_valores[$c][3]=$chk_puede_agd;
	$a_valores[$c][4]=$observ;
}
?>
<script>
fnjs_mod_cargo=function(formulario){
	rta=fnjs_solo_uno(formulario);
	if (rta==1) {
		$('#mod').val("editar");
  		$(formulario).attr('action',"programas/dossiers/form_3102.php");
  		fnjs_enviar_formulario(formulario,'#ficha_activ');
  	}
}
fnjs_borrar_cargo=function(formulario){
	rta=fnjs_solo_uno(formulario);
	if (rta==1) {
		<?php
		if (($GLOBALS['oPerm']->have_perm("des")) or ($GLOBALS['oPerm']->have_perm("vcsd"))) { 
			$txt= _("Esto también borrará a esta persona de la lista de asistentes?");
		}
		?>
		if (confirm("<?php echo _("¿Está seguro que desea quitar este cargo a esta persona?");?><?= $txt ?>") ) {
			$('#mod').val("eliminar");
			go=$('#go_to').val();
			$(formulario).attr('action',"programas/dossiers/update_3102.php");
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
<input type='hidden' id='mod' name='mod' value=''>
<input type="hidden" id="pau" name="pau" value="<?= $pau ?>">
<input type="hidden" id="id_pau" name="id_pau" value="<?= $id_pau ?>">
<input type="hidden" id="tabla_pau" name="tabla_pau" value="<?= $_POST['tabla_pau'] ?>">
<input type="hidden" id="id_dossier" name="id_dossier" value="<?= $id_dossier ?>">
<input type="hidden" id="permiso" name="permiso" value="3">
<input type="hidden" id="go_to" name="go_to" value="<?= $go_to ?>">
<?php
$oTabla = new Lista();
$oTabla->setId_tabla('sql_3102');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);
echo $oTabla->mostrar_tabla();
?>
</form>
<?php
// --------------  boton insert ----------------------
$go_to=urlencode($go_to);

reset ($a_ref_perm);
echo "<div class='no_print'><br><table class=botones><tr class=botones><th align=RIGHT>"._("dl").":</th>";
while (list ($clave, $val) = each ($a_ref_perm)) {
	$perm=$val["perm"];
	$tabla_p=$val["tabla"];
	$nom=$val["nom"];
   	if (!empty($perm)) {
		$pagina="programas/dossiers/form_3102.php?mod=nuevo&dele=".ConfigGlobal::$dele."&pau=$pau&db=".ConfigGlobal::$db_dl."&tabla_p=$tabla_p&id_pau=$id_pau&go_to=$go_to";
		echo "<td class=botones><span class=link_inv onclick=\"fnjs_update_div('#ficha_activ','$pagina');\" >".sprintf(_("añadir %s"),$nom)."</span></td>";
	}
}
?>
