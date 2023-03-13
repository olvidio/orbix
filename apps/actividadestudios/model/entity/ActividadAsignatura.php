<?php

namespace actividadestudios\model\entity;

use core;
use web;

/**
 * Fitxer amb la Classe que accedeix a la taula d_asignaturas_activ_all
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 14/11/2014
 */

/**
 * Clase que implementa la entidad d_asignaturas_activ_all
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 14/11/2014
 */
class ActividadAsignatura extends core\ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    // tipo constants.
    const TIPO_CA = 'v'; // Verano: ca,cv.
    const TIPO_INV = 'i'; // Semestre de invierno.
    const TIPO_PRECEPTOR = 'p'; // Preceptor.

    /**
     * aPrimary_key de ActividadAsignatura
     *
     * @var array
     */
    protected $aPrimary_key;

    /**
     * aDades de ActividadAsignatura
     *
     * @var array
     */
    protected $aDades;

    /**
     * Id_schema de ActividadAsignatura
     *
     * @var integer
     */
    protected $iid_schema;
    /**
     * Id_activ de ActividadAsignatura
     *
     * @var integer
     */
    protected $iid_activ;
    /**
     * Id_asignatura de ActividadAsignatura
     *
     * @var integer
     */
    protected $iid_asignatura;
    /**
     * Id_profesor de ActividadAsignatura
     *
     * @var integer
     */
    protected $iid_profesor;
    /**
     * Avis_profesor de ActividadAsignatura
     *
     * @var string
     */
    protected $savis_profesor;
    /**
     * Tipo de ActividadAsignatura
     *
     * @var string
     */
    protected $stipo;
    /**
     * F_ini de ActividadAsignatura
     *
     * @var web\DateTimeLocal
     */
    protected $df_ini;
    /**
     * F_fin de ActividadAsignatura
     *
     * @var web\DateTimeLocal
     */
    protected $df_fin;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * oDbl de ActividadAsignatura
     *
     * @var object
     */
    protected $oDbl;
    /**
     * NomTabla de ActividadAsignatura
     *
     * @var string
     */
    protected $sNomTabla;
    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     * Si només necessita un valor, se li pot passar un integer.
     * En general se li passa un array amb les claus primàries.
     *
     * @param integer|array iid_activ,iid_asignatura
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBP'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'id_activ') && $val_id !== '') $this->iid_activ = (int)$val_id; 
                if (($nom_id == 'id_asignatura') && $val_id !== '') $this->iid_asignatura = (int)$val_id; 
            }
        }
        $this->setoDbl($oDbl);
        $this->setNomTabla('d_asignaturas_activ_all');
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
        $aDades = array();
        $aDades['id_profesor'] = $this->iid_profesor;
        $aDades['avis_profesor'] = $this->savis_profesor;
        $aDades['tipo'] = $this->stipo;
        $aDades['f_ini'] = $this->df_ini;
        $aDades['f_fin'] = $this->df_fin;
        array_walk($aDades, 'core\poner_null');

        if ($bInsert === false) {
            //UPDATE
            $update = "
					id_profesor              = :id_profesor,
					avis_profesor            = :avis_profesor,
					tipo                     = :tipo,
					f_ini                    = :f_ini,
					f_fin                    = :f_fin";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_activ='$this->iid_activ' AND id_asignatura='$this->iid_asignatura'")) === false) {
                $sClauError = 'ActividadAsignatura.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'ActividadAsignatura.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        } else {
            // INSERT
            array_unshift($aDades, $this->iid_activ, $this->iid_asignatura);
            $campos = "(id_activ,id_asignatura,id_profesor,avis_profesor,tipo,f_ini,f_fin)";
            $valores = "(:id_activ,:id_asignatura,:id_profesor,:avis_profesor,:tipo,:f_ini,:f_fin)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'ActividadAsignatura.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'ActividadAsignatura.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        }
        $this->setAllAtributes($aDades);
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
        if (isset($this->iid_activ) && isset($this->iid_asignatura)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_activ='$this->iid_activ' AND id_asignatura='$this->iid_asignatura'")) === false) {
                $sClauError = 'ActividadAsignatura.carregar';
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
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_activ='$this->iid_activ' AND id_asignatura='$this->iid_asignatura'")) === false) {
            $sClauError = 'ActividadAsignatura.eliminar';
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
    function setAllAtributes($aDades, $convert = FALSE)
    {
        if (!is_array($aDades)) return;
        if (array_key_exists('id_schema', $aDades)) $this->setId_schema($aDades['id_schema']);
        if (array_key_exists('id_activ', $aDades)) $this->setId_activ($aDades['id_activ']);
        if (array_key_exists('id_asignatura', $aDades)) $this->setId_asignatura($aDades['id_asignatura']);
        if (array_key_exists('id_profesor', $aDades)) $this->setId_profesor($aDades['id_profesor']);
        if (array_key_exists('avis_profesor', $aDades)) $this->setAvis_profesor($aDades['avis_profesor']);
        if (array_key_exists('tipo', $aDades)) $this->setTipo($aDades['tipo']);
        if (array_key_exists('f_ini', $aDades)) $this->setF_ini($aDades['f_ini'], $convert);
        if (array_key_exists('f_fin', $aDades)) $this->setF_fin($aDades['f_fin'], $convert);
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
        $this->setId_asignatura('');
        $this->setId_profesor('');
        $this->setAvis_profesor('');
        $this->setTipo('');
        $this->setF_ini('');
        $this->setF_fin('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de ActividadAsignatura en un array
     *
     * @return array aDades
     */
    function getTot()
    {
        if (!is_array($this->aDades)) {
            $this->DBCarregar('tot');
        }
        return $this->aDades;
    }

    /**
     * Recupera la clave primaria de ActividadAsignatura en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('id_activ' => $this->iid_activ, 'id_asignatura' => $this->iid_asignatura);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de ActividadAsignatura en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'id_activ') && $val_id !== '') $this->iid_activ = (int)$val_id; 
                if (($nom_id == 'id_asignatura') && $val_id !== '') $this->iid_asignatura = (int)$val_id; 
            }
        }
    }

    /**
     * Recupera el atributo iid_activ de ActividadAsignatura
     *
     * @return integer iid_activ
     */
    function getId_activ()
    {
        if (!isset($this->iid_activ) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_activ;
    }

    /**
     * Establece el valor del atributo iid_activ de ActividadAsignatura
     *
     * @param integer iid_activ
     */
    function setId_activ($iid_activ)
    {
        $this->iid_activ = $iid_activ;
    }

    /**
     * Recupera el atributo iid_asignatura de ActividadAsignatura
     *
     * @return integer iid_asignatura
     */
    function getId_asignatura()
    {
        if (!isset($this->iid_asignatura) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_asignatura;
    }

    /**
     * Establece el valor del atributo iid_asignatura de ActividadAsignatura
     *
     * @param integer iid_asignatura
     */
    function setId_asignatura($iid_asignatura)
    {
        $this->iid_asignatura = $iid_asignatura;
    }

    /**
     * Recupera el atributo iid_profesor de ActividadAsignatura
     *
     * @return integer iid_profesor
     */
    function getId_profesor()
    {
        if (!isset($this->iid_profesor) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_profesor;
    }

    /**
     * Establece el valor del atributo iid_profesor de ActividadAsignatura
     *
     * @param integer iid_profesor='' optional
     */
    function setId_profesor($iid_profesor = '')
    {
        $this->iid_profesor = $iid_profesor;
    }

    /**
     * Recupera el atributo savis_profesor de ActividadAsignatura
     *
     * @return string savis_profesor
     */
    function getAvis_profesor()
    {
        if (!isset($this->savis_profesor) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->savis_profesor;
    }

    /**
     * Establece el valor del atributo savis_profesor de ActividadAsignatura
     *
     * @param string savis_profesor='' optional
     */
    function setAvis_profesor($savis_profesor = '')
    {
        $this->savis_profesor = $savis_profesor;
    }

    /**
     * Recupera el atributo stipo de ActividadAsignatura
     *
     * @return string stipo
     */
    function getTipo()
    {
        if (!isset($this->stipo) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->stipo;
    }

    /**
     * Establece el valor del atributo stipo de ActividadAsignatura
     *
     * @param string stipo='' optional
     */
    function setTipo($stipo = '')
    {
        $this->stipo = $stipo;
    }

    /**
     * Recupera el atributo df_ini de ActividadAsignatura
     *
     * @return web\DateTimeLocal df_ini
     */
    function getF_ini()
    {
        if (!isset($this->df_ini) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        if (empty($this->df_ini)) {
            return new web\NullDateTimeLocal();
        }
        $oConverter = new core\ConverterDate('date', $this->df_ini);
        return $oConverter->fromPg();
    }

    /**
     * Establece el valor del atributo df_ini de ActividadAsignatura
     * Si df_ini es string, y convert=true se convierte usando el formato webDateTimeLocal->getFormat().
     * Si convert es false, df_ini debe ser un string en formato ISO (Y-m-d). Corresponde al pgstyle de la base de datos.
     *
     * @param date|string df_ini='' optional.
     * @param boolean convert=true optional. Si es false, df_ini debe ser un string en formato ISO (Y-m-d).
     */
    function setF_ini($df_ini = '', $convert = true)
    {
        if ($convert === true && !empty($df_ini)) {
            $oConverter = new core\ConverterDate('date', $df_ini);
            $this->df_ini = $oConverter->toPg();
        } else {
            $this->df_ini = $df_ini;
        }
    }

    /**
     * Recupera el atributo df_fin de ActividadAsignatura
     *
     * @return web\DateTimeLocal df_fin
     */
    function getF_fin()
    {
        if (!isset($this->df_fin) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        if (empty($this->df_fin)) {
            return new web\NullDateTimeLocal();
        }
        $oConverter = new core\ConverterDate('date', $this->df_fin);
        return $oConverter->fromPg();
    }

    /**
     * Establece el valor del atributo df_fin de ActividadAsignatura
     * Si df_fin es string, y convert=true se convierte usando el formato webDateTimeLocal->getFormat().
     * Si convert es false, df_fin debe ser un string en formato ISO (Y-m-d). Corresponde al pgstyle de la base de datos.
     *
     * @param date|string df_fin='' optional.
     * @param boolean convert=true optional. Si es false, df_fin debe ser un string en formato ISO (Y-m-d).
     */
    function setF_fin($df_fin = '', $convert = true)
    {
        if ($convert === true && !empty($df_fin)) {
            $oConverter = new core\ConverterDate('date', $df_fin);
            $this->df_fin = $oConverter->toPg();
        } else {
            $this->df_fin = $df_fin;
        }
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oActividadAsignaturaSet = new core\Set();

        $oActividadAsignaturaSet->add($this->getDatosId_profesor());
        $oActividadAsignaturaSet->add($this->getDatosAvis_profesor());
        $oActividadAsignaturaSet->add($this->getDatosTipo());
        $oActividadAsignaturaSet->add($this->getDatosF_ini());
        $oActividadAsignaturaSet->add($this->getDatosF_fin());
        return $oActividadAsignaturaSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut iid_profesor de ActividadAsignatura
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosId_profesor()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_profesor'));
        $oDatosCampo->setEtiqueta(_("id_profesor"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut savis_profesor de ActividadAsignatura
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosAvis_profesor()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'avis_profesor'));
        $oDatosCampo->setEtiqueta(_("aviso profesor"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut stipo de ActividadAsignatura
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosTipo()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'tipo'));
        $oDatosCampo->setEtiqueta(_("tipo"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut df_ini de ActividadAsignatura
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosF_ini()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'f_ini'));
        $oDatosCampo->setEtiqueta(_("fecha inicio"));
        $oDatosCampo->setTipo('fecha');
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut df_fin de ActividadAsignatura
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosF_fin()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'f_fin'));
        $oDatosCampo->setEtiqueta(_("fecha fin"));
        $oDatosCampo->setTipo('fecha');
        return $oDatosCampo;
    }
}