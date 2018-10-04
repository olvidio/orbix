<?php
namespace dbextern\model;

use core\ConfigGlobal;
use dbextern\model\entity\GestorIdMatchPersona;
use dbextern\model\entity\GestorPersonaListas;
use dbextern\model\entity\IdMatchPersona;
use dbextern\model\entity\PersonaListas;
use PDO;
use personas\model\entity\GestorPersonaDl;
use personas\model\entity\GestorTelecoPersonaDl;
use personas\model\entity\TelecoPersonaDl;
use personas\model\entity\TrasladoDl;

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
			$oGesListas = new GestorPersonaListas();	
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

		$oGesPersonasDl = new GestorPersonaDl();
		$cPersonasDl = $oGesPersonasDl->getPersonasDl($aWhere,$aOperador);
		if ($cPersonasDl !== false && count($cPersonasDl) == 1) {
			$oPersonaDl = $cPersonasDl[0];
			$id_nom = $oPersonaDl->getId_nom();

			$oIdMatch = new IdMatchPersona($id_nom_listas);
			$oIdMatch->setId_orbix($id_nom);
			$oIdMatch->setId_tabla($this->tipo_persona);
			
			if ($oIdMatch->DBGuardar() === false) {
				echo _("hay un error, no se ha guardado");
				print_r($oIdMatch);
				echo '<br>';
				return false;
			}
			return true;
		}
		return false;
	}
	
	public function posiblesOrbixOtrasDl($id_nom_listas) {
		// posibles esquemas
		/*
		 * @todo: filtrar por regiones?
		 */
		$oDBR = $GLOBALS['oDBR'];
		$qRs = $oDBR->query("SELECT DISTINCT schemaname FROM pg_stat_user_tables");
		$aResultSql = $qRs->fetchAll(\PDO::FETCH_ASSOC);
		$aEsquemas = $aResultSql;
		//Utilizo la conexión oDBR para cambiar momentáneamente el search_path.
		$oDBR = $GLOBALS['oDBR'];
		$qRs = $oDBR->query('SHOW search_path');
		$aPath = $qRs->fetch(\PDO::FETCH_ASSOC);
		$path_org = addslashes($aPath['search_path']);
		$a_posibles = [];
		foreach ($aEsquemas as $esquemaName) {
			$esquema = $esquemaName['schemaname'];
			//elimino el de H-H
			if (strpos($esquema, '-')) {
				$a_reg = explode('-',$esquema);
				$reg = $a_reg[0]; 
				$dl = substr($a_reg[1],0,-1); // quito la v o la f.
				if ($reg == $dl) { continue; }
			}
			//elimino public, publicv, global
			if ($esquema == 'global') { continue; }
			if ($esquema == 'public') { continue; }
			if ($esquema == 'publicv') { continue; }
			if ($esquema == 'restov') { continue; }
//			$esquema_slash = '"'.$esquema.'"';
//			$oDBR->exec("SET search_path TO public,$esquema_slash");
			// buscar en cada esquema
			$a_lista_orbix = $this->posiblesOrbix($id_nom_listas, $esquema);
			$a_posibles = array_merge($a_posibles, $a_lista_orbix);
		}
		return $a_posibles;
	}
	
	public function posiblesOrbix($id_nom_listas, $esquema='') {
		$oPersonaListas = new PersonaListas($id_nom_listas);	
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

		$oGesPersonasDl = new GestorPersonaDl();
		if (!empty($esquema)) {
			$oDB = $this->conexion($esquema);
			$oGesPersonasDl->setoDbl($oDB);
		}
		$cPersonasDl = $oGesPersonasDl->getPersonasDl($aWhere,$aOperador);
		$i = 0;
		$a_lista_orbix = array();
		foreach ($cPersonasDl as $oPersonaDl) {
			$id_nom = $oPersonaDl->getId_nom();
			$oGesMatch = new GestorIdMatchPersona();
			$cIdMatch = $oGesMatch->getIdMatchPersonas(array('id_orbix'=>$id_nom));
			if (!empty($cIdMatch[0]) AND count($cIdMatch) > 0) {
				continue;
			}
			$ape_nom = $oPersonaDl->getApellidosNombre();
			$nombre = $oPersonaDl->getNom();
			$dl_persona = $oPersonaDl->getDl();
			$apellido1 = $oPersonaDl->getApellido1();
			$apellido2 = $oPersonaDl->getApellido2();
			$f_nacimiento = empty($oPersonaDl->getF_nacimiento())? '??' : $oPersonaDl->getF_nacimiento();
			$a_lista_orbix[$i] = array('id_nom'=>$id_nom,
										'ape_nom'=>$ape_nom,
										'nombre'=>$nombre,
										'dl_persona'=>$dl_persona,
										'apellido1'=>$apellido1,
										'apellido2'=>$apellido2,
										'f_nacimiento'=>$f_nacimiento);
			$i++;
		}
		if (!empty($esquema)) {
			$this->restaurarConexion($oDB);
		}
		return $a_lista_orbix;
	}

	
	function syncro($oPersonaListas,$id_orbix) {
		$msg = '';
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
		//por alguna razón puede no exitir el centro en la lista
		if (!empty($a_ctr[$Ctr])) {
			$id_ubi = $a_ctr[$Ctr];
		} else {
			$id_ubi = 0;
			if (empty($Ctr)) {
				$msg = sprintf(_("parece que %s  no tiene puesto el ctr en \"listas\""),$ape_nom);
			} else {
				$msg = sprintf(_("no se encuentra el ctr %s en la lista de ctr"),$Ctr);
			}
		}
		
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
		$obj = 'personas\\model\\entity\\'.$obj_pau;
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
			exit(_("hay un error, no se ha guardado"));
		}

		//Dossiers
		$GesTeleco = new GestorTelecoPersonaDl();
		// Telf movil  --particular(5)
		if (!empty($Tfno_Movil)) {
			$cTelecos = $GesTeleco->getTelecos(array('id_nom'=>$id_orbix,'tipo_teleco'=>'móvil','desc_teleco'=>5));
			if (!empty($cTelecos) && count($cTelecos) > 0) {
				$oTeleco = $cTelecos[0];
				$oTeleco->setNum_teleco($Tfno_Movil);
				$oTeleco->setObserv('de listas');
			} else {
				$oTeleco = new TelecoPersonaDl();
				$oTeleco->setId_nom($id_orbix);
				$oTeleco->setTipo_teleco('móvil');
				$oTeleco->setDesc_teleco(5);
				$oTeleco->setNum_teleco($Tfno_Movil);
				$oTeleco->setObserv('de listas');
			}
			if ($oTeleco->DBGuardar() === false) {
				echo (_("hay un error, no se ha guardado"));
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
				$oTeleco = new TelecoPersonaDl();
				$oTeleco->setId_nom($id_orbix);
				$oTeleco->setTipo_teleco('e-mail');
				$oTeleco->setDesc_teleco(13);
				$oTeleco->setNum_teleco($Email);
				$oTeleco->setObserv('de listas');
			}
			if ($oTeleco->DBGuardar() === false) {
				echo (_("hay un error, no se ha guardado"));
			}
			
		}
		return $msg;
	}

	public function buscarEnOrbix($id_orbix) {
		$dl = '';
		$oTrasladoDl = new TrasladoDl();
		$a_esquemas = $oTrasladoDl->getEsquemas($id_orbix,$this->tipo_persona);
		$esquema = '';
		foreach ($a_esquemas as $info_eschema){
			// array(schemaName,id_schema,situacion,f_situacion)
			if ($info_eschema['situacion'] == 'A') {
				$esquema = $info_eschema['schemaname'];
			}
		}
		return $esquema;
	}
	
	public function conexion($esquema) {
		$sfsv_txt = (configGlobal::mi_sfsv() == 1)? 'v' :'f';
		//Utilizo la conexión oDBR para cambiar momentáneamente el search_path.
		if (ConfigGlobal::mi_region_dl() == $esquema) {
			//Utilizo la conexión oDB para cambiar momentáneamente el search_path.
			$oDB = $GLOBALS['oDB'];
		} else {
			// Sólo funciona con la conexión oDBR porque el usuario es orbixv que 
			// tiene permiso de lectura para todos los esquemas
			$oDB = $GLOBALS['oDBR'];
		}
		$qRs = $oDB->query('SHOW search_path');
		$aPath = $qRs->fetch(PDO::FETCH_ASSOC);
		$this->path_ini = $aPath['search_path'];
		$oDB->exec('SET search_path TO public,"'.$esquema.'"');
		return $oDB;
	}
	public function restaurarConexion($oDB) {
		// Volver oDBR a su estado original:
		$oDB->exec("SET search_path TO $this->path_ini");
	}
}
