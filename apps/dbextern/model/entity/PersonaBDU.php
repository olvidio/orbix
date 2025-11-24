<?php

namespace dbextern\model\entity;


use core\ClasePropiedades;
use core\DatosCampo;
use core\Set;
use DateTime;
use web\DateTimeLocal;
use web\NullDateTimeLocal;

class PersonaBDU extends ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /*
    identif bigint NOTNULL
    apenom varchar (56)
    dl vachar (5)
    ctr varchar (40)
    lugar_naci varchar (45)
    fecha_naci date
    email varchar (50)
    tfno_movil varchar(17)
    ce varchar (40)
    edad int (10)
    prof_carg varchar(350)
    titu_estu varchar(110)
    encargos varchar (150)
    incorp varchar (14)
    pertenece_r varchar(5)
    ---
    camb_fic varchar(1)
    fecha_c_fic date
    ---
    compartida_con_r
     */

    /**
     * aPrimary_key de Listas
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de Listas
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
     * identif de Listas
     *
     * @var integer
     */
    private $iIdentif;
    /**
     * apenom de Listas
     *
     * @var string
     */
    private $sApenom;
    /**
     * dl de Listas
     *
     * @var string
     */
    private $sdl;
    /**
     * ctr de Listas
     *
     * @var string
     */
    private $sctr;
    /**
     * lugar_naci de Listas
     *
     * @var string
     */
    private $sLugar_Naci;
    /**
     * fecha_naci de Listas
     *
     * @var DateTimeLocal
     */
    private $dFecha_Naci;
    /**
     * email de Listas
     *
     * @var string
     */
    private $sEmail;
    /**
     * tfno_movil de Listas
     *
     * @var string
     */
    private $sTfno_Movil;
    /**
     * ce de Listas
     *
     * @var string
     */
    private $sCe;
    /**
     * prof_carg de Listas
     *
     * @var string
     */
    private $sProfesion_cargo;
    /**
     * titu_estu de Listas
     *
     * @var string
     */
    private $sTitulo_Estudios;
    /**
     * encargos de Listas
     *
     * @var string
     */
    private $sEncargos;
    /**
     * incorp de Listas
     *
     * @var string
     */
    private $sIncorporacion;
    /**
     * Pertenece_r de Listas
     *
     * @var string
     */
    private $spertenece_r;

    /**
     * Camb_fic de Listas
     *
     * @var string
     */
    private $scamb_fic;
    /**
     * Fecha_c_fic de Listas
     *
     * @var DateTimeLocal
     */
    private $dfecha_c_fic;
    /**
     * compartida_con_r de Listas
     *
     * @var string
     */
    private $scompartida_con_r;

    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * nombre de Listas
     *
     * @var string
     */
    private $snombre;
    /**
     * apellido1 de Listas
     *
     * @var string
     */
    private $sapellido1;
    /**
     * apellido2 de Listas
     *
     * @var string
     */
    private $sapellido2;
    /**
     * Nx1 de Listas
     *
     * @var string
     */
    private $snx1;
    /**
     * Nx2 de Listas
     *
     * @var string
     */
    private $snx2;
    /**
     * ce_num de Listas
     *
     * @var integer
     */
    private $ice_num;
    /**
     * ce_ini de Listas
     *
     * @var integer
     */
    private $ice_ini;
    /**
     * ce_fin de Listas
     *
     * @var integer
     */
    private $ice_fin;
    /**
     * ce_lugar de Listas
     *
     * @var string
     */
    private $sce_lugar;
    /**
     * inc de Listas
     *
     * @var string
     */
    private $sinc;
    /**
     * f_inc de Listas
     *
     * @var string date
     */
    private $df_inc;

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     * Si només necessita un valor, se li pot passar un integer.
     * En general se li passa un array amb les claus primàries.
     *
     * @param integer|array iIdentif
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBP'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'identif') && $val_id !== '') $this->iIdentif = (int)$val_id;
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->iIdentif = (integer)$a_id;
                $this->aPrimary_key = array('iIdentif' => $this->iIdentif);
            }
        }
        $this->setoDbl($oDbl);
        $this->setNomTabla('tmp_bdu');
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
        $aDades['apenom'] = $this->sApenom;
        $aDades['dl'] = $this->sdl;
        $aDades['ctr'] = $this->sctr;
        $aDades['lugar_naci'] = $this->sLugar_Naci;
        $aDades['fecha_naci'] = $this->dFecha_Naci;
        $aDades['email'] = $this->sEmail;
        $aDades['tfno_movil'] = $this->sTfno_Movil;
        $aDades['ce'] = $this->sCe;
        $aDades['prof_carg'] = $this->sProfesion_cargo;
        $aDades['titu_estu'] = $this->sTitulo_Estudios;
        $aDades['encargos'] = $this->sEncargos;
        $aDades['incorp'] = $this->sIncorporacion;
        $aDades['pertenece_r'] = $this->spertenece_r;
        $aDades['camb_fic'] = $this->scamb_fic;
        $aDades['fecha_c_fic'] = $this->dfecha_c_fic;
        $aDades['compartida_con_r'] = $this->scompartida_con_r;
        array_walk($aDades, 'core\poner_null');

        if ($bInsert === false) {
            //UPDATE
            $update = "
					apenom                  = :apenom,
					dl                     	= :dl,
					ctr               		= :ctr,
					lugar_naci              = :lugar_naci,
					fecha_naci              = :fecha_naci,
					email              		= :email,
					tfno_movil              = :tfno_movil,
					ce              		= :ce,
                    prof_carg               = :prof_carg,
                    titu_estu               = :titu_estu,
                    encargos                = :encargos,
                    incorp                  = :incorp,
                    pertenece_r             = :pertenece_r,
                    camb_fic                 = :camb_fic,
                    fecha_c_fic             = :fecha_c_fic,
                    compartida_con_r        = :compartida_con_r";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE identif='$this->iIdentif'")) === false) {
                $sClauError = 'Listas.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'Listas.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        } else {
            // INSERT
            $campos = "(apenom,dl,ctr,lugar_naci,fecha_naci,email,tfno_movil,ce,prof_carg,titu_estu,encargos,incorp,pertenece_r,camb_fic,fecha_c_fic,compartida_con_r)";
            $valores = "(:apenom,:dl,:ctr,:lugar_naci,:fecha_naci,:email,:tfno_movil,:ce,:prof_carg,:titu_estu,:encargos,:incorp:pertenece_r,:camb_fic,:fecha_c_fic,:compartida_con_r)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'Listas.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'Listas.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
            $this->iIdentif = $oDbl->lastInsertId($nom_tabla . '_id_menu_seq');
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
        if (isset($this->iIdentif)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE identif='$this->iIdentif'")) === false) {
                $sClauError = 'Listas.carregar';
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
        if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE identif='$this->iIdentif'")) === false) {
            $sClauError = 'Listas.eliminar';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        return true;
    }

    /* OTROS MÉTODOS  ----------------------------------------------------------*/
    public function dividirIncorporacion()
    {
        $matches = [];
        $subject = $this->getIncorporacion();
        $pattern = '/^(\w+)\s*(\d*)-(\d*)-(\d*)/';
        if (preg_match($pattern, $subject, $matches)) {
            $this->sinc = substr($matches[1], 0, 2); // Aparace una nueva: elc, pero solo tengo 2 caracteres (el).
            $dia = $matches[2];
            $mes = $matches[3];
            $any = $matches[4];
            // iso
            $this->df_inc = "$any-$mes-$dia";
        } else {
            $this->ice_num = '';
            $this->sce_lugar = '';
            $this->ice_ini = '';
            $this->ice_fin = '';
        }
    }

    public function dividirCe()
    {
        $matches = [];
        $this->ice_num = '';
        $this->sce_lugar = '';
        $this->ice_ini = '';
        $this->ice_fin = '';

        $subject = $this->getCe();
        if (!empty($subject)) {
            $pattern = '/^(\d?)(\w+.*)[,:]\s*(\d*)-(\d*)/';
            $pattern2 = '/^(\d*)-(\d*)/';
            if (preg_match($pattern, $subject, $matches)) {
                $this->ice_num = $matches[1];
                $this->sce_lugar = $matches[2];
                $this->ice_ini = $matches[3];
                $this->ice_fin = $matches[4];
            } else if (preg_match($pattern2, $subject, $matches)) {
                $this->ice_num = '';
                $this->sce_lugar = '';
                $this->ice_ini = $matches[1];
                $this->ice_fin = $matches[2];
            }
        }
    }

    public function sinPrep($apellido)
    {
        /* separar el apellidos completo en espacios */
        $tokens = explode(' ', trim($apellido));
        $names = "";
        /* palabras de apellidos compuetos */
        // 27.3.2019 He quitado 'san', pues hay un apellido así
        $special_tokens = array('da', 'de', 'del', 'la', 'las', 'los', 'mac', 'mc', 'van', 'von', 'y', 'i', 'santa');

        //Sólo si la prep está al inicio
        $i = 0;
        foreach ($tokens as $token) {
            if ($i == 0) {
                $_token = strtolower($token ?? '');
                if (in_array($_token, $special_tokens)) {
                    continue;
                }
            }
            $names .= " " . $token;
            $i++;
        }
        return trim($names);
    }

    public function dividirNombreCompleto()
    {
        $Apenom = $this->getApenom();

        $nombre = '';
        $apellido1 = '';
        $apellido2 = '';
        $nx1 = '';
        $nx2 = '';

        /* separar el nombre, de los apellidos */
        $partes = explode(',', trim($Apenom));
        $apellidos = $partes[0];
        $nombre = empty($partes[1]) ? '' : $partes[1];

        /* separar el apellidos completo en espacios */
        $tokens = explode(' ', trim($apellidos));
        /* array donde se guardan las "palabras" del apellidos */
        $names = [];
        /* palabras de apellidos compuestos */
        // 27.3.2019 He quitado 'san', pues hay un apellido así
        $special_tokens = array('da', 'de', 'del', 'la', 'las', 'los', 'mac', 'mc', 'van', 'von', 'y', 'i', 'santa');

        $prev = "";
        foreach ($tokens as $token) {
            $_token = strtolower($token ?? '');
            if (in_array($_token, $special_tokens)) {
                $prev .= "$token ";
            } else {
                $prep = empty($prev) ? 'n' : 's';
                $names[] = array('txt' => $prev . $token, 'prep' => $prep, 'nx' => $prev);
                $prev = "";
            }
        }

        $num_nombres = count($names);
        $nombres = $apellidos = "";
        switch ($num_nombres) {
            case 0:
                $apellido1 = '';
                break;
            case 1:
                $apellido1 = $names[0]['txt'];
                $nx1 = $names[0]['nx'];
                break;
            case 2:
                $apellido1 = $names[0]['txt'];
                $nx1 = $names[0]['nx'];
                $apellido2 = $names[1]['txt'];
                $nx2 = $names[1]['nx'];
                break;
            case 3:
                //con preposicion o sin preposicion
                if ($names[1]['prep'] === 'n') {
                    $apellido1 = $names[0]['txt'];
                    $nx1 = $names[0]['nx'];
                    $apellido2 = $names[1]['txt'] . ' ' . $names[2]['txt'];
                    $nx2 = $names[1]['nx'];
                } else {
                    $apellido1 = $names[0]['txt'] . ' ' . $names[1]['txt'];
                    $nx1 = $names[0]['nx'];
                    $apellido2 = $names[2]['txt'];
                    $nx2 = $names[2]['nx'];
                }
                break;
            case 4:
                $apellido1 = $names[0]['txt'] . ' ' . $names[1]['txt'];
                $nx1 = $names[0]['nx'];
                $apellido2 = $names[2]['txt'] . ' ' . $names[3]['txt'];
                $nx2 = $names[2]['nx'];
                break;
        }

        //$nombres    = mb_convert_case($nombres, MB_CASE_TITLE, 'UTF-8');
        //$apellidos  = mb_convert_case($apellidos, MB_CASE_TITLE, 'UTF-8');

        $this->snombre = trim($nombre);
        $this->sapellido1 = trim($apellido1);
        $this->sapellido2 = trim($apellido2);
        $this->snx1 = trim($nx1);
        $this->snx2 = trim($nx2);

        return array('nombre' => $this->snombre, 'apellido1' => $this->sapellido1, 'apellido2' => $this->sapellido2);
    }

    /* MÉTODOS PRIVADOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDades
     */
    function setAllAttributes(array $aDades)
    {
        if (!is_array($aDades)) return;
        if (array_key_exists('id_schema', $aDades)) $this->setId_schema($aDades['id_schema']);
        if (array_key_exists('identif', $aDades)) $this->setIdentif($aDades['identif']);
        if (array_key_exists('apenom', $aDades)) $this->setApenom($aDades['apenom']);
        if (array_key_exists('dl', $aDades)) $this->setDl($aDades['dl']);
        if (array_key_exists('ctr', $aDades)) $this->setCtr($aDades['ctr']);
        if (array_key_exists('lugar_naci', $aDades)) $this->setLugar_Naci($aDades['lugar_naci']);
        if (array_key_exists('fecha_naci', $aDades)) $this->setFecha_Naci($aDades['fecha_naci']);
        if (array_key_exists('email', $aDades)) $this->setEmail($aDades['email']??'');
        if (array_key_exists('tfno_movil', $aDades)) $this->setTfno_Movil($aDades['tfno_movil']??'');
        if (array_key_exists('ce', $aDades)) $this->setCe($aDades['ce']??'');
        if (array_key_exists('prof_carg', $aDades)) $this->setProfesion_cargo($aDades['prof_carg']??'');
        if (array_key_exists('titu_estu', $aDades)) $this->setTitulo_Estudios($aDades['titu_estu']??'');
        if (array_key_exists('encargos', $aDades)) $this->setEncargos($aDades['encargos']??'');
        if (array_key_exists('incorp', $aDades)) $this->setIncorporacion($aDades['incorp']);
        if (array_key_exists('pertenece_r', $aDades)) $this->setPertenece_r($aDades['pertenece_r']);
        if (array_key_exists('camb_fic', $aDades)) $this->setCamb_fic($aDades['camb_fic']??'');
        if (array_key_exists('fecha_c_fic', $aDades)) $this->setFecha_c_fic($aDades['fecha_c_fic']);
        if (array_key_exists('compartida_con_r', $aDades)) $this->setCompartida_con_r($aDades['compartida_con_r']??'');
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_schema('');
        $this->setIdentif('');
        $this->setApenom('');
        $this->setDl('');
        $this->setCtr('');
        $this->setLugar_Naci('');
        $this->setFecha_Naci('');
        $this->setEmail('');
        $this->setTfno_Movil('');
        $this->setCe('');
        $this->setProfesion_cargo('');
        $this->setTitulo_Estudios('');
        $this->setEncargos('');
        $this->setIncorporacion('');
        $this->setPertenece_r('');
        $this->setCamb_fic('');
        $this->setFecha_c_fic('');
        $this->setCompartida_con_r('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/
    /**
     * Recupera todos los atributos de Listas en un array
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
     * Recupera la clave primaria de Listas en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('identif' => $this->iIdentif);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de Listas en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'identif') && $val_id !== '') $this->iIdentif = (int)$val_id;
            }
        }
    }

    /**
     * Recupera el atributo iIdentif de Listas
     *
     * @return integer iIdentif
     */
    function getIdentif()
    {
        if (!isset($this->iIdentif) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iIdentif;
    }

    /**
     * Establece el valor del atributo iIdentif de Listas
     *
     * @param integer iIdentif
     */
    function setIdentif($iIdentif)
    {
        $this->iIdentif = $iIdentif;
    }

    /**
     * Recupera el atributo sApenom de Listas
     *
     * @return string sApenom
     */
    function getApenom()
    {
        if (!isset($this->sApenom) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sApenom;
    }

    /**
     * Establece el valor del atributo sApenom de Listas
     *
     * @param string sApenom
     */
    function setApenom($sApenom)
    {
        $this->sApenom = $sApenom;
    }

    /**
     * Recupera el atributo sdl de Listas
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
     * Establece el valor del atributo sdl de Listas
     *
     * @param string sdl
     */
    function setDl($sdl)
    {
        $this->sdl = $sdl;
    }

    /**
     * Recupera el atributo sctr de Listas
     *
     * @return string sctr
     */
    function getCtr()
    {
        if (!isset($this->sctr) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sctr;
    }

    /**
     * Establece el valor del atributo sdl de Listas
     *
     * @param string sctr
     */
    function setCtr($sctr)
    {
        $this->sctr = $sctr;
    }

    /**
     * Recupera el atributo sLugar_Naci de Listas
     *
     * @return string sLugar_Naci
     */
    function getLugar_Naci()
    {
        if (!isset($this->sLugar_Naci) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sLugar_Naci;
    }

    /**
     * Establece el valor del atributo sLugar_Naci de Listas
     *
     * @param string sLugar_Naci
     */
    function setLugar_Naci($sLugar_Naci)
    {
        $this->sLugar_Naci = $sLugar_Naci;
    }

    /**
     * Recupera el atributo dFecha_Naci de Listas
     *
     * @return string|DateTimeLocal
     */
    function getFecha_Naci()
    {
        if (!isset($this->dFecha_Naci) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->dFecha_Naci;
    }

    /**
     * Establece el valor del atributo dFecha_Naci de Listas
     *
     * @param null|string|DateTimeLocal dFecha_Naci
     */
    function setFecha_Naci($dFecha_Naci)
    {
        $oFecha = new DateTime($dFecha_Naci);
        $new_fecha = date_format($oFecha, 'j/m/Y');
        $this->dFecha_Naci = $new_fecha;
    }

    /**
     * Recupera el atributo sEmail de Listas
     *
     * @return string sEmail
     */
    function getEmail()
    {
        if (!isset($this->sEmail) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sEmail;
    }

    /**
     * Establece el valor del atributo sEmail de Listas
     *
     * @param string sEmail
     */
    function setEmail($sEmail)
    {
        $this->sEmail = $sEmail;
    }

    /**
     * Recupera el atributo sTfno_Movil de Listas
     *
     * @return string sTfno_Movil
     */
    function getTfno_Movil()
    {
        if (!isset($this->sTfno_Movil) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sTfno_Movil;
    }

    /**
     * Establece el valor del atributo sTfno_Movil de Listas
     *
     * @param string sTfno_Movil
     */
    function setTfno_Movil($sTfno_Movil)
    {
        $this->sTfno_Movil = $sTfno_Movil;
    }

    /**
     * Recupera el atributo sCe de Listas
     *
     * @return string sCe
     */
    function getCe()
    {
        if (!isset($this->sCe) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sCe;
    }

    /**
     * Establece el valor del atributo sCe de Listas
     *
     * @param string sCe
     */
    function setCe($sCe)
    {
        $this->sCe = $sCe;
    }

    /**
     * @return string
     */
    public function getProfesion_cargo()
    {
        return $this->sProfesion_cargo;
    }

    /**
     * @return string
     */
    public function getTitulo_Estudios()
    {
        return $this->sTitulo_Estudios;
    }

    /**
     * @return string
     */
    public function getEncargos()
    {
        return $this->sEncargos;
    }

    /**
     * @return string
     */
    public function getIncorporacion()
    {
        return $this->sIncorporacion;
    }


    /**
     * @param string $sProfesion_cargo
     */
    public function setProfesion_cargo(string $sProfesion_cargo)
    {
        $this->sProfesion_cargo = $sProfesion_cargo;
    }

    /**
     * @param string $sTitulo_Estudios
     */
    public function setTitulo_Estudios(string $sTitulo_Estudios)
    {
        $this->sTitulo_Estudios = $sTitulo_Estudios;
    }

    /**
     * @param string $sEncargos
     */
    public function setEncargos(string $sEncargos)
    {
        $this->sEncargos = $sEncargos;
    }

    /**
     * @param string $sIncorporacion
     */
    public function setIncorporacion(string $sIncorporacion)
    {
        $this->sIncorporacion = $sIncorporacion;
    }


    /**
     * @return string
     */
    public function getPertenece_r()
    {
        return $this->spertenece_r;
    }

    /**
     * @return string
     */
    public function getCamb_fic()
    {
        return $this->scamb_fic;
    }

    /**
     * @return DateTimeLocal
     */
    public function getFecha_c_fic()
    {
        return $this->dfecha_c_fic;
    }

    /**
     * @param string $spertenece_r
     */
    public function setPertenece_r(string $spertenece_r)
    {
        $this->spertenece_r = $spertenece_r;
    }

    /**
     * @param string $scamb_fic
     */
    public function setCamb_fic(string $scamb_fic)
    {
        $this->scamb_fic = $scamb_fic;
    }

    /**
     * @param null|string|DateTimeLocal $dfecha_c_fic
     */
    public function setFecha_c_fic($dfecha_c_fic)
    {
        if (empty($dfecha_c_fic)) {
            $this->dfecha_c_fic = new NullDateTimeLocal();
        } else {
            $oFecha = new DateTime($dfecha_c_fic);
            $new_fecha = date_format($oFecha, 'j/m/Y');
            $this->dfecha_c_fic = $new_fecha;
        }
    }

    /**
     * @return string
     */
    public function getCompartida_con_r()
    {
        return $this->scompartida_con_r;
    }

    /**
     * @param string $spertenece_r
     */
    public function setCompartida_con_r($scompartida_con_r)
    {
        $this->scompartida_con_r = $scompartida_con_r;
    }


    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    public function getNombre()
    {
        if (!isset($this->snombre)) {
            $this->dividirNombreCompleto();
        }
        return $this->snombre;
    }

    public function getApellido1_sinprep()
    {
        return $this->sinPrep($this->getApellido1());
    }

    public function getApellido1()
    {
        if (!isset($this->sapellido1)) {
            $this->dividirNombreCompleto();
        }
        return $this->sapellido1;
    }

    public function getApellido2()
    {
        if (!isset($this->sapellido2)) {
            $this->dividirNombreCompleto();
        }
        return $this->sapellido2;
    }

    public function getApellido2_sinprep()
    {
        return $this->sinPrep($this->getApellido2());
    }

    public function getCe_num()
    {
        if (!isset($this->ice_num)) {
            $this->dividirCe();
        }
        return $this->ice_num;
    }

    public function getCe_lugar()
    {
        if (!isset($this->sce_lugar)) {
            $this->dividirCe();
        }
        return $this->sce_lugar;
    }

    public function getCe_ini()
    {
        if (!isset($this->ice_ini)) {
            $this->dividirCe();
        }
        if (!empty($this->ice_ini)) {
            if ($this->ice_ini > 30) {
                $this->ice_ini = $this->ice_ini + 1900;
            } else {
                $this->ice_ini = $this->ice_ini + 2000;
            }
        }
        return $this->ice_ini;
    }

    public function getCe_fin()
    {
        if (!isset($this->ice_fin)) {
            $this->dividirCe();
        }
        if (!empty($this->ice_fin)) {
            if ($this->ice_fin > 60) {
                $this->ice_fin = $this->ice_fin + 1900;
            } else {
                $this->ice_fin = $this->ice_fin + 2000;
            }
        }
        return $this->ice_fin;
    }

    public function getInc()
    {
        if (!isset($this->sinc)) {
            $this->dividirIncorporacion();
        }
        return $this->sinc;
    }

    /**
     *
     * @return string fecha iso
     */
    public function getF_inc()
    {
        if (!isset($this->df_inc)) {
            $this->dividirIncorporacion();
        }
        return $this->df_inc;
    }

    /**
     * Recupera el atributo snx1 de PersonaListas
     *
     * @return string snx1
     */
    function getNx1()
    {
        if (!isset($this->snx1)) {
            $this->dividirNombreCompleto();
        }
        return $this->snx1;
    }

    /**
     * Recupera el atributo snx2 de PersonaListas
     *
     * @return string snx2
     */
    function getNx2()
    {
        if (!isset($this->snx2)) {
            $this->dividirNombreCompleto();
        }
        return $this->snx2;
    }

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oListasSet = new Set();

        $oListasSet->add($this->getDatosApenom());
        $oListasSet->add($this->getDatosDl());
        $oListasSet->add($this->getDatosCtr());
        $oListasSet->add($this->getDatosLugar_Naci());
        $oListasSet->add($this->getDatosFecha_Naci());
        $oListasSet->add($this->getDatosEmail());
        $oListasSet->add($this->getDatosTfno_Movil());
        $oListasSet->add($this->getDatosCe());
        $oListasSet->add($this->getDatosProfesion_cargo());
        $oListasSet->add($this->getDatosTitulo_estudios());
        $oListasSet->add($this->getDatosEncargos());
        $oListasSet->add($this->getDatosIncorporacion());
        return $oListasSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut sApenom de Listas
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosApenom()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'apenom'));
        $oDatosCampo->setEtiqueta(_("apellidos, nombre"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sdl de Listas
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
     * Recupera les propietats de l'atribut sctr de Listas
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosCtr()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'Ctr'));
        $oDatosCampo->setEtiqueta(_("ctr"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sLugar_Naci de Listas
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosLugar_Naci()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'lugar_naci'));
        $oDatosCampo->setEtiqueta(_("lugar de nacimiento"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut dFecha_Naci de Listas
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosFecha_Naci()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'fecha_naci'));
        $oDatosCampo->setEtiqueta(_("fecha de nacimiento"));
        $oDatosCampo->setTipo('fecha');
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sEmail de Listas
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosEmail()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'email'));
        $oDatosCampo->setEtiqueta(_("email"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sTfno_Movil de Listas
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosTfno_Movil()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'tfno_movil'));
        $oDatosCampo->setEtiqueta(_("teléfono móvil"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sCe de Listas
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosCe()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'ce'));
        $oDatosCampo->setEtiqueta(_("ce"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sProfesion_cargo de Listas
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosProfesion_cargo()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'prof_carg'));
        $oDatosCampo->setEtiqueta(_("profesión cargo"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sEncargos de Listas
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosEncargos()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'encargos'));
        $oDatosCampo->setEtiqueta(_("encargos"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sTitulo_Estudios de Listas
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosTitulo_estudios()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'titu_estu'));
        $oDatosCampo->setEtiqueta(_("titulo estudios"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sIncorporacion de Listas
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosIncorporacion()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'incorp'));
        $oDatosCampo->setEtiqueta(_("incorporación"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sIncorporacion de Listas
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosPertenece_r()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'pertenece_r'));
        $oDatosCampo->setEtiqueta(_("Pertenece_r"));
        return $oDatosCampo;
    }
}

