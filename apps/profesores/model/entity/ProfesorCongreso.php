<?php

namespace profesores\model\entity;

use core\ClasePropiedades;
use core\ConverterDate;
use core\DatosCampo;
use core\Set;
use web\DateTimeLocal;
use web\NullDateTimeLocal;

/**
 * Fitxer amb la Classe que accedeix a la taula d_congresos
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 28/10/2014
 */

/**
 * Clase que implementa la entidad d_congresos
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 28/10/2014
 */
class ProfesorCongreso extends ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de ProfesorCongreso
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de ProfesorCongreso
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
     * Id_schema de ProfesorCongreso
     *
     * @var integer
     */
    private $iid_schema;
    /**
     * Id_item de ProfesorCongreso
     *
     * @var integer
     */
    private $iid_item;
    /**
     * Id_nom de ProfesorCongreso
     *
     * @var integer
     */
    private $iid_nom;
    /**
     * ProfesorCongreso de ProfesorCongreso
     *
     * @var string
     */
    private $scongreso;
    /**
     * Lugar de ProfesorCongreso
     *
     * @var string
     */
    private $slugar;
    /**
     * F_ini de ProfesorCongreso
     *
     * @varDateTimeLocal
     */
    private $df_ini;
    /**
     * F_fin de ProfesorCongreso
     *
     * @varDateTimeLocal
     */
    private $df_fin;
    /**
     * Organiza de ProfesorCongreso
     *
     * @var string
     */
    private $sorganiza;
    /**
     * Tipo de ProfesorCongreso
     *
     * @var integer
     */
    private $itipo;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * oDbl de ProfesorCongreso
     *
     * @var object
     */
    protected $oDbl;
    /**
     * NomTabla de ProfesorCongreso
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
     * @param integer|array iid_item
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
        $this->setNomTabla('d_congresos');
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
        $aDades['congreso'] = $this->scongreso;
        $aDades['lugar'] = $this->slugar;
        $aDades['f_ini'] = $this->df_ini;
        $aDades['f_fin'] = $this->df_fin;
        $aDades['organiza'] = $this->sorganiza;
        $aDades['tipo'] = $this->itipo;
        array_walk($aDades, 'core\poner_null');

        if ($bInsert === false) {
            //UPDATE
            $update = "
					congreso                 = :congreso,
					lugar                    = :lugar,
					f_ini                    = :f_ini,
					f_fin                    = :f_fin,
					organiza                 = :organiza,
					tipo                     = :tipo";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item=$this->iid_item")) === false) {
                $sClauError = 'ProfesorCongreso.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'ProfesorCongreso.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        } else {
            // INSERT
            array_unshift($aDades, $this->iid_nom);
            $campos = "(id_nom,congreso,lugar,f_ini,f_fin,organiza,tipo)";
            $valores = "(:id_nom,:congreso,:lugar,:f_ini,:f_fin,:organiza,:tipo)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'ProfesorCongreso.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'ProfesorCongreso.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
            $this->iid_item = $oDbl->lastInsertId('d_congresos_id_item_seq');
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
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE  id_item=$this->iid_item")) === false) {
                $sClauError = 'ProfesorCongreso.carregar';
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
        if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE  id_item=$this->iid_item")) === false) {
            $sClauError = 'ProfesorCongreso.eliminar';
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
        if (array_key_exists('congreso', $aDades)) $this->setCongreso($aDades['congreso']);
        if (array_key_exists('lugar', $aDades)) $this->setLugar($aDades['lugar']);
        if (array_key_exists('f_ini', $aDades)) $this->setF_ini($aDades['f_ini'], $convert);
        if (array_key_exists('f_fin', $aDades)) $this->setF_fin($aDades['f_fin'], $convert);
        if (array_key_exists('organiza', $aDades)) $this->setOrganiza($aDades['organiza']);
        if (array_key_exists('tipo', $aDades)) $this->setTipo($aDades['tipo']);
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
        $this->setCongreso('');
        $this->setLugar('');
        $this->setF_ini('');
        $this->setF_fin('');
        $this->setOrganiza('');
        $this->setTipo('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de ProfesorCongreso en un array
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
     * Recupera la clave primaria de ProfesorCongreso en un array
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
     * Establece la clave primaria de ProfesorCongreso en un array
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
     * Recupera el atributo iid_item de ProfesorCongreso
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
     * Establece el valor del atributo iid_item de ProfesorCongreso
     *
     * @param integer iid_item
     */
    function setId_item($iid_item)
    {
        $this->iid_item = $iid_item;
    }

    /**
     * Recupera el atributo iid_nom de ProfesorCongreso
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
     * Establece el valor del atributo iid_nom de ProfesorCongreso
     *
     * @param integer iid_nom='' optional
     */
    function setId_nom($iid_nom = '')
    {
        $this->iid_nom = $iid_nom;
    }

    /**
     * Recupera el atributo scongreso de ProfesorCongreso
     *
     * @return string scongreso
     */
    function getCongreso()
    {
        if (!isset($this->scongreso) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->scongreso;
    }

    /**
     * Establece el valor del atributo scongreso de ProfesorCongreso
     *
     * @param string scongreso='' optional
     */
    function setCongreso($scongreso = '')
    {
        $this->scongreso = $scongreso;
    }

    /**
     * Recupera el atributo slugar de ProfesorCongreso
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
     * Establece el valor del atributo slugar de ProfesorCongreso
     *
     * @param string slugar='' optional
     */
    function setLugar($slugar = '')
    {
        $this->slugar = $slugar;
    }

    /**
     * Recupera el atributo df_ini de ProfesorCongreso
     *
     * @returnDateTimeLocal df_ini
     */
    function getF_ini()
    {
        if (!isset($this->df_ini) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        if (empty($this->df_ini)) {
            return new NullDateTimeLocal();
        }
        $oConverter = new ConverterDate('date', $this->df_ini);
        return $oConverter->fromPg();
    }

    /**
     * Establece el valor del atributo df_ini de ProfesorCongreso
     * Si df_ini es string, y convert=true se convierte usando el formato webDateTimeLocal->getFormat().
     * Si convert es false, df_ini debe ser un string en formato ISO (Y-m-d). Corresponde al pgstyle de la base de datos.
     *
     * @param DateTimeLocal|string df_ini='' optional.
     * @param boolean convert=true optional. Si es false, df_ini debe ser un string en formato ISO (Y-m-d).
     */
    function setF_ini($df_ini = '', $convert = true)
    {
        if ($convert === true && !empty($df_ini)) {
            $oConverter = new ConverterDate('date', $df_ini);
            $this->df_ini = $oConverter->toPg();
        } else {
            $this->df_ini = $df_ini;
        }
    }

    /**
     * Recupera el atributo df_fin de ProfesorCongreso
     *
     * @returnDateTimeLocal df_fin
     */
    function getF_fin()
    {
        if (!isset($this->df_fin) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        if (empty($this->df_fin)) {
            return new NullDateTimeLocal();
        }
        $oConverter = new ConverterDate('date', $this->df_fin);
        return $oConverter->fromPg();
    }

    /**
     * Establece el valor del atributo df_fin de ProfesorCongreso
     * Si df_fin es string, y convert=true se convierte usando el formato webDateTimeLocal->getFormat().
     * Si convert es false, df_fin debe ser un string en formato ISO (Y-m-d). Corresponde al pgstyle de la base de datos.
     *
     * @param DateTimeLocal|string df_fin='' optional.
     * @param boolean convert=true optional. Si es false, df_fin debe ser un string en formato ISO (Y-m-d).
     */
    function setF_fin($df_fin = '', $convert = true)
    {
        if ($convert === true && !empty($df_fin)) {
            $oConverter = new ConverterDate('date', $df_fin);
            $this->df_fin = $oConverter->toPg();
        } else {
            $this->df_fin = $df_fin;
        }
    }

    /**
     * Recupera el atributo sorganiza de ProfesorCongreso
     *
     * @return string sorganiza
     */
    function getOrganiza()
    {
        if (!isset($this->sorganiza) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sorganiza;
    }

    /**
     * Establece el valor del atributo sorganiza de ProfesorCongreso
     *
     * @param string sorganiza='' optional
     */
    function setOrganiza($sorganiza = '')
    {
        $this->sorganiza = $sorganiza;
    }

    /**
     * Recupera el atributo itipo de ProfesorCongreso
     *
     * @return integer itipo
     */
    function getTipo()
    {
        if (!isset($this->itipo) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->itipo;
    }

    /**
     * Establece el valor del atributo itipo de ProfesorCongreso
     *
     * @param integer itipo='' optional
     */
    function setTipo($itipo = '')
    {
        $this->itipo = $itipo;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oProfesorCongresoSet = new Set();

        $oProfesorCongresoSet->add($this->getDatosCongreso());
        $oProfesorCongresoSet->add($this->getDatosLugar());
        $oProfesorCongresoSet->add($this->getDatosF_ini());
        $oProfesorCongresoSet->add($this->getDatosF_fin());
        $oProfesorCongresoSet->add($this->getDatosOrganiza());
        $oProfesorCongresoSet->add($this->getDatosTipo());
        return $oProfesorCongresoSet->getTot();
    }

    /**
     * Recupera les propietats de l'atribut scongreso de ProfesorCongreso
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosCongreso()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'congreso'));
        $oDatosCampo->setEtiqueta(_("congreso"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(80);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut slugar de ProfesorCongreso
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
        $oDatosCampo->setArgument(30);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut df_ini de ProfesorCongreso
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosF_ini()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'f_ini'));
        $oDatosCampo->setEtiqueta(_("fecha inicio"));
        $oDatosCampo->setTipo('fecha');
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut df_fin de ProfesorCongreso
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosF_fin()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'f_fin'));
        $oDatosCampo->setEtiqueta(_("fecha fin"));
        $oDatosCampo->setTipo('fecha');
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sorganiza de ProfesorCongreso
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosOrganiza()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'organiza'));
        $oDatosCampo->setEtiqueta(_("organiza"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(50);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut itipo de ProfesorCongreso
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
        $oDatosCampo->setLista([1 => _("cv"),
            2 => _("congreso"),
            3 => _("reunión"),
            4 => _("claustro"),
        ]);
        return $oDatosCampo;
    }
}
