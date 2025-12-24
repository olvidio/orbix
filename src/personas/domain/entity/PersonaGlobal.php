<?php

namespace src\personas\domain\entity;

use core\ConfigGlobal;
use ReflectionClass;
use src\actividades\domain\value_objects\NivelStgrId;
use src\personas\domain\value_objects\{ApelFamText,
    EapText,
    IncCode,
    LenguaCode,
    LugarNacimientoText,
    ObservText,
    PersonaApellido1Text,
    PersonaApellido2Text,
    PersonaNombreText,
    PersonaNx1Text,
    PersonaNx2Text,
    PersonaTablaCode,
    ProfesionText,
    SituacionCode};
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\value_objects\DelegacionCode;
use web\DateTimeLocal;
use web\NullDateTimeLocal;
use function core\is_true;
use function core\strtoupper_dlb;

/**
 * Clase que implementa la entidad personas_dl
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 9/12/2025
 */
class PersonaGlobal
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_schema de PersonaDl
     *
     * @var int
     */
    private int $iid_schema;
    /**
     * Id_nom de PersonaDl
     *
     * @var int
     */
    private int $iid_nom;
    /**
     * Id_tabla de PersonaDl
     *
     * @var string
     */
    private string $sid_tabla;
    /**
     * Dl de PersonaDl
     *
     * @var string|null
     */
    private string|null $sdl = null;
    /**
     * Sacd de PersonaDl
     *
     * @var bool|null
     */
    private bool|null $bsacd = null;
    /**
     * Trato de PersonaDl
     *
     * @var string|null
     */
    private string|null $strato = null;
    /**
     * Nom de PersonaDl
     *
     * @var string|null
     */
    private string|null $snom = null;
    /**
     * Nx1 de PersonaDl
     *
     * @var string|null
     */
    private string|null $snx1 = null;
    /**
     * Apellido1 de PersonaDl
     *
     * @var string
     */
    private string $sapellido1;
    /**
     * Nx2 de PersonaDl
     *
     * @var string|null
     */
    private string|null $snx2 = null;
    /**
     * Apellido2 de PersonaDl
     *
     * @var string|null
     */
    private string|null $sapellido2 = null;
    /**
     * F_nacimiento de PersonaDl
     *
     * @var DateTimeLocal|null
     */
    private DateTimeLocal|null $df_nacimiento = null;
    /**
     * Lengua de PersonaDl
     *
     * @var string|null
     */
    private string|null $idioma_preferido = null;
    /**
     * Situacion de PersonaDl
     *
     * @var string
     */
    private string $ssituacion;
    /**
     * F_situacion de PersonaDl
     *
     * @var DateTimeLocal|null
     */
    private DateTimeLocal|null $df_situacion = null;
    /**
     * Apel_fam de PersonaDl
     *
     * @var string|null
     */
    private string|null $sapel_fam = null;
    /**
     * Inc de PersonaDl
     *
     * @var string|null
     */
    private string|null $sinc = null;
    /**
     * F_inc de PersonaDl
     *
     * @var DateTimeLocal|null
     */
    private DateTimeLocal|null $df_inc = null;
    /**
     * Stgr de PersonaDl
     *
     * @var string|null
     */
    private int|null $inivel_stgr = null;
    /**
     * Profesion de PersonaDl
     *
     * @var string|null
     */
    private string|null $sprofesion = null;
    /**
     * Eap de PersonaDl
     *
     * @var string|null
     */
    private string|null $seap = null;
    /**
     * Observ de PersonaDl
     *
     * @var string|null
     */
    private string|null $sobserv = null;
    /**
     * Id_ctr de PersonaDl
     *
     * @var int|null
     */
    private int|null $iid_ctr = null;
    /**
     * Lugar_nacimiento de PersonaDl
     *
     * @var string|null
     */
    private string|null $slugar_nacimiento = null;

    private ?bool $bes_publico = false;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return PersonaDl
     */
    public function setAllAttributes(array $aDatos): PersonaGlobal
    {
        if (array_key_exists('id_schema', $aDatos)) {
            $this->setId_schema($aDatos['id_schema']);
        }
        if (array_key_exists('id_nom', $aDatos)) {
            $this->setId_nom($aDatos['id_nom']);
        }
        if (array_key_exists('id_tabla', $aDatos)) {
            $this->setId_tabla($aDatos['id_tabla']);
        }
        if (array_key_exists('dl', $aDatos)) {
            $this->setDl($aDatos['dl']);
        }
        if (array_key_exists('sacd', $aDatos)) {
            $this->setSacd(is_true($aDatos['sacd']));
        }
        if (array_key_exists('trato', $aDatos)) {
            $this->setTrato($aDatos['trato']);
        }
        if (array_key_exists('nom', $aDatos)) {
            $this->setNom($aDatos['nom']);
        }
        if (array_key_exists('nx1', $aDatos)) {
            $this->setNx1($aDatos['nx1']);
        }
        if (array_key_exists('apellido1', $aDatos)) {
            $this->setApellido1($aDatos['apellido1']);
        }
        if (array_key_exists('nx2', $aDatos)) {
            $this->setNx2($aDatos['nx2']);
        }
        if (array_key_exists('apellido2', $aDatos)) {
            $this->setApellido2($aDatos['apellido2']);
        }
        if (array_key_exists('f_nacimiento', $aDatos)) {
            $this->setF_nacimiento($aDatos['f_nacimiento']);
        }
        if (array_key_exists('idioma_preferido', $aDatos)) {
            $this->setIdioma_preferido($aDatos['idioma_preferido']);
        }
        if (array_key_exists('situacion', $aDatos)) {
            $this->setSituacion($aDatos['situacion']);
        }
        if (array_key_exists('f_situacion', $aDatos)) {
            $this->setF_situacion($aDatos['f_situacion']);
        }
        if (array_key_exists('apel_fam', $aDatos)) {
            $this->setApel_fam($aDatos['apel_fam']);
        }
        if (array_key_exists('inc', $aDatos)) {
            $this->setInc($aDatos['inc']);
        }
        if (array_key_exists('f_inc', $aDatos)) {
            $this->setF_inc($aDatos['f_inc']);
        }
        if (array_key_exists('nivel_stgr', $aDatos)) {
            $this->setNivel_stgr($aDatos['nivel_stgr']);
        }
        if (array_key_exists('profesion', $aDatos)) {
            $this->setProfesion($aDatos['profesion']);
        }
        if (array_key_exists('eap', $aDatos)) {
            $this->setEap($aDatos['eap']);
        }
        if (array_key_exists('observ', $aDatos)) {
            $this->setObserv($aDatos['observ']);
        }
        if (array_key_exists('id_ctr', $aDatos)) {
            $this->setId_ctr($aDatos['id_ctr']);
        }
        if (array_key_exists('lugar_nacimiento', $aDatos)) {
            $this->setLugar_nacimiento($aDatos['lugar_nacimiento']);
        }
        if (array_key_exists('es_publico', $aDatos)) {
            $this->setEs_publico($aDatos['es_publico']);
        }
        return $this;
    }

    /**
     *
     * @return int $iid_schema
     */
    public function getId_schema(): int
    {
        return $this->iid_schema;
    }
    /**
     *
     * @param int $iid_schema
     */
    public function setId_schema(int $iid_schema): void
    {
        $this->iid_schema = $iid_schema;
    }

    /**
     *
     * @return int $iid_nom
     */
    public function getId_nom(): int
    {
        return $this->iid_nom;
    }

    /**
     *
     * @param int $iid_nom
     */
    public function setId_nom(int $iid_nom): void
    {
        $this->iid_nom = $iid_nom;
    }

    /**
     *
     * @return string $sid_tabla
     */
    public function getId_tabla(): string
    {
        return $this->sid_tabla;
    }

    /**
     *
     * @param string $sid_tabla
     */
    public function setId_tabla(string $sid_tabla): void
    {
        $this->sid_tabla = $sid_tabla;
    }

    // VO API -------------------------------------------------------------------
    public function getIdTablaVo(): PersonaTablaCode
    {
        return new PersonaTablaCode($this->sid_tabla);
    }

    public function setIdTablaVo(PersonaTablaCode $idTabla): void
    {
        $this->sid_tabla = $idTabla->value();
    }

    /**
     *
     * @return string|null $sdl
     */
    /**
     * @deprecated use getDlVo() instead
     */
    public function getDl(): ?string
    {
        return $this->sdl;
    }

    /**
     *
     * @param string|null $sdl
     */
    /**
     * @deprecated use setDlVo() instead
     */
    public function setDl(?string $sdl = null): void
    {
        $this->sdl = $sdl;
    }

    public function getDlVo(): ?DelegacionCode
    {
        return isset($this->sdl) && $this->sdl !== '' ? new DelegacionCode($this->sdl) : null;
    }

    public function setDlVo(?DelegacionCode $dl = null): void
    {
        $this->sdl = $dl?->value();
    }

    /**
     *
     * @return bool|null $bsacd
     */
    public function isSacd(): ?bool
    {
        return $this->bsacd;
    }

    /**
     *
     * @param bool|null $bsacd
     */
    public function setSacd(?bool $bsacd = null): void
    {
        $this->bsacd = $bsacd;
    }

    /**
     *
     * @return string|null $strato
     */
    /**
     * @deprecated use getTratoVo() instead
     */
    public function getTrato(): ?string
    {
        return $this->strato;
    }

    /**
     *
     * @param string|null $strato
     */
    /**
     * @deprecated use setTratoVo() instead
     */
    public function setTrato(?string $strato = null): void
    {
        $this->strato = $strato;
    }

    public function getTratoVo(): ?\src\personas\domain\value_objects\PersonaTratoCode
    {
        return \src\personas\domain\value_objects\PersonaTratoCode::fromNullableString($this->strato);
    }

    public function setTratoVo(?\src\personas\domain\value_objects\PersonaTratoCode $trato = null): void
    {
        $this->strato = $trato?->value();
    }

    /**
     *
     * @return string|null $snom
     */
    /**
     * @deprecated use getNomVo() instead
     */
    public function getNom(): ?string
    {
        return $this->snom;
    }

    /**
     *
     * @param string|null $snom
     */
    /**
     * @deprecated use setNomVo() instead
     */
    public function setNom(?string $snom = null): void
    {
        $this->snom = $snom;
    }

    public function getNomVo(): ?PersonaNombreText
    {
        return PersonaNombreText::fromNullableString($this->snom);
    }

    public function setNomVo(?PersonaNombreText $nom = null): void
    {
        $this->snom = $nom?->value();
    }

    /**
     *
     * @return string|null $snx1
     */
    /**
     * @deprecated use getNx1Vo() instead
     */
    public function getNx1(): ?string
    {
        return $this->snx1;
    }

    /**
     *
     * @param string|null $snx1
     */
    /**
     * @deprecated use setNx1Vo() instead
     */
    public function setNx1(?string $snx1 = null): void
    {
        $this->snx1 = $snx1;
    }

    public function getNx1Vo(): ?PersonaNx1Text
    {
        return PersonaNx1Text::fromNullableString($this->snx1);
    }

    public function setNx1Vo(?PersonaNx1Text $nx1 = null): void
    {
        $this->snx1 = $nx1?->value();
    }

    /**
     *
     * @return string $sapellido1
     */
    /**
     * @deprecated use getApellido1Vo() instead
     */
    public function getApellido1(): string
    {
        return $this->sapellido1;
    }

    /**
     *
     * @param string $sapellido1
     */
    /**
     * @deprecated use setApellido1Vo() instead
     */
    public function setApellido1(string $sapellido1): void
    {
        $this->sapellido1 = $sapellido1;
    }

    public function getApellido1Vo(): PersonaApellido1Text
    {
        return PersonaApellido1Text::fromString($this->sapellido1);
    }

    public function setApellido1Vo(PersonaApellido1Text $apellido1): void
    {
        $this->sapellido1 = $apellido1->value();
    }

    /**
     *
     * @return string|null $snx2
     */
    /**
     * @deprecated use getNx2Vo() instead
     */
    public function getNx2(): ?string
    {
        return $this->snx2;
    }

    /**
     *
     * @param string|null $snx2
     */
    /**
     * @deprecated use setNx2Vo() instead
     */
    public function setNx2(?string $snx2 = null): void
    {
        $this->snx2 = $snx2;
    }

    public function getNx2Vo(): ?PersonaNx2Text
    {
        return PersonaNx2Text::fromNullableString($this->snx2);
    }

    public function setNx2Vo(?PersonaNx2Text $nx2 = null): void
    {
        $this->snx2 = $nx2?->value();
    }

    /**
     *
     * @return string|null $sapellido2
     */
    /**
     * @deprecated use getApellido2Vo() instead
     */
    public function getApellido2(): ?string
    {
        return $this->sapellido2;
    }

    /**
     *
     * @param string|null $sapellido2
     */
    /**
     * @deprecated use setApellido2Vo() instead
     */
    public function setApellido2(?string $sapellido2 = null): void
    {
        $this->sapellido2 = $sapellido2;
    }

    public function getApellido2Vo(): ?PersonaApellido2Text
    {
        return PersonaApellido2Text::fromNullableString($this->sapellido2);
    }

    public function setApellido2Vo(?PersonaApellido2Text $apellido2 = null): void
    {
        $this->sapellido2 = $apellido2?->value();
    }

    /**
     *
     * @return DateTimeLocal|NullDateTimeLocal|null $df_nacimiento
     */
    public function getF_nacimiento(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->df_nacimiento ?? new NullDateTimeLocal;
    }

    /**
     *
     * @param DateTimeLocal|null $df_nacimiento
     */
    public function setF_nacimiento(DateTimeLocal|null $df_nacimiento = null): void
    {
        $this->df_nacimiento = $df_nacimiento;
    }

    /**
     *
     * @return string|null $slengua
     */
    /**
     * @deprecated use getIdiomaPreferidoVo() instead
     */
    public function getIdioma_preferido(): ?string
    {
        return $this->idioma_preferido;
    }

    /**
     *
     * @param string|null $slengua
     */
    /**
     * @deprecated use setIdiomaPreferidoVo() instead
     */
    public function setIdioma_preferido(?string $idioma_preferido = null): void
    {
        $this->idioma_preferido = $idioma_preferido;
    }

    public function getIdiomaPreferido(): ?LenguaCode
    {
        return LenguaCode::fromNullableString($this->idioma_preferido);
    }

    public function setIdiomaPreferidoVo(?LenguaCode $lengua = null): void
    {
        $this->idioma_preferido = $lengua?->value();
    }

    /**
     *
     * @return string $ssituacion
     */
    /**
     * @deprecated use getSituacionVo() instead
     */
    public function getSituacion(): string
    {
        return $this->ssituacion;
    }

    /**
     *
     * @param string $ssituacion
     */
    /**
     * @deprecated use setSituacionVo() instead
     */
    public function setSituacion(string $ssituacion): void
    {
        $this->ssituacion = $ssituacion;
    }

    public function getSituacionVo(): SituacionCode
    {
        return new SituacionCode($this->ssituacion);
    }

    public function setSituacionVo(SituacionCode $situacion): void
    {
        $this->ssituacion = $situacion->value();
    }

    /**
     *
     * @return DateTimeLocal|NullDateTimeLocal|null $df_situacion
     */
    public function getF_situacion(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->df_situacion ?? new NullDateTimeLocal;
    }

    /**
     *
     * @param DateTimeLocal|null $df_situacion
     */
    public function setF_situacion(DateTimeLocal|null $df_situacion = null): void
    {
        $this->df_situacion = $df_situacion;
    }

    /**
     *
     * @return string|null $sapel_fam
     */
    /**
     * @deprecated use getApelFamVo() instead
     */
    public function getApel_fam(): ?string
    {
        return $this->sapel_fam;
    }

    /**
     *
     * @param string|null $sapel_fam
     */
    /**
     * @deprecated use setApelFamVo() instead
     */
    public function setApel_fam(?string $sapel_fam = null): void
    {
        $this->sapel_fam = $sapel_fam;
    }

    public function getApelFamVo(): ?ApelFamText
    {
        return ApelFamText::fromNullableString($this->sapel_fam);
    }

    public function setApelFamVo(?ApelFamText $apelFam = null): void
    {
        $this->sapel_fam = $apelFam?->value();
    }

    /**
     *
     * @return string|null $sinc
     */
    /**
     * @deprecated use getIncVo() instead
     */
    public function getInc(): ?string
    {
        return $this->sinc;
    }

    /**
     *
     * @param string|null $sinc
     */
    /**
     * @deprecated use setIncVo() instead
     */
    public function setInc(?string $sinc = null): void
    {
        $this->sinc = $sinc;
    }

    public function getIncVo(): ?IncCode
    {
        return IncCode::fromNullableString($this->sinc);
    }

    public function setIncVo(?IncCode $inc = null): void
    {
        $this->sinc = $inc?->value();
    }

    /**
     *
     * @return DateTimeLocal|NullDateTimeLocal|null $df_inc
     */
    public function getF_inc(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->df_inc ?? new NullDateTimeLocal;
    }

    /**
     *
     * @param DateTimeLocal|null $df_inc
     */
    public function setF_inc(DateTimeLocal|null $df_inc = null): void
    {
        $this->df_inc = $df_inc;
    }

    /**
     * @deprecated use getNivelStgrVo() instead
     */
    public function getNivel_stgr(): ?int
    {
        return $this->inivel_stgr;
    }

    /**
     * @deprecated use setNivelStgrVo() instead
     */
    public function setNivel_stgr(?int $istgr = null): void
    {
        $this->inivel_stgr = $istgr;
    }

    public function getNivelStgrVo(): ?NivelStgrId
    {
        return NivelStgrId::fromNullableInt($this->inivel_stgr ?? null);
    }

    public function setNivelStgrVo(?NivelStgrId $nivel = null): void
    {
        $this->inivel_stgr = $nivel?->value();
    }

    /**
     *
     * @return string|null $sprofesion
     */
    /**
     * @deprecated use getProfesionVo() instead
     */
    public function getProfesion(): ?string
    {
        return $this->sprofesion;
    }

    /**
     *
     * @param string|null $sprofesion
     */
    /**
     * @deprecated use setProfesionVo() instead
     */
    public function setProfesion(?string $sprofesion = null): void
    {
        $this->sprofesion = $sprofesion;
    }

    public function getProfesionVo(): ?ProfesionText
    {
        return ProfesionText::fromNullableString($this->sprofesion);
    }

    public function setProfesionVo(?ProfesionText $profesion = null): void
    {
        $this->sprofesion = $profesion?->value();
    }

    /**
     *
     * @return string|null $seap
     */
    /**
     * @deprecated use getEapVo() instead
     */
    public function getEap(): ?string
    {
        return $this->seap;
    }

    /**
     *
     * @param string|null $seap
     */
    /**
     * @deprecated use setEapVo() instead
     */
    public function setEap(?string $seap = null): void
    {
        $this->seap = $seap;
    }

    public function getEapVo(): ?EapText
    {
        return EapText::fromNullableString($this->seap);
    }

    public function setEapVo(?EapText $eap = null): void
    {
        $this->seap = $eap?->value();
    }

    /**
     *
     * @return string|null $sobserv
     */
    /**
     * @deprecated use getObservVo() instead
     */
    public function getObserv(): ?string
    {
        return $this->sobserv;
    }

    /**
     *
     * @param string|null $sobserv
     */
    /**
     * @deprecated use setObservVo() instead
     */
    public function setObserv(?string $sobserv = null): void
    {
        $this->sobserv = $sobserv;
    }

    public function getObservVo(): ?ObservText
    {
        return ObservText::fromNullableString($this->sobserv);
    }

    public function setObservVo(?ObservText $observ = null): void
    {
        $this->sobserv = $observ?->value();
    }

    /**
     *
     * @return int|null $iid_ctr
     */
    public function getId_ctr(): ?int
    {
        return $this->iid_ctr;
    }

    /**
     *
     * @param int|null $iid_ctr
     */
    public function setId_ctr(?int $iid_ctr = null): void
    {
        $this->iid_ctr = $iid_ctr;
    }

    /**
     *
     * @return string|null $slugar_nacimiento
     */
    /**
     * @deprecated use getLugarNacimientoVo() instead
     */
    public function getLugar_nacimiento(): ?string
    {
        return $this->slugar_nacimiento;
    }

    /**
     *
     * @param string|null $slugar_nacimiento
     */
    /**
     * @deprecated use setLugarNacimientoVo() instead
     */
    public function setLugar_nacimiento(?string $slugar_nacimiento = null): void
    {
        $this->slugar_nacimiento = $slugar_nacimiento;
    }

    public function getLugarNacimientoVo(): ?LugarNacimientoText
    {
        return LugarNacimientoText::fromNullableString($this->slugar_nacimiento);
    }

    public function setLugarNacimientoVo(?LugarNacimientoText $lugar = null): void
    {
        $this->slugar_nacimiento = $lugar?->value();
    }


    /**
     *
     * @return bool|null $bes_publico
     */
    public function isEs_publico(): ?bool
    {
        return $this->bes_publico;
    }

    /**
     *
     * @param bool|null $es_publico
     */
    public function setEs_publico(?bool $es_publico = null): void
    {
        $this->bes_publico = $es_publico;
    }


    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    private string $sApellidos;
    private string $sApellidosNombre;
    private string $sApellidosNombreCr1_05;
    private string $sNombreApellidos;
    private string $sNombreApellidosCrSin;
    private string $sTituloNombre;
    private string $sCentro_o_dl;

    public function getClassName()
    {
        return (new ReflectionClass($this))->getShortName();
    }

    public function getPrefApellidosNombre()
    {
        $Pref_ordenApellidos = ConfigGlobal::mi_ordenApellidos() ?? '';

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
                case 'PersonaSacd':
                    $ctr = $this->getDl();
                    if ($ctr === ConfigGlobal::mi_dele()) {
                        $oPersonasDl = new PersonaDl($this->getId_nom());
                        $id_ctr = $oPersonasDl->getId_ctr();
                        $oCentroDl = null;
                        if ($id_ctr !== null) {
                            $CentroDlRepository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
                            $oCentroDl = $CentroDlRepository->findById($id_ctr);
                        }
                        $ctr = $oCentroDl?->getNombre_ubi() ?? '?';
                    }
                    break;
                case 'PersonaEx':
                case 'PersonaIn':
                    $ctr = $this->getDl();
                    break;
                case 'PersonaGlobal':
                    $oCentroDl = null;
                    if ($this->getId_ctr() !== null) {
                        // OJO CON las regiones de stgr
                        if (ConfigGlobal::mi_ambito() === 'rstgr') {
                            $CentroRepository = $GLOBALS['container']->get(CentroRepositoryInterface::class);
                            $oCentroDl = $CentroRepository->findById($this->getId_ctr());
                        } else {
                            $CentroDlRepository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
                            $oCentroDl = $CentroDlRepository->findById($this->getId_ctr());
                        }
                    }
                    $ctr = $oCentroDl?->getNombre_ubi() ?? '?';
                    break;
                case 'PersonaDl':
                case 'PersonaAgd':
                case 'PersonaN':
                case 'PersonaNax':
                case 'PersonaS':
                case 'PersonaSSSC':
                    $oCentro = null;
                    if ($this->getId_ctr() !== null) {
                        // OJO CON las regiones de stgr
                        if (ConfigGlobal::mi_ambito() === 'rstgr') {
                            $CentroRepository = $GLOBALS['container']->get(CentroRepositoryInterface::class);
                            $oCentro = $CentroRepository->findById($this->getId_ctr());
                        } else {
                            $CentroDlRepository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
                            $oCentro = $CentroDlRepository->findById($this->getId_ctr());
                        }
                    }
                    $ctr = $oCentro?->getNombre_ubi() ?? '?';
                    break;
            }
            $this->sCentro_o_dl = $ctr;
        }
        return $this->sCentro_o_dl;
    }


}