<?php
namespace permisos\model;
use actividades\model\entity\TipoDeActividad;
use core\ConfigGlobal;
class xResto {
	/* ATRIBUTS ----------------------------------------------------------------- */
	/**
	 * Per saber a quina activitat fa referència.
	 *
	 * @var integer
	 */
	protected $iid_activ;

	/**
	 * fases. array amb les posibles fases.
	 *
	 * @var array
	 */
	protected $aFases = array();
	/**
	 * permis amb el que es contruyeix la clase. La resta es compara amb aquest.
	 *
	 * @var integer
	 */
	protected $iaccion;
	/**
	 * array per posar el permis per cada fase
	 *
	 * @var array
	 */
	protected $aAfecta;
	/**
	 * array per posar el permis per cada fase
	 *
	 * @var array
	 */
	protected $aDades=array();
	/**
	 * id d'usuari o grup que ha generat el permís.
	 *
	 * @var integer
	 */
	protected $iGenerador;

	/* METODES ----------------------------------------------------------------- */
	public function __construct($iid_tipo_activ) {
		$this->iid_tipo_activ = $iid_tipo_activ;
		
		$oTipoDeActividad = new TipoDeActividad($iid_tipo_activ);
        $this->iid_tipo_proceso = $oTipoDeActividad->getId_tipo_proceso();
	}

	/**
	 * Para mirar si tiene el iAfecta, sino, hay que mirar en el id_tipo_activ 
	 * de nivel superior.
	 * 
	 * @param integer $iAfecta
	 * @return boolean
	 */
	public function hasAfecta($iAfecta) {
	    foreach($this->aDades as $sumaAfecta => $arr) {
	        // miro la suma de bits
	        $has_one = (($sumaAfecta & $iAfecta) != 0);
	        if ($has_one) {
	            return TRUE;
	        }
	    }
        return FALSE;
	}
	
	public function getPerm($id_tipo_proceso,$iAfecta,$iFase) {
		$i=0;
		foreach ($this->aDades as $key => $a_proceso_perm) {
			$i++;
			$c = (int)$key & (int)$iAfecta;
			if ($c > 0) {
				$val = $a_proceso_perm[$id_tipo_proceso];
				if (array_key_exists($iFase,$val) && !empty($val[$iFase])) {
					return $val[$iFase];
				} else {
					//echo "<br>posible error: Fase=>$iFase, Afecta=>$iAfecta.  dades:<br>";
					//print_r($this->aDades);
					//continue;
				}
			} else {
				//return false;
				//echo "i: $i<br>";
			}
		}
		return false;
		//return 'next';
	}
	public function setFasesInterval($iFaseIni,$iFaseFin) {
	}
	public function setOmplir($id_tipo_proceso,$iFase,$iPerm,$iAfecta) {
		$this->aDades[$iAfecta][$id_tipo_proceso][$iFase]=$iPerm;
	}
	public function setOrdenar() {
		if (is_array($this->aDades)) ksort($this->aDades);
	}
}
