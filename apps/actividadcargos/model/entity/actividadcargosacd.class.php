<?php
namespace actividadcargos\model\entity;
/**
 * Classe que implementa l'entitat d_cargos_activ amb restricció a només els sacd.
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 17/04/2012
 */
class ActividadCargoSacd Extends ActividadCargo {
	/* de fet no té cap métode adicional (de moment), pero a l'hora de fer els avisos són dues classes diferents,
	   i es poden donar permisos diferents...
	*/
	public function printItem($string) {
		$GesCargos = new GestorCargo();
		//$GesCargos->getListaCargosxTipoCargo($sTipoCargo);
        //echo 'Bar: ' . $string . PHP_EOL;
    }
}