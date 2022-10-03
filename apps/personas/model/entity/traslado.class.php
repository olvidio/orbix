<?php

namespace personas\model\entity;

use core;
use web;

/**
 * Fitxer amb la Classe que accedeix a la taula d_traslados
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 12/05/2014
 */

/**
 * Clase que implementa la entidad d_traslados
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 12/05/2014
 */
class Traslado extends core\ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de Traslado
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de Traslado
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
     * Id_item de Traslado
     *
     * @var integer
     */
    private $iid_item;
    /**
     * Id_nom de Traslado
     *
     * @var integer
     */
    private $iid_nom;
    /**
     * F_traslado de Traslado
     *
     * @var web\DateTimeLocal
     */
    private $df_traslado;
    /**
     * Tipo_cmb de Traslado
     *
     * @var string
     */
    private $stipo_cmb;
    /**
     * Id_ctr_origen de Traslado
     *
     * @var integer
     */
    private $iid_ctr_origen;
    /**
     * Ctr_origen de Traslado
     *
     * @var string
     */
    private $sctr_origen;
    /**
     * Id_ctr_destino de Traslado
     *
     * @var integer
     */
    private $iid_ctr_destino;
    /**
     * Ctr_destino de Traslado
     *
     * @var string
     */
    private $sctr_destino;
    /**
     * Observ de Traslado
     *
     * @var string
     */
    private $sobserv;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * oDbl de Traslado
     *
     * @var object
     */
    protected $oDbl;
    /**
     * NomTabla de Traslado
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
                if (($nom_id == 'id_item') && $val_id !== '') $this->iid_item = (int)$val_id; // evitem SQL injection fent cast a integer
                if (($nom_id == 'id_nom') && $val_id !== '') $this->iid_nom = (int)$val_id; // evitem SQL injection fent cast a integer
            }
        }
        $this->setoDbl($oDbl);
        $this->setNomTabla('d_traslados');
    }

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Desa els atributs de l'objecte a la base de dades.
     * Si no hi ha el registre, fa el insert, si hi es fa el update.
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
        $aDades['f_traslado'] = $this->df_traslado;
        $aDades['tipo_cmb'] = $this->stipo_cmb;
        $aDades['id_ctr_origen'] = $this->iid_ctr_origen;
        $aDades['ctr_origen'] = $this->sctr_origen;
        $aDades['id_ctr_destino'] = $this->iid_ctr_destino;
        $aDades['ctr_destino'] = $this->sctr_destino;
        $aDades['observ'] = $this->sobserv;
        array_walk($aDades, 'core\poner_null');

        if ($bInsert === false) {
            //UPDATE
            $update = "
					f_traslado               = :f_traslado,
					tipo_cmb                 = :tipo_cmb,
					id_ctr_origen            = :id_ctr_origen,
					ctr_origen               = :ctr_origen,
					id_ctr_destino           = :id_ctr_destino,
					ctr_destino              = :ctr_destino,
					observ                   = :observ";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item=$this->iid_item")) === false) {
                $sClauError = 'Traslado.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'Traslado.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        } else {
            // INSERT
            array_unshift($aDades, $this->iid_nom);
            $campos = "(id_nom,f_traslado,tipo_cmb,id_ctr_origen,ctr_origen,id_ctr_destino,ctr_destino,observ)";
            $valores = "(:id_nom,:f_traslado,:tipo_cmb,:id_ctr_origen,:ctr_origen,:id_ctr_destino,:ctr_destino,:observ)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'Traslado.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'Traslado.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
            $this->id_item = $oDbl->lastInsertId('d_traslados_id_item_seq');
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
        if (!empty($this->iid_item) && is_numeric($this->iid_item)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_item=$this->iid_item")) === false) {
                $sClauError = 'Traslado.carregar';
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
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_item=$this->iid_item")) === false) {
            $sClauError = 'Traslado.eliminar';
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
        if (array_key_exists('f_traslado', $aDades)) $this->setF_traslado($aDades['f_traslado'], $convert);
        if (array_key_exists('tipo_cmb', $aDades)) $this->setTipo_cmb($aDades['tipo_cmb']);
        if (array_key_exists('id_ctr_origen', $aDades)) $this->setId_ctr_origen($aDades['id_ctr_origen']);
        if (array_key_exists('ctr_origen', $aDades)) $this->setCtr_origen($aDades['ctr_origen']);
        if (array_key_exists('id_ctr_destino', $aDades)) $this->setId_ctr_destino($aDades['id_ctr_destino']);
        if (array_key_exists('ctr_destino', $aDades)) $this->setCtr_destino($aDades['ctr_destino']);
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
        $this->setF_traslado('');
        $this->setTipo_cmb('');
        $this->setId_ctr_origen('');
        $this->setCtr_origen('');
        $this->setId_ctr_destino('');
        $this->setCtr_destino('');
        $this->setObserv('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de Traslado en un array
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
     * Recupera la clave primaria de Traslado en un array
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
     * Establece la clave primaria de Traslado en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'id_item') && $val_id !== '') $this->iid_item = (int)$val_id; // evitem SQL injection fent cast a integer
            }
        }
    }

    /**
     * Recupera el atributo iid_item de Traslado
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
     * Establece el valor del atributo iid_item de Traslado
     *
     * @param integer iid_item
     */
    function setId_item($iid_item)
    {
        $this->iid_item = $iid_item;
    }

    /**
     * Recupera el atributo iid_nom de Traslado
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
     * Establece el valor del atributo iid_nom de Traslado
     *
     * @param integer iid_nom
     */
    function setId_nom($iid_nom)
    {
        $this->iid_nom = $iid_nom;
    }

    /**
     * Recupera el atributo df_traslado de Traslado
     *
     * @return web\DateTimeLocal df_traslado
     */
    function getF_traslado()
    {
        if (!isset($this->df_traslado) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        if (empty($this->df_traslado)) {
            return new web\NullDateTimeLocal();
        }
        $oConverter = new core\Converter('date', $this->df_traslado);
        return $oConverter->fromPg();
    }

    /**
     * Establece el valor del atributo df_traslado de Traslado
     * Si df_traslado es string, y convert=true se convierte usando el formato webDateTimeLocal->getFormat().
     * Si convert es false, df_traslado debe ser un string en formato ISO (Y-m-d). Corresponde al pgstyle de la base de datos.
     *
     * @param date|string df_traslado='' optional.
     * @param boolean convert=true optional. Si es false, df_traslado debe ser un string en formato ISO (Y-m-d).
     */
    function setF_traslado($df_traslado = '', $convert = true)
    {
        if ($convert === true && !empty($df_traslado)) {
            $oConverter = new core\Converter('date', $df_traslado);
            $this->df_traslado = $oConverter->toPg();
        } else {
            $this->df_traslado = $df_traslado;
        }
    }

    /**
     * Recupera el atributo stipo_cmb de Traslado
     *
     * @return string stipo_cmb
     */
    function getTipo_cmb()
    {
        if (!isset($this->stipo_cmb) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->stipo_cmb;
    }

    /**
     * Establece el valor del atributo stipo_cmb de Traslado
     *
     * @param string stipo_cmb='' optional
     */
    function setTipo_cmb($stipo_cmb = '')
    {
        $this->stipo_cmb = $stipo_cmb;
    }

    /**
     * Recupera el atributo iid_ctr_origen de Traslado
     *
     * @return integer iid_ctr_origen
     */
    function getId_ctr_origen()
    {
        if (!isset($this->iid_ctr_origen) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_ctr_origen;
    }

    /**
     * Establece el valor del atributo iid_ctr_origen de Traslado
     *
     * @param integer iid_ctr_origen='' optional
     */
    function setId_ctr_origen($iid_ctr_origen = '')
    {
        $this->iid_ctr_origen = $iid_ctr_origen;
    }

    /**
     * Recupera el atributo sctr_origen de Traslado
     *
     * @return string sctr_origen
     */
    function getCtr_origen()
    {
        if (!isset($this->sctr_origen) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sctr_origen;
    }

    /**
     * Establece el valor del atributo sctr_origen de Traslado
     *
     * @param string sctr_origen='' optional
     */
    function setCtr_origen($sctr_origen = '')
    {
        $this->sctr_origen = $sctr_origen;
    }

    /**
     * Recupera el atributo iid_ctr_destino de Traslado
     *
     * @return integer iid_ctr_destino
     */
    function getId_ctr_destino()
    {
        if (!isset($this->iid_ctr_destino) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_ctr_destino;
    }

    /**
     * Establece el valor del atributo iid_ctr_destino de Traslado
     *
     * @param integer iid_ctr_destino='' optional
     */
    function setId_ctr_destino($iid_ctr_destino = '')
    {
        $this->iid_ctr_destino = $iid_ctr_destino;
    }

    /**
     * Recupera el atributo sctr_destino de Traslado
     *
     * @return string sctr_destino
     */
    function getCtr_destino()
    {
        if (!isset($this->sctr_destino) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sctr_destino;
    }

    /**
     * Establece el valor del atributo sctr_destino de Traslado
     *
     * @param string sctr_destino='' optional
     */
    function setCtr_destino($sctr_destino = '')
    {
        $this->sctr_destino = $sctr_destino;
    }

    /**
     * Recupera el atributo sobserv de Traslado
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
     * Establece el valor del atributo sobserv de Traslado
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
        $oTrasladoSet = new core\Set();

        $oTrasladoSet->add($this->getDatosF_traslado());
        $oTrasladoSet->add($this->getDatosTipo_cmb());
        $oTrasladoSet->add($this->getDatosId_ctr_origen());
        $oTrasladoSet->add($this->getDatosCtr_origen());
        $oTrasladoSet->add($this->getDatosId_ctr_destino());
        $oTrasladoSet->add($this->getDatosCtr_destino());
        $oTrasladoSet->add($this->getDatosObserv());
        return $oTrasladoSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut df_traslado de Traslado
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosF_traslado()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'f_traslado'));
        $oDatosCampo->setEtiqueta(_("fecha de traslado"));
        $oDatosCampo->setTipo('fecha');
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut stipo_cmb de Traslado
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosTipo_cmb()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'tipo_cmb'));
        $oDatosCampo->setEtiqueta(_("tipo"));
        $oDatosCampo->setTipo('array');
        $oDatosCampo->setLista(array("sede" => _("ctr sede"), "cr" => _("ctr cr"), "dl" => _("delegación")));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iid_ctr_origen de Traslado
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosId_ctr_origen()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_ctr_origen'));
        $oDatosCampo->setEtiqueta(_("id origen"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(25);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sctr_origen de Traslado
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosCtr_origen()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'ctr_origen'));
        $oDatosCampo->setEtiqueta(_("centro de origen"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(25);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iid_ctr_destino de Traslado
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosId_ctr_destino()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_ctr_destino'));
        $oDatosCampo->setEtiqueta(_("id destino"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(25);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sctr_destino de Traslado
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosCtr_destino()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'ctr_destino'));
        $oDatosCampo->setEtiqueta(_("centro de destino"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(25);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sobserv de Traslado
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosObserv()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'observ'));
        $oDatosCampo->setEtiqueta(_("observaciones"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(50);
        return $oDatosCampo;
    }
}
