<?php

namespace actividades\model\entity;

use cambios\model\GestorAvisoCambios;
use core\ConfigGlobal;
use procesos\model\entity\GestorActividadProcesoTarea;
use ReflectionClass;
use function core\is_true;

/**
 * Clase que implementa la entidad a_actividades_ex
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */
class ActividadEx extends ActividadAll
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
        $oDbl = $GLOBALS['oDBRC'];
        $oDbl_Select = $GLOBALS['oDBRC_Select'];
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
        $this->setNomTabla('a_actividades_ex');
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
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if ($this->DBCarregar('guardar') === false) {
            $bInsert = true;
        } else {
            $bInsert = false;
        }
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
        // Una actividad del esquema resto, debe ser publicada siempre, sino no se puede ver 
        // desde ningún sitio.
        $aDades['publicado'] = 'true';

        if ($bInsert === false) {
            //UPDATE
            $update = "
					id_tipo_activ            = :id_tipo_activ,
					dl_org                   = :dl_org,
					nom_activ                = :nom_activ,
					id_ubi                   = :id_ubi,
					desc_activ               = :desc_activ,
					f_ini                    = :f_ini,
					h_ini                    = :h_ini,
					f_fin                    = :f_fin,
					h_fin                    = :h_fin,
					tipo_horario             = :tipo_horario,
					precio                   = :precio,
					num_asistentes           = :num_asistentes,
					status                   = :status,
					observ                   = :observ,
					nivel_stgr               = :nivel_stgr,
					observ_material          = :observ_material,
					lugar_esp                = :lugar_esp,
					tarifa                   = :tarifa,
					id_repeticion            = :id_repeticion,
					publicado   	         = :publicado,
					plazas   	        	 = :plazas";
            //id_tabla   	        	 = :id_tabla,
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_activ='$this->iid_activ'")) === false) {
                $sClauError = 'ActividadEx.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'ActividadEx.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return FALSE;
                }
            }
            // Aunque no tenga el módulo de 'cambios', quizá otra dl si lo tenga.
            // Anoto el cambio si la actividad está publicada
            if (empty($quiet) && (ConfigGlobal::is_app_installed('cambios') || is_true($aDades['publicado']))) {
                $oGestorCanvis = new GestorAvisoCambios();
                $shortClassName = (new ReflectionClass($this))->getShortName();
                $oGestorCanvis->addCanvi($shortClassName, 'UPDATE', $this->iid_activ, $aDades, $this->aDadesActuals);
            }
            $this->setAllAtributes($aDades);
        } else {
            // INSERT
            //$campos="(id_tipo_activ,dl_org,nom_activ,id_ubi,desc_activ,f_ini,h_ini,f_fin,h_fin,tipo_horario,precio,num_asistentes,status,observ,nivel_stgr,observ_material,lugar_esp,tarifa,id_repeticion,publicado,id_tabla,plazas)";
            $campos = "(id_tipo_activ,dl_org,nom_activ,id_ubi,desc_activ,f_ini,h_ini,f_fin,h_fin,tipo_horario,precio,num_asistentes,status,observ,nivel_stgr,observ_material,lugar_esp,tarifa,id_repeticion,publicado,plazas)";
            //$valores="(:id_tipo_activ,:dl_org,:nom_activ,:id_ubi,:desc_activ,:f_ini,:h_ini,:f_fin,:h_fin,:tipo_horario,:precio,:num_asistentes,:status,:observ,:nivel_stgr,:observ_material,:lugar_esp,:tarifa,:id_repeticion,:publicado,:id_tabla,:plazas)";
            $valores = "(:id_tipo_activ,:dl_org,:nom_activ,:id_ubi,:desc_activ,:f_ini,:h_ini,:f_fin,:h_fin,:tipo_horario,:precio,:num_asistentes,:status,:observ,:nivel_stgr,:observ_material,:lugar_esp,:tarifa,:id_repeticion,:publicado,:plazas)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'ActividadEx.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'ActividadEx.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
            $id_auto = $oDbl->lastInsertId('a_actividades_ex_id_auto_seq');
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_auto=$id_auto")) === false) {
                $sClauError = 'ActividadEx.carregar.Last';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            }
            $aDadesLast = $oDblSt->fetch(\PDO::FETCH_ASSOC);
            $this->aDades = $aDadesLast;
            $this->setAllAtributes($aDadesLast);

            // generar proceso.
            if (ConfigGlobal::is_app_installed('procesos')) {
                $oGestorActividadProcesoTarea = new GestorActividadProcesoTarea();
                $oGestorActividadProcesoTarea->generarProceso($aDadesLast['id_activ']);
            }

            // anotar cambio.
            // Aunque no tenga el módulo de 'cambios', quizá otra dl si lo tenga.
            // Anoto el cambio si la actividad está publicada
            if (empty($quiet) && (ConfigGlobal::is_app_installed('cambios') || is_true($aDades['publicado']))) {
                $oGestorCanvis = new GestorAvisoCambios();
                $shortClassName = (new ReflectionClass($this))->getShortName();
                $oGestorCanvis->addCanvi($shortClassName, 'INSERT', $aDadesLast['id_activ'], $aDadesLast, array());
            }
        }
        return true;
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
                $sClauError = 'ActividadEx.carregar';
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
                        $this->setAllAtributes($aDades);
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
    public function DBEliminar($quiet = 0)
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if ($this->DBCarregar('guardar') === false) {
            // Si no existeix no cal eliminar-el.
            return false;
        } else {
            if (empty($quiet) && (ConfigGlobal::is_app_installed('cambios') || $this->bpublicado === TRUE)) {
                // per carregar les dades a $this->aDadesActuals i poder posar-les als canvis.
                $this->DBCarregar('guardar');
                // ho poso abans d'esborrar perque sino no trova cap valor. En el cas d'error s'hauria d'esborrar l'apunt.
                $oGestorCanvis = new GestorAvisoCambios();
                $shortClassName = (new ReflectionClass($this))->getShortName();
                $oGestorCanvis->addCanvi($shortClassName, 'DELETE', $this->iid_activ, [], $this->aDadesActuals);
            }
            if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_activ='$this->iid_activ'")) === false) {
                $sClauError = 'ActividadEx.eliminar';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            }
            return true;
        }
    }

    /**
     * Canvia el id_activ
     *
     */
    public function DBCambioId($id_new)
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();

        $id_actual = $this->getId_activ();
        if (($oDbl->exec("UPDATE $nom_tabla SET id_activ=$id_new WHERE id_activ=$id_actual")) === false) {
            $sClauError = 'ActividadEx.CambioId';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        return true;
    }
    /* OTROS MÉTODOS  ----------------------------------------------------------*/
    /* MÉTODOS PRIVADOS ----------------------------------------------------------*/
}
