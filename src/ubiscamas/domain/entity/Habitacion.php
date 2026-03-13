<?php

namespace src\ubiscamas\domain\entity;

use src\shared\domain\traits\Hydratable;
use src\ubiscamas\domain\value_objects\{TipoLavabo,
    HabitacionId,
    HabitacionNombre,
    HabitacionOrden,
    NumeroCamas,
    PlantaText};

class Habitacion
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private ?int $id_schema = null;

    private int $id_ubi;

    private HabitacionId $id_habitacion;

    private HabitacionOrden $orden;

    private ?HabitacionNombre $nombre = null;

    private ?NumeroCamas $numero_camas = null;

    private ?NumeroCamas $numero_camas_vip = null;

    private ?PlantaText $planta = null;

    private ?bool $sillon = null;

    private ?bool $adaptada = null;

    private ?bool $fumador = null;

    private ?TipoLavabo $tipoLavabo = null;

    private ?bool $despacho = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_schema(): ?int
    {
        return $this->id_schema;
    }

    public function setId_schema(?int $id_schema = null): void
    {
        $this->id_schema = $id_schema;
    }

    /**
     * @deprecated Usar `getIdUbiVo(): int` en su lugar.
     */
    public function getId_ubi(): int
    {
        return $this->id_ubi;
    }

    /**
     * @deprecated Usar `setIdUbiVo(int $id): void` en su lugar.
     */
    public function setId_ubi(int $id_ubi): void
    {
        $this->id_ubi = $id_ubi;
    }

    // -------- API VO (nueva) ---------
    public function getIdUbiVo(): int
    {
        return $this->id_ubi;
    }

    public function setIdUbiVo(int $id): void
    {
        $this->id_ubi = $id;
    }

    /**
     * @deprecated Usar `getIdHabitacionVo(): HabitacionId` en su lugar.
     */
    public function getId_habitacion(): string
    {
        return $this->id_habitacion->value();
    }

    /**
     * @deprecated Usar `setIdHabitacionVo(HabitacionId $id): void` en su lugar.
     */
    public function setId_habitacion(string $id_habitacion): void
    {
        $this->id_habitacion = new HabitacionId($id_habitacion);
    }

    public function getIdHabitacionVo(): HabitacionId
    {
        return $this->id_habitacion;
    }

    public function setIdHabitacionVo(HabitacionId|string|null $id): void
    {
        $this->id_habitacion = $id instanceof HabitacionId
            ? $id
            : HabitacionId::fromNullableString($id);
    }

    /**
     * @deprecated Usar `getOrdenVo(): HabitacionOrden` en su lugar.
     */
    public function getOrden(): int
    {
        return $this->orden->value();
    }

    /**
     * @deprecated Usar `setOrdenVo(HabitacionOrden $orden): void` en su lugar.
     */
    public function setOrden(int $orden): void
    {
        $this->orden = new HabitacionOrden($orden);
    }

    public function getOrdenVo(): HabitacionOrden
    {
        return $this->orden;
    }

    public function setOrdenVo(HabitacionOrden|int $valor): void
    {
        $this->orden = $valor instanceof HabitacionOrden
            ? $valor
            : new HabitacionOrden($valor);
    }

    /**
     * @deprecated Usar `getNombreVo(): ?HabitacionNombre` en su lugar.
     */
    public function getNombre(): ?string
    {
        return $this->nombre?->value();
    }

    /**
     * @deprecated Usar `setNombreVo(?HabitacionNombre $nombre): void` en su lugar.
     */
    public function setNombre(?string $nombre = null): void
    {
        $this->nombre = HabitacionNombre::fromNullableString($nombre);
    }

    public function getNombreVo(): ?HabitacionNombre
    {
        return $this->nombre;
    }

    public function setNombreVo(HabitacionNombre|string|null $texto = null): void
    {
        $this->nombre = $texto instanceof HabitacionNombre
            ? $texto
            : HabitacionNombre::fromNullableString($texto);
    }

    /**
     * @deprecated Usar `getNumeroCamasVo(): ?NumeroCamas` en su lugar.
     */
    public function getNumero_camas(): ?int
    {
        return $this->numero_camas?->value();
    }

    /**
     * @deprecated Usar `setNumeroCamasVo(?NumeroCamas $numero): void` en su lugar.
     */
    public function setNumero_camas(?int $numero_camas = null): void
    {
        $this->numero_camas = NumeroCamas::fromNullableInt($numero_camas);
    }

    public function getNumeroCamasVo(): ?NumeroCamas
    {
        return $this->numero_camas;
    }

    public function setNumeroCamasVo(NumeroCamas|int|null $valor = null): void
    {
        $this->numero_camas = $valor instanceof NumeroCamas
            ? $valor
            : NumeroCamas::fromNullableInt($valor);
    }

    /**
     * @deprecated Usar `getNumeroCamasVipVo(): ?NumeroCamas` en su lugar.
     */
    public function getNumero_camas_vip(): ?int
    {
        return $this->numero_camas_vip?->value();
    }

    /**
     * @deprecated Usar `setNumeroCamasVipVo(?NumeroCamas $numero): void` en su lugar.
     */
    public function setNumero_camas_vip(?int $numero_camas_vip = null): void
    {
        $this->numero_camas_vip = NumeroCamas::fromNullableInt($numero_camas_vip);
    }

    public function getNumeroCamasVipVo(): ?NumeroCamas
    {
        return $this->numero_camas_vip;
    }

    public function setNumeroCamasVipVo(NumeroCamas|int|null $valor = null): void
    {
        $this->numero_camas_vip = $valor instanceof NumeroCamas
            ? $valor
            : NumeroCamas::fromNullableInt($valor);
    }

    /**
     * @deprecated Usar `getPlantaVo(): ?PlantaText` en su lugar.
     */
    public function getPlanta(): ?string
    {
        return $this->planta?->value();
    }

    /**
     * @deprecated Usar `setPlantaVo(?PlantaText $planta): void` en su lugar.
     */
    public function setPlanta(?string $planta = null): void
    {
        $this->planta = PlantaText::fromNullableString($planta);
    }

    public function getPlantaVo(): ?PlantaText
    {
        return $this->planta;
    }

    public function setPlantaVo(PlantaText|string|null $texto = null): void
    {
        $this->planta = $texto instanceof PlantaText
            ? $texto
            : PlantaText::fromNullableString($texto);
    }

    public function isSillon(): ?bool
    {
        return $this->sillon;
    }

    public function setSillon(?bool $sillon = null): void
    {
        $this->sillon = $sillon;
    }

    public function isAdaptada(): ?bool
    {
        return $this->adaptada;
    }

    public function setAdaptada(?bool $adaptada = null): void
    {
        $this->adaptada = $adaptada;
    }

    public function isFumador(): ?bool
    {
        return $this->fumador;
    }

    public function setFumador(?bool $fumador = null): void
    {
        $this->fumador = $fumador;
    }

    /**
     * @deprecated Usar `getTipoLavaboVo(): ?TipoLavabo` en su lugar.
     */
    public function getTipoLavabo(): ?int
    {
        return $this->tipoLavabo?->value();
    }

    /**
     * @deprecated Usar `setTipoLavaboVo(?TipoLavabo $tipo): void` en su lugar.
     */
    public function setTipoLavabo(?int $tipoLavabo = null): void
    {
        $this->tipoLavabo = TipoLavabo::fromNullableInt($tipoLavabo);
    }

    public function getTipoLavaboVo(): ?TipoLavabo
    {
        return $this->tipoLavabo;
    }

    public function setTipoLavaboVo(TipoLavabo|int|null $valor = null): void
    {
        $this->tipoLavabo = $valor instanceof TipoLavabo
            ? $valor
            : TipoLavabo::fromNullableInt($valor);
    }

    public function isDespacho(): ?bool
    {
        return $this->despacho;
    }

    public function setDespacho(?bool $despacho = null): void
    {
        $this->despacho = $despacho;
    }
}
