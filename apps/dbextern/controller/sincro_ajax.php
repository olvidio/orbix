<?php
use personas;
/**
* En el fichero config tenemos las variables genéricas del sistema
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$sfsv = core\ConfigGlobal::mi_sfsv();

$que = (string)  filter_input(INPUT_POST, 'que');

switch ($que) {
	// 4 casos:
	//	está en listas(midl), esta en orbix(otradl), está unido (si-no)
	//	está en listas(midl), y NO esta en orbix, está unido (si-no)
	case "crear":
		$id_nom_listas = (integer)  filter_input(INPUT_POST, 'id_nom_listas');
		$id = (integer)  filter_input(INPUT_POST, 'id');
		$tipo_persona = (string)  filter_input(INPUT_POST, 'tipo_persona');
		
		$Query = "SELECT * FROM dbo.q_dl_Estudios_b WHERE Identif = $id_nom_listas";
		$oGesListas = new dbextern\model\GestorPersonaListas();	
		$cPersonasListas = $oGesListas->getPersonaListasQuery($Query);
		if ($cPersonasListas !== FALSE && count($cPersonasListas) == 1)  {
			$oPersonaListas = $cPersonasListas[0];
			$id_nom_listas = $oPersonaListas->getIdentif();
			$ape_nom = $oPersonaListas->getApeNom();
			$nombre = $oPersonaListas->getNombre();
			$apellido1 = $oPersonaListas->getApellido1();
			$nx1 = $oPersonaListas->getNx1();
			$apellido1_sinprep = $oPersonaListas->getApellido1_sinprep();
			$nx2 = $oPersonaListas->getNx2();
			$apellido2 = $oPersonaListas->getApellido2();
			$apellido2_sinprep = $oPersonaListas->getApellido2_sinprep();
			$f_nacimiento = $oPersonaListas->getFecha_Naci();

			$id_tipo_persona = substr($id_nom_listas, 0, 1);
			switch ($id_tipo_persona){
				case '3': // supernumerarios
					$obj_pau = 'PersonaS';
				break;
				case '1': // numerarios
					$obj_pau = 'PersonaN';
				break;
				case '2': // agregados
					$obj_pau = 'PersonaAgd';
				break;
				case "p_nax":
					$obj_pau = 'PersonaNax';
				break;
			}
			// Buscar si está en orbix (otras dl)
			// a) si ya está unida; b) si está sin unir.
			$oGesMatch = new dbextern\model\GestorIdMatchPersona();
			$cIdMatch = $oGesMatch->getIdMatchPersonas(array('id_listas'=>$id_nom_listas));
			if (!empty($cIdMatch[0]) AND count($cIdMatch) > 0) { // (a) unida
				$id_orbix = $cIdMatch[0]->getId_orbix();
				$oTrasladoDl = new personas\model\trasladoDl();
				$aaa = $oTrasladoDl->getEsquemas($id_orbix);
				
			} else { //(b) mala suerte!
				
			}
			
			$obj = 'personas\\model\\'.$obj_pau;
			$oPersona = new $obj();
		
			$oPersona->setSituacion('A');
			$oPersona->setF_situacion(date("d/m/Y"));
			$oPersona->setNom($nombre);
			$oPersona->setNx1($nx1);
			$oPersona->setApellido1($apellido1_sinprep);
			$oPersona->setNx2($nx2);
			$oPersona->setApellido2($apellido2_sinprep);
			$oPersona->setF_nacimiento($f_nacimiento);

			if ($oPersona->DBGuardar() === false) {
				exit(_('Hay un error, no se ha guardado'));
			}
			$id_orbix = $oPersona->getId_nom();
		} else {
			echo "Error";
		}
		
		// Empalmo con lo de unir:
	case 'unir':
		if ($que != 'crear') {
			$id_orbix = (integer)  filter_input(INPUT_POST, 'id_orbix');
			$id_nom_listas = (integer)  filter_input(INPUT_POST, 'id_nom_listas');
			$id = (integer)  filter_input(INPUT_POST, 'id');
			$tipo_persona = (string)  filter_input(INPUT_POST, 'tipo_persona');
		}
		$oIdMatch = new dbextern\model\IdMatchPersona($id_nom_listas);
		$oIdMatch->setId_orbix($id_orbix);
		$oIdMatch->setId_tabla($tipo_persona);
		
		if ($oIdMatch->DBGuardar() === false) {
			echo _('Hay un error, no se ha guardado');
		}
		//Elimino el valor del array
		unset($_SESSION['DBListas'][$id]);
		array_filter($_SESSION['DBListas']);
		// reindexo el array
		$_SESSION['DBListas'] = array_values($_SESSION['DBListas']);
		
		break;
	case 'syncro':
		$dl = (string)  filter_input(INPUT_POST, 'dl');
		$tipo_persona = (string)  filter_input(INPUT_POST, 'tipo_persona');
		
		$id_tipo = 0;
		switch ($tipo_persona) {
			case 'n':
				if ($_SESSION['oPerm']->have_perm("sm")) {
					$id_tipo = 1;
				}
				break;
			case 'a':
				if ($_SESSION['oPerm']->have_perm("agd")) {
					$id_tipo = 2;
				}
				break;
			case 's':
				if ($_SESSION['oPerm']->have_perm("sg")) {
					$id_tipo = 3;
				}
				break;
		}
		// prepara lista de ctr
		$GesCentros = new \ubis\model\GestorCentroDl();
		$cCentros = $GesCentros->getCentros(array('tipo_ctr'=>'^'.$tipo_persona),array('tipo_ctr'=>'~'));
		$a_centros = array();
		foreach ($cCentros as $oCentro) {
			$id_ubi = $oCentro->getId_ubi();
			$ctr = $oCentro->getNombre_ubi();
			$a_centros[$ctr] = $id_ubi;
		}

		
		$Query = "SELECT * FROM dbo.q_dl_Estudios_b WHERE Dl='$dl' AND Identif LIKE '$id_tipo%'";
		// todos los de listas
		$oGesListas = new dbextern\model\GestorPersonaListas();	
		$cPersonasListas = $oGesListas->getPersonaListasQuery($Query);
		$i = 0;
		foreach ($cPersonasListas as $oPersonaListas) {
			$id_nom_listas = $oPersonaListas->getIdentif();

			$oGesMatch = new dbextern\model\GestorIdMatchPersona();
			$cIdMatch = $oGesMatch->getIdMatchPersonas(array('id_listas'=>$id_nom_listas));
			if (!empty($cIdMatch[0]) AND count($cIdMatch) > 0) {
				$id_orbix = $cIdMatch[0]->getId_orbix();
				syncro($oPersonaListas,$id_orbix);
			} else {
				continue;
			}
		}
		break;
}


function syncro($oPersonaListas,$id_orbix) {
	$a_ctr = $GLOBALS['a_centros'];
	
	$id_nom_listas = $oPersonaListas->getIdentif();
	$ape_nom = $oPersonaListas->getApeNom();
	$nombre = $oPersonaListas->getNombre();
	$apellido1 = $oPersonaListas->getApellido1();
	$nx1 = $oPersonaListas->getNx1();
	$apellido1_sinprep = $oPersonaListas->getApellido1_sinprep();
	$nx2 = $oPersonaListas->getNx2();
	$apellido2 = $oPersonaListas->getApellido2();
	$apellido2_sinprep = $oPersonaListas->getApellido2_sinprep();
	$f_nacimiento = $oPersonaListas->getFecha_Naci();
	$lugar_nacimiento = $oPersonaListas->getLugar_Naci();

	$dl = $oPersonaListas->getDl();
	$Ctr = $oPersonaListas->getCtr();
	$id_ubi = $a_ctr[$Ctr];
	
	$Email = $oPersonaListas->getEmail();
	$Tfno_Movil = $oPersonaListas->getTfno_Movil();
	
	$ce_num = $oPersonaListas->getCe_num();
	$ce_lugar = $oPersonaListas->getCe_lugar();
	$ce_ini = $oPersonaListas->getCe_ini();
	$ce_fin = $oPersonaListas->getCe_fin();

		
	$id_tipo_persona = substr($id_nom_listas, 0, 1);
	switch ($id_tipo_persona){
		case '3': // supernumerarios
			$obj_pau = 'PersonaS';
		break;
		case '1': // numerarios
			$obj_pau = 'PersonaN';
		break;
		case '2': // agregados
			$obj_pau = 'PersonaAgd';
		break;
		case "p_nax":
			$obj_pau = 'PersonaNax';
		break;
	}
	$obj = 'personas\\model\\'.$obj_pau;
	$oPersona = new $obj($id_orbix);

	$oPersona->DBCarregar();
	//Las personas en listas siempre están en situación 'A'
	if ($oPersona->getSituacion() != 'A') {
		$oPersona->setSituacion('A');
		$oPersona->setF_situacion(date("d/m/Y"));
	}
	$oPersona->setNom($nombre);
	$oPersona->setNx1($nx1);
	$oPersona->setApellido1($apellido1_sinprep);
	$oPersona->setNx2($nx2);
	$oPersona->setApellido2($apellido2_sinprep);
	$oPersona->setF_nacimiento($f_nacimiento);
	$oPersona->setLugar_nacimiento($lugar_nacimiento);
	$oPersona->setCe($ce_num);
	$oPersona->setCe_lugar($ce_lugar);
	$oPersona->setCe_ini($ce_ini);
	$oPersona->setCe_fin($ce_fin);

	if ($dl == 'Hcr') { 
		$oPersona->setDl('cr');
	} else {
		$oPersona->setDl('dl'.$dl);
	}
	$oPersona->setId_ctr($id_ubi);

		
	if ($oPersona->DBGuardar() === false) {
		exit(_('Hay un error, no se ha guardado'));
	}

	//Dossiers
	$GesTeleco = new personas\model\GestorTelecoPersonaDl();
	// Telf movil  --particular(5)
	if (!empty($Tfno_Movil)) {
		$cTelecos = $GesTeleco->getTelecos(array('id_nom'=>$id_orbix,'tipo_teleco'=>'móvil','desc_teleco'=>5));
		if (!empty($cTelecos) && count($cTelecos) > 0) {
			$oTeleco = $cTelecos[0];
			$oTeleco->setNum_teleco($Tfno_Movil);
			$oTeleco->setObserv('de listas');
		} else {
			$oTeleco = new personas\model\TelecoPersonaDl();
			$oTeleco->setId_nom($id_orbix);
			$oTeleco->setTipo_teleco('móvil');
			$oTeleco->setDesc_teleco(5);
			$oTeleco->setNum_teleco($Tfno_Movil);
			$oTeleco->setObserv('de listas');
		}
		if ($oTeleco->DBGuardar() === false) {
			echo (_('Hay un error, no se ha guardado'));
		}
	}
	// e-mail   --principal(13)
	if (!empty($Email)) {
		$cTelecos = $GesTeleco->getTelecos(array('id_nom'=>$id_orbix,'tipo_teleco'=>'e-mail','desc_teleco'=>13));
		if (!empty($cTelecos) && count($cTelecos) > 0) {
			$oTeleco = $cTelecos[0];
			$oTeleco->setNum_teleco($Email);
			$oTeleco->setObserv('de listas');
		} else {
			$oTeleco = new personas\model\TelecoPersonaDl();
			$oTeleco->setId_nom($id_orbix);
			$oTeleco->setTipo_teleco('e-mail');
			$oTeleco->setDesc_teleco(13);
			$oTeleco->setNum_teleco($Email);
			$oTeleco->setObserv('de listas');
		}
		if ($oTeleco->DBGuardar() === false) {
			echo (_('Hay un error, no se ha guardado'));
		}
		
	}
}