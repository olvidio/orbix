<?php
namespace web;
/**
 * Classe per les dates. Afageix a la clase del php la vista amn num. romans.
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 26/11/2010
 */
class DateTimeLocal Extends \DateTime {
	private $oData;

	public static function Meses() {
		$aMeses = array('1'=>_("enero"),
			'2'=>_("febrero"),
			'3'=>_("marzo"),
			'4'=>_("abril"),
			'5'=>_("mayo"),
			'6'=>_("junio"),
			'7'=>_("julio"),
			'8'=>_("agosto"),
			'9'=>_("septiembre"),
			'10'=>_("octubre"),
			'11'=>_("noviembre"),
			'12'=>_("diciembre")	
		);
		return $aMeses;
	}

	public function setDateTime($oDateTime) {
		$this->oData = $oDateTime;
	}
	public function setFromFormat($format,$data) {
		//$this->oData = DateTime::createFromFormat($format,$data);
		$this->oData = parent::createFromFormat($format,$data);
	}
    public function format($format) {
        $english = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
        $local = array(_("Lunes"), _("Martes"), _("Miércoles"), _("Jueves"), _("Viernes"), _("Sábado"), _("Domingo"));
        return str_replace($english, $local, $this->oData->format($format));
    }
    public function formatRoman() {
		$a_num_romanos=array('1'=>"I",'2'=>"II",'3'=>"III",'4'=>"IV",'5'=>"V",'6'=>"VI",'7'=>"VII",'8'=>"VIII",'9'=>"IX",
				'10'=>"X",'11'=>"XI",'12'=>"XII");
		$dia = $this->oData->format('j');
		$mes = $this->oData->format('n');
		$any = $this->oData->format('y');
        return "$dia.".$a_num_romanos[$mes].".$any";
    }

    public function duracion($oDateDiff) {
		$interval = $this->oData->diff($oDateDiff);
		$horas = $interval->format('%a')*24 +$interval->format('%h')+$interval->format('%i')/60+$interval->format('%s')/3600;
		/*
		$dias=$horas/24;
		$e_dias=($dias % $horas);
		$dec=round(($dias-$e_dias),1);
		if ($dec > 0.1) { $dec=0.5; } else { $dec=0; }
		return ($e_dias+$dec);
		*/
		$dias=round($horas/24,2);
		return $dias;
	}
    public function duracionAjustada($oDateDiff) {
		$interval = $this->oData->diff($oDateDiff);
		$horas = $interval->format('%a')*24 +$interval->format('%h')+$interval->format('%i')/60+$interval->format('%s')/3600 + 12;
		$dias=$horas/24;
		$e_dias=($dias % $horas);
		$dec=round(($dias-$e_dias),1);
		if ($dec > 0.1) { $dec=0.5; } else { $dec=0; }
		return ($e_dias+$dec);
	}
}

?>
