<?php
namespace web;
use core\ConfigGlobal;
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
	 * @var DateTimeLocal
	 */
	 private $df_ini;
	/**
	 * df_fin de Periodo
	 *
	 * @var DateTimeLocal
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
	function getF_ini_iso() {
		return $this->df_ini;
	}
	function getF_fin_iso() {
		return $this->df_fin;
	}

	function setPeriodo($sPeriodo) {
		$any = empty($this->iany)? date('Y') : $this->iany;
		$mes = date('m');
		switch ($sPeriodo) {
			case "desdeHoy":
				$inicio = date('Y/m/d');	
				$fin = date('Y/m/d',mktime(0, 0, 0, $mes+6, 0, $any));
				break;
			case "curso":
				if ($mes>9) {
					$any2=$any+1;
					$inicio = $any."/10/1";	
					$fin = $any2."/5/31";
				} else {
					$any2=$any-1;
					$inicio = $any2."/10/1";	
					$fin = $any."/5/31";
				}
				break;
			case "curso_crt":
			    $ini_d = ConfigGlobal::$crt_inicio['d'];
			    $ini_m = ConfigGlobal::$crt_inicio['m'];
			    $fin_d = ConfigGlobal::$crt_fin['d'];
			    $fin_m = ConfigGlobal::$crt_fin['m'];
				if ($mes>9) {
				    $any2=$any-1;
				    $inicio = "$any2-$ini_m-$ini_d";
				    $fin = "$any-$fin_m-$fin_d";
				} else {
				    $any2=$any-2;
				    $any--;
				    $inicio = "$any2-$ini_m-$ini_d";
				    $fin = "$any-$fin_m-$fin_d";
				}
				break;
			case "curso_ca":
			    $ini_d = ConfigGlobal::$est_inicio['d'];
			    $ini_m = ConfigGlobal::$est_inicio['m'];
			    $fin_d = ConfigGlobal::$est_fin['d'];
			    $fin_m = ConfigGlobal::$est_fin['m'];
				if ($mes>9) {
				    $any2=$any-1;
				    $inicio = "$any2-$ini_m-$ini_d";
				    $fin = "$any-$fin_m-$fin_d";
				} else {
				    $any2=$any-2;
				    $any--;
				    $inicio = "$any2-$ini_m-$ini_d";
				    $fin = "$any-$fin_m-$fin_d";
				}
				break;
			case "trimestre":
				$inicio = $any."/$mes/1";	
				$fin = date('Y/m/d',mktime(0, 0, 0, $mes+3, 0, $any));
				break;
			case "mes":
				$inicio = $any."/$mes/1";	
				$fin = date('Y/m/d',mktime(0, 0, 0, $mes+1, 0, $any));
				break;
			case "verano":
				$inicio = $any."/6/1";	
				$fin = $any."/9/30";
				break;
			case "trimestre_1":
				$inicio = $any."/1/1";	
				$fin = $any."/3/31";
				break;
			case "trimestre_2":
				$inicio = $any."/4/1";	
				$fin = $any."/6/30";
				break;
			case "trimestre_3":
				$inicio = $any."/7/1";	
				$fin = $any."/9/30";
				break;
			case "trimestre_4":
				$inicio = $any."/10/1";	
				$fin = $any."/12/31";
				break;
			case "tot_any":
				$inicio = $any."/1/1";	
				$fin = $any."/12/31";
				break;
			case "any_prox":
				$inicio = ($any+1)."/1/1";	
				$fin = ($any+1)."/12/31";
				break;
			default:
				if (empty($inicio)) $inicio = $any."/1/1";	
				if (empty($fin)) $fin = $any."/12/31";
		}
		$this->df_ini = $inicio;
		$this->df_fin = $fin;
	}

}
