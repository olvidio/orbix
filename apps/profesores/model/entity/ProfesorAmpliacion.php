<?php

namespace profesores\model\entity;

use core\ClasePropiedades;
use core\ConverterDate;
use core\DatosCampo;
use core\Set;
use web\DateTimeLocal;
use web\NullDateTimeLocal;

/**
 * Fitxer amb la Classe que accedeix a la taula d_profesor_ampliacion
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 08/04/2014
 */

/**
 * Clase que implementa la entidad d_profesor_ampliacion
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 08/04/2014
 */
class ProfesorAmpliacion extends ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de ProfesorAmpliacion
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de ProfesorAmpliacion
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
     * Id_item de ProfesorAmpliacion
     *
     * @var integer
     */
    private $iid_item;
    /**
     * Id_nom de ProfesorAmpliacion
     *
     * @var integer
     */
    private $iid_nom;
    /**
     * Id_asignatura de ProfesorAmpliacion
     *
     * @var integer
     */
    private $iid_asignatura;
    /**
     * Escrito_nombramiento de ProfesorAmpliacion
     *
     * @var string
     */
    private $sescrito_nombramiento;
    /**
     * F_nombramiento de ProfesorAmpliacion
     *
     * @varDateTimeLocal
     */
    private $df_nombramiento;
    /**
     * Escrito_cese de ProfesorAmpliacion
     *
     * @var string
     */
    private $sescrito_cese;
    /**
     * F_cese de ProfesorAmpliacion
     *
     * @varDateTimeLocal
     */
    private $df_cese;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * oDbl de ProfesorAmpliacion
     *
     * @var object
     */
    protected $oDbl;
    /**
     * NomTabla de ProfesorAmpliacion
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
        $this->setNomTabla('d_profesor_ampliacion');
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
        $aDades['escrito_nombramiento'] = $this->sescrito_nombramiento;
        $aDades['f_nombramiento'] = $this->df_nombramiento;
        $aDades['escrito_cese'] = $this->sescrito_cese;
        $aDades['f_cese'] = $this->df_cese;
        array_walk($aDades, 'core\poner_null');

        if ($bInsert === false) {
            //UPDATE
            $update = "
					id_asignatura            = :id_asignatura,
					escrito_nombramiento     = :escrito_nombramiento,
					f_nombramiento           = :f_nombramiento,
					escrito_cese             = :escrito_cese,
					f_cese                   = :f_cese";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item=$this->iid_item")) === false) {
                $sClauError = 'ProfesorAmpliacion.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'ProfesorAmpliacion.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        } else {
            // INSERT
            array_unshift($aDades, $this->iid_nom);
            $campos = "(id_nom,id_asignatura,escrito_nombramiento,f_nombramiento,escrito_cese,f_cese)";
            $valores = "(:id_nom,:id_asignatura,:escrito_nombramiento,:f_nombramiento,:escrito_cese,:f_cese)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'ProfesorAmpliacion.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'ProfesorAmpliacion.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
            $this->iid_item = $oDbl->lastInsertId('d_profesor_ampliacion_id_item_seq');
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
                $sClauError = 'ProfesorAmpliacion.carregar';
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
            $sClauError = 'ProfesorAmpliacion.eliminar';
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
        if (array_key_exists('id_item', $aDades)) $this->setId_item($aDades['id_item']);
        if (array_key_exists('id_nom', $aDades)) $this->setId_nom($aDades['id_nom']);
        if (array_key_exists('id_asignatura', $aDades)) $this->setId_asignatura($aDades['id_asignatura']);
        if (array_key_exists('escrito_nombramiento', $aDades)) $this->setEscrito_nombramiento($aDades['escrito_nombramiento']);
        if (array_key_exists('f_nombramiento', $aDades)) $this->setF_nombramiento($aDades['f_nombramiento'], $convert);
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
        $this->setId_asignatura('');
        $this->setEscrito_nombramiento('');
        $this->setF_nombramiento('');
        $this->setEscrito_cese('');
        $this->setF_cese('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de ProfesorAmpliacion en un array
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
     * Recupera la clave primaria de ProfesorAmpliacion en un array
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
     * Establece la clave primaria de ProfesorAmpliacion en un array
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
     * Recupera el atributo iid_item de ProfesorAmpliacion
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
     * Establece el valor del atributo iid_item de ProfesorAmpliacion
     *
     * @param integer iid_item
     */
    function setId_item($iid_item)
    {
        $this->iid_item = $iid_item;
    }

    /**
     * Recupera el atributo iid_nom de ProfesorAmpliacion
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
     * Establece el valor del atributo iid_nom de ProfesorAmpliacion
     *
     * @param integer iid_nom
     */
    function setId_nom($iid_nom)
    {
        $this->iid_nom = $iid_nom;
    }

    /**
     * Recupera el atributo iid_asignatura de ProfesorAmpliacion
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
     * Establece el valor del atributo iid_asignatura de ProfesorAmpliacion
     *
     * @param integer iid_asignatura='' optional
     */
    function setId_asignatura($iid_asignatura = '')
    {
        $this->iid_asignatura = $iid_asignatura;
    }

    /**
     * Recupera el atributo sescrito_nombramiento de ProfesorAmpliacion
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
     * Establece el valor del atributo sescrito_nombramiento de ProfesorAmpliacion
     *
     * @param string sescrito_nombramiento='' optional
     */
    function setEscrito_nombramiento($sescrito_nombramiento = '')
    {
        $this->sescrito_nombramiento = $sescrito_nombramiento;
    }

    /**
     * Recupera el atributo df_nombramiento de ProfesorAmpliacion
     *
     * @returnDateTimeLocal df_nombramiento
     */
    function getF_nombramiento()
    {
        if (!isset($this->df_nombramiento) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        if (empty($this->df_nombramiento)) {
            return new NullDateTimeLocal();
        }
        $oConverter = new ConverterDate('date', $this->df_nombramiento);
        return $oConverter->fromPg();
    }

    /**
     * Establece el valor del atributo df_nombramiento de ProfesorAmpliacion
     * Si df_nombramiento es string, y convert=true se convierte usando el formato webDateTimeLocal->getFormat().
     * Si convert es false, df_nombramiento debe ser un string en formato ISO (Y-m-d). Corresponde al pgstyle de la base de datos.
     *
     * @param DateTimeLocal|string df_nombramiento='' optional.
     * @param boolean convert=true optional. Si es false, df_nombramiento debe ser un string en formato ISO (Y-m-d).
     */
    function setF_nombramiento($df_nombramiento = '', $convert = true)
    {
        if ($convert === true && !empty($df_nombramiento)) {
            $oConverter = new ConverterDate('date', $df_nombramiento);
            $this->df_nombramiento = $oConverter->toPg();
        } else {
            $this->df_nombramiento = $df_nombramiento;
        }
    }

    /**
     * Recupera el atributo sescrito_cese de ProfesorAmpliacion
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
     * Establece el valor del atributo sescrito_cese de ProfesorAmpliacion
     *
     * @param string sescrito_cese='' optional
     */
    function setEscrito_cese($sescrito_cese = '')
    {
        $this->sescrito_cese = $sescrito_cese;
    }

    /**
     * Recupera el atributo df_cese de ProfesorAmpliacion
     *
     * @returnDateTimeLocal df_cese
     */
    function getF_cese()
    {
        if (!isset($this->df_cese) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        if (empty($this->df_cese)) {
            return new NullDateTimeLocal();
        }
        $oConverter = new ConverterDate('date', $this->df_cese);
        return $oConverter->fromPg();
    }

    /**
     * Establece el valor del atributo df_cese de ProfesorAmpliacion
     * Si df_cese es string, y convert=true se convierte usando el formato webDateTimeLocal->getFormat().
     * Si convert es false, df_cese debe ser un string en formato ISO (Y-m-d). Corresponde al pgstyle de la base de datos.
     *
     * @param DateTimeLocal|string df_cese='' optional.
     * @param boolean convert=true optional. Si es false, df_cese debe ser un string en formato ISO (Y-m-d).
     */
    function setF_cese($df_cese = '', $convert = true)
    {
        if ($convert === true && !empty($df_cese)) {
            $oConverter = new ConverterDate('date', $df_cese);
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
        $oProfesorAmpliacionSet = new Set();

        $oProfesorAmpliacionSet->add($this->getDatosId_asignatura());
        $oProfesorAmpliacionSet->add($this->getDatosEscrito_nombramiento());
        $oProfesorAmpliacionSet->add($this->getDatosF_nombramiento());
        $oProfesorAmpliacionSet->add($this->getDatosEscrito_cese());
        $oProfesorAmpliacionSet->add($this->getDatosF_cese());
        return $oProfesorAmpliacionSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut iid_asignatura de ProfesorAmpliacion
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
     * Recupera les propietats de l'atribut sescrito_nombramiento de ProfesorAmpliacion
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosEscrito_nombramiento()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'escrito_nombramiento'));
        $oDatosCampo->setEtiqueta(_("escrito de nombramiento"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(30);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut df_nombramiento de ProfesorAmpliacion
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosF_nombramiento()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'f_nombramiento'));
        $oDatosCampo->setEtiqueta(_("fecha de nombramiento"));
        $oDatosCampo->setTipo('fecha');
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sescrito_cese de ProfesorAmpliacion
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosEscrito_cese()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'escrito_cese'));
        $oDatosCampo->setEtiqueta(_("escrito de cese"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(30);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut df_cese de ProfesorAmpliacion
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosF_cese()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'f_cese'));
        $oDatosCampo->setEtiqueta(_("fecha de cese"));
        $oDatosCampo->setTipo('fecha');
        return $oDatosCampo;
    }
}