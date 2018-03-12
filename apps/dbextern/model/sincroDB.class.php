<?php
namespace dbextern\model;

/**
 * Description of sincroDB
 *
 * @author Daniel Serrabou <dani@moneders.net>
 */
class sincroDB {

	private $tipo_persona; 	//'n', 'a', 's'.
	private $id_tipo; 		//1  ,  2,   3.
	
	private $cPersonasListas;
	
	private $dl;
	private $aCentros;



	public function getTipo_persona() {
		return $this->tipo_persona;
	}

	public function getId_tipo() {
		return $this->id_tipo;
	}

	public function setTipo_persona($tipo_persona) {
		$this->tipo_persona = $tipo_persona;
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
		$this->id_tipo = $id_tipo;
	}

	public function getDl() {
		return $this->dl;
	}

	public function setDl($dl) {
		$this->dl = $dl;
	}
	public function getCentros() {
		return $this->aCentros;
	}

	public function setCentros($aCentros) {
		$this->aCentros = $aCentros;
	}

		

	public function getPersonasListas() {
		if (empty($this->cPersonasListas)) {
			$Query = "SELECT * FROM dbo.q_dl_Estudios_b WHERE Dl='$this->dl' AND Identif LIKE '$this->id_tipo%'";
			// todos los de listas
			$oGesListas = new dbextern\model\GestorPersonaListas();	
			$cPersonasListas = $oGesListas->getPersonaListasQuery($Query);
			$this->cPersonasListas = $cPersonasListas;
		}
		return $this->cPersonasListas;
	}
		
	public function union_automatico($oPersonaListas) {
		$id_nom_listas = $oPersonaListas->getIdentif();
		$apellido1_sinprep = $oPersonaListas->getApellido1_sinprep();
		$apellido2_sinprep = $oPersonaListas->getApellido2_sinprep();
		$f_nacimiento = $oPersonaListas->getFecha_Naci();
		$nombre = $oPersonaListas->getNombre();
		
		$aWhere = array();
		$aOperador = array();
		$aWhere['id_tabla'] = $this->tipo_persona;
		$aWhere['apellido1'] = $apellido1_sinprep;
		$aWhere['apellido2'] = $apellido2_sinprep;
		$aWhere['f_nacimiento'] = "'$f_nacimiento'";
		$aWhere['nom'] = trim($nombre);

		$oGesPersonasDl = new personas\model\GestorPersonaDl();
		$cPersonasDl = $oGesPersonasDl->getPersonasDl($aWhere,$aOperador);
		if ($cPersonasDl !== false && count($cPersonasDl) == 1) {
			$oPersonaDl = $cPersonasDl[0];
			$id_nom = $oPersonaDl->getId_nom();

			$oIdMatch = new dbextern\model\IdMatchPersona($id_nom_listas);
			$oIdMatch->setId_orbix($id_nom);
			$oIdMatch->setId_tabla($this->tipo_persona);
			
			if ($oIdMatch->DBGuardar() === false) {
				echo _('Hay un error, no se ha guardado');
				print_r($oIdMatch);
				echo '<br>';
				return false;
			}
			return true;
		}
		return false;
	}
	
	public function posiblesOrbix($id_nom_listas) {
		$oPersonaListas = new dbextern\model\PersonaListas($id_nom_listas);	
		$oPersonaListas->DBCarregar();
		
		$apellido1_sinprep = $oPersonaListas->getApellido1_sinprep();
		// Si tiene más de una palabra cojo la primera
		$tokens = explode(' ', $apellido1_sinprep);
		$apellido1_sinprep_c = $tokens[0];
		$aWhere = array();
		$aOperador = array();
		$aWhere['id_tabla'] = $this->tipo_persona;
		$aWhere['situacion'] = 'A';
		$aWhere['apellido1'] = $apellido1_sinprep_c;
		$aOperador['apellido1'] = 'sin_acentos';
		$aWhere['_ordre'] = 'apellido1, apellido2, nom';

		$oGesPersonasDl = new personas\model\GestorPersonaDl();
		$cPersonasDl = $oGesPersonasDl->getPersonasDl($aWhere,$aOperador);
		$i = 0;
		$a_lista_orbix = array();
		foreach ($cPersonasDl as $oPersonaDl) {
			$id_nom = $oPersonaDl->getId_nom();
			$oGesMatch = new dbextern\model\GestorIdMatchPersona();
			$cIdMatch = $oGesMatch->getIdMatchPersonas(array('id_orbix'=>$id_nom));
			if (!empty($cIdMatch[0]) AND count($cIdMatch) > 0) {
				continue;
			}
			$ape_nom = $oPersonaDl->getApellidosNombre();
			$nombre = $oPersonaDl->getNom();
			$apellido1 = $oPersonaDl->getApellido1();
			$apellido2 = $oPersonaDl->getApellido2();
			$f_nacimiento = empty($oPersonaDl->getF_nacimiento())? '??' : $oPersonaDl->getF_nacimiento();
			$a_lista_orbix[$i] = array('id_nom'=>$id_nom,
										'ape_nom'=>$ape_nom,
										'nombre'=>$nombre,
										'apellido1'=>$apellido1,
										'apellido2'=>$apellido2,
										'f_nacimiento'=>$f_nacimiento);
			$i++;
		}
		return $a_lista_orbix;
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
}
