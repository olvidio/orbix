<?php
namespace encargossacd\model\entity;
use core;
use web;
/**
 * Fitxer amb la Classe que accedeix a la taula encargo_horario
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/01/2019
 */
/**
 * Classe que implementa l'entitat encargo_horario
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/01/2019
 */
class EncargoHorario Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de EncargoHorario
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de EncargoHorario
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_enc de EncargoHorario
	 *
	 * @var integer
	 */
	 private $iid_enc;
	/**
	 * Id_item_h de EncargoHorario
	 *
	 * @var integer
	 */
	 private $iid_item_h;
	/**
	 * F_ini de EncargoHorario
	 *
	 * @var web\DateTimeLocal
	 */
	 private $df_ini;
	/**
	 * F_fin de EncargoHorario
	 *
	 * @var web\DateTimeLocal
	 */
	 private $df_fin;
	/**
	 * Dia_ref de EncargoHorario
	 *
	 * @var string
	 */
	 private $sdia_ref;
	/**
	 * Dia_num de EncargoHorario
	 *
	 * @var integer
	 */
	 private $idia_num;
	/**
	 * Mas_menos de EncargoHorario
	 *
	 * @var string
	 */
	 private $smas_menos;
	/**
	 * Dia_inc de EncargoHorario
	 *
	 * @var integer
	 */
	 private $idia_inc;
	/**
	 * H_ini de EncargoHorario
	 *
	 * @var string time
	 */
	 private $th_ini;
	/**
	 * H_fin de EncargoHorario
	 *
	 * @var string time
	 */
	 private $th_fin;
	/**
	 * N_sacd de EncargoHorario
	 *
	 * @var integer
	 */
	 private $in_sacd;
	/**
	 * Mes de EncargoHorario
	 *
	 * @var integer
	 */
	 private $imes;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de EncargoHorario
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de EncargoHorario
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
	 * @param integer|array iid_enc,iid_item_h
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBE'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_enc') && $val_id !== '') $this->iid_enc = (int)$val_id; // evitem SQL injection fent cast a integer
				if (($nom_id == 'id_item_h') && $val_id !== '') $this->iid_item_h = (int)$val_id; // evitem SQL injection fent cast a integer
			}	
		} else {
            if (isset($a_id) && $a_id !== '') {
                $this->iid_item_h = intval($a_id); // evitem SQL injection fent cast a integer
                $this->aPrimary_key = array('id_item_h' => $this->iid_item_h);
            }
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('encargo_horario');
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
		$aDades['f_ini'] = $this->df_ini;
		$aDades['f_fin'] = $this->df_fin;
		$aDades['dia_ref'] = $this->sdia_ref;
		$aDades['dia_num'] = $this->idia_num;
		$aDades['mas_menos'] = $this->smas_menos;
		$aDades['dia_inc'] = $this->idia_inc;
		$aDades['h_ini'] = $this->th_ini;
		$aDades['h_fin'] = $this->th_fin;
		$aDades['n_sacd'] = $this->in_sacd;
		$aDades['mes'] = $this->imes;
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === FALSE) {
			//UPDATE
			$update="
					id_enc                   = :id_enc,
					f_ini                    = :f_ini,
					f_fin                    = :f_fin,
					dia_ref                  = :dia_ref,
					dia_num                  = :dia_num,
					mas_menos                = :mas_menos,
					dia_inc                  = :dia_inc,
					h_ini                    = :h_ini,
					h_fin                    = :h_fin,
					n_sacd                   = :n_sacd,
					mes                      = :mes";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item_h='$this->iid_item_h'")) === FALSE) {
				$sClauError = 'EncargoHorario.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = 'EncargoHorario.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
		} else {
			// INSERT
			array_unshift($aDades, $this->iid_item_h);
			$campos="(id_enc,f_ini,f_fin,dia_ref,dia_num,mas_menos,dia_inc,h_ini,h_fin,n_sacd,mes)";
			$valores="(:id_enc,:f_ini,:f_fin,:dia_ref,:dia_num,:mas_menos,:dia_inc,:h_ini,:h_fin,:n_sacd,:mes)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
				$sClauError = 'EncargoHorario.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = 'EncargoHorario.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
			$this->id_item_h = $oDbl->lastInsertId('encargo_horario_id_item_h_seq');
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
		if (isset($this->iid_item_h)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_item_h='$this->iid_item_h'")) === FALSE) {
				$sClauError = 'EncargoHorario.carregar';
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
		if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_item_h='$this->iid_item_h'")) === FALSE) {
			$sClauError = 'EncargoHorario.eliminar';
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
		if (array_key_exists('id_item_h',$aDades)) $this->setId_item_h($aDades['id_item_h']);
		if (array_key_exists('f_ini',$aDades)) $this->setF_ini($aDades['f_ini'],$convert);
		if (array_key_exists('f_fin',$aDades)) $this->setF_fin($aDades['f_fin'],$convert);
		if (array_key_exists('dia_ref',$aDades)) $this->setDia_ref($aDades['dia_ref']);
		if (array_key_exists('dia_num',$aDades)) $this->setDia_num($aDades['dia_num']);
		if (array_key_exists('mas_menos',$aDades)) $this->setMas_menos($aDades['mas_menos']);
		if (array_key_exists('dia_inc',$aDades)) $this->setDia_inc($aDades['dia_inc']);
		if (array_key_exists('h_ini',$aDades)) $this->setH_ini($aDades['h_ini']);
		if (array_key_exists('h_fin',$aDades)) $this->setH_fin($aDades['h_fin']);
		if (array_key_exists('n_sacd',$aDades)) $this->setN_sacd($aDades['n_sacd']);
		if (array_key_exists('mes',$aDades)) $this->setMes($aDades['mes']);
	}

	/**
	 * Estableix a empty el valor de tots els atributs
	 *
	 */
	function setNullAllAtributes() {
		$aPK = $this->getPrimary_key();
		$this->setId_enc('');
		$this->setId_item_h('');
		$this->setF_ini('');
		$this->setF_fin('');
		$this->setDia_ref('');
		$this->setDia_num('');
		$this->setMas_menos('');
		$this->setDia_inc('');
		$this->setH_ini('');
		$this->setH_fin('');
		$this->setN_sacd('');
		$this->setMes('');
		$this->setPrimary_key($aPK);
	}


	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de EncargoHorario en un array
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
	 * Recupera las claus primàries de EncargoHorario en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('id_enc' => $this->iid_enc,'id_item_h' => $this->iid_item_h);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Estableix las claus primàries de EncargoHorario en un array
	 *
	 * @return array aPrimary_key
	 */
	public function setPrimary_key($a_id='') {
	    if (is_array($a_id)) {
	        $this->aPrimary_key = $a_id;
	        foreach($a_id as $nom_id=>$val_id) {
	            if (($nom_id == 'id_enc') && $val_id !== '') $this->iid_enc = (int)$val_id; // evitem SQL injection fent cast a integer
	            if (($nom_id == 'id_item_h') && $val_id !== '') $this->iid_item_h = (int)$val_id; // evitem SQL injection fent cast a integer
	        }
	    }
	}
	
	/**
	 * Recupera l'atribut iid_enc de EncargoHorario
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
	 * estableix el valor de l'atribut iid_enc de EncargoHorario
	 *
	 * @param integer iid_enc
	 */
	function setId_enc($iid_enc) {
		$this->iid_enc = $iid_enc;
	}
	/**
	 * Recupera l'atribut iid_item_h de EncargoHorario
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
	 * estableix el valor de l'atribut iid_item_h de EncargoHorario
	 *
	 * @param integer iid_item_h
	 */
	function setId_item_h($iid_item_h) {
		$this->iid_item_h = $iid_item_h;
	}
	/**
	 * Recupera l'atribut df_ini de EncargoHorario
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
	 * estableix el valor de l'atribut df_ini de EncargoHorario
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
	 * Recupera l'atribut df_fin de EncargoHorario
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
	 * estableix el valor de l'atribut df_fin de EncargoHorario
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
	 * Recupera l'atribut sdia_ref de EncargoHorario
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
	 * estableix el valor de l'atribut sdia_ref de EncargoHorario
	 *
	 * @param string sdia_ref='' optional
	 */
	function setDia_ref($sdia_ref='') {
		$this->sdia_ref = $sdia_ref;
	}
	/**
	 * Recupera l'atribut idia_num de EncargoHorario
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
	 * estableix el valor de l'atribut idia_num de EncargoHorario
	 *
	 * @param integer idia_num='' optional
	 */
	function setDia_num($idia_num='') {
		$this->idia_num = $idia_num;
	}
	/**
	 * Recupera l'atribut smas_menos de EncargoHorario
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
	 * estableix el valor de l'atribut smas_menos de EncargoHorario
	 *
	 * @param string smas_menos='' optional
	 */
	function setMas_menos($smas_menos='') {
		$this->smas_menos = $smas_menos;
	}
	/**
	 * Recupera l'atribut idia_inc de EncargoHorario
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
	 * estableix el valor de l'atribut idia_inc de EncargoHorario
	 *
	 * @param integer idia_inc='' optional
	 */
	function setDia_inc($idia_inc='') {
		$this->idia_inc = $idia_inc;
	}
	/**
	 * Recupera l'atribut th_ini de EncargoHorario
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
	 * estableix el valor de l'atribut th_ini de EncargoHorario
	 *
	 * @param string time th_ini='' optional
	 */
	function setH_ini($th_ini='') {
		$this->th_ini = $th_ini;
	}
	/**
	 * Recupera l'atribut th_fin de EncargoHorario
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
	 * estableix el valor de l'atribut th_fin de EncargoHorario
	 *
	 * @param string time th_fin='' optional
	 */
	function setH_fin($th_fin='') {
		$this->th_fin = $th_fin;
	}
	/**
	 * Recupera l'atribut in_sacd de EncargoHorario
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
	 * estableix el valor de l'atribut in_sacd de EncargoHorario
	 *
	 * @param integer in_sacd='' optional
	 */
	function setN_sacd($in_sacd='') {
		$this->in_sacd = $in_sacd;
	}
	/**
	 * Recupera l'atribut imes de EncargoHorario
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
	 * estableix el valor de l'atribut imes de EncargoHorario
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
		$oEncargoHorarioSet = new core\Set();

		$oEncargoHorarioSet->add($this->getDatosF_ini());
		$oEncargoHorarioSet->add($this->getDatosF_fin());
		$oEncargoHorarioSet->add($this->getDatosDia_ref());
		$oEncargoHorarioSet->add($this->getDatosDia_num());
		$oEncargoHorarioSet->add($this->getDatosMas_menos());
		$oEncargoHorarioSet->add($this->getDatosDia_inc());
		$oEncargoHorarioSet->add($this->getDatosH_ini());
		$oEncargoHorarioSet->add($this->getDatosH_fin());
		$oEncargoHorarioSet->add($this->getDatosN_sacd());
		$oEncargoHorarioSet->add($this->getDatosMes());
		return $oEncargoHorarioSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut df_ini de EncargoHorario
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
	 * Recupera les propietats de l'atribut df_fin de EncargoHorario
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
	 * Recupera les propietats de l'atribut sdia_ref de EncargoHorario
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
	 * Recupera les propietats de l'atribut idia_num de EncargoHorario
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
	 * Recupera les propietats de l'atribut smas_menos de EncargoHorario
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
	 * Recupera les propietats de l'atribut idia_inc de EncargoHorario
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
	 * Recupera les propietats de l'atribut th_ini de EncargoHorario
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
	 * Recupera les propietats de l'atribut th_fin de EncargoHorario
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
	 * Recupera les propietats de l'atribut in_sacd de EncargoHorario
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
	 * Recupera les propietats de l'atribut imes de EncargoHorario
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
