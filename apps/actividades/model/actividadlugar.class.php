<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace actividades\model;

use ubis\model\entity as ubis;
use web;

/**
 * Description of actividadlugar
 *
 * @author Daniel Serrabou <dani@moneders.net>
 */
class ActividadLugar {
	
	private $isfsv;
	private $ssfsv;
	private $opcion_sel;

	public function getFiltroLugar($id_ubi) {
			
		//dl|dlb,r|Aut
		$oCasa = new ubis\Casa($id_ubi);
		$dl = $oCasa->getDl();
		$reg = $oCasa->getRegion();

		if (empty($dl)) {
			$filtro_lugar = 'r|'.$reg;
		} else {
			$filtro_lugar = 'dl|'.$dl;
		}
		return $filtro_lugar;
	}

	public function getLugaresPosibles($Qentrada='') {
	 /* Para que los de dre puedan introducir actividades de la sf habrá que hacer de otra manera... */
		$donde='';
		if (empty($Qentrada)) die();

		$dl_r=strtok($Qentrada,"|");
		$reg=strtok("|");
		switch ($dl_r) {
			case "dl":
				$donde = "WHERE dl='$reg' ";
				$tabla_ctr="u_centros";
				$donde_ctr = "$donde AND cdc='t'";
				break;
			case "r":
				$donde = "WHERE region='$reg' ";
				$tabla_ctr="u_centros";
				$donde_ctr = "$donde AND cdc='t'";
				break;
		}
		if ($this->ssfsv == 'sv') $this->isfsv = 1;
		if ($this->ssfsv == 'sf') $this->isfsv = 2;
		switch ($this->isfsv) {
			case 1:
				$donde_ctr = "$donde AND cdc='t'";
				$donde .= "AND sv='true' ";
				break;
			case 2:
				$donde_ctr = "$donde AND cdc='t'";
				$donde .= "AND sf='true' ";
				break;
		}
		if ($dl_r!="dl" and $dl_r!="r") { $donde=""; }
		if (!empty($donde)) { $donde.=" AND status='t'"; } else { $donde="WHERE status='t'"; }
		$oGesCasas= new ubis\GestorCasa();
		$oOpcionesCasas = $oGesCasas->getPosiblesCasas($donde);
	
		$oGesCentros = new ubis\GestorCentroDl();
		$oOpcionesCentros = $oGesCentros->getPosiblesCentros($donde_ctr);

		$oDesplCasas = new web\Desplegable(array('oOpciones'=>$oOpcionesCasas));	
		$oDesplCasas->setNombre('id_ubi');
		$oDesplCasas->setBlanco(true);
		if (!empty($this->opcion_sel)) {
			$oDesplCasas->setOpcion_sel($this->opcion_sel);
		}
		return $oDesplCasas;
	}
	
	public function setIsfsv($isfsv) {
		$this->isfsv = $isfsv;
	}

	public function setSsfsv($ssfsv) {
		$this->ssfsv = $ssfsv;
	}

	public function setOpcion_sel($opcion_sel) {
		$this->opcion_sel = $opcion_sel;
	}
}