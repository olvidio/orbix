<?php
namespace personas\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula p_de_paso
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */
/**
 * Classe que implementa l'entitat p_de_paso
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */
class PersonaPub Extends PersonaGlobal {
	/* ATRIBUTS ----------------------------------------------------------------- */
	/**
	 * Profesor strg de PersonaPub
	 *
	 * @var boolean
	 */
	 protected $bprofesor_stgr;
	/**
	 * Edad de PersonaPub
	 *
	 * @var integer
	 */
	 protected $iedad;

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
		$oDbl = $GLOBALS['oDBP'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_nom') && $val_id !== '') $this->iid_nom = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_nom = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_nom' => $this->iid_nom);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('p_de_paso');
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
		$aDades['id_tabla'] = $this->sid_tabla;
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
		$aDades['edad'] = $this->iedad;
		$aDades['profesion'] = $this->sprofesion;
		$aDades['eap'] = $this->seap;
		$aDades['observ'] = $this->sobserv;
		$aDades['lugar_nacimiento'] = $this->slugar_nacimiento;
		$aDades['profesor_stgr'] = $this->bprofesor_stgr;
		array_walk($aDades, 'core\poner_null');
		//para el caso de los boolean false, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		if (empty($aDades['sacd']) || ($aDades['sacd'] === 'off') || ($aDades['sacd'] === false) || ($aDades['sacd'] === 'f')) { $aDades['sacd']='f'; } else { $aDades['sacd']='t'; }
		if (empty($aDades['profesor_stgr']) || ($aDades['profesor_stgr'] === 'off') || ($aDades['profesor_stgr'] === false) || ($aDades['profesor_stgr'] === 'f')) { $aDades['profesor_stgr']='f'; } else { $aDades['profesor_stgr']='t'; }

		if ($bInsert === false) {
			//UPDATE
			$update="
					id_cr                    = :id_cr,
					id_tabla                 = :id_tabla,
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
					edad                     = :edad,
					profesion                = :profesion,
					eap                      = :eap,
					observ                   = :observ,
					lugar_nacimiento         = :lugar_nacimiento,
					profesor_stgr            = :profesor_stgr";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_nom=$this->iid_nom")) === false) {
				$sClauError = get_class($this).'.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = get_class($this).'.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
			$this->setAllAtributes($aDades);
		} else {
			// INSERT
			//array_unshift($aDades, $this->iid_nom);
			$campos="(id_cr,id_tabla,dl,sacd,trato,nom,nx1,apellido1,nx2,apellido2,f_nacimiento,lengua,situacion,f_situacion,apel_fam,inc,f_inc,stgr,edad,profesion,eap,observ,lugar_nacimiento,profesor_stgr)";
			$valores="(:id_cr,:id_tabla,:dl,:sacd,:trato,:nom,:nx1,:apellido1,:nx2,:apellido2,:f_nacimiento,:lengua,:situacion,:f_situacion,:apel_fam,:inc,:f_inc,:stgr,:edad,:profesion,:eap,:observ,:lugar_nacimiento,:profesor_stgr)";
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = get_class($this).'.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = get_class($this).'.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
			$id_auto = $oDbl->lastInsertId($nom_tabla.'_id_auto_seq');
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_auto=$id_auto")) === false) {
				$sClauError = 'PersonaAgd.carregar.Last';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
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
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_nom=$this->iid_nom")) === false) {
				$sClauError = get_class($this).'.carregar';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
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
		if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_nom=$this->iid_nom")) === false) {
			$sClauError = get_class($this).'.eliminar';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
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
		if (array_key_exists('edad',$aDades)) $this->setEdad($aDades['edad']);
		if (array_key_exists('profesion',$aDades)) $this->setProfesion($aDades['profesion']);
		if (array_key_exists('eap',$aDades)) $this->setEap($aDades['eap']);
		if (array_key_exists('observ',$aDades)) $this->setObserv($aDades['observ']);
		if (array_key_exists('lugar_nacimiento',$aDades)) $this->setLugar_nacimiento($aDades['lugar_nacimiento']);
		if (array_key_exists('profesor_stgr',$aDades)) $this->setProfesor_stgr($aDades['profesor_stgr']);
	}

	/* METODES GET i SET --------------------------------------------------------*/
	/**
	 * Recupera l'atribut iedad de PersonaPub
	 *
	 * @return integer iedad
	 */
	function getEdad() {
		if (!isset($this->iedad)) {
			$this->DBCarregar();
		}
		return $this->iedad;
	}
	/**
	 * estableix el valor de l'atribut iedad de PersonaPub
	 *
	 * @param integer iedad='' optional
	 */
	function setEdad($iedad='') {
		$this->iedad = $iedad;
	}
	/**
	 * Recupera l'atribut bprofesor_stgr de PersonaPub
	 *
	 * @return boolean bprofesor_stgr
	 */
	function getProfesor_stgr() {
		if (!isset($this->bprofesor_stgr)) {
			$this->DBCarregar();
		}
		return $this->bprofesor_stgr;
	}
	/**
	 * estableix el valor de l'atribut bprofesor_stgr de PersonaPub
	 *
	 * @param boolean bprofesor_stgr='' optional
	 */
	function setProfesor_stgr($bprofesor_stgr='f') {
		$this->bprofesor_stgr = $bprofesor_stgr;
	}

	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oPersonaPubSet = new core\Set();

		$oPersonaPubSet->add($this->getDatosId_cr());
		$oPersonaPubSet->add($this->getDatosId_tabla());
		$oPersonaPubSet->add($this->getDatosDl());
		$oPersonaPubSet->add($this->getDatosSacd());
		$oPersonaPubSet->add($this->getDatosTrato());
		$oPersonaPubSet->add($this->getDatosNom());
		$oPersonaPubSet->add($this->getDatosNx1());
		$oPersonaPubSet->add($this->getDatosApellido1());
		$oPersonaPubSet->add($this->getDatosNx2());
		$oPersonaPubSet->add($this->getDatosApellido2());
		$oPersonaPubSet->add($this->getDatosF_nacimiento());
		$oPersonaPubSet->add($this->getDatosLengua());
		$oPersonaPubSet->add($this->getDatosSituacion());
		$oPersonaPubSet->add($this->getDatosF_situacion());
		$oPersonaPubSet->add($this->getDatosApel_fam());
		$oPersonaPubSet->add($this->getDatosInc());
		$oPersonaPubSet->add($this->getDatosF_inc());
		$oPersonaPubSet->add($this->getDatosStgr());
		$oPersonaPubSet->add($this->getDatosEdad());
		$oPersonaPubSet->add($this->getDatosProfesion());
		$oPersonaPubSet->add($this->getDatosEap());
		$oPersonaPubSet->add($this->getDatosObserv());
		$oPersonaPubSet->add($this->getDatosLugar_nacimiento());
		$oPersonaPubSet->add($this->getDatosProfesor_stgr());
		return $oPersonaPubSet->getTot();
	}

	/**
	 * Recupera les propietats de l'atribut iedad PersonaPub
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosEdad() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'edad'));
		$oDatosCampo->setEtiqueta(_("edad"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iprofesor_stgr PersonaPub
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosProfesor_stgr() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'profesor_stgr'));
		$oDatosCampo->setEtiqueta(_("profesor stgr"));
		$oDatosCampo->setTipo('check');
		return $oDatosCampo;
	}


}
