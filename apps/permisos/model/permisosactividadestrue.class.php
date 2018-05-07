<?php
namespace permisos\model;
use actividades\model\entity as actividades;
use procesos\model\entity as procesos;
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
class PermisosActividadesTrue {
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

	 private $oGesActiv;
	/**
	 * Dbl objeto conexión DB.
	 *
	 * @var object
	 */
	 private $oDbl;


	/* METODES ----------------------------------------------------------------- */
	public function __construct($iid_usuario) {
		/*
		$oDbl = $GLOBALS['oDBC'];
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
		$this->carregarTrue($sCondicion_usuario,'t');
		$this->carregarTrue($sCondicion_usuario,'f');
		
		$this->oGesActiv = new procesos\GestorActividadProcesoTarea();
		$this->setoDbl($oDbl);
		*/
	}

	public function carregarTrue($sCondicion_usuario,$dl_propia) {
		/*
		$oDbl = $this->getoDbl();
		$Qry="SELECT DISTINCT u.*
			FROM aux_usuarios_perm u
			WHERE $sCondicion_usuario AND dl_propia='$dl_propia' 
			ORDER BY id_usuario DESC
			";
		//echo "<br>permActiv: $Qry<br>";
		if (($oDblSt = $oDbl->query($Qry)) === false) {
			$sClauError = 'ActividadCargo.carregarTrue';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
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
			$GesTiposActiv = new actividades\GestorTipoDeActividad();
			$aTiposDeProcesos = $GesTiposActiv->getTiposDeProcesos($id_tipo_activ_txt,$dl_propia);
			// para cada proceso hay que generar una entrada.
			foreach ($aTiposDeProcesos as $id_tipo_proceso) {
				// buscar las fases para estos procesos
				$oGesFases= new procesos\GestorActividadFase();
				$aFases = $oGesFases->getTodasActividadFases($id_tipo_proceso);
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
					echo _('ERROR: la fase de permiso no está en el proceso.');
				}
			}
		}
		if (!empty($id_tipo_activ_txt)) {
			if (!empty($this->aPermDl[$id_tipo_activ_txt])) $this->aPermDl[$id_tipo_activ_txt]->setOrdenar();
			if (!empty($this->aPermOtras[$id_tipo_activ_txt])) $this->aPermOtras[$id_tipo_activ_txt]->setOrdenar();
		}
		*/
	}

	public function setActividad($id_activ,$id_tipo_activ='',$dl_org='') {
		/*
		$this->btop = false;
		$this->iid_activ = $id_activ;
		// Si sólo paso el id_activ:
		if (empty($id_tipo_activ)) {
			$oActividad = new actividades\Actividad($id_activ);
			$id_tipo_activ = $oActividad->getId_tipo_activ();
			$dl_org = $oActividad->getDl_org();
		}
		$this->iid_tipo_activ = $id_tipo_activ;
		$oTipoDeActividad = new actividades\TipoDeActividad($id_tipo_activ);

		if ($dl_org == ConfigGlobal::$dele) {
			$this->bpropia=true;
			$this->iid_tipo_proceso = $oTipoDeActividad->getId_tipo_proceso();
		} else {
			$this->bpropia=false;
			$this->iid_tipo_proceso = $oTipoDeActividad->getId_tipo_proceso_ex();
		}
		$this->iid_fase = $this->oGesActiv->getFaseActual($this->iid_activ); 
		//print_r($this);
		*/
	}

	public function setId_fase($iid_fase) {
		$this->iid_fase = $iid_fase;
	}
	public function getId_fase() {
		if (empty($this->iid_fase)) {
		//	$this->iid_fase = $this->oGesActiv->getFaseActual($this->iid_activ); 
		}
		return $this->iid_fase;
	}

	public function getPermisoActual($iAfecta) {
		// devuleve permiso de crear (15) en cualquier caso
			return  new PermAccion(15);
		// para poder pasar el valor de afecta con texto:
		if (is_string($iAfecta)) $iAfecta = $this->aAfecta[$iAfecta];
		$id_tipo_activ_txt = $this->getId_tipo_activ();
		$id_tipo_proceso = $this->getId_tipo_proceso();
		$faseActual = $this->getId_fase();
		//echo "afec: $iAfecta, fase: $faseActual, proceso: $id_tipo_proceso, tipo_activ: $id_tipo_activ_txt<br>";
		$iperm=0;
		if ($this->btop === true) { return  new PermAccion(0); }
		if (($oP = $this->getPermisos()) === false) {
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

	public function getPermisos($id_tipo_activ_txt='') {
		//echo "tipo_activ: $id_tipo_activ_txt, propia: ".$this->bpropia."<br>";
		//if ($this->btop === true) {echo "ERROR2"; die();}
		if (empty($id_tipo_activ_txt)) $id_tipo_activ_txt = $this->iid_tipo_activ;
		if ($this->bpropia === true) {
			if (array_key_exists($id_tipo_activ_txt,$this->aPermDl)) {
				return $this->aPermDl[$id_tipo_activ_txt];
			} else {
				return $this->getPermisosPrev($id_tipo_activ_txt);
			}
		} else {
			if (array_key_exists($id_tipo_activ_txt,$this->aPermOtras)) {
				return $this->aPermOtras[$id_tipo_activ_txt];
			} else {
				return $this->getPermisosPrev($id_tipo_activ_txt);
			}
		}
	}

	public function getPermisosPrev($id_tipo_activ_txt='') {
		if (empty($id_tipo_activ_txt)) $id_tipo_activ_txt = $this->iid_tipo_activ;
		if (($prev_id_tipo = $this->getIdTipoPrev($id_tipo_activ_txt)) === false ) {
			return false;
		}
		return $this->getPermisos($prev_id_tipo);
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
}
?>
