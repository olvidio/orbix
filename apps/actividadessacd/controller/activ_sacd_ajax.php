<?php
use actividadcargos\model\entity\ActividadCargo;
use actividadcargos\model\entity\GestorActividadCargo;
use actividadescentro\model\entity\GestorCentroEncargado;
use asistentes\model\entity\AsistenteDl;

use web\Periodo;
use actividades\model\entity\GestorActividadDl;
use personas\model\entity\GestorPersonaDl;
use actividades\model\entity\ActividadDl;
use personas\model\entity\PersonaDl;

/**
* Esta página sirve para ejecutar las operaciones de guardar, eliminar, listar...
* que se piden desde: activ_sacd.php
*
*@param string $que
*			'orden' -> cambia el orden del cargo a uno más o menos. También borra el cargo y la asistencia.
*			'get'   -> lista de los sacd encargados (por orden cargo) con onclick para cambiar o borrar.
*			'nuevo'	-> una lista con los sacd posibles. Primero el sacd del centro encargado con (*) y después el resto.
*			'asignar'-> asigna el sacd a la actividad con cargo uno más del que exista. También poen la asistencia a la
*						actividad si es de sv.
*			'lista_activ'-> para la primera presentación. Devuelve la lista de actividades con los sacd encargados.
*						Se puede pasar el parámetro $tipo para seleccionar un tipo de actividades.
*@param string $tipo na|sg|sr|sssc|sf_na|sf_sg|sf_sr
*
*
*@package	delegacion
*@subpackage	actividades
*@author	Daniel Serrabou
*@since		22/12/2010.
*		
*/

// INICIO Cabecera global de URL de controlador *********************************
require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

/**
* En teroia tendria que cambiar el orden de la lista de los centros encargados
* de la actividad. Si num_orden és '+' (más importante), hago descender el orden un valor, y reordeno el resto de cargos...
*/
function ordena($id_activ,$id_nom,$num_orden) {
    $aWhere = [];
    $aOperador = [];
	$aWhere['id_cargo']='35,39';
	$aOperador['id_cargo']= 'BETWEEN';
	$aWhere['id_activ']=$id_activ;
	$aWhere['_ordre']='id_cargo';
	$GesActividadCargos = new GestorActividadCargo();
	$cActividadCargos = $GesActividadCargos->getActividadCargos($aWhere,$aOperador);
	$i_max=count($cActividadCargos);
	for($i=0;$i<$i_max;$i++) {
		if ($cActividadCargos[$i]->getId_nom() == $id_nom) {
			switch ($num_orden) {
				case "mas":
					if ($i>=1) {
						$anterior_id_nom=$cActividadCargos[($i-1)]->getId_nom();
						if (!empty($anterior_id_nom)) {
							$cActividadCargos[($i-1)]->setId_nom($id_nom);
							if ($cActividadCargos[($i-1)]->DBGuardar() === false) {
								echo _('Hay un error, no se ha guardado');
							}
							$cActividadCargos[($i)]->setId_nom($anterior_id_nom);
							if ($cActividadCargos[($i)]->DBGuardar() === false) {
								echo _('Hay un error, no se ha guardado');
							}
						}
					}
					break;
				case "menos":
					if ($i<($i_max-1)) {
						$post_id_nom=$cActividadCargos[($i+1)]->getId_nom();
						if (!empty($post_id_nom)) {
							$cActividadCargos[($i+1)]->setId_nom($id_nom);
							if ($cActividadCargos[($i+1)]->DBGuardar() === false) {
								echo _('Hay un error, no se ha guardado');
							}
							$cActividadCargos[($i)]->setId_nom($post_id_nom);
							if ($cActividadCargos[($i)]->DBGuardar() === false) {
								echo _('Hay un error, no se ha guardado');
							}
						}
					}
					break;
			}
		}
	}
}

$Qque = (string) \filter_input(INPUT_POST, 'que');
$Qid_activ = (integer) \filter_input(INPUT_POST, 'id_activ');

$aWhere = [];
$aOperador = [];
switch ($Qque) {
	case "orden":
	    $Qnum_orden = (string) \filter_input(INPUT_POST, 'num_orden');
	    $Qid_cargo = (integer) \filter_input(INPUT_POST, 'id_cargo');
	    $Qid_nom = (integer) \filter_input(INPUT_POST, 'id_nom');
		$error_txt='';
		if ($Qnum_orden=="borrar") { //entonces es borrar:
			if ($Qid_activ && $Qid_cargo) {
				$oCargoActiv = new ActividadCargo(array('id_activ'=>$Qid_activ,'id_cargo'=>$Qid_cargo));
                // Para obligar a cargar el id_item y poder eliminar. (hemos creado el objeto: con id_activ,id_cargo)
				$oCargoActiv->DBCarregar();
				if ($oCargoActiv->DBEliminar() === false) {
					$error_txt=_("Hay un error, no se ha eliminado el cargo");
				}
				// también la asistencia
				$oAsisActiv = new AsistenteDl(array('id_activ'=>$Qid_activ,'id_nom'=>$Qid_nom));
				if ($oAsisActiv->DBEliminar() === false) {
					$error_txt= _("Hay un error, no se ha eliminado la asistencia");
				}
			} else {
				$error_txt=_("no sé cuál he de borar");
			}
		} else {
			ordena($Qid_activ,$Qid_nom,$Qnum_orden);
		}
		if (!empty($error_txt)) {
		  echo $error_txt;
		}
		break;
	case "get":
		// mirar permisos.
		$_SESSION['oPermActividades']->setActividad($Qid_activ);
		$oPermSacd = $_SESSION['oPermActividades']->getPermisoActual('sacd');
		$txt='';
		if ($oPermSacd->have_perm('ver') === true) { // sólo si tiene permiso
			// listado de sacd encargados
			$aWhere['id_cargo']='35,39';
			$aOperador['id_cargo']= 'BETWEEN';
			$aWhere['id_activ']=$Qid_activ;
			$aWhere['_ordre']='id_cargo';
			$GesActividadCargos = new GestorActividadCargo();
			$cActividadCargos = $GesActividadCargos->getActividadCargos($aWhere,$aOperador);
			$txt_sacd='';

			foreach($cActividadCargos as $oActividadCargo) {
				$id_nom=$oActividadCargo->getId_nom();
				$id_cargo=$oActividadCargo->getId_cargo();
				$oPersona = new PersonaDl($id_nom);
				$ap_nom=$oPersona->getApellidosNombre();
				$id_txt_nom=$Qid_activ."_".$id_nom;

				if ($oPermSacd->have_perm('modificar') === true) { // sólo si tiene permiso para modificar
					$txt_sacd.="<span class=link id=$id_txt_nom onclick=fnjs_cambiar_sacd(event,$Qid_activ,$id_cargo,$id_nom)> $ap_nom;</span>";
				} else { // permiso para ver (si no tiene permisos ya estamos aqui)
					$txt_sacd.="<span> $ap_nom</span>";
				}
			}
			$txt_id=$Qid_activ."_sacds";
			$txt="<td id=$txt_id>$txt_sacd</td>";
		}
		echo $txt;
		break;
	case "nuevo":
	    $Qseleccion = (string) \filter_input(INPUT_POST, 'seleccion');
		// una lista con los sacd posibles. Primero el sacd del centro encargado con (*) y después el resto.
		// ctr encargado:
		$oEnc=new GestorCentroEncargado();
		$sacd_posibles='';
		foreach($oEnc->getCentrosEncargados(array('id_activ'=>$Qid_activ,'_ordre'=>'num_orden')) as $oEncargado) {
			$id_ctr=$oEncargado->getId_ubi();
			$num_orden = $oEncargado->getNum_orden();
            if (core\configGlobal::is_app_installed('encargossacd')) {
                $GesEncargos = new GestorEncargo();
                // Tipos de encargo que son atención centro. No los rt.
                // 1000,1100,1200,1300
                $cEncargos = $GesEncargos->getEncargos(array('id_ubi'=>$id_ctr,'id_tipo_enc'=>'1[0123]00'),array('id_tipo_enc'=>'~'));
                if (is_array($cEncargos) && count($cEncargos) > 0) { // puede ser que no haya sacd encargado (dlb, dlbf).
                    // només n'hi hauria d'haver un.
                    $id_enc = $cEncargos[0]->getId_enc();
                    $GesTareasSacd = new GestorTareaSacd();
                    $aWhere=array('id_enc'=>$id_enc,'modo'=>'2|3','f_fin'=>'');
                    $aOperador=array('modo'=>'~','f_fin'=>'IS NULL');
                    $cTareasSacd = $GesTareasSacd->getTareasSacd($aWhere,$aOperador);
                    $id_nom = $cTareasSacd[0]->getId_nom();
                    $oPersona = new PersonaDl($id_nom);
                    $ap_nom=$oPersona->getApellidosNombre();
                    $sacd_posibles.="<tr><td><span class=link id=$id_nom onclick=fnjs_asignar_sacd('".$Qid_activ."','$id_nom') >$num_orden* $ap_nom</span></td></tr>";
                }
            }
		}
		// listado de todos los sacd.
		// selecciono según la variable selecion ('2'=> n y agd, '4'=> de paso, '8'=> sssc, '16'=>cp)
		$cond_personas='';
		if ($Qseleccion & 2) {
			if (!empty($cond_personas)) $cond_personas.= "|";
			$cond_personas.= "n|a";
		}
		if ($Qseleccion & 4) {
			if (!empty($cond_personas)) $cond_personas.= "|";
			$cond_personas.="p";
		}
		if ($Qseleccion & 8) {
			if (!empty($cond_personas)) $cond_personas.= "|";
			$cond_personas.="sss";
		}
		if ($Qseleccion & 16) {
			if (!empty($cond_personas)) $cond_personas.= "|";
			$cond_personas.="cp_sss";
		}
		$cond_personas='^('.$cond_personas.')';
		unset($aWhere);
		unset($aOperador);
		$aWhere['id_tabla'] = $cond_personas;
		$aOperador['id_tabla'] = '~';
		$aWhere['sacd'] = 't';
		$aWhere['situacion'] = 'A';
		$aWhere['_ordre'] = 'apellido1,apellido2,nom';
		$GesPersonas = new GestorPersonaDl();
		$cPersonas = $GesPersonas->getPersonas($aWhere,$aOperador);
		foreach ($cPersonas as $oPersona) {
			$id_nom=$oPersona->getId_nom();
			$ap_nom=$oPersona->getApellidosNombre();
			$sacd_posibles.="<tr><td><span class=link id=$id_nom onclick=fnjs_asignar_sacd('".$Qid_activ."','$id_nom')> $ap_nom</span></td></tr>";
		}
		$txt="<table><tr><td class=cabecera>"._("sacd")."</td></tr>$sacd_posibles</table>";
		echo $txt;
		break;
	case "asignar":
	    $Qid_nom = (integer) \filter_input(INPUT_POST, 'id_nom');
		// miro si hay sacds encargados
		$aWhere['id_activ']=$Qid_activ;
		$aWhere['id_cargo']=34;
		$aOperador['id_cargo']= '>';
		$aWhere['_ordre']='id_cargo DESC';
		$GesCargoActiv = new GestorActividadCargo();
		$cCargosActiv = $GesCargoActiv->getActividadCargos($aWhere,$aOperador);
		if (is_array($cCargosActiv) && count($cCargosActiv) >= 1) {
			$id_cargo=$cCargosActiv[0]->getId_cargo()+1;
		} else {
			$id_cargo=35;
		}
		$oCargoActiv = new ActividadCargo();
		$oCargoActiv->setId_activ($Qid_activ);
		$oCargoActiv->setId_nom($Qid_nom);
		$oCargoActiv->setId_cargo($id_cargo);
		if ($oCargoActiv->DBGuardar() === false) {
			echo _("Hay un error, no se ha guardado el cargo");
		}
		// pongo que asiste si no es de sf
		$oActividad = new ActividadDl($Qid_activ);
		$id_tipo_activ = (string)$oActividad->getId_tipo_activ();
		if ($id_tipo_activ{0} == 1) {
			$oAsisActiv = new AsistenteDl(array('id_activ'=>$Qid_activ,'id_nom'=>$Qid_nom,'propio'=>'f','falta'=>'f'));
			if ($oAsisActiv->DBGuardar() === false) {
				echo _("Hay un error, no se ha guardado la asistencia");
			}
		}
		break;
	case 'lista_activ':
		$Qtipo = (string) \filter_input(INPUT_POST, 'tipo');
		$Qyear = (integer) \filter_input(INPUT_POST, 'year');
	    $Qperiodo = (string) \filter_input(INPUT_POST, 'periodo');
	    $Qempiezamin = (string) \filter_input(INPUT_POST, 'empiezamin');
	    $Qempiezamax = (string) \filter_input(INPUT_POST, 'empiezamax');
	    $Qinicio = (string) \filter_input(INPUT_POST, 'inicio');
		$Qfin = (string) \filter_input(INPUT_POST, 'fin');

		$any=empty($Qyear)? date('Y')+1 : $Qyear;
		$mes=date("m");
		$Qempiezamin=empty($Qempiezamin)? "1/$mes/$any" : $Qempiezamin;
		$Qempiezamax=empty($Qempiezamax)? "1/$mes/$any+1" : $Qempiezamax;
		
		if (empty($Qperiodo) || $Qperiodo == 'otro') {
			$inicio = empty($Qinicio)? $Qempiezamin : $Qinicio;
			$fin = empty($Qfin)? $Qempiezamax : $Qfin;
		} else {
			$oPeriodo = new Periodo();
			$oPeriodo->setAny($any);
			$oPeriodo->setPeriodo($Qperiodo);
			$inicio = $oPeriodo->getF_ini_iso();
			$fin = $oPeriodo->getF_fin_iso();
		}

		$aWhere['f_ini']="'$inicio','$fin'";
		$aOperador['f_ini']='BETWEEN';

		$aWhere['status']=3;
		$aOperador['status']="<";

		switch ($Qtipo) {
			case "na":
				$aWhere['id_tipo_activ']='^1[13]';
				$aOperador['id_tipo_activ']='~';
				break;
			case "sg":
				$aWhere['id_tipo_activ']='^1[45]';
				$aOperador['id_tipo_activ']='~';
				break;
			case "sr":
				$aWhere['id_tipo_activ']='^1[7]';
				$aOperador['id_tipo_activ']='~';
				break;
			case "sssc":
				$aWhere['id_tipo_activ']='^1[6]';
				$aOperador['id_tipo_activ']='~';
				break;
			case "sf_na":
				$aWhere['id_tipo_activ']='^2[123]';
				$aOperador['id_tipo_activ']='~';
				break;
			case "sf_sg":
				$aWhere['id_tipo_activ']='^2[45]';
				$aOperador['id_tipo_activ']='~';
				break;
			case "sf_sr":
				$aWhere['id_tipo_activ']='^2[789]';
				$aOperador['id_tipo_activ']='~';
				break;
		}
		$aWhere['_ordre']='f_ini';

		$GesActividades = new GestorActividadDl();
		$cActividades = $GesActividades->getActividades($aWhere,$aOperador);

		$titulo=ucfirst(_("listado de actividades"));
			
		$a_cabeceras = [];
		$a_cabeceras[]=ucfirst(_("actividad"));
		$a_cabeceras[]=ucfirst(_("sacd encargados"));

		$i=0;
		$sin=0;
		$a_valores=array();
		foreach ($cActividades as $oActividad) {
			$i++;
			$id_activ = $oActividad->getId_activ();
			$id_tipo_activ = $oActividad->getId_tipo_activ();
			$dl_org = $oActividad->getDl_org();
			$nom_activ = $oActividad->getNom_activ();
			$f_ini = $oActividad->getF_ini()->getFromLocal();
			$f_fin = $oActividad->getF_fin()->getFromLocal();
			// mirar permisos.
			if(core\ConfigGlobal::is_app_installed('procesos')) {
			    $_SESSION['oPermActividades']->setActividad($id_activ,$id_tipo_activ,$dl_org);
			    $oPermActiv = $_SESSION['oPermActividades']->getPermisoActual('datos');
			    $oPermCtr = $_SESSION['oPermActividades']->getPermisoActual('ctr');
				$oPermSacd = $_SESSION['oPermActividades']->getPermisoActual('sacd');
			}

			if ($oPermActiv->have_perm('ocupado') === false) { $sin++; continue; } // no tiene permisos ni para ver.
			if ($oPermActiv->have_perm('ver') === false) { // sólo puede ver que està ocupado
			} else {
				$a_valores[$i][0]=$id_activ;
				$a_valores[$i][10]=$oPermSacd; // para no tener que recalcularlo despues.
				$a_valores[$i][1]=$nom_activ;
				// permisos centro encargado.
				if ($oPermCtr->have_perm('ver') === true) {
					$oEnc=new GestorCentroEncargado();
					$ctrs="";
					foreach($oEnc->getCentrosEncargadosActividad($id_activ) as $oUbi) {;
						$ctrs.=$oUbi->getNombre_ubi().", ";
					}
					$ctrs=substr($ctrs,0,-2);
					if (!empty($ctrs)) {	
						$a_valores[$i][1]=$nom_activ." [$ctrs]";
					}
				}
				$sacds=array();
				if ($oPermSacd->have_perm('ver') === true) { // sólo si tiene permiso
					//echo "<br>dani: $nom_activ<br>";
					unset($aWhere);
					unset($aOperador);
					$aWhere['id_activ']=$id_activ;
					$aWhere['id_cargo']=34;
					$aOperador['id_cargo']= '>';
					$aWhere['_ordre']='id_cargo DESC';
					$GesCargoActiv = new GestorActividadCargo();
					$cCargosActividad = $GesCargoActiv->getActividadCargos($aWhere,$aOperador);
					foreach($cCargosActividad as $oActividadCargo) {;
						$id_nom=$oActividadCargo->getId_nom();
						$oPersona = new PersonaDl($id_nom);
						$sacds[] = array ('id_nom'=>$id_nom,
									'id_cargo'=>$oActividadCargo->getId_cargo(),
									'ap_nom'=>$oPersona->getApellidosNombre()
									);
					}
				}
				$a_valores[$i][2]=$sacds;
				$a_valores[$i][3]=$f_ini;
				$a_valores[$i][4]=$f_fin;
			}
		}
		?>
		
		<h3><?= $titulo ?></h3>
		<table onclick="fnjs_cambiar_sacd(event,'','','');"><tr>
		<?php
		foreach ($a_cabeceras as $cabecera) {
			echo "<td>$cabecera</td>";
		}
		?>
		<td id="lst_sacd"><td>
		</tr>
		<?php
		foreach ($a_valores as $valores) {
			//print_r($valores[10]);
			$oPermSacd=$valores[10];
			$id_activ=$valores[0];
			$f_ini=$valores[3];
			$f_fin=$valores[4];
			$txt_sacd="";
			if (is_array($valores[2])) {
				foreach ($valores[2] as $a_sacd){
					$id_nom=$a_sacd['id_nom'];
					$id_cargo=$a_sacd['id_cargo'];
					$id_txt_nom=$id_activ."_".$id_nom;
					if ($oPermSacd->have_perm('modificar') === true) { // sólo si tiene permiso para modificar
						$txt_sacd.="<span class=link id=$id_txt_nom onclick=fnjs_cambiar_sacd(event,'$id_activ','$id_cargo','$id_nom')> ${a_sacd['ap_nom']};</span>";
					} else { // permiso para ver (si no tiene permisos el valor($valores[2]) ya está en blanco)
						$txt_sacd.="<span> ${a_sacd['ap_nom']};</span>";
					}
				}
			}
			$txt_id=$id_activ."_sacds";
			if (($_SESSION['oPerm']->have_perm('des')) && ($oPermSacd->have_perm('crear') === true)) { // sólo si tiene permiso para crear
				$nuevo_txt="<span class=link onclick=fnjs_nuevo_sacd('$id_activ','$f_ini','$f_fin')>nuevo</span>";
			} else {
				$nuevo_txt='';
			}
			echo "<tr id=$id_activ ><td>$valores[1]</td><td id=$txt_id>$txt_sacd</td><td>$nuevo_txt</td></tr>";

		}
		?>
		</table>
		<?php
		break;
}