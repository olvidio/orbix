<?php

namespace permisos\model;

use function core\is_true;
use procesos\model\PermAccion;

/**
 * Classe que genera un array amb els permisos per cada usuari. Es guarda a la sesió per tenir-ho a l'abast en qualsevol moment:
 *
 *    $_SESSION['oPermActividades'] = new PermisosActividades(ConfigGlobal::id_usuario());
 *
 * Estructura de l'array:
 *    - aAfecta: el nom i corresponent integer de les propietats a les que afecta.
 *    - 2 coponents: aPermDl i aPermOtras, segons siguin els permisos per les activitats de la dl o la resta.
 *      Cada un d'aquests vectors es composa de:
 *        a) primer component: id_tipo_activ_txt = '12....'
 *            a1) iAfecta
 *            a2) id_tipo_proceso
 *            a3) iFase
 *            a4) permiso
 *
 *            $this->aDades[$iAfecta][$id_tipo_proceso][$iFase]=$iPerm;
 *
 *
 *
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 20/11/2010
 */
class PermisosActividadesTrue
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Array amb els permisos.
     *
     * @var array
     */
    protected $aPermDl = array();
    protected $aPermOtras = array();
    /**
     * Per saber a quina activitat fa referència.
     *
     * @var string
     */
    protected $sid_tipo_activ;

    /**
     * Id_activ de PermisoActividad
     *
     * @var integer
     */
    private $iid_activ;
    /**
     * Id_tipo_proceso de PermisoActividad
     *
     * @var integer
     */
    private $iid_tipo_proceso;
    /**
     * propia de PermisoActividad
     *
     * @var boolean
     */
    private $bpropia;
    /**
     * número de orden de la fase actual
     *
     * @var integer
     */
    private $iid_fase;
    /**
     * si ha llegado al final.
     *
     * @var boolean
     */
    private $btop;

    private $oGesActiv;
    /**
     * Dbl objeto conexión DB.
     *
     * @var object
     */
    private $oDbl;


    /* METODES ----------------------------------------------------------------- */
    public function __construct($iid_usuario)
    {
    }

    public function carregarTrue($sCondicion_usuario, $dl_propia)
    {
    }

    public function setActividad($id_activ, $id_tipo_activ = '', $dl_org = '')
    {
    }

    public function setId_fase($iid_fase)
    {
        $this->iid_fase = $iid_fase;
    }

    public function getId_fase()
    {
        if (empty($this->iid_fase)) {
            echo "No hay fase!!";
        }
        return $this->iid_fase;
    }

    public function getPermisoActual($sAfecta)
    {
        // devuelve permiso de crear (15) en cualquier caso
        return new PermAccion(15);
    }

    public function getPermisoActualPrev($iAfecta)
    {
        //if ($this->getIdTipoPrev() === false) return false;
        if ($this->getIdTipoPrev() === false) return new PermAccion(0);
        return $this->getPermisoActual($iAfecta);
    }

    public function getPermisos($id_tipo_activ_txt = '')
    {
        //echo "tipo_activ: $id_tipo_activ_txt, propia: ".$this->bpropia."<br>";
        //if ($this->btop === true) {echo "ERROR2"; die();}
        if (empty($id_tipo_activ_txt)) $id_tipo_activ_txt = $this->sid_tipo_activ;
        if ($this->bpropia === true) {
            if (array_key_exists($id_tipo_activ_txt, $this->aPermDl)) {
                return $this->aPermDl[$id_tipo_activ_txt];
            } else {
                return $this->getPermisosPrev($id_tipo_activ_txt);
            }
        } else {
            if (array_key_exists($id_tipo_activ_txt, $this->aPermOtras)) {
                return $this->aPermOtras[$id_tipo_activ_txt];
            } else {
                return $this->getPermisosPrev($id_tipo_activ_txt);
            }
        }
    }

    public function getPermisosPrev($id_tipo_activ_txt = '')
    {
        if (empty($id_tipo_activ_txt)) $id_tipo_activ_txt = $this->sid_tipo_activ;
        if (($prev_id_tipo = $this->getIdTipoPrev($id_tipo_activ_txt)) === false) {
            return false;
        }
        return $this->getPermisos($prev_id_tipo);
    }

    public function setId_tipo_activ($id_tipo_activ)
    {
        if ($id_tipo_activ == '......') {
            $this->btop = true;
        } else {
            $this->btop = false;
        }
        // actualizar el id_tipo_activ
        $this->sid_tipo_activ = $id_tipo_activ;
    }

    public function setId_activ($id_activ)
    {
        // actualiza el id_tipo_activ
        $this->iid_activ = $id_activ;
    }

    public function setId_tipo_proceso($id_tipo_proceso)
    {
        // actualiza el id_tipo_proceso
        $this->iid_tipo_proceso = $id_tipo_proceso;
    }

    public function setPropia($bpropia)
    {
        // actualiza el bpropia
        if (is_true($bpropia)) {
            $this->bpropia = true;
        } else {
            $this->bpropia = false;
        }
    }

    public function getIdTipoPrev($id_tipo_activ_txt = '')
    {
        if (empty($id_tipo_activ_txt)) $id_tipo_activ_txt = $this->sid_tipo_activ;
        $rta = preg_match('/(\d+)(\d)(\.*)/', $id_tipo_activ_txt, $match);
        if (empty($rta)) {
            if ($id_tipo_activ_txt == '1.....' || $id_tipo_activ_txt == '2.....' || $id_tipo_activ_txt == '3.....') {
                return '......';
            } else {
                $this->btop = true; // ja no puc pujar més amunt.
                return false;
            }
        }

        $num_prev = $match[1];
        $num = $match[2];
        $pto = $match[3];

        $prev_id_tipo = $num_prev . "." . $pto;
        //echo "<br>$num, $num_prev, $prev_id_tipo <br>";
        //print_r($this);
        $this->sid_tipo_activ = $prev_id_tipo;
        return $prev_id_tipo;
    }

    /* MÉTODOS PRIVADOS ----------------------------------------------------------*/
    private function getId_tipo_activ()
    {
        // buscar el id_tipo_activ
        return $this->sid_tipo_activ;
    }

    private function getId_tipo_proceso()
    {
        // buscar el id_tipo_proceso
        return $this->iid_tipo_proceso;
    }
}
