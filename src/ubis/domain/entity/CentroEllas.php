<?php

namespace src\ubis\domain\entity;

use src\shared\domain\traits\Hydratable;
use src\ubis\application\services\UbiContactsTrait;
use src\ubis\domain\value_objects\DelegacionCode;
use src\ubis\domain\value_objects\PaisName;
use src\ubis\domain\value_objects\RegionNameText;
use src\ubis\domain\value_objects\TipoCentroCode;
use src\ubis\domain\value_objects\TipoLaborId;
use src\ubis\domain\value_objects\UbiNombreText;
use web\DateTimeLocal;
use web\NullDateTimeLocal;
use function core\is_true;

/**
 * Clase que implementa la entidad cu_centros_dlf
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 21/11/2025
 */
class CentroEllas
{
    use Hydratable;
    // Esto inyecta los métodos getDirecciones, emailPrincipalOPrimero y getTeleco aquí
    use UbiContactsTrait;

    protected $repoCasaDireccion;
    protected $repoDireccion;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private int $id_ubi;

    private string|null $tipo_ubi = null;

    private string $nombre_ubi;

    private string|null $dl = null;

    private string|null $pais = null;

    private string|null $region = null;

    private bool $active;

    private DateTimeLocal|null $f_active = null;

    private bool|null $sv = null;

    private bool|null $sf = null;

    private string|null $tipo_ctr = null;

    private int|null $tipo_labor = null;

    private bool|null $cdc = null;

    private int|null $id_ctr_padre = null;

    private int|null $id_zona = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Constructor para inyectar los repositorios necesarios.
     *
     * $repoCasaDireccion Repositorio de relación Casa-Dirección
     * $repoDireccion Repositorio de Direcciones
     */
    public function __construct()
    {
        /* TODO: resolver el tema de las direcciones en DB-comun */
        $this->repoCasaDireccion = [];
        $this->repoDireccion = [];
    }

    public function getId_ubi(): int
    {
        return $this->id_ubi;
    }


    public function setId_ubi(int $id_ubi): void
    {
        $this->id_ubi = $id_ubi;
    }


    public function getTipo_ubi(): ?string
    {
        return $this->tipo_ubi;
    }


    public function setTipo_ubi(?string $tipo_ubi = null): void
    {
        $this->tipo_ubi = $tipo_ubi;
    }

    /**
     * @deprecated usar getNombreUbiVo()
     */
    public function getNombre_ubi(): string
    {
        return $this->nombre_ubi;
    }

    /**
     * @deprecated usar setNombreUbiVo()
     */
    public function setNombre_ubi(string $nombre_ubi): void
    {
        $this->nombre_ubi = $nombre_ubi;
    }

    public function getNombreUbiVo(): UbiNombreText
    {
        return new UbiNombreText($this->nombre_ubi);
    }

    public function setNombreUbiVo(UbiNombreText $vo): void
    {
        $this->nombre_ubi = $vo->value();
    }

    /**
     * @deprecated usar getDlVo()
     */
    public function getDl(): ?string
    {
        return $this->dl;
    }

    /**
     * @deprecated usar setDlVo()
     */
    public function setDl(?string $dl = null): void
    {
        $this->dl = $dl;
    }

    public function getDlVo(): ?DelegacionCode
    {
        return $this->dl !== null ? new DelegacionCode($this->dl) : null;
    }

    public function setDlVo(?DelegacionCode $vo = null): void
    {
        $this->dl = $vo?->value();
    }

    /**
     * @deprecated usar getPaisVo()
     */
    public function getPais(): ?string
    {
        return $this->pais;
    }

    /**
     * @deprecated usar setPaisVo()
     */
    public function setPais(?string $pais = null): void
    {
        $this->pais = $pais;
    }

    public function getPaisVo(): ?PaisName
    {
        return $this->pais !== null ? new PaisName($this->pais) : null;
    }

    public function setPaisVo(?PaisName $vo = null): void
    {
        $this->pais = $vo?->value();
    }

    /**
     * @deprecated usar getRegionVo()
     */
    public function getRegion(): ?string
    {
        return $this->region;
    }

    /**
     * @deprecated usar setRegionVo()
     */
    public function setRegion(?string $region = null): void
    {
        $this->region = $region;
    }

    public function getRegionVo(): ?RegionNameText
    {
        return $this->region !== null ? new RegionNameText($this->region) : null;
    }

    public function setRegionVo(?RegionNameText $vo = null): void
    {
        $this->region = $vo?->value();
    }


    public function isActive(): bool
    {
        return $this->active;
    }


    public function setActive(bool $active): void
    {
        $this->active = $active;
    }


    public function getF_active(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->f_active ?? new NullDateTimeLocal;
    }


    public function setF_active(DateTimeLocal|NullDateTimeLocal|null $f_active = null): void
    {
        $this->f_active = $f_active instanceof NullDateTimeLocal ? null : $f_active;
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
     * @deprecated usar getTipoCtrVo()
     */
    public function getTipo_ctr(): ?string
    {
        return $this->tipo_ctr;
    }

    /**
     * @deprecated usar setTipoCtrVo()
     */
    public function setTipo_ctr(?string $tipo_ctr = null): void
    {
        $this->tipo_ctr = $tipo_ctr;
    }

    public function getTipoCtrVo(): ?TipoCentroCode
    {
        return $this->tipo_ctr !== null ? new TipoCentroCode($this->tipo_ctr) : null;
    }

    public function setTipoCtrVo(?TipoCentroCode $vo = null): void
    {
        $this->tipo_ctr = $vo?->value();
    }

    /**
     * @deprecated usar getTipoLaborVo()
     */
    public function getTipo_labor(): ?int
    {
        return $this->tipo_labor;
    }

    /**
     * @deprecated usar setTipoLaborVo()
     */
    public function setTipo_labor(?int $tipo_labor = null): void
    {
        $this->tipo_labor = $tipo_labor;
    }

    public function getTipoLaborVo(): ?TipoLaborId
    {
        return $this->tipo_labor !== null ? new TipoLaborId($this->tipo_labor) : null;
    }

    public function setTipoLaborVo(?TipoLaborId $vo = null): void
    {
        $this->tipo_labor = $vo?->value();
    }


    public function isCdc(): ?bool
    {
        return $this->cdc;
    }


    public function setCdc(?bool $cdc = null): void
    {
        $this->cdc = $cdc;
    }


    public function getId_ctr_padre(): ?int
    {
        return $this->id_ctr_padre;
    }


    public function setId_ctr_padre(?int $id_ctr_padre = null): void
    {
        $this->id_ctr_padre = $id_ctr_padre;
    }


    public function getId_zona(): ?int
    {
        return $this->id_zona;
    }


    public function setId_zona(?int $id_zona = null): void
    {
        $this->id_zona = $id_zona;
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