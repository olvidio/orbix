<?php

namespace src\asistentes\domain\entity;

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\shared\config\ConfigGlobal;
use src\actividadplazas\domain\value_objects\PlazaId;
use src\asistentes\domain\contracts\PlazaPropietarioAsignacionInterface;
use src\asistentes\domain\value_objects\AsistenteEncargo;
use src\asistentes\domain\value_objects\AsistenteObserv;
use src\asistentes\domain\value_objects\AsistenteObservEst;
use src\asistentes\domain\value_objects\AsistentePk;
use src\asistentes\domain\value_objects\AsistentePropietario;
use src\personas\domain\entity\Persona;
use src\personas\domain\value_objects\PersonaTablaCode;
use src\shared\domain\contracts\AggregateRoot;
use src\shared\domain\DatosCampo;
use src\shared\domain\entity\Entity;
use src\shared\domain\traits\Hydratable;
use src\shared\domain\value_objects\Uuid;
use src\ubis\domain\value_objects\DelegacionCode;
use src\ubiscamas\domain\entity\Cama;
use src\ubiscamas\domain\value_objects\CamaId;


class Asistente extends Entity implements AggregateRoot
{
    use Hydratable;

    /**
     * Saber si puedo modificar.
     * - true para asistentes de mi dl, y para los de paso que he puesto yo
     * - false para asistentes de otra dl, y para los de paso que NO he puesto yo
     *
     * @return boolean
     */
    public function perm_modificar(): bool
    {
        return $this->getDl_responsable() === ConfigGlobal::mi_delef();
    }

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_activ;

    private int $id_nom;

    private bool $propio = false;

    private bool $est_ok = false;

    private bool $cfi = false;

    private ?int $cfi_con = null;

    private bool $falta = false;

    private ?AsistenteEncargo $encargo = null;

    private ?DelegacionCode $dl_responsable = null;

    private ?AsistenteObserv $observ = null;

    private ?PersonaTablaCode $id_tabla = null;

    private ?PlazaId $plaza = null;

    private ?AsistentePropietario $propietario = null;

    private ?AsistenteObservEst $observ_est = null;

    private ?CamaId $cama = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getAsistentePk(): AsistentePk
    {
        return AsistentePk::fromArray([
            'id_activ' => $this->id_activ,
            'id_nom' => $this->id_nom,
        ]);
    }


    public function getId_activ(): int
    {
        return $this->id_activ;
    }

    public function setId_activ(int $id_activ): void
    {
        $this->id_activ = $id_activ;
    }

    public function getId_nom(): int
    {
        return $this->id_nom;
    }

    public function setId_nom(int $id_nom): void
    {
        $this->id_nom = $id_nom;
    }


    public function isPropio(): bool
    {
        return $this->propio;
    }


    public function setPropio(bool $propio): void
    {
        $this->propio = $propio;
    }


    public function isEst_ok(): bool
    {
        return $this->est_ok;
    }


    public function setEst_ok(bool $est_ok): void
    {
        $this->est_ok = $est_ok;
    }


    public function isCfi(): bool
    {
        return $this->cfi;
    }


    public function setCfi(bool $cfi): void
    {
        $this->cfi = $cfi;
    }


    public function getCfi_con(): ?int
    {
        return $this->cfi_con;
    }


    public function setCfi_con(?int $cfi_con = null): void
    {
        $this->cfi_con = $cfi_con;
    }


    public function isFalta(): bool
    {
        return $this->falta;
    }


    public function setFalta(bool $falta): void
    {
        $this->falta = $falta;
    }

    /**
     * @deprecated usar getEncargoVo()
     */
    public function getEncargo(): ?string
    {
        return $this->encargo?->value();
    }

    /**
     * @deprecated usar setEncargoVo()
     */
    public function setEncargo(?string $encargo = null): void
    {
        $this->encargo = AsistenteEncargo::fromNullableString($encargo);
    }

    /**
     * @return AsistenteEncargo|null
     */
    public function getEncargoVo(): ?AsistenteEncargo
    {
        return $this->encargo;
    }


    public function setEncargoVo(AsistenteEncargo|string|null $texto = null): void
    {
        $this->encargo = $texto instanceof AsistenteEncargo
            ? $texto
            : AsistenteEncargo::fromNullableString($texto);
    }

    /**
     * @deprecated usar getDlResponsableVo()
     */
    public function getDl_responsable(): ?string
    {
        return $this->dl_responsable?->value();
    }

    /**
     * @deprecated usar setDlResponsableVo()
     */
    public function setDl_responsable(?string $dl_responsable = null): void
    {
        $this->dl_responsable = DelegacionCode::fromNullableString($dl_responsable);
    }

    /**
     * @return DelegacionCode|null
     */
    public function getDlResponsableVo(): ?DelegacionCode
    {
        return $this->dl_responsable;
    }


    public function setDlResponsableVo(DelegacionCode|string|null $texto = null): void
    {
        $this->dl_responsable = $texto instanceof DelegacionCode
            ? $texto
            : DelegacionCode::fromNullableString($texto);
    }

    /**
     * @deprecated usar getObservVo()
     */
    public function getObserv(): ?string
    {
        return $this->observ?->value();
    }

    /**
     * @deprecated usar setObservVo()
     */
    public function setObserv(?string $observ = null): void
    {
        $this->observ = AsistenteObserv::fromNullableString($observ);
    }

    /**
     * @return AsistenteObserv|null
     */
    public function getObservVo(): ?AsistenteObserv
    {
        return $this->observ;
    }


    public function setObservVo(AsistenteObserv|string|null $texto = null): void
    {
        $this->observ = $texto instanceof AsistenteObserv
            ? $texto
            : AsistenteObserv::fromNullableString($texto);
    }

    /**
     * @deprecated usar getIdTablaVo()
     */
    public function getId_tabla(): ?string
    {
        return $this->id_tabla?->value();
    }

    /**
     * @deprecated usar setIdTablaVo()
     */
    public function setId_tabla(?string $id_tabla = null): void
    {
        $this->id_tabla = PersonaTablaCode::fromNullableString($id_tabla);
    }

    /**
     * @return PersonaTablaCode|null
     */
    public function getIdTablaVo(): ?PersonaTablaCode
    {
        return $this->id_tabla;
    }

    public function getNomTabla(): string
    {
        return match ($this->getIdTablaVo()?->value()) {
            'dl' => 'd_asistentes_dl',
            'ex' => 'd_asistentes_ex',
            'out' => 'd_asistentes_out',
            default => 'd_asistentes_' . ($this->getIdTablaVo()?->value() ?? 'all'),
        };
    }


    public function setIdTablaVo(PersonaTablaCode|string|null $texto = null): void
    {
        $this->id_tabla = $texto instanceof PersonaTablaCode
            ? $texto
            : PersonaTablaCode::fromNullableString($texto);
    }

    /**
     * @deprecated usar getPlazaVo()
     */
    public function getPlaza(): ?string
    {
        $value = $this->plaza?->value();

        return $value !== null ? (string) $value : null;
    }

    /**
     * @deprecated usar setPlazaVo()
     */
    public function setPlaza(?int $plaza = null): void
    {
        $this->plaza = PlazaId::fromNullableInt($plaza);
    }

    /**
     * @return PlazaId|null
     */
    public function getPlazaVo(): ?PlazaId
    {
        return $this->plaza;
    }


    public function setPlazaVo(PlazaId|int|null $valor = null): void
    {
        $this->plaza = $valor instanceof PlazaId
            ? $valor
            : PlazaId::fromNullableInt($valor);
    }

    /**
     * No puede estar en setPlaza, porque cuando se hidrata con la DB entra en un bucle infinito
     *
     * @deprecated usar setPlazaVoComprobando()
     * @return string vacio si ok, mensaje de error si la plaza exige propietario y no hay libre
     */
    public function setPlazaComprobando(
        ?int $plaza = null,
        ?PlazaPropietarioAsignacionInterface $plazaPropietario = null,
    ): string {
        $plaza_actual = $this->getPlazaVo()?->value() ?? PlazaId::PEDIDA;
        $plaza = (int) $plaza;
        $this->plaza = PlazaId::fromNullableInt($plaza);

        return $plazaPropietario?->asegurar($this, $plaza_actual, $plaza) ?? '';
    }

    /**
     * No puede estar en setPlaza, porque cuando se hidrata con la DB entra en un bucle infinito
     *
     * @return string vacio si ok, mensaje de error si la plaza exige propietario y no hay libre
     */
    public function setPlazaVoComprobando(
        PlazaId|int|null $oPlazaId = null,
        ?PlazaPropietarioAsignacionInterface $plazaPropietario = null,
    ): string {
        $plaza_actual = $this->getPlazaVo()?->value() ?? PlazaId::PEDIDA;
        $iplaza = $oPlazaId instanceof PlazaId
            ? $oPlazaId->value()
            : $oPlazaId;
        $iplaza = (int) $iplaza;
        $this->plaza = PlazaId::fromNullableInt($iplaza);

        return $plazaPropietario?->asegurar($this, $plaza_actual, $iplaza) ?? '';
    }

    /**
     * @deprecated usar getPropietarioVo()
     */
    public function getPropietario(): ?string
    {
        return $this->propietario?->value();
    }

    /**
     * @deprecated usar setPropietarioVo()
     */
    public function setPropietario(?string $propietario = null): void
    {
        $this->propietario = AsistentePropietario::fromNullableString($propietario);
    }

    /**
     * @return AsistentePropietario|null
     */
    public function getPropietarioVo(): ?AsistentePropietario
    {
        return $this->propietario;
    }

    /**
     * @param AsistentePropietario|string|null $texto
     */
    public function setPropietarioVo(AsistentePropietario|string|null $texto = null): void
    {
        $this->propietario = $texto instanceof AsistentePropietario
            ? $texto
            : AsistentePropietario::fromNullableString($texto);
    }

    /**
     * @deprecated usar getObservEstVo()
     */
    public function getObserv_est(): ?string
    {
        return $this->observ_est?->value();
    }

    /**
     * @deprecated usar setObservEstVo()
     */
    public function setObserv_est(?string $observ_est = null): void
    {
        $this->observ_est = AsistenteObservEst::fromNullableString($observ_est);
    }

    /**
     * @return AsistenteObservEst|null
     */
    public function getObservEstVo(): ?AsistenteObservEst
    {
        return $this->observ_est;
    }

    /**
     * @param AsistenteObservEst|string|null $texto
     */
    public function setObservEstVo(AsistenteObservEst|string|null $texto = null): void
    {
        $this->observ_est = $texto instanceof AsistenteObservEst
            ? $texto
            : AsistenteObservEst::fromNullableString($texto);
    }

    /*
     * deprecated usar getCamaVo()
     */
    public function getCama(): ?string
    {
        return $this->cama?->value();
    }

    /*
     * deprecated usar setCamaVo()
     */
    public function setCama(?string $cama = null): void
    {
        $this->cama = CamaId::fromNullableString($cama);
    }

    public function getCamaVo(): ?CamaId
    {
        return $this->cama;
    }

    public function setCamaVo(?CamaId $cama = null): void
    {
        $this->cama = $cama instanceof CamaId
            ? $cama
            : CamaId::fromNullableString($cama);
    }

    /**
     * {@see DatosTablaRepo} / dossier datos_tabla: PK compuesta JSON en campo `sel`.
     */
    public function getPrimary_key(): string
    {
        return 'pkey';
    }

    /**
     * @return array{id_activ:int, id_nom:int}
     */
    public function getPkey(): array
    {
        return [
            'id_activ' => $this->id_activ,
            'id_nom' => $this->id_nom,
        ];
    }

    /**
     * @return list<DatosCampo>
     */
    public function getDatosCampos(): array
    {
        return [
            $this->datosCampoIdNomHidden(),
            $this->datosCampoNomActividad(),
            $this->datosCampoPropio(),
            $this->datosCampoEstOk(),
            $this->datosCampoFalta(),
            $this->datosCampoObserv(),
        ];
    }

    private function datosCampoIdNomHidden(): DatosCampo
    {
        $c = new DatosCampo();
        $c->setNom_camp('id_nom');
        $c->setMetodoGet('getId_nom');
        $c->setMetodoSet('setId_nom');
        $c->setEtiqueta('id_nom');
        $c->setTipo('hidden');

        return $c;
    }

    private function datosCampoNomActividad(): DatosCampo
    {
        $c = new DatosCampo();
        $c->setNom_camp('id_activ');
        $c->setMetodoGet('getId_activ');
        $c->setMetodoSet('setId_activ');
        $c->setEtiqueta(_('nombre actividad'));
        $c->setTipo('opciones');
        $c->setArgument(ActividadAllRepositoryInterface::class);
        $c->setArgument2('getNom_activ');

        return $c;
    }

    private function datosCampoPropio(): DatosCampo
    {
        $c = new DatosCampo();
        $c->setNom_camp('propio');
        $c->setMetodoGet('isPropio');
        $c->setMetodoSet('setPropio');
        $c->setEtiqueta(_('propio'));
        $c->setTipo('check');

        return $c;
    }

    private function datosCampoEstOk(): DatosCampo
    {
        $c = new DatosCampo();
        $c->setNom_camp('est_ok');
        $c->setMetodoGet('isEst_ok');
        $c->setMetodoSet('setEst_ok');
        $c->setEtiqueta(_('est. ok'));
        $c->setTipo('check');

        return $c;
    }

    private function datosCampoFalta(): DatosCampo
    {
        $c = new DatosCampo();
        $c->setNom_camp('falta');
        $c->setMetodoGet('isFalta');
        $c->setMetodoSet('setFalta');
        $c->setEtiqueta(_('falta'));
        $c->setTipo('check');

        return $c;
    }

    private function datosCampoObserv(): DatosCampo
    {
        $c = new DatosCampo();
        $c->setNom_camp('observ');
        $c->setMetodoGet('getObserv');
        $c->setMetodoSet('setObserv');
        $c->setEtiqueta(_('observ.'));
        $c->setTipo('texto');
        $c->setArgument('40');

        return $c;
    }
}