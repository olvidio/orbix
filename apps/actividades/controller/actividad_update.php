<?php
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

use actividades\model\entity as actividades;
use procesos\model\entity\GestorActividadProcesoTarea;
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
	$oDB->exec($insert);
}

function quitar_asistencia($id_activ,$sacd) {
	$delete="DELETE FROM d_asistentes_activ
			        WHERE id_activ='$id_activ' AND id_nom='$sacd' ";
	//echo "sql: $delete";
	$oDB->exec($delete);
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
				echo _("hay un error, no se ha eliminado");
			}
		} else {
			$oActividad->setStatus(4); // la pongo en estado borrable
			if ($oActividad->DBGuardar() === false) {
				echo _("hay un error, no se ha guardado");
			}
		}
	} else {
		if ($id_tabla == 'dl') {
			// No se puede eliminar una actividad de otra dl. Hay que borrarla como importada
			$oImportada = new actividades\Importada($id_activ);
			$oImportada->DBEliminar();
		} else { // de otras dl en resto
			$oActividad->setStatus(4); // la pongo en estado borrable
			if ($oActividad->DBGuardar() === false) {
				echo _("hay un error, no se ha guardado");
			}
		}
	}
}

$Qid_activ = (integer) \filter_input(INPUT_POST, 'id_activ');
$Qmod = (string) \filter_input(INPUT_POST, 'mod');

switch ($Qmod) {
case 'publicar':
	$a_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
	if (!empty($a_sel)) { // puedo seleccionar más de uno.
		foreach ($a_sel as $id) {
		    $id_activ = (integer) strtok($id,'#');
			$oActividad = new actividades\Actividad($id_activ);
			$oActividad->DBCarregar();
			$oActividad->setPublicado('t');
			if ($oActividad->DBGuardar() === false) { 
				echo _("hay un error, no se ha guardado");
				$err = 1;
			}
		}
	}
	break;
case 'importar':
	$a_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
	if (!empty($a_sel)) { // puedo seleccionar más de uno.
		foreach ($a_sel as $id) {
		    $id_activ = (integer) strtok($id,'#');
			$oImportada = new actividades\Importada($id_activ);
			if ($oImportada->DBGuardar() === false) {
				echo _("hay un error, no se ha importado");
			}
		}
	}
	break;
case "nuevo":
	$Qid_tipo_activ = (integer) \filter_input(INPUT_POST, 'id_tipo_activ');
	$Qid_ubi = (integer) \filter_input(INPUT_POST, 'id_ubi');
	$Qnum_asistentes = (integer) \filter_input(INPUT_POST, 'num_asistentes');
	$Qstatus = (integer) \filter_input(INPUT_POST, 'status');
	$Qid_repeticion = (integer) \filter_input(INPUT_POST, 'id_repeticion');
	$Qplazas = (integer) \filter_input(INPUT_POST, 'plazas');
	$Qtarifa = (integer) \filter_input(INPUT_POST, 'tarifa');
	$Qprecio = (float) \filter_input(INPUT_POST, 'precio');
	
	$Qdl_org = (string) \filter_input(INPUT_POST, 'dl_org');
	$Qnom_activ = (string) \filter_input(INPUT_POST, 'nom_activ');
	$Qlugar_esp = (string) \filter_input(INPUT_POST, 'lugar_esp');
	$Qdesc_activ = (string) \filter_input(INPUT_POST, 'desc_activ');
	$Qf_ini = (string) \filter_input(INPUT_POST, 'f_ini');
	$Qf_fin = (string) \filter_input(INPUT_POST, 'f_fin');
	$Qtipo_horario = (string) \filter_input(INPUT_POST, 'tipo_horario');
	$Qobserv = (string) \filter_input(INPUT_POST, 'observ');
	$Qnivel_stgr = (string) \filter_input(INPUT_POST, 'nivel_stgr');
	$Qobserv_material = (string) \filter_input(INPUT_POST, 'observ_material');
	$Qh_ini = (string) \filter_input(INPUT_POST, 'h_ini');
	$Qh_fin = (string) \filter_input(INPUT_POST, 'h_fin');
	$Qpublicado = (string) \filter_input(INPUT_POST, 'publicado');
	
	// Puede ser '000' > sin especificar
	$Qinom_tipo_val = (string) \filter_input(INPUT_POST, 'inom_tipo_val');
	//Compruebo que estén todos los campos necesasrios
	if (empty($Qnom_activ) or empty($Qf_ini) or empty($Qf_fin) or empty($Qstatus) or empty($Qdl_org) ) {
		echo _("debe llenar todos los campos que tengan un (*)")."<br>";
		die();
	}
	if (empty($Qinom_tipo_val)) {
		echo _("debe seleccionar un tipo de actividad")."<br>";
		die();
	}

	// si es de la sf quito la 'f'
	$dele = preg_replace('/f$/', '',$Qdl_org);
	if ($dele == core\ConfigGlobal::mi_dele()) {
		$oActividad= new actividades\ActividadDl();
	} else {
		$oActividad= new actividades\ActividadEx();
		$oActividad->setPublicado('t');
		$oActividad->setId_tabla('ex');
		$Qstatus = actividades\ActividadAll::STATUS_ACTUAL; // Que sea estado actual.
	}
	$oActividad->setDl_org($Qdl_org);
	if (isset($Qid_tipo_activ)) {
	   if ($oActividad->setId_tipo_activ($Qid_tipo_activ) === false) {
		 echo _("tipo de actividad incorrecto");
		 die();
	   }
	}
	$oActividad->setNom_activ($Qnom_activ);

	// En el caso de tener id_ubi (!=1) borro el campo lugar_esp.
	if (!empty($Qid_ubi) && $Qid_ubi != 1 ) {
		$oActividad->setId_ubi($Qid_ubi);
		$oActividad->setLugar_esp('');
	} else {
		$oActividad->setId_ubi($Qid_ubi);
		$oActividad->setLugar_esp($Qlugar_esp);
	}
	$oActividad->setDesc_activ($Qdesc_activ);
	$oActividad->setF_ini($Qf_ini);
	$oActividad->setF_fin($Qf_fin);
	$oActividad->setTipo_horario($Qtipo_horario);
	$oActividad->setPrecio($Qprecio);
	$oActividad->setNum_asistentes($Qnum_asistentes);
	$oActividad->setStatus($Qstatus);
	$oActividad->setObserv($Qobserv);
	$oActividad->setNivel_stgr($Qnivel_stgr);
	$oActividad->setId_repeticion($Qid_repeticion);
	$oActividad->setObserv_material($Qobserv_material);
	$oActividad->setTarifa($Qtarifa);
	$oActividad->setH_ini($Qh_ini);
	$oActividad->setH_fin($Qh_fin);
	$oActividad->setPublicado($Qpublicado);
	$oActividad->setPlazas($Qplazas);
	if ($oActividad->DBGuardar() === false) { 
		echo '<br>'._("hay un error, no se ha guardado");
	}
	// si estoy creando una actividad de otra dl es porque la quiero importar.
	if ($dele != core\ConfigGlobal::mi_dele()) {
		$id_activ = $oActividad->getId_activ();
		$oImportada = new actividades\Importada($id_activ);
		if ($oImportada->DBGuardar() === false) {
			echo _("hay un error, no se ha importado");
		}
	}
	break;
case "duplicar": // duplicar la actividad.
	$a_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
	if (!empty($a_sel)) { 
	    $id_activ = (integer) strtok($a_sel[0],'#');
		$oActividadAll = new actividades\Actividad($id_activ);
		$dl = $oActividadAll->getDl_org();
		if ($dl == core\ConfigGlobal::mi_dele()) {
			$oActividad = new actividades\ActividadDl($id_activ);
		} else {
			exit(_("no se puede duplicar actividades que no sean de la propia dl"));
		}
		$oActividad->DBCarregar();
		$oActividad->setId_activ('0'); //para que al guardar genere un nuevo id.
		$nom = _("dup").' '.$oActividad->getNom_activ();
		$oActividad->setNom_activ($nom);
		$oActividad->setStatus(1); // la pongo en estado proyecto
		if ($oActividad->DBGuardar() === false) {
			echo _("hay un error, no se ha guardado");
		}
		$oActividad->DBCarregar();
	}
	break;
case "eliminar": // Eliminar la actividad.
	$a_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
	if (!empty($a_sel)) { // puedo seleccionar más de uno.
		foreach ($a_sel as $id) {
		    $id_activ = (integer) strtok($id,'#');
			borrar_actividad($id_activ);
		}
	}
	// si vengo desde la presentacion del planning, ya tengo el id_activ.
	if (!empty($Qid_activ)) {
		borrar_actividad($Qid_activ);
	}
	break;
case "cmb_tipo": // sólo cambio el tipo a una actividad existente //____________________________
	$Qid_tipo_activ = (integer) \filter_input(INPUT_POST, 'id_tipo_activ');
	$Qisfsv_val = (integer) \filter_input(INPUT_POST, 'isfsv_val');
	$Qiasistentes_val = (integer) \filter_input(INPUT_POST, 'iasistentes_val');
	$Qiactividad_val = (integer) \filter_input(INPUT_POST, 'iactividad_val');
	// Puede ser '000' > sin especificar
	$Qinom_tipo_val = (string) \filter_input(INPUT_POST, 'inom_tipo_val');

	$Qdl_org = (string) \filter_input(INPUT_POST, 'dl_org');

	
	$Qnum_asistentes = (integer) \filter_input(INPUT_POST, 'num_asistentes');
	$Qstatus = (integer) \filter_input(INPUT_POST, 'status');
	$Qid_repeticion = (integer) \filter_input(INPUT_POST, 'id_repeticion');
	$Qplazas = (integer) \filter_input(INPUT_POST, 'plazas');
	$Qtarifa = (integer) \filter_input(INPUT_POST, 'tarifa');
	$Qprecio = (float) \filter_input(INPUT_POST, 'precio');
	
	$Qnom_activ = (string) \filter_input(INPUT_POST, 'nom_activ');
	$Qid_ubi = (integer) \filter_input(INPUT_POST, 'id_ubi');
	$Qlugar_esp = (string) \filter_input(INPUT_POST, 'lugar_esp');
	$Qdesc_activ = (string) \filter_input(INPUT_POST, 'desc_activ');
	$Qf_ini = (string) \filter_input(INPUT_POST, 'f_ini');
	$Qf_fin = (string) \filter_input(INPUT_POST, 'f_fin');
	$Qtipo_horario = (string) \filter_input(INPUT_POST, 'tipo_horario');
	$Qobserv = (string) \filter_input(INPUT_POST, 'observ');
	$Qnivel_stgr = (string) \filter_input(INPUT_POST, 'nivel_stgr');
	$Qobserv_material = (string) \filter_input(INPUT_POST, 'observ_material');
	$Qh_ini = (string) \filter_input(INPUT_POST, 'h_ini');
	$Qh_fin = (string) \filter_input(INPUT_POST, 'h_fin');
	$Qpublicado = (string) \filter_input(INPUT_POST, 'publicado');

	//echo "id_tipo de actividad: $id_tipo_activ<br>";
	if (!empty($Qid_tipo_activ) and !strstr($Qid_tipo_activ,'.')) {
		$valor_id_tipo_activ=$Qid_tipo_activ;
	} else {
		$condta=$Qisfsv_val.$Qiasistentes_val.$Qiactividad_val.$Qinom_tipo_val;
		if (!strstr ($condta, '.')) {
			$valor_id_tipo_activ = $condta;
		} else {
			echo _("debe seleccionar un tipo de actividad")."<br>";
			die();
		}
	}
	$oActividad = new actividades\Actividad($id_activ);
	$oActividad->DBCarregar();
	$oActividad->setId_tipo_activ($valor_id_tipo_activ);
	if(isset($Qdl_org)) {
		$dl_org = strtok($Qdl_org,'#');
		$oActividad->setDl_org($dl_org);
	} else {	
		$oActividad->setDl_org('');
	}	
	$oActividad->setNom_activ($Qnom_activ);
	$oActividad->setId_ubi($Qid_ubi);
	$oActividad->setDesc_activ($Qdesc_activ);
	$oActividad->setF_ini($Qf_ini);
	$oActividad->setF_fin($Qf_fin);
	$oActividad->setTipo_horario($Qtipo_horario);
	$oActividad->setPrecio($Qprecio);
	$oActividad->setNum_asistentes($Qnum_asistentes);
	$oActividad->setStatus($Qstatus);
	$oActividad->setObserv($Qobserv);
	$oActividad->setNivel_stgr($Qnivel_stgr);
	$oActividad->setId_repeticion($Qid_repeticion);
	$oActividad->setObserv_material($Qobserv_material);
	$oActividad->setLugar_esp($Qlugar_esp);
	$oActividad->setTarifa($Qtarifa);
	$oActividad->setH_ini($Qh_ini);
	$oActividad->setH_fin($Qh_fin);
	$oActividad->setPlazas($Qplazas);
	if ($oActividad->DBGuardar() === false) { 
		echo _("hay un error, no se ha guardado");
	}
	break;
case "editar": // editar la actividad.
	$Qid_tipo_activ = (integer) \filter_input(INPUT_POST, 'id_tipo_activ');
	$Qid_ubi = (integer) \filter_input(INPUT_POST, 'id_ubi');
	$Qnum_asistentes = (integer) \filter_input(INPUT_POST, 'num_asistentes');
	$Qstatus = (integer) \filter_input(INPUT_POST, 'status');
	$Qid_repeticion = (integer) \filter_input(INPUT_POST, 'id_repeticion');
	$Qplazas = (integer) \filter_input(INPUT_POST, 'plazas');
	$Qtarifa = (integer) \filter_input(INPUT_POST, 'tarifa');
	$Qprecio = (float) \filter_input(INPUT_POST, 'precio');
	
	$Qdl_org = (string) \filter_input(INPUT_POST, 'dl_org');
	$Qnom_activ = (string) \filter_input(INPUT_POST, 'nom_activ');
	$Qlugar_esp = (string) \filter_input(INPUT_POST, 'lugar_esp');
	$Qdesc_activ = (string) \filter_input(INPUT_POST, 'desc_activ');
	$Qf_ini = (string) \filter_input(INPUT_POST, 'f_ini');
	$Qf_fin = (string) \filter_input(INPUT_POST, 'f_fin');
	$Qtipo_horario = (string) \filter_input(INPUT_POST, 'tipo_horario');
	$Qobserv = (string) \filter_input(INPUT_POST, 'observ');
	$Qnivel_stgr = (string) \filter_input(INPUT_POST, 'nivel_stgr');
	$Qobserv_material = (string) \filter_input(INPUT_POST, 'observ_material');
	$Qh_ini = (string) \filter_input(INPUT_POST, 'h_ini');
	$Qh_fin = (string) \filter_input(INPUT_POST, 'h_fin');
	$Qpublicado = (string) \filter_input(INPUT_POST, 'publicado');
	
	$oActividad = new actividades\Actividad($Qid_activ);
	$oActividad->DBCarregar();

	$oActividad->setId_tipo_activ($Qid_tipo_activ);
	if (isset($Qdl_org)) {
		$dl_orig=$oActividad->getDl_org();
		$dl_org = strtok($Qdl_org,'#');
		$oActividad->setDl_org($dl_org);
	} else {	
		$oActividad->setDl_org('');
	}	
	$oActividad->setNom_activ($Qnom_activ);
	
	// En el caso de tener id_ubi (!=1) borro el campo lugar_esp.
	if (!empty($Qid_ubi) && $Qid_ubi != 1 ) {
		$oActividad->setId_ubi($Qid_ubi);
		$oActividad->setLugar_esp('');
	} else {
		$oActividad->setId_ubi($Qid_ubi);
		$oActividad->setLugar_esp($Qlugar_esp);
	}
	$oActividad->setDesc_activ($Qdesc_activ);
	$oActividad->setF_ini($Qf_ini);
	$oActividad->setF_fin($Qf_fin);
	$oActividad->setTipo_horario($Qtipo_horario);
	$oActividad->setPrecio($Qprecio);
	$oActividad->setNum_asistentes($Qnum_asistentes);
	$oActividad->setStatus($Qstatus);
	$oActividad->setObserv($Qobserv);
	$oActividad->setNivel_stgr($Qnivel_stgr);
	$oActividad->setId_repeticion($Qid_repeticion);
	$oActividad->setObserv_material($Qobserv_material);
	$oActividad->setTarifa($Qtarifa);
	$oActividad->setH_ini($Qh_ini);
	$oActividad->setH_fin($Qh_fin);
	$oActividad->setPublicado($Qpublicado);
	$oActividad->setPlazas($Qplazas);
	if ($oActividad->DBGuardar() === false) { 
		echo '<br>'._("hay un error, no se ha guardado");
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
			  	  	    WHERE id_activ=".$Qid_activ." AND id_nom=".$_POST['sacd1_antiguo']." AND id_cargo='35'";
			// si es una actividad de sv le añado la asistencia
			if (substr($id_tipo_activ,0,1)==1) {
				quitar_asistencia($Qid_activ,$_POST['sacd1_antiguo']);
				poner_asistencia($Qid_activ,$_POST['sacd1']);
			}
	    }  else  {
	      $update_sacd="DELETE FROM d_cargos_activ
		                WHERE id_activ=".$Qid_activ." AND id_nom=".$_POST['sacd1_antiguo']." AND id_cargo='35'";  
		   // si es una actividad de sv le quito la asistencia
			if (substr($_POST['id_tipo_activ'],0,1)==1) {
				quitar_asistencia($Qid_activ,$_POST['sacd1_antiguo']);
			}
	    } 
	    $oDBSt_q=$oDB->query($update_sacd);
	  }
	} else {
	  if (!empty($_POST['sacd1'])) {
	    $update_sacd="INSERT INTO d_cargos_activ
		              (id_activ,id_cargo,id_nom)
					  VALUES ('".$Qid_activ."','35','".$_POST['sacd1']."')";
	    $oDBSt_q=$oDB->query($update_sacd);
		// si es una actividad de sv le añado la asistencia
		if (substr($_POST['id_tipo_activ'],0,1)==1) {
			poner_asistencia($Qid_activ,$_POST['sacd1']);
		}
	  }
	}  
	
	if (!empty($_POST['sacd2_antiguo']))	{
	  if ($_POST['sacd2']<>$_POST['sacd2_antiguo']) { 
	    if (!empty($_POST['sacd2'])){
	      $update_sacd="UPDATE d_cargos_activ
		                SET id_nom=".$_POST['sacd2']."
		  			    WHERE id_activ=".$Qid_activ." AND id_nom=".$_POST['sacd2_antiguo']." AND id_cargo='36'";
			// si es una actividad de sv le añado la asistencia
			if (substr($_POST['id_tipo_activ'],0,1)==1) {
				quitar_asistencia($Qid_activ,$_POST['sacd2_antiguo']);
				poner_asistencia($Qid_activ,$_POST['sacd2']);
			}
	    } else {
	      $update_sacd="DELETE FROM d_cargos_activ
		                WHERE id_activ=".$Qid_activ." AND id_nom=".$_POST['sacd2_antiguo']." AND id_cargo='36'"; 
		   // si es una actividad de sv le quito la asistencia
			if (substr($_POST['id_tipo_activ'],0,1)==1) {
				quitar_asistencia($Qid_activ,$_POST['sacd2_antiguo']);
			}
	    }
	    $oDBSt_q=$oDB->query($update_sacd);
	  }
	} else {
	  if (!empty($_POST['sacd2'])) {
	    $update_sacd="INSERT INTO d_cargos_activ
		              (id_activ,id_cargo,id_nom)
					  VALUES ('".$Qid_activ."','36','".$_POST['sacd2']."')";
	    $oDBSt_q=$oDB->query($update_sacd);
		// si es una actividad de sv le añado la asistencia
		if (substr($_POST['id_tipo_activ'],0,1)==1) {
			poner_asistencia($Qid_activ,$_POST['sacd2']);
		}
	  }
	}  
	
	if (!empty($_POST['sacd3_antiguo'])) {
	  if ($_POST['sacd3']<>$_POST['sacd3_antiguo']) { 
	    if (!empty($_POST['sacd3'])){
	      $update_sacd="UPDATE d_cargos_activ
		                SET id_nom=".$_POST['sacd3']."
			  		    WHERE id_activ=".$Qid_activ." AND id_nom=".$_POST['sacd3_antiguo']." AND id_cargo='37'";
			// si es una actividad de sv le añado la asistencia
			if (substr($_POST['id_tipo_activ'],0,1)==1) {
				quitar_asistencia($Qid_activ,$_POST['sacd3_antiguo']);
				poner_asistencia($Qid_activ,$_POST['sacd3']);
			}
	    } else {
	      $update_sacd="DELETE FROM d_cargos_activ
		                WHERE id_activ=".$Qid_activ." AND id_nom=".$_POST['sacd3_antiguo']." AND id_cargo='37'"; 
			// si es una actividad de sv le quito la asistencia
			if (substr($_POST['id_tipo_activ'],0,1)==1) {
				quitar_asistencia($Qid_activ,$_POST['sacd3_antiguo']);
			}
	    }
	    $oDBSt_q=$oDB->query($update_sacd);
	  }
	} else {
	  if (!empty($_POST['sacd3'])) {
	    $update_sacd="INSERT INTO d_cargos_activ
		              (id_activ,id_cargo,id_nom)
					  VALUES ('".$Qid_activ."','37','".$_POST['sacd3']."')";
	    $oDBSt_q=$oDB->query($update_sacd);
		// si es una actividad de sv le añado la asistencia
		if (substr($_POST['id_tipo_activ'],0,1)==1) {
			poner_asistencia($Qid_activ,$_POST['sacd3']);
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
							WHERE id_activ=".$Qid_activ." AND id_ubi=$ctr_antiguo AND num_orden='$i'";
				//echo "up: $update_ctr, $i<br>";
			} else { // si es nulo lo elimino
				$update_ctr="DELETE FROM d_encargados_activ
							WHERE id_activ=".$Qid_activ." AND id_ubi=$ctr_antiguo ";  
			} 
			$oDBSt_q=$oDB->exec($update_ctr);
		  }
		} else { //No hay centro antiguo
		  if ($_POST['ctr'][$i]) { // si hay nuevo: lo añado
			$update_ctr="INSERT INTO d_encargados_activ
						  (id_activ,num_orden,id_ubi,encargo)
						  VALUES (?,?,?,'organizador')";
			$a_values=array($Qid_activ,$i,$_POST['ctr'][$i]);
			//echo "q: $update_ctr<br>";
			//print_r($a_values);
			//ejecuta
			$oDBSt_q=$oDB->prepare($update_ctr);
			$oDBSt_q->execute($a_values);

		  }
		}  
	}	
} // fin del switch de mod.
