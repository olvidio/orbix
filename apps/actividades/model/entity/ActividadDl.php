<?php

namespace actividades\model\entity;

use cambios\model\GestorAvisoCambios;
use core\ConfigGlobal;
use procesos\model\entity\GestorActividadProcesoTarea;
use ReflectionClass;
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
class ActividadDl extends ActividadAll
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
        $this->setNomTabla('a_actividades_dl');
        $this->setId_tabla('dl');
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
        //para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (is_true($aDades['publicado'])) {
            $aDades['publicado'] = 'true';
        } else {
            $aDades['publicado'] = 'false';
        }

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
					plazas                 	 = :plazas";
            //id_tabla                 = :id_tabla,
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_activ='$this->iid_activ'")) === false) {
                $sClauError = 'ActividadDl.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'ActividadDl.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
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
                $sClauError = 'ActividadDl.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'ActividadDl.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
            $id_auto = $oDbl->lastInsertId('a_actividades_dl_id_auto_seq');
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_auto=$id_auto")) === false) {
                $sClauError = 'ActividadDl.carregar.Last';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            }
            $aDadesLast = $oDblSt->fetch(\PDO::FETCH_ASSOC);
            $this->aDades = $aDadesLast;
            $this->setAllAtributes($aDadesLast);

            // generar proceso.
            if (ConfigGlobal::is_app_installed('procesos') && $this->bNoGenerarProceso === FALSE) {
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
                $sClauError = 'ActividadDl.carregar';
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
            // Aunque no tenga el módulo de 'cambios', quizá otra dl si lo tenga.
            // Anoto el cambio si la actividad está publicada
            if (empty($quiet) && (ConfigGlobal::is_app_installed('cambios') || $this->bpublicado === TRUE)) {
                // per carregar les dades a $this->aDadesActuals i poder posar-les als canvis.
                $this->DBCarregar('guardar');
                // ho poso abans d'esborrar perque sino no trova cap valor. En el cas d'error s'hauria d'esborrar l'apunt.
                $oGestorCanvis = new GestorAvisoCambios();
                $shortClassName = (new ReflectionClass($this))->getShortName();
                $oGestorCanvis->addCanvi($shortClassName, 'DELETE', $this->iid_activ, [], $this->aDadesActuals);
            }
            if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_activ='$this->iid_activ'")) === false) {
                $sClauError = 'ActividadDl.eliminar';
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
            $sClauError = 'ActividadDl.CambioId';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        return true;
    }

    /* OTROS MÉTODOS  ----------------------------------------------------------*/
    /* MÉTODOS PRIVADOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDades
     */
    function setAllAtributes(array $aDades, $convert = FALSE)
    {
        if (!is_array($aDades)) return;
        if (array_key_exists('id_schema', $aDades)) $this->setId_schema($aDades['id_schema']);
        if (array_key_exists('id_activ', $aDades)) $this->setId_activ($aDades['id_activ']);
        if (array_key_exists('id_tipo_activ', $aDades)) $this->setId_tipo_activ($aDades['id_tipo_activ']);
        if (array_key_exists('dl_org', $aDades)) $this->setDl_org($aDades['dl_org']);
        if (array_key_exists('nom_activ', $aDades)) $this->setNom_activ($aDades['nom_activ']);
        if (array_key_exists('id_ubi', $aDades)) $this->setId_ubi($aDades['id_ubi']);
        if (array_key_exists('desc_activ', $aDades)) $this->setDesc_activ($aDades['desc_activ']);
        if (array_key_exists('f_ini', $aDades)) $this->setF_ini($aDades['f_ini'], $convert);
        if (array_key_exists('h_ini', $aDades)) $this->setH_ini($aDades['h_ini']);
        if (array_key_exists('f_fin', $aDades)) $this->setF_fin($aDades['f_fin'], $convert);
        if (array_key_exists('h_fin', $aDades)) $this->setH_fin($aDades['h_fin']);
        if (array_key_exists('tipo_horario', $aDades)) $this->setTipo_horario($aDades['tipo_horario']);
        if (array_key_exists('precio', $aDades)) $this->setPrecio($aDades['precio']);
        if (array_key_exists('num_asistentes', $aDades)) $this->setNum_asistentes($aDades['num_asistentes']);
        if (array_key_exists('status', $aDades)) $this->setStatus($aDades['status']);
        if (array_key_exists('observ', $aDades)) $this->setObserv($aDades['observ']);
        if (array_key_exists('nivel_stgr', $aDades)) $this->setNivel_stgr($aDades['nivel_stgr']);
        if (array_key_exists('observ_material', $aDades)) $this->setObserv_material($aDades['observ_material']);
        if (array_key_exists('lugar_esp', $aDades)) $this->setLugar_esp($aDades['lugar_esp']);
        if (array_key_exists('tarifa', $aDades)) $this->setTarifa($aDades['tarifa']);
        if (array_key_exists('id_repeticion', $aDades)) $this->setId_repeticion($aDades['id_repeticion']);
        if (array_key_exists('publicado', $aDades)) $this->setPublicado($aDades['publicado']);
        if (array_key_exists('id_tabla', $aDades)) $this->setId_tabla($aDades['id_tabla']);
        if (array_key_exists('plazas', $aDades)) $this->setPlazas($aDades['plazas']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_schema('');
        $this->setId_activ('');
        $this->setId_tipo_activ('');
        $this->setDl_org('');
        $this->setNom_activ('');
        $this->setId_ubi('');
        $this->setDesc_activ('');
        $this->setF_ini('');
        $this->setH_ini('');
        $this->setF_fin('');
        $this->setH_fin('');
        $this->setTipo_horario('');
        $this->setPrecio('');
        $this->setNum_asistentes('');
        $this->setStatus('');
        $this->setObserv('');
        $this->setNivel_stgr('');
        $this->setObserv_material('');
        $this->setLugar_esp('');
        $this->setTarifa('');
        $this->setId_repeticion('');
        $this->setPublicado('');
        $this->setId_tabla('');
        $this->setPlazas('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/
}
