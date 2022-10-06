<?php

namespace dbextern\model\entity;

use core;
use web;

class PersonaListas extends core\ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /*
    Identif bigint NOTNULL
    ApeNom varchar (56)
    Dl vachar (5)
    Ctr varchar (40)
    Lugar_Naci varchar (45)
    Fecha_Naci date
    Email varchar (50)
    Tfno_Movil varchar(17)
    Ce varchar (40)
    Edad int (10)
    Prof_Carg varchar(350)
    Titu_Estu varchar(110)
    Encargos varchar (150)
    INCORP varchar (14)
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
     * Identif de Listas
     *
     * @var integer
     */
    private $iIdentif;
    /**
     * ApeNom de Listas
     *
     * @var string
     */
    private $sApeNom;
    /**
     * Dl de Listas
     *
     * @var string
     */
    private $sDl;
    /**
     * Ctr de Listas
     *
     * @var string
     */
    private $sCtr;
    /**
     * Lugar_Naci de Listas
     *
     * @var string
     */
    private $sLugar_Naci;
    /**
     * Fecha_Naci de Listas
     *
     * @var web\DateTimeLocal
     */
    private $dFecha_Naci;
    /**
     * Email de Listas
     *
     * @var string
     */
    private $sEmail;
    /**
     * Tfno_Movil de Listas
     *
     * @var string
     */
    private $sTfno_Movil;
    /**
     * Ce de Listas
     *
     * @var string
     */
    private $sCe;
    /**
     * Prof_Carg de Listas
     *
     * @var string
     */
    private $sProfesion_cargo;
    /**
     * Titu_Estu de Listas
     *
     * @var string
     */
    private $sTitulo_Estudios;
    /**
     * Encargos de Listas
     *
     * @var string
     */
    private $sEncargos;
    /**
     * INCORP de Listas
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
     * @var web\DateTimeLocal
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
        if (!empty($_SESSION['oDBListas']) && $_SESSION['oDBListas'] == 'error') {
            exit(_("no se puede conectar con la base de datos de Listas"));
        }
        $oDbl = $GLOBALS['oDBListas'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'Identif') && $val_id !== '') $this->iIdentif = (int)$val_id; // evitem SQL injection fent cast a integer
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->iIdentif = (integer)$a_id; // evitem SQL injection fent cast a integer
                $this->aPrimary_key = array('iIdentif' => $this->iIdentif);
            }
        }
        $this->setoDbl($oDbl);
        $this->setNomTabla('dbo.q_dl_Estudios_b');
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
        $aDades['ApeNom'] = $this->sApeNom;
        $aDades['Dl'] = $this->sDl;
        $aDades['Ctr'] = $this->sCtr;
        $aDades['Lugar_Naci'] = $this->sLugar_Naci;
        $aDades['Fecha_Naci'] = $this->dFecha_Naci;
        $aDades['Email'] = $this->sEmail;
        $aDades['Tfno_Movil'] = $this->sTfno_Movil;
        $aDades['Ce'] = $this->sCe;
        $aDades['Prof_Carg'] = $this->sProfesion_cargo;
        $aDades['Titu_Estu'] = $this->sTitulo_Estudios;
        $aDades['Encargos'] = $this->sEncargos;
        $aDades['INCORP'] = $this->sIncorporacion;
        $aDades['pertenece_r'] = $this->spertenece_r;
        $aDades['camb_fic'] = $this->scamb_fic;
        $aDades['fecha_c_fic'] = $this->dfecha_c_fic;
        $aDades['compartida_con_r'] = $this->scompartida_con_r;
        array_walk($aDades, 'core\poner_null');

        if ($bInsert === false) {
            //UPDATE
            $update = "
					ApeNom                  = :ApeNom,
					Dl                     	= :Dl,
					Ctr               		= :Ctr,
					Lugar_Naci              = :Lugar_Naci,
					Fecha_Naci              = :Fecha_Naci,
					Email              		= :Email,
					Tfno_Movil              = :Tfno_Movil,
					Ce              		= :Ce,
                    Prof_Carg               = :Prof_Carg,
                    Titu_Estu               = :Titu_Estu,
                    Encargos                = :Encargos,
                    INCORP                  = :INCORP,
                    pertenece_r             = :pertenece_r,
                    camb_fic                 = :camb_fic,
                    fecha_c_fic             = :fecha_c_fic,
                    compartida_con_r        = :compartida_con_r";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE Identif='$this->iIdentif'")) === false) {
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
            $campos = "(ApeNom,Dl,Ctr,Lugar_Naci,Fecha_Naci,Email,Tfno_Movil,Ce,Prof_Carg,Titu_Estu,Encargos,INCORP,pertenece_r,camb_fic,fecha_c_fic,compartida_con_r)";
            $valores = "(:ApeNom,:Dl,:Ctr,:Lugar_Naci,:Fecha_Naci,:Email,:Tfno_Movil,:Ce,:Prof_Carg,:Titu_Estu,:Encargos,:INCORP:pertenece_r,:camb_fic,:fecha_c_fic,:compartida_con_r)";
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
            $this->Identif = $oDbl->lastInsertId($nom_tabla . '_id_menu_seq');
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
        if (isset($this->iIdentif)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE Identif='$this->iIdentif'")) === false) {
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
        if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE Identif='$this->iIdentif'")) === false) {
            $sClauError = 'Listas.eliminar';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        return true;
    }

    /* OTROS MÉTODOS  ----------------------------------------------------------*/
    public function dividirIncorporacion()
    {
        $matches = array();
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
        $matches = array();
        $subject = $this->getCe();
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
        } else {
            $this->ice_num = '';
            $this->sce_lugar = '';
            $this->ice_ini = '';
            $this->ice_fin = '';
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
                $_token = strtolower($token);
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
        $ApeNom = $this->getApeNom();

        $nombre = '';
        $apellido1 = '';
        $apellido2 = '';
        $nx1 = '';
        $nx2 = '';

        /* separar el nombre, de los apellidos */
        $partes = explode(',', trim($ApeNom));
        $apellidos = $partes[0];
        $nombre = $partes[1];


        /* separar el apellidos completo en espacios */
        $tokens = explode(' ', trim($apellidos));
        /* array donde se guardan las "palabras" del apellidos */
        $names = array();
        /* palabras de apellidos compuetos */
        // 27.3.2019 He quitado 'san', pues hay un apellido así
        $special_tokens = array('da', 'de', 'del', 'la', 'las', 'los', 'mac', 'mc', 'van', 'von', 'y', 'i', 'santa');

        $prev = "";
        foreach ($tokens as $token) {
            $_token = strtolower($token);
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
                if ($names[1]['prep'] == 'n') {
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
    function setAllAtributes($aDades)
    {
        if (!is_array($aDades)) return;
        if (array_key_exists('id_schema', $aDades)) $this->setId_schema($aDades['id_schema']);
        if (array_key_exists('Identif', $aDades)) $this->setIdentif($aDades['Identif']);
        if (array_key_exists('ApeNom', $aDades)) $this->setApeNom($aDades['ApeNom']);
        if (array_key_exists('Dl', $aDades)) $this->setDl($aDades['Dl']);
        if (array_key_exists('Ctr', $aDades)) $this->setCtr($aDades['Ctr']);
        if (array_key_exists('Lugar_Naci', $aDades)) $this->setLugar_Naci($aDades['Lugar_Naci']);
        if (array_key_exists('Fecha_Naci', $aDades)) $this->setFecha_Naci($aDades['Fecha_Naci']);
        if (array_key_exists('Email', $aDades)) $this->setEmail($aDades['Email']);
        if (array_key_exists('Tfno_Movil', $aDades)) $this->setTfno_Movil($aDades['Tfno_Movil']);
        if (array_key_exists('Ce', $aDades)) $this->setCe($aDades['Ce']);
        if (array_key_exists('Prof_Carg', $aDades)) $this->setProfesion_cargo($aDades['Prof_Carg']);
        if (array_key_exists('Titu_Estu', $aDades)) $this->setTitulo_Estudios($aDades['Titu_Estu']);
        if (array_key_exists('Encargos', $aDades)) $this->setEncargos($aDades['Encargos']);
        if (array_key_exists('INCORP', $aDades)) $this->setIncorporacion($aDades['INCORP']);
        if (array_key_exists('pertenece_r', $aDades)) $this->setPertenece_r($aDades['pertenece_r']);
        if (array_key_exists('camb_fic', $aDades)) $this->setCamb_fic($aDades['camb_fic']);
        if (array_key_exists('fecha_c_fic', $aDades)) $this->setFecha_c_fic($aDades['fecha_c_fic']);
        if (array_key_exists('compartida_con_r', $aDades)) $this->setCompartida_con_r($aDades['compartida_con_r']);
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
        $this->setApeNom('');
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
            $this->aPrimary_key = array('Identif' => $this->iIdentif);
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
                if (($nom_id == 'Identif') && $val_id !== '') $this->iIdentif = (int)$val_id; // evitem SQL injection fent cast a integer
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
     * Recupera el atributo sApeNom de Listas
     *
     * @return string sApeNom
     */
    function getApeNom()
    {
        if (!isset($this->sApeNom) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sApeNom;
    }

    /**
     * Establece el valor del atributo sApeNom de Listas
     *
     * @param string sApeNom
     */
    function setApeNom($sApeNom)
    {
        $this->sApeNom = $sApeNom;
    }

    /**
     * Recupera el atributo sDl de Listas
     *
     * @return string sDl
     */
    function getDl()
    {
        if (!isset($this->sDl) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sDl;
    }

    /**
     * Establece el valor del atributo sDl de Listas
     *
     * @param string sDl
     */
    function setDl($sDl)
    {
        $this->sDl = $sDl;
    }

    /**
     * Recupera el atributo sCtr de Listas
     *
     * @return string sCtr
     */
    function getCtr()
    {
        if (!isset($this->sCtr) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sCtr;
    }

    /**
     * Establece el valor del atributo sCtr de Listas
     *
     * @param string sCtr
     */
    function setCtr($sCtr)
    {
        $this->sCtr = $sCtr;
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
     * @return string date dFecha_Naci
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
     * @param date dFecha_Naci
     */
    function setFecha_Naci($dFecha_Naci)
    {
        $oFecha = new \DateTime($dFecha_Naci);
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
     * Recupera el atributo iID_TABLA de Listas
     *
     * @return integer iID_TABLA
     */
    function getID_TABLA()
    {
        if (!isset($this->iID_TABLA) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iID_TABLA;
    }

    /**
     * Establece el valor del atributo iID_TABLA de Listas
     *
     * @param integer iID_TABLA
     */
    function setID_TABLA($iID_TABLA)
    {
        $this->iID_TABLA = $iID_TABLA;
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
    public function setProfesion_cargo($sProfesion_cargo)
    {
        $this->sProfesion_cargo = $sProfesion_cargo;
    }

    /**
     * @param string $sTitulo_Estudios
     */
    public function setTitulo_Estudios($sTitulo_Estudios)
    {
        $this->sTitulo_Estudios = $sTitulo_Estudios;
    }

    /**
     * @param string $sEncargos
     */
    public function setEncargos($sEncargos)
    {
        $this->sEncargos = $sEncargos;
    }

    /**
     * @param string $sIncorporacion
     */
    public function setIncorporacion($sIncorporacion)
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
     * @return \web\DateTimeLocal
     */
    public function getFecha_c_fic()
    {
        return $this->dfecha_c_fic;
    }

    /**
     * @param string $spertenece_r
     */
    public function setPertenece_r($spertenece_r)
    {
        $this->spertenece_r = $spertenece_r;
    }

    /**
     * @param string $scamb_fic
     */
    public function setCamb_fic($scamb_fic)
    {
        $this->scamb_fic = $scamb_fic;
    }

    /**
     * @param \web\DateTimeLocal $dfecha_c_fic
     */
    public function setFecha_c_fic($dfecha_c_fic)
    {
        $this->dfecha_c_fic = $dfecha_c_fic;
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
        $oListasSet = new core\Set();

        $oListasSet->add($this->getDatosApeNom());
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
     * Recupera les propietats de l'atribut sApeNom de Listas
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosApeNom()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'ApeNom'));
        $oDatosCampo->setEtiqueta(_("apellidos nombre"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sDl de Listas
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosDl()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'Dl'));
        $oDatosCampo->setEtiqueta(_("dl"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sCtr de Listas
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosCtr()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'Ctr'));
        $oDatosCampo->setEtiqueta(_("ctr"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sLugar_Naci de Listas
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosLugar_Naci()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'Lugar_Naci'));
        $oDatosCampo->setEtiqueta(_("lugar de nacimiento"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut dFecha_Naci de Listas
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosFecha_Naci()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'Fecha_Naci'));
        $oDatosCampo->setEtiqueta(_("fecha de nacimiento"));
        $oDatosCampo->setTipo('fecha');
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sEmail de Listas
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosEmail()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'Email'));
        $oDatosCampo->setEtiqueta(_("email"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sTfno_Movil de Listas
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosTfno_Movil()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'Tfno_Movil'));
        $oDatosCampo->setEtiqueta(_("teléfono móvil"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sCe de Listas
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosCe()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'Ce'));
        $oDatosCampo->setEtiqueta(_("ce"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sProfesion_cargo de Listas
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosProfesion_cargo()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'Prof_Carg'));
        $oDatosCampo->setEtiqueta(_("profesión cargo"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sEncargos de Listas
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosEncargos()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'Encargos'));
        $oDatosCampo->setEtiqueta(_("encargos"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sTitulo_Estudios de Listas
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosTitulo_estudios()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'Titu_Estu'));
        $oDatosCampo->setEtiqueta(_("titulo estudios"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sIncorporacion de Listas
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosIncorporacion()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'INCORP'));
        $oDatosCampo->setEtiqueta(_("incorporación"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sIncorporacion de Listas
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosPertenece_r()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'pertenece_r'));
        $oDatosCampo->setEtiqueta(_("Pertenece_r"));
        return $oDatosCampo;
    }
}

