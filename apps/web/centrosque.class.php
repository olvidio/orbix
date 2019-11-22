<?php
namespace web;
use ubis\model\entity\GestorCentroDl;
/**
 * Classe que presenta un quadre per buscar diferents centres.
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 24/04/2012
 */
class CentrosQue {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * sTitulo de CentroQue
	 *
	 * @var string
	 */
	 private $sTitulo;
	/**
	 * aCentros de CentroQue
	 *
	 * @var array
	 */
	 private $aCentros;
	/**
	 * oDesplCentros de CentroQue
	 *
	 * @var object tipo Desplegble
	 */
	 private $oDesplCentros;



	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 *
	 */
	function __construct() {
	}

	/* METODES PUBLICS ----------------------------------------------------------*/
	
	/**
	 * Retorna el codi html amb el desplegable de cases.
	 *
	 * @return string
	 */
	public function getHtmlTabla2() {
		$aOpcionesCentros = $this->getDesplCentros()->getOpciones();
		$oSelects = new DesplegableArray('',$aOpcionesCentros,'id_ctr');
		$oSelects->setBlanco('t');
		$oSelects->setAccionConjunto('fnjs_mas_centros(event)');
		$sHtml='<script>
		fnjs_mas_centros = function(evt) {
			if(evt=="x") {
				var valor=1;
			} else {
				var id_campo=evt.currentTarget.id;
				var valor=$(id_campo).val();
				evt.preventDefault();
				evt.stopPropagation();
			}
			if (evt.keyCode==9 || evt.type=="change" || evt=="x") {
				if (valor!=0) {
					'.$oSelects->ListaSelectsJs().'
				}
			}
		}';
		$sHtml.=$oSelects->ComprobarSelectJs(); 
		$sHtml.= '</script>';
		$sHtml.='<table>';
		if (!empty($this->sTitulo)) {
			$sHtml.='<tr><th class=titulo_inv colspan="6">';
			$sHtml.=$this->sTitulo;
			$sHtml.='</th></tr>';
		}
		$sHtml.='<tr>';
		if (isset($this->sAntes)) {
			$sHtml.='<td>'.$this->sAntes.'</td>';
		}
		$sHtml.='<td>'.$oSelects->ListaSelects().'</td>';
		if (isset($this->sBoton)) {
			$sHtml.='<td>'.$this->sBoton.'</td>';
		}
		$sHtml.='</tr></table>';
		return $sHtml;
	}

	/**
	 * Retorna el codi html amb els radio buttons per escollir un grup de cases sv,sf.
	 *
	 * @return string
	 */
	public function getHtmlTabla() {
		$aOpcionesCentros = $this->getDesplCentros()->getOpciones();
		$oSelects = new DesplegableArray('',$aOpcionesCentros,'id_ctr');
		$oSelects->setBlanco('t');
		$oSelects->setAccionConjunto('fnjs_mas_centros(event)');
		$sHtml='<script>
		funjs_otro = function(v) {
			if (v==1) {
				$(\'#id_ctr_span\').addClass(\'d_visible\');
				$(\'#ctr_sel_9\').prop("checked",true);
			} else {
				$(\'#id_ctr_span\').html("");	
			}
		}
		fnjs_mas_centros = function(evt) {
			funjs_otro(1);
			if(evt=="x") {
				var valor=1;
			} else {
				var id_campo=evt.currentTarget.id;
				var valor=$(id_campo).val();
				evt.preventDefault();
				evt.stopPropagation();
			}
			if (evt.keyCode==9 || evt.type=="change" || evt=="x") {
				if (valor!=0) {
					'.$oSelects->ListaSelectsJs().'
				}
			}
		}';
		$sHtml.=$oSelects->ComprobarSelectJs(); 
		$sHtml.= '</script>';
		$sHtml.='<table>';
		$sHtml.='<tr><th class=titulo_inv colspan="3">';
		$sHtml.=$this->sTitulo;
		$sHtml.='</th></tr>';
		foreach ($this->aCentros as $inum => $sCentro) {
			if ($inum === 9) {
				$sHtml.='<tr><td><input type="radio" id="ctr_sel_'.$inum.'" name="ctr_sel" value="'.$inum.'" onClick="funjs_otro(1);">'.$sCentro.'</td>';
				// para seleccionar más de una centro
				$sHtml.='<td>'.$oSelects->ListaSelects().'</td>';
			} else {
				$sHtml.='<tr><td><input type="radio" id="ctr_sel_'.$inum.'" name="ctr_sel" value="'.$inum.'" onClick="funjs_otro(0);">'.$sCentro.'</td></tr>';
			}
		}
		$sHtml.='<tr><td> </td></tr>';
		$sHtml.='</table>';

		return $sHtml;
	}

	function setCentros($sQue) {
		$this->aCentros=array();
		switch ($sQue) {
			case 'all':
				$this->aCentros[1] = _("centros sólo sv");
				$this->aCentros[2] = _("centros sólo sf");
				$this->aCentros[3] = _("centros comunes");
				$this->aCentros[4] = _("centros sv");
				$this->aCentros[5] = _("centros sf");
				$this->aCentros[6] = _("centros sv y sf");
				$this->aCentros[9] = _("un centro o lugar");
				$this->aCentros[11] = _("todas las actividades sv");
				$this->aCentros[12] = _("todas las actividades sf");
				break;
			case 'sv':
				$this->aCentros[3] = _("centros comunes");
				$this->aCentros[4] = _("centros sv");
				$this->aCentros[9] = _("un centro o lugar");
				$this->aCentros[11] = _("todas las actividades sv");
				break;
			case 'sf':
				$this->aCentros[3] = _("centros comunes");
				$this->aCentros[5] = _("centros sf");
				$this->aCentros[6] = _("centros sv y sf");
				$this->aCentros[9] = _("un centro o lugar");
				$this->aCentros[12] = _("todas las actividades sf");
				break;
			case 'centro':
				$this->aCentros[9] = _("un centro o lugar");
				break;
		}
	}
	
	function setPosiblesCentros($sCondicion) {
		if (!isset($this->oDesplCentros)) {
			$oDesplCentros = new Desplegable();
			$oDesplCentros->setNombre('id_ctr');
			$oDesplCentros->setBlanco(true);
			$oDesplCentros->setAction('funjs_otro(1)');
			$this->oDesplCentros = $oDesplCentros;
		}
		$oGesCentros= new GestorCentroDl();
		$oOpciones = $oGesCentros->getPosiblesCentros($sCondicion);
		$oDesplCentros->setOpciones($oOpciones);
		$this->oDesplCentros = $oDesplCentros;
	}
	function getPosiblesCentros() {
		return $this->oDesplCentros->getOpciones();
	}
	function getDesplCentros() {
		if (!isset($this->oDesplCentros)) {
			$oGesCentros= new GestorCentroDl();
			$oOpciones = $oGesCentros->getPosiblesCentros();
			$oDesplCentros = new Desplegable();
			$oDesplCentros->setNombre('id_ctr');
			$oDesplCentros->setOpciones($oOpciones);
			$oDesplCentros->setBlanco(true);
			$oDesplCentros->setAction('funjs_otro(1)');
			$this->oDesplCentros = $oDesplCentros;
		}
		return $this->oDesplCentros;
	}
	function setAction($sAction) {
		if (!isset($this->oDesplCentros)) {
			$oDesplCentros = new Desplegable();
			$oDesplCentros->setNombre('id_ctr');
			$oDesplCentros->setBlanco(true);
			$this->oDesplCentros = $oDesplCentros;
		}
		$this->oDesplCentros->setAction($sAction);
	}
	function setTitulo($sTitulo) {
		$this->sTitulo=$sTitulo;
	}
	function setBoton($sBoton) {
		$this->sBoton=$sBoton;
	}
	function setAntes($sAntes) {
		$this->sAntes=$sAntes;
	}
}
