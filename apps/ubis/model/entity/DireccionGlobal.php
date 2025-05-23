<?php
namespace ubis\model\entity;

use Cassandra\Date;
use core\ClasePropiedades;
use core\ConverterDate;
use core\DatosCampo;
use core\Set;
use web\DateTimeLocal;
use web\NullDateTimeLocal;
use function core\is_true;

/**
 * Clase que implementa la entidad u_direcciones_global
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */
abstract class DireccionGlobal extends ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */
    /**
     * aPrimary_key de Direccion
     *
     * @var array
     */
    protected $aPrimary_key;

    /**
     * aDades de Direccion
     *
     * @var array
     */
    protected $aDades;

    /**
     * bLoaded
     *
     * @var boolean
     */
    protected $bLoaded = FALSE;

    /**
     * Id_direccion de Direccion
     *
     * @var integer
     */
    protected $iid_direccion;
    /**
     * Direccion de Direccion
     *
     * @var string
     */
    protected $sdireccion;
    /**
     * C_p de Direccion
     *
     * @var string
     */
    protected $sc_p;
    /**
     * Poblacion de Direccion
     *
     * @var string
     */
    protected $spoblacion;
    /**
     * Provincia de Direccion
     *
     * @var string
     */
    protected $sprovincia;
    /**
     * A_p de Direccion
     *
     * @var string
     */
    protected $sa_p;
    /**
     * Pais de Direccion
     *
     * @var string
     */
    protected $spais;
    /**
     * F_direccion de Direccion
     *
     * @var DateTimeLocal
     */
    protected $df_direccion;
    /**
     * Observ de Direccion
     *
     * @var string
     */
    protected $sobserv;
    /**
     * Cp_dcha de Direccion
     *
     * @var boolean
     */
    protected $bcp_dcha;
    /**
     * Latitud de Direccion
     *
     * @var integer
     */
    protected $ilatitud;
    /**
     * Longitud de Direccion
     *
     * @var integer
     */
    protected $ilongitud;
    /**
     * Plano_doc de Direccion
     *
     * @var string bytea
     */
    protected $iplano_doc;
    /**
     * Plano_extension de Direccion
     *
     * @var string
     */
    protected $splano_extension;
    /**
     * Plano_nom de Direccion
     *
     * @var string
     */
    protected $splano_nom;
    /**
     * Nom_sede de Direccion
     *
     * @var string
     */
    protected $snom_sede;

    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     * Si només necessita un valor, se li pot passar un integer.
     * En general se li passa un array amb les claus primàries.
     *
     * @param integer|array iid_direccion
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
    }

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/
    /* OTROS MÉTODOS  ----------------------------------------------------------*/
    /* MÉTODOS PRIVADOS ----------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/
    /**
     * Devuelve los ubis de una direccion
     *
     * @return array de objetos Ubis
     *
     */
    function getUbis()
    {
        $aClassName = explode('\\', get_called_class());
        $childClassName = end($aClassName);
        switch ($childClassName) {
            case 'DireccionCtr':
                $obj = 'ubis\model\entity\Centro';
                break;
            case 'DireccionCtrDl':
                $obj = 'ubis\model\entity\CentroDl';
                break;
            case 'DireccionCtrEx':
                $obj = 'ubis\model\entity\CentroEx';
                break;
            case 'DireccionCdc':
                $obj = 'ubis\model\entity\Casa';
                break;
            case 'DireccionCdcDl':
                $obj = 'ubis\model\entity\CasaDl';
                break;
            case 'DireccionCdcEx':
                $obj = 'ubis\model\entity\CasaEx';
                break;
        }

        $aWhere['id_direccion'] = $this->getId_direccion();
        $GesUbixDireccion = new GestorUbixDireccion();
        $cUbixDireccion = $GesUbixDireccion->getUbixDirecciones($aWhere);
        $ubis = [];
        if ($cUbixDireccion !== false) {
            foreach ($cUbixDireccion as $oUbixDireccion) {
                $id_ubi = $oUbixDireccion->getId_ubi();
                $propietario = $oUbixDireccion->getPropietario();
                $oUbi = new $obj($id_ubi);
                $ubis[] = $oUbi;
            }
        }
        return $ubis;
    }


    /**
     * Recupera todos los atributos de Direccion en un array
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
     * Recupera la clave primaria de Direccion en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('id_direccion' => $this->iid_direccion);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de Direccion en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_direccion') && $val_id !== '') $this->iid_direccion = (int)$val_id;
            }
        }
    }

    /**
     * Recupera el atributo iid_direccion de Direccion
     *
     * @return integer iid_direccion
     */
    function getId_direccion()
    {
        if (!isset($this->iid_direccion) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_direccion;
    }

    /**
     * Establece el valor del atributo iid_direccion de Direccion
     *
     * @param integer iid_direccion
     */
    function setId_direccion($iid_direccion)
    {
        $this->iid_direccion = $iid_direccion;
    }

    /**
     * Recupera el atributo sdireccion de Direccion
     *
     * @return string sdireccion
     */
    function getDireccion()
    {
        if (!isset($this->sdireccion) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sdireccion;
    }

    /**
     * Establece el valor del atributo sdireccion de Direccion
     *
     * @param string sdireccion='' optional
     */
    function setDireccion($sdireccion = '')
    {
        $this->sdireccion = $sdireccion;
    }

    /**
     * Recupera el atributo sc_p de Direccion
     *
     * @return string sc_p
     */
    function getC_p()
    {
        if (!isset($this->sc_p) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sc_p;
    }

    /**
     * Establece el valor del atributo sc_p de Direccion
     *
     * @param string sc_p='' optional
     */
    function setC_p($sc_p = '')
    {
        $this->sc_p = $sc_p;
    }

    /**
     * Recupera el atributo spoblacion de Direccion
     *
     * @return string spoblacion
     */
    function getPoblacion()
    {
        if (!isset($this->spoblacion) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->spoblacion;
    }

    /**
     * Establece el valor del atributo spoblacion de Direccion
     *
     * @param string spoblacion='' optional
     */
    function setPoblacion($spoblacion = '')
    {
        $this->spoblacion = $spoblacion;
    }

    /**
     * Recupera el atributo sprovincia de Direccion
     *
     * @return string sprovincia
     */
    function getProvincia()
    {
        if (!isset($this->sprovincia) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sprovincia;
    }

    /**
     * Establece el valor del atributo sprovincia de Direccion
     *
     * @param string sprovincia='' optional
     */
    function setProvincia($sprovincia = '')
    {
        $this->sprovincia = $sprovincia;
    }

    /**
     * Recupera el atributo sa_p de Direccion
     *
     * @return string sa_p
     */
    function getA_p()
    {
        if (!isset($this->sa_p) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sa_p;
    }

    /**
     * Establece el valor del atributo sa_p de Direccion
     *
     * @param string sa_p='' optional
     */
    function setA_p($sa_p = '')
    {
        $this->sa_p = $sa_p;
    }

    /**
     * Recupera el atributo spais de Direccion
     *
     * @return string spais
     */
    function getPais()
    {
        if (!isset($this->spais) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->spais;
    }

    /**
     * Establece el valor del atributo spais de Direccion
     *
     * @param string spais='' optional
     */
    function setPais($spais = '')
    {
        $this->spais = $spais;
    }

    /**
     * Recupera el atributo df_direccion de Direccion
     *
     * @return DateTimeLocal|NullDateTimeLocal
     */
    function getF_direccion()
    {
        if (!isset($this->df_direccion) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        if (empty($this->df_direccion)) {
            return new NullDateTimeLocal();
        }
        $oConverter = new ConverterDate('date', $this->df_direccion);
        return $oConverter->fromPg();
    }

    /**
     * Establece el valor del atributo df_direccion de Direccion
     * Si df_direccion es string, y convert=true se convierte usando el formato webDateTimeLocal->getFormat().
     * Si convert es false, df_direccion debe ser un string en formato ISO (Y-m-d). Corresponde al pgstyle de la base de datos.
     *
     * @param date|string df_direccion='' optional.
     * @param boolean convert=true optional. Si es false, df_direccion debe ser un string en formato ISO (Y-m-d).
     */
    function setF_direccion($df_direccion = '', $convert = true)
    {
        if ($convert === true && !empty($df_direccion)) {
            $oConverter = new ConverterDate('date', $df_direccion);
            $this->df_direccion = $oConverter->toPg();
        } else {
            $this->df_direccion = $df_direccion;
        }
    }

    /**
     * Recupera el atributo sobserv de Direccion
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
     * Establece el valor del atributo sobserv de Direccion
     *
     * @param string sobserv='' optional
     */
    function setObserv($sobserv = '')
    {
        $this->sobserv = $sobserv;
    }

    /**
     * Recupera el atributo bcp_dcha de Direccion
     *
     * @return boolean bcp_dcha
     */
    function getCp_dcha()
    {
        if (!isset($this->bcp_dcha) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->bcp_dcha;
    }

    /**
     * Establece el valor del atributo bcp_dcha de Direccion
     *
     * @param boolean bcp_dcha='f' optional
     */
    function setCp_dcha($bcp_dcha = 'f')
    {
        $this->bcp_dcha = $bcp_dcha;
    }

    /**
     * Recupera el atributo ilatitud de Direccion
     *
     * @return string ilatitud
     */
    function getLatitud()
    {
        if (!isset($this->ilatitud) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->ilatitud;
    }

    /**
     * Establece el valor del atributo ilatitud de Direccion
     *
     * @param string ilatitud='' optional
     */
    function setLatitud($ilatitud = '')
    {
        $this->ilatitud = $ilatitud;
    }

    /**
     * Recupera el atributo ilongitud de Direccion
     *
     * @return string ilongitud
     */
    function getLongitud()
    {
        if (!isset($this->ilongitud) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->ilongitud;
    }

    /**
     * Establece el valor del atributo ilongitud de Direccion
     *
     * @param string ilongitud='' optional
     */
    function setLongitud($ilongitud = '')
    {
        $this->ilongitud = $ilongitud;
    }

    /**
     * Recupera el atributo iplano_doc de Direccion
     *
     * @return string iplano_doc
     */
    function getPlano_doc()
    {
        if (!isset($this->iplano_doc) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iplano_doc;
    }

    /**
     * Establece el valor del atributo iplano_doc de Direccion
     *
     * @param string iplano_doc='' optional
     */
    function setPlano_doc($iplano_doc = '')
    {
        $this->iplano_doc = $iplano_doc;
    }

    /**
     * Recupera el atributo splano_extension de Direccion
     *
     * @return string splano_extension
     */
    function getPlano_extension()
    {
        if (!isset($this->splano_extension) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->splano_extension;
    }

    /**
     * Establece el valor del atributo splano_extension de Direccion
     *
     * @param string splano_extension='' optional
     */
    function setPlano_extension($splano_extension = '')
    {
        $this->splano_extension = $splano_extension;
    }

    /**
     * Recupera el atributo splano_nom de Direccion
     *
     * @return string splano_nom
     */
    function getPlano_nom()
    {
        if (!isset($this->splano_nom) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->splano_nom;
    }

    /**
     * Establece el valor del atributo splano_nom de Direccion
     *
     * @param string splano_nom='' optional
     */
    function setPlano_nom($splano_nom = '')
    {
        $this->splano_nom = $splano_nom;
    }

    /**
     * Recupera el atributo snom_sede de Direccion
     *
     * @return string snom_sede
     */
    function getNom_sede()
    {
        if (!isset($this->snom_sede) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->snom_sede;
    }

    /**
     * Establece el valor del atributo snom_sede de Direccion
     *
     * @param string snom_sede='' optional
     */
    function setNom_sede($snom_sede = '')
    {
        $this->snom_sede = $snom_sede;
    }

    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    public function planoDownload()
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $id_direccion = $this->getId_direccion();

        $sql = "SELECT plano_nom,plano_extension,plano_doc FROM $nom_tabla WHERE id_direccion=?";
        //echo "sql: $sql_update<br>";
        $stmt = $oDbl->prepare($sql);
        $stmt->execute(array($id_direccion));
        $stmt->bindColumn(1, $plano_nom, \PDO::PARAM_STR, 256);
        $stmt->bindColumn(2, $plano_extension, \PDO::PARAM_STR, 256);
        $stmt->bindColumn(3, $plano_doc, \PDO::PARAM_LOB);
        $stmt->fetch(\PDO::FETCH_BOUND);

        return [
            'plano_nom' => $plano_nom,
            'plano_extension' => $plano_extension,
            'plano_doc' => $plano_doc,
        ];
    }

    public function planoUpload($nom, $extension, $fichero)
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $id_direccion = $this->getId_direccion();

        $nom = empty($nom) ? '' : $nom;
        $extension = empty($extension) ? '' : $extension;
        $fichero = empty($fichero) ? '' : $fichero;

        $sql_update = "UPDATE $nom_tabla SET plano_nom=:plano_nom,plano_extension=:plano_extension,plano_doc=:plano_doc WHERE id_direccion=$id_direccion";

        $oDBSt_a = $oDbl->prepare($sql_update);
        $oDBSt_a->bindParam(":plano_nom", $nom, \PDO::PARAM_STR);
        $oDBSt_a->bindParam(":plano_extension", $extension, \PDO::PARAM_STR);
        $oDBSt_a->bindParam(":plano_doc", $fichero, \PDO::PARAM_LOB);

        $oDBSt_a->execute();
    }

    public function planoBorrar()
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $id_direccion = $this->getId_direccion();

        $nom = NULL;
        $extension = NULL;
        $fichero = NULL;

        $sql_update = "UPDATE $nom_tabla SET plano_nom=:plano_nom,plano_extension=:plano_extension,plano_doc=:plano_doc WHERE id_direccion=$id_direccion";

        $oDBSt_a = $oDbl->prepare($sql_update);
        $oDBSt_a->bindParam(":plano_nom", $nom, \PDO::PARAM_STR);
        $oDBSt_a->bindParam(":plano_extension", $extension, \PDO::PARAM_STR);
        $oDBSt_a->bindParam(":plano_doc", $fichero, \PDO::PARAM_LOB);

        $oDBSt_a->execute();
    }

    /**
     * texte amb l'adreça formatejada
     *
     */
    public function getDireccionPostal($salto_linea = '<br>', $espacio = ' ')
    {
        $this->DBCarregar();
        $txt = '';
        $rtn = $salto_linea;
        $spc = $espacio;
        if (isset($this->sdireccion)) $txt .= $this->sdireccion . $rtn;
        if (is_true($this->bcp_dcha)) {
            if (!empty($this->spoblacion)) $txt .= $this->spoblacion . $spc;
            if (!empty($this->sc_p)) $txt .= $this->sc_p;
        } else {
            if (!empty($this->sc_p)) $txt .= $this->sc_p . $spc;
            if (!empty($this->spoblacion)) $txt .= $this->spoblacion;
        }
        $txt .= $rtn;
        if (!empty($this->sa_p)) $txt .= $this->sa_p . $rtn;

        return $txt;
    }

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oDireccionSet = new Set();

        $oDireccionSet->add($this->getDatosDireccion());
        $oDireccionSet->add($this->getDatosC_p());
        $oDireccionSet->add($this->getDatosPoblacion());
        $oDireccionSet->add($this->getDatosProvincia());
        $oDireccionSet->add($this->getDatosA_p());
        $oDireccionSet->add($this->getDatosPais());
        $oDireccionSet->add($this->getDatosF_direccion());
        $oDireccionSet->add($this->getDatosObserv());
        $oDireccionSet->add($this->getDatosCp_dcha());
        $oDireccionSet->add($this->getDatosLatitud());
        $oDireccionSet->add($this->getDatosLongitud());
        $oDireccionSet->add($this->getDatosPlano_doc());
        $oDireccionSet->add($this->getDatosPlano_extension());
        $oDireccionSet->add($this->getDatosPlano_nom());
        $oDireccionSet->add($this->getDatosNom_sede());
        return $oDireccionSet->getTot();
    }

    /**
     * Recupera les propietats de l'atribut sdireccion de Direccion
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosDireccion()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'direccion'));
        $oDatosCampo->setEtiqueta(_("dirección"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sc_p de Direccion
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosC_p()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'c_p'));
        $oDatosCampo->setEtiqueta(_("código postal"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut spoblacion de Direccion
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosPoblacion()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'poblacion'));
        $oDatosCampo->setEtiqueta(_("población"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sprovincia de Direccion
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosProvincia()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'provincia'));
        $oDatosCampo->setEtiqueta(_("provincia"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sa_p de Direccion
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosA_p()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'a_p'));
        $oDatosCampo->setEtiqueta(_("ap. correos"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut spais de Direccion
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosPais()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'pais'));
        $oDatosCampo->setEtiqueta(_("país"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut df_direccion de Direccion
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosF_direccion()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'f_direccion'));
        $oDatosCampo->setEtiqueta(_("fecha dirección"));
        $oDatosCampo->setTipo('fecha');
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sobserv de Direccion
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosObserv()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'observ'));
        $oDatosCampo->setEtiqueta(_("observaciones"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut bcp_dcha de Direccion
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosCp_dcha()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'cp_dcha'));
        $oDatosCampo->setEtiqueta(_("cp dcha"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut ilatitud de Direccion
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosLatitud()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'latitud'));
        $oDatosCampo->setEtiqueta(_("latitud"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut ilongitud de Direccion
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosLongitud()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'longitud'));
        $oDatosCampo->setEtiqueta(_("longitud"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iplano_doc de Direccion
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosPlano_doc()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'plano_doc'));
        $oDatosCampo->setEtiqueta(_("plano documento"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut splano_extension de Direccion
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosPlano_extension()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'plano_extension'));
        $oDatosCampo->setEtiqueta(_("plano extensión"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut splano_nom de Direccion
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosPlano_nom()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'plano_nom'));
        $oDatosCampo->setEtiqueta(_("plano nombre"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut snom_sede de Direccion
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosNom_sede()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'nom_sede'));
        $oDatosCampo->setEtiqueta(_("nombre de la sede"));
        return $oDatosCampo;
    }
}

?>
