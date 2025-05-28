<?php
namespace profesores\model\entity;

use actividadestudios\model\entity\ActividadAsignatura;
use core\ClasePropiedades;
use core\DatosCampo;
use core\Set;

/**
 * Fitxer amb la Classe que accedeix a la taula d_docencia_stgr
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 28/10/2014
 */

/**
 * Clase que implementa la entidad d_docencia_stgr
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 28/10/2014
 */
class ProfesorDocenciaStgr extends ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de ProfesorDocenciaStgr
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de ProfesorDocenciaStgr
     *
     * @var array
     */
    private $aDades;

    /**
     * bLoaded
     *
     * @var boolean
     */
    private $bLoaded = FALSE;

    /**
     * Id_schema de ProfesorDocenciaStgr
     *
     * @var integer
     */
    private $iid_schema;
    /**
     * Id_item de ProfesorDocenciaStgr
     *
     * @var integer
     */
    private $iid_item;
    /**
     * Id_nom de ProfesorDocenciaStgr
     *
     * @var integer
     */
    private $iid_nom;
    /**
     * Id_asignatura de ProfesorDocenciaStgr
     *
     * @var integer
     */
    private $iid_asignatura;
    /**
     * Id_activ de ProfesorDocenciaStgr
     *
     * @var integer
     */
    private $iid_activ;
    /**
     * Tipo de ProfesorDocenciaStgr
     *
     * @var string
     */
    private $stipo;
    /**
     * Curso_inicio de ProfesorDocenciaStgr
     *
     * @var integer
     */
    private $icurso_inicio;
    /**
     * Acta de ProfesorDocenciaStgr
     *
     * @var string
     */
    private $sacta;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * oDbl de ProfesorDocenciaStgr
     *
     * @var object
     */
    protected $oDbl;
    /**
     * NomTabla de ProfesorDocenciaStgr
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
     * @param integer|array iid_item,iid_nom
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDB'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_item') && $val_id !== '') $this->iid_item = (int)$val_id;
                if (($nom_id === 'id_nom') && $val_id !== '') $this->iid_nom = (int)$val_id;
            }
        }
        $this->setoDbl($oDbl);
        $this->setNomTabla('d_docencia_stgr');
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
        $aDades['id_asignatura'] = $this->iid_asignatura;
        $aDades['id_activ'] = $this->iid_activ;
        $aDades['tipo'] = $this->stipo;
        $aDades['curso_inicio'] = $this->icurso_inicio;
        $aDades['acta'] = $this->sacta;
        array_walk($aDades, 'core\poner_null');

        if ($bInsert === false) {
            //UPDATE
            $update = "
					id_asignatura            = :id_asignatura,
					id_activ                 = :id_activ,
					tipo                     = :tipo,
					curso_inicio             = :curso_inicio,
					acta                     = :acta";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item=$this->iid_item")) === false) {
                $sClauError = 'ProfesorDocenciaStgr.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'ProfesorDocenciaStgr.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        } else {
            // INSERT
            array_unshift($aDades, $this->iid_nom);
            $campos = "(id_nom,id_asignatura,id_activ,tipo,curso_inicio,acta)";
            $valores = "(:id_nom,:id_asignatura,:id_activ,:tipo,:curso_inicio,:acta)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'ProfesorDocenciaStgr.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'ProfesorDocenciaStgr.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
            $this->iid_item = $oDbl->lastInsertId('d_docencia_stgr_id_item_seq');
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
        if (isset($this->iid_item)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_item=$this->iid_item")) === false) {
                $sClauError = 'ProfesorDocenciaStgr.carregar';
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
        if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_item=$this->iid_item")) === false) {
            $sClauError = 'ProfesorDocenciaStgr.eliminar';
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
    function setAllAtributes(array $aDades)
    {
        if (!is_array($aDades)) return;
        if (array_key_exists('id_schema', $aDades)) $this->setId_schema($aDades['id_schema']);
        if (array_key_exists('id_item', $aDades)) $this->setId_item($aDades['id_item']);
        if (array_key_exists('id_nom', $aDades)) $this->setId_nom($aDades['id_nom']);
        if (array_key_exists('id_asignatura', $aDades)) $this->setId_asignatura($aDades['id_asignatura']);
        if (array_key_exists('id_activ', $aDades)) $this->setId_activ($aDades['id_activ']);
        if (array_key_exists('tipo', $aDades)) $this->setTipo($aDades['tipo']);
        if (array_key_exists('curso_inicio', $aDades)) $this->setCurso_inicio($aDades['curso_inicio']);
        if (array_key_exists('acta', $aDades)) $this->setActa($aDades['acta']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_schema('');
        $this->setId_item('');
        $this->setId_nom('');
        $this->setId_asignatura('');
        $this->setId_activ('');
        $this->setTipo('');
        $this->setCurso_inicio('');
        $this->setActa('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de ProfesorDocenciaStgr en un array
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
     * Recupera la clave primaria de ProfesorDocenciaStgr en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('id_item' => $this->iid_item);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de ProfesorDocenciaStgr en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_item') && $val_id !== '') $this->iid_item = (int)$val_id;
            }
        }
    }

    /**
     * Recupera el atributo iid_item de ProfesorDocenciaStgr
     *
     * @return integer iid_item
     */
    function getId_item()
    {
        if (!isset($this->iid_item) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_item;
    }

    /**
     * Establece el valor del atributo iid_item de ProfesorDocenciaStgr
     *
     * @param integer iid_item
     */
    function setId_item($iid_item)
    {
        $this->iid_item = $iid_item;
    }

    /**
     * Recupera el atributo iid_nom de ProfesorDocenciaStgr
     *
     * @return integer iid_nom
     */
    function getId_nom()
    {
        if (!isset($this->iid_nom) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_nom;
    }

    /**
     * Establece el valor del atributo iid_nom de ProfesorDocenciaStgr
     *
     * @param integer iid_nom
     */
    function setId_nom($iid_nom)
    {
        $this->iid_nom = $iid_nom;
    }

    /**
     * Recupera el atributo iid_asignatura de ProfesorDocenciaStgr
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
     * Establece el valor del atributo iid_asignatura de ProfesorDocenciaStgr
     *
     * @param integer iid_asignatura='' optional
     */
    function setId_asignatura($iid_asignatura = '')
    {
        $this->iid_asignatura = $iid_asignatura;
    }

    /**
     * Recupera el atributo iid_activ de ProfesorDocenciaStgr
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
     * Establece el valor del atributo iid_activ de ProfesorDocenciaStgr
     *
     * @param integer iid_activ='' optional
     */
    function setId_activ($iid_activ = '')
    {
        $this->iid_activ = $iid_activ;
    }

    /**
     * Recupera el atributo stipo de ProfesorDocenciaStgr
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
     * Establece el valor del atributo stipo de ProfesorDocenciaStgr
     *
     * @param string stipo='' optional
     */
    function setTipo($stipo = '')
    {
        $this->stipo = $stipo;
    }

    /**
     * Recupera el atributo icurso_inicio de ProfesorDocenciaStgr
     *
     * @return string icurso_inicio
     */
    function getCurso_inicio()
    {
        if (!isset($this->icurso_inicio) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->icurso_inicio;
    }

    /**
     * Establece el valor del atributo icurso_inicio de ProfesorDocenciaStgr
     *
     * @param string icurso_inicio='' optional
     */
    function setCurso_inicio($icurso_inicio = '')
    {
        $this->icurso_inicio = $icurso_inicio;
    }

    /**
     * Recupera el atributo sacta de ProfesorDocenciaStgr
     *
     * @return string sacta
     */
    function getActa()
    {
        if (!isset($this->sacta) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sacta;
    }

    /**
     * Establece el valor del atributo sacta de ProfesorDocenciaStgr
     *
     * @param string sacta='' optional
     */
    function setActa($sacta = '')
    {
        $this->sacta = $sacta;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oProfesorDocenciaStgrSet = new Set();

        $oProfesorDocenciaStgrSet->add($this->getDatosId_asignatura());
        $oProfesorDocenciaStgrSet->add($this->getDatosId_activ());
        $oProfesorDocenciaStgrSet->add($this->getDatosTipo());
        $oProfesorDocenciaStgrSet->add($this->getDatosCurso_inicio());
        $oProfesorDocenciaStgrSet->add($this->getDatosActa());
        return $oProfesorDocenciaStgrSet->getTot();
    }

    /**
     * Recupera les propietats de l'atribut iid_asignatura de ProfesorDocenciaStgr
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosId_asignatura()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_asignatura'));
        $oDatosCampo->setEtiqueta(_("asignatura"));
        $oDatosCampo->setTipo('opciones');
        $oDatosCampo->setArgument('asignaturas\model\entity\Asignatura'); // nombre del objeto relacionado
        $oDatosCampo->setArgument2('getNombre_corto'); // método para obtener el valor a mostrar del objeto relacionado.
        $oDatosCampo->setArgument3('getListaAsignaturas'); // método con que crear la lista de opciones del Gestor objeto relacionado.
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iid_activ de ProfesorDocenciaStgr
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosId_activ()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_activ'));
        $oDatosCampo->setEtiqueta(_("actividad"));
        $oDatosCampo->setTipo('opciones');
        $oDatosCampo->setArgument('actividades\model\entity\ActividadAll'); // nombre del objeto relacionado
        $oDatosCampo->setArgument2('getNom_activ'); // método para obtener el valor a mostrar del objeto relacionado.
        $oDatosCampo->setArgument3('getListaActividadesEstudios'); // método con que crear la lista de opciones del Gestor objeto relacionado.
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut stipo de ProfesorDocenciaStgr
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosTipo()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'tipo'));
        $oDatosCampo->setEtiqueta(_("tipo"));
        $oDatosCampo->setTipo('array');
        $oDatosCampo->setLista(array(ActividadAsignatura::TIPO_CA => _("ca/cv"),
            ActividadAsignatura::TIPO_INV => _("sem. invierno"),
            ActividadAsignatura::TIPO_PRECEPTOR => _("preceptor")));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut icurso_inicio de ProfesorDocenciaStgr
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosCurso_inicio()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'curso_inicio'));
        $oDatosCampo->setEtiqueta(_("año inicio curso"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(5);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sacta de ProfesorDocenciaStgr
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosActa()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'acta'));
        $oDatosCampo->setEtiqueta(_("acta"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(30);
        return $oDatosCampo;
    }
}

?>
