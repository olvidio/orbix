<?php

namespace src\ubis\domain\entity;

use src\shared\domain\traits\Hydratable;
use src\ubis\application\services\UbiContactsTrait;
use src\ubis\domain\contracts\DireccionCentroRepositoryInterface;
use src\ubis\domain\contracts\RelacionCentroDireccionRepositoryInterface;
use src\ubis\domain\value_objects\{CentroId,
    DelegacionCode,
    PaisName,
    RegionNameText,
    TipoCentroCode,
    TipoLaborId,
    UbiNombreText};
use web\DateTimeLocal;
use web\NullDateTimeLocal;
use function core\is_true;

class Centro
{
    use Hydratable;

    // Esto inyecta los métodos getDirecciones, emailPrincipalOPrimero y getTeleco aquí
    use UbiContactsTrait;

    protected $repoCasaDireccion;
    protected $repoDireccion;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private string|null $tipo_ubi = null;

    private CentroId $id_ubi;

    private UbiNombreText $nombre_ubi;

    private ?DelegacionCode $dl = null;

    private ?PaisName $pais = null;

    private ?RegionNameText $region = null;

    private bool $active;

    private DateTimeLocal|null $f_active = null;

    private bool|null $sv = null;

    private bool|null $sf = null;

    private ?TipoCentroCode $tipo_ctr = null;

    private ?TipoLaborId $tipo_labor = null;

    private bool|null $cdc = null;

    private ?CentroId $id_ctr_padre = null;

    private int $id_auto;

    /**
     * Constructor para inyectar los repositorios necesarios.
     *
     * $repoCasaDireccion Repositorio de relación Casa-Dirección
     * $repoDireccion Repositorio de Direcciones
     */
    public function __construct()
    {
        $this->repoCasaDireccion = $GLOBALS['container']->get(RelacionCentroDireccionRepositoryInterface::class);
        $this->repoDireccion = $GLOBALS['container']->get(DireccionCentroRepositoryInterface::class);
    }

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getTipo_ubi(): ?string
    {
        return $this->tipo_ubi;
    }


    public function setTipo_ubi(?string $tipo_ubi = null): void
    {
        $this->tipo_ubi = $tipo_ubi;
    }


    /**
     * @deprecated Usar `getIdUbiVo(): CentroId` en su lugar.
     */
    public function getId_ubi(): int
    {
        return $this->id_ubi->value();
    }


    /**
     * @deprecated Usar `setIdUbiVo(CentroId $id): void` en su lugar.
     */
    public function setId_ubi(int $id_ubi): void
    {
        $this->id_ubi = new CentroId($id_ubi);
    }

    // -------- API VO (nueva) ---------

    /** Getter VO para id_ubi */
    public function getIdUbiVo(): CentroId
    {
        return $this->id_ubi;
    }

    /** Setter VO para id_ubi */
    public function setIdUbiVo(CentroId $id): void
    {
        $this->id_ubi = $id;
    }


    /**
     * @deprecated Usar `getNombreUbiVo(): UbiNombreText` en su lugar.
     */
    public function getNombre_ubi(): string
    {
        return $this->nombre_ubi->value();
    }


    /**
     * @deprecated Usar `setNombreUbiVo(UbiNombreText $texto): void` en su lugar.
     */
    public function setNombre_ubi(string $nombre_ubi): void
    {
        $this->nombre_ubi = new UbiNombreText($nombre_ubi);
    }

    public function getNombreUbiVo(): UbiNombreText
    {
        return $this->nombre_ubi;
    }

    public function setNombreUbiVo(UbiNombreText $texto): void
    {
        $this->nombre_ubi = $texto;
    }


    /**
     * @deprecated Usar `getDlVo(): ?DelegacionCode` en su lugar.
     */
    public function getDl(): ?string
    {
        return $this->dl?->value();
    }


    /**
     * @deprecated Usar `setDlVo(?DelegacionCode $codigo = null): void` en su lugar.
     */
    public function setDl(?string $dl = null): void
    {
        $this->dl = DelegacionCode::fromString($dl);
    }

    public function getDlVo(): ?DelegacionCode
    {
        return $this->dl;
    }

    public function setDlVo(?DelegacionCode $codigo = null): void
    {
        $this->dl = $codigo;
    }


    /**
     * @deprecated Usar `getPaisVo(): ?PaisName` en su lugar.
     */
    public function getPais(): ?string
    {
        return $this->pais?->value();
    }


    /**
     * @deprecated Usar `setPaisVo(?PaisName $nombre = null): void` en su lugar.
     */
    public function setPais(?string $pais = null): void
    {
        $this->pais = PaisName::fromNullableString($pais);
    }

    public function getPaisVo(): ?PaisName
    {
        return $this->pais;
    }

    public function setPaisVo(?PaisName $nombre = null): void
    {
        $this->pais = $nombre;
    }


    /**
     * @deprecated Usar `getRegionVo(): ?RegionNameText` en su lugar.
     */
    public function getRegion(): ?string
    {
        return $this->region?->value();
    }


    /**
     * @deprecated Usar `setRegionVo(?RegionNameText $texto = null): void` en su lugar.
     */
    public function setRegion(?string $region = null): void
    {
        $this->region = RegionNameText::fromNullableString($region);
    }

    public function getRegionVo(): ?RegionNameText
    {
        return $this->region;
    }

    public function setRegionVo(?RegionNameText $texto = null): void
    {
        $this->region = $texto;
    }

    /**
     *
     * @return bool $bactive
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     *
     * @param bool $active
     */
    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    /**
     *
     * @return DateTimeLocal|NullDateTimeLocal|null $df_active
     */
    public function getF_active(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->f_active ?? new NullDateTimeLocal;
    }

    /**
     *
     * @param DateTimeLocal|null $df_active
     */
    public function setF_active(DateTimeLocal|NullDateTimeLocal|null $df_active = null): void
    {
        $this->f_active = $df_active instanceof NullDateTimeLocal ? null : $df_active;
    }



    public function isSv(): ?bool
    {
        return $this->sv;
    }



    public function setSv(?bool $sv = null): void
    {
        $this->sv = $sv;
    }



    public function isSf(): ?bool
    {
        return $this->sf;
    }



    public function setSf(?bool $sf = null): void
    {
        $this->sf = $sf;
    }


    /**
     * @deprecated Usar `getTipoCtrVo(): ?TipoCentroCode` en su lugar.
     */
    public function getTipo_ctr(): ?string
    {
        return $this->tipo_ctr?->value();
    }


    /**
     * @deprecated Usar `setTipoCtrVo(?TipoCentroCode $codigo = null): void` en su lugar.
     */
    public function setTipo_ctr(?string $tipo_ctr = null): void
    {
        $this->tipo_ctr = $tipo_ctr !== null && $tipo_ctr !== '' ? new TipoCentroCode($tipo_ctr) : null;
    }

    public function getTipoCtrVo(): ?TipoCentroCode
    {
        return $this->tipo_ctr;
    }

    public function setTipoCtrVo(?TipoCentroCode $codigo = null): void
    {
        $this->tipo_ctr = $codigo;
    }


    /**
     * @deprecated Usar `getTipoLaborVo(): ?TipoLaborId` en su lugar.
     */
    public function getTipo_labor(): ?int
    {
        return $this->tipo_labor?->value();
    }


    /**
     * @deprecated Usar `setTipoLaborVo(?TipoLaborId $valor = null): void` en su lugar.
     */
    public function setTipo_labor(?int $tipo_labor = null): void
    {
        $this->tipo_labor = TipoLaborId::fromNullable($tipo_labor);
    }

    public function getTipoLaborVo(): ?TipoLaborId
    {
        return $this->tipo_labor;
    }

    public function setTipoLaborVo(?TipoLaborId $valor = null): void
    {
        $this->tipo_labor = $valor;
    }



    public function isCdc(): ?bool
    {
        return $this->cdc;
    }



    public function setCdc(?bool $cdc = null): void
    {
        $this->cdc = $cdc;
    }


    /**
     * @deprecated Usar `getIdCtrPadreVo(): ?CentroId` en su lugar.
     */
    public function getId_ctr_padre(): ?int
    {
        return $this->id_ctr_padre?->value();
    }


    /**
     * @deprecated Usar `setIdCtrPadreVo(?CentroId $id = null): void` en su lugar.
     */
    public function setId_ctr_padre(?int $id_ctr_padre = null): void
    {
        $this->id_ctr_padre = $id_ctr_padre !== null ? new CentroId($id_ctr_padre) : null;
    }

    public function getIdCtrPadreVo(): ?CentroId
    {
        return $this->id_ctr_padre;
    }

    public function setIdCtrPadreVo(?CentroId $id = null): void
    {
        $this->id_ctr_padre = $id;
    }

    public function getIdAuto(): int
    {
        return $this->id_auto;
    }

    public function setIdAuto(int $id_auto): void
    {
        $this->id_auto = $id_auto;
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