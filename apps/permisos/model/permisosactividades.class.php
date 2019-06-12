<?php
namespace permisos\model;
use actividades\model\entity\Actividad;
use actividades\model\entity\GestorTipoDeActividad;
use actividades\model\entity\TipoDeActividad;
use core\ConfigGlobal;
use procesos\model\PermAccion;
use procesos\model\entity as procesos;
use procesos\model\entity\GestorTareaProceso;
use usuarios\model\entity as usuarios;
/**
 * Classe que genera un array amb els permisos per cada usuari. Es guarda a la sesió per tenir-ho a l'abast en qualsevol moment:
 *
 *	$_SESSION['oPermActividades'] = new PermisosActividades(ConfigGlobal::id_usuario());
 *
 * Estructura de l'array: 
 *	- aAfecta: el nom i corresponent integer de les propietats a les que afecta.
 *	- 2 coponents: aPermDl i aPermOtras, segons siguin els permisos per les activitats de la dl o la resta.
 *      Cada un d'aquests vectors es composa de:
 *	    a) primer component: id_tipo_activ_txt = '12....'
 *			a1) iAfecta
 *			a2) id_tipo_proceso
 *			a3) iFase
 *			a4) permiso
 *
 *			$this->aDades[$iAfecta][$id_tipo_proceso][$iFase]=$iPerm;
 *
 *
 *
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 20/11/2010
 */
class PermisosActividades {
	/* ATRIBUTS ----------------------------------------------------------------- */
	/**
	 * Perm de PermisoActividad
	 *
	 * @var array
	 */
	private $aAfecta = array ('datos'	=>1,
							'economic'	=>2,
							'sacd'		=>4,
							'ctr'		=>8,
							'tarifa'	=>16,
							'cargos'	=>32,
							'asistentes'=>64
							);
	/**
	 * Array amb els permisos.
	 *
	 * @var array
	 */
	protected $aPermDl = array();
	protected $aPermOtras = array();
	/**
	 * Per saber a quina activitat fa referència.
	 *
	 * @var integer
	 */
	protected $iid_tipo_activ;

	/**
	 * Id_activ de PermisoActividad
	 *
	 * @var integer
	 */
	 private $iid_activ;
	/**
	 * Id_tipo_proceso de PermisoActividad
	 *
	 * @var integer
	 */
	 private $iid_tipo_proceso;
	/**
	 * propia de PermisoActividad
	 *
	 * @var boolean
	 */
	 private $bpropia;
	/**
	 * número de orden de la fase actual
	 *
	 * @var integer
	 */
	 private $iid_fase;
	/**
	 * si ha llegado al final.
	 *
	 * @var boolean
	 */
	 private $btop;

	/* METODES ----------------------------------------------------------------- */
	public function __construct($iid_usuario) {
		// permiso para el usuario
		$sCondicion_usuario="u.id_usuario=$iid_usuario";
		// miro en els grups als que pertany
		$oGesGrupos = new usuarios\GestorUsuarioGrupo();
		$oGrupos = $oGesGrupos->getUsuariosGrupos(array('id_usuario'=>$iid_usuario));
		if (count($oGrupos) > 0) {
			foreach ($oGrupos as $oUsuarioGrupo) {
				$id = $oUsuarioGrupo->getId_grupo();
				$sCondicion_usuario.=" OR u.id_usuario=$id";
			}
			$sCondicion_usuario="($sCondicion_usuario)";
		}
		// carrego dues vegades, per la dl_propia i la resta.
		$this->carregar($sCondicion_usuario,'t');
		$this->carregar($sCondicion_usuario,'f');
		
	}

	private function carregar($sCondicion_usuario,$dl_propia) {
	    $oDbl = $GLOBALS['oDB'];
	    // Orden: los usuarios empiezan por 4, los grupos por 5.
	    // Al ordenar, el usuario (queda el último) sobreescribe al grupo.
	    // Los grupos, como puede haber más de uno los ordeno por orden alfabético DESC (prioridad A-Z). 
		$Qry="SELECT DISTINCT p.*, SUBSTRING( p.id_usuario::text, 1, 1 ) as orden, u.usuario
			FROM aux_usuarios_perm p JOIN aux_grupos_y_usuarios u USING (id_usuario)
			WHERE $sCondicion_usuario AND dl_propia='$dl_propia' 
			ORDER BY orden DESC, usuario DESC
			";
		//echo "<br>permActiv: $Qry<br>";
		if (($oDbl->query($Qry)) === false) {
			$sClauError = 'PermisosActividades.carregar';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		// per cada fila genero els permisos
		$f=0;
		foreach ($oDbl->query($Qry) as $row) {
			$f++;
			$id_tipo_activ_txt = $row['id_tipo_activ_txt'];	
			$id_fase_ini = $row['id_fase_ini'];	
			$id_fase_fin = $row['id_fase_fin'];	
			$iAccion = $row['accion'];	
			$iAfecta = $row['afecta_a'];
			if ($dl_propia == 't') {
				if (array_key_exists($id_tipo_activ_txt,$this->aPermDl)) {
					// machaco los valores existentes. Si he ordenado por id usuario (DESC), el último és el más importante.
				} else { //nuevo
					$this->aPermDl[$id_tipo_activ_txt] = new xResto($id_tipo_activ_txt);
				}
			} else {
				if (array_key_exists($id_tipo_activ_txt,$this->aPermOtras)) {
					// machaco los valores existentes. Si he ordenado por id usuario (DESC), el último és el más importante.
				} else { //nuevo
					$this->aPermOtras[$id_tipo_activ_txt] = new xResto($id_tipo_activ_txt);
				}
			}
			// buscar los procesos posibles para estos tipos de actividad
			$GesTiposActiv = new GestorTipoDeActividad();
			$aTiposDeProcesos = $GesTiposActiv->getTiposDeProcesos($id_tipo_activ_txt,$dl_propia);
			// para cada proceso hay que generar una entrada.
			foreach ($aTiposDeProcesos as $id_tipo_proceso) {
				// buscar las fases para estos procesos
				$oGesFases= new procesos\GestorActividadFase();
				$aFases = $oGesFases->getTodasActividadFases($id_tipo_proceso);
				if ($aFases === FALSE) {
				    echo '<br>';
				    echo sprintf(_("No se encuentran las fases de este tipo de proceso:%s"),$id_tipo_proceso);
				    continue;
				}
				// aFases es un array con todas las fases (sf o sv) de la actividad ordenado según el proceso.
				// compruebo que existan las fases inicial i final, sino doy un error 
				if (in_array($id_fase_ini, $aFases) && in_array($id_fase_ini, $aFases)) {
					// por cada fase generar los permisos
					$grabar = 0;
					foreach ($aFases as $id_fase) {
						if ($id_fase == $id_fase_ini) $grabar = 1;
						if ($grabar == 1) {
							if ($dl_propia == 't') {
								$this->aPermDl[$id_tipo_activ_txt]->setOmplir($id_tipo_proceso,$id_fase,$iAccion,$iAfecta);
							} else {
								$this->aPermOtras[$id_tipo_activ_txt]->setOmplir($id_tipo_proceso,$id_fase,$iAccion,$iAfecta);
							}
						}
						if ($id_fase == $id_fase_fin) $grabar = 0;
					}
				} else {
					echo _("error: la fase de permiso no está en el proceso.");
				}
			}
		}
		if (!empty($id_tipo_activ_txt)) {
			if (!empty($this->aPermDl[$id_tipo_activ_txt])) $this->aPermDl[$id_tipo_activ_txt]->setOrdenar();
			if (!empty($this->aPermOtras[$id_tipo_activ_txt])) $this->aPermOtras[$id_tipo_activ_txt]->setOrdenar();
		}
	}

	public function setActividad($id_activ,$id_tipo_activ='',$dl_org='') {
		$this->btop = false;
		$this->iid_activ = $id_activ;
		
		// Si sólo paso el id_activ:
		if (empty($id_tipo_activ)) {
			$oActividad = new Actividad($id_activ);
			$id_tipo_activ = $oActividad->getId_tipo_activ();
			$dl_org = $oActividad->getDl_org();
		}
        $dl_org_no_f = substr($dl_org, 0, -1);
        
		$this->iid_tipo_activ = $id_tipo_activ;
		$oTipoDeActividad = new TipoDeActividad($id_tipo_activ);

        if ($dl_org == ConfigGlobal::mi_delef() OR $dl_org_no_f == ConfigGlobal::mi_dele()) {
			$this->bpropia=true;
            $this->iid_tipo_proceso = $oTipoDeActividad->getId_tipo_proceso();
		} else {
			$this->bpropia=false;
            $this->iid_tipo_proceso = $oTipoDeActividad->getId_tipo_proceso_ex();
		}
		
		$oGesActiv = new procesos\GestorActividadProcesoTarea();
		$this->iid_fase = $oGesActiv->getFaseActual($this->iid_activ); 
		//print_r($this);
	}

	public function setId_fase($iid_fase) {
		$this->iid_fase = $iid_fase;
	}
	public function getId_fase() {
		if (empty($this->iid_fase)) {
    		$oGesActiv = new procesos\GestorActividadProcesoTarea();
			$this->iid_fase = $oGesActiv->getFaseActual($this->iid_activ); 
		}
		return $this->iid_fase;
	}

	/**
	 * Para saber si puedo crear una actividad del tipo
	 * para dl, ex
	 * 
	 * @param bool $dl_propia dl organizadora
	 * @return array [$of_respnsable, $status]
	 */
	public function getPermisoCrear($dl_propia) {
	    $this->bpropia = $dl_propia;
		$id_tipo_activ = $this->iid_tipo_activ;
		// si vengo de una búsqueda, el id_tipo_actividad puede ser con '...'
		// pongo el tipo básico (sin specificar)
		//$id_tipo_activ = str_replace('.', '0', $id_tipo_activ);
		$GesTiposActiv = new GestorTipoDeActividad();
		$aTiposDeProcesos = $GesTiposActiv->getTiposDeProcesos($id_tipo_activ,$dl_propia);
		
		if (empty($aTiposDeProcesos)) {
		    echo _("debería crear un proceso para este tipo de actividad");
		    return FALSE;
		}
		// Cojo el primero
		$oPerm = FALSE;
		foreach($aTiposDeProcesos as $id_tipo_proceso) {
            //$this->iid_tipo_proceso = $aTiposDeProcesos[0];
            $GesTareaProceso = new GestorTareaProceso();
            $cFasesdelProcesos = $GesTareaProceso->getTareasProceso(array('id_tipo_proceso'=>$id_tipo_proceso,'_ordre'=>'n_orden'));
            // La primera fase:
            $oTareaProceso = $cFasesdelProcesos[0];
            $fasePrimera = $oTareaProceso->getId_Fase();
            $of_responsable = $oTareaProceso->getOf_responsable();	
            $status = $oTareaProceso->getStatus();	

            // devolver false si no puedo crear
            $iAfecta = 1; //datos
            if (($oP = $this->getPermisos($iAfecta)) === false) {
                return FALSE;
            } else {
                $iperm = $oP->getPerm($id_tipo_proceso,$iAfecta,$fasePrimera);
                if ($iperm !== false) {
                    $oPerm = new PermAccion($iperm);
                    break;
                }
            }
		}
	    
	    if ($oPerm !== FALSE && $oPerm->have_perm_activ('crear')) {
            return ['of_responsable' => $of_responsable,
	           'status' => $status,
	           ];
	    } else {
	        return FALSE;
	    }
	}

	public function getPermisoActual($iAfecta) {
	    // hay que poner a cero el id_tipo_activ, sino 
	    // aprovecha el que se ha buscado con el anterior iAfecta.
	    $this->setActividad($this->iid_activ);
		// para poder pasar el valor de afecta con texto:
		if (is_string($iAfecta)) $iAfecta = $this->aAfecta[$iAfecta];
		$id_tipo_proceso = $this->getId_tipo_proceso();
		if (empty($id_tipo_proceso)) {
		    echo _("No tiene definido el proceso para este tipo de actividad. Puede que falte definir la dl org");
			return  new PermAccion(0);
		}
		$faseActual = $this->getId_fase();
		//echo "afec: $iAfecta, fase: $faseActual, proceso: $id_tipo_proceso, tipo_activ: $id_tipo_activ_txt<br>";
		$iperm=0;
		if ($this->btop === true) { return  new PermAccion(0); }
		if (($oP = $this->getPermisos($iAfecta)) === false) {
			return  new PermAccion(0);
		} else {
			$iperm = $oP->getPerm($id_tipo_proceso,$iAfecta,$faseActual);
			if ($iperm == 'next') {
				return $this->getPermisoActualPrev($iAfecta);
			} elseif ($iperm !== false) {
				//return $iperm;
				$oPerm = new PermAccion($iperm);
				return $oPerm;
			}
			return  new PermAccion(0);
		}
	}
	public function getPermisoActualPrev($iAfecta) {
		//if ($this->getIdTipoPrev() === false) return false;
		if ($this->getIdTipoPrev() === false) return new PermAccion(0);
		return $this->getPermisoActual($iAfecta);
	}

	public function getPermisos($iAfecta,$id_tipo_activ_txt='') {
		if (empty($id_tipo_activ_txt)) $id_tipo_activ_txt = $this->iid_tipo_activ;
		$id_tipo_activ_txt = $this->completarId($id_tipo_activ_txt);
		if ($this->bpropia === true) {
			if (array_key_exists($id_tipo_activ_txt,$this->aPermDl)) {
                $PermIdTipo = $this->aPermDl[$id_tipo_activ_txt];
			    // a ver si existe el iAfecta para este id_tipo_activ:
			    if ($PermIdTipo->hasAfecta($iAfecta)) {
				    return $this->aPermDl[$id_tipo_activ_txt];
			    } else {
                    return $this->getPermisosPrev($iAfecta,$id_tipo_activ_txt);
			    }
			} else {
				return $this->getPermisosPrev($iAfecta,$id_tipo_activ_txt);
			}
		} else {
			if (array_key_exists($id_tipo_activ_txt,$this->aPermOtras)) {
				return $this->aPermOtras[$id_tipo_activ_txt];
			} else {
				return $this->getPermisosPrev($iAfecta,$id_tipo_activ_txt);
			}
		}
	}

	public function getPermisosPrev($iAfecta,$id_tipo_activ_txt='') {
		if (empty($id_tipo_activ_txt)) $id_tipo_activ_txt = $this->iid_tipo_activ;
		if (($prev_id_tipo = $this->getIdTipoPrev($id_tipo_activ_txt)) === false ) {
			return false;
		}
		return $this->getPermisos($iAfecta,$prev_id_tipo);
	}

	public function setId_tipo_activ($id_tipo_activ) {
		if ($id_tipo_activ == '......') {
			$this->btop = true;
		} else {
			$this->btop = false;
		}
		// actualitzar el id_tipo_activ
		$this->iid_tipo_activ = $id_tipo_activ;
	}
	public function setId_tipo_proceso($id_tipo_proceso) {
		// actualitza el id_tipo_proceso
		$this->iid_tipo_proceso = $id_tipo_proceso;
	}
	public function setPropia($bpropia) {
		// actualitza el bpropia
		if ($bpropia == 't' || $bpropia == 'true' || $bpropia == 'on' || $bpropia == 1) {
			$this->bpropia = true;
		} else {
			$this->bpropia = false;
		}
	}

	public function getIdTipoPrev($id_tipo_activ_txt='') {
		if (empty($id_tipo_activ_txt)) $id_tipo_activ_txt = $this->iid_tipo_activ;
		$match = [];
		$rta = preg_match('/(\d+)(\d)(\.*)/',$id_tipo_activ_txt,$match);
		if (empty($rta)) {
			if ($id_tipo_activ_txt=='1.....' || $id_tipo_activ_txt=='2.....' || $id_tipo_activ_txt=='3.....') {
				return '......';
			} else {
				$this->btop = true; // ja no puc pujar més amunt.
				return false;
			}
		}

		$num_prev = $match[1];
		$num = $match[2];
		$pto = $match[3];

		$prev_id_tipo = $num_prev.".".$pto;
		//echo "<br>$num, $num_prev, $prev_id_tipo <br>";
		//print_r($this);
		$this->iid_tipo_activ = $prev_id_tipo;
		return $prev_id_tipo;
	}
	/* METODES PRIVATS ----------------------------------------------------------*/
	private function getId_tipo_activ() {
		// buscar el id_tipo_activ
		return $this->iid_tipo_activ;
	}
	private function getId_tipo_proceso() {
		// buscar el id_tipo_proceso
		return $this->iid_tipo_proceso;
	}

	private function completarId($id_tipo_activ_txt){
	    $len = strlen($id_tipo_activ_txt);
	    if ($len < 6) {
	        $relleno = 6 - $len;
	        for ($i=0; $i < $relleno; $i++ ) {
	            $id_tipo_activ_txt .= '.';
	        }
	    }
	    return $id_tipo_activ_txt;
	}
}
