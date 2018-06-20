<?php
namespace personas\model\entity;
use core;
use ubis\model\entity as ubis;
/**
 * Fitxer amb la Classe que accedeix a la taula personas_dl
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */
/**
 * Classe que implementa l'entitat personas_dl
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */
abstract class PersonaGlobal Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de PersonaDl
	 *
	 * @var array
	 */
	 protected $aPrimary_key;

	/**
	 * aDades de PersonaDl
	 *
	 * @var array
	 */
	 protected $aDades;

	/**
	 * Id_schema de PersonaDl
	 *
	 * @var integer
	 */
	 protected $iid_schema;
	/**
	 * Id_nom de PersonaDl
	 *
	 * @var integer
	 */
	 protected $iid_nom;
	/**
	 * Id_cr de PersonaDl
	 *
	 * @var integer
	 */
	 protected $iid_cr;
	/**
	 * Id_tabla de PersonaDl
	 *
	 * @var string
	 */
	 protected $sid_tabla;
	/**
	 * Dl de PersonaDl
	 *
	 * @var string
	 */
	 protected $sdl;
	/**
	 * Sacd de PersonaDl
	 *
	 * @var boolean
	 */
	 protected $bsacd;
	/**
	 * Trato de PersonaDl
	 *
	 * @var string
	 */
	 protected $strato;
	/**
	 * Nom de PersonaDl
	 *
	 * @var string
	 */
	 protected $snom;
	/**
	 * Nx1 de PersonaDl
	 *
	 * @var string
	 */
	 protected $snx1;
	/**
	 * Apellido1 de PersonaDl
	 *
	 * @var string
	 */
	 protected $sapellido1;
	/**
	 * Nx2 de PersonaDl
	 *
	 * @var string
	 */
	 protected $snx2;
	/**
	 * Apellido2 de PersonaDl
	 *
	 * @var string
	 */
	 protected $sapellido2;
	/**
	 * F_nacimiento de PersonaDl
	 *
	 * @var date
	 */
	 protected $df_nacimiento;
	/**
	 * Lengua de PersonaDl
	 *
	 * @var string
	 */
	 protected $slengua;
	/**
	 * Situacion de PersonaDl
	 *
	 * @var string
	 */
	 protected $ssituacion;
	/**
	 * F_situacion de PersonaDl
	 *
	 * @var date
	 */
	 protected $df_situacion;
	/**
	 * Apel_fam de PersonaDl
	 *
	 * @var string
	 */
	 protected $sapel_fam;
	/**
	 * Inc de PersonaDl
	 *
	 * @var string
	 */
	 protected $sinc;
	/**
	 * F_inc de PersonaDl
	 *
	 * @var date
	 */
	 protected $df_inc;
	/**
	 * Stgr de PersonaDl
	 *
	 * @var string
	 */
	 protected $sstgr;
	/**
	 * Profesion de PersonaDl
	 *
	 * @var string
	 */
	 protected $sprofesion;
	/**
	 * Eap de PersonaDl
	 *
	 * @var string
	 */
	 protected $seap;
	/**
	 * Observ de PersonaDl
	 *
	 * @var string
	 */
	 protected $sobserv;
	 /**
	 * Lugar_nacimiento de PersonaDl
	 *
	 * @var string
	 */
	 protected $slugar_nacimiento;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
 	 /**
	 * ApellidosNombre de Persona
	 *
	 * @var string
	 */
	 protected $sApellidosNombre;
	 /**
	 * ApellidosNombreCr1_05 de Persona
	 *
	 * @var string
	 */
	 protected $sApellidosNombreCr1_05;
	 /**
	 * NombreApellidos de Persona
	 *
	 * @var string
	 */
	 protected $sNombreApellidos;
	 /**
	 * NombreApellidosCrSin de Persona
	 *
	 * @var string
	 */
	 protected $sNombreApellidosCrSin;
	 /**
	 * Apellidosde Persona
	 *
	 * @var string
	 */
	 protected $sApellidos;
	 /**
	 * Nombre en latin del porfesor, para las actas
	 *
	 * @var string
	 */
	 protected $sTituloNombreLatin;

	/**
	 * Centro_o_dl de Persona
	 *
	 * @var string
	 */
	 protected $sCentro_o_dl;

	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 *
	function __construct($a_id='') {
	}
	 */

	/* METODES PUBLICS ----------------------------------------------------------*/
	/* METODES ALTRES  ----------------------------------------------------------*/
	/* METODES PRIVATS ----------------------------------------------------------*/


	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de PersonaDl en un array
	 *
	 * @return array aDades
	 */
	function getTot() {
		if (!is_array($this->aDades)) {
			$this->DBCarregar('tot');
		}
		return $this->aDades;
	}

	/**
	 * Recupera las claus primàries de PersonaDl en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('iid_nom' => $this->iid_nom);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Recupera l'atribut iid_nom de PersonaDl
	 *
	 * @return integer iid_nom
	 */
	function getId_nom() {
		if (!isset($this->iid_nom)) {
			$this->DBCarregar();
		}
		return $this->iid_nom;
	}
	/**
	 * estableix el valor de l'atribut iid_nom de PersonaDl
	 *
	 * @param integer iid_nom
	 */
	function setId_nom($iid_nom) {
		$this->iid_nom = $iid_nom;
	}
	/**
	 * Recupera l'atribut iid_cr de PersonaDl
	 *
	 * @return integer iid_cr
	 */
	function getId_cr() {
		if (!isset($this->iid_cr)) {
			$this->DBCarregar();
		}
		return $this->iid_cr;
	}
	/**
	 * estableix el valor de l'atribut iid_cr de PersonaDl
	 *
	 * @param integer iid_cr='' optional
	 */
	function setId_cr($iid_cr='') {
		$this->iid_cr = $iid_cr;
	}
	/**
	 * Recupera l'atribut sid_tabla de PersonaDl
	 *
	 * @return string sid_tabla
	 */
	function getId_tabla() {
		if (!isset($this->sid_tabla)) {
			$this->DBCarregar();
		}
		return $this->sid_tabla;
	}
	/**
	 * estableix el valor de l'atribut sid_tabla de PersonaDl
	 *
	 * @param string sid_tabla='' optional
	 */
	function setId_tabla($sid_tabla='') {
		$this->sid_tabla = $sid_tabla;
	}
	/**
	 * Recupera l'atribut sdl de PersonaDl
	 *
	 * @return string sdl
	 */
	function getDl() {
		if (!isset($this->sdl)) {
			$this->DBCarregar();
		}
		return $this->sdl;
	}
	/**
	 * estableix el valor de l'atribut sdl de PersonaDl
	 *
	 * @param string sdl='' optional
	 */
	function setDl($sdl='') {
		$this->sdl = $sdl;
	}
	/**
	 * Recupera l'atribut bsacd de PersonaDl
	 *
	 * @return boolean bsacd
	 */
	function getSacd() {
		if (!isset($this->bsacd)) {
			$this->DBCarregar();
		}
		return $this->bsacd;
	}
	/**
	 * estableix el valor de l'atribut bsacd de PersonaDl
	 *
	 * @param boolean bsacd='f' optional
	 */
	function setSacd($bsacd='f') {
		$this->bsacd = $bsacd;
	}
	/**
	 * Recupera l'atribut strato de PersonaDl
	 *
	 * @return string strato
	 */
	function getTrato() {
		if (!isset($this->strato)) {
			$this->DBCarregar();
		}
		return $this->strato;
	}
	/**
	 * estableix el valor de l'atribut strato de PersonaDl
	 *
	 * @param string strato='' optional
	 */
	function setTrato($strato='') {
		$this->strato = $strato;
	}
	/**
	 * Recupera l'atribut snom de PersonaDl
	 *
	 * @return string snom
	 */
	function getNom() {
		if (!isset($this->snom)) {
			$this->DBCarregar();
		}
		return $this->snom;
	}
	/**
	 * estableix el valor de l'atribut snom de PersonaDl
	 *
	 * @param string snom='' optional
	 */
	function setNom($snom='') {
		$this->snom = $snom;
	}
	/**
	 * Recupera l'atribut snx1 de PersonaDl
	 *
	 * @return string snx1
	 */
	function getNx1() {
		if (!isset($this->snx1)) {
			$this->DBCarregar();
		}
		return $this->snx1;
	}
	/**
	 * estableix el valor de l'atribut snx1 de PersonaDl
	 *
	 * @param string snx1='' optional
	 */
	function setNx1($snx1='') {
		$this->snx1 = $snx1;
	}
	/**
	 * Recupera l'atribut sapellido1 de PersonaDl
	 *
	 * @return string sapellido1
	 */
	function getApellido1() {
		if (!isset($this->sapellido1)) {
			$this->DBCarregar();
		}
		return $this->sapellido1;
	}
	/**
	 * estableix el valor de l'atribut sapellido1 de PersonaDl
	 *
	 * @param string sapellido1='' optional
	 */
	function setApellido1($sapellido1='') {
		$this->sapellido1 = $sapellido1;
	}
	/**
	 * Recupera l'atribut snx2 de PersonaDl
	 *
	 * @return string snx2
	 */
	function getNx2() {
		if (!isset($this->snx2)) {
			$this->DBCarregar();
		}
		return $this->snx2;
	}
	/**
	 * estableix el valor de l'atribut snx2 de PersonaDl
	 *
	 * @param string snx2='' optional
	 */
	function setNx2($snx2='') {
		$this->snx2 = $snx2;
	}
	/**
	 * Recupera l'atribut sapellido2 de PersonaDl
	 *
	 * @return string sapellido2
	 */
	function getApellido2() {
		if (!isset($this->sapellido2)) {
			$this->DBCarregar();
		}
		return $this->sapellido2;
	}
	/**
	 * estableix el valor de l'atribut sapellido2 de PersonaDl
	 *
	 * @param string sapellido2='' optional
	 */
	function setApellido2($sapellido2='') {
		$this->sapellido2 = $sapellido2;
	}
	/**
	 * Recupera l'atribut df_nacimiento de PersonaDl
	 *
	 * @return date df_nacimiento
	 */
	function getF_nacimiento() {
		if (!isset($this->df_nacimiento)) {
			$this->DBCarregar();
		}
		return $this->df_nacimiento;
	}
	/**
	 * estableix el valor de l'atribut df_nacimiento de PersonaDl
	 *
	 * @param date df_nacimiento='' optional
	 */
	function setF_nacimiento($df_nacimiento='') {
		$this->df_nacimiento = $df_nacimiento;
	}
	/**
	 * Recupera l'atribut slengua de PersonaDl
	 *
	 * @return string slengua
	 */
	function getLengua() {
		if (!isset($this->slengua)) {
			$this->DBCarregar();
		}
		return $this->slengua;
	}
	/**
	 * estableix el valor de l'atribut slengua de PersonaDl
	 *
	 * @param string slengua='' optional
	 */
	function setLengua($slengua='') {
		$this->slengua = $slengua;
	}
	/**
	 * Recupera l'atribut ssituacion de PersonaDl
	 *
	 * @return string ssituacion
	 */
	function getSituacion() {
		if (!isset($this->ssituacion)) {
			$this->DBCarregar();
		}
		return $this->ssituacion;
	}
	/**
	 * estableix el valor de l'atribut ssituacion de PersonaDl
	 *
	 * @param string ssituacion='' optional
	 */
	function setSituacion($ssituacion='') {
		$this->ssituacion = $ssituacion;
	}
	/**
	 * Recupera l'atribut df_situacion de PersonaDl
	 *
	 * @return date df_situacion
	 */
	function getF_situacion() {
		if (!isset($this->df_situacion)) {
			$this->DBCarregar();
		}
		return $this->df_situacion;
	}
	/**
	 * estableix el valor de l'atribut df_situacion de PersonaDl
	 *
	 * @param date df_situacion='' optional
	 */
	function setF_situacion($df_situacion='') {
		$this->df_situacion = $df_situacion;
	}
	/**
	 * Recupera l'atribut sapel_fam de PersonaDl
	 *
	 * @return string sapel_fam
	 */
	function getApel_fam() {
		if (!isset($this->sapel_fam)) {
			$this->DBCarregar();
		}
		return $this->sapel_fam;
	}
	/**
	 * estableix el valor de l'atribut sapel_fam de PersonaDl
	 *
	 * @param string sapel_fam='' optional
	 */
	function setApel_fam($sapel_fam='') {
		$this->sapel_fam = $sapel_fam;
	}
	/**
	 * Recupera l'atribut sinc de PersonaDl
	 *
	 * @return string sinc
	 */
	function getInc() {
		if (!isset($this->sinc)) {
			$this->DBCarregar();
		}
		return $this->sinc;
	}
	/**
	 * estableix el valor de l'atribut sinc de PersonaDl
	 *
	 * @param string sinc='' optional
	 */
	function setInc($sinc='') {
		$this->sinc = $sinc;
	}
	/**
	 * Recupera l'atribut df_inc de PersonaDl
	 *
	 * @return date df_inc
	 */
	function getF_inc() {
		if (!isset($this->df_inc)) {
			$this->DBCarregar();
		}
		return $this->df_inc;
	}
	/**
	 * estableix el valor de l'atribut df_inc de PersonaDl
	 *
	 * @param date df_inc='' optional
	 */
	function setF_inc($df_inc='') {
		$this->df_inc = $df_inc;
	}
	/**
	 * Recupera l'atribut sstgr de PersonaDl
	 *
	 * @return string sstgr
	 */
	function getStgr() {
		if (!isset($this->sstgr)) {
			$this->DBCarregar();
		}
		return $this->sstgr;
	}
	/**
	 * estableix el valor de l'atribut sstgr de PersonaDl
	 *
	 * @param string sstgr='' optional
	 */
	function setStgr($sstgr='') {
		$this->sstgr = $sstgr;
	}
	/**
	 * Recupera l'atribut sprofesion de PersonaDl
	 *
	 * @return string sprofesion
	 */
	function getProfesion() {
		if (!isset($this->sprofesion)) {
			$this->DBCarregar();
		}
		return $this->sprofesion;
	}
	/**
	 * estableix el valor de l'atribut sprofesion de PersonaDl
	 *
	 * @param string sprofesion='' optional
	 */
	function setProfesion($sprofesion='') {
		$this->sprofesion = $sprofesion;
	}
	/**
	 * Recupera l'atribut seap de PersonaDl
	 *
	 * @return string seap
	 */
	function getEap() {
		if (!isset($this->seap)) {
			$this->DBCarregar();
		}
		return $this->seap;
	}
	/**
	 * estableix el valor de l'atribut seap de PersonaDl
	 *
	 * @param string seap='' optional
	 */
	function setEap($seap='') {
		$this->seap = $seap;
	}
	/**
	 * Recupera l'atribut sobserv de PersonaDl
	 *
	 * @return string sobserv
	 */
	function getObserv() {
		if (!isset($this->sobserv)) {
			$this->DBCarregar();
		}
		return $this->sobserv;
	}
	/**
	 * estableix el valor de l'atribut sobserv de PersonaDl
	 *
	 * @param string sobserv='' optional
	 */
	function setObserv($sobserv='') {
		$this->sobserv = $sobserv;
	}
	/**
	 * Recupera l'atribut slugar_nacimiento de PersonaDl
	 *
	 * @return integer slugar_nacimiento
	 */
	function getLugar_nacimiento() {
		if (!isset($this->slugar_nacimiento)) {
			$this->DBCarregar();
		}
		return $this->slugar_nacimiento;
	}
	/**
	 * estableix el valor de l'atribut slugar_nacimiento de PersonaDl
	 *
	 * @param integer slugar_nacimiento='' optional
	 */
	function setLugar_nacimiento($slugar_nacimiento='') {
		$this->slugar_nacimiento = $slugar_nacimiento;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Recupera l'atribut sApellidos de Persona
	 *
	 * @return string sApellidos
	 */
	function getApellidos() {
		if (!isset($this->sApellidos)) {
			$this->DBCarregar();
			$ap_nom= !empty($this->snx1)? $this->snx1.' ' : '';
			$ap_nom.= $this->sapellido1;
			$ap_nom.= !empty($this->snx2)? ' '.$this->snx2.' ' : ' ';
			$ap_nom.= !empty($this->sapellido2)? $this->sapellido2 : '';

			$this->sApellidos=$ap_nom;
		}
		return $this->sApellidos;
	}
	/**
	 * Recupera l'atribut sApellidosNombre de Persona
	 *
	 * @return string sApellidosNombre
	 */
	function getApellidosNombre() {
		if (!isset($this->sApellidosNombre)) {
			$this->DBCarregar();
			if (empty($this->sapellido1)) {
				$ap_nom = '';
			} else {
				$ap_nom=$this->sapellido1;
				$ap_nom.= !empty($this->snx2)? ' '.$this->snx2.' ' : ' ';
				$ap_nom.= !empty($this->sapellido2)? $this->sapellido2 : '';
				$ap_nom.= ', ';
				$ap_nom.= !empty($this->strato)? $this->strato.' ' : ' ';
				$ap_nom.= !empty($this->sapel_fam)? $this->sapel_fam : $this->snom;
				$ap_nom.= !empty($this->snx1)? ' '.$this->snx1 : '';
			}
			$this->sApellidosNombre=$ap_nom;
		}
		return $this->sApellidosNombre;
	}
	/**
	 * estableix el valor de l'atribut sApellidosNombre de Persona
	 *
	 * @param string sApellidosNombre
	 */
	function setApellidosNombre($sApellidosNombre) {
		$this->sApellidosNombre = $sApellidosNombre;
	}
	/**
	 * Recupera l'atribut sApellidosNombreCr1_05 de Persona (según cr 1/05).
	 *
	 * @return string sApellidosNombreCr1_05
	 */
	function getApellidosNombreCr1_05() {
		if (!isset($this->sApellidosNombreCr1_05)) {
			$this->DBCarregar();
			$ap_nom= !empty($this->snx1)? $this->snx1.' ' : '';
			$ap_nom.= $this->sapellido1;
			$ap_nom.= !empty($this->snx2)? ' '.$this->snx2.' ' : '';
			$ap_nom.= !empty($this->sapellido2)? ' ' .$this->sapellido2 : '';
			$ap_nom.= ', ';
			$ap_nom.= $this->snom;

			$this->sApellidosNombreCr1_05=$ap_nom;
		}
		return $this->sApellidosNombreCr1_05;
	}
	/**
	 * estableix el valor de l'atribut sApellidosNombreCr1_05 de Persona
	 *
	 * @param string sApellidosNombreCr1_05
	 */
	function setApellidosNombreCr1_05($sApellidosNombreCr1_05) {
		$this->sApellidosNombreCr1_05 = $sApellidosNombreCr1_05;
	}
	/**
	 * Recupera l'atribut sNombreApellidos de Persona
	 *
	 * @return string sNombreApellidos
	 */
	public function getNombreApellidos() {
		if (!isset($this->sNombreApellidos)) {
			$this->DBCarregar();
			$nom_ap = !empty($this->strato)? $this->strato.' ' : '';
			$nom_ap.= !empty($this->sapel_fam)? $this->sapel_fam : $this->snom;
			$nom_ap.= !empty($this->snx1)? ' '.$this->snx1 : '';
			$nom_ap.= ' '.$this->sapellido1;
			$nom_ap.= !empty($this->snx2)? ' '.$this->snx2.' ' : ' ';
			$nom_ap.= !empty($this->sapellido2)? $this->sapellido2 : '';

			$this->sNombreApellidos=$nom_ap;
		}
		return $this->sNombreApellidos;
	}
	/**
	 * Recupera l'atribut sNombreApellidosCrSin de Persona
	 * el nombre más los nexos más los apellidos, sin el tratamiento (para des).
	 *
	 * @return string sNombreApellidosCrSin
	 */
	function getNombreApellidosCrSin() {
		if (!isset($this->sNombreApellidosCrSin)) {
			$this->DBCarregar();
			$nom_ap = $this->snom;
			$nom_ap.= !empty($this->snx1)? ' '.$this->snx1 : '';
			$nom_ap.= ' '.$this->sapellido1;
			$nom_ap.= !empty($this->snx2)? ' '.$this->snx2.' ' : ' ';
			$nom_ap.= !empty($this->sapellido2)? $this->sapellido2 : '';

			$this->sNombreApellidosCrSin=$nom_ap;
		}
		return $this->sNombreApellidosCrSin;
	}
	/**
	 * Recupera l'atribut sTituloNombreLatin de Persona
	 * el nombre más los nexos más los apellidos (para des actas).
	 *
	 * @return string sTituloNombreLatin
	 */
	function getTituloNombreLatin() {
		if (!isset($this->sTituloNombreLatin)) {
			$this->DBCarregar();
			$oGesNomLatin = new GestorNombreLatin();
			$nom_ap = 'Dnus. Dr. ';
			$nom_ap .= $oGesNomLatin->getVernaculaLatin($this->snom); 
			$nom_ap .= !empty($this->snx1)? ' '.$this->snx1 : '';
			$nom_ap .= ' '.$this->sapellido1;
			$nom_ap .= !empty($this->snx2)? ' '.$this->snx2.' ' : ' ';
			$nom_ap .= !empty($this->sapellido2)? $this->sapellido2 : '';

			$this->sTituloNombreLatin=$nom_ap;
		}
		return $this->sTituloNombreLatin;
	}
	/**
	 * Recupera l'atribut sCentro_o_dl de Persona
	 *
	 * @return string sCentro_o_dl
	 */
	function getCentro_o_dl() {
		if (!isset($this->sCentro_o_dl)) {
			$classname = get_class($this);
			if (preg_match('@\\\\([\w]+)$@', $classname, $matches)) {
        		$classname = $matches[1];
    		}
			switch($classname) {
				case 'PersonaEx':
				case 'PersonaIn':
					$ctr = $this->getDl();
					break;
				case 'PersonaDl':
					if (core\ConfigGlobal::mi_dele() === core\ConfigGlobal::mi_region()) {
						$oCentroDl = new ubis\Centro($this->getId_ctr());
					} else {
						$oCentroDl = new ubis\CentroDl($this->getId_ctr());
					}
					$ctr = $oCentroDl->getNombre_ubi();
					break;
			}
			$this->sCentro_o_dl=$ctr;
		}
		return $this->sCentro_o_dl;
	}
	/**
	 * estableix el valor de l'atribut sCentro_o_dl de Persona
	 *
	 * @param string sCentro_o_dl
	 */
	function setCentro_o_dl($sCentro_o_dl) {
		$this->sCentro_o_dl = $sCentro_o_dl;
	}

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oPersonaDlSet = new core\Set();

		$oPersonaDlSet->add($this->getDatosId_cr());
		$oPersonaDlSet->add($this->getDatosId_tabla());
		$oPersonaDlSet->add($this->getDatosDl());
		$oPersonaDlSet->add($this->getDatosSacd());
		$oPersonaDlSet->add($this->getDatosTrato());
		$oPersonaDlSet->add($this->getDatosNom());
		$oPersonaDlSet->add($this->getDatosNx1());
		$oPersonaDlSet->add($this->getDatosApellido1());
		$oPersonaDlSet->add($this->getDatosNx2());
		$oPersonaDlSet->add($this->getDatosApellido2());
		$oPersonaDlSet->add($this->getDatosF_nacimiento());
		$oPersonaDlSet->add($this->getDatosLengua());
		$oPersonaDlSet->add($this->getDatosSituacion());
		$oPersonaDlSet->add($this->getDatosF_situacion());
		$oPersonaDlSet->add($this->getDatosApel_fam());
		$oPersonaDlSet->add($this->getDatosInc());
		$oPersonaDlSet->add($this->getDatosF_inc());
		$oPersonaDlSet->add($this->getDatosStgr());
		$oPersonaDlSet->add($this->getDatosEdad());
		$oPersonaDlSet->add($this->getDatosProfesion());
		$oPersonaDlSet->add($this->getDatosEap());
		$oPersonaDlSet->add($this->getDatosObserv());
		return $oPersonaDlSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut iid_cr de PersonaDl
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosId_cr() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_cr'));
		$oDatosCampo->setEtiqueta(_("id_cr"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sid_tabla de PersonaDl
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosId_tabla() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_tabla'));
		$oDatosCampo->setEtiqueta(_("id_tabla"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sdl de PersonaDl
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosDl() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'dl'));
		$oDatosCampo->setEtiqueta(_("dl"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut bsacd de PersonaDl
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosSacd() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'sacd'));
		$oDatosCampo->setEtiqueta(_("sacd"));
		$oDatosCampo->setTipo('check');
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut strato de PersonaDl
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosTrato() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'trato'));
		$oDatosCampo->setEtiqueta(_("trato"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut snom de PersonaDl
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosNom() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'nom'));
		$oDatosCampo->setEtiqueta(_("nom"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut snx1 de PersonaDl
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosNx1() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'nx1'));
		$oDatosCampo->setEtiqueta(_("nx1"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sapellido1 de PersonaDl
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosApellido1() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'apellido1'));
		$oDatosCampo->setEtiqueta(_("apellido1"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut snx2 de PersonaDl
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosNx2() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'nx2'));
		$oDatosCampo->setEtiqueta(_("nx2"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sapellido2 de PersonaDl
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosApellido2() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'apellido2'));
		$oDatosCampo->setEtiqueta(_("apellido2"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut df_nacimiento de PersonaDl
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosF_nacimiento() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'f_nacimiento'));
		$oDatosCampo->setEtiqueta(_("fecha nacimiento"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut slengua de PersonaDl
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosLengua() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'lengua'));
		$oDatosCampo->setEtiqueta(_("lengua"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut ssituacion de PersonaDl
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosSituacion() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'situacion'));
		$oDatosCampo->setEtiqueta(_("situacion"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut df_situacion de PersonaDl
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosF_situacion() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'f_situacion'));
		$oDatosCampo->setEtiqueta(_("f_situacion"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sapel_fam de PersonaDl
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosApel_fam() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'apel_fam'));
		$oDatosCampo->setEtiqueta(_("apelativo familiar"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sinc de PersonaDl
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosInc() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'inc'));
		$oDatosCampo->setEtiqueta(_("inc"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut df_inc de PersonaDl
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosF_inc() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'f_inc'));
		$oDatosCampo->setEtiqueta(_("f_inc"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sstgr de PersonaDl
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosStgr() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'stgr'));
		$oDatosCampo->setEtiqueta(_("stgr"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sprofesion de PersonaDl
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosProfesion() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'profesion'));
		$oDatosCampo->setEtiqueta(_("profesion"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut seap de PersonaDl
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosEap() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'eap'));
		$oDatosCampo->setEtiqueta(_("eap"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sobserv de PersonaDl
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosObserv() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'observ'));
		$oDatosCampo->setEtiqueta(_("observ"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut slugar_nacimiento de PersonaDl
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosLugar_nacimiento() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'lugar_nacimiento'));
		$oDatosCampo->setEtiqueta(_("lugar nacimiento"));
		return $oDatosCampo;
	}
}
?>
