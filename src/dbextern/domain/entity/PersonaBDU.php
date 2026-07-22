<?php

namespace src\dbextern\domain\entity;

use DateTime;
use src\shared\domain\traits\Hydratable;
use src\shared\domain\value_objects\DateTimeLocal;

class PersonaBDU
{
    use Hydratable;

    public function __construct()
    {
        $this->dfecha_c_fic = null;
    }

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
     * identif de Listas
     *
     * @var integer
     */
    private int $iIdentif = 0;
    /**
     * apenom de Listas
     *
     * @var string
     */
    private string $sApenom = '';
    /**
     * dl de Listas
     *
     * @var string
     */
    private string $sdl = '';
    /**
     * ctr de Listas
     *
     * @var string
     */
    private string $sctr = '';
    /**
     * lugar_naci de Listas
     *
     * @var string
     */
    private string $sLugar_Naci = '';
    private string $dFecha_Naci = '';
    /**
     * email de Listas
     *
     * @var string
     */
    private string $sEmail = '';
    /**
     * tfno_movil de Listas
     *
     * @var string
     */
    private string $sTfno_Movil = '';
    /**
     * ce de Listas
     *
     * @var string
     */
    private string $sCe = '';
    /**
     * prof_carg de Listas
     *
     * @var string
     */
    private string $sProfesion_cargo = '';
    /**
     * titu_estu de Listas
     *
     * @var string
     */
    private string $sTitulo_Estudios = '';
    /**
     * encargos de Listas
     *
     * @var string
     */
    private string $sEncargos = '';
    /**
     * incorp de Listas
     *
     * @var string
     */
    private string $sIncorporacion = '';
    /**
     * Pertenece_r de Listas
     *
     * @var string
     */
    private string $spertenece_r = '';

    /**
     * Camb_fic de Listas
     *
     * @var string
     */
    private string $scamb_fic = '';
    private DateTimeLocal|null $dfecha_c_fic = null;

    /**
     * compartida_con_r de Listas
     *
     * @var string
     */
    private string $scompartida_con_r = '';

    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * nombre de Listas
     *
     * @var string
     */
    private ?string $snombre = null;
    /**
     * apellido1 de Listas
     *
     * @var string
     */
    private ?string $sapellido1 = null;
    /**
     * apellido2 de Listas
     *
     * @var string
     */
    private ?string $sapellido2 = null;
    /**
     * Nx1 de Listas
     *
     * @var string
     */
    private ?string $snx1 = null;
    /**
     * Nx2 de Listas
     *
     * @var string
     */
    private ?string $snx2 = null;
    /**
     * ce_num de Listas
     *
     * @var string|null
     */
    private ?string $ice_num = null;
    /**
     * ce_ini de Listas
     *
     * @var integer
     */
    private string|int|null $ice_ini = null;
    /**
     * ce_fin de Listas
     *
     * @var integer
     */
    private string|int|null $ice_fin = null;
    /**
     * ce_lugar de Listas
     *
     * @var string
     */
    private ?string $sce_lugar = null;
    /**
     * inc de Listas
     *
     * @var string
     */
    private ?string $sinc = null;
    /**
     * f_inc de Listas
     *
     * @var string date
     */
    private ?string $df_inc = null;


    /* OTROS MÉTODOS  ----------------------------------------------------------*/
    public function dividirIncorporacion(): void
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

    public function dividirCe(): void
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

    public function sinPrep(string $apellido): string
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

    /** @return array{nombre: string, apellido1: string, apellido2: string} */ public function dividirNombreCompleto(): array
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
            $_token = strtolower($token);
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

    function setNullAllAtributes(): void
    {
        $this->setIdentif(0);
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

    function getIdentif(): int
    {
        return $this->iIdentif;
    }

    function setIdentif(int|string $iIdentif): void
    {
        $this->iIdentif = (int)$iIdentif;
    }

    function getApenom(): string
    {
        return $this->sApenom;
    }

    function setApenom(?string $sApenom = null): void
    {
        $this->sApenom = $sApenom ?? '';
    }

    function getDl(): string
    {
        return $this->sdl;
    }

    function setDl(?string $sdl = null): void
    {
        $this->sdl = $sdl ?? '';
    }

    function getCtr(): string
    {
        return $this->sctr;
    }

    function setCtr(?string $sctr = null): void
    {
        $this->sctr = $sctr ?? '';
    }

    function getLugar_Naci(): string
    {
        return $this->sLugar_Naci;
    }

    function setLugar_Naci(?string $sLugar_Naci = null): void
    {
        $this->sLugar_Naci = $sLugar_Naci ?? '';
    }

    function getFecha_Naci(): string
    {
        return $this->dFecha_Naci;
    }

    function setFecha_Naci(mixed $dFecha_Naci): void
    {
        if ($dFecha_Naci === null || $dFecha_Naci === '') {
            $this->dFecha_Naci = '';
            return;
        }
        if ($dFecha_Naci instanceof DateTimeLocal) {
            $this->dFecha_Naci = $dFecha_Naci->getFromLocal();
            return;
        }
        if (!is_string($dFecha_Naci)) {
            $this->dFecha_Naci = '';
            return;
        }
        $oFecha = new DateTime($dFecha_Naci);
        $new_fecha = date_format($oFecha, 'j/m/Y');
        $this->dFecha_Naci = $new_fecha;
    }

    function getEmail(): string
    {
        return $this->sEmail;
    }

    function setEmail(?string $sEmail = null): void
    {
        $this->sEmail = $sEmail ?? '';
    }

    function getTfno_Movil(): string
    {
        return $this->sTfno_Movil;
    }

    function setTfno_Movil(?string $sTfno_Movil = null): void
    {
        $this->sTfno_Movil = $sTfno_Movil ?? '';
    }

    function getCe(): string
    {
        return $this->sCe;
    }

    function setCe(?string $sCe = null): void
    {
        $this->sCe = $sCe ?? '';
    }

    public function getProfesion_cargo(): string
    {
        return $this->sProfesion_cargo;
    }

    public function getTitulo_Estudios(): string
    {
        return $this->sTitulo_Estudios;
    }

    public function getEncargos(): string
    {
        return $this->sEncargos;
    }

    public function getIncorporacion(): string
    {
        return $this->sIncorporacion;
    }


    public function setProfesion_cargo(?string $sProfesion_cargo = null): void
    {
        $this->sProfesion_cargo = $sProfesion_cargo ?? '';
    }

    public function setTitulo_Estudios(?string $sTitulo_Estudios = null): void
    {
        $this->sTitulo_Estudios = $sTitulo_Estudios ?? '';
    }

    public function setEncargos(?string $sEncargos = null): void
    {
        $this->sEncargos = $sEncargos ?? '';
    }

    public function setIncorporacion(?string $sIncorporacion = null): void
    {
        $this->sIncorporacion = $sIncorporacion ?? '';
    }


    public function getPertenece_r(): string
    {
        return $this->spertenece_r;
    }

    public function getCamb_fic(): string
    {
        return $this->scamb_fic;
    }

    public function getFecha_c_fic(): DateTimeLocal|null|string
    {
        return $this->dfecha_c_fic;
    }

    public function setPertenece_r(?string $spertenece_r = null): void
    {
        $this->spertenece_r = $spertenece_r ?? '';
    }

    public function setCamb_fic(?string $scamb_fic = null): void
    {
        $this->scamb_fic = $scamb_fic ?? '';
    }

    public function setFecha_c_fic(mixed $dfecha_c_fic): void
    {
        if ($dfecha_c_fic === null || $dfecha_c_fic === '') {
            $this->dfecha_c_fic = null;
            return;
        }
        if ($dfecha_c_fic instanceof DateTimeLocal) {
            $this->dfecha_c_fic = $dfecha_c_fic;
            return;
        }
        if (!is_string($dfecha_c_fic)) {
            $this->dfecha_c_fic = null;
            return;
        }
        $vo = DateTimeLocal::createFromLocal($dfecha_c_fic);
        $this->dfecha_c_fic = $vo instanceof DateTimeLocal ? $vo : null;
    }

    public function getCompartida_con_r(): string
    {
        return $this->scompartida_con_r;
    }

    public function setCompartida_con_r(?string $scompartida_con_r = null): void
    {
        $this->scompartida_con_r = $scompartida_con_r ?? '';
    }


    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    public function getNombre(): string
    {
        if ($this->snombre === null) {
            $this->dividirNombreCompleto();
        }
        return $this->snombre ?? '';
    }

    public function getApellido1_sinprep(): string
    {
        return $this->sinPrep($this->getApellido1());
    }

    public function getApellido1(): string
    {
        if ($this->sapellido1 === null) {
            $this->dividirNombreCompleto();
        }
        return $this->sapellido1 ?? '';
    }

    public function getApellido2(): string
    {
        if ($this->sapellido2 === null) {
            $this->dividirNombreCompleto();
        }
        return $this->sapellido2 ?? '';
    }

    public function getApellido2_sinprep(): string
    {
        return $this->sinPrep($this->getApellido2());
    }

    public function getCe_num(): string|int
    {
        if ($this->ice_num === null) {
            $this->dividirCe();
        }
        return $this->ice_num ?? '';
    }

    public function getCe_lugar(): string
    {
        if ($this->sce_lugar === null) {
            $this->dividirCe();
        }
        return $this->sce_lugar ?? '';
    }

    public function getCe_ini(): int|string
    {
        if ($this->ice_ini === null) {
            $this->dividirCe();
        }
        if ($this->ice_ini !== null && $this->ice_ini !== '') {
            $val = is_numeric($this->ice_ini) ? (int)$this->ice_ini : 0;
            if ($val > 30) {
                $this->ice_ini = $val + 1900;
            } else {
                $this->ice_ini = $val + 2000;
            }
        }
        return $this->ice_ini ?? '';
    }

    public function getCe_fin(): int|string
    {
        if ($this->ice_fin === null) {
            $this->dividirCe();
        }
        if ($this->ice_fin !== null && $this->ice_fin !== '') {
            $val = is_numeric($this->ice_fin) ? (int)$this->ice_fin : 0;
            if ($val > 60) {
                $this->ice_fin = $val + 1900;
            } else {
                $this->ice_fin = $val + 2000;
            }
        }
        return $this->ice_fin ?? '';
    }

    public function getInc(): string
    {
        if ($this->sinc === null) {
            $this->dividirIncorporacion();
        }
        return $this->sinc ?? '';
    }

    public function getF_inc(): string
    {
        if ($this->df_inc === null) {
            $this->dividirIncorporacion();
        }
        return $this->df_inc ?? '';
    }

    function getNx1(): string
    {
        if ($this->snx1 === null) {
            $this->dividirNombreCompleto();
        }
        return $this->snx1 ?? '';
    }

    function getNx2(): string
    {
        if ($this->snx2 === null) {
            $this->dividirNombreCompleto();
        }
        return $this->snx2 ?? '';
    }

}