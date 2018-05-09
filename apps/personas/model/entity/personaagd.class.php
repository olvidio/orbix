<?php
namespace personas\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula p_agregados
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */
/**
 * Classe que implementa l'entitat p_agregados
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */
class PersonaAgd Extends PersonaDl {
	/* ATRIBUTS ----------------------------------------------------------------- */
	/**
	 * ce de PersonaAgd
	 *
	 * @var integer
	 */
	 protected $ice;
	/**
	 * ce_ini de PersonaAgd
	 *
	 * @var integer
	 */
	 protected $ice_ini;
	/**
	 * ce_fin de PersonaAgd
	 *
	 * @var integer
	 */
	 protected $ice_fin;
	/**
	 * ce_lugar de PersonaAgd
	 *
	 * @var string
	 */
	 protected $sce_lugar;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 * Si només necessita un valor, se li pot passar un integer.
	 * En general se li passa un array amb les claus primàries.
	 *
	 * @param integer|array iid_nom
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDB'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_nom') && $val_id !== '') $this->iid_nom = (int)$val_id; // evitem SQL injection fent cast a integer
			}	} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_nom = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_nom' => $this->iid_nom);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('p_agregados');
	}

	/* METODES PUBLICS ----------------------------------------------------------*/

	/**
	 * Desa els atributs de l'objecte a la base de dades.
	 * Si no hi ha el registre, fa el insert, si hi es fa el update.
	 *
	 */
	public function DBGuardar() {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		if ($this->DBCarregar('guardar') === false) { $bInsert=true; } else { $bInsert=false; }
		$aDades=array();
		$aDades['id_cr'] = $this->iid_cr;
		//$aDades['id_tabla'] = $this->sid_tabla;
		$aDades['dl'] = $this->sdl;
		$aDades['sacd'] = $this->bsacd;
		$aDades['trato'] = $this->strato;
		$aDades['nom'] = $this->snom;
		$aDades['nx1'] = $this->snx1;
		$aDades['apellido1'] = $this->sapellido1;
		$aDades['nx2'] = $this->snx2;
		$aDades['apellido2'] = $this->sapellido2;
		$aDades['f_nacimiento'] = $this->df_nacimiento;
		$aDades['lengua'] = $this->slengua;
		$aDades['situacion'] = $this->ssituacion;
		$aDades['f_situacion'] = $this->df_situacion;
		$aDades['apel_fam'] = $this->sapel_fam;
		$aDades['inc'] = $this->sinc;
		$aDades['f_inc'] = $this->df_inc;
		$aDades['stgr'] = $this->sstgr;
		$aDades['profesion'] = $this->sprofesion;
		$aDades['eap'] = $this->seap;
		$aDades['observ'] = $this->sobserv;
		$aDades['id_ctr'] = $this->iid_ctr;
		$aDades['lugar_nacimiento'] = $this->slugar_nacimiento;
		$aDades['ce'] = $this->ice;
		$aDades['ce_ini'] = $this->ice_ini;
		$aDades['ce_fin'] = $this->ice_fin;
		$aDades['ce_lugar'] = $this->sce_lugar;
		array_walk($aDades, 'core\poner_null');
		//para el caso de los boolean false, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		if (empty($aDades['sacd']) || ($aDades['sacd'] === 'off') || ($aDades['sacd'] === false) || ($aDades['sacd'] === 'false') || ($aDades['sacd'] === 'f')) { $aDades['sacd']='f'; } else { $aDades['sacd']='t'; }
		if ($bInsert === false) {
			//UPDATE
					//id_tabla                 = :id_tabla,
			$update="
					id_cr                    = :id_cr,
					dl                       = :dl,
					sacd                     = :sacd,
					trato                    = :trato,
					nom                      = :nom,
					nx1                      = :nx1,
					apellido1                = :apellido1,
					nx2                      = :nx2,
					apellido2                = :apellido2,
					f_nacimiento             = :f_nacimiento,
					lengua                   = :lengua,
					situacion                = :situacion,
					f_situacion              = :f_situacion,
					apel_fam                 = :apel_fam,
					inc                      = :inc,
					f_inc                    = :f_inc,
					stgr                     = :stgr,
					profesion                = :profesion,
					eap                      = :eap,
					observ                   = :observ,
					id_ctr                   = :id_ctr,
					lugar_nacimiento         = :lugar_nacimiento,
					ce         				 = :ce,
					ce_ini         			 = :ce_ini,
					ce_fin         			 = :ce_fin,
					ce_lugar         		 = :ce_lugar";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_nom='$this->iid_nom'")) === false) {
				$sClauError = 'PersonaAgd.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'PersonaAgd.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
			$this->setAllAtributes($aDades);
		} else {
			// INSERT
				$campos="id_cr,dl,sacd,trato,nom,nx1,apellido1,nx2,apellido2,f_nacimiento,lengua,situacion,f_situacion,apel_fam,inc,f_inc,stgr,profesion,eap,observ,id_ctr,lugar_nacimiento,ce,ce_ini,ce_fin,ce_lugar";
			$valores=":id_cr,:dl,:sacd,:trato,:nom,:nx1,:apellido1,:nx2,:apellido2,:f_nacimiento,:lengua,:situacion,:f_situacion,:apel_fam,:inc,:f_inc,:stgr,:profesion,:eap,:observ,:id_ctr,:lugar_nacimiento,:ce,:ce_ini,:ce_fin,:ce_lugar";
			if (empty($this->iid_nom)) {
				$campos="($campos)";
				$valores="($valores)";
			} else {
				array_unshift($aDades, $this->iid_nom);
				$campos="(id_nom,$campos)";
				$valores="(:id_nom,$valores)";
			}
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'PersonaAgd.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'PersonaAgd.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
			$id_auto = $oDbl->lastInsertId($nom_tabla.'_id_auto_seq');
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_auto=$id_auto")) === false) {
				$sClauError = get_class($this).'.carregar.Last';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
				return false;
			}
			$aDadesLast = $oDblSt->fetch(\PDO::FETCH_ASSOC);
			$this->aDades=$aDadesLast;
			$this->setAllAtributes($aDadesLast);
		}
		return true;
	}

	/**
	 * Carrega els camps de la base de dades com atributs de l'objecte.
	 *
	 */
	public function DBCarregar($que=null) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		if (isset($this->iid_nom)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_nom='$this->iid_nom'")) === false) {
				$sClauError = 'PersonaAgd.carregar';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
				return false;
			}
			$aDades = $oDblSt->fetch(\PDO::FETCH_ASSOC);
			switch ($que) {
				case 'tot':
					$this->aDades=$aDades;
					break;
				case 'guardar':
					if (!$oDblSt->rowCount()) return false;
					break;
				default:
					$this->setAllAtributes($aDades);
			}
			return true;
		} else {
		   	return false;
		}
	}

	/**
	 * Elimina el registre de la base de dades corresponent a l'objecte.
	 *
	 */
	public function DBEliminar() {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_nom='$this->iid_nom'")) === false) {
			$sClauError = 'PersonaAgd.eliminar';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
			return false;
		}
		return true;
	}
	
	/* METODES ALTRES  ----------------------------------------------------------*/
	/* METODES PRIVATS ----------------------------------------------------------*/

	/**
	 * Estableix el valor de tots els atributs
	 *
	 * @param array $aDades
	 */
	function setAllAtributes($aDades) {
		if (!is_array($aDades)) return;
		if (array_key_exists('id_schema',$aDades)) $this->setId_schema($aDades['id_schema']);
		if (array_key_exists('id_nom',$aDades)) $this->setId_nom($aDades['id_nom']);
		if (array_key_exists('id_cr',$aDades)) $this->setId_cr($aDades['id_cr']);
		if (array_key_exists('id_tabla',$aDades)) $this->setId_tabla($aDades['id_tabla']);
		if (array_key_exists('dl',$aDades)) $this->setDl($aDades['dl']);
		if (array_key_exists('sacd',$aDades)) $this->setSacd($aDades['sacd']);
		if (array_key_exists('trato',$aDades)) $this->setTrato($aDades['trato']);
		if (array_key_exists('nom',$aDades)) $this->setNom($aDades['nom']);
		if (array_key_exists('nx1',$aDades)) $this->setNx1($aDades['nx1']);
		if (array_key_exists('apellido1',$aDades)) $this->setApellido1($aDades['apellido1']);
		if (array_key_exists('nx2',$aDades)) $this->setNx2($aDades['nx2']);
		if (array_key_exists('apellido2',$aDades)) $this->setApellido2($aDades['apellido2']);
		if (array_key_exists('f_nacimiento',$aDades)) $this->setF_nacimiento($aDades['f_nacimiento']);
		if (array_key_exists('lengua',$aDades)) $this->setLengua($aDades['lengua']);
		if (array_key_exists('situacion',$aDades)) $this->setSituacion($aDades['situacion']);
		if (array_key_exists('f_situacion',$aDades)) $this->setF_situacion($aDades['f_situacion']);
		if (array_key_exists('apel_fam',$aDades)) $this->setApel_fam($aDades['apel_fam']);
		if (array_key_exists('inc',$aDades)) $this->setInc($aDades['inc']);
		if (array_key_exists('f_inc',$aDades)) $this->setF_inc($aDades['f_inc']);
		if (array_key_exists('stgr',$aDades)) $this->setStgr($aDades['stgr']);
		if (array_key_exists('profesion',$aDades)) $this->setProfesion($aDades['profesion']);
		if (array_key_exists('eap',$aDades)) $this->setEap($aDades['eap']);
		if (array_key_exists('observ',$aDades)) $this->setObserv($aDades['observ']);
		if (array_key_exists('id_ctr',$aDades)) $this->setId_ctr($aDades['id_ctr']);
		if (array_key_exists('lugar_nacimiento',$aDades)) $this->setLugar_nacimiento($aDades['lugar_nacimiento']);
		if (array_key_exists('ce',$aDades)) $this->setCe($aDades['ce']);
		if (array_key_exists('ce_ini',$aDades)) $this->setCe_ini($aDades['ce_ini']);
		if (array_key_exists('ce_fin',$aDades)) $this->setCe_fin($aDades['ce_fin']);
		if (array_key_exists('ce_lugar',$aDades)) $this->setCe_lugar($aDades['ce_lugar']);
	}

	/* METODES GET i SET --------------------------------------------------------*/
	/**
	 * Recupera l'atribut ice de PersonaAgd
	 *
	 * @return integer ice
	 */
	function getCe() {
		if (!isset($this->ice)) {
			$this->DBCarregar();
		}
		return $this->ice;
	}
	/**
	 * estableix el valor de l'atribut ice de PersonaAgd
	 *
	 * @param integer ice='' optional
	 */
	function setCe($ice='') {
		$this->ice = $ice;
	}
	/**
	 * Recupera l'atribut ice_ini de PersonaAgd
	 *
	 * @return integer ice_ini
	 */
	function getCe_ini() {
		if (!isset($this->ice_ini)) {
			$this->DBCarregar();
		}
		return $this->ice_ini;
	}
	/**
	 * estableix el valor de l'atribut ice_ini de PersonaAgd
	 *
	 * @param integer ice_ini='' optional
	 */
	function setCe_ini($ice_ini='') {
		$this->ice_ini = $ice_ini;
	}
	/**
	 * Recupera l'atribut ice_fin de PersonaAgd
	 *
	 * @return integer ice_fin
	 */
	function getCe_fin() {
		if (!isset($this->ice_fin)) {
			$this->DBCarregar();
		}
		return $this->ice_fin;
	}
	/**
	 * estableix el valor de l'atribut ice_fin de PersonaAgd
	 *
	 * @param integer ice_fin='' optional
	 */
	function setCe_fin($ice_fin='') {
		$this->ice_fin = $ice_fin;
	}
	/**
	 * Recupera l'atribut sce_lugar de PersonaAgd
	 *
	 * @return integer sce_lugar
	 */
	function getCe_lugar() {
		if (!isset($this->sce_lugar)) {
			$this->DBCarregar();
		}
		return $this->sce_lugar;
	}
	/**
	 * estableix el valor de l'atribut sce_lugar de PersonaAgd
	 *
	 * @param integer sce_lugar='' optional
	 */
	function setCe_lugar($sce_lugar='') {
		$this->sce_lugar = $sce_lugar;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oPersonaAgdSet = new core\Set();

		$oPersonaAgdSet->add($this->getDatosId_cr());
		$oPersonaAgdSet->add($this->getDatosId_tabla());
		$oPersonaAgdSet->add($this->getDatosDl());
		$oPersonaAgdSet->add($this->getDatosSacd());
		$oPersonaAgdSet->add($this->getDatosTrato());
		$oPersonaAgdSet->add($this->getDatosNom());
		$oPersonaAgdSet->add($this->getDatosNx1());
		$oPersonaAgdSet->add($this->getDatosApellido1());
		$oPersonaAgdSet->add($this->getDatosNx2());
		$oPersonaAgdSet->add($this->getDatosApellido2());
		$oPersonaAgdSet->add($this->getDatosF_nacimiento());
		$oPersonaAgdSet->add($this->getDatosLengua());
		$oPersonaAgdSet->add($this->getDatosSituacion());
		$oPersonaAgdSet->add($this->getDatosF_situacion());
		$oPersonaAgdSet->add($this->getDatosApel_fam());
		$oPersonaAgdSet->add($this->getDatosInc());
		$oPersonaAgdSet->add($this->getDatosF_inc());
		$oPersonaAgdSet->add($this->getDatosStgr());
		$oPersonaAgdSet->add($this->getDatosProfesion());
		$oPersonaAgdSet->add($this->getDatosEap());
		$oPersonaAgdSet->add($this->getDatosObserv());
		$oPersonaAgdSet->add($this->getDatosId_ctr());
		$oPersonaAgdSet->add($this->getDatosLugar_nacimiento());
		$oPersonaAgdSet->add($this->getDatosCe());
		$oPersonaAgdSet->add($this->getDatosCe_ini());
		$oPersonaAgdSet->add($this->getDatosCe_fin());
		$oPersonaAgdSet->add($this->getDatosCe_lugar());
		return $oPersonaAgdSet->getTot();
	}

	/**
	 * Recupera les propietats de l'atribut ice de PersonaAgd
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosCe() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'ce'));
		$oDatosCampo->setEtiqueta(_("ce"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut ice_ini de PersonaAgd
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosCe_ini() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'ce_ini'));
		$oDatosCampo->setEtiqueta(_("ce_ini"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut ice_fin de PersonaAgd
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosCe_fin() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'ce_fin'));
		$oDatosCampo->setEtiqueta(_("ce_fin"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sce_lugar de PersonaAgd
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosCe_lugar() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'ce_lugar'));
		$oDatosCampo->setEtiqueta(_("ce_lugar"));
		return $oDatosCampo;
	}
}
?>