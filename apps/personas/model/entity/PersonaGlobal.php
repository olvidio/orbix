<?php

namespace personas\model\entity;

use core\ClasePropiedades;
use core\ConfigGlobal;
use core\ConverterDate;
use core\DatosCampo;
use core\Set;
use ReflectionClass;
use ubis\model\entity\Centro;
use ubis\model\entity\CentroDl;
use ubis\model\entity\DescTeleco;
use web\DateTimeLocal;
use web\NullDateTimeLocal;
use function core\strtoupper_dlb;

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
 * Clase que implementa la entidad personas
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */
abstract class PersonaGlobal extends ClasePropiedades
{

    // Para que la variable stgr_posibles coja las traducciones, hay 
    // que ejecutar la función 'traduccion_init()'. Cosa que se hace justo
    // al final de la definición de la clase: PersonaGlobal::traduccion_init();
    static $stgr_posibles;

    static function traduccion_init()
    {
        self::$stgr_posibles = [
            'n' => _("no cursa est."),
            'b' => _("bienio"),
            'c1' => _("cuadrienio año I"),
            'c2' => _("cuadrienio año II-IV"),
            'r' => _("repaso"),
        ];
    }

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de PersonaGlobal
     *
     * @var array
     */
    protected $aPrimary_key;

    /**
     * aDades de PersonaGlobal
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
     * Id_schema de PersonaGlobal
     *
     * @var integer
     */
    protected $iid_schema;
    /**
     * Id_nom de PersonaGlobal
     *
     * @var integer
     */
    protected $iid_nom;
    /**
     * Id_cr de PersonaGlobal
     *
     * @var integer
     */
    protected $iid_cr;
    /**
     * Id_tabla de PersonaGlobal
     *
     * @var string
     */
    protected $sid_tabla;
    /**
     * Dl de PersonaGlobal
     *
     * @var string
     */
    protected $sdl;
    /**
     * Sacd de PersonaGlobal
     *
     * @var boolean
     */
    protected $bsacd;
    /**
     * Trato de PersonaGlobal
     *
     * @var string
     */
    protected $strato;
    /**
     * Nom de PersonaGlobal
     *
     * @var string
     */
    protected $snom;
    /**
     * Nx1 de PersonaGlobal
     *
     * @var string
     */
    protected $snx1;
    /**
     * Apellido1 de PersonaGlobal
     *
     * @var string
     */
    protected $sapellido1;
    /**
     * Nx2 de PersonaGlobal
     *
     * @var string
     */
    protected $snx2;
    /**
     * Apellido2 de PersonaGlobal
     *
     * @var string
     */
    protected $sapellido2;
    /**
     * F_nacimiento de PersonaGlobal
     *
     * @varDateTimeLocal
     */
    protected $df_nacimiento;
    /**
     * Lengua de PersonaGlobal
     *
     * @var string
     */
    protected $slengua;
    /**
     * Situacion de PersonaGlobal
     *
     * @var string
     */
    protected $ssituacion;
    /**
     * F_situacion de PersonaGlobal
     *
     * @varDateTimeLocal
     */
    protected $df_situacion;
    /**
     * Apel_fam de PersonaGlobal
     *
     * @var string
     */
    protected $sapel_fam;
    /**
     * Inc de PersonaGlobal
     *
     * @var string
     */
    protected $sinc;
    /**
     * F_inc de PersonaGlobal
     *
     * @varDateTimeLocal
     */
    protected $df_inc;
    /**
     * Stgr de PersonaGlobal
     *
     * @var string
     */
    protected $sstgr;
    /**
     * Profesion de PersonaGlobal
     *
     * @var string
     */
    protected $sprofesion;
    /**
     * Eap de PersonaGlobal
     *
     * @var string
     */
    protected $seap;
    /**
     * Observ de PersonaGlobal
     *
     * @var string
     */
    protected $sobserv;
    /**
     * Lugar_nacimiento de PersonaGlobal
     *
     * @var string
     */
    protected $slugar_nacimiento;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * ApellidosNombre de Persona
     *
     * @var string
     */
    protected $sApellidosNombre;
    /**
     * ApellidosNombreCr1_05 de Persona
     *
     * @var string
     */
    protected $sApellidosNombreCr1_05;
    /**
     * NombreApellidos de Persona
     *
     * @var string
     */
    protected $sNombreApellidos;
    /**
     * NombreApellidosCrSin de Persona
     *
     * @var string
     */
    protected $sNombreApellidosCrSin;
    /**
     * Apellidosde Persona
     *
     * @var string
     */
    protected $sApellidos;
    /**
     * Nombre en latin,  para las actas
     *
     * @var string
     */
    protected $sTituloNombreLatin;
    /**
     * Nombre del profesor, para las actas
     *
     * @var string
     */
    protected $sTituloNombre;

    /**
     * Centro_o_dl de Persona
     *
     * @var string
     */
    protected $sCentro_o_dl;

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     *
     * function __construct($a_id='') {
     * }
     */

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Devuelve el e-mail principal o primero de la lista de teleco de una persona
     *
     *    $desc_teleco en la tabla (DB: comun) public.xd_desc_teleco
     *
     *       13    e-mail    principal
     *       14    e-mail    profesional
     *       15    e-mail    otros
     */
    public function emailPrincipalOPrimero($id_nom, $desc_teleco = 13)
    {
        $aWhere = [];
        $aWhere['id_nom'] = $id_nom;
        $aWhere['tipo_teleco'] = 'e-mail';
        if ($desc_teleco !== 13) {
            $aWhere['desc_teleco'] = $desc_teleco;
        }
        $aWhere['_ordre'] = 'desc_teleco';

        $e_mail = '';
        $GesTelecoPersonas = new GestorTelecoPersona();
        $cTelecos = $GesTelecoPersonas->getTelecos($aWhere);
        if (!empty($cTelecos) && count($cTelecos) > 0) {
            $oTeleco = $cTelecos[0];
            $e_mail = $oTeleco->getNum_teleco();
        }
        return $e_mail;
    }

    /**
     * Devuelve los teleco de una persona especificados por
     *
     *     parámetros $id_nom,$tipo_teleco,$desc_teleco,$separador
     *
     *    Si $desc_teleco es '*', entonces se añade la descripción entre paréntesis
     *      al final del número...
     */
    public function telecos_persona($id_nom, $tipo_teleco, $separador, $desc_teleco = '', $bDescripcion = TRUE)
    {

        $aWhere = [];
        $aWhere['id_nom'] = $id_nom;
        $aWhere['tipo_teleco'] = $tipo_teleco;
        if ($desc_teleco !== '*' && !empty($desc_teleco)) {
            $aWhere['desc_teleco'] = $desc_teleco;
        }
        $GesTelecoPersonas = new GestorTelecoPersona();
        $cTelecos = $GesTelecoPersonas->getTelecos($aWhere);
        $tels = '';
        $separador = empty($separador) ? ".-<br>" : $separador;
        foreach ($cTelecos as $oTelecoPersona) {
            $iDescTel = $oTelecoPersona->getDesc_teleco();
            $num_teleco = $oTelecoPersona->getNum_teleco();
            if ($desc_teleco === "*" && !empty($iDescTel) && $bDescripcion) {
                $oDescTel = new DescTeleco($iDescTel);
                $tels .= $num_teleco . "(" . $oDescTel->getDesc_teleco() . ")" . $separador;
            } else {
                $tels .= $num_teleco . $separador;
            }
        }
        $tels = substr($tels, 0, -(strlen($separador)));
        return $tels;
    }

    /* OTROS MÉTODOS  ----------------------------------------------------------*/
    /* MÉTODOS PRIVADOS ----------------------------------------------------------*/


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de PersonaGlobal en un array
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
     * Recupera la clave primaria de PersonaGlobal en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('id_nom' => $this->iid_nom);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de PersonaGlobal en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_nom') && $val_id !== '') $this->iid_nom = (int)$val_id;
            }
        }
    }

    /**
     * Recupera el atributo iid_nom de PersonaGlobal
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
     * Establece el valor del atributo iid_nom de PersonaGlobal
     *
     * @param integer iid_nom
     */
    function setId_nom($iid_nom)
    {
        $this->iid_nom = $iid_nom;
    }

    /**
     * Recupera el atributo iid_cr de PersonaGlobal
     *
     * @return integer iid_cr
     */
    function getId_cr()
    {
        if (!isset($this->iid_cr) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_cr;
    }

    /**
     * Establece el valor del atributo iid_cr de PersonaGlobal
     *
     * @param integer iid_cr='' optional
     */
    function setId_cr($iid_cr = '')
    {
        $this->iid_cr = $iid_cr;
    }

    /**
     * Recupera el atributo sid_tabla de PersonaGlobal
     *
     * @return string sid_tabla
     */
    function getId_tabla()
    {
        if (!isset($this->sid_tabla) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sid_tabla;
    }

    /**
     * Establece el valor del atributo sid_tabla de PersonaGlobal
     *
     * @param string sid_tabla='' optional
     */
    function setId_tabla($sid_tabla = '')
    {
        $this->sid_tabla = $sid_tabla;
    }

    /**
     * Recupera el atributo sdl de PersonaGlobal
     *
     * @return string sdl
     */
    function getDl()
    {
        if (!isset($this->sdl) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sdl;
    }

    /**
     * Establece el valor del atributo sdl de PersonaGlobal
     *
     * @param string sdl='' optional
     */
    function setDl($sdl = '')
    {
        $this->sdl = $sdl;
    }

    /**
     * Recupera el atributo bsacd de PersonaGlobal
     *
     * @return boolean bsacd
     */
    function getSacd()
    {
        if (!isset($this->bsacd) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->bsacd;
    }

    /**
     * Establece el valor del atributo bsacd de PersonaGlobal
     *
     * @param boolean bsacd='f' optional
     */
    function setSacd($bsacd = 'f')
    {
        $this->bsacd = $bsacd;
    }

    /**
     * Recupera el atributo strato de PersonaGlobal
     *
     * @return string strato
     */
    function getTrato()
    {
        if (!isset($this->strato) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->strato;
    }

    /**
     * Establece el valor del atributo strato de PersonaGlobal
     *
     * @param string strato='' optional
     */
    function setTrato($strato = '')
    {
        $this->strato = $strato;
    }

    /**
     * Recupera el atributo snom de PersonaGlobal
     *
     * @return string snom
     */
    function getNom()
    {
        if (!isset($this->snom) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->snom;
    }

    /**
     * Establece el valor del atributo snom de PersonaGlobal
     *
     * @param string snom='' optional
     */
    function setNom($snom = '')
    {
        $this->snom = $snom;
    }

    /**
     * Recupera el atributo snx1 de PersonaGlobal
     *
     * @return string snx1
     */
    function getNx1()
    {
        if (!isset($this->snx1) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->snx1;
    }

    /**
     * Establece el valor del atributo snx1 de PersonaGlobal
     *
     * @param string snx1='' optional
     */
    function setNx1($snx1 = '')
    {
        $this->snx1 = $snx1;
    }

    /**
     * Recupera el atributo sapellido1 de PersonaGlobal
     *
     * @return string sapellido1
     */
    function getApellido1()
    {
        if (!isset($this->sapellido1) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sapellido1;
    }

    /**
     * Establece el valor del atributo sapellido1 de PersonaGlobal
     *
     * @param string sapellido1='' optional
     */
    function setApellido1($sapellido1 = '')
    {
        $this->sapellido1 = $sapellido1;
    }

    /**
     * Recupera el atributo snx2 de PersonaGlobal
     *
     * @return string snx2
     */
    function getNx2()
    {
        if (!isset($this->snx2) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->snx2;
    }

    /**
     * Establece el valor del atributo snx2 de PersonaGlobal
     *
     * @param string snx2='' optional
     */
    function setNx2($snx2 = '')
    {
        $this->snx2 = $snx2;
    }

    /**
     * Recupera el atributo sapellido2 de PersonaGlobal
     *
     * @return string sapellido2
     */
    function getApellido2()
    {
        if (!isset($this->sapellido2) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sapellido2;
    }

    /**
     * Establece el valor del atributo sapellido2 de PersonaGlobal
     *
     * @param string sapellido2='' optional
     */
    function setApellido2($sapellido2 = '')
    {
        $this->sapellido2 = $sapellido2;
    }

    /**
     * Recupera el atributo df_nacimiento de PersonaGlobal
     *
     * @returnDateTimeLocal df_nacimiento
     */
    function getF_nacimiento()
    {
        if (!isset($this->df_nacimiento) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        if (empty($this->df_nacimiento)) {
            return new NullDateTimeLocal();
        }
        $oConverter = new ConverterDate('date', $this->df_nacimiento);
        return $oConverter->fromPg();
    }

    /**
     * Establece el valor del atributo df_nacimiento de PersonaGlobal
     * Si df_nacimiento es string, y convert=true se convierte usando el formato webDateTimeLocal->getFormat().
     * Si convert es false, df_nacimiento debe ser un string en formato ISO (Y-m-d). Corresponde al pgstyle de la base de datos.
     *
     * @param DateTimeLocal|string df_nacimiento='' optional.
     * @param boolean convert=true optional. Si es false, df_nacimiento debe ser un string en formato ISO (Y-m-d).
     */
    function setF_nacimiento($df_nacimiento = '', $convert = true)
    {
        if ($convert === true && !empty($df_nacimiento)) {
            $oConverter = new ConverterDate('date', $df_nacimiento);
            $this->df_nacimiento = $oConverter->toPg();
        } else {
            $this->df_nacimiento = $df_nacimiento;
        }
    }

    /**
     * Recupera el atributo slengua de PersonaGlobal
     *
     * @return string slengua
     */
    function getLengua()
    {
        if (!isset($this->slengua) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->slengua;
    }

    /**
     * Establece el valor del atributo slengua de PersonaGlobal
     *
     * @param string slengua='' optional
     */
    function setLengua($slengua = '')
    {
        $this->slengua = $slengua;
    }

    /**
     * Recupera el atributo ssituacion de PersonaGlobal
     *
     * @return string ssituacion
     */
    function getSituacion()
    {
        if (!isset($this->ssituacion) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->ssituacion;
    }

    /**
     * Establece el valor del atributo ssituacion de PersonaGlobal
     *
     * @param string ssituacion='' optional
     */
    function setSituacion($ssituacion = '')
    {
        $this->ssituacion = $ssituacion;
    }

    /**
     * Recupera el atributo df_situacion de PersonaGlobal
     *
     * @returnDateTimeLocal df_situacion
     */
    function getF_situacion()
    {
        if (!isset($this->df_situacion) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        if (empty($this->df_situacion)) {
            return new NullDateTimeLocal();
        }
        $oConverter = new ConverterDate('date', $this->df_situacion);
        return $oConverter->fromPg();
    }

    /**
     * Establece el valor del atributo df_situacion de PersonaGlobal
     * Si df_situacion es string, y convert=true se convierte usando el formato webDateTimeLocal->getFormat().
     * Si convert es false, df_situacion debe ser un string en formato ISO (Y-m-d). Corresponde al pgstyle de la base de datos.
     *
     * @param DateTimeLocal|string df_situacion='' optional.
     * @param boolean convert=true optional. Si es false, df_situacion debe ser un string en formato ISO (Y-m-d).
     */
    function setF_situacion($df_situacion = '', $convert = true)
    {
        if ($convert === true && !empty($df_situacion)) {
            $oConverter = new ConverterDate('date', $df_situacion);
            $this->df_situacion = $oConverter->toPg();
        } else {
            $this->df_situacion = $df_situacion;
        }
    }

    /**
     * Recupera el atributo sapel_fam de PersonaGlobal
     *
     * @return string sapel_fam
     */
    function getApel_fam()
    {
        if (!isset($this->sapel_fam) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sapel_fam;
    }

    /**
     * Establece el valor del atributo sapel_fam de PersonaGlobal
     *
     * @param string sapel_fam='' optional
     */
    function setApel_fam($sapel_fam = '')
    {
        $this->sapel_fam = $sapel_fam;
    }

    /**
     * Recupera el atributo sinc de PersonaGlobal
     *
     * @return string sinc
     */
    function getInc()
    {
        if (!isset($this->sinc) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sinc;
    }

    /**
     * Establece el valor del atributo sinc de PersonaGlobal
     *
     * @param string sinc='' optional
     */
    function setInc($sinc = '')
    {
        $this->sinc = $sinc;
    }

    /**
     * Recupera el atributo df_inc de PersonaGlobal
     *
     * @returnDateTimeLocal df_inc
     */
    function getF_inc()
    {
        if (!isset($this->df_inc) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        if (empty($this->df_inc)) {
            return new NullDateTimeLocal();
        }
        $oConverter = new ConverterDate('date', $this->df_inc);
        return $oConverter->fromPg();
    }

    /**
     * Establece el valor del atributo df_inc de PersonaGlobal
     * Si df_inc es string, y convert=true se convierte usando el formato webDateTimeLocal->getFormat().
     * Si convert es false, df_inc debe ser un string en formato ISO (Y-m-d). Corresponde al pgstyle de la base de datos.
     *
     * @param DateTimeLocal|string df_inc='' optional.
     * @param boolean convert=true optional. Si es false, df_inc debe ser un string en formato ISO (Y-m-d).
     */
    function setF_inc($df_inc = '', $convert = true)
    {
        if ($convert === true && !empty($df_inc)) {
            $oConverter = new ConverterDate('date', $df_inc);
            $this->df_inc = $oConverter->toPg();
        } else {
            $this->df_inc = $df_inc;
        }
    }

    /**
     * Recupera el atributo sstgr de PersonaGlobal
     *
     * @return string sstgr
     */
    function getStgr()
    {
        if (!isset($this->sstgr) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sstgr;
    }

    /**
     * Establece el valor del atributo sstgr de PersonaGlobal
     *
     * @param string sstgr='' optional
     */
    function setStgr($sstgr = '')
    {
        $this->sstgr = $sstgr;
    }

    /**
     * Recupera el atributo sprofesion de PersonaGlobal
     *
     * @return string sprofesion
     */
    function getProfesion()
    {
        if (!isset($this->sprofesion) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sprofesion;
    }

    /**
     * Establece el valor del atributo sprofesion de PersonaGlobal
     *
     * @param string sprofesion='' optional
     */
    function setProfesion($sprofesion = '')
    {
        $this->sprofesion = $sprofesion;
    }

    /**
     * Recupera el atributo seap de PersonaGlobal
     *
     * @return string seap
     */
    function getEap()
    {
        if (!isset($this->seap) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->seap;
    }

    /**
     * Establece el valor del atributo seap de PersonaGlobal
     *
     * @param string seap='' optional
     */
    function setEap($seap = '')
    {
        $this->seap = $seap;
    }

    /**
     * Recupera el atributo sobserv de PersonaGlobal
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
     * Establece el valor del atributo sobserv de PersonaGlobal
     *
     * @param string sobserv='' optional
     */
    function setObserv($sobserv = '')
    {
        $this->sobserv = $sobserv;
    }

    /**
     * Recupera el atributo slugar_nacimiento de PersonaGlobal
     *
     * @return integer slugar_nacimiento
     */
    function getLugar_nacimiento()
    {
        if (!isset($this->slugar_nacimiento) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->slugar_nacimiento;
    }

    /**
     * Establece el valor del atributo slugar_nacimiento de PersonaGlobal
     *
     * @param integer slugar_nacimiento='' optional
     */
    function setLugar_nacimiento($slugar_nacimiento = '')
    {
        $this->slugar_nacimiento = $slugar_nacimiento;
    }

    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    public function getClassName()
    {
        $shortClassName = (new ReflectionClass($this))->getShortName();
        return $shortClassName;
    }

    public function getPrefApellidosNombre()
    {
        $Pref_ordenApellidos = ConfigGlobal::mi_ordenApellidos()?? '';

        if ($Pref_ordenApellidos === 'nom_ap') {
            return $this->getNombreApellidos();
        }

        return $this->getApellidosNombre();
    }

    /**
     * Recupera el atributo sApellidos de Persona
     *
     * @return string sApellidos
     */
    function getApellidos()
    {
        if (!isset($this->sApellidos)) {
            if (!$this->bLoaded) {
                $this->DBCarregar();
            }
            $ap_nom = !empty($this->snx1) ? $this->snx1 . ' ' : '';
            $ap_nom .= $this->sapellido1;
            $ap_nom .= !empty($this->snx2) ? ' ' . $this->snx2 : '';
            $ap_nom .= !empty($this->sapellido2) ? ' ' . $this->sapellido2 : '';

            $this->sApellidos = $ap_nom;
        }
        return $this->sApellidos;
    }

    /**
     * Recupera el atributo sApellidosNombre de Persona
     *
     * @return string sApellidosNombre
     */
    function getApellidosNombre()
    {
        if (!isset($this->sApellidosNombre)) {
            if (!$this->bLoaded) {
                $this->DBCarregar();
            }
            if (empty($this->sapellido1)) {
                $ap_nom = '';
            } else {
                $ap_nom = $this->sapellido1;
                $ap_nom .= !empty($this->snx2) ? ' ' . $this->snx2 : '';
                $ap_nom .= !empty($this->sapellido2) ? ' ' . $this->sapellido2 : '';
                $ap_nom .= ', ';
                $ap_nom .= !empty($this->strato) ? $this->strato . ' ' : ' ';
                $ap_nom .= !empty($this->sapel_fam) ? $this->sapel_fam : $this->snom;
                $ap_nom .= !empty($this->snx1) ? ' ' . $this->snx1 : '';
            }
            $this->sApellidosNombre = trim($ap_nom);
        }
        return $this->sApellidosNombre;
    }

    public function getApellidosUpperNombre()
    {
        $apellidos = $this->getApellidos();
        //Ni la función del postgresql ni la del php convierten los acentos.
        $apellidos = trim($apellidos);

        $apellidos = empty($apellidos) ? '????' : $apellidos;
        $ap_nom = strtoupper_dlb($apellidos);
        $ap_nom .= ', ';
        $ap_nom .= !empty($this->strato) ? $this->strato . ' ' : '';
        $ap_nom .= $this->snom;

        return $ap_nom;
    }

    /**
     * Establece el valor del atributo sApellidosNombre de Persona
     *
     * @param string sApellidosNombre
     */
    function setApellidosNombre($sApellidosNombre)
    {
        $this->sApellidosNombre = $sApellidosNombre;
    }

    /**
     * Recupera el atributo sApellidosNombreCr1_05 de Persona (según cr 1/05).
     *
     * @return string sApellidosNombreCr1_05
     */
    function getApellidosNombreCr1_05()
    {
        if (!isset($this->sApellidosNombreCr1_05)) {
            if (!$this->bLoaded) {
                $this->DBCarregar();
            }
            $ap_nom = !empty($this->snx1) ? $this->snx1 . ' ' : '';
            $ap_nom .= $this->sapellido1;
            $ap_nom .= !empty($this->snx2) ? ' ' . $this->snx2 : '';
            $ap_nom .= !empty($this->sapellido2) ? ' ' . $this->sapellido2 : '';
            $ap_nom .= ', ';
            $ap_nom .= $this->snom;

            $this->sApellidosNombreCr1_05 = $ap_nom;
        }
        return $this->sApellidosNombreCr1_05;
    }

    /**
     * Establece el valor del atributo sApellidosNombreCr1_05 de Persona
     *
     * @param string sApellidosNombreCr1_05
     */
    function setApellidosNombreCr1_05($sApellidosNombreCr1_05)
    {
        $this->sApellidosNombreCr1_05 = $sApellidosNombreCr1_05;
    }

    /**
     * Recupera el atributo sNombreApellidos de Persona
     *
     * @return string sNombreApellidos
     */
    public function getNombreApellidos()
    {
        if (!isset($this->sNombreApellidos)) {
            if (!$this->bLoaded) {
                $this->DBCarregar();
            }
            $nom_ap = !empty($this->strato) ? $this->strato . ' ' : '';
            $nom_ap .= !empty($this->sapel_fam) ? $this->sapel_fam : $this->snom;
            $nom_ap .= !empty($this->snx1) ? ' ' . $this->snx1 : '';
            $nom_ap .= ' ' . $this->sapellido1;
            $nom_ap .= !empty($this->snx2) ? ' ' . $this->snx2 : '';
            $nom_ap .= !empty($this->sapellido2) ? ' ' . $this->sapellido2 : '';

            $this->sNombreApellidos = $nom_ap;
        }
        return $this->sNombreApellidos;
    }

    /**
     * Recupera el atributo sNombreApellidosCrSin de Persona
     * el nombre más los nexos más los apellidos, sin el tratamiento (para des).
     *
     * @return string sNombreApellidosCrSin
     */
    function getNombreApellidosCrSin()
    {
        if (!isset($this->sNombreApellidosCrSin)) {
            if (!$this->bLoaded) {
                $this->DBCarregar();
            }
            $nom_ap = $this->snom;
            $nom_ap .= !empty($this->snx1) ? ' ' . $this->snx1 : '';
            $nom_ap .= ' ' . $this->sapellido1;
            $nom_ap .= !empty($this->snx2) ? ' ' . $this->snx2 : ' ';
            $nom_ap .= !empty($this->sapellido2) ? ' ' . $this->sapellido2 : '';

            $this->sNombreApellidosCrSin = $nom_ap;
        }
        return $this->sNombreApellidosCrSin;
    }

    /**
     * Recupera el atributo sTituloNombre de Persona
     * el nombre más los nexos más los apellidos (para des actas).
     *
     * @return string sTituloNombre
     */
    function getTituloNombre()
    {
        if (!isset($this->sTituloNombre)) {
            if (!$this->bLoaded) {
                $this->DBCarregar();
            }
            $nom_ap = 'Dnus. Dr. ';
            $nom_ap .= $this->snom;
            $nom_ap .= !empty($this->snx1) ? ' ' . $this->snx1 : '';
            $nom_ap .= ' ' . $this->sapellido1;
            $nom_ap .= !empty($this->snx2) ? ' ' . $this->snx2 : ' ';
            $nom_ap .= !empty($this->sapellido2) ? ' ' . $this->sapellido2 : '';

            $this->sTituloNombre = $nom_ap;
        }
        return $this->sTituloNombre;
    }

    /**
     * Recupera el atributo sTituloNombreLatin de Persona
     * el nombre más los nexos más los apellidos (para des actas).
     *
     * @return string sTituloNombreLatin
     */
    function getTituloNombreLatin()
    {
        if (!isset($this->sTituloNombreLatin)) {
            if (!$this->bLoaded) {
                $this->DBCarregar();
            }
            $oGesNomLatin = new GestorNombreLatin();
            $nom_ap = 'Dnus. Dr. ';
            $nom_ap .= $oGesNomLatin->getVernaculaLatin($this->snom);
            $nom_ap .= !empty($this->snx1) ? ' ' . $this->snx1 : '';
            $nom_ap .= ' ' . $this->sapellido1;
            $nom_ap .= !empty($this->snx2) ? ' ' . $this->snx2 : ' ';
            $nom_ap .= !empty($this->sapellido2) ? ' ' . $this->sapellido2 : '';

            $this->sTituloNombreLatin = $nom_ap;
        }
        return $this->sTituloNombreLatin;
    }

    /**
     * Recupera el atributo sCentro_o_dl de Persona
     *
     * @return string sCentro_o_dl
     */
    function getCentro_o_dl()
    {
        if (!isset($this->sCentro_o_dl)) {
            $classname = get_class($this);
            $matches = [];
            if (preg_match('@\\\\([\w]+)$@', $classname, $matches)) {
                $classname = $matches[1];
            }
            switch ($classname) {
                case 'PersonaEx':
                case 'PersonaIn':
                    $ctr = $this->getDl();
                    break;
                case 'PersonaGlobal':
                    if (ConfigGlobal::mi_ambito() === 'rstgr') {
                        $oCentroDl = new Centro($this->getId_ctr());
                    } else {
                        $oCentroDl = new CentroDl($this->getId_ctr());
                    }
                    $ctr = $oCentroDl->getNombre_ubi();
                    break;
                case 'PersonaDl':
                case 'PersonaAgd':
                case 'PersonaN':
                case 'PersonaNax':
                case 'PersonaS':
                case 'PersonaSSSC':
                    // OJO CON las regiones de stgr
                    if (ConfigGlobal::mi_ambito() === 'rstgr') {
                        $oCentro = new Centro($this->getId_ctr());
                    } else {
                        $oCentro = new CentroDl($this->getId_ctr());
                    }
                    $ctr = $oCentro->getNombre_ubi();
                    break;
            }
            $this->sCentro_o_dl = $ctr;
        }
        return $this->sCentro_o_dl;
    }

    /**
     * Establece el valor del atributo sCentro_o_dl de Persona
     *
     * @param string sCentro_o_dl
     */
    function setCentro_o_dl($sCentro_o_dl)
    {
        $this->sCentro_o_dl = $sCentro_o_dl;
    }

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oPersonaGlobalSet = new Set();

        $oPersonaGlobalSet->add($this->getDatosId_cr());
        $oPersonaGlobalSet->add($this->getDatosId_tabla());
        $oPersonaGlobalSet->add($this->getDatosDl());
        $oPersonaGlobalSet->add($this->getDatosSacd());
        $oPersonaGlobalSet->add($this->getDatosTrato());
        $oPersonaGlobalSet->add($this->getDatosNom());
        $oPersonaGlobalSet->add($this->getDatosNx1());
        $oPersonaGlobalSet->add($this->getDatosApellido1());
        $oPersonaGlobalSet->add($this->getDatosNx2());
        $oPersonaGlobalSet->add($this->getDatosApellido2());
        $oPersonaGlobalSet->add($this->getDatosF_nacimiento());
        $oPersonaGlobalSet->add($this->getDatosLengua());
        $oPersonaGlobalSet->add($this->getDatosSituacion());
        $oPersonaGlobalSet->add($this->getDatosF_situacion());
        $oPersonaGlobalSet->add($this->getDatosApel_fam());
        $oPersonaGlobalSet->add($this->getDatosInc());
        $oPersonaGlobalSet->add($this->getDatosF_inc());
        $oPersonaGlobalSet->add($this->getDatosStgr());
        $oPersonaGlobalSet->add($this->getDatosEdad());
        $oPersonaGlobalSet->add($this->getDatosProfesion());
        $oPersonaGlobalSet->add($this->getDatosEap());
        $oPersonaGlobalSet->add($this->getDatosObserv());
        return $oPersonaGlobalSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut iid_cr de PersonaGlobal
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosId_cr()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_cr'));
        $oDatosCampo->setEtiqueta(_("id_cr"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sid_tabla de PersonaGlobal
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosId_tabla()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_tabla'));
        $oDatosCampo->setEtiqueta(_("id_tabla"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sdl de PersonaGlobal
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosDl()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'dl'));
        $oDatosCampo->setEtiqueta(_("dl"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut bsacd de PersonaGlobal
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosSacd()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'sacd'));
        $oDatosCampo->setEtiqueta(_("sacd"));
        $oDatosCampo->setTipo('check');
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut strato de PersonaGlobal
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosTrato()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'trato'));
        $oDatosCampo->setEtiqueta(_("trato"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut snom de PersonaGlobal
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosNom()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'nom'));
        $oDatosCampo->setEtiqueta(_("nombre"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut snx1 de PersonaGlobal
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosNx1()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'nx1'));
        $oDatosCampo->setEtiqueta(_("nx1"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sapellido1 de PersonaGlobal
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosApellido1()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'apellido1'));
        $oDatosCampo->setEtiqueta(_("apellido1"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut snx2 de PersonaGlobal
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosNx2()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'nx2'));
        $oDatosCampo->setEtiqueta(_("nx2"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sapellido2 de PersonaGlobal
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosApellido2()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'apellido2'));
        $oDatosCampo->setEtiqueta(_("apellido2"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut df_nacimiento de PersonaGlobal
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosF_nacimiento()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'f_nacimiento'));
        $oDatosCampo->setEtiqueta(_("fecha nacimiento"));
        $oDatosCampo->setTipo('fecha');
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut slengua de PersonaGlobal
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosLengua()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'lengua'));
        $oDatosCampo->setEtiqueta(_("lengua"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut ssituacion de PersonaGlobal
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosSituacion()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'situacion'));
        $oDatosCampo->setEtiqueta(_("situación"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut df_situacion de PersonaGlobal
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosF_situacion()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'f_situacion'));
        $oDatosCampo->setEtiqueta(_("fecha situación"));
        $oDatosCampo->setTipo('fecha');
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sapel_fam de PersonaGlobal
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosApel_fam()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'apel_fam'));
        $oDatosCampo->setEtiqueta(_("apelativo familiar"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sinc de PersonaGlobal
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosInc()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'inc'));
        $oDatosCampo->setEtiqueta(_("inc"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut df_inc de PersonaGlobal
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosF_inc()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'f_inc'));
        $oDatosCampo->setEtiqueta(_("fecha incorporación"));
        $oDatosCampo->setTipo('fecha');
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sstgr de PersonaGlobal
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosStgr()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'stgr'));
        $oDatosCampo->setEtiqueta(_("stgr"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sprofesion de PersonaGlobal
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosProfesion()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'profesion'));
        $oDatosCampo->setEtiqueta(_("profesión"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut seap de PersonaGlobal
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosEap()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'eap'));
        $oDatosCampo->setEtiqueta(_("eap"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sobserv de PersonaGlobal
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
     * Recupera les propietats de l'atribut slugar_nacimiento de PersonaGlobal
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosLugar_nacimiento()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'lugar_nacimiento'));
        $oDatosCampo->setEtiqueta(_("lugar de nacimiento"));
        return $oDatosCampo;
    }
}

PersonaGlobal::traduccion_init();