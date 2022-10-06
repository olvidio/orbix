<?php

namespace permisos\model;

use actividades\model\entity\TipoDeActividad;
use core\ConfigGlobal;
use procesos\model\entity\GestorActividadFase;
use procesos\model\entity\GestorActividadProcesoTarea;

class XResto
{
    /* ATRIBUTOS ----------------------------------------------------------------- */
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
    protected $aDades = array();
    /**
     * id d'usuari o grup que ha generat el permís.
     *
     * @var integer
     */
    protected $iGenerador;

    /* METODES ----------------------------------------------------------------- */
    public function __construct($iid_tipo_activ)
    {
        $this->iid_tipo_activ = $iid_tipo_activ;
    }

    public function setOmplir($iAfecta, $fase_ref, $perm_on, $perm_off)
    {
        $this->aDades[$iAfecta][$fase_ref]['on'] = $perm_on;
        $this->aDades[$iAfecta][$fase_ref]['off'] = $perm_off;
    }

    /**
     * Para mirar si tiene el iAfecta, sino, hay que mirar en el id_tipo_activ
     * de nivel superior.
     *
     * @param integer $iAfecta
     * @return boolean
     */
    public function hasAfecta($iAfecta)
    {
        foreach ($this->aDades as $sumaAfecta => $arr) {
            // miro la suma de bits
            $has_one = (($sumaAfecta & $iAfecta) != 0);
            if ($has_one) {
                return TRUE;
            }
        }
        return FALSE;
    }

    public function getFaseRef($iAfecta)
    {
        $fase_ref = key($this->aDades[$iAfecta]);
        return $fase_ref;
    }

    public function getPerm($iAfecta, $id_fase_ref, $on_off)
    {
        if (empty($this->aDades[$iAfecta][$id_fase_ref][$on_off])) {
            return 0; // No tiene permiso
        }
        $perm = $this->aDades[$iAfecta][$id_fase_ref][$on_off];
        return $perm;
    }

    public function setOrdenar()
    {
        if (is_array($this->aDades)) ksort($this->aDades);
    }

    public function getFases()
    {
        $aFases = [];
        foreach ($this->aDades as $iAfecta => $byProceso) {
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
