<?php
namespace asistentes\model\entity;

use cambios\model\GestorAvisoCambios;
use core;
use personas\model\entity as personas;
use profesores\model\entity as profesores;

/**
 * Fitxer amb la Classe que accedeix a la taula d_asistentes_out
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */

/**
 * Clase que implementa la entidad d_asistentes_out
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */
class AsistenteOut extends AsistentePub
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * btraslado de AsistenteOut
     *
     * @var boolean
     */
    protected $btraslado = 'f';


    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     * Si només necessita un valor, se li pot passar un integer.
     * En general se li passa un array amb les claus primàries.
     *
     * @param integer|array iid_activ,iid_nom
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBE'];
        $oDbl_Select = $GLOBALS['oDBE_Select'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'id_activ') && $val_id !== '') $this->iid_activ = (int)$val_id;
                if (($nom_id == 'id_nom') && $val_id !== '') $this->iid_nom = (int)$val_id;
            }
        }
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('d_asistentes_out');
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
        $aDades = array();
        $aDades['propio'] = $this->bpropio;
        $aDades['est_ok'] = $this->best_ok;
        $aDades['cfi'] = $this->bcfi;
        $aDades['cfi_con'] = $this->icfi_con;
        $aDades['falta'] = $this->bfalta;
        $aDades['encargo'] = $this->sencargo;
        $aDades['dl_responsable'] = $this->sdl_responsable;
        $aDades['observ'] = $this->sobserv;
        $aDades['observ_est'] = $this->sobserv_est;
        $aDades['plaza'] = $this->iplaza;
        $aDades['propietario'] = $this->spropietario;
        array_walk($aDades, 'core\poner_null');
        //para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (core\is_true($aDades['propio'])) {
            $aDades['propio'] = 'true';
        } else {
            $aDades['propio'] = 'false';
        }
        if (core\is_true($aDades['est_ok'])) {
            $aDades['est_ok'] = 'true';
        } else {
            $aDades['est_ok'] = 'false';
        }
        if (core\is_true($aDades['cfi'])) {
            $aDades['cfi'] = 'true';
        } else {
            $aDades['cfi'] = 'false';
        }
        if (core\is_true($aDades['falta'])) {
            $aDades['falta'] = 'true';
        } else {
            $aDades['falta'] = 'false';
        }

        if ($bInsert === false) {
            //UPDATE
            $update = "
					propio                   = :propio,
					est_ok                   = :est_ok,
					cfi                      = :cfi,
					cfi_con                  = :cfi_con,
					falta                    = :falta,
					encargo                  = :encargo,
					dl_responsable                     = :dl_responsable,
					observ                   = :observ,
					observ_est               = :observ_est,
					plaza                    = :plaza,
					propietario              = :propietario";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_activ='$this->iid_activ' AND id_nom=$this->iid_nom")) === false) {
                $sClauError = get_class($this) . '.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = get_class($this) . '.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
            // Aunque no tenga el módulo de 'cambios', quizá otra dl si lo tenga.
            // Anoto el cambio.
            if (empty($quiet)) {
                $oGestorCanvis = new GestorAvisoCambios();
                $shortClassName = (new \ReflectionClass($this))->getShortName();
                $oGestorCanvis->addCanvi($shortClassName, 'UPDATE', $this->iid_activ, $aDades, $this->aDadesActuals);
            }
            $this->setAllAtributes($aDades);
        } else {
            // INSERT
            array_unshift($aDades, $this->iid_activ, $this->iid_nom);
            $campos = "(id_activ,id_nom,propio,est_ok,cfi,cfi_con,falta,encargo,dl_responsable,observ,observ_est,plaza,propietario)";
            $valores = "(:id_activ,:id_nom,:propio,:est_ok,:cfi,:cfi_con,:falta,:encargo,:dl_responsable,:observ,:observ_est,:plaza,:propietario)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = get_class($this) . '.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = get_class($this) . '.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_activ='$this->iid_activ' AND id_nom=$this->iid_nom")) === false) {
                $sClauError = get_class($this) . '.carregar.Last';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            }
            $aDadesLast = $oDblSt->fetch(\PDO::FETCH_ASSOC);
            $this->aDades = $aDadesLast;
            $this->setAllAtributes($aDadesLast);
            // anotar cambio.
            // Aunque no tenga el módulo de 'cambios', quizá otra dl si lo tenga.
            // Anoto el cambio.
            if (empty($quiet)) {
                $oGestorCanvis = new GestorAvisoCambios();
                $shortClassName = (new \ReflectionClass($this))->getShortName();
                $oGestorCanvis->addCanvi($shortClassName, 'INSERT', $aDadesLast['id_activ'], $this->aDades, array());
            }
            // Hay que copiar los datos del asistente a PersonaOut
            // Excepto en el caso de estar copiando dossiers por traslado
            if ($this->btraslado == 'f') {
                $oPersona = personas\Persona::NewPersona($this->iid_nom);
                if (!is_object($oPersona)) {
                    $msg_err = "<br>$oPersona con id_nom: $id_nom en  " . __FILE__ . ": line " . __LINE__;
                    exit($msg_err);
                }
                $oPersona->DBCarregar();
                // No para los de paso:
                $nom_tablaP = $oPersona->getNomTabla();
                if ($nom_tablaP != 'p_de_paso_out') {
                    $oPersonaOut = new personas\PersonaOut($this->iid_nom);
                    $oPersonaOut->import($oPersona);
                    $oPersonaOut->DBGuardar();
                    // miro si es profesor
                    $cProfesores = array();
                    $gesProfesores = new profesores\GestorProfesor();
                    $cProfesores = $gesProfesores->getProfesores(array('id_nom' => $this->iid_nom, 'f_cese' => ''), array('f_cese' => 'IS NULL'));
                    if (count($cProfesores) > 0) {
                        $oPersonaOut->setProfesor_stgr('t');
                        $oPersonaOut->DBGuardar();
                    }
                }
            }
        }
        return true;
    }

    /* MÉTODOS GET y SET --------------------------------------------------------*/
    /**
     * Recupera el atributo btraslado de AsistenteOut
     *
     * @return boolean btraslado
     */
    function getTraslado()
    {
        return $this->btraslado;
    }

    /**
     * Establece el valor del atributo btraslado de AsistenteOut
     *
     * @param boolean btraslado='f' optional
     */
    function setTraslado($btraslado = 'f')
    {
        $this->btraslado = $btraslado;
    }

    /* OTROS MÉTODOS  ----------------------------------------------------------*/
    /* MÉTODOS PRIVADOS ----------------------------------------------------------*/
}
