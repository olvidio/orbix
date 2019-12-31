<?php
namespace encargossacd\model\entity;
use core;
use web;
/**
 * Fitxer amb la Classe que accedeix a la taula encargo_sacd_horario
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/01/2019
 */
/**
 * Classe que implementa l'entitat encargo_sacd_horario
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/01/2019
 */
class EncargoSacdHorario Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de EncargoSacdHorario
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de EncargoSacdHorario
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_item de EncargoSacdHorario
	 *
	 * @var integer
	 */
	 private $iid_item;
	/**
	 * Id_enc de EncargoSacdHorario
	 *
	 * @var integer
	 */
	 private $iid_enc;
	/**
	 * Id_nom de EncargoSacdHorario
	 *
	 * @var integer
	 */
	 private $iid_nom;
	/**
	 * F_ini de EncargoSacdHorario
	 *
	 * @var web\DateTimeLocal
	 */
	 private $df_ini;
	/**
	 * F_fin de EncargoSacdHorario
	 *
	 * @var web\DateTimeLocal
	 */
	 private $df_fin;
	/**
	 * Dia_ref de EncargoSacdHorario
	 *
	 * @var string
	 */
	 private $sdia_ref;
	/**
	 * Dia_num de EncargoSacdHorario
	 *
	 * @var integer
	 */
	 private $idia_num;
	/**
	 * Mas_menos de EncargoSacdHorario
	 *
	 * @var string
	 */
	 private $smas_menos;
	/**
	 * Dia_inc de EncargoSacdHorario
	 *
	 * @var integer
	 */
	 private $idia_inc;
	/**
	 * H_ini de EncargoSacdHorario
	 *
	 * @var string time
	 */
	 private $th_ini;
	/**
	 * H_fin de EncargoSacdHorario
	 *
	 * @var string time
	 */
	 private $th_fin;
	/**
	 * Id_item_tarea_sacd de EncargoSacdHorario
	 *
	 * @var integer
	 */
	 private $iid_item_tarea_sacd;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de EncargoSacdHorario
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de EncargoSacdHorario
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
	 * @param integer|array iid_item,iid_enc,iid_nom
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBE'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_item') && $val_id !== '') $this->iid_item = (int)$val_id; // evitem SQL injection fent cast a integer
				if (($nom_id == 'id_enc') && $val_id !== '') $this->iid_enc = (int)$val_id; // evitem SQL injection fent cast a integer
				if (($nom_id == 'id_nom') && $val_id !== '') $this->iid_nom = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('encargo_sacd_horario');
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
		$aDades['f_ini'] = $this->df_ini;
		$aDades['f_fin'] = $this->df_fin;
		$aDades['dia_ref'] = $this->sdia_ref;
		$aDades['dia_num'] = $this->idia_num;
		$aDades['mas_menos'] = $this->smas_menos;
		$aDades['dia_inc'] = $this->idia_inc;
		$aDades['h_ini'] = $this->th_ini;
		$aDades['h_fin'] = $this->th_fin;
		$aDades['id_item_tarea_sacd'] = $this->iid_item_tarea_sacd;
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === FALSE) {
			//UPDATE
			$update="
					id_enc                   = :id_enc,
					id_nom                   = :id_nom,
					f_ini                    = :f_ini,
					f_fin                    = :f_fin,
					dia_ref                  = :dia_ref,
					dia_num                  = :dia_num,
					mas_menos                = :mas_menos,
					dia_inc                  = :dia_inc,
					h_ini                    = :h_ini,
					h_fin                    = :h_fin,
					id_item_tarea_sacd       = :id_item_tarea_sacd";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item='$this->iid_item' ")) === FALSE) {
				$sClauError = 'EncargoSacdHorario.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				if ($oDblSt->execute($aDades) === FALSE) {
					$sClauError = 'EncargoSacdHorario.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
		} else {
			// INSERT
			//array_unshift($aDades, $this->iid_enc, $this->iid_nom);
			$campos="(id_enc,id_nom,f_ini,f_fin,dia_ref,dia_num,mas_menos,dia_inc,h_ini,h_fin,id_item_tarea_sacd)";
			$valores="(:id_enc,:id_nom,:f_ini,:f_fin,:dia_ref,:dia_num,:mas_menos,:dia_inc,:h_ini,:h_fin,:id_item_tarea_sacd)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
				$sClauError = 'EncargoSacdHorario.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				if ($oDblSt->execute($aDades) === FALSE) {
					$sClauError = 'EncargoSacdHorario.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
			$this->id_item = $oDbl->lastInsertId('encargo_sacd_horario_id_item_seq');
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
		if (isset($this->iid_item) && isset($this->iid_enc) && isset($this->iid_nom)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_item='$this->iid_item' ")) === FALSE) {
				$sClauError = 'EncargoSacdHorario.carregar';
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
		if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_item='$this->iid_item' ")) === FALSE) {
			$sClauError = 'EncargoSacdHorario.eliminar';
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
		if (array_key_exists('f_ini',$aDades)) $this->setF_ini($aDades['f_ini'],$convert);
		if (array_key_exists('f_fin',$aDades)) $this->setF_fin($aDades['f_fin'],$convert);
		if (array_key_exists('dia_ref',$aDades)) $this->setDia_ref($aDades['dia_ref']);
		if (array_key_exists('dia_num',$aDades)) $this->setDia_num($aDades['dia_num']);
		if (array_key_exists('mas_menos',$aDades)) $this->setMas_menos($aDades['mas_menos']);
		if (array_key_exists('dia_inc',$aDades)) $this->setDia_inc($aDades['dia_inc']);
		if (array_key_exists('h_ini',$aDades)) $this->setH_ini($aDades['h_ini']);
		if (array_key_exists('h_fin',$aDades)) $this->setH_fin($aDades['h_fin']);
		if (array_key_exists('id_item_tarea_sacd',$aDades)) $this->setId_item_tarea_sacd($aDades['id_item_tarea_sacd']);
	}

	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de EncargoSacdHorario en un array
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
	 * Recupera las claus primàries de EncargoSacdHorario en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('id_item' => $this->iid_item,'id_enc' => $this->iid_enc,'id_nom' => $this->iid_nom);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Recupera l'atribut iid_item de EncargoSacdHorario
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
	 * estableix el valor de l'atribut iid_item de EncargoSacdHorario
	 *
	 * @param integer iid_item
	 */
	function setId_item($iid_item) {
		$this->iid_item = $iid_item;
	}
	/**
	 * Recupera l'atribut iid_enc de EncargoSacdHorario
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
	 * estableix el valor de l'atribut iid_enc de EncargoSacdHorario
	 *
	 * @param integer iid_enc
	 */
	function setId_enc($iid_enc) {
		$this->iid_enc = $iid_enc;
	}
	/**
	 * Recupera l'atribut iid_nom de EncargoSacdHorario
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
	 * estableix el valor de l'atribut iid_nom de EncargoSacdHorario
	 *
	 * @param integer iid_nom
	 */
	function setId_nom($iid_nom) {
		$this->iid_nom = $iid_nom;
	}
	/**
	 * Recupera l'atribut df_ini de EncargoSacdHorario
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
	 * estableix el valor de l'atribut df_ini de EncargoSacdHorario
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
	 * Recupera l'atribut df_fin de EncargoSacdHorario
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
	 * estableix el valor de l'atribut df_fin de EncargoSacdHorario
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
	/**
	 * Recupera l'atribut sdia_ref de EncargoSacdHorario
	 *
	 * @return string sdia_ref
	 */
	function getDia_ref() {
		if (!isset($this->sdia_ref)) {
			$this->DBCarregar();
		}
		return $this->sdia_ref;
	}
	/**
	 * estableix el valor de l'atribut sdia_ref de EncargoSacdHorario
	 *
	 * @param string sdia_ref='' optional
	 */
	function setDia_ref($sdia_ref='') {
		$this->sdia_ref = $sdia_ref;
	}
	/**
	 * Recupera l'atribut idia_num de EncargoSacdHorario
	 *
	 * @return integer idia_num
	 */
	function getDia_num() {
		if (!isset($this->idia_num)) {
			$this->DBCarregar();
		}
		return $this->idia_num;
	}
	/**
	 * estableix el valor de l'atribut idia_num de EncargoSacdHorario
	 *
	 * @param integer idia_num='' optional
	 */
	function setDia_num($idia_num='') {
		$this->idia_num = $idia_num;
	}
	/**
	 * Recupera l'atribut smas_menos de EncargoSacdHorario
	 *
	 * @return string smas_menos
	 */
	function getMas_menos() {
		if (!isset($this->smas_menos)) {
			$this->DBCarregar();
		}
		return $this->smas_menos;
	}
	/**
	 * estableix el valor de l'atribut smas_menos de EncargoSacdHorario
	 *
	 * @param string smas_menos='' optional
	 */
	function setMas_menos($smas_menos='') {
		$this->smas_menos = $smas_menos;
	}
	/**
	 * Recupera l'atribut idia_inc de EncargoSacdHorario
	 *
	 * @return integer idia_inc
	 */
	function getDia_inc() {
		if (!isset($this->idia_inc)) {
			$this->DBCarregar();
		}
		return $this->idia_inc;
	}
	/**
	 * estableix el valor de l'atribut idia_inc de EncargoSacdHorario
	 *
	 * @param integer idia_inc='' optional
	 */
	function setDia_inc($idia_inc='') {
		$this->idia_inc = $idia_inc;
	}
	/**
	 * Recupera l'atribut th_ini de EncargoSacdHorario
	 *
	 * @return string time th_ini
	 */
	function getH_ini() {
		if (!isset($this->th_ini)) {
			$this->DBCarregar();
		}
		return $this->th_ini;
	}
	/**
	 * estableix el valor de l'atribut th_ini de EncargoSacdHorario
	 *
	 * @param string time th_ini='' optional
	 */
	function setH_ini($th_ini='') {
		$this->th_ini = $th_ini;
	}
	/**
	 * Recupera l'atribut th_fin de EncargoSacdHorario
	 *
	 * @return string time th_fin
	 */
	function getH_fin() {
		if (!isset($this->th_fin)) {
			$this->DBCarregar();
		}
		return $this->th_fin;
	}
	/**
	 * estableix el valor de l'atribut th_fin de EncargoSacdHorario
	 *
	 * @param string time th_fin='' optional
	 */
	function setH_fin($th_fin='') {
		$this->th_fin = $th_fin;
	}
	/**
	 * Recupera l'atribut iid_item_tarea_sacd de EncargoSacdHorario
	 *
	 * @return integer iid_item_tarea_sacd
	 */
	function getId_item_tarea_sacd() {
		if (!isset($this->iid_item_tarea_sacd)) {
			$this->DBCarregar();
		}
		return $this->iid_item_tarea_sacd;
	}
	/**
	 * estableix el valor de l'atribut iid_item_tarea_sacd de EncargoSacdHorario
	 *
	 * @param integer iid_item_tarea_sacd='' optional
	 */
	function setId_item_tarea_sacd($iid_item_tarea_sacd='') {
		$this->iid_item_tarea_sacd = $iid_item_tarea_sacd;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oEncargoSacdHorarioSet = new core\Set();

		$oEncargoSacdHorarioSet->add($this->getDatosF_ini());
		$oEncargoSacdHorarioSet->add($this->getDatosF_fin());
		$oEncargoSacdHorarioSet->add($this->getDatosDia_ref());
		$oEncargoSacdHorarioSet->add($this->getDatosDia_num());
		$oEncargoSacdHorarioSet->add($this->getDatosMas_menos());
		$oEncargoSacdHorarioSet->add($this->getDatosDia_inc());
		$oEncargoSacdHorarioSet->add($this->getDatosH_ini());
		$oEncargoSacdHorarioSet->add($this->getDatosH_fin());
		$oEncargoSacdHorarioSet->add($this->getDatosId_item_tarea_sacd());
		return $oEncargoSacdHorarioSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut df_ini de EncargoSacdHorario
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
	 * Recupera les propietats de l'atribut df_fin de EncargoSacdHorario
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
	 * Recupera les propietats de l'atribut sdia_ref de EncargoSacdHorario
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosDia_ref() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'dia_ref'));
		$oDatosCampo->setEtiqueta(_("dia_ref"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut idia_num de EncargoSacdHorario
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosDia_num() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'dia_num'));
		$oDatosCampo->setEtiqueta(_("dia_num"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut smas_menos de EncargoSacdHorario
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosMas_menos() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'mas_menos'));
		$oDatosCampo->setEtiqueta(_("mas_menos"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut idia_inc de EncargoSacdHorario
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosDia_inc() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'dia_inc'));
		$oDatosCampo->setEtiqueta(_("dia_inc"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut th_ini de EncargoSacdHorario
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosH_ini() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'h_ini'));
		$oDatosCampo->setEtiqueta(_("h_ini"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut th_fin de EncargoSacdHorario
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosH_fin() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'h_fin'));
		$oDatosCampo->setEtiqueta(_("h_fin"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iid_item_tarea_sacd de EncargoSacdHorario
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_item_tarea_sacd() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_item_tarea_sacd'));
		$oDatosCampo->setEtiqueta(_("id_item_tarea_sacd"));
		return $oDatosCampo;
	}
}
