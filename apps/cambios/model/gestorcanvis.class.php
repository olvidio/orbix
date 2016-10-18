<?php
include_once ('classes/actividades/ext_a_actividades.class');
include_once ('classes/actividades/ext_a_cambios.class');
include_once ('classes/actividades/ext_a_actividad_proceso_gestor.class');
/**
 * Classe para manejar los cambios
 *
 * @package delegación
 * @subpackage model
 * @author
 * @version 1.0
 * @created 3/1/2012
 */

class gestorCanvis {
	/* METODES ESTATICS ----------------------------------------------------------*/

	/**
	 * Recupera un array amb els objectes (de taula) dels que es pot avisa i els seus noms.
	 *
	 * @return array
	 */
	public static function getNom_obj() {
		$aNomTablas_obj = array('Actividad' => _('Actividad'),
					'ActividadCargoSacd' => _('Sacd'),
					'CentroEncargado'=> _('ctr'),
					'ActividadCargo' => _('cl'),
					'ActividadAsistente' => _('Asistencias'),
					'ActividadProcesoTarea'=> _('Fases Actividad')
		);
		return $aNomTablas_obj;
	}
	/**
	 * Recupera un string amb el corresponent  'nom objecte'.
	 *
	 * @return string
	 */
	public static function getTablas_obj($sTabla,$aDades=array()) {
		if ($sTabla == 'd_cargos_activ') {
			$GesCargos = new GestorCargo();
			$sListaCargosSacd = $GesCargos->getListaCargosxTipoCargo('sacd');
			$aListaCargosSacd = explode(',',$sListaCargosSacd);
			$iid_cargo = $aDades['id_cargo'];
			if (in_array($iid_cargo,$aListaCargosSacd)) {
				return 'ActividadCargoSacd';
			} else {
				return 'ActividadCargo';
			}

		} else {
			$aTablas = array('a_actividades' => 'Actividad',
						'd_encargados_activ' => 'CentroEncargado',
						'd_asistentes_activ' => 'ActividadAsistente',
						'a_actividad_proceso' => 'ActividadProcesoTarea'
					);
			return $aTablas[$sTabla];
		}
	}

	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aDades de GestorCanvis
	 *
	 * @var array
	 */
	 private $aDades;


	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 *
	 */
	function __construct() {
	
	}

	/* METODES PUBLICS ----------------------------------------------------------*/

	function muestraMensaje($sClauError,$goto) {
		//$_SESSION['oGestorErrores']->addErrorAppLastError($oDBSt, $sClauError, $file);
		$txt=$this->leerErrorAppLastError();
		$err=$oDBSt->errorInfo();
		if (strstr($txt, 'duplicate key')) {
			echo _("Ya existe un registro con esta información");
		} else {
			echo "\n dd".$txt."\n $sClauError <br>";
		}
		$oPosicion = new web\Posicion();
		$seguir = $oPosicion->link_a($goto,0);
		//$seguir=link_a($goto,0);
		echo "<br><span class='link' onclick=fnjs_update_div('#main',$seguir)>"._("continuar")."</span>";


	}

	function addCanvi($sTabla, $sTipoCambio, $iid_activ, $aDadesNew, $aDadesActuals) {
		// poso el nom de l'objecte que gestiona la taula en comptes del nom de la taula.
		$oGestorActividadProcesoTarea = new GestorActividadProcesoTarea();
		$id_fase = $oGestorActividadProcesoTarea->getFaseActual($iid_activ);
		$id_user=ConfigGlobal::id_usuario();
		$ahora=date("d/m/Y H:i:s");
		// per saber el tipus d'activitat.
		if ($sTabla == 'a_actividades') { //si el canvi és a l'activitat, ja el tinc.
			$iId_tipo_activ = empty($aDadesNew['id_tipo_activ'])? $aDadesActuals['id_tipo_activ'] : $aDadesNew['id_tipo_activ'];
			$sNomActiv = empty($aDadesNew['nom_activ'])? $aDadesActuals['nom_activ'] : $aDadesNew['nom_activ'];
			$dl_org = empty($aDadesActuals['dl_org'])? $aDadesNew['dl_org'] : $aDadesActuals['dl_org'];
		} else {
			$oActividad = new Actividad($iid_activ);
			$iId_tipo_activ = $oActividad->getId_tipo_activ();
			$sNomActiv = $oActividad->getNom_activ();
			$dl_org = $oActividad->getDl_org();
		}
		$bdl_propia = ($dl_org === ConfigGlobal::$dele)? 't' : 'f';
		
		switch ($sTipoCambio) {
			case 'INSERT':
				$sTablaObj = self::getTablas_obj($sTabla,$aDadesNew);
				$oActividadCambio = new ActividadCambio();
				$oActividadCambio->setTipo_cambio(1);
				$oActividadCambio->setId_activ($iid_activ);
				$oActividadCambio->setId_tipo_activ($iId_tipo_activ);
				$oActividadCambio->setId_fase($id_fase);
				$oActividadCambio->setDl_propia($bdl_propia);
				$oActividadCambio->setTabla_obj($sTablaObj);
				$oActividadCambio->setQuien_cambia($id_user);
				$oActividadCambio->setTimestamp_cambio($ahora);
				$oActividadCambio->setValor_old();
				switch ($sTablaObj) {
					case 'Actividad':
						$oActividadCambio->setCampo('nom_activ');
						$oActividadCambio->setValor_new($aDadesNew['nom_activ']);
						break;
					case 'ActividadAsistente':
					case 'ActividadCargo':
					case 'ActividadCargoSacd':
						$oActividadCambio->setCampo('id_nom');
						$oActividadCambio->setValor_new($aDadesNew['id_nom']);
						break;
					case 'CentroEncargado':
						$oActividadCambio->setCampo('id_ubi');
						$oActividadCambio->setValor_new($aDadesNew['id_ubi']);
						break;
				}
				$oActividadCambio->DBGuardar();
				break;
			case 'UPDATE':
				$sTablaObj = self::getTablas_obj($sTabla,$aDadesNew);
				$result = array_diff_assoc($aDadesNew, $aDadesActuals);
				foreach ($result as $key=>$value) {
					$oActividadCambio = new ActividadCambio();
					$oActividadCambio->setTipo_cambio(2);
					$oActividadCambio->setId_activ($iid_activ);
					$oActividadCambio->setId_tipo_activ($iId_tipo_activ);
					$oActividadCambio->setId_fase($id_fase);
					$oActividadCambio->setDl_propia($bdl_propia);
					$oActividadCambio->setTabla_obj($sTablaObj);
					$oActividadCambio->setCampo($key);
					$oActividadCambio->setValor_old($aDadesActuals[$key]);
					$oActividadCambio->setValor_new($value);
					$oActividadCambio->setQuien_cambia($id_user);
					$oActividadCambio->setTimestamp_cambio($ahora);
					$oActividadCambio->DBGuardar();
				}
				break;
			case 'DELETE':
				$sTablaObj = self::getTablas_obj($sTabla,$aDadesActuals);
				$oActividadCambio = new ActividadCambio();
				$oActividadCambio->setTipo_cambio(3);
				$oActividadCambio->setId_activ($iid_activ);
				$oActividadCambio->setId_tipo_activ($iId_tipo_activ);
				$oActividadCambio->setId_fase($id_fase);
				$oActividadCambio->setDl_propia($bdl_propia);
				$oActividadCambio->setTabla_obj($sTablaObj);
				$oActividadCambio->setValor_new();
				$oActividadCambio->setQuien_cambia($id_user);
				$oActividadCambio->setTimestamp_cambio($ahora);
				switch ($sTablaObj) {
					case 'Actividad':
						// pongo id_activ = 0, pues al eliminar la actividad se eliminan todas las filas relacionadas.
					    //	Así mantengo el dato que se ha eliminado .
						$oActividadCambio->setId_activ(0);
						$oActividadCambio->setCampo('nom_activ');
						$oActividadCambio->setValor_old($aDadesActuals['nom_activ']);
						break;
					case 'ActividadAsistente':
					case 'ActividadCargo':
					case 'ActividadCargoSacd':
						$oActividadCambio->setCampo('id_nom');
						$oActividadCambio->setValor_old($aDadesActuals['id_nom']);
						break;
					case 'CentroEncargado':
						$oActividadCambio->setCampo('id_ubi');
						$oActividadCambio->setValor_old($aDadesActuals['id_ubi']);
						break;
				}
				$oActividadCambio->DBGuardar();
				break;
			case 'FASE':
				$sTablaObj = self::getTablas_obj($sTabla,$aDadesActuals);
				// només mi fixo en el 'completado'
				// amb els boolean no s'aclar: 0,1,false ,true,f,t...
				if (empty($aDadesNew['completado']) || ($aDadesNew['completado'] === 'off') || ($aDadesNew['completado'] === 'false') || ($aDadesNew['completado'] === 'f')) { $boolCompletadoNew=false; } else { $boolCompletadoNew=true; }
				if (empty($aDadesActuals['completado']) || ($aDadesActuals['completado'] === 'off') || ($aDadesActuals['completado'] === 'false') || ($aDadesActuals['completado'] === 'f')) { $boolCompletadoActuals=false; } else { $boolCompletadoActuals=true; }

				if ($boolCompletadoNew != $boolCompletadoActuals) {
					$oActividadCambio = new ActividadCambio();
					$oActividadCambio->setTipo_cambio(4);
					$oActividadCambio->setId_activ($iid_activ);
					$oActividadCambio->setId_tipo_activ($iId_tipo_activ);
					$oActividadCambio->setId_fase($id_fase);
					$oActividadCambio->setDl_propia($bdl_propia);
					$oActividadCambio->setTabla_obj($sTablaObj);
					$oActividadCambio->setCampo('completado');
					$oActividadCambio->setValor_old($boolCompletadoActuals);
					$oActividadCambio->setValor_new($boolCompletadoNew);
					$oActividadCambio->setQuien_cambia($id_user);
					$oActividadCambio->setTimestamp_cambio($ahora);
					$oActividadCambio->DBGuardar();
				}
				break;
		}
	}
}
?>
