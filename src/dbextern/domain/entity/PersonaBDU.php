<?php

namespace src\dbextern\domain\entity;

use DateTime;
use src\shared\domain\traits\Hydratable;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\NullDateTimeLocal;

class PersonaBDU
{
    use Hydratable;

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
        }
        else {
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
            }
            else if (preg_match($pattern2, $subject, $matches)) {
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
            }
            else {
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
                }
                else {
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
     * @param array $aDatos
     */
    public function setAllAttributes(array $aDatos): static
    {
        if (!is_array($aDatos)) {
            return $this;
        }
        if (array_key_exists('identif', $aDatos))
            $this->setIdentif($aDatos['identif']);
        if (array_key_exists('apenom', $aDatos))
            $this->setApenom($aDatos['apenom']);
        if (array_key_exists('dl', $aDatos))
            $this->setDl($aDatos['dl']);
        if (array_key_exists('ctr', $aDatos))
            $this->setCtr($aDatos['ctr']);
        if (array_key_exists('lugar_naci', $aDatos))
            $this->setLugar_Naci($aDatos['lugar_naci']);
        if (array_key_exists('fecha_naci', $aDatos))
            $this->setFecha_Naci($aDatos['fecha_naci']);
        if (array_key_exists('email', $aDatos))
            $this->setEmail($aDatos['email'] ?? '');
        if (array_key_exists('tfno_movil', $aDatos))
            $this->setTfno_Movil($aDatos['tfno_movil'] ?? '');
        if (array_key_exists('ce', $aDatos))
            $this->setCe($aDatos['ce'] ?? '');
        if (array_key_exists('prof_carg', $aDatos))
            $this->setProfesion_cargo($aDatos['prof_carg'] ?? '');
        if (array_key_exists('titu_estu', $aDatos))
            $this->setTitulo_Estudios($aDatos['titu_estu'] ?? '');
        if (array_key_exists('encargos', $aDatos))
            $this->setEncargos($aDatos['encargos'] ?? '');
        if (array_key_exists('incorp', $aDatos))
            $this->setIncorporacion($aDatos['incorp']);
        if (array_key_exists('pertenece_r', $aDatos))
            $this->setPertenece_r($aDatos['pertenece_r']);
        if (array_key_exists('camb_fic', $aDatos))
            $this->setCamb_fic($aDatos['camb_fic'] ?? '');
        if (array_key_exists('fecha_c_fic', $aDatos))
            $this->setFecha_c_fic($aDatos['fecha_c_fic']);
        if (array_key_exists('compartida_con_r', $aDatos))
            $this->setCompartida_con_r($aDatos['compartida_con_r'] ?? '');

        return $this;
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
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
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera el atributo iIdentif de Listas
     *
     * @return integer iIdentif
     */
    function getIdentif()
    {
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
        return $this->sApenom;
    }

    /**
     * Alias para código que usa el nombre getApeNom (p. ej. sincronización Listas).
     */
    public function getApeNom(): string
    {
        return (string) ($this->sApenom ?? '');
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
        }
        else {
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
            }
            else {
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
            }
            else {
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

}