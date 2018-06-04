<?php
namespace actividadplazas\model;
use core;
/**
 * GestorActividadPlazas
 *
 * Classe per gestionar la llista d'objectes de la clase actividadPlazas
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 09/11/2016
 */

class GestorResumenPlazas {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * Id_activ de actividadPlazas
	 *
	 * @var integer
	 */
	 protected $iid_activ;
	 
	/**
	 * Plazas de actividadPlazas
	 *
	 * @var integer
	 */
	 protected $dl_org;

	/**
	 * Array de dl por nombre
	 *
	 * @var array
	 */
	 protected $a_dele;
	/**
	 * Array de dl por id
	 *
	 * @var array
	 */
	 protected $a_id_dele;
	 
	 /* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 *
	 * @return $gestor
	 *
	 */
	function __construct() {
	}


	/* METODES PUBLICS -----------------------------------------------------------*/

	/**
	 * Posibles propietarios de la plaza: propias más cedidas
	 * 
	 * @param bool dl_de_paso
	 * @return object Desplegable
	 */
	public function getPosiblesPropietarios($dl_de_paso = FALSE){
		$id_activ = $this->getId_activ();
		$mi_dl = \core\ConfigGlobal::mi_dele();
		$id_mi_dl = $this->getDlId($mi_dl);
		$dl_org = $this->getDl_org();

		$gesAsistentes = new \asistentes\model\entity\GestorAsistente();
		//Conseguidas
		$gesActividadPlazas = new \actividadplazas\model\entity\GestorActividadPlazas();
		// plazas de calendario de cada dl
		$a_dl = array();
		$plazas_conseguidas = 0;
		$cActividadPlazas = $gesActividadPlazas->getActividadesPlazas(array('id_activ'=>$id_activ));
		foreach ($cActividadPlazas as $oActividadPlazas) {
			$id_dl_otra = $oActividadPlazas->getId_dl();
			$dl_otra = $this->getDlText($id_dl_otra);
			$dl_tabla = $oActividadPlazas->getDl_tabla();
			
			$json_cedidas = $oActividadPlazas->getCedidas();
			if (!empty($json_cedidas)){
				$aCedidas = json_decode($json_cedidas,TRUE);
				foreach ($aCedidas as $dl_2 => $num_plazas) {
					if ($mi_dl == $dl_2) {
						$ocu = $gesAsistentes->getPlazasOcupadasPorDl($id_activ,$mi_dl,$dl_otra);
						$a_dl["$dl_otra>$dl_2"] = "$dl_otra ($ocu de $num_plazas)";
					}
					//Si son plazas cedidas a una dl de paso. Solo cuento las que he cedido yo
					if ($dl_de_paso !== FALSE){
						if ($dl_de_paso == $dl_2 && $id_dl_otra == $id_mi_dl) {
							$ocu = $gesAsistentes->getPlazasOcupadasPorDl($id_activ,$dl_2,$mi_dl);
							$a_dl["$mi_dl>$dl_2"] = "$dl_2 ($ocu de $num_plazas)";
						}
					}
				}
			}
		}
		// las que me correponden por calendario - las cedidas
		$pl_calendario = $this->getPlazasCalendario($mi_dl);
		$pl_cedidas = $this->getPlazasCedidas($mi_dl);
		$pl_propias = $pl_calendario - $pl_cedidas;
		if ($pl_propias > 0) {
			$ocu = $gesAsistentes->getPlazasOcupadasPorDl($id_activ,$mi_dl,$mi_dl);
			$a_dl["$mi_dl>$mi_dl"] = "$mi_dl ($ocu de $pl_propias)";
		}
		// Debe haber al menos un valor para que se pase el campo y no dé error de 'llega distinto número de campos...'
		//if (count($a_dl) == 0 ) $a_dl["nadie"] = "nadie";
		if (count($a_dl) == 0 ) $a_dl["$dl_org>$dl_org"] = $dl_org;
		return new \web\Desplegable('',$a_dl,'',true);
	}
	
	/**
	 * Devuelve las plazas diponibles para una dl de una actividad
	 */
	public function getPlazasCalendario($dl) {
		
		$id_activ = $this->getId_activ();
		$mi_dl = $dl;
		$id_mi_dl = $this->getDlId($mi_dl);
		$dl_org = $this->getDl_org();

		$gesActividadPlazas = new \actividadplazas\model\entity\GestorActividadPlazas();
		// plazas de calendario de cada dl
		$cActividadPlazas = $gesActividadPlazas->getActividadesPlazas(array('id_activ'=>$id_activ,'id_dl'=>$id_mi_dl,'dl_tabla'=>$dl_org));
		$plazas_calendario = 0;
		foreach ($cActividadPlazas as $oActividadPlazas) {
			$plazas_calendario += $oActividadPlazas->getPlazas();
		}
		return $plazas_calendario;
	}

	/**
	 * Devuelve las plazas cedidas para una dl de una actividad
	 */
	public function getPlazasCedidas($dl) {
		$plazas_cedidas = 0;
		$id_activ = $this->getId_activ();
		$mi_dl = $dl;
		$id_mi_dl = $this->getDlId($mi_dl);
		$dl_org = $this->getDl_org();

		$gesActividadPlazas = new \actividadplazas\model\entity\GestorActividadPlazas();
		// plazas de calendario de cada dl
		$cActividadPlazas = $gesActividadPlazas->getActividadesPlazas(array('id_activ'=>$id_activ,'id_dl'=>$id_mi_dl,'dl_tabla'=>$mi_dl));
		foreach ($cActividadPlazas as $oActividadPlazas) {
			$json_cedidas = $oActividadPlazas->getCedidas();
			$plazas_cedidas = 0;
			if (!empty($json_cedidas)){
				$aCedidas = json_decode($json_cedidas,TRUE);
				foreach ($aCedidas as $dl_otra => $plazas) {
					$plazas_cedidas += $plazas;
				}
			}
		}
		return $plazas_cedidas;	
	}
	/**
	 * Devuelve las plazas conseguidas para una dl de un actividad
	 */
	public function getPlazasConseguidas($dl) {
		$id_activ = $this->getId_activ();
		$mi_dl = $dl;
		$dl_org = $this->getDl_org();

		$gesActividadPlazas = new \actividadplazas\model\entity\GestorActividadPlazas();
		// plazas de calendario de cada dl
		$plazas_conseguidas = 0;
		$cActividadPlazas = $gesActividadPlazas->getActividadesPlazas(array('id_activ'=>$id_activ));
		foreach ($cActividadPlazas as $oActividadPlazas) {
			$id_dl_otra = $oActividadPlazas->getId_dl();
			$dl_otra = $this->getDlText($id_dl_otra);
			$dl_tabla = $oActividadPlazas->getDl_tabla();
			
			$json_cedidas = $oActividadPlazas->getCedidas();
			if (!empty($json_cedidas)){
				$aCedidas = json_decode($json_cedidas,TRUE);
				foreach ($aCedidas as $dl_2 => $num_plazas) {
					if ($mi_dl == $dl_2) {
						$plazas_conseguidas += $num_plazas;
					}
				}
			}
		}
		return $plazas_conseguidas;	
		
	}
	/**
	 * Devuelve las plazas diponibles para una dl de una actividad
	 */
	public function getPlazasDisponibles($dl) {
		$plazas_calendario = $this->getPlazasCalendario($dl);
		$plazas_cedidas = $this->getPlazasCedidas($dl);
		$plazas_conseguidas = $this->getPlazasConseguidas($dl);

		$plazas_disponibles = $plazas_calendario + $plazas_conseguidas - $plazas_cedidas;

		return $plazas_disponibles;
	}
	/**
	 * Devuelve las plazas totales de una actividad, o las de la casa.
	 */
	public function getPlazasTotales() {
		$id_activ = $this->getId_activ();
		$oActividad = new \actividades\model\entity\Actividad($id_activ);
		$dl_org = $oActividad->getDl_org();
		$plazas_totales = $oActividad->getPlazas();
		if (empty($plazas_totales)) {
			$id_ubi = $oActividad->getId_ubi();
			$oCasa = \ubis\model\entity\Ubi::NewUbi($id_ubi);
			// Si la casa es un ctr de otra dl, no sé las plazas
			if(method_exists($oCasa, 'getPlazas')){
				$plazas_totales = $oCasa->getPlazas();
			} else {
				$plazas_totales = '';
			}
			if (empty($plazas_totales)) {
				$plazas_totales = '?';
			}
		}

		return $plazas_totales;
	}
	
	/**
	 * usa clases externas:
	 *	asistentes\model\entity\GestorAsistente();
	 *	actividades\Actividad($id_activ);
	 *	ubis\model\entity\Ubi::NewUbi($id_ubi);
	 * 	ubis\model\entity\GestorDelegacion();
	 * 
	 * @param biginteger $id_activ
	 * @return array $a_plazas
	 * 
	 */
	public function getResumen() {
		$a_plazas = array();
		$id_activ = $this->getId_activ();
		$gesActividadPlazas = new \actividadplazas\model\entity\GestorActividadPlazas();
		$gesAsistentes = new \asistentes\model\entity\GestorAsistente();
		$oActividad = new \actividades\model\entity\Actividad($id_activ);
		$dl_org = $oActividad->getDl_org();
		$plazas_totales = $this->getPlazasTotales();
		// si la actividad no está pulicada, no hay plazas de otras dl. Todas para la dl org.
		if ($oActividad->getPublicado() === false) {
//			$ocupadas = $gesAsistentes->getPlazasOcupadasPorDl($id_activ,$dl_org);
//			if ($ocupadas < 0) { // No se sabe
//				$a_plazas[$dl_org]['ocupadas'] = '?';
//				$a_plazas['total']['ocupadas'] = $ocupadas;
//			} else {
//				$a_plazas[$dl_org]['ocupadas'] = $ocupadas;
//				$a_plazas['total']['ocupadas'] = $ocupadas;
//			}
			
			$a_plazas['total']['actividad'] = $plazas_totales;
			$a_plazas['total']['calendario'] = $plazas_totales;
			$a_plazas['total']['cedidas'] = 0;
			$a_plazas['total']['conseguidas'] = 0;
			$a_plazas['total']['actual'] = $plazas_totales;
		
			return $a_plazas;
		}
		// plazas de calendario de cada dl + cedidas
		$cActividadPlazas = $gesActividadPlazas->getActividadesPlazas(array('id_activ'=>$id_activ));
		foreach ($cActividadPlazas as $oActividadPlazas) {
			$id_dl = $oActividadPlazas->getId_dl();
			$dl = $this->getDlText($id_dl);
			$dl_tabla = $oActividadPlazas->getDl_tabla();
			if (empty($a_plazas[$dl]['cedidas'])) {
				$a_plazas[$dl]['cedidas'] = array();
			}
			if (empty($a_plazas[$dl]['conseguidas'])) {
				$a_plazas[$dl]['conseguidas'] = array();
			}
			$a_plazas[$dl]['calendario'] = 0;
			if ($dl_org == $dl_tabla) {
				$a_plazas[$dl]['calendario'] = $oActividadPlazas->getPlazas();
				// las cedidas se guardan en la tabla que pertenece a la dl
				if($dl === $dl_org) {
					$json_cedidas = $oActividadPlazas->getCedidas();
					if (!empty($json_cedidas)){
						$aCedidas = json_decode($json_cedidas,TRUE);
						$a_plazas[$dl]['cedidas'] = $aCedidas;
					}
				}
			} else { //para plazas cedidas de una dl que no es la que organiza.
				$json_cedidas = $oActividadPlazas->getCedidas();
				if (!empty($json_cedidas)){
					$aCedidas = json_decode($json_cedidas,TRUE);
					$a_plazas[$dl]['cedidas'] = $aCedidas;
				}
			}
			$a_plazas[$dl]['total_cedidas'] = 0;
			$a_plazas[$dl]['total_conseguidas'] = 0;
		}
		//Calcular totales
		$tot_calendario = 0;
		$tot_disponibles = 0;
		$tot_ocupadas = 0;
		$tot_cedidas = 0;
		$tot_conseguidas = 0;
		// Conseguidas
		foreach ($a_plazas as $dl=>$aa) {
			$total_cedidas = 0;
			// si no tiene por calendario le pongo 0
			if (!array_key_exists('calendario',$aa)) {
				$num_plazas_calendario = 0;
			} else {
				$num_plazas_calendario = $aa['calendario'];
			}
			if (!array_key_exists('cedidas',$aa)) {
				$aCedidas = array();
			} else {
				$aCedidas = $aa['cedidas'];
			}
			foreach ($aCedidas as $dl_otra=>$num_plazas){
				if ($dl != $dl_otra && array_key_exists($dl_otra,$a_plazas)) {
					$a_plazas[$dl_otra]['conseguidas'][$dl] = $num_plazas;
				} else {
					$a_plazas[$dl_otra]['conseguidas'][$dl] = $num_plazas;
//					
//					$ocu = $gesAsistentes->getPlazasOcupadasPorDl($id_activ,$dl_otra,$dl);
//					$a_plazas[$dl]['ocupadas'][$dl_otra] = $ocu;
				} 
				$total_cedidas += $num_plazas;
			}
			$a_plazas[$dl]['total_cedidas'] = $total_cedidas;
			$tot_calendario += $num_plazas_calendario;
			$tot_cedidas += $total_cedidas;
		}
		foreach ($a_plazas as $dl=>$aa) {
			$total_conseguidas = 0;
			$aCedidas = $aa['conseguidas'];
			foreach ($aCedidas as $dl_otra=>$num_plazas){
				$total_conseguidas += $num_plazas;
			}
			$a_plazas[$dl]['total_conseguidas'] = $total_conseguidas;
			$tot_conseguidas += $total_conseguidas;
		}
		// Disponibles (calendario - cedidas // conseguidas)
		foreach ($a_plazas as $dl=>$aa) {
			$total_disponibles = 0;
			$aa['calendario'] = empty($aa['calendario'])? 0 : $aa['calendario'];
			if (empty($aa['cedidas'])) {
				$disponibles = $aa['calendario'];
			} else {
				$disponibles = $aa['calendario'] - \array_sum($aa['cedidas']);
			}
			$total_disponibles += $disponibles;
			$a_plazas[$dl]['disponibles'][$dl] = $disponibles;
			foreach ($aa['conseguidas'] as $dl_otra => $num) {
				// conseguidas - cedidas
				$a_plazas[$dl]['disponibles'][$dl_otra] = $num;
				$total_disponibles += $num;
			}
			$a_plazas[$dl]['total_disponibles'] = $total_disponibles;
			$tot_disponibles += $total_disponibles;
		}
		// Ocupadas (de las disponibles)
		foreach ($a_plazas as $dl=>$aa) {
			foreach ($aa['disponibles'] as $dl_otra => $num) {
				$ocupadas = $gesAsistentes->getPlazasOcupadasPorDl($id_activ,$dl,$dl_otra);
				$a_plazas[$dl]['ocupadas'][$dl_otra] = $ocupadas;
			}
			$a_plazas[$dl]['total_ocupadas'] = \array_sum($a_plazas[$dl]['ocupadas']);
			$tot_ocupadas += $a_plazas[$dl]['total_ocupadas'];
		}
		
		$a_plazas['total']['actividad'] = $plazas_totales;
		$a_plazas['total']['calendario'] = $tot_calendario;
		$a_plazas['total']['cedidas'] = $tot_cedidas;
		$a_plazas['total']['conseguidas'] = $tot_conseguidas;
		$a_plazas['total']['disponibles'] =  $tot_disponibles;
		$a_plazas['total']['ocupadas'] = $tot_ocupadas;
		
		ksort($a_plazas);
		return $a_plazas;
	}
	
	/**
	 * Plazas disponibles menos las ocupadas
	 * 
	 * @param string $dl delegación, si esta vacio: la mia.
	 * @return integer numero de plazas libres para la dl
	 * 
	 */
	public function getLibres($dl='') {
		if (empty($dl)) {
			$dl = core\ConfigGlobal::mi_dele();
		}
		
		$a_plazas = $this->getResumen();
		// Puede no tener plazas asignadas...
		if (isset($a_plazas[$dl]['total_disponibles']) && isset ($a_plazas[$dl]['total_ocupadas'])) {
			$libres = $a_plazas[$dl]['total_disponibles'] - $a_plazas[$dl]['total_ocupadas'];
		} else {
			$libres = 0;
		}
		return $libres;
	}
	
	/**
	 * Devuelve el nombre del popietario de la primera plaza libre
	 * 
	 * usa clases externas:
	 *	asistentes\model\entity\GestorAsistente();
	 *	actividades\Actividad($id_activ);
	 *  actividadplazas\model\entity\GestorActividadPlazas();
	 * 
	 * @return array $propiedad key = "dl_propietaria>dl_cedida", value = texto explicativo. 
	 * 
	 */
	public function getPropiedadPlazaLibre() {
		/*
		puede ser una plaza propia o una cedida.
		 */
		$gesAsistentes = new \asistentes\model\entity\GestorAsistente();
			
		$propiedad = array();
		$id_activ = $this->getId_activ();
		$mi_dl = \core\ConfigGlobal::mi_dele();
		
		$oActividad = new \actividades\model\entity\Actividad($id_activ);
		$publicado = $oActividad->getPublicado();
		// Si no está publicada no tiene plazas de calendario.
		// Se toman todas la de la actividad como propias.
		if ($publicado === false) {
			$pl_propias = $this->getPlazasTotales();
		} else {
			// las que me correponden por calendario - las cedidas
			$pl_calendario = $this->getPlazasCalendario($mi_dl);
			$pl_cedidas = $this->getPlazasCedidas($mi_dl);
			$pl_propias = $pl_calendario - $pl_cedidas;
		}
		if ($pl_propias > 0) {
			$ocu = $gesAsistentes->getPlazasOcupadasPorDl($id_activ,$mi_dl,$mi_dl);
			if ($ocu < $pl_propias) {
				$num = $pl_propias - $ocu;
				$propiedad["$mi_dl>$mi_dl"] = "$mi_dl ($ocu de $pl_propias)";
			}
		}

		// Si no quedan, ver si dispongo de otras
		if (empty($propiedad)) {
			$id_mi_dl = $this->getDlId($mi_dl);
			$dl_org = $this->getDl_org();

			//Conseguidas
			$gesActividadPlazas = new \actividadplazas\model\entity\GestorActividadPlazas();
			// plazas de calendario de cada dl
			$plazas_conseguidas = 0;
			$cActividadPlazas = $gesActividadPlazas->getActividadesPlazas(array('id_activ'=>$id_activ));
			foreach ($cActividadPlazas as $oActividadPlazas) {
				$id_dl_otra = $oActividadPlazas->getId_dl();
				$dl_otra = $this->getDlText($id_dl_otra);
				$dl_tabla = $oActividadPlazas->getDl_tabla();
				
				$json_cedidas = $oActividadPlazas->getCedidas();
				if (!empty($json_cedidas)){
					$aCedidas = json_decode($json_cedidas,TRUE);
					foreach ($aCedidas as $dl_2 => $num_plazas) {
						if ($mi_dl == $dl_2) {
							$ocu = $gesAsistentes->getPlazasOcupadasPorDl($id_activ,$mi_dl,$dl_otra);
							if ($ocu < $num_plazas) {
								$propiedad["$dl_otra>$dl_2"] = "$dl_otra ($ocu de $num_plazas)";
							}
						}
					}
				}
			}
		}
		return $propiedad;
	}


	/* METODES PROTECTED --------------------------------------------------------*/
	public function setId_activ($iid_activ='') {
		$this->iid_activ = $iid_activ;
	}
	
	protected function getId_activ() {
		if (!isset($this->iid_activ)) {
			exit('error');
		}
		return $this->iid_activ;
	}
	
	protected function getDl_org() {
		if (!isset($this->dl_org)) {
			$id_activ = $this->getId_activ();
			$oActividad = new \actividades\model\entity\Actividad($id_activ);
			$this->dl_org = $oActividad->getDl_org();
		}
		return $this->dl_org;
	}

	// array de id=>dl
	protected function setArrayDl() {
		if (!isset($this->a_dele)) {
			$gesDelegacion = new \ubis\model\entity\GestorDelegacion();
			$cDelegaciones = $gesDelegacion->getDelegaciones(array('_ordre'=>'region,dl'));
			$this->a_dele = array();
			$this->a_id_dele = array();
			foreach ($cDelegaciones as $oDelegacion) {
				$dl = $oDelegacion->getDl();
				$id_dl = $oDelegacion->getId_dl();
				$a_dele[$id_dl] = $dl;
				$a_id_dele[$dl] = $id_dl;
			}
			$this->a_dele = $a_dele;
			$this->a_id_dele = $a_id_dele;
		}
		return true;
	}
	protected function getDlText($id_dl) {
		if (!isset($this->a_dele)) {
			$this->setArrayDl();
		}
		$a_dele = $this->a_dele;
		return $a_dele[$id_dl];
	}
	protected function getDlId($dl) {
		if (!isset($this->a_id_dele)) {
			$this->setArrayDl();
		}
		$a_id_dele = $this->a_id_dele;
		return $a_id_dele[$dl];
	}
	/* METODES GET i SET --------------------------------------------------------*/
}
?>