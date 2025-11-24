<?php

namespace src\ubis\domain\entity;

use src\ubis\application\services\UbiContactsTrait;
use src\ubis\domain\value_objects\{CentroId,
    DlCode,
    NBuzon,
    NumCartas,
    NumHabitIndiv,
    NumPi,
    ObservCentroText,
    PaisName,
    Plazas,
    RegionNameText,
    TipoCentroCode,
    TipoLaborId,
    UbiNombreText,
    ZonaId};
use src\ubis\infrastructure\repositories\PgCentroDlDireccionRepository;
use src\ubis\infrastructure\repositories\PgDireccionCentroDlRepository;
use web\DateTimeLocal;
use web\NullDateTimeLocal;
use function core\is_true;

/**
 * Clase que implementa la entidad u_centros_dl
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 20/11/2025
 */
class CentroDl
{
    // Esto inyecta los métodos getDirecciones, emailPrincipalOPrimero y getTeleco aquí
    use UbiContactsTrait;

    protected $repoCasaDireccion;
    protected $repoDireccion;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Tipo_ubi de CentroDl
     *
     * @var string|null
     */
    private string|null $stipo_ubi = null;
    /**
     * Id_ubi de CentroDl (VO)
     */
    private CentroId $iid_ubi;
    /**
     * Nombre_ubi de CentroDl (VO)
     */
    private UbiNombreText $snombre_ubi;
    /**
     * Dl de CentroDl (VO)
     */
    private ?DlCode $sdl = null;
    /**
     * Pais de CentroDl (VO)
     */
    private ?PaisName $spais = null;
    /**
     * Region de CentroDl (VO)
     */
    private ?RegionNameText $sregion = null;
    /**
     * Status de CentroDl
     *
     * @var bool
     */
    private bool $bstatus;
    /**
     * F_status de CentroDl
     *
     * @var DateTimeLocal|null
     */
    private DateTimeLocal|null $df_status = null;
    /**
     * Sv de CentroDl
     *
     * @var bool|null
     */
    private bool|null $bsv = null;
    /**
     * Sf de CentroDl
     *
     * @var bool|null
     */
    private bool|null $bsf = null;
    /**
     * Tipo_ctr de CentroDl (VO código)
     */
    private ?TipoCentroCode $stipo_ctr = null;
    /**
     * Tipo_labor de CentroDl (VO id)
     */
    private ?TipoLaborId $itipo_labor = null;
    /**
     * Cdc de CentroDl
     *
     * @var bool|null
     */
    private bool|null $bcdc = null;
    /**
     * Id_ctr_padre de CentroDl (VO)
     */
    private ?CentroId $iid_ctr_padre = null;
    /**
     * Id_auto de CentroDl
     *
     * @var int
     */
    private int $iid_auto;
    /**
     * N_buzon de CentroDl (VO)
     */
    private ?NBuzon $in_buzon = null;
    /**
     * Num_pi de CentroDl (VO)
     */
    private ?NumPi $inum_pi = null;
    /**
     * Num_cartas de CentroDl (VO)
     */
    private ?NumCartas $inum_cartas = null;
    /**
     * Observ de CentroDl (VO)
     */
    private ?ObservCentroText $sobserv = null;
    /**
     * Num_habit_indiv de CentroDl (VO)
     */
    private ?NumHabitIndiv $inum_habit_indiv = null;
    /**
     * Plazas de CentroDl (VO)
     */
    private ?Plazas $iplazas = null;
    /**
     * Id_zona de CentroDl (VO)
     */
    private ?ZonaId $iid_zona = null;
    /**
     * Sede de CentroDl
     *
     * @var bool|null
     */
    private bool|null $bsede = null;
    /**
     * Num_cartas_mensuales de CentroDl (VO)
     */
    private ?NumCartas $inum_cartas_mensuales = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function __construct()
    {
        $this->repoCasaDireccion = new PgCentroDlDireccionRepository();
        $this->repoDireccion = new PgDireccionCentroDlRepository();
    }

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return CentroDl
     */
    public function setAllAttributes(array $aDatos): CentroDl
    {
        if (array_key_exists('tipo_ubi', $aDatos)) {
            $this->setTipo_ubi($aDatos['tipo_ubi']);
        }
        if (array_key_exists('id_ubi', $aDatos)) {
            $valor = $aDatos['id_ubi'];
            if ($valor instanceof CentroId) {
                $this->setIdUbiVo($valor);
            } else {
                $this->setId_ubi((int)$valor);
            }
        }
        if (array_key_exists('nombre_ubi', $aDatos)) {
            $valor = $aDatos['nombre_ubi'];
            if ($valor instanceof UbiNombreText) {
                $this->setNombreUbiVo($valor);
            } else {
                $this->setNombre_ubi((string)$valor);
            }
        }
        if (array_key_exists('dl', $aDatos)) {
            $valor = $aDatos['dl'] ?? null;
            if ($valor instanceof DlCode || $valor === null) {
                $this->setDlVo($valor);
            } else {
                $this->setDl($valor !== null ? (string)$valor : null);
            }
        }
        if (array_key_exists('pais', $aDatos)) {
            $valor = $aDatos['pais'] ?? null;
            if ($valor instanceof PaisName || $valor === null) {
                $this->setPaisVo($valor);
            } else {
                $this->setPais($valor !== null ? (string)$valor : null);
            }
        }
        if (array_key_exists('region', $aDatos)) {
            $valor = $aDatos['region'] ?? null;
            if ($valor instanceof RegionNameText || $valor === null) {
                $this->setRegionVo($valor);
            } else {
                $this->setRegion($valor !== null ? (string)$valor : null);
            }
        }
        if (array_key_exists('status', $aDatos)) {
            $this->setStatus(is_true($aDatos['status']));
        }
        if (array_key_exists('f_status', $aDatos)) {
            $this->setF_status($aDatos['f_status']);
        }
        if (array_key_exists('sv', $aDatos)) {
            $this->setSv(is_true($aDatos['sv']));
        }
        if (array_key_exists('sf', $aDatos)) {
            $this->setSf(is_true($aDatos['sf']));
        }
        if (array_key_exists('tipo_ctr', $aDatos)) {
            $valor = $aDatos['tipo_ctr'] ?? null;
            if ($valor instanceof TipoCentroCode || $valor === null) {
                $this->setTipoCtrVo($valor);
            } else {
                $this->setTipo_ctr($valor !== null ? (string)$valor : null);
            }
        }
        if (array_key_exists('tipo_labor', $aDatos)) {
            $valor = $aDatos['tipo_labor'] ?? null;
            if ($valor instanceof TipoLaborId || $valor === null) {
                $this->setTipoLaborVo($valor);
            } else {
                $this->setTipo_labor(isset($valor) && $valor !== '' ? (int)$valor : null);
            }
        }
        if (array_key_exists('cdc', $aDatos)) {
            $this->setCdc(is_true($aDatos['cdc']));
        }
        if (array_key_exists('id_ctr_padre', $aDatos)) {
            $valor = $aDatos['id_ctr_padre'] ?? null;
            if ($valor instanceof CentroId || $valor === null) {
                $this->setIdCtrPadreVo($valor);
            } else {
                $this->setId_ctr_padre(isset($valor) && $valor !== '' ? (int)$valor : null);
            }
        }
        if (array_key_exists('id_auto', $aDatos)) {
            $this->setId_auto($aDatos['id_auto']);
        }
        if (array_key_exists('n_buzon', $aDatos)) {
            $valor = $aDatos['n_buzon'] ?? null;
            if ($valor instanceof NBuzon || $valor === null) {
                $this->setNBuzonVo($valor);
            } else {
                $this->setN_buzon(isset($valor) && $valor !== '' ? (int)$valor : null);
            }
        }
        if (array_key_exists('num_pi', $aDatos)) {
            $valor = $aDatos['num_pi'] ?? null;
            if ($valor instanceof NumPi || $valor === null) {
                $this->setNumPiVo($valor);
            } else {
                $this->setNum_pi(isset($valor) && $valor !== '' ? (int)$valor : null);
            }
        }
        if (array_key_exists('num_cartas', $aDatos)) {
            $valor = $aDatos['num_cartas'] ?? null;
            if ($valor instanceof NumCartas || $valor === null) {
                $this->setNumCartasVo($valor);
            } else {
                $this->setNum_cartas(isset($valor) && $valor !== '' ? (int)$valor : null);
            }
        }
        if (array_key_exists('observ', $aDatos)) {
            $valor = $aDatos['observ'] ?? null;
            if ($valor instanceof ObservCentroText || $valor === null) {
                $this->setObservVo($valor);
            } else {
                $this->setObserv($valor !== null ? (string)$valor : null);
            }
        }
        if (array_key_exists('num_habit_indiv', $aDatos)) {
            $valor = $aDatos['num_habit_indiv'] ?? null;
            if ($valor instanceof NumHabitIndiv || $valor === null) {
                $this->setNumHabitIndivVo($valor);
            } else {
                $this->setNum_habit_indiv(isset($valor) && $valor !== '' ? (int)$valor : null);
            }
        }
        if (array_key_exists('plazas', $aDatos)) {
            $valor = $aDatos['plazas'] ?? null;
            if ($valor instanceof Plazas || $valor === null) {
                $this->setPlazasVo($valor);
            } else {
                $this->setPlazas(isset($valor) && $valor !== '' ? (int)$valor : null);
            }
        }
        if (array_key_exists('id_zona', $aDatos)) {
            $valor = $aDatos['id_zona'] ?? null;
            if ($valor instanceof ZonaId || $valor === null) {
                $this->setIdZonaVo($valor);
            } else {
                $this->setId_zona(isset($valor) && $valor !== '' ? (int)$valor : null);
            }
        }
        if (array_key_exists('sede', $aDatos)) {
            $this->setSede(is_true($aDatos['sede']));
        }
        if (array_key_exists('num_cartas_mensuales', $aDatos)) {
            $valor = $aDatos['num_cartas_mensuales'] ?? null;
            if ($valor instanceof NumCartas || $valor === null) {
                $this->setNumCartasMensualesVo($valor);
            } else {
                $this->setNum_cartas_mensuales(isset($valor) && $valor !== '' ? (int)$valor : null);
            }
        }
        return $this;
    }

    /**
     *
     * @return string|null $stipo_ubi
     */
    public function getTipo_ubi(): ?string
    {
        return $this->stipo_ubi;
    }

    /**
     *
     * @param string|null $stipo_ubi
     */
    public function setTipo_ubi(?string $stipo_ubi = null): void
    {
        $this->stipo_ubi = $stipo_ubi;
    }

    /**
     *
     * @return int $iid_ubi
     */
    /**
     * @deprecated Usar `getIdUbiVo(): CentroId` en su lugar.
     */
    public function getId_ubi(): int
    {
        return $this->iid_ubi->value();
    }

    /**
     *
     * @param int $iid_ubi
     */
    /**
     * @deprecated Usar `setIdUbiVo(CentroId $id): void` en su lugar.
     */
    public function setId_ubi(int $iid_ubi): void
    {
        $this->iid_ubi = new CentroId($iid_ubi);
    }

    // -------- API VO (nueva) ---------
    public function getIdUbiVo(): CentroId
    {
        return $this->iid_ubi;
    }

    public function setIdUbiVo(CentroId $id): void
    {
        $this->iid_ubi = $id;
    }

    /**
     *
     * @return string $snombre_ubi
     */
    /**
     * @deprecated Usar `getNombreUbiVo(): UbiNombreText` en su lugar.
     */
    public function getNombre_ubi(): string
    {
        return $this->snombre_ubi->value();
    }

    /**
     *
     * @param string $snombre_ubi
     */
    /**
     * @deprecated Usar `setNombreUbiVo(UbiNombreText $texto): void` en su lugar.
     */
    public function setNombre_ubi(string $snombre_ubi): void
    {
        $this->snombre_ubi = new UbiNombreText($snombre_ubi);
    }

    public function getNombreUbiVo(): UbiNombreText
    {
        return $this->snombre_ubi;
    }

    public function setNombreUbiVo(UbiNombreText $texto): void
    {
        $this->snombre_ubi = $texto;
    }

    /**
     *
     * @return string|null $sdl
     */
    /**
     * @deprecated Usar `getDlVo(): ?DlCode` en su lugar.
     */
    public function getDl(): ?string
    {
        return $this->sdl?->value();
    }

    /**
     *
     * @param string|null $sdl
     */
    /**
     * @deprecated Usar `setDlVo(?DlCode $codigo = null): void` en su lugar.
     */
    public function setDl(?string $sdl = null): void
    {
        $this->sdl = DlCode::fromNullableString($sdl);
    }

    public function getDlVo(): ?DlCode
    {
        return $this->sdl;
    }

    public function setDlVo(?DlCode $codigo = null): void
    {
        $this->sdl = $codigo;
    }

    /**
     *
     * @return string|null $spais
     */
    /**
     * @deprecated Usar `getPaisVo(): ?PaisName` en su lugar.
     */
    public function getPais(): ?string
    {
        return $this->spais?->value();
    }

    /**
     *
     * @param string|null $spais
     */
    /**
     * @deprecated Usar `setPaisVo(?PaisName $nombre = null): void` en su lugar.
     */
    public function setPais(?string $spais = null): void
    {
        $this->spais = PaisName::fromNullableString($spais);
    }

    public function getPaisVo(): ?PaisName
    {
        return $this->spais;
    }

    public function setPaisVo(?PaisName $nombre = null): void
    {
        $this->spais = $nombre;
    }

    /**
     *
     * @return string|null $sregion
     */
    /**
     * @deprecated Usar `getRegionVo(): ?RegionNameText` en su lugar.
     */
    public function getRegion(): ?string
    {
        return $this->sregion?->value();
    }

    /**
     *
     * @param string|null $sregion
     */
    /**
     * @deprecated Usar `setRegionVo(?RegionNameText $texto = null): void` en su lugar.
     */
    public function setRegion(?string $sregion = null): void
    {
        $this->sregion = RegionNameText::fromNullableString($sregion);
    }

    public function getRegionVo(): ?RegionNameText
    {
        return $this->sregion;
    }

    public function setRegionVo(?RegionNameText $texto = null): void
    {
        $this->sregion = $texto;
    }

    /**
     *
     * @return bool $bstatus
     */
    /**
     * @deprecated Mantener por compatibilidad UI; no se define VO para booleanos.
     */
    public function isStatus(): bool
    {
        return $this->bstatus;
    }

    /**
     *
     * @param bool $bstatus
     */
    /**
     * @deprecated Mantener por compatibilidad UI; no se define VO para booleanos.
     */
    public function setStatus(bool $bstatus): void
    {
        $this->bstatus = $bstatus;
    }

    /**
     *
     * @return DateTimeLocal|NullDateTimeLocal|null $df_status
     */
    /**
     * @deprecated Mantener por compatibilidad UI; se usa DateTimeLocal/NullDateTimeLocal.
     */
    public function getF_status(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->df_status ?? new NullDateTimeLocal;
    }

    /**
     *
     * @param DateTimeLocal|null $df_status
     */
    /**
     * @deprecated Mantener por compatibilidad UI; se usa DateTimeLocal/NullDateTimeLocal.
     */
    public function setF_status(DateTimeLocal|null $df_status = null): void
    {
        $this->df_status = $df_status;
    }

    /**
     *
     * @return bool|null $bsv
     */
    /**
     * @deprecated Mantener por compatibilidad UI; no se define VO para booleanos.
     */
    public function isSv(): ?bool
    {
        return $this->bsv;
    }

    /**
     *
     * @param bool|null $bsv
     */
    /**
     * @deprecated Mantener por compatibilidad UI; no se define VO para booleanos.
     */
    public function setSv(?bool $bsv = null): void
    {
        $this->bsv = $bsv;
    }

    /**
     *
     * @return bool|null $bsf
     */
    /**
     * @deprecated Mantener por compatibilidad UI; no se define VO para booleanos.
     */
    public function isSf(): ?bool
    {
        return $this->bsf;
    }

    /**
     *
     * @param bool|null $bsf
     */
    /**
     * @deprecated Mantener por compatibilidad UI; no se define VO para booleanos.
     */
    public function setSf(?bool $bsf = null): void
    {
        $this->bsf = $bsf;
    }

    /**
     *
     * @return string|null $stipo_ctr
     */
    /**
     * @deprecated Usar `getTipoCtrVo(): ?TipoCentroCode` en su lugar.
     */
    public function getTipo_ctr(): ?string
    {
        return $this->stipo_ctr?->value();
    }

    /**
     *
     * @param string|null $stipo_ctr
     */
    /**
     * @deprecated Usar `setTipoCtrVo(?TipoCentroCode $codigo = null): void` en su lugar.
     */
    public function setTipo_ctr(?string $stipo_ctr = null): void
    {
        $this->stipo_ctr = $stipo_ctr !== null && $stipo_ctr !== '' ? new TipoCentroCode($stipo_ctr) : null;
    }

    public function getTipoCtrVo(): ?TipoCentroCode
    {
        return $this->stipo_ctr;
    }

    public function setTipoCtrVo(?TipoCentroCode $codigo = null): void
    {
        $this->stipo_ctr = $codigo;
    }

    /**
     *
     * @return int|null $itipo_labor
     */
    /**
     * @deprecated Usar `getTipoLaborVo(): ?TipoLaborId` en su lugar.
     */
    public function getTipo_labor(): ?int
    {
        return $this->itipo_labor?->value();
    }

    /**
     *
     * @param int|null $itipo_labor
     */
    /**
     * @deprecated Usar `setTipoLaborVo(?TipoLaborId $valor = null): void` en su lugar.
     */
    public function setTipo_labor(?int $itipo_labor = null): void
    {
        $this->itipo_labor = TipoLaborId::fromNullable($itipo_labor);
    }

    public function getTipoLaborVo(): ?TipoLaborId
    {
        return $this->itipo_labor;
    }

    public function setTipoLaborVo(?TipoLaborId $valor = null): void
    {
        $this->itipo_labor = $valor;
    }

    /**
     *
     * @return bool|null $bcdc
     */
    public function isCdc(): ?bool
    {
        return $this->bcdc;
    }

    /**
     *
     * @param bool|null $bcdc
     */
    public function setCdc(?bool $bcdc = null): void
    {
        $this->bcdc = $bcdc;
    }

    /**
     *
     * @return int|null $iid_ctr_padre
     */
    /**
     * @deprecated Usar `getIdCtrPadreVo(): ?CentroId` en su lugar.
     */
    public function getId_ctr_padre(): ?int
    {
        return $this->iid_ctr_padre?->value();
    }

    /**
     *
     * @param int|null $iid_ctr_padre
     */
    /**
     * @deprecated Usar `setIdCtrPadreVo(?CentroId $id = null): void` en su lugar.
     */
    public function setId_ctr_padre(?int $iid_ctr_padre = null): void
    {
        $this->iid_ctr_padre = $iid_ctr_padre !== null ? new CentroId($iid_ctr_padre) : null;
    }

    public function getIdCtrPadreVo(): ?CentroId
    {
        return $this->iid_ctr_padre;
    }

    public function setIdCtrPadreVo(?CentroId $id = null): void
    {
        $this->iid_ctr_padre = $id;
    }

    /**
     *
     * @return int $iid_auto
     */
    public function getId_auto(): int
    {
        return $this->iid_auto;
    }

    /**
     *
     * @param int $iid_auto
     */
    public function setId_auto(int $iid_auto): void
    {
        $this->iid_auto = $iid_auto;
    }

    /**
     *
     * @return int|null $in_buzon
     */
    /**
     * @deprecated Usar `getNBuzonVo(): ?NBuzon` en su lugar.
     */
    public function getN_buzon(): ?int
    {
        return $this->in_buzon?->value();
    }

    /**
     *
     * @param int|null $in_buzon
     */
    /**
     * @deprecated Usar `setNBuzonVo(?NBuzon $valor = null): void` en su lugar.
     */
    public function setN_buzon(?int $in_buzon = null): void
    {
        $this->in_buzon = NBuzon::fromNullable($in_buzon);
    }

    public function getNBuzonVo(): ?NBuzon
    {
        return $this->in_buzon;
    }

    public function setNBuzonVo(?NBuzon $valor = null): void
    {
        $this->in_buzon = $valor;
    }

    /**
     *
     * @return int|null $inum_pi
     */
    /**
     * @deprecated Usar `getNumPiVo(): ?NumPi` en su lugar.
     */
    public function getNum_pi(): ?int
    {
        return $this->inum_pi?->value();
    }

    /**
     *
     * @param int|null $inum_pi
     */
    /**
     * @deprecated Usar `setNumPiVo(?NumPi $valor = null): void` en su lugar.
     */
    public function setNum_pi(?int $inum_pi = null): void
    {
        $this->inum_pi = NumPi::fromNullable($inum_pi);
    }

    public function getNumPiVo(): ?NumPi
    {
        return $this->inum_pi;
    }

    public function setNumPiVo(?NumPi $valor = null): void
    {
        $this->inum_pi = $valor;
    }

    /**
     *
     * @return int|null $inum_cartas
     */
    /**
     * @deprecated Usar `getNumCartasVo(): ?NumCartas` en su lugar.
     */
    public function getNum_cartas(): ?int
    {
        return $this->inum_cartas?->value();
    }

    /**
     *
     * @param int|null $inum_cartas
     */
    /**
     * @deprecated Usar `setNumCartasVo(?NumCartas $valor = null): void` en su lugar.
     */
    public function setNum_cartas(?int $inum_cartas = null): void
    {
        $this->inum_cartas = NumCartas::fromNullable($inum_cartas);
    }

    public function getNumCartasVo(): ?NumCartas
    {
        return $this->inum_cartas;
    }

    public function setNumCartasVo(?NumCartas $valor = null): void
    {
        $this->inum_cartas = $valor;
    }

    /**
     *
     * @return string|null $sobserv
     */
    /**
     * @deprecated Usar `getObservVo(): ?ObservCentroText` en su lugar.
     */
    public function getObserv(): ?string
    {
        return $this->sobserv?->value();
    }

    /**
     *
     * @param string|null $sobserv
     */
    /**
     * @deprecated Usar `setObservVo(?ObservCentroText $texto = null): void` en su lugar.
     */
    public function setObserv(?string $sobserv = null): void
    {
        $this->sobserv = ObservCentroText::fromNullableString($sobserv);
    }

    public function getObservVo(): ?ObservCentroText
    {
        return $this->sobserv;
    }

    public function setObservVo(?ObservCentroText $texto = null): void
    {
        $this->sobserv = $texto;
    }

    /**
     *
     * @return int|null $inum_habit_indiv
     */
    /**
     * @deprecated Usar `getNumHabitIndivVo(): ?NumHabitIndiv` en su lugar.
     */
    public function getNum_habit_indiv(): ?int
    {
        return $this->inum_habit_indiv?->value();
    }

    /**
     *
     * @param int|null $inum_habit_indiv
     */
    /**
     * @deprecated Usar `setNumHabitIndivVo(?NumHabitIndiv $valor = null): void` en su lugar.
     */
    public function setNum_habit_indiv(?int $inum_habit_indiv = null): void
    {
        $this->inum_habit_indiv = NumHabitIndiv::fromNullable($inum_habit_indiv);
    }

    public function getNumHabitIndivVo(): ?NumHabitIndiv
    {
        return $this->inum_habit_indiv;
    }

    public function setNumHabitIndivVo(?NumHabitIndiv $valor = null): void
    {
        $this->inum_habit_indiv = $valor;
    }

    /**
     *
     * @return int|null $iplazas
     */
    /**
     * @deprecated Usar `getPlazasVo(): ?Plazas` en su lugar.
     */
    public function getPlazas(): ?int
    {
        return $this->iplazas?->value();
    }

    /**
     *
     * @param int|null $iplazas
     */
    /**
     * @deprecated Usar `setPlazasVo(?Plazas $valor = null): void` en su lugar.
     */
    public function setPlazas(?int $iplazas = null): void
    {
        $this->iplazas = Plazas::fromNullable($iplazas);
    }

    public function getPlazasVo(): ?Plazas
    {
        return $this->iplazas;
    }

    public function setPlazasVo(?Plazas $valor = null): void
    {
        $this->iplazas = $valor;
    }

    /**
     *
     * @return int|null $iid_zona
     */
    /**
     * @deprecated Usar `getIdZonaVo(): ?ZonaId` en su lugar.
     */
    public function getId_zona(): ?int
    {
        return $this->iid_zona?->value();
    }

    /**
     *
     * @param int|null $iid_zona
     */
    /**
     * @deprecated Usar `setIdZonaVo(?ZonaId $id = null): void` en su lugar.
     */
    public function setId_zona(?int $iid_zona = null): void
    {
        $this->iid_zona = ZonaId::fromNullable($iid_zona);
    }

    public function getIdZonaVo(): ?ZonaId
    {
        return $this->iid_zona;
    }

    public function setIdZonaVo(?ZonaId $id = null): void
    {
        $this->iid_zona = $id;
    }

    /**
     *
     * @return bool|null $bsede
     */
    /**
     * @deprecated Mantener por compatibilidad UI; no se define VO para booleanos.
     */
    public function isSede(): ?bool
    {
        return $this->bsede;
    }

    /**
     *
     * @param bool|null $bsede
     */
    /**
     * @deprecated Mantener por compatibilidad UI; no se define VO para booleanos.
     */
    public function setSede(?bool $bsede = null): void
    {
        $this->bsede = $bsede;
    }

    /**
     *
     * @return int|null $inum_cartas_mensuales
     */
    /**
     * @deprecated Usar `getNumCartasMensualesVo(): ?NumCartas` en su lugar.
     */
    public function getNum_cartas_mensuales(): ?int
    {
        return $this->inum_cartas_mensuales?->value();
    }

    /**
     *
     * @param int|null $inum_cartas_mensuales
     */
    /**
     * @deprecated Usar `setNumCartasMensualesVo(?NumCartas $valor = null): void` en su lugar.
     */
    public function setNum_cartas_mensuales(?int $inum_cartas_mensuales = null): void
    {
        $this->inum_cartas_mensuales = NumCartas::fromNullable($inum_cartas_mensuales);
    }

    public function getNumCartasMensualesVo(): ?NumCartas
    {
        return $this->inum_cartas_mensuales;
    }

    public function setNumCartasMensualesVo(?NumCartas $valor = null): void
    {
        $this->inum_cartas_mensuales = $valor;
    }

    /* MÉTODOS PARA GESTIÓN DE DIRECCIONES ----------------------------------------*/

    /**
     * Obtiene Una direccione con sus metadatos (principal, propietario)
     *
     * @return array<DireccionDetalle>
     */
    public function getUnaDireccionDetallada(int $id_direccion): ?DireccionDetalle
    {
        $relaciones = $this->repoCasaDireccion->getRelacionesPorUbi($this->getId_ubi());
        $direcciDetallada = null;

        foreach ($relaciones as $row) {
            if ($id_direccion !== $row['id_direccion']) {
                continue;
            }
            $direccion = $this->repoDireccion->findById($row['id_direccion']);
            if ($direccion !== null) {
                // Creamos el objeto intermedio con los booleanos de la DB
                // Aseguramos conversión a bool explícita
                $esPrincipal = is_true($row['principal']);
                $esPropietario = is_true($row['propietario']);

                $direccionDetallada = new DireccionDetalle(
                    $direccion,
                    $esPrincipal,
                    $esPropietario
                );
            }
        }

        return $direccionDetallada;
    }

    /**
     * Obtiene las direcciones con sus metadatos (principal, propietario)
     *
     * @return array<DireccionDetalle>
     */
    public function getDireccionesDetalladas(): array
    {

        $relaciones = $this->repoCasaDireccion->getRelacionesPorUbi($this->getId_ubi());
        $direccionesDetalladas = [];

        foreach ($relaciones as $row) {
            $direccion = $this->repoDireccion->findById($row['id_direccion']);
            if ($direccion !== null) {
                // Creamos el objeto intermedio con los booleanos de la DB
                // Aseguramos conversión a bool explícita
                $esPrincipal = is_true($row['principal']);
                $esPropietario = is_true($row['propietario']);

                $direccionesDetalladas[] = new DireccionDetalle(
                    $direccion,
                    $esPrincipal,
                    $esPropietario
                );
            }
        }

        return $direccionesDetalladas;
    }

    /**
     * Obtiene todas las direcciones asociadas a esta casa
     *
     * @return array<Direccion>
     */
    public function getDirecciones(): array
    {
        $detalles = $this->getDireccionesDetalladas();
        return array_map(fn(DireccionDetalle $d) => $d->getDireccion(), $detalles);
    }

    /**
     * Añade una dirección a esta casa
     */
    public function addDireccion(int $id_direccion, bool $principal = false, bool $propietario = false): void
    {
        $this->repoCasaDireccion->asociarDireccion(
            $this->getId_ubi(),
            $id_direccion,
            $principal,
            $propietario
        );
    }

    /**
     * Elimina una dirección de esta casa
     */
    public function removeDireccion(int $id_direccion): void
    {
        $this->repoCasaDireccion->desasociarDireccion($this->getId_ubi(), $id_direccion);
    }

    /**
     * Obtiene la dirección principal de esta casa
     */
    public function getDireccionPrincipal(): ?Direccion
    {
        $id_direccion_principal = $this->repoCasaDireccion->getDireccionPrincipal($this->getId_ubi());

        if ($id_direccion_principal === null) {
            return null;
        }

        return $this->repoDireccion->findById($id_direccion_principal);
    }

    /**
     * Establece una dirección como principal
     */
    public function establecerDireccionPrincipal(int $id_direccion): void
    {
        $this->repoCasaDireccion->establecerDireccionPrincipal($this->getId_ubi(), $id_direccion);
    }

    /**
     * Cambia el estado de propiedad de una dirección específica.
     */
    public function cambiarEstadoPropietario(int $id_direccion, bool $esPropietario): void
    {
        // Llamamos a un método en el repositorio para actualizar solo este campo
        $this->repoCasaDireccion->updatePropietario($this->getId_ubi(), $id_direccion, $esPropietario);
    }
}