<?php
use actividadescentro\model\entity\CentroEncargado;
use actividadescentro\model\entity\GestorCentroEncargado;
use ubis\model\entity\GestorCentroDl;
use ubis\model\entity\GestorCentroEllas;
use web\Periodo;
use actividades\model\entity\GestorActividadDl;

// INICIO Cabecera global de URL de controlador *********************************
require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

/**
* En teoria tendria que cambiar el orden de la lista de los centros encargados
* de la actividad. Si num_orden és '+' (más importante), hago descender el orden un valor, y reordeno el resto de centros...
*/
function ordena($id_activ,$id_ubi,$num_orden) {
	$GesCentroEncargado = new GestorCentroEncargado();
	$cCentrosEncargados = $GesCentroEncargado->getCentrosEncargados(array('id_activ'=>$id_activ,'_ordre'=>'num_orden')); 
	$i_max=count($cCentrosEncargados);
	for($i=0;$i<$i_max;$i++) {
		if ($cCentrosEncargados[$i]->getId_ubi() == $id_ubi) {
			switch ($num_orden) {
				case "mas":
					if ($i>=1) {
						$anterior_id_ubi=$cCentrosEncargados[($i-1)]->getId_ubi();
						if (!empty($anterior_id_ubi)) {
							$cCentrosEncargados[($i-1)]->setId_ubi($id_ubi);
							if ($cCentrosEncargados[($i-1)]->DBGuardar() === false) {
								echo _("hay un error, no se ha guardado");
							}
							$cCentrosEncargados[($i)]->setId_ubi($anterior_id_ubi);
							if ($cCentrosEncargados[($i)]->DBGuardar() === false) {
								echo _("hay un error, no se ha guardado");
							}
						}
					}
					break;
				case "menos":
					if ($i<($i_max-1)) {
						$post_id_ubi=$cCentrosEncargados[($i+1)]->getId_ubi();
						if (!empty($post_id_ubi)) {
							$cCentrosEncargados[($i+1)]->setId_ubi($id_ubi);
							if ($cCentrosEncargados[($i+1)]->DBGuardar() === false) {
								echo _("hay un error, no se ha guardado");
							}
							$cCentrosEncargados[($i)]->setId_ubi($post_id_ubi);
							if ($cCentrosEncargados[($i)]->DBGuardar() === false) {
								echo _("hay un error, no se ha guardado");
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
        $Qid_ubi = (integer) \filter_input(INPUT_POST, 'id_ubi');
		$error_txt = '';
		if ($Qnum_orden=="borrar") { //entonces es borrar:
			if ($Qid_activ && $Qid_ubi) {
				$oCentroEncargado = new CentroEncargado(array('id_activ'=>$Qid_activ,'id_ubi'=>$Qid_ubi));
				if ($oCentroEncargado->DBEliminar() === false) {
					$error_txt=_("hay un error, no se ha eliminado el centro");
				}
			} else {
				$error_txt=_("no sé cuál he de borar");
			}
		} else {
			$error_txt = ordena($Qid_activ,$Qid_ubi,$Qnum_orden);
		}
		$error_txt=addslashes($error_txt);
		echo "{ \"que\": \"$Qque}\", \"txt\": \"\", \"error\": \"$error_txt\" }";
		break;
	case "get":
		// mirar permisos.
		$Qid_tipo_activ = (integer) \filter_input(INPUT_POST, 'id_tipo_activ');
		$Qdl_org = (string) \filter_input(INPUT_POST, 'dl_org');
		$_SESSION['oPermActividades']->setActividad($Qid_activ,$Qid_tipo_activ,$Qdl_org);
		$oPermCtr = $_SESSION['oPermActividades']->getPermisoActual('ctr');

		$txt='';
		if ($oPermCtr->have_perm('ver') === true) { // sólo si tiene permiso
			// listado de centros encargados
			$GesCtrEncargados = new GestorCentroEncargado();
			$cCtrsEncargados = $GesCtrEncargados->getCentrosEncargadosActividad($Qid_activ);	
			$txt_ctr='';

			foreach($cCtrsEncargados as $oCentro) {
				$id_ubi = $oCentro->getId_ubi();
				$nombre_ubi = $oCentro->getNombre_ubi();
				$id_txt_ubi=$Qid_activ."_".$id_ubi;
				
				if ($oPermCtr->have_perm('modificar') === true) { // sólo si tiene permiso para modificar
					$txt_ctr.="<span class=link id=$id_txt_ubi onclick=fnjs_cambiar_ctr(event,'$Qid_activ','$id_ubi')> $nombre_ubi;</span>";
				} else { // permiso para ver (si no tiene permisos ya estamos aqui)
					$txt_ctr.="<span> $nombre_ubi</span>";
				}
			}
			$txt_id=$Qid_activ."_ctrs";
			$txt="<td id=$txt_id>$txt_ctr</td>";
		}
		echo $txt;
		break;
	case "nuevo_sg":
		$Qinicio = (string) \filter_input(INPUT_POST, 'inicio');
		$Qfin = (string) \filter_input(INPUT_POST, 'fin');
		$Qf_ini_act = (string) \filter_input(INPUT_POST, 'f_ini_act');
		$aWhere['status'] = 't';
		$aWhere['tipo_ctr'] = '^s[jm]';
		$aOperador['tipo_ctr'] = '~';
		$GesCentros = new GestorCentroDl();
		$cCentros = $GesCentros->getCentros($aWhere,$aOperador);
		$periodo="f_ini BETWEEN '".$Qinicio."' AND '".$Qfin."'";
		$txt_ctr='';
		foreach ($cCentros as $oCentro) {
			$id_ubi = $oCentro->getId_ubi();
			$nombre_ubi = $oCentro->getNombre_ubi();
			// número de actividades en periodo
			$sql="SELECT f_ini,f_fin FROM a_actividades a LEFT JOIN d_encargados_activ e  USING (id_activ) 
					WHERE e.id_ubi=$id_ubi AND $periodo
					ORDER BY f_ini";
			//echo "sql: $sql<br>";
			$num_activ=$oDBA->query($sql)->rowCount();
			
			//próxima actividad
			$sql_dif="SELECT nom_activ,f_ini,f_fin,(f_ini - date '".$Qf_ini_act."') as dif
				FROM a_actividades a JOIN d_encargados_activ e USING (id_activ)
				WHERE e.id_ubi=$id_ubi
				ORDER BY abs(f_ini - date '".$Qf_ini_act."')
				limit 3
				";
			//echo "sql_dif: $sql_dif<br>";
			$txt_dif="";
			foreach ($oDBA->query($sql_dif) as $row_dif) {
				extract($row_dif);
				$txt_dif.=" $dif;";
			}
			//$txt_ctr.="<tr><td><span class=link id=$id_ubi onclick=fnjs_asignar_ctr('$Qid_activ','$id_ubi')> $nombre_ubi</span></td>";
			$txt_ctr.="<tr><td class=link id=$id_ubi onclick=fnjs_asignar_ctr('$Qid_activ','$id_ubi')> $nombre_ubi</td></tr>";
			$txt_ctr.="<td>$num_activ</td><td>$txt_dif</td></tr>";
		}
		$txt="<table><tr><td class=cabecera>"._("centro")."</td><td class=cabecera>"._("num")."</td><td class=cabecera>"._("dif días")."</td></tr>$txt_ctr</table>";
		echo $txt;
		break;
	case "nuevo_sr":
		$aWhere['_ordre'] = 'nombre_ubi';
		$aWhere['status'] = 't';
		$aWhere['tipo_labor'] = '512'; //sg -> 512
		$aOperador['tipo_labor'] = '&';
		$GesCentros = new GestorCentroDl();
		$cCentros = $GesCentros->getCentros($aWhere,$aOperador);
		$txt_ctr='';
		foreach ($cCentros as $oCentro) {
			$id_ubi = $oCentro->getId_ubi();
			$nombre_ubi = $oCentro->getNombre_ubi();
			//$txt_ctr.="<tr><td><span class=link id=$id_ubi onclick=fnjs_asignar_ctr('$Qid_activ','$id_ubi')> $nombre_ubi</span></td></tr>";
			$txt_ctr.="<tr><td class=link id=$id_ubi onclick=fnjs_asignar_ctr('$Qid_activ','$id_ubi')> $nombre_ubi</td></tr>";
		}
		$txt="<table><tr><td class=cabecera>"._("centro")."</td></tr>$txt_ctr</table>";
		echo $txt;
		break;
	case "nuevo_nagd":
		$aWhere['_ordre'] = 'nombre_ubi';
		$aWhere['status'] = 't';
		$aWhere['tipo_ctr'] = '^[na]';
		$aOperador['tipo_ctr'] = '~';
		$GesCentros = new GestorCentroDl();
		$cCentros = $GesCentros->getCentros($aWhere,$aOperador);
		$txt_ctr='';
		foreach ($cCentros as $oCentro) {
			$id_ubi = $oCentro->getId_ubi();
			$nombre_ubi = $oCentro->getNombre_ubi();
			//$txt_ctr.="<tr><td><span class=link id=$id_ubi onclick=fnjs_asignar_ctr('$Qid_activ','$id_ubi')> $nombre_ubi</span></td></tr>";
			$txt_ctr.="<tr><td class=link id=$id_ubi onclick=fnjs_asignar_ctr('$Qid_activ','$id_ubi')> $nombre_ubi</td></tr>";
		}
		$txt="<table><tr><td class=cabecera>"._("centro")."</td></tr>$txt_ctr</table>";
		echo $txt;
		break;
	case "nuevo_sssc":
		$aWhere['_ordre'] = 'nombre_ubi';
		$aWhere['status'] = 't';
		$aWhere['tipo_ctr'] = '^sss';
		$aOperador['tipo_ctr'] = '~';
		$GesCentros = new GestorCentroDl();
		$cCentros = $GesCentros->getCentros($aWhere,$aOperador);
		$txt_ctr='';
		foreach ($cCentros as $oCentro) {
			$id_ubi = $oCentro->getId_ubi();
			$nombre_ubi = $oCentro->getNombre_ubi();
			//$txt_ctr.="<tr><td><span class=link id=$id_ubi onclick=fnjs_asignar_ctr('$Qid_activ','$id_ubi')> $nombre_ubi</span></td></tr>";
			$txt_ctr.="<tr><td class=link id=$id_ubi onclick=fnjs_asignar_ctr('$Qid_activ','$id_ubi')> $nombre_ubi</td></tr>";
		}
		$txt="<table><tr><td class=cabecera>"._("centro")."</td></tr>$txt_ctr</table>";
		echo $txt;
		break;
	case "nuevo_sfsg":
		$aWhere['_ordre'] = 'nombre_ubi';
		$aWhere['status'] = 't';
		$aWhere['tipo_labor'] = '64'; //sg -> 64
		$aOperador['tipo_labor'] = '&';
		/*
		$aWhere['tipo_ctr'] = '^s';
		$aOperador['tipo_ctr'] = '~';
		*/
		$GesCentros = new GestorCentroEllas();
		$cCentros = $GesCentros->getCentrosSf($aWhere,$aOperador);
		$txt_ctr='';
		foreach ($cCentros as $oCentro) {
			$id_ubi = $oCentro->getId_ubi();
			$nombre_ubi = $oCentro->getNombre_ubi();
			//$txt_ctr.="<tr><td><span class=link id=$id_ubi onclick=fnjs_asignar_ctr('$Qid_activ','$id_ubi')> $nombre_ubi</span></td></tr>";
			$txt_ctr.="<tr><td class=link id=$id_ubi onclick=fnjs_asignar_ctr('$Qid_activ','$id_ubi')> $nombre_ubi</td></tr>";
		}
		$txt="<table><tr><td class=cabecera>"._("centro")."</td></tr>$txt_ctr</table>";
		echo $txt;
		break;
	case "nuevo_sfsr":
		$aWhere['_ordre'] = 'nombre_ubi';
		$aWhere['status'] = 't';
		$aWhere['tipo_labor'] = '512'; //sg -> 512
		$aOperador['tipo_labor'] = '&';
		$GesCentros = new GestorCentroEllas();
		$cCentros = $GesCentros->getCentrosSf($aWhere,$aOperador);
		$txt_ctr='';
		foreach ($cCentros as $oCentro) {
			$id_ubi = $oCentro->getId_ubi();
			$nombre_ubi = $oCentro->getNombre_ubi();
			//$txt_ctr.="<tr><td><span class=link id=$id_ubi onclick=fnjs_asignar_ctr('$Qid_activ','$id_ubi')> $nombre_ubi</span></td></tr>";
			$txt_ctr.="<tr><td class=link id=$id_ubi onclick=fnjs_asignar_ctr('$Qid_activ','$id_ubi')> $nombre_ubi</td></tr>";
		}
		$txt="<table><tr><td class=cabecera>"._("centro")."</td></tr>$txt_ctr</table>";
		echo $txt;
		break;
	case "nuevo_sfnagd":
		$aWhere['_ordre'] = 'nombre_ubi';
		$aWhere['status'] = 't';
		$aWhere['tipo_ctr'] = '^[na]';
		$aOperador['tipo_ctr'] = '~';
		$GesCentros = new GestorCentroEllas();
		$cCentros = $GesCentros->getCentrosSf($aWhere,$aOperador);
		$txt_ctr='';
		foreach ($cCentros as $oCentro) {
			$id_ubi = $oCentro->getId_ubi();
			$nombre_ubi = $oCentro->getNombre_ubi();
			//$txt_ctr.="<tr><td><span class=link id=$id_ubi onclick=fnjs_asignar_ctr('$Qid_activ','$id_ubi')> $nombre_ubi</span></td></tr>";
			$txt_ctr.="<tr><td class=link id=$id_ubi onclick=fnjs_asignar_ctr('$Qid_activ','$id_ubi')> $nombre_ubi</td></tr>";
		}
		$txt="<table><tr><td class=cabecera>"._("centro")."</td></tr>$txt_ctr</table>";
		echo $txt;
		break;
	case "asignar":
        $Qid_ubi = (integer) \filter_input(INPUT_POST, 'id_ubi');
		// miro si hay centros encargados, para poner num orden después.
		$aWhere['id_activ']=$Qid_activ;
		$aWhere['_ordre']='num_orden DESC';
		$GesCentrosEncargados = new GestorCentroEncargado();
		$cCentros = $GesCentrosEncargados->getCentrosEncargados($aWhere);
		if (is_array($cCentros) && count($cCentros) >= 1) {
			$num_orden=$cCentros[0]->getNum_orden() + 1;
		} else {
			$num_orden=0;
		}
		$oCentroEncargado = new CentroEncargado(array('id_activ'=>$Qid_activ,'id_ubi'=>$Qid_ubi));
		$oCentroEncargado->setNum_orden($num_orden);
		$oCentroEncargado->setEncargo('organizador');
		if ($oCentroEncargado->DBGuardar() === false) {
			echo _("hay un error, no se ha guardado el cargo");
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
		$Qempiezamin = empty($Qempiezamin)? "1/$mes/$any" : $Qempiezamin;
		$Qempiezamax = empty($Qempiezamax)? "1/$mes/$any+1" : $Qempiezamax;
		
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
			case "sg":
				$aWhere['id_tipo_activ']='^1[45]';
				$aOperador['id_tipo_activ']='~';
				break;
			case "sr":
				$aWhere['id_tipo_activ']='^17';
				$aOperador['id_tipo_activ']='~';
				break;
			case "nagd":
				$aWhere['id_tipo_activ']='^1[13]';
				$aOperador['id_tipo_activ']='~';
				break;
			case "sfsg":
				$aWhere['id_tipo_activ']='^2[45]';
				$aOperador['id_tipo_activ']='~';
				break;
			case "sfsr":
				$aWhere['id_tipo_activ']='^2[789]';
				$aOperador['id_tipo_activ']='~';
				break;
			case "sfnagd":
				$aWhere['id_tipo_activ']='^2[123]';
				$aOperador['id_tipo_activ']='~';
				break;
			case "sssc":
				$aWhere['id_tipo_activ']='^16';
				$aOperador['id_tipo_activ']='~';
				break;
		}
		$aWhere['_ordre']='f_ini';

		$GesActividades = new GestorActividadDl();
		$cActividades = $GesActividades->getActividades($aWhere,$aOperador);

		$titulo = sprintf(_("listado de actividades %s"),$Qtipo);

		$a_cabeceras = [];
		$a_cabeceras[]=ucfirst(_("actividad"));
		$a_cabeceras[]=ucfirst(_("ctr encargados"));

		$i=0;
		$sin=0;
		$a_valores = array();
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
			}

			if ($oPermActiv->have_perm('ocupado') === false) { $sin++; continue; } // no tiene permisos ni para ver.
			if ($oPermActiv->have_perm('ver') === false) { // sólo puede ver que està ocupado
			} else {
				$a_valores[$i][0]=$id_activ;
				$a_valores[$i][10]=$oPermCtr; // para no tener que recalcularlo despues.

				$a_valores[$i][1]=$nom_activ;
			
				$GesCtrEncargados = new GestorCentroEncargado();
				$cCtrsEncargados = $GesCtrEncargados->getCentrosEncargadosActividad($id_activ);	
				$a_centros=array();
				if ($oPermCtr->have_perm('ver') === true) { // sólo si tiene permiso
					foreach($cCtrsEncargados as $oCentro) {
						$id_ubi = $oCentro->getId_ubi();
						$nombre_ubi = $oCentro->getNombre_ubi();
                        $a_centros[]=array('nombre_ubi'=>$nombre_ubi,'id_ubi'=>$id_ubi);
					}
				}
				$a_valores[$i][2]=$a_centros;
				$a_valores[$i][3]=$f_ini;
				$a_valores[$i][4]=$f_fin;
			}
		}
		?>

		<p><h3><?= $titulo ?></h3></p>	
		<table onclick="fnjs_cambiar_ctr(event,'','','');"><tr>
		<?php
		foreach ($a_cabeceras as $cabecera) {
			echo "<td>$cabecera</td>";
		}
		?>
		<td id="lst_ctr"><td>
		</tr>
		<?php
		foreach ($a_valores as $valores) {
			//print_r($valores[2]);
			$oPermCtr=$valores[10];
			$id_activ=$valores[0];
			$f_ini=$valores[3];
			$f_fin=$valores[4];
			$txt_ctr="";
			if (is_array($valores[2])) {
				foreach ($valores[2] as $a_centro){
					$id_ubi=$a_centro['id_ubi'];
					$id_txt_ubi=$id_activ."_".$id_ubi;
					if ($oPermCtr->have_perm('modificar') === true) { // sólo si tiene permiso para modificar
						$txt_ctr.="<span class=link id=$id_txt_ubi onclick=fnjs_cambiar_ctr(event,'$id_activ','$id_ubi')> ${a_centro['nombre_ubi']};</span>";
					} else { // permiso para ver (si no tiene permisos el valor($valores[2]) ya está en blanco)
						$txt_ctr.="<span> ${a_centro['nombre_ubi']};</span>";
					}
				}
			}
			$txt_id=$valores[0]."_ctrs";
			if ($oPermCtr->have_perm('crear') === true) { // sólo si tiene permiso para crear
				$nuevo_txt="<span class=link onclick=fnjs_nuevo_ctr('$id_activ','$inicio','$fin','$f_ini','$f_fin')>nuevo</span>";
			} else {
				$nuevo_txt='';
			}
			echo "<tr id=$valores[0]><td>$valores[1]</td><td id=$txt_id>$txt_ctr</td><td>$nuevo_txt</td></tr>";
		}
		?>
		</table>
		<?php
		break;
}
