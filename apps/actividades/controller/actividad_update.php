<?php
use actividades\model as actividades;
/**
* Esta página actualiza la tabla de las actividades.
*
*
*@package	delegacion
*@subpackage	actividades
*@author	Daniel Serrabou
*@since		15/5/02.
*		
*
*@param string $mod  'nuevo'|'cmb_tipo'|'eliminar'|'editar'|'actualizar_sacd'|'actualizar_ctr'
*@param string $origen 'calendario' sirve para volver (si no es calendario).
*/

/**
* Para asegurar que inicia la sesion, y poder acceder a los permisos
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


function poner_asistencia($id_activ,$sacd) {
	$insert="INSERT INTO d_asistentes_activ
			              (id_activ,id_nom,propio)
						  VALUES ('$id_activ','$sacd','f')";
	$oDBSt_q=$oDB->exec($insert);
}

function quitar_asistencia($id_activ,$sacd) {
	$delete="DELETE FROM d_asistentes_activ
			        WHERE id_activ='$id_activ' AND id_nom='$sacd' ";
	//echo "sql: $delete";
	$oDBSt_q=$oDB->exec($delete);
}

function borrar_actividad($id_activ) {
	$oActividad = new actividades\Actividad($id_activ);
	$oActividad->DBCarregar();
	// si es de la sf quito la 'f'
	$dl = preg_replace('/f$/', '',$oActividad->getDl_org());
	$id_tabla = $oActividad->getId_tabla();
	if ($dl == core\ConfigGlobal::mi_dele()) { // de la propia dl
		$status = $oActividad->getStatus();
		if (!empty($status) && $status == 1) { // si no esta en proyecto (status=1) no dejo borrar,
			if ($oActividad->DBEliminar() === false) {
				echo _('Hay un error, no se ha eliminado');
			}
		} else {
			$oActividad->setStatus(4); // la pongo en estado borrable
			if ($oActividad->DBGuardar() === false) {
				echo _('Hay un error, no se ha guardado');
			}
		}
	} else {
		if ($id_tabla == 'dl') {
			// No se puede eliminar una actividad de otra dl. Hay que borrarla como importada
			$oImportada = new actividades\Importar($id_activ);
			$oImportada->DBEliminar();
		} else { // de otras dl en resto
			$oActividad->setStatus(4); // la pongo en estado borrable
			if ($oActividad->DBGuardar() === false) {
				echo _('Hay un error, no se ha guardado');
			}
		}
	}
}

$id_activ = empty($_POST['id_activ'])? '' : $_POST['id_activ'];

switch ($_POST['mod']) {
case 'publicar':
	if (!empty($_POST['sel'])) { // puedo seleccionar más de uno.
		foreach ($_POST['sel'] as $id) {
			$id_activ=strtok($id,'#');
			$oActividad = new actividades\Actividad($id_activ);
			$oActividad->DBCarregar();
			$oActividad->setPublicado('t');
			if ($oActividad->DBGuardar() === false) { 
				echo _('Hay un error, no se ha guardado');
				$err = 1;
			}
		}
	}
	break;
case 'importar':
	if (!empty($_POST['sel'])) { // puedo seleccionar más de uno.
		foreach ($_POST['sel'] as $id) {
			$id_activ=strtok($id,'#');
			$oImportar = new actividades\Importar($id_activ);
			if ($oImportar->DBGuardar() === false) {
				echo _('Hay un error, no se ha importado');
			}
		}
	}
	break;
case "nuevo":
	//Compruebo que estén todos los campos necesasrios
	if (empty($_POST['nom_activ']) or empty($_POST['f_ini']) or empty($_POST['f_fin']) or empty($_POST['status']) or empty($_POST['dl_org']) ) {
		echo _("Debe llenar todos los campos que tengan un (*)")."<br>";
		exit;
	}
	if (empty($_POST['inom_tipo_val']) || strstr($_POST['inom_tipo_val'],'.')) {
		echo _("Debe seleccionar un tipo de actividad")."<br>";
		exit;
	}

	$dl_org = empty($_POST['dl_org'])? '' : $_POST['dl_org'];
	// si es de la sf quito la 'f'
	$dele = preg_replace('/f$/', '',$dl_org);
	if ($dele == core\ConfigGlobal::mi_dele()) {
		$oActividad= new actividades\ActividadDl();
	} else {
		$oActividad= new actividades\ActividadEx();
		$oActividad->setPublicado('t');
		$oActividad->setId_tabla('ex');
		$_POST['status'] = 2; // Que sea estado actual.
	}
	$oActividad->setDl_org($dl_org);
	if (isset($_POST['id_tipo_activ'])) {
	   if ($oActividad->setId_tipo_activ($_POST['id_tipo_activ']) === false) {
		 echo _("Tipo de actividad incorrecto");
		 exit;
	   }
	}
	isset($_POST['nom_activ']) ? $oActividad->setNom_activ($_POST['nom_activ']) : '';
	// En el caso de tener id_ubi (!=1) borro el campo lugar_esp.
	if (!empty($_POST['id_ubi']) && $_POST['id_ubi'] != 1 ) {
		$oActividad->setId_ubi($_POST['id_ubi']);
		$oActividad->setLugar_esp('');
	} else {
		$oActividad->setId_ubi($_POST['id_ubi']);
		$oActividad->setLugar_esp($_POST['lugar_esp']);
	}
	isset($_POST['desc_activ']) ? $oActividad->setDesc_activ($_POST['desc_activ']) : '';
	isset($_POST['f_ini']) ? $oActividad->setF_ini($_POST['f_ini']) : '';
	isset($_POST['f_fin']) ? $oActividad->setF_fin($_POST['f_fin']) : '';
	isset($_POST['tipo_horario']) ? $oActividad->setTipo_horario($_POST['tipo_horario']) : '';
	isset($_POST['precio']) ? $oActividad->setPrecio($_POST['precio']) : '';
	isset($_POST['num_asistentes']) ? $oActividad->setNum_asistentes($_POST['num_asistentes']) : '';
	isset($_POST['status']) ? $oActividad->setStatus($_POST['status']) : '';
	isset($_POST['observ']) ? $oActividad->setObserv($_POST['observ']) : '';
	isset($_POST['nivel_stgr']) ? $oActividad->setNivel_stgr($_POST['nivel_stgr']) : '';
	isset($_POST['id_repeticion']) ? $oActividad->setId_repeticion($_POST['id_repeticion']) : '';
	isset($_POST['observ_material']) ? $oActividad->setObserv_material($_POST['observ_material']) : '';
	isset($_POST['tarifa']) ? $oActividad->setTarifa($_POST['tarifa']) : '';
	isset($_POST['h_ini']) ? $oActividad->setH_ini($_POST['h_ini']) : '';
	isset($_POST['h_fin']) ? $oActividad->setH_fin($_POST['h_fin']) : '';
	if ($oActividad->DBGuardar() === false) { 
		echo _('Hay un error, no se ha guardado');
	}
	// si estoy creando una actividad de otra dl es porque la quiero importar.
	if ($dele != core\ConfigGlobal::mi_dele()) {
		$id_activ = $oActividad->getId_activ();
		$oImportar = new actividades\Importar($id_activ);
		if ($oImportar->DBGuardar() === false) {
			echo _('Hay un error, no se ha importado');
		}
	}
	break;
case "duplicar": // duplicar la actividad.
	if (!empty($_POST['sel'])) {
		$id_activ=strtok($_POST['sel'][0],'#');
		$oActividadAll = new actividades\Actividad($id_activ);
		$dl = $oActividadAll->getDl_org();
		if ($dl == core\ConfigGlobal::mi_dele()) {
			$oActividad = new actividades\ActividadDl($id_activ);
		} else {
			exit(_("No se puede duplicar actividades que no sean de la propia dl"));
		}
		$oActividad->DBCarregar();
		$oActividad->setId_activ('0'); //para que al guardar genere un nuevo id.
		$nom = _("dup").' '.$oActividad->getNom_activ();
		$oActividad->setNom_activ($nom);
		$oActividad->setStatus(1); // la pongo en estado proyecto
		if ($oActividad->DBGuardar() === false) {
			echo _('Hay un error, no se ha guardado');
		}
		$oActividad->DBCarregar();
	}
	break;
case "eliminar": // Eliminar la actividad.
	if (!empty($_POST['sel'])) { // puedo seleccionar más de uno.
		foreach ($_POST['sel'] as $id) {
			$id_activ=strtok($id,'#');
			borrar_actividad($id_activ);
		}
	}
	// si vengo desde la presentacion del planning, ya tengo el id_activ.
	if (!empty($id_activ)) {
		borrar_actividad($id_activ);
	}
	break;
case "cmb_tipo": // sólo cambio el tipo a una actividad existente //____________________________
	//echo "id_tipo de actividad: $id_tipo_activ<br>";
	if (!empty($_POST['id_tipo_activ']) and !strstr($_POST['id_tipo_activ'],'.')) {
		$valor_id_tipo_activ=$_POST['id_tipo_activ'];
	} else {
		$condta=$_POST['isfsv_val'].$_POST['iasistentes_val'].$_POST['iactividad_val'].$_POST['inom_tipo_val'];
		if (!strstr ($condta, '.')) {
			$valor_id_tipo_activ = $condta;
		} else {
			echo _("Debe seleccionar un tipo de actividad")."<br>";
			exit;
		}
	}
	$oActividad = new actividades\Actividad($id_activ);
	$oActividad->DBCarregar();
	$oActividad->setId_tipo_activ($valor_id_tipo_activ);
	if(isset($_POST['dl_org'])) {
		$dl_org = strtok($_POST['dl_org'],'#');
		$oActividad->setDl_org($dl_org);
	} else {	
		$oActividad->setDl_org('');
	}	
	isset($_POST['nom_activ']) ? $oActividad->setNom_activ($_POST['nom_activ']) : '';
	isset($_POST['id_ubi']) ? $oActividad->setId_ubi($_POST['id_ubi']) : '';
	isset($_POST['desc_activ']) ? $oActividad->setDesc_activ($_POST['desc_activ']) : '';
	isset($_POST['f_ini']) ? $oActividad->setF_ini($_POST['f_ini']) : '';
	isset($_POST['f_fin']) ? $oActividad->setF_fin($_POST['f_fin']) : '';
	isset($_POST['tipo_horario']) ? $oActividad->setTipo_horario($_POST['tipo_horario']) : '';
	isset($_POST['precio']) ? $oActividad->setPrecio($_POST['precio']) : '';
	isset($_POST['num_asistentes']) ? $oActividad->setNum_asistentes($_POST['num_asistentes']) : '';
	isset($_POST['status']) ? $oActividad->setStatus($_POST['status']) : '';
	isset($_POST['observ']) ? $oActividad->setObserv($_POST['observ']) : '';
	isset($_POST['nivel_stgr']) ? $oActividad->setNivel_stgr($_POST['nivel_stgr']) : '';
	isset($_POST['id_repeticion']) ? $oActividad->setId_repeticion($_POST['id_repeticion']) : '';
	isset($_POST['observ_material']) ? $oActividad->setObserv_material($_POST['observ_material']) : '';
	isset($_POST['lugar_esp']) ? $oActividad->setLugar_esp($_POST['lugar_esp']) : '';
	isset($_POST['tarifa']) ? $oActividad->setTarifa($_POST['tarifa']) : '';
	isset($_POST['h_ini']) ? $oActividad->setH_ini($_POST['h_ini']) : '';
	isset($_POST['h_fin']) ? $oActividad->setH_fin($_POST['h_fin']) : '';
	if ($oActividad->DBGuardar() === false) { 
		echo _('Hay un error, no se ha guardado');
	}
	break;
case "editar": // editar la actividad.
	$oActividad = new actividades\Actividad($id_activ);
	$oActividad->DBCarregar();
	isset($_POST['id_tipo_activ']) ? $oActividad->setId_tipo_activ($_POST['id_tipo_activ']) : '';
	if (isset($_POST['dl_org'])) {
		$dl_orig=$oActividad->getDl_org();
		$dl_org = strtok($_POST['dl_org'],'#');
		$oActividad->setDl_org($dl_org);
	} else {	
		$oActividad->setDl_org('');
	}	
	isset($_POST['nom_activ']) ? $oActividad->setNom_activ($_POST['nom_activ']) : '';
	// En el caso de tener id_ubi (!=1) borro el campo lugar_esp.
	if (!empty($_POST['id_ubi']) && $_POST['id_ubi'] != 1 ) {
		$oActividad->setId_ubi($_POST['id_ubi']);
		$oActividad->setLugar_esp('');
	} else {
		$oActividad->setId_ubi($_POST['id_ubi']);
		$oActividad->setLugar_esp($_POST['lugar_esp']);
	}
	isset($_POST['desc_activ']) ? $oActividad->setDesc_activ($_POST['desc_activ']) : '';
	isset($_POST['f_ini']) ? $oActividad->setF_ini($_POST['f_ini']) : '';
	isset($_POST['f_fin']) ? $oActividad->setF_fin($_POST['f_fin']) : '';
	isset($_POST['tipo_horario']) ? $oActividad->setTipo_horario($_POST['tipo_horario']) : '';
	isset($_POST['precio']) ? $oActividad->setPrecio($_POST['precio']) : '';
	isset($_POST['num_asistentes']) ? $oActividad->setNum_asistentes($_POST['num_asistentes']) : '';
	isset($_POST['status']) ? $oActividad->setStatus($_POST['status']) : '';
	isset($_POST['observ']) ? $oActividad->setObserv($_POST['observ']) : '';
	isset($_POST['nivel_stgr']) ? $oActividad->setNivel_stgr($_POST['nivel_stgr']) : '';
	isset($_POST['id_repeticion']) ? $oActividad->setId_repeticion($_POST['id_repeticion']) : '';
	isset($_POST['observ_material']) ? $oActividad->setObserv_material($_POST['observ_material']) : '';
	isset($_POST['tarifa']) ? $oActividad->setTarifa($_POST['tarifa']) : '';
	isset($_POST['h_ini']) ? $oActividad->setH_ini($_POST['h_ini']) : '';
	isset($_POST['h_fin']) ? $oActividad->setH_fin($_POST['h_fin']) : '';
	isset($_POST['publicado']) ? $oActividad->setPublicado($_POST['publicado']) : '';
	if ($oActividad->DBGuardar() === false) { 
		echo '<br>'._('Hay un error, no se ha guardado');
		$err = 1;
	}
	// Si cambio de dl_propia a otra (o al revés), hay que cambiar el proceso. Se hace al final para que la actividad ya tenga puesta la nueva dl
	if(core\ConfigGlobal::is_app_installed('procesos')){
		if (($dl_orig != $dl_org) && ($dl_org==core\ConfigGlobal::mi_dele() || $dl_orig==core\ConfigGlobal::mi_dele())) {
			$oGestorActividadProcesoTarea = new GestorActividadProcesoTarea();
			$oGestorActividadProcesoTarea->generarProceso($oActividad->getId_activ());
		}
	}
	break;
case "actualizar_sacd": // para actualizar los sacd encargados.
	if (!empty($_POST['sacd1_antiguo']))	{
	  if ($_POST['sacd1']<>$_POST['sacd1_antiguo']) { 
	    if (!empty($_POST['sacd1'])) {
	      $update_sacd="UPDATE d_cargos_activ
		                SET id_nom=".$_POST['sacd1']."
			  	  	    WHERE id_activ=".$id_activ." AND id_nom=".$_POST['sacd1_antiguo']." AND id_cargo='35'";
			// si es una actividad de sv le añado la asistencia
			if (substr($id_tipo_activ,0,1)==1) {
				quitar_asistencia($id_activ,$_POST['sacd1_antiguo']);
				poner_asistencia($id_activ,$_POST['sacd1']);
			}
	    }  else  {
	      $update_sacd="DELETE FROM d_cargos_activ
		                WHERE id_activ=".$id_activ." AND id_nom=".$_POST['sacd1_antiguo']." AND id_cargo='35'";  
		   // si es una actividad de sv le quito la asistencia
			if (substr($_POST['id_tipo_activ'],0,1)==1) {
				quitar_asistencia($id_activ,$_POST['sacd1_antiguo']);
			}
	    } 
	    $oDBSt_q=$oDB->query($update_sacd);
	  }
	} else {
	  if (!empty($_POST['sacd1'])) {
	    $update_sacd="INSERT INTO d_cargos_activ
		              (id_activ,id_cargo,id_nom)
					  VALUES ('".$id_activ."','35','".$_POST['sacd1']."')";
	    $oDBSt_q=$oDB->query($update_sacd);
		// si es una actividad de sv le añado la asistencia
		if (substr($_POST['id_tipo_activ'],0,1)==1) {
			poner_asistencia($id_activ,$_POST['sacd1']);
		}
	  }
	}  
	
	if (!empty($_POST['sacd2_antiguo']))	{
	  if ($_POST['sacd2']<>$_POST['sacd2_antiguo']) { 
	    if (!empty($_POST['sacd2'])){
	      $update_sacd="UPDATE d_cargos_activ
		                SET id_nom=".$_POST['sacd2']."
		  			    WHERE id_activ=".$id_activ." AND id_nom=".$_POST['sacd2_antiguo']." AND id_cargo='36'";
			// si es una actividad de sv le añado la asistencia
			if (substr($_POST['id_tipo_activ'],0,1)==1) {
				quitar_asistencia($id_activ,$_POST['sacd2_antiguo']);
				poner_asistencia($id_activ,$_POST['sacd2']);
			}
	    } else {
	      $update_sacd="DELETE FROM d_cargos_activ
		                WHERE id_activ=".$id_activ." AND id_nom=".$_POST['sacd2_antiguo']." AND id_cargo='36'"; 
		   // si es una actividad de sv le quito la asistencia
			if (substr($_POST['id_tipo_activ'],0,1)==1) {
				quitar_asistencia($id_activ,$_POST['sacd2_antiguo']);
			}
	    }
	    $oDBSt_q=$oDB->query($update_sacd);
	  }
	} else {
	  if (!empty($_POST['sacd2'])) {
	    $update_sacd="INSERT INTO d_cargos_activ
		              (id_activ,id_cargo,id_nom)
					  VALUES ('".$id_activ."','36','".$_POST['sacd2']."')";
	    $oDBSt_q=$oDB->query($update_sacd);
		// si es una actividad de sv le añado la asistencia
		if (substr($_POST['id_tipo_activ'],0,1)==1) {
			poner_asistencia($id_activ,$_POST['sacd2']);
		}
	  }
	}  
	
	if (!empty($_POST['sacd3_antiguo'])) {
	  if ($_POST['sacd3']<>$_POST['sacd3_antiguo']) { 
	    if (!empty($_POST['sacd3'])){
	      $update_sacd="UPDATE d_cargos_activ
		                SET id_nom=".$_POST['sacd3']."
			  		    WHERE id_activ=".$id_activ." AND id_nom=".$_POST['sacd3_antiguo']." AND id_cargo='37'";
			// si es una actividad de sv le añado la asistencia
			if (substr($_POST['id_tipo_activ'],0,1)==1) {
				quitar_asistencia($id_activ,$_POST['sacd3_antiguo']);
				poner_asistencia($id_activ,$_POST['sacd3']);
			}
	    } else {
	      $update_sacd="DELETE FROM d_cargos_activ
		                WHERE id_activ=".$id_activ." AND id_nom=".$_POST['sacd3_antiguo']." AND id_cargo='37'"; 
			// si es una actividad de sv le quito la asistencia
			if (substr($_POST['id_tipo_activ'],0,1)==1) {
				quitar_asistencia($id_activ,$_POST['sacd3_antiguo']);
			}
	    }
	    $oDBSt_q=$oDB->query($update_sacd);
	  }
	} else {
	  if (!empty($_POST['sacd3'])) {
	    $update_sacd="INSERT INTO d_cargos_activ
		              (id_activ,id_cargo,id_nom)
					  VALUES ('".$id_activ."','37','".$_POST['sacd3']."')";
	    $oDBSt_q=$oDB->query($update_sacd);
		// si es una actividad de sv le añado la asistencia
		if (substr($_POST['id_tipo_activ'],0,1)==1) {
			poner_asistencia($id_activ,$_POST['sacd3']);
		}
	  }
	}  
	//echo "$update_sacd, SACD1:$sacd1, SACD1_ANT: $sacd1_antiguo";
	
	break;
case "actualizar_ctr": // cambiar sólo los ctr encargados
	//para actualizar los centros encargados.
	$n=count($_POST['ctr']);
	for ($i=0; $i<$n; $i++) {
		//echo "cen: $n<br>";
		if ($_POST['ctr_antiguo'][$i]) { // miro si ya existía un centro antiguo
		  if ($_POST['ctr'][$i]<>$_POST['ctr_antiguo'][$i]) { //¿ha cambiado?
			$ctr_antiguo=$_POST['ctr_antiguo'][$i];
			if ($_POST['ctr'][$i]) { // si el nuevo no es nulo, lo actualizo
				$ctr=$_POST['ctr'][$i];
				$update_ctr="UPDATE d_encargados_activ
							SET id_ubi=$ctr
							WHERE id_activ=".$id_activ." AND id_ubi=$ctr_antiguo AND num_orden='$i'";
				//echo "up: $update_ctr, $i<br>";
			} else { // si es nulo lo elimino
				$update_ctr="DELETE FROM d_encargados_activ
							WHERE id_activ=".$id_activ." AND id_ubi=$ctr_antiguo ";  
			} 
			$oDBSt_q=$oDB->exec($update_ctr);
		  }
		} else { //No hay centro antiguo
		  if ($_POST['ctr'][$i]) { // si hay nuevo: lo añado
			$update_ctr="INSERT INTO d_encargados_activ
						  (id_activ,num_orden,id_ubi,encargo)
						  VALUES (?,?,?,'organizador')";
			$a_values=array($id_activ,$i,$_POST['ctr'][$i]);
			//echo "q: $update_ctr<br>";
			//print_r($a_values);
			//ejecuta
			$oDBSt_q=$oDB->prepare($update_ctr);
			$oDBSt_q->execute($a_values);

		  }
		}  
	}	
} // fin del switch de mod.


if (empty($_POST['origen'])) { $_POST['origen']=''; }

if ($_POST['origen'] != 'calendario' && $_POST['mod'] != 'eliminar') {
	if ($_POST['mod']=='nuevo' || $_POST['mod']=='duplicar') {
		$tabla='Actividad';
		$go_to='actividad_ver.php?que=ver&id_activ='.$oActividad->getId_activ()."&tabla=$tabla";
		$oPosicion = new web\Posicion();
		echo $oPosicion->ir_a($go_to);
		exit;
	} else {
		$tabla="Actividad";
		$go_to="actividad_ver.php?que=ver&id_activ=".$id_activ."&tabla=$tabla";
	}
	//vuelve a la presentacion de la ficha.
	if (!empty($_POST['mem'])) {
		$go_to="session@sel";
	}
	//echo "go_to: $go_to<br>";
	//$r=ir_a($go_to);
	if (empty($err)) {
		$oPosicion->setId_div('ir_a');
		echo $oPosicion->atras();
	} else {
		?>
		<table>
		<tr>
		<td class="atras no_print">
		<?= $oPosicion->atras2(); ?>
		</td>
		<td style="vertical-align: bottom;">
		<h3>
		<?php echo _("volver"); ?></h3></td>
		</tr>
		</table>
	<?php
	}
}
?>
