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
    PersonaTratoCode,
    ProfesionText,
    SituacionCode};
use src\shared\domain\traits\Hydratable;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\value_objects\DelegacionCode;
use web\DateTimeLocal;
use web\NullDateTimeLocal;
use function core\strtoupper_dlb;

class PersonaGlobal
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private int $id_schema;

    private int $id_nom;

    private string $id_tabla;

    private string|null $dl = null;

    private bool|null $sacd = null;

    private string|null $trato = null;

    private string|null $nom = null;

    private string|null $nx1 = null;

    private string $apellido1;

    private string|null $nx2 = null;

    private string|null $apellido2 = null;

    private DateTimeLocal|null $f_nacimiento = null;

    private string|null $idioma_preferido = null;

    private string $situacion;

    private DateTimeLocal|null $f_situacion = null;

    private string|null $apel_fam = null;

    private string|null $inc = null;

    private DateTimeLocal|null $f_inc = null;

    private int|null $nivel_stgr = null;

    private string|null $profesion = null;

    private string|null $eap = null;

    private string|null $observ = null;

    private int|null $id_ctr = null;

    private string|null $lugar_nacimiento = null;

    private ?bool $es_publico = false;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_schema(): int
    {
        return $this->id_schema;
    }

    public function setId_schema(int $id_schema): void
    {
        $this->id_schema = $id_schema;
    }


    public function getId_nom(): int
    {
        return $this->id_nom;
    }


    public function setId_nom(int $id_nom): void
    {
        $this->id_nom = $id_nom;
    }

    /**
     * @deprecated use getIdTablaVo()
     */
    public function getId_tabla(): string
    {
        return $this->id_tabla;
    }

    /**
     * @deprecated use setIdTablaVo()
     */
    public function setId_tabla(string $sid_tabla): void
    {
        $this->id_tabla = $sid_tabla;
    }

    // VO API -------------------------------------------------------------------
    public function getIdTablaVo(): PersonaTablaCode
    {
        return new PersonaTablaCode($this->id_tabla);
    }

    public function setIdTablaVo(PersonaTablaCode $idTabla): void
    {
        $this->id_tabla = $idTabla->value();
    }

    /**
     * @deprecated use getDlVo() instead
     */
    public function getDl(): ?string
    {
        return $this->dl;
    }

    /**
     * @deprecated use setDlVo() instead
     */
    public function setDl(?string $dl = null): void
    {
        $this->dl = $dl;
    }

    public function getDlVo(): ?DelegacionCode
    {
        return isset($this->dl) && $this->dl !== '' ? new DelegacionCode($this->dl) : null;
    }

    public function setDlVo(?DelegacionCode $dl = null): void
    {
        $this->dl = $dl?->value();
    }


    public function isSacd(): ?bool
    {
        return $this->sacd;
    }


    public function setSacd(?bool $sacd = null): void
    {
        $this->sacd = $sacd;
    }


    /**
     * @deprecated use getTratoVo() instead
     */
    public function getTrato(): ?string
    {
        return $this->trato;
    }


    /**
     * @deprecated use setTratoVo() instead
     */
    public function setTrato(?string $trato = null): void
    {
        $this->trato = $trato;
    }

    public function getTratoVo(): ?PersonaTratoCode
    {
        return PersonaTratoCode::fromNullableString($this->trato);
    }

    public function setTratoVo(?PersonaTratoCode $trato = null): void
    {
        $this->trato = $trato?->value();
    }


    /**
     * @deprecated use getNomVo() instead
     */
    public function getNom(): ?string
    {
        return $this->nom;
    }


    /**
     * @deprecated use setNomVo() instead
     */
    public function setNom(?string $nom = null): void
    {
        $this->nom = $nom;
    }

    public function getNomVo(): ?PersonaNombreText
    {
        return PersonaNombreText::fromNullableString($this->nom);
    }

    public function setNomVo(?PersonaNombreText $nom = null): void
    {
        $this->nom = $nom?->value();
    }


    /**
     * @deprecated use getNx1Vo() instead
     */
    public function getNx1(): ?string
    {
        return $this->nx1;
    }


    /**
     * @deprecated use setNx1Vo() instead
     */
    public function setNx1(?string $nx1 = null): void
    {
        $this->nx1 = $nx1;
    }

    public function getNx1Vo(): ?PersonaNx1Text
    {
        return PersonaNx1Text::fromNullableString($this->nx1);
    }

    public function setNx1Vo(?PersonaNx1Text $nx1 = null): void
    {
        $this->nx1 = $nx1?->value();
    }


    /**
     * @deprecated use getApellido1Vo() instead
     */
    public function getApellido1(): string
    {
        return $this->apellido1;
    }


    /**
     * @deprecated use setApellido1Vo() instead
     */
    public function setApellido1(string $apellido1): void
    {
        $this->apellido1 = $apellido1;
    }

    public function getApellido1Vo(): PersonaApellido1Text
    {
        return PersonaApellido1Text::fromString($this->apellido1);
    }

    public function setApellido1Vo(PersonaApellido1Text $apellido1): void
    {
        $this->apellido1 = $apellido1->value();
    }


    /**
     * @deprecated use getNx2Vo() instead
     */
    public function getNx2(): ?string
    {
        return $this->nx2;
    }


    /**
     * @deprecated use setNx2Vo() instead
     */
    public function setNx2(?string $nx2 = null): void
    {
        $this->nx2 = $nx2;
    }

    public function getNx2Vo(): ?PersonaNx2Text
    {
        return PersonaNx2Text::fromNullableString($this->nx2);
    }

    public function setNx2Vo(?PersonaNx2Text $nx2 = null): void
    {
        $this->nx2 = $nx2?->value();
    }


    /**
     * @deprecated use getApellido2Vo() instead
     */
    public function getApellido2(): ?string
    {
        return $this->apellido2;
    }


    /**
     * @deprecated use setApellido2Vo() instead
     */
    public function setApellido2(?string $apellido2 = null): void
    {
        $this->apellido2 = $apellido2;
    }

    public function getApellido2Vo(): ?PersonaApellido2Text
    {
        return PersonaApellido2Text::fromNullableString($this->apellido2);
    }

    public function setApellido2Vo(?PersonaApellido2Text $apellido2 = null): void
    {
        $this->apellido2 = $apellido2?->value();
    }


    public function getF_nacimiento(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->f_nacimiento ?? new NullDateTimeLocal;
    }


    public function setF_nacimiento(DateTimeLocal|null $f_nacimiento = null): void
    {
        $this->f_nacimiento = $f_nacimiento;
    }


    /**
     * @deprecated use getIdiomaPreferidoVo() instead
     */
    public function getIdioma_preferido(): ?string
    {
        return $this->idioma_preferido;
    }


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
     * @deprecated use getSituacionVo() instead
     */
    public function getSituacion(): string
    {
        return $this->situacion;
    }


    /**
     * @deprecated use setSituacionVo() instead
     */
    public function setSituacion(string $situacion): void
    {
        $this->situacion = $situacion;
    }

    public function getSituacionVo(): SituacionCode
    {
        return new SituacionCode($this->situacion);
    }

    public function setSituacionVo(SituacionCode $situacion): void
    {
        $this->situacion = $situacion->value();
    }


    public function getF_situacion(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->f_situacion ?? new NullDateTimeLocal;
    }


    public function setF_situacion(DateTimeLocal|null $f_situacion = null): void
    {
        $this->f_situacion = $f_situacion;
    }


    /**
     * @deprecated use getApelFamVo() instead
     */
    public function getApel_fam(): ?string
    {
        return $this->apel_fam;
    }


    /**
     * @deprecated use setApelFamVo() instead
     */
    public function setApel_fam(?string $apel_fam = null): void
    {
        $this->apel_fam = $apel_fam;
    }

    public function getApelFamVo(): ?ApelFamText
    {
        return ApelFamText::fromNullableString($this->apel_fam);
    }

    public function setApelFamVo(?ApelFamText $apelFam = null): void
    {
        $this->apel_fam = $apelFam?->value();
    }


    /**
     * @deprecated use getIncVo() instead
     */
    public function getInc(): ?string
    {
        return $this->inc;
    }


    /**
     * @deprecated use setIncVo() instead
     */
    public function setInc(?string $inc = null): void
    {
        $this->inc = $inc;
    }

    public function getIncVo(): ?IncCode
    {
        return IncCode::fromNullableString($this->inc);
    }

    public function setIncVo(?IncCode $inc = null): void
    {
        $this->inc = $inc?->value();
    }


    public function getF_inc(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->f_inc ?? new NullDateTimeLocal;
    }


    public function setF_inc(DateTimeLocal|null $f_inc = null): void
    {
        $this->f_inc = $f_inc;
    }

    /**
     * @deprecated use getNivelStgrVo() instead
     */
    public function getNivel_stgr(): ?int
    {
        return $this->nivel_stgr;
    }

    /**
     * @deprecated use setNivelStgrVo() instead
     */
    public function setNivel_stgr(?int $nivel_stgr = null): void
    {
        $this->nivel_stgr = $nivel_stgr;
    }

    public function getNivelStgrVo(): ?NivelStgrId
    {
        return NivelStgrId::fromNullableInt($this->nivel_stgr ?? null);
    }

    public function setNivelStgrVo(?NivelStgrId $nivel = null): void
    {
        $this->nivel_stgr = $nivel?->value();
    }


    /**
     * @deprecated use getProfesionVo() instead
     */
    public function getProfesion(): ?string
    {
        return $this->profesion;
    }


    /**
     * @deprecated use setProfesionVo() instead
     */
    public function setProfesion(?string $profesion = null): void
    {
        $this->profesion = $profesion;
    }

    public function getProfesionVo(): ?ProfesionText
    {
        return ProfesionText::fromNullableString($this->profesion);
    }

    public function setProfesionVo(?ProfesionText $profesion = null): void
    {
        $this->profesion = $profesion?->value();
    }


    /**
     * @deprecated use getEapVo() instead
     */
    public function getEap(): ?string
    {
        return $this->eap;
    }


    /**
     * @deprecated use setEapVo() instead
     */
    public function setEap(?string $eap = null): void
    {
        $this->eap = $eap;
    }

    public function getEapVo(): ?EapText
    {
        return EapText::fromNullableString($this->eap);
    }

    public function setEapVo(?EapText $eap = null): void
    {
        $this->eap = $eap?->value();
    }


    /**
     * @deprecated use getObservVo() instead
     */
    public function getObserv(): ?string
    {
        return $this->observ;
    }


    /**
     * @deprecated use setObservVo() instead
     */
    public function setObserv(?string $observ = null): void
    {
        $this->observ = $observ;
    }

    public function getObservVo(): ?ObservText
    {
        return ObservText::fromNullableString($this->observ);
    }

    public function setObservVo(?ObservText $observ = null): void
    {
        $this->observ = $observ?->value();
    }


    public function getId_ctr(): ?int
    {
        return $this->id_ctr;
    }


    public function setId_ctr(?int $id_ctr = null): void
    {
        $this->id_ctr = $id_ctr;
    }


    /**
     * @deprecated use getLugarNacimientoVo() instead
     */
    public function getLugar_nacimiento(): ?string
    {
        return $this->lugar_nacimiento;
    }


    /**
     * @deprecated use setLugarNacimientoVo() instead
     */
    public function setLugar_nacimiento(?string $lugar_nacimiento = null): void
    {
        $this->lugar_nacimiento = $lugar_nacimiento;
    }

    public function getLugarNacimientoVo(): ?LugarNacimientoText
    {
        return LugarNacimientoText::fromNullableString($this->lugar_nacimiento);
    }

    public function setLugarNacimientoVo(?LugarNacimientoText $lugar = null): void
    {
        $this->lugar_nacimiento = $lugar?->value();
    }

    public function isEs_publico(): ?bool
    {
        return $this->es_publico;
    }

    public function setEs_publico(?bool $es_publico = null): void
    {
        $this->es_publico = $es_publico;
    }


    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    private string $Apellidos;
    private string $ApellidosNombre;
    private string $ApellidosNombreCr1_05;
    private string $NombreApellidos;
    private string $NombreApellidosCrSin;
    private string $TituloNombre;
    private string $Centro_o_dl;

    public function getClassName()
    {
        return (new ReflectionClass($this))->getShortName();
    }

    public function getPrefApellidosNombre(): string
    {
        $Pref_ordenApellidos = ConfigGlobal::mi_ordenApellidos() ?? '';

        if ($Pref_ordenApellidos === 'nom_ap') {
            return $this->getNombreApellidos();
        }

        return $this->getApellidosNombre();
    }


    function getApellidos(): string
    {
        if (!isset($this->Apellidos)) {
            $ap_nom = !empty($this->nx1) ? $this->nx1 . ' ' : '';
            $ap_nom .= $this->apellido1;
            $ap_nom .= !empty($this->nx2) ? ' ' . $this->nx2 : '';
            $ap_nom .= !empty($this->apellido2) ? ' ' . $this->apellido2 : '';

            $this->Apellidos = $ap_nom;
        }
        return $this->Apellidos;
    }


    function getApellidosNombre(): string
    {
        if (!isset($this->ApellidosNombre)) {
            if (empty($this->apellido1)) {
                $ap_nom = '';
            } else {
                $ap_nom = $this->apellido1;
                $ap_nom .= !empty($this->nx2) ? ' ' . $this->nx2 : '';
                $ap_nom .= !empty($this->apellido2) ? ' ' . $this->apellido2 : '';
                $ap_nom .= ', ';
                $ap_nom .= !empty($this->trato) ? $this->trato . ' ' : ' ';
                $ap_nom .= !empty($this->apel_fam) ? $this->apel_fam : $this->nom;
                $ap_nom .= !empty($this->nx1) ? ' ' . $this->nx1 : '';
            }
            $this->ApellidosNombre = trim($ap_nom);
        }
        return $this->ApellidosNombre;
    }

    public function getApellidosUpperNombre(): string
    {
        $apellidos = $this->getApellidos();
        //Ni la función del postgresql ni la del php convierten los acentos.
        $apellidos = trim($apellidos);

        $apellidos = empty($apellidos) ? '????' : $apellidos;
        $ap_nom = strtoupper_dlb($apellidos);
        $ap_nom .= ', ';
        $ap_nom .= !empty($this->trato) ? $this->trato . ' ' : '';
        $ap_nom .= $this->nom;

        return $ap_nom;
    }


    function setApellidosNombre($sApellidosNombre): void
    {
        $this->ApellidosNombre = $sApellidosNombre;
    }


    function getApellidosNombreCr1_05(): string
    {
        if (!isset($this->ApellidosNombreCr1_05)) {
            $ap_nom = !empty($this->nx1) ? $this->nx1 . ' ' : '';
            $ap_nom .= $this->apellido1;
            $ap_nom .= !empty($this->nx2) ? ' ' . $this->nx2 : '';
            $ap_nom .= !empty($this->apellido2) ? ' ' . $this->apellido2 : '';
            $ap_nom .= ', ';
            $ap_nom .= $this->nom;

            $this->ApellidosNombreCr1_05 = $ap_nom;
        }
        return $this->ApellidosNombreCr1_05;
    }


    function setApellidosNombreCr1_05($sApellidosNombreCr1_05): void
    {
        $this->ApellidosNombreCr1_05 = $sApellidosNombreCr1_05;
    }


    public function getNombreApellidos(): string
    {
        if (!isset($this->NombreApellidos)) {
            $nom_ap = !empty($this->trato) ? $this->trato . ' ' : '';
            $nom_ap .= !empty($this->apel_fam) ? $this->apel_fam : $this->nom;
            $nom_ap .= !empty($this->nx1) ? ' ' . $this->nx1 : '';
            $nom_ap .= ' ' . $this->apellido1;
            $nom_ap .= !empty($this->nx2) ? ' ' . $this->nx2 : '';
            $nom_ap .= !empty($this->apellido2) ? ' ' . $this->apellido2 : '';

            $this->NombreApellidos = $nom_ap;
        }
        return $this->NombreApellidos;
    }


    function getNombreApellidosCrSin(): string
    {
        if (!isset($this->NombreApellidosCrSin)) {
            $nom_ap = $this->nom;
            $nom_ap .= !empty($this->nx1) ? ' ' . $this->nx1 : '';
            $nom_ap .= ' ' . $this->apellido1;
            $nom_ap .= !empty($this->nx2) ? ' ' . $this->nx2 : ' ';
            $nom_ap .= !empty($this->apellido2) ? ' ' . $this->apellido2 : '';

            $this->NombreApellidosCrSin = $nom_ap;
        }
        return $this->NombreApellidosCrSin;
    }

    function getTituloNombre(): string
    {
        if (!isset($this->TituloNombre)) {
            $nom_ap = 'Dnus. Dr. ';
            $nom_ap .= $this->nom;
            $nom_ap .= !empty($this->nx1) ? ' ' . $this->nx1 : '';
            $nom_ap .= ' ' . $this->apellido1;
            $nom_ap .= !empty($this->nx2) ? ' ' . $this->nx2 : ' ';
            $nom_ap .= !empty($this->apellido2) ? ' ' . $this->apellido2 : '';

            $this->TituloNombre = $nom_ap;
        }
        return $this->TituloNombre;
    }


    function getTituloNombreLatin(): string
    {
        if (!isset($this->sTituloNombreLatin)) {
            $oGesNomLatin = new GestorNombreLatin();
            $nom_ap = 'Dnus. Dr. ';
            $nom_ap .= $oGesNomLatin->getVernaculaLatin($this->nom);
            $nom_ap .= !empty($this->nx1) ? ' ' . $this->nx1 : '';
            $nom_ap .= ' ' . $this->apellido1;
            $nom_ap .= !empty($this->nx2) ? ' ' . $this->nx2 : ' ';
            $nom_ap .= !empty($this->apellido2) ? ' ' . $this->apellido2 : '';

            $this->sTituloNombreLatin = $nom_ap;
        }
        return $this->sTituloNombreLatin;
    }


    function getCentro_o_dl(): string
    {
        if (!isset($this->Centro_o_dl)) {
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
            $this->Centro_o_dl = $ctr;
        }
        return $this->Centro_o_dl;
    }


}