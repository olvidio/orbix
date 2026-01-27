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
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\NullDateTimeLocal;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\value_objects\DelegacionCode;
use function core\strtoupper_dlb;

class PersonaGlobal
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private int $id_schema;

    private int $id_nom;

    private PersonaTablaCode $id_tabla;

    private ?DelegacionCode $dl = null;

    private ?bool $sacd = null;

    private ?PersonaTratoCode $trato = null;

    private ?PersonaNombreText $nom = null;

    private ?PersonaNx1Text $nx1 = null;

    private PersonaApellido1Text $apellido1;

    private ?PersonaNx2Text $nx2 = null;

    private ?PersonaApellido2Text $apellido2 = null;

    private ?DateTimeLocal $f_nacimiento = null;

    private ?LenguaCode $idioma_preferido = null;

    private SituacionCode $situacion;

    private ?DateTimeLocal $f_situacion = null;

    private ?ApelFamText $apel_fam = null;

    private ?IncCode $inc = null;

    private ?DateTimeLocal $f_inc = null;

    private ?NivelStgrId $nivel_stgr = null;

    private ?ProfesionText $profesion = null;

    private ?EapText $eap = null;

    private ?ObservText $observ = null;

    private ?int $id_ctr = null;

    private ?LugarNacimientoText $lugar_nacimiento = null;

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
        return $this->id_tabla->value();
    }

    /**
     * @deprecated use setIdTablaVo()
     */
    public function setId_tabla(string $sid_tabla): void
    {
        $this->id_tabla = PersonaTablaCode::fromNullableString($sid_tabla);
    }

    public function getIdTablaVo(): PersonaTablaCode
    {
        return $this->id_tabla;
    }

    public function setIdTablaVo(PersonaTablaCode|string|null $idTabla): void
    {
        $this->id_tabla = $idTabla instanceof PersonaTablaCode
            ? $idTabla
            : PersonaTablaCode::fromNullableString($idTabla);
    }

    /**
     * @deprecated use getDlVo() instead
     */
    public function getDl(): ?string
    {
        return $this->dl?->value();
    }

    /**
     * @deprecated use setDlVo() instead
     */
    public function setDl(?string $dl = null): void
    {
        $this->dl = DelegacionCode::fromNullableString($dl);
    }

    public function getDlVo(): ?DelegacionCode
    {
        return $this->dl;
    }

    public function setDlVo(DelegacionCode|string|null $dl = null): void
    {
        $this->dl = $dl instanceof DelegacionCode
            ? $dl
            : DelegacionCode::fromNullableString($dl);
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
        return $this->trato?->value();
    }

    /**
     * @deprecated use setTratoVo() instead
     */
    public function setTrato(?string $trato = null): void
    {
        $this->trato = PersonaTratoCode::fromNullableString($trato);
    }

    public function getTratoVo(): ?PersonaTratoCode
    {
        return $this->trato;
    }

    public function setTratoVo(PersonaTratoCode|string|null $trato = null): void
    {
        $this->trato = $trato instanceof PersonaTratoCode
            ? $trato
            : PersonaTratoCode::fromNullableString($trato);
    }

    /**
     * @deprecated use getNomVo() instead
     */
    public function getNom(): ?string
    {
        return $this->nom?->value();
    }

    /**
     * @deprecated use setNomVo() instead
     */
    public function setNom(?string $nom = null): void
    {
        $this->nom = PersonaNombreText::fromNullableString($nom);
    }

    public function getNomVo(): ?PersonaNombreText
    {
        return $this->nom;
    }

    public function setNomVo(PersonaNombreText|string|null $nom = null): void
    {
        $this->nom = $nom instanceof PersonaNombreText
            ? $nom
            : PersonaNombreText::fromNullableString($nom);
    }

    /**
     * @deprecated use getNx1Vo() instead
     */
    public function getNx1(): ?string
    {
        return $this->nx1?->value();
    }

    /**
     * @deprecated use setNx1Vo() instead
     */
    public function setNx1(?string $nx1 = null): void
    {
        $this->nx1 = PersonaNx1Text::fromNullableString($nx1);
    }

    public function getNx1Vo(): ?PersonaNx1Text
    {
        return $this->nx1;
    }

    public function setNx1Vo(PersonaNx1Text|string|null $nx1 = null): void
    {
        $this->nx1 = $nx1 instanceof PersonaNx1Text
            ? $nx1
            : PersonaNx1Text::fromNullableString($nx1);
    }

    /**
     * @deprecated use getApellido1Vo() instead
     */
    public function getApellido1(): string
    {
        return $this->apellido1->value();
    }

    /**
     * @deprecated use setApellido1Vo() instead
     */
    public function setApellido1(string $apellido1): void
    {
        $this->apellido1 = PersonaApellido1Text::fromNullableString($apellido1);
    }

    public function getApellido1Vo(): PersonaApellido1Text
    {
        return $this->apellido1;
    }

    public function setApellido1Vo(PersonaApellido1Text|string|null $apellido1): void
    {
        $this->apellido1 = $apellido1 instanceof PersonaApellido1Text
            ? $apellido1
            : PersonaApellido1Text::fromNullableString($apellido1);
    }

    /**
     * @deprecated use getNx2Vo() instead
     */
    public function getNx2(): ?string
    {
        return $this->nx2?->value();
    }

    /**
     * @deprecated use setNx2Vo() instead
     */
    public function setNx2(?string $nx2 = null): void
    {
        $this->nx2 = PersonaNx2Text::fromNullableString($nx2);
    }

    public function getNx2Vo(): ?PersonaNx2Text
    {
        return $this->nx2;
    }

    public function setNx2Vo(PersonaNx2Text|string|null $nx2 = null): void
    {
        $this->nx2 = $nx2 instanceof PersonaNx2Text
            ? $nx2
            : PersonaNx2Text::fromNullableString($nx2);
    }

    /**
     * @deprecated use getApellido2Vo() instead
     */
    public function getApellido2(): ?string
    {
        return $this->apellido2?->value();
    }

    /**
     * @deprecated use setApellido2Vo() instead
     */
    public function setApellido2(?string $apellido2 = null): void
    {
        $this->apellido2 = PersonaApellido2Text::fromNullableString($apellido2);
    }

    public function getApellido2Vo(): ?PersonaApellido2Text
    {
        return $this->apellido2;
    }

    public function setApellido2Vo(PersonaApellido2Text|string|null $apellido2 = null): void
    {
        $this->apellido2 = $apellido2 instanceof PersonaApellido2Text
            ? $apellido2
            : PersonaApellido2Text::fromNullableString($apellido2);
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
        return $this->idioma_preferido?->value();
    }

    /**
     * @deprecated use setIdiomaPreferidoVo() instead
     */
    public function setIdioma_preferido(?string $idioma_preferido = null): void
    {
        $this->idioma_preferido = LenguaCode::fromNullableString($idioma_preferido);
    }

    public function getIdiomaPreferidoVo(): ?LenguaCode
    {
        return $this->idioma_preferido;
    }

    public function setIdiomaPreferidoVo(LenguaCode|string|null $lengua = null): void
    {
        $this->idioma_preferido = $lengua instanceof LenguaCode
            ? $lengua
            : LenguaCode::fromNullableString($lengua);
    }

    /**
     * @deprecated use getSituacionVo() instead
     */
    public function getSituacion(): string
    {
        return $this->situacion->value();
    }

    /**
     * @deprecated use setSituacionVo() instead
     */
    public function setSituacion(?string $situacion): void
    {
        $this->situacion = SituacionCode::fromNullableString($situacion);
    }

    public function getSituacionVo(): SituacionCode
    {
        return $this->situacion;
    }

    public function setSituacionVo(SituacionCode|string|null $situacion): void
    {
        $this->situacion = $situacion instanceof SituacionCode
            ? $situacion
            : SituacionCode::fromNullableString($situacion);
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
        return $this->apel_fam?->value();
    }

    /**
     * @deprecated use setApelFamVo() instead
     */
    public function setApel_fam(?string $apel_fam = null): void
    {
        $this->apel_fam = ApelFamText::fromNullableString($apel_fam);
    }

    public function getApelFamVo(): ?ApelFamText
    {
        return $this->apel_fam;
    }

    public function setApelFamVo(ApelFamText|string|null $apelFam = null): void
    {
        $this->apel_fam = $apelFam instanceof ApelFamText
            ? $apelFam
            : ApelFamText::fromNullableString($apelFam);
    }


    /**
     * @deprecated use getIncVo() instead
     */
    public function getInc(): ?string
    {
        return $this->inc?->value();
    }

    /**
     * @deprecated use setIncVo() instead
     */
    public function setInc(?string $inc = null): void
    {
        $this->inc = IncCode::fromNullableString($inc);
    }

    public function getIncVo(): ?IncCode
    {
        return $this->inc;
    }

    public function setIncVo(IncCode|string|null $inc = null): void
    {
        $this->inc = $inc instanceof IncCode
            ? $inc
            : IncCode::fromNullableString($inc);
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
    public function getNivel_stgr(): ?string
    {
        return $this->nivel_stgr?->value();
    }

    /**
     * @deprecated use setNivelStgrVo() instead
     */
    public function setNivel_stgr(?int $nivel_stgr = null): void
    {
        $this->nivel_stgr = NivelStgrId::fromNullableInt($nivel_stgr);
    }

    public function getNivelStgrVo(): ?NivelStgrId
    {
        return $this->nivel_stgr;
    }

    public function setNivelStgrVo(NivelStgrId|int|null $nivel = null): void
    {
        $this->nivel_stgr = $nivel instanceof NivelStgrId
            ? $nivel
            : NivelStgrId::fromNullableInt($nivel);
    }

    /**
     * @deprecated use getProfesionVo() instead
     */
    public function getProfesion(): ?string
    {
        return $this->profesion?->value();
    }

    /**
     * @deprecated use setProfesionVo() instead
     */
    public function setProfesion(?string $profesion = null): void
    {
        $this->profesion = ProfesionText::fromNullableString($profesion);
    }

    public function getProfesionVo(): ?ProfesionText
    {
        return $this->profesion;
    }

    public function setProfesionVo(ProfesionText|string|null $profesion = null): void
    {
        $this->profesion = $profesion instanceof ProfesionText
            ? $profesion
            : ProfesionText::fromNullableString($profesion);
    }

    /**
     * @deprecated use getEapVo() instead
     */
    public function getEap(): ?string
    {
        return $this->eap?->value();
    }

    /**
     * @deprecated use setEapVo() instead
     */
    public function setEap(?string $eap = null): void
    {
        $this->eap = EapText::fromNullableString($eap);
    }

    public function getEapVo(): ?EapText
    {
        return $this->eap;
    }

    public function setEapVo(EapText|string|null $eap = null): void
    {
        $this->eap = $eap instanceof EapText
            ? $eap
            : EapText::fromNullableString($eap);
    }

    /**
     * @deprecated use getObservVo() instead
     */
    public function getObserv(): ?string
    {
        return $this->observ?->value();
    }

    /**
     * @deprecated use setObservVo() instead
     */
    public function setObserv(?string $observ = null): void
    {
        $this->observ = ObservText::fromNullableString($observ);
    }

    public function getObservVo(): ?ObservText
    {
        return $this->observ;
    }

    public function setObservVo(ObservText|string|null $observ = null): void
    {
        $this->observ = $observ instanceof ObservText
            ? $observ
            : ObservText::fromNullableString($observ);
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
        return $this->lugar_nacimiento?->value();
    }

    /**
     * @deprecated use setLugarNacimientoVo() instead
     */
    public function setLugar_nacimiento(?string $lugar_nacimiento = null): void
    {
        $this->lugar_nacimiento = LugarNacimientoText::fromNullableString($lugar_nacimiento);
    }

    public function getLugarNacimientoVo(): ?LugarNacimientoText
    {
        return $this->lugar_nacimiento;
    }

    public function setLugarNacimientoVo(LugarNacimientoText|string|null $lugar = null): void
    {
        $this->lugar_nacimiento = $lugar instanceof LugarNacimientoText
            ? $lugar
            : LugarNacimientoText::fromNullableString($lugar);
    }

    public function isEs_publico(): ?bool
    {
        return $this->es_publico;
    }

    public function setEs_publico(?bool $es_publico = null): void
    {
        $this->es_publico = $es_publico;
    }


    /* MÉTODOS GET y SET DE ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    public function getClassName(): string
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


    public function getApellidos(): string
    {
        $ap_nom = !empty($this->nx1) ? $this->nx1 . ' ' : '';
        $ap_nom .= $this->apellido1;
        $ap_nom .= !empty($this->nx2) ? ' ' . $this->nx2 : '';
        $ap_nom .= !empty($this->apellido2) ? ' ' . $this->apellido2 : '';

        return $ap_nom;
    }


    public function getApellidosNombre(): string
    {
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
        return trim($ap_nom);
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


    public function getApellidosNombreCr1_05(): string
    {
        $ap_nom = !empty($this->nx1) ? $this->nx1 . ' ' : '';
        $ap_nom .= $this->apellido1;
        $ap_nom .= !empty($this->nx2) ? ' ' . $this->nx2 : '';
        $ap_nom .= !empty($this->apellido2) ? ' ' . $this->apellido2 : '';
        $ap_nom .= ', ';
        $ap_nom .= $this->nom;

        return $ap_nom;
    }

    public function getNombreApellidos(): string
    {
        $nom_ap = !empty($this->trato) ? $this->trato . ' ' : '';
        $nom_ap .= !empty($this->apel_fam) ? $this->apel_fam : $this->nom;
        $nom_ap .= !empty($this->nx1) ? ' ' . $this->nx1 : '';
        $nom_ap .= ' ' . $this->apellido1;
        $nom_ap .= !empty($this->nx2) ? ' ' . $this->nx2 : '';
        $nom_ap .= !empty($this->apellido2) ? ' ' . $this->apellido2 : '';

        return $nom_ap;
    }


    public function getNombreApellidosCrSin(): string
    {
        $nom_ap = $this->nom;
        $nom_ap .= !empty($this->nx1) ? ' ' . $this->nx1 : '';
        $nom_ap .= ' ' . $this->apellido1;
        $nom_ap .= !empty($this->nx2) ? ' ' . $this->nx2 : ' ';
        $nom_ap .= !empty($this->apellido2) ? ' ' . $this->apellido2 : '';

        return $nom_ap;
    }

    public function getTituloNombre(): string
    {
        $nom_ap = 'Dnus. Dr. ';
        $nom_ap .= $this->nom;
        $nom_ap .= !empty($this->nx1) ? ' ' . $this->nx1 : '';
        $nom_ap .= ' ' . $this->apellido1;
        $nom_ap .= !empty($this->nx2) ? ' ' . $this->nx2 : ' ';
        $nom_ap .= !empty($this->apellido2) ? ' ' . $this->apellido2 : '';

        return $nom_ap;
    }


    public function getCentro_o_dl(): string
    {
        $classname = get_class($this);
        $matches = [];
        if (preg_match('@\\\\(\w+)$@', $classname, $matches)) {
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
        return $ctr;
    }


}