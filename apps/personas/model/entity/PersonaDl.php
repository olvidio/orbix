<?php
namespace personas\model\entity;

use core\DatosCampo;
use core\Set;
use web\DateTimeLocal;
use function core\is_true;
use function PHPUnit\Framework\isInstanceOf;

/**
 * Fitxer amb la Classe que accedeix a la taula personas_dl
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */

/**
 * Clase que implementa la entidad personas_dl
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */
class PersonaDl extends PersonaGlobal
{
    /* ATRIBUTOS ----------------------------------------------------------------- */
    /**
     * Id_ctr de PersonaDl
     *
     * @var integer
     */
    protected $iid_ctr;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */

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
        $oDbl = $GLOBALS['oDB'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_nom') && $val_id !== '') $this->iid_nom = (int)$val_id;
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->iid_nom = (integer)$a_id;
                $this->aPrimary_key = array('id_nom' => $this->iid_nom);
            }
        }
        $this->setoDbl($oDbl);
        $this->setNomTabla('personas_dl');
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
        //$aDades['id_tabla'] = $this->sid_tabla;
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
        $aDades['profesion'] = $this->sprofesion;
        $aDades['eap'] = $this->seap;
        $aDades['observ'] = $this->sobserv;
        $aDades['id_ctr'] = $this->iid_ctr;
        $aDades['lugar_nacimiento'] = $this->slugar_nacimiento;
        array_walk($aDades, 'core\poner_null');
        //para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (is_true($aDades['sacd'])) {
            $aDades['sacd'] = 'true';
        } else {
            $aDades['sacd'] = 'false';
        }

        if ($bInsert === false) {
            //UPDATE
            //id_tabla                 = :id_tabla,
            $update = "
					id_cr                    = :id_cr,
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
					profesion                = :profesion,
					eap                      = :eap,
					observ                   = :observ,
					id_ctr                   = :id_ctr,
					lugar_nacimiento         = :lugar_nacimiento";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_nom=$this->iid_nom")) === false) {
                $sClauError = 'PersonaDl.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'PersonaDl.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
            $this->aDades = $aDades;
            $this->setAllAtributes($aDades);
        } else {
            // INSERT
            $campos = "id_cr,dl,sacd,trato,nom,nx1,apellido1,nx2,apellido2,f_nacimiento,lengua,situacion,f_situacion,apel_fam,inc,f_inc,stgr,profesion,eap,observ,id_ctr,lugar_nacimiento";
            $valores = ":id_cr,:dl,:sacd,:trato,:nom,:nx1,:apellido1,:nx2,:apellido2,:f_nacimiento,:lengua,:situacion,:f_situacion,:apel_fam,:inc,:f_inc,:stgr,:profesion,:eap,:observ,:id_ctr,:lugar_nacimiento";
            if (empty($this->iid_nom)) {
                $campos = "($campos)";
                $valores = "($valores)";
            } else {
                array_shift($aDades, $this->iid_nom);
                $campos = "(id_nom,$campos)";
                $valores = "(:id_nom,$valores)";
            }
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'PersonaDl.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'PersonaDl.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
            $id_auto = $oDbl->lastInsertId($nom_tabla . '_id_auto_seq');
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_auto=$id_auto")) === false) {
                $sClauError = get_class($this) . '.carregar.Last';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            }
            $aDadesLast = $oDblSt->fetch(\PDO::FETCH_ASSOC);
            $this->aDades = $aDadesLast;
            $this->setAllAtributes($aDadesLast);
        }
        // Modifico la ficha en la BD-comun
        if ($this->bsacd) {
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
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_nom=$this->iid_nom")) === false) {
                $sClauError = 'PersonaDl.carregar';
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
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_nom=$this->iid_nom")) === false) {
            $sClauError = 'PersonaDl.eliminar';
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
        if (array_key_exists('profesion', $aDades)) $this->setProfesion($aDades['profesion']);
        if (array_key_exists('eap', $aDades)) $this->setEap($aDades['eap']);
        if (array_key_exists('observ', $aDades)) $this->setObserv($aDades['observ']);
        if (array_key_exists('id_ctr', $aDades)) $this->setId_ctr($aDades['id_ctr']);
        if (array_key_exists('lugar_nacimiento', $aDades)) $this->setLugar_nacimiento($aDades['lugar_nacimiento']);
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
        $this->setProfesion('');
        $this->setEap('');
        $this->setObserv('');
        $this->setId_ctr('');
        $this->setLugar_nacimiento('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/
    /**
     * Recupera el atributo iid_ctr de PersonaDl
     *
     * @return integer iid_ctr
     */
    function getId_ctr()
    {
        if (!isset($this->iid_ctr) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_ctr;
    }

    /**
     * Establece el valor del atributo iid_ctr de PersonaDl
     *
     * @param integer iid_ctr='' optional
     */
    function setId_ctr($iid_ctr = '')
    {
        $this->iid_ctr = $iid_ctr;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Para clacular la edad a partir de la fecha de nacimiento
     *
     *
     * @author    Daniel Serrabou
     * @since     25/11/2010.
     *
     */
    function getEdad()
    {
        $oF_nacimiento = $this->getF_nacimiento();
        if (!empty($oF_nacimiento) && ($oF_nacimiento instanceof DateTimeLocal)) {
            $m = (int) $oF_nacimiento->format('m');
            $a = (int) $oF_nacimiento->format('Y');
            $ah = (int) date("Y");
            $mh = (int) date("m");
            $inc_m = 0;
            $mh >= $m ? 0 : $inc_m = 1;
            $edad = $ah - $a - $inc_m;
        } else {
            $edad = "-";
        }
        return $edad;
    }


    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oPersonaDlSet = new Set();

        $oPersonaDlSet->add($this->getDatosId_cr());
        $oPersonaDlSet->add($this->getDatosId_tabla());
        $oPersonaDlSet->add($this->getDatosDl());
        $oPersonaDlSet->add($this->getDatosSacd());
        $oPersonaDlSet->add($this->getDatosTrato());
        $oPersonaDlSet->add($this->getDatosNom());
        $oPersonaDlSet->add($this->getDatosNx1());
        $oPersonaDlSet->add($this->getDatosApellido1());
        $oPersonaDlSet->add($this->getDatosNx2());
        $oPersonaDlSet->add($this->getDatosApellido2());
        $oPersonaDlSet->add($this->getDatosF_nacimiento());
        $oPersonaDlSet->add($this->getDatosLengua());
        $oPersonaDlSet->add($this->getDatosSituacion());
        $oPersonaDlSet->add($this->getDatosF_situacion());
        $oPersonaDlSet->add($this->getDatosApel_fam());
        $oPersonaDlSet->add($this->getDatosInc());
        $oPersonaDlSet->add($this->getDatosF_inc());
        $oPersonaDlSet->add($this->getDatosStgr());
        $oPersonaDlSet->add($this->getDatosProfesion());
        $oPersonaDlSet->add($this->getDatosEap());
        $oPersonaDlSet->add($this->getDatosObserv());
        $oPersonaDlSet->add($this->getDatosId_ctr());
        $oPersonaDlSet->add($this->getDatosLugar_nacimiento());
        return $oPersonaDlSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut iid_ctr de PersonaDl
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosId_ctr()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_ctr'));
        $oDatosCampo->setEtiqueta(_("id_ctr"));
        return $oDatosCampo;
    }
}

?>
