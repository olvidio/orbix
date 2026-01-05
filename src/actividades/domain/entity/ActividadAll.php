<?php

namespace src\actividades\domain\entity;

use ReflectionClass;
use src\actividades\domain\value_objects\ActividadDescText;
use src\actividades\domain\value_objects\ActividadNomText;
use src\actividades\domain\value_objects\ActividadTipoId;
use src\actividades\domain\value_objects\IdTablaCode;
use src\actividades\domain\value_objects\NivelStgrId;
use src\actividades\domain\value_objects\RepeticionId;
use src\actividades\domain\value_objects\StatusId;
use src\actividadtarifas\domain\value_objects\TarifaId;
use src\shared\domain\traits\Hydratable;
use src\shared\domain\value_objects\Dinero;
use src\ubis\domain\value_objects\DelegacionCode;
use src\usuarios\domain\value_objects\IdLocale;
use web\DateTimeLocal;
use web\NullDateTimeLocal;
use web\NullTimeLocal;
use web\TimeLocal;
use function core\is_true;

class ActividadAll
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private int $id_auto; // para las clases Dl y Ex, al hacer insert
    private int $id_activ;
    private ActividadTipoId $id_tipo_activ;
    private ?DelegacionCode $dl_org = null;
    private ActividadNomText $nom_activ;
    private ?int $id_ubi = null;
    private ?ActividadDescText $desc_activ = null;
    private DateTimeLocal $f_ini;
    private ?TimeLocal $h_ini = null;
    private DateTimeLocal $f_fin;
    private ?TimeLocal $h_fin = null;
    private ?int $tipo_horario = null;
    private ?Dinero $precio = null;
    private int|null $num_asistentes = null;
    private StatusId $status;
    private string|null $observ = null;
    private ?NivelStgrId $nivel_stgr = null;
    private string|null $observ_material = null;
    private string|null $lugar_esp = null;
    private ?TarifaId $tarifa = null;
    private ?RepeticionId $id_repeticion = null;
    private ?bool $publicado = false;
    private ?IdTablaCode $id_tabla = null;
    private ?int $plazas = null;
    private ?IdLocale $idioma = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getClassName(): string
    {
        return (new ReflectionClass($this))->getShortName();
    }

    public function getId_auto(): int
    {
        return $this->id_auto;
    }


    public function setId_auto(int $id_auto): void
    {
        $this->id_auto = $id_auto;
    }


    public function getId_activ(): int
    {
        return $this->id_activ;
    }

    public function setId_activ(int $id_activ): void
    {
        $this->id_activ = $id_activ;
    }

    /**
     * @deprecated Usar `getTipoActividadVo(): ActividadTipoId` en su lugar.
     */
    public function getId_tipo_activ(): int
    {
        return $this->id_tipo_activ->value();
    }

    /**
     * @deprecated Usar `setTipoActividadVo(ActividadTipoId $vo): void` en su lugar.
     */
    public function setId_tipo_activ(int $id_tipo_activ): void
    {
        $this->id_tipo_activ = new ActividadTipoId($id_tipo_activ);
    }

    public function getTipoActividadVo(): ActividadTipoId
    {
        return $this->id_tipo_activ;
    }

    public function setTipoActividadVo(ActividadTipoId $vo): void
    {
        $this->id_tipo_activ = $vo;
    }

    /**
     * @deprecated Usar `getDlOrgVo(): ?DelegacionCode` en su lugar.
     */
    public function getDl_org(): ?string
    {
        return $this->dl_org?->value();
    }

    /**
     * @deprecated Usar `setDlOrgVo(?DelegacionCode $codigo = null): void` en su lugar.
     */
    public function setDl_org(?string $sdl_org = null): void
    {
        $this->dl_org = new DelegacionCode($sdl_org);
    }

    public function getDlOrgVo(): ?DelegacionCode
    {
        return $this->dl_org;
    }

    public function setDlOrgVo(?DelegacionCode $codigo = null): void
    {
        $this->dl_org = $codigo?->value();
    }

    /**
     * @deprecated Usar `getNomActivVo(): ActividadNomText` en su lugar.
     */
    public function getNom_activ(): string
    {
        return $this->nom_activ;
    }

    /**
     * @deprecated Usar `setNomActivVo(ActividadNomText $vo): void` en su lugar.
     */
    public function setNom_activ(string $snom_activ): void
    {
        $this->nom_activ = new ActividadNomText($snom_activ);
    }

    public function getNomActivVo(): ActividadNomText
    {
        return $this->nom_activ;
    }

    public function setNomActivVo(ActividadNomText $vo): void
    {
        $this->nom_activ = $vo;
    }

    public function getId_ubi(): ?int
    {
        return $this->id_ubi;
    }

    public function setId_ubi(?int $id_ubi = null): void
    {
        $this->id_ubi = $id_ubi;
    }

    /**
     * @deprecated Usar `getDescActivVo(): ?ActividadDescText` en su lugar.
     */
    public function getDesc_activ(): ?string
    {
        return $this->desc_activ?->value();
    }

    /**
     * @deprecated Usar `setDescActivVo(?ActividadDescText $vo = null): void` en su lugar.
     */
    public function setDesc_activ(?string $sdesc_activ = null): void
    {
        $this->desc_activ = ActividadDescText::fromNullableString($sdesc_activ);
    }

    public function getDescActivVo(): ?ActividadDescText
    {
        return $this->desc_activ;
    }

    public function setDescActivVo(?ActividadDescText $vo = null): void
    {
        $this->desc_activ = $vo?->value();
    }

    public function getF_ini(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->f_ini ?? new NullDateTimeLocal;
    }

    public function setF_ini(DateTimeLocal|null $df_ini = null): void
    {
        $this->f_ini = $df_ini;
    }

    public function getH_ini(): TimeLocal|NullTimeLocal|null
    {
        return $this->h_ini;
    }

    public function setH_ini(TimeLocal|null $th_ini = null): void
    {
        $this->h_ini = $th_ini;
    }

    public function getF_fin(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->f_fin;
    }

    public function setF_fin(DateTimeLocal|null $df_fin = null): void
    {
        $this->f_fin = $df_fin;
    }

    public function getH_fin(): TimeLocal|NullTimeLocal|null
    {
        return $this->h_fin;
    }

    public function setH_fin(TimeLocal|null $th_fin = null): void
    {
        $this->h_fin = $th_fin;
    }

    public function getTipo_horario(): ?int
    {
        return $this->tipo_horario;
    }

    public function setTipo_horario(?int $itipo_horario = null): void
    {
        $this->tipo_horario = $itipo_horario;
    }

    /**
     * @deprecated Usar `getPrecioVo(): ?Dinero` en su lugar.
     */
    public function getPrecio(): ?float
    {
        return $this->precio?->asFloat();
    }

    /**
     * @deprecated Usar `setPrecioVo(?Dinero $vo = null): void` en su lugar.
     */
    public function setPrecio(?float $iprecio = null): void
    {
        $this->precio = Dinero::fromNullable($iprecio);
    }

    public function getPrecioVo(): ?Dinero
    {
        return $this->precio;
    }

    public function setPrecioVo(?Dinero $vo = null): void
    {
        $this->precio = $vo;
    }

    public function getNum_asistentes(): ?int
    {
        return $this->num_asistentes;
    }

    public function setNum_asistentes(?int $inum_asistentes = null): void
    {
        $this->num_asistentes = $inum_asistentes;
    }

    /**
     * @deprecated Usar `getStatusVo(): StatusId` en su lugar.
     */
    public function getStatus(): int
    {
        return $this->status->value();
    }

    /**
     * @deprecated Usar `setStatusVo(StatusId $vo): void` en su lugar.
     */
    public function setStatus(int $istatus): void
    {
        $this->status = new StatusId($istatus);
    }

    public function getStatusVo(): StatusId
    {
        return $this->status;
    }

    public function setStatusVo(StatusId $vo): void
    {
        $this->status = $vo;
    }

    public function getObserv(): ?string
    {
        return $this->observ;
    }

    public function setObserv(?string $sobserv = null): void
    {
        $this->observ = $sobserv;
    }

    /**
     * @deprecated Usar `getNivelStgrVo(): ?NivelStgrId` en su lugar.
     */
    public function getNivel_stgr(): ?int
    {
        return $this->nivel_stgr?->value();
    }

    /**
     * @deprecated Usar `setNivelStgrVo(?NivelStgrId $vo = null): void` en su lugar.
     */
    public function setNivel_stgr(?int $inivel_stgr = null): void
    {
        $this->nivel_stgr = new NivelStgrId($inivel_stgr);
    }

    public function getNivelStgrVo(): ?NivelStgrId
    {
        return $this->nivel_stgr;
    }

    public function setNivelStgrVo(?NivelStgrId $vo = null): void
    {
        $this->nivel_stgr = $vo?->value();
    }

    public function getObserv_material(): ?string
    {
        return $this->observ_material;
    }

    public function setObserv_material(?string $sobserv_material = null): void
    {
        $this->observ_material = $sobserv_material;
    }

    public function getLugar_esp(): ?string
    {
        return $this->lugar_esp;
    }

    public function setLugar_esp(?string $slugar_esp = null): void
    {
        $this->lugar_esp = $slugar_esp;
    }

    /**
     * @deprecated Usar `getTarifaVo(): ?TarifaId` en su lugar.
     */
    public function getTarifa(): ?int
    {
        return $this->tarifa?->value();
    }

    /**
     * @deprecated Usar `setTarifaVo(?TarifaId $vo = null): void` en su lugar.
     */
    public function setTarifa(?int $itarifa = null): void
    {
        $this->tarifa = TarifaId::fromNullable($itarifa);
    }

    public function getTarifaVo(): ?TarifaId
    {
        return $this->tarifa;
    }

    public function setTarifaVo(?TarifaId $vo = null): void
    {
        $this->tarifa = $vo;
    }

    /**
     * @deprecated Usar `setIdRepeticionVo()` en su lugar.
     */
    public function getId_repeticion(): ?int
    {
        return $this->id_repeticion?->value();
    }

    /**
     * @deprecated Usar `setIdRepeticionVo(): void` en su lugar.
     */
    public function setId_repeticion(?int $id_repeticion = null): void
    {
        $this->id_repeticion = new RepeticionId($id_repeticion);
    }

    public function getIdRepeticionVo(): ?RepeticionId
    {
        return $this->id_repeticion;
    }

    public function setIdRepeticionVo(?RepeticionId $id_repeticion = null): void
    {
        $this->id_repeticion = $id_repeticion;
    }

    public function isPublicado(): ?bool
    {
        return $this->publicado;
    }

    public function setPublicado(?bool $bpublicado = null): void
    {
        $this->publicado = $bpublicado;
    }

    /**
     * @deprecated Usar `getIdTablaVo(): ?IdTablaCode` en su lugar.
     */
    public function getId_tabla(): ?string
    {
        return $this->id_tabla?->value();
    }

    /**
     * @deprecated Usar `setIdTablaVo(?IdTablaCode $vo = null): void` en su lugar.
     */
    public function setId_tabla(?string $sid_tabla = null): void
    {
        $this->id_tabla = new IdTablaCode($sid_tabla);
    }

    public function getIdTablaVo(): ?IdTablaCode
    {
        return IdTablaCode::fromString($this->id_tabla);
    }

    public function setIdTablaVo(?IdTablaCode $vo = null): void
    {
        $this->id_tabla = $vo?->value();
    }

    public function getPlazas(): ?int
    {
        return $this->plazas;
    }

    public function setPlazas(?int $iplazas = null): void
    {
        $this->plazas = $iplazas;
    }


    public function getIdiomaVo(): ?IdLocale
    {
        return ($this->idioma === null) ? null : new IdLocale($this->idioma);
    }

    public function setIdiomaVo(?IdLocale $vo = null): void
    {
        $this->idioma = $vo;
    }

}