<?php
namespace documentos\model\entity;
use core;
use web;
/**
 * Fitxer amb la Classe que accedeix a la taula doc_equipajes
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 28/4/2022
 */
/**
 * Classe que implementa l'entitat doc_equipajes
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 28/4/2022
 */
class Equipaje Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de Equipaje
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de Equipaje
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * bLoaded de Equipaje
	 *
	 * @var boolean
	 */
	 private $bLoaded = FALSE;

	/**
	 * Id_schema de Equipaje
	 *
	 * @var integer
	 */
	 private $iid_schema;

	/**
	 * Id_equipaje de Equipaje
	 *
	 * @var integer
	 */
	 private $iid_equipaje;
	/**
	 * Ids_activ de Equipaje
	 *
	 * @var string
	 */
	 private $sids_activ;
	/**
	 * Lugar de Equipaje
	 *
	 * @var string
	 */
	 private $slugar;
	/**
	 * F_ini de Equipaje
	 *
	 * @var web\DateTimeLocal
	 */
	 private $df_ini;
	/**
	 * F_fin de Equipaje
	 *
	 * @var web\DateTimeLocal
	 */
	 private $df_fin;
	/**
	 * Id_ubi_activ de Equipaje
	 *
	 * @var integer
	 */
	 private $iid_ubi_activ;
	/**
	 * Nom_equipaje de Equipaje
	 *
	 * @var string
	 */
	 private $snom_equipaje;
	/**
	 * Cabecera de Equipaje
	 *
	 * @var string
	 */
	 private $scabecera;
	/**
	 * Pie de Equipaje
	 *
	 * @var string
	 */
	 private $spie;
	/**
	 * Cabecerab de Equipaje
	 *
	 * @var string
	 */
	 private $scabecerab;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de Equipaje
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de Equipaje
	 *
	 * @var string
	 */
	 protected $sNomTabla;
	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 * Si només necessita un valor, se li pot passar un integer.
	 * En general se li passa un array amb les claus primàries.
	 *
	 * @param integer|array iid_equipaje
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDB'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_equipaje') && $val_id !== '') $this->iid_equipaje = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_equipaje = (integer) $a_id; // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_equipaje' => $this->iid_equipaje);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('doc_equipajes');
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
		if ($this->DBCarregar('guardar') === FALSE) { $bInsert=TRUE; } else { $bInsert=FALSE; }
		$aDades=array();
		$aDades['ids_activ'] = $this->sids_activ;
		$aDades['lugar'] = $this->slugar;
		$aDades['f_ini'] = $this->df_ini;
		$aDades['f_fin'] = $this->df_fin;
		$aDades['id_ubi_activ'] = $this->iid_ubi_activ;
		$aDades['nom_equipaje'] = $this->snom_equipaje;
		$aDades['cabecera'] = $this->scabecera;
		$aDades['pie'] = $this->spie;
		$aDades['cabecerab'] = $this->scabecerab;
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === FALSE) {
			//UPDATE
			$update="
					ids_activ                = :ids_activ,
					lugar                    = :lugar,
					f_ini                    = :f_ini,
					f_fin                    = :f_fin,
					id_ubi_activ             = :id_ubi_activ,
					nom_equipaje             = :nom_equipaje,
					cabecera                 = :cabecera,
					pie                      = :pie,
					cabecerab                = :cabecerab";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_equipaje='$this->iid_equipaje'")) === FALSE) {
				$sClauError = 'Equipaje.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = 'Equipaje.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
		} else {
			// INSERT
			$campos="(ids_activ,lugar,f_ini,f_fin,id_ubi_activ,nom_equipaje,cabecera,pie,cabecerab)";
			$valores="(:ids_activ,:lugar,:f_ini,:f_fin,:id_ubi_activ,:nom_equipaje,:cabecera,:pie,:cabecerab)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
				$sClauError = 'Equipaje.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = 'Equipaje.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
			$this->id_equipaje = $oDbl->lastInsertId('doc_equipajes_id_equipaje_seq');
		}
		$this->setAllAtributes($aDades);
		return TRUE;
	}

	/**
	 * Carrega els camps de la base de dades com atributs de l'objecte.
	 *
	 */
	public function DBCarregar($que=null) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		if (isset($this->iid_equipaje)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_equipaje='$this->iid_equipaje'")) === FALSE) {
				$sClauError = 'Equipaje.carregar';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			}
			$aDades = $oDblSt->fetch(\PDO::FETCH_ASSOC);
            // Para evitar posteriores cargas
            $this->bLoaded = TRUE;
			switch ($que) {
				case 'tot':
					$this->aDades=$aDades;
					break;
				case 'guardar':
					if (!$oDblSt->rowCount()) return FALSE;
					break;
                default:
					// En el caso de no existir esta fila, $aDades = FALSE:
					if ($aDades === FALSE) {
						$this->setNullAllAtributes();
					} else {
						$this->setAllAtributes($aDades);
					}
			}
			return TRUE;
		} else {
		   	return FALSE;
		}
	}

	/**
	 * Elimina el registre de la base de dades corresponent a l'objecte.
	 *
	 */
	public function DBEliminar() {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_equipaje='$this->iid_equipaje'")) === FALSE) {
			$sClauError = 'Equipaje.eliminar';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
		return TRUE;
	}
	
	/* METODES ALTRES  ----------------------------------------------------------*/
	/* METODES PRIVATS ----------------------------------------------------------*/

	/**
	 * Estableix el valor de tots els atributs
	 *
	 * @param array $aDades
	 */
	function setAllAtributes($aDades,$convert=FALSE) {
		if (!is_array($aDades)) return;
		if (array_key_exists('id_schema',$aDades)) $this->setId_schema($aDades['id_schema']);
		if (array_key_exists('id_equipaje',$aDades)) $this->setId_equipaje($aDades['id_equipaje']);
		if (array_key_exists('ids_activ',$aDades)) $this->setIds_activ($aDades['ids_activ']);
		if (array_key_exists('lugar',$aDades)) $this->setLugar($aDades['lugar']);
		if (array_key_exists('f_ini',$aDades)) $this->setF_ini($aDades['f_ini'],$convert);
		if (array_key_exists('f_fin',$aDades)) $this->setF_fin($aDades['f_fin'],$convert);
		if (array_key_exists('id_ubi_activ',$aDades)) $this->setId_ubi_activ($aDades['id_ubi_activ']);
		if (array_key_exists('nom_equipaje',$aDades)) $this->setNom_equipaje($aDades['nom_equipaje']);
		if (array_key_exists('cabecera',$aDades)) $this->setCabecera($aDades['cabecera']);
		if (array_key_exists('pie',$aDades)) $this->setPie($aDades['pie']);
		if (array_key_exists('cabecerab',$aDades)) $this->setCabecerab($aDades['cabecerab']);
	}	
	/**
	 * Estableix a empty el valor de tots els atributs
	 *
	 */
	function setNullAllAtributes() {
		$aPK = $this->getPrimary_key();
		$this->setId_schema('');
		$this->setId_equipaje('');
		$this->setIds_activ('');
		$this->setLugar('');
		$this->setF_ini('');
		$this->setF_fin('');
		$this->setId_ubi_activ('');
		$this->setNom_equipaje('');
		$this->setCabecera('');
		$this->setPie('');
		$this->setCabecerab('');
		$this->setPrimary_key($aPK);
	}

	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de Equipaje en un array
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
	 * Recupera las claus primàries de Equipaje en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('id_equipaje' => $this->iid_equipaje);
		}
		return $this->aPrimary_key;
	}
	/**
	 * Estableix las claus primàries de Equipaje en un array
	 *
	 */
	public function setPrimary_key($a_id='') {
	    if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_equipaje') && $val_id !== '') $this->iid_equipaje = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_equipaje = (integer) $a_id; // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_equipaje' => $this->iid_equipaje);
			}
		}
	}
	

	/**
	 * Recupera l'atribut iid_equipaje de Equipaje
	 *
	 * @return integer iid_equipaje
	 */
	function getId_equipaje() {
		if (!isset($this->iid_equipaje) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->iid_equipaje;
	}
	/**
	 * estableix el valor de l'atribut iid_equipaje de Equipaje
	 *
	 * @param integer iid_equipaje
	 */
	function setId_equipaje($iid_equipaje) {
		$this->iid_equipaje = $iid_equipaje;
	}
	/**
	 * Recupera l'atribut sids_activ de Equipaje
	 *
	 * @return string sids_activ
	 */
	function getIds_activ() {
		if (!isset($this->sids_activ) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->sids_activ;
	}
	/**
	 * estableix el valor de l'atribut sids_activ de Equipaje
	 *
	 * @param string sids_activ='' optional
	 */
	function setIds_activ($sids_activ='') {
		$this->sids_activ = $sids_activ;
	}
	/**
	 * Recupera l'atribut slugar de Equipaje
	 *
	 * @return string slugar
	 */
	function getLugar() {
		if (!isset($this->slugar) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->slugar;
	}
	/**
	 * estableix el valor de l'atribut slugar de Equipaje
	 *
	 * @param string slugar='' optional
	 */
	function setLugar($slugar='') {
		$this->slugar = $slugar;
	}
	/**
	 * Recupera l'atribut df_ini de Equipaje
	 *
	 * @return web\DateTimeLocal df_ini
	 */
	function getF_ini() {
		if (!isset($this->df_ini) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		if (empty($this->df_ini)) {
			return new web\NullDateTimeLocal();
		}
        $oConverter = new core\Converter('date', $this->df_ini);
		return $oConverter->fromPg();
	}
	/**
	 * estableix el valor de l'atribut df_ini de Equipaje
	 * Si df_ini es string, y convert=TRUE se convierte usando el formato web\DateTimeLocal->getForamat().
	 * Si convert es FALSE, df_ini debe ser un string en formato ISO (Y-m-d). Corresponde al pgstyle de la base de datos.
	 * 
	 * @param web\DateTimeLocal|string df_ini='' optional.
     * @param boolean convert=TRUE optional. Si es FALSE, df_ini debe ser un string en formato ISO (Y-m-d).
	 */
	function setF_ini($df_ini='',$convert=TRUE) {
        if ($convert === TRUE  && !empty($df_ini)) {
            $oConverter = new core\Converter('date', $df_ini);
            $this->df_ini = $oConverter->toPg();
	    } else {
            $this->df_ini = $df_ini;
	    }
	}
	/**
	 * Recupera l'atribut df_fin de Equipaje
	 *
	 * @return web\DateTimeLocal df_fin
	 */
	function getF_fin() {
		if (!isset($this->df_fin) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		if (empty($this->df_fin)) {
			return new web\NullDateTimeLocal();
		}
        $oConverter = new core\Converter('date', $this->df_fin);
		return $oConverter->fromPg();
	}
	/**
	 * estableix el valor de l'atribut df_fin de Equipaje
	 * Si df_fin es string, y convert=TRUE se convierte usando el formato web\DateTimeLocal->getForamat().
	 * Si convert es FALSE, df_fin debe ser un string en formato ISO (Y-m-d). Corresponde al pgstyle de la base de datos.
	 * 
	 * @param web\DateTimeLocal|string df_fin='' optional.
     * @param boolean convert=TRUE optional. Si es FALSE, df_ini debe ser un string en formato ISO (Y-m-d).
	 */
	function setF_fin($df_fin='',$convert=TRUE) {
        if ($convert === TRUE  && !empty($df_fin)) {
            $oConverter = new core\Converter('date', $df_fin);
            $this->df_fin = $oConverter->toPg();
	    } else {
            $this->df_fin = $df_fin;
	    }
	}
	/**
	 * Recupera l'atribut iid_ubi_activ de Equipaje
	 *
	 * @return integer iid_ubi_activ
	 */
	function getId_ubi_activ() {
		if (!isset($this->iid_ubi_activ) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->iid_ubi_activ;
	}
	/**
	 * estableix el valor de l'atribut iid_ubi_activ de Equipaje
	 *
	 * @param integer iid_ubi_activ='' optional
	 */
	function setId_ubi_activ($iid_ubi_activ='') {
		$this->iid_ubi_activ = $iid_ubi_activ;
	}
	/**
	 * Recupera l'atribut snom_equipaje de Equipaje
	 *
	 * @return string snom_equipaje
	 */
	function getNom_equipaje() {
		if (!isset($this->snom_equipaje) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->snom_equipaje;
	}
	/**
	 * estableix el valor de l'atribut snom_equipaje de Equipaje
	 *
	 * @param string snom_equipaje='' optional
	 */
	function setNom_equipaje($snom_equipaje='') {
		$this->snom_equipaje = $snom_equipaje;
	}
	/**
	 * Recupera l'atribut scabecera de Equipaje
	 *
	 * @return string scabecera
	 */
	function getCabecera() {
		if (!isset($this->scabecera) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->scabecera;
	}
	/**
	 * estableix el valor de l'atribut scabecera de Equipaje
	 *
	 * @param string scabecera='' optional
	 */
	function setCabecera($scabecera='') {
		$this->scabecera = $scabecera;
	}
	/**
	 * Recupera l'atribut spie de Equipaje
	 *
	 * @return string spie
	 */
	function getPie() {
		if (!isset($this->spie) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->spie;
	}
	/**
	 * estableix el valor de l'atribut spie de Equipaje
	 *
	 * @param string spie='' optional
	 */
	function setPie($spie='') {
		$this->spie = $spie;
	}
	/**
	 * Recupera l'atribut scabecerab de Equipaje
	 *
	 * @return string scabecerab
	 */
	function getCabecerab() {
		if (!isset($this->scabecerab) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->scabecerab;
	}
	/**
	 * estableix el valor de l'atribut scabecerab de Equipaje
	 *
	 * @param string scabecerab='' optional
	 */
	function setCabecerab($scabecerab='') {
		$this->scabecerab = $scabecerab;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oEquipajeSet = new core\Set();

		$oEquipajeSet->add($this->getDatosIds_activ());
		$oEquipajeSet->add($this->getDatosLugar());
		$oEquipajeSet->add($this->getDatosF_ini());
		$oEquipajeSet->add($this->getDatosF_fin());
		$oEquipajeSet->add($this->getDatosId_ubi_activ());
		$oEquipajeSet->add($this->getDatosNom_equipaje());
		$oEquipajeSet->add($this->getDatosCabecera());
		$oEquipajeSet->add($this->getDatosPie());
		$oEquipajeSet->add($this->getDatosCabecerab());
		return $oEquipajeSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut sids_activ de Equipaje
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosIds_activ() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'ids_activ'));
		$oDatosCampo->setEtiqueta(_("ids_activ"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut slugar de Equipaje
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosLugar() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'lugar'));
		$oDatosCampo->setEtiqueta(_("lugar"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut df_ini de Equipaje
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosF_ini() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'f_ini'));
		$oDatosCampo->setEtiqueta(_("f_ini"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut df_fin de Equipaje
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosF_fin() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'f_fin'));
		$oDatosCampo->setEtiqueta(_("f_fin"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iid_ubi_activ de Equipaje
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_ubi_activ() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_ubi_activ'));
		$oDatosCampo->setEtiqueta(_("id_ubi_activ"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut snom_equipaje de Equipaje
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosNom_equipaje() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'nom_equipaje'));
		$oDatosCampo->setEtiqueta(_("nom_equipaje"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut scabecera de Equipaje
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosCabecera() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'cabecera'));
		$oDatosCampo->setEtiqueta(_("cabecera"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut spie de Equipaje
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosPie() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'pie'));
		$oDatosCampo->setEtiqueta(_("pie"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut scabecerab de Equipaje
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosCabecerab() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'cabecerab'));
		$oDatosCampo->setEtiqueta(_("cabecerab"));
		return $oDatosCampo;
	}
}
