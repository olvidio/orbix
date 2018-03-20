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

function incrementa (&$var){
	if (empty($var)) {
		$var = 1;
	} else {
		$var++;
	}
}

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
	// A veces por error se puede poner una actividad a un ctr...
	if (method_exists($oCasa,'getPlazas')) {
		$plazas_max = $oCasa->getPlazas();
		$plazas_min = $oCasa->getPlazas_min();
	} else {
		$plazas_max = '';
		$plazas_min = '';
	}
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
	$a_botones[] = array( 'txt' => _('E43'), 'click' =>"fnjs_e43(\"#seleccionados\",\"#frm_matriculas\")" );
}

$a_cabeceras=array( array('name'=>_("num"),'width'=>40), array('name'=>_("nombre y apellidos"),'width'=>300),array('name'=>_("propio"),'width'=>40),array('name'=>_("est. ok"),'width'=>40),array('name'=>_("falta"),'width'=>40),array('name'=>_("observ."),'width'=>150) );


if (core\configGlobal::is_app_installed('actividadplazas')) {
	// Si no esta publicada todas las plazas de la actividad son para la dl.
	// No hay plazas de calendario.
	if ( $oActividad->getPublicado() ===false) {
		$dl = $dl_org;
		$a_plazas_resumen[$dl]['calendario'] = $plazas_totales;
		$a_plazas_resumen[$dl]['conseguidas'] = 0;
		$a_plazas_resumen[$dl]['disponibles'] = $plazas_totales;
		$a_plazas_resumen[$dl]['total_cedidas'] = 0;
		$a_plazas_conseguidas =array();
	} else {
		// array para pasar id_dl a dl.
		$gesDelegacion = new ubis\model\GestorDelegacion();
		$a_dl = $gesDelegacion->getArrayDelegaciones(array("H"));
		//print_r($a_dl);
		
		$gesActividadPlazasR = new \actividadplazas\model\GestorResumenPlazas();
		$gesActividadPlazasR->setId_activ($id_pau);
		
		$gesActividadPlazas = new \actividadplazas\model\GestorActividadPlazas();
		$cActividadPlazas = $gesActividadPlazas->getActividadesPlazas(array('id_activ'=>$id_pau));
		$a_plazas_resumen =array();
		$a_plazas_conseguidas =array();
		foreach ($cActividadPlazas as $oActividadPlazas) {
			$dl_tabla = $oActividadPlazas->getDl_tabla();
			$id_dl = $oActividadPlazas->getId_dl();
			$json_cedidas = $oActividadPlazas->getCedidas();
			$dl = $a_dl[$id_dl];
			$calendario = $gesActividadPlazasR->getPlazasCalendario($dl);
			//if (empty($calendario)) { continue; }
			$a_plazas_resumen[$dl]['calendario'] = $gesActividadPlazasR->getPlazasCalendario($dl);
			$a_plazas_resumen[$dl]['conseguidas'] = $gesActividadPlazasR->getPlazasConseguidas($dl);
			$a_plazas_resumen[$dl]['disponibles'] = $gesActividadPlazasR->getPlazasDisponibles($dl);
			$a_plazas_resumen[$dl]['total_cedidas'] = $gesActividadPlazasR->getPlazasCedidas($dl);
			if ($dl_org == $dl_tabla) {
				// las cedidas se guardan en la tabla que pertenece a la dl
				if($dl === $dl_org) {
					if (!empty($json_cedidas)){
						//$aCedidas = json_decode($json_cedidas,TRUE);
						//$a_plazas_resumen[$dl]['cedidas'] = $aCedidas;
						$a_plazas_resumen[$dl]['json_cedidas'] = $json_cedidas;
					} else {
						//$a_plazas_resumen[$dl]['cedidas'] = array();
						$a_plazas_resumen[$dl]['json_cedidas'] = array();
					}
				}
			} else {
				if (!empty($json_cedidas)){
					//$aCedidas = json_decode($json_cedidas,TRUE);
					//$a_plazas_resumen[$dl]['cedidas'] = $aCedidas;
					$a_plazas_resumen[$dl]['json_cedidas'] = $json_cedidas;
				} else {
					//$a_plazas_resumen[$dl]['cedidas'] = array();
					$a_plazas_resumen[$dl]['json_cedidas'] = array();
				}
			}
			if (!empty($json_cedidas)){
				$aCedidas = json_decode($json_cedidas,TRUE);
				foreach ($aCedidas as $dl2 => $num) {
					$a_plazas_conseguidas[$dl2][$dl]['cedidas'] = $num;
				}
			}
		}
	}
}

// primero el cl:
$c=0;
$num=0;
$a_valores=array();
$aListaCargos=array();
$msg_err = '';
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
		if (!is_object($oPersona)) {
			$msg_err .= "<br>$oPersona con id_nom: $id_nom";
			continue;
		}
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
			$oAsistente = $cAsistente[0];
			$propio=$oAsistente->getPropio();
			$falta=$oAsistente->getFalta();
			$est_ok=$oAsistente->getEst_ok();
			$observ1=$oAsistente->getObserv();
			$plaza= empty($oAsistente->getPlaza())? asistentes\Asistente::PLAZA_PEDIDA : $oAsistente->getPlaza();

			// contar plazas
			if (core\configGlobal::is_app_installed('actividadplazas')) {
				// las cuento todas y a la hora de enseñar miro si soy la dl org o no.
				// propiedad de la plaza:
				$propietario = $oAsistente->getPropietario();
				$padre = strtok($propietario,'>');
				$child = strtok('>');
				$dl = $child;
				//si es de otra dl no distingo cedidas.
				// no muestro ni cuento las que esten en estado distinto al asignado o confirmado (>3)
				if ($padre != $mi_dele) {
					if ($plaza > 3) {
						incrementa($a_plazas_resumen[$padre]['ocupadas'][$dl][$plaza]);
						if (!empty($child) && $child != $padre) {
							incrementa($a_plazas_conseguidas[$child][$padre]['ocupadas'][$dl][$plaza]);
						}
					} else {
						if (!empty($child) && $child == $mi_dele) {
							incrementa($a_plazas_conseguidas[$child][$padre]['ocupadas'][$dl][$plaza]);
						}elseif (!empty($padre)) {
							continue;
						}
					}
				} else {  // En mi dl distingo las cedidas
					// si no es de (la dl o de paso ) y no tiene la plaza asignada o confirmada no lo muestro
					if ($child != $mi_dele) {
						if ($plaza < asistentes\Asistente::PLAZA_ASIGNADA) {
							continue;
						} else {
							incrementa($a_plazas_conseguidas[$child][$padre]['ocupadas'][$dl][$plaza]);
							incrementa($a_plazas_resumen[$padre]['ocupadas'][$dl][$plaza]);
						}
					} else {
						incrementa($a_plazas_resumen[$padre]['ocupadas'][$dl][$plaza]);
					}
				}
			}
			/*
			// contar plazas
			if (core\configGlobal::is_app_installed('actividadplazas')) {
				//dl de la persona
				$dl = $oPersona->getDl();
				//si no es de la dl sólo cuento las asignadas
				if ($dl != $mi_dele){
					if ($plaza < 4) continue;
				}
				incrementa($a_plazas[$dl][$plaza]);
			}
			 * 
			 */

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
$cAsistentes = $gesAsistentes->getAsistentes(array('id_activ'=>$id_pau));
foreach($cAsistentes as $oAsistente) {
	$c++;
	$num++;
	$id_nom=$oAsistente->getId_nom();
	// si ya está en la lista voy a por otro asistente
	if(in_array($id_nom,$aListaCargos)) { $num--; continue; }

	$oPersona = personas\Persona::NewPersona($id_nom);
	if (!is_object($oPersona)) {
		$msg_err .= "<br>$oPersona con id_nom: $id_nom";
		continue;
	}
	$nom=$oPersona->getApellidosNombre();
	//$dl=$oPersona->getDl();
	$ctr_dl=$oPersona->getCentro_o_dl();

	$propio=$oAsistente->getPropio();
	$falta=$oAsistente->getFalta();
	$est_ok=$oAsistente->getEst_ok();
	$observ=$oAsistente->getObserv();
	$plaza = asistentes\Asistente::PLAZA_PEDIDA;
	
	// contar plazas
	//if (core\configGlobal::is_app_installed('actividadplazas') && !empty($dl)) {
	if (core\configGlobal::is_app_installed('actividadplazas')) {
		$plaza= empty($oAsistente->getPlaza())? asistentes\Asistente::PLAZA_PEDIDA : $oAsistente->getPlaza();
		// las cuento todas y a la hora de enseñar miro si soy la dl org o no.
		// propiedad de la plaza:
		$propietario = $oAsistente->getPropietario();
		$padre = strtok($propietario,'>');
		$child = strtok('>');
		$dl = $child;
		//si es de otra dl no distingo cedidas.
		// no muestro ni cuento las que esten en estado distinto al asignado o confirmado (>3)
		if ($padre != $mi_dele) {
			if ($plaza > asistentes\Asistente::PLAZA_DENEGADA) {
				incrementa($a_plazas_resumen[$padre]['ocupadas'][$dl][$plaza]);
				if (!empty($child) && $child != $padre) {
					incrementa($a_plazas_conseguidas[$child][$padre]['ocupadas'][$dl][$plaza]);
				}
			} else {
				if (!empty($child) && $child == $mi_dele) {
					incrementa($a_plazas_conseguidas[$child][$padre]['ocupadas'][$dl][$plaza]);
				}elseif (!empty($padre)) {
					continue;
				}
			}
		} else {  // En mi dl distingo las cedidas
			// si no es de (la dl o de paso ) y no tiene la plaza asignada o confirmada no lo muestro
			if ($child != $mi_dele) {
				if ($plaza < asistentes\Asistente::PLAZA_ASIGNADA) {
					continue;
				} else {
					incrementa($a_plazas_conseguidas[$child][$padre]['ocupadas'][$dl][$plaza]);
					incrementa($a_plazas_resumen[$padre]['ocupadas'][$dl][$plaza]);
				}
			} else {
				incrementa($a_plazas_resumen[$padre]['ocupadas'][$dl][$plaza]);
			}
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
	
	$a_val['clase']='plaza1';
	if(!empty($plaza)) {
		$a_val['clase']='plaza'.$plaza;
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
$disponibles ='';
$resumen_plazas2 = '';
if (core\configGlobal::is_app_installed('actividadplazas')) {
	//leyenda colores
	$explicacion1 = _("plaza que contabiliza pero que las otras delegaciones no ven. Podría explicarse como una plaza que se desea pero no se puede conceder porque no hay sitio.");
	$explicacion2 = _("como la plaza pedida, pero cuando ya se ha solicitado a la otra delegación que nos conceda ese plaza. Implica que por nuestra parte nos parece correcto que vaya pero necesitamos confirmación de que hay sitio.");
	$explicacion4 = _("plaza ocupada en toda regla. Las delegaciones organizadoras ven a los nuestros. Si somos nosotros los organizadores, podemos ocupar más plazas de las previstas. Si son de otra delegación, no debería poder pasar a asignada si no hay plazas.");
	$explicacion5 = _("como la anterior pero con el plus de que se ha comunicado al interesado y no hay cambio.");
	
	$leyenda_html = '<p class="contenido">';
	$leyenda_html .= _("Para seleccionar varios: 'Ctrl+Click' o bien 'Mays+Click'");
	$leyenda_html .= "<br><style>
		.box {
		display: inline;
		height: 1em;
		line-height: 3;
		padding: 0.3em;
		border-style: outset;
		cursor: pointer;
		}
		</style>
		";
	$oGesAsistente = new asistentes\GestorAsistente();
	$aOpciones = $oGesAsistente->getOpcionesPosiblesPlaza();
	foreach ($aOpciones as $plaza => $plaza_txt) {
		$expl = "explicacion$plaza";
		$explicacion = $$expl;
		$leyenda_html .= "<div class='box plaza$plaza' onCLick=fnjs_cmb_plaza(\"#seleccionados\",'$plaza') title='$explicacion'>$plaza_txt</div>  ";
	}
	$leyenda_html .= "</p>";
	////////////////////////////////////////////////////////////////////
	if (array_key_exists($mi_dele, $a_plazas_resumen)) {
		$resumen_plazas = '';
		foreach ($a_plazas_resumen as $padre => $aa) {
			if ($padre != $mi_dele && $mi_dele != $dl_org) {	continue; }
			$calendario = empty($aa['calendario'])? 0 : $aa['calendario']; // calendario.
			$conseguidas = empty($aa['conseguidas'])? 0 : $aa['conseguidas']; // conseguidas.
			$total_cedidas = empty($aa['total_cedidas'])? 0 : $aa['total_cedidas'];
			$disponibles = empty($aa['disponibles'])? 0 : $aa['disponibles'];
			$json_cedidas = empty($aa['json_cedidas'])? '' : $aa['json_cedidas'];
			$total = $calendario + $conseguidas;
			$aCed = array();
			if (!empty($json_cedidas)){
				$aCed = json_decode($json_cedidas,TRUE);
			}
			$decidir = 0;
			$espera = 0;
			$ocupadas = 0;
			$resumen_plazas .= "$padre: " 	;
			// ocupadas por la dl padre
			$plazas = empty($aa['ocupadas'][$padre])? array() : $aa['ocupadas'][$padre];
			$ocupadas_dl = 0;
			foreach ($plazas as $plaza => $num) {
				if ($plaza == asistentes\Asistente::PLAZA_PEDIDA) { $decidir = $num; }
				if ($plaza == asistentes\Asistente::PLAZA_EN_ESPERA) { $espera = $num; }
				if ($plaza > asistentes\Asistente::PLAZA_DENEGADA) { $ocupadas_dl += $num; }
			}
			$ocu_padre = $ocupadas_dl;
			$ocupadas += $ocupadas_dl;
			$resumen_plazas .= 	"$ocupadas_dl($padre)";

			// ocupadas por las dl cedidas
			$i = 0;
			foreach ($aCed as $dl2 => $numCedidas) {
				$plazas = empty($aa['ocupadas'][$dl2])? array() : $aa['ocupadas'][$dl2];
				$i++;
				$ocupadas_dl = 0;
				foreach ($plazas as $plaza => $num) {
					if ($plaza == asistentes\Asistente::PLAZA_PEDIDA) { $decidir = $num; }
					if ($plaza == asistentes\Asistente::PLAZA_EN_ESPERA) { $espera = $num; }
					if ($plaza > asistentes\Asistente::PLAZA_DENEGADA) { $ocupadas_dl += $num; }
				}
				$ocupadas += $ocupadas_dl;
				$resumen_plazas .= " + ";
				$resumen_plazas .= 	"$ocupadas_dl($dl2)";
				$a_plazas_resumen[$padre]['cedidas'][$dl2] = array('ocupadas' => $ocupadas_dl);
				// pongo los de otras dl, que todavia no estan asignados como genéricos:
				if ($mi_dele != $dl2 && $dl2 != $dl_org) {
					$pl = empty($aCed[$dl2])? 0 : $aCed[$dl2];
					if (!array_key_exists($dl2, $a_plazas_resumen)) {
						for ($i=$ocupadas_dl+1; $i <= $pl ;$i++ ) {
							$nom = "$dl2----$i";
							$a_val['sel'] = '';
							$a_val['clase'] = 'plaza4';
							$a_val[2] = $nom;
							$a_val[3] = ''; 
							$a_val[4] = ''; 
							$a_val[5] = ''; 
							$a_val[6] = ''; 
							
							$asistentes[$nom] = $a_val;
						}
						//$pl_relleno[$dl2] = $i-1;
					}
					$pl_relleno[$dl2] = $pl-$ocupadas_dl;
				}
			}
			// Conseguidas	
			if (array_key_exists($padre, $a_plazas_conseguidas)) {
				$a_dl_plazas = $a_plazas_conseguidas[$padre];
				//$decidir = 0;
				//$espera = 0;
				$ocupadas_otra = 0;
				// ocupadas por la dl padre
				foreach ($a_dl_plazas as $dl3 => $pla) {
					$plazas = empty($pla['ocupadas'])? array() : $pla['ocupadas'];
					$pla['cedidas'] = empty($pla['cedidas'])? '?' : $pla['cedidas'];
					foreach ($plazas as $dl => $pl) {
						foreach ($pl as $plaza => $num) {
							if ($plaza == asistentes\Asistente::PLAZA_PEDIDA) { $decidir += $num; }
							if ($plaza == asistentes\Asistente::PLAZA_EN_ESPERA) { $espera += $num; }
							if ($plaza > asistentes\Asistente::PLAZA_DENEGADA) { $ocupadas_otra += $num; }
						}
						if (!empty($ocupadas_otra)) { $resumen_plazas .= " + "; }
						$txt = sprintf(_("(de las %s cedidas por %s)"),$pla['cedidas'],$dl3);
						$resumen_plazas .= $ocupadas_otra." ".$txt;
					}
				}
				$ocupadas += $ocupadas_otra;
				$ocu_padre += $ocupadas_otra;
			}

			$resumen_plazas .= 	"  => "._("ocupadas")."=$ocupadas/($total)";
			if (!empty($json_cedidas)) { $resumen_plazas .= " "._("cedidas")."=$total_cedidas $json_cedidas"; }
			$libres = $disponibles - $ocu_padre;
			if (($libres < 0)) {
				$resumen_plazas .= 	"<span style='background-color: red'> disponibles= $libres</span>";
			} else {
				$resumen_plazas .= 	" disponibles=$libres";
			}
			if ($mi_dele == $padre) {
				if (!empty($espera)) { $resumen_plazas .= " ".sprintf(_("(%s en espera)"),$espera); }
				if (!empty($decidir)) { $resumen_plazas .= " ".sprintf(_("(%s por decidir)"),$decidir); }
			}
			$resumen_plazas .= ";<br>";
			// pongo los de otras dl, que todavia no estan asignados como genéricos:
			if ($mi_dele != $padre && $padre != $dl_org) {
				$ocu_relleno = $total - $libres;
				for ($i=$ocu_relleno+1; $i <= $total ;$i++ ) {
					$nom = "$padre-$i";
					$a_val['sel'] = '';
					$a_val['clase'] = 'plaza4';
					$a_val[2] = $nom;
					$a_val[3] = ''; 
					$a_val[4] = ''; 
					$a_val[5] = ''; 
					$a_val[6] = ''; 
					
					$asistentes[$nom] = $a_val;
				}
			}
		}
	} elseif (array_key_exists($mi_dele, $a_plazas_conseguidas)) {  // No es una dl organizadora/colaboradora
		$a_dl_plazas = $a_plazas_conseguidas[$mi_dele];
		$decidir = 0;
		$espera = 0;
		$ocupadas_dl = 0;
		// ocupadas por la dl padre
		$resumen_plazas2 = "$mi_dele: ";
		foreach ($a_dl_plazas as $dl2 => $pla) {
			$plazas = empty($pla['ocupadas'])? array() : $pla['ocupadas'];
			$pla['cedidas'] = empty($pla['cedidas'])? '?' : $pla['cedidas'];
			foreach ($plazas as $dl => $pl) {
				foreach ($pl as $plaza => $num) {
					if ($plaza == asistentes\Asistente::PLAZA_PEDIDA) { $decidir += $num; }
					if ($plaza == asistentes\Asistente::PLAZA_EN_ESPERA) { $espera += $num; }
					if ($plaza > asistentes\Asistente::PLAZA_DENEGADA) { $ocupadas_dl += $num; }
				}
				$txt = sprintf(_("(de las %s cedidas por %s)"),$pla['cedidas'],$dl2);
				$resumen_plazas2 .= $ocupadas_dl." ".$txt;
				if (!empty($espera)) { $resumen_plazas2 .= " ".sprintf(_("(%s en espera)"),$espera); }
				if (!empty($decidir)) { $resumen_plazas2 .= " ".sprintf(_("(%s por decidir)"),$decidir); }
			}
		}
		$resumen_plazas2 .= ";<br>";
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

if (!empty($msg_err)) { echo $msg_err; }

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
	rta=fnjs_solo_uno(formulario);
	if (rta==1) {
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
}
fnjs_cerrar=function(){
	$('#div_modificar').html('');
	$('#div_modificar').width('0');
	$('#div_modificar').height('0');
	$('#div_modificar').removeClass('ventana');
	$('#resto').removeClass('sombra');
}

fnjs_e43=function(frm_sel,frm_enviar){
	rta=fnjs_solo_uno(frm_sel);
	if (rta==1) {
		var form=$(frm_sel).attr('id');
		/* selecciono los elementos con class="sel" de las tablas del id=formulario */
		var sel=$('#'+form+' input.sel:checked');
		var id = sel.val();
		$('#sel2').val(id);
  		$(frm_enviar).attr('action',"apps/actividadestudios/controller/e43.php");
  		fnjs_enviar_formulario(frm_enviar,'#main');
  	}
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
<?= $plazas_txt ?>
<br><br>
<?= $resumen_plazas ?>
<br>
<?= $resumen_plazas2 ?>
<br>
<?= $leyenda_html ?>
<?php
// --------------  boton insert ----------------------
if ($permiso > 2) {
	reset ($a_ref_perm);
	echo "<div class='no_print'><br><table><tr class=botones><th align=RIGHT>"._("dl").":</th>";
	foreach ($a_ref_perm as $clave => $val) {
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
