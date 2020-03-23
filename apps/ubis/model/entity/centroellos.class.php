<?php
namespace ubis\model\entity;
use core;
/**
 * Classe que implementa l'entitat u_centros
 * Está ala base de dades comun, per poder accedir des de l'altre secció.
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 2/01/2020
 */

class CentroEllos Extends Centro {
	/* ATRIBUTS ----------------------------------------------------------------- */
    /**
     * Id_zona de CentroDl
     *
     * @var integer
     */
    protected $iid_zona;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/* CONSTRUCTOR -------------------------------------------------------------- */

 	/**
	 * Constructor de la classe.
	 * Si només necessita un valor, se li pot passar un integer.
	 * En general se li passa un array amb les claus primàries.
	 *
	 * @param integer|array iid_ubi
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBC'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				$nom_id='i'.$nom_id; //imagino que es un integer
				if ($val_id !== '') $this->$nom_id = intval($val_id); // evitem SQL injection fent cast a integer
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_ubi = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('id_ubi' => $this->iid_ubi);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('cu_centros_dl');
	}

	/* METODES PUBLICS ----------------------------------------------------------*/
	/**
	 *  s'ha de fer diferent perqué he afegit el id_zona
	 *
	 */
	public function DBGuardar() {
	    $oDbl = $this->getoDbl();
	    $nom_tabla = $this->getNomTabla();
	    if ($this->DBCarregar('guardar') === false) { $bInsert=true; } else { $bInsert=false; }
	    $aDades=array();
	    $aDades['tipo_ubi'] = $this->stipo_ubi;
	    $aDades['nombre_ubi'] = $this->snombre_ubi;
	    $aDades['dl'] = $this->sdl;
	    $aDades['pais'] = $this->spais;
	    $aDades['region'] = $this->sregion;
	    $aDades['status'] = $this->bstatus;
	    $aDades['f_status'] = $this->df_status;
	    $aDades['sv'] = $this->bsv;
	    $aDades['sf'] = $this->bsf;
	    $aDades['tipo_ctr'] = $this->stipo_ctr;
	    $aDades['tipo_labor'] = $this->itipo_labor;
	    $aDades['cdc'] = $this->bcdc;
	    $aDades['id_ctr_padre'] = $this->iid_ctr_padre;
	    $aDades['id_zona'] = $this->iid_zona;
	    array_walk($aDades, 'core\poner_null');
	    //para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
	    if ( core\is_true($aDades['status']) ) { $aDades['status']='true'; } else { $aDades['status']='false'; }
	    if ( core\is_true($aDades['sv']) ) { $aDades['sv']='true'; } else { $aDades['sv']='false'; }
	    if ( core\is_true($aDades['sf']) ) { $aDades['sf']='true'; } else { $aDades['sf']='false'; }
	    if ( core\is_true($aDades['cdc']) ) { $aDades['cdc']='true'; } else { $aDades['cdc']='false'; }
	    
	    if ($bInsert === false) {
	        //UPDATE
	        $update="
					tipo_ubi                 = :tipo_ubi,
					nombre_ubi               = :nombre_ubi,
					dl                       = :dl,
					pais                     = :pais,
					region                   = :region,
					status                   = :status,
					f_status                 = :f_status,
					sv                       = :sv,
					sf                       = :sf,
					tipo_ctr                 = :tipo_ctr,
					tipo_labor               = :tipo_labor,
					cdc                      = :cdc,
					id_ctr_padre             = :id_ctr_padre,
					id_zona                  = :id_zona
					";
	        //print_r($aDades);
	        if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_ubi='$this->iid_ubi'")) === false) {
	            $sClauError = 'CentroDl.update.prepare';
	            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	            return false;
	        } else {
	            if ($oDblSt->execute($aDades) === false) {
	                $sClauError = 'CentroDl.update.execute';
	                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
	                return false;
	            }
	        }
	    } else {
	        // INSERT
	        // Aqui si hay que poner el id_ubi, pues es copia de DB-sv
	        $aDades['id_ubi'] = $this->iid_ubi;
	        
	        $campos="(id_ubi,tipo_ubi,nombre_ubi,dl,pais,region,status,f_status,sv,sf,tipo_ctr,tipo_labor,cdc,id_ctr_padre,id_zona)";
	        $valores="(:id_ubi,:tipo_ubi,:nombre_ubi,:dl,:pais,:region,:status,:f_status,:sv,:sf,:tipo_ctr,:tipo_labor,:cdc,:id_ctr_padre,:id_zona)";
	        if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
	            $sClauError = 'CentroDl.insertar.prepare';
	            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	            return false;
	        } else {
	            if ($oDblSt->execute($aDades) === false) {
	                $sClauError = 'CentroDl.insertar.execute';
	                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
	                return false;
	            }
	        }
	    }
	    $this->setAllAtributes($aDades);
	    return true;
	}
	
	/**
	 * Carrega els camps de la base de dades com atributs de l'objecte.
	 *
	 */
	public function DBCarregar($que=null) {
	    $oDbl = $this->getoDbl();
	    $nom_tabla = $this->getNomTabla();
	    if (isset($this->iid_ubi)) {
	        if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_ubi='$this->iid_ubi'")) === false) {
	            $sClauError = 'CentroDl.carregar';
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
					// En el caso de no existir esta fila, $aDades = FALSE:
					if ($aDades === FALSE) {
						$this->setNullAllAtributes();
					} else {
						$this->setAllAtributes($aDades);
					}
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
	    if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_ubi='$this->iid_ubi'")) === false) {
	        $sClauError = 'CentroDl.eliminar';
	        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	        return false;
	    }
	    return true;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/
	/* METODES PRIVATS ----------------------------------------------------------*/
	
	/**
	 * Estableix el valor de tots els atributs
	 *
	 * @param array $aDades
	 */
	function setAllAtributes($aDades,$convert=FALSE) {
	    //print_r($aDades);
	    if (!is_array($aDades)) return;
	    if (array_key_exists('id_schema',$aDades)) $this->setId_schema($aDades['id_schema']);
	    if (array_key_exists('id_ubi',$aDades)) $this->setId_ubi($aDades['id_ubi']);
	    if (array_key_exists('tipo_ubi',$aDades)) $this->setTipo_ubi($aDades['tipo_ubi']);
	    if (array_key_exists('nombre_ubi',$aDades)) $this->setNombre_ubi($aDades['nombre_ubi']);
	    if (array_key_exists('dl',$aDades)) $this->setDl($aDades['dl']);
	    if (array_key_exists('pais',$aDades)) $this->setPais($aDades['pais']);
	    if (array_key_exists('region',$aDades)) $this->setRegion($aDades['region']);
	    if (array_key_exists('status',$aDades)) $this->setStatus($aDades['status']);
	    if (array_key_exists('f_status',$aDades)) $this->setF_status($aDades['f_status'],$convert);
	    if (array_key_exists('sv',$aDades)) $this->setSv($aDades['sv']);
	    if (array_key_exists('sf',$aDades)) $this->setSf($aDades['sf']);
	    if (array_key_exists('tipo_ctr',$aDades)) $this->setTipo_ctr($aDades['tipo_ctr']);
	    if (array_key_exists('tipo_labor',$aDades)) $this->setTipo_labor($aDades['tipo_labor']);
	    if (array_key_exists('cdc',$aDades)) $this->setCdc($aDades['cdc']);
	    if (array_key_exists('id_ctr_padre',$aDades)) $this->setId_ctr_padre($aDades['id_ctr_padre']);
	    if (array_key_exists('id_zona',$aDades)) $this->setId_zona($aDades['id_zona']);
	}

	/**
	 * Estableix a empty el valor de tots els atributs
	 *
	 */
	function setNullAllAtributes() {
		$aPK = $this->getPrimary_key();
	    $this->setId_schema('');
		$this->setId_ubi('');
		$this->setTipo_ubi('');
		$this->setNombre_ubi('');
		$this->setDl('');
		$this->setPais('');
		$this->setRegion('');
		$this->setStatus('');
		$this->setF_status('');
		$this->setSv('');
		$this->setSf('');
		$this->setTipo_ctr('');
		$this->setTipo_labor('');
		$this->setCdc('');
		$this->setId_ctr_padre('');
		$this->setId_zona('');
		$this->setPrimary_key($aPK);
	}

	
	/* METODES GET i SET --------------------------------------------------------*/
	/**
	 * Recupera l'atribut iid_zona de CentroEllos
	 *
	 * @return integer iid_zona
	 */
	function getId_zona() {
	    if (!isset($this->iid_zona)) {
	        $this->DBCarregar();
	    }
	    return $this->iid_zona;
	}
	/**
	 * estableix el valor de l'atribut iid_zona de CentroEllos
	 *
	 * @param integer iid_zona='' optional
	 */
	function setId_zona($iid_zona='') {
	    $this->iid_zona = $iid_zona;
	}
	
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/
	
	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
	    $oCentroooDlSet = new core\Set();
	    
	    $oCentroooDlSet->add($this->getDatosTipo_ubi());
	    $oCentroooDlSet->add($this->getDatosNombre_ubi());
	    $oCentroooDlSet->add($this->getDatosDl());
	    $oCentroooDlSet->add($this->getDatosPais());
	    $oCentroooDlSet->add($this->getDatosRegion());
	    $oCentroooDlSet->add($this->getDatosStatus());
	    $oCentroooDlSet->add($this->getDatosF_status());
	    $oCentroooDlSet->add($this->getDatosSv());
	    $oCentroooDlSet->add($this->getDatosSf());
	    $oCentroooDlSet->add($this->getDatosTipo_ctr());
	    $oCentroooDlSet->add($this->getDatosTipo_labor());
	    $oCentroooDlSet->add($this->getDatosCdc());
	    $oCentroooDlSet->add($this->getDatosId_ctr_padre());
	    $oCentroooDlSet->add($this->getDatosId_zona());
	    return $oCentroooDlSet->getTot();
	}
	
}