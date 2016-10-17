<?php
use dossiers\model as dossiers;
use personas\model as personas;
/**
* Para asegurar que inicia la sesion, y poder acceder a los permisos
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$error = '';

$id_pau  = empty($_POST['id_pau'])? '' : $_POST['id_pau'];
$oPersonaDl = new personas\PersonaDl($id_pau);
$oPersonaDl->DBCarregar();

//centro
$new_ctr  = empty($_POST['new_ctr'])? '' : $_POST['new_ctr'];
$f_ctr  = empty($_POST['f_ctr'])? '' : $_POST['f_ctr'];

if (!empty($new_ctr) AND !empty($f_ctr)){
	$id_ctr_o  = empty($_POST['id_ctr_o'])? '' : $_POST['id_ctr_o'];
	$ctr_o  = empty($_POST['ctr_o'])? '' : $_POST['ctr_o'];

	$id_new_ctr=strtok($new_ctr,"#");
	$nom_new_ctr=strtok("#");

	$oPersonaDl->setId_ctr($id_new_ctr);
	// ?? $oPersonaDl->setF_ctr($f_ctr);
	if ($oPersonaDl->DBGuardar() === false) {
		$error .= '<br>'._('Hay un error, no se ha guardado');
	}

  	//para el dossier de traslados
 	$oTraslado = new personas\Traslado();
	$oTraslado->setId_nom($id_pau);
	$oTraslado->setF_traslado($f_ctr);
	$oTraslado->setTipo_cmb('sede');
	$oTraslado->setId_ctr_origen($id_ctr_o);
	$oTraslado->setCtr_origen($ctr_o);
	$oTraslado->setId_ctr_destino($id_new_ctr);
	$oTraslado->setCtr_destino($nom_new_ctr);
	if ($oTraslado->DBGuardar() === false) {
		$error .= '<br>'._('Hay un error, no se ha guardado');
	}
}

//cambio de dl
$old_dl = $oPersonaDl->getDl();
$new_dl  = empty($_POST['new_dl'])? '' : $_POST['new_dl'];
$f_dl  = empty($_POST['f_dl'])? '' : $_POST['f_dl'];
$situacion  = empty($_POST['situacion'])? '' : $_POST['situacion'];
if (!empty($new_dl) AND !empty($f_dl)){
	$a_reg = explode('-',$new_dl);
	$dl = $a_reg[1];
	if (!empty($dl) AND $dl == $old_dl) {
		exit (_("Ya esta trasladado. No se ha hecho ningún cambio."));
	}
	$dl_o  = empty($_POST['dl'])? '' : $_POST['dl'];
	// Aviso si le faltan notas
	$gesMatriculas = new actividadestudios\model\gestorMatriculaDl();
	$cMatriculasPendientes = $gesMatriculas->getMatriculasPendientes($id_pau);
	$msg = '';
	foreach ($cMatriculasPendientes as $oMatricula) {
		$id_activ = $oMatricula->getId_activ();
		$id_asignatura = $oMatricula->getId_asignatura();
		$oActividad = new actividades\model\ActividadAll($id_activ);
		$nom_activ = $oActividad->getNom_activ();
		$oAsignatura = new asignaturas\model\Asignatura($id_asignatura);
		$nombre_corto=$oAsignatura->getNombre_corto();
		$msg .= empty($msg)? '' : '<br>';
		$msg .= sprintf(_("ca: %s, asignatura: %s"),$nom_activ,$nombre_corto);
	}
	if (!empty($msg)) {
		$error .= _("Tiene pendiente de poner las notas de:") .'<br>'.$msg;
	}
	// Trasladar persona
	// Cambio la situación de la persona. Debo hacerlo lo primero, pues no puedo tener la misma persona en dos dl en la misma situación
	$oPersonaDl->setSituacion($situacion);
	$oPersonaDl->setF_situacion($f_dl);
	$oPersonaDl->setDl($dl);
	if ($oPersonaDl->DBGuardar() === false) {
		$error .= '<br>'._('Hay un error, no se ha guardado');
	}
	if ($situacion == 'A') $error .= '<br>'. _("OJO: Debería cambiar el campo situación");

	$oDbl = $GLOBALS['oDB'];
	$sfsv_txt = (core\configGlobal::mi_sfsv() == 1)? 'v' :'f';
	$new_esquema = $new_dl.$sfsv_txt;
	// Copiar los datos a la dl destino si existe en orbix.
	if (($qRs = $oDbl->query("SELECT EXISTS(SELECT 1 FROM pg_namespace WHERE nspname = '$new_esquema') AS existe")) === false) {
			$sClauError = 'Controller.Traslados';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
	$aDades = $qRs->fetch(\PDO::FETCH_ASSOC);
	// si existe el esquema (dl)
	if (!empty($aDades['existe'])) {
		$qRs = $oDBR->query('SHOW search_path');
		$aPath = $qRs->fetch(\PDO::FETCH_ASSOC);
		$path_org = addslashes($aPath['search_path']);
		$oDBR->exec("SET search_path TO public,\"$new_esquema\"");
		//$oDBR->exec("SET DATESTYLE TO '".ConfigGlexecobal::$datestyle."'");
		$id_tabla = $oPersonaDl->getId_tabla();
		switch ($id_tabla) {
			case 'n':
				$obj = 'personas\model\PersonaN';
				break;
			case 'a':
				$obj = 'personas\model\PersonaAgd';
				break;
			case 's':
				$obj = 'personas\model\PersonaS';
				break;
			case 'sssc':
				$obj = 'personas\model\PersonaSSSC';
				break;
			case 'x':
				$obj = 'personas\model\PersonaNax';
				break;
		}
		$oPersona = new $obj($id_pau);
		$oPersona->DBCarregar();
		$oPersonaNew = clone $oPersona;
		$oPersonaNew->setoDbl($oDBR);
		$oPersonaNew->setDl($dl);
		$oPersonaNew->setSituacion('A');
		$oPersonaNew->setF_situacion($f_dl);
		$oPersonaNew->setId_ctr('');
		if ($oPersonaNew->DBGuardar() === false) {
			$error .= '<br>'._('Hay un error, no se ha guardado');
		}
		// Todos los dossiers

		//$GesDossiers = new dossiers\GestorDossier(array('tabla'=>'p','id_pau'=>$id_pau,'status_dossier'=>'t'));
		$GesDossiers = new dossiers\GestorDossier();
		// Comprobar que estan apuntados.
		$GesDossiers->comprobarDossiersAbiertos('p',$id_pau);

		$cDossiers = $GesDossiers->getDossiers(array('tabla'=>'p','id_pau'=>$id_pau));
		foreach ($cDossiers as $oDossier) {
			$id_tipo_dossier = $oDossier->getId_tipo_dossier();
			$oTipoDossier = new dossiers\TipoDossier($id_tipo_dossier);
			$app = $oTipoDossier->getApp();
			$class = $oTipoDossier->getClass();
			if (empty($class)) continue;
			$colection = array();
			switch ($class) {
				case 'TelecoPersonaDl':
					$gestor = "$app\model\GestorTelecoPersonaDl";
					$ges = new $gestor();
					$colection = $ges->getTelecos(array('id_nom'=>$id_pau));
					break;
				case 'Profesor':
					$gestor = "$app\model\Gestor$class";
					$ges = new $gestor();
					$colection = $ges->getProfesores(array('id_nom'=>$id_pau));
					break;
				case 'ProfesorAmpliacion':
					$gestor = "$app\model\Gestor$class";
					$ges = new $gestor();
					$colection = $ges->getProfesorAmpliaciones(array('id_nom'=>$id_pau));
					break;
				case 'ProfesorCongreso':
					$gestor = "$app\model\Gestor$class";
					$ges = new $gestor();
					$colection = $ges->getProfesorCongresos(array('id_nom'=>$id_pau));
					break;
				case 'ProfesorDirector':
					$gestor = "$app\model\Gestor$class";
					$ges = new $gestor();
					$colection = $ges->getProfesoresDirectores(array('id_nom'=>$id_pau));
					break;
				case 'ProfesorDocenciaStgr':
					$gestor = "$app\model\Gestor$class";
					$ges = new $gestor();
					$colection = $ges->getProfesorDocenciasStgr(array('id_nom'=>$id_pau));
					break;
				case 'ProfesorJuramento':
					$gestor = "$app\model\Gestor$class";
					$ges = new $gestor();
					$colection = $ges->getProfesorJuramentos(array('id_nom'=>$id_pau));
					break;
				case 'ProfesorLatin':
					$gestor = "$app\model\Gestor$class";
					$ges = new $gestor();
					$colection = $ges->getProfesoresLatin(array('id_nom'=>$id_pau));
					break;
				case 'ProfesorPublicacion':
					$gestor = "$app\model\Gestor$class";
					$ges = new $gestor();
					$colection = $ges->getProfesorPublicaciones(array('id_nom'=>$id_pau));
					break;
				case 'ProfesorTituloEst':
					$gestor = "$app\model\Gestor$class";
					$ges = new $gestor();
					$colection = $ges->getTitulosEst(array('id_nom'=>$id_pau));
					break;
				case 'PersonaNotaDl':
					// No cal fer res. Les notes són visibles per tothom.
					/*
					$gestor = "$app\model\Gestor$class";
					$ges = new $gestor();
					$colection = $ges->getPersonaNotas(array('id_nom'=>$id_pau));
					*/
					break;
				case 'MatriculaDl':
					/*
					$gestor = "$app\model\Gestor$class";
					$ges = new $gestor();
					$colection = $ges->getMatriculas(array('id_nom'=>$id_pau));
					*/
					break;
				case 'Traslado':
					$gestor = "$app\model\Gestor$class";
					$ges = new $gestor();
					$colection = $ges->getTraslados(array('id_nom'=>$id_pau));
					break;
				case 'AsistenteDl':
					// Los Out pasan a Dl si la dl destino es la que organiza.
					$gestor = "$app\model\GestorAsistenteOut";
					$ges = new $gestor();
					$colection = $ges->getAsistentesOut(array('id_nom'=>$id_pau));
					foreach ($colection as $oAsistenteOut) {
						$oAsistenteOut->DBCarregar();
						$id_activ = $oAsistenteOut->getId_activ();
						$oActividad = new actividades\model\ActividadAll($id_activ);
						// si es de la sf quito la 'f'
						$dl_org = preg_replace('/f$/', '', $oActividad->getDl_org());
						if ($dl_org == $dl) {
							$oAsistenteDl = new asistentes\model\AsistenteDl();
							$oAsistenteDl = copiar($oAsistenteOut,$oAsistenteDl); 
							$oAsistenteDl->setoDbl($oDBR);
							$oAsistenteDl->DBGuardar();
						} else{
							$NuevoObj = clone $oAsistenteOut;
							if (method_exists($NuevoObj,'getId_item') === true) $NuevoObj->setId_item('');
							$NuevoObj->setoDbl($oDBR);
							$NuevoObj->setTraslado('t');
							$NuevoObj->DBGuardar();
						}
					}
					// Los Dl pasan a Out
					$gestor = "$app\model\GestorAsistenteDl";
					$ges = new $gestor();
					$colection = $ges->getAsistentesDl(array('id_nom'=>$id_pau));
					foreach ($colection as $oAsistenteDl) {
						$oAsistenteDl->DBCarregar();
						$oAsistenteOut = new asistentes\model\AsistenteOut();
						$oAsistenteOut = copiar($oAsistenteDl,$oAsistenteOut); 
						$oAsistenteOut->setoDbl($oDBR);
						$oAsistenteOut->setTraslado('t');
						$oAsistenteOut->DBGuardar();
					}
					// Los Ex no deberían existir, son gente de otras dl, no afecta al traslado
					// reseteo la variable.
					$colection = array();
					break;
				case 'AsistenteCargo':
					// De momento no lo traslado.
					/*
					$gestor = "$app\model\Gestor$class";
					$ges = new $gestor();
					$colection = $ges->getActividadCargos(array('id_nom'=>$id_pau));
					*/
					break;
			}
			if (!empty($colection)) {
				foreach ($colection as $Objeto) {
					$Objeto->DBCarregar();
					//print_r($Objeto);
					$NuevoObj = clone $Objeto;
					if (method_exists($NuevoObj,'getId_item') === true) $NuevoObj->setId_item('');
					$NuevoObj->setoDbl($oDBR);
					$NuevoObj->DBGuardar();
				}
			}
			// también copia el estado del dossier
			$NuevoObj = clone $oDossier;
			$NuevoObj->setoDbl($oDBR);
			$NuevoObj->DBGuardar();
		}
		// Volver oDBR a su estado original:
		$oDBR->exec("SET search_path TO $path_org");
	}
	// apunto el traslado.
 	$oTraslado = new personas\Traslado();
	$oTraslado->setId_nom($id_pau);
	$oTraslado->setF_traslado($f_dl);
	$oTraslado->setTipo_cmb('dl');
	$oTraslado->setId_ctr_origen('');
	$oTraslado->setCtr_origen($dl_o);
	$oTraslado->setId_ctr_destino('');
	$oTraslado->setCtr_destino($dl);
	if ($oTraslado->DBGuardar() === false) {
		$error .= '<br>'._('Hay un error, no se ha guardado');
	}
}


// hay que abrir el dossier para esta persona/actividad/ubi, si no tiene.
$oDossier = new dossiers\Dossier(array('tabla'=>'p','id_pau'=>$id_pau,'id_tipo_dossier'=>1004));
$oDossier->abrir(); // ya pone la fecha de hoy.
$oDossier->DBGuardar();

if (empty($error)) {
	$oPosicion->setId_div('ir_a');
	echo $oPosicion->atras();
} else {
	echo $error;
}

function copiar($oOrigen, $oDestino) {
	$oDestino->setId_activ($oOrigen->getId_activ()); 
	$oDestino->setId_nom($oOrigen->getId_nom()); 
	$oDestino->setPropio($oOrigen->getPropio()); 
	$oDestino->setEst_ok($oOrigen->getEst_ok()); 
	$oDestino->setCfi($oOrigen->getCfi());    
	$oDestino->setCfi_con($oOrigen->getCfi_con()); 
	$oDestino->setFalta($oOrigen->getFalta()); 
	$oDestino->setEncargo($oOrigen->getEncargo()); 
	$oDestino->setCama($oOrigen->getCama()); 
	$oDestino->setObserv($oOrigen->getObserv()); 
	return $oDestino;
}
?>
