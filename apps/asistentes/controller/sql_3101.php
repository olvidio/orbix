<?php
use actividades\model as actividades;
use personas\model as personas;
use asistentes\model as asistentes;
/**
 * Esta página muestra una tabla con los asistentes de una actividad.
 * Primero los miembros del cl y después el resto.
 *  Con los botones de:
 *			modificar y borrar asistencia.
 *			añadir, modificar y quitar cargo.
 *			plan de estudios
 *			transferir a históricos.
 *  En el caso de ser "des" o "vcsd" al quitar cargo, también elimino la asistencia.
 * abajo se añaden los botones para añadir una nueva persona.
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
 * @param integer $_POST['obj_pau']  Se pasa a otras páginas.
 */

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

	
//include_once(core\ConfigGlobal::$dir_programas.'/dossiers/func_dossiers.php');
$que = empty($que)? '' : $que;

//pongo aqui el $go_to porque al ir al mismo update que las actividaes, no se donde voler
//$go_to=core\ConfigGlobal::getWeb()."/programas/dossiers/dossiers_ver.php?pau=$pau&id_pau=$id_pau&obj_pau=${_POST['obj_pau']}&id_dossier=$id_dossier";
$go_to='';

$gesAsistentes = new asistentes\GestorAsistente();
//$oCargosEnActividad=new actividades\GestorActividadCargo();

// Permisos según el tipo de actividad
$oActividad=new actividades\Actividad($id_pau);
$a_ref_perm = dossiers\controller\perm_pers_activ($oActividad->getId_tipo_activ());

if (core\configGlobal::is_app_installed('asistentes')) {
	$a_botones[] = array( 'txt' => _('modificar asistencia'), 'click' =>"fnjs_modificar(\"#seleccionados\")" );
	$a_botones[] = array( 'txt' => _('borrar asistencia'), 'click' =>"fnjs_borrar(\"#seleccionados\")" );
	$a_botones[] = array( 'txt' => _("transferir a históricos"), 'click'=>"fnjs_transferir(this.form)");
}
if (core\configGlobal::is_app_installed('actividadcargos')) {
	$a_botones[] = array( 'txt' => _('añadir cargo'), 'click' =>"fnjs_add_cargo(\"#seleccionados\")" );
	$a_botones[] = array( 'txt' => _('modificar cargo'), 'click' =>"fnjs_mod_cargo(\"#seleccionados\")" );
	$a_botones[] = array( 'txt' => _('quitar cargo'), 'click' =>"fnjs_borrar_cargo(\"#seleccionados\")" );
}
if (core\configGlobal::is_app_installed('actividadestudios')) {
	$a_botones[] = array( 'txt' => _('plan estudios'), 'click' =>"fnjs_matriculas(\"#seleccionados\")" );
}

$a_cabeceras=array( array('name'=>_("num"),'width'=>40), array('name'=>_("nombre y apellidos"),'width'=>300),array('name'=>_("propio"),'width'=>40),array('name'=>_("est. ok"),'width'=>40),array('name'=>_("falta"),'width'=>40),array('name'=>_("observ."),'width'=>150) );
// primero el cl:
$c=0;
$num=0;
$a_valores=array();
$aListaCargos=array();
/*
foreach($oCargosEnActividad->getActividadCargos($id_pau) as $oActividadCargo) {
	$c++;
	$num++; // número total de asistentes.
	$id_nom=$oActividadCargo->getId_nom();
	$aListaCargos[]=$id_nom;
	$id_cargo=$oActividadCargo->getId_cargo();
	$oPersona=new personas\Persona($id_nom);
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
	if (($_SESSION['oPerm']->have_perm("des")) or ($_SESSION['oPerm']->have_perm("vcsd"))) { $eliminar=2; } else { $eliminar=1; }
	if ($permiso==3) {
		$a_valores[$c]['sel']="$id_nom#$id_cargo#$eliminar";
	} else {
		$a_valores[$c]['sel']="";
	}

	// ahora miro si también asiste:
	$aWhere=array('id_activ'=>$id_pau,'id_nom'=>$id_nom);
	$aOperador=array('id_activ'=>'=','id_nom'=>'=');
	// me aseguro de que no sea un cargo vacío (sin id_nom)
	if (!empty($id_nom) && $cAsistente=$oAsistentesEnActividad->getAsistentes($aWhere,$aOperador)) {
		if(is_array($cAsistente) && count($cAsistente)>1) exit ("ERROR: más de un asistente con el mismo id_nom<br>");
		$propio=$cAsistente[0]->getPropio();
		$falta=$cAsistente[0]->getFalta();
		$est_ok=$cAsistente[0]->getEst_ok();
		$observ1=$cAsistente[0]->getObserv();

		if ($propio=='t') {
			$chk_propio=_("si");
			$eliminar=1;
		} else { 
			$chk_propio=_("no") ;
			$eliminar=2;  //si no es propio, al eliminar el cargo, elimino la asistencia
		}
		$falta=='t' ? $chk_falta=_("si") : $chk_falta=_("no") ;
		$est_ok=='t' ? $chk_est_ok=_("si") : $chk_est_ok=_("no") ;
		$asis="t";
		$a_valores[$c][3]=$chk_propio;
		$a_valores[$c][4]=$chk_est_ok;
		$a_valores[$c][5]=$chk_falta;
	} else {
		$a_valores[$c][3]= array( 'span'=>3, 'valor'=> _("no asiste"));
		$observ1='';
		$num--;
		$asis="f";
	}

	$a_valores[$c][1]=$cargo;
	$a_valores[$c][2]="$nom  ($ctr_dl)";
	$a_valores[$c][6]="$observ $observ1";
}
*/
// ahora los asistentes sin los cargos
$asistentes = array();
foreach($gesAsistentes->getAsistentes(array('id_activ'=>$id_pau)) as $oAsistente) {
	$c++;
	$num++;
	$id_nom=$oAsistente->getId_nom();
	// si ya está en la lista voy a por otro asistente
	if(in_array($id_nom,$aListaCargos)) { $num--; continue; }

	$oPersona = personas\Persona::NewPersona($id_nom);
	$nom=$oPersona->getApellidosNombre();
	$ctr_dl=$oPersona->getCentro_o_dl();

	$propio=$oAsistente->getPropio();
	$falta=$oAsistente->getFalta();
	$est_ok=$oAsistente->getEst_ok();
	$observ=$oAsistente->getObserv();

	if ($propio=='t') {
		$chk_propio=_("si");
		$eliminar=1;
	} else { 
		$chk_propio=_("no") ;
		$eliminar=2;  //si no es propio, al eliminar el cargo, elimino la asistencia
	}
	$falta=='t' ? $chk_falta=_("si") : $chk_falta=_("no") ;
	$est_ok=='t' ? $chk_est_ok=_("si") : $chk_est_ok=_("no") ;
	if ($permiso==3) {
		//$a_valores[$c]['sel']="$id_nom";
		$a_val['sel']="$id_nom";
	} else {
		//$a_valores[$c]['sel']="";
		$a_val['sel']="";
	}
			
	$a_val[2]="$nom  ($ctr_dl)";
	$a_val[3]=$chk_propio;
	$a_val[4]=$chk_est_ok;
	$a_val[5]=$chk_falta;
	$a_val[6]=$observ;
	$asistentes[$nom] = $a_val;
}
uksort($asistentes,"core\strsinacentocmp");
$c = 0;
foreach ($asistentes as $nom => $val) {
	$c++;
	$val[1] = "$c.-";
	$a_valores[$c] = $val;
}



$oHash = new web\Hash();
$oHash->setcamposForm('');
$oHash->setCamposNo('sel!mod!que');
$a_camposHidden = array(
		'pau' => $pau,
		'id_pau' => $id_pau,
		'id_dossier' => $id_dossier,
		'permiso' => 3,
		'go_to' => $go_to
		);
		//'obj_pau' => $_POST['obj_pau'],
$oHash->setArraycamposHidden($a_camposHidden);

/* ---------------------------------- html --------------------------------------- */
?>
<script>
fnjs_matriculas=function(formulario){
	rta=fnjs_solo_uno(formulario);
	if (rta==1) {
		$('#que').val("matriculas");
		$('#pau').val("p");
		$('#obj_pau').val("personas");
		$('#id_dossier').val("1303");
  		$(formulario).attr('action',"apps/dossiers/controller/dossiers_ver.php");
  		fnjs_enviar_formulario(formulario,'#ficha_activ');
  	}
}
fnjs_modificar=function(formulario){
	rta=fnjs_solo_uno(formulario);
	if (rta==1) {
		$('#mod').val("editar");
  		$(formulario).attr('action',"apps/asistentes/controller/form_3101.php");
  		fnjs_enviar_formulario(formulario,'#ficha_activ');
  	}
}

fnjs_mod_cargo=function(formulario){
	rta=fnjs_solo_uno(formulario);
	if (rta==1) {
		$('#mod').val("editar");
  		$(formulario).attr('action',"programas/dossiers/form_3102.php");
  		fnjs_enviar_formulario(formulario,'#ficha_activ');
  	}
}
fnjs_add_cargo=function(formulario){
	rta=fnjs_solo_uno(formulario);
	if (rta==1) {
		$('#mod').val("nuevo");
  		$(formulario).attr('action',"programas/dossiers/form_3102.php");
  		fnjs_enviar_formulario(formulario,'#ficha_activ');
  	}
}
fnjs_borrar=function(formulario){
	rta=fnjs_solo_uno(formulario);
	if (rta==1) {
		if (confirm("<?php echo _("¿Esta Seguro que desea borrar a esta persona de esta actividad?");?>") ) {
			$('#mod').val("eliminar");
			go=$('#go_to').val();
			$(formulario).attr('action',"apps/asistentes/controller/update_3101.php");
	  		//fnjs_enviar_formulario(formulario,'#ficha_activ');
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
fnjs_borrar_cargo=function(formulario){
	var asis="test";
	rta=fnjs_solo_uno(formulario);
	if (rta==1) {
		if (confirm("<?php echo _("¿Esta Seguro que desea quitar este cargo a esta persona?");?>") ) {
			$('#mod').val("eliminar");
			go=$('#go_to').val();
			$(formulario).attr('action',"programas/dossiers/update_3102.php");
	  		//fnjs_enviar_formulario(formulario,'#ficha_activ');
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
fnjs_transferir=function(formulario){
	if (confirm("<?php echo _("¿Esta Seguro que desea transferir todas las personas seleccionadas a históricos?");?>") ) {
			$(formulario).attr('action',"programas/dossiers/historics_insert.php?");
	  		fnjs_enviar_formulario(formulario,'#ficha_activ');
	}
}

</script>
<h2 class=titulo><?php echo ucfirst(_("relación de asistentes")); ?></h2>
<form id="seleccionados" name="seleccionados" action="" method="post">
<?= $oHash->getCamposHtml(); ?>
<input type='hidden' id='mod' name='mod' value=''>
<input type='hidden' id='que' name='que' value='<?= $que ?>'>
<?php
$oTabla = new web\Lista();
$oTabla->setId_tabla('sql_3101');
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
echo "<div class='no_print'><br><table><tr class=botones><th align=RIGHT>"._("dl").":</th>";
while (list ($clave, $val) = each ($a_ref_perm)) {
	$permis=$val["perm"];
	$tabla_p=$val["tabla"];
	$nom=$val["nom"];
   	if (!empty($permis)) {
		$pagina=web\Hash::link("apps/asistentes/controller/form_3101.php?que_dl=".core\ConfigGlobal::mi_dele()."&pau=$pau&tabla_p=$tabla_p&id_pau=$id_pau&go_to=$go_to");
		echo "<td class=botones><span class=link_inv onclick=\"fnjs_update_div('#ficha_activ','$pagina');\">".sprintf(_("añadir %s"),$nom)."</span></td>";
	}
}
?>
