<?php

namespace src\ubis\domain\entity;

use src\ubis\application\services\UbiContactsTrait;
use web\DateTimeLocal;
use web\NullDateTimeLocal;
use function core\is_true;
use src\ubis\domain\value_objects\{CasaId, UbiNombreText, DelegacionCode, PaisName, RegionNameText, TipoCasaText, BibliotecaText, ObservCasaText, Plazas, PlazasMin, NumSacerdotes};

class Casa
{
    // Esto inyecta los métodos getDirecciones, emailPrincipalOPrimero y getTeleco aquí
    use UbiContactsTrait;

    protected $repoCasaDireccion;
    protected $repoDireccion;
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Tipo_ubi de Casa
     *
     * @var string|null
     */
    private string|null $stipo_ubi = null;
    /**
     * Id_ubi de Casa (VO)
     */
    private CasaId $iid_ubi;
    /**
     * Nombre_ubi de Casa (VO)
     */
    private UbiNombreText $snombre_ubi;
    /**
     * Dl de Casa (VO)
     */
    private ?DelegacionCode $sdl = null;
    /**
     * Pais de Casa (VO)
     */
    private ?PaisName $spais = null;
    /**
     * Region de Casa (VO)
     */
    private ?RegionNameText $sregion = null;
    /**
     * Status de Casa
     *
     * @var bool
     */
    private bool $bstatus;
    /**
     * F_status de Casa
     *
     * @var DateTimeLocal|null
     */
    private DateTimeLocal|null $df_status = null;
    /**
     * Sv de Casa
     *
     * @var bool|null
     */
    private bool|null $bsv = null;
    /**
     * Sf de Casa
     *
     * @var bool|null
     */
    private bool|null $bsf = null;
    /**
     * Tipo_casa de Casa (VO)
     */
    private ?TipoCasaText $stipo_casa = null;
    /**
     * Plazas de Casa (VO)
     */
    private ?Plazas $iplazas = null;
    /**
     * Plazas_min de Casa (VO)
     */
    private ?PlazasMin $iplazas_min = null;
    /**
     * Num_sacd de Casa (VO)
     */
    private ?NumSacerdotes $inum_sacd = null;
    /**
     * Biblioteca de Casa (VO)
     */
    private ?BibliotecaText $sbiblioteca = null;
    /**
     * Observ de Casa (VO)
     */
    private ?ObservCasaText $sobserv = null;

    private int $iid_auto;
    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return Casa
     */
    public function setAllAttributes(array $aDatos): Casa
    {
        if (array_key_exists('tipo_ubi', $aDatos)) {
            $this->setTipo_ubi($aDatos['tipo_ubi']);
        }
        if (array_key_exists('id_ubi', $aDatos)) {
            $valor = $aDatos['id_ubi'];
            if ($valor instanceof CasaId) {
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
            if ($valor instanceof DelegacionCode || $valor === null) {
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
        if (array_key_exists('tipo_casa', $aDatos)) {
            $valor = $aDatos['tipo_casa'] ?? null;
            if ($valor instanceof TipoCasaText || $valor === null) {
                $this->setTipoCasaVo($valor);
            } else {
                $this->setTipo_casa($valor !== null ? (string)$valor : null);
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
        if (array_key_exists('plazas_min', $aDatos)) {
            $valor = $aDatos['plazas_min'] ?? null;
            if ($valor instanceof PlazasMin || $valor === null) {
                $this->setPlazasMinVo($valor);
            } else {
                $this->setPlazas_min(isset($valor) && $valor !== '' ? (int)$valor : null);
            }
        }
        if (array_key_exists('num_sacd', $aDatos)) {
            $valor = $aDatos['num_sacd'] ?? null;
            if ($valor instanceof NumSacerdotes || $valor === null) {
                $this->setNumSacdVo($valor);
            } else {
                $this->setNum_sacd(isset($valor) && $valor !== '' ? (int)$valor : null);
            }
        }
        if (array_key_exists('biblioteca', $aDatos)) {
            $valor = $aDatos['biblioteca'] ?? null;
            if ($valor instanceof BibliotecaText || $valor === null) {
                $this->setBibliotecaVo($valor);
            } else {
                $this->setBiblioteca($valor !== null ? (string)$valor : null);
            }
        }
        if (array_key_exists('observ', $aDatos)) {
            $valor = $aDatos['observ'] ?? null;
            if ($valor instanceof ObservCasaText || $valor === null) {
                $this->setObservVo($valor);
            } else {
                $this->setObserv($valor !== null ? (string)$valor : null);
            }
        }
        return $this;
    }

    /**
     *
     * @return string|null $stipo_ubi
     */
    /**
     * @return string|null $stipo_ubi
     *
     * @deprecated Usar la API VO específica si aplica, o mantener este método mientras la UI lo requiera.
     */
    public function getTipo_ubi(): ?string
    {
        return $this->stipo_ubi;
    }

    /**
     *
     * @param string|null $stipo_ubi
     */
    /**
     * @param string|null $stipo_ubi
     *
     * @deprecated Usar la API VO específica si aplica, o mantener este método mientras la UI lo requiera.
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
     * @return int $iid_ubi
     *
     * @deprecated Usar `getIdUbiVo(): CasaId` en su lugar.
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
     * @param int $iid_ubi
     *
     * @deprecated Usar `setIdUbiVo(CasaId $id): void` en su lugar.
     */
    public function setId_ubi(int $iid_ubi): void
    {
        $this->iid_ubi = new CasaId($iid_ubi);
    }

    // -------- API VO (nueva) ---------
    /** Getter VO para id_ubi */
    public function getIdUbiVo(): CasaId
    {
        return $this->iid_ubi;
    }

    /** Setter VO para id_ubi */
    public function setIdUbiVo(CasaId $id): void
    {
        $this->iid_ubi = $id;
    }

    /**
     *
     * @return string $snombre_ubi
     */
    /**
     * @return string $snombre_ubi
     *
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
     * @param string $snombre_ubi
     *
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
     * @return string|null $sdl
     *
     * @deprecated Usar `getDlVo(): ?DelegacionCode` en su lugar.
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
     * @param string|null $sdl
     *
     * @deprecated Usar `setDlVo(?DelegacionCode $codigo = null): void` en su lugar.
     */
    public function setDl(?string $sdl = null): void
    {
        $this->sdl = DelegacionCode::fromString($sdl);
    }

    public function getDlVo(): ?DelegacionCode
    {
        return $this->sdl;
    }

    public function setDlVo(?DelegacionCode $codigo = null): void
    {
        $this->sdl = $codigo;
    }

    /**
     *
     * @return string|null $spais
     */
    /**
     * @return string|null $spais
     *
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
     * @param string|null $spais
     *
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
     * @return string|null $sregion
     *
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
     * @param string|null $sregion
     *
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
     * @return string|null $stipo_casa
     */
    /**
     * @return string|null $stipo_casa
     *
     * @deprecated Usar `getTipoCasaVo(): ?TipoCasaText` en su lugar.
     */
    public function getTipo_casa(): ?string
    {
        return $this->stipo_casa?->value();
    }

    /**
     *
     * @param string|null $stipo_casa
     */
    /**
     * @param string|null $stipo_casa
     *
     * @deprecated Usar `setTipoCasaVo(?TipoCasaText $texto = null): void` en su lugar.
     */
    public function setTipo_casa(?string $stipo_casa = null): void
    {
        $this->stipo_casa = TipoCasaText::fromNullableString($stipo_casa);
    }

    public function getTipoCasaVo(): ?TipoCasaText
    {
        return $this->stipo_casa;
    }

    public function setTipoCasaVo(?TipoCasaText $texto = null): void
    {
        $this->stipo_casa = $texto;
    }

    /**
     *
     * @return int|null $iplazas
     */
    /**
     * @return int|null $iplazas
     *
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
     * @param int|null $iplazas
     *
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
     * @return int|null $iplazas_min
     */
    /**
     * @return int|null $iplazas_min
     *
     * @deprecated Usar `getPlazasMinVo(): ?PlazasMin` en su lugar.
     */
    public function getPlazas_min(): ?int
    {
        return $this->iplazas_min?->value();
    }

    /**
     *
     * @param int|null $iplazas_min
     */
    /**
     * @param int|null $iplazas_min
     *
     * @deprecated Usar `setPlazasMinVo(?PlazasMin $valor = null): void` en su lugar.
     */
    public function setPlazas_min(?int $iplazas_min = null): void
    {
        $this->iplazas_min = PlazasMin::fromNullable($iplazas_min);
    }

    public function getPlazasMinVo(): ?PlazasMin
    {
        return $this->iplazas_min;
    }

    public function setPlazasMinVo(?PlazasMin $valor = null): void
    {
        $this->iplazas_min = $valor;
    }

    /**
     *
     * @return int|null $inum_sacd
     */
    /**
     * @return int|null $inum_sacd
     *
     * @deprecated Usar `getNumSacdVo(): ?NumSacerdotes` en su lugar.
     */
    public function getNum_sacd(): ?int
    {
        return $this->inum_sacd?->value();
    }

    /**
     *
     * @param int|null $inum_sacd
     */
    /**
     * @param int|null $inum_sacd
     *
     * @deprecated Usar `setNumSacdVo(?NumSacerdotes $valor = null): void` en su lugar.
     */
    public function setNum_sacd(?int $inum_sacd = null): void
    {
        $this->inum_sacd = NumSacerdotes::fromNullable($inum_sacd);
    }

    public function getNumSacdVo(): ?NumSacerdotes
    {
        return $this->inum_sacd;
    }

    public function setNumSacdVo(?NumSacerdotes $valor = null): void
    {
        $this->inum_sacd = $valor;
    }

    /**
     *
     * @return string|null $sbiblioteca
     */
    /**
     * @return string|null $sbiblioteca
     *
     * @deprecated Usar `getBibliotecaVo(): ?BibliotecaText` en su lugar.
     */
    public function getBiblioteca(): ?string
    {
        return $this->sbiblioteca?->value();
    }

    /**
     *
     * @param string|null $sbiblioteca
     */
    /**
     * @param string|null $sbiblioteca
     *
     * @deprecated Usar `setBibliotecaVo(?BibliotecaText $texto = null): void` en su lugar.
     */
    public function setBiblioteca(?string $sbiblioteca = null): void
    {
        $this->sbiblioteca = BibliotecaText::fromNullableString($sbiblioteca);
    }

    public function getBibliotecaVo(): ?BibliotecaText
    {
        return $this->sbiblioteca;
    }

    public function setBibliotecaVo(?BibliotecaText $texto = null): void
    {
        $this->sbiblioteca = $texto;
    }

    /**
     *
     * @return string|null $sobserv
     */
    /**
     * @return string|null $sobserv
     *
     * @deprecated Usar `getObservVo(): ?ObservCasaText` en su lugar.
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
     * @param string|null $sobserv
     *
     * @deprecated Usar `setObservVo(?ObservCasaText $texto = null): void` en su lugar.
     */
    public function setObserv(?string $sobserv = null): void
    {
        $this->sobserv = ObservCasaText::fromNullableString($sobserv);
    }

    public function getObservVo(): ?ObservCasaText
    {
        return $this->sobserv;
    }

    public function setObservVo(?ObservCasaText $texto = null): void
    {
        $this->sobserv = $texto;
    }

     public function getIdAuto(): int
    {
        return $this->iid_auto;
    }

    public function setIdAuto(int $iid_auto): void
    {
        $this->iid_auto = $iid_auto;
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