<?php
use actividades\model as actividades;
use actividadcargos\model as actividadcargos;
use asistentes\model as asistentes;
use dossiers\model as dossiers;
use personas\model as personas;
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

$que = empty($que)? '' : $que;
$mi_dele = core\ConfigGlobal::mi_dele();

//pongo aqui el $go_to porque al ir al mismo update que las actividaes, no se donde voler
$a_dataUrl = array('queSel'=>'asis','pau'=>$pau,'id_pau'=>$id_pau,'obj_pau'=>$_POST['obj_pau'],'id_dossier'=>$id_dossier);
$go_to=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/dossiers/controller/dossiers_ver.php?'.http_build_query($a_dataUrl));

$gesAsistentes = new asistentes\GestorAsistente();

// Permisos según el tipo de actividad
$oActividad = new actividades\Actividad($id_pau);
$id_tipo_activ = $oActividad->getId_tipo_activ();
$dl_org = $oActividad->getDl_org();
$plazas_totales = $oActividad->getPlazas();
if (empty($plazas_totales)) {
	$id_ubi = $oActividad->getId_ubi();
	$oCasa = ubis\model\Ubi::NewUbi($id_ubi);
	$plazas_max = $oCasa->getPlazas();
	$plazas_min = $oCasa->getPlazas_min();
	$plazas_txt = _("Plazas casa (max - min)").": ";
	$plazas_txt .= !empty($plazas_max)? $plazas_max : '?';
	$plazas_txt .= !empty($plazas_min)? ' - '.$plazas_min : '';
} else {
	$plazas_txt = _("Plazas actividad").": ";
	$plazas_txt .= !empty($plazas_totales)? $plazas_totales : '?';
}

$oPermDossier = new dossiers\PermDossier();
$a_ref_perm = $oPermDossier->perm_pers_activ($id_tipo_activ);

if (core\configGlobal::is_app_installed('asistentes')) {
	$a_botones[] = array( 'txt' => _('modificar asistencia'), 'click' =>"fnjs_modificar(\"#seleccionados\")" );
	$a_botones[] = array( 'txt' => _('cambiar actividad'), 'click' =>"fnjs_mover(\"#seleccionados\",$id_pau)" );
	$a_botones[] = array( 'txt' => _('borrar asistencia'), 'click' =>"fnjs_borrar(\"#seleccionados\")" );
	$a_botones[] = array( 'txt' => _("transferir a históricos"), 'click'=>"fnjs_transferir(this.form)");
}
if (core\configGlobal::is_app_installed('actividadcargos')) {
	$a_botones[] = array( 'txt' => _('añadir cargo'), 'click' =>"fnjs_add_cargo(\"#seleccionados\")" );
	$a_botones[] = array( 'txt' => _('modificar cargo'), 'click' =>"fnjs_mod_cargo(\"#seleccionados\")" );
	$a_botones[] = array( 'txt' => _('quitar cargo'), 'click' =>"fnjs_borrar_cargo(\"#seleccionados\")" );
}
if (core\configGlobal::is_app_installed('actividadestudios')) {
	$a_botones[] = array( 'txt' => _('plan estudios'), 'click' =>"fnjs_matriculas(\"#seleccionados\",\"#frm_matriculas\")" );
}

$a_cabeceras=array( array('name'=>_("num"),'width'=>40), array('name'=>_("nombre y apellidos"),'width'=>300),array('name'=>_("propio"),'width'=>40),array('name'=>_("est. ok"),'width'=>40),array('name'=>_("falta"),'width'=>40),array('name'=>_("observ."),'width'=>150) );


if (core\configGlobal::is_app_installed('actividadplazas')) {
	// array para pasar id_dl a dl.
	$gesDelegacion = new ubis\model\GestorDelegacion();
	$a_dl = $gesDelegacion->getArrayDelegaciones(array("H"));
	//print_r($a_dl);
	
	$gesActividadPlazas = new \actividadplazas\model\GestorActividadPlazas();
	$cActividadPlazas = $gesActividadPlazas->getActividadesPlazas(array('id_activ'=>$id_pau));
	$a_plazas_dist =array();
	foreach ($cActividadPlazas as $oActividadPlazas) {
		$dl_tabla = $oActividadPlazas->getDl_tabla();
		$id_dl = $oActividadPlazas->getId_dl();
		$dl = $a_dl[$id_dl];
		if ($dl_org == $dl_tabla) {
			$concedidas = $oActividadPlazas->getPlazas();
			$a_plazas_dist[$dl] = $concedidas;
		} else {
			$pedidas = $oActividadPlazas->getPlazas();
		}
	}
	$a_plazas =array(); 
}

// primero el cl:
$c=0;
$num=0;
$a_valores=array();
$aListaCargos=array();
// primero los cargos
if (core\configGlobal::is_app_installed('actividadcargos')) {
	$GesCargosEnActividad=new actividadcargos\GestorActividadCargo();
	$cCargosEnActividad = $GesCargosEnActividad->getActividadCargos(array('id_activ'=>$id_pau));
	$mi_sfsv = core\ConfigGlobal::mi_sfsv();
	foreach($cCargosEnActividad as $oActividadCargo) {
		$c++;
		$num++; // número total de asistentes.
		$id_nom=$oActividadCargo->getId_nom();
		$aListaCargos[]=$id_nom;
		$id_cargo=$oActividadCargo->getId_cargo();
		$oCargo = new actividadcargos\Cargo(array('id_cargo'=>$id_cargo));
		$tipo_cargo=$oCargo->getTipo_cargo();		
		// para los sacd en sf
		if ($tipo_cargo == 'sacd' && $mi_sfsv == 2) {
			continue;
		}

		$oPersona = personas\Persona::NewPersona($id_nom);
		$oCargo=new actividadcargos\Cargo($id_cargo);

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
		// me aseguro de que no sea un cargo vacio (sin id_nom)
		if (!empty($id_nom) && $cAsistente=$gesAsistentes->getAsistentes($aWhere,$aOperador)) {
			if(is_array($cAsistente) && count($cAsistente)>1) {
				$tabla = '';
				foreach ($cAsistente as $Asistente) {
					$tabla .= "<li>".$Asistente->getNomTabla()."</li>";
				}
				$msg_err = "ERROR: más de un asistente con el mismo id_nom<br>";
				$msg_err .= "<br>$nom(".$oPersona->getId_tabla().")<br><br>En las tablas:<ul>$tabla</ul>";
				exit ("$msg_err");
			}
			$propio=$cAsistente[0]->getPropio();
			$falta=$cAsistente[0]->getFalta();
			$est_ok=$cAsistente[0]->getEst_ok();
			$observ1=$cAsistente[0]->getObserv();
			$plaza= empty($cAsistente[0]->getPlaza())? 1 : $cAsistente[0]->getPlaza();

			// contar plazas
			if (core\configGlobal::is_app_installed('actividadplazas')) {
				//dl de la persona
				$dl = $oPersona->getDl();
				//si no es de la dl sólo cuento las asignadas
				if ($dl != $mi_dele){
					if ($plaza < 4) continue;
				}
				if (empty($a_plazas[$dl][$plaza])) {
					$a_plazas[$dl][$plaza] =  1;
				} else {
					$a_plazas[$dl][$plaza]++; 
				}

			}

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

		if(!empty($plaza)) {
			$a_valores[$c]['clase']='plaza'.$plaza;
		} else {
			$a_valores[$c]['clase']='plaza1';
		}
			
		$a_valores[$c][1]=$cargo;
		$a_valores[$c][2]="$nom  ($ctr_dl)";
		$a_valores[$c][6]="$observ $observ1";
	}
}
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
	$plaza= empty($oAsistente->getPlaza())? 1 : $oAsistente->getPlaza();
	
	// contar plazas
	if (core\configGlobal::is_app_installed('actividadplazas')) {
		//dl de la persona
		$dl = $oPersona->getDl();
		//si no es de la dl sólo cuento las asignadas
		if ($dl != $mi_dele){
			if ($plaza < 4) continue;
		}
		if (empty($a_plazas[$dl][$plaza])) {
			$a_plazas[$dl][$plaza] =  1;
		} else {
			$a_plazas[$dl][$plaza]++; 
		}

	}

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
		$a_val['sel']="$id_nom";
	} else {
		$a_val['sel']="";
	}
	
	if(!empty($plaza)) {
		$a_val['clase']='plaza'.$plaza;
	} else {
		$a_val['clase']='plaza1';
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
if (core\configGlobal::is_app_installed('actividadcargos')) {
	$c = count($a_valores);
}

//leyenda colores
$leyenda_html = '';
// resumen plazas
$resumen_plazas = '';
$disponibles ='';
if (core\configGlobal::is_app_installed('actividadplazas')) {
	//leyenda colores
	$leyenda_html ="<style>
		.box {
		display: inline;
		height: 1em;
		line-height: 3;
		}
		</style>
		<div class='box plaza1' onCLick=fnjs_cmb_plaza(\"#seleccionados\",'1') >"._("pedida")."</div>
		<div class='box plaza2' onCLick=fnjs_cmb_plaza(\"#seleccionados\",'2') >"._("en espera")."</div>
		<div class='box plaza3' onCLick=fnjs_cmb_plaza(\"#seleccionados\",'3') >"._("denegada")."</div>
		<div class='box plaza4' onCLick=fnjs_cmb_plaza(\"#seleccionados\",'4') >"._("asignada")."</div>
		<div class='box plaza5' onCLick=fnjs_cmb_plaza(\"#seleccionados\",'5') >"._("confirmada")."</div>
		";
	$resumen_plazas = $plazas_txt .'<br>';
	$resumen_plazas .= _("dl: plazas ocupadas / plazas disponibles") .'<br>';
	foreach ($a_plazas_dist as $dl => $disponibles) {
		if ($mi_dele == $dl || $mi_dele == $dl_org) {
			$a_plaz = empty($a_plazas[$dl])? array() : $a_plazas[$dl];
			$decidir ='';
			$espera ='';
			$ocupadas ='';
			$resumen_plazas .= "  $dl: " 	;
			foreach ($a_plaz as $plaza => $num) {
				if ($plaza == 1) { $decidir = $num; }
				if ($plaza == 2) { $espera = $num; }
				if ($plaza > 3) {
					$ocupadas += $num;
				}
			}
			$resumen_plazas .= 	"$ocupadas/$disponibles";
			// pongo los de otras dl, que todavia no estan asignados como genéricos:
			if ($mi_dele != $dl && $dl != $dl_org) {
				for ($i=$ocupadas+1; $i <= $disponibles ;$i++ ) {
					$nom = "$dl($i)";
					$a_val['sel'] = '';
					$a_val['clase'] = 'plaza4';
					$a_val[2] = $nom;
					$a_val[3] = ''; 
					$a_val[4] = ''; 
					$a_val[5] = ''; 
					$a_val[6] = ''; 
					
					$asistentes[$nom] = $a_val;
					
				}
			} else {
				if (!empty($espera)) { $resumen_plazas .= " ($espera en espera)"; }
				if (!empty($decidir)) { $resumen_plazas .= "[$decidir por decidir]"; }
			}
			$resumen_plazas .= ";";
		}
	}
}

$n = $c;
foreach ($asistentes as $nom => $val) {
	$c++;
	$val[1] = "-";
	// sólo numero los asignados y confirmados
	if (core\configGlobal::is_app_installed('actividadplazas')) {
		if ($val['clase'] == 'plaza4' || $val['clase'] == 'plaza5') {
			$n++;
			$val[1] = "$n.-";
		}
	} else {
		$n++;
		$val[1] = "$n.-";
	}
	$a_valores[$c] = $val;
}

$oHash = new web\Hash();
$oHash->setcamposForm('');
$oHash->setCamposNo('sel!scroll_id!mod!que');
$a_camposHidden = array(
		'pau' => $pau,
		'id_pau' => $id_pau,
		'id_dossier' => $id_dossier,
		'permiso' => 3,
		'go_to' => $go_to
		);
$oHash->setArraycamposHidden($a_camposHidden);

// para el hash de las matrículas. Hago otro formulario, pues cambio demasiadas cosas
$oHash1 = new web\Hash();
$oHash1->setcamposForm('');
$oHash1->setCamposNo('sel!scroll_id!mod');
$a_camposHidden = array(
		'que' => 'matriculas',
		'pau' => 'p',
		'id_pau' => $id_pau,
		'obj_pau' => 'Persona',
		'id_dossier' => 1303,
		'permiso' => 3,
		'go_to' => $go_to
		);
$oHash1->setArraycamposHidden($a_camposHidden);

$url = core\ConfigGlobal::getWeb()."/apps/dossiers/controller/dossiers_ver.php";
$oHash2 = new web\Hash();
$oHash2->setUrl($url);
$oHash2->setCamposForm('depende!pau!obj_pau!id_pau!id_dossier!permiso'); 
$h = $oHash2->linkSinVal();
$pagina = "depende=1&pau=a&obj_pau=Actividad&id_pau=$id_pau&id_dossier=3101&permiso=3$h";

$oHash3 = new web\Hash();
$oHash3->setUrl(core\ConfigGlobal::getWeb()."/apps/asistentes/controller/form_mover.php");
$oHash3->setCamposForm('id_pau!id_activ'); 
$h3 = $oHash3->linkSinVal();

$oHash4 = new web\Hash();
$oHash4->setUrl(core\ConfigGlobal::getWeb()."/apps/asistentes/controller/update_3101.php");
$oHash4->setCamposForm('mod!plaza!lista_json!id_activ'); 
$h4 = $oHash4->linkSinVal();


//$godossiers=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/dossiers/controller/dossiers_ver.php?'.http_build_query(array('pau'=>$pau,'id_pau'=>$id_pau,'obj_pau'=>$obj_pau,'go_atras'=>$_POST['go_atras'])));
//$godossiers=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/dossiers/controller/dossiers_ver.php?'.http_build_query(array('pau'=>$pau,'id_pau'=>$id_pau,'obj_pau'=>$obj_pau,'id_dossier'=>$id_dossier,'permiso'=>$perm_a,'depende'=>$depende_modificar)));
$godossiers=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/dossiers/controller/dossiers_ver.php?'.http_build_query(array('pau'=>$pau,'id_pau'=>$id_pau,'obj_pau'=>$obj_pau,'id_dossier'=>$id_dossier,'permiso'=>3)));
/* ---------------------------------- html --------------------------------------- */

echo $oPosicion->atras();
?>
<script>
<?php
if (core\configGlobal::is_app_installed('actividadplazas')) {
?>
fnjs_cmb_plaza=function(formulario,plaza){
	var form=$(formulario).attr('id');
	//var lista_json=$('#'+form+' input.sel:checked');
	var lista_json=JSON.stringify($('#'+form+' input.sel:checked').serializeArray());
	var url='apps/asistentes/controller/update_3101.php';
	var parametros='mod=plaza&plaza='+plaza+'&lista_json='+lista_json+'&id_activ=<?= $id_pau ?><?= $h4 ?>&PHPSESSID=<?php echo session_id(); ?>';
	/*
	fnjs_update_div('#div_modificar',url+'?'+parametros);
	fnjs_actualizar();
	*/
	$(formulario).submit(function() {
		$.ajax({
			data: parametros,
			url: url,
			type: 'post',
			complete: function (rta) {
				rta_txt=rta.responseText;
				if (rta_txt != '' && rta_txt != '\n') {
					alert (rta_txt);
				}
			},
			success: function () { fnjs_actualizar(); }
		});
		return false;
	});
	$(formulario).submit();
	$(formulario).off();

}

<?php	
}
?>
fnjs_actualizar=function(){
	fnjs_update_div('#main','<?= $godossiers ?>');
}
fnjs_guardar=function(formulario){
	var err=0;
	//$(formulario+' input[name="que"]').val(que);
	//$(formulario).attr('action','programas/casa_ajax.php');
	$(formulario).submit(function() {
		$.ajax({
			data: $(this).serialize(),
			url: $(this).attr('action'),
			type: 'post',
			complete: function (rta) {
				rta_txt=rta.responseText;
				if (rta_txt != '' && rta_txt != '\n') {
					alert (rta_txt);
				} else {
					$('#div_modificar').html('');
					$('#div_modificar').width('0');
					$('#div_modificar').height('0');
					$('#div_modificar').removeClass('ventana');
					$('#resto').removeClass('sombra');
				}
			},
			success: function () { fnjs_update_div('#main','<?= $url ?>'+'?'+'<?= $pagina ?>'); }
		});
		return false;
	});
	$(formulario).submit();
	$(formulario).off();
}
fnjs_mover=function(formulario,id_activ){
	$('#div_modificar').addClass('ventana');
	$('#div_modificar').width('700');
	$('#div_modificar').height('220');
	$('#resto').addClass('sombra');

	var form=$(formulario).attr('id');
	/* selecciono los elementos con class="sel" de las tablas del id=formulario */
	var sel=$('#'+form+' input.sel:checked');
	id_pau = sel.val();
	var url='apps/asistentes/controller/form_mover.php';
	var parametros='id_pau='+id_pau+'&id_activ='+id_activ+"<?= $h3 ?>";
	fnjs_update_div('#div_modificar',url+'?'+parametros);
}
fnjs_cerrar=function(){
	$('#div_modificar').html('');
	$('#div_modificar').width('0');
	$('#div_modificar').height('0');
	$('#div_modificar').removeClass('ventana');
	$('#resto').removeClass('sombra');
}

fnjs_matriculas=function(frm_sel,frm_enviar){
	rta=fnjs_solo_uno(frm_sel);
	if (rta==1) {
		var form=$(frm_sel).attr('id');
		/* selecciono los elementos con class="sel" de las tablas del id=formulario */
		var sel=$('#'+form+' input.sel:checked');
		var id = sel.val();
		$('#sel2').val(id);
  		$(frm_enviar).attr('action',"apps/dossiers/controller/dossiers_ver.php");
  		fnjs_enviar_formulario(frm_enviar,'#main');
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
  		$(formulario).attr('action',"apps/actividadcargos/controller/form_3102.php");
  		fnjs_enviar_formulario(formulario,'#ficha_activ');
  	}
}
fnjs_add_cargo=function(formulario){
	rta=fnjs_solo_uno(formulario);
	if (rta==1) {
		$('#mod').val("nuevo");
  		$(formulario).attr('action',"apps/actividadcargos/controller/form_3102.php");
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
			$(formulario).attr('action',"apps/actividadcargos/controller/update_3102.php");
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
			$(formulario).attr('action',"apps/dossiers/historics_insert.php?");
	  		fnjs_enviar_formulario(formulario,'#ficha_activ');
	}
}

</script>
<div id='div_modificar'></div>
<div id='resto'>

<h2 class=titulo><?php echo ucfirst(_("relación de asistentes")); ?></h2>
<form id="seleccionados" name="seleccionados" action="" method="post">
<?= $oHash->getCamposHtml(); ?>
<input type='hidden' id='mod' name='mod' value=''>
<?php
$oTabla = new web\Lista();
$oTabla->setId_tabla('sql_3101');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);
echo $oTabla->mostrar_tabla();
?>
</form>
<form id="frm_matriculas" name="frm_matriculas" action="" method="post">
<?= $oHash1->getCamposHtml(); ?>
<input type='hidden' id='mod' name='mod' value=''>
<input type='hidden' id='sel2' name='sel[]' value=''>
</form>
<?= $resumen_plazas ?>
<br>
<?= $leyenda_html ?>
<?php
// --------------  boton insert ----------------------
if ($permiso > 2) {
	reset ($a_ref_perm);
	echo "<div class='no_print'><br><table><tr class=botones><th align=RIGHT>"._("dl").":</th>";
	while (list ($clave, $val) = each ($a_ref_perm)) {
		$permis=$val["perm"];
		$obj_pau=$val["obj"];
		$nom=$val["nom"];
		if (!empty($permis)) {
			$pagina=web\Hash::link('apps/asistentes/controller/form_3101.php?'.http_build_query(array('que_dl'=>$mi_dele,'pau'=>$pau,'obj_pau'=>$obj_pau,'id_pau'=>$id_pau,'go_to'=>$go_to)));
			echo "<td class=botones><span class=link_inv onclick=\"fnjs_update_div('#ficha_activ','$pagina');\">".sprintf(_("añadir %s"),$nom)."</span></td>";
		}
	}
}
?>
</div>