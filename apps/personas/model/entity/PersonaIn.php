<?php

namespace personas\model\entity;
use core\ConfigGlobal;
use core\DatosCampo;
use core\Set;
use function core\is_true;

/**
 * Fitxer amb la Classe que accedeix a la taula Hdlb(v|f).pv_de_paso_in
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */

/**
 * Se distingue de PersonaPub porque hay que añadir el id_schema a la
 * hora de seleccionar etc. La misma persona puede estar en dos esquemas
 * distintos y hay que saber a cúal se refiere.
 *
 */
class PersonaIn extends PersonaPub
{

    /**
     * Constructor de la classe.
     * Si només necessita un valor, se li pot passar un integer.
     * En general se li passa un array amb les claus primàries.
     *
     * @param integer|array iid_nom
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBP'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_nom') && $val_id !== '') $this->iid_nom = (int)$val_id;
                if (($nom_id === 'id_schema') && $val_id !== '') $this->iid_schema = (int)$val_id;
            }
            $this->aPrimary_key = array('id_nom' => $this->iid_nom, 'id_schema' => $this->iid_schema);
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->iid_nom = (integer)$a_id;
                $this->aPrimary_key = array('id_nom' => $this->iid_nom, 'id_schema' => $this->iid_schema);
            }
        }
        $this->setoDbl($oDbl);
        $this->setNomTabla('p_de_paso');
    }

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Guarda los atributos de la clase en la base de datos.
     * Si no existe el registro, hace el insert; Si existe hace el update.
     *
     */
    public function DBGuardar()
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if ($this->DBCarregar('guardar') === false) {
            $bInsert = true;
        } else {
            $bInsert = false;
        }
        $aDades = [];
        $aDades['id_cr'] = $this->iid_cr;
        $aDades['id_tabla'] = $this->sid_tabla;
        $aDades['dl'] = $this->sdl;
        $aDades['sacd'] = $this->bsacd;
        $aDades['trato'] = $this->strato;
        $aDades['nom'] = $this->snom;
        $aDades['nx1'] = $this->snx1;
        $aDades['apellido1'] = $this->sapellido1;
        $aDades['nx2'] = $this->snx2;
        $aDades['apellido2'] = $this->sapellido2;
        $aDades['f_nacimiento'] = $this->df_nacimiento;
        $aDades['lengua'] = $this->slengua;
        $aDades['situacion'] = $this->ssituacion;
        $aDades['f_situacion'] = $this->df_situacion;
        $aDades['apel_fam'] = $this->sapel_fam;
        $aDades['inc'] = $this->sinc;
        $aDades['f_inc'] = $this->df_inc;
        $aDades['stgr'] = $this->sstgr;
        $aDades['edad'] = $this->iedad;
        $aDades['profesion'] = $this->sprofesion;
        $aDades['eap'] = $this->seap;
        $aDades['observ'] = $this->sobserv;
        $aDades['lugar_nacimiento'] = $this->slugar_nacimiento;
        $aDades['profesor_stgr'] = $this->bprofesor_stgr;
        array_walk($aDades, 'core\poner_null');
        //para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (is_true($aDades['sacd'])) {
            $aDades['sacd'] = 'true';
        } else {
            $aDades['sacd'] = 'false';
        }
        if (is_true($aDades['profesor_stgr'])) {
            $aDades['profesor_stgr'] = 'true';
        } else {
            $aDades['profesor_stgr'] = 'false';
        }

        if ($bInsert === false) {
            //UPDATE
            $update = "
					id_cr                    = :id_cr,
					id_tabla                 = :id_tabla,
					dl                       = :dl,
					sacd                     = :sacd,
					trato                    = :trato,
					nom                      = :nom,
					nx1                      = :nx1,
					apellido1                = :apellido1,
					nx2                      = :nx2,
					apellido2                = :apellido2,
					f_nacimiento             = :f_nacimiento,
					lengua                   = :lengua,
					situacion                = :situacion,
					f_situacion              = :f_situacion,
					apel_fam                 = :apel_fam,
					inc                      = :inc,
					f_inc                    = :f_inc,
					stgr                     = :stgr,
					edad                     = :edad,
					profesion                = :profesion,
					eap                      = :eap,
					observ                   = :observ,
					lugar_nacimiento         = :lugar_nacimiento,
					profesor_stgr            = :profesor_stgr";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_nom=$this->iid_nom AND id_schema=$this->iid_schema")) === false) {
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
            $this->setAllAtributes($aDades);
            $this->aDades = $aDades;
        } else {
            // INSERT
            //array_unshift($aDades, $this->iid_nom);
            $campos = "(id_cr,id_tabla,dl,sacd,trato,nom,nx1,apellido1,nx2,apellido2,f_nacimiento,lengua,situacion,f_situacion,apel_fam,inc,f_inc,stgr,edad,profesion,eap,observ,lugar_nacimiento,profesor_stgr)";
            $valores = "(:id_cr,:id_tabla,:dl,:sacd,:trato,:nom,:nx1,:apellido1,:nx2,:apellido2,:f_nacimiento,:lengua,:situacion,:f_situacion,:apel_fam,:inc,:f_inc,:stgr,:edad,:profesion,:eap,:observ,:lugar_nacimiento,:profesor_stgr)";
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
            $id_auto = $oDbl->lastInsertId($nom_tabla . '_id_auto_seq');
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_auto=$id_auto")) === false) {
                $sClauError = 'PersonaAgd.carregar.Last';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            }
            $aDadesLast = $oDblSt->fetch(\PDO::FETCH_ASSOC);
            $this->aDades = $aDadesLast;
            $this->setAllAtributes($aDadesLast);
        }
        // Modifico la ficha en la BD-comun. En el caso de los de paso (ex) y dl = mi_dele
        // Cuando se va, cambio la dl y allí lo borro.
        if (get_class($this) === 'personas\model\entity\PersonaEx' &&
            is_true($this->bsacd) && $this->sdl == ConfigGlobal::mi_dele()) {
            $aDades = $this->aDades;
            $aDades['id_tabla'] = $this->sid_tabla;
            $this->copia2Comun($aDades);
        }
        return true;
    }

    /**
     * Carga los campos de la base de datos como atributos de la clase.
     *
     */
    public function DBCarregar($que = null)
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (isset($this->iid_nom)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_nom=$this->iid_nom AND id_schema=$this->iid_schema")) === false) {
                $sClauError = get_class($this) . '.carregar';
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
    public function DBEliminar()
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_nom=$this->iid_nom AND id_schema=$this->iid_schema")) === false) {
            $sClauError = get_class($this) . '.eliminar';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        return true;
    }

    /* OTROS MÉTODOS  ----------------------------------------------------------*/
    protected function copia2Comun($aDades)
    {
        $oPersonaSacd = new PersonaSacd($this->iid_nom);
        $oPersonaSacd->setAllAtributes($aDades);
        $oPersonaSacd->DBGuardar();

    }

    protected function eliminarDeComun()
    {
        $oPersonaSacd = new PersonaSacd($this->iid_nom);
        $oPersonaSacd->DBEliminar();

    }
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
        if (array_key_exists('id_nom', $aDades)) $this->setId_nom($aDades['id_nom']);
        if (array_key_exists('id_cr', $aDades)) $this->setId_cr($aDades['id_cr']);
        if (array_key_exists('id_tabla', $aDades)) $this->setId_tabla($aDades['id_tabla']);
        if (array_key_exists('dl', $aDades)) $this->setDl($aDades['dl']);
        if (array_key_exists('sacd', $aDades)) $this->setSacd($aDades['sacd']);
        if (array_key_exists('trato', $aDades)) $this->setTrato($aDades['trato']);
        if (array_key_exists('nom', $aDades)) $this->setNom($aDades['nom']);
        if (array_key_exists('nx1', $aDades)) $this->setNx1($aDades['nx1']);
        if (array_key_exists('apellido1', $aDades)) $this->setApellido1($aDades['apellido1']);
        if (array_key_exists('nx2', $aDades)) $this->setNx2($aDades['nx2']);
        if (array_key_exists('apellido2', $aDades)) $this->setApellido2($aDades['apellido2']);
        if (array_key_exists('f_nacimiento', $aDades)) $this->setF_nacimiento($aDades['f_nacimiento'], $convert);
        if (array_key_exists('lengua', $aDades)) $this->setLengua($aDades['lengua']);
        if (array_key_exists('situacion', $aDades)) $this->setSituacion($aDades['situacion']);
        if (array_key_exists('f_situacion', $aDades)) $this->setF_situacion($aDades['f_situacion'], $convert);
        if (array_key_exists('apel_fam', $aDades)) $this->setApel_fam($aDades['apel_fam']);
        if (array_key_exists('inc', $aDades)) $this->setInc($aDades['inc']);
        if (array_key_exists('f_inc', $aDades)) $this->setF_inc($aDades['f_inc'], $convert);
        if (array_key_exists('stgr', $aDades)) $this->setStgr($aDades['stgr']);
        if (array_key_exists('edad', $aDades)) $this->setEdad($aDades['edad']);
        if (array_key_exists('profesion', $aDades)) $this->setProfesion($aDades['profesion']);
        if (array_key_exists('eap', $aDades)) $this->setEap($aDades['eap']);
        if (array_key_exists('observ', $aDades)) $this->setObserv($aDades['observ']);
        if (array_key_exists('lugar_nacimiento', $aDades)) $this->setLugar_nacimiento($aDades['lugar_nacimiento']);
        if (array_key_exists('profesor_stgr', $aDades)) $this->setProfesor_stgr($aDades['profesor_stgr']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_schema('');
        $this->setId_nom('');
        $this->setId_cr('');
        $this->setId_tabla('');
        $this->setDl('');
        $this->setSacd('');
        $this->setTrato('');
        $this->setNom('');
        $this->setNx1('');
        $this->setApellido1('');
        $this->setNx2('');
        $this->setApellido2('');
        $this->setF_nacimiento('');
        $this->setLengua('');
        $this->setSituacion('');
        $this->setF_situacion('');
        $this->setApel_fam('');
        $this->setInc('');
        $this->setF_inc('');
        $this->setStgr('');
        $this->setEdad('');
        $this->setProfesion('');
        $this->setEap('');
        $this->setObserv('');
        $this->setLugar_nacimiento('');
        $this->setProfesor_stgr('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Establece el valor del atributo bsacd de PersonaGlobal
     *
     * @param boolean bsacd='f' optional
     */
    function setSacd($bsacd = 'f')
    {
        //para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (is_true($this->bsacd)) {
            $bsacd_old = 'true';
        } else {
            $bsacd_old = 'false';
        }
        // Si un sacd de paso lo cambio a NO sacd (de mi_dele), lo elimino de cp_sacd.
        if (get_class($this) === 'personas\model\entity\PersonaEx' &&
            $this->sdl == ConfigGlobal::mi_dele() &&
            is_true($bsacd_old) &&
            !is_true($bsacd)) {
            $this->eliminarDeComun();
        }

        $this->bsacd = $bsacd;
    }

    /**
     * Establece el valor del atributo sdl de PersonaGlobal
     *
     * @param string sdl='' optional
     */
    function setDl($sdl = '')
    {
        //para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (is_true($this->bsacd)) {
            $sacd = 'true';
        } else {
            $sacd = 'false';
        }
        // Si un sacd de paso pasa a estar el dl, lo copio a cp_sacd.
        // (ya se hace al guardar)
        // Si un sacd de paso marcha de la dl, lo elimino de cp_sacd.
        if (get_class($this) === 'personas\model\entity\PersonaEx' &&
            is_true($sacd) &&
            $this->sdl == ConfigGlobal::mi_dele() &&
            $sdl != ConfigGlobal::mi_dele()) {
            $this->eliminarDeComun();
        }

        $this->sdl = $sdl;
    }


    /**
     * Recupera el atributo bprofesor_stgr de PersonaPub
     *
     * @return boolean bprofesor_stgr
     */
    function getProfesor_stgr()
    {
        if (!isset($this->bprofesor_stgr) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->bprofesor_stgr;
    }

    /**
     * Establece el valor del atributo bprofesor_stgr de PersonaPub
     *
     * @param boolean bprofesor_stgr='' optional
     */
    function setProfesor_stgr($bprofesor_stgr = 'f')
    {
        $this->bprofesor_stgr = $bprofesor_stgr;
    }

    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oPersonaPubSet = new Set();

        $oPersonaPubSet->add($this->getDatosId_cr());
        $oPersonaPubSet->add($this->getDatosId_tabla());
        $oPersonaPubSet->add($this->getDatosDl());
        $oPersonaPubSet->add($this->getDatosSacd());
        $oPersonaPubSet->add($this->getDatosTrato());
        $oPersonaPubSet->add($this->getDatosNom());
        $oPersonaPubSet->add($this->getDatosNx1());
        $oPersonaPubSet->add($this->getDatosApellido1());
        $oPersonaPubSet->add($this->getDatosNx2());
        $oPersonaPubSet->add($this->getDatosApellido2());
        $oPersonaPubSet->add($this->getDatosF_nacimiento());
        $oPersonaPubSet->add($this->getDatosLengua());
        $oPersonaPubSet->add($this->getDatosSituacion());
        $oPersonaPubSet->add($this->getDatosF_situacion());
        $oPersonaPubSet->add($this->getDatosApel_fam());
        $oPersonaPubSet->add($this->getDatosInc());
        $oPersonaPubSet->add($this->getDatosF_inc());
        $oPersonaPubSet->add($this->getDatosStgr());
        $oPersonaPubSet->add($this->getDatosEdad());
        $oPersonaPubSet->add($this->getDatosProfesion());
        $oPersonaPubSet->add($this->getDatosEap());
        $oPersonaPubSet->add($this->getDatosObserv());
        $oPersonaPubSet->add($this->getDatosLugar_nacimiento());
        $oPersonaPubSet->add($this->getDatosProfesor_stgr());
        return $oPersonaPubSet->getTot();
    }

    /**
     * Recupera les propietats de l'atribut iedad PersonaPub
     * en una clase del tipus DatosCampo
     *
     * @return object DatosCampo
     */
    function getDatosEdad()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'edad'));
        $oDatosCampo->setEtiqueta(_("edad"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iprofesor_stgr PersonaPub
     * en una clase del tipus DatosCampo
     *
     * @return object DatosCampo
     */
    function getDatosProfesor_stgr()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'profesor_stgr'));
        $oDatosCampo->setEtiqueta(_("profesor stgr"));
        $oDatosCampo->setTipo('check');
        return $oDatosCampo;
    }

}
