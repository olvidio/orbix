<?php
namespace encargossacd\model\entity;
use core;
use web;
/**
 * Fitxer amb la Classe que accedeix a la taula encargos_sacd
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/01/2019
 */
/**
 * Classe que implementa l'entitat encargos_sacd
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/01/2019
 */
class EncargoSacd Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de EncargoSacd
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de EncargoSacd
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_item de EncargoSacd
	 *
	 * @var integer
	 */
	 private $iid_item;
	/**
	 * Id_enc de EncargoSacd
	 *
	 * @var integer
	 */
	 private $iid_enc;
	/**
	 * Id_nom de EncargoSacd
	 *
	 * @var integer
	 */
	 private $iid_nom;
	/**
	 * Modo de EncargoSacd
	 *
	 * @var integer
	 */
	 private $imodo;
	/**
	 * F_ini de EncargoSacd
	 *
	 * @var web\DateTimeLocal
	 */
	 private $df_ini;
	/**
	 * F_fin de EncargoSacd
	 *
	 * @var web\DateTimeLocal
	 */
	 private $df_fin;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de EncargoSacd
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de EncargoSacd
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
	 * @param integer|array iid_item
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBE'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_item') && $val_id !== '') $this->iid_item = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_item = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_item' => $this->iid_item);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('encargos_sacd');
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
		$aDades['id_enc'] = $this->iid_enc;
		$aDades['id_nom'] = $this->iid_nom;
		$aDades['modo'] = $this->imodo;
		$aDades['f_ini'] = $this->df_ini;
		$aDades['f_fin'] = $this->df_fin;
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === FALSE) {
			//UPDATE
			$update="
					id_enc                   = :id_enc,
					id_nom                   = :id_nom,
					modo                     = :modo,
					f_ini                    = :f_ini,
					f_fin                    = :f_fin";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item='$this->iid_item'")) === FALSE) {
				$sClauError = 'EncargoSacd.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				if ($oDblSt->execute($aDades) === FALSE) {
					$sClauError = 'EncargoSacd.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
		} else {
			// INSERT
			$campos="(id_enc,id_nom,modo,f_ini,f_fin)";
			$valores="(:id_enc,:id_nom,:modo,:f_ini,:f_fin)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
				$sClauError = 'EncargoSacd.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				if ($oDblSt->execute($aDades) === FALSE) {
					$sClauError = 'EncargoSacd.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
			$this->id_item = $oDbl->lastInsertId('encargos_sacd_id_item_seq');
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
		if (isset($this->iid_item)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_item='$this->iid_item'")) === FALSE) {
				$sClauError = 'EncargoSacd.carregar';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			}
			$aDades = $oDblSt->fetch(\PDO::FETCH_ASSOC);
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
		if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_item='$this->iid_item'")) === FALSE) {
			$sClauError = 'EncargoSacd.eliminar';
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
		if (array_key_exists('id_item',$aDades)) $this->setId_item($aDades['id_item']);
		if (array_key_exists('id_enc',$aDades)) $this->setId_enc($aDades['id_enc']);
		if (array_key_exists('id_nom',$aDades)) $this->setId_nom($aDades['id_nom']);
		if (array_key_exists('modo',$aDades)) $this->setModo($aDades['modo']);
		if (array_key_exists('f_ini',$aDades)) $this->setF_ini($aDades['f_ini'],$convert);
		if (array_key_exists('f_fin',$aDades)) $this->setF_fin($aDades['f_fin'],$convert);
	}

	/**
	 * Estableix a empty el valor de tots els atributs
	 *
	 */
	function setNullAllAtributes() {
		$aPK = $this->getPrimary_key();
		$this->setId_item('');
		$this->setId_enc('');
		$this->setId_nom('');
		$this->setModo('');
		$this->setF_ini('');
		$this->setF_fin('');
		$this->setPrimary_key($aPK);
	}


	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de EncargoSacd en un array
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
	 * Recupera las claus primàries de EncargoSacd en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('id_item' => $this->iid_item);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Recupera l'atribut iid_item de EncargoSacd
	 *
	 * @return integer iid_item
	 */
	function getId_item() {
		if (!isset($this->iid_item)) {
			$this->DBCarregar();
		}
		return $this->iid_item;
	}
	/**
	 * estableix el valor de l'atribut iid_item de EncargoSacd
	 *
	 * @param integer iid_item
	 */
	function setId_item($iid_item) {
		$this->iid_item = $iid_item;
	}
	/**
	 * Recupera l'atribut iid_enc de EncargoSacd
	 *
	 * @return integer iid_enc
	 */
	function getId_enc() {
		if (!isset($this->iid_enc)) {
			$this->DBCarregar();
		}
		return $this->iid_enc;
	}
	/**
	 * estableix el valor de l'atribut iid_enc de EncargoSacd
	 *
	 * @param integer iid_enc='' optional
	 */
	function setId_enc($iid_enc='') {
		$this->iid_enc = $iid_enc;
	}
	/**
	 * Recupera l'atribut iid_nom de EncargoSacd
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
	 * estableix el valor de l'atribut iid_nom de EncargoSacd
	 *
	 * @param integer iid_nom='' optional
	 */
	function setId_nom($iid_nom='') {
		$this->iid_nom = $iid_nom;
	}
	/**
	 * Recupera l'atribut imodo de EncargoSacd
	 *
	 * @return integer imodo
	 */
	function getModo() {
		if (!isset($this->imodo)) {
			$this->DBCarregar();
		}
		return $this->imodo;
	}
	/**
	 * estableix el valor de l'atribut imodo de EncargoSacd
	 *
	 * @param integer imodo='' optional
	 */
	function setModo($imodo='') {
		$this->imodo = $imodo;
	}
	/**
	 * Recupera l'atribut df_ini de EncargoSacd
	 *
	 * @return web\DateTimeLocal df_ini
	 */
	function getF_ini() {
		if (!isset($this->df_ini)) {
			$this->DBCarregar();
		}
        $oConverter = new core\Converter('date', $this->df_ini);
		return $oConverter->fromPg();
	}
	/**
	 * estableix el valor de l'atribut df_ini de EncargoSacd
	 * Si df_ini es string, y convert=true se convierte usando el formato web\DateTimeLocal->getForamat().
	 * Si convert es false, df_ini debe ser un string en formato ISO (Y-m-d). Corresponde al pgstyle de la base de datos.
	 * 
	 * @param web\DateTimeLocal|string df_ini='' optional.
     * @param boolean convert=TRUE optional. Si es false, df_ini debe ser un string en formato ISO (Y-m-d).
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
	 * Recupera l'atribut df_fin de EncargoSacd
	 *
	 * @return web\DateTimeLocal df_fin
	 */
	function getF_fin() {
		if (!isset($this->df_fin)) {
			$this->DBCarregar();
		}
        $oConverter = new core\Converter('date', $this->df_fin);
		return $oConverter->fromPg();
	}
	/**
	 * estableix el valor de l'atribut df_fin de EncargoSacd
	 * Si df_fin es string, y convert=true se convierte usando el formato web\DateTimeLocal->getForamat().
	 * Si convert es false, df_fin debe ser un string en formato ISO (Y-m-d). Corresponde al pgstyle de la base de datos.
	 * 
	 * @param web\DateTimeLocal|string df_fin='' optional.
     * @param boolean convert=TRUE optional. Si es false, df_ini debe ser un string en formato ISO (Y-m-d).
	 */
	function setF_fin($df_fin='',$convert=TRUE) {
        if ($convert === TRUE  && !empty($df_fin)) {
            $oConverter = new core\Converter('date', $df_fin);
            $this->df_fin = $oConverter->toPg();
	    } else {
            $this->df_fin = $df_fin;
	    }
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oEncargoSacdSet = new core\Set();

		$oEncargoSacdSet->add($this->getDatosId_enc());
		$oEncargoSacdSet->add($this->getDatosId_nom());
		$oEncargoSacdSet->add($this->getDatosModo());
		$oEncargoSacdSet->add($this->getDatosF_ini());
		$oEncargoSacdSet->add($this->getDatosF_fin());
		return $oEncargoSacdSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut iid_enc de EncargoSacd
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_enc() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_enc'));
		$oDatosCampo->setEtiqueta(_("id_enc"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iid_nom de EncargoSacd
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_nom() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_nom'));
		$oDatosCampo->setEtiqueta(_("id_nom"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut imodo de EncargoSacd
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosModo() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'modo'));
		$oDatosCampo->setEtiqueta(_("modo"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut df_ini de EncargoSacd
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
	 * Recupera les propietats de l'atribut df_fin de EncargoSacd
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
}
