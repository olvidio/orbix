<?php

namespace permisos\model;

use actividades\model\entity\Actividad;
use actividades\model\entity\GestorTipoDeActividad;
use core\ConfigGlobal;
use procesos\model\entity\ActividadFase;
use procesos\model\entity\GestorActividadProcesoTarea;
use procesos\model\entity\GestorTareaProceso;
use procesos\model\PermAccion;
use usuarios\model\entity\GestorUsuarioGrupo;
use function core\is_true;

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
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 20/11/2010
 */
class PermisosActividades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */
    /**
     * Perm de PermisoActividad
     *
     * @var array
     */
    public const AFECTA = ['datos' => 1,
        'economic' => 2,
        'sacd' => 4,
        'ctr' => 8,
        'id_tarifa' => 16,
        'cargos' => 32,
        'asistentes' => 64,
        'asistentesSacd' => 128,
    ];
    /**
     * Array amb els permisos.
     *
     * @var array
     */
    private $aPermDl = array();
    private $aPermOtras = array();
    /**
     * Per saber a quina activitat fa referència.
     *
     * @var string
     */
    private string $sid_tipo_activ;

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

    /**
     * fases de la actividad completadas.
     *
     * @var array
     */
    private $aFasesCompletadas = [];

    /* METODES ----------------------------------------------------------------- */
    public function __construct($iid_usuario)
    {
        // permiso para el usuario
        $sCondicion_usuario = "u.id_usuario=$iid_usuario";
        // miro en els grups als que pertany
        $oGesGrupos = new GestorUsuarioGrupo();
        $oGrupos = $oGesGrupos->getUsuariosGrupos(array('id_usuario' => $iid_usuario));
        if (count($oGrupos) > 0) {
            foreach ($oGrupos as $oUsuarioGrupo) {
                $id = $oUsuarioGrupo->getId_grupo();
                $sCondicion_usuario .= " OR u.id_usuario=$id";
            }
            $sCondicion_usuario = "($sCondicion_usuario)";
        }
        // carrego dues vegades, per la dl_propia i la resta.
        $this->carregar($sCondicion_usuario, 't');
        $this->carregar($sCondicion_usuario, 'f');

    }

    private function carregar($sCondicion_usuario, $dl_propia)
    {
        $oDbl = $GLOBALS['oDBE'];
        // Orden: los usuarios empiezan por 4, los grupos por 5.
        // Al ordenar, el usuario (queda el último) sobreescribe al grupo.
        // Los grupos, como puede haber más de uno los ordeno por orden alfabético DESC (prioridad A-Z).
        $Qry = "SELECT DISTINCT p.*, SUBSTRING( p.id_usuario::text, 1, 1 ) as orden, u.usuario
			FROM aux_usuarios_perm p JOIN aux_grupos_y_usuarios u USING (id_usuario)
			WHERE $sCondicion_usuario AND dl_propia='$dl_propia' 
			ORDER BY orden DESC, usuario DESC
			";
        //echo "<br>permActiv: $Qry<br>";
        if (($oDbl->query($Qry)) === false) {
            $sClauError = 'PermisosActividades.carregar';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        // per cada fila genero els permisos
        $f = 0;
        foreach ($oDbl->query($Qry) as $row) {
            $f++;
            $id_tipo_activ_txt = $row['id_tipo_activ_txt'];
            $fase_ref = $row['fase_ref'];
            $iAfecta = $row['afecta_a'];
            $perm_on = $row['perm_on'];
            $perm_off = $row['perm_off'];

            if (is_true($dl_propia)) {
                if (array_key_exists($id_tipo_activ_txt, $this->aPermDl)) {
                    // machaco los valores existentes. Si he ordenado por id usuario (DESC), el último és el más importante.
                } else { //nuevo
                    $this->aPermDl[$id_tipo_activ_txt] = new XResto($id_tipo_activ_txt);
                }
            } else {
                if (array_key_exists($id_tipo_activ_txt, $this->aPermOtras)) {
                    // machaco los valores existentes. Si he ordenado por id usuario (DESC), el último és el más importante.
                } else { //nuevo
                    $this->aPermOtras[$id_tipo_activ_txt] = new XResto($id_tipo_activ_txt);
                }
            }
            if (is_true($dl_propia)) {
                $this->aPermDl[$id_tipo_activ_txt]->setOmplir($iAfecta, $fase_ref, $perm_on, $perm_off);
            } else {
                $this->aPermOtras[$id_tipo_activ_txt]->setOmplir($iAfecta, $fase_ref, $perm_on, $perm_off);
            }

            if (!empty($id_tipo_activ_txt)) {
                if (!empty($this->aPermDl[$id_tipo_activ_txt])) {
                    $this->aPermDl[$id_tipo_activ_txt]->setOrdenar();
                }
                if (!empty($this->aPermOtras[$id_tipo_activ_txt])) {
                    $this->aPermOtras[$id_tipo_activ_txt]->setOrdenar();
                }
            }
        }
    }

    /**
     * fija las propiedades de dl_propia y id_tipo_activ.
     *
     * @param integer $id_activ
     */
    public function setActividad(int $id_activ): void
    {
        $this->btop = false;
        $this->iid_activ = $id_activ;

        $oActividad = new Actividad($id_activ);
        $id_tipo_activ = $oActividad->getId_tipo_activ();
        $dl_org = $oActividad->getDl_org();
        $dl_org_no_f = preg_replace('/(\.*)f$/', '\1', $dl_org);

        $this->sid_tipo_activ = (string) $id_tipo_activ;

        if ($dl_org === ConfigGlobal::mi_delef() || $dl_org_no_f === ConfigGlobal::mi_dele()) {
            $this->bpropia = true;
        } else {
            $this->bpropia = false;
        }
    }

    public function setId_fase($iid_fase)
    {
        $this->iid_fase = $iid_fase;
    }

    private function isCompletada($id_fase)
    {
        if (empty($id_fase)) {
            exit (_("Hay que indicar para que fase"));
        }
        // para cuando se mira la actividad en un estado anterior, se cargan las
        // fases completadas con la funcion setFasesCompletadas($aFases) en la variable
        // $this->aFasesCompletadas
        if (!empty($this->aFasesCompletadas)) {
            if (in_array($id_fase, $this->aFasesCompletadas)) {
                return TRUE;
            } else {
                return FALSE;
            }
        }
        $oGesActiv = new GestorActividadProcesoTarea();
        $completada = $oGesActiv->faseCompletada($this->iid_activ, $id_fase);
        return $completada;
    }

    public function setFasesCompletadas($aFases = [])
    {
        $this->aFasesCompletadas = $aFases;
    }

    /**
     * Para saber si puedo crear una actividad del tipo
     * para dl, ex
     *
     * @param bool $dl_propia dl organizadora
     * @return array|false
     */
    public function getPermisoCrear(bool $dl_propia)
    {
        $this->bpropia = $dl_propia;
        $id_tipo_activ = $this->sid_tipo_activ;
        // si vengo de una búsqueda, el id_tipo_actividad puede ser con '...'
        // pongo el tipo básico (sin specificar)
        //$id_tipo_activ = str_replace('.', '0', $id_tipo_activ);
        $GesTiposActiv = new GestorTipoDeActividad();
        $aTiposDeProcesos = $GesTiposActiv->getTiposDeProcesos($id_tipo_activ, $dl_propia);

        if (empty($aTiposDeProcesos)) {
            echo _("debería crear un proceso para este tipo de actividad");
            return FALSE;
        }
        // Cojo el primero
        $oPerm = FALSE;
        foreach ($aTiposDeProcesos as $id_tipo_proceso) {
            // Buscar la primera fase (no depende de fases previas)
            $GesTareaProceso = new GestorTareaProceso();
            $oTareaProceso = $GesTareaProceso->getFaseIndependiente($id_tipo_proceso);
            $of_responsable_txt = $oTareaProceso->getOf_responsable_txt();
            $status = $oTareaProceso->getStatus();

            // devolver false si no puedo crear
            $iAfecta = 1; //datos
            $id_fase_ref = ActividadFase::FASE_APROBADA;
            $on_off = 'off';

            if (($oP = $this->getPermisos($iAfecta)) === false) {
                return FALSE;
            } else {
                $iperm = $oP->getPerm($iAfecta, $id_fase_ref, $on_off);
                if ($iperm !== false) {
                    $oPerm = new PermAccion($iperm);
                    break;
                }
            }
        }

        if ($oPerm !== FALSE && $oPerm->have_perm_activ('crear')) {
            return ['of_responsable_txt' => $of_responsable_txt,
                'status' => $status,
            ];
        } else {
            return FALSE;
        }
    }

    /**
     * Devuelve el oPersonaNota PermAction para $sAfecta
     * Para la actividad $this->iidactiv y en la fase $this->id_fase
     *
     * @param string $sAfecta
     * @return PermAccion
     */
    public function getPermisoActual(string $sAfecta)
    {
        // hay que poner a cero el id_tipo_activ, sino
        // aprovecha el que se ha buscado con el anterior iAfecta.
        if (!empty($this->iid_activ)) {
            $this->setActividad($this->iid_activ);
        }
        // para poder pasar el valor de afecta con texto:
        $iAfecta = self::AFECTA[$sAfecta];

        // buscar fase_ref para iAfecta
        $id_fase_ref = $this->getFaseRef($iAfecta);
        if ($this->btop === TRUE) {
            return new PermAccion(0);
        }
        // buscar estado de la fase ref
        $completada = $this->isCompletada($id_fase_ref);
        if (is_true($completada)) {
            $on_off = 'on';
        } else {
            $on_off = 'off';
        }

        if ($this->bpropia === true) {
            $oPerm = $this->aPermDl[$this->sid_tipo_activ];
        } else {
            $oPerm = $this->aPermOtras[$this->sid_tipo_activ];
        }
        $perm = $oPerm->getPerm($iAfecta, $id_fase_ref, $on_off);
        if ($perm === FALSE) {
            return new PermAccion(0);
        } else {
            return new PermAccion($perm);
        }
    }

    /**
     * Devuelve el oPersonaNota PermAction para $iAfecta
     * Para la actividad $this->iidactiv
     * que esté con la $this->id_fase en 'on'.
     *
     * @param integer|string $iAfecta
     * @return PermAccion
     */
    public function getPermisoOn(int|string $iAfecta)
    {
        // hay que poner a cero el id_tipo_activ, sino
        // aprovecha el que se ha buscado con el anterior iAfecta.
        if (!empty($this->iid_activ)) {
            $this->setActividad($this->iid_activ);
        }
        // para poder pasar el valor de afecta con texto:
        if (is_string($iAfecta)) {
            $iAfecta = self::AFECTA[$iAfecta];
        }

        // buscar fase_ref para iAfecta
        $id_fase_ref = $this->getFaseRef($iAfecta);
        if ($this->btop === TRUE) {
            return new PermAccion(0);
        }
        // buscar estado de la fase ref
        $completada = $this->isCompletada($id_fase_ref);
        if (!is_true($completada)) {
            return new PermAccion(0);
        }

        if ($this->bpropia === true) {
            $oPerm = $this->aPermDl[$this->sid_tipo_activ];
        } else {
            $oPerm = $this->aPermOtras[$this->sid_tipo_activ];
        }

        $on_off = 'on';
        $perm = $oPerm->getPerm($iAfecta, $id_fase_ref, $on_off);

        if ($perm === FALSE) {
            return new PermAccion(0);
        } else {
            return new PermAccion($perm);
        }
    }

    private function getFaseRef($iAfecta, $id_tipo_activ_txt = '')
    {
        if (empty($id_tipo_activ_txt)) $id_tipo_activ_txt = $this->sid_tipo_activ;
        $id_tipo_activ_txt = $this->completarId($id_tipo_activ_txt);
        if ($this->bpropia === true) {
            if (array_key_exists($id_tipo_activ_txt, $this->aPermDl)) {
                $PermIdTipo = $this->aPermDl[$id_tipo_activ_txt];
                // a ver si existe el iAfecta para este id_tipo_activ:
                if ($PermIdTipo->hasAfecta($iAfecta)) {
                    return $PermIdTipo->getFaseRef($iAfecta);
                } else {
                    return $this->getFaseRefPrev($iAfecta, $id_tipo_activ_txt);
                }
            } else {
                return $this->getFaseRefPrev($iAfecta, $id_tipo_activ_txt);
            }
        } else {
            if (array_key_exists($id_tipo_activ_txt, $this->aPermOtras)) {
                $PermIdTipo = $this->aPermOtras[$id_tipo_activ_txt];
                // a ver si existe el iAfecta para este id_tipo_activ:
                if ($PermIdTipo->hasAfecta($iAfecta)) {
                    return $PermIdTipo->getFaseRef($iAfecta);
                } else {
                    return $this->getFaseRefPrev($iAfecta, $id_tipo_activ_txt);
                }
            } else {
                return $this->getFaseRefPrev($iAfecta, $id_tipo_activ_txt);
            }
        }
    }

    private function getFaseRefPrev($iAfecta, $id_tipo_activ_txt = '')
    {
        if (empty($id_tipo_activ_txt)) $id_tipo_activ_txt = $this->sid_tipo_activ;
        if (($prev_id_tipo = $this->getIdTipoPrev($id_tipo_activ_txt)) === false) {
            return false;
        }
        return $this->getFaseRef($iAfecta, $prev_id_tipo);
    }

    /**
     * para saber si un sacd puede ver una actividad, según sea el encargado, o asistente
     * o los dos.
     * los parámetros provienen de la consulta:
     * $cAsistentes = $oGesActividadCargo ->getAsistenteCargoDeActividad();
     *
     * @param ?integer $id_cargo
     * @param boolean $propio
     * @return boolean
     */
    public function havePermisoSacd(?int $id_cargo, bool $propio): bool
    {
        $permiso_ver = FALSE;
        $oPermActiv = $this->getPermisoActual('datos');
        // sólo si la fase de 'ok sacd' está completada:
        $oPermSacd = $this->getPermisoOn('sacd');
        // sólo si la fase de 'ok asist. sacd' está completada:
        $oPermAsisSacd = $this->getPermisoOn('asistentesSacd');
        // para ver la actividad:
        if ($oPermActiv->have_perm_activ('ver') === FALSE) {
            return FALSE;
            // No hace falta seguir mirando.
        }

        // si es solo cargo, tiene propio='f' como sacd de la actividad
        if (!empty($id_cargo)) {
            if ($oPermSacd->have_perm_activ('ver') === TRUE) {
                $permiso_ver = TRUE;
            }
            //si también asiste. tiene propio = 't'
            if (is_true($propio) && $oPermAsisSacd->have_perm_activ('ver') === TRUE) {
                $permiso_ver = TRUE;
            }
        } else {
            // sólo asiste
            if ($oPermAsisSacd->have_perm_activ('ver') === TRUE) {
                $permiso_ver = TRUE;
            }
        }
        return $permiso_ver;
    }

    public function getPermisos($iAfecta, $id_tipo_activ_txt = '')
    {
        if (empty($id_tipo_activ_txt)) $id_tipo_activ_txt = $this->sid_tipo_activ;
        $id_tipo_activ_txt = $this->completarId($id_tipo_activ_txt);
        if ($this->bpropia === true) {
            if (array_key_exists($id_tipo_activ_txt, $this->aPermDl)) {
                $PermIdTipo = $this->aPermDl[$id_tipo_activ_txt];
                // a ver si existe el iAfecta para este id_tipo_activ:
                if ($PermIdTipo->hasAfecta($iAfecta)) {
                    return $this->aPermDl[$id_tipo_activ_txt];
                } else {
                    return $this->getPermisosPrev($iAfecta, $id_tipo_activ_txt);
                }
            } else {
                return $this->getPermisosPrev($iAfecta, $id_tipo_activ_txt);
            }
        } else {
            if (array_key_exists($id_tipo_activ_txt, $this->aPermOtras)) {
                return $this->aPermOtras[$id_tipo_activ_txt];
            } else {
                return $this->getPermisosPrev($iAfecta, $id_tipo_activ_txt);
            }
        }
    }

    public function getPermisosPrev($iAfecta, $id_tipo_activ_txt = '')
    {
        if (empty($id_tipo_activ_txt)) $id_tipo_activ_txt = $this->sid_tipo_activ;
        if (($prev_id_tipo = $this->getIdTipoPrev($id_tipo_activ_txt)) === false) {
            return false;
        }
        return $this->getPermisos($iAfecta, $prev_id_tipo);
    }

    public function getAfecta()
    {
        return self::AFECTA;
    }

    public function setId_tipo_activ($id_tipo_activ_txt)
    {
        if ($id_tipo_activ_txt === '......') {
            $this->btop = true;
        } else {
            $this->btop = false;
        }
        // actualizar el id_tipo_activ
        $this->sid_tipo_activ = $id_tipo_activ_txt;
    }

    public function setId_activ($id_activ)
    {
        // actualitza el id_tipo_activ
        $this->iid_activ = $id_activ;
    }

    public function setId_tipo_proceso($id_tipo_proceso)
    {
        // actualitza el id_tipo_proceso
        $this->iid_tipo_proceso = $id_tipo_proceso;
    }

    public function setPropia($bpropia)
    {
        // actualitza el bpropia
        if (is_true($bpropia)) {
            $this->bpropia = true;
        } else {
            $this->bpropia = false;
        }
    }

    /* MÉTODOS PRIVADOS ----------------------------------------------------------*/

    private function getIdTipoPrev($id_tipo_activ_txt = '')
    {
        if (empty($id_tipo_activ_txt)) $id_tipo_activ_txt = $this->sid_tipo_activ;
        $match = [];
        $rta = preg_match('/(\d+)(\d)(\.*)/', $id_tipo_activ_txt, $match);
        if (empty($rta)) {
            if ($id_tipo_activ_txt === '1.....' || $id_tipo_activ_txt === '2.....' || $id_tipo_activ_txt === '3.....') {
                $this->btop = true; // ja no puc pujar més amunt.
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

    private function completarId($id_tipo_activ_txt)
    {
        $len = strlen($id_tipo_activ_txt);
        if ($len < 6) {
            $relleno = 6 - $len;
            for ($i = 0; $i < $relleno; $i++) {
                $id_tipo_activ_txt .= '.';
            }
        }
        return $id_tipo_activ_txt;
    }
}
