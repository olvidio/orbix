<?php

namespace profesores\model\entity;

use core;
use web;

/**
 * Fitxer amb la Classe que accedeix a la taula d_profesor_stgr
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */

/**
 * Clase que implementa la entidad d_profesor_stgr
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */
class Profesor extends core\ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de Profesor
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de Profesor
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
     * Id_item de Profesor
     *
     * @var integer
     */
    private $iid_item;
    /**
     * Id_nom de Profesor
     *
     * @var integer
     */
    private $iid_nom;
    /**
     * Id_departamento de Profesor
     *
     * @var integer
     */
    private $iid_departamento;
    /**
     * Escrito_nombramiento de Profesor
     *
     * @var string
     */
    private $sescrito_nombramiento;
    /**
     * F_nombramiento de Profesor
     *
     * @var web\DateTimeLocal
     */
    private $df_nombramiento;
    /**
     * Id_tipo_profesor de Profesor
     *
     * @var integer
     */
    private $iid_tipo_profesor;
    /**
     * Escrito_cese de Profesor
     *
     * @var string
     */
    private $sescrito_cese;
    /**
     * F_cese de Profesor
     *
     * @var web\DateTimeLocal
     */
    private $df_cese;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * oDbl de Profesor
     *
     * @var object
     */
    protected $oDbl;
    /**
     * NomTabla de Profesor
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
     * @param integer|array iid_item,iid_nom,iid_departamento
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDB'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'id_item') && $val_id !== '') $this->iid_item = (int)$val_id;
                if (($nom_id == 'id_nom') && $val_id !== '') $this->iid_nom = (int)$val_id;
                if (($nom_id == 'id_departamento') && $val_id !== '') $this->iid_departamento = (int)$val_id;
            }
        }
        $this->setoDbl($oDbl);
        $this->setNomTabla('d_profesor_stgr');
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
        $aDades['escrito_nombramiento'] = $this->sescrito_nombramiento;
        $aDades['f_nombramiento'] = $this->df_nombramiento;
        $aDades['id_tipo_profesor'] = $this->iid_tipo_profesor;
        $aDades['escrito_cese'] = $this->sescrito_cese;
        $aDades['f_cese'] = $this->df_cese;
        array_walk($aDades, 'core\poner_null');

        if ($bInsert === false) {
            //UPDATE
            $update = "
					escrito_nombramiento     = :escrito_nombramiento,
					f_nombramiento           = :f_nombramiento,
					id_tipo_profesor         = :id_tipo_profesor,
					escrito_cese             = :escrito_cese,
					f_cese                   = :f_cese";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item=$this->iid_item")) === false) {
                $sClauError = 'Profesor.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'Profesor.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        } else {
            // INSERT
            array_unshift($aDades, $this->iid_nom, $this->iid_departamento);
            $campos = "(id_nom,id_departamento,escrito_nombramiento,f_nombramiento,id_tipo_profesor,escrito_cese,f_cese)";
            $valores = "(:id_nom,:id_departamento,:escrito_nombramiento,:f_nombramiento,:id_tipo_profesor,:escrito_cese,:f_cese)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'Profesor.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'Profesor.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
            $this->id_item = $oDbl->lastInsertId('d_profesor_stgr_id_item_seq');
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
                $sClauError = 'Profesor.carregar';
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
            $sClauError = 'Profesor.eliminar';
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
        if (array_key_exists('id_item', $aDades)) $this->setId_item($aDades['id_item']);
        if (array_key_exists('id_nom', $aDades)) $this->setId_nom($aDades['id_nom']);
        if (array_key_exists('id_departamento', $aDades)) $this->setId_departamento($aDades['id_departamento']);
        if (array_key_exists('escrito_nombramiento', $aDades)) $this->setEscrito_nombramiento($aDades['escrito_nombramiento']);
        if (array_key_exists('f_nombramiento', $aDades)) $this->setF_nombramiento($aDades['f_nombramiento'], $convert);
        if (array_key_exists('id_tipo_profesor', $aDades)) $this->setId_tipo_profesor($aDades['id_tipo_profesor']);
        if (array_key_exists('escrito_cese', $aDades)) $this->setEscrito_cese($aDades['escrito_cese']);
        if (array_key_exists('f_cese', $aDades)) $this->setF_cese($aDades['f_cese'], $convert);
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
        $this->setId_departamento('');
        $this->setEscrito_nombramiento('');
        $this->setF_nombramiento('');
        $this->setId_tipo_profesor('');
        $this->setEscrito_cese('');
        $this->setF_cese('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de Profesor en un array
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
     * Recupera la clave primaria de Profesor en un array
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
     * Establece la clave primaria de Profesor en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'id_item') && $val_id !== '') $this->iid_item = (int)$val_id;
            }
        }
    }

    /**
     * Recupera el atributo iid_item de Profesor
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
     * Establece el valor del atributo iid_item de Profesor
     *
     * @param integer iid_item
     */
    function setId_item($iid_item)
    {
        $this->iid_item = $iid_item;
    }

    /**
     * Recupera el atributo iid_nom de Profesor
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
     * Establece el valor del atributo iid_nom de Profesor
     *
     * @param integer iid_nom
     */
    function setId_nom($iid_nom)
    {
        $this->iid_nom = $iid_nom;
    }

    /**
     * Recupera el atributo iid_departamento de Profesor
     *
     * @return integer iid_departamento
     */
    function getId_departamento()
    {
        if (!isset($this->iid_departamento) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_departamento;
    }

    /**
     * Establece el valor del atributo iid_departamento de Profesor
     *
     * @param integer iid_departamento
     */
    function setId_departamento($iid_departamento)
    {
        $this->iid_departamento = $iid_departamento;
    }

    /**
     * Recupera el atributo sescrito_nombramiento de Profesor
     *
     * @return string sescrito_nombramiento
     */
    function getEscrito_nombramiento()
    {
        if (!isset($this->sescrito_nombramiento) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sescrito_nombramiento;
    }

    /**
     * Establece el valor del atributo sescrito_nombramiento de Profesor
     *
     * @param string sescrito_nombramiento='' optional
     */
    function setEscrito_nombramiento($sescrito_nombramiento = '')
    {
        $this->sescrito_nombramiento = $sescrito_nombramiento;
    }

    /**
     * Recupera el atributo df_nombramiento de Profesor
     *
     * @return web\DateTimeLocal df_nombramiento
     */
    function getF_nombramiento()
    {
        if (!isset($this->df_nombramiento) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        if (empty($this->df_nombramiento)) {
            return new web\NullDateTimeLocal();
        }
        $oConverter = new core\ConverterDate('date', $this->df_nombramiento);
        return $oConverter->fromPg();
    }

    /**
     * Establece el valor del atributo df_nombramiento de Profesor
     * Si df_nombramiento es string, y convert=true se convierte usando el formato webDateTimeLocal->getFormat().
     * Si convert es false, df_nombramiento debe ser un string en formato ISO (Y-m-d). Corresponde al pgstyle de la base de datos.
     *
     * @param date|string df_nombramiento='' optional.
     * @param boolean convert=true optional. Si es false, df_nombramiento debe ser un string en formato ISO (Y-m-d).
     */
    function setF_nombramiento($df_nombramiento = '', $convert = true)
    {
        if ($convert === true && !empty($df_nombramiento)) {
            $oConverter = new core\ConverterDate('date', $df_nombramiento);
            $this->df_nombramiento = $oConverter->toPg();
        } else {
            $this->df_nombramiento = $df_nombramiento;
        }
    }

    /**
     * Recupera el atributo iid_tipo_profesor de Profesor
     *
     * @return integer iid_tipo_profesor
     */
    function getId_tipo_profesor()
    {
        if (!isset($this->iid_tipo_profesor) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_tipo_profesor;
    }

    /**
     * Establece el valor del atributo iid_tipo_profesor de Profesor
     *
     * @param integer iid_tipo_profesor='' optional
     */
    function setId_tipo_profesor($iid_tipo_profesor = '')
    {
        $this->iid_tipo_profesor = $iid_tipo_profesor;
    }

    /**
     * Recupera el atributo sescrito_cese de Profesor
     *
     * @return string sescrito_cese
     */
    function getEscrito_cese()
    {
        if (!isset($this->sescrito_cese) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sescrito_cese;
    }

    /**
     * Establece el valor del atributo sescrito_cese de Profesor
     *
     * @param string sescrito_cese='' optional
     */
    function setEscrito_cese($sescrito_cese = '')
    {
        $this->sescrito_cese = $sescrito_cese;
    }

    /**
     * Recupera el atributo df_cese de Profesor
     *
     * @return web\DateTimeLocal df_cese
     */
    function getF_cese()
    {
        if (!isset($this->df_cese) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        if (empty($this->df_cese)) {
            return new web\NullDateTimeLocal();
        }
        $oConverter = new core\ConverterDate('date', $this->df_cese);
        return $oConverter->fromPg();
    }

    /**
     * Establece el valor del atributo df_cese de Profesor
     * Si df_cese es string, y convert=true se convierte usando el formato webDateTimeLocal->getFormat().
     * Si convert es false, df_cese debe ser un string en formato ISO (Y-m-d). Corresponde al pgstyle de la base de datos.
     *
     * @param date|string df_cese='' optional.
     * @param boolean convert=true optional. Si es false, df_cese debe ser un string en formato ISO (Y-m-d).
     */
    function setF_cese($df_cese = '', $convert = true)
    {
        if ($convert === true && !empty($df_cese)) {
            $oConverter = new core\ConverterDate('date', $df_cese);
            $this->df_cese = $oConverter->toPg();
        } else {
            $this->df_cese = $df_cese;
        }
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oProfesorSet = new core\Set();
        $oProfesorSet->add($this->getDatosId_departamento());
        $oProfesorSet->add($this->getDatosEscrito_nombramiento());
        $oProfesorSet->add($this->getDatosF_nombramiento());
        $oProfesorSet->add($this->getDatosId_tipo_profesor());
        $oProfesorSet->add($this->getDatosEscrito_cese());
        $oProfesorSet->add($this->getDatosF_cese());
        return $oProfesorSet->getTot();
    }

    /**
     * Recupera les propietats de l'atribut iid_departamento de Profesor
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosId_departamento()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_departamento'));
        $oDatosCampo->setEtiqueta(_("departamento"));
        $oDatosCampo->setTipo('opciones');
        $oDatosCampo->setArgument('asignaturas\model\entity\Departamento'); // nombre del objeto relacionado
        $oDatosCampo->setArgument2('getDepartamento'); // método para obtener el valor a mostrar del objeto relacionado.
        $oDatosCampo->setArgument3('getListaDepartamentos'); // método con que crear la lista de opciones del Gestor objeto relacionado.
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sescrito_nombramiento de Profesor
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosEscrito_nombramiento()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'escrito_nombramiento'));
        $oDatosCampo->setEtiqueta(_("escrito de nombramiento"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(30);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut df_nombramiento de Profesor
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosF_nombramiento()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'f_nombramiento'));
        $oDatosCampo->setEtiqueta(_("fecha de nombramiento"));
        $oDatosCampo->setTipo('fecha');
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iid_tipo_profesor de Profesor
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosId_tipo_profesor()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_tipo_profesor'));
        $oDatosCampo->setEtiqueta(_("id_tipo_profesor"));
        $oDatosCampo->setTipo('opciones');
        $oDatosCampo->setArgument('profesores\model\entity\ProfesorTipo'); // nombre del objeto relacionado
        $oDatosCampo->setArgument2('getTipo_profesor'); // método para obtener el valor a mostrar del objeto relacionado.
        $oDatosCampo->setArgument3('getListaProfesorTipos'); // método con que crear la lista de opciones del Gestor objeto relacionado.
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sescrito_cese de Profesor
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosEscrito_cese()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'escrito_cese'));
        $oDatosCampo->setEtiqueta(_("escrito de cese"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(30);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut df_cese de Profesor
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosF_cese()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'f_cese'));
        $oDatosCampo->setEtiqueta(_("fecha de cese"));
        $oDatosCampo->setTipo('fecha');
        return $oDatosCampo;
    }

}