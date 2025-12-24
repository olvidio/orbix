<?php

namespace actividades\legacy;

use actividades\model\entity\ActividadAll;
use actividades\model\entity\ActividadDl;
use actividades\model\entity\Importada;
use core\ConfigGlobal;
use function core\is_true;

/**
 * Clase que implementa la entidad a_actividades_dl
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */
class Actividad extends ActividadAll
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     * Si només necessita un valor, se li pot passar un integer.
     * En general se li passa un array amb les claus primàries.
     *
     * @param integer|array iid_activ
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBC'];
        $oDbl_Select = $GLOBALS['oDBC_Select'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                $nom_id = 'i' . $nom_id; //imagino que es un integer
                if ($val_id !== '') $this->$nom_id = (integer)$val_id; 
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->iid_activ = (integer)$a_id; 
                $this->aPrimary_key = array('id_activ' => $this->iid_activ);
            }
        }
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('av_actividades');
    }

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Guarda los atributos de la clase en la base de datos.
     * Si no existe el registro, hace el insert; Si existe hace el update.
     *
     * @param bool optional $quiet : true per que no apunti els canvis. 0 (per defecte) apunta els canvis.
     */
    public function DBGuardar($quiet = 0)
    {
        $aDades = [];
        $aDades['id_tipo_activ'] = $this->iid_tipo_activ;
        $aDades['dl_org'] = $this->sdl_org;
        $aDades['nom_activ'] = $this->snom_activ;
        $aDades['id_ubi'] = $this->iid_ubi;
        $aDades['desc_activ'] = $this->sdesc_activ;
        $aDades['f_ini'] = $this->df_ini;
        $aDades['h_ini'] = $this->th_ini;
        $aDades['f_fin'] = $this->df_fin;
        $aDades['h_fin'] = $this->th_fin;
        $aDades['tipo_horario'] = $this->itipo_horario;
        $aDades['precio'] = $this->iprecio;
        $aDades['num_asistentes'] = $this->inum_asistentes;
        $aDades['status'] = $this->istatus;
        $aDades['observ'] = $this->sobserv;
        $aDades['nivel_stgr'] = $this->inivel_stgr;
        $aDades['observ_material'] = $this->sobserv_material;
        $aDades['lugar_esp'] = $this->slugar_esp;
        $aDades['tarifa'] = $this->itarifa;
        $aDades['id_repeticion'] = $this->iid_repeticion;
        $aDades['publicado'] = $this->bpublicado;
        //$aDades['id_tabla'] = $this->sid_tabla;
        $aDades['plazas'] = $this->iplazas;
        array_walk($aDades, 'core\poner_null');
        //para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (is_true($aDades['publicado'])) {
            $aDades['publicado'] = 'true';
        } else {
            $aDades['publicado'] = 'false';
        }

        $a_pkey = $this->aPrimary_key;
        $dl_org = $aDades['dl_org'];
        $id_tabla = $this->sid_tabla;
        if ($dl_org == ConfigGlobal::mi_delef()) {
            $oActividad = new ActividadDl($a_pkey);
        } else {
            if ($id_tabla === 'dl') {
                // caso especial dre puede cambiar las actividades de sf:
                $dl_org_no_f = preg_replace('/(\.*)f$/', '\1', $dl_org);
                if ($dl_org_no_f == ConfigGlobal::mi_delef() && $_SESSION['oPerm']->have_perm_oficina('des')) {
                    $oActividad = new ActividadDl($a_pkey);
                } else {
                    // No se puede modificar una actividad de otra dl
                    $msg = sprintf(_("no se puede modificar una actividad de otra dl: %s"), $dl_org);
                    echo $msg;
                    return false;
                }
            } else {
                $oActividad = new ActividadEx($a_pkey);
            }
        }
        $oActividad->setAllAttributes($aDades);
        if ($oActividad->DBGuardar() === FALSE) {
            $this->setErrorTxt($oActividad->getErrorTxt());
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * Carga los campos de la base de datos como atributos de la clase.
     *
     */
    public function DBCarregar($que = null)
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        if (isset($this->iid_activ)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_activ='$this->iid_activ'")) === false) {
                $sClauError = 'Actividad.carregar';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            }
            $aDades = $oDblSt->fetch(\PDO::FETCH_ASSOC);
            // Para evitar posteriores cargas
            $this->bLoaded = TRUE;
            switch ($que) {
                case 'tot':
                    $this->aDades = $aDades;
                    break;
                case 'guardar':
                    if (!$oDblSt->rowCount()) return false;
                    // Hay que guardar los boolean de la misma manera que al guardar los datos ('false','true'):
                    if (is_true($aDades['publicado'])) {
                        $aDades['publicado'] = 'true';
                    } else {
                        $aDades['publicado'] = 'false';
                    }
                    $this->aDadesActuals = $aDades;
                    break;
                default:
                    // En el caso de no existir esta fila, $aDades = FALSE:
                    if ($aDades === FALSE) {
                        $this->setNullAllAtributes();
                    } else {
                        $this->setAllAttributes($aDades);
                    }
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Elimina la fila de la base de datos que corresponde a la clase.
     *
     */
    public function DBEliminar()
    {
        $a_pkey = $this->aPrimary_key;
        $dl_org = $this->sdl_org;
        $id_tabla = $this->sid_tabla;

        // para des => dl y dlf:
        $dl_org_no_f = preg_replace('/(\.*)f$/', '\1', $dl_org);
        $dl_propia = (ConfigGlobal::mi_dele() == $dl_org_no_f) ? TRUE : FALSE;

        if ($dl_propia) {
            $oActividadAll = new ActividadDl($a_pkey);
        } else {
            if ($id_tabla === 'dl') {
                // No se puede eliminar una actividad de otra dl. Hay que borrarla como importada
                $oImportada = new Importada($a_pkey);
                $oImportada->DBEliminar();
                return true;
            } else {
                $oActividadAll = new ActividadEx($a_pkey);
            }
        }
        $oActividadAll->DBEliminar();
        return true;
    }

    /* OTROS MÉTODOS  ----------------------------------------------------------*/
    /* MÉTODOS PRIVADOS ----------------------------------------------------------*/
}