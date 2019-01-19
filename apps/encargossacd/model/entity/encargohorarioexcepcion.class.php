<?php
namespace encargossacd\model\entity;
use core;
use web;
/**
 * Fitxer amb la Classe que accedeix a la taula encargo_horario_excepcion
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/01/2019
 */
/**
 * Classe que implementa l'entitat encargo_horario_excepcion
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/01/2019
 */
class EncargoHorarioExcepcion Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de EncargoHorarioExcepcion
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de EncargoHorarioExcepcion
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_enc de EncargoHorarioExcepcion
	 *
	 * @var integer
	 */
	 private $iid_enc;
	/**
	 * Id_item_ex de EncargoHorarioExcepcion
	 *
	 * @var integer
	 */
	 private $iid_item_ex;
	/**
	 * Id_item_h de EncargoHorarioExcepcion
	 *
	 * @var integer
	 */
	 private $iid_item_h;
	/**
	 * F_ini de EncargoHorarioExcepcion
	 *
	 * @var web\DateTimeLocal
	 */
	 private $df_ini;
	/**
	 * F_fin de EncargoHorarioExcepcion
	 *
	 * @var web\DateTimeLocal
	 */
	 private $df_fin;
	/**
	 * Desc_ex de EncargoHorarioExcepcion
	 *
	 * @var string
	 */
	 private $sdesc_ex;
	/**
	 * Horario de EncargoHorarioExcepcion
	 *
	 * @var boolean
	 */
	 private $bhorario;
	/**
	 * Dia_ref de EncargoHorarioExcepcion
	 *
	 * @var string
	 */
	 private $sdia_ref;
	/**
	 * Dia_num de EncargoHorarioExcepcion
	 *
	 * @var integer
	 */
	 private $idia_num;
	/**
	 * Mas_menos de EncargoHorarioExcepcion
	 *
	 * @var string
	 */
	 private $smas_menos;
	/**
	 * Dia_inc de EncargoHorarioExcepcion
	 *
	 * @var integer
	 */
	 private $idia_inc;
	/**
	 * H_ini de EncargoHorarioExcepcion
	 *
	 * @var string time
	 */
	 private $th_ini;
	/**
	 * H_fin de EncargoHorarioExcepcion
	 *
	 * @var string time
	 */
	 private $th_fin;
	/**
	 * N_sacd de EncargoHorarioExcepcion
	 *
	 * @var integer
	 */
	 private $in_sacd;
	/**
	 * Mes de EncargoHorarioExcepcion
	 *
	 * @var integer
	 */
	 private $imes;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de EncargoHorarioExcepcion
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de EncargoHorarioExcepcion
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
	 * @param integer|array iid_enc,iid_item_ex
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBC'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_enc') && $val_id !== '') $this->iid_enc = (int)$val_id; // evitem SQL injection fent cast a integer
				if (($nom_id == 'id_item_ex') && $val_id !== '') $this->iid_item_ex = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('encargo_horario_excepcion');
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
		$aDades['id_item_h'] = $this->iid_item_h;
		$aDades['f_ini'] = $this->df_ini;
		$aDades['f_fin'] = $this->df_fin;
		$aDades['desc_ex'] = $this->sdesc_ex;
		$aDades['horario'] = $this->bhorario;
		$aDades['dia_ref'] = $this->sdia_ref;
		$aDades['dia_num'] = $this->idia_num;
		$aDades['mas_menos'] = $this->smas_menos;
		$aDades['dia_inc'] = $this->idia_inc;
		$aDades['h_ini'] = $this->th_ini;
		$aDades['h_fin'] = $this->th_fin;
		$aDades['n_sacd'] = $this->in_sacd;
		$aDades['mes'] = $this->imes;
		array_walk($aDades, 'core\poner_null');
		//para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		$aDades['horario'] = ($aDades['horario'] === 't')? 'true' : $aDades['horario'];
		if ( filter_var( $aDades['horario'], FILTER_VALIDATE_BOOLEAN)) { $aDades['horario']='t'; } else { $aDades['horario']='f'; }

		if ($bInsert === FALSE) {
			//UPDATE
			$update="
					id_item_h                = :id_item_h,
					f_ini                    = :f_ini,
					f_fin                    = :f_fin,
					desc_ex                  = :desc_ex,
					horario                  = :horario,
					dia_ref                  = :dia_ref,
					dia_num                  = :dia_num,
					mas_menos                = :mas_menos,
					dia_inc                  = :dia_inc,
					h_ini                    = :h_ini,
					h_fin                    = :h_fin,
					n_sacd                   = :n_sacd,
					mes                      = :mes";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_enc='$this->iid_enc' AND id_item_ex='$this->iid_item_ex'")) === FALSE) {
				$sClauError = 'EncargoHorarioExcepcion.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				if ($oDblSt->execute($aDades) === FALSE) {
					$sClauError = 'EncargoHorarioExcepcion.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
		} else {
			// INSERT
			array_unshift($aDades, $this->iid_enc);
			$campos="(id_enc,id_item_h,f_ini,f_fin,desc_ex,horario,dia_ref,dia_num,mas_menos,dia_inc,h_ini,h_fin,n_sacd,mes)";
			$valores="(:id_enc,:id_item_h,:f_ini,:f_fin,:desc_ex,:horario,:dia_ref,:dia_num,:mas_menos,:dia_inc,:h_ini,:h_fin,:n_sacd,:mes)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
				$sClauError = 'EncargoHorarioExcepcion.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				if ($oDblSt->execute($aDades) === FALSE) {
					$sClauError = 'EncargoHorarioExcepcion.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
			$this->id_item_ex = $oDbl->lastInsertId('encargo_horario_excepcion_id_item_ex_seq');
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
		if (isset($this->iid_enc) && isset($this->iid_item_ex)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_enc='$this->iid_enc' AND id_item_ex='$this->iid_item_ex'")) === FALSE) {
				$sClauError = 'EncargoHorarioExcepcion.carregar';
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
		if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_enc='$this->iid_enc' AND id_item_ex='$this->iid_item_ex'")) === FALSE) {
			$sClauError = 'EncargoHorarioExcepcion.eliminar';
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
		if (array_key_exists('id_enc',$aDades)) $this->setId_enc($aDades['id_enc']);
		if (array_key_exists('id_item_ex',$aDades)) $this->setId_item_ex($aDades['id_item_ex']);
		if (array_key_exists('id_item_h',$aDades)) $this->setId_item_h($aDades['id_item_h']);
		if (array_key_exists('f_ini',$aDades)) $this->setF_ini($aDades['f_ini'],$convert);
		if (array_key_exists('f_fin',$aDades)) $this->setF_fin($aDades['f_fin'],$convert);
		if (array_key_exists('desc_ex',$aDades)) $this->setDesc_ex($aDades['desc_ex']);
		if (array_key_exists('horario',$aDades)) $this->setHorario($aDades['horario']);
		if (array_key_exists('dia_ref',$aDades)) $this->setDia_ref($aDades['dia_ref']);
		if (array_key_exists('dia_num',$aDades)) $this->setDia_num($aDades['dia_num']);
		if (array_key_exists('mas_menos',$aDades)) $this->setMas_menos($aDades['mas_menos']);
		if (array_key_exists('dia_inc',$aDades)) $this->setDia_inc($aDades['dia_inc']);
		if (array_key_exists('h_ini',$aDades)) $this->setH_ini($aDades['h_ini']);
		if (array_key_exists('h_fin',$aDades)) $this->setH_fin($aDades['h_fin']);
		if (array_key_exists('n_sacd',$aDades)) $this->setN_sacd($aDades['n_sacd']);
		if (array_key_exists('mes',$aDades)) $this->setMes($aDades['mes']);
	}

	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de EncargoHorarioExcepcion en un array
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
	 * Recupera las claus primàries de EncargoHorarioExcepcion en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('id_enc' => $this->iid_enc,'id_item_ex' => $this->iid_item_ex);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Recupera l'atribut iid_enc de EncargoHorarioExcepcion
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
	 * estableix el valor de l'atribut iid_enc de EncargoHorarioExcepcion
	 *
	 * @param integer iid_enc
	 */
	function setId_enc($iid_enc) {
		$this->iid_enc = $iid_enc;
	}
	/**
	 * Recupera l'atribut iid_item_ex de EncargoHorarioExcepcion
	 *
	 * @return integer iid_item_ex
	 */
	function getId_item_ex() {
		if (!isset($this->iid_item_ex)) {
			$this->DBCarregar();
		}
		return $this->iid_item_ex;
	}
	/**
	 * estableix el valor de l'atribut iid_item_ex de EncargoHorarioExcepcion
	 *
	 * @param integer iid_item_ex
	 */
	function setId_item_ex($iid_item_ex) {
		$this->iid_item_ex = $iid_item_ex;
	}
	/**
	 * Recupera l'atribut iid_item_h de EncargoHorarioExcepcion
	 *
	 * @return integer iid_item_h
	 */
	function getId_item_h() {
		if (!isset($this->iid_item_h)) {
			$this->DBCarregar();
		}
		return $this->iid_item_h;
	}
	/**
	 * estableix el valor de l'atribut iid_item_h de EncargoHorarioExcepcion
	 *
	 * @param integer iid_item_h='' optional
	 */
	function setId_item_h($iid_item_h='') {
		$this->iid_item_h = $iid_item_h;
	}
	/**
	 * Recupera l'atribut df_ini de EncargoHorarioExcepcion
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
	 * estableix el valor de l'atribut df_ini de EncargoHorarioExcepcion
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
	 * Recupera l'atribut df_fin de EncargoHorarioExcepcion
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
	 * estableix el valor de l'atribut df_fin de EncargoHorarioExcepcion
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
	 * Recupera l'atribut sdesc_ex de EncargoHorarioExcepcion
	 *
	 * @return string sdesc_ex
	 */
	function getDesc_ex() {
		if (!isset($this->sdesc_ex)) {
			$this->DBCarregar();
		}
		return $this->sdesc_ex;
	}
	/**
	 * estableix el valor de l'atribut sdesc_ex de EncargoHorarioExcepcion
	 *
	 * @param string sdesc_ex='' optional
	 */
	function setDesc_ex($sdesc_ex='') {
		$this->sdesc_ex = $sdesc_ex;
	}
	/**
	 * Recupera l'atribut bhorario de EncargoHorarioExcepcion
	 *
	 * @return boolean bhorario
	 */
	function getHorario() {
		if (!isset($this->bhorario)) {
			$this->DBCarregar();
		}
		return $this->bhorario;
	}
	/**
	 * estableix el valor de l'atribut bhorario de EncargoHorarioExcepcion
	 *
	 * @param boolean bhorario='f' optional
	 */
	function setHorario($bhorario='f') {
		$this->bhorario = $bhorario;
	}
	/**
	 * Recupera l'atribut sdia_ref de EncargoHorarioExcepcion
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
	 * estableix el valor de l'atribut sdia_ref de EncargoHorarioExcepcion
	 *
	 * @param string sdia_ref='' optional
	 */
	function setDia_ref($sdia_ref='') {
		$this->sdia_ref = $sdia_ref;
	}
	/**
	 * Recupera l'atribut idia_num de EncargoHorarioExcepcion
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
	 * estableix el valor de l'atribut idia_num de EncargoHorarioExcepcion
	 *
	 * @param integer idia_num='' optional
	 */
	function setDia_num($idia_num='') {
		$this->idia_num = $idia_num;
	}
	/**
	 * Recupera l'atribut smas_menos de EncargoHorarioExcepcion
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
	 * estableix el valor de l'atribut smas_menos de EncargoHorarioExcepcion
	 *
	 * @param string smas_menos='' optional
	 */
	function setMas_menos($smas_menos='') {
		$this->smas_menos = $smas_menos;
	}
	/**
	 * Recupera l'atribut idia_inc de EncargoHorarioExcepcion
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
	 * estableix el valor de l'atribut idia_inc de EncargoHorarioExcepcion
	 *
	 * @param integer idia_inc='' optional
	 */
	function setDia_inc($idia_inc='') {
		$this->idia_inc = $idia_inc;
	}
	/**
	 * Recupera l'atribut th_ini de EncargoHorarioExcepcion
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
	 * estableix el valor de l'atribut th_ini de EncargoHorarioExcepcion
	 *
	 * @param string time th_ini='' optional
	 */
	function setH_ini($th_ini='') {
		$this->th_ini = $th_ini;
	}
	/**
	 * Recupera l'atribut th_fin de EncargoHorarioExcepcion
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
	 * estableix el valor de l'atribut th_fin de EncargoHorarioExcepcion
	 *
	 * @param string time th_fin='' optional
	 */
	function setH_fin($th_fin='') {
		$this->th_fin = $th_fin;
	}
	/**
	 * Recupera l'atribut in_sacd de EncargoHorarioExcepcion
	 *
	 * @return integer in_sacd
	 */
	function getN_sacd() {
		if (!isset($this->in_sacd)) {
			$this->DBCarregar();
		}
		return $this->in_sacd;
	}
	/**
	 * estableix el valor de l'atribut in_sacd de EncargoHorarioExcepcion
	 *
	 * @param integer in_sacd='' optional
	 */
	function setN_sacd($in_sacd='') {
		$this->in_sacd = $in_sacd;
	}
	/**
	 * Recupera l'atribut imes de EncargoHorarioExcepcion
	 *
	 * @return integer imes
	 */
	function getMes() {
		if (!isset($this->imes)) {
			$this->DBCarregar();
		}
		return $this->imes;
	}
	/**
	 * estableix el valor de l'atribut imes de EncargoHorarioExcepcion
	 *
	 * @param integer imes='' optional
	 */
	function setMes($imes='') {
		$this->imes = $imes;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oEncargoHorarioExcepcionSet = new core\Set();

		$oEncargoHorarioExcepcionSet->add($this->getDatosId_item_h());
		$oEncargoHorarioExcepcionSet->add($this->getDatosF_ini());
		$oEncargoHorarioExcepcionSet->add($this->getDatosF_fin());
		$oEncargoHorarioExcepcionSet->add($this->getDatosDesc_ex());
		$oEncargoHorarioExcepcionSet->add($this->getDatosHorario());
		$oEncargoHorarioExcepcionSet->add($this->getDatosDia_ref());
		$oEncargoHorarioExcepcionSet->add($this->getDatosDia_num());
		$oEncargoHorarioExcepcionSet->add($this->getDatosMas_menos());
		$oEncargoHorarioExcepcionSet->add($this->getDatosDia_inc());
		$oEncargoHorarioExcepcionSet->add($this->getDatosH_ini());
		$oEncargoHorarioExcepcionSet->add($this->getDatosH_fin());
		$oEncargoHorarioExcepcionSet->add($this->getDatosN_sacd());
		$oEncargoHorarioExcepcionSet->add($this->getDatosMes());
		return $oEncargoHorarioExcepcionSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut iid_item_h de EncargoHorarioExcepcion
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_item_h() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_item_h'));
		$oDatosCampo->setEtiqueta(_("id_item_h"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut df_ini de EncargoHorarioExcepcion
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
	 * Recupera les propietats de l'atribut df_fin de EncargoHorarioExcepcion
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
	 * Recupera les propietats de l'atribut sdesc_ex de EncargoHorarioExcepcion
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosDesc_ex() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'desc_ex'));
		$oDatosCampo->setEtiqueta(_("desc_ex"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut bhorario de EncargoHorarioExcepcion
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosHorario() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'horario'));
		$oDatosCampo->setEtiqueta(_("horario"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sdia_ref de EncargoHorarioExcepcion
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
	 * Recupera les propietats de l'atribut idia_num de EncargoHorarioExcepcion
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
	 * Recupera les propietats de l'atribut smas_menos de EncargoHorarioExcepcion
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
	 * Recupera les propietats de l'atribut idia_inc de EncargoHorarioExcepcion
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
	 * Recupera les propietats de l'atribut th_ini de EncargoHorarioExcepcion
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
	 * Recupera les propietats de l'atribut th_fin de EncargoHorarioExcepcion
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
	 * Recupera les propietats de l'atribut in_sacd de EncargoHorarioExcepcion
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosN_sacd() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'n_sacd'));
		$oDatosCampo->setEtiqueta(_("n_sacd"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut imes de EncargoHorarioExcepcion
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosMes() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'mes'));
		$oDatosCampo->setEtiqueta(_("mes"));
		return $oDatosCampo;
	}
}