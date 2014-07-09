<?php
namespace web;
/**
 * Classe que passa el periode amb texte a data inici i data fi.
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 26/11/2010
 */
class Periodo {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * sPeriodo de Periodo
	 *
	 * @var string
	 */
	 private $sPeriodo;
	/**
	 * iAny de Periodo
	 *
	 * @var integer
	 */
	 private $iany;
	/**
	 * df_ini de Periodo
	 *
	 * @var date
	 */
	 private $df_ini;
	/**
	 * df_fin de Periodo
	 *
	 * @var date
	 */
	 private $df_fin;

	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 *
	 */
	function __construct() {
	}

	/* METODES PUBLICS ----------------------------------------------------------*/
	
	/**
	 * Recupera l'atribut id_tipo_activ en format de regexp
	 *
	 * @return string
	 */
	public function getHtml() {
		$sHtml='<script>
			funjs_activar_fecha = function() {
				var f=$(\'#periodo\').val();	
				if (f=="otro") {
					$(\'#span_fechas\').removeClass(\'d_novisible\');
					$(\'#span_fechas\').toggleClass(\'d_visible\');
				} else {
					$(\'#span_fechas\').removeClass(\'d_visible\');
					$(\'#span_fechas\').toggleClass(\'d_novisible\');
				}
			}
			$(function() { $( "#empiezamin" ).datepicker(); });
			$(function() { $( "#empiezamax" ).datepicker(); });
			</script>';
	}

	function setAny($iany) {
		$this->iany = $iany;
	}
	function getF_ini() {
		return $this->df_ini;
	}
	function getF_fin() {
		return $this->df_fin;
	}

	function setPeriodo($sPeriodo) {
		$any = empty($this->iany)? date('Y') : $this->iany;
		$mes = date('m');
		switch ($sPeriodo) {
			case "desdeHoy":
				$inicio = date('d/m/Y');	
				$fin = date('d/m/Y',mktime(0, 0, 0, $mes+6, 0, $any));
				break;
			case "curso":
				if ($mes>9) {
					$any2=$any+1;
					$inicio = "1/10/".$any;	
					$fin = "31/5/".$any2;
				} else {
					$any2=$any-1;
					$inicio = "1/10/".$any2;	
					$fin = "31/5/".$any;
				}
				break;
			case "curso_crt":
				if ($mes>9) {
					$any2=$any+1;
					$inicio = "1/10/".$any;	
					$fin = "31/8/".$any2;
				} else {
					$any2=$any-1;
					$inicio = "1/10/".$any2;	
					$fin = "31/8/".$any;
				}
				break;
			case "curso_ca":
				if ($mes>9) {
					$any2=$any+1;
					$inicio = "1/10/".$any;	
					$fin = "30/9/".$any2;
				} else {
					$any2=$any-1;
					$inicio = "1/10/".$any2;	
					$fin = "30/9/".$any;
				}
				break;
			case "trimestre":
				$inicio = "1/$mes/".($any);	
				$fin = date('d/m/Y',mktime(0, 0, 0, $mes+3, 0, $any));
				break;
			case "mes":
				$inicio = "1/$mes/".($any);	
				$fin = date('d/m/Y',mktime(0, 0, 0, $mes+1, 0, $any));
				break;
			case "verano":
				$inicio = "1/6/".$any;	
				$fin = "30/9/".$any;
				break;
			case "trimestre_1":
				$inicio = "1/1/".($any);	
				$fin = "31/3/".($any);
				break;
			case "trimestre_2":
				$inicio = "1/4/".($any);	
				$fin = "30/6/".($any);
				break;
			case "trimestre_3":
				$inicio = "1/7/".($any);	
				$fin = "30/9/".($any);
				break;
			case "trimestre_4":
				$inicio = "1/10/".($any);	
				$fin = "31/12/".($any);
				break;
			case "tot_any":
				$inicio = "1/1/".($any);	
				$fin = "31/12/".($any);
				break;
			case "any_prox":
				$inicio = "1/1/".($any+1);	
				$fin = "31/12/".($any+1);
				break;
			default:
				if (empty($inicio)) $inicio = "1/1/".($any);	
				if (empty($fin)) $fin = "31/12/".($any);
		}
		$this->df_ini = $inicio;
		$this->df_fin = $fin;
	}

}
?>
