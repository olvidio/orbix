<?php

namespace profesores\model\entity;

use core\ClasePropiedades;
use core\ConverterDate;
use core\DatosCampo;
use core\Set;
use web\DateTimeLocal;
use web\NullDateTimeLocal;
use function core\is_true;

/**
 * Fitxer amb la Classe que accedeix a la taula d_publicaciones
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 04/09/2015
 */

/**
 * Clase que implementa la entidad d_publicaciones
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 04/09/2015
 */
class ProfesorPublicacion extends ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de ProfesorPublicacion
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de ProfesorPublicacion
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
     * Id_item de ProfesorPublicacion
     *
     * @var integer
     */
    private $iid_item;
    /**
     * Id_nom de ProfesorPublicacion
     *
     * @var integer
     */
    private $iid_nom;
    /**
     * Tipo_publicacion de ProfesorPublicacion
     *
     * @var string
     */
    private $stipo_publicacion;
    /**
     * Titulo de ProfesorPublicacion
     *
     * @var string
     */
    private $stitulo;
    /**
     * Editorial de ProfesorPublicacion
     *
     * @var string
     */
    private $seditorial;
    /**
     * Coleccion de ProfesorPublicacion
     *
     * @var string
     */
    private $scoleccion;
    /**
     * F_publicacion de ProfesorPublicacion
     *
     * @var DateTimeLocal
     */
    private $df_publicacion;
    /**
     * Pendiente de ProfesorPublicacion
     *
     * @var boolean
     */
    private $bpendiente;
    /**
     * Referencia de ProfesorPublicacion
     *
     * @var string
     */
    private $sreferencia;
    /**
     * Lugar de ProfesorPublicacion
     *
     * @var string
     */
    private $slugar;
    /**
     * Observ de ProfesorPublicacion
     *
     * @var string
     */
    private $sobserv;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * oDbl de ProfesorPublicacion
     *
     * @var object
     */
    protected $oDbl;
    /**
     * NomTabla de ProfesorPublicacion
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
        $this->setNomTabla('d_publicaciones');
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
        $aDades['tipo_publicacion'] = $this->stipo_publicacion;
        $aDades['titulo'] = $this->stitulo;
        $aDades['editorial'] = $this->seditorial;
        $aDades['coleccion'] = $this->scoleccion;
        $aDades['f_publicacion'] = $this->df_publicacion;
        $aDades['pendiente'] = $this->bpendiente;
        $aDades['referencia'] = $this->sreferencia;
        $aDades['lugar'] = $this->slugar;
        $aDades['observ'] = $this->sobserv;
        array_walk($aDades, 'core\poner_null');
        //para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (is_true($aDades['pendiente'])) {
            $aDades['pendiente'] = 'true';
        } else {
            $aDades['pendiente'] = 'false';
        }

        if ($bInsert === false) {
            //UPDATE
            $update = "
					tipo_publicacion         = :tipo_publicacion,
					titulo                   = :titulo,
					editorial                = :editorial,
					coleccion                = :coleccion,
					f_publicacion            = :f_publicacion,
					pendiente                = :pendiente,
					referencia               = :referencia,
					lugar                    = :lugar,
					observ                   = :observ";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item=$this->iid_item")) === false) {
                $sClauError = 'ProfesorPublicacion.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'ProfesorPublicacion.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        } else {
            // INSERT
            array_unshift($aDades, $this->iid_nom);
            $campos = "(id_nom,tipo_publicacion,titulo,editorial,coleccion,f_publicacion,pendiente,referencia,lugar,observ)";
            $valores = "(:id_nom,:tipo_publicacion,:titulo,:editorial,:coleccion,:f_publicacion,:pendiente,:referencia,:lugar,:observ)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'ProfesorPublicacion.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'ProfesorPublicacion.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
            $this->iid_item = $oDbl->lastInsertId('d_publicaciones_id_item_seq');
        }
        $this->setAllAttributes($aDades);
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
                $sClauError = 'ProfesorPublicacion.carregar';
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
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_item=$this->iid_item")) === false) {
            $sClauError = 'ProfesorPublicacion.eliminar';
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
    function setAllAttributes(array $aDades, $convert = FALSE)
    {
        if (!is_array($aDades)) return;
        if (array_key_exists('id_schema', $aDades)) $this->setId_schema($aDades['id_schema']);
        if (array_key_exists('id_item', $aDades)) $this->setId_item($aDades['id_item']);
        if (array_key_exists('id_nom', $aDades)) $this->setId_nom($aDades['id_nom']);
        if (array_key_exists('tipo_publicacion', $aDades)) $this->setTipo_publicacion($aDades['tipo_publicacion']);
        if (array_key_exists('titulo', $aDades)) $this->setTitulo($aDades['titulo']);
        if (array_key_exists('editorial', $aDades)) $this->setEditorial($aDades['editorial']);
        if (array_key_exists('coleccion', $aDades)) $this->setColeccion($aDades['coleccion']);
        if (array_key_exists('f_publicacion', $aDades)) $this->setF_publicacion($aDades['f_publicacion'], $convert);
        if (array_key_exists('pendiente', $aDades)) $this->setPendiente($aDades['pendiente']);
        if (array_key_exists('referencia', $aDades)) $this->setReferencia($aDades['referencia']);
        if (array_key_exists('lugar', $aDades)) $this->setLugar($aDades['lugar']);
        if (array_key_exists('observ', $aDades)) $this->setObserv($aDades['observ']);
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
        $this->setTipo_publicacion('');
        $this->setTitulo('');
        $this->setEditorial('');
        $this->setColeccion('');
        $this->setF_publicacion('');
        $this->setPendiente('');
        $this->setReferencia('');
        $this->setLugar('');
        $this->setObserv('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de ProfesorPublicacion en un array
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
     * Recupera la clave primaria de ProfesorPublicacion en un array
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
     * Establece la clave primaria de ProfesorPublicacion en un array
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
     * Recupera el atributo iid_item de ProfesorPublicacion
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
     * Establece el valor del atributo iid_item de ProfesorPublicacion
     *
     * @param integer iid_item
     */
    function setId_item($iid_item)
    {
        $this->iid_item = $iid_item;
    }

    /**
     * Recupera el atributo iid_nom de ProfesorPublicacion
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
     * Establece el valor del atributo iid_nom de ProfesorPublicacion
     *
     * @param integer iid_nom
     */
    function setId_nom($iid_nom)
    {
        $this->iid_nom = $iid_nom;
    }

    /**
     * Recupera el atributo stipo_publicacion de ProfesorPublicacion
     *
     * @return string stipo_publicacion
     */
    function getTipo_publicacion()
    {
        if (!isset($this->stipo_publicacion) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->stipo_publicacion;
    }

    /**
     * Establece el valor del atributo stipo_publicacion de ProfesorPublicacion
     *
     * @param string stipo_publicacion='' optional
     */
    function setTipo_publicacion($stipo_publicacion = '')
    {
        $this->stipo_publicacion = $stipo_publicacion;
    }

    /**
     * Recupera el atributo stitulo de ProfesorPublicacion
     *
     * @return string stitulo
     */
    function getTitulo()
    {
        if (!isset($this->stitulo) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->stitulo;
    }

    /**
     * Establece el valor del atributo stitulo de ProfesorPublicacion
     *
     * @param string stitulo='' optional
     */
    function setTitulo($stitulo = '')
    {
        $this->stitulo = $stitulo;
    }

    /**
     * Recupera el atributo seditorial de ProfesorPublicacion
     *
     * @return string seditorial
     */
    function getEditorial()
    {
        if (!isset($this->seditorial) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->seditorial;
    }

    /**
     * Establece el valor del atributo seditorial de ProfesorPublicacion
     *
     * @param string seditorial='' optional
     */
    function setEditorial($seditorial = '')
    {
        $this->seditorial = $seditorial;
    }

    /**
     * Recupera el atributo scoleccion de ProfesorPublicacion
     *
     * @return string scoleccion
     */
    function getColeccion()
    {
        if (!isset($this->scoleccion) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->scoleccion;
    }

    /**
     * Establece el valor del atributo scoleccion de ProfesorPublicacion
     *
     * @param string scoleccion='' optional
     */
    function setColeccion($scoleccion = '')
    {
        $this->scoleccion = $scoleccion;
    }

    /**
     * Recupera el atributo df_publicacion de ProfesorPublicacion
     *
     * @return DateTimeLocal|NullDateTimeLocal
     */
    function getF_publicacion()
    {
        if (!isset($this->df_publicacion) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        if (empty($this->df_publicacion)) {
            return new NullDateTimeLocal();
        }
        $oConverter = new ConverterDate('date', $this->df_publicacion);
        return $oConverter->fromPg();
    }

    /**
     * Establece el valor del atributo df_publicacion de ProfesorPublicacion
     * Si df_publicacion es string, y convert=true se convierte usando el formato webDateTimeLocal->getFormat().
     * Si convert es false, df_publicacion debe ser un string en formato ISO (Y-m-d). Corresponde al pgstyle de la base de datos.
     *
     * @param DateTimeLocal|string df_publicacion='' optional.
     * @param boolean convert=true optional. Si es false, df_publicacion debe ser un string en formato ISO (Y-m-d).
     */
    function setF_publicacion($df_publicacion = '', $convert = true)
    {
        if ($convert === true && !empty($df_publicacion)) {
            $oConverter = new ConverterDate('date', $df_publicacion);
            $this->df_publicacion = $oConverter->toPg();
        } else {
            $this->df_publicacion = $df_publicacion;
        }
    }

    /**
     * Recupera el atributo bpendiente de ProfesorPublicacion
     *
     * @return boolean bpendiente
     */
    function getPendiente()
    {
        if (!isset($this->bpendiente) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->bpendiente;
    }

    /**
     * Establece el valor del atributo bpendiente de ProfesorPublicacion
     *
     * @param boolean bpendiente='f' optional
     */
    function setPendiente($bpendiente = 'f')
    {
        $this->bpendiente = $bpendiente;
    }

    /**
     * Recupera el atributo sreferencia de ProfesorPublicacion
     *
     * @return string sreferencia
     */
    function getReferencia()
    {
        if (!isset($this->sreferencia) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sreferencia;
    }

    /**
     * Establece el valor del atributo sreferencia de ProfesorPublicacion
     *
     * @param string sreferencia='' optional
     */
    function setReferencia($sreferencia = '')
    {
        $this->sreferencia = $sreferencia;
    }

    /**
     * Recupera el atributo slugar de ProfesorPublicacion
     *
     * @return string slugar
     */
    function getLugar()
    {
        if (!isset($this->slugar) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->slugar;
    }

    /**
     * Establece el valor del atributo slugar de ProfesorPublicacion
     *
     * @param string slugar='' optional
     */
    function setLugar($slugar = '')
    {
        $this->slugar = $slugar;
    }

    /**
     * Recupera el atributo sobserv de ProfesorPublicacion
     *
     * @return string sobserv
     */
    function getObserv()
    {
        if (!isset($this->sobserv) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sobserv;
    }

    /**
     * Establece el valor del atributo sobserv de ProfesorPublicacion
     *
     * @param string sobserv='' optional
     */
    function setObserv($sobserv = '')
    {
        $this->sobserv = $sobserv;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oProfesorPublicacionSet = new Set();

        $oProfesorPublicacionSet->add($this->getDatosTipo_publicacion());
        $oProfesorPublicacionSet->add($this->getDatosTitulo());
        $oProfesorPublicacionSet->add($this->getDatosEditorial());
        $oProfesorPublicacionSet->add($this->getDatosColeccion());
        $oProfesorPublicacionSet->add($this->getDatosF_publicacion());
        $oProfesorPublicacionSet->add($this->getDatosPendiente());
        $oProfesorPublicacionSet->add($this->getDatosReferencia());
        $oProfesorPublicacionSet->add($this->getDatosLugar());
        $oProfesorPublicacionSet->add($this->getDatosObserv());
        return $oProfesorPublicacionSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut stipo_publicacion de ProfesorPublicacion
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosTipo_publicacion()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'tipo_publicacion'));
        $oDatosCampo->setEtiqueta(_("tipo de publicación"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(15);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut stitulo de ProfesorPublicacion
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosTitulo()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'titulo'));
        $oDatosCampo->setEtiqueta(_("título"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(100);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut seditorial de ProfesorPublicacion
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosEditorial()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'editorial'));
        $oDatosCampo->setEtiqueta(_("editorial"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(50);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut scoleccion de ProfesorPublicacion
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosColeccion()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'coleccion'));
        $oDatosCampo->setEtiqueta(_("colección"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(50);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut df_publicacion de ProfesorPublicacion
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosF_publicacion()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'f_publicacion'));
        $oDatosCampo->setEtiqueta(_("fecha de la publicación"));
        $oDatosCampo->setTipo('fecha');
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut bpendiente de ProfesorPublicacion
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosPendiente()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'pendiente'));
        $oDatosCampo->setEtiqueta(_("pendiente"));
        $oDatosCampo->setTipo('check');
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sreferencia de ProfesorPublicacion
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosReferencia()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'referencia'));
        $oDatosCampo->setEtiqueta(_("referencia"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(50);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut slugar de ProfesorPublicacion
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosLugar()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'lugar'));
        $oDatosCampo->setEtiqueta(_("lugar"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(100);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sobserv de ProfesorPublicacion
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosObserv()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'observ'));
        $oDatosCampo->setEtiqueta(_("observaciones"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(100);
        return $oDatosCampo;
    }
}