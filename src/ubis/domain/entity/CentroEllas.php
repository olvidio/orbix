<?php

namespace src\ubis\domain\entity;

use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\traits\Hydratable;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\application\services\UbiContactsTrait;
use src\ubis\domain\contracts\DireccionCentroDlRepositoryInterface;
use src\ubis\domain\contracts\RelacionCentroDlDireccionRepositoryInterface;
use src\ubis\domain\value_objects\CentroId;
use src\ubis\domain\value_objects\DelegacionCode;
use src\ubis\domain\value_objects\PaisName;
use src\ubis\domain\value_objects\RegionNameText;
use src\ubis\domain\value_objects\TipoCentroCode;
use src\ubis\domain\value_objects\TipoLaborId;
use src\ubis\domain\value_objects\UbiNombreText;
use src\shared\domain\helpers\FuncTablasSupport;

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

    protected RelacionCentroDlDireccionRepositoryInterface $repoCasaDireccion;
    protected DireccionCentroDlRepositoryInterface $repoDireccion;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private CentroId $id_ubi;

    private ?string $tipo_ubi = null;

    private UbiNombreText $nombre_ubi;

    private ?DelegacionCode $dl = null;

    private ?PaisName $pais = null;

    private ?RegionNameText $region = null;

    private bool $active = false;

    private ?DateTimeLocal $f_active = null;

    private ?bool $sv = null;

    private ?bool $sf = null;

    private ?TipoCentroCode $tipo_ctr = null;

    private ?TipoLaborId $tipo_labor = null;

    private ?bool $cdc = null;

    private ?CentroId $id_ctr_padre = null;

    private ?int $id_zona = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Constructor para inyectar los repositorios necesarios.
     *
     * $repoCasaDireccion Repositorio de relación Casa-Dirección
     * $repoDireccion Repositorio de Direcciones
     */
    public function __construct()
    {
        $this->repoCasaDireccion = DependencyResolver::get(RelacionCentroDlDireccionRepositoryInterface::class);
        $this->repoDireccion = DependencyResolver::get(DireccionCentroDlRepositoryInterface::class);
    }

    /**
     * @deprecated use getIdUbiVo()
     */
    public function getId_ubi(): int
    {
        return $this->id_ubi->value();
    }

    public function getIdUbiVo(): CentroId
    {
        return $this->id_ubi;
    }

    /**
     * @deprecated use setIdUbiVo()
     */
    public function setId_ubi(int $id_ubi): void
    {
        $this->id_ubi = new CentroId($id_ubi);
    }

    public function setIdUbiVo(CentroId|int|null $valor = null): void
    {
        if ($valor instanceof CentroId) {
            $this->id_ubi = $valor;
        } elseif ($valor !== null) {
            $this->id_ubi = new CentroId($valor);
        }
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
        return $this->nombre_ubi->value();
    }

    /**
     * @deprecated usar setNombreUbiVo()
     */
    public function setNombre_ubi(string $nombre_ubi): void
    {
        $this->nombre_ubi = new UbiNombreText($nombre_ubi);
    }

    public function getNombreUbiVo(): UbiNombreText
    {
        return $this->nombre_ubi;
    }

    public function setNombreUbiVo(UbiNombreText|string|null $vo): void
    {
        if ($vo instanceof UbiNombreText) {
            $this->nombre_ubi = $vo;
        } elseif ($vo !== null && $vo !== '') {
            $this->nombre_ubi = new UbiNombreText($vo);
        }
    }

    /**
     * @deprecated usar getDlVo()
     */
    public function getDl(): ?string
    {
        return $this->dl?->value();
    }

    /**
     * @deprecated usar setDlVo()
     */
    public function setDl(?string $dl = null): void
    {
        $this->dl = new DelegacionCode($dl);
    }

    public function getDlVo(): ?DelegacionCode
    {
        return $this->dl;
    }

    public function setDlVo(DelegacionCode|string|null $vo = null): void
    {
        $this->dl = $vo instanceof DelegacionCode
            ? $vo
            : DelegacionCode::fromNullableString($vo);
    }

    /**
     * @deprecated usar getPaisVo()
     */
    public function getPais(): ?string
    {
        return $this->pais?->value();
    }

    /**
     * @deprecated usar setPaisVo()
     */
    public function setPais(?string $pais = null): void
    {
        $this->pais = PaisName::fromNullableString($pais);
    }

    public function getPaisVo(): ?PaisName
    {
        return $this->pais;
    }

    public function setPaisVo(PaisName|string|null $vo = null): void
    {
        $this->pais = $vo instanceof PaisName
            ? $vo
            : PaisName::fromNullableString($vo);
    }

    /**
     * @deprecated usar getRegionVo()
     */
    public function getRegion(): ?string
    {
        return $this->region?->value();
    }

    /**
     * @deprecated usar setRegionVo()
     */
    public function setRegion(?string $region = null): void
    {
        $this->region = RegionNameText::fromNullableString($region);
    }

    public function getRegionVo(): ?RegionNameText
    {
        return $this->region;
    }

    public function setRegionVo(RegionNameText|string|null $vo = null): void
    {
        $this->region = $vo instanceof RegionNameText
            ? $vo
            : RegionNameText::fromNullableString($vo);
    }


    public function isActive(): bool
    {
        return (bool) $this->active;
    }


    public function setActive(bool $active): void
    {
        $this->active = $active;
    }


    public function getF_active(): DateTimeLocal|null
    {
        return $this->f_active;
    }


    public function setF_active(DateTimeLocal|null $f_active = null): void
    {
        $this->f_active = $f_active;
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
        return $this->tipo_ctr?->value();
    }

    /**
     * @deprecated usar setTipoCtrVo()
     */
    public function setTipo_ctr(?string $tipo_ctr = null): void
    {
        $this->tipo_ctr = $tipo_ctr !== null && $tipo_ctr !== '' ? new TipoCentroCode($tipo_ctr) : null;
    }

    public function getTipoCtrVo(): ?TipoCentroCode
    {
        return $this->tipo_ctr;
    }

    public function setTipoCtrVo(TipoCentroCode|string|null $vo = null): void
    {
        $this->tipo_ctr = $vo instanceof TipoCentroCode
            ? $vo
            : TipoCentroCode::fromNullableString($vo);
    }

    /**
     * @deprecated usar getTipoLaborVo()
     */
    public function getTipo_labor(): ?int
    {
        return $this->tipo_labor?->value();
    }

    /**
     * @deprecated usar setTipoLaborVo()
     */
    public function setTipo_labor(?int $tipo_labor = null): void
    {
        $this->tipo_labor = TipoLaborId::fromNullableInt($tipo_labor);
    }

    public function getTipoLaborVo(): ?TipoLaborId
    {
        return $this->tipo_labor;
    }

    public function setTipoLaborVo(TipoLaborId|int|null $vo = null): void
    {
        $this->tipo_labor = $vo instanceof TipoLaborId
            ? $vo
            : TipoLaborId::fromNullableInt($vo);
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
     * @deprecated usar getIdCtrPadreVo()
     */
    public function getId_ctr_padre(): ?int
    {
        return $this->id_ctr_padre?->value();
    }

    public function getIdCtrPadreVo(): ?CentroId
    {
        return $this->id_ctr_padre;
    }

    /**
     * @deprecated usar setIdCtrPadreVo()
     */
    public function setId_ctr_padre(?int $id_ctr_padre = null): void
    {
        $this->id_ctr_padre = CentroId::fromNullableInt($id_ctr_padre);
    }
    public function setIdCtrPadreVo(CentroId|int|null $valor = null): void
    {
        $this->id_ctr_padre = $valor instanceof CentroId
            ? $valor
            : CentroId::fromNullableInt($valor);
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
     */
    public function getUnaDireccionDetallada(int $id_direccion): ?DireccionDetalle
    {
        $relaciones = $this->repoCasaDireccion->getRelacionesPorUbi($this->getId_ubi());
        $direccionDetallada = null;

        foreach ($relaciones as $row) {
            if (!isset($row['id_direccion'])) {
                continue;
            }
            $idDireccionRaw = $row['id_direccion'];
            if (!is_int($idDireccionRaw) && !is_string($idDireccionRaw)) {
                continue;
            }
            $idDireccion = (int) $idDireccionRaw;
            if ($id_direccion !== $idDireccion) {
                continue;
            }
            $direccion = $this->repoDireccion->findById($idDireccion);
            if ($direccion !== null) {
                // Creamos el objeto intermedio con los booleanos de la DB
                // Aseguramos conversión a bool explícita
                $esPrincipal = FuncTablasSupport::isTrue($row['principal']);
                $esPropietario = FuncTablasSupport::isTrue($row['propietario']);

                $direccionDetallada = new DireccionDetalle([
                    'direccion' => $direccion,
                    'principal' => $esPrincipal,
                    'propietario' => $esPropietario
                ]);
            }
        }

        return $direccionDetallada;
    }

    /**
     * Obtiene las direcciones con sus metadatos (principal, propietario)
     *
     * @return list<DireccionDetalle>
     */
    public function getDireccionesDetalladas(): array
    {

        $relaciones = $this->repoCasaDireccion->getRelacionesPorUbi($this->getId_ubi());
        $direccionesDetalladas = [];

        foreach ($relaciones as $row) {
            if (!isset($row['id_direccion'])) {
                continue;
            }
            $idDireccionRaw = $row['id_direccion'];
            if (!is_int($idDireccionRaw) && !is_string($idDireccionRaw)) {
                continue;
            }
            $direccion = $this->repoDireccion->findById((int) $idDireccionRaw);
            if ($direccion !== null) {
                // Creamos el objeto intermedio con los booleanos de la DB
                // Aseguramos conversión a bool explícita
                $esPrincipal = FuncTablasSupport::isTrue($row['principal']);
                $esPropietario = FuncTablasSupport::isTrue($row['propietario']);

                $direccionesDetalladas[] = new DireccionDetalle([
                    'direccion' => $direccion,
                    'principal' => $esPrincipal,
                    'propietario' => $esPropietario
                ]);
            }
        }

        return $direccionesDetalladas;
    }

    /**
     * Obtiene todas las direcciones asociadas a esta casa
     *
     * @return list<Direccion>
     */
    public function getDirecciones(): array
    {
        $detalles = $this->getDireccionesDetalladas();
        return array_values(array_filter(array_map(static fn (DireccionDetalle $d) => $d->getDireccionVo(), $detalles)));
    }

    /**
     * Añade una dirección a esta casa
     */
    public function addDireccion(int $id_direccion, bool $principal = false, bool $propietario = false): void
    {
        $this->repoCasaDireccion->asociarDireccion(
            $this->getId_ubi(),
            $id_direccion,
            $principal
        );
        if ($propietario) {
            $this->repoCasaDireccion->updatePropietario($this->getId_ubi(), $id_direccion, true);
        }
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
