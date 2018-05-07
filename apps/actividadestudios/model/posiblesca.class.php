<?php
namespace actividadestudios\model;

use actividades\model\entity as actividades;
use asignaturas\model\entity as asignaturas;
use asistentes\model\entity as asistentes;
use personas\model\entity as personas;
use core;
use web;

/**
 * Calcula los posibles ca para una persona o grupo
 * 
 * 
 *
 * @package	orbix
 * @subpackage	actividadestudios
 * @author	Daniel Serrabou
 * @since		15/5/02.
 * @version 1.0  refactoring: separar vistas
 * @created Mayo 2018
 */
class PosiblesCa {


	public function getListaCa() {
		$aParamGo=array('que'=>'activ','pau'=>'p','id_pau'=>$id_nom,'obj_pau'=>$obj_pau,'id_dossier'=>'1301y1302');
		$pagina=web\Hash::link('apps/dossiers/controller/dossiers_ver.php?'.http_build_query($aParamGo));

		$f=0;
		$a1=array();
		foreach($cuadro as $a1) {
			$f++;
			$ctr=$a1["ctr"];
			$nom=$a1["nom"];
			$stgr=$a1["stgr"];
			$actividades=$a1["actividades"];


			
			//las filas
			echo $oPosicion->mostrar_left_slide(1);
			echo "<table>";
			echo "<th class='ca_posibles_nom' colspan=2>posibles ca de $nom ($ctr)</th><th>stgr: $stgr</th>";
			foreach($actividades as $a3) {
					   $nom_activ=$a3["nom_activ"];
					   $creditos=$a3["creditos"];
					   $nivel_stgr=$a3["nivel_stgr"];
					   switch ($nivel_stgr) {
						case 1:
							$est=_("bienio");
							break;
						case 2:
							$est=_("cuadrienio-I");
							break;
						case 3:
							$est=_("cuadrienio-II-IV");
							break;
						case 4: 
							$est=_("repaso");
							break;
						case 5: 
							$est=_("ce");
							break;
						}
					   echo "<tr><td>$est</td><td style=\"text-align: left;\">  $nom_activ</td><td>$creditos</td></tr>";
					}
		}
		echo "</table>";
		echo "<h3><span class=link onclick=\"fnjs_update_div('#main','$pagina')\" >". _("ir a dossier de actividades")."</span></h3>";
	}

	public function getTablaCa() {
		
	}
}