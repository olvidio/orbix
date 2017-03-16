<?php
namespace personas\model;
use core;
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
class PersonaDl Extends PersonaGlobal {
	/* ATRIBUTS ----------------------------------------------------------------- */
	/**
	 * Id_ctr de PersonaDl
	 *
	 * @var integer
	 */
	 protected $iid_ctr;
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
			}	
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_nom = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_nom' => $this->iid_nom);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('personas_dl');
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
		array_walk($aDades, 'core\poner_null');
		//para el caso de los boolean false, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		if (empty($aDades['sacd']) || ($aDades['sacd'] === 'off') || ($aDades['sacd'] === false) || ($aDades['sacd'] === 'f')) { $aDades['sacd']='f'; } else { $aDades['sacd']='t'; }

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
					id_ctr                   = :id_ctr";
			if (($qRs = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_nom='$this->iid_nom'")) === false) {
				$sClauError = 'PersonaDl.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($qRs->execute($aDades) === false) {
					$sClauError = 'PersonaDl.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
			$this->setAllAtributes($aDades);
		} else {
			// INSERT
				$campos="id_cr,dl,sacd,trato,nom,nx1,apellido1,nx2,apellido2,f_nacimiento,lengua,situacion,f_situacion,apel_fam,inc,f_inc,stgr,profesion,eap,observ,id_ctr";
			$valores=":id_cr,:dl,:sacd,:trato,:nom,:nx1,:apellido1,:nx2,:apellido2,:f_nacimiento,:lengua,:situacion,:f_situacion,:apel_fam,:inc,:f_inc,:stgr,:profesion,:eap,:observ,:id_ctr";
			if (empty($this->iid_nom)) {
				$campos="($campos)";
				$valores="($valores)";
			} else {
				array_unshift($aDades, $this->iid_nom);
				$campos="(id_nom,$campos)";
				$valores="(:id_nom,$valores)";
			}
			if (($qRs = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'PersonaDl.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($qRs->execute($aDades) === false) {
					$sClauError = 'PersonaDl.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
			$id_auto = $oDbl->lastInsertId($nom_tabla.'_id_auto_seq');
			if (($qRs = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_auto=$id_auto")) === false) {
				$sClauError = get_class($this).'.carregar.Last';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			}
			$aDadesLast = $qRs->fetch(\PDO::FETCH_ASSOC);
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
			if (($qRs = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_nom='$this->iid_nom'")) === false) {
				$sClauError = 'PersonaDl.carregar';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			}
			$aDades = $qRs->fetch(\PDO::FETCH_ASSOC);
			switch ($que) {
				case 'tot':
					$this->aDades=$aDades;
					break;
				case 'guardar':
					if (!$qRs->rowCount()) return false;
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
		if (($qRs = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_nom='$this->iid_nom'")) === false) {
			$sClauError = 'PersonaDl.eliminar';
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
		if (array_key_exists('profesion',$aDades)) $this->setProfesion($aDades['profesion']);
		if (array_key_exists('eap',$aDades)) $this->setEap($aDades['eap']);
		if (array_key_exists('observ',$aDades)) $this->setObserv($aDades['observ']);
		if (array_key_exists('id_ctr',$aDades)) $this->setId_ctr($aDades['id_ctr']);
	}

	/* METODES GET i SET --------------------------------------------------------*/
	/**
	 * Recupera l'atribut iid_ctr de PersonaDl
	 *
	 * @return integer iid_ctr
	 */
	function getId_ctr() {
		if (!isset($this->iid_ctr)) {
			$this->DBCarregar();
		}
		return $this->iid_ctr;
	}
	/**
	 * estableix el valor de l'atribut iid_ctr de PersonaDl
	 *
	 * @param integer iid_ctr='' optional
	 */
	function setId_ctr($iid_ctr='') {
		$this->iid_ctr = $iid_ctr;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	* Para clacular la edad a partir de la fecha de nacimiento
	*
	*
	*@author    Daniel Serrabou
	*@since     25/11/2010.
	*       
	*/
	function getEdad() {
		$f_nacimiento = $this->getF_nacimiento();
		if (!empty($f_nacimiento)) {
			list($d,$m,$a) = preg_split('/[\.\/-]/', $f_nacimiento );   //separo la fecha en dia, mes, año
			$ah=date("Y");
			$mh=date("m");
			$inc_m=0 ;
			$mh >= $m ? 0 : $inc_m=1 ;
			$edad=$ah - $a - $inc_m;
		} else {
			$edad ="-";
		}
		return $edad;
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
		$oPersonaDlSet->add($this->getDatosProfesion());
		$oPersonaDlSet->add($this->getDatosEap());
		$oPersonaDlSet->add($this->getDatosObserv());
		$oPersonaDlSet->add($this->getDatosId_ctr());
		return $oPersonaDlSet->getTot();
	}


	/**
	 * Recupera les propietats de l'atribut iid_ctr de PersonaDl
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosId_ctr() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_ctr'));
		$oDatosCampo->setEtiqueta(_("id_ctr"));
		return $oDatosCampo;
	}
}
?>
