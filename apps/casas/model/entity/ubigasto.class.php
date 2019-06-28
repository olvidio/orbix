<?php
namespace casas\model\entity;
use core;
use web;
/**
 * Fitxer amb la Classe que accedeix a la taula du_gastos_dl
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 26/6/2019
 */
/**
 * Classe que implementa l'entitat du_gastos_dl
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 26/6/2019
 */
class UbiGasto Extends core\ClasePropiedades {
    
    // tipo constants.
    const TIPO_APORTACION_SV = 1; // aportación sv.
    const TIPO_APORTACION_SF = 2; // aportación sf.
    const TIPO_GASTO  	     = 3; // gastos.
    
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de UbiGasto
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de UbiGasto
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_item de UbiGasto
	 *
	 * @var integer
	 */
	 private $iid_item;
	/**
	 * Id_ubi de UbiGasto
	 *
	 * @var integer
	 */
	 private $iid_ubi;
	/**
	 * F_gasto de UbiGasto
	 *
	 * @var web\DateTimeLocal
	 */
	 private $df_gasto;
	/**
	 * Tipo de UbiGasto
	 *
	 * @var integer
	 */
	 private $itipo;
	/**
	 * Cantidad de UbiGasto
	 *
	 * @var float
	 */
	 private $icantidad;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de UbiGasto
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de UbiGasto
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
		$oDbl = $GLOBALS['oDBC'];
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
		$this->setNomTabla('du_gastos_dl');
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
		$aDades['id_ubi'] = $this->iid_ubi;
		$aDades['f_gasto'] = $this->df_gasto;
		$aDades['tipo'] = $this->itipo;
		$aDades['cantidad'] = $this->icantidad;
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === FALSE) {
			//UPDATE
			$update="
					id_ubi                   = :id_ubi,
					f_gasto                  = :f_gasto,
					tipo                     = :tipo,
					cantidad                 = :cantidad";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item='$this->iid_item'")) === FALSE) {
				$sClauError = 'UbiGasto.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				if ($oDblSt->execute($aDades) === FALSE) {
					$sClauError = 'UbiGasto.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
		} else {
			// INSERT
			$campos="(id_ubi,f_gasto,tipo,cantidad)";
			$valores="(:id_ubi,:f_gasto,:tipo,:cantidad)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
				$sClauError = 'UbiGasto.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				if ($oDblSt->execute($aDades) === FALSE) {
					$sClauError = 'UbiGasto.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
			$this->id_item = $oDbl->lastInsertId('du_gastos_dl_id_item_seq');
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
				$sClauError = 'UbiGasto.carregar';
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
					$this->setAllAtributes($aDades);
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
			$sClauError = 'UbiGasto.eliminar';
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
		if (array_key_exists('id_ubi',$aDades)) $this->setId_ubi($aDades['id_ubi']);
		if (array_key_exists('f_gasto',$aDades)) $this->setF_gasto($aDades['f_gasto'],$convert);
		if (array_key_exists('tipo',$aDades)) $this->setTipo($aDades['tipo']);
		if (array_key_exists('cantidad',$aDades)) $this->setCantidad($aDades['cantidad']);
	}

	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de UbiGasto en un array
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
	 * Recupera las claus primàries de UbiGasto en un array
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
	 * Recupera l'atribut iid_item de UbiGasto
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
	 * estableix el valor de l'atribut iid_item de UbiGasto
	 *
	 * @param integer iid_item
	 */
	function setId_item($iid_item) {
		$this->iid_item = $iid_item;
	}
	/**
	 * Recupera l'atribut iid_ubi de UbiGasto
	 *
	 * @return integer iid_ubi
	 */
	function getId_ubi() {
		if (!isset($this->iid_ubi)) {
			$this->DBCarregar();
		}
		return $this->iid_ubi;
	}
	/**
	 * estableix el valor de l'atribut iid_ubi de UbiGasto
	 *
	 * @param integer iid_ubi='' optional
	 */
	function setId_ubi($iid_ubi='') {
		$this->iid_ubi = $iid_ubi;
	}
	/**
	 * Recupera l'atribut df_gasto de UbiGasto
	 *
	 * @return web\DateTimeLocal df_gasto
	 */
	function getF_gasto() {
		if (!isset($this->df_gasto)) {
			$this->DBCarregar();
		}
        $oConverter = new core\Converter('date', $this->df_gasto);
		return $oConverter->fromPg();
	}
	/**
	 * estableix el valor de l'atribut df_gasto de UbiGasto
	 * Si df_gasto es string, y convert=true se convierte usando el formato web\DateTimeLocal->getForamat().
	 * Si convert es false, df_gasto debe ser un string en formato ISO (Y-m-d). Corresponde al pgstyle de la base de datos.
	 * 
	 * @param web\DateTimeLocal|string df_gasto='' optional.
     * @param boolean convert=TRUE optional. Si es false, df_ini debe ser un string en formato ISO (Y-m-d).
	 */
	function setF_gasto($df_gasto='',$convert=TRUE) {
        if ($convert === TRUE  && !empty($df_gasto)) {
            $oConverter = new core\Converter('date', $df_gasto);
            $this->df_gasto = $oConverter->toPg();
	    } else {
            $this->df_gasto = $df_gasto;
	    }
	}
	/**
	 * Recupera l'atribut itipo de UbiGasto
	 *
	 * @return integer itipo
	 */
	function getTipo() {
		if (!isset($this->itipo)) {
			$this->DBCarregar();
		}
		return $this->itipo;
	}
	/**
	 * estableix el valor de l'atribut itipo de UbiGasto
	 *
	 * @param integer itipo='' optional
	 */
	function setTipo($itipo='') {
		$this->itipo = $itipo;
	}
	/**
	 * Recupera l'atribut icantidad de UbiGasto
	 *
	 * @return float icantidad
	 */
	function getCantidad() {
		if (!isset($this->icantidad)) {
			$this->DBCarregar();
		}
		return $this->icantidad;
	}
	/**
	 * estableix el valor de l'atribut icantidad de UbiGasto
	 *
	 * @param float icantidad='' optional
	 */
	function setCantidad($icantidad='') {
		$this->icantidad = $icantidad;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oUbiGastoSet = new core\Set();

		$oUbiGastoSet->add($this->getDatosId_ubi());
		$oUbiGastoSet->add($this->getDatosF_gasto());
		$oUbiGastoSet->add($this->getDatosTipo());
		$oUbiGastoSet->add($this->getDatosCantidad());
		return $oUbiGastoSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut iid_ubi de UbiGasto
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_ubi() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_ubi'));
		$oDatosCampo->setEtiqueta(_("id_ubi"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut df_gasto de UbiGasto
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosF_gasto() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'f_gasto'));
		$oDatosCampo->setEtiqueta(_("f_gasto"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut itipo de UbiGasto
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosTipo() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'tipo'));
		$oDatosCampo->setEtiqueta(_("tipo"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut icantidad de UbiGasto
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosCantidad() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'cantidad'));
		$oDatosCampo->setEtiqueta(_("cantidad"));
		return $oDatosCampo;
	}
}
