<?php

namespace src\ubis\domain\entity;

use src\shared\domain\traits\Hydratable;
use src\ubis\application\services\UbiContactsTrait;
use src\ubis\domain\contracts\DireccionCasaRepositoryInterface;
use src\ubis\domain\contracts\RelacionCasaDireccionRepositoryInterface;
use src\ubis\domain\value_objects\{BibliotecaText,
    CasaId,
    DelegacionCode,
    NumSacerdotes,
    ObservCasaText,
    PaisName,
    Plazas,
    PlazasMin,
    RegionNameText,
    TipoCasaText,
    UbiNombreText};
use web\DateTimeLocal;
use web\NullDateTimeLocal;
use function core\is_true;

class Casa
{
    use Hydratable;

    // Esto inyecta los métodos getDirecciones, emailPrincipalOPrimero y getTeleco aquí
    use UbiContactsTrait;

    protected $repoCasaDireccion;
    protected $repoDireccion;
    /* ATRIBUTOS ----------------------------------------------------------------- */


    private ?string $tipo_ubi = null;

    private CasaId $id_ubi;

    private UbiNombreText $nombre_ubi;

    private ?DelegacionCode $dl = null;

    private ?PaisName $pais = null;

    private ?RegionNameText $region = null;

    private bool $active;

    private ?DateTimeLocal $f_active = null;

    private?bool $sv = null;

    private?bool $sf = null;

    private ?TipoCasaText $tipo_casa = null;

    private ?Plazas $plazas = null;

    private ?PlazasMin $plazas_min = null;

    private ?NumSacerdotes $num_sacd = null;

    private ?BibliotecaText $biblioteca = null;

    private ?ObservCasaText $observ = null;

    private int $id_auto;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Constructor para inyectar los repositorios necesarios.
     *
     * $repoCasaDireccion Repositorio de relación Casa-Dirección
     * $repoDireccion Repositorio de Direcciones
     */
    public function __construct()
    {
        $this->repoCasaDireccion = $GLOBALS['container']->get(RelacionCasaDireccionRepositoryInterface::class);
        $this->repoDireccion = $GLOBALS['container']->get(DireccionCasaRepositoryInterface::class);
    }

    /**
     * @deprecated Usar la API VO específica si aplica, o mantener este método mientras la UI lo requiera.
     */
    public function getTipo_ubi(): ?string
    {
        return $this->tipo_ubi;
    }


    /**
     * @deprecated Usar la API VO específica si aplica, o mantener este método mientras la UI lo requiera.
     */
    public function setTipo_ubi(?string $tipo_ubi = null): void
    {
        $this->tipo_ubi = $tipo_ubi;
    }


    /**
     * @deprecated Usar `getIdUbiVo(): CasaId` en su lugar.
     */
    public function getId_ubi(): int
    {
        return $this->id_ubi->value();
    }


    /**
     * @deprecated Usar `setIdUbiVo(CasaId $id): void` en su lugar.
     */
    public function setId_ubi(int $id_ubi): void
    {
        $this->id_ubi = new CasaId($id_ubi);
    }

    // -------- API VO (nueva) ---------

    /** Getter VO para id_ubi */
    public function getIdUbiVo(): CasaId
    {
        return $this->id_ubi;
    }

    /** Setter VO para id_ubi */
    public function setIdUbiVo(CasaId|int $id): void
    {
        $this->id_ubi = $id instanceof CasaId
            ? $id
            : new CasaId($id);
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

    public function setNombreUbiVo(UbiNombreText|string $texto): void
    {
        $this->nombre_ubi = $texto instanceof UbiNombreText
            ? $texto
            : new UbiNombreText($texto);
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

    public function setDlVo(DelegacionCode|string|null $texto = null): void
    {
        $this->dl = $texto instanceof DelegacionCode
            ? $texto
            : DelegacionCode::fromNullableString($texto);
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

    public function setPaisVo(PaisName|string|null $texto = null): void
    {
        $this->pais = $texto instanceof PaisName
            ? $texto
            : PaisName::fromNullableString($texto);
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

    public function setRegionVo(RegionNameText|string|null $texto = null): void
    {
        $this->region = $texto instanceof RegionNameText
            ? $texto
            : RegionNameText::fromNullableString($texto);
    }


    public function isActive(): bool
    {
        return $this->active;
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
        $this->f_active = $f_active instanceof DateTimeLocal
            ? $f_active
            : null;
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
     * @deprecated Usar `getTipoCasaVo(): ?TipoCasaText` en su lugar.
     */
    public function getTipo_casa(): ?string
    {
        return $this->tipo_casa?->value();
    }


    /**
     * @deprecated Usar `setTipoCasaVo(?TipoCasaText $texto = null): void` en su lugar.
     */
    public function setTipo_casa(?string $tipo_casa = null): void
    {
        $this->tipo_casa = TipoCasaText::fromNullableString($tipo_casa);
    }

    public function getTipoCasaVo(): ?TipoCasaText
    {
        return $this->tipo_casa;
    }

    public function setTipoCasaVo(TipoCasaText|string|null $texto = null): void
    {
        $this->tipo_casa = $texto instanceof TipoCasaText
            ? $texto
            : TipoCasaText::fromNullableString($texto);
    }


    /**
     * @deprecated Usar `getPlazasVo(): ?Plazas` en su lugar.
     */
    public function getPlazas(): ?int
    {
        return $this->plazas?->value();
    }


    /**
     * @deprecated Usar `setPlazasVo(?Plazas $valor = null): void` en su lugar.
     */
    public function setPlazas(?int $plazas = null): void
    {
        $this->plazas = Plazas::fromNullableInt($plazas);
    }

    public function getPlazasVo(): ?Plazas
    {
        return $this->plazas;
    }

    public function setPlazasVo(Plazas|int|null $valor = null): void
    {
        $this->plazas = $valor instanceof Plazas
            ? $valor
            : Plazas::fromNullableInt($valor);
    }


    /**
     * @deprecated Usar `getPlazasMinVo(): ?PlazasMin` en su lugar.
     */
    public function getPlazas_min(): ?int
    {
        return $this->plazas_min?->value();
    }


    /**
     * @deprecated Usar `setPlazasMinVo(?PlazasMin $valor = null): void` en su lugar.
     */
    public function setPlazas_min(?int $plazas_min = null): void
    {
        $this->plazas_min = PlazasMin::fromNullableInt($plazas_min);
    }

    public function getPlazasMinVo(): ?PlazasMin
    {
        return $this->plazas_min;
    }

    public function setPlazasMinVo(PlazasMin|string|null $texto = null): void
    {
        $this->plazas_min = $texto instanceof PlazasMin
            ? $texto
            : PlazasMin::fromNullableInt($texto);
    }


    /**
     * @deprecated Usar `getNumSacdVo(): ?NumSacerdotes` en su lugar.
     */
    public function getNum_sacd(): ?int
    {
        return $this->num_sacd?->value();
    }


    /**
     * @deprecated Usar `setNumSacdVo(?NumSacerdotes $valor = null): void` en su lugar.
     */
    public function setNum_sacd(?int $num_sacd = null): void
    {
        $this->num_sacd = NumSacerdotes::fromNullableInt($num_sacd);
    }

    public function getNumSacdVo(): ?NumSacerdotes
    {
        return $this->num_sacd;
    }

    public function setNumSacdVo(NumSacerdotes|int|null $valor = null): void
    {
        $this->num_sacd = $valor instanceof NumSacerdotes
            ? $valor
            : NumSacerdotes::fromNullableInt($valor);
    }


    /**
     * @deprecated Usar `getBibliotecaVo(): ?BibliotecaText` en su lugar.
     */
    public function getBiblioteca(): ?string
    {
        return $this->biblioteca?->value();
    }


    /**
     * @deprecated Usar `setBibliotecaVo(?BibliotecaText $texto = null): void` en su lugar.
     */
    public function setBiblioteca(?string $biblioteca = null): void
    {
        $this->biblioteca = BibliotecaText::fromNullableString($biblioteca);
    }

    public function getBibliotecaVo(): ?BibliotecaText
    {
        return $this->biblioteca;
    }

    public function setBibliotecaVo(BibliotecaText|string|null $texto = null): void
    {
        $this->biblioteca = $texto instanceof BibliotecaText
            ? $texto
            : BibliotecaText::fromNullableString($texto);
    }


    /**
     * @deprecated Usar `getObservVo(): ?ObservCasaText` en su lugar.
     */
    public function getObserv(): ?string
    {
        return $this->observ?->value();
    }


    /**
     * @deprecated Usar `setObservVo(?ObservCasaText $texto = null): void` en su lugar.
     */
    public function setObserv(?string $observ = null): void
    {
        $this->observ = ObservCasaText::fromNullableString($observ);
    }

    public function getObservVo(): ?ObservCasaText
    {
        return $this->observ;
    }

    public function setObservVo(ObservCasaText|string|null $texto = null): void
    {
        $this->observ = $texto instanceof ObservCasaText
            ? $texto
            : ObservCasaText::fromNullableString($texto);
    }

    public function getId_auto(): int
    {
        return $this->id_auto;
    }

    public function setId_auto(int $id_auto): void
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