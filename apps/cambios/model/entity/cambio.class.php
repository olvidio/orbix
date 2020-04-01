<?php
namespace cambios\model\entity;
use actividades\model\entity\Actividad;
use cambios\model\gestorAvisoCambios;
use core\ConfigGlobal;
use core;
use personas\model\entity\Persona;
use procesos\model\entity\GestorActividadFase;
use procesos\model\entity\GestorActividadProcesoTarea;
use ubis\model\entity\Ubi;
use web\DateTimeLocal;
use personas\model\entity\PersonaSacd;
/**
 * Fitxer amb la Classe que accedeix a la taula av_cambios
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 17/4/2019
 */
/**
 * Classe que implementa l'entitat av_cambios
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 17/4/2019
 */
class Cambio Extends core\ClasePropiedades {
    
    //  tipo cambio constants.
    const TIPO_CMB_INSERT		 = 1;
    const TIPO_CMB_UPDATE	 	 = 2;
    const TIPO_CMB_DELETE	 	 = 3;
    const TIPO_CMB_FASE	 	  	 = 4;
	
    /* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de Cambio
	 *
	 * @var array
	 */
	 protected $aPrimary_key;

	/**
	 * aDades de Cambio
	 *
	 * @var array
	 */
	 protected $aDades;

	/**
	 * Id_schema de Cambio
	 *
	 * @var integer
	 */
	 protected $iid_schema;
	/**
	 * Id_item_cambio de Cambio
	 *
	 * @var integer
	 */
	 protected $iid_item_cambio;
	/**
	 * Id_tipo_cambio de Cambio
	 *
	 * @var integer
	 */
	 protected $iid_tipo_cambio;
	/**
	 * Id_activ de Cambio
	 *
	 * @var integer
	 */
	 protected $iid_activ;
	/**
	 * Id_tipo_activ de Cambio
	 *
	 * @var integer
	 */
	 protected $iid_tipo_activ;
	/**
	 * Id_fase_sv de Cambio
	 *
	 * @var integer
	 */
	 protected $iid_fase_sv;
	/**
	 * Id_fase_sf de Cambio
	 *
	 * @var integer
	 */
	 protected $iid_fase_sf;
	/**
	 * Id_status de Cambio
	 *
	 * @var integer
	 */
	 protected $iid_status;
	/**
	 * Dl_org de Cambio
	 *
	 * @var string
	 */
	 protected $sdl_org;
	/**
	 * Objeto de Cambio
	 *
	 * @var string
	 */
	 protected $sobjeto;
	/**
	 * Propiedad de Cambio
	 *
	 * @var string
	 */
	 protected $spropiedad;
	/**
	 * Valor_old de Cambio
	 *
	 * @var string
	 */
	 protected $svalor_old;
	/**
	 * Valor_new de Cambio
	 *
	 * @var string
	 */
	 protected $svalor_new;
	/**
	 * Quien_cambia de Cambio
	 *
	 * @var integer
	 */
	 protected $iquien_cambia;
	/**
	 * Sfsv_quien_cambia de Cambio
	 *
	 * @var integer
	 */
	 protected $isfsv_quien_cambia;
	/**
	 * Timestamp_cambio de Cambio
	 *
	 * @var integer
	 */
	 protected $itimestamp_cambio;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de Cambio
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de Cambio
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
	 * @param integer|array iid_item_cambio
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBPC'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_item_cambio') && $val_id !== '') $this->iid_item_cambio = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_item_cambio = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('id_item_cambio' => $this->iid_item_cambio);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('av_cambios');
	}

	/* METODES PUBLICS ----------------------------------------------------------*/

	/**
	 * Desa els atributs de l'objecte a la base de dades.
	 * Si no hi ha el registre, fa el insert, si hi es fa el update.
	 *
	 *@param bool optional $quiet : true per que no apunti els canvis. 0 (per defecte) apunta els canvis.
	 */
	public function DBGuardar($quiet=0) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		if ($this->DBCarregar('guardar') === FALSE) { $bInsert=TRUE; } else { $bInsert=FALSE; }
		$aDades=array();
		$aDades['id_tipo_cambio'] = $this->iid_tipo_cambio;
		$aDades['id_activ'] = $this->iid_activ;
		$aDades['id_tipo_activ'] = $this->iid_tipo_activ;
		$aDades['id_fase_sv'] = $this->iid_fase_sv;
		$aDades['id_fase_sf'] = $this->iid_fase_sf;
		$aDades['id_status'] = $this->iid_status;
		$aDades['dl_org'] = $this->sdl_org;
		$aDades['objeto'] = $this->sobjeto;
		$aDades['propiedad'] = $this->spropiedad;
		$aDades['valor_old'] = $this->svalor_old;
		$aDades['valor_new'] = $this->svalor_new;
		$aDades['quien_cambia'] = $this->iquien_cambia;
		$aDades['sfsv_quien_cambia'] = $this->isfsv_quien_cambia;
		$aDades['timestamp_cambio'] = $this->itimestamp_cambio;
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === FALSE) {
			//UPDATE
			$update="
					id_tipo_cambio           = :id_tipo_cambio,
					id_activ                 = :id_activ,
					id_tipo_activ            = :id_tipo_activ,
                    id_fase_sv               = :id_fase_sv,
					id_fase_sf               = :id_fase_sf,
					id_status                = :id_status,
					dl_org                   = :dl_org,
					objeto                   = :objeto,
					propiedad                = :propiedad,
					valor_old                = :valor_old,
					valor_new                = :valor_new,
					quien_cambia             = :quien_cambia,
					sfsv_quien_cambia        = :sfsv_quien_cambia,
					timestamp_cambio         = :timestamp_cambio
                    ";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item_cambio='$this->iid_item_cambio'")) === FALSE) {
				$sClauError = 'Cambio.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = 'Cambio.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
		} else {
			// INSERT
			/* uso el id_schema 3000, que no debería corresponder a ningun esquema (en todo caso a 'public')
			 * Es para el caso de las dl que no tienen instalado el módulo de 'cambios'. Para distinguir los cambios
			 * debo usar la dl_org. No uso el id_schema correspondiente, porque si más tarde instalan el módulo
			 * 'cambios', puede haber conflico con el id_item_cambio. 
			 */
			$mi_esquema = 3000;
			$campos="(id_schema,id_tipo_cambio,id_activ,id_tipo_activ,id_fase_sv,id_fase_sf,id_status,dl_org,objeto,propiedad,valor_old,valor_new,quien_cambia,sfsv_quien_cambia,timestamp_cambio)";
			$valores="($mi_esquema,:id_tipo_cambio,:id_activ,:id_tipo_activ,:id_fase_sv,:id_fase_sf,:id_status,:dl_org,:objeto,:propiedad,:valor_old,:valor_new,:quien_cambia,:sfsv_quien_cambia,:timestamp_cambio)";
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
				$sClauError = 'Cambio.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = 'Cambio.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
			$id_seq =  $nom_tabla."_id_item_cambio_seq";
			$this->id_item_cambio = $oDbl->lastInsertId($id_seq);
		}
		$this->setAllAtributes($aDades);
		// Creo que solo hay que disparar el generador de avisos en las dl que tengan el módulo.
		if (ConfigGlobal::is_app_installed('cambios')) {
		    // Para el caso de poner anotado, no debo disparar el generador de avisos.
		    if (empty($quiet)) {
		        $this->generarTabla();
		    }
		}
		return TRUE;
	}

	/**
	 * Carrega els camps de la base de dades com atributs de l'objecte.
	 *
	 */
	public function DBCarregar($que=null) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		if (isset($this->iid_item_cambio)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_item_cambio='$this->iid_item_cambio'")) === FALSE) {
				$sClauError = 'Cambio.carregar';
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
		if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_item_cambio='$this->iid_item_cambio'")) === FALSE) {
			$sClauError = 'Cambio.eliminar';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
		return TRUE;
	}
	
	/* METODES ALTRES  ----------------------------------------------------------*/
	
	protected function getNomActivEliminada($iId) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
	    if (($qRs = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_activ=$iId AND id_tipo_cambio=3")) === false) {
	        $sClauError = 'ActividadCambio.NomActivEliminada';
	        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	        return false;
	    }
	    $aDades = $qRs->fetch(\PDO::FETCH_ASSOC);
	    $nomActiv = $aDades['valor_old']."("._('Eliminado').")";
	    return $nomActiv;
	}
	
	public function getAvisoTxt() {
	    $bEliminada = false;
	    $sPropiedad = '';
	    $sValor_old = '';
	    $sValor_new = '';
	    //$sformat = 'la actividad "%1$s" ha cambiado el campo "%2$s" de "%3$s" a "%4$s" (por %5$s)';
	    $iTipo_cambio = $this->getId_tipo_cambio();
	    $sObjeto = $this->getObjeto();
	    $iId = $this->getId_activ();
	    $sPropiedad = $this->getPropiedad();
	    $sValor_old = $this->getValor_old();
	    $sValor_new = $this->getValor_new();
	    
	    $oActividad = new Actividad($iId);
	    $id_statusActual = $oActividad->getStatus();
	    $DatosCampoStatus = $oActividad->getDatosStatus();
	    $aStatus = $DatosCampoStatus->getLista();
	    
	    $sNomActiv = $oActividad->getNom_activ();
	    if (empty($sNomActiv)) { // se ha eliminado. Busco el nombre en el apunte eliminado
	        $bEliminada = true;
	        $sNomActiv = $this->getNomActivEliminada($iId);
	    }
	    
	    $sPropiedad = empty($sPropiedad)? '-':$sPropiedad;
	    
	    /*
	     * De momento, desde el servidor exterior (conexión a mail) solo 
	     * están accesibles los sacd.
	     * 
	     */
	    if ($sPropiedad == 'id_nom') {
	        if (!empty($sValor_old)) {
	            //$oPersona = Persona::NewPersona($sValor_old);
	            $oPersona = new PersonaSacd($sValor_old);
	            $sValor_old = $oPersona->getApellidosNombre();
	        }
	        if (!empty($sValor_new)) {
	            //$oPersona = Persona::NewPersona($sValor_new);
	            $oPersona = new PersonaSacd($sValor_new);
	            $sValor_new = $oPersona->getApellidosNombre();
	        }
	    }
	    if ($sPropiedad == 'id_ubi') {
            if (!empty($sValor_old)) {
                $oUbi = Ubi::NewUbi($sValor_old);
                $sValor_old = $oUbi->getNombre_ubi();
            }
            if (!empty($sValor_new)) {
                $oUbi = Ubi::NewUbi($sValor_new);
                $sValor_new = $oUbi->getNombre_ubi();
            }
	    }
	    /* Per posar noms que s'entenguin als camps de l'activitat */
	    if ($sObjeto == 'Actividad' && $sPropiedad == 'status') {
	        $sValor_old = $aStatus[$sValor_old];
	        $sValor_new = $aStatus[$sValor_new];
	    }
	    if ($sObjeto == 'Actividad' && $sPropiedad == 'tarifa') {
	        $aTarifas = $this->getTarifas();
	        $sValor_old = empty($sValor_old)? $sValor_old : $aTarifas[$sValor_old];
	        $sValor_new = empty($sValor_new)? $sValor_new : $aTarifas[$sValor_new];
	    }
	    
	    $ObjetoFullPath = gestorAvisoCambios::getFullPathObj($sObjeto);
	    $oObject = new $ObjetoFullPath();
	    $cDatosCampos = $oObject->getDatosCampos();
	    $etiqueta = $sPropiedad;
	    foreach ($cDatosCampos as $oDatosCampo) {
	        if ($oDatosCampo->getNom_camp() == $sPropiedad) {
                $etiqueta = $oDatosCampo->getEtiqueta();
	        }
	    }
	    
	    $sValor_old = empty($sValor_old)? '-':$sValor_old;
	    $sValor_new = empty($sValor_new)? '-':$sValor_new;
	    
	    $sformat = '';
	    switch($iTipo_cambio) {
	        case Cambio::TIPO_CMB_INSERT: // (1) insert.
	            switch($sObjeto) {
	                case 'Actividad':
	                case 'ActividadDl':
	                case 'ActividadEx':
	                    $sformat = 'Actividad: se ha creado la actividad "%1$s"';
	                    break;
	                case 'ActividadCargo':
	                    $sformat = 'Cl: se ha asignado un cargo a "%4$s" a la actividad "%1$s"';
	                    break;
	                case 'ActividadCargoSacd':
	                    $sformat = 'Sacd: se ha asignado el sacd "%4$s" a la actividad "%1$s"';
	                    break;
	                case 'Asistente':
	                case 'AsistenteDl':
	                case 'AsistenteOut':
	                case 'AsistenteEx':
	                case 'AsistenteIn':
	                    $sformat = 'Asistencia: "%4$s" se ha incorporado a la actividad "%1$s"';
	                    break;
	                case 'CentroEncargado':
	                    $sformat = 'Ctr: se ha asignado el ctr "%4$s" a la actividad "%1$s"';
	                    break;
	            }
	            break;
	        case Cambio::TIPO_CMB_UPDATE: // (2) update.
	            switch($sObjeto) {
	                case 'Actividad':
	                case 'ActividadDl':
	                case 'ActividadEx':
	                    // Caso especial si el campo es: 'status'
	                    if ($sPropiedad == 'status') {
	                       $sValor_old = $aStatus[$sValor_old]; 
	                       $sValor_new = $aStatus[$sValor_new]; 
	                    }
	                    // Caso especial si el campo es fecha.
	                    if ($sPropiedad == 'f_ini' OR $sPropiedad == 'f_fin') {
	                        $oFOld = new DateTimeLocal($sValor_old);
	                        $sValor_old = $oFOld->getFromLocal();
	                        $oFNew = new DateTimeLocal($sValor_new);
	                        $sValor_new = $oFNew->getFromLocal();
	                    }
                       $sformat = 'Actividad: la actividad "%1$s" ha cambiado el campo "%2$s" de "%3$s" a "%4$s"';
	                    break;
	                case 'ActividadCargo':
	                    $sformat = 'Cl: ha cambiado el cargo en la actividad "%1$s" el campo "%2$s" de "%3$s" a "%4$s"';
	                    break;
	                case 'ActividadCargoSacd':
	                    $sformat = 'Sacd: ha cambiado el cargo en la actividad "%1$s" el campo "%2$s" de "%3$s" a "%4$s"';
	                    break;
	                case 'Asistente':
	                case 'AsistenteDl':
	                case 'AsistenteOut':
	                case 'AsistenteEx':
	                case 'AsistenteIn':
	                    $sformat = 'Asistente: ha cambiado la asistencia en la actividad "%1$s" el campo "%2$s" de "%3$s" a "%4$s"';
	                    break;
	                case 'CentroEncargado':
	                    $sformat = 'Ctr: ctr "%2$s" Ha cambiado a la actividad "%1$s"';
	                    break;
	            }
	            break;
	        case Cambio::TIPO_CMB_DELETE: // (3) delete.
	            switch($sObjeto) {
	                case 'Actividad':
	                case 'ActividadDl':
	                case 'ActividadEx':
	                    //$sformat = 'Actividad: Eliminado la actividad "%1$s":"%2$s" de "%3$s" a "%4$s"';
	                    $sformat = 'Actividad: se ha eliminado la actividad "%3$s"';
	                    break;
	                case 'ActividadCargo':
	                    $sformat = 'Cl: se ha quitado el cargo a "%3$s" de la actividad "%1$s"';
	                    break;
	                case 'ActividadCargoSacd':
	                    $sformat = 'Sacd: se ha quitado al sacd "%3$s" de la actividad "%1$s"';
	                    break;
	                case 'Asistente':
	                case 'AsistenteDl':
	                case 'AsistenteOut':
	                case 'AsistenteEx':
	                case 'AsistenteIn':
	                    $sformat = 'Asistencia: "%3$s" se ha borrado de la actividad "%1$s"';
	                    break;
	                case 'CentroEncargado':
	                    $sformat = 'Ctr: se ha quitado al ctr "%3$s" de la actividad "%1$s"';
	                    break;
	            }
	            break;
	        case Cambio::TIPO_CMB_FASE: // (4) cambio de fase o status.
	            $GesActividadFase = new GestorActividadFase();
	            
	            if (ConfigGlobal::mi_sfsv() == 1) {
                    $idFase = $this->getId_fase_sv();
	            } else {
                    $idFase = $this->getId_fase_sf();
	            }
	            $idStatus = $this->getId_status();

	            if (!$bEliminada) {
	                if (!empty($idFase)) {
                        $GestorActividadProcesoTarea = new GestorActividadProcesoTarea();
                        $id_faseActual = $GestorActividadProcesoTarea->getFaseActual($iId);
                        
                        $cFases = $GesActividadFase->getActividadFases(array('id_fase'=>$idFase));
                        $sFase = $cFases[0]->getDesc_fase();
                        $cFases = $GesActividadFase->getActividadFases(array('id_fase'=>$id_faseActual));
                        $sFaseActual = $cFases[0]->getDesc_fase();
                        
                        if ($sValor_old == '-' && $sValor_new == 1) {
                            $sformat = 'Fase "%2$s" completada en la actividad "%1$s". Fase actual "%3$s"';
                        }
                        if ($sValor_old == 1 && $sValor_new == '-') {
                            $sformat = 'Fase "%2$s" eliminada en la actividad "%1$s". Fase actual "%3$s"';
                        }
	                } else if (!empty($idStatus)) {
                        $sFaseActual = $aStatus[$id_statusActual];
                        $sFase =  $aStatus[$idStatus];
                        
                        $sformat = 'Fase cambiada en la actividad "%1$s". Status "%3$s"' ;
                        if ($sValor_old == '-' && $sValor_new == 1) {
                            $sformat = 'Status "%2$s" completada en la actividad "%1$s". Status actual "%3$s"';
                        }
                        if ($sValor_old == 1 && $sValor_new == '-') {
                            $sformat = 'Status "%2$s" eliminada en la actividad "%1$s". Status actual "%3$s"';
                        }
	                }
	            } else {
	                $sFase = '';
	                $sFaseActual = '';
	                $sformat = 'Fase cambiada en la actividad "%1$s".';
	            }
	            return sprintf($sformat,$sNomActiv,$sFase,$sFaseActual);
	            break;
	    }
	    
	    if (empty($sformat)) {
	       $sTxt = "$sNomActiv; $etiqueta; $sValor_old; $sValor_new";
	    } else {
	       $sTxt = sprintf($sformat,$sNomActiv,$etiqueta,$sValor_old,$sValor_new);
	    }
	    return $sTxt;
	}
	
	
	/* METODES PRIVATS ----------------------------------------------------------*/
	
	/**
	 * Posa en marxa un procés per generar la taula d'avisos per cada usuari.
	 *
	 * @return true.
	 *
	 */
	function generarTabla() {
	    $program = ConfigGlobal::$directorio.'/apps/cambios/controller/avisos_generar_tabla.php';
	    $username = ConfigGlobal::mi_usuario();
	    $pwd = ConfigGlobal::mi_pass();
	    $err = ConfigGlobal::$directorio.'/log/avisos.err';
	    $out = ConfigGlobal::$directorio.'/log/avisos.out';
        /* Hay que pasarle los argumentos que no tienen si se le llama por command line:
         $username;
         $password;
         $dir_web = orbix | pruebas;
         document_root = /home/dani/orbix_local
         $ubicacion = 'sv';
         $esquema_web = 'H-dlbv';
         */
	    $dirweb = $_SERVER['DIRWEB'];
	    $doc_root = $_SERVER['DOCUMENT_ROOT'];
        $ubicacion = getenv('UBICACION');
        $esquema_web = getenv('ESQUEMA');
        $private = getenv('PRIVATE');
        
        // Si he entrado escogiendo el esquema de un desplegable, no tengo el valor
        if (empty($esquema_web)) {
            $esquema_web = ConfigGlobal::mi_region_dl();
        }
        
	    $command = "nohup /usr/bin/php $program $username $pwd $dirweb $doc_root $ubicacion $esquema_web $private >> $out 2>> $err < /dev/null &";
	    exec($command);
	}
	
	/**
	 * Estableix el valor de tots els atributs
	 *
	 * @param array $aDades
	 */
	function setAllAtributes($aDades) {
		if (!is_array($aDades)) return;
		if (array_key_exists('id_schema',$aDades)) $this->setId_schema($aDades['id_schema']);
		if (array_key_exists('id_item_cambio',$aDades)) $this->setId_item_cambio($aDades['id_item_cambio']);
		if (array_key_exists('id_tipo_cambio',$aDades)) $this->setId_tipo_cambio($aDades['id_tipo_cambio']);
		if (array_key_exists('id_activ',$aDades)) $this->setId_activ($aDades['id_activ']);
		if (array_key_exists('id_tipo_activ',$aDades)) $this->setId_tipo_activ($aDades['id_tipo_activ']);
		if (array_key_exists('id_fase_sv',$aDades)) $this->setId_fase_sv($aDades['id_fase_sv']);
		if (array_key_exists('id_fase_sf',$aDades)) $this->setId_fase_sf($aDades['id_fase_sf']);
		if (array_key_exists('id_status',$aDades)) $this->setId_status($aDades['id_status']);
		if (array_key_exists('dl_org',$aDades)) $this->setDl_org($aDades['dl_org']);
		if (array_key_exists('objeto',$aDades)) $this->setObjeto($aDades['objeto']);
		if (array_key_exists('propiedad',$aDades)) $this->setPropiedad($aDades['propiedad']);
		if (array_key_exists('valor_old',$aDades)) $this->setValor_old($aDades['valor_old']);
		if (array_key_exists('valor_new',$aDades)) $this->setValor_new($aDades['valor_new']);
		if (array_key_exists('quien_cambia',$aDades)) $this->setQuien_cambia($aDades['quien_cambia']);
		if (array_key_exists('sfsv_quien_cambia',$aDades)) $this->setSfsv_quien_cambia($aDades['sfsv_quien_cambia']);
		if (array_key_exists('timestamp_cambio',$aDades)) $this->setTimestamp_cambio($aDades['timestamp_cambio']);
	}

	/**
	 * Estableix a empty el valor de tots els atributs
	 *
	 */
	function setNullAllAtributes() {
		$aPK = $this->getPrimary_key();
		$this->setId_schema('');
		$this->setId_item_cambio('');
		$this->setId_tipo_cambio('');
		$this->setId_activ('');
		$this->setId_tipo_activ('');
		$this->setId_fase_sv('');
		$this->setId_fase_sf('');
		$this->setId_status('');
		$this->setDl_org('');
		$this->setObjeto('');
		$this->setPropiedad('');
		$this->setValor_old('');
		$this->setValor_new('');
		$this->setQuien_cambia('');
		$this->setSfsv_quien_cambia('');
		$this->setTimestamp_cambio('');
		$this->setPrimary_key($aPK);
	}


	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de Cambio en un array
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
	 * Recupera las claus primàries de Cambio en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('id_item_cambio' => $this->iid_item_cambio);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Estableix las claus primàries de Cambio en un array
	 *
	 * @return array aPrimary_key
	 */
	public function setPrimary_key($a_id='') {
	    if (is_array($a_id)) {
	        $this->aPrimary_key = $a_id;
	        foreach($a_id as $nom_id=>$val_id) {
	            if (($nom_id == 'id_item_cambio') && $val_id !== '') $this->iid_item_cambio = (int)$val_id; // evitem SQL injection fent cast a integer
	        }
	    }
	}
	
	/**
	 * Recupera l'atribut iid_item_cambio de Cambio
	 *
	 * @return integer iid_item_cambio
	 */
	function getId_item_cambio() {
		if (!isset($this->iid_item_cambio)) {
			$this->DBCarregar();
		}
		return $this->iid_item_cambio;
	}
	/**
	 * estableix el valor de l'atribut iid_item_cambio de Cambio
	 *
	 * @param integer iid_item_cambio
	 */
	function setId_item_cambio($iid_item_cambio) {
		$this->iid_item_cambio = $iid_item_cambio;
	}
	/**
	 * Recupera l'atribut iid_tipo_cambio de Cambio
	 *
	 * @return integer iid_tipo_cambio
	 */
	function getId_tipo_cambio() {
		if (!isset($this->iid_tipo_cambio)) {
			$this->DBCarregar();
		}
		return $this->iid_tipo_cambio;
	}
	/**
	 * estableix el valor de l'atribut iid_tipo_cambio de Cambio
	 *
	 * @param integer iid_tipo_cambio='' optional
	 */
	function setId_tipo_cambio($iid_tipo_cambio='') {
		$this->iid_tipo_cambio = $iid_tipo_cambio;
	}
	/**
	 * Recupera l'atribut iid_activ de Cambio
	 *
	 * @return integer iid_activ
	 */
	function getId_activ() {
		if (!isset($this->iid_activ)) {
			$this->DBCarregar();
		}
		return $this->iid_activ;
	}
	/**
	 * estableix el valor de l'atribut iid_activ de Cambio
	 *
	 * @param integer iid_activ='' optional
	 */
	function setId_activ($iid_activ='') {
		$this->iid_activ = $iid_activ;
	}
	/**
	 * Recupera l'atribut iid_tipo_activ de Cambio
	 *
	 * @return integer iid_tipo_activ
	 */
	function getId_tipo_activ() {
		if (!isset($this->iid_tipo_activ)) {
			$this->DBCarregar();
		}
		return $this->iid_tipo_activ;
	}
	/**
	 * estableix el valor de l'atribut iid_tipo_activ de Cambio
	 *
	 * @param integer iid_tipo_activ='' optional
	 */
	function setId_tipo_activ($iid_tipo_activ='') {
		$this->iid_tipo_activ = $iid_tipo_activ;
	}
	/**
	 * Recupera l'atribut iid_fase_sv de Cambio
	 *
	 * @return integer iid_fase_sv
	 */
	function getId_fase_sv() {
		if (!isset($this->iid_fase_sv)) {
			$this->DBCarregar();
		}
		return $this->iid_fase_sv;
	}
	/**
	 * estableix el valor de l'atribut iid_fase_sv de Cambio
	 *
	 * @param integer iid_fase_sv='' optional
	 */
	function setId_fase_sv($iid_fase_sv='') {
		$this->iid_fase_sv = $iid_fase_sv;
	}
	/**
	 * Recupera l'atribut iid_fase_sf de Cambio
	 *
	 * @return integer iid_fase_sf
	 */
	function getId_fase_sf() {
		if (!isset($this->iid_fase_sf)) {
			$this->DBCarregar();
		}
		return $this->iid_fase_sf;
	}
	/**
	 * estableix el valor de l'atribut iid_fase_sf de Cambio
	 *
	 * @param integer iid_fase_sf='' optional
	 */
	function setId_fase_sf($iid_fase_sf='') {
		$this->iid_fase_sf = $iid_fase_sf;
	}
	/**
	 * Recupera l'atribut iid_status de Cambio
	 *
	 * @return integer iid_status
	 */
	function getId_status() {
		if (!isset($this->iid_status)) {
			$this->DBCarregar();
		}
		return $this->iid_status;
	}
	/**
	 * estableix el valor de l'atribut iid_status de Cambio
	 *
	 * @param integer iid_status='' optional
	 */
	function setId_status($iid_status='') {
		$this->iid_status = $iid_status;
	}
	/**
	 * Recupera l'atribut sdl_org de Cambio
	 *
	 * @return boolean sdl_org
	 */
	function getDl_org() {
		if (!isset($this->sdl_org)) {
			$this->DBCarregar();
		}
		return $this->sdl_org;
	}
	/**
	 * estableix el valor de l'atribut sdl_org de Cambio
	 *
	 * @param boolean sdl_org='f' optional
	 */
	function setDl_org($sdl_org='f') {
		$this->sdl_org = $sdl_org;
	}
	/**
	 * Recupera l'atribut sobjeto de Cambio
	 *
	 * @return string sobjeto
	 */
	function getObjeto() {
		if (!isset($this->sobjeto)) {
			$this->DBCarregar();
		}
		return $this->sobjeto;
	}
	/**
	 * estableix el valor de l'atribut sobjeto de Cambio
	 *
	 * @param string sobjeto='' optional
	 */
	function setObjeto($sobjeto='') {
		$this->sobjeto = $sobjeto;
	}
	/**
	 * Recupera l'atribut spropiedad de Cambio
	 *
	 * @return string spropiedad
	 */
	function getPropiedad() {
		if (!isset($this->spropiedad)) {
			$this->DBCarregar();
		}
		return $this->spropiedad;
	}
	/**
	 * estableix el valor de l'atribut spropiedad de Cambio
	 *
	 * @param string spropiedad='' optional
	 */
	function setPropiedad($spropiedad='') {
		$this->spropiedad = $spropiedad;
	}
	/**
	 * Recupera l'atribut svalor_old de Cambio
	 *
	 * @return string svalor_old
	 */
	function getValor_old() {
		if (!isset($this->svalor_old)) {
			$this->DBCarregar();
		}
		return $this->svalor_old;
	}
	/**
	 * estableix el valor de l'atribut svalor_old de Cambio
	 *
	 * @param string svalor_old='' optional
	 */
	function setValor_old($svalor_old='') {
		$this->svalor_old = $svalor_old;
	}
	/**
	 * Recupera l'atribut svalor_new de Cambio
	 *
	 * @return string svalor_new
	 */
	function getValor_new() {
		if (!isset($this->svalor_new)) {
			$this->DBCarregar();
		}
		return $this->svalor_new;
	}
	/**
	 * estableix el valor de l'atribut svalor_new de Cambio
	 *
	 * @param string svalor_new='' optional
	 */
	function setValor_new($svalor_new='') {
		$this->svalor_new = $svalor_new;
	}
	/**
	 * Recupera l'atribut iquien_cambia de Cambio
	 *
	 * @return integer iquien_cambia
	 */
	function getQuien_cambia() {
		if (!isset($this->iquien_cambia)) {
			$this->DBCarregar();
		}
		return $this->iquien_cambia;
	}
	/**
	 * estableix el valor de l'atribut iquien_cambia de Cambio
	 *
	 * @param integer iquien_cambia='' optional
	 */
	function setQuien_cambia($iquien_cambia='') {
		$this->iquien_cambia = $iquien_cambia;
	}
	/**
	 * Recupera l'atribut isfsv_quien_cambia de Cambio
	 *
	 * @return integer isfsv_quien_cambia
	 */
	function getSfsv_quien_cambia() {
		if (!isset($this->isfsv_quien_cambia)) {
			$this->DBCarregar();
		}
		return $this->isfsv_quien_cambia;
	}
	/**
	 * estableix el valor de l'atribut isfsv_quien_cambia de Cambio
	 *
	 * @param integer isfsv_quien_cambia='' optional
	 */
	function setSfsv_quien_cambia($isfsv_quien_cambia='') {
		$this->isfsv_quien_cambia = $isfsv_quien_cambia;
	}
	/**
	 * Recupera l'atribut itimestamp_cambio de Cambio
	 *
	 * @return integer itimestamp_cambio
	 */
	function getTimestamp_cambio() {
		if (!isset($this->itimestamp_cambio)) {
			$this->DBCarregar();
		}
		return $this->itimestamp_cambio;
	}
	/**
	 * estableix el valor de l'atribut itimestamp_cambio de Cambio
	 *
	 * @param integer itimestamp_cambio='' optional
	 */
	function setTimestamp_cambio($itimestamp_cambio='') {
		$this->itimestamp_cambio = $itimestamp_cambio;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oCambioSet = new core\Set();

		$oCambioSet->add($this->getDatosId_tipo_cambio());
		$oCambioSet->add($this->getDatosId_activ());
		$oCambioSet->add($this->getDatosId_tipo_activ());
		$oCambioSet->add($this->getDatosId_fase_sv());
		$oCambioSet->add($this->getDatosId_fase_sf());
		$oCambioSet->add($this->getDatosDl_org());
		$oCambioSet->add($this->getDatosObjeto());
		$oCambioSet->add($this->getDatosPropiedad());
		$oCambioSet->add($this->getDatosValor_old());
		$oCambioSet->add($this->getDatosValor_new());
		$oCambioSet->add($this->getDatosQuien_cambia());
		$oCambioSet->add($this->getDatosTimestamp_cambio());
		return $oCambioSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut iid_tipo_cambio de Cambio
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_tipo_cambio() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_tipo_cambio'));
		$oDatosCampo->setEtiqueta(_("id_tipo_cambio"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iid_activ de Cambio
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_activ() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_activ'));
		$oDatosCampo->setEtiqueta(_("id_activ"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iid_tipo_activ de Cambio
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_tipo_activ() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_tipo_activ'));
		$oDatosCampo->setEtiqueta(_("id_tipo_activ"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iid_fase_sv de Cambio
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_fase_sv() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_fase_sv'));
		$oDatosCampo->setEtiqueta(_("id fase sv"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iid_fase_sf de Cambio
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_fase_sf() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_fase_sf'));
		$oDatosCampo->setEtiqueta(_("id fase sf"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iid_status de Cambio
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_status() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_status'));
		$oDatosCampo->setEtiqueta(_("id_status"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sdl_org de Cambio
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosDl_org() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'dl_org'));
		$oDatosCampo->setEtiqueta(_("dl_org"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sobjeto de Cambio
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosObjeto() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'objeto'));
		$oDatosCampo->setEtiqueta(_("objeto"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut spropiedad de Cambio
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosPropiedad() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'propiedad'));
		$oDatosCampo->setEtiqueta(_("propiedad"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut svalor_old de Cambio
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosValor_old() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'valor_old'));
		$oDatosCampo->setEtiqueta(_("valor_old"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut svalor_new de Cambio
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosValor_new() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'valor_new'));
		$oDatosCampo->setEtiqueta(_("valor_new"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iquien_cambia de Cambio
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosQuien_cambia() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'quien_cambia'));
		$oDatosCampo->setEtiqueta(_("quien_cambia"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut isfsv_quien_cambia de Cambio
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosSfsv_quien_cambia() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'sfsv_quien_cambia'));
		$oDatosCampo->setEtiqueta(_("sección de quien cambia"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut itimestamp_cambio de Cambio
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosTimestamp_cambio() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'timestamp_cambio'));
		$oDatosCampo->setEtiqueta(_("timestamp_cambio"));
		return $oDatosCampo;
	}
}
