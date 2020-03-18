<?php
namespace permisos\model;
use actividades\model\entity\TipoDeActividad;
use core\ConfigGlobal;
use procesos\model\entity\GestorActividadFase;
use procesos\model\entity\GestorActividadProcesoTarea;
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
	 * @var array  aDades[$iAfecta][$id_tipo_proceso][$iFase]=$iPerm;
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
	
	public function getPerm($id_tipo_proceso,$iAfecta,$iFase,$id_activ='') {
		$i=0;
		foreach ($this->aDades as $key => $a_proceso_perm) {
			$i++;
			$c = (int)$key & (int)$iAfecta;
			if ($c > 0) {
			    // si no existe 
			    if (empty($a_proceso_perm[$id_tipo_proceso])) {
			        continue;
			    }
				$val = $a_proceso_perm[$id_tipo_proceso];
            	return $this->getPermFase($val,$id_tipo_proceso,$iFase,$id_activ);
			} else {
				//return false;
				//echo "i: $i<br>";
			}
		}
		return false;
		//return 'next';
	}
	
	
	private function getPermFase($val,$id_tipo_proceso,$id_fase,$id_activ) {
        if (array_key_exists($id_fase,$val) && !empty($val[$id_fase])) {
            return $val[$id_fase];
        } else {
            // En el caso de crear, no hay id_activ. Miro la fase según el proceso:
            if (empty($id_activ)) {
                $gesActividadFase = new GestorActividadFase();
                $id_fase_anterior = $gesActividadFase->getFaseAnterior($id_tipo_proceso,$id_fase);
            } else {
                $gesActividadProcesoTarea = new GestorActividadProcesoTarea();
                $id_fase_anterior = $gesActividadProcesoTarea->getFaseAnteriorCompletada($id_activ,$id_fase);
            }
            if (empty($id_fase_anterior)) {
                //No hay fase anterior
                return FALSE;
            } else {
                return $this->getPermFase($val,$id_tipo_proceso,$id_fase_anterior,$id_activ);
            }
        }
	}
	
	public function setFasesInterval($iFaseIni,$iFaseFin) {
	}
	public function setOmplir($id_tipo_proceso,$iFase,$iPerm,$iAfecta) {
		$this->aDades[$iAfecta][$id_tipo_proceso][$iFase]=$iPerm;
	}
	public function setOrdenar() {
		if (is_array($this->aDades)) ksort($this->aDades);
	}
	
	public function getFases() {
	    $aFases = [];
	    foreach($this->aDades as $iAfecta => $byProceso) {
	        $aa = $byProceso;
	        foreach ($byProceso as $id_tipo_proceso => $byFase) {
	            foreach ($byFase as $id_fase => $perm) {
	               $aFases[] = $id_fase;
	            }
	        }
	    }
		//[$id_tipo_proceso][$iFase]=$iPerm;
	   return $aFases; 
	}
}
